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
 * @package    local_fsbaclogin
 * @copyright  2021 Ariadne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fsbaclogin\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use templatable;

class user_profile implements renderable, templatable {

    public $generalfields;
    public $additionalfields;
    public $preferences;

    public function __construct($generalfields, $additionalfields, $preferences) {

        $this->generalfields = $generalfields;
        $this->additionalfields = $additionalfields;
        $this->preferences = $preferences;

    }

    public function export_for_template(renderer_base $output) {

        $data = array();

        $data["changegeneralfieldslink"] = new moodle_url("/local/fsbaclogin/user_profile_general_fields.php");
        foreach ($this->generalfields as $fieldlabel => $fieldvalue) {
            $field = array("name" => $fieldlabel, "value" => $fieldvalue);
            $data["generalfields"][] = $field;
        }

        $data["changeadditionalfieldslink"] = new moodle_url("/local/fsbaclogin/profilazione.php", array("makeonlyadditionalfields" => true));
        foreach ($this->additionalfields as $fieldlabel => $fieldvalue) {
            $field = array("name" => $fieldlabel, "value" => $fieldvalue);
            $data["additionalfields"][] = $field;
        }

        $data["changepreferenceslink"] = new moodle_url("/local/fsbaclogin/profilazione.php", array("makeonlyprofilazione" => true));
        foreach ($this->preferences as $preference) {
            $data["preferences"][]["tag"] = $preference;
        }

        return $data;

    }
}
