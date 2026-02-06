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
 */
class activity_ranking {
    /**
     * Constructor
     *
     * @param string $pluginname Name of the mod plugin
     * @param string $reason Reason why this plugin fits to the request
     * @param string $hint Way how to use this plugin, so it can fulfill the request
     * @param int $popularity How often it's used in the moodle 0-10
     * @param int $ranking Ranking, how much it fits to the request 0-10
     */
    public function __construct(
        public readonly string $pluginname,
        public readonly string $reason,
        public readonly string $hint,
        public readonly int $occurences,
        public readonly int $ranking,
    ) {
    }

    public static function from_stdclass(stdClass $obj): activity_ranking {
        return new self(
            $obj->pluginname,
            $obj->reason ?? "",
            $obj->hint ?? "",
            $obj->occurences ?? 0,
            $obj->ranking ?? 0,
        );
    }
}
