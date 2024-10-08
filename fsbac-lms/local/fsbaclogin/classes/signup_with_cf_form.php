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
 * User sign-up form.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fsbaclogin;

use moodle_url;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot . '/local/fsbaclogin/locallib.php');

class signup_with_cf_form extends \moodleform
{
    public function definition()
    {
        global $DB, $OUTPUT;

        $mform = $this->_form;

        $cf_exists = $this->_customdata['cf_exists'];

        $mform->addElement('text', 'cf', format_string($DB->get_field("user_info_field", "name", array("shortname" => "CF"))));
        $mform->setType('cf', PARAM_TEXT);

        $mform->addElement('checkbox', 'notitaliancf', get_string('notitaliancf', 'local_fsbaclogin'));

        $this->add_action_buttons(false, get_string("registerforfree", "local_fsbaclogin"));
    }

    public function definition_after_data()
    {
        // $mform = $this->_form;
        // $mform->applyFilter('username', 'trim');

        // // Trim required name fields.
        // foreach (useredit_get_required_name_fields() as $field) {
        //     $mform->applyFilter($field, 'trim');
        // }
    }

    /**
     * Validate user supplied data on the signup form.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        // Extend validation for any form extensions from plugins.
        // $errors = array_merge($errors, local_fsbaclogin_validate_extend_signup_form($data));

        if (isset($data["cf"])) {
            if (!is_cf_invalid($data["cf"])) {
                if (cf_exists($data["cf"])) {
                    $errors["cf"] = get_string('existsCF', 'local_fsbaclogin');
                }
            } else {
                $errors["cf"] = get_string('invalidCF', 'local_fsbaclogin');
            }
        }

        // if (signup_captcha_enabled()) {
        //     $recaptchaelement = $this->_form->getElement('recaptcha_element');
        //     if (!empty($this->_form->_submitValues['g-recaptcha-response'])) {
        //         $response = $this->_form->_submitValues['g-recaptcha-response'];
        //         if (!$recaptchaelement->verify($response)) {
        //             $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'auth');
        //         }
        //     } else {
        //         $errors['recaptcha_element'] = get_string('missingrecaptchachallengefield');
        //     }
        // }

        // $errors += local_fsbaclogin_signup_validate_data($data, $files);

        return $errors;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(\renderer_base $output)
    {
        ob_start();
        $this->display();
        $formhtml = ob_get_contents();
        ob_end_clean();
        $context = [
            'formhtml' => $formhtml
        ];
        return $context;
    }
}
