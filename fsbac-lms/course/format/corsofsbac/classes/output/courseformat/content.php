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
 * @package   format_corsofsbac
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_corsofsbac\output\courseformat;

use core_courseformat\output\local\content as content_base;
use moodle_url;
use renderer_base;
use stdClass;

/**
 * Base class to render a course content.
 *
 * @package   format_corsofsbac
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

        $PAGE->requires->js_call_amd('format_corsofsbac/mutations', 'init');
        $PAGE->requires->js_call_amd('format_corsofsbac/section', 'init');
        $data = parent::export_for_template($output);

        require_once($CFG->dirroot . '/course/format/corsofsbac/locallib.php');

        $coursecontextinstance = \context_course::instance($COURSE->id);

        $sql = "SELECT *
                  FROM {customfield_data} cd
                  JOIN {customfield_field} cf
                    ON cf.id = cd.fieldid
                 WHERE cd.contextid = ?
                   AND cf.shortname = 'pnrr'
                   AND cd.intvalue = 1";
        $isdicolabcourse = $DB->get_record_sql($sql, array($coursecontextinstance->id));
        // se il corso è DICOLAB
        /* NF if ($isdicolabcourse)*/ {
            $coursecompletion = $DB->get_record("course_completions", array("userid" => $USER->id, "course" => $COURSE->id));
            // se l'utente ha completato il corso
            if ($coursecompletion && !empty($coursecompletion->timecompleted)) {
                $coursecompletedtime = $coursecompletion->timecompleted;
                // se l'utente sta visualizzando il corso dopo averlo completato
                if (time() > $coursecompletedtime) {
                    $sql = "SELECT *
                              FROM {logstore_standard_log}
                             WHERE component = 'core'
                               AND action = 'viewed'
                               AND target = 'course'
                               AND contextlevel = 50
                               AND contextinstanceid = ?
                               AND userid = ?
                               AND timecreated > ?
                             LIMIT 1";
                    $courseviewsaftercompletion = $DB->get_records_sql($sql, array($COURSE->id, $USER->id, $coursecompletedtime));
                    // se l'utente visualizza il corso per la prima volta dopo averlo completato
                    if (!$courseviewsaftercompletion) {
                        // creo il link di redirect alla pagina popup in cui scaricare il certificato del corso
						if ($isdicolabcourse) {
                        $completedcourseredirecturl = $CFG->wwwroot . "/local/fsbaclogin/completed_dicolab_course.php?courseid=" . $COURSE->id;
						} else {
							$completedcourseredirecturl = $CFG->wwwroot . "/local/fsbaclogin/completed_fsbac_course.php?courseid=" . $COURSE->id;
						}
                        $data->completedcourseredirecturl = $completedcourseredirecturl;
                    }
                }
            }
        }

        $data->wwwroot = $CFG->wwwroot;
        $data->isNew = false;
        $data->titolo = format_string($COURSE->fullname);
        $data->link = $CFG->wwwroot . '/course/view.php?id=' . $COURSE->id;
        $data->courseid = $COURSE->id;
        $data->format = $this->format->get_format();

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

        $uselabels = false;
        if (isset($coursecustomfields["barracompletamentolabels"]) && !empty($coursecustomfields["barracompletamentolabels"]->value)) {
            $uselabels = true;
        }
        $progressbar = new \format_corsofsbac\progressbar($uselabels);

        if ($usertheme == "fsbac") {
            // inizio descrizione corso
            $options = array('overflowdiv' => true, 'noclean' => true, 'para' => false);
            $summary = file_rewrite_pluginfile_urls($COURSE->summary, 'pluginfile.php', $coursecontextinstance->id, 'course', 'summary', null);
            $summary = format_text($summary, $COURSE->summaryformat, $options, $COURSE->id);
            $data->course_descr = $summary;
            // fine descrizione corso
            // inizio certificato
            $sql = "SELECT cm.id
                      FROM {course_modules} cm
                      JOIN {modules} m
                        ON m.id = cm.module
                     WHERE cm.course = ?
                       AND m.name = 'certificate' and cm.visible=1";
            $customcertsids = $DB->get_records_sql($sql, array($COURSE->id));
            $iscustomcertavailable = false;
            if (!empty($customcertsids)) {
                $customcertid = array_keys($customcertsids)[0];
                $isstudent = false;
                $roles = get_user_roles($coursecontextinstance, $USER->id, true);
                foreach ($roles as $role) {
                    if ($role->shortname == "student") {
                        $isstudent = true;
                        break;
                    }
                }
                if ($isstudent) {
                    $modinfo = get_fast_modinfo($COURSE->id, $USER->id);
                    $cm = $modinfo->get_cm($customcertid);
                    $iscustomcertavailable = $cm->__get("available");
                } else {
                    $iscustomcertavailable = true;
                }
                $data->customcertid = $customcertid;
            }
            $data->iscustomcertavailable = $iscustomcertavailable;
            // fine certificato
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
                                AND c.id <> ?
                           ORDER BY ordine";
            $courses = $DB->get_records_sql($sql, array($USER->id, $COURSE->id));
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
                    } else {
                        $dicolabcourseinfo["sottotitolo"] = "";
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
                $coursecompletioninfo = $progressbar::get_course_completion_info($COURSE, $USER->id);
                $courseprogressinfo["courseprogressperc"] = $coursecompletioninfo->courseprogressperc;
                $courseprogressinfo["courseprogressstate"] = $coursecompletioninfo->courseprogressstate;
                $courseprogressinfo["totalmodules"] = $coursecompletioninfo->totalmodules;
                $courseprogressinfo["completedmodules"] = $coursecompletioninfo->completedmodules;
                $data->coursecompletioninfo = $courseprogressinfo;
                // fine progress bar
                // inizio informazioni generali
                $generalinformation = array();
                $courseidnumber = $DB->get_field("course", "idnumber", array("id" => $COURSE->id));
                if (!empty($courseidnumber)) {
                    $generalinformation["codicecorso"] = $courseidnumber;
                }
                if (isset($coursecustomfields["mode"])) {
                    if (!empty($coursecustomfields["mode"]->value)) {
                        $modeoptions = explode("\r\n", json_decode($coursecustomfields["mode"]->configdata)->options);
                        $generalinformation["mode"] = get_string($modeoptions[$coursecustomfields["mode"]->value - 1], "format_corsofsbac");
                    }
                }
                if (isset($coursecustomfields["programma"])) {
                    if (!empty($coursecustomfields["programma"]->value)) {
                        $generalinformation["programma"] = $coursecustomfields["programma"]->value;
                    }
                }
                if (isset($coursecustomfields["teacherslist"])) {
                    if (!empty($coursecustomfields["teacherslist"]->value)) {
                        $generalinformation["teacherslist"] = $coursecustomfields["teacherslist"]->value;
                    }
                }
                if (isset($coursecustomfields["sottotitolo"])) {
                    if (!empty($coursecustomfields["sottotitolo"]->value)) {
                        $data->sottotitolo = $coursecustomfields["sottotitolo"]->value;
                    }
                }
                if (isset($coursecustomfields["new"])) {
                    if (!empty($coursecustomfields["new"]->value)) {
                        $data->isNew = true;
                    }
                }
                if (isset($coursecustomfields["duration"])) {
                    if ($coursecustomfields["duration"]->value != "") {
                        $generalinformation["duration"] = convert_minutes_into_hours_minutes($coursecustomfields["duration"]->value);
                    }
                }
                if (isset($coursecustomfields["destinato_a"])) {
                    if (!empty($coursecustomfields["destinato_a"]->value)) {
                        $generalinformation["destinato_a"] = $coursecustomfields["destinato_a"]->value;
                    }
                }
                if (isset($coursecustomfields["livello"])) {
                    if (!empty($coursecustomfields["livello"]->value)) {
                        $leveloptions = explode("\r\n", json_decode($coursecustomfields["livello"]->configdata)->options);
                        $generalinformation["level"] = get_string('level.' . $leveloptions[$coursecustomfields["livello"]->value - 1], "theme_fsbac");
                    }
                }
                $courseexpiration = $DB->get_field("course", "enddate", array("id" => $COURSE->id));
                if (!empty($courseexpiration)) {
                    $generalinformation["expiration"] = userdate($courseexpiration, get_string('strftimedate', 'core_langconfig'));
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
                    $generalinformation["tags"] = implode(", ", array_values($coursetagsarray));
                }
                if (!empty($generalinformation)) {
                    $data->generalinformation = $generalinformation;
                }
                // fine informazioni generali
                // inizio percorsi di appartenenza
                $paths = $DB->get_records("subcourse", array("refcourse" => $COURSE->id));
                if (!empty($paths)) {
                    $data->paths = array("number" => count($paths), "multiplepaths" => count($paths) > 1 ? true : false, "pathsinfo" => array());
                    foreach ($paths as $path) {
                        $pathinfo = array();
                        $pathname = $DB->get_field("course", "fullname", array("id" => $path->course));
                        $pathinfo["pathname"] = $pathname;
                        $pathinfo["id"] = $path->course;
                        $courseimageurl = get_course_image($path->course);
                        if (!empty($courseimageurl)) {
                            $pathinfo["img_url"] = $courseimageurl;
                        }
                        $pathcustomfields = get_course_customfields($path->course);
                        if (isset($pathcustomfields["sottotitolo"])) {
                            if (!empty($pathcustomfields["sottotitolo"]->value)) {
                                $pathinfo["sottotitolo"] = $pathcustomfields["sottotitolo"]->value;
                            } else {
                                $pathinfo["sottotitolo"] = "";
                            }
                        }
                        if (isset($pathcustomfields["livello"])) {
                            if (!empty($pathcustomfields["livello"]->value)) {
                                $leveloptions = explode("\r\n", json_decode($pathcustomfields["livello"]->configdata)->options);
                                $pathinfo["level"] = get_string('level.' . $leveloptions[$pathcustomfields["livello"]->value - 1], "theme_fsbac");
                            }
                        }
                        $pathduration = get_path_total_duration($path->course);
                        if (!empty($pathduration)) {
                            $pathinfo["duration"] = convert_minutes_into_hours_minutes($pathduration);
                        }
                        $data->paths["pathsinfo"][] = $pathinfo;
                    }
                }
                // fine percorsi di appartenenza
                // inizio certificato da ottenere
                $sql = "SELECT cm.id
                          FROM {course_modules} cm
                          JOIN {modules} m
                            ON m.id = cm.module
                         WHERE cm.course = ?
                           AND m.name = 'certificate' and cm.visible=1";
                $customcertsids = $DB->get_records_sql($sql, array($COURSE->id));
                $iscustomcertavailable = false;
                if (!empty($customcertsids)) {
                    $customcertid = array_keys($customcertsids)[0];
                    $isstudent = false;
                    $roles = get_user_roles($coursecontextinstance, $USER->id, true);
                    foreach ($roles as $role) {
                        if ($role->shortname == "student") {
                            $isstudent = true;
                            break;
                        }
                    }
                    if ($isstudent) {
                        $modinfo = get_fast_modinfo($COURSE->id, $USER->id);
                        $cm = $modinfo->get_cm($customcertid);
                        $iscustomcertavailable = $cm->__get("available");
                    } else {
                        $iscustomcertavailable = true;
                    }
                    $data->customcertid = $customcertid;
                }
                // fine certificato da ottenere
                // inizio bottone inizia/continua a seguire
                $courseprogressperc = $progressbar::get_course_completion_info($COURSE, $USER->id)->courseprogressperc;
                if ($courseprogressperc == 100) {
                    $data->coursebuttonlabel = get_string("share", "format_corsofsbac");
                    $data->coursebuttonlink = new moodle_url("/course/view.php", array("id" => $COURSE->id));
                    $data->isCompleted = true;
                } else {
                    $iscoursestarted = false;
                    $firstincompletemodulefound = false;
                    $completion = new \completion_info($COURSE);
                    $modules = $completion->get_activities();
                    $modules = $this->setcertificatesaslastmodules($modules);
                    if (!empty($modules)) {
                        foreach ($modules as $module) {
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
                        if ($iscoursestarted) {
                            $data->coursebuttonlabel = get_string("keepfollowing", "format_corsofsbac");
                        } else {
                            $data->coursebuttonlabel = get_string("startcourse", "format_corsofsbac");
                        }
                    }
                }
                // fine bottone inizia/continua a seguire
                // inizio bottone disiscriviti
                $sql = "SELECT ue.id, e.enrol
                          FROM {user_enrolments} ue
                          JOIN {enrol} e
                            ON ue.enrolid = e.id
                         WHERE ue.userid = ?
                           AND e.courseid = ?
                           AND e.enrol IN ('fsbac', 'meta')";
                $isfsbacormetaenrolled = $DB->get_records_sql($sql, array($USER->id, $COURSE->id));
                if ($isfsbacormetaenrolled) {
                    $data->isfsbacormetaenrolled = true;
                    $PAGE->requires->js_call_amd('format_corsofsbac/unenrol_user_from_course', 'init', array($USER->id, $COURSE->id));
                }
                // fine bottone disiscriviti

                $favouritecourseids = [];

                $ufservice = \core_favourites\service_factory::get_service_for_user_context(\context_user::instance($USER->id));
                $favourites = $ufservice->find_favourites_by_type('core_course', 'courses');

                if ($favourites) {
                    $favouritecourseids = array_map(
                        function ($favourite) {
                            return $favourite->itemid;
                        },
                        $favourites
                    );
                }

                $isfavourite = false;
                if (in_array($COURSE->id, $favouritecourseids)) {
                    $isfavourite = true;
                }

                $data->isfavourite = $isfavourite;
            } else {
                // inizio immagine corso
                $data->img_url = get_course_image($COURSE->id);
                // fine immagine corso
                // inizio informazioni generali
                $generalinformation = array();
                $courseidnumber = $DB->get_field("course", "idnumber", array("id" => $COURSE->id));
                if (!empty($courseidnumber)) {
                    $generalinformation["codicecorso"] = $courseidnumber;
                }
                if (isset($coursecustomfields["mode"])) {
                    if (!empty($coursecustomfields["mode"]->value)) {
                        $modeoptions = explode("\r\n", json_decode($coursecustomfields["mode"]->configdata)->options);
                        $generalinformation["mode"] = get_string('typecourse.' . $modeoptions[$coursecustomfields["mode"]->value - 1], "theme_fsbac");
                    }
                }
                if (isset($coursecustomfields["programma"])) {
                    if (!empty($coursecustomfields["programma"]->value)) {
                        $generalinformation["programma"] = $coursecustomfields["programma"]->value;
                    }
                }
                if (isset($coursecustomfields["teacherslist"])) {
                    if (!empty($coursecustomfields["teacherslist"]->value)) {
                        $generalinformation["teacherslist"] = $coursecustomfields["teacherslist"]->value;
                    }
                }
                if (isset($coursecustomfields["duration"])) {
                    if ($coursecustomfields["duration"]->value != "") {
                        $generalinformation["duration"] = convert_minutes_into_hours_minutes($coursecustomfields["duration"]->value);
                    }
                }
                if (isset($coursecustomfields["livello"])) {
                    if (!empty($coursecustomfields["livello"]->value)) {
                        $leveloptions = explode("\r\n", json_decode($coursecustomfields["livello"]->configdata)->options);
                        $generalinformation["level"] = get_string('level.' . $leveloptions[$coursecustomfields["livello"]->value - 1], "theme_fsbac");
                    }
                }
                if (isset($coursecustomfields["destinato_a"])) {
                    if (!empty($coursecustomfields["destinato_a"]->value)) {
                        $generalinformation["destinato_a"] = $coursecustomfields["destinato_a"]->value;
                    }
                }
                if (isset($coursecustomfields["sottotitolo"])) {
                    if (!empty($coursecustomfields["sottotitolo"]->value)) {
                        $data->sottotitolo = $coursecustomfields["sottotitolo"]->value;
                    }
                }
                if (isset($coursecustomfields["new"])) {
                    if (!empty($coursecustomfields["new"]->value)) {
                        $data->isNew = true;
                    }
                }
                $courseexpiration = $DB->get_field("course", "enddate", array("id" => $COURSE->id));
                if (!empty($courseexpiration)) {
                    $generalinformation["expiration"] = userdate($courseexpiration, get_string('strftimedate', 'core_langconfig'));
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
                    $generalinformation["tags"] = implode(", ", array_values($coursetagsarray));
                }
                if (!empty($generalinformation)) {
                    $data->generalinformation = $generalinformation;
                }
                // fine informazioni generali
                // inizio percorsi collegati
                $paths = $DB->get_records("subcourse", array("refcourse" => $COURSE->id));
                if (!empty($paths)) {
                    $data->paths = array("number" => count($paths), "multiplepaths" => count($paths) > 1 ? true : false, "pathsinfo" => array());
                    if (isloggedin() && !isguestuser()) {
                        foreach ($paths as $path) {
                            $pathinfo = array();
                            $pathname = $DB->get_field("course", "fullname", array("id" => $path->course));
                            $pathinfo["pathname"] = $pathname;
                            $pathinfo["id"] = $path->course;
                            $courseimageurl = get_course_image($path->course);
                            if (!empty($courseimageurl)) {
                                $pathinfo["img_url"] = $courseimageurl;
                            }
                            $pathcustomfields = get_course_customfields($path->course);
                            if (isset($pathcustomfields["sottotitolo"])) {
                                if (!empty($pathcustomfields["sottotitolo"]->value)) {
                                    $pathinfo["sottotitolo"] = $pathcustomfields["sottotitolo"]->value;
                                } else {
                                    $pathinfo["sottotitolo"] = "";
                                }
                            }
                            if (isset($pathcustomfields["livello"])) {
                                if (!empty($pathcustomfields["livello"]->value)) {
                                    $leveloptions = explode("\r\n", json_decode($pathcustomfields["livello"]->configdata)->options);
                                    $pathinfo["level"] = get_string('level.' . $leveloptions[$pathcustomfields["livello"]->value - 1], "theme_fsbac");
                                }
                            }
                            $pathduration = get_path_total_duration($path->course);
                            if (!empty($pathduration)) {
                                $pathinfo["duration"] = convert_minutes_into_hours_minutes($pathduration);
                            }
                            $data->paths["pathsinfo"][] = $pathinfo;
                        }
                    } else {
                        foreach ($paths as $path) {
                            $pathname = $DB->get_field("course", "fullname", array("id" => $path->course));
                            $data->paths["pathsinfo"][] = array("pathlink" => new \moodle_url("/course/view.php", array("id" => $path->course)), "pathname" => $pathname);
                        }
                    }
                }
                // fine percorsi collegati
                // inizio controllo accettazione policy
                if (isloggedin() && !isguestuser()) {
                    $data->isloggedinandnotguest = true;
                    $isdicolabcourse = false;
                    $pnrrcustomfieldid = $DB->get_field("customfield_field", "id", array("shortname" => "pnrr"));
                    $pnrrcustomfield = $DB->get_record("customfield_data", array("fieldid" => $pnrrcustomfieldid, "contextid" => $coursecontextinstance->id, "intvalue" => 1));
                    if ($pnrrcustomfield) {
                        $isdicolabcourse = true;
                    }
                    $sql = "SELECT *
                              FROM {tool_policy_versions}
                             WHERE optional = 1
                               AND archived = 0
                               AND summary LIKE ?";
                    $summary = "%fsbac_necessario%";
                    if ($isdicolabcourse) {
                        $summary = "%dicolab_necessario%";
                    }
                    $policycurrentversion = $DB->get_record_sql($sql, array($summary));
                    if ($policycurrentversion) {
                        $ispolicycurrentversionaccepted = false;
                        $policycurrentversionacceptance = $DB->get_record("tool_policy_acceptances", array(
                            "policyversionid" => $policycurrentversion->id,
                            "userid" => $USER->id,
                            "status" => 1
                        ));
                        if ($policycurrentversionacceptance) {
                            $ispolicycurrentversionaccepted = true;
                        }
                        $data->policyid = $policycurrentversion->id;
                        $data->ispolicycurrentversionaccepted = $ispolicycurrentversionaccepted;
                    } else {
                        $data->ispolicycurrentversionaccepted = true;
                    }
                }
                // fine controllo accettazione policy
            }
        }
        $theme = \theme_config::load('fsbac');
        $imageBannerBlueImage = $theme->setting_file_url('banner_blue_image', 'banner_blue_image');
        $data->imageBannerBlueImage = $imageBannerBlueImage;
        $data->linkPaths = $this->get_links_paths($COURSE->id);

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
        return 'format_corsofsbac/local/content';
    }


    private function get_links_paths($courseId)
    {
        global $DB, $CFG;

        $sql = "select cm.course, c.fullname from {subcourse} s
                inner join {course_modules} cm on s.id=cm.instance and cm.visible=1
                inner join {modules} m on cm.module=m.id and m.name='subcourse'
                inner join {course} c on cm.course=c.id
                where s.refcourse= ?
                ";
        $paths = $DB->get_records_sql($sql, array($courseId));

        $data = new stdClass();
        $data->count = count($paths);
        $data->paths = [];
        foreach ($paths as $path) {
            $p = get_course($path->course);
            $p->fullname = format_string($p->fullname);
            $p->viewurl = $CFG->wwwroot . '/course/view.php?id=' . $path->course;
            $data->paths = $p;
        }
        return $data;
    }

    /**
     * Get a list of activities for which completion is enabled on the
     * course. The list is ordered by the section order of those activities
     * and set the certificates activities as last in order
     *
     * @param cm_info[] Array from $cmid => $cm of all activities with completion enabled
     * @return cm_info[] Array from $cmid => $cm of all activities with completion enabled
     *
     */
    private function setcertificatesaslastmodules($modules)
    {
        $certificates = array();

        foreach ($modules as $cmid => $cminfo) {
            if ($cminfo->modname == "certificate") {
                $certificates[$cmid] = $cminfo;
                unset($modules[$cmid]);
            }
        }

        if (!empty($certificates)) {
            foreach ($certificates as $cmid => $cminfo) {
                $modules[$cmid] = $cminfo;
            }
        }

        return $modules;
    }
}
