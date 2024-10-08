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
 * user signup page.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot . '/user/editlib.php');

$courseid = required_param('courseid', PARAM_INT);

if (!isloggedin() || isguestuser()) {
    redirect(new moodle_url("/local/fsbaclogin/index.php"));
}

$baseurl = "/local/fsbaclogin/completed_fsbac_course.php";
$PAGE->set_url(new moodle_url($baseurl, array("courseid" => $courseid)));
$PAGE->set_context(context_system::instance());

$PAGE->set_title(get_string("completeddicolabcourse", "local_fsbaclogin"));

echo $OUTPUT->header();

$completed_fsbac_course = new \local_fsbaclogin\output\completed_fsbac_course($courseid);
$renderer = $PAGE->get_renderer('local_fsbaclogin');
echo $renderer->render($completed_fsbac_course);

echo $OUTPUT->footer();
