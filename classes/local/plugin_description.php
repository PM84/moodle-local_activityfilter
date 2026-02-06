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

class plugin_description {
    public static function get(string $pluginname): string {
        $usedefault = get_config('local_activityfilter', "ai_hint_{$pluginname}_use_default");
        if ($usedefault) {
            return self::get_default($pluginname);
        }

        return get_config('local_activityfilter', 'ai_hint_' . $pluginname) ?: '';
    }

    public static function get_default(string $pluginname): string {
        if ($pluginname == 'booking') {
            return 'Mit Buchung können Teilnehmer/innen Termine, Veranstaltungen oder Ressourcen eigenständig reservieren. Trainer/innen legen dafür Zeitfenster oder Optionen fest und behalten den Überblick über alle Buchungen. Diese Aktivität eignet sich beispielsweise für Elternsprechtage oder Projekttermine.';
        }

        if ($pluginname == 'mootimeter') {
            return 'Mit Mootimeter können interaktive Umfragen, Wortwolken und Abstimmungen unmittelbar in den Moodle-Kurs eingebunden werden. Die Teilnehmer/innen geben ihre Antworten in Echtzeit ein, die Ergebnisse werden dabei live visualisiert. Diese Aktivität unterstützt dabei, Stimmungen einzufangen, Vorwissen zu aktivieren oder Feedback auf einfache Weise einzuholen.';
        }

        return get_string('modulename_help', $pluginname);
    }
}
