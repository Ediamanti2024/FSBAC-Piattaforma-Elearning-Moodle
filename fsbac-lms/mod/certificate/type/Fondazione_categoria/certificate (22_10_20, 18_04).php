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
use Altaformazione\DB;
use Altaformazione\Models\User;

defined('MOODLE_INTERNAL') || die();

$user = User::find($USER->id);

$pdf = new PDF($certificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetTitle($certificate->name);
$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(true, 0);
$pdf->AddPage();
$x = 72;
$y = 30;
$sealx = 150;
$sealy = 220;
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
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');

// Add text
$pdf->SetTextColor(107, 72, 157);
certificate_print_text($pdf, $x, 37, 'L', 'neuehaasunicapro', '', 40, 'attestato');

$pdf->SetTextColor(24, 66, 135);
certificate_print_text($pdf, $x, 62, 'L', 'neuehaasunicapro', '', 12, 'Si attesta che');
certificate_print_text($pdf, $x, 68, 'L', 'neuehaasunicaprob', '', 12, $user->firstname . ' ' . $user->lastname);
$pdf->Line($x, 74, $x + 128, 74, ['height' => 0.5, 'color' => array(24, 66, 135)]);

certificate_print_text($pdf, $x, 81, 'L', 'neuehaasunicapro', '', 9, 'codice fiscale');
certificate_print_text($pdf, $x, 86, 'L', 'neuehaasunicaprob', '', 12, strtoupper($user->username));
$pdf->Line($x, 92, $x + 128, 92, ['height' => 0.5, 'color' => array(24, 66, 135)]);

certificate_print_text($pdf, $x, 98, 'L', 'neuehaasunicapro', '', 9, 'organizzazione');
certificate_print_text($pdf, $x, 104, 'L', 'neuehaasunicaprob', '', 10, $user->organization_name ? $user->organization_name : $user->organization);
$pdf->Line($x, 110, $x + 128, 110, ['height' => 0.5, 'color' => array(24, 66, 135)]);
certificate_print_text($pdf, $x, 112, 'L', 'neuehaasunicaprob', '', 10, $user->area);
$pdf->Line($x, 118, $x + 128, 118, ['height' => 0.5, 'color' => array(24, 66, 135)]);

$cat = $DB->get_record_sql("select name from {course_categories} where id=$course->category");

certificate_print_text($pdf, $x, 126, 'L', 'neuehaasunicapro', '', 9, 'ha frequentato i seguenti corsi della categoria');
certificate_print_text($pdf, $x, 126+6, 'L', 'neuehaasunicaprob', '', 10, $cat->name);
$pdf->Line($x, 126+12, $x + 128, 126+12, ['height' => 0.5, 'color' => array(24, 66, 135)]);

// colonna sinistra
$x = 15; $y = 134;
 certificate_print_text($pdf, $x, $y, 'L', 'neuehaasunicapro', '', 9, 'data');
 certificate_print_text($pdf, $x, $y+5, 'L', 'neuehaasunicaprob', '', 10,  userdate(time(), '%d %B %Y'));
 $pdf->Line($x,  $y+10, $x + 45, $y+10, ['height' => 0.5, 'color' => array(24, 66, 135)]);

$issue = certificate_get_issue($course, $user, $certificate, get_coursemodule_from_instance('certificate', $certificate->id));

$x = 15; $y = 154;
certificate_print_text($pdf, $x,  $y, 'L', 'neuehaasunicapro', '', 9, 'numero attestato');
certificate_print_text($pdf, $x, $y+5, 'L', 'neuehaasunicaprob', '', 10, str_pad($issue->id, 6, '0', STR_PAD_LEFT));
$pdf->Line($x,  $y+10, $x + 45, $y+10, ['height' => 0.5, 'color' => array(24, 66, 135)]);

$x = 15; $y = 174;
 certificate_print_text($pdf, $x, $y, 'L', 'neuehaasunicapro', '', 7, 'I corsi si sono svolti online<br>sulla piattaforma LMS',50);
 certificate_print_text($pdf, $x, $y+9, 'L', 'neuehaasunicaprob', '', 7, 'fad.fondazionescuolapatrimonio.it',45);

$x = 15; $y = 194;
certificate_print_text($pdf, $x, $y, 'L', 'neuehaasunicaprob', '', 7, 'firmato dal Direttore<br>arch. Maria Alessandra Vittorini', 45);


// tabella corsi
$y = 138;
$pdf->SetXY(73, $y); //72 or 15

$rows = $DB->get_records_sql("select c.fullname, date(from_unixtime(cc.timecompleted)) as data from {course_completions} cc join {course} c on cc.course=c.id and cc.userid=$user->id where category=$course->category and visible=0");

$rows = $DB->get_records_sql("select c.fullname, date(from_unixtime(cc.timecompleted)) as data from {course_completions} cc join {course} c on cc.course=c.id and cc.userid=88 where category=5 and visible=1");

  $table = '<table>
              <tr>
                <th width="80%"><font size="-1"><b><!--CORSO--></b></font></th>
                <th width="20%"><font size="-1"><b><!--DATA--></b></font></th>
              </tr>';

foreach ($rows as $row) {
	    $table .= '<tr>
                  <td align="left"><font size="10">' . $row->fullname . '<br></font></td>
                  <td align="right"><font size="8">'. $row->data . '</font></td>
                  </tr>';
}

  $table .= '</table>';

  $pdf->writeHTML($table, true, false, false, false, '');
//  $pdf->writeHTML($pdf->GetY(), true, false, false, false, '');



$y = $pdf->GetY();
if ($y > 280) {
	$pdf->AddPage();
}


  
certificate_print_text($pdf, 32, 284, 'L', 'neuehaasunicapro', '', 7, 'Fondazione Scuola dei beni e delle attività culturali');
certificate_print_text($pdf, 32, 287, 'L', 'neuehaasunicapro', '', 7, 'Sede legale via del Collegio Romano 27 - 00186 Roma | C.F. 97900380581 | PEC scuoladelpatrimonio@pec.it | www.fondazionescuolapatrimonio.it');

