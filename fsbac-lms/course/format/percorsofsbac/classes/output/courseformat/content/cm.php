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
 * Contains the default activity list from a section.
 *
 * @package   format_percorsofsbac
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_percorsofsbac\output\courseformat\content;

use renderer_base;
use stdClass;

use core_courseformat\output\local\content\cm as cm_base;

/**
 * Base class to render a course module inside a course format.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cm extends cm_base {

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $CFG, $USER, $COURSE;

        $data = parent::export_for_template($output);

        // se sei uno studente, non devi vedere le attività subcourse nelle sezioni del corso
        $ismoduleavailable = true;
        if ($CFG->theme == "fsbac") {
            if ($this->mod->modname == "subcourse") {
                $context = \context_course::instance($COURSE->id);
                $roles = get_user_roles($context, $USER->id, true);
                foreach ($roles as $role) {
                    if ($role->shortname == "student") {
                        $ismoduleavailable = false;
                        break;
                    }
                }
            }
        }
        $data->ismoduleavailable = $ismoduleavailable;

        return $data;
    }

    /**
     * Returns the output class template path.
     *
     * This method redirects the default template when the section activity item is rendered.
     */
    public function get_template_name(renderer_base $renderer): string {
        return 'format_percorsofsbac/local/content/cm';
    }

}
