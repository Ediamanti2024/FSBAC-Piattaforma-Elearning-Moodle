<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    local_ard_notification
 * @copyright  2021-2022 Ariadne Digital
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ard_notification\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use renderable;

class renderer extends plugin_renderer_base {

    /**
     * Render bulk monitoring page
     */
    public function render_bulk_monitoring(renderable $renderer) {
        $data = $renderer->export_for_template($this);
        return parent::render_from_template('local_ard_notification/bulk_monitoring', $data);
    }

    /**
     * Render bulk users monitoring page
     */
    public function render_bulk_users_monitoring(renderable $renderer) {
        $data = $renderer->export_for_template($this);
        return parent::render_from_template('local_ard_notification/bulk_users_monitoring', $data);
    }

    /**
     * Render user bulks monitoring page
     */
    public function render_user_bulks_monitoring(renderable $renderer) {
        $data = $renderer->export_for_template($this);
        return parent::render_from_template('local_ard_notification/user_bulks_monitoring', $data);
    }

}
