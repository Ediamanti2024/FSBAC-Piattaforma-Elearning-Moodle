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
 * Prints the contact form to the site's Data Protection Officer
 *
 * @copyright 2018 onwards Jun Pataleta
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package tool_dataprivacy
 */

require_once("../../../config.php");
require_once('lib.php');

$courseid = optional_param('course', 0, PARAM_INT);

$url = new moodle_url('/admin/tool/dataprivacy/mydatarequests.php');
if ($courseid) {
    $url->param('course', $courseid);
}

$PAGE->set_url($url);

// ARIADNE INIZIO MODIFICA CORE
$PAGE->add_body_class('limitedwidth');
// ARIADNE FINE MODIFICA CORE

require_login();
if (isguestuser()) {
    throw new \moodle_exception('noguest');
}

$usercontext = context_user::instance($USER->id);
// ARIADNE INIZIO MODIFICA CORE
// $PAGE->set_context($usercontext);
// ARIADNE FINE MODIFICA CORE

if ($profilenode = $PAGE->settingsnav->find('myprofile', null)) {
    $profilenode->make_active();
}

$title = get_string('datarequests', 'tool_dataprivacy');
// ARIADNE INIZIO MODIFICA CORE
// $PAGE->navbar->add($title);
// ARIADNE FINE MODIFICA CORE

// Return URL.
$params = ['id' => $USER->id];
if ($courseid) {
    $params['course'] = $courseid;
}
$returnurl = new moodle_url('/user/profile.php', $params);

$PAGE->set_heading($title);
$PAGE->set_title($title);
echo $OUTPUT->header();
// ARIADNE INIZIO MODIFICA CORE
// echo $OUTPUT->heading($title);
// ARIADNE FINE MODIFICA CORE

$requests = tool_dataprivacy\api::get_data_requests($USER->id, [], [], [], 'timecreated DESC');
$requestlist = new tool_dataprivacy\output\my_data_requests_page($requests);
$requestlistoutput = $PAGE->get_renderer('tool_dataprivacy');
echo $requestlistoutput->render($requestlist);

echo $OUTPUT->footer();
