<?php

 /**
  * Difinas la klason "Kvitanco"
  * por krei PDF-ajn kvitancojn.
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
  
class Kvitanco
{
  var $font;
  var $x=10;
  var $y=10;
  var $pdf;

  var $lingvo;
     
  function Kvitanco($lingvo = 'eo')
  {
	$this->lingvo = $lingvo;

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
	$this->pdf->SetRightMargin(20);
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
  function kreu_kvitancon($partoprenantoID,$partoprenoID)
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

	$this->pdf->setFontSize(10);
 
	$this->pdf->text(27,20,'Persona nomo:');
	$this->pdf->text(27,25,'Familia nomo:');
 

	$this->pdf->text(130, 20, "Kvitanconumero:");


	$this->pdf->setFontSize(15);

	if ($partoprenanto->datoj['sxildnomo']!='') 
	  $kajo=" (".$partoprenanto->datoj['sxildnomo'].")";
	else
		  $kajo= "";
	$this->pdf->text(53,20,uni($partoprenanto->datoj['personanomo'] . $kajo));
	$this->pdf->text(53,25,uni($partoprenanto->datoj['nomo']));
	
	$prefikso = implode("", explode(" ", $_SESSION['renkontigxo']->datoj['mallongigo']));

	$this->pdf->text(160, 20, $prefikso . "#" . $partoprenoID);


	$this->pdf->SetFont('','B',20);


	$this->pdf->setY(38);

   
	$this->pdf->Cell(0, 10, "Kvitanco",
					 0, 1, 'C');


	$this->pdf->ln();
	$this->pdf->setFont('','', 10);

 


	$datumoj = array("igxo" => $_SESSION['renkontigxo'],
					 "anto" => $partoprenanto,
					 "eno" => $partopreno,
					 "pagoj" => array("sumo"
									  => $ko->donu_informon('pagoSumo'),
									  "valuto"
									  => CXEFA_VALUTO),
					 );

 
	$sxablono = donu_tekston("kvitanco-enkonduko");
 
	$teksto = transformu_tekston($sxablono, $datumoj);


	$this->pdf->write(5, uni($teksto));
	$this->pdf->ln();
	$this->pdf->ln();

	// $this->metu_titolon("Kotizokalkulo");


	$ko->tabelu_kotizon(new PDFKotizoFormatilo($this->pdf, $this->lingvo,
											   true, true));


	// TODO: stampo

	$this->pdf->setFontSize(10);

	$teksto2 = donu_tekston("kvitanco-elkonduko");

	$this->pdf->ln(8);
	$this->pdf->ln();
	$this->pdf->write(4.5, uni($teksto2));
	

	// TODO: eble tamen enmetu la informon, ke ni fajfis pri la resto?
   /*

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
   */
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
  $this->kreu_kvitancon($pID,$pnID);  
}  
 
function sendu()
{  
 $this->pdf->Output($GLOBALS['prafix'] . '/dosieroj_generitaj/kvitancoj.pdf');
}
}