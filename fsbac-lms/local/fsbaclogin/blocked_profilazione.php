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


if (!isloggedin() || isguestuser()) {
    redirect(new moodle_url("/local/fsbaclogin/index.php"));
}

$baseurl = "/local/fsbaclogin/blocked_profilazione.php";
$PAGE->set_url(new moodle_url($baseurl));
$PAGE->set_context(context_system::instance());

$PAGE->set_title(get_string("blockedprofilazione", "local_fsbaclogin"));

echo $OUTPUT->header();

$blocked_profilazione = new \local_fsbaclogin\output\blocked_profilazione();
$renderer = $PAGE->get_renderer('local_fsbaclogin');
echo $renderer->render($blocked_profilazione);

echo $OUTPUT->footer();
