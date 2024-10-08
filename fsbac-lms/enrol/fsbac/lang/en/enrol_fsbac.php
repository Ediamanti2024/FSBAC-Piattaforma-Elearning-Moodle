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
 * Strings for component 'enrol_fsbac', language 'en'.
 *
 * @package    enrol_fsbac
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['canntenrol'] = 'Enrolment is disabled or inactive';
$string['canntenrolearly'] = 'You cannot enrol yet; enrolment starts on {$a}.';
$string['canntenrollate'] = 'You cannot enrol any more, since enrolment ended on {$a}.';
$string['cohortnonmemberinfo'] = 'Only members of cohort \'{$a}\' can fsbac-enrol.';
$string['cohortonly'] = 'Only cohort members';
$string['cohortonly_help'] = 'Self enrolment may be restricted to members of a specified cohort only. Note that changing this setting has no effect on existing enrolments.';
$string['confirmbulkdeleteenrolment'] = 'Are you sure you want to delete these user enrolments?';
$string['customwelcomemessage'] = 'Custom welcome message';
$string['customwelcomemessage_help'] = 'A custom welcome message may be added as plain text or Moodle-auto format, including HTML tags and multi-lang tags.

The following placeholders may be included in the message:

* Course name {$a->coursename}
* Link to user\'s profile page {$a->profileurl}
* User email {$a->email}
* User fullname {$a->fullname}';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during fsbac enrolment';
$string['deleteselectedusers'] = 'Delete selected user enrolments';
$string['editselectedusers'] = 'Edit selected user enrolments';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can enrol themselves until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolme'] = 'Enrol me';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user enrols themselves. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can enrol themselves from this date onward only.';
$string['expiredaction'] = 'Enrolment expiry action';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data and settings are purged from course during course unenrolment.';
$string['expirymessageenrollersubject'] = 'Self enrolment expiry notification';
$string['expirymessageenrollerbody'] = 'Self enrolment in the course \'{$a->course}\' will expire within the next {$a->threshold} for the following users:

{$a->users}

To extend their enrolment, go to {$a->extendurl}';
$string['expirymessageenrolledsubject'] = 'Self enrolment expiry notification';
$string['expirymessageenrolledbody'] = 'Dear {$a->user},

This is a notification that your enrolment in the course \'{$a->course}\' is due to expire on {$a->timeend}.

If you need help, please contact {$a->enroller}.';
$string['expirynotifyall'] = 'Teacher and enrolled user';
$string['expirynotifyenroller'] = 'Teacher only';
$string['groupkey'] = 'Use group enrolment keys';
$string['groupkey_desc'] = 'Use group enrolment keys by default.';
$string['groupkey_help'] = 'In addition to restricting access to the course to only those who know the key, use of group enrolment keys means users are automatically added to groups when they enrol in the course.

Note: An enrolment key for the course must be specified in the fsbac enrolment settings as well as group enrolment keys in the group settings.';
$string['keyholder'] = 'You should have received this enrolment key from:';
$string['longtimenosee'] = 'Unenrol inactive after';
$string['longtimenosee_help'] = 'If users haven\'t accessed a course for a long time, then they are automatically unenrolled. This parameter specifies that time limit.';
$string['maxenrolled'] = 'Max enrolled users';
$string['maxenrolled_help'] = 'Specifies the maximum number of users that can fsbac enrol. 0 means no limit.';
$string['maxenrolledreached'] = 'Maximum number of users allowed to fsbac-enrol was already reached.';
$string['messageprovider:expiry_notification'] = 'Self enrolment expiry notifications';
$string['newenrols'] = 'Allow new enrolments';
$string['newenrols_desc'] = 'Allow users to fsbac enrol into new courses by default.';
$string['newenrols_help'] = 'This setting determines whether a user can enrol into this course.';
$string['nopassword'] = 'No enrolment key required.';
$string['password'] = 'Enrolment key';
$string['password_help'] = 'An enrolment key enables access to the course to be restricted to only those who know the key.

If the field is left blank, any user may enrol in the course.

If an enrolment key is specified, any user attempting to enrol in the course will be required to supply the key. Note that a user only needs to supply the enrolment key ONCE, when they enrol in the course.';
$string['passwordinvalid'] = 'Incorrect enrolment key, please try again';
$string['passwordinvalidhint'] = 'That enrolment key was incorrect, please try again<br />
(Here\'s a hint - it starts with \'{$a}\')';
$string['pluginname'] = 'Fsbac enrolment';
$string['pluginname_desc'] = 'The fsbac enrolment plugin allows users to choose which courses they want to participate in. The courses may be protected by an enrolment key. Internally the enrolment is done via the manual enrolment plugin which has to be enabled in the same course.';
$string['registertostart'] = 'Register for free to get started';
$string['requirepassword'] = 'Require enrolment key';
$string['requirepassword_desc'] = 'Require enrolment key in new courses and prevent removing of enrolment key from existing courses.';
$string['role'] = 'Default assigned role';
$string['fsbac:config'] = 'Configure fsbac enrol instances';
$string['fsbac:enrolself'] = 'Self enrol in course';
$string['fsbac:holdkey'] = 'Appear as the fsbac enrolment key holder';
$string['fsbac:manage'] = 'Manage enrolled users';
$string['fsbac:unenrol'] = 'Unenrol users from course';
$string['fsbac:unenrolself'] = 'Unenrol fsbac from the course';
$string['sendcoursewelcomemessage'] = 'Send course welcome message';
$string['sendcoursewelcomemessage_help'] = 'When a user fsbac enrols in the course, they may be sent a welcome message email. If sent from the course contact (by default the teacher), and more than one user has this role, the email is sent from the first user to be assigned the role.';
$string['sendexpirynotificationstask'] = "Self enrolment send expiry notifications task";
$string['showhint'] = 'Show hint';
$string['showhint_desc'] = 'Show first letter of the guest access key.';
$string['status'] = 'Allow existing enrolments';
$string['status_desc'] = 'Enable fsbac enrolment method in new courses.';
$string['status_help'] = 'If enabled together with \'Allow new enrolments\' disabled, only users who fsbac enrolled previously can access the course. If disabled, this fsbac enrolment method is effectively disabled, since all existing fsbac enrolments are suspended and new users cannot fsbac enrol.';
$string['syncenrolmentstask'] = 'Synchronise fsbac enrolments task';
$string['unenrol'] = 'Unenrol user';
$string['unenrolfsbacconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['unenroluser'] = 'Do you really want to unenrol "{$a->user}" from course "{$a->course}"?';
$string['unenrolusers'] = 'Unenrol users';
$string['usepasswordpolicy'] = 'Use password policy';
$string['usepasswordpolicy_desc'] = 'Use standard password policy for enrolment keys.';
$string['welcometocourse'] = 'Enrolling in the FAD Fondazione Scuola dei beni e delle attività culturali {$a}';
$string['welcometocoursetext'] = 'Hello {$a->firstname},

you have just joined {$a->coursename}. You can now start enjoying the course content by accessing the course card, or by clicking here <a href="{$a->courselink}">{$a->courselink}</a>.

Enjoy your training!

<i>This e-mail is generated by an automatic system.
Do not reply to the sender. E-mail is automatically deleted.
For any information or assistance please write to <a href ="mailto:{$a->emailfrom}">{$a->emailfrom}</a></i>';
$string['privacy:metadata'] = 'The Self enrolment plugin does not store any personal data.';
