<?php



require_once(dirname(__FILE__) . '/../../../config.php');
defined('MOODLE_INTERNAL') || die;



global $CFG, $USER, $PAGE;



require_login();



$theme = \theme_config::load('fsbac');
$courses = \theme_fsbac\fsbac::get_corsi_riservati();

$imageBannerBlueImage = $theme->setting_file_url('banner_blue_image', 'banner_blue_image');

$filtersCourses = new stdClass();
$filtersCourses->tags = \theme_fsbac\fsbac::get_options_tag();
$filtersCourses->paths = \theme_fsbac\fsbac::get_options_percorsi();
$filtersCourses->typeCourses = \theme_fsbac\fsbac::get_options_type_courses();
$filtersCourses->teachers = \theme_fsbac\fsbac::get_options_teacher();
$filtersCourses->program = \theme_fsbac\fsbac::get_options_programma();
$filtersCourses->scadenza = \theme_fsbac\fsbac::get_options_scadenza();

$params = new stdClass();
foreach ($_GET as $key => $value) {
    $params->{$key} = $value;
}


$PAGE->set_title(get_string('courses.title', 'theme_fsbac'));
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('theme_fsbac/pages/courses/main', [
    'isloggedin' => true,
    'imageBannerBlueImage' => $imageBannerBlueImage, 'courses' => $courses,
    'coursesJson' => json_encode($courses),
    'orderFilters' => \theme_fsbac\fsbac::get_options_filter_order(),
    'filtersCourses' => $filtersCourses,
    'searchLabel' => get_string('courses.title_filters', 'theme_fsbac'),
    'params' => json_encode($params)
]);
echo $OUTPUT->footer();
