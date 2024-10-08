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

require_once __DIR__ . '/../../config.php';
require_once $CFG->libdir . '/adminlib.php';

// Parameters
$search = optional_param('q', '', PARAM_RAW);

$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', get_config('local_ard_notification')->bulkpagination, PARAM_INT);
$sort = optional_param('sort', 'id', PARAM_TEXT);
$order = optional_param('order', 'desc', PARAM_TEXT);

// Page setup
admin_externalpage_setup('bulkmonitoring');
// Require manage all messaging
require_capability('moodle/site:manageallmessaging', context_system::instance());

// Start setting up the page
$PAGE->set_context(context_system::instance());
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('bulkmonitoring', 'local_ard_notification'));
$PAGE->set_url(new moodle_url('/local/ard_notification/bulk_monitoring.php'),
    array('q' => $search, 'page' => $page, 'perpage' => $perpage, 'sort' => $sort, 'order' => $order)
);
$PAGE->set_pagelayout('admin');
$PAGE->set_cacheable(false);

echo $OUTPUT->header();

// Render page content
$renderer = $PAGE->get_renderer('local_ard_notification');
echo $renderer->render_bulk_monitoring(new \local_ard_notification\output\bulk_monitoring($search, $page, $perpage, $sort, $order));

echo $OUTPUT->footer();
