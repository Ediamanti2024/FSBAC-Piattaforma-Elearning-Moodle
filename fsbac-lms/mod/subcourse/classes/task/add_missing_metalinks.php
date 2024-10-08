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
 * Provides the {@see mod_subcourse\task\fetch_grades} class.
 *
 * @package     mod_subcourse
 * @category    task
 * @copyright   2021-2022 Ariadne Digital
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_subcourse\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/subcourse/lib.php');

/**
 * Add missing metalinks
 * Example: for each course, if there are metalinks enrol methods that do not match any cm subcourse, they will be deleted.
 *
 * @copyright 2021-2022 Ariadne Digital
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class add_missing_metalinks extends \core\task\scheduled_task {

    /**
     * Returns a descriptive name for this task shown to admins
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskaddmissingmetalinks', 'mod_subcourse');
    }

    /**
     * Performs the task
     *
     * @throws moodle_exception on an error (the job will be retried)
     */
    public function execute() {

        // Get Subcourse modules
        $subcourse_modules = $this->get_subcourse_modules();

        foreach ($subcourse_modules as $subcourse) {
            // If does not exists a metalink between course and refcourse
            if (!$this->exists_a_metalink($subcourse->course, $subcourse->refcourse)) {
                // Trace event
                mtrace(
                    get_string('addmissingmetalink', 'mod_subcourse',
                    ['fromcourseid' => $subcourse->refcourse, 'tocourseid' => $subcourse->course]
                ));
                // Set course metalink
                subcourse_set_course_metalink($subcourse->course, $subcourse->refcourse);
            }
        }
    }

    /**
     * Get Subcourse modules
     */
    private function get_subcourse_modules() {

        global $DB;
        return $DB->get_records('subcourse', []);
    }

    /**
     * Exists a metalink
     */
    private function exists_a_metalink($course, $refcourse) {

        global $DB;
        return $DB->record_exists('enrol', ['courseid' => $refcourse, 'customint1' => $course]);
    }

}
