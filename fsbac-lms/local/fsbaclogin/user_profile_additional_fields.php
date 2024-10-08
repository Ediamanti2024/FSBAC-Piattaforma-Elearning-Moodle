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

$makeonlyadditionalfields = optional_param("makeonlyadditionalfields", false, PARAM_BOOL);

$PAGE->set_url('/local/fsbaclogin/user_profile_general_fields.php', array("makeonlyadditionalfields" => $makeonlyadditionalfields));
$PAGE->set_context(context_system::instance());

$PAGE->requires->js_call_amd('local_fsbaclogin/login_conditional_fields', 'init');
$PAGE->requires->js_call_amd('local_fsbaclogin/remove_error_messages', 'init');

$PAGE->set_pagelayout('login');
$PAGE->set_title(get_string("user_profile_additional_fields", "local_fsbaclogin"));
$PAGE->set_heading(get_string("user_profile_additional_fields", "local_fsbaclogin"));

$mform_signup = new local_fsbaclogin\user_profile_additional_fields_form(null, array("makeonlyadditionalfields" => $makeonlyadditionalfields));
if ($mform_signup->is_cancelled()) {
    redirect(get_login_url());
} else if ($user = $mform_signup->get_data()) {
    if ($user->country != "IT") {
        unset($user->profile_field_provinciamenu);
    }
    if ($user->profile_field_tipologia_utente == "lavoratore") {
        if ($user->profile_field_specifica_settore == "--") {
            unset($user->profile_field_networkappartenenza);
            unset($user->profile_field_organizzazione_pubblico);
            unset($user->profile_field_specifica);
            unset($user->profile_field_qualifica);
            unset($user->profile_field_professione);
            unset($user->profile_field_ambito);
            unset($user->profile_field_specifica_ambito);
            unset($user->profile_field_organizzazione_noprofit);
            unset($user->profile_field_organizzazione_privato);
        } else {
            if ($user->profile_field_specifica_settore == "pubblico") {
                unset($user->profile_field_organizzazione_noprofit);
                unset($user->profile_field_organizzazione_privato);
            } else {
                unset($user->profile_field_organizzazione_pubblico);
                unset($user->profile_field_specifica);
                unset($user->profile_field_qualifica);
                if ($user->profile_field_specifica_settore == "privato") {
                    unset($user->profile_field_organizzazione_noprofit);
                } else if ($user->profile_field_specifica_settore == "non_profit") {
                    unset($user->profile_field_organizzazione_privato);
                }
            }
            if ($user->profile_field_ambito != "altro") {
                unset($user->profile_field_specifica_ambito);
            }
        }
    } else {
        unset($user->profile_field_specifica_settore);
        unset($user->profile_field_networkappartenenza);
        unset($user->profile_field_organizzazione_pubblico);
        unset($user->profile_field_specifica);
        unset($user->profile_field_qualifica);
        unset($user->profile_field_professione);
        unset($user->profile_field_ambito);
        unset($user->profile_field_specifica_ambito);
        unset($user->profile_field_organizzazione_noprofit);
        unset($user->profile_field_organizzazione_privato);
    }
    if ($makeonlyadditionalfields) {
        $additionalfieldsnames = array("provinciamenu",
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
                  AND uid.userid = ?";
        $inparams[] = $USER->id;
        $existingadditionalfields = $DB->get_records_sql($sql, $inparams);
        foreach ($existingadditionalfields as $existingadditionalfield) {
            $DB->delete_records("user_info_data", array("id" => $existingadditionalfield->id));
        }
    }
    $customfields = (array) $user;
    foreach ($customfields as $customfieldname => $customfieldvalue) {
        if ($customfieldname == "city") {
            $updateobj = new stdClass();
            $updateobj->id = $USER->id;
            $updateobj->city = $customfieldvalue;
            $DB->update_record("user", $updateobj);
        }
        if ($customfieldname == "country") {
            $updateobj = new stdClass();
            $updateobj->id = $USER->id;
            $updateobj->country = $customfieldvalue;
            $DB->update_record("user", $updateobj);
        }
        if (strpos($customfieldname, "profile_field_") !== false) {
            if (!empty($customfieldvalue) && $customfieldvalue != "--") {
                $customfieldname = str_replace("profile_field_", "", $customfieldname);
                $customfieldid = $DB->get_field("user_info_field", "id", array("shortname" => $customfieldname));
                $existingfield = $DB->get_record("user_info_data", array("userid" => $USER->id, "fieldid" => $customfieldid));
                if ($existingfield) {
                    $DB->delete_records("user_info_data", array("id" => $existingfield->id));
                }
                if ($customfieldname == "networkappartenenza") {
                    $customfieldvalue = implode(",", $customfieldvalue);
                }
                $insertobj = new stdClass();
                $insertobj->userid = $USER->id;
                $insertobj->fieldid = $customfieldid;
                $insertobj->data = $customfieldvalue;
                $insertobj->dataformat = 0;
                $DB->insert_record("user_info_data", $insertobj);
            }
        }
    }

    $fillednewadditionalfieldsfieldid = $DB->get_field("user_info_field", "id", array("shortname" => "nuovicampiaggiuntivicompilati"));
    $existingfield = $DB->get_record("user_info_data", array("userid" => $USER->id, "fieldid" => $fillednewadditionalfieldsfieldid));
    if (!$existingfield) {
        $insertobj = new stdClass();
        $insertobj->userid = $USER->id;
        $insertobj->fieldid = $fillednewadditionalfieldsfieldid;
        $insertobj->data = 1;
        $insertobj->dataformat = 0;
        $DB->insert_record("user_info_data", $insertobj);
    } else {
        if ($existingfield->data == 0) {
            $updateobj = new stdClass();
            $updateobj->id = $existingfield->id;
            $updateobj->data = 1;
            $DB->update_record("user_info_data", $updateobj);
        }
    }

    if ($makeonlyadditionalfields) {
        redirect($CFG->wwwroot);
    } else {
        $profilazionefieldid = $DB->get_field("user_info_field", "id", array("shortname" => "profilazione"));
        $existingfield = $DB->get_record("user_info_data", array("userid" => $USER->id, "fieldid" => $profilazionefieldid));
        if ($existingfield) {
            $DB->delete_records("user_info_data", array("id" => $existingfield->id));
        }
        redirect(new moodle_url('/local/fsbaclogin/profilazione.php'));
    }
} else {
    echo $OUTPUT->header();

    $buttonlabel = $makeonlyadditionalfields ? get_string("aggiornacampiaggiuntivi", "local_fsbaclogin") : get_string("inseriscicampiaggiuntivi", "local_fsbaclogin");
    $languagedata = new \core\output\language_menu($PAGE);
    $languagemenu = $languagedata->export_for_action_menu($OUTPUT);
    $languagemenu = new \local_fsbaclogin\output\language_menu($languagemenu);
    $renderer = $PAGE->get_renderer('local_fsbaclogin');
    $logourl = $OUTPUT->get_logo_url("150", "150");
    echo $OUTPUT->render_from_template('local_fsbaclogin/signup_with_cf_data', [
        'form' => $mform_signup->render(),
        'languagemenu' => $renderer->render($languagemenu),
        'logourl' => $logourl,
        'buttonlabel' => $buttonlabel
    ]);
    echo $OUTPUT->footer();
}
