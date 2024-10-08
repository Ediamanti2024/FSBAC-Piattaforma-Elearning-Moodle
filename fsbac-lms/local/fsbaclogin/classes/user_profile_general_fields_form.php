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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot . '/local/fsbaclogin/locallib.php');

class user_profile_general_fields_form extends \moodleform implements \renderable, \templatable
{
    public function definition() {
        global $CFG, $OUTPUT, $DB, $USER;

        $mform = $this->_form;

        $mform->addElement('text', 'cf', format_string($DB->get_field("user_info_field", "name", array("shortname" => "CF"))));
        $mform->setType('cf', PARAM_TEXT);
        $sql = "SELECT uid.id, uif.shortname, uid.data
                  FROM {user_info_data} uid
                  JOIN {user_info_field} uif
                    ON uif.id = uid.fieldid
                 WHERE uif.shortname = 'CF'
                   AND uid.userid = ?";
        $existingcf = $DB->get_record_sql($sql, array($USER->id));
        if ($existingcf) {
            $mform->setDefault("cf", $existingcf->data);
        }

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25"');
        $mform->setType('email', \core_user::get_property_type('email'));
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email');
        $existingemail = $DB->get_field("user", "email", array("id" => $USER->id));
        $mform->setDefault('email', $existingemail);

        $mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" size="25"');
        $mform->setType('email2', \core_user::get_property_type('email'));
        $mform->addRule('email2', get_string('missingemail'), 'required', null, 'client');
        $mform->setForceLtr('email2');
        $mform->setDefault('email2', $existingemail);

        $namefields = useredit_get_required_name_fields();
        foreach ($namefields as $field) {
            $mform->addElement('text', $field, get_string($field), 'maxlength="100" size="30"');
            $mform->setType($field, \core_user::get_property_type('firstname'));
            $stringid = 'missing' . $field;
            if (!get_string_manager()->string_exists($stringid, 'moodle')) {
                $stringid = 'required';
            }
            $mform->addRule($field, get_string($stringid), 'required', null, 'client');
            $existingfield = $DB->get_field("user", $field, array("id" => $USER->id));
            $mform->setDefault($field, $existingfield);
        }

        // buttons
        $this->set_display_vertical();
        $this->add_action_buttons(false, get_string("completeregistration", "local_fsbaclogin"));
    }

    public function definition_after_data() {

    }

    /**
     * Validate user supplied data on the signup form.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        $errors += local_fsbaclogin_changegeneralfieldsvalidatedata($data, $files);

        return $errors;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(\renderer_base $output) {
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
