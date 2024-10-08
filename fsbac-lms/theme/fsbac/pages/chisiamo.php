<?php



require_once(dirname(__FILE__) . '/../../../config.php');
defined('MOODLE_INTERNAL') || die;



global $CFG, $USER, $PAGE;



//require_login();



$theme = \theme_config::load('fsbac');


$imageBannerBlueImage = $theme->setting_file_url('banner_blue_image', 'banner_blue_image');



$PAGE->set_title(get_string('chisiamo.title', 'theme_fsbac'));
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('theme_fsbac/pages/chisiamo/main', [
    'imageBannerBlueImage' => $imageBannerBlueImage,

]);
echo $OUTPUT->footer();
