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
 * A4_embedded certificate type
 *
 * @package    mod
 * @subpackage certificate
 * @copyright  Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
function myUpper($str) {

$str = mb_strtoupper(trim(fullname($str)), 'UTF-8');
$str = str_replace("Ά", "Α", $str);
$str = str_replace("Έ", "Ε", $str);
$str = str_replace("Ί", "Ι", $str);
$str = str_replace("Ύ", "Υ", $str);
$str = str_replace("Ή", "Η", $str);
$str = str_replace("Ό", "Ο", $str);
$str = str_replace("Ώ", "Ω", $str);

return $str;
}

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from view.php
}

$pdf = new PDF($certificate->orientation, 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetMargins(5, 5, 5);
$pdf->SetTitle($certificate->name);
$pdf->SetAuthor("ΚΕ.ΠΛΗ.ΝΕ.Τ ΚΥΚΛΑΔΩΝ");
$pdf->SetSubject("Βεβαίωση συμμετοχής στο σεμινάριο " . $course->fullname);
//$pdf->SetProtection(array('modify'));
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetAutoPageBreak(false, 0);
$pdf->AddPage();

// Define variables
// Landscape
if ($certificate->orientation == 'L') {
    $x = 10;
    $y = 30;
    $sealx = 230;
    $sealy = 150;
    $sigx = 47;
    $sigy = 155;
    $custx = 47;
    $custy = 155;
    $wmarkx = 40;
    $wmarky = 31;
    $wmarkw = 212;
    $wmarkh = 148;
    $brdrx = 0;
    $brdry = 0;
    $brdrw = 297;
    $brdrh = 210;
    $codey = 175;
} else { // Portrait
    $x = 10;
    $y = 40;
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
}

// Add images and lines
certificate_print_image($pdf, $certificate, CERT_IMAGE_BORDER, $brdrx, $brdry, $brdrw, $brdrh);
certificate_draw_frame($pdf, $certificate);
// Set alpha to semi-transparency
$pdf->SetAlpha(0.2);
certificate_print_image($pdf, $certificate, CERT_IMAGE_WATERMARK, $wmarkx, $wmarky, $wmarkw, $wmarkh);
$pdf->SetAlpha(1);
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SIGNATURE, $sigx, $sigy, '', '');


$LOGO_FILE = $CFG->wwwroot.'/mod/certificate/pix/' .CERT_IMAGE_SIGNATURE. '/logo_keplinet.png';
$SIGN_FILE = $CFG->wwwroot.'/mod/certificate/pix/' .CERT_IMAGE_SIGNATURE. '/sign_ntzimop.png';
$SIGN_FILE_ETWINNING = $CFG->wwwroot.'/mod/certificate/pix/' .CERT_IMAGE_SIGNATURE. '/sign_mp.png';
$role = certificate_get_user_role($course, $USER);
$course_info = certificate_get_course_info($course);

if ($_SERVER['SERVER_NAME'] == "moodle2.epyna.eu") {
	$user_field_code = 6;
	$titleYPos = 40;
}

global $CFG;
$path = "$CFG->dirroot/mod/certificate/html";
$filename = $path . '/cert_sepehy_'. strtolower($role). '.html';
$statement = file_get_contents($filename);

$statement = str_replace("%LOGO_FILE%", $LOGO_FILE ,$statement);
$statement = str_replace("%SIGN_FILE%", $SIGN_FILE ,$statement);
$statement = str_replace("%SIGN_FILE_ETWINNING%", $SIGN_FILE_ETWINNING ,$statement);
//$statement = str_replace("%FULLNAME%", mb_strtoupper(trim(fullname($USER)), 'UTF-8') ,$statement);
$statement = str_replace("%FULLNAME%", myUpper($USER), $statement);
$statement = str_replace("%MOODLE_SEMINAR%", $course->fullname ,$statement);
$statement = str_replace("%CERTTYPE%", '' ,$statement);
$statement = str_replace("%KLADOS%", certificate_get_user_field($user_field_code, $USER) ,$statement); // (1) on Moodle1, (6) on Moodle2 
$statement = str_replace("%MOODLE_START%", $course_info->start_date, $statement);
$statement = str_replace("%MOODLE_END%", $course_info->end_date , $statement);
$statement = str_replace("%COURSE_LENGTH%", $course_info->duration . ' ωρών' ,$statement);
$statement = str_replace("%WEEKS%", '4' ,$statement);
//$statement = str_replace("%CUR_DATE%", date("d/m/Y") ,$statement);
$statement = str_replace("%CUR_DATE%",  $course_info->sign_date ,$statement);
$statement = str_replace("%SIGN_NAME%", 'Νίκος Τζιμόπουλος' ,$statement);


//echo(utf8_decode($statement));
//die(0);

// Add text
//$pdf->SetTextColor(0, 0, 120);

certificate_print_text($pdf, $x, $titleYPos, 'C', 'freesans', '', 26, '<p><strong>Β Ε Β Α Ι Ω Σ Η</strong></p>');
certificate_print_text($pdf, $x, $y - 30, 'C', 'freesans', '', 14, $statement);

if ($certificate->printhours) {
    certificate_print_text($pdf, $x, $y + 122, 'C', 'freeserif', '', 10, get_string('credithours', 'certificate') . ': ' . $certificate->printhours);
}
certificate_print_text($pdf, $x, $codey, 'C', 'freeserif', '', 10, certificate_get_code($certificate, $certrecord));
$i = 0;
if ($certificate->printteacher) {
    $context = context_module::instance($cm->id);
    if ($teachers = get_users_by_capability($context, 'mod/certificate:printteacher', '', $sort = 'u.lastname ASC', '', '', '', '', false)) {
        foreach ($teachers as $teacher) {
            $i++;
            certificate_print_text($pdf, $sigx, $sigy + ($i * 4), 'L', 'freeserif', '', 12, fullname($teacher));
        }
    }
}

certificate_print_text($pdf, $custx, $custy, 'L', null, null, null, $certificate->customtext);
?>