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

class delete_bulk_user_form extends moodleform {

    /**
     * Form definition
     */
    public function definition() {

        // Form
        $mform = &$this->_form;

        // Bulk Id, hidden Parameter
        $mform->addElement('hidden', 'bulkid', 0);
        $mform->setType('bulkid', PARAM_INT);

        // User Id, hidden Parameter
        $mform->addElement('hidden', 'userid', 0);
        $mform->setType('userid', PARAM_INT);

        // Go to, hidden Parameter
        $mform->addElement('hidden', 'goto', 0);
        $mform->setType('goto', PARAM_INT);

        // Bulk delete message
        $mform->addElement(
            'static',
            'deletebulkuser',
            get_string('deletebulkuser', 'local_ard_notification'),
            get_string('deletebulkuserdesc', 'local_ard_notification')
        );

        // Action buttons
        $this->add_action_buttons();
    }

    /**
     * Delete bulk user
     */
    public function delete_bulk_user($formdata) {

        global $DB;
        $DB->delete_records('local_ard_notification_users', ['bulkid' => $formdata->bulkid, 'userid' => $formdata->userid]);
    }
}
