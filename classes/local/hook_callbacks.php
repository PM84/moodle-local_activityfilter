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

namespace local_activityfilter\local;

use context_system;
use core\hook\output\before_html_attributes;
use core\hook\di_configuration;
use core_plugin_manager;
use local_activityfilter\activity_searcher\ai_dummy_searcher;
use local_activityfilter\activity_searcher\content_item_manager;
use local_activityfilter\activity_searcher\activity_summarizer;
use local_activityfilter\activity_searcher\ai_searcher;
use local_activityfilter\activity_searcher\contracts\i_activity_searcher;
use local_activityfilter\activity_searcher\i_activity_summarizer;
use local_activityfilter\activity_searcher\i_text_compressor;
use local_activityfilter\activity_searcher\stopword_remover;
use moodle_database;

/**
 * Hook Callback definitions.
 *
 * @author Konrad Ebel <konrad.ebel@oncampus.de>
 * @copyright 2025, oncampus GmbH
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * Dependency injection configuration method
     *
     * @param di_configuration $hook DI Instance
     * @return void
     */
    public static function di_configuration(di_configuration $hook): void {
        $hook->add_definition(
            id: i_text_compressor::class,
            definition: function (): i_text_compressor {
                return new stopword_remover();
            }
        );

        $hook->add_definition(
            id: i_activity_summarizer::class,
            definition: function (): i_activity_summarizer {
                return new activity_summarizer(
                    new content_item_manager(),
                );
            }
        );

        $hook->add_definition(
            id: i_activity_searcher::class,
            definition: function (
                i_activity_summarizer $summerizer,
            ): i_activity_searcher {
                if (get_config('local_activityfilter', 'dummy_mode')) {
                    return new ai_dummy_searcher();
                }

                return new ai_searcher(
                    $summerizer,
                    new stopword_remover()
                );
            }
        );
    }

    /**
     * Checks if the AI backend is available based on the configured backend setting.
     *
     * @return bool True if the configured backend is available.
     */
    private static function is_ai_available(): bool {
        if (get_config('local_activityfilter', 'dummy_mode')) {
            return true;
        }

        $backend = get_config('local_activityfilter', 'backend');
        if ($backend === 'local_ai_manager') {
            if (!class_exists('\local_ai_manager\local\tenant')) {
                return false;
            }
            $tenant = \core\di::get(\local_ai_manager\local\tenant::class);
            return $tenant->is_tenant_allowed();
        }

        // Default: core_ai_subsystem.
        $manager = \core\di::get(\core_ai\manager::class);
        return $manager->is_action_available(\core_ai\aiactions\generate_text::class);
    }

    /**
     * Injects JS to add activity filter to course section menu.
     *
     * @param before_html_attributes $hook After config hook.
     * @return void
     */
    public static function before_html_attributes(before_html_attributes $hook): void {
        if (!self::is_ai_available()) {
            return;
        }

        global $PAGE;
        if (
            !has_all_capabilities([
                'local/activityfilter:filter_activities',
                'local/activityfilter:get_max_content_item_occurrence',
            ], $PAGE->context)
        ) {
            return;
        }

        $PAGE->requires->js_call_amd(
            'local_activityfilter/auto_resize_text_field',
            'init'
        );
        $PAGE->requires->js_call_amd(
            'local_activityfilter/content_item_filter_modal',
            'init'
        );
    }

    /**
     * Provide additional information about which purposes are being used by this plugin.
     *
     * @param \local_ai_manager\hook\purpose_usage $hook The purpose_usage hook object.
     */
    public static function handle_purpose_usage(\local_ai_manager\hook\purpose_usage $hook): void {
        $hook->set_component_displayname(
            'local_activityfilter',
            get_string('pluginname', 'local_activityfilter')
        );
        $hook->add_purpose_usage_description(
            'singleprompt',
            'local_activityfilter',
            get_string('purposeplacedescription_singleprompt', 'local_activityfilter')
        );
    }
}
