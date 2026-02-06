# Activity Filter

**Activity Filter** ist ein lokales Plugin für Moodle, welches dabei helfen soll passende Aktivitäten
auszuwählen. Mithilfe des AI Subsystems werden Nutzeranfragen verarbeitet und passende Aktivitäten heraus-
gesucht.

## Features

- Vorschlagen von Aktivitäten für bestimmte Aufgaben
- Einstellbarer System- und Pluginprompt

## Installation

1. Clone das Repository in das `/local/pluginname`-Verzeichnis der Moodle-Installation.
2. Ruf' **Website-Administration → Systemnachrichten** auf, um die Installation anzustoßen oder führ'
   `admin/cli/upgrade.php` aus.

### Voraussetzungen

- Abhängig vom AI-Subsystem (generate_text)

## Konfiguration

Nach Installation kann das Plugin auf folgenden Weg konfiguriert werden:  
**Website-Administration → Plugins → Lokale Plugins → Plugin Name**

Vor Inbetriebnahme des Plugins müssen folgende Einstellungen gesetzt werden:

- AI Subsystem muss aktiviert sein, mit mindestens einer Textgenerierungsoption

Einstellungen:

- Systemprompt: Mithilfe des Systemprompts können die Ergebnisse der AI passend konfiguriert werden.

## Nutzung

- Navigiere zu einem Kurs.
- Öffne den Button um eine Aktivität hinzufügen.
- Klicke auf "KI-Aktivitätensuche öffnen"

## Rechte

-

## Cronjobs

-

## Web Services

Dieses Plugin stellt folgende Webservice-Funktionen zur Verfügung:

| Webservice-Funktion                      | Beschreibung                                           |
|------------------------------------------|--------------------------------------------------------|
| `local_activityfilter_filter_activities` | Stellt eine Anfrage an die KI für eine Ratingübersicht |
| `local_activityfilter_prepare_results`   | Erstellt die Datestellung für die Ratingübersicht      |

## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/local/activityfilter

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## License ##

2025 Team 13 <team13@mailbox.org>
& 2025 Konrad Ebel <konrad.ebel@oncampus.de>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program. If not, see <https://www.gnu.org/licenses/>.
