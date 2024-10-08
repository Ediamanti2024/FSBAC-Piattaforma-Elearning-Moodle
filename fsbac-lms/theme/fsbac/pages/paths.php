<?php



require_once(dirname(__FILE__) . '/../../../config.php');
defined('MOODLE_INTERNAL') || die;



global $CFG, $USER, $PAGE;



//require_login();



$theme = \theme_config::load('fsbac');
$courses = \theme_fsbac\fsbac::get_percorsi_pubblici();
$isloggedin = isloggedin() && !isguestuser();
$imageBannerBlueImage = $theme->setting_file_url('banner_blue_image', 'banner_blue_image');

$filtersPaths = new stdClass();
$filtersPaths->levels = \theme_fsbac\fsbac::get_options_level();



$PAGE->set_title(get_string('paths.title', 'theme_fsbac'));
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('theme_fsbac/pages/paths/main', [
    'isloggedin' => $isloggedin,
    'imageBannerBlueImage' => $imageBannerBlueImage,
    'courses' => $courses,
    'coursesJson' => json_encode($courses),
    'orderFilters' => \theme_fsbac\fsbac::get_options_filter_order(),
    'searchLabel' => get_string('paths.title_filters', 'theme_fsbac'),
    'filtersPaths' => $filtersPaths,


]);
echo $OUTPUT->footer();
