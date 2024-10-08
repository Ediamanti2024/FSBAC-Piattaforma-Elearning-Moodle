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

use moodle_url;
use renderable;
use renderer_base;
use templatable;

class completed_fsbac_course implements renderable, templatable
{

    public $courseid;

    public function __construct($courseid)
    {

        $this->courseid = $courseid;
    }
    public function export_for_template(renderer_base $output)
    {

        global $DB;

        $data = array();

        $data["courseid"] = $this->courseid;
        $coursename = $DB->get_field("course", "fullname", array("id" => $this->courseid));
        $data["coursename"] = $coursename;

        $sql = "SELECT cm.id
                  FROM {course_modules} cm
                  JOIN {modules} m
                    ON m.id = cm.module
                 WHERE cm.course = ?
                   AND m.name = 'certificate'";
        $customcertsids = $DB->get_records_sql($sql, array($this->courseid));
        if (!empty($customcertsids)) {
            $customcertid = array_keys($customcertsids)[0];
            $data["customcertid"] = $customcertid;
        }

        $courses = \theme_fsbac\fsbac::get_corsi_fsbacCard($this->courseid);
        $data['fsbacCourses'] = $courses;
        return $data;
    }
}
