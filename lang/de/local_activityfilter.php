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
 * @copyright   Konrad Ebel <konrad.ebel@oncampus.de>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Aktivitäten-Filter';

$string['settings:systemprompt'] = 'System-Prompt';
$string['settings:systemprompt_desc'] = 'Legt den System-Prompt fest, der das Verhalten und die Antworten des KI-Modells steuert.';
$string['settings:dummy_mode'] = 'Dummy-Modus';
$string['settings:dummy_mode_desc'] = 'Aktiviert einen Entwicklungsmodus, in dem KI-Antworten simuliert werden, ohne einen echten KI-Dienst zu verwenden.';
$string['settings:plugin_ai_hint'] = 'Leittext, der der KI erklärt, welche Möglichkeiten dieses Plugin bietet.';
$string['settings:use_default'] = 'Verwende Standard ({$a})';
$string['settings:use_default_desc'] = 'Verwende die Standardeinstellung für {$a}';

$string['modal_title'] = 'Vorgeschlagene Aktivitäten';
$string['promptdesc'] = 'Nach welcher Art von Aktivität suchen Sie?';
$string['search'] = 'Suchen';
$string['hintcolumn'] = 'Beschreibung';
$string['reasoncolumn'] = 'Begründung';
$string['popularitycolumn'] = 'Häufigkeit';
$string['noresults'] = 'Noch keine Ergebnisse – bitte geben Sie zuerst eine Anfrage ein.';
$string['open_activityfilter'] = 'KI-Aktivitätensuche';

$string['occurences:very_rare'] = 'sehr selten';
$string['occurences:rare'] = 'selten';
$string['occurences:moderately'] = 'moderat';
$string['occurences:often'] = 'oft';
$string['occurences:very_frequent'] = 'sehr häufig';
