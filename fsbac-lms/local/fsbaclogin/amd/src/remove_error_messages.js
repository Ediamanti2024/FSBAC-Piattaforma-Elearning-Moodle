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
 * @module     local_fsbaclogin/remove_error_messages
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export const init = () => {
    $("input#id_cf").on("click", function () {
        if ($("input#id_cf").hasClass("is-invalid")) {
            $("input#id_cf").removeClass("is-invalid");
            $("#id_error_cf").remove();
        }
    });

    $("input#id_email").on("click", function () {
        if ($("input#id_email").hasClass("is-invalid")) {
            $("input#id_email").removeClass("is-invalid");
            $("#id_error_email").remove();
        }
    });

    $("input#id_email2").on("click", function () {
        if ($("input#id_email2").hasClass("is-invalid")) {
            $("input#id_email2").removeClass("is-invalid");
            $("#id_error_email2").remove();
        }
    });

    $("#id_profile_field_provinciamenu").on( "change", function() {
        if ($("#id_profile_field_provinciamenu option:selected").val() != "--") {
            if ($("select#id_profile_field_provinciamenu").hasClass("is-invalid")) {
                $("select#id_profile_field_provinciamenu").removeClass("is-invalid");
                $("#id_error_profile_field_provinciamenu").remove();
            }
        }
    });

    $("#id_profile_field_titolo_studio").on( "change", function() {
        if ($("#id_profile_field_titolo_studio option:selected").val() != "--") {
            if ($("select#id_profile_field_titolo_studio").hasClass("is-invalid")) {
                $("select#id_profile_field_titolo_studio").removeClass("is-invalid");
                $("#id_error_profile_field_titolo_studio").remove();
            }
        }
    });

    $("#id_profile_field_tipologia_utente").on( "change", function() {
        if ($("#id_profile_field_tipologia_utente option:selected").val() != "--") {
            if ($("select#id_profile_field_tipologia_utente").hasClass("is-invalid")) {
                $("select#id_profile_field_tipologia_utente").removeClass("is-invalid");
                $("#id_error_profile_field_tipologia_utente").remove();
            }
        }
    });

    $("#id_profile_field_specifica_settore").on( "change", function() {
        if ($("#id_profile_field_specifica_settore option:selected").val() != "--") {
            if ($("select#id_profile_field_specifica_settore").hasClass("is-invalid")) {
                $("select#id_profile_field_specifica_settore").removeClass("is-invalid");
                $("#id_error_profile_field_specifica_settore").remove();
            }
        }
    });

    $("#fitem_id_profile_field_networkappartenenza").on( "click", function() {
        if ($("select#id_profile_field_networkappartenenza").hasClass("is-invalid")) {
            $("select#id_profile_field_networkappartenenza").removeClass("is-invalid");
            $("#id_error_profile_field_networkappartenenza").remove();
        }
    });

    $("#id_profile_field_organizzazione_pubblico").on( "change", function() {
        if ($("#id_profile_field_organizzazione_pubblico option:selected").val() != "--") {
            if ($("select#id_profile_field_organizzazione_pubblico").hasClass("is-invalid")) {
                $("select#id_profile_field_organizzazione_pubblico").removeClass("is-invalid");
                $("#id_error_profile_field_organizzazione_pubblico").remove();
            }
        }
    });

    $("#id_profile_field_specifica").on( "click", function() {
        if ($("input#id_profile_field_specifica").hasClass("is-invalid")) {
            $("input#id_profile_field_specifica").removeClass("is-invalid");
            $("#id_error_profile_field_specifica").remove();
        }
    });

    $("#id_profile_field_qualifica").on( "change", function() {
        if ($("#id_profile_field_qualifica option:selected").val() != "--") {
            if ($("select#id_profile_field_qualifica").hasClass("is-invalid")) {
                $("select#id_profile_field_qualifica").removeClass("is-invalid");
                $("#id_error_profile_field_qualifica").remove();
            }
        }
    });

    $("#id_profile_field_professione").on( "change", function() {
        if ($("#id_profile_field_professione option:selected").val() != "--") {
            if ($("select#id_profile_field_professione").hasClass("is-invalid")) {
                $("select#id_profile_field_professione").removeClass("is-invalid");
                $("#id_error_profile_field_professione").remove();
            }
        }
    });

    $("#id_profile_field_ambito").on( "change", function() {
        if ($("#id_profile_field_ambito option:selected").val() != "--") {
            if ($("select#id_profile_field_ambito").hasClass("is-invalid")) {
                $("select#id_profile_field_ambito").removeClass("is-invalid");
                $("#id_error_profile_field_ambito").remove();
            }
        }
    });

    $("#id_profile_field_specifica_ambito").on( "click", function() {
        if ($("input#id_profile_field_specifica_ambito").hasClass("is-invalid")) {
            $("input#id_profile_field_specifica_ambito").removeClass("is-invalid");
            $("#id_error_profile_field_specifica_ambito").remove();
        }
    });

    $("#id_profile_field_organizzazione_noprofit").on( "click", function() {
        if ($("input#id_profile_field_organizzazione_noprofit").hasClass("is-invalid")) {
            $("input#id_profile_field_organizzazione_noprofit").removeClass("is-invalid");
            $("#id_error_profile_field_organizzazione_noprofit").remove();
        }
    });

    $("#id_profile_field_organizzazione_privato").on( "click", function() {
        if ($("input#id_profile_field_organizzazione_privato").hasClass("is-invalid")) {
            $("input#id_profile_field_organizzazione_privato").removeClass("is-invalid");
            $("#id_error_profile_field_organizzazione_privato").remove();
        }
    });
};
