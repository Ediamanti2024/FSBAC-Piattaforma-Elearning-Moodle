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
 * Event observers supported by this module
 *
 * @package    format_percorsofsbac
 * @copyright  2017 Ariadne {@link http://www.ariadne.it}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class format_percorsofsbac_observer {
    /**
     * Observer for user_loggedin event. If the user logged ,start control in
     * local_pearsonprivacy_usr_wrn in order to delete user logged
     * @param $event
     * @return void
     */

    public static function created_subcourse_in_path($event) {

        global $DB, $CFG;

        $data = $event->get_data();
        $courseid = $data["courseid"];
        $coursemodulename = $data["other"]["modulename"];
        $timecreated = $data["timecreated"];
        $courseformat = course_get_format($courseid)->get_format();
        $coursemoduleid = $data["contextinstanceid"];

        if ($courseformat == "percorsofsbac") {
            if ($coursemodulename == "subcourse") {
                $sql = "SELECT *
                          FROM {course_modules} cm
                          JOIN {subcourse} s
                            ON s.id = cm.instance
                         WHERE cm.id = ?";
                $subcourse = $DB->get_record_sql($sql, array($coursemoduleid));
                $sql = "SELECT *
                          FROM {percorsofsbac_follow_path}
                         WHERE followed = 1
                           AND courseid = ?
                           AND timemodified < ?";
                $usersto = $DB->get_records_sql($sql, array($courseid, $timecreated));
                if (!empty($usersto)) {
                    foreach ($usersto as $userto) {
                        $user = $DB->get_record("user", array("id" => $userto->userid));
                        $emailto = $user->email;
                        $emailfrom = "helpdesk@fondazionescuolapatrimonio.it";
                        $pathname = $DB->get_field("course", "fullname", array("id" => $courseid));
                        $pathlink = "$CFG->wwwroot/course/view.php?id=$courseid";
                        $subject = get_string("new_course_added_to_percorso_subject", "format_percorsofsbac", $pathname);
                        $message = get_string("new_course_added_to_percorso_message", "format_percorsofsbac", array("firstname" => $user->firstname,
                                                                                                                    "pathname" => $pathname,
                                                                                                                    "pathlink" => $pathlink,
                                                                                                                    "emailfrom" => $emailfrom));
                        self::send_email($emailfrom, "", $emailto, "", $subject, $message);
                    }
                }
            }
        }
    }

    private static function send_email($from_email, $from_alias, $to_email, $to_alias, $subject, $message) {

        // Mailer
        $mail = get_mailer();
        // From
        $mail->setFrom($from_email, $from_alias);
        // To
        $mail->addAddress($to_email, $to_alias);

        // Message content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        // Send
        $mail->send();

        return $mail->ErrorInfo;
    }

    /**
     * Handle the course_completed event.
     *
     * Notify all subcourse instances with the relevant completion rule enabled
     * that the user completed the referenced course - so that  they can be eventually
     * marked as completed, too.
     *
     * @param \core\event\course_completed $event
     * @return void
     */
    public static function subcourse_completed(\core\event\course_completed $event) {

        global $CFG, $DB;

        require_once($CFG->dirroot.'/lib/completionlib.php');

        $courseid = $event->courseid;
        $userid = $event->relateduserid;

        // Get all subcourses that have the completed course as the referenced one.
        $subcourses = $DB->get_records('subcourse', array('refcourse' => $courseid, 'completioncourse' => 1));

        if (empty($subcourses)) {
            // No subcourse interested in this.
            return;
        }

        // Load the courses where the subcourses are located in.
        $courseids = [];

        foreach ($subcourses as $subcourse) {
            $courseids[$subcourse->course] = true;
        }

        $courses = $DB->get_records_list('course', 'id', array_keys($courseids), '', '*');

        foreach ($subcourses as $subcourse) {
            $course = $courses[$subcourse->course];
            $cm = get_coursemodule_from_instance('subcourse', $subcourse->id, $course->id);
            $completion = new completion_info($course);

            if ($completion->is_enabled($cm)) {
                // Notify the subcourse to check the completion status.
                $completion->update_state($cm, COMPLETION_COMPLETE, $userid);
            }
        }
    }
}
