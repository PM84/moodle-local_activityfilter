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

use core\di;
use core\test\handler_one;
use core_course\local\entity\content_item;
use dml_exception;
use JsonSerializable;
use local_activityfilter\local\overwritten_content_item_description;
use moodle_database;

/**
 * Content type data, required for an AI rating.
 *
 * @author Konrad Ebel <konrad.ebel@oncampus.de>
 * @copyright 2025, oncampus GmbH
 * @license https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class activity_data implements JsonSerializable {
    /** @var moodle_database Moodle Database */
    private readonly moodle_database $db;
    /** @var i_text_compressor Text Compressor */
    private readonly i_text_compressor $compressor;

    /**
     * Constructor.
     *
     * @param content_item $contentitem Content item
     */
    public function __construct(
        private content_item $contentitem,
    ) {
        $this->db = di::get(moodle_database::class);
        $this->compressor = di::get(i_text_compressor::class);
    }

    /**
     * Content id name
     *
     * @return string Get id name
     */
    public function get_name(): string {
        return $this->contentitem->get_name();
    }

    /**
     * Human-readable name of content item
     *
     * @return string
     */
    public function get_title(): string {
        return $this->contentitem->get_title()->get_value();
    }

    /**
     * Get logo as html
     *
     * @return string
     */
    public function get_logo_html(): string {
        return $this->contentitem->get_icon();
    }

    /**
     * Get plugin description or overwrite from local plugin
     *
     * @return string
     * @throws dml_exception
     */
    public function get_description(): string {
        $description = overwritten_content_item_description::get($this->contentitem->get_name());
        if (!$description) {
            $description = overwritten_content_item_description::clean_core_help($this->contentitem->get_help());
        }

        return $description;
    }

    /**
     * Get compressed plugin description (not human-readable, but ai-readable)
     *
     * @return string
     * @throws dml_exception
     */
    public function get_compressed_description(): string {
        return $this->compressor->compress($this->get_description());
    }

    /**
     * Gets the total usage amount of the given plugin
     *
     * @param string $activityname Activity name of the plugin
     * @return int Total usage amount in moodle
     * @throws dml_exception
     */
    public function get_usage_amount(): int {
        return $this->db->count_records_sql(
            'SELECT COUNT(1)
         FROM {course_modules} cm
         LEFT JOIN {modules} m
            ON m.id = cm.module
         WHERE m.name = :activityname',
            ['activityname' => $this->contentitem->get_component_name()]
        );
    }

    /**
     * Converts data as json for the AI
     *
     * @return array Exported json serializable data
     */
    public function jsonSerialize(): array {
        return [
            'name' => $this->contentitem->get_name(),
            'displayname' => $this->contentitem->get_title()->get_value(),
            'help' => $this->get_compressed_description(),
        ];
    }
}
