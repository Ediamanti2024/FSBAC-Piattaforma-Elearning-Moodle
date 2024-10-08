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
 * Output cmitem for the format_corsofsbac plugin.
 *
 * @package   format_corsofsbac
 * @copyright Year, You Name <your@email.address>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_corsofsbac\output\courseformat\content\section;

use core_courseformat\output\local\content\section\cmitem as cmitem_base;
use stdClass;
use renderer_base;

class cmitem extends cmitem_base {

    /**
     * Returns the output class template path.
     *
     * This method redirects the default template when the section activity item is rendered.
     */
    public function get_template_name(\renderer_base $renderer): string {
        return 'format_corsofsbac/local/content/section/cmitem';
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output) : stdClass {
        global $CFG, $COURSE, $USER;

        $data = parent::export_for_template($output);

        $ismoduleavailable = true;

        if ($CFG->theme == "fsbac") {
            if ($this->mod->modname == "certificate") {
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
}
