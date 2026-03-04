<?php

namespace local_activityfilter\external;

use core\di;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use moodle_database;

/**
 * Webservice to get most used content item occurrence count
 */
class get_max_content_item_occurrence extends external_api {
    /**
     * Get most used content item occurrence count
     *
     * @return int most used content item occurrence count
     *             0 if no content item exists
     */
    public static function execute(): int {
        $db = di::get(moodle_database::class);
        $record = $db->get_record_sql(
            'SELECT COUNT(*) AS count
                FROM {course_modules} cm
                JOIN {modules} m ON m.id = cm.module
                GROUP BY m.name
                ORDER BY COUNT(*) DESC',
            strictness: IGNORE_MULTIPLE
        );

        return $record ? $record->count : 0;
    }

    /**
     * Get expected input parameter structure for webservice
     *
     * @return external_function_parameters Function parameter structure
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([]);
    }

    /**
     * Get expected output format of webservice
     *
     * @return external_value expected output format
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_INT, 'Max content item occurrence count');
    }
}
