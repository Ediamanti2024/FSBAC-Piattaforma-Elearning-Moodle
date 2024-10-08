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
 * Login library file of login/password related Moodle functions.
 *
 * @package    local_fsbaclogin
 * @copyright  Catalyst IT
 * @copyright  Peter Bulmer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function local_fsbaclogin_after_require_login() {
    global $USER, $CFG;

    require_once($CFG->dirroot . '/local/fsbaclogin/locallib.php');

    $user_profilazione = get_user_customfield_profilazione($USER->id);
    if (!$user_profilazione) {
        redirect(new moodle_url('/local/fsbaclogin/profilazione.php'));
    }

    if (!has_user_filled_new_additional_fields($USER->id)) {
        redirect(new moodle_url('/local/fsbaclogin/user_profile_additional_fields.php', array("makeonlyadditionalfields" => true)));
    }
}
