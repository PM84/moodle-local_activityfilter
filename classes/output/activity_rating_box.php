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

namespace local_activityfilter\output;

use coding_exception;
use core\di;
use core\output\renderer_base;
use dml_exception;
use lang_string;
use local_activityfilter\activity_searcher\contracts\activity_ranking;
use moodle_database;
use renderable;
use templatable;
use function debugging;

/**
 * UI-Component for an activity rating for a use case.
 *
 * @author Konrad Ebel <konrad.ebel@oncampus.de>
 * @copyright 2025, oncampus GmbH
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_rating_box implements renderable, templatable {
    /** @var string Name of this plugin */
    private const PLUGIN_NAME = 'local_activityfilter';
    /** @var activity_ranking Activity rating data from AI */
    private readonly activity_ranking $activityrating;

    /**
     * Constructor.
     *
     * @param array $activityrating Activity rating data from AI
     */
    public function __construct(
        private int $id,
        activity_ranking $activityrating
    ) {
        $this->activityrating = $activityrating;
    }

    /**
     * Converts the data to the view data
     *
     * @param renderer_base|null $output
     * @return array
     * @throws coding_exception
     */
    public function export_for_template(?renderer_base $output = null): array {
        $rating = $this->activityrating;
        // Validate AI Ranking.
        $rankfix = (int)$rating->ranking ?? 0;
        $rankfix = max(0, min(10, $rankfix));

        return [
            'id' => $this->id,
            'pluginname' => $rating->title,
            'hint' => $rating->hint,
            'ranking' => $rankfix,
            'reason' => $rating->reason,
            'occurences' => $this->get_occurance_string(),
            'stars' => self::convert_ranking_stars($rankfix),
            'activityicon' => $rating->logohtml,
        ];
    }

    /**
     * Converts the occurrence number for output
     *
     * @return lang_string Language string, how often this activity occurs in the moodle
     * @throws coding_exception
     */
    private function get_occurance_string(): string {
        $maxusage = max($this->get_max_activity_usage_amount(), 1);
        $frequencyranking = $this->activityrating->occurences * 5 / $maxusage;
        $frequencyranking = floor($frequencyranking);

        switch ($frequencyranking) {
            case 0:
                return get_string('occurences:very_rare', self::PLUGIN_NAME);
            case 1:
                return get_string('occurences:rare', self::PLUGIN_NAME);
            case 2:
                return get_string('occurences:moderately', self::PLUGIN_NAME);
            case 3:
                return get_string('occurences:often', self::PLUGIN_NAME);
            case 4:
                return get_string('occurences:very_frequent', self::PLUGIN_NAME);
            default:
                return get_string('occurences:very_frequent', self::PLUGIN_NAME);
        }
    }

    /**
     * Fetch the usage amount of the given
     *
     * @return int Usage count of most used activity
     * @throws dml_exception
     */
    private function get_max_activity_usage_amount(): int {
        $db = di::get(moodle_database::class);
        $record = $db->get_record_sql(
            'SELECT COUNT(*) AS count
                 FROM {course_modules} cm
                 JOIN {modules} m ON m.id = cm.module
                 GROUP BY m.name
                 ORDER BY COUNT(*) DESC',
            strictness: IGNORE_MULTIPLE
        );
        if ($record === false) {
            debugging('No activities available for counting');
            return 0;
        }
        return $record->count;
    }

    /**
     * Converts an int from 0 to 10 into a UI-Star rating
     *
     * @param int $ranking Rating from 0 to 10
     * @return array UI Data for star rating
     */
    private static function convert_ranking_stars(int $ranking): array {
        $staricons = [];

        $stars = intdiv($ranking, 2);
        for ($i = 1; $i <= $stars; $i++) {
            $staricons[] = 'fa-star';
        }

        if ($ranking % 2 == 1) {
            $staricons[] = 'fa-star-half-full';
            $stars++;
        }

        for ($i = 1; $i <= (5 - $stars); $i++) {
            $staricons[] = 'fa-star-o';
        }

        return $staricons;
    }
}
