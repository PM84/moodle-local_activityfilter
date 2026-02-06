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

use local_activityfilter\activity_searcher\activity_data;
use local_activityfilter\activity_searcher\activity_plugins;
use local_activityfilter\activity_searcher\activity_summarizer;

class activity_summarizer_test extends advanced_testcase {
    private activity_summarizer $activitysummerizer;

    public function setUp(): void {
        global $DB;
        $this->resetAfterTest();

        $pluginmanager = $this->createMock(core_plugin_manager::class);
        $pluginmanager->method('get_plugins_of_type')
                      ->willReturn([
                        'myplugin' => [],
                        'subsection' => [],
                      ]);

        $activities = $this->createMock(activity_plugins::class);
        $activities->method('get_enabled_activity_names')
                   ->willReturn(['myplugin' => 'myplugin']);

        $this->activitysummerizer = new activity_summarizer(
            $DB,
            $pluginmanager,
            $activities
        );
    }

    public function test_get_activities(): void {
        $expected = [
            'myplugin' => [],
        ];

        $activities = $this->activitysummerizer->get_activities();

        $this->assertEquals($expected, $activities);
    }

    public function test_get_activity_data(): void {
        $expected = [
            new activity_data(
                'myplugin',
                'my ai help',
                0
            ),
        ];
        set_config('ai_hint_myplugin', 'my ai help', 'local_activityfilter');

        $data = $this->activitysummerizer->get_activity_data();

        $this->assertEquals($expected, $data);
    }
}
