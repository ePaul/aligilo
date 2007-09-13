<?php


 /**
  * Difinas la klason "Akceptofolio"
  * por krei PDF-ajn akceptofoliojn.
  */


  /**
   * TODO!: - TEJO-kotizo + TEJO-rabato
   */


 define('FPDF_FONTPATH', $prafix.'/iloj/fpdf/font/');
 require_once($prafix .'/iloj/fpdf/ufpdf.php');
  
 class Akceptofolio
 {
 var $font='FreeSans';
 var $x=10;
 var $y=10;
 var $pdf;
 
 function Akceptofolio()
 {
  $this->pdf=new UFPDF();
  if (DEBUG)
	{
	  echo "<!-- FPDF_FONTPATH: " . FPDF_FONTPATH . "\n-->";
	}
  $this->pdf->AddFont($this->font,'',$this->font.'.php');
  $this->pdf->AddFont($this->font.'D','',$this->font.'Bold.php');
//   $this->pdf->AddFont(TEMPO,'','TEMPO.php');
  $this->pdf->SetFont($this->font,'',15);
  $this->pdf->Open();
  
  $this->pdf->SetLeftMargin(20);
 }

 function esso($s)
 {
//    if (strpos($s,utf8_encode('ß'))>0) $this->pdf->SetFont('TEMPO','',15);   
   return $s;
 }
  
 function kreu_akceptofolio($partoprenantoID,$partoprenoID)
 {
 
 if ($partoprenoID != 0)
 {
   $partopreno = new Partopreno($partoprenoID);
   $partoprenanto = new Partoprenanto($partoprenantoID);
    $ko = new Kotizo($partopreno,$partoprenanto,$_SESSION["renkontigxo"]);
 }
 
 $this->pdf->setFontSize(10);
 
 $this->pdf->text(27,20,'Persona nomo:');
 $this->pdf->text(27,25,'Familia nomo:');

 if ($partoprenoID != 0)
 {
   $this->pdf->setFontSize(15);
   if ($partoprenanto->datoj[sxildnomo]!='')
      $kajo=" (sur noms^ildo: ".$partoprenanto->datoj[sxildnomo].")";
	else
		$kajo= "";
   $this->pdf->text(53,20,uni($this->esso($partoprenanto->datoj[personanomo] . $kajo)));
   $this->pdf->text(53,25,uni($this->esso($partoprenanto->datoj[nomo])));
	$this->pdf->text(105, 20, $partoprenoID);
 }
 $this->pdf->SetFont($this->font,'',15); 
 
 $this->pdf->setFontSize(12);
 $this->pdf->setY(50);
 
 $this->pdf->MultiCell(160,5,uni("1. Bonvolu tralegi c^i tiun folion kaj kontrolu, c^u la datumoj (nomo, adreso, telefonnumero ktp.) g^ustas."),0,'L');
 $this->pdf->MultiCell(160,5,uni("2. Se vi trovas eraron au^ se mankas informoj (ekz. naskig^dato) skribu la g^ustan informon dekstre apud la malg^usta (au^ mankanta)."),0,'L');
 $this->pdf->MultiCell(160,5,uni("3. Notu sur tiu c^i folio, je kioma horo estas noktomezo en via hejmurbo lau^ la c^i-loka tempo."),0,'L');
 $this->pdf->MultiCell(160,5,uni("4. Atendu en la antau^halo g^is ni alvokos vin au^ vian atendnumeron."),0,'L');

 $this->pdf->SetFont($this->font.'D','',20);
 $this->pdf->text(85,40,'Akcepto-Folio');
 $this->pdf->setFontSize(13);
 $this->pdf->ln(5);
 $this->pdf->write(5,uni('Vi alig^is kiel:'));
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
 else
	$kajo = "";
 $this->pdf->cell($X,$Y,uni("Persona Nomo:"),0,0,'R');
 if ($partoprenoID != 0)
 {
   $this->pdf->cell($X,$Y,uni($this->esso($partoprenanto->datoj[personanomo].$kajo)),0,1,'L');
 }
 else
 	$this->pdf->ln();

 $this->pdf->cell($X,$Y,uni("Nomo:"),0,0,'R');
 if ($partoprenoID != 0)
 {
 $this->pdf->cell($X,$Y,uni($this->esso($partoprenanto->datoj[nomo])),0,1,'L');
 }  
  else
 	$this->pdf->ln();
 $this->pdf->SetFont($this->font,'',13);
  
 $this->pdf->cell($X,$Y,uni("Adresaldonaj^o:"),0,0,'R');
 if ($partoprenoID != 0)
 {
 $this->pdf->cell($X,$Y,uni($partoprenanto->datoj[adresaldonajxo]),0,1,'L');
 }
 else
	$this->pdf->ln();

 $this->pdf->cell($X,$Y,uni("Strato:"),0,0,'R');
 if ($partoprenoID != 0)
 {
 $this->pdf->cell($X,$Y,uni($partoprenanto->datoj[strato]),0,1,'L');
 }
 else
	$this->pdf->ln();

 $this->pdf->cell($X,$Y,uni("Pos^tkodo kaj urbo:"),0,0,'R');
 if ($partoprenoID != 0)
 {
 $this->pdf->cell($X,$Y,uni($partoprenanto->datoj[posxtkodo].', '.$partoprenanto->datoj[urbo]),0,1,'L');
 }
 else
	$this->pdf->ln();


 $this->pdf->cell($X,$Y,uni("Lando kaj provinco:"),0,0,'R');
 if ($partoprenoID != 0)
 {
   if ($partoprenanto->datoj[sxildlando]!='')
	 $kajo=" (".$partoprenanto->datoj[sxildlando].")";
   else
	 $kajo = "";
   if ($partoprenanto->datoj[provinco]!='')
	 $kajo2=" / ".$partoprenanto->datoj[provinco];
   else
	 $kajo2 = "";
   $this->pdf->cell($X,$Y,
					uni($partoprenanto->landonomo().$kajo.$kajo2),
					0,1,'L');
 }
 else
	$this->pdf->ln();

 $this->pdf->cell($X,$Y,uni("Telefonnumero:"),0,0,'R');
 if ($partoprenoID != 0) 
    $this->pdf->cell($X,$Y,uni($partoprenanto->datoj[telefono]),0,1,'L');
 else
	$this->pdf->ln();

 $this->pdf->cell($X,$Y,uni("Telefakso:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,uni($partoprenanto->datoj[telefakso]),0,0,'L');
 else
	$this->pdf->cell($X, $Y, "", 0,0,'L');

 $this->pdf->cell($X,$Y,uni("Partoprentempo:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,uni(substr($partopreno->datoj[de],8,2).
                              "a g^is ".substr($partopreno->datoj[gxis], 8,2)."a"),
                    0,1,'L');
 else
	$this->pdf->ln();

 $this->pdf->cell($X,$Y,uni("Retpos^tadreso:"),0,0,'R');
 
 if ($partoprenoID != 0) 
  {
  $i=23;
  $this->pdf->setFontSize(13);
  $epost = uni($partoprenanto->datoj[retposxto]);
  while ($this->pdf->GetStringWidth($epost)>60)
  {
     $i--;
     $this->pdf->setFontSize($i);
  }  
 $this->pdf->cell($X,$Y,uni($partoprenanto->datoj[retposxto]),0,0,'L');
 }
 else
	$this->pdf->cell($X, $Y, "", 0,0,'L');
 $this->pdf->setFontSize(13);
     
 $this->pdf->cell($X,$Y,uni("Partoprentagoj:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,uni($ko->partoprentagoj),0,1,'L');
 else
	$this->pdf->ln();
  
 $this->pdf->cell($X,$Y,uni("Naskig^dato:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,uni($partoprenanto->datoj[naskigxdato]),0,0,'L');
 else
	$this->pdf->cell($X, $Y, "", 0,0,'L');

 $this->pdf->cell($X,$Y,uni("Memzorganto:"),0,0,'R');
 if ($partoprenoID != 0) 
 {
   if ($partopreno->datoj[domotipo]=='M')
	 {
		$domo="jes";$domotipo='memzorgantejo';
	 }
   else
	 {
		$domo="ne";$domotipo='junulargastejo';
	 }
 $this->pdf->cell($X,$Y,$domo,0,1,'L');
 }
 else
	$this->pdf->ln();

 $this->pdf->cell($X,$Y,uni("Sekso:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,uni($partoprenanto->sekso),0,0,'L');
 else
	$this->pdf->cell($X, $Y, "", 0,0,'L');

 $this->pdf->cell($X,$Y,uni("Mang^maniero:"),0,0,'R');
 if ($partoprenoID != 0) 
  {
	if ($partopreno->datoj[vegetare]=='J')
	  {
		$vegi="vegetare";
	  }
	else if ($partopreno->datoj[vegetare]=='A')
	  {
		$vegi="vegane";
	  }
	else  if ($partopreno->datoj[vegetare]=='N')
	  {
		$vegi = "viande";
	  }
	else
	  {
		$vegi = "";
	  }
	$this->pdf->cell($X,$Y,$vegi,0,1,'L');
 }
 else
	$this->pdf->ln();

 $this->pdf->ln(3);

 $this->pdf->write(5, uni("Je kioma horo estas noktomezo en via hejmurbo".
						  " lau^ la c^i-loka tempo?"));
 $this->pdf->ln(10);

 $this->pdf->setFontSize(10);
 $this->pdf->multicell(170,4.7, uni("Mi konscias, ke fumado estas malpermesata en la tuta".
				    " junulargastejo.\n".
				    "Mi promesas ke mi ne fumos en la junulargastejo".
				    "kaj ankau^ ekstere ne fumos \nproksime al la".
								  " pordoj kaj fenestroj.\n"));

 $this->pdf->setX(50);
 $this->pdf->write(10, "subskribo:"); 
 $this->pdf->ln(15);

 $this->pdf->setFontSize(12);

 
 $X=40;
 $this->pdf->cell($X,$Y,uni("Kotizo"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->bazakotizo-$ko->landarabato,2) .
					uni(" E^"),0,1,'R');
 else
	$this->pdf->ln();


 $this->pdf->cell($X,$Y,uni("Rabato"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->rabato,2).uni(" E^"),0,1,'R');
 else
	$this->pdf->ln();

 $this->pdf->cell($X,$Y,uni("Krompago"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->krompago,2).uni(" E^"),0,1,'R');
 else
	$this->pdf->ln();

 $this->pdf->ln(4);
 
 $this->pdf->cell($X,$Y,uni("Pagenda kotizo"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->kotizo,2).uni(" E^"),0,1,'R');
 else
	$this->pdf->ln();

 $this->pdf->cell($X,$Y,uni("Antau^pago"),0,0,'R');
 if ($partoprenoID != 0) 
 {
   $this->pdf->cell($X,$Y,number_format($ko->antauxpago,2).uni(" E^"),0,0,'R');
 
   $this->pdf->cell(30,$Y,uni("alvenis je la:"),0,0,'R');
   $this->pdf->cell(30,$Y,$ko->antauxpagdato,0,1,'R');
 }
 else
	$this->pdf->ln();
 
 $this->pdf->cell($X,$Y,uni("Membrokotizo"),0,0,'R');
// TODO: Überlegen, was tun 
// $this->pdf->cell($X,$Y,number_format(0.00,2)." EUR",0,1,'R');

 $this->pdf->ln(10);

 $this->pdf->cell($X,$Y,uni("Pagenda"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->pagenda,2).uni(" E^"),0,1,'R');
 else
	$this->pdf->ln();

 $this->pdf->rect(160,15,30,15);
 
 
 $this->pdf->rect(20,265,30,15);
 $this->pdf->rect(90,265,30,15);
 $this->pdf->rect(160,265,30,15);
 
 $this->pdf->setFontSize(8);
 $this->pdf->text(31,268,"pagis");
 $this->pdf->text(97,268,"enkomputiligita");
 $this->pdf->text(170,268,uni("C^ambro"));

 $this->pdf->text(168,18,"Atendnumero");

 $this->pdf->SetLineWidth(0.6);

 // linio antaux "vi aligxis kiel:"
 $this->pdf->line(20,87,190,87);

 // linioj antaux kaj post "kiam noktomezo":
 $this->pdf->line(20,163,190,163);
 $this->pdf->line(20,170,190,170);

 // linio por la subskribo
 $this->pdf->line(70,197,140,197);

 // linio antaux "Pagenda kotizo"
 $this->pdf->line(30,223,100,223);

 // linio antaux "Pagenda"
 $this->pdf->line(30,244,100,244);

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
