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
 * @package format_corsofsbac
 * @category task
 * @copyright 2023 Ariadne {@link http://www.ariadne.it}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_corsofsbac\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/grade/lib.php');
require_once($CFG->dirroot . '/grade/report/grader/lib.php');

class assign_grades_for_zoom_modules extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('assign_grades_for_zoom_modules', 'format_corsofsbac');
    }

    public function execute() {
        global $DB;

        $zoommaxdaysoldness = 7;
        $users = $this->get_zoom_meeting_participants($zoommaxdaysoldness);
        foreach ($users as $user) {
            mtrace("\nUSER ID: " . $user->userid);
            $zoommeetings = $this->get_zoom_meetings($user, $zoommaxdaysoldness);
            foreach ($zoommeetings as $zoommeeting) {
                mtrace("\n   ZOOM MEETING ID: " . $zoommeeting->id);
                $zoommeetingusertimes = $this->get_zoom_meeting_user_times($user, $zoommeeting);
                // $zoommeetingorganizer = $this->get_zoom_meeting_organizer($zoommeeting, $zoommaxdaysoldness);
                // $zoommeetingsessions = $this->get_zoom_meeting_sessions($zoommeetingorganizer, $zoommeeting, $zoommaxdaysoldness);
                // $attendancetime = 0;
                // $meetingduration = 0;
                // foreach ($zoommeetingsessions as $zoommeetingsession) {
                //     mtrace("      ZOOM MEETING SESSION ID: " . $zoommeetingsession->id);
                //     $zoomsessionusertimes = $this->get_zoom_meeting_session_user_times($user, $zoommeetingsession);
                //     $meetingorganizersessionjointime = $this->get_zoom_meeting_organizer_session_join_time($zoommeetingsession);
                //     $meetingorganizersessionleavetime = $this->get_zoom_meeting_organizer_session_leave_time($zoommeetingsession);
                //     if (empty($zoomsessionusertimes)) {
                //         $sessionattendancetime = 0;
                //     } else {
                //         $sessionattendancetime = $this->get_zoom_meeting_session_user_attendance_time($zoomsessionusertimes, $meetingorganizersessionjointime, $meetingorganizersessionleavetime);
                //     }
                //     mtrace("         SESSION ATTENDANCE TIME: " . $sessionattendancetime . " sec");
                //     $attendancetime += $sessionattendancetime;
                //     if ($meetingorganizersessionleavetime < $zoommeeting->start_time) {
                //         $sessionduration = 0;
                //     } else if ($meetingorganizersessionjointime > $zoommeeting->start_time + $zoommeeting->duration) {
                //         $sessionduration = 0;
                //     } else {
                //         $sessionduration = min($meetingorganizersessionleavetime, $zoommeeting->start_time + $zoommeeting->duration) - max($zoommeeting->start_time, $meetingorganizersessionjointime);
                //     }
                //     mtrace("         SESSION DURATION: " . $sessionduration . " sec");
                //     $meetingduration += $sessionduration;
                // }
                mtrace("      ATTENDANCE TIME: " . $zoommeetingusertimes->attendancetime . " sec");
                mtrace("      MEETING DURATION: " . $zoommeetingusertimes->meetingduration . " sec");
                $grade = $this->get_zoom_meeting_participant_grade($zoommeeting, $zoommeetingusertimes->meetingduration, $zoommeetingusertimes->attendancetime);
                mtrace("      GRADE: " . $grade);
                $this->assign_grade_to_zoom_meeting_participant($user, $zoommeeting, $grade);
                $evaluations = explode(",", $DB->get_field("scale", "scale", array("id" => $zoommeeting->scaleid)));
                $evaluation = $evaluations[$grade - 1];
                mtrace("      EVALUATION: " . $evaluation);
            }
        }
    }

    private function get_zoom_meeting_participants($zoommaxdaysoldness) {
        global $DB;

        $sql = "SELECT zmp.userid
                  FROM {zoom_meeting_participants} zmp
                  JOIN {zoom_meeting_details} zmd
                    ON zmd.id = zmp.detailsid
                  JOIN {zoom} z
                    ON z.id = zmd.zoomid
                 WHERE z.start_time > ?
                   AND zmp.userid IS NOT NULL
              GROUP BY zmp.userid";
        $users = $DB->get_records_sql($sql, array(time() - $zoommaxdaysoldness * 86400));
        return $users;
    }

    private function get_zoom_meetings($user, $zoommaxdaysoldness) {
        global $DB;

        $sql = "SELECT z.id, gi.id AS grade_item_id, gi.scaleid, gi.courseid, z.start_time, z.duration, z.host_id
                  FROM {zoom_meeting_participants} zmp
                  JOIN {zoom_meeting_details} zmd
                    ON zmd.id = zmp.detailsid
                  JOIN {zoom} z
                    ON z.id = zmd.zoomid
                  JOIN {grade_items} gi
                    ON gi.iteminstance = z.id
                 WHERE zmp.userid = ?
                   AND z.start_time > ?
                   AND gi.itemmodule = 'zoom'
              GROUP BY z.id";
        $zoommeetings = $DB->get_records_sql($sql, array($user->userid, time() - $zoommaxdaysoldness * 86400));
        return $zoommeetings;
    }

    private function get_zoom_meeting_user_times($user, $zoommeeting) {
        global $DB;

        $sql = "SELECT userid, z.duration AS meetingduration, (max(zmp.leave_time) - min(zmp.join_time)) AS attendancetime
                  FROM {zoom_meeting_participants} zmp
                  JOIN {zoom_meeting_details} zmd
                    ON zmp.detailsid = zmd.id
                  JOIN {zoom} z
                    ON zmd.zoomid = z.id
                 WHERE zmp.userid = ?
                   AND z.id = ?
              GROUP BY zmp.userid";
        $zoommeetingusertimes = $DB->get_record_sql($sql, array($user->userid, $zoommeeting->id));
        return $zoommeetingusertimes;
    }

    // private function get_zoom_meeting_organizer($zoommeeting, $zoommaxdaysoldness) {
    //     global $DB;

    //     $sql = "SELECT zmp.userid
    //               FROM {zoom_meeting_participants} zmp
    //               JOIN {zoom_meeting_details} zmd
    //                 ON zmd.id = zmp.detailsid
    //               JOIN {zoom} z
    //                 ON z.id = zmd.zoomid
    //              WHERE z.start_time > ?
    //                AND z.id = ?
    //                AND zmp.userid IS NOT NULL
    //                AND zmp.uuid = ?
    //           GROUP BY zmp.userid";
    //     $meetingorganizer = $DB->get_record_sql($sql, array(time() - $zoommaxdaysoldness * 86400, $zoommeeting->id, $zoommeeting->host_id));
    //     return $meetingorganizer;
    // }

    // private function get_zoom_meeting_sessions($zoommeetingorganizer, $zoommeeting, $zoommaxdaysoldness) {
    //     global $DB;

    //     $sql = "SELECT zmd.id, z.host_id, zmd.start_time
    //               FROM {zoom_meeting_participants} zmp
    //               JOIN {zoom_meeting_details} zmd
    //                 ON zmd.id = zmp.detailsid
    //               JOIN {zoom} z
    //                 ON z.id = zmd.zoomid
    //              WHERE zmp.userid = ?
    //                AND z.start_time > ?
    //                AND z.id = ?
    //           GROUP BY zmd.id";
    //     $zoommeetingsessions = $DB->get_records_sql($sql, array($zoommeetingorganizer->userid, time() - $zoommaxdaysoldness * 86400, $zoommeeting->id));
    //     return $zoommeetingsessions;
    // }

    // private function get_zoom_meeting_session_user_times($user, $zoommeetingsession) {
    //     global $DB;

    //     $sql = "SELECT zmp.id, zmp.join_time, zmp.leave_time, z.start_time, z.duration
    //               FROM {zoom_meeting_participants} zmp
    //               JOIN {zoom_meeting_details} zmd
    //                 ON zmd.id = zmp.detailsid
    //               JOIN {zoom} z
    //                 ON z.id = zmd.zoomid
    //              WHERE zmp.userid = ?
    //                AND zmp.detailsid = ?";
    //     $zoomsessionusertimes = $DB->get_records_sql($sql, array($user->userid, $zoommeetingsession->id));
    //     return $zoomsessionusertimes;
    // }

    // private function get_zoom_meeting_organizer_session_join_time($zoommeetingsession) {
    //     global $DB;

    //     $sql = "SELECT id, join_time
    //               FROM {zoom_meeting_participants}
    //              WHERE uuid = ?
    //                AND detailsid = ?";
    //     $meetingorganizertimes = $DB->get_record_sql($sql, array($zoommeetingsession->host_id, $zoommeetingsession->id));
    //     $meetingorganizersessionjointime = $meetingorganizertimes->join_time;
    //     return $meetingorganizersessionjointime;
    // }

    // private function get_zoom_meeting_organizer_session_leave_time($zoommeetingsession) {
    //     global $DB;

    //     $sql = "SELECT id, leave_time
    //               FROM {zoom_meeting_participants}
    //              WHERE uuid = ?
    //                AND detailsid = ?";
    //     $meetingorganizertimes = $DB->get_record_sql($sql, array($zoommeetingsession->host_id, $zoommeetingsession->id));
    //     $meetingorganizersessionleavetime = $meetingorganizertimes->leave_time;
    //     return $meetingorganizersessionleavetime;
    // }

    // private function get_zoom_meeting_session_user_attendance_time($zoomsessionusertimes, $meetingorganizersessionjointime, $meetingorganizersessionleavetime) {
    //     $sessionattendancetime = 0;
    //     foreach ($zoomsessionusertimes as $zoomsessionusertime) {
    //         if ($meetingorganizersessionjointime < $zoomsessionusertime->start_time + $zoomsessionusertime->duration) {
    //             if ($zoomsessionusertime->join_time < $zoomsessionusertime->start_time) {
    //                 if ($zoomsessionusertime->leave_time > $zoomsessionusertime->start_time && $zoomsessionusertime->leave_time <= $meetingorganizersessionleavetime) {
    //                     $sessionattendancetime += min($zoomsessionusertime->leave_time, $zoomsessionusertime->start_time + $zoomsessionusertime->duration) - $zoomsessionusertime->start_time;
    //                 } else if ($zoomsessionusertime->leave_time > $meetingorganizersessionleavetime) {
    //                     $sessionattendancetime +=  min($meetingorganizersessionleavetime, $zoomsessionusertime->start_time + $zoomsessionusertime->duration) - $zoomsessionusertime->start_time;
    //                 }
    //             } else if ($zoomsessionusertime->join_time >= $zoomsessionusertime->start_time && $zoomsessionusertime->join_time < $meetingorganizersessionleavetime) {
    //                 if ($zoomsessionusertime->leave_time < $meetingorganizersessionleavetime) {
    //                     $sessionattendancetime += min($zoomsessionusertime->leave_time, $zoomsessionusertime->start_time + $zoomsessionusertime->duration) - $zoomsessionusertime->join_time;
    //                 } else {
    //                     $sessionattendancetime += min($meetingorganizersessionleavetime, $zoomsessionusertime->start_time + $zoomsessionusertime->duration) - $zoomsessionusertime->join_time;
    //                 }
    //             }
    //         }
    //     }
    //     return $sessionattendancetime;
    // }

    private function get_zoom_meeting_participant_grade($zoommeeting, $meetingduration, $attendancetime) {
        global $DB;

        $zoommoduleid = $DB->get_field("modules", "id", array("name" => "zoom"));
        $customfieldsogliapresenzaid = $DB->get_field("customfield_field", "id", array("shortname" => "soglia_presenza"));
        $customfieldtolleranzaid = $DB->get_field("customfield_field", "id", array("shortname" => "tolleranza"));

        $coursemoduleid = $DB->get_field("course_modules", "id", array("course" => $zoommeeting->courseid, "module" => $zoommoduleid, "instance" => $zoommeeting->id));
        $coursemoduleinstance = \context_module::instance($coursemoduleid);
        $sogliapresenza = $DB->get_field("customfield_data", "value", array("fieldid" => $customfieldsogliapresenzaid, "contextid" => $coursemoduleinstance->id));
        $tolleranza = $DB->get_field("customfield_data", "value", array("fieldid" => $customfieldtolleranzaid, "contextid" => $coursemoduleinstance->id));

        if ($attendancetime == 0) {
            $grade = 1;
        } else if ($attendancetime > 0 && $attendancetime <= ($meetingduration * $sogliapresenza / 100) - (($meetingduration * $sogliapresenza / 100) * $tolleranza / 100)) {
            $grade = 2;
        } else {
            $grade = 3;
        }
        return $grade;
    }

    private function assign_grade_to_zoom_meeting_participant($user, $zoommeeting, $grade) {
        global $DB;

        $warnings = array();

        $courseid = $zoommeeting->courseid;
        $context = \context_course::instance($courseid);
        $viewfullnames = has_capability('moodle/site:viewfullnames', $context);

        $students = array($user->userid => array($zoommeeting->grade_item_id => $grade));

        foreach ($students as $userid => $items) {
            $userid = clean_param($userid, PARAM_INT);
            foreach ($items as $itemid => $postedvalue) {
                $itemid = clean_param($itemid, PARAM_INT);

                if (!$gradeitem = \grade_item::fetch(array('id' => $itemid, 'courseid' => $courseid))) {
                    throw new \moodle_exception('invalidgradeitemid');
                }

                // Pre-process grade
                if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
                    if ($postedvalue == -1) { // -1 means no grade
                        $finalgrade = null;
                    } else {
                        $finalgrade = $postedvalue;
                    }
                } else {
                    $finalgrade = unformat_float($postedvalue);
                }

                $errorstr = '';
                if (!is_null($finalgrade)) {
                    // Warn if the grade is out of bounds.
                    $bounded = $gradeitem->bounded_grade($finalgrade);
                    if ($bounded > $finalgrade) {
                        $errorstr = 'lessthanmin';
                    } else if ($bounded < $finalgrade) {
                        $errorstr = 'morethanmax';
                    }
                }

                if ($errorstr) {
                    $userfieldsapi = \core_user\fields::for_name();
                    $userfields = 'id, ' . $userfieldsapi->get_sql('', false, '', '', false)->selects;
                    $user = $DB->get_record('user', array('id' => $userid), $userfields);
                    $gradestr = new \stdClass();
                    $gradestr->username = fullname($user, $viewfullnames);
                    $gradestr->itemname = $gradeitem->get_name();
                    $warnings[] = get_string($errorstr, 'grades', $gradestr);
                }

                if (empty($warnings)) {
                    $gradeitem->update_final_grade($userid, $finalgrade, 'gradebook', false,
                        FORMAT_MOODLE, null, null, true);
                }
            }
        }
    }
}
