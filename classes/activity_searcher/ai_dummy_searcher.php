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

use Exception;
use local_activityfilter\activity_searcher\contracts\i_activity_searcher;

/**
 * Gives back dummy replies for activity searches
 *
 * @author Konrad Ebel <konrad.ebel@oncampus.de>
 * @copyright 2025, oncampus GmbH
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ai_dummy_searcher implements i_activity_searcher {
    /**
     * Return dummy reply
     *
     * @param string $request Ignored request
     * @return contracts\activity_ranking[] Stale dummy reply for activity search
     * @throws Exception
     */
    public function filter_activities(string $request): array {
        $response = file_get_contents(__DIR__ . '/dummydata.json');
        $decoded = json_decode($response, true);
        if ($decoded === null) {
            throw new Exception("Json encode error: " . json_last_error_msg());
        }
        return $decoded;
    }
}
