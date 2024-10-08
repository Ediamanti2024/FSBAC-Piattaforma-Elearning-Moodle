<?php



require_once(dirname(__FILE__) . '/../../../config.php');
defined('MOODLE_INTERNAL') || die;



global $CFG, $USER, $PAGE;



//require_login();



$theme = \theme_config::load('fsbac');
$isLogged = isloggedin() && !isguestuser();

$imageBannerBlueImage = $theme->setting_file_url('banner_blue_image', 'banner_blue_image');



$PAGE->set_title(get_string('dicolab.title', 'theme_fsbac'));
$PAGE->set_pagelayout('standard');
$courses = \theme_fsbac\fsbac::get_corsi_dicolabpage();
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('theme_fsbac/pages/dicolab/main', ["courses" => $courses, 'isLogged ' => $isLogged]);
echo $OUTPUT->footer();
