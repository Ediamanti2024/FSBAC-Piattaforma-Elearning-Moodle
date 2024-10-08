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
require_once($CFG->libdir . '/authlib.php');
require_once('locallib.php');

if (!isloggedin() || isguestuser()) {
    redirect(new moodle_url("/local/fsbaclogin/index.php"));
}

$PAGE->set_url('/local/fsbaclogin/user_profile_general_fields.php');
$PAGE->set_context(context_system::instance());

$PAGE->requires->js_call_amd('local_fsbaclogin/remove_error_messages', 'init');

$PAGE->set_pagelayout('login');
$PAGE->set_title(get_string("user_profile_general_fields", "local_fsbaclogin"));
$PAGE->set_heading(get_string("user_profile_general_fields", "local_fsbaclogin"));

$user_profile_general_fields_form = new local_fsbaclogin\user_profile_general_fields_form();
if ($user_profile_general_fields_form->is_cancelled()) {
    redirect(get_login_url());
} else if ($user_profile_general_fields = $user_profile_general_fields_form->get_data()) {
    $user_profile_general_fields = (array) $user_profile_general_fields;
    foreach ($user_profile_general_fields as $fieldname => $fieldvalue) {
        if ($fieldname == "firstname" || $fieldname == "lastname") {
            $updateobj = new stdClass();
            $updateobj->id = $USER->id;
            $updateobj->$fieldname = $fieldvalue;
            $DB->update_record("user", $updateobj);
        }
        if ($fieldname == "email") {
            $updateobj = new stdClass();
            $updateobj->id = $USER->id;
            $updateobj->username = $fieldvalue;
            $updateobj->$fieldname = $fieldvalue;
            $DB->update_record("user", $updateobj);
        }
        if ($fieldname == "cf") {
            if (!empty(trim($fieldvalue))) {
                $sql = "SELECT uid.id, uif.shortname, uid.data
                          FROM {user_info_data} uid
                          JOIN {user_info_field} uif
                            ON uif.id = uid.fieldid
                         WHERE uif.shortname = 'CF'
                           AND uid.userid = ?";
                $usercfdata = $DB->get_record_sql($sql, array($USER->id));
                if ($usercfdata) {
                    $updateobj = new stdClass();
                    $updateobj->id = $usercfdata->id;
                    $updateobj->data = strtoupper(trim($fieldvalue));
                    $DB->update_record("user_info_data", $updateobj);
                } else {
                    $cffieldid = $DB->get_field("user_info_field", "id", array("shortname" => "CF"));
                    $insertobj = new stdClass();
                    $insertobj->userid = $USER->id;
                    $insertobj->fieldid = $cffieldid;
                    $insertobj->data = strtoupper(trim($fieldvalue));
                    $insertobj->dataformat = 0;
                    $DB->insert_record("user_info_data", $insertobj);
                }
            }
        }
    }

    redirect($CFG->wwwroot);

} else {
    echo $OUTPUT->header();

    $buttonlabel = get_string("aggiornacampigenerali", "local_fsbaclogin");
    $languagedata = new \core\output\language_menu($PAGE);
    $languagemenu = $languagedata->export_for_action_menu($OUTPUT);
    $languagemenu = new \local_fsbaclogin\output\language_menu($languagemenu);
    $renderer = $PAGE->get_renderer('local_fsbaclogin');
    $logourl = $OUTPUT->get_logo_url("150", "150");
    echo $OUTPUT->render_from_template('local_fsbaclogin/signup_with_cf_data', [
        'form' => $user_profile_general_fields_form->render(),
        'languagemenu' => $renderer->render($languagemenu),
        'logourl' => $logourl,
        'buttonlabel' => $buttonlabel
    ]);
    echo $OUTPUT->footer();
}
