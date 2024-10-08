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
 * Contains the default content output class.
 *
 * @package   format_percorsofsbac
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_percorsofsbac\output\courseformat;

use core\plugininfo\format;
use core_courseformat\output\local\content as content_base;
use moodle_url;
use renderer_base;

/**
 * Base class to render a course content.
 *
 * @package   format_percorsofsbac
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content extends content_base
{

    /**
     * @var bool Topic format has add section after each topic.
     *
     * The responsible for the buttons is core_courseformat\output\local\content\section.
     */
    protected $hasaddsection = false;

    protected $format;

    /**
     * Export this data so it can be used as the context for a mustache template (core/inplace_editable).
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(renderer_base $output)
    {
        global $PAGE, $COURSE, $DB, $USER, $CFG;
        $PAGE->requires->js_call_amd('format_percorsofsbac/mutations', 'init');
        $PAGE->requires->js_call_amd('format_percorsofsbac/section', 'init');
        $PAGE->requires->js_call_amd('format_percorsofsbac/followbutton', 'init', [$COURSE->id, $USER->id]);
        $data = parent::export_for_template($output);

        require_once($CFG->dirroot . '/course/format/corsofsbac/locallib.php');
        $data->link = $CFG->wwwroot . '/course/view.php?id=' . $COURSE->id;
        $coursecontextinstance = \context_course::instance($COURSE->id);

        $data->isNew = false;
        $data->titolo = format_string($COURSE->fullname);
        $format = $this->format->get_format();
        $data->format = $format;

        $isloggedandenrolled = is_user_logged_and_enrolled_to_course($USER, $COURSE->id);
        $data->isloggedandenrolled = $isloggedandenrolled;

        $islogged = isloggedin() && !isguestuser();
        $data->islogged = $islogged;

        $isenrolled = is_user_enrolled_to_course($USER, $COURSE->id);
        $data->isenrolled = $isenrolled;

        $coursecustomfields = get_course_customfields($COURSE->id);

        if (!empty($USER->theme)) {
            $usertheme = $USER->theme;
        } else {
            $usertheme = $CFG->theme;
        }

        if ($usertheme == "fsbac") {
            // inizio descrizione corso
            $options = array('overflowdiv' => true, 'noclean' => true, 'para' => false);
            $summary = file_rewrite_pluginfile_urls($COURSE->summary, 'pluginfile.php', $coursecontextinstance->id, 'course', 'summary', null);
            $summary = format_text($summary, $COURSE->summaryformat, $options, $COURSE->id);
            $data->course_descr = $summary;
            // fine descrizione corso
            // inizio panoramica corsi contenuti nel path
            $sql = "SELECT s.id, s.refcourse
                    FROM {subcourse} s
                    JOIN {course_modules} cm
                        ON cm.instance = s.id
                    JOIN {modules} m
                        ON m.id = cm.module
                    WHERE s.course = ?
                    AND cm.deletioninprogress = 0
                    AND m.name = 'subcourse'";
            $subcourses = $DB->get_records_sql($sql, array($COURSE->id));
            $data->subcoursesCount = count($subcourses);
            $data->subcoursesShow = count($subcourses) > 0;
            if (!empty($subcourses)) {
                $data->subcourses = array("number" => count($subcourses), "multiplesubcourses" => count($subcourses) > 1 ? true : false, "subcoursesinfo" => array());
                foreach ($subcourses as $subcourse) {
                    $subcourseinfo = array();
                    $subcourseinfo["courseimage"] = get_course_image($subcourse->refcourse);
                    $subcoursecustomfields = get_course_customfields($subcourse->refcourse);
                    if (isset($subcoursecustomfields["mode"])) {
                        if (!empty($subcoursecustomfields["mode"]->value)) {
                            $modeoptions = explode("\r\n", json_decode($subcoursecustomfields["mode"]->configdata)->options);
                            $subcourseinfo["tipologia"] = $modeoptions[$subcoursecustomfields["mode"]->value - 1];
                            $subcourseinfo["tipologiaLabel"] = get_string('typecourse.' . $subcourseinfo["tipologia"], "theme_fsbac");
                        }
                    }
                    if (isset($subcoursecustomfields["teacherslist"])) {
                        if (!empty($subcoursecustomfields["teacherslist"])) {
                            $subcourseinfo["autore"] = $subcoursecustomfields["teacherslist"]->value;
                        }
                    }
                    $subcourseinfo["fullname"] = format_string($DB->get_field("course", "fullname", array("id" => $subcourse->refcourse)));
                    if (isset($subcoursecustomfields["sottotitolo"])) {
                        if (!empty($subcoursecustomfields["sottotitolo"])) {
                            $subcourseinfo["sottotitolo"] = $subcoursecustomfields["sottotitolo"]->value;
                        }
                    }

                    $uselabels = false;
                    if (isset($subcoursecustomfields["barracompletamentolabels"]) && !empty($subcoursecustomfields["barracompletamentolabels"]->value)) {
                        $uselabels = true;
                    }
                    $progressbar = new \format_corsofsbac\progressbar($uselabels);
                    $coursecompletioninfo = $progressbar::get_course_completion_info(get_course($subcourse->refcourse), $USER->id);

                    $subcourseinfo["progressShow"] = $islogged;
                    $subcourseinfo["progress"] = floor($coursecompletioninfo->courseprogressperc);
                    $subcourseinfo["progressComplete"] = $subcourseinfo["progress"] == 100;
                    $subcourseinfo["viewurl"] = new moodle_url("/course/view.php", array("id" => $subcourse->refcourse));



                    $data->subcourses["subcoursesinfo"][] = $subcourseinfo;
                }
            }
            // fine panoramica corsi contenuti nel path
            // inizio corsi per approfondimento
            $coursesinsight = $DB->get_field("course_format_options", "value", array("courseid" => $COURSE->id, "format" => $format, "name" => "coursesinsight"));
            if (!empty($coursesinsight)) {
                $coursesinsightids = explode(",", $coursesinsight);
                if (!empty($coursesinsightids)) {
                    $data->coursesinsight = array("number" => count($coursesinsightids), "multiplecoursesinsight" => count($coursesinsightids) > 1 ? true : false, "coursesinsightinfo" => array());
                    foreach ($coursesinsightids as $courseinsightid) {
                        $courseinsightinfo = array();
                        $courseinsightinfo["id"] = $courseinsightid;
                        $coursename = $DB->get_field("course", "fullname", array("id" => $courseinsightid));
                        $courseinsightinfo["fullname"] = format_string($coursename);
                        $courseimageurl = get_course_image($courseinsightid);
                        if (!empty($courseimageurl)) {
                            $courseinsightinfo["courseimage"] = $courseimageurl;
                        }
                        $courseinsightcustomfields = get_course_customfields($courseinsightid);
                        if (isset($courseinsightcustomfields["mode"])) {
                            if (!empty($courseinsightcustomfields["mode"]->value)) {
                                $modeoptions = explode("\r\n", json_decode($courseinsightcustomfields["mode"]->configdata)->options);
                                $courseinsightinfo["tipologia"] = $modeoptions[$courseinsightcustomfields["mode"]->value - 1];
                                $courseinsightinfo["tipologiaLabel"] = get_string('typecourse.' . $courseinsightinfo["tipologia"], "theme_fsbac");
                            }
                        }
                        if (isset($courseinsightcustomfields["sottotitolo"])) {
                            if (!empty($courseinsightcustomfields["sottotitolo"]->value)) {
                                $courseinsightinfo["sottotitolo"] = $courseinsightcustomfields["sottotitolo"]->value;
                            }
                        }

                        if (isset($courseinsightcustomfields["teacherslist"])) {
                            if (!empty($courseinsightcustomfields["teacherslist"]->value)) {
                                $courseinsightinfo["autore"] = $courseinsightcustomfields["teacherslist"]->value;
                            }
                        }

                        $courseinsightinfo["viewurl"] = new moodle_url("/course/view.php", array("id" => $courseinsightid));
                        $data->coursesinsight["coursesinsightinfo"][] = $courseinsightinfo;
                    }
                }
            }
            // fine corsi per approfondimento
            // inizio bottone segui/non segui percorso
            $pathfollow = $DB->get_record("percorsofsbac_follow_path", array("courseid" => $COURSE->id, "userid" => $USER->id));
            if (!$pathfollow) {
                $followpathlabel = get_string("startfollowing", "format_percorsofsbac");
            } else {
                if ($pathfollow->followed == 1) {
                    $followpathlabel = get_string("stopfollowing", "format_percorsofsbac");
                } else if ($pathfollow->followed == 0) {
                    $followpathlabel = get_string("startfollowing", "format_percorsofsbac");
                }
            }
            $data->followed = !$pathfollow || $pathfollow->followed == 0 ? false : true;
            $data->followpathlabel = $followpathlabel;
            // fine bottone segui/non segui percorso
            // inizio corsi DICOLAB
            $dicolabcourses = array();
            $sql = "SELECT DISTINCT c.id, c.fullname, ordine
                               FROM (
                             SELECT 1 AS ordine, ti.itemid
                               FROM {tag} t
                         INNER JOIN {tag_instance} ti
                                 ON t.id = ti.tagid
                                AND ti.itemtype = 'course'
                         INNER JOIN (
                             SELECT instanceid, value, configdata
                               FROM {customfield_data} cd
                         INNER JOIN {customfield_field} cf
                                 ON cf.id = cd.fieldid
                              WHERE shortname = 'pnrr'
                                AND cd.value = 1 ) dicolab
                                 ON ti.itemid = dicolab.instanceid
                              WHERE t.name IN (
                             SELECT name
                               FROM {tag} t
                         INNER JOIN {tag_instance} ti
                                 ON t.id = ti.tagid
                                AND itemtype ='user'
                                AND itemid = ? )
                              UNION
                             SELECT 2 AS ordine, cd.instanceid
                               FROM {customfield_field} cf
                         INNER JOIN {customfield_data} cd
                                 ON cf.id = cd.fieldid
                                AND cf.shortname = 'pnrr'
                                AND cd.value = 1 ) cc
                         INNER JOIN {course} c
                                 ON c.id = cc.itemid
                              WHERE c.visible = 1
                                AND c.format = 'corsofsbac'
                                AND c.id NOT IN (
                             SELECT instanceid FROM {customfield_data} cd
                         INNER JOIN {customfield_field} cf
                                 ON cf.id = cd.fieldid
                              WHERE shortname = 'riservato'
                                AND cd.value = 1 )
                                AND c.id IN (
                             SELECT e.courseid
                               FROM {enrol} e
                              WHERE e.enrol = 'fsbac'
                                AND status = 0 )
                           ORDER BY ordine";
            $courses = $DB->get_records_sql($sql, array($USER->id));
            $maxviewdicolabcourses = 3;
            $dicolabcoursenum = 0;
            foreach ($courses as $course) {
                if ($dicolabcoursenum == $maxviewdicolabcourses) {
                    break;
                }

                $dicolabcourseinfo = array();
                $dicolabcourseinfo["id"] = $course->id;
                $dicolabcourseinfo["fullname"] = $course->fullname;
                $courseimageurl = get_course_image($course->id);
                if (!empty($courseimageurl)) {
                    $dicolabcourseinfo["courseimage"] = $courseimageurl;
                }
                $dicolabcoursecustomfields = get_course_customfields($course->id);
                if (isset($dicolabcoursecustomfields["sottotitolo"])) {
                    if (!empty($dicolabcoursecustomfields["sottotitolo"]->value)) {
                        $dicolabcourseinfo["sottotitolo"] = $dicolabcoursecustomfields["sottotitolo"]->value;
                    }
                }
                if (isset($dicolabcoursecustomfields["mode"])) {
                    if (!empty($dicolabcoursecustomfields["mode"]->value)) {
                        $modeoptions = explode("\r\n", json_decode($dicolabcoursecustomfields["mode"]->configdata)->options);
                        $dicolabcourseinfo["tipologia"] = $modeoptions[$dicolabcoursecustomfields["mode"]->value - 1];
                        $dicolabcourseinfo["tipologiaLabel"] = get_string('typecourse.' . $dicolabcourseinfo["tipologia"], "theme_fsbac");
                    }
                }
                if (isset($dicolabcoursecustomfields["teacherslist"])) {
                    if (!empty($dicolabcoursecustomfields["teacherslist"]->value)) {
                        $dicolabcourseinfo["autore"] = $dicolabcoursecustomfields["teacherslist"]->value;
                    }
                }
                if (isset($dicolabcoursecustomfields["duration"])) {
                    if ($dicolabcoursecustomfields["duration"]->value != "") {
                        $dicolabcourseinfo["duration"] = convert_minutes_into_hours_minutes($dicolabcoursecustomfields["duration"]->value);
                    }
                }
                $dicolabcourseinfo["viewurl"] = new moodle_url("/course/view.php", array("id" => $course->id));
                $dicolabcourses[] = $dicolabcourseinfo;

                $dicolabcoursenum += 1;
            }
            if (!empty($dicolabcourses)) {
                $data->dicolabcourses = $dicolabcourses;
            } else {
                $data->emptydicolabcourses = true;
            }
            // fine corsi DICOLAB
            if ($isloggedandenrolled) {
                // inizio recensioni
                $reviews = course_get_format($COURSE->id)->get_format_options()["reviews_editor"];
                if (isset($reviews)) {
                    if (isset($reviews["text"])) {
                        $data->reviews = format_text(course_get_format($COURSE->id)->get_format_options()["reviews_editor"]["text"]);
                    }
                }
                // fine recensioni
                // inizio progress bar
                $courseprogressinfo = array();
                $progressbar = new \format_corsofsbac\progressbar(false);
                $coursecompletioninfo = $progressbar::get_course_completion_info($COURSE, $USER->id);
                $courseprogressinfo["courseprogressperc"] = $coursecompletioninfo->courseprogressperc;
                $courseprogressinfo["courseprogressstate"] = $coursecompletioninfo->courseprogressstate;
                $courseprogressinfo["totalmodules"] = $coursecompletioninfo->totalmodules;
                $courseprogressinfo["completedmodules"] = $coursecompletioninfo->completedmodules;
                $data->coursecompletioninfo = $courseprogressinfo;
                // fine progress bar
                // inizio informazioni generali
                $generalinfo = array();
                $courseidnumber = $DB->get_field("course", "idnumber", array("id" => $COURSE->id));
                if (!empty($courseidnumber)) {
                    $generalinfo["codicecorso"] = $courseidnumber;
                }
                $coursecustomfields = get_course_customfields($COURSE->id);
                if (isset($coursecustomfields["destinato_a"])) {
                    if (!empty($coursecustomfields["destinato_a"]->value)) {
                        $generalinfo["destinato_a"] = $coursecustomfields["destinato_a"]->value;
                    }
                }
                if (isset($coursecustomfields["livello"])) {
                    if (!empty($coursecustomfields["livello"]->value)) {
                        $leveloptions = explode("\r\n", json_decode($coursecustomfields["livello"]->configdata)->options);
                        $generalinfo["level"] = get_string('level.' . $leveloptions[$coursecustomfields["livello"]->value - 1], "theme_fsbac");
                    }
                }
                if (isset($coursecustomfields["programma"])) {
                    if (!empty($coursecustomfields["programma"]->value)) {
                        $generalinfo["programma"] = $coursecustomfields["programma"]->value;
                    }
                }
                if (isset($coursecustomfields["teacherslist"])) {
                    if (!empty($coursecustomfields["teacherslist"]->value)) {
                        $generalinfo["teacherslist"] = $coursecustomfields["teacherslist"]->value;
                    }
                }
                if (isset($coursecustomfields["mode"])) {
                    if (!empty($coursecustomfields["mode"]->value)) {
                        $modeoptions = explode("\r\n", json_decode($coursecustomfields["mode"]->configdata)->options);
                        $generalinfo["mode"] = get_string($modeoptions[$coursecustomfields["mode"]->value - 1], "format_corsofsbac");
                    }
                }
                $pathduration = get_path_total_duration($COURSE->id);
                if (!empty($pathduration)) {
                    $generalinfo["duration"] = convert_minutes_into_hours_minutes($pathduration);
                }
                if (!empty($generalinfo)) {
                    $data->generalinfo = $generalinfo;
                }
                // fine informazioni generali
                // inizio competenze
                $coursecustomfields = get_course_customfields($COURSE->id);
                if (isset($coursecustomfields["competenze"])) {
                    if (!empty($coursecustomfields["competenze"]->value)) {
                        $data->competenze = $coursecustomfields["competenze"]->value;
                    }
                }
                // fine competenze
                // inizio percorsi consequenziali
                $consequentialpaths = $DB->get_field("course_format_options", "value", array("courseid" => $COURSE->id, "format" => $format, "name" => "consequentialpaths"));
                if (!empty($consequentialpaths)) {
                    $consequentialpathsids = explode(",", $consequentialpaths);
                    if (!empty($consequentialpathsids)) {
                        $data->consequentialpaths = array("number" => count($consequentialpathsids), "multipleconsequentialpaths" => count($consequentialpathsids) > 1 ? true : false, "consequentialpathsinfo" => array());
                        foreach ($consequentialpathsids as $pathid) {
                            $consequentialpathinfo = array();
                            $consequentialpathinfo["id"] = $pathid;
                            $pathname = $DB->get_field("course", "fullname", array("id" => $pathid));
                            $consequentialpathinfo["pathname"] = $pathname;
                            $courseimageurl = get_course_image($pathid);
                            if (!empty($courseimageurl)) {
                                $consequentialpathinfo["img_url"] = $courseimageurl;
                            }
                            $pathcustomfields = get_course_customfields($pathid);
                            if (isset($pathcustomfields["sottotitolo"])) {
                                if (!empty($pathcustomfields["sottotitolo"]->value)) {
                                    $consequentialpathinfo["sottotitolo"] = $pathcustomfields["sottotitolo"]->value;
                                }
                            }
                            if (isset($pathcustomfields["livello"])) {
                                if (!empty($pathcustomfields["livello"]->value)) {
                                    $leveloptions = explode("\r\n", json_decode($pathcustomfields["livello"]->configdata)->options);
                                    $consequentialpathinfo["level"] = get_string('level.' . $leveloptions[$pathcustomfields["livello"]->value - 1], "theme_fsbac");
                                }
                            }
                            $pathduration = get_path_total_duration($pathid);
                            if (!empty($pathduration)) {
                                $consequentialpathinfo["duration"] = convert_minutes_into_hours_minutes($pathduration);
                            }
                            $data->consequentialpaths["consequentialpathsinfo"][] = $consequentialpathinfo;
                        }
                    }
                }
                // fine percorsi consequenziali
                // inizio attestati ottenibili
                $sql = "SELECT c.id, c.course, c.name
                            FROM {certificate} c
                            JOIN {subcourse} s ON s.refcourse = c.course
                            WHERE s.course = ?
                        GROUP BY c.course";
                $totalcertificates = $DB->get_records_sql($sql, array($COURSE->id));
                if (!empty($totalcertificates)) {
                    $data->totalcertificates = array("number" => count($totalcertificates), "multipletotalcertificates" => count($totalcertificates) > 1 ? true : false);
                }
                // fine attestati ottenibili
                // inizio bottone inizia/continua a seguire
                $progressbar = new \format_corsofsbac\progressbar(false);
                $courseprogressperc = $progressbar::get_course_completion_info($COURSE, $USER->id)->courseprogressperc;
                if ($courseprogressperc == 100) {
                    $data->coursebuttonlabel = get_string("share", "format_corsofsbac");
                    $data->coursebuttonlink = new moodle_url("/course/view.php", array("id" => $COURSE->id));
                } else {
                    $iscoursestarted = false;
                    $firstincompletemodulefound = false;
                    $completion = new \completion_info($COURSE);
                    $modules = $completion->get_activities();
                    if (!empty($modules)) {
                        foreach ($modules as $module) {
                            if ($module->modname != "certificate") {
                                $datacompletion = $completion->get_data($module, true, $USER->id);
                                if (($datacompletion->completionstate == COMPLETION_INCOMPLETE) || ($datacompletion->completionstate == COMPLETION_COMPLETE_FAIL)) {
                                    if (!$firstincompletemodulefound) {
                                        $data->coursebuttonlink = new moodle_url("/mod/$module->modname/view.php", array("id" => $module->id));
                                        $firstincompletemodulefound = true;
                                    }
                                } else {
                                    $iscoursestarted = true;
                                }
                            }
                        }
                        if ($iscoursestarted) {
                            $data->coursebuttonlabel = get_string("keepfollowing", "format_corsofsbac");
                        } else {
                            $data->coursebuttonlabel = get_string("startpath", "format_corsofsbac");
                        }
                    }
                }
                // fine bottone inizia/continua a seguire
            } else {
                // inizio immagine percorso
                $data->img_url = get_course_image($COURSE->id);
                // fine immagine percorso
                // inizio informazioni generali
                $generalinfo = array();
                $coursecustomfields = get_course_customfields($COURSE->id);
                if (isset($coursecustomfields["destinato_a"])) {
                    if (!empty($coursecustomfields["destinato_a"]->value)) {
                        $generalinfo["destinato_a"] = $coursecustomfields["destinato_a"]->value;
                    }
                }
                if (isset($coursecustomfields["livello"])) {
                    if (!empty($coursecustomfields["livello"]->value)) {
                        $leveloptions = explode("\r\n", json_decode($coursecustomfields["livello"]->configdata)->options);
                        $subcourseinfo["levelId"] = $leveloptions[$coursecustomfields["livello"]->value - 1];
                        $generalinfo["level"] = get_string('level.' .  $subcourseinfo["levelId"], 'theme_fsbac');
                    }
                }
                if (isset($coursecustomfields["mode"])) {
                    if (!empty($coursecustomfields["mode"]->value)) {
                        $modeoptions = explode("\r\n", json_decode($coursecustomfields["mode"]->configdata)->options);
                        $generalinfo["mode"] = get_string($modeoptions[$coursecustomfields["mode"]->value - 1], "format_corsofsbac");
                    }
                }
                if (isset($coursecustomfields["programma"])) {
                    if (!empty($coursecustomfields["programma"]->value)) {
                        $generalinfo["programma"] = $coursecustomfields["programma"]->value;
                    }
                }
                if (isset($coursecustomfields["teacherslist"])) {
                    if (!empty($coursecustomfields["teacherslist"]->value)) {
                        $generalinfo["teacherslist"] = $coursecustomfields["teacherslist"]->value;
                    }
                }
                if (isset($coursecustomfields["new"])) {
                    if (!empty($coursecustomfields["new"]->value)) {
                        $data->isNew = true;
                    }
                }
                $pathduration = get_path_total_duration($COURSE->id);
                if (!empty($pathduration)) {
                    $generalinfo["duration"] = convert_minutes_into_hours_minutes($pathduration);
                }
                $sql = "SELECT ti.id, t.name
                        FROM {tag_instance} ti
                        JOIN {tag} t
                            ON t.id = ti.tagid
                        WHERE ti.itemtype = 'course'
                        AND ti.contextid = ?";
                $coursetags = $DB->get_records_sql($sql, array($coursecontextinstance->id));
                if (!empty($coursetags)) {
                    $coursetagsarray = array();
                    foreach ($coursetags as $tag) {
                        $coursetagsarray[] = get_string('tag.' . $tag->name, "theme_fsbac");
                    }
                    $generalinfo["tags"] = implode(", ", array_values($coursetagsarray));
                }
                if (!empty($generalinfo)) {
                    $data->generalinfo = $generalinfo;
                }
                // fine informazioni generali
            }
        }
        $theme = \theme_config::load('fsbac');
        $imageBannerBlueImage = $theme->setting_file_url('banner_blue_image', 'banner_blue_image');
        $data->imageBannerBlueImage = $imageBannerBlueImage;


        $data->percorsiSimili = \theme_fsbac\fsbac::get_percorsi_simili($COURSE->id);
        $data->corsiSimili = \theme_fsbac\fsbac::get_corsi_simili($COURSE->id);
        return $data;
    }

    /**
     * Returns the output class template path.
     *
     * This method redirects the default template when the course content is rendered.
     */
    public function get_template_name(\renderer_base $renderer): string
    {
        return 'format_percorsofsbac/local/content';
    }
}
