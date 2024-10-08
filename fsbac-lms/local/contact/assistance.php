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
 * @package    local_contact
 * @subpackage auth
 * @copyright  1999 Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$PAGE->set_url('/local/contact/assistance.php');
$PAGE->set_context(context_system::instance());

$PAGE->set_title(get_string("bisognodiassistenza", "local_contact"));
$PAGE->set_heading(get_string("bisognodiassistenza", "local_contact"));

echo $OUTPUT->header();
$defaultname = "";
$defaultemail = "";
if (isloggedin() && !isguestuser()) {
    $user = $DB->get_record("user", array("id" => $USER->id));
    $defaultname = $user->firstname . " " . $user->lastname;
    $defaultemail = $user->email;
}
$assistance = new \local_contact\output\assistance($defaultname, $defaultemail);
$renderer = $PAGE->get_renderer('local_contact');
echo $renderer->render($assistance);
echo $OUTPUT->footer();
