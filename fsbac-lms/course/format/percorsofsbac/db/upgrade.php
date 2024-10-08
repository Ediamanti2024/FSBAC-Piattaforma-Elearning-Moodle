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
 * Upgrade scripts for Percorsofsbac course format.
 *
 * @package    format_percorsofsbac
 * @copyright  2017 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade script for Percorsofsbac course format.
 *
 * @param int|float $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_format_percorsofsbac_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023030700) {
        // For sites migrating from 4.0.x or 4.1.x where the indentation was removed,
        // we are disabling 'indentation' value by default.
        if ($oldversion >= 2022041900) {
            set_config('indentation', 0, 'format_percorsofsbac');
        } else {
            set_config('indentation', 1, 'format_percorsofsbac');
        }
        upgrade_plugin_savepoint(true, 2023030700, 'format', 'percorsofsbac');
    }

    if ($oldversion < 2023042404) {

        // Define table percorsofsbac_follow_path to be created.
        $table = new xmldb_table('percorsofsbac_follow_path');

        // Adding fields to table percorsofsbac_follow_path.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);
        $table->add_field('followed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '18', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table percorsofsbac_follow_path.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for percorsofsbac_follow_path.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Percorsofsbac savepoint reached.
        upgrade_plugin_savepoint(true, 2023042404, 'format', 'percorsofsbac');
    }


    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
