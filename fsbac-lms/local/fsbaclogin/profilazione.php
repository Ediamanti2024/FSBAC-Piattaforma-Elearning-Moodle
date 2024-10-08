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
 *
 * @package    local_fsbaclogin
 * @subpackage auth
 * @copyright  1999 Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('locallib.php');
require_once($CFG->libdir . "/formslib.php");

if (!isloggedin() || isguestuser()) {
    redirect(new moodle_url("/local/fsbaclogin/index.php"));
}

$page = optional_param("page", 0, PARAM_INT);
// $newsletter = optional_param("newsletter", 0, PARAM_INT);
$makeonlyprofilazione = optional_param("makeonlyprofilazione", false, PARAM_BOOL);
$makeonlyadditionalfields = optional_param("makeonlyadditionalfields", false, PARAM_BOOL);

if ($makeonlyadditionalfields) {
    redirect(new moodle_url('/local/fsbaclogin/user_profile_additional_fields.php', array("makeonlyadditionalfields" => $makeonlyadditionalfields)));
}

$tipologiautentefieldid = $DB->get_field("user_info_field", "id", array("shortname" => "tipologia_utente"));
$tipologiautente = $DB->get_record("user_info_data", array("fieldid" => $tipologiautentefieldid, "userid" => $USER->id));
if (!$tipologiautente) {
    redirect(new moodle_url('/local/fsbaclogin/user_profile_additional_fields.php'));
} else {
    if (empty($tipologiautente->data) || $tipologiautente->data == "--") {
        redirect(new moodle_url('/local/fsbaclogin/user_profile_additional_fields.php'));
    }
}

// recupero le policies PREFERENZE accettate dall'utente
$likesummary = $DB->sql_like('tpv.summary', ':summary');
$sql = "SELECT *
          FROM {tool_policy_acceptances} tpa
          JOIN {tool_policy_versions} tpv
            ON tpv.id = tpa.policyversionid
          JOIN {tool_policy} tp
            ON tp.currentversionid = tpv.id
         WHERE {$likesummary}
           AND tpa.userid = :userid
           AND tpa.status = 1";
$preferenzepolicies = $DB->get_records_sql($sql, array("summary" => '%_preferenze%', "userid" => $USER->id));
// se l'utente vuole modificare la sua profilazione
if ($makeonlyprofilazione) {
    // se l'utente non ha accettato almeno una policy PREFERENZE, gli dico di modificare le sue accettazioni
    if (count($preferenzepolicies) == 0) {
        redirect(new moodle_url("/local/fsbaclogin/blocked_profilazione.php"));
    }
} else {
    // se l'utente non ha accettato almeno una policy PREFERENZE, gli faccio saltare la profilazione
    if (count($preferenzepolicies) == 0) {
        $user_profilazione = get_user_customfield_profilazione($USER->id);
        if (!$user_profilazione) {
            $profilazionefieldid = $DB->get_field("user_info_field", "id", array("shortname" => "profilazione"));
            $insertobj = new stdClass();
            $insertobj->userid = $USER->id;
            $insertobj->fieldid = $profilazionefieldid;
            $insertobj->data = 1;
            $insertobj->dataformat = 0;
            $DB->insert_record("user_info_data", $insertobj);
        }
        redirect($CFG->wwwroot);
    }
}

$profilazionefields = array(
    array(
        "aggiornamenti_periodici_su_novita_e_dinamiche_del_settore_culturale" => "aggiornamento",
        "contenuti_di_approfondimento_per_la_mia_professione" => "approfondimento",
        "altro" => ""
    ),
    array(
        "archeologia" => "archeologia",
        "architettura_urbanistica_paesaggio" => array("architettura", "urbanistica", "paesaggio_e_ambiente"),
        "archivi" => "archivi",
        "biblioteche" => "biblioteche",
        "industrie_culturali_e_creative" => "industrie_culturali_e_creative",
        "musei" => "musei",
        "patrimonio_immateriale" => "patrimonio_immateriale",
        "patrimonio_culturale_digitale" => "patrimonio_culturale_digitale",
        "altro" => ""
    ),
    array(
        "comunicazione_promozione_e_storytelling" => array("comunicazione", "promozione_e_storytelling"),
        "cura_e_gestione_delle_collezioni" => "cura_e_gestione_delle_collezioni",
        "data_management" => "data_management",
        "digitalizzazione_del_patrimonio" => "digitalizzazione_del_patrimonio",
        "educazione_e_mediazione" => "educazione_e_mediazione",
        "marketing_e_fundraising" => "marketing_e_fundraising",
        "fruizione_accoglienza_e_vigilanza" => "fruizione_accoglienza_e_vigilanza",
        "management_e_organizzazione" => "management_e_organizzazione",
        "policy_making" => "policy_making",
        "progettazione_culturale" => "progettazione_culturale",
        "ricerca_e_innovazione" => "ricerca_e_innovazione",
        "strategia_e_pianificazione" => "strategia_e_pianificazione",
        "tutela_conservazione_e_restauro" => array("tutela", "conservazione_e_restauro"),
        "valorizzazione_territoriale_e_sviluppo_locale" => "valorizzazione_territoriale_e_sviluppo_locale",
        "altro" => ""
    ),
    array(
        "accessibilita_inclusione_e_welfare_culturale" => array("accessibilita", "inclusione_e_welfare_culturale"),
        "audience_engagement_e_partecipazione" => "audience_engagement_e_partecipazione",
        "design_dei_servizi" => "design_dei_servizi",
        "europrogettazione" => "europrogettazione",
        "politiche_culturali" => "politiche_culturali",
        "professioni_e_competenze" => "professioni_e_competenze",
        "rigenerazione_urbana_e_sostenibilita" => "rigenerazione_urbana_e_sostenibilita",
        "sicurezza" => "sicurezza",
        "trasformazione_digitale" => "trasformazione_digitale",
        "altro" => ""
    ),
    array(
        "partecipando_a_incontri_in_diretta_o_attivita_in_presenza" => "live",
        "guardando_video_e_consultando_materiali_sempre_disponibili" => "on_demand",
        "interagendo_con_contenuti_multimediali" => "corso_multimediale",
        "ascoltando_podcast" => "podcast"
    )
);
$params = array();
if ($page == 0) {
    $pageoneoptionselected = true;
    $pageselectedoptions = 0;
} else if ($page > 0 && $page <= 5) {
    for ($j = 0; $j < $page; $j++) {
        $options = $profilazionefields[$j];
        $countoptions = count($options);
        $pageoneoptionselected = false;
        $pageselectedoptions = 0;
        for ($i = 0; $i <= $countoptions; $i++) {
            $option = optional_param("page$j" . "opzione$i", 0, PARAM_INT);
            if ($option == 1) {
                $pageoneoptionselected = true;
                $pageselectedoptions += 1;
                $params["page$j" . "opzione$i"] = $option;
            }
        }
        if (!$pageoneoptionselected) {
            $page = $j;
            break;
        } else {
            if ($page > 1 && $page <= 4) {
                if ($pageselectedoptions > 3) {
                    foreach (array_keys($params) as $param) {
                        if (strpos($param, "page$j") !== false) {
                            unset($params[$param]);
                        }
                    }
                    $page = $j;
                    break;
                }
            }
        }
    }
} else if ($page == 6) {
    // if ($newsletter == 1) {
        $newsletterfieldid = $DB->get_field("user_info_field", "id", array("shortname" => "newsletter"));
        $existingfield = $DB->get_record("user_info_data", array("userid" => $USER->id, "fieldid" => $newsletterfieldid));
        if (!$existingfield) {
            $insertobj = new stdClass();
            $insertobj->userid = $USER->id;
            $insertobj->fieldid = $newsletterfieldid;
            $insertobj->data = 1;
            $insertobj->dataformat = 0;
            $DB->insert_record("user_info_data", $insertobj);
        } else {
            $updateobj = new stdClass();
            $updateobj->id = $existingfield->id;
            $updateobj->data = 1;
            $DB->update_record("user_info_data", $updateobj);
        }
    // }

    for ($j = 0; $j < $page - 1; $j++) {
        $options = $profilazionefields[$j];
        $countoptions = count($options);
        for ($i = 0; $i <= $countoptions; $i++) {
            $option = optional_param("page$j" . "opzione$i", 0, PARAM_INT);
            if ($option == 1) {
                $params["page$j" . "opzione$i"] = $option;
            }
        }
    }

    $customfieldvalues = array();
    for ($j = 0; $j <= 4; $j++) {
        foreach (array_keys($params) as $param) {
            if (strpos($param, "page$j") !== false) {
                $param = (int) str_replace("page$j" . "opzione", "", $param);
                $customfieldvalues[] = array_values($profilazionefields[$j])[$param];
            }
        }
    }

    $usercontext = context_user::instance($USER->id);

    if ($makeonlyprofilazione) {
        core_tag_tag::delete_instances('core', 'user', $usercontext->id);
    }

    foreach ($customfieldvalues as $tags) {
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                core_tag_tag::add_item_tag('core', 'user', $USER->id, $usercontext, $tag);
            }
        } else if (is_string($tags) && !empty($tags)) {
            core_tag_tag::add_item_tag('core', 'user', $USER->id, $usercontext, $tags);
        }
    }

    $user_profilazione = get_user_customfield_profilazione($USER->id);
    if (!$user_profilazione) {
        $profilazionefieldid = $DB->get_field("user_info_field", "id", array("shortname" => "profilazione"));
        $insertobj = new stdClass();
        $insertobj->userid = $USER->id;
        $insertobj->fieldid = $profilazionefieldid;
        $insertobj->data = 1;
        $insertobj->dataformat = 0;
        $DB->insert_record("user_info_data", $insertobj);
    }

    redirect($CFG->wwwroot);
}

$baseurl = "/local/fsbaclogin/profilazione.php";
$PAGE->set_url(new moodle_url($baseurl, array("makeonlyprofilazione" => $makeonlyprofilazione)));

$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');

$PAGE->set_title(get_string("conosciamoci", "local_fsbaclogin"));
$PAGE->set_heading(get_string("conosciamoci", "local_fsbaclogin"));

if ($page <= 5) {
    $mform = new local_fsbaclogin\profilazione_form(
        new moodle_url('/local/fsbaclogin/profilazione.php', array('page' => $page + 1)),
        array("page" => $page,
                "params" => $params,
                "pageoneoptionselected" => $pageoneoptionselected,
                "pageselectedoptions" => $pageselectedoptions,
                "makeonlyprofilazione" => $makeonlyprofilazione)
    );

    if ($mform->is_cancelled()) {
        redirect(new moodle_url('/local/fsbaclogin/profilazione.php'));
    } else {
        echo $OUTPUT->header();
        $languagedata = new \core\output\language_menu($PAGE);
        $languagemenu = $languagedata->export_for_action_menu($OUTPUT);
        $languagemenu = new \local_fsbaclogin\output\language_menu($languagemenu);
        $renderer = $PAGE->get_renderer('local_fsbaclogin');
        $logourl = $OUTPUT->get_logo_url("150", "150");
        $logoutUrl = new moodle_url('/login/logout.php', ['sesskey' => sesskey()]);
        echo $OUTPUT->render_from_template('local_fsbaclogin/profilazione', [
            'title' => get_string("conosciamoci", "local_fsbaclogin"),
            'form' => $mform->render(),
            'languagemenu' => $renderer->render($languagemenu),
            'logourl' => $logourl,
            'page' => $page,
            'logoutUrl' => $logoutUrl

        ]);
        echo $OUTPUT->footer();
    }
}
