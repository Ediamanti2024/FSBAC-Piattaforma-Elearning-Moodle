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
 *
 * @package local_crons
 * @category task
 * @copyright 2023 Ariadne {@link http://www.ariadne.it}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_crons\task;

use stdClass;

defined('MOODLE_INTERNAL') || die();

class check_course_certificate_existence extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('check_course_certificate_existence', 'local_crons');
    }

    public function execute() {
        global $DB;

        $hascertificatecustomfieldid = $DB->get_field("customfield_field", "id", array("shortname" => "hacertificato"));
        $templatecertificatecustomfieldid = $DB->get_field("customfield_field", "id", array("shortname" => "templatecertificato"));
        $certificates = $DB->get_records("certificate");
        $handler = \core_course\customfield\course_handler::create();
        foreach ($certificates as $certificate) {
            $coursecontextinstance = \context_course::instance($certificate->course);

            // aggiorno o inserisco il course custom field 'hacertificato'
            $hascertificatecustomfield = $DB->get_record("customfield_data", array("fieldid" => $hascertificatecustomfieldid, "contextid" => $coursecontextinstance->id));
            $data = new stdClass();
            $data->id = $certificate->course;
            $data->customfield_hacertificato = 1;
            if ($hascertificatecustomfield) {
                if ($hascertificatecustomfield->value == 0) {
                    $handler->instance_form_save($data, false);
                    mtrace("Aggiornato course custom field 'hacertificato' da 0 a 1 per corso " . $certificate->course);
                }
            } else {
                $handler->instance_form_save($data, true);
                mtrace("Aggiunto course custom field 'hacertificato' = 1 per corso " . $certificate->course);
            }

            // aggiorno o inserisco il course custom field 'templatecertificato'
            $templatecertificatecustomfield = $DB->get_record("customfield_data", array("fieldid" => $templatecertificatecustomfieldid, "contextid" => $coursecontextinstance->id));
            $data = new stdClass();
            $data->id = $certificate->course;
            $data->customfield_templatecertificato = $certificate->certificatetype;
            if ($templatecertificatecustomfield) {
                if ($templatecertificatecustomfield->value != $certificate->certificatetype) {
                    $handler->instance_form_save($data, false);
                    mtrace("Aggiornato course custom field 'templatecertificato' da " . $templatecertificatecustomfield->value . " a " . $certificate->certificatetype . " per corso " . $certificate->course);
                }
            } else {
                $handler->instance_form_save($data, true);
                mtrace("Aggiunto course custom field 'templatecertificato' = " . $certificate->certificatetype . " per corso " . $certificate->course);
            }
        }

    }

}
