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

namespace local_activityfilter\activity_searcher\contracts;

use stdClass;

/**
 * Rating for an activity in moodle
 *
 * @author Konrad Ebel <konrad.ebel@oncampus.de>
 * @copyright 2025, oncampus GmbH
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_ranking {
    /**
     * Constructor
     *
     * @param string $pluginname Name of the mod plugin
     * @param string $title Title of the plugin in the user language
     * @param string $reason Reason why this plugin fits to the request
     * @param string $hint Way how to use this plugin, so it can fulfill the request
     * @param int $popularity How often it's used in the moodle 0-10
     * @param int $ranking Ranking, how much it fits to the request 0-10
     * @param string $logohtml HTML of the component item logo
     */
    public function __construct(
        public readonly string $pluginname,
        public readonly string $title,
        public readonly string $reason,
        public readonly string $hint,
        public readonly int $occurrences,
        public readonly int $ranking,
        public readonly string $logohtml
    ) {
    }

    /**
     * Converts an stdClass to this data class
     *
     * @param stdClass $obj stdClass object
     * @return activity_ranking data object, containing the stdClass data
     */
    public static function from_stdclass(stdClass $obj): activity_ranking {
        return new self(
            $obj->pluginname,
            $obj->title ?? "",
            $obj->reason ?? "",
            $obj->hint ?? "",
            $obj->occurrences ?? 0,
            $obj->ranking ?? 0,
            $obj->logohtml ?? ""
        );
    }
}
