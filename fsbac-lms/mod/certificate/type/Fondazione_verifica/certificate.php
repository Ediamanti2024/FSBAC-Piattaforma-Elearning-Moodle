<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
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
 * A4_non_embedded certificate type
 *
 * @package    mod_certificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//use Altaformazione\DB;
//use Altaformazione\Models\User;

defined('MOODLE_INTERNAL') || die();

//$user = User::find($USER->id);





$user = $DB->get_record('user',array('id' => $USER->id) );
$usercf = $DB->get_record_sql("select upper(data) as codice_fiscale from {user_info_data} cd inner join {user_info_field} cf on cf.id=cd.fieldid where shortname='CF' and cd.userid=".$USER->id);

$org1 = $DB->get_record_sql("select data as org from {user_info_data} cd inner join {user_info_field} cf on cf.id=cd.fieldid where shortname='organizzazione_pubblico' and cd.userid=$USER->id");
$org2 = $DB->get_record_sql("select data as org from {user_info_data} cd inner join {user_info_field} cf on cf.id=cd.fieldid where shortname='organizzazione_altro_pubblico' and cd.userid=$USER->id");
$org3 = $DB->get_record_sql("select data as org from {user_info_data} cd inner join {user_info_field} cf on cf.id=cd.fieldid where shortname='organizzazione_privata' and cd.userid=$USER->id");
$org4 = $DB->get_record_sql("select data as org from {user_info_data} cd inner join {user_info_field} cf on cf.id=cd.fieldid where shortname='organizzazione_noprofit' and cd.userid=$USER->id");

$organization="";
if ($org1->org && $org1->org!="--") $organization=get_string($org1->org,"theme_fsbac");
	else if($org2->org) $organization=$org2->org;
		else if ($org3->org) $organization=$org3->org;
			else if ($org4->org) $organization=$org4->org;







$pdf = new PDF($certificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($certificate->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();
$x = 72;
$y = 30;
$sealx = 150;
$sealy = 5;
$sigx = 30;
$sigy = 230;
$custx = 30;
$custy = 230;
$wmarkx = 26;
$wmarky = 58;
$wmarkw = 158;
$wmarkh = 170;
$brdrx = 0;
$brdry = 0;
$brdrw = 210;
$brdrh = 297;
$codey = 250;
// Add images and lines
certificate_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
certificate_draw_frame($pdf, $certificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(0.2);
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, 50, '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Add text
$pdf->SetTextColor(107, 72, 157);
certificate_print_text($pdf, $x, 37, 'L', '', '', 40, 'attestato');

$pdf->SetTextColor(24, 66, 135);
certificate_print_text($pdf, $x, 62, 'L', '', '', 12, 'Si attesta che');
certificate_print_text($pdf, $x, 68, 'L', '', '', 12, $user->firstname . ' ' . $user->lastname);
$pdf->Line($x, 74, $x + 123, 74, ['height' => 0.5, 'color' => array(24, 66, 135)]);

certificate_print_text($pdf, $x, 81, 'L', '', '', 9, 'codice fiscale');
certificate_print_text($pdf, $x, 86, 'L', '', '', 12, strtoupper($usercf->codice_fiscale));
$pdf->Line($x, 92, $x + 123, 92, ['height' => 0.5, 'color' => array(24, 66, 135)]);

certificate_print_text($pdf, $x, 98, 'L', '', '', 9, 'organizzazione');
certificate_print_text($pdf, $x, 104, 'L', '', '', 10, $organization);
$pdf->Line($x, 110, $x + 123, 110, ['height' => 0.5, 'color' => array(24, 66, 135)]);
certificate_print_text($pdf, $x, 112, 'L', '', '', 10, $user->area);
$pdf->Line($x, 118, $x + 123, 118, ['height' => 0.5, 'color' => array(24, 66, 135)]);

certificate_print_text($pdf, $x, 126, 'L', '', '', 12, 'ha superato la verifica di apprendimento del corso');
//certificate_print_text($pdf, $x, 132, 'L', '', '', 12, 'corso');

certificate_print_text($pdf, 36, 134, 'L', '', '', 22, format_string($course->fullname));

certificate_print_text($pdf, $x, 166, 'L', '', '', 12, 'in data');
certificate_print_text($pdf, $x, 172, 'L', '', '', 12, certificate_get_date($certificate, $certrecord, $course));
$pdf->Line($x, 178, $x + 123, 178, ['height' => 0.5, 'color' => array(24, 66, 135)]);

certificate_print_text($pdf, $x, 183, 'L', '', '', 12, 'della durata di');
certificate_print_text($pdf, $x, 189, 'L', '', '', 12, $certificate->printhours);
$pdf->Line($x, 195.5, $x + 123, 195.5, ['height' => 0.5, 'color' => array(24, 66, 135)]);

certificate_print_text($pdf, 36, 206, 'L', '', '', 12, 'Il corso si è svolto online sulla piattaforma LMS');
certificate_print_text($pdf, 36, 211.5, 'L', '', '', 12, 'fad.fondazionescuolapatrimonio.it');

certificate_print_text($pdf, $x, 222, 'L', '', '', 12, 'data');
certificate_print_text($pdf, $x, 228, 'L', '', '', 12,  userdate(time(), '%d %B %Y'));
$pdf->Line($x, 234, $x + 123, 234, ['height' => 0.5, 'color' => array(24, 66, 135)]);

$issue = certificate_get_issue($course, $user, $certificate, get_coursemodule_from_instance('certificate', $certificate->id));

certificate_print_text($pdf, $x, 239.5, 'L', '', '', 12, 'numero attestato');
certificate_print_text($pdf, $x, 245, 'L', '', '', 12, str_pad($issue->id, 6, '0', STR_PAD_LEFT));
$pdf->Line($x, 251, $x + 123, 251, ['height' => 0.5, 'color' => array(24, 66, 135)]);

certificate_print_text($pdf, 0, 263.5, 'R', '', '', 9, 'firmato dal Direttore', 195);
certificate_print_text($pdf, 0, 267, 'R', '', '', 9, 'arch. Maria Alessandra Vittorini', 195);
  
certificate_print_text($pdf, 32, 284, 'L', '', '', 7, 'Fondazione Scuola dei beni e delle attività culturali');
certificate_print_text($pdf, 32, 287, 'L', '', '', 7, 'Sede legale via del Collegio Romano 27 - 00186 Roma | C.F. 97900380581 | PEC scuoladelpatrimonio@pec.it | www.fondazionescuolapatrimonio.it');