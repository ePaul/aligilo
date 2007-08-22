<?php
 require_once('fpdf.php');
 define('FPDF_FONTPATH','./font/');
  
  //Zeichensatz anzeigen im PDF
  
$font='ORION';
 
  $pdf=new FPDF();
  $pdf->AddFont($font,'',$font.'.php');
  $pdf->SetFont($font,'',15);
  $pdf->Open();
  $pdf->AddPage(); 
  
  $pdf->setFontSize(15);
  
  for ($i=32;$i<256;$i++)
    $text .=" #$i: ".chr($i);
  $pdf->write(10,$text);
  $pdf->output('test.pdf')
  
?>