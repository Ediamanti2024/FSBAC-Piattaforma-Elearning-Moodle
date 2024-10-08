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

$cf = optional_param('cf', '', PARAM_TEXT);
$notitaliancf = optional_param('notitaliancf', '', PARAM_TEXT);

if (!$authplugin = signup_is_enabled()) {
    throw new \moodle_exception('notlocalisederrormessage', 'error', '', 'Sorry, you may not use this page.');
}

$PAGE->set_url('/local/fsbaclogin/signup_with_cf.php');
$PAGE->set_context(context_system::instance());

$PAGE->requires->js_call_amd('local_fsbaclogin/remove_error_messages', 'init');

// If wantsurl is empty or /login/signup_with_cf.php, override wanted URL.
// We do not want to end up here again if user clicks "Login".
if (empty($SESSION->wantsurl)) {
    $SESSION->wantsurl = $CFG->wwwroot . '/';
} else {
    $wantsurl = new moodle_url($SESSION->wantsurl);
    if ($PAGE->url->compare($wantsurl, URL_MATCH_BASE)) {
        $SESSION->wantsurl = $CFG->wwwroot . '/';
    }
}

if (isloggedin() and !isguestuser()) {
    // Prevent signing up when already logged in.
    echo $OUTPUT->header();
    echo $OUTPUT->box_start();
    $logout = new single_button(new moodle_url(
        '/login/logout.php',
        array('sesskey' => sesskey(), 'loginpage' => 1)
    ), get_string('logout'), 'post');
    $continue = new single_button(new moodle_url('/'), get_string('cancel'), 'get');
    echo $OUTPUT->confirm(get_string('cannotsignup', 'error', fullname($USER)), $logout, $continue);
    echo $OUTPUT->box_end();
    echo $OUTPUT->footer();
    exit;
}

// If verification of age and location (digital minor check) is enabled.
if (\core_auth\digital_consent::is_age_digital_consent_verification_enabled()) {
    $cache = cache::make('core', 'presignup');
    $isminor = $cache->get('isminor');
    if ($isminor === false) {
        // The verification of age and location (minor) has not been done.
        redirect(new moodle_url('/local/fsbaclogin/verify_age_location.php'));
    } else if ($isminor === 'yes') {
        // The user that attempts to sign up is a digital minor.
        redirect(new moodle_url('/local/fsbaclogin/digital_minor.php'));
    }
}
// Plugins can create pre sign up requests.
// Can be used to force additional actions before sign up such as acceptance of policies, validations, etc.
local_fsbaclogin_presignuprequests();

$newaccount = get_string('newaccount');
$login      = get_string('login');

$PAGE->navbar->add($login);
$PAGE->navbar->add($newaccount);

$PAGE->set_pagelayout('login');
$PAGE->set_title($newaccount);
$PAGE->set_heading($SITE->fullname);

if (empty($notitaliancf)) {
    if (empty($cf) || is_cf_invalid($cf) || cf_exists($cf)) {
        if (cf_exists($cf)) {
            $cf_exists = true;
        } else {
            $cf_exists = false;
        }
        $mform_fiscalcode = new local_fsbaclogin\signup_with_cf_form(null, array("cf_exists" => $cf_exists));
        if ($mform_fiscalcode->is_cancelled()) {
            redirect(get_login_url());
        } else if ($fiscalcode = $mform_fiscalcode->get_data()) {
            // Mi serve solo per visualizzare se il codice fiscale ha un formato corretto
        } else {
            echo $OUTPUT->header();
            $languagedata = new \core\output\language_menu($PAGE);
            $languagemenu = $languagedata->export_for_action_menu($OUTPUT);
            $languagemenu = new \local_fsbaclogin\output\language_menu($languagemenu);
            $renderer = $PAGE->get_renderer('local_fsbaclogin');
            $logourl = $OUTPUT->get_logo_url("150", "150");
            echo $OUTPUT->render_from_template('local_fsbaclogin/signup_with_cf', [
                'form' => $mform_fiscalcode->render(),
                'languagemenu' => $renderer->render($languagemenu),
                'logourl' => $logourl,
                'cfExistsError' => cf_exists($cf)
            ]);

            echo $OUTPUT->footer();
        }
    } else {
        $mform_signup = new local_fsbaclogin\signup_form(null, array('cf' => $cf));
        if ($mform_signup->is_cancelled()) {
            redirect(get_login_url());
        } else if ($user = $mform_signup->get_data()) {
            // Add missing required fields.
            $user = signup_setup_new_user($user);

            // Plugins can perform post sign up actions once data has been validated.
            local_fsbaclogin_postsignuprequests($user);

            // $authplugin->user_signup($user, true); // prints notice and link to login/index.php
            local_fsbaclogin_usersignup($user, true);
        } else {
            echo $OUTPUT->header();
            $buttonlabel = get_string("login.signup-data_button", "theme_fsbac");
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
    }
} else {
    $mform_signup = new local_fsbaclogin\signup_form(null, array("notitaliancf" => $notitaliancf));
    if ($mform_signup->is_cancelled()) {
        redirect(get_login_url());
    } else if ($user = $mform_signup->get_data()) {
        // Add missing required fields.
        $user = signup_setup_new_user($user);

        // Plugins can perform post sign up actions once data has been validated.
        local_fsbaclogin_postsignuprequests($user);

        // $authplugin->user_signup($user, true); // prints notice and link to login/index.php
        local_fsbaclogin_usersignup($user, true);
    } else {
        echo $OUTPUT->header();
        $buttonlabel = get_string("login.signup-data_button", "theme_fsbac");
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
}
