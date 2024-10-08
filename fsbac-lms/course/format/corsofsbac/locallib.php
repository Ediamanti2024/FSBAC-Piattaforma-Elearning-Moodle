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
 * This file contains main class for Corsofsbac course format.
 *
 * @since     Moodle 2.0
 * @package   format_corsofsbac
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the course image url
 *
 * @param int $courseid The id of the course
 * @return \moodle_url course image url
 */
function get_course_image($courseid) {
    global $CFG;
    $url = '';
    require_once($CFG->libdir . '/filelib.php');

    $context = \context_course::instance($courseid);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', 0);

    foreach ($files as $f) {
        if ( $f->is_valid_image() ) {
            $url = \moodle_url::make_pluginfile_url($f->get_contextid(), $f->get_component(), $f->get_filearea(), null, $f->get_filepath(), $f->get_filename(), false);
        }
    }
    return $url;
}

/**
 * Returns the course image url
 *
 * @param int $courseid The id of the course
 * @return array course custom fields
 */
function get_course_customfields($courseid) {
    global $DB;
    $coursecontextinstance = \context_course::instance($courseid);

    $sql = "SELECT cf.shortname, cd.value, cf.configdata
            FROM {customfield_data} cd
            JOIN {customfield_field} cf
                ON cf.id = cd.fieldid
            WHERE cd.contextid = ?";
    $coursecustomfields = $DB->get_records_sql($sql, array($coursecontextinstance->id));
    return $coursecustomfields;
}

function is_course_a_path($courseid) {
    global $DB;

    $format = $DB->get_field("course", "format", array("id" => $courseid));
    if ($format == "percorsofsbac") {
        return true;
    }
    return false;
}

function convert_minutes_into_hours_minutes(int $minutes) : string {
    $hours = floor($minutes / 60);
    $minutes = $minutes % 60;
    if ($hours == 0) {
        if ($minutes == 0) {
            return  "0h 0m";
        } else {
            return  $minutes . "m";
        }
    } else {
        if ($minutes == 0) {
            return  $hours . "h";
        } else {
            return  $hours . "h " . $minutes . "m";
        }
    }
}

/**
 * Returns true if che specified user is logged and enrolled to the specified course else returns false
 *
 * @param int|stdClass $user if null $USER is used, otherwise user object or id expected
 * @param int $courseid The id of the course
 */
function is_user_logged_and_enrolled_to_course($user, $courseid) {
    $coursecontextinstance = \context_course::instance($courseid);
    if (isloggedin()) {
        if (!isguestuser() && is_enrolled($coursecontextinstance, $user, '', true)) {
            return true;
        }
    }
    return false;
}

/**
 * Returns the sum of durations of courses in the specified path
 *
 * @param int $courseid The id of the path
 */

function get_path_total_duration($courseid) {
    global $DB;

    $pathduration = 0;
    $pathcustomfields = get_course_customfields($courseid);
    if (isset($pathcustomfields["duration"])) {
        if ($pathcustomfields["duration"]->value != "") {
            $pathduration = $pathcustomfields["duration"]->value;
            return $pathduration;
        }
    }
    $subcourses = $DB->get_records("subcourse", array("course" => $courseid));
    if (!empty($subcourses)) {
        foreach ($subcourses as $subcourse) {
            $coursecustomfields = get_course_customfields($subcourse->refcourse);
            if (isset($coursecustomfields["duration"])) {
                if ($coursecustomfields["duration"]->value != "") {
                    $pathduration += $coursecustomfields["duration"]->value;
                }
            }
        }
    }
    return $pathduration;
}

/**
 * Returns the course completion percentage, the course progress state, the number of total modules and the number of completed modules by a certain user
 *
 * @param \stdClass $course Moodle course object
 * @param int $userid The id of the user
 * @param int $sectionnum The section number in course
 * @return \stdClass completion percentage, progress state, total modules and completed modules
 */
function is_course_section_complete($course, $userid, $sectionnum) {
    global $DB;

    $completion = new \completion_info($course);
    $modules = $completion->get_activities();
    $completedsection = false;
    $totalcms = 0;
    $completedcms = 0;
    foreach ($modules as $module) {
        $datacompletion = $completion->get_data($module, true, $userid);
        $sql = "SELECT cm.id, cm.section, cs.section AS sectionnum
                  FROM {course_sections} cs
                  JOIN {course_modules} cm
                    ON cm.section = cs.id
                 WHERE cm.id = ?";
        $cmsectioninfo = $DB->get_record_sql($sql, array($module->id));
        if ($cmsectioninfo->sectionnum == $sectionnum) {
            $totalcms += 1;
            if (($datacompletion->completionstate != COMPLETION_INCOMPLETE) && ($datacompletion->completionstate != COMPLETION_COMPLETE_FAIL)) {
                $completedcms += 1;
            }
        }
    }
    if ($totalcms > 0) {
        if ($totalcms == $completedcms) {
            $completedsection = true;
        }
    }

    return $completedsection;
}

function is_user_enrolled_to_course($user, $courseid) {
    $coursecontextinstance = \context_course::instance($courseid);
    if (!isguestuser() && is_enrolled($coursecontextinstance, $user, '', true)) {
        return true;
    }
    return false;
}
