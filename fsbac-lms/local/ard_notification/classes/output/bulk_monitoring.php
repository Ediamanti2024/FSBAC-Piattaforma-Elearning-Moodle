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

class bulk_monitoring implements renderable, templatable {

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
     * @param string $search
     * @param int $page
     * @param int $perpage
     * @param int $sort
     * @param int $order
     */
    public function __construct($search, $page, $perpage, $sort, $order) {

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

        global $USER;
        // Data
        $data = array();
        // Get bulk list
        $bulk_list = $this->get_bulk_list($this->search, $this->page, $this->perpage, $this->sort, $this->order);
        // Search query
        $data['q'] = $this->search;
        // Has bulks
        $data['hasbulks'] = false;
        if($bulk_list) {
            $data['hasbulks'] = true;
            $data['myuserid'] = $USER->id;
        }
        foreach($bulk_list as $bulk) {
            // Get user detail
            $user = $this->get_bulk_user_creator($bulk->bulkcreatedby);
            // Data
            $data['bulks'][] = array(
                'bulkid' => intval($bulk->id),
                'bulkname' => $bulk->bulkname,
                'bulkcreatedbyid' => $bulk->bulkcreatedby,
                'bulkcreatedby' => $user->firstname . ' ' . $user->lastname,
                'bulkscheduletime' => $bulk->bulkscheduletime ? date('d-m-Y H:i:s', $bulk->bulkscheduletime) : '-',
                'bulkstatus' => array(
                    'color' => local_ard_notification_get_colors_map()[$bulk->bulkstatus],
                    'label' => get_string($bulk->bulkstatus, 'local_ard_notification')
                ),
                'bulknotes' => local_ard_notification_rewrite_field_files($bulk->id, 'bulknotes', $bulk->bulknotes),
                'messagesubject' => $bulk->messagesubject,
                'messagebody' => local_ard_notification_rewrite_field_files($bulk->id, 'messagebody', $bulk->messagebody),
                'messagesenderalias' => $bulk->messagesenderalias,
                'messagelanguage' => $this->get_bulk_language($bulk->messagelanguage),
                'enablesendtest' => $bulk->bulkstatus == 'notscheduled' ? false : true
            );
        }
        // Pagination
        $data = $this->pagination($output, $data);
        return $data;
    }

    /**
     * Get bulk list, paginated
     */
    private function get_bulk_list($search, $page, $perpage, $sort, $order) {

        global $DB;
        return $DB->get_records_sql('
            SELECT * FROM {local_ard_notification_bulk}
            WHERE ' . $DB->sql_like('bulkname', ':bulkname', false) . ' ORDER BY ' . $sort . ' ' . $order,
            ['bulkname' => '%' . $DB->sql_like_escape($search) . '%'], ($page * $perpage), $perpage
        );
    }

    /**
     * Get bulk user creator
     * id, firstname, lastname
     */
    private function get_bulk_user_creator($userid) {

        global $DB;
        return $DB->get_record('user', ['id' => $userid], 'id, firstname, lastname');
    }

    /**
     * Get bulk language as a string
     */
    private function get_bulk_language($bulk_messagelanguage) {

        $languages = get_string_manager()->get_list_of_translations();
        foreach ($languages as $key => $value) {
            if($key == $bulk_messagelanguage) {
                $messagelanguage = $value;
            }
        }
        return $messagelanguage;
    }

    /**
     * Handle list pagination
     */
    private function pagination($output, $data) {

        global $DB, $PAGE;

        $pagingurl = new moodle_url($PAGE->url, array('page' => $this->page));
        $pagingbar = new paging_bar(
            $DB->count_records('local_ard_notification_bulk', []),
            $this->page,
            $this->perpage,
            $pagingurl->out()
        );

        $pagination = $pagingbar->export_for_template($output);
        $data = array_merge($data, (array) $pagination);
        return $data;
    }

}
