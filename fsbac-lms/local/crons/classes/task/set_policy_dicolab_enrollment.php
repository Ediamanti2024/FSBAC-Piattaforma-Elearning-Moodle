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
use core\session\manager;

defined('MOODLE_INTERNAL') || die();

class set_policy_dicolab_enrollment extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('set_policy_dicolab_enrollment', 'local_crons');
    }

    public function execute() {
        global $DB;

        $likesummary = $DB->sql_like('tpv.summary', ':summary');
        $sql = "SELECT tpv.id
                  FROM {tool_policy_versions} tpv
                  JOIN {tool_policy} tp
                    ON tp.currentversionid = tpv.id
                 WHERE {$likesummary}";
        $policybase = $DB->get_record_sql($sql, array('summary' => '%id="dicolab_necessario"%'));
        if ($policybase) {
            $sql = "SELECT id
                      FROM {user}
                     WHERE deleted = 0 and auth='manual' 
                       AND id > 2
                       AND id NOT IN (
                    SELECT userid
                      FROM {tool_policy_acceptances}
                     WHERE policyversionid = ?)
                     AND id in (select distinct ue.userid from {enrol} e inner join {customfield_data} cd on e.courseid=cd.instanceid 
                            inner join {customfield_field} cf on cd.fieldid=cf.id and cf.shortname='pnrr' and intvalue=1
                            inner join {user_enrolments} ue on e.id=ue.enrolid
                        )";
            $notacceptedpolicybaseusers = $DB->get_records_sql($sql, array($policybase->id));
            if (!empty($notacceptedpolicybaseusers)) {
                foreach ($notacceptedpolicybaseusers as $notacceptedpolicybaseuser) {
                    $insertobj = new stdClass();
                    $insertobj->policyversionid = $policybase->id;
                    $insertobj->userid = $notacceptedpolicybaseuser->id;
                    $insertobj->status = 1;
                    $insertobj->lang = current_language();
                    $insertobj->usermodified = 0;
                    $insertobj->timecreated = time();
                    $insertobj->timemodified = time();
                    $insertobj->note = null;
                    $DB->insert_record("tool_policy_acceptances", $insertobj);
                    mtrace("L'utente " . $notacceptedpolicybaseuser->id . " ha accettato la policy dicolab " . $policybase->id);
                }
            } else {
                mtrace("La policy dicolab " . $policybase->id . " non è stata accettata da nessun nuovo utente");
            }
        }

    }

}
