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
 * Sticky footer module.
 *
 * @module     format_corsofsbac/unenrol_user_from_course
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import { call as fetchMany } from 'core/ajax';
import $ from 'jquery';

const unenrol_user_from_course = (event) => {
    let userid = event.data.userid;
    let courseid = event.data.courseid;
    fetchMany([{
        methodname: 'format_corsofsbac_unenrol_user_from_course',
        args: {
            userid,
            courseid
        },
    }]);
    window.location.reload();
};

export const init = (userid, courseid) => {
    $('.button-unenrol').on("click", { userid, courseid }, unenrol_user_from_course);
};
