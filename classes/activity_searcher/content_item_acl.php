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

use core_course\local\entity\content_item;
use core_course\local\repository\caching_content_item_readonly_repository;
use core_course\local\repository\content_item_readonly_repository;
use core_course\local\repository\content_item_readonly_repository_interface;

/**
 * Anti Corruption Layer for fetching content items.
 *
 * @author Konrad Ebel <konrad.ebel@oncampus.de>
 * @copyright 2025, oncampus GmbH
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_item_acl {
    /** @var content_item_readonly_repository_interface Repo for fetching content items */
    private content_item_readonly_repository_interface $repository;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->repository = new caching_content_item_readonly_repository(
            \cache::make('core', 'user_course_content_items'),
            new content_item_readonly_repository()
        );
    }

    /**
     * Returns an array of all content items for a user in a course
     *
     * @return content_item[] Array of all enabled activities
     */
    public function get_enabled_activity_names(int $courseid, int $userid): array|null {
        global $DB;
        $user = $DB->get_record('user', ['id' => $userid], strictness: MUST_EXIST);
        $course = get_course($courseid);
        return $this->repository->find_all_for_course($user, $course);
    }

    /**
     * Returns an array of all content items
     *
     * @return content_item[] Array of all enabled activities
     */
    public function get_all() {
        return $this->repository->find_all();
    }
}
