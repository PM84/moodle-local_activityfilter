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

namespace local_activityfilter\test\unit;

use core_course\local\entity\content_item;
use core_course\local\entity\string_title;
use moodle_url;

/**
 * Generator for content items
 *
 * @author Konrad Ebel <konrad.ebel@oncampus.de>
 * @copyright 2026, oncampus GmbH
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_item_generator {
    /**
     * Generate content item
     *
     * @param string $name Name of content item
     * @param array $content Content to overwrite
     * @return content_item Content item
     * @throws \Random\RandomException
     */
    public static function generate_content_item(
        string $name,
        array $content,
    ): content_item {
        return new content_item(
            random_int(1, 100),
            $name,
            new string_title($content['title'] ?? ucfirst($name)),
            new moodle_url($content['url'] ?? ""),
            $content['icon'] ?? "",
            $content['help'] ?? "",
            $content['archtype'] ?? "",
            $content['componentname'] ?? "",
            $content['purpose'] ?? ""
        );
    }
}
