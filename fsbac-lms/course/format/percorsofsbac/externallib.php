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
 * @package    format_percorsofsbac
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;

class format_percorsofsbac_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function follow_unfollow_path_parameters() {

        return new external_function_parameters(
            array('courseid' => new external_value(PARAM_INT, 'courseid', VALUE_REQUIRED),
                  'userid' => new external_value(PARAM_INT, 'userid', VALUE_REQUIRED)
            )
        );
    }

    public static function follow_unfollow_path($courseid, $userid) {

        global $DB, $CFG;

        // Validate parameter
        $params = self::validate_parameters(
            self::follow_unfollow_path_parameters(),
            array('courseid' => $courseid,
            'userid' => $userid
            )
        );
        $courseid = $params['courseid'];
        $userid = $params['userid'];

        $user = $DB->get_record("user", array("id" => $userid));
        $emailto = $user->email;
        $emailfrom = "helpdesk@fondazionescuolapatrimonio.it";
        $pathname = $DB->get_field("course", "fullname", array("id" => $courseid));
        $pathlink = "$CFG->wwwroot/course/view.php?id=$courseid";
        $subject = get_string("follow_percorso_subject", "format_percorsofsbac", $pathname);
        $message = get_string("follow_percorso_message", "format_percorsofsbac", array("firstname" => $user->firstname,
                                                                                        "pathname" => $pathname,
                                                                                        "pathlink" => $pathlink,
                                                                                        "emailfrom" => $emailfrom));

        $return = array();

        $newpathfollowlabel = get_string("startfollowing", "format_percorsofsbac");

        $pathfollow = $DB->get_record("percorsofsbac_follow_path", array("courseid" => $courseid, "userid" => $userid));
        if (!$pathfollow) {
            $insertobj = new stdClass();
            $insertobj->courseid = $courseid;
            $insertobj->userid = $userid;
            $insertobj->followed = 1;
            $insertobj->timemodified = time();
            $DB->insert_record("percorsofsbac_follow_path", $insertobj);
            self::send_email($emailfrom, "", $emailto, "", $subject, $message);
            $newpathfollowlabel = get_string("stopfollowing", "format_percorsofsbac");
        } else {
            $updateobj = new stdClass();
            $updateobj->id = $pathfollow->id;
            $updateobj->timemodified = time();
            if ($pathfollow->followed == 1) {
                $updateobj->followed = 0;
                $DB->update_record("percorsofsbac_follow_path", $updateobj);
            } else if ($pathfollow->followed == 0) {
                $updateobj->followed = 1;
                $DB->update_record("percorsofsbac_follow_path", $updateobj);
                self::send_email($emailfrom, "", $emailto, "", $subject, $message);
                $newpathfollowlabel = get_string("stopfollowing", "format_percorsofsbac");
            }
        }

        $return["pathfollow"] = $newpathfollowlabel;

        return $return;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function follow_unfollow_path_returns() {

        return new external_single_structure(
            array('pathfollow' => new external_value(PARAM_TEXT, 'pathfollow', VALUE_REQUIRED),
            )
        );
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

}
