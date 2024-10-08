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

use renderable;
use renderer_base;
use templatable;

class confirmlogin implements renderable, templatable {

    public $fullusername;
    public $buttonurl;
    public $logourl;

    public function __construct($fullusername, $buttonurl, $logourl) {

        $this->fullusername = $fullusername;
        $this->buttonurl = $buttonurl;
        $this->logourl = $logourl;

    }

    public function export_for_template(renderer_base $output) {

        $data = array("fullusername" => $this->fullusername, "buttonurl" => $this->buttonurl, "logourl" => $this->logourl);

        return $data;

    }
}
