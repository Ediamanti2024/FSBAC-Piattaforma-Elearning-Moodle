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

namespace local_fsbaclogin;

defined('MOODLE_INTERNAL') || die();

class user_profilazione {

    public static function is_user_profilazione_complete($userid) : bool {

        global $DB;

        $profilazione_field = $DB->get_record("user_info_field", array("shortname" => "profilazione"));
        // se non è ancora stato creato lo user custom field 'profilazione'
        if (!$profilazione_field) {
            debugging("User custom field 'profilazione' doesn't exist", DEBUG_DEVELOPER);
            $is_user_profilazione_complete = true;
        } else {
            $sql = "SELECT uid.id, uid.data
                      FROM {user_info_data} uid
                      JOIN {user_info_field} uif
                        ON uif.id = uid.fieldid
                     WHERE uid.userid = ?
                       AND uif.shortname = 'profilazione'
                       AND uid.data = 1";
            $user_profilazione = $DB->get_record_sql($sql, array($userid));
            // se l'utente ha lo user custom field 'profilazione' valorizzato a 1
            if ($user_profilazione) {
                $is_user_profilazione_complete = true;
            } else {
                $is_user_profilazione_complete = false;
            }
        }

        return $is_user_profilazione_complete;

    }

}
