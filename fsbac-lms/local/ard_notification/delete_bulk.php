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
require_once __DIR__ . '/classes/form/delete_bulk_form.php';
require_once __DIR__ . '/locallib.php';

// Parameters
$bulkid = required_param('id', PARAM_INT);

// Page setup
admin_externalpage_setup('bulkmonitoring');
// Require manage all messaging
require_capability('moodle/site:manageallmessaging', context_system::instance());

// Start setting up the page
$PAGE->set_context(context_system::instance());
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('deletebulk', 'local_ard_notification'));
$PAGE->set_url(new moodle_url('/local/ard_notification/delete_bulk.php'), array('id' => $bulkid));
$PAGE->set_pagelayout('admin');
$PAGE->set_cacheable(false);

// Navbar
$PAGE->navbar->add(
    get_string('deletebulk', 'local_ard_notification'),
    new moodle_url($PAGE->url)
);

// Check if bulk exists
if(!local_ard_notification_bulk_exists($bulkid)) {
    print_error(get_string('invalidid', 'local_ard_notification'));
}

// Url of bulk monitoring page
$bulk_monitoring = new moodle_url('/local/ard_notification/bulk_monitoring.php');

// Messaging disable
// if (empty($CFG->messaging))
//     print_error('messagingdisable', 'error');

// Form instance
$mform = new delete_bulk_form(null, array());

// Form cancelled
if ($mform->is_cancelled()) {
    // Redirect
    redirect($bulk_monitoring);
// Form submitted and confirm
} else if ($formdata = $mform->get_data()) {
    // Delete bulk
    $mform->delete_bulk($formdata);
    // Redirect
    redirect(
        $bulk_monitoring,
        get_string('bulkdeleted', 'local_ard_notification'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
// Form view
} else {
    // Header
    echo $OUTPUT->header();

    // Form set data
    $formdata = new stdClass();
    $formdata->id = $bulkid;
    $mform->set_data($formdata);

    // Form display
    $mform->display();
    // Footer
    echo $OUTPUT->footer();
}
