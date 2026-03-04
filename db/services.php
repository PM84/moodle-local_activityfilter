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

/**
 * Plugin services are defined here
 *
 * @category    string
 * @copyright   2025 Team 13 <team13@mailbox.org>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_activityfilter\external\filter_activities;
use local_activityfilter\external\get_max_content_item_occurrence;

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_activityfilter_filter_activities' => [
        'classname' => filter_activities::class,
        'description' => 'Filters all active modules by given string using LLM',
        'type' => 'read',
        'ajax' => true,
    ],
    'local_activityfilter_get_max_content_item_occurrence' => [
        'classname' => get_max_content_item_occurrence::class,
        'description' => 'Get most used content item occurrence count',
        'type' => 'read',
        'ajax' => true,
    ],
];
