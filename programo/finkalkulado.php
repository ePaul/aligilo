<?php

/* 
 * Montras la pago-bilancojn de la unuopaj partoprenantoj
 * en grandega tabelo.
 */


// TODO: unikodigo por la nomoj.

require ("iloj/iloj.php");
require_once('iloj/fpdf/fpdf.php');
session_start();

define('FPDF_FONTPATH','./font/');


malfermu_datumaro();

if (!rajtas("administri"))
{
  ne_rajtas();
}

class finkalkulado
{

  var $pdf, $font;

  // pagxsumoj
  var $IPago, $APago, $Rabatoj, $SPago, $Skotizo, $S_membro, $S_nemembro, $resto;

  // entutaj sumoj
  var $TIPago, $TAPago, $TRabatoj, $TSPago, $TSkotizo, $TS_membro, $TS_nemembro, $Tresto;


  /**
   * Trancxas literojn de la fino de la cxeno $io, gxis
   * gxia largxeco en la aktuala tiparo de $this->pdf
   * estas malpli ol $grandeco.
   */
  function malgrandigu($io,$grandeco)
  {
	while ($this->pdf->GetStringWidth($io) > $grandeco)
	  $io=substr($io,0,strlen($io)-1);
	return $io;
  }

  /**
   * formatas $io kiel numero kun '.' kaj du post-komo-ciferoj.
   */
  function nf($io)
  {
	if ($io)
	  return number_format($io, 2, '.', '');
	else
	  return "0.    ";
  }

  function klariglinio($klarigendajxo, $klarigo)
  {
	$this->pdf->SetFont($this->font.'D','',10);
	$this->pdf->Cell(15,5, eo($klarigendajxo), 1,0,C);
	$this->pdf->SetFont($this->font,'',10);
	$this->pdf->MultiCell(150,5,eo($klarigo), 1,1,L);
  }
  
  function klarigoj()
  {
	$this->pdf->SetFont($this->font.'D','',12);
	$this->pdf->Cell (165, 8, "Klarigoj\n", 1,1,L);
	$this->klariglinio("?", "La alvenstato: v = venos, a = alvenis, m = malalig^is.");
	$this->klariglinio('T', "La nombro de partoprentagoj.");
	$this->klariglinio('I', "C^u li/s^i ricevis invitilon? (J = Jes, malplena = Ne)");
	$this->klariglinio('IPago', "Antau^pago por la invitilo. " .
							 "Tiu sumo estas forprenita de la antau^pago.");
	$this->klariglinio('APago', "La antau^pago (sen la antau^pago por la invitilo)");
	$this->klariglinio('Rabato',"Rabato, kiun li/s^i ricevis ial ajn ".
							 "(programkontribuo, KKRen, ...)");
	$this->klariglinio('IS-Kotizo',
					   "La baza IS-kotizo por lia lando- kaj alig^kategorio, inkluzivanta".
					   " kelkajn krompagojn (ekzemple invitilo, ekskurso, dulita c^ambro),".
					   " sed ne rabatojn au^ la monon en la sekvaj du kolonoj.");

	$this->klariglinio('m-kotizo',"Membrokotizo por GEJ au^ GEA (sekva jaro), kiun ".
					   "li/s^i pagis surloke.");

	$this->klariglinio('punpago',
					   "Li/s^i rifuzis membrig^i en GEJ au^ GEA kaj pro tio".
					   " devis krompagi.");

	$this->klariglinio('restas',
					   "Kiom da mono li/s^i ankorau^ devos pagi (se pozitiva) ".
					   "au^ rericevos (se negativa).");
	$this->klariglinio('pag^sumo',
					   "La sumoj de c^iuj kolonoj en tiu c^i pag^o.");
	$this->klariglinio('entute',
					   "La sumoj de c^iuj kolonoj de c^iuj pag^oj.");
	$this->pdf->Ln();

  }

  /**
   * Elprintas la kaplinion de la tabelo.
   */
  function kaplinio()
  {
	$this->pdf->SetFont($this->font . 'D', '', 10);
    $this->pdf->Cell(4, 5 ,"?", 1,0,C);    

    $this->pdf->Cell(25, 5 ,"persona nomo", 1,0,L);    
    $this->pdf->Cell(25, 5 ,"nomo", 1,0,L);
    $this->pdf->Cell(4, 5 ,"T", 1,0,C);    
       
    $this->pdf->Cell(17, 5 ,eo("log^lando"), 1,0,L);     

    $this->pdf->Cell(4, 5 ,eo("I"), 1,0,C);   
    

    $this->pdf->Cell(12, 5 ,'IPago', 1,0,R);
    $this->pdf->Cell(14, 5 ,'APago', 1,0,R);
    $this->pdf->Cell(14, 5 ,'Rabato', 1,0,R);
    $this->pdf->Cell(14, 5 ,'SPago', 1,0,R);
    
    $this->pdf->Cell(15, 5 ,'IS-kotizo', 1,0,R);
	$this->pdf->Cell(15, 5, 'm-kotizo', 1,0,R);
	$this->pdf->Cell(15, 5, 'punpago', 1,0,R);
    
    $this->pdf->Cell(15, 5 ,'restas', 1,1,R);    
  }

  /**
   * Eldonas la sumojn de unu pagxo kaj
   * helpas kalkuli la entutajn sumojn.
   */
  function pagxsumo()
  {
	$this->pdf->SetFont($this->font.'D','',10);

	$this->pdf->Cell(4, 5 ,"", 'LTB',0,L);

	$this->pdf->Cell(25, 5 ,"", 'TB',0,L);    
	$this->pdf->Cell(25, 5 ,"", 'TB',0,L);
	$this->pdf->Cell(4, 5 ,"", 'TB',0,R);    
      
	// kadro nur supre, malsupre kaj dekstre
	$this->pdf->Cell(17, 5 ,eo('pag^sumo '), 'TBR',0,R);

	// tuta kadro
	$this->pdf->Cell(4, 5 ,'', 1,0,L);   
    
  
	$this->pdf->Cell(12, 5 ,$this->nf($this->IPago), 1,0,R);
	$this->TIPago+=$this->IPago;
	$this->IPago=0;
	
	$this->pdf->Cell(14, 5 ,$this->nf($this->APago), 1,0,R);    
	$this->TAPago+=$this->APago;
	$this->APago=0;
	
	$this->pdf->Cell(14, 5 ,$this->nf($this->Rabatoj), 1,0,R);    
	$this->TRabatoj+=$this->Rabatoj;
	$this->Rabatoj=0;
	
	$this->pdf->Cell(14, 5 ,$this->nf($this->SPago), 1,0,R);      
	$this->TSPago += $this->SPago;
	$this->SPago=0;
	
	$this->pdf->Cell(15, 5 ,$this->nf($this->Skotizo), 1,0,R);    
	$this->TSkotizo += $this->Skotizo;
	$this->Skotizo = 0;
	
	$this->pdf->Cell(15, 5 ,$this->nf($this->S_membro), 1,0,R);    
	$this->TS_membro += $this->S_membro;
	$this->S_membro = 0;

	$this->pdf->Cell(15, 5 ,$this->nf($this->S_nemembro), 1,0,R);    
	$this->TS_nemembro += $this->S_nemembro;
	$this->S_nemembro = 0;

	$this->pdf->Cell(15, 5 ,$this->nf($this->resto), 1,1,R);
	$this->Tresto += $this->resto;
	$this->resto = 0;

  }

  function tabellinio($partoprenanto, $partopreno)
  {
    $ko = new Kotizo($partopreno,$partoprenanto,$_SESSION["renkontigxo"]);
    $kotizo += $ko->kotizo;

	$this->pdf->SetFont($this->font,'',10);
    
    $this->pdf->Cell(4, 5 ,eo($partopreno->datoj[alvenstato]), 1,0,L);
    
    
    $this->pdf->Cell(25, 5,
					 $this->malgrandigu(eo($partoprenanto->datoj[personanomo]),23), 1,0,L);
    $this->pdf->Cell(25, 5, $this->malgrandigu(eo($partoprenanto->datoj[nomo]),23), 1,0,L);
    $this->pdf->Cell(4, 5, $ko->partoprentagoj, 1,0,R);
       
    $this->pdf->Cell(17, 5,
					 $this->malgrandigu(eo($partoprenanto->landonomo()),15),
					 1,0,L);
    if ($partopreno->datoj[invitilosendata]!='0000-00-00')
	  $aus='J';
	else
	  $aus='';
    $this->pdf->Cell(4, 5 ,eo($aus), 1,0,L);
    
    if (/*eltrovu_landokategorion($partoprenanto->datoj[lando])=='C' and*/
		$aus=='J' and $ko->antauxpago >= $ko->krominvitilo)
    {
      $this->pdf->Cell(12, 5 ,$this->nf($ko->krominvitilo), 1,0,R);   // Antauxpago por invitilo
      $this->pdf->Cell(14, 5 ,$this->nf($ko->antauxpago - $ko->krominvitilo), 1,0,R);  // resto de la antauxpago
      $this->APago += $ko->antauxpago - $ko->krominvitilo;
      $this->IPago += $ko->krominvitilo;
    }
    else
    {
      $this->pdf->Cell(12, 5 ,$this->nf(0), 1,0,R); 
      $this->pdf->Cell(14, 5 ,$this->nf($ko->antauxpago), 1,0,R);    
      $this->APago+=$ko->antauxpago;
     }
    $this->pdf->Cell(14, 5 ,$this->nf($ko->rabato), 1,0,R);      
    $this->Rabatoj +=$ko->rabato;
    $this->pdf->Cell(14, 5 ,$this->nf($ko->surlokapago), 1,0,R);      
    $this->SPago +=$ko->surlokapago;
    
	// IS-kotizo
    $aus3=$ko->kotizo+$ko->rabato - $ko->krom_membro - $ko->krom_nemembro;
	// TODO: pli gxusta kalkulo de kotizo por malaligxinto
    if ($partopreno->datoj['alvenstato']=='m')
	  $aus3='';
    $this->pdf->Cell(15, 5 ,$this->nf($aus3), 1,0,R);    
    $this->Skotizo += $aus3;

	switch($partopreno->datoj['surloka_membrokotizo'])
	  {
	  case 'j':
		$krom_membro = $ko->krom_membro;
		$this->S_membro += $ko->krom_membro;
		$krom_nemembro = '';
		break;
	  case 'k':
		$krom_membro = '';
		$krom_nemembro = $ko->krom_nemembro;
		$this->S_nemembro += $ko->krom_nemembro;
		break;
	  case 'n':
		$krom_membro = '';
		$krom_nemembro = '';
	  }
	$this->pdf->Cell(15,5,$this->nf($krom_membro), 1,0,R);
	$this->pdf->Cell(15,5,$this->nf($krom_nemembro), 1,0,R);

    $restas = $aus3+ $krom_membro + $krom_nemembro -
	  $ko->surlokapago - $ko->antauxpago - $ko->rabato;
    $this->resto += $restas;
    $this->pdf->Cell(15, 5 ,$this->nf($restas), 1,1,R);    
  } // tabellinio


  function fina_sumo()
  {
	$this->pdf->Cell(4, 5 ,"", 'LTB',0,L);

     $this->pdf->Cell(25, 5 ,"", 'TB',0,L);    
     $this->pdf->Cell(25, 5 ,"", 'TB',0,L);
     $this->pdf->Cell(4, 5 ,"", 'TB',0,R);    
      
     $this->pdf->Cell(17, 5 ,'entute ', 'TBR',0,R);
     $this->pdf->Cell(4, 5 ,'', 1,0,L);   
    
     $this->pdf->Cell(12, 5 ,$this->nf($this->TIPago), 1,0,R);
     $this->pdf->Cell(14, 5 ,$this->nf($this->TAPago), 1,0,R);    
     $this->pdf->Cell(14, 5 ,$this->nf($this->TRabatoj), 1,0,R);    
     $this->pdf->Cell(14, 5 ,$this->nf($this->TSPago), 1,0,R);      
     $this->pdf->Cell(15, 5 ,$this->nf($this->TSkotizo), 1,0,R);    
     $this->pdf->Cell(15, 5 ,$this->nf($this->TS_membro), 1,0,R);    
     $this->pdf->Cell(15, 5 ,$this->nf($this->TS_nemembro), 1,0,R);    
     $this->pdf->Cell(15, 5 ,$this->nf($this->Tresto), 1,1,R);    

  }

  /**
   * konstruilo por la objekto.
   * Gxi kreas novan $pdf-objekton kaj aldonas
   * al gxi tiparojn.
   */
  function finkalkulado()
  {
	$this->font='TEMPO';
 
	$this->pdf=new FPDF();
	$this->pdf->AddFont($this->font,'',$this->font.'.php');
	$this->pdf->AddFont($this->font.'D','',$this->font.'D.php');
  }

  /**
   * La cxefa funkcio por krei la PDF-dosieron.
   */
  function kreu_pdf($dosiernomo)
  {
	$this->pdf->Open();
	$this->pdf->AddPage();
	$this->pdf->SetFont($this->font.'D','',20);

	$this->pdf->Write(10, eo("Finkalkulo de kotizoj: ".
						 $_SESSION["renkontigxo"]->datoj[nomo].
						 " en ".$_SESSION["renkontigxo"]->datoj[loko] . "\n"));
	$this->pdf->SetFontSize(12);
	$this->pdf->Write(10, "Dato: ".date('Y-m-d')."\n");
	$this->klarigoj();
 
	//	$this->pdf->setY(40);
 
	$rezulto = sql_faru(datumbazdemando(array("p.ID" => 'antoid', "pn.ID" => 'enoid'),
										array("partoprenantoj" => "p",
											  "partoprenoj" => "pn"),
										"p.ID = pn.partoprenantoID",
										"renkontigxoID",
										array("order" => "alvenstato, personanomo, nomo")));

	$this->kaplinio();
	while ($row = mysql_fetch_assoc($rezulto))
	  {
		$partoprenanto = new Partoprenanto($row['antoid']);
		$partopreno = new Partopreno($row['enoid']);
		$this->tabellinio($partoprenanto, $partopreno);
    
		if ($this->pdf->getY()>250)
		  {
			$this->pagxsumo();
			$this->kaplinio();
			$this->pdf->AddPage();
			$this->kaplinio();  
		  }
	  }
	$this->pagxsumo();
	$this->fina_sumo();
	$this->kaplinio();
    $this->pdf->Output($dosiernomo);
	hazard_ligu($dosiernomo,"els^uti la kalkul-rezulton.","_top","jes");
  } // kreu_pdf

} // class finkalkulo

$kalk = new finkalkulado();
$kalk->kreu_pdf("dosieroj_generitaj/finkalkulo.pdf");


?>
