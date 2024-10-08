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

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {

    // Add notification category to users navigation
    $ADMIN->add('users', new admin_category(
        'notification',
        new lang_string('notification', 'local_ard_notification')
    ));

    // Add bulk monitoring page to users > notification navigation
    $settings = new admin_externalpage(
        'bulkmonitoring',
        new lang_string('bulkmonitoring', 'local_ard_notification'),
        $CFG->wwwroot . '/local/ard_notification/bulk_monitoring.php',
        array('moodle/site:config', 'moodle/site:manageallmessaging')
    );

    $ADMIN->add('notification', $settings);

    // Add local plugin settings page
    $settingspage = new admin_settingpage(
        'local_ard_notification',
        new lang_string('pluginname', 'local_ard_notification'),
        array('moodle/site:config', 'moodle/site:manageallmessaging')
    );
    $choices = array();
    for ($i = 10; $i <= 50; $i = $i + 10) {
        if ($i == 10) $default = $i;
        $choices[$i] = $i;
    }
    $settingspage->add(new admin_setting_configselect(
        'local_ard_notification/bulkpagination',
        new lang_string('bulkpagination', 'local_ard_notification'),
        new lang_string('bulkpagination_desc', 'local_ard_notification'),
        $default,
        $choices
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_ard_notification/bulkuserspagination',
        new lang_string('bulkuserspagination', 'local_ard_notification'),
        new lang_string('bulkuserspagination_desc', 'local_ard_notification'),
        $default,
        $choices
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_ard_notification/userbulkspagination',
        new lang_string('userbulkspagination', 'local_ard_notification'),
        new lang_string('userbulkspagination_desc', 'local_ard_notification'),
        $default,
        $choices
    ));

    $ADMIN->add('localplugins', $settingspage);

}
