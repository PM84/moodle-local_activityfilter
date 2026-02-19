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

namespace local_activityfilter\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use local_activityfilter\output\activity_rating_list;

/**
 * External service for rendering plugin rating results.
 *
 * @author Konrad Ebel <konrad.ebel@oncampus.de>
 * @copyright 2025, oncampus GmbH
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prepare_results extends external_api {
    /**
     * Render given activity ratings
     *
     * @param array $items List of activity ratings
     * @return array Structure containing html code
     * @throws \core\exception\coding_exception
     * @throws \invalid_parameter_exception
     */
    public static function execute(array $items): array {
        global $OUTPUT;

        $params = self::validate_parameters(self::execute_parameters(), ['items' => $items]);

        $ratinglist = new activity_rating_list($params['items']);
        return ['html' => $OUTPUT->render($ratinglist)];
    }

    /**
     * Get expected input parameter structure for webservice
     *
     * @return external_function_parameters Function parameter structure
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'items' => new external_multiple_structure(new external_single_structure([
                'pluginname' => new external_value(PARAM_TEXT, 'module name (e.g. assign, wiki)'),
                'ranking'    => new external_value(PARAM_INT, '1..10'),
                'occurences' => new external_value(PARAM_INT, 'optional', VALUE_OPTIONAL),
                'hint'       => new external_value(PARAM_RAW, 'optional', VALUE_OPTIONAL),
                'reason'     => new external_value(PARAM_RAW, 'optional', VALUE_OPTIONAL),
            ])),
        ]);
    }

    /**
     * Get expected output format of webservice
     *
     * @return external_single_structure expected output format
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'html' => new external_value(PARAM_RAW, 'Rendered HTML for results'),
        ]);
    }
}
