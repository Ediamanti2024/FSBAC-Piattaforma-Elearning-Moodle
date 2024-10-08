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

class delete_bulk_form extends moodleform {

    /**
     * Form definition
     */
    public function definition() {

        // Form
        $mform = &$this->_form;

        // Bulk Id, hidden Parameter
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // Bulk delete message
        $mform->addElement(
            'static',
            'deletebulk',
            get_string('deletebulk', 'local_ard_notification'),
            get_string('deletebulkdesc', 'local_ard_notification')
        );

        // Action buttons
        $this->add_action_buttons();
    }

    /**
     * Delete bulk
     */
    public function delete_bulk($formdata) {

        global $DB;
        // Delete bulk
        $DB->delete_records('local_ard_notification_bulk', ['id' => $formdata->id]);
        // Delete bulk users
        $DB->delete_records('local_ard_notification_users', ['bulkid' => $formdata->id]);
        // Get file areas
        $fileareas = local_ard_notification_get_fileareas();
        // Delete files associated to bulk
        $fs = get_file_storage();
        foreach($fileareas as $filearea) {
            $fs->delete_area_files(context_system::instance()->id, 'local_ard_notification', $filearea, $formdata->id);
        }
    }
}
