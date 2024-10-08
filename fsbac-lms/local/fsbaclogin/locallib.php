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
 * Login library file of login/password related Moodle functions.
 *
 * @package    core
 * @subpackage lib
 * @copyright  Catalyst IT
 * @copyright  Peter Bulmer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 *  Processes a user's request to set a new password in the event they forgot the old one.
 *  If no user identifier has been supplied, it displays a form where they can submit their identifier.
 *  Where they have supplied identifier, the function will check their status, and send email as appropriate.
 */
function local_fsbaclogin_processpasswordresetrequest()
{
    global $OUTPUT, $PAGE;
    $mform = new local_fsbaclogin\forgot_password_form();

    if ($mform->is_cancelled()) {
        redirect(get_login_url());
    } else if ($data = $mform->get_data()) {

        $username = $email = '';
        if (!empty($data->username)) {
            $username = $data->username;
        } else {
            $email = $data->email;
        }
        list($status, $notice, $url) = local_fsbaclogin_processpasswordreset($username, $email);

        // Plugins can perform post forgot password actions once data has been validated.
        local_fsbaclogin_postforgotpasswordrequests($data);

        // Any email has now been sent.
        // Next display results to requesting user if settings permit.
        echo $OUTPUT->header();
        notice($notice, $url);
        die; // Never reached.
    }

    // DISPLAY FORM.

    echo $OUTPUT->header();


    $languagedata = new \core\output\language_menu($PAGE);
    $languagemenu = $languagedata->export_for_action_menu($OUTPUT);
    $languagemenu = new \local_fsbaclogin\output\language_menu($languagemenu);
    $renderer = $PAGE->get_renderer('local_fsbaclogin');
    $logourl = $OUTPUT->get_logo_url("150", "150");
    echo $OUTPUT->render_from_template('local_fsbaclogin/forgot_password_email', [
        'form' => $mform->render(),
        'languagemenu' => $renderer->render($languagemenu),
        'logourl' =>  $logourl
    ]);
    echo $OUTPUT->footer();
}

/**
 * Process the password reset for the given user (via username or email).
 *
 * @param  string $username the user name
 * @param  string $email    the user email
 * @return array an array containing fields indicating the reset status, a info notice and redirect URL.
 * @since  Moodle 3.4
 */
function local_fsbaclogin_processpasswordreset($username, $email)
{
    global $CFG, $DB;

    define('PWRESET_STATUS_NOEMAILSENT', 1);
    define('PWRESET_STATUS_TOKENSENT', 2);
    define('PWRESET_STATUS_OTHEREMAILSENT', 3);
    define('PWRESET_STATUS_ALREADYSENT', 4);

    if (empty($username) && empty($email)) {
        throw new \moodle_exception('cannotmailconfirm');
    }

    // Next find the user account in the database which the requesting user claims to own.
    if (!empty($username)) {
        // Username has been specified - load the user record based on that.
        $username = core_text::strtolower($username); // Mimic the login page process.
        $userparams = array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id, 'deleted' => 0, 'suspended' => 0);
        $user = $DB->get_record('user', $userparams);
    } else {
        // Try to load the user record based on email address.
        // This is tricky because:
        // 1/ the email is not guaranteed to be unique - TODO: send email with all usernames to select the account for pw reset
        // 2/ mailbox may be case sensitive, the email domain is case insensitive - let's pretend it is all case-insensitive.
        //
        // The case-insensitive + accent-sensitive search may be expensive as some DBs such as MySQL cannot use the
        // index in that case. For that reason, we first perform accent-insensitive search in a subselect for potential
        // candidates (which can use the index) and only then perform the additional accent-sensitive search on this
        // limited set of records in the outer select.
        $sql = "SELECT *
                  FROM {user}
                 WHERE " . $DB->sql_equal('email', ':email1', false, true) . "
                   AND id IN (SELECT id
                                FROM {user}
                               WHERE mnethostid = :mnethostid
                                 AND deleted = 0
                                 AND suspended = 0
                                 AND " . $DB->sql_equal('email', ':email2', false, false) . ")";

        $params = array(
            'email1' => $email,
            'email2' => $email,
            'mnethostid' => $CFG->mnet_localhost_id,
        );

        $user = $DB->get_record_sql($sql, $params, IGNORE_MULTIPLE);
    }

    // Target user details have now been identified, or we know that there is no such account.
    // Send email address to account's email address if appropriate.
    $pwresetstatus = PWRESET_STATUS_NOEMAILSENT;
    if ($user and !empty($user->confirmed)) {
        $systemcontext = context_system::instance();

        $userauth = get_auth_plugin($user->auth);
        if (
            !$userauth->can_reset_password() or !is_enabled_auth($user->auth)
            or !has_capability('moodle/user:changeownpassword', $systemcontext, $user->id)
        ) {
            if (send_password_change_info($user)) {
                $pwresetstatus = PWRESET_STATUS_OTHEREMAILSENT;
            } else {
                throw new \moodle_exception('cannotmailconfirm');
            }
        } else {
            // The account the requesting user claims to be is entitled to change their password.
            // Next, check if they have an existing password reset in progress.
            $resetinprogress = $DB->get_record('user_password_resets', array('userid' => $user->id));
            if (empty($resetinprogress)) {
                // Completely new reset request - common case.
                $resetrecord = local_fsbaclogin_generatepasswordreset($user);
                $sendemail = true;
            } else if ($resetinprogress->timerequested < (time() - $CFG->pwresettime)) {
                // Preexisting, but expired request - delete old record & create new one.
                // Uncommon case - expired requests are cleaned up by cron.
                $DB->delete_records('user_password_resets', array('id' => $resetinprogress->id));
                $resetrecord = local_fsbaclogin_generatepasswordreset($user);
                $sendemail = true;
            } else if (empty($resetinprogress->timererequested)) {
                // Preexisting, valid request. This is the first time user has re-requested the reset.
                // Re-sending the same email once can actually help in certain circumstances
                // eg by reducing the delay caused by greylisting.
                $resetinprogress->timererequested = time();
                $DB->update_record('user_password_resets', $resetinprogress);
                $resetrecord = $resetinprogress;
                $sendemail = true;
            } else {
                // Preexisting, valid request. User has already re-requested email.
                $pwresetstatus = PWRESET_STATUS_ALREADYSENT;
                $sendemail = false;
            }

            if ($sendemail) {
                $sendresult = local_fsbaclogin_sendpasswordchangeconfirmationemail($user, $resetrecord);
                if ($sendresult) {
                    $pwresetstatus = PWRESET_STATUS_TOKENSENT;
                } else {
                    throw new \moodle_exception('cannotmailconfirm');
                }
            }
        }
    }

    $url = $CFG->wwwroot . '/index.php';
    if (!empty($CFG->protectusernames)) {
        // Neither confirm, nor deny existance of any username or email address in database.
        // Print general (non-commital) message.
        $status = 'emailpasswordconfirmmaybesent';
        $notice = get_string($status);
    } else if (empty($user)) {
        // Protect usernames is off, and we couldn't find the user with details specified.
        // Print failure advice.
        $status = 'emailpasswordconfirmnotsent';
        $notice = get_string($status);
        $url = $CFG->wwwroot . '/forgot_password.php';
    } else if (empty($user->email)) {
        // User doesn't have an email set - can't send a password change confimation email.
        $status = 'emailpasswordconfirmnoemail';
        $notice = get_string($status);
    } else if ($pwresetstatus == PWRESET_STATUS_ALREADYSENT) {
        // User found, protectusernames is off, but user has already (re) requested a reset.
        // Don't send a 3rd reset email.
        $status = 'emailalreadysent';
        $notice = get_string($status);
    } else if ($pwresetstatus == PWRESET_STATUS_NOEMAILSENT) {
        // User found, protectusernames is off, but user is not confirmed.
        // Pretend we sent them an email.
        // This is a big usability problem - need to tell users why we didn't send them an email.
        // Obfuscate email address to protect privacy.
        $protectedemail = preg_replace('/([^@]*)@(.*)/', '******@$2', $user->email);
        $status = 'emailpasswordconfirmsent';
        $notice = get_string($status, '', $protectedemail);
    } else {
        // Confirm email sent. (Obfuscate email address to protect privacy).
        $protectedemail = preg_replace('/([^@]*)@(.*)/', '******@$2', $user->email);
        // This is a small usability problem - may be obfuscating the email address which the user has just supplied.
        $status = 'emailresetconfirmsent';
        $notice = get_string($status, '', $protectedemail);
    }
    return array($status, $notice, $url);
}

/**
 * Sends a password change confirmation email.
 *
 * @param stdClass $user A {@link $USER} object
 * @param stdClass $resetrecord An object tracking metadata regarding password reset request
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function local_fsbaclogin_sendpasswordchangeconfirmationemail($user, $resetrecord)
{
    global $CFG;

    $site = get_site();
    $supportuser = core_user::get_support_user();
    $pwresetmins = isset($CFG->pwresettime) ? floor($CFG->pwresettime / MINSECS) : 30;

    $data = new stdClass();
    $data->firstname = $user->firstname;
    $data->lastname  = $user->lastname;
    $data->username  = $user->username;
    $data->sitename  = format_string($site->fullname);
    $data->link      = $CFG->wwwroot . '/local/fsbaclogin/forgot_password.php?token=' . $resetrecord->token;
    $data->admin     = generate_email_signoff();
    $data->resetminutes = $pwresetmins;

    $message = get_string('emailresetconfirmation', '', $data);
    $subject = get_string('emailresetconfirmationsubject', '', format_string($site->fullname));

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    return email_to_user($user, $supportuser, $subject, $message);
}

/**
 * This function processes a user's submitted token to validate the request to set a new password.
 * If the user's token is validated, they are prompted to set a new password.
 * @param string $token the one-use identifier which should verify the password reset request as being valid.
 * @return void
 */
function local_fsbaclogin_processpasswordset($token)
{
    global $DB, $CFG, $OUTPUT, $PAGE, $SESSION;
    require_once($CFG->dirroot . '/user/lib.php');

    $pwresettime = isset($CFG->pwresettime) ? $CFG->pwresettime : 1800;
    $sql = "SELECT u.*, upr.token, upr.timerequested, upr.id as tokenid
              FROM {user} u
              JOIN {user_password_resets} upr ON upr.userid = u.id
             WHERE upr.token = ?";
    $user = $DB->get_record_sql($sql, array($token));

    $forgotpasswordurl = "{$CFG->wwwroot}/local/fsbaclogin/forgot_password.php";
    if (empty($user) or ($user->timerequested < (time() - $pwresettime - DAYSECS))) {
        // There is no valid reset request record - not even a recently expired one.
        // (suspicious)
        // Direct the user to the forgot password page to request a password reset.
        echo $OUTPUT->header();
        notice(get_string('noresetrecord'), $forgotpasswordurl);
        die; // Never reached.
    }
    if ($user->timerequested < (time() - $pwresettime)) {
        // There is a reset record, but it's expired.
        // Direct the user to the forgot password page to request a password reset.
        $pwresetmins = floor($pwresettime / MINSECS);
        echo $OUTPUT->header();
        notice(get_string('resetrecordexpired', '', $pwresetmins), $forgotpasswordurl);
        die; // Never reached.
    }

    if ($user->auth === 'nologin' or !is_enabled_auth($user->auth)) {
        // Bad luck - user is not able to login, do not let them set password.
        echo $OUTPUT->header();
        throw new \moodle_exception('forgotteninvalidurl');
        die; // Never reached.
    }

    // Check this isn't guest user.
    if (isguestuser($user)) {
        throw new \moodle_exception('cannotresetguestpwd');
    }

    // Token is correct, and unexpired.
    $mform = new local_fsbaclogin\set_password_form(null, $user);
    $data = $mform->get_data();
    if (empty($data)) {
        // User hasn't submitted form, they got here directly from email link.
        // Next, display the form.
        $setdata = new stdClass();
        $setdata->username = $user->username;
        $setdata->username2 = $user->username;
        $setdata->token = $user->token;
        $mform->set_data($setdata);
        echo $OUTPUT->header();
        $languagedata = new \core\output\language_menu($PAGE);
        $languagemenu = $languagedata->export_for_action_menu($OUTPUT);
        $languagemenu = new \local_fsbaclogin\output\language_menu($languagemenu);
        $renderer = $PAGE->get_renderer('local_fsbaclogin');
        $logourl = $OUTPUT->get_logo_url("150", "150");
        echo $OUTPUT->render_from_template('local_fsbaclogin/reset_password', [
            'form' => $mform->render(),
            'languagemenu' => $renderer->render($languagemenu),
            'logourl' =>  $logourl
        ]);
        echo $OUTPUT->footer();
        return;
    } else {
        // User has submitted form.
        // Delete this token so it can't be used again.
        $DB->delete_records('user_password_resets', array('id' => $user->tokenid));
        $userauth = get_auth_plugin($user->auth);
        if (!$userauth->user_update_password($user, $data->password)) {
            throw new \moodle_exception('errorpasswordupdate', 'auth');
        }
        user_add_password_history($user->id, $data->password);
        if (!empty($CFG->passwordchangelogout)) {
            \core\session\manager::kill_user_sessions($user->id, session_id());
        }
        // Reset login lockout (if present) before a new password is set.
        login_unlock_account($user);
        // Clear any requirement to change passwords.
        unset_user_preference('auth_forcepasswordchange', $user);
        unset_user_preference('create_password', $user);

        if (!empty($user->lang)) {
            // Unset previous session language - use user preference instead.
            unset($SESSION->lang);
        }
        complete_user_login($user); // Triggers the login event.

        \core\session\manager::apply_concurrent_login_limit($user->id, session_id());

        $urltogo = local_fsbaclogin_getreturnurl();
        unset($SESSION->wantsurl);

        // Plugins can perform post set password actions once data has been validated.
        local_fsbaclogin_postsetpasswordrequests($data, $user);

        redirect($urltogo, get_string('passwordset'), 1);
    }
}

/** Create a new record in the database to track a new password set request for user.
 * @param object $user the user record, the requester would like a new password set for.
 * @return record created.
 */
function local_fsbaclogin_generatepasswordreset($user)
{
    global $DB;
    $resetrecord = new stdClass();
    $resetrecord->timerequested = time();
    $resetrecord->userid = $user->id;
    $resetrecord->token = random_string(32);
    $resetrecord->id = $DB->insert_record('user_password_resets', $resetrecord);
    return $resetrecord;
}

/**  Determine where a user should be redirected after they have been logged in.
 * @return string url the user should be redirected to.
 */
function local_fsbaclogin_getreturnurl()
{
    global $CFG, $SESSION, $USER;
    // Prepare redirection.
    if (user_not_fully_set_up($USER, true)) {
        $urltogo = $CFG->wwwroot . '/local/fsbaclogin/profilazione.php';
        // We don't delete $SESSION->wantsurl yet, so we get there later.

    } else if (isset($SESSION->wantsurl) and (strpos($SESSION->wantsurl, $CFG->wwwroot) === 0
        or strpos($SESSION->wantsurl, str_replace('http://', 'https://', $CFG->wwwroot)) === 0)) {
        $urltogo = $SESSION->wantsurl;    // Because it's an address in this site.
        unset($SESSION->wantsurl);
    } else {
        // No wantsurl stored or external - go to homepage.
        $urltogo = $CFG->wwwroot . '/';
        unset($SESSION->wantsurl);
    }

    // If the url to go to is the same as the site page, check for default homepage.
    if ($urltogo == ($CFG->wwwroot . '/')) {
        $homepage = get_home_page();
        // Go to my-moodle page instead of site homepage if defaulthomepage set to homepage_my.
        if ($homepage === HOMEPAGE_MY && !isguestuser()) {
            if ($urltogo == $CFG->wwwroot or $urltogo == $CFG->wwwroot . '/' or $urltogo == $CFG->wwwroot . '/index.php') {
                $urltogo = $CFG->wwwroot . '/my/';
            }
        }
        if ($homepage === HOMEPAGE_MYCOURSES && !isguestuser()) {
            if ($urltogo == $CFG->wwwroot or $urltogo == $CFG->wwwroot . '/' or $urltogo == $CFG->wwwroot . '/index.php') {
                $urltogo = $CFG->wwwroot . '/my/courses.php';
            }
        }
    }
    return $urltogo;
}

/**
 * Validates the forgot password form data.
 *
 * This is used by the forgot_password_form and by the core_auth_request_password_rest WS.
 * @param  array $data array containing the data to be validated (email and username)
 * @return array array of errors compatible with mform
 * @since  Moodle 3.4
 */
function local_fsbaclogin_validateforgotpassworddata($data)
{
    global $CFG, $DB;

    $errors = array();

    if ((!empty($data['username']) and !empty($data['email'])) or (empty($data['username']) and empty($data['email']))) {
        $errors['username'] = get_string('usernameoremail');
        $errors['email']    = get_string('usernameoremail');
    } else if (!empty($data['email'])) {
        if (!validate_email($data['email'])) {
            $errors['email'] = get_string('invalidemail');
        } else {
            try {
                $user = get_complete_user_data('email', $data['email'], null, true);
                if (empty($user->confirmed)) {
                    send_confirmation_email($user);
                    if (empty($CFG->protectusernames)) {
                        $errors['email'] = get_string('confirmednot');
                    }
                }
            } catch (dml_missing_record_exception $missingexception) {
                // User not found. Show error when $CFG->protectusernames is turned off.
                if (empty($CFG->protectusernames)) {
                    $errors['email'] = get_string('emailnotfound');
                }
            } catch (dml_multiple_records_exception $multipleexception) {
                // Multiple records found. Ask the user to enter a username instead.
                if (empty($CFG->protectusernames)) {
                    $errors['email'] = get_string('forgottenduplicate');
                }
            }
        }
    } else {
        if ($user = get_complete_user_data('username', $data['username'])) {
            if (empty($user->confirmed)) {
                send_confirmation_email($user);
                if (empty($CFG->protectusernames)) {
                    $errors['username'] = get_string('confirmednot');
                }
            }
        }
        if (!$user and empty($CFG->protectusernames)) {
            $errors['username'] = get_string('usernamenotfound');
        }
    }

    return $errors;
}

/**
 * Plugins can create pre sign up requests.
 */
function local_fsbaclogin_presignuprequests()
{
    $callbacks = get_plugins_with_function('pre_signup_requests');
    foreach ($callbacks as $type => $plugins) {
        foreach ($plugins as $plugin => $pluginfunction) {
            $pluginfunction();
        }
    }
}

/**
 * Plugins can extend forms.
 */

/** Inject form elements into change_password_form.
 * @param mform $mform the form to inject elements into.
 * @param stdClass $user the user object to use for context.
 */
function local_fsbaclogin_extend_change_password_form($mform, $user)
{
    $callbacks = get_plugins_with_function('extend_change_password_form');
    foreach ($callbacks as $type => $plugins) {
        foreach ($plugins as $plugin => $pluginfunction) {
            $pluginfunction($mform, $user);
        }
    }
}

/** Inject form elements into set_password_form.
 * @param mform $mform the form to inject elements into.
 * @param stdClass $user the user object to use for context.
 */
function local_fsbaclogin_extendsetpasswordform($mform, $user)
{
    $callbacks = get_plugins_with_function('extend_set_password_form');
    foreach ($callbacks as $type => $plugins) {
        foreach ($plugins as $plugin => $pluginfunction) {
            $pluginfunction($mform, $user);
        }
    }
}

/** Inject form elements into forgot_password_form.
 * @param mform $mform the form to inject elements into.
 */
function local_fsbaclogin_extendforgotpasswordform($mform)
{
    $callbacks = get_plugins_with_function('extend_forgot_password_form');
    foreach ($callbacks as $type => $plugins) {
        foreach ($plugins as $plugin => $pluginfunction) {
            $pluginfunction($mform);
        }
    }
}

/** Inject form elements into signup_form.
 * @param mform $mform the form to inject elements into.
 */
function local_fsbaclogin_extendsignupform($mform)
{
    $callbacks = get_plugins_with_function('extend_signup_form');
    foreach ($callbacks as $type => $plugins) {
        foreach ($plugins as $plugin => $pluginfunction) {
            $pluginfunction($mform);
        }
    }
}

/**
 * Plugins can add additional validation to forms.
 */

/** Inject validation into change_password_form.
 * @param array $data the data array from submitted form values.
 * @param stdClass $user the user object to use for context.
 * @return array $errors the updated array of errors from validation.
 */
function local_fsbaclogin_validate_extend_change_password_form($data, $user)
{
    $pluginsfunction = get_plugins_with_function('validate_extend_change_password_form');
    $errors = array();
    foreach ($pluginsfunction as $plugintype => $plugins) {
        foreach ($plugins as $pluginfunction) {
            $pluginerrors = $pluginfunction($data, $user);
            $errors = array_merge($errors, $pluginerrors);
        }
    }
    return $errors;
}

/** Inject validation into set_password_form.
 * @param array $data the data array from submitted form values.
 * @param stdClass $user the user object to use for context.
 * @return array $errors the updated array of errors from validation.
 */
function local_fsbaclogin_validateextendsetpasswordform($data, $user)
{
    $pluginsfunction = get_plugins_with_function('validate_extend_set_password_form');
    $errors = array();
    foreach ($pluginsfunction as $plugintype => $plugins) {
        foreach ($plugins as $pluginfunction) {
            $pluginerrors = $pluginfunction($data, $user);
            $errors = array_merge($errors, $pluginerrors);
        }
    }
    return $errors;
}

/** Inject validation into forgot_password_form.
 * @param array $data the data array from submitted form values.
 * @return array $errors the updated array of errors from validation.
 */
function local_fsbaclogin_validateextendforgotpasswordform($data)
{
    $pluginsfunction = get_plugins_with_function('validate_extend_forgot_password_form');
    $errors = array();
    foreach ($pluginsfunction as $plugintype => $plugins) {
        foreach ($plugins as $pluginfunction) {
            $pluginerrors = $pluginfunction($data);
            $errors = array_merge($errors, $pluginerrors);
        }
    }
    return $errors;
}

/** Inject validation into signup_form.
 * @param array $data the data array from submitted form values.
 * @return array $errors the updated array of errors from validation.
 */
function local_fsbaclogin_validateextendsignupform($data)
{
    $pluginsfunction = get_plugins_with_function('validate_extend_signup_form');
    $errors = array();
    foreach ($pluginsfunction as $plugintype => $plugins) {
        foreach ($plugins as $pluginfunction) {
            $pluginerrors = $pluginfunction($data);
            $errors = array_merge($errors, $pluginerrors);
        }
    }
    return $errors;
}

/**
 * Plugins can perform post submission actions.
 */

/** Post change_password_form submission actions.
 * @param stdClass $data the data object from the submitted form.
 */
function local_fsbaclogin_post_change_password_requests($data)
{
    $pluginsfunction = get_plugins_with_function('post_change_password_requests');
    foreach ($pluginsfunction as $plugintype => $plugins) {
        foreach ($plugins as $pluginfunction) {
            $pluginfunction($data);
        }
    }
}

/** Post set_password_form submission actions.
 * @param stdClass $data the data object from the submitted form.
 * @param stdClass $user the user object for set_password context.
 */
function local_fsbaclogin_postsetpasswordrequests($data, $user)
{
    $pluginsfunction = get_plugins_with_function('post_set_password_requests');
    foreach ($pluginsfunction as $plugintype => $plugins) {
        foreach ($plugins as $pluginfunction) {
            $pluginfunction($data, $user);
        }
    }
}

/** Post forgot_password_form submission actions.
 * @param stdClass $data the data object from the submitted form.
 */
function local_fsbaclogin_postforgotpasswordrequests($data)
{
    $pluginsfunction = get_plugins_with_function('post_forgot_password_requests');
    foreach ($pluginsfunction as $plugintype => $plugins) {
        foreach ($plugins as $pluginfunction) {
            $pluginfunction($data);
        }
    }
}

/** Post signup_form submission actions.
 * @param stdClass $data the data object from the submitted form.
 */
function local_fsbaclogin_postsignuprequests($data)
{
    $pluginsfunction = get_plugins_with_function('post_signup_requests');
    foreach ($pluginsfunction as $plugintype => $plugins) {
        foreach ($plugins as $pluginfunction) {
            $pluginfunction($data);
        }
    }
}

/**
 * Sign up a new user ready for confirmation.
 * Password is passed in plaintext.
 *
 * @param object $user new user object
 * @param boolean $notify print notice with link and terminate
 */
function local_fsbaclogin_usersignup($user, $notify = true)
{
    // Standard signup, without custom confirmatinurl.
    return local_fsbaclogin_usersignupwithconfirmation($user, $notify);
}

/**
 * Sign up a new user ready for confirmation.
 *
 * Password is passed in plaintext.
 * A custom confirmationurl could be used.
 *
 * @param object $user new user object
 * @param boolean $notify print notice with link and terminate
 * @param string $confirmationurl user confirmation URL
 * @return boolean true if everything well ok and $notify is set to true
 * @throws moodle_exception
 * @since Moodle 3.2
 */
function local_fsbaclogin_usersignupwithconfirmation($user, $notify = true, $confirmationurl = null)
{
    global $CFG, $DB, $SESSION;
    require_once($CFG->dirroot . '/user/profile/lib.php');
    require_once($CFG->dirroot . '/user/lib.php');

    $plainpassword = $user->password;
    $user->password = hash_internal_user_password($user->password);
    if (empty($user->calendartype)) {
        $user->calendartype = $CFG->calendartype;
    }

    $user->id = local_fsbaclogin_user_create_user($user, false, false);

    user_add_password_history($user->id, $plainpassword);

    profile_save_data($user);

    // Save fiscal code in custom profile field CF
    if (isset($user->cf)) {
        if ($fieldid = $DB->get_field('user_info_field', 'id', array("shortname" => "CF"))) {
            $insertobj = new stdClass();
            $insertobj->userid = $user->id;
            $insertobj->fieldid = $fieldid;
            $insertobj->data = strtoupper(trim($user->cf));
            $DB->insert_record('user_info_data', $insertobj);
        }
    }

    // Save wantsurl against user's profile, so we can return them there upon confirmation.
    if (!empty($SESSION->wantsurl)) {
        set_user_preference('auth_email_wantsurl', $SESSION->wantsurl, $user);
    }

    // Trigger event.
    \core\event\user_created::create_from_userid($user->id)->trigger();

    if (!local_fsbaclogin_sendconfirmationemail($user, $confirmationurl)) {
        throw new \moodle_exception('auth_emailnoemail', 'auth_email');
    }

    if ($notify) {
        global $CFG, $PAGE, $OUTPUT;
        $emailconfirm = get_string('emailconfirm');
        $PAGE->navbar->add($emailconfirm);
        $PAGE->set_title($emailconfirm);
        $PAGE->set_heading($PAGE->course->fullname);
        echo $OUTPUT->header();
        notice(get_string('emailconfirmsent', '', $user->email), "$CFG->wwwroot/index.php");
    } else {
        return true;
    }
}

/**
 * Creates a user
 *
 * @throws moodle_exception
 * @param stdClass|array $user user to create
 * @param bool $updatepassword if true, authentication plugin will update password.
 * @param bool $triggerevent set false if user_created event should not be triggred.
 *             This will not affect user_password_updated event triggering.
 * @return int id of the newly created user
 */
function local_fsbaclogin_user_create_user($user, $updatepassword = true, $triggerevent = true)
{
    global $DB;

    // Set the timecreate field to the current time.
    if (!is_object($user)) {
        $user = (object) $user;
    }

    // Check username.
    // if (trim($user->username) === '') {
    //     throw new moodle_exception('invalidusernameblank');
    // }

    // if ($user->username !== core_text::strtolower($user->username)) {
    //     throw new moodle_exception('usernamelowercase');
    // }

    // if ($user->username !== core_user::clean_field($user->username, 'username')) {
    //     throw new moodle_exception('invalidusername');
    // }
    $user->username = $user->email;

    // Save the password in a temp value for later.
    if ($updatepassword && isset($user->password)) {

        // Check password toward the password policy.
        if (!check_password_policy($user->password, $errmsg, $user)) {
            throw new moodle_exception($errmsg);
        }

        $userpassword = $user->password;
        unset($user->password);
    }

    // Apply default values for user preferences that are stored in users table.
    if (!isset($user->calendartype)) {
        $user->calendartype = core_user::get_property_default('calendartype');
    }
    if (!isset($user->maildisplay)) {
        $user->maildisplay = core_user::get_property_default('maildisplay');
    }
    if (!isset($user->mailformat)) {
        $user->mailformat = core_user::get_property_default('mailformat');
    }
    if (!isset($user->maildigest)) {
        $user->maildigest = core_user::get_property_default('maildigest');
    }
    if (!isset($user->autosubscribe)) {
        $user->autosubscribe = core_user::get_property_default('autosubscribe');
    }
    if (!isset($user->trackforums)) {
        $user->trackforums = core_user::get_property_default('trackforums');
    }
    if (!isset($user->lang)) {
        $user->lang = core_user::get_property_default('lang');
    }
    if (!isset($user->city)) {
        $user->city = core_user::get_property_default('city');
    }
    if (!isset($user->country)) {
        // The default value of $CFG->country is 0, but that isn't a valid property for the user field, so switch to ''.
        $user->country = core_user::get_property_default('country') ?: '';
    }

    $user->timecreated = time();
    $user->timemodified = $user->timecreated;

    // Validate user data object.
    $uservalidation = core_user::validate($user);
    if ($uservalidation !== true) {
        foreach ($uservalidation as $field => $message) {
            debugging("The property '$field' has invalid data and has been cleaned.", DEBUG_DEVELOPER);
            $user->$field = core_user::clean_field($user->$field, $field);
        }
    }

    // Insert the user into the database.
    $newuserid = $DB->insert_record('user', $user);

    // Create USER context for this user.
    $usercontext = context_user::instance($newuserid);

    // Update user password if necessary.
    if (isset($userpassword)) {
        // Get full database user row, in case auth is default.
        $newuser = $DB->get_record('user', array('id' => $newuserid));
        $authplugin = get_auth_plugin($newuser->auth);
        $authplugin->user_update_password($newuser, $userpassword);
    }

    // Trigger event If required.
    if ($triggerevent) {
        \core\event\user_created::create_from_userid($newuserid)->trigger();
    }

    // Purge the associated caches for the current user only.
    $presignupcache = \cache::make('core', 'presignup');
    $presignupcache->purge_current_user();

    return $newuserid;
}

/**
 * Validates the standard sign-up data (except recaptcha that is validated by the form element).
 *
 * @param  array $data  the sign-up data
 * @param  array $files files among the data
 * @return array list of errors, being the key the data element name and the value the error itself
 * @since Moodle 3.2
 */
function local_fsbaclogin_signupvalidatedata($data, $files)
{
    global $CFG, $DB;

    $errors = array();
    $authplugin = get_auth_plugin($CFG->registerauth);

    if ($DB->record_exists('user', array('username' => $data['email'], 'mnethostid' => $CFG->mnet_localhost_id))) {
        $errors['username'] = get_string('usernameexists');
    } else {
        // Check allowed characters.
        if ($data['email'] !== core_text::strtolower($data['email'])) {
            $errors['username'] = get_string('usernamelowercase');
        } else {
            if ($data['email'] !== core_user::clean_field($data['email'], 'username')) {
                $errors['username'] = get_string('invalidusername');
            }
        }
    }

    // Check if user exists in external db.
    // TODO: maybe we should check all enabled plugins instead.
    if ($authplugin->user_exists($data['email'])) {
        $errors['username'] = get_string('usernameexists');
    }

    if (!validate_email($data['email'])) {
        $errors['email'] = get_string('invalidemail');
    } else if (empty($CFG->allowaccountssameemail)) {
        // Emails in Moodle as case-insensitive and accents-sensitive. Such a combination can lead to very slow queries
        // on some DBs such as MySQL. So we first get the list of candidate users in a subselect via more effective
        // accent-insensitive query that can make use of the index and only then we search within that limited subset.
        $sql = "SELECT 'x'
                  FROM {user}
                 WHERE " . $DB->sql_equal('email', ':email1', false, true) . "
                   AND id IN (SELECT id
                                FROM {user}
                               WHERE " . $DB->sql_equal('email', ':email2', false, false) . "
                                 AND mnethostid = :mnethostid)";

        $params = array(
            'email1' => $data['email'],
            'email2' => $data['email'],
            'mnethostid' => $CFG->mnet_localhost_id,
        );

        // If there are other user(s) that already have the same email, show an error.
        if ($DB->record_exists_sql($sql, $params)) {
            $forgotpasswordurl = new moodle_url('/local/fsbaclogin/forgot_password.php');
            $forgotpasswordlink = html_writer::link($forgotpasswordurl, get_string('emailexistshintlink'));
            $errors['email'] = get_string('emailexists') . ' ' . get_string('emailexistssignuphint', 'moodle', $forgotpasswordlink);
        }
    }
    if (empty($data['email2'])) {
        $errors['email2'] = get_string('missingemail');
    } else if (core_text::strtolower($data['email2']) != core_text::strtolower($data['email'])) {
        $errors['email2'] = get_string('invalidemail');
    }
    if (!isset($errors['email'])) {
        if ($err = email_is_not_allowed($data['email'])) {
            $errors['email'] = $err;
        }
    }

    // Construct fake user object to check password policy against required information.
    $tempuser = new stdClass();
    $tempuser->id = 1;
    $tempuser->username = $data['email'];
    $tempuser->firstname = $data['firstname'];
    $tempuser->lastname = $data['lastname'];
    $tempuser->email = $data['email'];

    $errmsg = '';
    if (!check_password_policy($data['password'], $errmsg, $tempuser)) {
        $errors['password'] = $errmsg;
    }

    // Validate customisable profile fields. (profile_validation expects an object as the parameter with userid set).
    $dataobject = (object)$data;
    $dataobject->id = 0;
    $errors += profile_validation($dataobject, $files);

    return $errors;
}

function is_cf_invalid($cf)
{
    $cf = trim($cf);
    if ($cf === '') {
        return 1;
    }
    if (strlen($cf) != 16) {
        return 2;
    }
    $cf = strtoupper($cf);
    if (preg_match("/^[A-Z0-9]+\$/", $cf) != 1) {
        return 3;
    }
    $s = 0;
    for ($i = 1; $i <= 13; $i += 2) {
        $c = $cf[$i];
        if (strcmp($c, "0") >= 0 and strcmp($c, "9") <= 0) {
            $s += ord($c) - ord('0');
        } else {
            $s += ord($c) - ord('A');
        }
    }
    for ($i = 0; $i <= 14; $i += 2) {
        $c = $cf[$i];
        switch ($c) {
            case '0':
                $s += 1;
                break;
            case '1':
                $s += 0;
                break;
            case '2':
                $s += 5;
                break;
            case '3':
                $s += 7;
                break;
            case '4':
                $s += 9;
                break;
            case '5':
                $s += 13;
                break;
            case '6':
                $s += 15;
                break;
            case '7':
                $s += 17;
                break;
            case '8':
                $s += 19;
                break;
            case '9':
                $s += 21;
                break;
            case 'A':
                $s += 1;
                break;
            case 'B':
                $s += 0;
                break;
            case 'C':
                $s += 5;
                break;
            case 'D':
                $s += 7;
                break;
            case 'E':
                $s += 9;
                break;
            case 'F':
                $s += 13;
                break;
            case 'G':
                $s += 15;
                break;
            case 'H':
                $s += 17;
                break;
            case 'I':
                $s += 19;
                break;
            case 'J':
                $s += 21;
                break;
            case 'K':
                $s += 2;
                break;
            case 'L':
                $s += 4;
                break;
            case 'M':
                $s += 18;
                break;
            case 'N':
                $s += 20;
                break;
            case 'O':
                $s += 11;
                break;
            case 'P':
                $s += 3;
                break;
            case 'Q':
                $s += 6;
                break;
            case 'R':
                $s += 8;
                break;
            case 'S':
                $s += 12;
                break;
            case 'T':
                $s += 14;
                break;
            case 'U':
                $s += 16;
                break;
            case 'V':
                $s += 10;
                break;
            case 'W':
                $s += 22;
                break;
            case 'X':
                $s += 25;
                break;
            case 'Y':
                $s += 24;
                break;
            case 'Z':
                $s += 23;
                break;
                /*. missing_default: .*/
        }
    }
    if (chr($s % 26 + ord('A')) != $cf[15]) {
        return 4;
    }
    return 0;
}

function cf_exists($cf)
{
    global $DB;

    $cfplaceholder = $DB->sql_compare_text(":data");
    $sql = "SELECT uid.id
              FROM {user_info_data} uid
              JOIN {user_info_field} uif
                ON uif.id = uid.fieldid
             WHERE uif.shortname = 'CF'
               AND upper(uid.data) = upper({$cfplaceholder})
               AND uid.data <> ''";
    $existingcf = $DB->get_record_sql($sql, array("data" => $cf));
    if ($existingcf) {
        return true;
    }
    return false;
}

/**
 * Send email to specified user with confirmation text and activation link.
 *
 * @param stdClass $user A {@link $USER} object
 * @param string $confirmationurl user confirmation URL
 * @return bool Returns true if mail was sent OK and false if there was an error.
 */
function local_fsbaclogin_sendconfirmationemail($user, $confirmationurl = null)
{
    global $CFG;

    $site = get_site();
    $supportuser = core_user::get_support_user();

    $data = new stdClass();
    $data->sitename  = format_string($site->fullname);
    $data->admin     = generate_email_signoff();

    $subject = get_string('emailconfirmationsubject', '', format_string($site->fullname));

    if (empty($confirmationurl)) {
        $confirmationurl = '/local/fsbaclogin/confirm.php';
    }

    $confirmationurl = new moodle_url($confirmationurl);
    // Remove data parameter just in case it was included in the confirmation so we can add it manually later.
    $confirmationurl->remove_params('data');
    $confirmationpath = $confirmationurl->out(false);

    // We need to custom encode the username to include trailing dots in the link.
    // Because of this custom encoding we can't use moodle_url directly.
    // Determine if a query string is present in the confirmation url.
    $hasquerystring = strpos($confirmationpath, '?') !== false;
    // Perform normal url encoding of the username first.
    $username = urlencode($user->username);
    // Prevent problems with trailing dots not being included as part of link in some mail clients.
    $username = str_replace('.', '%2E', $username);

    $data->link = $confirmationpath . ($hasquerystring ? '&' : '?') . 'data=' . $user->secret . '/' . $username;

    $message     = get_string('emailconfirmation', '', $data);
    $messagehtml = text_to_html(get_string('emailconfirmation', '', $data), false, false, true);

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    return email_to_user($user, $supportuser, $subject, $message, $messagehtml);
}


function get_user_customfield_profilazione($userid)
{
    global $DB;

    $sql = "SELECT uid.id, uid.data
              FROM {user_info_data} uid
              JOIN {user_info_field} uif
                ON uif.id = uid.fieldid
             WHERE uid.userid = ?
               AND uif.shortname = 'profilazione'
               AND uid.data = 1";
    $user_profilazione = $DB->get_record_sql($sql, array($userid));
    return $user_profilazione;
}

function has_user_filled_new_additional_fields($userid) {
    global $DB;

    $sql = "SELECT uid.id, uid.data
              FROM {user_info_data} uid
              JOIN {user_info_field} uif
                ON uif.id = uid.fieldid
             WHERE uid.userid = ?
               AND uif.shortname = 'nuovicampiaggiuntivicompilati'
               AND uid.data = 1";
    $user_filled_new_additional_fields = $DB->get_record_sql($sql, array($userid));
    if ($user_filled_new_additional_fields) {
        return true;
    }
    return false;
}

/**
 * Returns full login url.
 *
 * Any form submissions for authentication to this URL must include username,
 * password as well as a logintoken generated by \core\session\manager::get_login_token().
 *
 * @return string login url
 */
function local_fsbaclogin_getloginurl() {
    global $CFG;

    return "$CFG->wwwroot/local/fsbaclogin/index.php";
}

function local_fsbaclogin_changegeneralfieldsvalidatedata($data, $files)
{
    global $CFG, $DB, $USER;

    $errors = array();

    $sql = "SELECT uid.id, uif.shortname, uid.data
              FROM {user_info_data} uid
              JOIN {user_info_field} uif
                ON uif.id = uid.fieldid
             WHERE uif.shortname = 'CF'
               AND uid.userid = ?";
    $usercf = $DB->get_record_sql($sql, array($USER->id));
    if ($usercf) {
        if ($usercf->data != strtoupper(trim($data['cf']))) {
            if (!is_cf_invalid($data["cf"])) {
                if (cf_exists($data["cf"])) {
                    $errors["cf"] = get_string('existsCF', 'local_fsbaclogin');
                }
            } else {
                $errors["cf"] = get_string('invalidCF', 'local_fsbaclogin');
            }
        }
    } else {
        if (!empty(trim($data['cf']))) {
            if (!is_cf_invalid($data["cf"])) {
                if (cf_exists($data["cf"])) {
                    $errors["cf"] = get_string('existsCF', 'local_fsbaclogin');
                }
            } else {
                $errors["cf"] = get_string('invalidCF', 'local_fsbaclogin');
            }
        }
    }

    if (!validate_email($data['email'])) {
        $errors['email'] = get_string('invalidemail');
    } else if (empty($CFG->allowaccountssameemail)) {
        // Emails in Moodle as case-insensitive and accents-sensitive. Such a combination can lead to very slow queries
        // on some DBs such as MySQL. So we first get the list of candidate users in a subselect via more effective
        // accent-insensitive query that can make use of the index and only then we search within that limited subset.
        $useremail = $DB->get_field("user", "email", array("id" => $USER->id));
        if ($useremail != $data['email']) {
            $sql = "SELECT 'x'
                    FROM {user}
                    WHERE " . $DB->sql_equal('email', ':email1', false, true) . "
                    AND id IN (SELECT id
                                    FROM {user}
                                WHERE " . $DB->sql_equal('email', ':email2', false, false) . "
                                    AND mnethostid = :mnethostid)";

            $params = array(
                'email1' => $data['email'],
                'email2' => $data['email'],
                'mnethostid' => $CFG->mnet_localhost_id,
            );

            // If there are other user(s) that already have the same email, show an error.
            if ($DB->record_exists_sql($sql, $params)) {
                $forgotpasswordurl = new moodle_url('/local/fsbaclogin/forgot_password.php');
                $forgotpasswordlink = html_writer::link($forgotpasswordurl, get_string('emailexistshintlink'));
                $errors['email'] = get_string('emailexists');
            }
        }
    }
    if (empty($data['email2'])) {
        $errors['email2'] = get_string('missingemail');
    } else if (core_text::strtolower($data['email2']) != core_text::strtolower($data['email'])) {
        $errors['email2'] = get_string('invalidemail');
    }
    if (!isset($errors['email'])) {
        if ($err = email_is_not_allowed($data['email'])) {
            $errors['email'] = $err;
        }
    }

    // Validate customisable profile fields. (profile_validation expects an object as the parameter with userid set).
    $dataobject = (object)$data;
    $dataobject->id = 0;
    $errors += profile_validation($dataobject, $files);

    return $errors;
}