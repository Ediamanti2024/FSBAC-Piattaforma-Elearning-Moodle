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
 * @module     format_percorsofsbac/followbutton
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import { call as fetchMany } from 'core/ajax';
import $ from 'jquery';

const followpath = (event) => {
    let courseid = event.data.courseid;
    let userid = event.data.userid;
    let result = fetchMany([{
        methodname: 'format_percorsofsbac_follow_unfollow_path',
        args: {
            courseid,
            userid
        },
    }])[0];
    result.then(data => {
        const button = $("#followpathlabel").parent()
        const isPathfollow = $(button).hasClass('btn-primary');

        if (isPathfollow) {
            $(button).removeClass('btn-primary')
            $(button).addClass('btn-secondary')
        }
        if (!isPathfollow) {
            $(button).removeClass('btn-secondary')
            $(button).addClass('btn-primary')
        }
        $("#followpathlabel").text(data.pathfollow);
    });
};

export const init = (courseid, userid) => {
    $("button#followpath").on("click",
        { courseid, userid }
        , followpath);
};
