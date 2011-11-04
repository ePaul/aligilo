<?php

 /**
  * Difinas la klason "Akceptofolio"
  * por krei PDF-ajn akceptofoliojn.
  *
  * @package aligilo
  * @subpackage iloj
  * @author Martin Sawitzki, Paul Ebermann
  * @version $Id$
  * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
  *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
  */



 /**
  */


require_once($GLOBALS['prafix'] . '/iloj/tcpdf_php4/tcpdf.php');
require_once($GLOBALS['prafix'] . '/tradukendaj_iloj/trad_htmliloj.php');
  
 class Akceptofolio
 {
     var $font;
     var $x=10;
     var $y=10;
     var $pdf;
     
     function Akceptofolio()
     {
         $this->pdf=new TCPDF("P", "mm", "A4");
         if (DEBUG)
             {
                 echo "<!-- FPDF_FONTPATH: " . FPDF_FONTPATH . "\n-->";
             }
         $this->font = 'freesans';
         $this->pdf->AddFont($this->font,'',$this->font.'.php');
         $this->pdf->AddFont($this->font,'B',$this->font.'b.php');
         
         $this->pdf->SetFont($this->font,'',15);
         $this->pdf->Open();

		 $this->pdf->setFillColor(255);
         
         $this->pdf->SetLeftMargin(20);
         $this->pdf->SetPrintHeader(false);
         $this->pdf->SetPrintFooter(false);
 }

 /**
  * funkcio por ŝajnigi kompatibilecon al Konfirmilo.
  * Ni nur uzas la esperantan version.
  */
 function dulingva($esperanta, $germana, $lingvo) {
     return uni($esperanta);
 }

 /**
  * transformas la tekston al unikodo.
  */
 function trans_eo($teksto) {
     return uni($teksto);
 }

 function trans_uni($teksto) {
   return $teksto;
}



 /**
  * Aldonas unuopan akceptofolion por la menciita partoprenanto/partopreno
  * al la dosiero.
  * @param int $partoprenantoID identigilo de {@link Partoprenanto} (aŭ 0
  *                             por malplena folio)
  * @param int $partoprenoID identigilo de {@link Partopreno} (aŭ 0
  *                             por malplena folio)
  */
 function kreu_akceptofolion($partoprenantoID,$partoprenoID)
 {
 
     if ($partoprenoID != 0)
         {
             $partopreno = new Partopreno($partoprenoID);
             $partoprenanto = new Partoprenanto($partoprenantoID);
             $ko = new Kotizokalkulilo($partoprenanto,
                                       $partopreno,
                                       $_SESSION["renkontigxo"],
                                       new Kotizosistemo($_SESSION["renkontigxo"]->datoj['kotizosistemo']));

			 echo "<!-- ";
			 var_export($partopreno);
			 var_export($partoprenanto);
			 echo "-->";

         }
 
 $this->pdf->setFontSize(10);
 
 $this->pdf->text(27,20,'Persona nomo:');
 $this->pdf->text(27,25,'Familia nomo:');
 


 $this->pdf->setFontSize(15);
 if ($partoprenoID != 0)
 {
   if ($partoprenanto->datoj['sxildnomo']!='') 
      $kajo=" (".$partoprenanto->datoj['sxildnomo'].")";
	else
		$kajo= "";
   $this->pdf->text(53,20,uni($partoprenanto->datoj['personanomo'] . $kajo));
   $this->pdf->text(53,25,uni($partoprenanto->datoj['nomo']));
	$this->pdf->text(115, 20, $partoprenoID);
 }



 $this->pdf->SetFont('','B',20);
 $this->pdf->text(85,38,'Akcepto-Folio');

 $this->pdf->setY(42);

 $this->pdf->setFont('','', 15);
 $this->pdf->Cell(173, 6,
                  uni("Bonvenon al la " .
                      $_SESSION['renkontigxo']->datoj['nomo'] . " en " .
                      $_SESSION['renkontigxo']->datoj['loko'] . "!"),
                  0, 0, 'C');
 $this->pdf->ln();
 $this->pdf->ln(3);

 /**
  * @todo metu la liston al konfiguro aŭ datumbazo (tekstoj?)
  */
 $farendajxoj = array(1 => "Bonvolu tralegi c^i tiun folion kaj kontrolu, ".
                      "c^u la datumoj (nomo, adreso, telefonnumero ktp.)".
                      " g^ustas.",
                      2=> "Se vi trovas eraron au^ se mankas informoj ".
                      "(ekz. naskig^dato), skribu la g^ustan informon ".
                      "dekstre apud la malg^usta (au^ mankanta).",
//                      // TODO: IS-specifaĵo.
//                      /* 3 */ "Notu sur tiu c^i folio, je kioma horo estas ".
//                      "noktomezo en via hejmurbo lau^ la c^i-loka ".
//                      "tempo.",
//                      /* 4 */ "Subskribu sube, ke vi ne fumos.",
					  );
 
 // TODO: tiu parto estu nur uzata, kiam oni havas TEJO-rabaton ...

 switch ($partopreno->datoj['tejo_membro_kontrolita']) {
 case 'j':
   // kotizo jam pagita.
   break;
 case 'n':
 case '?':
   // jen niaj viktimoj ...
   $teksto = "Lau^ niaj kontroloj vi ankorau^ ne estas individua membro de" .
	 " TEJO/UEA por 2009. Se vi decidas nun (re)alig^i kaj pagi la kotizon" .
	 " por 2009, vi ricevos la UEA-rabaton. Bonvolu plenigi la verdan" .
	 " alig^ilon kaj kunportu g^in al la akceptado, kie vi povos pagi la" .
	 " kotizon.";
   if ($partoprenanto->datoj['ueakodo']) {
	 $teksto .= " Via UEA-kodo estas " . $partoprenanto->datoj['ueakodo'] . ". Notu tiun ankaŭ en la alig^ilo.";
   }
   $teksto .= "\nSe vi jam pagis vian kotizon por tiu c^i jaro, bonvolu havi pruvilon preta.";

   $farendajxoj []= $teksto;
   break;
 case 'i':
   // ne estu en la datumbazo antaŭ printado de la akceptofolioj.
   break;
 default:
   // tute ne estu en la datumbazo
 }

 if ($partoprenanto->datoj['posxtkodo'] == "") {
   $farendajxoj[]= "Bonvolu aldoni vian pos^tkodon.";
 }

 if ($partopreno->datoj['studento'] == '?') {
   $farendajxoj[]= "C^u vi nun estas studento au^ lernanto kaj kunportis studentan legitimilon?    JES   /   NE "; 
 }

 // $farendajxoj []= "Atendu en la antau^halo g^is ni alvokos vin ".
 //  "au^ vian atendnumeron.";

 $this->pdf->setFontSize(10);

 $lo = 168; // larĝeco?
 foreach($farendajxoj AS $indekso => $teksto) {
     $this->pdf->Cell(5, 4.3, uni($indekso . ". "), 0, 0, 'R');
     $this->pdf->MultiCell($lo, 4.5, uni($teksto), 0, 'L');
 }

 $this->metu_titolon("Alig^datumoj");

 // TODO: enketo sur la dorsflanko

/*  $this->pdf->ln(); */
/*  $this->pdf->write(5,uni('Vi alig^is kiel:')); */
 
    
 $Xtit = 30;
 $Xenh = 65;
 $X=50;
 $Y=5;

 if ($partoprenoID != 0 and $partoprenanto->datoj['sxildnomo'] != '')
   {
	 $kajo=" (".$partoprenanto->datoj['sxildnomo'].")";
   }
 else
   {
	 $kajo = "";
   }

 $this->pdf->cell($Xtit,$Y,uni("Persona nomo:"),0,0,'R');
 if ($partoprenoID != 0)
 {
   $this->pdf->cell($Xenh,$Y,uni($partoprenanto->datoj['personanomo'].$kajo),0,0,'L');
 }

 // $this->pdf->ln();

 $this->pdf->cell($Xtit,$Y,uni("Familia nomo:"),0,0,'R');
 if ($partoprenoID != 0)
 {
     $this->pdf->cell($Xenh,$Y,uni($partoprenanto->datoj['nomo']),0,0,'L');
 }
 $this->pdf->ln();


 //     $this->pdf->SetFont('','',13);


 if (KAMPOELEKTO_IJK) {
     $this->pdf->cell($Xtit, $Y, uni("Adreso:"), 0, 0, 'R');
     if ($partoprenoID != 0) {
         $this->pdf->MultiCell($Xenh+$Xtit, $Y,
							   uni($partoprenanto->datoj['adreso']),
                               0, 'L', 0, 1);
     } else {
         $this->pdf->ln();
         $this->pdf->ln();
     }
 }
 else {
     
     $this->pdf->cell($Xtit,$Y,uni("Adresaldonaj^o:"),0,0,'R');
     if ($partoprenoID != 0)
         {
             $this->pdf->cell($Xenh,$Y,uni($partoprenanto->datoj[adresaldonajxo]),0,1,'L');
         }
     else
         $this->pdf->ln();
     
     $this->pdf->cell($Xtit,$Y,uni("Strato:"),0,0,'R');
     if ($partoprenoID != 0)
         {
             $this->pdf->cell($Xenh,$Y,uni($partoprenanto->datoj['strato']));
         }
	 
	 $this->pdf->ln();
   
 }

 $this->pdf->cell($Xtit,$Y,uni("Pos^tkodo kaj urbo:"),0,0,'R');
 if ($partoprenoID != 0)
 {
   $posxtkodo = $partoprenanto->datoj['posxtkodo'] or
	 $posxtkodo = "              ";
   $this->pdf->cell($Xenh,$Y,
					uni($posxtkodo . ', '.$partoprenanto->datoj['urbo']));
 }

 // $this->pdf->ln();

 
 $this->pdf->cell($Xtit,$Y,
                  (KAMPOELEKTO_IJK ?
                   uni("Lando:") : uni("Lando kaj provinco:")),
                  0,0,'R');
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
   $this->pdf->cell($Xenh,$Y,
					uni($partoprenanto->landonomo().$kajo.$kajo2),
					0,1,'L');
 }
 else
	$this->pdf->ln();

 $this->pdf->cell($Xtit,$Y,uni("Telefonnumero:"),0,0,'R');
 if ($partoprenoID != 0) {
    $this->pdf->cell($Xenh,$Y,uni($partoprenanto->datoj['telefono']));
 }
 else {
	$this->pdf->cell($Xenh, $Y);
 }

 $this->pdf->cell($Xtit,$Y, uni("UEA-kodo:"), 0,0,'R');
 if ($partoprenoID != 0 and $partoprenanto->datoj['ueakodo']) {
     $this->pdf->cell($Xenh,$Y,uni($partoprenanto->datoj['ueakodo']));
 } 
 $this->pdf->ln();


 $tuj_linioj = 1;
 if (KAMPOELEKTO_IJK) {
     $this->pdf->cell($Xtit,$Y,uni("Tujmesag^iloj:"),0,0,'R');
     if ($partoprenoID != 0) {
	   $tuj_linioj =
		 $this->pdf->MultiCell($Xenh,$Y,
							   uni($partoprenanto->datoj['tujmesagxiloj']),
							   0, 'L', 0, 0);
     }
     else {
         $this->pdf->cell($Xenh, $Y);
     }
     
 }
 else {
   $this->pdf->cell($Xtit,$Y,uni("Telefakso:"),0,0,'R');
   if ($partoprenoID != 0) {
	 $this->pdf->cell($Xenh,$Y,uni($partoprenanto->datoj['telefakso']),0,0,'L');
   }
   else {
	 $this->pdf->cell($Xenh, $Y);
   }
 }
 
 $this->pdf->cell($Xtit,$Y,uni("Partoprentempo:"),0,0,'R');
 if ($partoprenoID != 0) {
     $this->pdf->cell($Xenh,$Y,
					  uni(substr($partopreno->datoj['de'],8,2). "a g^is ".
						  substr($partopreno->datoj['gxis'], 8,2)."a"));
 }
 
 $this->pdf->ln($tuj_linioj * $Y);

 $this->pdf->cell($Xtit,$Y,uni("Retpos^tadreso:"),0,0,'R');
 
 if ($partoprenoID != 0) 
  {
	$i = 10;
	$this->pdf->setFontSize($i);
	$epost = uni($partoprenanto->datoj['retposxto']);
	while ($this->pdf->GetStringWidth($epost) > $Xenh)
	  {
		$i--;
		$this->pdf->setFontSize($i);
	  }  
	$this->pdf->cell($Xenh,$Y,uni($partoprenanto->datoj['retposxto']));
	$this->pdf->setFontSize(10);
  }
 else {
     $this->pdf->cell($X, $Y, "", 0,0,'L');
 }
     
 $this->pdf->cell($Xtit,$Y,uni("Partoprentagoj:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($Xenh,$Y,uni($ko->partoprennoktoj));
 $this->pdf->ln();
  
 $this->pdf->cell($Xtit,$Y,uni("Naskig^dato:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($Xenh,$Y,uni($partoprenanto->datoj['naskigxdato']));
 else
	$this->pdf->cell($Xenh, $Y);

 $this->pdf->cell($Xtit, $Y, uni("Log^ado:"), 0, 0, "R");
 if ($partoprenoID != 0) 
 {
   $teksto =
	 donu_konfiguran_tekston('logxtipo',
							 $partopreno->datoj['domotipo'],
							 $_SESSION['renkontigxo']->datoj['ID']);
   $this->pdf->cell($Xenh,$Y,uni($teksto));
 }

 $this->pdf->ln();

 $this->pdf->cell($Xtit,$Y,uni("Sekso:"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($Xenh,$Y,uni($partoprenanto->seksa));
 else
	$this->pdf->cell($Xenh, $Y);

 $this->pdf->cell($Xtit,$Y,uni("Mang^maniero:"),0,0,'R');
 if ($partoprenoID != 0) 
  {
	if ($partopreno->datoj['vegetare']=='J')
	  {
		$vegi="vegetare";
	  }
	else if ($partopreno->datoj['vegetare']=='A')
	  {
		$vegi="vegane";
		// IJK 2009: ni ne havas veganan manĝon.
		$vegi= "/////";
	  }
	else  if ($partopreno->datoj['vegetare']=='N')
	  {
		$vegi = "viande";
	  }
	else
	  {
		$vegi = "";
	  }
	$this->pdf->cell($Xenh,$Y,$vegi);
 }

 $this->pdf->ln();

 $this->metu_titolon("Mang^mendoj");
 $this->pdf->ln(-4);

 pdf_montru_manĝojn($this->pdf, $partopreno, $this);

 $this->metu_titolon("Kotizokalkulo");

 /*
 $this->pdf->write(5, uni("Je kioma horo estas noktomezo en via hejmurbo".
						  " lau^ la c^i-loka tempo?"));
 $this->pdf->ln(10);
 $this->dika_linio(-3);

 $this->pdf->setFontSize(10);
 $this->pdf->multicell(170,4.7, uni("Mi konscias, ke fumado estas malpermesata en la tuta".
				    " junulargastejo.\n".
				    "Mi promesas ke, mi ne fumos en la junulargastejo ".
				    "kaj ankau^ ekstere ne fumos \nproksime al la".
                                    " pordoj kaj fenestroj."), 0, 'L');

 $this->pdf->setX(70);
 $this->pdf->cell(10, 6, "subskribo:", 0, 0, 'R'); 
 $this->pdf->SetLineWidth(0.4);
 $this->pdf->cell(70, 6, "", "B");
 $this->pdf->SetLineWidth(0.2);
 $this->pdf->ln(10);
 */


 if (0 != $partoprenantoID) {
     $ko->tabelu_kotizon(new PDFKotizoFormatilo($this->pdf));

	 $this->pdf->setFontSize(12);
	 $this->pdf->setY(257);
	 // TODO: metu tuj antaŭ la kestojn
	 
	 $informoj = $ko->restas_pagenda_en_valutoj();

	 if ($informoj['traktenda']) {
	   if ($informoj['repagenda']) {
		 $this->pdf->write(5, uni("Ni repagas al vi"));
	   }
	   else {
		 $this->pdf->write(5, uni("Vi devos ankorau^ pagi al ni"));
	   }
	   foreach($informoj['listo'] AS $listero) {
		 $this->pdf->write(5, uni(" au^ "));
		 $this->pdf->setFont('', 'B');
		 $this->pdf->write(5, uni(number_format($listero['vere_pagenda'], 2, ".", "") . " " . $listero['valuto']));
		 $this->pdf->setFont('', '');
	   }
	   $this->pdf->write(5, ".");
	 } else if ($informoj['ni_fajfas']) {
	   $this->pdf->write(5, uni("La restanta mono estas tiom malmulte, ke ni " .
								"fajfas pri tio. Vi neniom plu devos pagi."));
	 } else {
	   $this->pdf->write(5, uni("Vi neniom plu devos pagi."));
	 }
 }

 /*
 $X=40;
 $this->pdf->cell($X,$Y,uni("Kotizo"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->bazakotizo-$ko->landarabato,2) .
					uni(" E^"),0,1,'R');
 else
	$this->pdf->ln();


 $this->pdf->cell($X,$Y,uni("Rabato"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,
                    number_format($ko->cxiuj_rabatoj,2).uni(" E^"),
                    0,1,'R');
 else
	$this->pdf->ln();

 $this->pdf->cell($X,$Y,uni("Krompago"),0,0,'R');
 if ($partoprenoID != 0)
   $this->pdf->cell($X,$Y,
                    number_format($ko->aliaj_krompagoj,2).uni(" E^"),
                    0,1,'R');
 else
	$this->pdf->ln();

 if (deviga_membreco_tipo != 'nenia')
     {
         $this->pdf->cell($X,$Y,
                          uni(deviga_membreco_nomo . "-Membrokotizo"),
                          0,0,'R');
         if ($partoprenoID != 0 and
             $ko->krom_membro + $ko->krom_nemembro > 0)
             {
                 $this->pdf->cell($X, $Y,
                                  number_format($ko->krom_membro +
                                                $ko->krom_nemembro) .
                                  uni(" E^"),
                                  0,1,'R');
             }
         else
             $this->pdf->ln();
     }

 if (TEJO_KOTIZO_EBLAS) {
     $this->pdf->cell($X, $Y,
                      uni("TEJO-kotizo"),
                      0,0,'R');
         if ($partoprenoID != 0 and
             $ko->kotizo_tejo > 0)
             {
                 $this->pdf->cell($X, $Y,
                                  number_format($ko->kotizo_tejo,2).uni(" E^"),
                                  0,1,'R');
             }
         else
             $this->pdf->ln();
 }

 
 // linio antaŭ "Pagenda kotizo"
 $this->pdf->SetLineWidth(0.3);
 $this->pdf->line(30,$this->pdf->getY(),100,$this->pdf->getY());

 // $this->pdf->ln(4);
 
 $this->pdf->cell($X,$Y,uni("Pagenda kotizo"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->kotizo,2).uni(" E^"),0,1,'R');
 else
	$this->pdf->ln();


 $this->pdf->cell($X,$Y,uni("Antau^pago"),0,0,'R');
 if ($partoprenoID != 0) 
 {
   $this->pdf->cell($X,$Y,
                    number_format($ko->antauxpago + $ko->surlokpago,2) .
                    uni(" E^"),
                    0,0,'R');
 
   $this->pdf->cell(30,$Y,uni("alvenis je la:"),0,0,'R');
   $this->pdf->cell(30,$Y,$ko->antauxpagdato,0,1,'R');
 }
 else
	$this->pdf->ln();

 // linio antaŭ "Pagenda"
 $this->pdf->line(30,$this->pdf->getY(),100,$this->pdf->getY());
 // $this->pdf->line(30,244,100,244);

 
 $this->pdf->cell($X,$Y,uni("Pagenda"),0,0,'R');
 if ($partoprenoID != 0) 
   $this->pdf->cell($X,$Y,number_format($ko->pagenda,2).uni(" E^"),0,1,'R');
 else
	$this->pdf->ln();
 */



 

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


 // // linioj antaŭ kaj post "kiam noktomezo":
 // $this->pdf->line(20,163,190,163);
 // $this->pdf->line(20,170,190,170);
 //
 // // linio por la subskribo
 // $this->pdf->line(70,197,140,197);


  if (0 == $partoprenantoID)
  {
      eoecho( " faris malplenan akceptfolion<br/>\n");
  }
 
} 

 function dika_linio($deltaY=0)
 {
     $this->pdf->SetLineWidth(0.6);
     $y = $this->pdf->getY() + $deltaY;
     $this->pdf->line(20,$y,190,$y);
     $this->pdf->SetLineWidth(0.2);
 }

 function metu_titolon($teksto)
 {
   $this->pdf->ln(2);
   $teksto = uni($teksto);
   $titollarĝeco = $this->pdf->GetStringWidth($teksto);
   $y = $this->pdf->getY() + 2;
   $this->pdf->setLineWidth(0.6);
   $this->pdf->line(20,$y,190,$y);
   $this->pdf->setLineWidth(0.2);
   $this->pdf->Cell(5);
   $this->pdf->Cell($titollarĝeco + 3, 6, $teksto, 1, 1, 'C', 1);
   $this->pdf->ln(2);
 }

 
 // TODO: elpensu pli taŭgan nomon.
function kaju($pID,$pnID)
{
  echo "($pID, $pnID)";
  $this->pdf->AddPage();
  $this->kreu_akceptofolion($pID,$pnID);  
}  
 
function sendu()
{  
 $this->pdf->Output($GLOBALS['prafix'] . '/dosieroj_generitaj/akceptofolioj.pdf');
}
}