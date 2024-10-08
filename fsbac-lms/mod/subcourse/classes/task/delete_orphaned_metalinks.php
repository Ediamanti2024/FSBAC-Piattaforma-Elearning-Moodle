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

/**
 * Delete orphaned metalinks
 * Example: for each course, if there are metalinks enrol methods that do not match any cm subcourse, they will be deleted.
 *
 * @copyright 2021-2022 Ariadne Digital
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_orphaned_metalinks extends \core\task\scheduled_task {

    /**
     * Returns a descriptive name for this task shown to admins
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskdeleteorphanedmetalinks', 'mod_subcourse');
    }

    /**
     * Performs the task
     *
     * @throws moodle_exception on an error (the job will be retried)
     */
    public function execute() {

        // Get courses with metalinks
        $courses = $this->get_courses_with_metalinks();

        foreach ($courses as $course) {
            // Get course metalinks
            $metalinks = $this->get_course_metalinks($course->courseid);

            foreach ($metalinks as $metalink) {
                // Check if metalink is orphaned
                if ($this->is_metalink_orphaned($metalink->courseid, $metalink->customint1)) {
                    // Remove metalink
                    $this->remove_metalink($metalink);
                }
            }
        }
    }

    /**
     * Get courses with metalinks
     */
    private function get_courses_with_metalinks() {

        global $DB;
        return $DB->get_records_sql('SELECT DISTINCT(courseid) FROM {enrol} WHERE enrol = :enrol', ['enrol' => 'meta']);
    }

    /**
     * Get course metalinks
     */
    private function get_course_metalinks($courseid) {

        global $DB;
        return $DB->get_records('enrol', ['enrol' => 'meta', 'courseid' => $courseid]);
    }

    /**
     * Is metalink orphaned
     * If in the course does not exists at least one Subcourse module to the refcourse
     * The metalink is considered orphaned
     */
    private function is_metalink_orphaned($courseid, $customint1) {

        global $DB;
        // Check if exists at least one Subcourse module to the refcourse
        $subcoursemodules = $DB->record_exists_sql('
            SELECT * FROM {subcourse} sb INNER JOIN {course_modules} cm on sb.id = cm.instance
            WHERE sb.course = :course AND sb.refcourse = :refcourse
            AND module = (SELECT id FROM {modules} WHERE name = :name) AND cm.deletioninprogress = :deletioninprogress',
            ['course' => $customint1, 'refcourse' => $courseid, 'name' => 'subcourse', 'deletioninprogress' => false]
        );

        if ($subcoursemodules) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Remove metalink
     */
    private function remove_metalink($metalink) {

        // Trace event
        mtrace(
            get_string('deleteorphanedmetalink', 'mod_subcourse',
            ['fromcourseid' => $metalink->courseid, 'tocourseid' => $metalink->customint1]
        ));
        // Get enrol plugin meta type
        $plugin = enrol_get_plugin('meta');
        // Delete metalink
        if ($plugin->can_delete_instance($metalink)) {
            $plugin->delete_instance($metalink);
        }
    }

}
