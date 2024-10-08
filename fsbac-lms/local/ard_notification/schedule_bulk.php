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
require_once __DIR__ . '/classes/form/schedule_bulk_form.php';
require_once __DIR__ . '/locallib.php';

// Parameters
$bulkid = optional_param('id', 0, PARAM_INT);

// Page setup
admin_externalpage_setup('bulkmonitoring');
// Require manage all messaging
require_capability('moodle/site:manageallmessaging', context_system::instance());

// Start setting up the page
$PAGE->set_context(context_system::instance());
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('schedulebulk', 'local_ard_notification'));
$PAGE->set_url(new moodle_url('/local/ard_notification/schedule_bulk.php'), array('id' => $bulkid));
$PAGE->set_pagelayout('admin');
$PAGE->set_cacheable(false);

// Navbar
$PAGE->navbar->add(
    get_string('schedulebulk', 'local_ard_notification'),
    new moodle_url($PAGE->url)
);

// Check if bulk exists
if($bulkid && !local_ard_notification_bulk_exists($bulkid)) {
    print_error(get_string('invalidid', 'local_ard_notification'));
}

// Return to user bulk page, in case of procedure abort
$user_bulk = new moodle_url('/' . $CFG->admin . '/user/user_bulk.php');
// Url of bulk monitoring page
$bulk_monitoring = new moodle_url('/local/ard_notification/bulk_monitoring.php');

// No bulk users
if (!$bulkid && empty($SESSION->bulk_users))
    // Redirect
    redirect(
        $user_bulk,
        get_string('nobulkusers', 'local_ard_notification'),
        null,
        \core\output\notification::NOTIFY_ERROR
    );

// Messaging disable
// if (empty($CFG->messaging))
//     print_error('messagingdisable', 'error');

// Form instance
$mform = new schedule_bulk_form(null, array(
    'languages' => get_string_manager()->get_list_of_translations(),
    'currentlang' => current_language()
));

// Form cancelled
if ($mform->is_cancelled()) {
    // Redirect
    $bulkid ? redirect($bulk_monitoring) : redirect($user_bulk);
// Form submitted and confirm
} else if ($formdata = $mform->get_data()) {
    // Update bulk
    if($formdata->id)
        $mform->update_bulk($formdata);
    // Insert bulk
    else
        $mform->insert_bulk($formdata, $SESSION->bulk_users);
    // Redirect
    redirect(
        $bulk_monitoring,
        get_string($bulkid ? 'bulkupdated' : 'bulkinserted', 'local_ard_notification'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
// Form view
} else {
    // Header
    echo $OUTPUT->header();

    // If bulk id, set form with bulk data
    if($bulkid) {
        // Get bulk
        $formdata = $mform->get_bulk($bulkid);
    // Set form with standard message body
    } else {
        // Get placeholder map
        $placeholders_map = local_ard_notification_get_placeholders_map();
        // Build data object, to replace email confirmation default tokens
        $data = new \stdClass();
        foreach($placeholders_map as $placeholderkey => $placeholdervalue) {
            $data->{$placeholderkey} = $placeholdervalue;
        }
        // Initialize message body
        // Replace email confirmation tokens with data
        $formdata = new stdClass();
        $formdata->messagebody_editor['text'] = text_to_html(
            get_string('emailconfirmation', 'core', $data),
            false, false, true
        );
    }
    // Form set data
    $mform->set_data($formdata);

    // Form display
    $mform->display();
    // Footer
    echo $OUTPUT->footer();
}
