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
 * @package    local_fsbaclogin
 * @copyright  2021 Ariadne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fsbaclogin;

class profilazione_form extends \moodleform {

    public function definition() {
        global $OUTPUT, $DB, $USER;

        $mform = $this->_form;

        if (isset($this->_customdata["page"])) {
            $page = $this->_customdata["page"];
        }

        if (isset($this->_customdata["params"])) {
            $params = $this->_customdata["params"];
        }

        if (isset($this->_customdata["pageoneoptionselected"])) {
            $pageoneoptionselected = $this->_customdata["pageoneoptionselected"];
        }

        if (isset($this->_customdata["pageselectedoptions"])) {
            $pageselectedoptions = $this->_customdata["pageselectedoptions"];
        }

        if (isset($this->_customdata["makeonlyprofilazione"])) {
            $makeonlyprofilazione = $this->_customdata["makeonlyprofilazione"];
        }

        $mform->addElement('hidden', "makeonlyprofilazione", $makeonlyprofilazione);
        $mform->setType("makeonlyprofilazione", PARAM_BOOL);

        if (!empty($params)) {
            foreach ($params as $k => $param) {
                $mform->addElement('hidden', $k, $param);
                $mform->setType($k, PARAM_INT);
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

        $profilazionepagetitles = array(
            "cosa_ti_aspetti_di_trovare_sulla_fad",
            "cosa_ti_sta_piu_a_cuore",
            "a_quali_processi_e_funzioni_sei_piu_interessato",
            "quali_temi_vorresti_approfondire",
            "con_quale_modalita_preferisci_aggiornarti",
            "ultimo_step"
        );

        $profilazionepagesubtitles = array(
            "scegli_almeno_un_opzione",
            "scegli_da_uno_a_tre_opzioni",
            "scegli_da_uno_a_tre_opzioni",
            "scegli_da_uno_a_tre_opzioni",
            "scegli_almeno_un_opzione"
        );

        $options = array();
        if ($page <= 4) {
            $options = array_keys($profilazionefields[$page]);
        }
        $mform->addElement('html', \html_writer::tag("h3", get_string($profilazionepagetitles[$page], "local_fsbaclogin")));

        $mform->addElement('html', "<div class='profilazione_steps'>
                                     <div class='step'></div>
                                     <div class='step'></div>
                                     <div class='step'></div>
                                     <div class='step'></div>
                                     <div class='step'></div>
                                     <div class='step'></div>
                                    </div>");

        if ($page <= 4) {
            $mform->addElement('html', \html_writer::tag("p", get_string($profilazionepagesubtitles[$page], "local_fsbaclogin"), ["class" => 'profilazione_subtitle']));
        }
        if ($page > 4) {
            $mform->addElement('html', \html_writer::tag("p", get_string("ultimo_step_info", "local_fsbaclogin"), ["class" => 'profilazione_subtitle']));
            $mform->addElement('html', \html_writer::tag("div", get_string("ultimo_step_info_list", "local_fsbaclogin"), ["class" => 'profilazione_info_list']));
        }
        if (!$pageoneoptionselected) {
            $mform->addElement('html', \html_writer::tag("p", get_string("chooseatleastoneoption", "local_fsbaclogin"), ["class" => 'profilazione_error']));
        } else {
            if ($page > 0 && $page <= 3) {
                if ($pageselectedoptions > 3) {
                    $mform->addElement('html', \html_writer::tag("p", get_string("choosemaxthreeoptions", "local_fsbaclogin"), ["class" => 'profilazione_error']));
                }
            }
        }

        foreach ($options as $k => $option) {
            $mform->addElement('advcheckbox', "page$page" . "opzione$k", get_string($option, "local_fsbaclogin"));

            if ($makeonlyprofilazione) {
                $usercontext = \context_user::instance($USER->id);
                $sql = "SELECT t.name
                          FROM {tag_instance} ti
                          JOIN {tag} t
                            ON t.id = ti.tagid
                         WHERE ti.contextid = ?";
                $existingtags = $DB->get_records_sql($sql, array($usercontext->id));
                $existingtagnames = array_keys($existingtags);
                $tagnames = $profilazionefields[$page][$option];

                if (is_array($tagnames)) {
                    foreach ($tagnames as $tagname) {
                        if (in_array($tagname, $existingtagnames)) {
                            $mform->setDefault("page$page" . "opzione$k", true);
                            break;
                        }
                    }
                } else if (is_string($tagnames) && !empty($tagnames)) {
                    if (in_array($tagnames, $existingtagnames)) {
                        $mform->setDefault("page$page" . "opzione$k", true);
                    }
                }
            }
        }

        if ($page <= 4) {
            $this->add_action_buttons(false, get_string("forwardtonextstep", "local_fsbaclogin"));
        } else {
            // $shownewslettercheckbox = true;
            // $newsletterfieldid = $DB->get_field("user_info_field", "id", array("shortname" => "newsletter"));
            // $existingfield = $DB->get_record("user_info_data", array("userid" => $USER->id, "fieldid" => $newsletterfieldid));
            // if ($existingfield && $existingfield->data == 1) {
            //     $shownewslettercheckbox = false;
            // }
            // if ($shownewslettercheckbox) {
            //     $mform->addElement('checkbox', "newsletter", get_string("newsletter_desc", "local_fsbaclogin"));
            // }
            $this->add_action_buttons(false, get_string("finishandgototrainingcontent", "local_fsbaclogin"));
        }
    }
}
