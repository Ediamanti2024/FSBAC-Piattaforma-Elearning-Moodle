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
 * Language file.
 *
 * @package   theme_fsbac
 * @copyright 2016 Frédéric Massart
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['advancedsettings'] = 'Advanced settings';
$string['backgroundimage'] = 'Background image';
$string['backgroundimage_desc'] = 'The image to display as a background of the site. The background image you upload here will override the background image in your theme preset files.';
$string['brandcolor'] = 'Brand colour';
$string['brandcolor_desc'] = 'The accent colour.';
$string['bootswatch'] = 'Bootswatch';
$string['bootswatch_desc'] = 'A bootswatch is a set of Bootstrap variables and css to style Bootstrap';
$string['choosereadme'] = 'Fsbac is a modern highly-customisable theme. This theme is intended to be used directly, or as a parent theme when creating new themes utilising Bootstrap 4.';
$string['configtitle'] = 'Fsbac';
$string['generalsettings'] = 'General settings';
$string['loginbackgroundimage'] = 'Login page background image';
$string['loginbackgroundimage_desc'] = 'The image to display as a background for the login page.';
$string['nobootswatch'] = 'None';
$string['pluginname'] = 'Fsbac';
$string['presetfiles'] = 'Additional theme preset files';
$string['presetfiles_desc'] = 'Preset files can be used to dramatically alter the appearance of the theme. See <a href="https://docs.moodle.org/dev/Fsbac_Presets">Fsbac presets</a> for information on creating and sharing your own preset files, and see the <a href="https://archive.moodle.net/fsbac">Presets repository</a> for presets that others have shared.';
$string['preset'] = 'Theme preset';
$string['preset_desc'] = 'Pick a preset to broadly change the look of the theme.';
$string['privacy:metadata'] = 'The Fsbac theme does not store any personal data about any user.';
$string['rawscss'] = 'Raw SCSS';
$string['rawscss_desc'] = 'Use this field to provide SCSS or CSS code which will be injected at the end of the style sheet.';
$string['rawscsspre'] = 'Raw initial SCSS';
$string['rawscsspre_desc'] = 'In this field you can provide initialising SCSS code, it will be injected before everything else. Most of the time you will use this setting to define variables.';
$string['region-side-pre'] = 'Right';
$string['showfooter'] = 'Show footer';
$string['unaddableblocks'] = 'Unneeded blocks';
$string['unaddableblocks_desc'] = 'The blocks specified are not needed when using this theme and will not be listed in the \'Add a block\' menu.';
$string['privacy:metadata:preference:draweropenblock'] = 'The user\'s preference for hiding or showing the drawer with blocks.';
$string['privacy:metadata:preference:draweropenindex'] = 'The user\'s preference for hiding or showing the drawer with course index.';
$string['privacy:metadata:preference:draweropennav'] = 'The user\'s preference for hiding or showing the drawer menu navigation.';
$string['privacy:drawerindexclosed'] = 'The current preference for the index drawer is closed.';
$string['privacy:drawerindexopen'] = 'The current preference for the index drawer is open.';
$string['privacy:drawerblockclosed'] = 'The current preference for the block drawer is closed.';
$string['privacy:drawerblockopen'] = 'The current preference for the block drawer is open.';

// Deprecated since Moodle 4.0.
$string['totop'] = 'Go to top';

// Deprecated since Moodle 4.1.
$string['currentinparentheses'] = '(current)';
$string['privacy:drawernavclosed'] = 'The current preference for the navigation drawer is closed.';
$string['privacy:drawernavopen'] = 'The current preference for the navigation drawer is open.';



#label Theme
$string['typecourse.live'] = 'Live';
$string['typecourse.video_ondemand'] = 'On demand';
$string['typecourse.corsi_multimediali'] = 'Corsi multimediali';
$string['typecourse.podcast'] = 'Podcast';

$string['level.base'] = 'Base';
$string['level.intermedio'] = 'Intermedio';
$string['level.avanzato'] = 'Avanzato';

/* OLD
$string['tag.accessibilita'] = 'Accessibilità';
$string['tag.accoglienza'] = 'Accoglienza';
$string['tag.agenda_2030'] = 'Agenda 2030';
$string['tag.anno_europeo_del_patrimonio_culturale'] = 'Anno Europeo del Patrimonio Culturale';
$string['tag.archeologia'] = 'Archeologia';
$string['tag.architettura'] = 'Architettura';
$string['tag.archivi_e_biblioteche'] = 'Archivi e biblioteche';
$string['tag.arte'] = 'Arte';
$string['tag.arti_visive'] = 'Arti visive';
$string['tag.aspetti_giuridici_e_organizzativi'] = 'Aspetti giuridici e organizzativi';
$string['tag.biblioteche_dautore'] = 'Biblioteche d\'autore';
$string['tag.cambiamento_climatico'] = 'Cambiamento climatico';
$string['tag.citta_come_cultura'] = 'Città come cultura';
$string['tag.collection_management'] = 'Collection management';
$string['tag.competitivita'] = 'Competitività';
$string['tag.comunicazione_e_promozione'] = 'Comunicazione e promozione';
$string['tag.conservazione_e_restauro'] = 'Conservazione e restauro';
$string['tag.conservazione_e_tutela'] = 'Conservazione e tutela';
$string['tag.costituzione_italiana'] = 'Costituzione Italiana';
$string['tag.covid'] = 'Covid';
$string['tag.depositi'] = 'Depositi';
$string['tag.digitale'] = 'Digitale';
$string['tag.diseguaglianze'] = 'Diseguaglianze';
$string['tag.educazione_e_mediazione'] = 'Educazione e mediazione';
$string['tag.efficientamento_energetico'] = 'Efficientamento energetico';
$string['tag.engagement'] = 'Engagement';
$string['tag.formazione'] = 'Formazione';
$string['tag.immateriale'] = 'Immateriale';
$string['tag.innovazione'] = 'Innovazione';
$string['tag.lubec2021'] = 'Lubec2021';
$string['tag.marketing'] = 'Marketing';
$string['tag.ministero'] = 'Ministero';
$string['tag.multimedia'] = 'Multimedia';
$string['tag.musei'] = 'Musei';
$string['tag.museo_egizio'] = 'Museo Egizio';
$string['tag.paesaggio_e_ambiente'] = 'Paesaggio e ambiente';
$string['tag.partecipazione'] = 'Partecipazione';
$string['tag.patrimonio_culturale'] = 'Patrimonio culturale';
$string['tag.pianificazione_e_strategia'] = 'Pianificazione e strategia';
$string['tag.pnrr'] = 'PNRR';
$string['tag.politiche_culturali'] = 'Politiche culturali';
$string['tag.politiche_di_genere'] = 'Politiche di genere';
$string['tag.politiche_europee'] = 'Politiche europee';
$string['tag.politiche_internazionali'] = 'Politiche internazionali';
$string['tag.pompei'] = 'Pompei';
$string['tag.produzione'] = 'Produzione';
$string['tag.professioni_e_competenze'] = 'Professioni e competenze';
$string['tag.pubblico_e_privato'] = 'Pubblico e Privato';
$string['tag.rapporti_con_il_territorio'] = 'Rapporti con il territorio';
$string['tag.ravello_lab_xvi'] = 'RAVELLO LAB XVI';
$string['tag.regolazione_normativa'] = 'Regolazione normativa';
$string['tag.resilienza'] = 'Resilienza';
$string['tag.ricerca'] = 'Ricerca';
$string['tag.rigenerazione'] = 'Rigenerazione';
$string['tag.sicurezza'] = 'Sicurezza';
$string['tag.sistema_museale_nazionale'] = 'Sistema Museale Nazionale';
$string['tag.social_media'] = 'Social Media';
$string['tag.sostenibilita'] = 'Sostenibilità';
$string['tag.statistiche_culturali'] = 'Statistiche culturali';
$string['tag.sussidiarieta'] = 'Sussidiarietà';
$string['tag.terremoto'] = 'Terremoto';
$string['tag.terzo_paradiso'] = 'Terzo Paradiso';
$string['tag.tutela'] = 'Tutela';
$string['tag.unesco'] = 'Unesco';
$string['tag.valorizzazione_e_sviluppo'] = 'Valorizzazione e sviluppo';
$string['tag.valutazione_di_impatto'] = 'Valutazione di impatto';
$string['tag.welfare_culturale'] = 'Welfare culturale';
$string['tag.memoria'] = 'Memoria';
$string['tag.itinerari_culturali_del_consiglio_deuropa'] = 'Itinerari culturali del Consiglio d\'Europa';
$string['tag.fundraising'] = 'Fundraising';
$string['tag.audience_development'] = 'Audience development';
$string['tag.narrazione_partecipata'] = 'Narrazione partecipata';
$string['tag.heritage_awarness'] = 'Heritage Awarness';
$string['tag.strumenti_digitali'] = 'Strumenti digitali';
$string['tag.digit'] = 'Digit';
$string['tag.sviluppo_del_territorio'] = 'Sviluppo del territorio';
$string['tag.europa'] = 'Europa';
$string['tag.europrogettazione'] = 'Europrogettazione';
$string['tag.capitale_sociale'] = 'Capitale sociale';
$string['tag.international_school'] = 'International school';
$string['tag.archaeology'] = 'Archaeology';
$string['tag.technologies'] = 'Technologies';
$string['tag.digi'] = 'digi';
$string['tag.management'] = 'Management';
$string['tag.emergenza'] = 'emergenza';
$string['tag.catalogazione'] = 'catalogazione';
$string['tag.iccd'] = 'ICCD';
$string['tag.governance'] = 'Governance';
$string['tag.patri'] = 'patri';
$string['tag.capitale_italiana_della_cultura'] = 'Capitale italiana della cultura';
$string['tag.inclusività'] = 'inclusività';
$string['tag.aggiornamento'] = 'Aggiornamento';
$string['tag.industrie_culturali_e_creative'] = 'Industrie culturali e creative';
$string['tag.patrimonio_immateriale'] = 'Patrimonio immateriale';
$string['tag.progettazione_culturale'] = 'Progettazione culturale';
$string['tag.ricerca_e_innovazione'] = 'Ricerca e innovazione';
$string['tag.cura_e_gestione_delle_collezioni'] = 'Cura e gestione delle collezioni';
$string['tag.agg'] = 'agg';
$string['tag.digitalizzazione_del_patrimonio'] = 'Digitalizzazione del patrimonio';
$string['tag.valorizzazione_territoriale_e_sviluppo_locale'] = 'Valorizzazione territoriale e sviluppo locale';
$string['tag.aud'] = 'aud';
$string['tag.valo'] = 'valo';
$string['tag.approfondimento'] = 'Approfondimento';
$string['tag.comunicazione'] = 'Comunicazione';
$string['tag.promozione_e_storytelling'] = 'promozione e storytelling';
$string['tag.promozione'] = 'promozione';
$string['tag.storytelling'] = 'storytelling';
$string['tag.fruizione'] = 'Fruizione';
$string['tag.fruizione_accoglienza_e_vigilanza'] = 'Fruizione accoglienza e vigilanza';
$string['tag.audience_engagement_partecipazione'] = 'Audience,engagement,partecipazione';
$string['tag.tutela_conservazione_e_restauro'] = 'Tutela conservazione e restauro';
$string['tag.accessibilità_inclusione_e_welfare_culturale'] = 'Accessibilità inclusione e welfare culturale';
$string['tag.design_dei_servizi'] = 'Design dei servizi';
$string['tag.rigenerazione_urbana_e_sostenibilita'] = 'Rigenerazione urbana e sostenibilità';
$string['tag.architettura_urbanistica_e_paesaggio'] = 'Architettura urbanistica e paesaggio';
$string['tag.archivi'] = 'Archivi';
$string['tag.biblioteche'] = 'Biblioteche';
$string['tag.patrimonio_culturale_digitale'] = 'Patrimonio culturale digitale';
$string['tag.comunicazione_promozione_e_storytelling'] = 'Comunicazione promozione e storytelling';
$string['tag.data_management'] = 'Data management';
$string['tag.com'] = 'com';
$string['tag.management_e_organizzazione'] = 'Management e organizzazione';
$string['tag.policy_making'] = 'Policy making';
$string['tag.marketing_e_fundraising'] = 'Marketing e fundraising';
$string['tag.trasformazione_digitale'] = 'Trasformazione digitale';
$string['tag.pol'] = 'pol';
$string['tag.dicolab'] = 'Dicolab';
$string['tag.pubb'] = 'pubb';
$string['tag.pensiero_digitale'] = 'Pensiero digitale';
$string['tag.produzione_e_gestione'] = 'Produzione e gestione';
$string['tag.uso_e_condivisione'] = 'Uso e condivisione';
$string['tag.governance_della_trasformazione_digitale'] = 'Governance della trasformazione digitale';
$string['tag.processi_di_supporto'] = 'Processi di supporto';
*/
$string['tag.accessibilita'] = 'Accessibilità';
$string['tag.accoglienza'] = 'Accoglienza';
$string['tag.agenda_2030'] = 'Agenda 2030';
$string['tag.anno_europeo_del_patrimonio_culturale'] = 'Anno Europeo del Patrimonio Culturale';
$string['tag.archeologia'] = 'Archeologia';
$string['tag.architettura'] = 'Architettura';
$string['tag.archivi_e_biblioteche'] = 'Archivi e biblioteche';
$string['tag.arte'] = 'Arte';
$string['tag.arti_visive'] = 'Arti visive';
$string['tag.aspetti_giuridici_e_organizzativi'] = 'Aspetti giuridici e organizzativi';
$string['tag.biblioteche_dautore'] = 'Biblioteche d\'autore';
$string['tag.cambiamento_climatico'] = 'Cambiamento climatico';
$string['tag.citta_come_cultura'] = 'Città come cultura';
$string['tag.collection_management'] = 'Collection management';
$string['tag.competitivita'] = 'Competitività';
$string['tag.comunicazione_e_promozione'] = 'Comunicazione e promozione';
$string['tag.conservazione_e_restauro'] = 'Conservazione e restauro';
$string['tag.conservazione_e_tutela'] = 'Conservazione e tutela';
$string['tag.costituzione_italiana'] = 'Costituzione Italiana';
$string['tag.covid'] = 'Covid';
$string['tag.depositi'] = 'Depositi';
$string['tag.digitale'] = 'Digitale';
$string['tag.diseguaglianze'] = 'Diseguaglianze';
$string['tag.educazione_e_mediazione'] = 'Educazione e mediazione';
$string['tag.efficientamento_energetico'] = 'Efficientamento energetico';
$string['tag.engagement'] = 'Engagement';
$string['tag.formazione'] = 'Formazione';
$string['tag.immateriale'] = 'Immateriale';
$string['tag.innovazione'] = 'Innovazione';
$string['tag.lubec2021'] = 'Lubec2021';
$string['tag.marketing'] = 'Marketing';
$string['tag.ministero'] = 'Ministero';
$string['tag.multimedia'] = 'Multimedia';
$string['tag.musei'] = 'Musei';
$string['tag.museo_egizio'] = 'Museo Egizio';
$string['tag.paesaggio_e_ambiente'] = 'Paesaggio e ambiente';
$string['tag.partecipazione'] = 'Partecipazione';
$string['tag.patrimonio_culturale'] = 'Patrimonio culturale';
$string['tag.pianificazione_e_strategia'] = 'Pianificazione e strategia';
$string['tag.strategia_e_pianificazione'] = 'Strategia e pianificazione';
$string['tag.pnrr'] = 'PNRR';
$string['tag.politiche_culturali'] = 'Politiche culturali';
$string['tag.politiche_di_genere'] = 'Politiche di genere';
$string['tag.politiche_europee'] = 'Politiche europee';
$string['tag.politiche_internazionali'] = 'Politiche internazionali';
$string['tag.pompei'] = 'Pompei';
$string['tag.produzione'] = 'Produzione';
$string['tag.professioni_e_competenze'] = 'Professioni e competenze';
$string['tag.pubblico_e_privato'] = 'Pubblico e Privato';
$string['tag.rapporti_con_il_territorio'] = 'Rapporti con il territorio';
$string['tag.ravello_lab_xvi'] = 'RAVELLO LAB XVI';
$string['tag.regolazione_normativa'] = 'Regolazione normativa';
$string['tag.resilienza'] = 'Resilienza';
$string['tag.ricerca'] = 'Ricerca';
$string['tag.rigenerazione'] = 'Rigenerazione';
$string['tag.sicurezza'] = 'Sicurezza';
$string['tag.sistema_museale_nazionale'] = 'Sistema Museale Nazionale';
$string['tag.social_media'] = 'Social Media';
$string['tag.sostenibilita'] = 'Sostenibilità';
$string['tag.statistiche_culturali'] = 'Statistiche culturali';
$string['tag.sussidiarieta'] = 'Sussidiarietà';
$string['tag.terremoto'] = 'Terremoto';
$string['tag.terzo_paradiso'] = 'Terzo Paradiso';
$string['tag.tutela'] = 'Tutela';
$string['tag.unesco'] = 'Unesco';
$string['tag.valorizzazione_e_sviluppo'] = 'Valorizzazione e sviluppo';
$string['tag.valutazione_di_impatto'] = 'Valutazione di impatto';
$string['tag.welfare_culturale'] = 'Welfare culturale';
$string['tag.memoria'] = 'Memoria';
$string['tag.itinerari_culturali_del_consiglio_deuropa'] = 'Itinerari culturali del Consiglio d\'Europa';
$string['tag.fundraising'] = 'Fundraising';
$string['tag.audience_development'] = 'Audience development';
$string['tag.narrazione_partecipata'] = 'Narrazione partecipata';
$string['tag.heritage_awarness'] = 'Heritage Awarness';
$string['tag.strumenti_digitali'] = 'Strumenti digitali';
$string['tag.digit'] = 'Digit';
$string['tag.sviluppo_del_territorio'] = 'Sviluppo del territorio';
$string['tag.europa'] = 'Europa';
$string['tag.europrogettazione'] = 'Europrogettazione';
$string['tag.capitale_sociale'] = 'Capitale sociale';
$string['tag.international_school'] = 'International school';
$string['tag.archaeology'] = 'Archaeology';
$string['tag.technologies'] = 'Technologies';
$string['tag.digi'] = 'digi';
$string['tag.management'] = 'Management';
$string['tag.emergenza'] = 'Emergenza';
$string['tag.catalogazione'] = 'Eatalogazione';
$string['tag.iccd'] = 'ICCD';
$string['tag.governance'] = 'Governance';
$string['tag.patri'] = 'patri';
$string['tag.capitale_italiana_della_cultura'] = 'Capitale italiana della cultura';
$string['tag.inclusivita'] = 'Inclusività';
$string['tag.audience_engagement_partecipazione'] = 'Audience, engagement, partecipazione';
$string['tag.aggiornamento'] = 'Aggiornamento';
$string['tag.industrie_culturali_e_creative'] = 'Industrie culturali e creative';
$string['tag.patrimonio_immateriale'] = 'Patrimonio immateriale';
$string['tag.progettazione_culturale'] = 'Progettazione culturale';
$string['tag.ricerca_e_innovazione'] = 'Ricerca e innovazione';
$string['tag.cura_e_gestione_delle_collezioni'] = 'Cura e gestione delle collezioni';
$string['tag.agg'] = 'agg';
$string['tag.digitalizzazione_del_patrimonio'] = 'Digitalizzazione del patrimonio';
$string['tag.valorizzazione_territoriale_e_sviluppo_locale'] = 'Valorizzazione territoriale e sviluppo locale';
$string['tag.aud'] = 'aud';
$string['tag.valo'] = 'valo';
$string['tag.approfondimento'] = 'Approfondimento';
$string['tag.comunicazione'] = 'Comunicazione';
$string['tag.promozione_e_storytelling'] = 'Promozione e storytelling';
$string['tag.promozione'] = 'Promozione';
$string['tag.storytelling'] = 'Storytelling';
$string['tag.fruizione'] = 'Fruizione';
$string['tag.fruizione_accoglienza_e_vigilanza'] = 'Fruizione accoglienza e vigilanza';
$string['tag.tutela_conservazione_e_restauro'] = 'Tutela conservazione e restauro';
$string['tag.inclusione_e_welfare_culturale'] = 'Inclusione e welfare culturale';
$string['tag.design_dei_servizi'] = 'Design dei servizi';
$string['tag.rigenerazione_urbana_e_sostenibilita'] = 'Rigenerazione urbana e sostenibilità';
$string['tag.architettura_urbanistica_e_paesaggio'] = 'Architettura urbanistica e paesaggio';
$string['tag.archivi'] = 'Archivi';
$string['tag.biblioteche'] = 'Biblioteche';
$string['tag.patrimonio_culturale_digitale'] = 'Patrimonio culturale digitale';
$string['tag.comunicazione_promozione_e_storytelling'] = 'Comunicazione promozione e storytelling';
$string['tag.data_management'] = 'Data management';
$string['tag.com'] = 'com';
$string['tag.management_e_organizzazione'] = 'Management e organizzazione';
$string['tag.policy_making'] = 'Policy making';
$string['tag.marketing_e_fundraising'] = 'Marketing e fundraising';
$string['tag.trasformazione_digitale'] = 'Trasformazione digitale';
$string['tag.pol'] = 'pol';
$string['tag.dicolab'] = 'Dicolab';
$string['tag.pubb'] = 'pubb';
$string['tag.pensiero_digitale'] = 'Pensiero digitale';
$string['tag.produzione_e_gestione'] = 'Produzione e gestione';
$string['tag.uso_e_condivisione'] = 'Uso e condivisione';
$string['tag.governance_della_trasformazione_digitale'] = 'Governance della trasformazione digitale';
$string['tag.processi_di_supporto'] = 'Processi di supporto';
$string['tag.live'] = 'Live';
$string['tag.on_demand'] = 'On demand';
$string['tag.corso_multimediale'] = 'Corso multimediale';
$string['tag.podcast'] = 'Podcast';


$string['common.corses'] = 'Corsi';
$string['common.registered_users'] = 'Utenti iscritti';
$string['common.coursecard_link'] = 'Scopri di più';
$string['common.coursecard_of'] = 'di';
$string['home.banner_top_header'] = 'Fondazione Scuola dei Beni e delle Attività Culturali';
$string['home.banner_top_button_enrol'] = 'Registrati';
$string['home.banner_top_button_catalog'] = 'Sfoglia il catalogo';
$string['home.your_interests_section'] = 'I tuoi interessi';
$string['home.your_interests_section_text'] = 'Corsi e percorsi, multidisciplinari o specialistici, sull\' ampio ventaglio di tematiche di tuo interesse.<br>Scegli e filtra il catalogo.';
$string['home.methods_training_section'] = 'La tua esperienza formativa';
$string['home.methods_training_section_text'] = 'Tecnologia e innovazione per metterti sempre al centro dell\'esperienza formativa:  live e on demand, webinar,  lezioni, laboratori, corsi multimediali, podcast, video-lezioni, video-pillole, MOOC, tutorial, aule virtuali, forum, questionari.<br>Scegli e filtra il catalogo.';
$string['home.methods_training_box1'] = 'Corsi multimediali';
$string['home.methods_training_box2'] = 'Podcast';
$string['home.methods_training_box3'] = 'Corsi on demand';
$string['home.methods_training_box4'] = 'Live';
$string['home.percorsi_360_section'] = 'Percorsi a 360°';
$string['home.percorsi_360_section_text'] = 'Abbiamo costruito per te percorsi e itinerari tra i diversi corsi per meglio accompagnarti in una  formazione completa sui temi che più ti stanno a cuore.';
$string['home.percorsi_360_section_button'] = 'Esplora i percorsi';
$string['home.banner_middle_section'] = 'Entra nella community dei professionisti della cultura';
$string['home.banner_middle_section_text'] = 'Uno strumento versatile e intuitivo per condividere conoscenze, visioni e prospettive sulla cura e gestione del patrimonio culturale.';
$string['home.banner_middle_section_enrol'] = "Registrati";
$string['home.banner_middle_section_catalog'] = "Sfoglia il catalogo";
$string['home.featured_courses_section'] = 'In evidenza';
$string['footer.label_logo'] = 'fad.fondazionescuolapatrimonio.it<br>la formazione per il patrimonio culturale a portata di clic';
$string['footer.label_bottom'] = 'Fondazione Scuola dei beni e delle attività culturali |Sede legale: via del Collegio Romano, 27 - 00186 Roma | C.F. 97900380581';

$string['setting:backgroundimage_banner_publichome'] = "Immagine Banner";
$string['setting:backgroundimage_banner_publichome_desc'] = "Home page pubblica";
$string['setting:title_banner_publichome'] = "Titolo home page pubblica";
$string['setting:title_banner_publichome_desc'] = "";
$string['setting:subtitle_banner_publichome'] = "Sottotitolo home page pubblica";
$string['setting:subtitle_banner_publichome_desc'] = "";
$string['setting:methods_training'] = "Banner 'Le modalità di formazione' Immagine";
$string['setting:methods_training_desc'] = "";
$string['setting:banner_blue_image'] = "Banner 'Il punto di riferimento per i professionisti della cultura' Immagine";
$string['setting:banner_blue_image_desc'] = "";
$string['setting:interests'] = "Interessi";
$string['setting:interests_desc'] = "Inserisisci le chiavi separate da virgola, es: politica,sport";

$string['course.title'] = 'In breve';
$string['course.program'] = 'Il programma';
$string['course.button_enrol'] = 'Iscriviti';
$string['course.button_unenrol'] = 'Disiscriviti';
$string['course.genaral_information'] = 'Informazioni generali';
$string['course.typology'] = 'Tipologia';
$string['course.category'] = 'Programma';
$string['course.tag'] = 'Tag';
$string['course.author'] = 'Chi';
$string['course.duration'] = 'Durata complessiva';
$string['course.button_shared'] = 'Condividi corso';
$string['course.banner_title'] = 'Esplora anche i corsi';
$string['course.code'] = 'Codice corso';
$string['course.button_course'] = 'Sfoglia il catalogo';
$string['course.banner_desc'] = '<p>Ti poponiamo strumenti formativi, curati da accademici, formatori, esperti e operatori, su tematiche puntuali per accrescere le tue conoscenze e competenze per la cura e gestione del patrimonio culturale. </p>';
$string['course.button_path'] = 'Scopri anche i percorsi';
$string['course.certificate'] = 'Attestato';
$string['course.certificate_desc'] = 'Al completamento di tutte le attività che compongono il corso potrai scaricare l\'attestato di partecipazione che certifica le tue nove competenze acquisite!';
$string['course.certificate_download'] = 'Scarica l\'attestato';
$string['course.isNew'] = 'New!';
$string['course.infocerticate'] = 'Ottieni un attestato';
$string['course.infocerticate_desc'] = 'Completa il corso per ottenere l\'attestato di partecipazione.';
$string['course.linkpathnum'] = '{$a} Percorsi collegati';
$string['course.linkpathnum_desc'] = 'Questo corso fa parte di {$a} percorsi:';


$string['path.title'] = 'Il programma in breve';
$string['path.info_interest'] = 'Può interessare a';
$string['path.info_level'] = 'Livello';
$string['path.info_tag'] = 'Tag';
$string['path.info_duration'] = 'Durata';
$string['path.genaral_information'] = 'Informazioni';
$string['path.button_enrol'] = 'Iscriviti';
$string['path.button_shared'] = 'Condividi percorso';
$string['path.program'] = 'Programma';

$string['path.sections_courses_title'] = 'I corsi in programma: {$a}';
$string['path.ctaCourse'] = 'Inizia il corso';
$string['path.readmorecourse'] = 'Approfondisci';
$string['path.banner_title'] = 'Eplora anche i percorsi';
$string['path.banner_desc'] = 'Abbiamo costruito per te percorsi e itinerari tra i diversi corsi per meglio accompagnarti in una formazione completa sui temi che più ti stanno a cuore.<br><br>Focus tematici, affondi specialistici, punti di vista, prospettive e scenari.';
$string['path.button_path'] = 'Esplora i percorsi';
$string['path.box1'] = '20 percorsi';
$string['path.box2'] = '10 categorie';
$string['path.box3'] = '3 Livelli';

$string['courses.title'] = 'Catalogo corsi';
$string['courses.header_info'] = 'Sfoglia il nostro catalogo';
$string['courses.header_title'] = 'Chi cerca, trova. <br>Live e on-demand,<br> informativo o specialistico, trova il corso per te';
$string['courses.title_filters'] = 'Cerca tra i corsi';
$string['search'] = 'Cerca';
$string['filter.order'] = 'Ordina per';
$string['filter.show_button'] = 'Mostra filtri';
$string['filter.tag'] = 'Tag di argomento';
$string['filter.program'] = 'Programma';
$string['filter.author'] = 'Autore';
$string['filter.typeCourse'] = 'Tipologia corso';
$string['filter.expiration'] = 'Scadenza';
$string['filter.destination'] = 'Destinatorio';
$string['filter.path'] = 'Percorso';
$string['filter.courseDuration'] = 'Durato corso';
$string['filter.order.date_start'] = 'Data inizio';
$string['filter.order.a_z'] = 'A-Z';
$string['filter.order.z_a'] = 'Z-A';
$string['filter.time.senza_scadenza'] = 'Senza scadenza';
$string['filter.time.entro_7_giorni'] = 'Entro 7 giorni';
$string['filter.time.entro_15_giorni'] = 'Entro 15 giorni';
$string['filter.time.entro_1_mese'] = 'Entro 1 mese';
$string['filter.time.entro_2_mesi'] = 'Entro 2 mesi';
$string['filter.time.entro_6_mesi'] = 'Entro 6 mesi';
$string['filter.time.dopo_6_mesi'] = 'Dopo 6 mesi';
$string['filter.zero_results'] = '0 Risultati';
$string['filter.zero_results_msg'] = 'Siamo spiacenti ma la ricerca non ha prodotto risultati.';
$string['filter.level'] = 'Livello';
$string['filter.duration_course'] = 'Durata corso';
$string['filter.duration_path'] = 'Durata percorso';


$string['paths.title'] = 'Catalogo percorsi';
$string['paths.header_info'] = 'Esplora i percorsi';
$string['paths.header_title'] = 'Di tappa in tappa.<br> Segui i nostri percorsi per raggiungere i tuoi traguardi formativi';
$string['paths.title_filters'] = 'Cerca tra i percorsi';

$string['chisiamo.title'] = 'Chi siamo';
$string['chisiamo.header_info'] = 'la Fondazione Scuola dei Beni e delle attivita Culturali';
$string['chisiamo.header_title'] = 'Istituto internazionale <br> di formazione e ricerca <br> fondato dal Ministero della cultura';
$string['chisiamo.todo'] = 'Cosa facciamo';
$string['chisiamo.internazionale'] = 'Internazionale';
$string['chisiamo.internazionale_text'] = 'Attraverso lo scambio di esperienze e il confronto tra eccellenze, su scala internazionale, rafforziamo le competenze dei professionisti italiani e stranieri e favoriamo  l\'internazionalizzazione delle istituzioni culturali italiane.<br>Con la partecipazione ad azioni di rete e a programmi di ricerca, contribuiamo ai più importanti dibattiti del panorama mondiale e sosteniamo l\'innovazione dei modelli di gestione culturale.';
$string['chisiamo.patrimonio'] = 'Il nostro patrimonio di conoscenza a portata di clic';
$string['chisiamo.patrimonio_text'] = 'Come centro di competenze sulla cura e gestione del patrimonio culturale, mettiamo in rete organizzazioni internazionali e istituzioni nazionali, pubbliche e private, statali e locali, luoghi della cultura con accademici, formatori, esperti, studiosi e operatori.<br>Il nostro patrimonio di conoscenze, su fad.fondazionescuolapatrimonio.it, a portata di clic. ';
$string['chisiamo.formazione'] = 'Formazione';
$string['chisiamo.formazione_text'] = 'Alta formazione, formazione continua, aggiornamento professionale.Ai professionisti del patrimonio culturale, del settore pubblico e privato, rivolgiamo un\'offerta formativa personalizzata, aggiornata ai tempi, multidisciplinare e trasversale, che integra didattica ed esperienza.';
$string['chisiamo.ricerca'] = 'Ricerca';
$string['chisiamo.ricerca_text'] = 'Sui temi più attuali e dibattuti della cura e gestione del patrimonio culturale, produciamo studi originali di carattere applicativo: strumenti per accompagnare gli operatori culturali nella lettura delle trasformazioni del settore, dati e raccomandazioni per sostenere l\'azione di decisori politici e amministatori.';
$string['chisiamo.innovazione'] = 'Innovazione';
$string['chisiamo.innovazione_text'] = 'Supportiamo istituzioni pubbliche e private nella definizione e nella attuazione di politiche culturali, innovative e sostenibili. Con laboratori e osservatori, azioni di monitoraggio e di valutazione immaginiamo e sperimentiamo metodi e modelli di intervento che condividiamo con l\'intero settore. ';
$string['chisiamo.banner_formazione_title'] = 'La formazione secondo noi';
$string['chisiamo.banner_formazione_txt'] = 'I professionisti del patrimonio culturale sono chiamati a nuove sfide e a grandi responsabilità. La nostra risposta sta in una formazione costruita su misura, aggiornata ai tempi e orientata alla dimensione internazionale.<br><br>[NOME FAD o url] dà accesso a una ricca offerta formativa, gratuita, su temi generali e su argomenti specialistici, tra corsi e percorsi,  fruibili sia live che on demand:  webinar, lezioni, laboratori, corsi multimediali, podcast,  video-lezioni, video-pillole, MOOC, tutorial, aule virtuali, forum, questionari. <br><br>Nuove conoscenze e competenze aggiornate per la cura e la gestione del patrimonio culturale.';
$string['chisiamo.corsi_multimediali'] = 'Corsi multimediali';
$string['chisiamo.corsi_multimediali_txt'] = 'Strumenti formativi che combinano diverse risorse: testuali, audiovisive e grafiche.';
$string['chisiamo.podcast'] = 'Podcast';
$string['chisiamo.podcast_txt'] = 'Contenuti audio per scoprire novità nel mondo della cultura, da ascoltare quando e dove vuoi.';
$string['chisiamo.ondemand'] = 'On demand';
$string['chisiamo.ondemand_txt'] = 'Contenuti formativi disponibili su richiesta in qualsiasi momento.';
$string['chisiamo.live'] = 'Live';
$string['chisiamo.live_txt'] = 'Appuntamenti formativi disponibili in diretta streaming.';
$string['chisiamo.box_title'] = 'Scuola dei beni e delle attività culturali';
$string['chisiamo.box_txt'] = 'Nasce con la missione di valorizzare e promuovere le competenze dei professionisti impegnati nella cura e gestione del patrimonio e delle attività culturali. Osservatorio sulle trasformazioni del sistema cultura, nazionale e internazionale, attraverso attività di formazione, ricerca, innovazione, comunicazione, crea nel settore occasioni di integrazione fra le discipline, confronto tra gli operatori, connessione tra i soggetti.';
$string['chisiamo.box1'] = 'Integrazione';
$string['chisiamo.box2'] = 'Confronto';
$string['chisiamo.box3'] = 'Connessione';

$string['footer.menu'] = 'Menu';
$string['footer.menu.home'] = 'Home';
$string['footer.menu.corsi'] = 'Corsi';
$string['footer.menu.percorsi'] = 'Percorsi';
$string['footer.menu.dicolab'] = 'Dicolab';
$string['footer.menu.chisiamo'] = 'Chi siamo';

$string['footer.help'] = 'Help';
$string['footer.help.faq'] = 'FAQ';
$string['footer.help.assistenza'] = 'Assistenza';
$string['footer.help.privacy'] = 'Privacy Policy';
$string['footer.help.termini'] = 'Termini e Condizioni';
$string['footer.help.cookiepolicy'] = 'Cookie Policy';
$string['footer.help.accessibility']  =  'Dichiarazione di accessibilità';

$string['footer.location'] = 'Fondazione Scuola dei beni e delle attività culturali';
$string['footer.location.name'] = 'Biblioteca Nazionale Centrale di Roma';
$string['footer.location.street'] = 'Viale Castro Pretorio, 105 | 00185 Roma';

$string['setting:facebooklink'] = "Facebook Link";
$string['setting:facebooklink_desc'] = "";
$string['setting:linkedinlink'] = "Linkedin Link";
$string['setting:linkedinlink_desc'] = "";
$string['setting:twitterlink'] = "Twitter Link";
$string['setting:twitterlink_desc'] = "";
$string['setting:instagramlink'] = "Instagram Link";
$string['setting:instagramlink_desc'] = "";

$string['courses.header_title_logged'] = 'Tutti i corsi';
$string['courses.header_subtitle_logged'] = 'Naviga tra tutti i corsi disponibili a catalogo e iscriviti!';
$string['paths.header_title_logged'] = 'Tutti i percorsi';
$string['paths.header_subtitle_logged'] = 'Naviga tra tutti i percorsi che abbiamo costruito per accompagnarti a una formazione completa sui temi che più ti stanno a cuore.';

$string['login.welcome'] = 'Accedi al tuo account';
$string['login.email'] = 'Indirizzo email';
$string['login.email_placeholder'] = 'es.mariorossi@gmail.com';

$string['login.title'] = 'È un piacere rivederti!';
$string['login.signup_msg_link'] = 'Non hai ancora un account?';
$string['login.signup_link'] = 'Registrati';
$string['login.footer'] = 'Fondazione Scuola dei Beni e delle Attività Culturali | Via del Collegio Romano, 27 - 00186 Roma | C.F. 97900380581';
$string['login.signup'] = 'Registrati';
$string['login.signup_msg'] = 'Crea il tuo account';
$string['login.signup_cf_text'] = 'Lo chiediamo per assicurarci che ci siano account univoci e per offrirti un\'esperienza unica.';
$string['login.signup_cf_title'] = 'Perché il codice fiscale?';
$string['login.signup_cf_error_title'] = 'Cosa fare adesso?';
$string['login.signup_cf_error_text'] = 'Il codice fiscale inserito è associato ad un account già registrato. Ecco cosa puoi fare:';
$string['login.signup_login'] = 'Accedi';
$string['login.signup_pw'] = 'Recupera password';
$string['login.signup_msg_account'] = 'Hai già un account?';
$string['login.forgotpassword'] = 'Password dimenticata?';
$string['login.signup-data_msg'] = 'Completa la registrazione';
$string['login.signup-data'] = 'Inserisci i tuoi dati';
$string['login.signup-data_button'] = 'Completa la registrazione';
$string['login.forgot_password_email_msg'] = 'Hai dimenticato la tua password?';
$string['login.forgot_password_email'] = 'Recupera la password';
$string['login.forgot_password_email_info'] = 'Inserisci la tua mail di registrazione per ricevere le istruzioni di recupero password';
$string['login.forgot_password_email_button'] = 'Richiedi istruzioni via email';
$string['login.reset_password_msg'] = 'Nuova password';
$string['login.reset_password'] = 'Scegli la password';
$string['login.reset_password_info'] = 'Inserisci una nuova password per il tuo account';
$string['login.reset_password_button'] = 'Salva';
$string['login.exit'] = 'Esci';
$string['confirm.welcome'] = 'Ti diamo il benvenuto';
$string['confirm.welcome_title'] = '{$a} è un piacere vederti qui.';
$string['confirm.welcome_info'] = 'Hai completato la creazione del tuo account e sei registrato alla piattaforma di formazione a distanza della Fondazione Scuola dei beni e delle attività culturali. Grazie del tuo interesse.<br><br>Per offrirti un\'esperienza personalizzata e  rispondente alle tue esigenze formative, vorremmo conoscerti meglio. Dedicaci ancora qualche minuto.';
$string['confirm.welcome_button'] = 'Conosciamoci meglio';
$string['showmore_button'] = 'Mostra tutti';
$string['path.buttonback'] = 'Esplora tutti i percorsi';
$string['course.buttonback'] = 'Esplora tutti i corsi';
$string['filter.label_course_show'] = 'Mostra corsi';
$string['filter.onlycerticate'] = 'Solo con attestato';

$string['accettapolicy'] = 'Per iscriverti accetta la policy {$a}';

$string['home_user.header_title_f'] = 'Ciao {$a}, bentornata!';
$string['home_user.header_title'] = 'Ciao {$a}, bentornato!';
$string['home_user.header_subtitle'] = 'Lasciati ispirare...<br>Scopri i corsi che ti abbiamo riservato, in linea con i tuoi intessessi e gli obiettivi di formazione.';
$string['home_user.header_button_catalog'] = 'Sfoglia il catalogo';
$string['home_user.header_button_personal_area'] = 'Area personale';

$string['home_user.section_course_like'] = 'Corsi che potrebbero piacerti';
$string['home_user.section_course_suggeriti'] = 'I nostri suggerimenti per te';

$string['home_user.section_course_last_minute'] = 'Last minute: i corsi in scadenza';
$string['home_user.section_course_tendenza'] = 'Di tendenza: i corsi più seguiti';
$string['home_user.section_course_new'] = 'Nuove proposte: i corsi recenti';
$string['home_user.section_course_new_tab1'] = 'Nuovi arrivi ({$a})';
$string['home_user.section_course_new_tab2'] = 'In arrivo ({$a})';
$string['card.progress'] = 'Completato al {$a} %';


$string['setting:dayshomequery'] = "Giorni corsi home";
$string['setting:dayshomequery_desc'] = "";
$string['continuaanavigare'] = "Continua a navigare";

$string['assistence.info'] = "Sei nel posto giusto. <br>Scegli tra le opzioni presenti in questa pagina quella che preferisci, per trovare le risposte a dubbi e domande sulla tua esperienza in piattaforma o per ricevere  assistenza. <br>Siamo qui per aiutarti al meglio.";
$string['assistence.guide_title'] = "Leggi la guida";
$string['assistence.guide_info'] = "Abbiamo preparato una guida alla piattaforma per spiegare al meglio tutte le funzioni e le modalità di utilizzo. ";
$string['assistence.guide_button'] = "Scarica la guida";

$string['assistence.faq_title'] = "Domande frequenti - FAQ";
$string['assistence.faq_info'] = "Abbiamo raccolto le riposte alle domande più frequenti per fornirti assistenza in qualsiasi momento. ";
$string['assistence.faq_button'] = "Vai alle FAQ";

$string['assistence.chat_title'] = "Richiedi assistenza";
$string['assistence.chat_info'] = "Hai altri dubbi? Compila il form di assistenza, ti risponderemo appena possibile. Lo specialista helpdesk risponderà ad ogni domanda negli orari d'ufficio tra le 09.00 e le 17.00 dal lunedì al venerdì.";
$string['assistence.chat_button'] = "Compila il form";



$string['dicolabcoursesforyou'] = 'Dicolab per te';

$string['profile.tuoi_dati'] = 'I tuoi dati';
$string['profile.change_password'] = 'Cambia password';
$string['profile.notification_settings'] = 'Impostazione notifiche';
$string['profile.politiche_consensi'] = 'Gestione policy';
$string['profile.elimina_account'] = 'Elimina account';



$string['assistence.title'] = "Hai bisogno di assistenza ?";

$string['assistence.access_to_fad_platform_question'] = "Come posso accedere alla piattaforma FAD della Fondazione?";
$string['assistence.access_to_fad_platform_answer'] = 'Per accedere alla piattaforma FAD della Fondazione è sufficiente creare un account personale. Seleziona la voce <a href="{$a}">"Crea un account"</a> sulla pagina di ingresso della piattaforma e segui la procedura guidata. Una volta creato l\'account, per accedere in piattaforma utilizza la tua email come username e la password personalizzata.
Ti ricordiamo che l\'accesso alla piattaforma, così come a tutti i suoi contenuti, è totalmente libero e gratuito.';
$string['assistence.cant_remember_username_question'] = "Non ricordo la username del mio account";
$string['assistence.cant_remember_username_answer'] = 'Sulla nostra FAD lo username equivale al proprio indirizzo email.';
$string['assistence.change_username_question'] = "Vorrei modificare lo USERNAME di accesso al mio account, è possibile?";
$string['assistence.change_username_answer'] = 'Non è possibile modificare autonomamente lo username associato al proprio account.';
$string['assistence.cant_remember_password_question'] = "Non ricordo la mia password/vorrei modificare la mia password";
$string['assistence.cant_remember_password_answer'] = 'Per recuperare o modificare la propria password, seleziona la voce <a href="{$a}">"Hai dimenticato lo username o la password?"</a> presente sulla pagina di ingresso della piattaforma e segui la procedura guidata. Dove richiesto, ricordati di inserire l\'indirizzo email associato al tuo account.
Qualora dopo pochi istanti non ricevessi l\'email di recupero password/username, controlla nella cartella SPAM della tua casella di posta elettronica o, in alternativa, ripeti la procedura assicurandoti di inserire correttamente l\'indirizzo email associato al tuo account.';
$string['assistence.change_profile_info_question'] = "Vorrei modificare o arricchire le informazioni del mio profilo, come si fa?";
$string['assistence.change_profile_info'] = "Vorrei modificare o arricchire le informazioni del mio profilo, come si fa?";
$string['assistence.change_profile_info_answer'] = "Per modificare o arricchire le informazioni del tuo profilo, seleziona all'interno della piattaforma la voce “modifica” presente sotto al tuo nome. Inserisci o modifica i dati del tuo profilo, quindi clicca sul pulsante “Salva modifiche”.
È importante compilare e mantenere aggiornate le diverse voci che compongono il tuo profilo per poter ricevere le giuste comunicazioni ed i migliori suggerimenti sui contenuti della piattaforma.";
$string['assistence.enrol_platform_course_question'] = "Come faccio ad iscrivermi ai corsi presenti sulla piattaforma?";
$string['assistence.enrol_platform_course_answer'] = "I corsi ospitati in piattaforma possono essere ad ACCESSO LIBERO o ad ACCESSO RISERVATO.
CORSI AD ACCESSO LIBERO. I corsi ad accesso libero sono disponibili nella sezione “CATALOGO”. Per iscriverti ad un corso sfoglia il “CATALOGO”, clicca sulla copertina del corso di tuo interesse, quindi clicca sui pulsanti “Scopri di più” e “Vedi dettaglio” che ti appariranno in seguenza. Da questo momento troverai il corso, insieme a tutti i corsi cui sei già iscritto, nella sezione “La tua formazione”.
CORSI AD ACCESSO RISERVATO. I corsi ad accesso riservato rientrano in iniziative di formazione che la Fondazione organizza in collaborazione con altri enti/istituzioni. I corsi ad accesso riservato non sono presenti nella sezione Catalogo. Per parteciparvi, sincerati delle modalità previste contattando i promotori";
$string['assistence.unenrol_platform_course_question'] = "Come posso annullare la mia iscrizione ad un corso?";
$string['assistence.unenrol_platform_course_answer'] = "Per disiscriversi da un corso ad accesso libero vai nella sezione “La tua formazione”, entra all'interno della scheda dedicata al corso a cui non vuoi più partecipare e clicca sul pulsante “Disiscrivimi” posto sulla parte destra dello schermo.
Per disiscriversi da un corso ad accesso riservato contatta i promotori dell'iniziativa.
NOTA BENE. Nel caso di webinar in diretta, dove la capienza è limitata, disiscriversi è un gesto responsabile quando si è certi di non poter partecipare. In questo modo lascerai il posto ad un'altra persona.";

$string['header.linkregister'] = "Registrati";


$string['header.menu.home'] = 'Home';
$string['header.menu.corsi'] = 'Corsi';
$string['header.menu.percorsi'] = 'Percorsi';
$string['header.menu.chisiamo'] = 'Chi siamo';
$string['header.menu.dicolab'] = 'Dicolab';
$string['header.menu.account'] = 'Il tuo account';


$string['setting:notify_text'] = "Testo banner notifica";
$string['setting:notify_text_desc'] = "Lasciare vuoto per non abilitiare";
$string['setting:notify_link'] = "Link banner notifica";
$string['setting:notify_link_desc'] = "Link opzionale";

$string['course.courseexpiration'] = "Scadenza del corso";
$string['course.customcertid-label'] = "Completa il corso per ottenere";
$string['course.customcertid-msg'] = "1 attestato";
$string['course.tab.course'] = "Il corso";
$string['course.tab.reviews'] = "Recensioni";
$string['course.nofavorite'] = "Aggiungi ai preferiti";
$string['course.favorite'] = "Elimina dai preferiti";


$string['path.tab.path'] = "Percorso";
$string['path.tab.reviews'] = "Recensioni";
$string['path.customcertid-label'] = "Completa il percorso per ottenere";
$string['path.customcerte'] = "1 Attestato";
$string['path.customcertes'] = '{$a} Attestati';
$string['path.competenze-title'] = "Al termine del percorso ecco le tue ";
$string['path.competenze-msg'] = "Competenze acquisite";

$string['header.menu.formazione'] = "La tua formazione";
$string['header.menu.bacheca'] = "Area personale";
$string['header.menu.catalogo'] = "Catalogo";
$string['no_reviews'] = "Al momento non ci sono recensioni";
$string['no_reviews_submsg'] = "Fai sapere agli altri utenti cosa ne pensi.";
$string['course.includepath'] = "Questo corso è parte di";
$string['path.includepath'] = "Questo percorso può essere integrato con altri";
$string['path.paths'] = '{$a} Percorsi';
$string['path.path'] = '{$a} Percorso';
$string['path.tab.similarpath'] = 'Percorsi simili';
$string['path.section.similarpath'] = 'Percorsi simili';
$string['path.section.similarcourses'] = 'Corsi simili';

$string['profile.title'] = 'Gestisci account';
$string['profile.section.sectiondata'] = 'Dati di accesso';
$string['profile.section.other_data'] = 'Altri dati';
$string['profile.section.preferences'] = 'Preferenze';

$string['dicolabcoursesforyou'] = 'DICOLAB courses for you';

$string['course.tab.similarcourse'] = 'Corsi simili';
$string['course.section.similarpath'] = 'Percorsi simili';
$string['course.section.similarcourses'] = 'Corsi simili';
$string['profile.section.edit_accessData'] = 'Modifica dati di accesso';
$string['profile.section.edit_otherData'] = 'Modifica altri dati';

$string['profile.section.edit_preferences'] = 'Modifica preferenze';
$string['profile.desc_page'] = 'Gestisci il tuo account e aggiorna le tue preferenze';
$string['course.includepath.path'] = '{$a} Percorso';
$string['course.includepath.paths'] = '{$a} Percorsi';
$string['home.gotocourse'] = "Esplora il catalogo";

$string['my.title'] = 'Area personale';
$string['my.tab1'] = 'Corsi e Percorsi';
$string['my.tab2'] = 'Preferiti';
$string['my.tab3'] = 'Attestati e Badge';
$string['my.info_followingCourses'] = '<b>Stai seguendo:</b> {$a} <span>corsi</span>';
$string['my.info_completedCourses'] = '<b>Hai completato:</b> {$a} <span>corsi</span>';
$string['my.info_followingPaths'] = '<b>Stai seguendo:</b> {$a} <span>percorsi</span>';
$string['my.info_completedPaths'] = '<b>Hai completato:</b> {$a} <span>percorsi</span>';
$string['my.info_badges'] = '<b>Hai ottenuto:</b> {$a} <span>badge</span>';

$string['my.tab1_followingCourses'] = 'Stai seguendo {$a} corsi';
$string['my.tab1_followingPaths'] = 'Stai seguendo {$a} percorsi';
$string['my.tab1_completedCourses'] = 'Hai completato {$a} corsi';
$string['common.completed'] = 'Completato';
$string['my.tab1_completedPaths'] = 'Hai completato {$a} percorsi';

$string['my.tab1_followingCourses_no'] = 'Al momento non stai seguendo nessun corso. <br>Quando ti iscriverai ad un corso troverai qui le informazioni per accedere.';
$string['my.tab1_followingCourses_no_title'] = 'Corsi che stai seguendo';
$string['my.tab1_followingCourses_no_linkLabel'] = 'Sfoglia il catalogo';

$string['my.tab1_followingPaths_no'] = 'Al momento non stai seguendo nessun percorso. <br>Quando ti iscriverai ad un percorso troverai qui le informazioni per accedere.';
$string['my.tab1_followingPaths_no_title'] = 'Percorsi che stai seguendo';
$string['my.tab1_followingPaths_no_linkLabel'] = 'Espolora i percorsi';

$string['my.tab1_completedCourses_no'] = 'Al momento non hai completato nessun corso di formazione. Quando completerai un corso troverai qui tutti i dettagli.';
$string['my.tab1_completedCourses_no_title'] = 'Corsi completati';
$string['my.tab1_completedCourses_no_linkLabel'] = 'Sfoglia il catalogo';

$string['my.tab1_completedPaths_no'] = 'Al momento non hai completato nessun percorso. Quando completerai un percorso troverai qui tutti i dettagli.';
$string['my.tab1_completedPaths_no_title'] = 'Percorsi completati';
$string['my.tab1_completedPaths_no_linkLabel'] = 'Espolora i percorsi';

$string['my.tab1_no_activity'] = 'Nessuna attività in corso';
$string['my.tab1_no_activity_message'] = 'In questa pagina troverai l\'elenco dei corsi e dei percorsi che decidi di seguire.';
$string['my.tab1_no_activity_link'] = 'Sfoglia il catalogo';

$string['home.coursesperte'] = 'Continua a guardare';

$string['my.tab2_incompletedCourses'] = 'Hai {$a} corsi da completare';
$string['my.tab2_incompletedCourse'] = 'Hai {$a} corso da completare';

$string['my.tab2_incompletedCourses_no_title'] = 'Corsi da completare';
$string['my.tab2_incompletedCourses_no'] = 'Al momento non hai corsi preferiti da completare. Sfoglia il catalogo, trova i corsi che più ti interessano e aggiungili alla tua lista dei preferiti.';
$string['my.tab2_incompletedCourses_linkLabel'] = 'Sfoglia il catalogo';

$string['path_similar_empty_title'] = 'Nessun percorso simili';

$string['course_similar_empty_title'] = 'Nessun corso simili';

$string['my.tab2_startedCourses'] = 'Hai {$a} corsi da iniziare';
$string['my.tab2_startedCourse'] = 'Hai {$a} corso da iniziare';
$string['my.tab2_no_activity'] = 'Non hai corsi preferiti';
$string['my.tab2_no_activity_message'] = 'Sfoglia il catalogo, trova i corsi che più ti interessano e aggiungili alla tua lista dei preferiti.';
$string['my.tab2_no_activity_link'] = 'Sfoglia il catalogo';

$string['my.tab3_your_certificates'] = 'Hai ottenuto {$a} cerificati';
$string['my.tab3_your_certificate'] = 'Hai ottenuto {$a} cerificato';
$string['common.of'] = 'di';
$string['common.download'] = 'Scarica';

$string['my.tab3_your_incompleted_certificates'] = 'Ci sei quasi, continua a formarti per ottenere {$a}  attestati';
$string['my.tab3_your_incompleted_certificate'] = 'Ci sei quasi, continua a formarti per ottenere {$a}  attestato';
$string['my.tab3_detailsCourse'] = 'Vedi dettaglio';


$string['my.tab3_no_activity'] = 'Un piccolo sforzo...';
$string['my.tab3_no_activity_message'] = 'Inizia subito a seguire un corso per ottenere un attestato o un badge.';
$string['my.tab3_no_activity_link'] = 'Sfoglia il catalogo';
$string['my.tab3_your_badges'] = 'Hai ottenuto {$a} badge';


$string['my.tab3_your_incompleted_certificates_no_title'] = 'Titolo';
$string['my.tab3_your_incompleted_certificates_no'] = 'messaggio';
$string['my.tab3_your_incompleted_certificates_no_linkLabel'] = 'Sfoglia il catalogo';

$string['my.tab3_your_certificates_no_title'] = 'Titolo';
$string['my.tab3_your_certificates_no'] = 'messaggio';
$string['my.tab3_your_certificates_no_linkLabel'] = 'Sfoglia il catalogo';


$string['dicolab.title'] = 'Dicolab';
$string['dicolab.header_info'] = 'Dicolab. Cultura al digitale';
$string['dicolab.header_title'] = 'L\'offerta formativa gratuita in continua evoluzione per la digitalizzazione del patrimonio culturale.';
$string['dicolab.argomenti_info'] = 'LE AREE DI INTERVENTO';
$string['dicolab.argomenti_title'] = 'Scopri i percorsi';
$string['dicolab.argomenti.link'] =  "Vai alla categoria";

$string['dicolab.argomenti.produzione_gestione'] = 'Produzione e gestione';
$string['dicolab.argomenti.produzione_gestione_desc'] = 'Come produrre e gestire prodotti e servizi digitali: user experience design, catalogazione, data governance per la digitalizzazione del patrimonio culturale.';

$string['dicolab.argomenti.processi_di_supporto'] = 'Processi di supporto';
$string['dicolab.argomenti.processi_di_supporto_desc'] = 'Metodi e processi a supporto delle organizzazioni, per fornire le premesse sostanziali alla trasformazione digitale: privacy, sicurezza, sostenibilità, management.';

$string['dicolab.argomenti.pensiero_digitale'] = 'Pensiero digitale';
$string['dicolab.argomenti.pensiero_digitale_desc'] = 'Sviluppa le soft skills a supporto della trasformazione digitale, allena le competenze trasversali e relazionali per utilizzare in modo efficace i nuovi linguaggi e strumenti digitali.';

$string['dicolab.argomenti.governance_della_rasformazione_digitale'] = 'Governance della trasformazione digitale';
$string['dicolab.argomenti.governance_della_rasformazione_digitale_desc'] = 'Rafforzamento dell’organizzazione in termini di standard, procedure e modelli di gestione del cambiamento.';


$string['dicolab.argomenti.uso_e_condivisione'] = 'Uso e condivisione';
$string['dicolab.argomenti.uso_e_condivisione_desc'] = 'Dalla produzione alla condivisione e utilizzo di dati, informazioni e risorse digitali: comunicazione, community management, open access.';

$string['dicolab.argomenti.ricerca_e_innovazione'] = 'Ricerca e innovazione';
$string['dicolab.argomenti.ricerca_e_innovazione_desc'] = 'Come definire nuovi spazi del possibile: strumenti innovativi di ricerca, promozione dell’innovazione, science storytelling.';


$string['dicolab.cultura.info'] = 'Dicolab. Cultura al digitale';
$string['dicolab.cultura.title'] = 'Formazione e miglioramento delle competenze digitali per il patrimonio culturale';
$string['dicolab.cultura.text'] =  'Un progetto aperto e in continua evoluzione, Dicolab è promosso dal Ministero della cultura – Digital Library nell\'ambito del PNRR Cultura 4.0, realizzato dalla Fondazione Scuola dei beni e delle attività culturali e finanziata dall\'Unione europea – Next Generation EU.';

$string['dicolab.offriamo_info'] = 'L\'offerta formativa';
$string['dicolab.offriamo_title'] = 'Aperta, certificata e in continua evoluzione';
$string['dicolab.offriamo.card1_title'] = 'Scegli il corso più adatto a te';
$string['dicolab.offriamo.card1_desc'] = 'Il programma Dicolab. Cultura al digitale si struttura in aree di intervento, percorsi e corsi per uno sviluppo verticale della formazione, che dalle conoscenze di base arrivi alle competenze tecnico-professionali più approfondite.';
$string['dicolab.offriamo.card2_title'] = 'Percorsi in divenire';
$string['dicolab.offriamo.card2_desc'] = 'Un\'offerta a catalogo in continuo aggiornamento: alla prima tranche di corsi fruibili a partire dall’autunno 2023 si aggiungeranno sempre nuove proposte, dedicate agli ambiti tematici di maggiore attualità e destinate ad offrire maggiori approfondimenti specialistici. ';
$string['dicolab.offriamo.card3_title'] = 'Ottieni l\'open badge Dicolab';
$string['dicolab.offriamo.card3_desc'] = 'L\'Open Badge Dicolab corrisponde la certificazione di conoscenze, abilità e competenze acquisite nei corsi. Può essere usato nei CV digitali e inserito sul profilo LinkedIn per comunicare in modo sintetico e credibile, che cosa è stato appreso, in che modo e con quali risultati.';
$string['dicolab.offriamo.link_course'] = 'Vai ai corsi';
$string['dicolab.offriamo.link_path'] = 'Vai ai percorsi';
$string['dicolab.offriamo.link_arg'] = 'Scropri gli argomenti';



$string['dicolab.banner.title'] = 'Formazione gratuita, aperta e certificata';
$string['dicolab.banner.desc'] = 'Attività online e in presenza, che uniscono la ricerca alla pratica. Podcast, video-pillole e approfondimenti per stimolare un apprendimento continuo.';
$string['dicolab.banner.button1'] =  "Iscriviti ora";
$string['dicolab.banner.button2'] =  "Scopri i corsi";


$string['dicolab.courses.title'] =  "Corsi Dicolab in evidenza";
$string['dicolab.courses.link'] =  "Scopri i corsi Dicolab";

$string['dicolab.completed_dicolab_course_title'] =  "Congratulazioni!";
$string['dicolab.completed_dicolab_course_msgcustomcertidno'] =  'Hai completato il corso DICOLAB {$a}.';
$string['dicolab.completed_dicolab_course_msgcustomcertid'] =  'Hai completato il corso DICOLAB {$a}. Puoi scaricare il certificato';
$string['dicolab.completed_dicolab_course_title2'] =  "Altri corsi Dicolab";
$string['dicolab.completed_fsbac_course_title'] =  "Congratulazioni!";
$string['dicolab.completed_fsbac_course_msgcustomcertidno'] =  'Hai completato il corso {$a}.';
$string['dicolab.completed_fsbac_course_msgcustomcertid'] =  'Hai completato il corso {$a}. Puoi scaricare il certificato';
$string['dicolab.completed_fsbac_course_title2'] =  "Altri corsi";

$string['footer.help.policy_fondazione'] = 'Policy Fondazione';
$string['footer.help.policy_dicolab'] = 'Policy Dicolab';

$string['footer.label_loghi']  = 'La piattaforma FAD è stata realizzata nell\'ambito del progetto Dicolab. Cultura al digitale, promosso dal Ministero della Cultura - Digital Library nell\'ambito del PNRR Cultura 4.0, realizzato dalla Fondazione Scuola dei beni e delle attività culturali e finanziato dall\'Unione europea - Next Generation EU';
