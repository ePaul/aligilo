<?php

/* 
 * Montras la pago-bilancojn de la unuopaj partoprenantoj
 * en grandega tabelo.
 */


// TODO!: unikodigo por la nomoj, adapto al nova kotizosistemo

require_once ("iloj/iloj.php");

session_start();


require_once($GLOBALS['prafix'] . '/iloj/tcpdf_php4/tcpdf.php');

malfermu_datumaro();

if (!rajtas("administri"))
{
  ne_rajtas();
}

class finkalkulado
{


    var $kotizosistemo;

  var $pdf, $font;

  // pagxsumoj
  var $IPago, $APago, $Rabatoj, $SPago, $Skotizo, $S_membro, $S_nemembro, $resto, $S_Tkotizo;

  // entutaj sumoj
  var $TIPago, $TAPago, $TRabatoj, $TSPago, $TSkotizo, $TS_membro, $TS_nemembro, $Tresto, $TS_Tkotizo;

  var $krom_pagxsumo, $krom_tutsumo;


  // nova varianto

  /**
   * array(
   *     kodo => array(titolo, kampo-largxeco, klarigo)
   */
  var $kampoj = array("alvenstato" => array("?",4,"La alvenstato: v = venos, a = akceptig^is, m = malalig^is., i = vidita, sed ne akceptig^is, n = ne venis/venos, sed ne malalig^is", 'teksto'),
                      "nomo_pers" => array("persona nomo",25,0, 'teksto'),
                      "nomo_fam" => array("familia nomo",25,0, 'teksto'),
                      "noktoj" => array("T",4, "La nombro de partoprennoktoj.", 'teksto'),
                      "lando" => array("log^lando",17,0, 'teksto'),
                      "invitilo" => array("I",4, "C^u li/s^i ricevis invitilon? (J = Jes, malplena = Ne)", 'teksto'),
                      "antauxpago" => array("APago",14, "La antau^pagoj (= c^iuj ne-surlokaj pagoj antaux la fino de la partopreno)"),
                      "surlokaPago" => array("SPago",16, "La 'surlokaj' pagoj (c^iuj pagoj al kaj repagoj de la IS-kaso)."),
                      "postaPago" => array("PPago",13,"Pagoj post la fino de la partopreno"),
                      "pagoSumo" => array("PSumo",16,"sumo de 'APago', 'SPago' kaj 'PPago'."),
                      "kotizo" => array("IS-kot.",16,"La baza IS-kotizo por lia lando- kaj alig^kategorio kaj partoprennoktoj, sen rabatoj kaj krompagoj."),
                      "rabatoj" => array("rabato",14,"Rabatoj entute."),
                      "krompagoj_gxeneralaj" => array("IS-kp.",15,"Krompagoj IS-rilataj (ekzemple unu-/dulita c^ambro, invitilo, aldona mang^kupono ktp.)"),
                      "TEJOkotizo" => array("TEJO-k.",15,"Membrokotizo au^ aliaj pagoj al TEJO/UEA, kiun li pagis surloke al la IS-kaso."),
                      "GEAkotizo" => array("GEA-k.",15,"Membrokotizo por GEA (sekva jaro), kiun li pagis surloke al la IS-kaso."),
                      "GEJkotizo" => array("GEJ-k.",15,"Membrokotizo por GEJ (sekva jaro), kiun li pagis surloke al la IS-kaso."),
                      "punpago" => array("punpago",15,"Li/s^i rifuzis membrig^i en GEJ au^ GEA kaj pro tio devis krompagi."),
                      "kSumo" => array("k.-sumo",16, "sumo de kotizo kaj krompagoj (kun membrokotizoj), minus rabatoj."),
                      "restas" => array("restas",15,"difereco de 'kot.-sumo' kaj 'PSumo'. Estu 0 post la postrenkontig^a prilaborado."),
                      );
  var $pagxsumoj = array();
  var $tutsumoj = array();


  /**
   * Trancxas literojn de la fino de la cxeno $io, gxis
   * gxia largxeco en la aktuala tiparo de $this->pdf
   * estas malpli ol $grandeco.
   */
  function malgrandigu($io,$grandeco)
  {
      if ($this->pdf->GetStringWidth($io) > $grandeco)
      {
          $nova = $io;
          do {
              $nova=substr($nova,0,-1); // fortrancxu lastan literon
          }
          while ($this->pdf->GetStringWidth($nova . ".") > $grandeco);
          return $nova . ".";
      }
      else 
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
	$this->pdf->SetFont('','B',10);
	$this->pdf->Cell(16,5, uni($klarigendajxo), 1,0,'C');
	$this->pdf->SetFont('','',10);
	$this->pdf->MultiCell(150,5,uni($klarigo), 1,1,'L');
  }
  
  function klarigoj()
  {
	$this->pdf->SetFont('','B',12);
	$this->pdf->Cell (166, 8, "Klarigoj", 1,1,L);
    $this->pdf->ln(1);
    foreach($this->kampoj AS $ero) {
        if ($ero[2]) {
            $this->klariglinio($ero[0], $ero[2]);
        }
    }
    $this->pdf->ln(1);
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
	$this->pdf->SetFont('', 'B', 9.5);
    
    foreach($this->kampoj AS $ero) {
        $this->pdf->Cell($ero[1], 5, uni($ero[0]), 1, 0, 'C');
    }
    $this->pdf->ln();


    /*

    $this->pdf->Cell(4, 5 ,"?", 1,0,'C');



    $this->pdf->Cell(25, 5 ,"persona nomo", 1,0,'L');
    $this->pdf->Cell(25, 5 ,"nomo", 1,0,'L');
    $this->pdf->Cell(4, 5 ,"T", 1,0,'C');
    
    $this->pdf->Cell(17, 5 ,uni("log^lando"), 1,0,'L');

    $this->pdf->Cell(4, 5 ,uni("I"), 1,0,'C');
    

    //    $this->pdf->Cell(12, 5 ,'IPago', 1,0,'C');
    $this->pdf->Cell(14, 5 ,'APago', 1,0,'C');
    $this->pdf->Cell(14, 5 ,'Rabato', 1,0,'C');
    $this->pdf->Cell(14, 5 ,'SPago', 1,0,'C');
    
    $this->pdf->Cell(15, 5 ,'IS-kotizo', 1,0,'C');
    $krompagolisto = $this->kotizosistemo->donu_krompagoliston();
    foreach($krompagolisto AS $ero) {
        $tipdatoj = &$ero['tipo']->datoj;
        if('j' == $tipdatoj['uzebla']) {
            // provizore tiel ... TODO: poste eble mallongigo
            $this->pdf->Cell(15,5, uni($ero['tipo']->datoj['mallongigo']), 1,0,'C');
        }
    }
	$this->pdf->Cell(15, 5, 'T-kotizo', 1,0,'C');
	$this->pdf->Cell(15, 5, 'm-kotizo', 1,0,'C');
	$this->pdf->Cell(15, 5, 'punpago', 1,0,'C');
    
    $this->pdf->Cell(15, 5 ,'restas', 1,1,'C');
    */
  }

  /**
   * Eldonas la sumojn de unu pagxo kaj
   * helpas kalkuli la entutajn sumojn.
   */
  function pagxsumo()
  {

	$this->pdf->SetFont('','B',10);

	$this->pdf->Cell($this->komenca_largxeco, 5 ,uni('pag^sumo '), 1,0,R);

    //	$this->pdf->Cell(17, 5 ,uni('pag^sumo '), 'TBR',0,R);
	$this->pdf->SetFont('', 'B',9.5);

    foreach($this->kampoj AS $kodo => $ero) {
          if ($ero[3]) {
          }
          else {
              $this->tabelcxelo_nombro($this->pagxsumoj[$kodo],
                                       $ero);
              $this->tutsumoj[$kodo] += $this->pagxsumoj[$kodo];
              $this->pagxsumoj[$kodo] = 0;
          }
      }
    $this->pdf->ln();

    /*
	// tuta kadro
	$this->pdf->Cell(4, 5 ,'', 1,0,L);   
    
	$this->pdf->SetFont('', 'B',9.5);
  
// 	$this->pdf->Cell(12, 5 ,$this->nf($this->IPago), 1,0,R);
// 	$this->TIPago+=$this->IPago;
// 	$this->IPago=0;
	
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

    $krompagolisto = $this->kotizosistemo->donu_krompagoliston();
    foreach($krompagolisto AS $ero) {
        $tipdatoj = &$ero['tipo']->datoj;
        if('j' == $tipdatoj['uzebla']) {
            $this->pdf->Cell(15,5, $this->nf($this->krom_pagxsumo[$tipdatoj['ID']]),
                             1,0,'R');
            $this->krom_tutsumo[$tipdatoj['ID']] += $this->krom_pagxsumo[$tipdatoj['ID']];
            $this->krom_pagxsumo[$tipdatoj['ID']] = 0;
        }
    }

	$this->pdf->Cell(15, 5 ,$this->nf($this->S_Tmembro), 1,0,R);    
	$this->TS_Tmembro += $this->S_Tmembro;
	$this->S_Tmembro = 0;

	
	$this->pdf->Cell(15, 5 ,$this->nf($this->S_membro), 1,0,R);    
	$this->TS_membro += $this->S_membro;
	$this->S_membro = 0;

	$this->pdf->Cell(15, 5 ,$this->nf($this->S_nemembro), 1,0,R);    
	$this->TS_nemembro += $this->S_nemembro;
	$this->S_nemembro = 0;

	$this->pdf->Cell(15, 5 ,$this->nf($this->resto), 1,1,R);
	$this->Tresto += $this->resto;
	$this->resto = 0;
    */
  }

  function tabelcxelo_teksto($teksto, $ero) {
      $this->pdf->Cell($ero[1], 5,
                       $this->malgrandigu(uni($teksto), $ero[1]),
                       1,0,'C');
  }

  function tabelcxelo_nombro($nombro, $ero) {
      $this->pdf->Cell($ero[1], 5,
                       $this->nf($nombro),
                       1,0,'R');
      
  }



  function tabellinio($partoprenanto, $partopreno)
  {
    $ko = new Kotizokalkulilo($partoprenanto,$partopreno,
                              $_SESSION["renkontigxo"],
                              $this->kotizosistemo);

	$this->pdf->SetFont('', '',9);


    foreach($this->kampoj AS $kodo => $ero) {
        $datumo = $ko->donu_informon($kodo);
        if ($ero[3]) {
            $this->tabelcxelo_teksto($datumo, $ero);
        }
        else {
            $this->tabelcxelo_nombro($datumo, $ero);
            $this->pagxsumoj[$kodo] += $datumo;
        }
    }

    /*

    
    $this->pdf->Cell(4, 5 ,uni($partopreno->datoj['alvenstato']), 1,0,'C');
    
    
    $this->pdf->Cell(25, 5,
					 $this->malgrandigu(uni($partoprenanto->datoj['personanomo']),23), 1,0,L);
    $this->pdf->Cell(25, 5, $this->malgrandigu(uni($partoprenanto->datoj['nomo']),23), 1,0,L);
    $this->pdf->Cell(4, 5, $ko->partoprennoktoj, 1,0,'R');
       
    $this->pdf->Cell(17, 5,
					 $this->malgrandigu(uni($partoprenanto->landonomo()),16),
					 1,0,L);
    $invitpeto = $partopreno->sercxu_invitpeton();
    if ($invitpeto->datoj['invitletero_sendenda']=='j')
	  $aus='J';
	else
	  $aus='';
    $this->pdf->Cell(4, 5 ,uni($aus), 1,0,L);

	$this->pdf->SetFont('', '',9.5);

    
    $this->pdf->Cell(14, 5 ,$this->nf($ko->antauxpagoj), 1,0,R);    
    $this->APago+=$ko->antauxpagoj;

    $this->pdf->Cell(14, 5 ,$this->nf($ko->rabatoj), 1,0,R);      
    $this->Rabatoj +=$ko->rabatoj;
    
    $this->pdf->Cell(14, 5 ,$this->nf($ko->surlokaj_pagoj), 1,0,R);      
    $this->SPago +=$ko->surlokaj_pagoj;
    
    //	// IS-kotizo
    //    $aus3=$ko->kotizo+$ko->rabato - $ko->krom_membro - $ko->krom_nemembro;
    //	// TODO!: pli gxusta kalkulo de kotizo por malaligxinto
    //    if ($partopreno->datoj['alvenstato']=='m')
    //	  $aus3='';
    $aus3 = $ko->partakotizo;

    $this->pdf->Cell(15, 5 ,$this->nf($aus3), 1,0,R);    
    $this->Skotizo += $aus3;

    $krompagoj = $ko->krompagolisto_diversaj;
    
    foreach($krompagoj AS $ero) {
        
        $tipdatoj = &$ero['tipo']->datoj;
        if('j' == $tipdatoj['uzebla']) {
            
            $this->pdf->Cell(15,5, $this->nf($ero['pago']),
                             1,0,'R');
            $this->krom_pagxsumo[$tipdatoj['ID']] += $ero['pago'];
        }
    }

    switch($partopreno->datoj['tejo_membro_kontrolita']) {
    case 'i':
    case 'p':
        $krom_tejo = $partopreno->datoj['tejo_membro_kotizo'];
        $this->pdf->Cell(15,5, $this->nf($krom_tejo),
                         1,0,'R');
        $this->S_Tmembro += $krom_tejo;
        break;
    default:
        $this->pdf->Cell(15,5, $this->nf(0),
                         1,0,'R');
    }


	switch($partopreno->datoj['surloka_membrokotizo'])
	  {
	  case 'j':
      case 'i':
		$krom_membro = $ko->krom_loka_membrokotizo;
		$this->S_membro += $ko->krom_loka_membrokotizo;
		$krom_nemembro = '';
		break;
	  case 'k':
		$krom_membro = '';
		$krom_nemembro = $ko->krom_nemembro;
		$this->S_nemembro += $ko->krom_nemembro;
		break;
	  case 'n':
      case 'h':
      case 'a':
		$krom_membro = '';
		$krom_nemembro = '';
        break;
	  }
	$this->pdf->Cell(15,5,$this->nf($krom_membro), 1,0,R);
	$this->pdf->Cell(15,5,$this->nf($krom_nemembro), 1,0,R);

    $restas = $ko->restas_pagenda();
    $this->resto += $restas;
    $this->pdf->Cell(15, 5 ,$this->nf($restas), 1,1,R);    
    */
    $this->pdf->ln();
  } // tabellinio


  function fina_sumo()
  {


	$this->pdf->SetFont('','B',10);

	$this->pdf->Cell($this->komenca_largxeco, 5 ,uni('entute'), 1,0,R);

    //	$this->pdf->Cell(17, 5 ,uni('pag^sumo '), 'TBR',0,R);
	$this->pdf->SetFont('', 'B',9.5);

    foreach($this->kampoj AS $kodo => $ero) {
          if ($ero[3]) {
          }
          else {
              $this->tabelcxelo_nombro($this->tutsumoj[$kodo],
                                       $ero);          
          }
      }
    $this->pdf->ln();

    /*

     $this->pdf->SetFont('', 'B',10);

      $this->pdf->Cell(4, 5 ,"", 'LTB',0,L);

     $this->pdf->Cell(25, 5 ,"", 'TB',0,L);    
     $this->pdf->Cell(25, 5 ,"", 'TB',0,L);
     $this->pdf->Cell(4, 5 ,"", 'TB',0,R);    
      
     $this->pdf->Cell(17, 5 ,'entute ', 'TBR',0,R);
     $this->pdf->Cell(4, 5 ,'', 1,0,L);   
     
     $this->pdf->SetFont('', 'B',9.5);
    
     //     $this->pdf->Cell(12, 5 ,$this->nf($this->TIPago), 1,0,R);
     $this->pdf->Cell(14, 5 ,$this->nf($this->TAPago), 1,0,R);    
     $this->pdf->Cell(14, 5 ,$this->nf($this->TRabatoj), 1,0,R);    
     $this->pdf->Cell(14, 5 ,$this->nf($this->TSPago), 1,0,R);      
     $this->pdf->Cell(15, 5 ,$this->nf($this->TSkotizo), 1,0,R);
     $krompagolisto = $this->kotizosistemo->donu_krompagoliston();
     foreach($krompagolisto AS $ero) {
         $tipdatoj = &$ero['tipo']->datoj;
         if('j' == $tipdatoj['uzebla']) {
            $this->pdf->Cell(15,5, $this->nf($this->krom_tutsumo[$tipdatoj['ID']]),
                             1,0,'R');
        }
    }
     $this->pdf->Cell(15, 5 ,$this->nf($this->TS_Tmembro), 1,0,R);    
     $this->pdf->Cell(15, 5 ,$this->nf($this->TS_membro), 1,0,R);    
     $this->pdf->Cell(15, 5 ,$this->nf($this->TS_nemembro), 1,0,R);    
     $this->pdf->Cell(15, 5 ,$this->nf($this->Tresto), 1,1,R);    
    */
  }

  /**
   * konstruilo por la objekto.
   * Gxi kreas novan $pdf-objekton kaj aldonas
   * al gxi tiparojn.
   */
  function finkalkulado()
  {
      $this->kotizosistemo = new Kotizosistemo($_SESSION['renkontigxo']->datoj['kotizosistemo']);
      $this->kotizosistemo->donu_krompagoliston();
      
      
      $this->font='freesans';
      
      $this->pdf=new TCPDF('L');
      $this->pdf->AddFont($this->font,'','freesans.php');
      $this->pdf->AddFont($this->font,'B','freesansb.php');
      $this->pdf->SetFont($this->font);
      $this->pdf->SetPrintHeader(false);
      $this->pdf->SetPrintFooter(false);
      $this->pdf->SetAutoPageBreak(false, 0);
      
      $this->komenca_largxeco = 0;
      foreach($this->kampoj AS $kodo => $ero) {
          if ($ero[3]) {
              $this->komenca_largxeco += $ero[1];
          }
          else
              break;
      }

      /*
      $krompagolisto = $this->kotizosistemo->donu_krompagoliston();
      foreach($krompagolisto AS $ero) {
          $tipdatoj = &$ero['tipo']->datoj;
          if('j' == $tipdatoj['uzebla']) {
              $this->krom_tutsumo[$tipdatoj['ID']] = 0;
              $this->krom_pagxsumo[$tipdatoj['ID']] = 0;
          }
          }
      */
  }

  /**
   * La cxefa funkcio por krei la PDF-dosieron.
   */
  function kreu_pdf($dosiernomo)
  {
	$this->pdf->Open();
	$this->pdf->AddPage();
    
	$this->pdf->SetFont('','B',20);

    $this->pdf->ln(15);

	$this->pdf->Write(8, uni("Finkalkulo de kotizoj: ".
						 $_SESSION["renkontigxo"]->datoj[nomo].
						 " en ".$_SESSION["renkontigxo"]->datoj[loko] ));
    $this->pdf->Ln(10);
	$this->pdf->SetFontSize(12);
	$this->pdf->Write(10, "Dato: ".date('Y-m-d')."\n");
	$this->klarigoj();
 
 
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
    
		if ($this->pdf->getY()>180)
		  {
			$this->pagxsumo();
			$this->kaplinio();
			$this->pdf->AddPage();
            $this->pdf->ln(15);
			$this->kaplinio();
		  }
	  }
	$this->pagxsumo();
	$this->fina_sumo();
	$this->kaplinio();
    $this->pdf->Output($dosiernomo);
	hazard_ligu($dosiernomo,"els^uti la kalkul-rezulton.");
  } // kreu_pdf

} // class finkalkulo

$kalk = new finkalkulado();
$kalk->kreu_pdf($GLOBALS['prafix'] . "/dosieroj_generitaj/finkalkulo.pdf");


?>
