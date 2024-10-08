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
 * A drawer based layout for the fsbac theme.
 *
 * @package   theme_fsbac
 * @copyright 2021 Bas Brands
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/format/corsofsbac/locallib.php');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

user_preference_allow_ajax_update('drawer-open-index', PARAM_BOOL);
user_preference_allow_ajax_update('drawer-open-block', PARAM_BOOL);
$isLogged = isloggedin() && !isguestuser();
if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}
if ($PAGE->theme->usescourseindex === false) {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING')) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers fsbac-my-dashboard'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses);
$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

$primary = new theme_fsbac\navigation($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;

$headercontent = $header->export_for_template($renderer);
$imagesFooter = \theme_fsbac\fsbac::get_footer_images();
$linksSocialFooter = \theme_fsbac\fsbac::get_footer_links_social();
$notify = \theme_fsbac\fsbac::get_header_notify();

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
    'imagesFooter' => $imagesFooter,
    'imageBannerBlueImage' => $imageBannerBlueImage,
    'linksSocialFooter' => $linksSocialFooter,
    'isLogged' => $isLogged,
    'menuNoLogin' => $primarymenu['menuNoLogin'],
    'menuLogin' => $primarymenu['menuLogin'],
    'notify' => $notify,
    'usescourseindex' => $PAGE->theme->usescourseindex,
];

$templatecontext['firstname'] = $USER->firstname;
$CF = $USER->profile['CF'];
$inverseCalculator = new \theme_fsbac\InverseCalculator($CF);
$templatecontext['isF'] = $inverseCalculator->getSubject()->getGender() === 'F';




$templatecontext['TAB0_LINK'] = new moodle_url("/my", array("tab" => 0));
$templatecontext['TAB1_LINK'] = new moodle_url("/my", array("tab" => 1));
$templatecontext['TAB2_LINK'] = new moodle_url("/my", array("tab" => 2));

$corsi_dashboard = \theme_fsbac\fsbac::get_corsi_dashboard();
$percorsi_dashboard =  \theme_fsbac\fsbac::get_percorsi_dashboard();
$badges =  \theme_fsbac\fsbac::get_badges();



$templatecontext['followingCourses'] = count($corsi_dashboard->following);
$templatecontext['completedCourses'] = count($corsi_dashboard->completed);
$templatecontext['followingPaths'] =  count($percorsi_dashboard->following);
$templatecontext['completedPaths'] =  count($percorsi_dashboard->completed);
$templatecontext['badges'] = $badges->num;


$tab = optional_param('tab', 0, PARAM_INT);

if ($tab > 2 || $tab < 0) $tab = 0;

$templatecontext['TAB_ACTIVE_' . $tab] = true;
if ($tab === 0) {



    $followingCourses = $corsi_dashboard->following;
    $followingPaths =  $percorsi_dashboard->following;
    $completedCourses = $corsi_dashboard->completed;
    $completedPaths =  $percorsi_dashboard->completed;
    $template = [
        'corsi_following' =>   $followingCourses,
        'corsi_following_show' =>   count($followingCourses) > 0,
        'corsi_following_num' =>  count($followingCourses),
        'percorsi_following' =>   $followingPaths,
        'percorsi_following_num' =>  count($followingPaths),
        'percorsi_following_show' => count($followingPaths) > 0,
        'corsi_completed' =>   $completedCourses,
        'corsi_completed_num' =>  count($completedCourses),
        'corsi_completed_show' =>   count($completedCourses) > 0,
        'percorsi_completed' =>   $completedPaths,
        'percorsi_completed_num' =>  count($completedPaths),
        'percorsi_completed_show' =>   count($completedPaths) > 0,
        'no_activity' => count($completedPaths) === 0 &&  count($completedCourses) === 0 && count($followingPaths) === 0 && count($followingCourses) === 0
    ];

    $templatecontext['contentTab'] =  $OUTPUT->render_from_template('theme_fsbac/mydashboard/tabs/paths_courses', $template);
}

if ($tab === 1) {
    $favorite = \theme_fsbac\fsbac::get_corsi_favorite();
    $inclompledCourse = $favorite->incompleted;
    $startedCourse = $favorite->started;
    $template = [
        'incomplete_course' =>   $inclompledCourse,
        'incomplete_course_num' =>   count($inclompledCourse),
        'incomplete_course_show' =>   count($inclompledCourse) > 0,
        'incomplete_course_multiple' =>   count($inclompledCourse) > 1,

        'started_course' =>   $startedCourse,
        'started_course_num' =>   count($startedCourse),
        'started_course_show' =>   count($startedCourse) > 0,
        'started_course_multiple' =>   count($startedCourse) > 1,
        'no_activity' => count($inclompledCourse) === 0 &&  count($startedCourse) === 0

    ];
    $templatecontext['contentTab'] =  $OUTPUT->render_from_template('theme_fsbac/mydashboard/tabs/favorites',  $template);
}

if ($tab === 2) {
    $yourCertificate =   \theme_fsbac\fsbac::get_certificate()->completed;
    $yourCertificateIncompleted =  \theme_fsbac\fsbac::get_certificate_inprogress()->incompleted;
    $template = [
        'yourCertificate' =>    $yourCertificate,
        'yourCertificate_num' =>   count($yourCertificate),
        'yourCertificate_show' =>    count($yourCertificate) > 0,
        'yourCertificate_multiple' =>   count($yourCertificate) > 1,

        'yourCertificateIncompleted' =>    $yourCertificateIncompleted,
        'yourCertificateIncompleted_num' =>   count($yourCertificateIncompleted),
        'yourCertificateIncompleted_show' =>    count($yourCertificateIncompleted) > 0,
        'yourCertificateIncompleted_multiple' =>   count($yourCertificateIncompleted) > 1,

        'sesskey' => sesskey(),
        'badges' => $badges->view,
        'badgesNum' => $badges->num,
        'badgeShow' => $badges->num > 0,

        'no_activity' => count($yourCertificate) === 0 &&  count($yourCertificateIncompleted) === 0 &&  $badges->num === 0,

    ];

    $templatecontext['contentTab'] = $OUTPUT->render_from_template('theme_fsbac/mydashboard/tabs/badges',   $template);
}

echo $OUTPUT->render_from_template('theme_fsbac/mydashboard/index', $templatecontext);
