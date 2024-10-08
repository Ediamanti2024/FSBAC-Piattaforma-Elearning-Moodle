<?php

namespace theme_fsbac;

defined('MOODLE_INTERNAL') || die();

use renderer_base;
use stdClass;

class navigation  extends \core\navigation\output\primary
{

    private $page = null;

    public function __construct($page)
    {
        $this->page = $page;
        parent::__construct($page);
    }


    private function getCorrectUrl($path)
    {
        try {
            $lastChar = substr($path, -1);

            return $lastChar == "/" ? substr($path, 0, -1) : $path;
        } catch (Exception $e) {
            return $path;
        }
    }

    /**
     * Combine the various menus into a standardized output.
     *
     * @param renderer_base|null $output
     * @return array
     */
    public function export_for_template(?renderer_base $output = null): array
    {
        global $CFG;
        if (!$output) {
            $output = $this->page->get_renderer('core');
        }

        $menudata = (object) array_merge($this->get_primary_nav(), $this->get_custom_menu($output));
        $moremenu = new \core\navigation\output\more_menu($menudata, 'navbar-nav', false);
        $mobileprimarynav = array_merge($this->get_primary_nav(), $this->get_custom_menu($output));

        $languagemenu = new \core\output\language_menu($this->page);

        $menuNoLogin = [];

        $menu = new stdClass();
        $menu->title = get_string('header.menu.home', 'theme_fsbac');
        $menu->isActive = true;
        $menu->link =  $CFG->wwwroot;
        $menuNoLogin[] =  $menu;

        $menuCourse = new stdClass();
        $menuCourse->title = get_string('header.menu.corsi', 'theme_fsbac');
        $menuCourse->isActive = false;
        $menuCourse->link = new \moodle_url('/theme/fsbac/pages/courses.php');
        $menuNoLogin[] =  $menuCourse;

        $menuPath = new stdClass();
        $menuPath->title = get_string('header.menu.percorsi', 'theme_fsbac');
        $menuPath->isActive = false;
        $menuPath->link = new \moodle_url('/theme/fsbac/pages/paths.php');
        $menuNoLogin[] =  $menuPath;

        $menu = new stdClass();
        $menu->title = get_string('header.menu.dicolab', 'theme_fsbac');
        $menu->isActive = false;
        $menu->link = new \moodle_url('/theme/fsbac/pages/dicolab.php');
        $menuNoLogin[] =  $menu;

        $menu = new stdClass();
        $menu->title = get_string('header.menu.chisiamo', 'theme_fsbac');
        $menu->isActive = false;
        $menu->link = new \moodle_url('/theme/fsbac/pages/chisiamo.php');
        $menuNoLogin[] =  $menu;

        $menuLogin = [];

        $menu = new stdClass();
        $menu->title = get_string('header.menu.formazione', 'theme_fsbac');
        $menu->isActive = false;
        $menu->link = $CFG->wwwroot;
        $menuLogin[] =  $menu;

        $menu = new stdClass();
        $menu->title = get_string('header.menu.bacheca', 'theme_fsbac');
        $menu->isActive = false;
        $menu->link = $CFG->wwwroot . '/my';
        $menuLogin[] =  $menu;

        $menu = new stdClass();
        $menu->title = get_string('header.menu.dicolab', 'theme_fsbac');
        $menu->isActive = false;
        $menu->link = new \moodle_url('/theme/fsbac/pages/dicolab.php');
        $menuLogin[] =  $menu;


        $menu = new stdClass();
        $menu->submenu = new stdClass();
        $menu->submenu->title =  get_string('header.menu.catalogo', 'theme_fsbac');
        $menu->submenu->links = [];
        $menu->submenu->links[] = $menuPath;
        $menu->submenu->links[] = $menuCourse;
        $menuLogin[] =  $menu;


        return [
            'mobileprimarynav' => $mobileprimarynav,
            'menuLogin' => $menuLogin,
            'menuNoLogin' => $menuNoLogin,
            'moremenu' => $moremenu->export_for_template($output),
            'lang' => !isloggedin() || isguestuser() ? $languagemenu->export_for_template($output) : [],
            'user' => $this->get_user_menu($output),

        ];
    }

    /**
     * Custom menu items reside on the same level as the original nodes.
     * Fetch and convert the nodes to a standardised array.
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_custom_menu(renderer_base $output): array
    {
        global $CFG;

        // Early return if a custom menu does not exists.
        if (empty($CFG->custommenuitems)) {
            return [];
        }

        $custommenuitems = $CFG->custommenuitems;
        $currentlang = current_language();
        $custommenunodes = custom_menu::convert_text_to_menu_nodes($custommenuitems, $currentlang);
        $nodes = [];
        foreach ($custommenunodes as $node) {
            $nodes[] = $node->export_for_template($output);
        }

        return $nodes;
    }

    /**
     * Get/Generate the user menu.
     *
     * This is leveraging the data from user_get_user_navigation_info and the logic in $OUTPUT->user_menu()
     *
     * @param renderer_base $output
     * @return array
     */
    public function get_user_menu(renderer_base $output): array
    {
        global $CFG, $USER, $PAGE;
        require_once($CFG->dirroot . '/user/lib.php');

        $usermenudata = [];
        $submenusdata = [];
        $info = user_get_user_navigation_info($USER, $PAGE);
        // tolgo dalla navigazione utente la voce "Preferenze"
        $info = (array) $info;
        foreach ($info["navitems"] as $k => $navitem) {
            if (isset($navitem->titleidentifier) && $navitem->titleidentifier == "preferences,moodle") {
                unset($info["navitems"][$k]);
            }
        }
        $info = (object) $info;

        if (isset($info->unauthenticateduser)) {
            $info->unauthenticateduser['content'] = get_string($info->unauthenticateduser['content']);
            $info->unauthenticateduser['url'] = get_login_url();
            return (array) $info;
        }
        // Gather all the avatar data to be displayed in the user menu.
        $usermenudata['avatardata'][] = [
            'content' => $info->metadata['useravatar'],
            'classes' => 'current'
        ];
        $usermenudata['userfullname'] = $info->metadata['realuserfullname'] ?? $info->metadata['userfullname'];

        // Logged in as someone else.
        if ($info->metadata['asotheruser']) {
            $usermenudata['avatardata'][] = [
                'content' => $info->metadata['realuseravatar'],
                'classes' => 'realuser'
            ];
            $usermenudata['metadata'][] = [
                'content' => get_string('loggedinas', 'moodle', $info->metadata['userfullname']),
                'classes' => 'viewingas'
            ];
        }

        // Gather all the meta data to be displayed in the user menu.
        $metadata = [
            'asotherrole' => [
                'value' => 'rolename',
                'class' => 'role role-##GENERATEDCLASS##',
            ],
            'userloginfail' => [
                'value' => 'userloginfail',
                'class' => 'loginfailures',
            ],
            'asmnetuser' => [
                'value' => 'mnetidprovidername',
                'class' => 'mnet mnet-##GENERATEDCLASS##',
            ],
        ];
        foreach ($metadata as $key => $value) {
            if (!empty($info->metadata[$key])) {
                $content = $info->metadata[$value['value']] ?? '';
                $generatedclass = strtolower(preg_replace('#[ ]+#', '-', trim($content)));
                $customclass = str_replace('##GENERATEDCLASS##', $generatedclass, ($value['class'] ?? ''));
                $usermenudata['metadata'][] = [
                    'content' => $content,
                    'classes' => $customclass
                ];
            }
        }

        $modifiedarray = array_map(function ($value) {
            $value->divider = $value->itemtype == 'divider';
            $value->link = $value->itemtype == 'link';
            if (isset($value->pix) && !empty($value->pix)) {
                $value->pixicon = $value->pix;
                unset($value->pix);
            }
            return $value;
        }, $info->navitems);

        // Include the language menu as a submenu within the user menu.
        $languagemenu = new \core\output\language_menu($this->page);
        $langmenu = $languagemenu->export_for_template($output);
        if (!empty($langmenu)) {
            $languageitems = $langmenu['items'];
            // If there are available languages, generate the data for the the language selector submenu.
            if (!empty($languageitems)) {
                $langsubmenuid = uniqid();
                // Generate the data for the link to language selector submenu.
                $language = (object) [
                    'itemtype' => 'submenu-link',
                    'submenuid' => $langsubmenuid,
                    'title' => get_string('language'),
                    'divider' => false,
                    'submenulink' => true,
                ];

                // Place the link before the 'Log out' menu item which is either the last item in the menu or
                // second to last when 'Switch roles' is available.
                $menuposition = count($modifiedarray) - 1;
                if (has_capability('moodle/role:switchroles', $PAGE->context)) {
                    $menuposition = count($modifiedarray) - 2;
                }
                array_splice($modifiedarray, $menuposition, 0, [$language]);

                // Generate the data for the language selector submenu.
                $submenusdata[] = (object)[
                    'id' => $langsubmenuid,
                    'title' => get_string('languageselector'),
                    'items' => $languageitems,
                ];
            }
        }

        // Add divider before the last item.
        $modifiedarray[count($modifiedarray) - 2]->divider = true;
        $usermenudata['items'] = $modifiedarray;
        $usermenudata['submenus'] = array_values($submenusdata);

        return $usermenudata;
    }
}
