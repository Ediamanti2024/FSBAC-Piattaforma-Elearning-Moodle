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
 * @package    local_ard_notification
 * @copyright  2021-2022 Ariadne Digital
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_ard_notification\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

use moodle_url;
use paging_bar;

require_once __DIR__ . '/../../locallib.php';

class user_bulks_monitoring implements renderable, templatable {

    /**
     * User id
     */
    public $userid;
    /**
     * Search
     */
    public $search;

    /**
     * Pagigation parameters
     */
    public $page;
    public $perpage;
    public $sort;
    public $order;

    /**
     * Constructor.
     * @param int $userid
     * @param int $page
     * @param int $perpage
     * @param int $sort
     * @param int $order
     */
    public function __construct($search, $userid, $page, $perpage, $sort, $order) {

        $this->userid = $userid;
        $this->search = $search;

        $this->page = $page;
        $this->perpage = $perpage;
        $this->sort = $sort;
        $this->order = $order;
    }

    /**
     * Export for template
     */
    public function export_for_template(renderer_base $output) {

        // Data
        $data = array();
        // Get user information
        $user = $this->get_user_information($this->userid);
        $data['user'] = array(
            'id' => $user->id,
            'name' => $user->firstname . ' ' . $user->lastname,
            'email' => $user->email,
            'institution' => $user->institution
        );
        // Get user bulks list
        $userbulks_list = $this->get_user_bulks_list($this->search, $this->userid, $this->page, $this->perpage, $this->sort, $this->order);
        // Search query
        $data['q'] = $this->search;
        // User id
        $data['userid'] = intval($this->userid);
        // Has bulk users
        $data['hasuserbulks'] = false;
        if($userbulks_list) {
            $data['hasuserbulks'] = true;
        }
        foreach($userbulks_list as $user_bulk) {
            // Data
            $data['bulks'][] = array(
                'bulkid' => $user_bulk->id,
                'bulkname' => $user_bulk->bulkname,
                'messagesubject' => $user_bulk->messagesubject,
                'messagebody' => local_ard_notification_rewrite_field_files($user_bulk->id, 'messagebody', $user_bulk->messagebody),
                'status' => array(
                    'color' => local_ard_notification_get_colors_map()[$user_bulk->status],
                    'label' => get_string($user_bulk->status, 'local_ard_notification')
                ),
                'statustime' => date('d-m-Y H:i:s', $user_bulk->statustime),
                'statusdescription' => $user_bulk->statusdescription,
                'enablesendnow' => $user_bulk->status == 'notscheduled' ? false : true
            );
        }
        // Pagination
        $data = $this->pagination($output, $data);
        return $data;
    }

    /**
     * Get user information
     */
    private function get_user_information($userid) {

        global $DB;
        return $DB->get_record('user', ['id' => $userid], 'id, firstname, lastname, email, institution');
    }

    /**
     * Get user bulks list, paginated
     */
    private function get_user_bulks_list($search, $userid, $page, $perpage, $sort, $order) {

        global $DB;
        return $DB->get_records_sql('
            SELECT b.id, b.bulkname, b.messagesubject, b.messagebody, u.status, u.statustime, u.statusdescription
            FROM {local_ard_notification_bulk} b JOIN {local_ard_notification_users} u ON b.id = u.bulkid
            WHERE u.userid = :userid AND ' . $DB->sql_like('bulkname', ':bulkname', false) . ' ORDER BY ' . $sort . ' ' . $order,
            ['userid' => $userid, 'bulkname' => '%' . $DB->sql_like_escape($search) . '%'], ($page * $perpage), $perpage
        );
    }

    /**
     * Handle list pagination
     */
    private function pagination($output, $data) {

        global $DB, $PAGE;

        $pagingurl = new moodle_url($PAGE->url, array('userid' => $this->userid, 'page' => $this->page));
        $pagingbar = new paging_bar(
            $DB->count_records('local_ard_notification_users', ['userid' => $this->userid]),
            $this->page,
            $this->perpage,
            $pagingurl->out()
        );

        $pagination = $pagingbar->export_for_template($output);
        $data = array_merge($data, (array) $pagination);
        return $data;
    }

}
