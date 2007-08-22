<?php

 /**
  * Difinas la klason "Akceptofolio"
  * por krei PDF-ajn akceptofoliojn.
  *
  * Tio estas la malnova ne-unikoda versio.
  */

 require_once($prafix .'/iloj/fpdf/fpdf.php');
 define('FPDF_FONTPATH','./font/');
  
 class Akceptofolio
 {
 var $font='ORION';
 var $x=10;
 var $y=10;
 var $pdf;
 
 function Akceptofolio()
 {
  $this->pdf=new FPDF();
  $this->pdf->AddFont($this->font,'',$this->font.'.php');
  $this->pdf->AddFont($this->font.'D','',$this->font.'D.php');
  $this->pdf->AddFont(TEMPO,'','TEMPO.php');
  $this->pdf->SetFont($this->font,'',15);
  $this->pdf->Open();
  
  $this->pdf->SetLeftMargin(20);
 }

 function esso($s)
 {
   if (strpos($s,utf8_encode('ß'))>0) $this->pdf->SetFont('TEMPO','',15);   
   return $s;
 }
  
 function kreu_akceptofolio($partoprenantoID,$partoprenoID)
 {
 
 if ($partoprenoID != 0)
 {
   $partopreno = new Partopreno($partoprenoID);
   $partoprenanto = new Partoprenanto($partoprenantoID);
 }
 
 $ko = new Kotizo($partopreno,$partoprenanto,$_SESSION["renkontigxo"]);
 
 $this->pdf->setFontSize(10);
 
 $this->pdf->text(27,20,'Persona nomo:');
 $this->pdf->text(40,25,' Nomo:');

 if ($partoprenoID != 0)
 {
   $this->pdf->setFontSize(15);
   if ($partoprenanto->datoj[sxildnomo]!='')
      $kajo=" (".$partoprenanto->datoj[sxildnomo].")";
   $this->pdf->text(53,20,eo($this->esso($partoprenanto->datoj[personanomo] . $kajo)));
   $this->pdf->text(53,25,eo($this->esso($partoprenanto->datoj[nomo])));
   $this->pdf->SetFont($this->font,'',15); 
 }
 
 $this->pdf->setFontSize(12);
 $this->pdf->setY(50);
 
 $this->pdf->MultiCell(160,5,eo("1. Bonvolu tralegi c^i tiun folion kaj kontrolu, c^u la datumoj (nomo, adreso, telefonnumero ktp.) g^ustas."),0,'L');
 $this->pdf->MultiCell(160,5,eo("2. Se vi trovas eraron au^ se mankas informojn (ekz. naskig^dato) skribu la g^ustan informon dekstre apud la malg^usta (au^ mankanta)."),0,'L');
 $this->pdf->MultiCell(160,5,eo("3. Notu sur tiu c^i folio, je kioma horo estas noktomezo en via hejmurbo lau^ la c^i-loka tempo."),0,'L');
 $this->pdf->MultiCell(160,5,eo("4. Atendu en la antau^halo g^is ni alvokos vin au^ vian atendnumeron."),0,'L');

 $this->pdf->SetFont($this->font.'D','',20);
 $this->pdf->text(85,40,'Akcepto-Folio');
 $this->pdf->setFontSize(13);
 $this->pdf->ln(1);
 $this->pdf->write(5,eo('Vi alig^is kiel:'));
 $this->pdf->SetFont($this->font,'',12);

/* $this->pdf->text(80,20,"Celo:");
 $this->pdf->setFontSize(30);
 $this->pdf->text(90,20,$partopreno->datoj[traktstato]);*/ // aufgrund der mengenmäßigen Verteilung nicht benötigt.
 
 
 $this->pdf->setFontSize(13);
 $this->pdf->ln(5);
 
    
 $X=50;
 $Y=6;

  if ($partoprenoID != 0)
  {
 if ($partoprenanto->datoj[sxildnomo]!='')
    $kajo=" (".$partoprenanto->datoj[sxildnomo].")";
  }
 $this->pdf->cell($X,$Y,eo("Persona Nomo:"),0,0,'R');
 if ($partoprenoID != 0)
 {
   $this->pdf->cell($X,$Y,eo($this->esso($partoprenanto->datoj[personanomo].$kajo)),0,1,'L');
 }

 $this->pdf->cell($X,$Y,eo("Nomo:"),0,0,'R');
 if ($partoprenoID != 0)
 {
 $this->pdf->cell($X,$Y,eo($this->esso($partoprenanto->datoj[nomo])),0,1,'L');
}  
 $this->pdf->SetFont($this->font,'',13);
  
 $this->pdf->cell($X,$Y,eo("Adresaldonaj^o:"),0,0,'R');
 if ($partoprenoID != 0)
 {
 $this->pdf->cell($X,$Y,eo($partoprenanto->datoj[adresaldonajxo]),0,1,'L');
 }

 $this->pdf->cell($X,$Y,eo("Strato"),0,0,'R');
 if ($partoprenoID != 0)
 {
 $this->pdf->cell($X,$Y,eo($partoprenanto->datoj[strato]),0,1,'L');
 }

 $this->pdf->cell($X,$Y,eo("Pos^tkodo kaj urbo"),0,0,'R');
 if ($partoprenoID != 0)
 {
 $this->pdf->cell($X,$Y,eo($partoprenanto->datoj[posxtkodo].', '.$partoprenanto->datoj[urbo]),0,1,'L');
 }


 $this->pdf->cell($X,$Y,eo("Lando kaj provinco:"),0,0,'R');
 if ($partoprenoID != 0)
 {
 if ($partoprenanto->datoj[sxildlando]!='') $kajo=" (".$partoprenanto->datoj[sxildlando].")";
 if ($partoprenanto->datoj[provinco]!='') $kajo2=" / ".$partoprenanto->datoj[provinco];
 $this->pdf->cell($X,$Y,eo(eltrovu_lando($partoprenanto->datoj[lando]).$kajo.$kajo2),0,1,'L');
 }

 $this->pdf->cell($X,$Y,eo("Telefonnumero:"),0,0,'R');
 if ($partoprenoID != 0) 
    $this->pdf->cell($X,$Y,eo($partoprenanto->datoj[telefono]),0,1,'L');

 $this->pdf->cell($X,$Y,eo("Telefakso:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,eo($partoprenanto->datoj[telefakso]),0,0,'L');

 $this->pdf->cell($X,$Y,eo("Partoprentempo:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,eo(substr($partopreno->datoj[de],8,2)."a g^is ".substr($partopreno->datoj[gxis],8,2)."a"),0,1,'L');

 $this->pdf->cell($X,$Y,eo("Retpos^tadreso:"),0,0,'R');
 
 if ($partoprenoID != 0) 
  {
  $i=23;
  $this->pdf->setFontSize(13);
  $epost = eo($partoprenanto->datoj[retposxto]);
  while ($this->pdf->GetStringWidth($epost)>60)
  {
     $i--;
     $this->pdf->setFontSize($i);
  }  
 $this->pdf->cell($X,$Y,eo($partoprenanto->datoj[retposxto]),0,0,'L');
 }
 $this->pdf->setFontSize(13);
     
 $this->pdf->cell($X,$Y,eo("Partoprentagoj:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,eo($ko->partoprentagoj),0,1,'L');
  
 $this->pdf->cell($X,$Y,eo("Naskig^dato:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,eo($partoprenanto->datoj[naskigxdato]),0,0,'L');

 $this->pdf->cell($X,$Y,eo("Memzorganto:"),0,0,'R');
 if ($partoprenoID != 0) 
 {
   if ($partopreno->datoj[domotipo]=='M') {$domo="jes";$domotipo='memzorgantejo';}
 else {$domo="ne";$domotipo='junulargastejo';}
 $this->pdf->cell($X,$Y,$domo,0,1,'L');
 }

 $this->pdf->cell($X,$Y,eo("Sekso:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,eo($partoprenanto->sekso),0,0,'L');

 $this->pdf->cell($X,$Y,eo("Vegetarano:"),0,0,'R');
 if ($partoprenoID != 0) 
  {
 if ($partopreno->datoj[vegetare]=='J') {$vegi="jes";}
 else {$vegi="ne";}
 $this->pdf->cell($X,$Y,$vegi,0,1,'L');
 }

 $this->pdf->ln(13);

 $this->pdf->write(5,eo("Je kioma horo estas noktomezo en via hejmurbo lau^ la c^i-loka tempo:"));

 $this->pdf->ln(20);
 
 $X=40;
 $this->pdf->cell($X,$Y,eo("Kotizo"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->bazakotizo-$ko->landarabato,2)." EUR",0,1,'R');

 $this->pdf->cell($X,$Y,eo("Rabato"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->rabato,2)." EUR",0,1,'R');

 $this->pdf->cell($X,$Y,eo("Krompago"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->krompago,2)." EUR",0,1,'R');

 $this->pdf->ln(4);
 
 $this->pdf->cell($X,$Y,eo("Pagenda kotizo"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->kotizo,2)." EUR",0,1,'R');

 $this->pdf->cell($X,$Y,eo("Antau^pago"),0,0,'R');
 if ($partoprenoID != 0) 
 {
   $this->pdf->cell($X,$Y,number_format($ko->antauxpago,2)." EUR",0,0,'R');
 
 $this->pdf->cell(30,$Y,eo("alvenis je la:"),0,0,'R');
 $this->pdf->cell(30,$Y,$ko->antauxpagdato,0,1,'R');
 }


 $this->pdf->cell($X,$Y,eo("Membrokotizo"),0,0,'R');
// TODO: Überlegen, was tun
// $this->pdf->cell($X,$Y,number_format(0.00,2)." EUR",0,1,'R');

 $this->pdf->ln(4);

 $this->pdf->cell($X,$Y,eo("Pagenda"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->pagenda,2)." EUR",0,1,'R');

 $this->pdf->rect(160,15,30,15);
 
 
 $this->pdf->rect(20,265,30,15);
 $this->pdf->rect(90,265,30,15);
 $this->pdf->rect(160,265,30,15);
 
 $this->pdf->setFontSize(8);
 $this->pdf->text(31,268,"pagis");
 $this->pdf->text(97,268,"enkomputiligita");
 $this->pdf->text(170,268,eo("C^ambro"));

 $this->pdf->text(168,18,"Atendnumero");

 $this->pdf->SetLineWidth(0.6);
 
 $this->pdf->line(20,165,190,165);
 $this->pdf->line(20,85,190,85);
 $this->pdf->line(20,180,190,180);
 
 $this->pdf->line(30,210,100,210);
 $this->pdf->line(30,230,100,230);

  if (0 == $partoprenantoID)
  {
    echo " faris malplenan akzeptfolion<br/>\n";
  }
 
} 
 
function kaju($pID,$pnID)
{
  echo "($pID, $pnID)";
  $this->pdf->AddPage();
  $this->kreu_akceptofolio($pID,$pnID);  
}  
 
function sendu()
{  
 $this->pdf->Output('dosieroj_generitaj/akceptofolioj.pdf');
}
}
?>
