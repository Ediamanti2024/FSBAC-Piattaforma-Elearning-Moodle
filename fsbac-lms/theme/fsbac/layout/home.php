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

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

user_preference_allow_ajax_update('drawer-open-index', PARAM_BOOL);
user_preference_allow_ajax_update('drawer-open-block', PARAM_BOOL);

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
    $blockdraweropen = (get_user_preferences('drawer-open-block') == true);
} else {
    $courseindexopen = false;
    $blockdraweropen = false;
}

if (defined('BEHAT_SITE_RUNNING')) {
    $blockdraweropen = true;
}

$extraclasses = ['uses-drawers'];
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

$primary =  new theme_fsbac\navigation($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);
$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);


$theme = \theme_config::load('fsbac');

$imageBannerUrl = $theme->setting_file_url('backgroundimage_banner_publichome', 'backgroundimage_banner_publichome');
$titlePage = format_string($theme->settings->{"title_banner_publichome"});
$subTitlePage = format_string($theme->settings->{"subtitle_banner_publichome"});
$imageBannerTraningMethod = $theme->setting_file_url('methods_training', 'methods_training');
$imageBannerBlueImage = $theme->setting_file_url('banner_blue_image', 'banner_blue_image');
$isLogged = isloggedin() && !isguestuser();

$imagesFooter = \theme_fsbac\fsbac::get_footer_images();
$linksSocialFooter =  \theme_fsbac\fsbac::get_footer_links_social();
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
    'imageBannerUrl' => $imageBannerUrl,
    'titlePage' => $titlePage,
    'subTitlePage' => $subTitlePage,
    'imageBannerTraningMethod' => $imageBannerTraningMethod,
    'imageBannerBlueImage' => $imageBannerBlueImage,
    'imagesFooter' => $imagesFooter,
    'linksSocialFooter' => $linksSocialFooter,
    'isLogged' => $isLogged,
    'menuNoLogin' =>  $primarymenu['menuNoLogin'],
    'menuLogin' => $primarymenu['menuLogin'],
    'notify' => $notify

];

if (!$isLogged) {
    $corsiInVetrina = \theme_fsbac\fsbac::get_corsi_invetrina();
    $percorsi = \theme_fsbac\fsbac::get_percorsi_home();
    $interests = \theme_fsbac\fsbac::get_interests();
    $templatecontext['corsiInVetrina'] = $corsiInVetrina;
    $templatecontext['percorsi'] = $percorsi;
    $templatecontext['interests'] = $interests;
} else {
    $count = format_string($theme->settings->{"dayshomequery"});;

    $corsiHome =  \theme_fsbac\fsbac::get_sql_course_home($count);
    $corsiPerTe = \theme_fsbac\fsbac::get_corsi_perte();
    $corsiLike = \theme_fsbac\fsbac::get_corsi_like();
    $corsiSuggeriti = \theme_fsbac\fsbac::get_corsi_suggeriti($corsiHome);
    $corsiLastMinute = \theme_fsbac\fsbac::get_corsi_last_minute($corsiHome);
    $corsiTendenze = \theme_fsbac\fsbac::get_corsi_tedenza();
    $corsiNew = \theme_fsbac\fsbac::get_corsi_nuovi($corsiHome);


    $templatecontext['corsiPerTe'] = $corsiPerTe;
    $templatecontext['corsiPerTeShow'] = count($corsiPerTe) > 0;

    $templatecontext['corsiLike'] = $corsiLike;
    $templatecontext['corsiLikeShow'] = count($corsiLike) > 0;

    $templatecontext['corsiSuggeriti'] = $corsiSuggeriti;
    $templatecontext['corsiSuggeritiShow'] = count($corsiSuggeriti) > 0;

    $templatecontext['corsiLastMinute'] = $corsiLastMinute;
    $templatecontext['corsiLastMinuteShow'] = count($corsiLastMinute) > 0;

    $templatecontext['corsiTendenze'] = $corsiTendenze;

    $templatecontext['corsiNew'] = $corsiNew;


    $templatecontext['firstname'] = $USER->firstname;
    $CF = $USER->profile['CF'];
    $inverseCalculator = new \theme_fsbac\InverseCalculator($CF);
    $templatecontext['isF'] = $inverseCalculator->getSubject()->getGender() === 'F';
}

echo $OUTPUT->render_from_template('theme_fsbac/home', $templatecontext);
