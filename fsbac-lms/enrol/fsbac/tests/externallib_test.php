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

namespace enrol_fsbac;

use core_external\external_api;
use enrol_fsbac_external;
use externallib_advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');
require_once($CFG->dirroot . '/enrol/fsbac/externallib.php');

/**
 * Self enrol external PHPunit tests
 *
 * @package   enrol_fsbac
 * @copyright 2013 Rajesh Taneja <rajesh@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.6
 */
class externallib_test extends externallib_advanced_testcase {

    /**
     * Test get_instance_info
     */
    public function test_get_instance_info() {
        global $DB;

        $this->resetAfterTest(true);

        // Check if fsbac enrolment plugin is enabled.
        $fsbacplugin = enrol_get_plugin('fsbac');
        $this->assertNotEmpty($fsbacplugin);

        $studentrole = $DB->get_record('role', array('shortname'=>'student'));
        $this->assertNotEmpty($studentrole);

        $coursedata = new \stdClass();
        $coursedata->visible = 0;
        $course = self::getDataGenerator()->create_course($coursedata);

        // Add enrolment methods for course.
        $instanceid1 = $fsbacplugin->add_instance($course, array('status' => ENROL_INSTANCE_ENABLED,
                                                                'name' => 'Test instance 1',
                                                                'customint6' => 1,
                                                                'roleid' => $studentrole->id));
        $instanceid2 = $fsbacplugin->add_instance($course, array('status' => ENROL_INSTANCE_DISABLED,
                                                                'customint6' => 1,
                                                                'name' => 'Test instance 2',
                                                                'roleid' => $studentrole->id));

        $instanceid3 = $fsbacplugin->add_instance($course, array('status' => ENROL_INSTANCE_ENABLED,
                                                                'roleid' => $studentrole->id,
                                                                'customint6' => 1,
                                                                'name' => 'Test instance 3',
                                                                'password' => 'test'));

        $enrolmentmethods = $DB->get_records('enrol', array('courseid' => $course->id, 'status' => ENROL_INSTANCE_ENABLED));
        $this->assertCount(3, $enrolmentmethods);

        $this->setAdminUser();
        $instanceinfo1 = enrol_fsbac_external::get_instance_info($instanceid1);
        $instanceinfo1 = external_api::clean_returnvalue(enrol_fsbac_external::get_instance_info_returns(), $instanceinfo1);

        $this->assertEquals($instanceid1, $instanceinfo1['id']);
        $this->assertEquals($course->id, $instanceinfo1['courseid']);
        $this->assertEquals('fsbac', $instanceinfo1['type']);
        $this->assertEquals('Test instance 1', $instanceinfo1['name']);
        $this->assertTrue($instanceinfo1['status']);
        $this->assertFalse(isset($instanceinfo1['enrolpassword']));

        $instanceinfo2 = enrol_fsbac_external::get_instance_info($instanceid2);
        $instanceinfo2 = external_api::clean_returnvalue(enrol_fsbac_external::get_instance_info_returns(), $instanceinfo2);
        $this->assertEquals($instanceid2, $instanceinfo2['id']);
        $this->assertEquals($course->id, $instanceinfo2['courseid']);
        $this->assertEquals('fsbac', $instanceinfo2['type']);
        $this->assertEquals('Test instance 2', $instanceinfo2['name']);
        $this->assertEquals(get_string('canntenrol', 'enrol_fsbac'), $instanceinfo2['status']);
        $this->assertFalse(isset($instanceinfo2['enrolpassword']));

        $instanceinfo3 = enrol_fsbac_external::get_instance_info($instanceid3);
        $instanceinfo3 = external_api::clean_returnvalue(enrol_fsbac_external::get_instance_info_returns(), $instanceinfo3);
        $this->assertEquals($instanceid3, $instanceinfo3['id']);
        $this->assertEquals($course->id, $instanceinfo3['courseid']);
        $this->assertEquals('fsbac', $instanceinfo3['type']);
        $this->assertEquals('Test instance 3', $instanceinfo3['name']);
        $this->assertTrue($instanceinfo3['status']);
        $this->assertEquals(get_string('password', 'enrol_fsbac'), $instanceinfo3['enrolpassword']);

        // Try to retrieve information using a normal user for a hidden course.
        $user = self::getDataGenerator()->create_user();
        $this->setUser($user);
        try {
            enrol_fsbac_external::get_instance_info($instanceid3);
        } catch (\moodle_exception $e) {
            $this->assertEquals('coursehidden', $e->errorcode);
        }
    }

    /**
     * Test enrol_user
     */
    public function test_enrol_user() {
        global $DB;

        self::resetAfterTest(true);

        $user = self::getDataGenerator()->create_user();
        self::setUser($user);

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course(array('groupmode' => SEPARATEGROUPS, 'groupmodeforce' => 1));
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $user4 = self::getDataGenerator()->create_user();

        $context1 = \context_course::instance($course1->id);
        $context2 = \context_course::instance($course2->id);

        $fsbacplugin = enrol_get_plugin('fsbac');
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $instance1id = $fsbacplugin->add_instance($course1, array('status' => ENROL_INSTANCE_ENABLED,
                                                                'name' => 'Test instance 1',
                                                                'customint6' => 1,
                                                                'roleid' => $studentrole->id));
        $instance2id = $fsbacplugin->add_instance($course2, array('status' => ENROL_INSTANCE_DISABLED,
                                                                'customint6' => 1,
                                                                'name' => 'Test instance 2',
                                                                'roleid' => $studentrole->id));
        $instance1 = $DB->get_record('enrol', array('id' => $instance1id), '*', MUST_EXIST);
        $instance2 = $DB->get_record('enrol', array('id' => $instance2id), '*', MUST_EXIST);

        self::setUser($user1);

        // Self enrol me.
        $result = enrol_fsbac_external::enrol_user($course1->id);
        $result = external_api::clean_returnvalue(enrol_fsbac_external::enrol_user_returns(), $result);

        self::assertTrue($result['status']);
        self::assertEquals(1, $DB->count_records('user_enrolments', array('enrolid' => $instance1->id)));
        self::assertTrue(is_enrolled($context1, $user1));

        // Add password.
        $instance2->password = 'abcdef';
        $DB->update_record('enrol', $instance2);

        // Try instance not enabled.
        try {
            enrol_fsbac_external::enrol_user($course2->id);
        } catch (\moodle_exception $e) {
            self::assertEquals('canntenrol', $e->errorcode);
        }

        // Enable the instance.
        $fsbacplugin->update_status($instance2, ENROL_INSTANCE_ENABLED);

        // Try not passing a key.
        $result = enrol_fsbac_external::enrol_user($course2->id);
        $result = external_api::clean_returnvalue(enrol_fsbac_external::enrol_user_returns(), $result);
        self::assertFalse($result['status']);
        self::assertCount(1, $result['warnings']);
        self::assertEquals('4', $result['warnings'][0]['warningcode']);

        // Try passing an invalid key.
        $result = enrol_fsbac_external::enrol_user($course2->id, 'invalidkey');
        $result = external_api::clean_returnvalue(enrol_fsbac_external::enrol_user_returns(), $result);
        self::assertFalse($result['status']);
        self::assertCount(1, $result['warnings']);
        self::assertEquals('4', $result['warnings'][0]['warningcode']);

        // Try passing an invalid key with hint.
        $fsbacplugin->set_config('showhint', true);
        $result = enrol_fsbac_external::enrol_user($course2->id, 'invalidkey');
        $result = external_api::clean_returnvalue(enrol_fsbac_external::enrol_user_returns(), $result);
        self::assertFalse($result['status']);
        self::assertCount(1, $result['warnings']);
        self::assertEquals('3', $result['warnings'][0]['warningcode']);

        // Everything correct, now.
        $result = enrol_fsbac_external::enrol_user($course2->id, 'abcdef');
        $result = external_api::clean_returnvalue(enrol_fsbac_external::enrol_user_returns(), $result);

        self::assertTrue($result['status']);
        self::assertEquals(1, $DB->count_records('user_enrolments', array('enrolid' => $instance2->id)));
        self::assertTrue(is_enrolled($context2, $user1));

        // Try group password now, other user.
        $instance2->customint1 = 1;
        $instance2->password = 'zyx';
        $DB->update_record('enrol', $instance2);

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course2->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course2->id, 'enrolmentkey' => 'zyx'));

        self::setUser($user2);
        // Try passing and invalid key for group.
        $result = enrol_fsbac_external::enrol_user($course2->id, 'invalidkey');
        $result = external_api::clean_returnvalue(enrol_fsbac_external::enrol_user_returns(), $result);
        self::assertFalse($result['status']);
        self::assertCount(1, $result['warnings']);
        self::assertEquals('2', $result['warnings'][0]['warningcode']);

        // Now, everything ok.
        $result = enrol_fsbac_external::enrol_user($course2->id, 'zyx');
        $result = external_api::clean_returnvalue(enrol_fsbac_external::enrol_user_returns(), $result);

        self::assertTrue($result['status']);
        self::assertEquals(2, $DB->count_records('user_enrolments', array('enrolid' => $instance2->id)));
        self::assertTrue(is_enrolled($context2, $user2));

        // Try multiple instances now, multiple errors.
        $instance3id = $fsbacplugin->add_instance($course2, array('status' => ENROL_INSTANCE_ENABLED,
                                                                'customint6' => 1,
                                                                'name' => 'Test instance 2',
                                                                'roleid' => $studentrole->id));
        $instance3 = $DB->get_record('enrol', array('id' => $instance3id), '*', MUST_EXIST);
        $instance3->password = 'abcdef';
        $DB->update_record('enrol', $instance3);

        self::setUser($user3);
        $result = enrol_fsbac_external::enrol_user($course2->id, 'invalidkey');
        $result = external_api::clean_returnvalue(enrol_fsbac_external::enrol_user_returns(), $result);
        self::assertFalse($result['status']);
        self::assertCount(2, $result['warnings']);

        // Now, everything ok.
        $result = enrol_fsbac_external::enrol_user($course2->id, 'zyx');
        $result = external_api::clean_returnvalue(enrol_fsbac_external::enrol_user_returns(), $result);
        self::assertTrue($result['status']);
        self::assertTrue(is_enrolled($context2, $user3));

        // Now test passing an instance id.
        self::setUser($user4);
        $result = enrol_fsbac_external::enrol_user($course2->id, 'abcdef', $instance3id);
        $result = external_api::clean_returnvalue(enrol_fsbac_external::enrol_user_returns(), $result);
        self::assertTrue($result['status']);
        self::assertTrue(is_enrolled($context2, $user3));
        self::assertCount(0, $result['warnings']);
        self::assertEquals(1, $DB->count_records('user_enrolments', array('enrolid' => $instance3->id)));
    }
}
