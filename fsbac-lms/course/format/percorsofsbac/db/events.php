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
 * Pearson event handler definition.
 *
 * @package format_percorsofsbac
 * @category event
 * @copyright 2017 Ariadne {@link http://www.ariadne.it}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// List of observers.

defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname' => 'core\event\course_module_created',
        'callback' => 'format_percorsofsbac_observer::created_subcourse_in_path'
    ),
    array(
        'eventname' => 'core\event\course_completed',
        'callback' => 'format_percorsofsbac_observer::subcourse_completed'
    )
);
