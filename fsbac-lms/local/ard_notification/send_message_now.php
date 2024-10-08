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

require_once __DIR__ . '/../../config.php';
require_once $CFG->libdir . '/adminlib.php';
require_once __DIR__ . '/classes/form/send_message_now_form.php';
require_once __DIR__ . '/locallib.php';

// Parameters
$bulkid = required_param('bulkid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$sendtest = optional_param('sendtest', 0, PARAM_INT);
$goto = optional_param('goto', '', PARAM_RAW);

// Page setup
admin_externalpage_setup('bulkmonitoring');
// Require manage all messaging
require_capability('moodle/site:manageallmessaging', context_system::instance());

// Start setting up the page
$PAGE->set_context(context_system::instance());
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('sendmessagenow', 'local_ard_notification'));
$PAGE->set_url(new moodle_url('/local/ard_notification/send_message_now.php'),
    array('bulkid' => $bulkid, 'userid' => $userid, 'sendtest' => $sendtest, 'goto' => $goto)
);
$PAGE->set_pagelayout('admin');
$PAGE->set_cacheable(false);

// Navbar
$PAGE->navbar->add(
    get_string('sendmessagenow', 'local_ard_notification'),
    new moodle_url($PAGE->url)
);

// Check if bulk exists
if(!local_ard_notification_bulk_exists($bulkid)) {
    print_error(get_string('invalidbulkid', 'local_ard_notification'));
}
// Check if user exists
if(!local_ard_notification_user_exists($userid)) {
    print_error(get_string('invaliduserid', 'local_ard_notification'));
}

// Url of bulk monitoring page
$bulk_monitoring = new moodle_url('/local/ard_notification/bulk_monitoring.php');
// Url of bulk users monitoring page
$bulk_users_monitoring = new moodle_url('/local/ard_notification/bulk_users_monitoring.php', array('bulkid' => $bulkid));
// Url of user bulks monitoring page
$user_bulks_monitoring = new moodle_url('/local/ard_notification/user_bulks_monitoring.php', array('userid' => $userid));

// Messaging disable
// if (empty($CFG->messaging))
//     print_error('messagingdisable', 'error');

// Form instance
$mform = new send_message_now_form(null, array('sendtest' => $sendtest));

// Form cancelled
if ($mform->is_cancelled()) {
    // Redirect
    $sendtest ? redirect($bulk_monitoring) : (($goto == 'userbulks') ? redirect($user_bulks_monitoring) : redirect($bulk_users_monitoring));
// Form submitted and confirm
} else if ($formdata = $mform->get_data()) {
    // Send message now
    $stacktrace = $mform->send_message_now($formdata, $sendtest);
    // Redirect
    redirect(
        $sendtest ? $bulk_monitoring : (($goto == 'userbulks') ? $user_bulks_monitoring : $bulk_users_monitoring),
        $stacktrace ? get_string('messageerror', 'local_ard_notification') . ': ' . $stacktrace : get_string('messagesent', 'local_ard_notification'),
        null,
        $stacktrace ? \core\output\notification::NOTIFY_ERROR : \core\output\notification::NOTIFY_SUCCESS
    );
// Form view
} else {
    // Header
    echo $OUTPUT->header();

    // Form set data
    $formdata = new stdClass();
    $formdata->bulkid = $bulkid;
    $formdata->userid = $userid;
    $formdata->sendtest = $sendtest;
    $formdata->goto = $goto;
    $mform->set_data($formdata);

    // Form display
    $mform->display();
    // Footer
    echo $OUTPUT->footer();
}
