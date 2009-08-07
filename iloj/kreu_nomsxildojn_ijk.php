<?php

 /**
  * Difinas la klason Nomŝildo por kreado de nomŝildoj.
  *
  *
  * @package aligilo
  * @subpackage iloj
  * @author Martin Sawitzki, Paul Ebermann
  * @version $Id$
  * @copyright 2001-2004 Martin Sawitzki, 2004-2009 Paul Ebermann
  *      ekde 2009 en kreu_nomsxildoj_ijk.php, antauxe kreu_nomsxildojn.php.
  *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
  */




  /**
   */
require_once($GLOBALS['prafix'].'/iloj/tcpdf_php4/tcpdf.php');
  

/**
 * Kreado de nomŝildoj.
 * Varianto por IJK, kie ni nur devas meti tekstojn fonbildon.
 */
 class Nomŝildo
 {

   var $deltaX = 94;
   var $deltaY = 55;


   // adaptenda laŭ printilo:
   var $orgX = 16; 
   var $orgY = 10;

   var $maxY = 260;
   var $maxX = 150;

   var $spegula_orgX = 13;
   //martin -adaptita al 20 au x 12 --origina valoro 16

   var $x, $y;

   var $pdf;

   var $fonbildo;
   
   var $kadro;
   
   function Nomŝildo()
   {
	  $this->fonbildo = ($GLOBALS['prafix'] . "/bildoj/nomsxildo-fono-ijk2009.png");

	 $this->pdf=new TCPDF();
	 $this->pdf->setAutoPageBreak(false);

	 //	 $tiparo = 'dejavuserif';
	 $tiparo = 'freesans';

	 $this->pdf->AddFont($tiparo, '',  $tiparo.'.php');
	 $this->pdf->AddFont($tiparo, 'B', $tiparo.'b.php');

	 $this->pdf->SetFont($tiparo,'');
	 $this->pdf->Open();
     $this->pdf->SetPrintHeader(false);
     $this->pdf->SetPrintFooter(false);
	 $this->pdf->AddPage(); 

	 $this->x = $this->orgX;
	 $this->y = $this->orgY;

	$this->kadro = true;

   }


   /**
	* kreas nomŝildon.
	* @param int $partoprenoID la identigilo de la partopreno. Se 0,
	*    kreas malplenan nomŝildon. Se -1, kreas specialan nomŝildon.
	* @param int $partoprenantoID - la identigilo de la partoprenanto, aŭ
	*    de la speciala nomŝildo.
	*/
 function kreu_nomsxildon($partoprenantoID,$partoprenoID,$savu)
 {

   if ($partoprenoID == -1)
	 {
	   $dungito= new Speciala_Nomsxildo($partoprenantoID);
	   echo "<!--";
	   var_export($dungito);
	   echo "-->";
	   $this->kreu_nomsxildon_interne($dungito->datoj['titolo_esperante'],
									  $dungito->datoj['nomo'],
									  $dungito->datoj['funkcio_esperante'],
									  20, 0, 0);
	   if ($savu=="J")
		 {
		   $dungito->datoj['havasNomsxildon']='P';
		   $dungito->skribu();
		 }
	 }
   else if ($partoprenoID == 0)
	 {
	   $this->kreu_nomsxildon_interne($x, $y, "","", "", 0, 0);
	   
	 }
   else
	 {
	   $partopreno = new Partopreno($partoprenoID);
	   $partoprenanto = new Partoprenanto($partoprenantoID);
   
	   if ($savu=="J")
		 {
		   $partopreno->datoj[havasNomsxildon]='P';
		   $partopreno->skribu();
		 }
	   if ($partoprenanto->datoj['sxildnomo']) {
		 $ĉefnomo = $partoprenanto->datoj['sxildnomo'];
		 $malĉefnomo =
		   $partoprenanto->datoj['personanomo']. " " .
		   $partoprenanto->datoj['nomo'];
	   }
	   else {
		 $ĉefnomo = $partoprenanto->datoj['personanomo'];
		 $malĉefnomo = $partoprenanto->datoj['nomo'];
	   }
	   

	   $this->kreu_nomsxildon_interne($ĉefnomo, $malĉefnomo,
									  ( $partoprenanto->datoj['sxildlando'] ?
										$partoprenanto->datoj['sxildlando'] :
										eltrovu_landon($partoprenanto->datoj['lando'])
										),
									  $partopreno->datoj['de'],
									  $partopreno->datoj['gxis']
									  );
	 }
 }

 
 /**
  * kreas nomŝildon el la menciitaj informoj.
  * @param eostring $cxefnomo
  * @param eostring $malcxefnomo
  * @param eostring $landonomo
  * @param datostring $de
  * @param datostring $gxis
  */
 function kreu_nomsxildon_interne($cxefnomo, $malcxefnomo, $landonomo,
								  $de, $gxis)
   {

	 $x = $this->x;
	 $y = $this->y;
	 $pdf = &$this->pdf;
	 $ĉefnomo = uni($cxefnomo);
	 $malĉefnomo = uni($malcxefnomo);
	 $ŝildlando = uni($landonomo);

	 $nomo_larĝeco = 49.57;
	 $lando_larĝeco = 30.50;

	 $pdf->image($this->fonbildo,
				 $x, $y, 85, 55);

	 $pdf->SetLineWidth(0.1);
	 if($this->kadro) 
	 	$pdf->rect($x,$y,85,55); // cxirkauxa kadro

	 	 

	 $pdf->setFont('', 'B');
	 $ĉef_grandeco = $this->malgrandigu($ĉefnomo, $nomo_larĝeco, 20);
	 $pdf->setFont('', '');
	 $malĉef_grandeco = $this->malgrandigu($malĉefnomo, $nomo_larĝeco, 9);

	 if ($ĉef_grandeco < $malĉef_grandeco)
	 {
	   $malĉef_grandeco = $ĉefgrandeco;
	 }

	 $pdf->setFont('', 'B', $ĉef_grandeco);
	 $pdf->setXY($x + 33.00, $y + 13.69);
	 $pdf->cell($nomo_larĝeco, 9.52, $ĉefnomo, 0, 0, 'R');


	 $pdf->setFont('', '', $malĉef_grandeco);
	 $pdf->setXY($x + 33.00, $y + 23.50);

	 $pdf->Cell($nomo_larĝeco, 4.51, $malĉefnomo, 0, 0, 'R');

	 $pdf->setFont('', '');
	 $this->malgrandigu($ŝildlando, $lando_larĝeco, 12);
	 $pdf->setXY($x + 53.00, $y+32.00);
	 $pdf->Cell($lando_larĝeco, 6.00, $ŝildlando, 0, 0, 'R');

	 return;

   }

/**
 * eltrovas, kiom necesas malgrandigi la tiparon, por ke iu teksto
 *  havu spacon en la loko, kie ĝi estu.
 * @param u8string $teksto la printenda teksto
 * @param float $largxeco tiom da spaco estas (en milimetroj).
 * @param int $orgGrandeco la originala tipar-grandeco. Ne iĝos pli granda,
 *      nur malpli.
 * @return int la uzenda tipar-grandeco. Tiu ankaŭ estos aŭtomate jam metita
 *    post la voko de la funkcio.
 */
   function malgrandigu($teksto, $largxeco, $orgGrandeco)
   {
	 $i = $orgGrandeco + 1;
	 do
	   {
		 $i = $i - 0.5;
		 $this->pdf->setFontSize($i);
	   }
	 while ($this->pdf->GetStringWidth($teksto) > $largxeco);
	return $i;

   }


   /**
    * aldonas novan nomŝildon al la dosiero.
    *
    * @param int $pID - la identigilo de la partoprenanto aŭ
    *                   de la speciala nomŝildo (depende de $pnID).
    * @param int $pnID se 0, ni kreu malplenan nomŝildon. Se -1, ni
    *                   kreu specialan nomŝildon (kies identigilo estas
    *                   donita kiel $pID).
    *                  Alikaze estas identigilo de la partopreno.
    * @param string $savu se "J", memoru ke la nomŝildo estis kreita (por
    *                    ne denove krei ĝin poste). Alikaze "ne" aŭ "NE".
    */
   function kaju($pID,$pnID,$savu='ne')
   {
	 if ($this->y > $this->maxY)
	   {
		 $this->kreu_spegulan_paĝon();
		 $this->pdf->AddPage();
		 $this->x=$this->orgX;
		 $this->y=$this->orgY;
	   }
	 
	 $this->kreu_nomsxildon($pID,$pnID,
							$savu);
	 array_push($this->nuna_linio, array($pID, $pnID));
	 
	 $this->x += $this->deltaX;
	 if ($this->x > $this->maxX)
	   {
		 array_push($this->linio_listo, $this->nuna_linio);
		 $this->nuna_linio = array();

		 $this->y += $this->deltaY;
		 $this->x= $this->orgX;
	   }
   }

   var $linio_listo = array();
   var $nuna_linio = array();



   function kreu_spegulan_paĝon()
   {
	 if(count($this->nuna_linio) > 0) {
	   array_push($this->linio_listo, $this->nuna_linio);
	   $this->nuna_linio = array();
	 }

	$this->kadro = false;

	 $this->pdf->AddPage();
	 $this->y = $this->orgY;
	 foreach($this->linio_listo AS $linio)
	   {
		 $this->x = $this->spegula_orgX +
		   ($this->deltaX * count($linio));
		 foreach($linio AS $ero)
		   {
			 $this->x -= $this->deltaX;
			 $this->kreu_nomsxildon($ero[0], $ero[1], false);
		   }
		 $this->y += $this->deltaY;
	   }

	 $this->linio_listo = array();
	 $this->kadro = true;
   }

 
   /**
    * kreas PDF-dosieron kaj skribas ĝin sur la diskon.
    *
    * Antaŭe ni plenigos la aktualan paĝon per malplenaj
    * nomŝildoj, se necesas.
    */
   function sendu()
   { 
       while ($this->y <= $this->maxY )
           $this->kaju(0,0);
	   $this->kreu_spegulan_paĝon();

       $this->pdf->Output($GLOBALS['prafix'] .
                          '/dosieroj_generitaj/nomsxildoj.pdf');
   }
}

