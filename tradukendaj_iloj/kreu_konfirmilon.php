<?php

/**
 * Tio ĉi estas kombino de
 *    kreu_konfirmilon_unikode
 * kaj
 *    kreu_konfirmilon_neunikode.
 *
 * Gxi ebligu lauxnecesan PDF-kreadon unikode aux neunikode.
 */





  // define('FPDF_FONTPATH',$prafix.'/iloj/fpdf/tiparoj/');
  //require_once($prafix . '/iloj/fpdf/fpdf.php');
//require_once($prafix . '/iloj/fpdf/ufpdf.php');
  // require_once($prafix . '/iloj/tcpdf_php4/tcpdf.php');


 
class Konfirmilo
{
  var $font;
  var $pdf;
  //  var $germane;
  //  var $unikode;


  /**
   * konstruilo.
   * Gxi kreas FPDF-objekton.
   */
  function Konfirmilo($unikode = "")
  {
	if ($unikode) // provizore, por testi.
	  {
		$this->init_unikode();
	  }
	else
	  {
		$this->init_neunikode();
	  }
  }

  function init_unikode()
  {
      require_once($GLOBALS['prafix'] . '/iloj/tcpdf_php4/tcpdf.php');
	$this->unikode = true;
	$this->pdf=new TCPDF();
	$this->font = 'freesans';
	$this->pdf->AddFont($this->font,'',$this->font.'.php');
	$this->pdf->AddFont($this->font,'B',$this->font.'b.php');
	$this->pdf->SetFont($this->font,'',15);
	$this->pdf->Open();  
	$this->pdf->SetTopMargin(0);
    $this->pdf->SetAutoPageBreak(false, 10);
    $this->pdf->SetPrintHeader(false);
    $this->pdf->SetPrintFooter(false);
  }

  function init_neunikode()
  {
    // TODO: eble uzu TCPDF sen unikodo.
    // http://sourceforge.net/forum/forum.php?thread_id=1854592&forum_id=435311
      if (!defined('FPDF_FONTPATH')) {
          define('FPDF_FONTPATH',$GLOBALS['prafix'].'/iloj/fpdf/tiparoj/');
      }
      require_once($GLOBALS['prafix'] . '/iloj/fpdf/fpdf.php');
      $this->unikode = false;
      $this->pdf=new FPDF();
      $this->font = 'TEMPO';
      $this->pdf->AddFont($this->font,'',$this->font.'.php');
      $this->pdf->AddFont($this->font,'B',$this->font.'D.php');
      $this->pdf->SetFont($this->font,'',15);
      $this->pdf->Open();  
      $this->pdf->SetTopMargin(0);
  }


  /**
   * transformas tekston al la gxusta formato
   * por doni al la PDF-libraro, depende de lingvo
   * kaj kodigo.
   * La rezulto estas unikoda (UTF-8), se $this->unikode
   * estas TRUE, alikaze en Latina-1-varianto kun
   * eo-supersignoj.
   * 
   * $esperanta - la esperanta versio de la teksto.
   *              Gxi estu en UTF-8, eble kun supersignoj
   *              en c^-kodigo.
   * $germana   - la germana versio de la teksto. Gxi
   *              estu en UTF-8.
   */
  function dulingva($esperanta, $germana, $lingvo)
  {
	if ($lingvo == "eo")
	  {
		return $this->trans_eo($esperanta);
	  }
	else
	  {
		return $this->trans_de($germana);
	  }
  }

  /**
   * transformas tekston aux al UTF-8 aux al la speciala
   * Latina-1-varianto uzata de ni, depende de $this->unikode.
   *
   * @param eostring $teksto - la teskto estu en UTF-8 kun c^-kodigo.
   * @param u8string|lat1pdfstring
   */
  function trans_eo($teksto)
  {
	if ($this->unikode)
	  {
		return uni($teksto);
	  }
	else
	  {
		return eo($teksto);
	  }
  }

  /**
   * transformas tekston aux al UTF-8 aux al la speciala
   * Latina-1-varianto uzata de ni, depende de $this->unikode.
   *
   * @param u8string $teksto
   * @return u8string|lat1pdfstring
   */
  function trans_uni($teksto) {
	if ($this->unikode)
	  {
		return $teksto;
	  }
	else
	  {
		return eo(utf8_al_eo($teksto));
	  }
  }


  /**
   * transformas tekston aux al UTF-8 aux al la speciala
   * Latina-1-varianto uzata de ni, depende de $this->unikode.
   *
   * $teksto - la teskto estu en UTF-8.
   */
  function trans_de($teksto)
  {
	if ($this->unikode)
	  {
		return $teksto;
	  }
	else
	  {
          return utf8_decode($teksto);
	  }
  }
  


  /**
   * kreas konfirmilon en unu el du lingvoj.
   * $partopreno    - Partopreno-objekto
   * $partoprenanto - la Partoprenanto-objekto
   * $renkontigxo   - Renkontigxo-objekto
   * @param Kotizokalkulilo $kotizo - Kotizo-objekto (estu kreita el la tri antauxe
   *                   menciitaj objektoj)
   * @param lingvokodo $lingvo 
   */
  function kreu_konfirmilon_unulingve($partopreno, $partoprenanto,
									  $renkontigxo, $kotizo, $lingvo)
  {

	require_once($GLOBALS['prafix'] . "/tradukendaj_iloj/trad_htmliloj.php");

	eniru_dosieron();
	eniru_lingvon($lingvo);

      // TODO!: cxio GEJ-specifa estu konfigurebla.

	$this->pdf->AddPage(); 
	$this->pdf->SetLeftMargin(20);
	$this->pdf->SetRightMargin(20);


	$this->pdf->Image($GLOBALS['prafix'] . '/bildoj/tejo-emblemo.png',
					  20, 10, 30);
	$this->pdf->Image($GLOBALS['prafix'] . '/bildoj/ijk-emblemo.png',
					  160, 10, 30);




	//	$this->pdf->Image($GLOBALS['prafix'] . '/bildoj/eo-logo.png', 162, 10, 28);
	
	$this->pdf->SetFontSize($this->unikode ? 17 : 19);
	// TODO: el datumbazo
	$this->pdf->text(50, 17, "Tutmonda Esperantista Junulara Organizo");
	$this->pdf->text(60, 26, $this->trans_eo($renkontigxo->datoj['nomo']));

	// falc- kaj truil-markiloj
	$this->pdf->line(4,100,9,100);
	$this->pdf->line(4,147,7,147);
	$this->pdf->line(4,198,9,198);
     



	// adreso de la partoprenanto
	$this->pdf->SetFont('','B',12);
	$this->pdf->setY(59);
	$this->pdf->cell(60, 5, $this->trans_eo($partoprenanto->tuta_nomo()));	
	$this->pdf->ln();
	$this->pdf->write(5, $this->trans_eo($partoprenanto->datoj['adreso']));

/* 	if ($partoprenanto->datoj[adresaldonajxo]!='') */
/* 	  { */
/* 		$this->pdf->write(5,$this->trans_eo($partoprenanto->datoj[adresaldonajxo])); */
/* 		$this->pdf->ln(); */
/* 	  } */
/* 	$this->pdf->write(5,$this->trans_eo($partoprenanto->datoj[strato])); */
	$this->pdf->ln();
	$this->pdf->write(5,$this->trans_eo($partoprenanto->datoj['posxtkodo']." ".$partoprenanto->datoj['urbo']));
	$this->pdf->ln();
	
	$this->pdf->Cell(60, 5,$this->trans_eo($partoprenanto->landonomo_en_lingvo($lingvo)));

	$this->pdf->setXY(120,60);

	if ($partoprenanto->datoj['sxildnomo']) {
	  $cxefnomo = $partoprenanto->datoj['sxildnomo'];
	  $malcxefnomo = 
		$partoprenanto->datoj['personanomo'] . " " . $partoprenanto->datoj['nomo'];
	}
	else {
	  $cxefnomo = $partoprenanto->datoj['personanomo'];
	  $malcxefnomo = $partoprenanto->datoj['nomo'];
	}

	$this->pdf->SetFont('', "B", 14);
	$this->pdf->Cell(40,10, $this->trans_eo($cxefnomo),
					 "LTR", 2, "R");
	$this->pdf->SetFont('', '', 10);
	$this->pdf->Cell(40,6, $this->trans_eo($malcxefnomo),
					 "LR", 2, "R");
	$this->pdf->Cell(40, 6, $this->trans_eo($partoprenanto->sxildlando()),
					 "LRB", 1, "R");
	


					  
 
	$this->pdf->SetFont('','',10);
	$this->pdf->setY(90);
	$this->pdf->write(5, "Saluton!");
	$this->pdf->ln();

	$this->pdf->write(5, $this->trans_eo(donu_tekston_lauxlingve("konf2-enkonduko",
																 $lingvo, $renkontigxo)));

	$this->pdf->ln();



	$this->pdf->SetLeftMargin(30);

    /*    $kotizo->montru_kotizon($lingvo == 'eo' ? 3 : 4, $this); */
    $kotizo->tabelu_kotizon(new PDFKotizoFormatilo($this->pdf, 
                                                   $lingvo,
                                                   $this->unikode));

	$this->pdf->SetLeftMargin(20);
    $this->pdf->ln(3);

 
	$this->pdf->SetFontSize(10);
    //	$this->pdf->setXY(30,102);


	if (DEBUG)
	  {
		echo "<!-- alk: [$alk] -->";
// 		echo "<!-- renkontigxo->datoj['meze']: [" . $renkontigxo->datoj['meze']. "] -->" ;
		echo "<!--";
// 		echo "\npartopreno: ";
// 		var_export($partopreno);
// 		echo "\npartoprenanto: ";
// 		var_export($partoprenanto);
		echo "\nrenkontigxo: ";
		var_export($renkontigxo);
		echo "-->";
	  }

	// TODO: tio ĉi estas speciala por IJK 2009:

	switch ($partopreno->datoj['domotipo']) {
	case 'A':
	  $mendo = CH("mendis-amaslogxejon");
	  break;
	case 'J':
	  if($partopreno->datoj['dulita'] == 'U')
		$mendo = CH("mendis-studenthejmon-unulitan");
	  else {
		if ($partopreno->datoj['cxambrotipo'] == 'u')
		  $mendo = CH("mendis-studenthejmon-dulitan-unuseksan");
		else
		  $mendo = CH("mendis-studenthejmon-dulitan-ambauxseksan");
		if ($partopreno->datoj['kunKiu'])
		  $mendo .= " " . CH("volas-logxi-kun",
							 eotransformado($partopreno->datoj['kunKiu'], 'utf-8'));
	  }
	  break;
	case 'T':
	  $mendo = CH("mendis-tendon");
	  break;
	case 'M':
	  $mendo = CH("mendis-nenion");
	  break;
	default:
	  $mendo = "-- erara domotipo: " . $partopreno->datoj['domotipo']. " --";
	  erareldono($mendo);
	}

	$teksto = $mendo;


 


    // la granda teksto, kiu konfirmas la aligxon.
	$this->pdf->SetFont('','B',10);
	$this->pdf->write(4, $this->trans_uni($teksto));
	$this->pdf->ln();


	// atentigo pri pagenda resto:
	$pagenda = $kotizo->restas_pagenda();
	echo("<!-- restas_pagenda: " . $pagenda . " -->");
    if ($pagenda > 0) {
	  list($kurzo, $dato) = eltrovu_kurzon('EUR');
	  $pagenda_eur = number_format((float)$pagenda / (float)$kurzo,
								   2, ".", "");
	  
	  $teksto = CH("restas-pagenda-CZK-EUR-dato", $pagenda, $pagenda_eur, $kurzo, $dato);

// 	  $teksto = strtr(donu_tekston_lauxlingve("konf2-kunportu-reston",
//                                                       $lingvo,
//                                                       $renkontigxo),
//                               array("{{sumo}}" => $pagenda));

        // atentigo pri kunportado de mono
        $this->pdf->SetFont('','B',8);
        $this->pdf->write(3.8, $this->trans_uni($teksto));
        $this->pdf->ln();
    }

	$this->pdf->SetFont("", "", 10);

	$this->pdf->write(5, $this->trans_uni(CH("vi-mendis-mangxojn")));
	$this->pdf->ln();
	pdf_montru_manĝojn($this->pdf, $partopreno, $this);
	


	//$this->pdf->setY(155);
	$this->pdf->SetFont('','B',11);
	$this->pdf->Write(6, $this->trans_uni(CH("gravaj-informoj")));
	$this->pdf->ln();
	$this->pdf->SetFont('','',9);
	$this->pdf->setX(25);

	$teksto = donu_tekston_lauxlingve("konf2-gravaj-informoj", $lingvo, $renkontigxo);

/* 	if ($partopreno->datoj['agxo']< 18) */
/* 	  $teksto .= " " . donu_tekston_lauxlingve("konf2-junulo", $lingvo, $renkontigxo); */
/* 	if ($domotipo=='junulargastejo' and $cioenordo == 'jes') */
/* 	  { */
/* 		$teksto .= " " . donu_tekston_lauxlingve("konf2-21a-horo", $lingvo, $renkontigxo); */
/* 		//aus der DB zaubern */
/* 	  } */
/* 	else if ($domotipo=='memzorgantejo') */
/* 	  { */
/* 		$teksto .= " " . donu_tekston_lauxlingve("konf2-memzorganto", $lingvo, $renkontigxo); */
/* 	  } */
/* 	if ($partoprenanto->datoj['lando']==HEJMLANDO) //germanio */
/* 	  { */
/* 		$teksto .= "\n" . donu_tekston_lauxlingve("konf2-membreco-averto", $lingvo, $renkontigxo); */
/* 	  } */

	if ($partopreno->datoj['tejo_membro_kontrolita'] == 'j')
	{
		$teksto .= "" . donu_tekston_lauxlingve("konf2_tejo_estos_membro", $lingvo, $renkontigxo);
	}
	else 
	{
		$teksto .= "" . donu_tekston_lauxlingve("konf2_tejo_ne_jam", $lingvo, $renkontigxo);
	}
 
	$teksto.=' ';
	$this->pdf->multicell(170, 3.8,
                          $this->trans_eo($teksto), 0, "L");

	$this->pdf->SetFontSize(10);

	 $this->pdf->ln(5);
	// $this->pdf->setY(200);
 
	// TODO: cxu sencas absoluta pozicio?
	//	$this->pdf->setY(240);
   
	$this->pdf->write(5, $this->trans_eo(donu_tekston_lauxlingve("konf2-elkonduko",
													 $lingvo, $renkontigxo)));
 

	//	$this->pdf->Image($GLOBALS['prafix'] . '/bildoj/subskribo-julia-2.png', 100, 251, 80); // TODO: allgemein (el konfiguro aux datumbazo)


	$this->pdf->Ln(10.0);

 

	eliru_dosieron();
					 eliru_lingvon();

  }

  /**
   * Aldonas konfirmilon por unu partopreno al la PDF-dosiero.
   *
   * @param Partopreno|int $partoprenoID
   * @param Partoprenanto|int $partoprenantoID Tiu estu kongrua al la
   *                          partopreno.
   * @param string $savu se "J", memoras la sendodaton en la
   *                 partopreno-objekto.
   * @param Renkontigxo|null $renkontigxoobjekto uzata por la
   *                          renkontigxo-specifaj informoj - se null
   *                          (aux malgxusta), ni uzas la renkontigxon rilata
   *                          al la partopreno.
   * @uses Kotizokalkulilo
   * @uses Kotizosistemo
   */
  function kreu_konfirmilon($partoprenoID, $partoprenantoID, $savu='NE',
                            $renkontigxoobjekto = null)
  {
      if (is_object($partoprenoID)) {
          $partopreno =& $partoprenoID;
      }
      else {
          $partopreno = new Partopreno($partoprenoID);
      }

      if (is_object($partoprenantoID)) {
          $partoprenanto =& $partoprenantoID;
      } else {
          $partoprenanto = new Partoprenanto($partoprenantoID);
      }

      if ($renkontigxoobjekto and
          ($renkontigxoobjekto->datoj['ID'] ==
           $partopreno->datoj['renkontigxoID'])) {
          $renkontigxo = $renkontigxoobjekto;
      } else {
          $renkontigxo = new Renkontigxo($partopreno->datoj['renkontigxoID']);
      }

	if (DEBUG)
	  {
		echo "<!--";
		echo "\npartopreno: ";
		var_export($partopreno);
		echo "\npartoprenanto: ";
		var_export($partoprenanto);
		echo "\nrenkontigxo: ";
		var_export($renkontigxo);
		echo "-->";
	  }

 
    $kotizosistemo = new Kotizosistemo($renkontigxo->datoj['kotizosistemo']);

	$kotizo = new Kotizokalkulilo($partoprenanto,$partopreno,$renkontigxo,
                                  $kotizosistemo);
    

	$this->kreu_konfirmilon_unulingve($partopreno, $partoprenanto,
									  $renkontigxo, $kotizo, "eo");

	if (estas_unu_el($partopreno->datoj['konfirmilolingvo'], 'de', 'cs', 'pl'))
	  {
		$this->kreu_konfirmilon_unulingve($partopreno, $partoprenanto,
										  $renkontigxo, $kotizo,
										  $partopreno->datoj['konfirmilolingvo']);
	  }

// 	if ($partopreno->datoj['agxo']<'18') //(Gepatra klarigo mit ranhängen)
// 	  {
// 		$this->kreu_permesilon($partoprenanto, $renkontigxo);
// 	  }

 
	if ($savu=='J')
	  {
		$partopreno->datoj['2akonfirmilosendata']=date("Y-m-d");
		$partopreno->skribu();
	  }

  }

  /**
   * kreas unupagxan permesilon subskribindaj de la gepatroj.
   * @param Partoprenanto|null $partoprenanto se donita, gxi
   *				  estos uzita por eltrovi la sekson de la
   *                  partoprenanto (por uzi li aux sxi ktp.)
   *                  kaj la nomon (por enmeti gxin en tauxga
   *                  loko).
   * @param Renkontigxo $renkontigxo  estos uzata
   *                 por eltrovi kaj enmeti la gxustan daton, kaj ankaux
   *                 iom da teksto.
   * $defVira      - se ne enestas partoprenanto, tiu estas
   *                 uzata por eltrovi cxu vira (se estas true)
   *                 aux ina (false aux forlasita) formo estas
   *                 uzenda.
   */ 
  function kreu_permesilon($partoprenanto, $renkontigxo, $defVira = "")
  {
	if ($partoprenanto)
	  {
		$vira = ($partoprenanto->datoj['sekso']{0} == 'v');
	  }
	else
	  {
		$vira = $defVira;
	  }
	$this->pdf->AddPage(); 
	$this->pdf->SetY(30);
	$this->pdf->SetFont('','',30);
	$this->pdf->cell(160,10,"Gepatra permeso por via IS partopreno",0,1,C);
	$this->pdf->SetFont('','',14);
	$this->pdf->cell(160,10,$this->trans_eo("(Nur por partoprenantoj, kiuj ankorau^ ne havas 18 jarojn je " . $renkontigxo->datoj['de'] . ")"),0,1,C); 
	 
	$this->pdf->SetY(55);
	$this->pdf->write(5,"Nomo de la partoprenanto:  ");
	$this->pdf->cell(100, 5, $this->trans_eo($partoprenanto->datoj['personanomo'] . " " . $partoprenanto->datoj['nomo']), "B", 1, 'C');
	//   $this->pdf->line(76,60,180,60);
	$this->pdf->write(5,$this->trans_eo("\nSe vi je la komencig^o de la IS ankorau^ ne havas 18 jarojn, bonvolu nepre kunporti la suban permesilon de viaj gepatroj:\n\n")); 
	$this->pdf->SetFont('',"B");
	$this->pdf->write(5,$this->trans_eo("Mi permesas al mia " . ($vira ? "filo" : "filino" ) ." vojag^i al la Internacia Seminario kaj partopreni g^in. Krome " . ($vira? "li" : "s^i") . " rajtas sen gardpersono partopreni la ekskursojn (inklusive la nag^vesperon)."));
	$this->pdf->SetFont('');
	$this->pdf->line(20,109,140,109);
	$this->pdf->SetY(110);
	$this->pdf->cell(80,5,"(dato kaj subskribo de la gepatroj)",0,1,C);
	$this->pdf->SetY(130);
	$this->pdf->SetFont('','',8);
	$this->pdf->cell(160,5,$this->trans_eo("Bonvolu uzi au^ la esperantlingvan, au^ la germanlingvan version /").$this->trans_de(" Benutze bitte entweder die deutsch-, oder die esperantosprachige Version."),0,1,C);
	$this->pdf->SetY(160);
	$this->pdf->SetFont('','',30);
	$this->pdf->cell(160,10,$this->trans_de("Einverständniserklärung der Eltern"),0,1,C); 
	$this->pdf->SetFont('','',14);
	$this->pdf->cell(160,10,$this->trans_de("(Nur für Teilnehmer, die am " .
							 $renkontigxo->datoj['de'] .
							 " noch nicht 18 Jahre alt sind.)"),0,1,C);

	$this->pdf->SetY(140+55);
	$this->pdf->write(5,"Name des Teilnehmers:  ");
	$this->pdf->cell(100, 5, $this->trans_eo($partoprenanto->datoj['personanomo'] . " " . $partoprenanto->datoj['nomo']), "B", 1, 'C');
	$this->pdf->write(5,$this->trans_de("\nWenn du zu Beginn des IS noch keine 18 Jahre alt bist, bring bitte auf jeden Fall die untenstehende Erlaubnis von deinen Eltern mit:\n\n"));
	$this->pdf->SetFont('','B');
	if ($vira)
	  {
		$this->pdf->write(5,$this->trans_de("Ich erlaube meinem Sohn zur Internationalen Woche zu reisen und daran teilzunehmen. Weiterhin darf er ohne Aufsichtsperson an den Ausflügen (inklusive des Schwimmabends) teilnehmen.")); 
	  }
	else
	  {
		$this->pdf->write(5,$this->trans_de("Ich erlaube meiner Tochter zur Internationalen Woche zu reisen und daran teilzunehmen. Weiterhin darf sie ohne Aufsichtsperson an den Ausflügen (inklusive des Schwimmabends) teilnehmen.")); 
	  }
	$this->pdf->SetFont('','');
	$this->pdf->line(20,140+109,140,109+140);
	$this->pdf->SetY(110+140);
	$this->pdf->cell(80,5,$this->trans_de("(Datum und Unterschrift der Eltern)"),0,1,C); 
	
  }
  


  function sendu($dosiernomo = "")
  {
      if (! $dosiernomo) {
          $dosiernomo = $GLOBALS['prafix'] .
              '/dosieroj_generitaj/konfirmilo.pdf';
      }
	  $this->pdf->Output($dosiernomo);
  }

//echo "<A HREF=getpdf.php>finished</A>";
}
?>
