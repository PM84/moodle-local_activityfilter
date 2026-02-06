<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace local_activityfilter\activity_searcher;

use local_activityfilter\activity_searcher\contracts\activity_ranking;
use local_activityfilter\activity_searcher\contracts\i_activity_searcher;
use context_system;
use core_ai\aiactions\generate_text;
use core_ai\manager;
use Exception;

/**
 * Filter activities
 *
 * @copyright   2025 Team 13 <team13@mailbox.org>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ai_searcher implements i_activity_searcher {
    public function __construct(
        private readonly i_activity_summarizer $summerizer,
        private readonly i_text_compressor $compressor,
    ) {
    }

    public function filter_activities(string $request): array {
        if (get_config('local_activityfilter', 'dummy_mode')) {
            $response = file_get_contents(__DIR__ . '/dummydata.json');
            $decoded = json_decode($response, true);
            if ($decoded === null) {
                throw new Exception("Json encode error: " . json_last_error_msg());
            }
            return $decoded;
        }

        $activitysummary = $this->summerizer->get_activity_data();
        $prompt = self::prepare_prompt($request, $activitysummary);
        $response = self::send_request($prompt);

        $json = $this->convert_ai_response_to_json($response);
        if ($json === false) {
            throw new invalid_ai_response("Invalid AI feedback: " . $response);
        }

        $ratings = $this->convert_json_to_ranking($json, $activitysummary);
        if ($ratings === false) {
            throw new invalid_ai_response("Invalid AI feedback: " . $response);
        }

        return $ratings;
    }

    public function send_request(generate_text $prompts): string {
        $response = (new manager())->process_action($prompts);
        if (!$response->get_success()) {
            throw new Exception($response->get_errormessage());
        }

        return $response->get_response_data()['generatedcontent'];
    }

    public function convert_json_to_ranking(mixed $json, array $activitydata): array|false {
        $data = [];
        foreach ($json as $rankingdata) {
            $pluginname = $rankingdata["pluginname"] ?? false;
            if (!$pluginname) {
                continue;
            }

            $activity = $this->find_activity($rankingdata["pluginname"], $activitydata);
            if (!$activity) {
                continue;
            }

            $rankingdata['occurences'] = $activity->usagecount;
            $data[] = activity_ranking::from_stdclass((object)$rankingdata);
        }

        return $data;
    }

    public function find_activity(string $pluginname, array $activitydata): activity_data|false {
        foreach ($activitydata as $activity) {
            if ($activity->name == $pluginname) {
                return $activity;
            }
        }

        return false;
    }

    public function convert_ai_response_to_json(string $text): array|false {
        $directdecode = json_decode($text, true);
        if (json_last_error() == JSON_ERROR_NONE) {
            return $directdecode;
        }

        $start = strpos($text, "```json");
        if ($start === false) {
            return false;
        }
        $start += strlen("```json");

        $end = strpos($text, "```", $start);
        if ($end === false) {
            return false;
        }

        $json = trim(substr($text, $start, $end - $start));

        $data = json_decode($json, true);
        return is_array($data) ? $data : false;
    }

    public function prepare_prompt(string $userrequest, array $activitydata): generate_text {
        global $USER;
        $plugindescription = json_encode($activitydata, JSON_UNESCAPED_UNICODE);
        $plugindescription = preg_replace('/<[^>]*>/', '', $plugindescription);
        $plugindescription = str_replace("\/innen", "", $plugindescription);
        $plugindescription = str_replace("\/", "/", $plugindescription);
        $plugindescription = str_replace('},{', "\n", $plugindescription);

        $promptmsg = (
            get_config('local_activityfilter', 'systemprompt') .
            'Plugin descriptions ' . $plugindescription . "\n" .
            'User request: ' . $this->compressor->compress($userrequest)
        );

        return new generate_text(
            context_system::instance()->id,
            $USER->id,
            $promptmsg
        );
    }
}
