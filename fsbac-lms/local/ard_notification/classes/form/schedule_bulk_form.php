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
 * @package    local_ard_notification
 * @copyright  2021-2022 Ariadne Digital
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once __DIR__ . '/../../locallib.php';

class schedule_bulk_form extends moodleform {

    /**
     * Form definition
     */
    public function definition() {

        // Form
        $mform = &$this->_form;

        // Bulk fields
        $mform->addElement('header', 'bulk', get_string('bulk', 'local_ard_notification'));

        // Bulk Id, hidden Parameter
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // Bulk name
        $mform->addElement(
            'text',
            'bulkname',
            get_string('bulkname', 'local_ard_notification')
        );
        $mform->setType('bulkname', PARAM_TEXT, $this->get_text_settings());
        $mform->addRule('bulkname', null, 'required', null, 'server');
        $mform->addHelpButton('bulkname', 'bulkname', 'local_ard_notification');

        // Schedule time
        $mform->addElement(
            'date_time_selector',
            'bulkscheduletime',
            get_string('bulkscheduletime', 'local_ard_notification'),
            $this->get_date_time_settings()
        );
        $mform->addHelpButton('bulkscheduletime', 'bulkscheduletime', 'local_ard_notification');

        // Bulk notes
        $mform->addElement(
            'editor',
            'bulknotes_editor',
            get_string('bulknotes_editor', 'local_ard_notification'),
            null,
            $this->get_editor_settings()
        );
        $mform->setType('bulknotes_editor', PARAM_RAW);
        $mform->addHelpButton('bulknotes_editor', 'bulknotes_editor', 'local_ard_notification');

        // Message fields
        $mform->addElement('header', 'message', get_string('message', 'local_ard_notification'));

        // Message sender alias
        $mform->addElement(
            'text',
            'messagesenderalias',
            get_string('messagesenderalias', 'local_ard_notification')
        );
        $mform->setType('messagesenderalias', PARAM_TEXT, $this->get_text_settings());
        $mform->addRule('messagesenderalias', null, 'required', null, 'server');
        $mform->addHelpButton('messagesenderalias', 'messagesenderalias', 'local_ard_notification');

        // Message language
        $mform->addElement(
            'select',
            'messagelanguage',
            get_string('messagelanguage', 'local_ard_notification'),
            $this->get_languages_list($this->_customdata['languages'])
        );
        $mform->setDefault('messagelanguage', $this->_customdata['currentlang']);
        $mform->addRule('messagelanguage', null, 'required', null, 'server');
        $mform->addHelpButton('messagelanguage', 'messagelanguage', 'local_ard_notification');

        // Message subject
        $mform->addElement(
            'text',
            'messagesubject',
            get_string('messagesubject', 'local_ard_notification')
        );
        $mform->setType('messagesubject', PARAM_TEXT, $this->get_text_settings());
        $mform->addRule('messagesubject', null, 'required', null, 'server');
        $mform->addHelpButton('messagesubject', 'messagesubject', 'local_ard_notification');

        // Message body
        $mform->addElement(
            'editor',
            'messagebody_editor',
            get_string('messagebody_editor', 'local_ard_notification'),
            null,
            $this->get_editor_settings()
        );
        $mform->setType('messagebody_editor', PARAM_RAW);
        $mform->addRule('messagebody_editor', null, 'required', null, 'server');
        $mform->addHelpButton('messagebody_editor', 'messagebody_editor', 'local_ard_notification');

        // Action buttons
        $this->add_action_buttons();
    }

    /**
     * Perform some extra moodle validation
     */
    public function validation($data, $files) {

        global $DB;
        // Get parent validation
        $errors = parent::validation($data, $files);

        // Check if bulk name already exists
        // If bulk is in edit mode, search for a bulk name equal to the current with another bulk id
        if($data['id']) {
            $bulkname = $DB->get_field_sql('
                SELECT bulkname FROM {local_ard_notification_bulk}
                WHERE id <> :id AND bulkname = :bulkname',
                ['id' => $data['id'], 'bulkname' => $data['bulkname']]
            );
        // If bulk is in create mode, search for a bulk name equal to the current
        } else {
            $bulkname = $DB->get_field('local_ard_notification_bulk', 'bulkname', ['bulkname' => $data['bulkname']]);
        }
        // If exists a bulk name equal to the current
        if(!empty($bulkname)) {
            $errors['bulkname'] = get_string('bulknameexists', 'local_ard_notification');
        }
        return $errors;
    }

    /**
     * Insert bulk
     */
    public function insert_bulk($formdata, $bulk_users) {

        global $DB, $USER;

        // Insert bulk data
        $bulk = new stdClass();
        $bulk->bulkname = $formdata->bulkname;
        $bulk->bulkcreatedby = $USER->id;
        $bulk->bulkscheduletime = $formdata->bulkscheduletime;
        $bulk->bulkstatus = $formdata->bulkscheduletime ?  'pending' : 'notscheduled';
        $bulk->messagesubject = $formdata->messagesubject;
        // Manage text fields
        $bulk = $this->set_text_fields($formdata, $bulk);
        $bulk->messagesenderalias = $formdata->messagesenderalias;
        $bulk->messagelanguage = $formdata->messagelanguage;
        $bulk->timecreated = time();
        $bulk->timemodified = time();
        $formdata->id = $DB->insert_record('local_ard_notification_bulk', $bulk);
        // Manage text fields files
        $this->set_text_fields_files($formdata);
        // Insert bulk users
        $this->insert_bulk_users($formdata, $bulk_users);
    }

    /**
     * Insert bulk users
     */
    private function insert_bulk_users($formdata, $bulk_users) {

        global $DB;

        // Get users from session
        list($in, $params) = $DB->get_in_or_equal($bulk_users);
        $users = $DB->get_recordset_select('user', "id $in", $params);
        foreach ($users as $user) {
            $bulk_user = new stdClass();
            $bulk_user->userid = $user->id;
            $bulk_user->bulkid = $formdata->id;
            $bulk_user->status = $formdata->bulkscheduletime ?  'pending' : 'notscheduled';
            $bulk_user->statustime = time();
            $bulk_user->statusdescription = '-';
            $bulk_user->timecreated = time();
            $bulk_user->timemodified = time();
            $DB->insert_record('local_ard_notification_users', $bulk_user);
        }
        // Close record set
        $users->close();
    }

    /**
     * Set text fields, set properties for correct field saving
     */
    private function set_text_fields($formdata, $bulk) {

        // Get file areas
        $fileareas = local_ard_notification_get_fileareas();
        foreach($fileareas as $filearea) {
            // Set filearea editor property
            $filearea_editor = $filearea . '_editor';
            // Format text
            $bulk->{$filearea} = format_text(
                $formdata->{$filearea_editor}['text'],
                $formdata->{$filearea_editor}['format'],
                trusttext_trusted(\context_system::instance())
            );
        }
        return $bulk;
    }

    /**
     * Set text fields files, set properties for correct field saving
     */
    private function set_text_fields_files($formdata) {

        global $DB;
        // Get file areas
        $fileareas = local_ard_notification_get_fileareas();
        foreach($fileareas as $filearea) {
            // Set files contained in form data fields
            $formdata = local_ard_notification_file_postupdate_standard_editor($filearea, $formdata, $this->get_editor_settings());
            // Update text field with plugin files url
            $DB->set_field('local_ard_notification_bulk', $filearea, $formdata->{$filearea}, ['id' => $formdata->id]);

        }
    }

    /**
     * Get text settings configuration
     */
    private function get_text_settings() {

        $options = array(
            'maxlength' => 32
        );
        return $options;
    }

    /**
     * Get date time settings configuration
     */
    private function get_date_time_settings() {

        $options = array(
            'startyear' => date("Y"),
            'stopyear'  => date("Y") + 5,
            'timezone'  => 99,
            'step'      => 5,
            'optional' => true,
        );
        return $options;
    }

    /**
     * Get editor settings configuration
     */
    private function get_editor_settings() {

        global $CFG;

        $options = array(
            'subdirs' => 0,
            'maxbytes' => $CFG->maxbytes,
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'changeformat' => 0,
            'context' => context_system::instance(),
            'noclean' => true,
            'trusttext' => false,
            'enable_filemanagement' => true
        );
        return $options;
    }

    /**
     * Get languages list to be selected
     */
    private function get_languages_list($languages) {

        $languages = $this->_customdata['languages'];
        $list = array();
        foreach ($languages as $langkey => $langvalue) {
            $list[$langkey] = $langvalue;
        }
        return $list;
    }

    /**
     * Get bulk
     */
    public function get_bulk($bulkid) {

        global $DB;
        // Get bulk
        $bulk = $DB->get_record('local_ard_notification_bulk', ['id' => $bulkid], '*');
        // Get bulk files
        $bulk = $this->get_bulk_files($bulk);
        return $bulk;
    }

    /**
     * Get bulk files, handle files on moodledata
     */
    private function get_bulk_files($bulk) {

        // Get file areas
        $fileareas = local_ard_notification_get_fileareas();
        // Get bulk files
        foreach($fileareas as $filearea) {
            // Set filearea format property
            $fileareaformat = $filearea . 'format';
            // Set format
            $bulk->{$fileareaformat} = 1;
            // Prepare files for standard editor
            $bulk = local_ard_notification_file_prepare_standard_editor($filearea, $bulk, $this->get_editor_settings());
            // Unset unused properties
            unset($bulk->{$filearea});
            unset($bulk->{$fileareaformat});
        }

        return $bulk;
    }

    /**
     * Update bulk
     */
    public function update_bulk($formdata) {

        global $DB;

        // Update bulk data
        $bulk = new stdClass();
        $bulk->id = $formdata->id;
        $bulk->bulkname = $formdata->bulkname;
        $bulk->bulkscheduletime = $formdata->bulkscheduletime;
        $bulk->bulkstatus = $formdata->bulkscheduletime ?  'pending' : 'notscheduled';
        $bulk->messagesubject = $formdata->messagesubject;
        // Set text fields
        $bulk = $this->set_text_fields($formdata, $bulk);
        $bulk->messagesenderalias = $formdata->messagesenderalias;
        $bulk->messagelanguage = $formdata->messagelanguage;
        $bulk->timemodified = time();
        $DB->update_record('local_ard_notification_bulk', $bulk);
        // Set text fields files
        $this->set_text_fields_files($formdata);
        // Update bulk users
        $this->update_bulk_users($formdata);
    }

    /**
     * Udpate bulk users
     */
    private function update_bulk_users($formdata) {

        global $DB;

        // Get bulk users list
        $bulk_users = $DB->get_records('local_ard_notification_users', ['bulkid' => $formdata->id]);
        foreach ($bulk_users as $bulk_user) {
            $user = new stdClass();
            $user->id = $bulk_user->id;
            // If bulk scheduled time has been set, update user status
            $user->status = $formdata->bulkscheduletime ? 'pending' : 'notscheduled';
            $user->statustime = time();
            $user->statusdescription = '-';
            $user->timemodified = time();
            $DB->update_record('local_ard_notification_users', $user);
        }
    }

}
