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

/**
 * Check if bulk exists
 */
function local_ard_notification_bulk_exists($bulkid) {

    global $DB;
    return $DB->record_exists('local_ard_notification_bulk', ['id' => $bulkid]);
}

/**
 * Check if bulk user exists
 */
function local_ard_notification_bulk_user_exists($bulkid, $userid) {

    global $DB;
    return $DB->record_exists('local_ard_notification_users', ['bulkid' => $bulkid, 'userid' => $userid]);
}

/**
 * Check if user exists
 */
function local_ard_notification_user_exists($userid) {

    global $DB;
    return $DB->record_exists('user', ['id' => $userid]);
}

/**
 * Get file areas
 */
function local_ard_notification_get_fileareas() {

    $fileareas = array(
        'bulknotes',
        'messagebody',
    );
    return $fileareas;
}

/**
 * File prepare standard editor
 */
function local_ard_notification_file_prepare_standard_editor($fieldname, $fieldcontent, $options) {

    $fieldcontent = file_prepare_standard_editor(
        $fieldcontent,
        $fieldname,
        $options,
        context_system::instance(),
        'local_ard_notification',
        $fieldname,
        $fieldcontent->id
    );
    return $fieldcontent;
}

/**
 * File post update standard editor
 */
function local_ard_notification_file_postupdate_standard_editor($fieldname, $fieldcontent, $options) {

    $fieldcontent = file_postupdate_standard_editor(
        $fieldcontent,
        $fieldname,
        $options,
        context_system::instance(),
        'local_ard_notification',
        $fieldname,
        $fieldcontent->id
    );
    return $fieldcontent;
}

/**
 * Rewrite field plugin file urls
 */
function local_ard_notification_rewrite_field_files($bulkid, $fieldname, $fieldcontent) {

    global $CFG;
    require_once $CFG->libdir . '/filelib.php';
    $fieldcontent = file_rewrite_pluginfile_urls(
        $fieldcontent,
        'pluginfile.php',
        context_system::instance()->id,
        'local_ard_notification',
        $fieldname,
        $bulkid
    );
    return $fieldcontent;
}

/**
 * Get placeholders map
 */
function local_ard_notification_get_placeholders_map() {

    $placeholders_map = array(
        'sitename' => '##SITENAME##',
        'link' => '##LINK##',
        'admin' => '##ADMIN##',
        'username' => '##USERNAME##',
        'firstname' => '##FIRSTNAME##',
        'lastname' => '##LASTNAME##',
        'institution' => '##INSTITUTION##'
    );
    return $placeholders_map;
}

/**
 * Get colors map
 */
function local_ard_notification_get_colors_map() {

    $colors_map = array(
        'notscheduled' => 'primary',
        'pending' => 'warning',
        'error' => 'danger',
        'sent' => 'success'
    );
    return $colors_map;
}
