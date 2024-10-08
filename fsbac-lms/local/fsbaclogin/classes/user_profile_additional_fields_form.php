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
 * User sign-up form.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_fsbaclogin;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot . '/local/fsbaclogin/locallib.php');

class user_profile_additional_fields_form extends \moodleform implements \renderable, \templatable
{
    public function definition()
    {
        global $CFG, $DB, $USER;

        $mform = $this->_form;

        $makeonlyadditionalfields = $this->_customdata['makeonlyadditionalfields'];

        $mform->addElement('hidden', 'makeonlyadditionalfields', $makeonlyadditionalfields);
        $mform->setType('makeonlyadditionalfields', PARAM_BOOL);

        $country = get_string_manager()->get_list_of_countries();
        $default_country[''] = get_string('selectacountry');
        $country = array_merge($default_country, $country);
        $mform->addElement('select', 'country', get_string('country'), $country);
        $mform->addRule('country', get_string('missingnazione', "local_fsbaclogin"), 'required', null, 'client');

        if ($makeonlyadditionalfields) {
            $defaultcountry = $DB->get_field("user", "country", array("id" => $USER->id));
            $mform->setDefault('country', $defaultcountry);
        } else if (!empty($CFG->country)) {
            $mform->setDefault('country', $CFG->country);
        } else {
            $mform->setDefault('country', '');
        }

        if ($fields = profile_get_signup_fields()) {
            foreach ($fields as $field) {
                if ($field->object->field->shortname == "provinciamenu") {
                    $field->object->edit_field($mform);
                }
            }
        }

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="20"');
        $mform->setType('city', \core_user::get_property_type('city'));
        if ($makeonlyadditionalfields) {
            $defaultcity = $DB->get_field("user", "city", array("id" => $USER->id));
            $mform->setDefault('city', $defaultcity);
        } else if (!empty($CFG->defaultcity)) {
            $mform->setDefault('city', $CFG->defaultcity);
        }
        $mform->addRule('city', get_string('missingcitta', "local_fsbaclogin"), 'required', null, 'client');

        if ($fields = profile_get_signup_fields()) {
            foreach ($fields as $field) {
                if ($field->object->field->shortname == "networkappartenenza") {
                    $options = array("nonetwork" => get_string("nonetwork", "local_fsbaclogin"),
                                        "aib" => get_string("aib", "local_fsbaclogin"),
                                        "aicrab" => get_string("aicrab", "local_fsbaclogin"),
                                        "aiem" => get_string("aiem", "local_fsbaclogin"),
                                        "aies" => get_string("aies", "local_fsbaclogin"),
                                        "aiucd" => get_string("aiucd", "local_fsbaclogin"),
                                        "ana" => get_string("ana", "local_fsbaclogin"),
                                        "anai" => get_string("anai", "local_fsbaclogin"),
                                        "anpia" => get_string("anpia", "local_fsbaclogin"),
                                        "ari" => get_string("ari", "local_fsbaclogin"),
                                        "cia" => get_string("cia", "local_fsbaclogin"),
                                        "cnappc" => get_string("cnappc", "local_fsbaclogin"),
                                        "cni" => get_string("cni", "local_fsbaclogin"),
                                        "conaf" => get_string("conaf", "local_fsbaclogin"),
                                        "fncf" => get_string("fncf", "local_fsbaclogin"),
                                        "fnob" => get_string("fnob", "local_fsbaclogin"),
                                        "icomitalia" => get_string("icomitalia", "local_fsbaclogin"),
                                        "icomositalia" => get_string("icomositalia", "local_fsbaclogin"),
                                        "registrarte" => get_string("registrarte", "local_fsbaclogin"),
                                        "restauratorisenzafrontiere" => get_string("restauratorisenzafrontiere", "local_fsbaclogin"),
                                        "simbdea" => get_string("simbdea", "local_fsbaclogin"),
                                        "statodeiluoghi" => get_string("statodeiluoghi", "local_fsbaclogin"),
                                        "altro" => get_string("altro", "local_fsbaclogin")
                                    );

                    $attributes = [
                        'multiple' => true,
                        'noselectionstring' => get_string('noselection', 'local_fsbaclogin')
                    ];
                    $mform->addElement('autocomplete', 'profile_field_networkappartenenza', get_string('networkappartenenza', 'local_fsbaclogin'), $options, $attributes);
                } else if ($field->object->field->shortname != "provinciamenu") {
                    if (isset($field->object->options)) {
                        foreach ($field->object->options as $optionname => $optionvalue) {
                            if ($optionvalue != "--") {
                                $newoptionname = strtolower(str_replace(array(' ', ',', '-'), '', $optionname));
                                $field->object->options[$optionname] = get_string($newoptionname, "local_fsbaclogin");
                            }
                        }
                    }
                    $field->object->edit_field($mform);
                }
            }
        }
        $mform->addRule('profile_field_titolo_studio', "", 'required', null, 'client');
        $mform->addRule('profile_field_tipologia_utente', "", 'required', null, 'client');

        if ($makeonlyadditionalfields) {
            $additionalfieldsnames = array("provinciamenu",
                                            "titolo_studio",
                                            "tipologia_utente",
                                            "specifica_settore",
                                            "networkappartenenza",
                                            "organizzazione_pubblico",
                                            "specifica",
                                            "qualifica",
                                            "professione",
                                            "ambito",
                                            "specifica_ambito",
                                            "organizzazione_noprofit",
                                            "organizzazione_privato"
                                        );
            [$insql, $inparams] = $DB->get_in_or_equal($additionalfieldsnames);
            $sql = "SELECT uid.id, uif.shortname, uid.data
                      FROM {user_info_data} uid
                      JOIN {user_info_field} uif
                        ON uif.id = uid.fieldid
                     WHERE uif.shortname $insql
                       AND uid.userid = ?";
            $inparams[] = $USER->id;
            $existingadditionalfields = $DB->get_records_sql($sql, $inparams);
            foreach ($existingadditionalfields as $existingadditionalfield) {
                if ($existingadditionalfield->shortname == "networkappartenenza") {
                    $mform->setDefault("profile_field_" . $existingadditionalfield->shortname, explode(",", $existingadditionalfield->data));
                } else {
                    $mform->setDefault("profile_field_" . $existingadditionalfield->shortname, $existingadditionalfield->data);
                }

            }
        }

        // Hook for plugins to extend form definition.
        local_fsbaclogin_extendsignupform($mform);

        // Add "Agree to sitepolicy" controls. By default it is a link to the policy text and a checkbox but
        // it can be implemented differently in custom sitepolicy handlers.
        $manager = new \core_privacy\local\sitepolicy\manager();
        $manager->signup_form($mform);

        // buttons
        $this->set_display_vertical();
        if ($makeonlyadditionalfields) {
            $buttontext = get_string("aggiornacampiaggiuntivi", "local_fsbaclogin");
        } else {
            $buttontext = get_string("completeregistration", "local_fsbaclogin");
        }
        $this->add_action_buttons(false, $buttontext);
    }

    public function definition_after_data()
    {
        $mform = $this->_form;
        $mform->applyFilter('username', 'trim');

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }
    }

    /**
     * Validate user supplied data on the signup form.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);

        $errors = array_merge($errors, local_fsbaclogin_validateextendsignupform($data));

        if ($data["country"] == "IT") {
            if ($data["profile_field_provinciamenu"] == "--") {
                $errors["profile_field_provinciamenu"] = get_string("cambia_valore_campo", "local_fsbaclogin", get_string("provinciamenu", "local_fsbaclogin"));
            }
        }
        if ($data["profile_field_titolo_studio"] == "--") {
            $errors["profile_field_titolo_studio"] = get_string("cambia_valore_campo", "local_fsbaclogin", get_string("titolo_studio", "local_fsbaclogin"));
        }
        if ($data["profile_field_tipologia_utente"] == "--") {
            $errors["profile_field_tipologia_utente"] = get_string("cambia_valore_campo", "local_fsbaclogin", get_string("tipologia_utente", "local_fsbaclogin"));
        } else if ($data["profile_field_tipologia_utente"] == "lavoratore") {
            if ($data["profile_field_specifica_settore"] == "--") {
                $errors["profile_field_specifica_settore"] = get_string("cambia_valore_campo", "local_fsbaclogin", get_string("specifica_settore", "local_fsbaclogin"));
            } else {
                if ($data["profile_field_ambito"] == "--") {
                    $errors["profile_field_ambito"] = get_string("cambia_valore_campo", "local_fsbaclogin", get_string("ambito", "local_fsbaclogin"));
                } else if ($data["profile_field_ambito"] == "altro") {
                    if (empty($data["profile_field_specifica_ambito"])) {
                        $errors["profile_field_specifica_ambito"] = get_string("valorizza_campo", "local_fsbaclogin", get_string("specifica_ambito", "local_fsbaclogin"));
                    }
                }
                if ($data["profile_field_professione"] == "--") {
                    $errors["profile_field_professione"] = get_string("cambia_valore_campo", "local_fsbaclogin", get_string("professione", "local_fsbaclogin"));
                }
                if ($data["profile_field_specifica_settore"] == "pubblico") {
                    if ($data["profile_field_organizzazione_pubblico"] == "--") {
                        $errors["profile_field_organizzazione_pubblico"] = get_string("cambia_valore_campo", "local_fsbaclogin", get_string("organizzazione_pubblico", "local_fsbaclogin"));
                    } else {
                        if (empty($data["profile_field_specifica"])) {
                            $errors["profile_field_specifica"] = get_string("valorizza_campo", "local_fsbaclogin", get_string("specifica", "local_fsbaclogin"));
                        }
                    }
                    if ($data["profile_field_qualifica"] == "--") {
                        $errors["profile_field_qualifica"] = get_string("cambia_valore_campo", "local_fsbaclogin", get_string("qualifica", "local_fsbaclogin"));
                    }
                } else if ($data["profile_field_specifica_settore"] == "non_profit") {
                    if (empty($data["profile_field_organizzazione_noprofit"])) {
                        $errors["profile_field_organizzazione_noprofit"] = get_string("valorizza_campo", "local_fsbaclogin", get_string("organizzazione_noprofit", "local_fsbaclogin"));
                    }
                } else if ($data["profile_field_specifica_settore"] == "privato") {
                    if (empty($data["profile_field_organizzazione_privato"])) {
                        $errors["profile_field_organizzazione_privato"] = get_string("valorizza_campo", "local_fsbaclogin", get_string("organizzazione_privato", "local_fsbaclogin"));
                    }
                }
            }
            // DA SCOMMENTARE QUANDO METTEREMO CONFIGURAZIONE "Display on signup page?" A YES PER IL CAMPO CUSTOM UTENTE NETWORK DI APPARTENENZA
            if (empty($data["profile_field_networkappartenenza"])) {
                $errors["profile_field_networkappartenenza"] = get_string("valorizza_campo", "local_fsbaclogin", get_string("networkappartenenza", "local_fsbaclogin"));
            }
        }

        return $errors;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export.
     * @return array
     */
    public function export_for_template(\renderer_base $output)
    {
        ob_start();
        $this->display();
        $formhtml = ob_get_contents();
        ob_end_clean();
        $context = [
            'formhtml' => $formhtml
        ];
        return $context;
    }
}
