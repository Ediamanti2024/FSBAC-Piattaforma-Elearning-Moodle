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
 * Set password form definition.
 *
 * @package    core
 * @subpackage auth
 * @copyright  2006 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fsbaclogin;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->dirroot . '/local/fsbaclogin/locallib.php');

/**
 * Set forgotten password form definition.
 *
 * @package    core
 * @subpackage auth
 * @copyright  2006 Petr Skoda {@link http://skodak.org}
 * @copyright  2013 Peter Bulmer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_password_form extends \moodleform
{

    /**
     * Define the set password form.
     */
    public function definition()
    {
        global $CFG;

        $mform = $this->_form;
        $mform->setDisableShortforms(true);

        // Include the username in the form so browsers will recognise that a password is being set.
        $mform->addElement('text', 'username', '', 'style="display: none;"');
        $mform->setType('username', PARAM_RAW);
        // Token gives authority to change password.
        $mform->addElement('hidden', 'token', '');
        $mform->setType('token', PARAM_ALPHANUM);

        // Visible elements.
        // $mform->addElement('static', 'username2', get_string('username'));

        $mform->addElement('password', 'password', get_string('newpassword'), array("placeholder" => get_string("passwordplaceholder", "local_fsbaclogin")));
        $mform->addRule('password', get_string('required'), 'required', null, 'client');
        $mform->setType('password', PARAM_RAW);

        $strpasswordagain = get_string('confirmpassword', "local_fsbaclogin");
        $mform->addElement('password', 'password2', $strpasswordagain, array("placeholder" => get_string("passwordplaceholder", "local_fsbaclogin")));
        $mform->addRule('password2', get_string('required'), 'required', null, 'client');
        $mform->setType('password2', PARAM_RAW);

        // Hook for plugins to extend form definition.
        $user = $this->_customdata;
        local_fsbaclogin_extendsetpasswordform($mform, $user);

        $this->add_action_buttons(true);
    }

    /**
     * Perform extra password change validation.
     * @param array $data submitted form fields.
     * @param array $files submitted with the form.
     * @return array errors occuring during validation.
     */
    public function validation($data, $files)
    {
        $user = $this->_customdata;

        $errors = parent::validation($data, $files);

        // Extend validation for any form extensions from plugins.
        $errors = array_merge($errors, local_fsbaclogin_validateextendsetpasswordform($data, $user));

        // Ignore submitted username.
        if ($data['password'] !== $data['password2']) {
            $errors['password'] = get_string('passwordsdiffer');
            $errors['password2'] = get_string('passwordsdiffer');
            return $errors;
        }

        $errmsg = ''; // Prevents eclipse warnings.
        if (!check_password_policy($data['password'], $errmsg, $user)) {
            $errors['password'] = $errmsg;
            $errors['password2'] = $errmsg;
            return $errors;
        }

        if (user_is_previously_used_password($user->id, $data['password'])) {
            $errors['password'] = get_string('errorpasswordreused', 'core_auth');
            $errors['password2'] = get_string('errorpasswordreused', 'core_auth');
        }

        return $errors;
    }
}
