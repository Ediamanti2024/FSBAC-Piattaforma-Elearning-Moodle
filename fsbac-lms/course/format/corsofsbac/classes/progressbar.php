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

namespace format_corsofsbac;

class progressbar {

    private static $uselabels;

    public function __construct($uselabels) {
        self::$uselabels = $uselabels;
    }

    public static function get_course_completion_info($course, $userid) {
        if (self::$uselabels) {
            return self::get_course_completion_info_with_labels($course, $userid);
        } else {
            return self::get_course_completion_info_without_labels($course, $userid);
        }
    }

    private static function get_course_completion_info_with_labels($course, $userid) {
        global $DB;

        $completion = new \completion_info($course);
        $moduleswithcompletioncriteria = $completion->get_activities();

        [$insql, $inparams] = $DB->get_in_or_equal(array_keys($moduleswithcompletioncriteria));

        $sql = "SELECT cd.value, GROUP_CONCAT(cm.id SEPARATOR ',') AS cmids
                  FROM {customfield_data} cd
                  JOIN {customfield_field} cf
                    ON cf.id = cd.fieldid
                  JOIN {context} c
                    ON c.id = cd.contextid
                  JOIN {course_modules} cm
                    ON cm.id = c.instanceid
                 WHERE cf.shortname = 'activitylabel'
                   AND cd.value <> ''
                   AND cm.id $insql
                   AND cm.course = ?
              GROUP BY cd.value";
        $inparams[] = $course->id;
        $moduleswithlabelsandcompletioncriteria = $DB->get_records_sql($sql, $inparams);

        $count = count($moduleswithlabelsandcompletioncriteria);
        $completed = 0;

        foreach (array_values($moduleswithlabelsandcompletioncriteria) as $modules) {
            if (strpos($modules->cmids, ",") !== false) {
                $modules = explode(",", $modules->cmids);
                foreach ($modules as $module) {
                    $datacompletion = $completion->get_data($moduleswithcompletioncriteria[$module], true, $userid);
                    if (($datacompletion->completionstate != COMPLETION_INCOMPLETE) && ($datacompletion->completionstate != COMPLETION_COMPLETE_FAIL)) {
                        $completed += 1;
                        break;
                    }
                }
            } else {
                $datacompletion = $completion->get_data($moduleswithcompletioncriteria[$modules->cmids], true, $userid);
                if (($datacompletion->completionstate != COMPLETION_INCOMPLETE) && ($datacompletion->completionstate != COMPLETION_COMPLETE_FAIL)) {
                    $completed += 1;
                }
            }
        }
        if ($count == 0) {
            $courseprogressstate = get_string("inprogress", "format_corsofsbac");
            $courseprogressperc = 0;
        } else {
            if ($completed < $count) {
                $courseprogressstate = get_string("inprogress", "format_corsofsbac");
            } else {
                $courseprogressstate = get_string("completed", "format_corsofsbac");
            }
            $courseprogressperc = ($completed / $count) * 100;
        }
        $coursecompletioninfo = new \stdClass();
        $coursecompletioninfo->courseprogressperc = $courseprogressperc;
        $coursecompletioninfo->courseprogressstate = $courseprogressstate;
        $coursecompletioninfo->totalmodules = $count;
        $coursecompletioninfo->completedmodules = $completed;

        return $coursecompletioninfo;
    }

    private static function get_course_completion_info_without_labels($course, $userid) {
        $completion = new \completion_info($course);
        $modules = $completion->get_activities();
        $count = count($modules);
        $completed = 0;
        foreach ($modules as $module) {
            $datacompletion = $completion->get_data($module, true, $userid);
            if (($datacompletion->completionstate != COMPLETION_INCOMPLETE) && ($datacompletion->completionstate != COMPLETION_COMPLETE_FAIL)) {
                $completed += 1;
            }
        }
        if ($count == 0) {
            $courseprogressstate = get_string("inprogress", "format_corsofsbac");
            $courseprogressperc = 0;
        } else {
            if ($completed < $count) {
                $courseprogressstate = get_string("inprogress", "format_corsofsbac");
            } else {
                $courseprogressstate = get_string("completed", "format_corsofsbac");
            }
            $courseprogressperc = ($completed / $count) * 100;
        }
        $coursecompletioninfo = new \stdClass();
        $coursecompletioninfo->courseprogressperc = $courseprogressperc;
        $coursecompletioninfo->courseprogressstate = $courseprogressstate;
        $coursecompletioninfo->totalmodules = $count;
        $coursecompletioninfo->completedmodules = $completed;

        return $coursecompletioninfo;
    }

}
