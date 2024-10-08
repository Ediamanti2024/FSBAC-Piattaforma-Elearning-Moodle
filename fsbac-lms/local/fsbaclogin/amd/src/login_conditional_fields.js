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
 * Sticky footer module.
 *
 * @module     local_fsbaclogin/login_conditional_fields
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export const init = () => {

    if ($("#id_country option:selected").val() != "IT") {
        if (document.getElementById('fitem_id_profile_field_provinciamenu')) {
            document.getElementById('fitem_id_profile_field_provinciamenu').style.display = "none";
        }
    }

    $("#id_country").on( "change", function() {
        if ($("#id_country option:selected").val() == "IT") {
            document.getElementById('fitem_id_profile_field_provinciamenu').style.display = "block";
        } else {
            document.getElementById('fitem_id_profile_field_provinciamenu').style.display = "none";
        }
    });

    if ($("#id_profile_field_tipologia_utente option:selected").val() == "lavoratore") {
        if ($("#id_profile_field_specifica_settore option:selected").val() == "--") {
            if (document.getElementById('fitem_id_profile_field_organizzazione_pubblico')) {
                document.getElementById('fitem_id_profile_field_organizzazione_pubblico').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_specifica')) {
                document.getElementById('fitem_id_profile_field_specifica').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_qualifica')) {
                document.getElementById('fitem_id_profile_field_qualifica').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_professione')) {
                document.getElementById('fitem_id_profile_field_professione').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_ambito')) {
                document.getElementById('fitem_id_profile_field_ambito').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_specifica_ambito')) {
                document.getElementById('fitem_id_profile_field_specifica_ambito').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_organizzazione_noprofit')) {
                document.getElementById('fitem_id_profile_field_organizzazione_noprofit').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_organizzazione_privato')) {
                document.getElementById('fitem_id_profile_field_organizzazione_privato').style.display = "none";
            }
        } else {
            if ($("#id_profile_field_specifica_settore option:selected").val() == "pubblico") {
                if (document.getElementById('fitem_id_profile_field_organizzazione_noprofit')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_noprofit').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_organizzazione_privato')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_privato').style.display = "none";
                }
            } else if ($("#id_profile_field_specifica_settore option:selected").val() == "privato") {
                if (document.getElementById('fitem_id_profile_field_organizzazione_pubblico')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_pubblico').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_specifica')) {
                    document.getElementById('fitem_id_profile_field_specifica').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_qualifica')) {
                    document.getElementById('fitem_id_profile_field_qualifica').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_organizzazione_noprofit')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_noprofit').style.display = "none";
                }
            } else if ($("#id_profile_field_specifica_settore option:selected").val() == "non_profit") {
                if (document.getElementById('fitem_id_profile_field_organizzazione_pubblico')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_pubblico').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_specifica')) {
                    document.getElementById('fitem_id_profile_field_specifica').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_qualifica')) {
                    document.getElementById('fitem_id_profile_field_qualifica').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_organizzazione_privato')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_privato').style.display = "none";
                }
            }
            if ($("#id_profile_field_ambito option:selected").val() != "altro") {
                if (document.getElementById('fitem_id_profile_field_specifica_ambito')) {
                    document.getElementById('fitem_id_profile_field_specifica_ambito').style.display = "none";
                }
            }
        }
    } else {
        if (document.getElementById('fitem_id_profile_field_specifica_settore')) {
            document.getElementById('fitem_id_profile_field_specifica_settore').style.display = "none";
        }
        if (document.getElementById('fitem_id_profile_field_organizzazione_pubblico')) {
            document.getElementById('fitem_id_profile_field_organizzazione_pubblico').style.display = "none";
        }
        if (document.getElementById('fitem_id_profile_field_specifica')) {
            document.getElementById('fitem_id_profile_field_specifica').style.display = "none";
        }
        if (document.getElementById('fitem_id_profile_field_qualifica')) {
            document.getElementById('fitem_id_profile_field_qualifica').style.display = "none";
        }
        if (document.getElementById('fitem_id_profile_field_professione')) {
            document.getElementById('fitem_id_profile_field_professione').style.display = "none";
        }
        if (document.getElementById('fitem_id_profile_field_ambito')) {
            document.getElementById('fitem_id_profile_field_ambito').style.display = "none";
        }
        if (document.getElementById('fitem_id_profile_field_specifica_ambito')) {
            document.getElementById('fitem_id_profile_field_specifica_ambito').style.display = "none";
        }
        if (document.getElementById('fitem_id_profile_field_organizzazione_noprofit')) {
            document.getElementById('fitem_id_profile_field_organizzazione_noprofit').style.display = "none";
        }
        if (document.getElementById('fitem_id_profile_field_organizzazione_privato')) {
            document.getElementById('fitem_id_profile_field_organizzazione_privato').style.display = "none";
        }
        if (document.getElementById('fitem_id_profile_field_networkappartenenza')) {
            document.getElementById('fitem_id_profile_field_networkappartenenza').style.display = "none";
        }
    }

    $("#id_profile_field_tipologia_utente").on( "change", function() {
        if ($("#id_profile_field_tipologia_utente option:selected").val() == "lavoratore") {
            document.getElementById('fitem_id_profile_field_specifica_settore').style.display = "block";
            document.getElementById('fitem_id_profile_field_networkappartenenza').style.display = "block";
            if ($("#id_profile_field_specifica_settore option:selected").val() != "--") {
                if ($("#id_profile_field_specifica_settore option:selected").val() == "pubblico") {
                    document.getElementById('fitem_id_profile_field_organizzazione_pubblico').style.display = "block";
                    document.getElementById('fitem_id_profile_field_specifica').style.display = "block";
                    document.getElementById('fitem_id_profile_field_qualifica').style.display = "block";
                } else if ($("#id_profile_field_specifica_settore option:selected").val() == "privato") {
                    document.getElementById('fitem_id_profile_field_organizzazione_privato').style.display = "block";
                } else if ($("#id_profile_field_specifica_settore option:selected").val() == "non_profit") {
                    document.getElementById('fitem_id_profile_field_organizzazione_noprofit').style.display = "block";
                }
                document.getElementById('fitem_id_profile_field_ambito').style.display = "block";
                if ($("#id_profile_field_ambito option:selected").val() == "altro") {
                    document.getElementById('fitem_id_profile_field_specifica_ambito').style.display = "block";
                }
                document.getElementById('fitem_id_profile_field_professione').style.display = "block";
            }
        } else {
            if (document.getElementById('fitem_id_profile_field_specifica_settore')) {
                document.getElementById('fitem_id_profile_field_specifica_settore').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_organizzazione_pubblico')) {
                document.getElementById('fitem_id_profile_field_organizzazione_pubblico').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_specifica')) {
                document.getElementById('fitem_id_profile_field_specifica').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_qualifica')) {
                document.getElementById('fitem_id_profile_field_qualifica').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_professione')) {
                document.getElementById('fitem_id_profile_field_professione').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_ambito')) {
                document.getElementById('fitem_id_profile_field_ambito').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_specifica_ambito')) {
                document.getElementById('fitem_id_profile_field_specifica_ambito').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_organizzazione_noprofit')) {
                document.getElementById('fitem_id_profile_field_organizzazione_noprofit').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_organizzazione_privato')) {
                document.getElementById('fitem_id_profile_field_organizzazione_privato').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_networkappartenenza')) {
                document.getElementById('fitem_id_profile_field_networkappartenenza').style.display = "none";
            }
        }
    });

    $("#id_profile_field_specifica_settore").on( "change", function() {
        if ($("#id_profile_field_specifica_settore option:selected").val() == "--") {
            if (document.getElementById('fitem_id_profile_field_organizzazione_pubblico')) {
                document.getElementById('fitem_id_profile_field_organizzazione_pubblico').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_specifica')) {
                document.getElementById('fitem_id_profile_field_specifica').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_qualifica')) {
                document.getElementById('fitem_id_profile_field_qualifica').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_professione')) {
                document.getElementById('fitem_id_profile_field_professione').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_ambito')) {
                document.getElementById('fitem_id_profile_field_ambito').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_specifica_ambito')) {
                document.getElementById('fitem_id_profile_field_specifica_ambito').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_organizzazione_noprofit')) {
                document.getElementById('fitem_id_profile_field_organizzazione_noprofit').style.display = "none";
            }
            if (document.getElementById('fitem_id_profile_field_organizzazione_privato')) {
                document.getElementById('fitem_id_profile_field_organizzazione_privato').style.display = "none";
            }
        } else {
            if ($("#id_profile_field_specifica_settore option:selected").val() == "pubblico") {
                document.getElementById('fitem_id_profile_field_organizzazione_pubblico').style.display = "block";
                document.getElementById('fitem_id_profile_field_qualifica').style.display = "block";
                document.getElementById('fitem_id_profile_field_specifica').style.display = "block";
                if (document.getElementById('fitem_id_profile_field_organizzazione_privato')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_privato').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_organizzazione_noprofit')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_noprofit').style.display = "none";
                }
            } else if ($("#id_profile_field_specifica_settore option:selected").val() == "privato") {
                document.getElementById('fitem_id_profile_field_organizzazione_privato').style.display = "block";
                if (document.getElementById('fitem_id_profile_field_organizzazione_pubblico')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_pubblico').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_specifica')) {
                    document.getElementById('fitem_id_profile_field_specifica').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_qualifica')) {
                    document.getElementById('fitem_id_profile_field_qualifica').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_organizzazione_noprofit')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_noprofit').style.display = "none";
                }
            } else if ($("#id_profile_field_specifica_settore option:selected").val() == "non_profit") {
                document.getElementById('fitem_id_profile_field_organizzazione_noprofit').style.display = "block";
                if (document.getElementById('fitem_id_profile_field_organizzazione_pubblico')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_pubblico').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_specifica')) {
                    document.getElementById('fitem_id_profile_field_specifica').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_qualifica')) {
                    document.getElementById('fitem_id_profile_field_qualifica').style.display = "none";
                }
                if (document.getElementById('fitem_id_profile_field_organizzazione_privato')) {
                    document.getElementById('fitem_id_profile_field_organizzazione_privato').style.display = "none";
                }
            }
            document.getElementById('fitem_id_profile_field_ambito').style.display = "block";
            if ($("#id_profile_field_ambito option:selected").val() == "altro") {
                document.getElementById('fitem_id_profile_field_specifica_ambito').style.display = "block";
            }
            document.getElementById('fitem_id_profile_field_professione').style.display = "block";
        }
    });

    $("#id_profile_field_ambito").on( "change", function() {
        if ($("#id_profile_field_ambito option:selected").val() == "altro") {
            document.getElementById('fitem_id_profile_field_specifica_ambito').style.display = "block";
        } else {
            if (document.getElementById('fitem_id_profile_field_specifica_ambito')) {
                document.getElementById('fitem_id_profile_field_specifica_ambito').style.display = "none";
            }
        }
    });

};

