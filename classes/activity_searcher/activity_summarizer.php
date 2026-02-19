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

use coding_exception;
use core_plugin_manager;
use dml_exception;
use local_activityfilter\local\plugin_description;
use moodle_database;
use RuntimeException;

/**
 * Collects more infos about activity plugins and converts
 * them into the AI format.
 *
 * @author Konrad Ebel <konrad.ebel@oncampus.de>
 * @copyright 2025, oncampus GmbH
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_summarizer implements i_activity_summarizer {
    /**
     * Constructor.
     *
     * @param moodle_database $db Database
     * @param core_plugin_manager $pluginmanager Moodle plugin manager
     * @param activity_plugins $activityplugins Activity plugin manager
     * @param i_text_compressor $compressor Text compressor
     */
    public function __construct(
        private readonly moodle_database $db,
        private readonly core_plugin_manager $pluginmanager,
        private readonly activity_plugins $activityplugins,
        private readonly i_text_compressor $compressor,
    ) {
    }

    /**
     * Combines all activity data for an option summary
     *
     * @return activity_data[] List of activity data
     * @throws coding_exception
     * @throws dml_exception
     */
    public function get_activity_data(): array {
        $activitynames = $this->activityplugins->get_enabled_activity_names();
        if (empty($activitynames)) {
            throw new RuntimeException("No activity data found");
        }

        $data = [];
        foreach ($activitynames as $activityname) {
            $data[] = new activity_data(
                $activityname,
                get_string('pluginname', "mod_" . $activityname),
                $this->compressor->compress(
                    plugin_description::get($activityname)
                ),
                $this->get_activity_usage_amount($activityname),
            );
        }

        return $data;
    }

    /**
     * Gets the total usage amount of the given plugin
     *
     * @param string $activityname Activity name of the plugin
     * @return int Total usage amount in moodle
     * @throws dml_exception
     */
    private function get_activity_usage_amount(string $activityname): int {
        return $this->db->count_records_sql(
            'SELECT COUNT(1)
             FROM {course_modules} cm
             LEFT JOIN {modules} m
                ON m.id = cm.module
             WHERE m.name = :activityname',
            ['activityname' => $activityname]
        );
    }

    /**
     * Get all activities, choose able in the activity chooser (including disabled)
     *
     * @return array List of all choose able activity plugins
     */
    public function get_activities(): array {
        $plugins = $this->pluginmanager->get_plugins_of_type('mod');
        unset($plugins["subsection"]);
        return $plugins;
    }
}
