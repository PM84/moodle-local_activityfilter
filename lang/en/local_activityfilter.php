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
 * Plugin strings are defined here.
 *
 * @category    string
 * @copyright   2025 Team 13 <team13@mailbox.org>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Activity Filter';

$string['settings:systemprompt'] = 'System prompt';
$string['settings:systemprompt_desc'] = 'Defines the system prompt used to guide the behaviour and responses of the AI model.';
$string['settings:dummy_mode'] = 'Dummy mode';
$string['settings:dummy_mode_desc'] = 'Enables a development mode that simulates AI responses without contacting a live AI service.';
$string['settings:plugin_ai_hint'] = 'Guidance text that informs the AI about the plugin’s features and behaviour.';
$string['settings:use_default'] = 'Use default ({$a})';
$string['settings:use_default_desc'] = 'Use default setting for the plugin {$a}';

$string['modal_title'] = 'Suggested Activities';
$string['promptdesc'] = 'What kind of activity would you like to find?';
$string['search'] = 'Search';
$string['hintcolumn'] = 'Description';
$string['reasoncolumn'] = 'Reason';
$string['popularitycolumn'] = 'Usage';
$string['noresults'] = 'No results yet - please enter a prompt to begin.';
$string['open_activityfilter'] = 'Open AI activity search';

$string['occurences:very_rare'] = 'very rare';
$string['occurences:rare'] = 'rare';
$string['occurences:moderately'] = 'moderately';
$string['occurences:often'] = 'often';
$string['occurences:very_frequent'] = 'very frequent';
