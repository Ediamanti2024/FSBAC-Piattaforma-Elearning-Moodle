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
 * Contains the default section controls output class.
 *
 * @package   format_percorsofsbac
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_percorsofsbac\output\courseformat\content;

use core_courseformat\base as course_format;
use core_courseformat\output\local\content\section as section_base;
use stdClass;

/**
 * Base class to render a course section.
 *
 * @package   format_percorsofsbac
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class section extends section_base {

    /** @var course_format the course format */
    protected $format;

    public function export_for_template(\renderer_base $output): stdClass {
        global $CFG, $COURSE, $USER;

        $format = $this->format;

        $data = parent::export_for_template($output);

        if (!$this->format->get_section_number()) {
            $addsectionclass = $format->get_output_classname('content\\addsection');
            $addsection = new $addsectionclass($format);
            $data->numsections = $addsection->export_for_template($output);
            $data->insertafter = true;
        }

        // se sei uno studente, non devi vedere nessuna sezione del corso
        $issectionavailable = true;
        if ($CFG->theme == "fsbac") {
            $context = \context_course::instance($COURSE->id);
            if ($USER->id == 1) {
                $issectionavailable = false;
            } else {
                $roles = get_user_roles($context, $USER->id, true);
                foreach ($roles as $role) {
                    if ($role->shortname == "student") {
                        $issectionavailable = false;
                        break;
                    }
                }
            }
        }
        $data->issectionavailable = $issectionavailable;

        return $data;
    }

    /**
     * Returns the output class template path.
     *
     * This method redirects the default template when the course section is rendered.
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'format_percorsofsbac/local/content/section';
    }
}
