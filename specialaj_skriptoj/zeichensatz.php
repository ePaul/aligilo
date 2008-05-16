<?php
 require_once('../iloj/fpdf/fpdf.php');
 define('FPDF_FONTPATH','../iloj/fpdf/tiparoj/');
  
  //Zeichensatz anzeigen im PDF
  
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
  $pdf->output('../dosieroj_generitaj/test.pdf')
  
      ?><a href='../dosieroj_generitaj/test.pdf?rand=<?php echo rand(1000, 9999); ?>'>Zeichensatz</a>