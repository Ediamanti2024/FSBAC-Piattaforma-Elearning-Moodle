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

require_once __DIR__ . '/locallib.php';

/**
 * Manage plugin files
 */
function local_ard_notification_pluginfile($course, $birecord_or_cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }

    // Get file areas
    $fileareas = local_ard_notification_get_fileareas();
    if (!in_array($filearea, $fileareas)) {
        send_file_not_found();
    }

    $fs = get_file_storage();
    $filename = array_pop($args);
    $instanceid = array_pop($args);
    $filepath = '/';

    if (!$file = $fs->get_file($context->id, 'local_ard_notification', $filearea, $instanceid, $filepath, $filename)
        or $file->is_directory() or !in_array($filearea, $fileareas)) {
        send_file_not_found();
    }

    \core\session\manager::write_close();
    send_stored_file($file, null, 0, $forcedownload, $options);
}

/**
 * Add bulk to user actions
 */
function local_ard_notification_bulk_user_actions() {

    global $CFG;
    $syscontext = context_system::instance();

    // Add schedule bulk action
    if (has_capability('moodle/site:readallmessages', $syscontext) /* && !empty($CFG->messaging) */) {
        $actions['local_ard_schedule_bulk'] = new action_link(
            new moodle_url('/local/ard_notification/schedule_bulk.php'),
            get_string('schedule_invitation_email', 'local_ard_notification'));
    }

    return $actions;

}
