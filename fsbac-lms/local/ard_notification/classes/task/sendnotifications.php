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

namespace local_ard_notification\task;

use stdClass;

defined('MOODLE_INTERNAL') || die();

require_once __DIR__ . '/../../locallib.php';

class sendnotifications extends \core\task\scheduled_task {

    /**
     * Get scheduled task name.
     *
     * @return string
     */
    public function get_name() {

        return get_string("sendnotifications", "local_ard_notification");
    }

    /**
     * Execute the scheduled task.
     */
    public function execute() {

        // Get bulks to send message
        $bulks = $this->get_bulks_to_send_msg();
        foreach($bulks as $bulk) {
            // Get bulk users to send message
            $bulk_users = $this->get_bulk_users_to_send_msg($bulk->id);
            foreach($bulk_users as $user) {
                // Send message
                $stacktrace = $this->send_message($bulk, $user);
                // Update bulk user status
                if(!empty($stacktrace))
                    $this->update_bulk_user_status($user, 'error', $stacktrace);
                else
                    $this->update_bulk_user_status($user, 'sent', '-');

            }
            // Update bulk status
            $this->update_bulk_status($bulk->id);
        }
    }

    /**
     * Get bulk to send message
     * Used for send message
     */
    public function get_bulk_to_send_msg($bulkid) {

        global $DB;
        return $DB->get_record(
            'local_ard_notification_bulk', ['id' => $bulkid],
            'id, messagesubject, messagebody, messagesenderalias'
        );
    }

    /**
     * Get bulks to send message
     * Used for scheduled message sending
     * The query takes all bulks that are less than or equal to time()
     */
    public function get_bulks_to_send_msg() {

        global $DB;
        return $DB->get_records_sql('
            SELECT id, messagesubject, messagebody, messagesenderalias
            FROM {local_ard_notification_bulk}
            WHERE bulkscheduletime <= :bulkscheduletime AND (bulkstatus = :pending OR bulkstatus = :error)',
            ['bulkscheduletime' => time(), 'pending' => 'pending', 'error' => 'error']
        );
    }

    /**
     * Get test user to send message
     * Used for send test message
     */
    public function get_test_user_to_send_msg($userid) {

        global $DB;
        return $DB->get_record(
            'user', ['id' => $userid],
            'id, username, firstname, lastname, email, institution, secret'
        );
    }

    /**
     * Get bulk user to send message
     * Used for send message
     */
    public function get_bulk_user_to_send_msg($bulkid, $userid) {

        global $DB;
        return $DB->get_record_sql('
            SELECT nu.id, nu.bulkid, nu.userid, u.username, u.firstname, u.lastname, u.email, u.institution, u.secret
            FROM {local_ard_notification_users} nu JOIN {user} u ON nu.userid = u.id
            WHERE nu.bulkid = :bulkid and nu.userid = :userid', ['bulkid' => $bulkid, 'userid' => $userid]
        );
    }

    /**
     * Get bulk users to send message, used for scheduled message sending
     * Select only ones with a status pending or error
     */
    public function get_bulk_users_to_send_msg($bulkid) {

        global $DB;
        return $DB->get_records_sql('
            SELECT nu.id, nu.bulkid, nu.userid, u.username, u.firstname, u.lastname, u.email, u.institution, u.secret
            FROM {local_ard_notification_users} nu JOIN {user} u ON nu.userid = u.id
            WHERE nu.bulkid = :bulkid AND (status = :pending OR status = :error)',
            ['bulkid' => $bulkid, 'pending' => 'pending', 'error' => 'error']
        );
    }

    /**
     * Update bulk status
     */
    public function update_bulk_status($bulkid) {

        global $DB;

        // Get bulk users status
        $bulkusers = $DB->get_records('local_ard_notification_users', ['bulkid' => $bulkid], null, 'id, status');
        // Filter by users status
        $bulkusers_status = array();
        foreach($bulkusers as $user) {
            array_push($bulkusers_status, $user->status);
        }

        // Update bulk status on bulk users
        $bulk = new \stdClass();
        $bulk->id = $bulkid;
        switch ($bulkusers_status) {
            case in_array('error', $bulkusers_status):
                $bulk->bulkstatus = 'error';
                break;
            case in_array('pending', $bulkusers_status):
                $bulk->bulkstatus = 'pending';
                break;
            default:
                $bulk->bulkstatus = 'sent';
        }
        $bulk->timemodified = time();
        $DB->update_record('local_ard_notification_bulk', $bulk);
    }

    /**
     * Update bulk user status
     */
    public function update_bulk_user_status($user, $status, $statusdescription) {

        global $DB;

        $bulk_user = new \stdClass();
        $bulk_user->id = $user->id;
        $bulk_user->bulkid = $user->bulkid;
        $bulk_user->userid = $user->userid;
        $bulk_user->status = $status;
        $bulk_user->statustime = time();
        $bulk_user->statusdescription = $statusdescription;
        $bulk_user->timemodified = time();
        $DB->update_record('local_ard_notification_users', $bulk_user);
    }

    /**
     * Send message
     */
    public function send_message($bulk, $user, $sendtest = false, $trace = true) {

        global $CFG;

        // Replace message body tokens
        $bulk_messagebody = $this->replace_field_tokens($bulk->messagebody, $user, $sendtest);

        // Send message
        $stacktrace = $this->send_email(
            $CFG->noreplyaddress,
            $bulk->messagesenderalias,
            $user->email,
            $user->firstname . ' ' . $user->lastname,
            $bulk->messagesubject,
            $bulk_messagebody,
        );
        // Trace
        if($trace) {
            mtrace(get_string('messagesendingattempt', 'local_ard_notification') . $user->userid . ', ' . $user->firstname . ' ' . $user->lastname . ', ' . $user->email);
            mtrace(get_string(!empty($stacktrace) ? 'messagesendingerror' : 'messagesendingsuccess', 'local_ard_notification') . $stacktrace);
        }
        return $stacktrace;
    }

    /**
     * Replace field tokens
     */
    private function replace_field_tokens($fieldcontent, $user, $sendtest) {

        $data = new \stdClass();
        // Get site
        $site = get_site();
        // Set data sitename
        $data->sitename = format_string($site->fullname);

        // Confirmation url
        $confirmationurl = new \moodle_url('/login/confirm.php');
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

        // Set data link
        // If is a test sending
        if($sendtest) {
            // Set a fake data link
            $data->link = $confirmationpath . ( $hasquerystring ? '&' : '?') . 'data='. random_string(15) .'/'. $username;
        } else {
            // Set user unconfirmed and generate secret
            $user->secret = $this->set_user_unconfirmed($user->userid);
            // Set data link
            $data->link = $confirmationpath . ( $hasquerystring ? '&' : '?') . 'data='. $user->secret .'/'. $username;
        }

        // Set data admin
        $data->admin = generate_email_signoff();
        // Set data username
        $data->username = $user->username;
        // Set data firstname
        $data->firstname = $user->firstname;
        // Set data lastname
        $data->lastname = $user->lastname;
        // Set data institution
        $data->institution = $user->institution;

        // Get placeholder map
        $placeholders_map = local_ard_notification_get_placeholders_map();
        // Build array to replace message body placeholders with data
        $replacedata = array();
        foreach($placeholders_map as $placeholderkey => $placeholdervalue) {
            $replacedata[$placeholdervalue] = $data->$placeholderkey;
        }
        // Replace tokens
        $fieldcontent = strtr($fieldcontent, $replacedata);

        return $fieldcontent;
    }

    /**
     * Set user unconfirmed, used for generating the confirm link
     */
    private function set_user_unconfirmed($userid) {

        global $DB;

        $user = new \stdClass();
        $user->id = $userid;
        $user->auth = 'email';
        //NF - l'utente viene confermato per generare il flusso corretto
        $user->confirmed = 1;
        $user->secret = random_string(15);
        $user->timemodified = time();
        $DB->update_record('user', $user);

        return $user->secret;
    }

    /**
     * Send email
     */
    private function send_email($from_email, $from_alias, $to_email, $to_alias, $subject, $message) {

        // Mailer
        $mail = get_mailer();
        // From
        $mail->setFrom($from_email, $from_alias);
        // To
        $mail->addAddress($to_email, $to_alias);

        // Message content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        // Send
        $mail->send();

        return $mail->ErrorInfo;
    }
}
