<?php

  /**
   * Kreas specimenon de PDF-tiparo.
   *
   * @package aligilo
   * @subpackage specialaj_skriptoj
   * @author Martin Sawitzki, Paul Ebermann
   */


  /**
   */

require_once("../iloj/iloj_html.php");
 require_once('../iloj/fpdf/fpdf.php');
 define('FPDF_FONTPATH','../iloj/fpdf/tiparoj/');
  
  
$font='TEMPO';
 
  $pdf=new FPDF();
  $pdf->AddFont($font,'',$font.'.php');
  $pdf->SetFont($font,'',15);
  $pdf->Open();
  $pdf->AddPage(); 
  
  $pdf->setFontSize(15);
  
for ($i=32;$i<256;$i++) {
    $text .=" #$i: ".chr($i);
    if (($i-32)%8 == 7) {
        $text .= "\n";
    }
 }
  $pdf->write(10,$text);


$dosiero = '../dosieroj_generitaj/signokodoj.pdf';

$pdf->output($dosiero);
hazard_ligu($dosiero, "signolisto");
      
?>