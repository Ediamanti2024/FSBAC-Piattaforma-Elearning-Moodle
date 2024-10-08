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

class bulk_users_monitoring implements renderable, templatable {

    /**
     * Bulk id
     */
    public $bulkid;
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
     * @param int $bulkid
     * @param int $page
     * @param int $perpage
     * @param int $sort
     * @param int $order
     */
    public function __construct($search, $bulkid, $page, $perpage, $sort, $order) {

        $this->bulkid = $bulkid;
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
        // Get bulk users list
        $bulkusers_list = $this->get_bulk_users_list($this->search, $this->bulkid, $this->page, $this->perpage, $this->sort, $this->order);
        // Search query
        $data['q'] = $this->search;
        // Bulk id
        $data['bulkid'] = intval($this->bulkid);
        // Has bulk users
        $data['hasbulkusers'] = false;
        if($bulkusers_list) {
            $data['hasbulkusers'] = true;
        }
        foreach($bulkusers_list as $bulk_user) {
            // Data
            $data['users'][] = array(
                'userid' => intval($bulk_user->userid),
                'user' => $bulk_user->firstname . ' ' . $bulk_user->lastname,
                'email' => $bulk_user->email,
                'institution' => $bulk_user->institution,
                'status' => array(
                    'color' => local_ard_notification_get_colors_map()[$bulk_user->status],
                    'label' => get_string($bulk_user->status, 'local_ard_notification')
                ),
                'statustime' => date('d-m-Y H:i:s', $bulk_user->statustime),
                'statusdescription' => $bulk_user->statusdescription,
                'enablesendnow' => $bulk_user->status == 'notscheduled' ? false : true
            );
        }
        // Pagination
        $data = $this->pagination($output, $data);
        return $data;
    }

    /**
     * Get bulk users list, paginated
     */
    private function get_bulk_users_list($search, $bulkid, $page, $perpage, $sort, $order) {

        global $DB;
        return $DB->get_records_sql('
            SELECT nu.*, u.firstname, u.lastname, u.email, u.institution
            FROM {local_ard_notification_users} nu JOIN {user} u ON nu.userid = u.id
            WHERE nu.bulkid = :bulkid AND ' . $DB->sql_like('u.email', ':email', false) . ' ORDER BY ' . $sort . ' ' . $order,
            ['bulkid' => $bulkid, 'email' => '%' . $DB->sql_like_escape($search) . '%'], ($page * $perpage), $perpage
        );
    }

    /**
     * Handle list pagination
     */
    private function pagination($output, $data) {

        global $DB, $PAGE;

        $pagingurl = new moodle_url($PAGE->url, array('bulkid' => $this->bulkid, 'page' => $this->page));
        $pagingbar = new paging_bar(
            $DB->count_records('local_ard_notification_users', ['bulkid' => $this->bulkid]),
            $this->page,
            $this->perpage,
            $pagingurl->out()
        );

        $pagination = $pagingbar->export_for_template($output);
        $data = array_merge($data, (array) $pagination);
        return $data;
    }

}
