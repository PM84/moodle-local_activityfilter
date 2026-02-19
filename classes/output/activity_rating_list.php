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

namespace local_activityfilter\output;

use coding_exception;
use core\output\renderer_base;
use renderable;
use templatable;

/**
 * UI-Component for a list of activity ratings.
 *
 * @author Konrad Ebel <konrad.ebel@oncampus.de>
 * @copyright 2025, oncampus GmbH
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_rating_list implements renderable, templatable {
    /** @var array[] List of activity ratings data arrays */
    private readonly array $activityratings;

    /**
     * Constructor.
     *
     * @param array[] $activityratings List of activity ratings data
     */
    public function __construct(
        array $activityratings
    ) {
        $this->activityratings = $activityratings;
    }

    /**
     * Render by using class activity_rating_box
     *
     * @param renderer_base|null $output Base renderer
     * @return array[] Variables for template (Activity ratings)
     * @throws coding_exception
     */
    public function export_for_template(?renderer_base $output = null): array {
        $exported = [];
        $i = 1;

        foreach ($this->activityratings as $activity) {
            $activity['id'] = $i;
            $box = new activity_rating_box($activity);
            $exported[] = $box->export_for_template($output);
            $i++;
        }

        return [
            'activityresponses' => $exported,
        ];
    }
}
