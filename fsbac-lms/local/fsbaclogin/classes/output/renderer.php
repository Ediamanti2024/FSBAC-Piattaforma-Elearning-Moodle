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
 * @package    local_fsbaclogin
 * @copyright  2021 Ariadne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fsbaclogin\output;
defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use renderable;

class renderer extends plugin_renderer_base {

    /**
     * Defer to template.
     * @param renderable $report_questions
     * @return string
     */
    public function render_login($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_fsbaclogin/login', $data);
    }

    public function render_confirmlogin($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_fsbaclogin/confirmlogin', $data);
    }

    public function render_language_menu($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_fsbaclogin/language_menu', $data);
    }

    public function render_user_profile($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_fsbaclogin/user_profile', $data);
    }

    public function render_blocked_course_enrollment($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_fsbaclogin/blocked_course_enrollment', $data);
    }

    public function render_blocked_profilazione($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_fsbaclogin/blocked_profilazione', $data);
    }

    public function render_completed_dicolab_course($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_fsbaclogin/completed_dicolab_course', $data);
    }

    public function render_completed_fsbac_course($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('local_fsbaclogin/completed_fsbac_course', $data);
    }
	
}
