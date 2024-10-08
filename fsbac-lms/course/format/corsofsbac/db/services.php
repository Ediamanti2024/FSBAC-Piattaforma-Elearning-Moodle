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

defined('MOODLE_INTERNAL') || die;

$functions = array(
        'format_corsofsbac_unenrol_user_from_course' => array(
            'classname'   => 'format_corsofsbac_external',
            'methodname'  => 'unenrol_user_from_course',
            'classpath'   => 'course/format/corsofsbac/externallib.php',
            'description' => 'Unenrol user from course',
            'type'        => 'write',
            'ajax'        => true
        )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'corsofsbac course format' => array(
        'functions' => array (
            'format_corsofsbac_unenrol_user_from_course',
        ),
        'restrictedusers' => 0,
        'enabled' => 1,
        'downloadfiles' => 1
    )
);
