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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once __DIR__ . '/../../locallib.php';

class copy_bulk_form extends moodleform {

    /**
     * Form definition
     */
    public function definition() {

        // Form
        $mform = &$this->_form;

        // Bulk Id, hidden Parameter
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // Bulk copy message
        $mform->addElement(
            'static',
            'copybulk',
            get_string('copybulk', 'local_ard_notification'),
            get_string('copybulkdesc', 'local_ard_notification')
        );

        // Action buttons
        $this->add_action_buttons();
    }

    /**
     * Copy bulk
     */
    public function copy_bulk($formdata) {

        global $DB;

        // Get bulk
        $bulk = $DB->get_record('local_ard_notification_bulk', ['id' => $formdata->id]);
        // Copy bulk
        $bulk->bulkname = get_string('copy', 'local_ard_notification') . ' ' . $bulk->bulkname;
        $bulk->timecreated = time();
        $bulk->timemodified = time();
        $copybulkid = $DB->insert_record('local_ard_notification_bulk', $bulk);
        // Copy bulk users
        $this->copy_bulk_users($formdata->id, $copybulkid);
        // Copy bulk files
        $this->copy_bulk_files($formdata->id, $copybulkid);
    }

    /**
     * Copy bulk users
     */
    private function copy_bulk_users($bulkid, $copybulkid) {

        global $DB;
        $bulk_users = $DB->get_records('local_ard_notification_users', ['bulkid' => $bulkid]);
        foreach($bulk_users as $bulk_user) {
            $bulk_user->bulkid = $copybulkid;
            $bulk_user->timecreated = time();
            $bulk_user->timemodified = time();
            $DB->insert_record('local_ard_notification_users', $bulk_user);
        }
    }

    /**
     * Copy bulk files
     */
    private function copy_bulk_files($bulkid, $copybulkid) {

        global $CFG;
        // Get file areas
        $fileareas = local_ard_notification_get_fileareas();
        // Get bulk area files for each filearea
        $fs = get_file_storage();
        foreach($fileareas as $filearea) {
            // Get area files
            $files = $fs->get_area_files(\context_system::instance()->id, 'local_ard_notification', $filearea, $bulkid);
            foreach ($files as $file) {
                // Create file record
                $newfile = array(
                    'contextid' => \context_system::instance()->id,
                    'component' => 'local_ard_notification',
                    'filearea' => $filearea,
                    'itemid' => $copybulkid
                );
                // Create file from existing one
                $fs->create_file_from_storedfile($newfile, $file);
            }
        }
    }
}
