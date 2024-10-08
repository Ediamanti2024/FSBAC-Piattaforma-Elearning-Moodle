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
 * @package   theme_fsbac
 * @copyright 2016 Ryan Wyllie
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_fsbac_admin_settingspage_tabs('themesettingfsbac', get_string('configtitle', 'theme_fsbac'));
    $page = new admin_settingpage('theme_fsbac_general', get_string('generalsettings', 'theme_fsbac'));

    // Unaddable blocks.
    // Blocks to be excluded when this theme is enabled in the "Add a block" list: Administration, Navigation, Courses and
    // Section links.
    $default = 'navigation,settings,course_list,section_links';
    $setting = new admin_setting_configtext(
        'theme_fsbac/unaddableblocks',
        get_string('unaddableblocks', 'theme_fsbac'),
        get_string('unaddableblocks_desc', 'theme_fsbac'),
        $default,
        PARAM_TEXT
    );
    //$page->add($setting);

    // Preset.
    $name = 'theme_fsbac/preset';
    $title = get_string('preset', 'theme_fsbac');
    $description = get_string('preset_desc', 'theme_fsbac');
    $default = 'default.scss';

    $context = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'theme_fsbac', 'preset', 0, 'itemid, filepath, filename', false);

    $choices = [];
    foreach ($files as $file) {
        $choices[$file->get_filename()] = $file->get_filename();
    }
    // These are the built in presets.
    $choices['default.scss'] = 'default.scss';
    $choices['plain.scss'] = 'plain.scss';

    $setting = new admin_setting_configthemepreset($name, $title, $description, $default, $choices, 'fsbac');
    $setting->set_updatedcallback('theme_reset_all_caches');
    //$page->add($setting);

    // Preset files setting.
    $name = 'theme_fsbac/presetfiles';
    $title = get_string('presetfiles', 'theme_fsbac');
    $description = get_string('presetfiles_desc', 'theme_fsbac');

    $setting = new admin_setting_configstoredfile(
        $name,
        $title,
        $description,
        'preset',
        0,
        array('maxfiles' => 20, 'accepted_types' => array('.scss'))
    );
    // $page->add($setting);


    // Background image Public home
    $key = "backgroundimage_banner_publichome";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configstoredfile($name, $title, $description, $key);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);


    $key = "title_banner_publichome";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);


    $key = "subtitle_banner_publichome";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Background methods_training Public home
    $key = "methods_training";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configstoredfile($name, $title, $description, $key);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Background banner_blue_image Public home
    $key = "banner_blue_image";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configstoredfile($name, $title, $description, $key);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $key = "dayshomequery";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configtext($name, $title, $description, 60, PARAM_INT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);


    $key = "interests";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);


    $key = "notify_text";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $key = "notify_link";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);


    $key = "facebooklink";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $key = "linkedinlink";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $key = "twitterlink";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $key = "instagramlink";
    $name = "theme_fsbac/$key";
    $title = get_string("setting:$key", 'theme_fsbac');
    $description = get_string("setting:$key" . "_desc", 'theme_fsbac');
    $setting = new admin_setting_configtext($name, $title, $description, '', PARAM_TEXT);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);













    // Background image setting.
    $name = 'theme_fsbac/backgroundimage';
    $title = get_string('backgroundimage', 'theme_fsbac');
    $description = get_string('backgroundimage_desc', 'theme_fsbac');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'backgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    //$page->add($setting);

    // Login Background image setting.
    $name = 'theme_fsbac/loginbackgroundimage';
    $title = get_string('loginbackgroundimage', 'theme_fsbac');
    $description = get_string('loginbackgroundimage_desc', 'theme_fsbac');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'loginbackgroundimage');
    $setting->set_updatedcallback('theme_reset_all_caches');
    //$page->add($setting);

    // Variable $body-color.
    // We use an empty default value because the default colour should come from the preset.
    $name = 'theme_fsbac/brandcolor';
    $title = get_string('brandcolor', 'theme_fsbac');
    $description = get_string('brandcolor_desc', 'theme_fsbac');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, '');
    $setting->set_updatedcallback('theme_reset_all_caches');
    //$page->add($setting);

    // Must add the page after definiting all the settings!
    $settings->add($page);

    // Advanced settings.
    $page = new admin_settingpage('theme_fsbac_advanced', get_string('advancedsettings', 'theme_fsbac'));

    // Raw SCSS to include before the content.
    $setting = new admin_setting_scsscode(
        'theme_fsbac/scsspre',
        get_string('rawscsspre', 'theme_fsbac'),
        get_string('rawscsspre_desc', 'theme_fsbac'),
        '',
        PARAM_RAW
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    // Raw SCSS to include after the content.
    $setting = new admin_setting_scsscode(
        'theme_fsbac/scss',
        get_string('rawscss', 'theme_fsbac'),
        get_string('rawscss_desc', 'theme_fsbac'),
        '',
        PARAM_RAW
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
