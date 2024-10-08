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

$PAGE->set_url('/local/fsbaclogin/profile.php');
$PAGE->set_context(context_system::instance());

$PAGE->set_title(get_string("editprofile", "local_fsbaclogin"));

$PAGE->add_body_class('limitedwidth');

$generalfields = array();
$additionalfields = array();
$preferences = array();

$cffieldid = $DB->get_field("user_info_field", "id", array("shortname" => "CF"));
$cf = $DB->get_field("user_info_data", "data", array("fieldid" => $cffieldid, "userid" => $USER->id));
if ($cf) {
    $cfvalue = $cf;
} else {
    $cfvalue = "";
}
$cflabel = $DB->get_field("user_info_field", "name", array("shortname" => "CF"));
$generalfields[$cflabel] = $cfvalue;

$email = $DB->get_field("user", "email", array("id" => $USER->id));
$generalfields[get_string("email")] = $email;

$namefields = useredit_get_required_name_fields();
foreach ($namefields as $field) {
    $fieldvalue = $DB->get_field("user", $field, array("id" => $USER->id));
    $generalfields[get_string($field)] = $fieldvalue;
}

$country = $DB->get_field("user", "country", array("id" => $USER->id));
$additionalfields[get_string("country")] = $country;

if ($country == "IT") {
    $sql = "SELECT uid.id, uif.shortname, uid.data
              FROM {user_info_data} uid
              JOIN {user_info_field} uif
                ON uif.id = uid.fieldid
             WHERE uif.shortname = 'provinciamenu'
               AND uid.userid = ?";
    $provinciamenu = $DB->get_record_sql($sql, array($USER->id));
    $additionalfields[get_string("provinciamenu", "local_fsbaclogin")] = $provinciamenu->data;
}

$city = $DB->get_field("user", "city", array("id" => $USER->id));
$additionalfields[get_string("city")] = $city;

$additionalfieldsnames = array(
    "titolo_studio",
    "tipologia_utente",
    "specifica_settore",
    "networkappartenenza",
    "organizzazione_pubblico",
    "specifica",
    "qualifica",
    "professione",
    "ambito",
    "specifica_ambito",
    "organizzazione_noprofit",
    "organizzazione_privato"
);
[$insql, $inparams] = $DB->get_in_or_equal($additionalfieldsnames);
$sql = "SELECT uid.id, uif.shortname, uid.data
          FROM {user_info_data} uid
          JOIN {user_info_field} uif
            ON uif.id = uid.fieldid
         WHERE uif.shortname $insql
           AND uid.userid = ?
      ORDER BY uif.sortorder";
$inparams[] = $USER->id;
$additionalfieldsdata = $DB->get_records_sql($sql, $inparams);
foreach ($additionalfieldsdata as $additionalfielddata) {
    if (in_array($additionalfielddata->shortname, array("specifica", "specifica_ambito", "organizzazione_noprofit", "organizzazione_privato"))) {
        $fieldvalue = $additionalfielddata->data;
    } else {
        if ($additionalfielddata->data && $additionalfielddata->data != "--") {
            if ($additionalfielddata->shortname == "networkappartenenza") {
                $fieldvalue = implode(", ", array_map(function ($value) {return get_string($value, "local_fsbaclogin");}, explode(",", $additionalfielddata->data)));
            } else {
                $fieldvalue = get_string($additionalfielddata->data, "local_fsbaclogin");
            }
        }
    }
    $additionalfields[get_string($additionalfielddata->shortname, "local_fsbaclogin")] = $fieldvalue;
}

$usercontext = \context_user::instance($USER->id);
$sql = "SELECT ti.id, t.name
          FROM {tag_instance} ti
          JOIN {tag} t
            ON ti.tagid = t.id and ti.itemtype='user'
         WHERE ti.itemid = ?";
$tags = $DB->get_records_sql($sql, array($USER->id));
foreach ($tags as $tag) {
    $preferences[] = get_string("tag." . $tag->name, "local_fsbaclogin");
}

echo $OUTPUT->header();

$user_profile = new \local_fsbaclogin\output\user_profile($generalfields, $additionalfields, $preferences);
$renderer = $PAGE->get_renderer('local_fsbaclogin');
echo $renderer->render($user_profile);

echo $OUTPUT->footer();
