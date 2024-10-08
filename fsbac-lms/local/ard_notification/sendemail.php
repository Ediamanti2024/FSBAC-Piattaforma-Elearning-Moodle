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

// Parameters
$email = required_param('e', PARAM_EMAIL);
$subject = optional_param('subject', 'Test Subject', PARAM_TEXT);
$message = optional_param('message', 'Test Message', PARAM_TEXT);

// Page setup
admin_externalpage_setup('bulkmonitoring');
// Require manage all messaging
require_capability('moodle/site:manageallmessaging', context_system::instance());

// Start setting up the page
$PAGE->set_context(context_system::instance());
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title('Send Email', 'local_ard_notification');
$PAGE->set_url(new moodle_url('/local/ard_notification/sendemail.php'), array('e' => $email));
$PAGE->set_pagelayout('admin');
$PAGE->set_cacheable(false);

// Header
echo $OUTPUT->header();

// Mailer
$mail = get_mailer();
// From
$mail->setFrom($CFG->noreplyaddress);
// To
$mail->addAddress($email);

// Message content
$mail->isHTML(true);
$mail->Subject = $subject;
$mail->Body = $message;
// Send
$mail->send();

if(!empty($mail->ErrorInfo))
    \core\notification::error(get_string('messagesendingerror', 'local_ard_notification') . $mail->ErrorInfo);
else
    \core\notification::success(get_string('messagesendingsuccess', 'local_ard_notification'));

// Footer
echo $OUTPUT->footer();
