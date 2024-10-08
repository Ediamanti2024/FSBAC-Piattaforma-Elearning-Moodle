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

use local_ard_notification\task\sendnotifications;

require_once($CFG->libdir . '/formslib.php');

class send_message_now_form extends moodleform {

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

        // Send Test, hidden Parameter
        $mform->addElement('hidden', 'sendtest', 0);
        $mform->setType('sendtest', PARAM_INT);

        // Go to, hidden Parameter
        $mform->addElement('hidden', 'goto', 0);
        $mform->setType('goto', PARAM_INT);

        if($this->_customdata['sendtest']) {
            // Send test message now
            $mform->addElement(
                'static',
                'sendtestmessagenow',
                get_string('sendtestmessagenow', 'local_ard_notification'),
                get_string('sendtestmessagenowdesc', 'local_ard_notification')
            );
        } else {
            // Send message now
            $mform->addElement(
                'static',
                'sendmessagenow',
                get_string('sendmessagenow', 'local_ard_notification'),
                get_string('sendmessagenowdesc', 'local_ard_notification')
            );
        }

        // Action buttons
        $this->add_action_buttons();
    }

    /**
     * Send message now
     */
    public function send_message_now($formdata, $sendtest) {

        // Get send notifications task
        $sendnotifications = new sendnotifications();
        // Get bulk to send message
        $bulk = $sendnotifications->get_bulk_to_send_msg($formdata->bulkid);

        if($sendtest) {
            // Get test user to send message
            $user = $sendnotifications->get_test_user_to_send_msg($formdata->userid);
            // Send message
            $stacktrace = $sendnotifications->send_message($bulk, $user, $sendtest, false);
        } else {
            // Get bulk user to send message
            $user = $sendnotifications->get_bulk_user_to_send_msg($formdata->bulkid, $formdata->userid);
            // Send message
            $stacktrace = $sendnotifications->send_message($bulk, $user, $sendtest, false);
            // Update bulk user status
            if(!empty($stacktrace))
                $sendnotifications->update_bulk_user_status($user, 'error', $stacktrace);
            else
                $sendnotifications->update_bulk_user_status($user, 'sent', '-');
            // Update bulk status
            $sendnotifications->update_bulk_status($bulk->id);
        }
        // Return success / error status
        return $stacktrace;
    }
}
