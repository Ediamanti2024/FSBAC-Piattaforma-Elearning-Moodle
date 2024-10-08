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
 *
 * @package    format_corsofsbac
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

class format_corsofsbac_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function unenrol_user_from_course_parameters() {

        return new external_function_parameters(
            array('userid' => new external_value(PARAM_INT, 'userid', VALUE_REQUIRED),
                    'courseid' => new external_value(PARAM_INT, 'courseid', VALUE_REQUIRED)
            )
        );
    }

    public static function unenrol_user_from_course($userid, $courseid) {

        global $DB;

        // Validate parameter
        $params = self::validate_parameters(
            self::unenrol_user_from_course_parameters(),
            array('userid' => $userid,
                    'courseid' => $courseid
            )
        );

        $userid = $params['userid'];
        $courseid = $params['courseid'];

        $sql = "SELECT ue.id, ue.enrolid, e.enrol
                  FROM {user_enrolments} ue
                  JOIN {enrol} e
                    ON ue.enrolid = e.id
                 WHERE ue.userid = ?
                   AND e.courseid = ?
                   AND e.enrol IN ('fsbac', 'meta')";
        $userenrolments = $DB->get_records_sql($sql, array($userid, $courseid));
        if (!empty($userenrolments)) {
            foreach ($userenrolments as $userenrolment) {
                $instance = $DB->get_record('enrol', array('id' => $userenrolment->enrolid));
                $plugin = enrol_get_plugin($userenrolment->enrol);
                $plugin->unenrol_user($instance, $userid);
            }
        }

        return null;

    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function unenrol_user_from_course_returns() {

        return null;

    }

}
