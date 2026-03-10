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

use context_system;
use core_ai\aiactions\generate_text;
use dml_exception;
use Exception;
use local_activityfilter\activity_searcher\contracts\activity_ranking;
use local_activityfilter\activity_searcher\contracts\i_activity_searcher;

/**
 * Filter activities
 *
 * @copyright   2025 Team 13 <team13@mailbox.org>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ai_searcher implements i_activity_searcher {
    /** @var i_activity_summarizer Activity summarizer */
    private readonly i_activity_summarizer $summerizer;
    /** @var i_text_compressor Text compressor */
    private readonly i_text_compressor $compressor;

    /**
     * Constructor.
     *
     * @param i_activity_summarizer $summerizer Activity summarizer
     * @param i_text_compressor $compressor Text compressor
     */
    public function __construct(
        i_activity_summarizer $summerizer,
        i_text_compressor $compressor,
    ) {
        $this->summerizer = $summerizer;
        $this->compressor = $compressor;
    }

    /**
     * Searches for activities fitting to the given user request.
     * It will return an array of activity rankings.
     *
     * @param string $request User request.
     * @param int $contextid The context ID for the AI request.
     * @return activity_ranking[] List of activity rankings.
     * @throws dml_exception
     * @throws invalid_ai_response
     */
    public function filter_activities(string $request, int $contextid = 0): array {
        $activitysummary = $this->summerizer->get_activity_data();
        $prompttext = $this->build_prompt_text($request, $activitysummary);
        $response = $this->send_request($prompttext, $contextid);

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

    /**
     * Builds the prompt text from user request and activity data.
     *
     * @param string $userrequest User request.
     * @param activity_data[] $activitydata List of activity data.
     * @return string The prompt text.
     * @throws dml_exception
     */
    public function build_prompt_text(string $userrequest, array $activitydata): string {
        $plugindescription = json_encode($activitydata, JSON_UNESCAPED_UNICODE);
        $plugindescription = preg_replace('/<[^>]*>/', '', $plugindescription);
        $plugindescription = str_replace("\/innen", "", $plugindescription);
        $plugindescription = str_replace("\/", "/", $plugindescription);
        $plugindescription = str_replace('},{', "\n", $plugindescription);

        return get_config('local_activityfilter', 'systemprompt') .
            'Plugin descriptions ' . $plugindescription . "\n" .
            'User request: ' . $this->compressor->compress($userrequest);
    }

    /**
     * Sends the request to the configured AI backend.
     *
     * @param string $prompttext The prompt text.
     * @param int $contextid The context ID for the AI request.
     * @return string AI Response.
     * @throws Exception
     */
    public function send_request(string $prompttext, int $contextid = 0): string {
        if ($contextid === 0) {
            $contextid = context_system::instance()->id;
        }
        $backend = get_config('local_activityfilter', 'backend');
        if ($backend === 'local_ai_manager') {
            return $this->send_request_local_ai_manager($prompttext, $contextid);
        }
        return $this->send_request_core_ai($prompttext, $contextid);
    }

    /**
     * Sends the request via core_ai subsystem.
     *
     * @param string $prompttext The prompt text.
     * @param int $contextid The context ID.
     * @return string AI Response.
     * @throws Exception
     */
    private function send_request_core_ai(string $prompttext, int $contextid): string {
        global $USER;
        $action = new generate_text(
            $contextid,
            $USER->id,
            $prompttext
        );
        $manager = \core\di::get(\core_ai\manager::class);
        $response = $manager->process_action($action);
        if (!$response->get_success()) {
            throw new Exception($response->get_errormessage());
        }
        return $response->get_response_data()['generatedcontent'];
    }

    /**
     * Sends the request via local_ai_manager.
     *
     * @param string $prompttext The prompt text.
     * @param int $contextid The context ID.
     * @return string AI Response.
     * @throws \moodle_exception
     */
    private function send_request_local_ai_manager(string $prompttext, int $contextid): string {
        $manager = new \local_ai_manager\manager('singleprompt');
        $response = $manager->perform_request($prompttext, 'local_activityfilter', $contextid);
        if ($response->get_code() !== 200) {
            throw new \moodle_exception(
                'error:ai_call',
                'local_activityfilter',
                '',
                $response->get_errormessage(),
                $response->get_debuginfo()
            );
        }
        // The ai_manager may wrap content in HTML tags (e.g. <p>...</p>), strip them for raw JSON.
        return trim(strip_tags($response->get_content()));
    }

    /**
     * Convert AI Response to an json
     *
     * @param string $text AI Response text
     * @return array|false Converted json object or false if not convertable
     */
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

    /**
     * Convert json object to ranking data
     *
     * @param mixed $json Array object or other data converted from json
     * @param activity_data[] $activitydata Unconverted activity data
     * @return activity_ranking[]|false Converted AI Response as rankings
     */
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

            $rankingdata['occurrences'] = $activity->get_usage_amount();
            $rankingdata['logohtml'] = $activity->get_logo_html();
            $rankingdata['title'] = $activity->get_title();
            $data[] = activity_ranking::from_stdclass((object)$rankingdata);
        }

        return $data;
    }

    /**
     * Searches if plugin exist in activity data
     *
     * @param string $pluginname Activity name
     * @param activity_data[] $activitydata List of all choose able activities
     * @return activity_data|false If plugin name is not found in given activity data
     */
    public function find_activity(string $pluginname, array $activitydata): activity_data|false {
        foreach ($activitydata as $activity) {
            if ($activity->get_name() == $pluginname) {
                return $activity;
            }
        }

        return false;
    }
}
