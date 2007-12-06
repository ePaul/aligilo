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


  /**
   * kontrolas, cxu unu el la signoj de $teksto, interpretita kiel
   * UTF-8-teksto, estas ekster la kodigo Latina-1.
   */
function estas_ekster_latin1($teksto) {
  // TODO: pripensu, cxu ankaux eblas tion
  // legi el la UTF-8 versio. (Tamen ne tiom gravas.)
  $cxiujdatoj_utf16 = mb_convert_encoding($teksto, "UTF-16", "UTF-8");
  for ($i = 0; $i < strlen($cxiujdatoj_utf16); $i += 2)
	{
	  if (ord($cxiujdatoj_utf16{$i}) > 0)
          // -> litero > 256, t.e. ne en ISO-8859-1
		return true;
	}
  return false;
}

function bezonas_unikodon($partoprenanto)
{
  $cxiujdatoj =
	$partoprenanto->datoj['nomo'].
	$partoprenanto->datoj['personanomo'].
	$partoprenanto->datoj['adresaldonajxo'].
	$partoprenanto->datoj['strato'].
	$partoprenanto->datoj['posxtkodo'].
	$partoprenanto->datoj['urbo'];
  return estas_ekster_latin1($cxiujdatoj);
}

 
class Konfirmilo
{
  var $font;
  var $pdf;
  var $germane;
  var $unikode;


  /**
   * konstruilo.
   * Gxi kreas FPDF-objekton.
   */
  function Konfirmilo($unikode = "")
  {
	if ($unikode)
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
   * $teksto - la teskto estu en UTF-8 kun c^-kodigo.
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
   * $kotizo        - Kotizo-objekto (estu kreita el la tri antauxe
   *                   menciitaj objektoj)
   * $lingvo - aux "de" aux "eo".
   */
  function kreu_konfirmilon_unulingve($partopreno, $partoprenanto,
									  $renkontigxo, $kotizo, $lingvo)
  {
	$this->pdf->AddPage(); 
	$this->pdf->SetLeftMargin(20);
	$this->pdf->SetRightMargin(20);

	$this->pdf->Image($GLOBALS['prafix'] . '/bildoj/eo-logo.png', 162, 10, 28);
	
	$this->pdf->SetFont('','',30);
	if (!$this->unikode)
	  {
		$aldona_x = 12;
	  }
	$this->pdf->text(29+$aldona_x, 17, "germana esperanto-junularo");
	$this->pdf->text(34+$aldona_x, 26, "deutsche esperanto-jugend");
	$this->pdf->SetFont('','',12);
	//	$this->pdf->SetFont('Arial','I',12);
	$this->pdf->text(105,34, $this->trans_de(".... wir machen Völkerverständigung"));
 
	$this->pdf->SetFont('','',8); 
	// $this->pdf->SetFont('Arial','',8); 
    $this->pdf->text(20,51, donu_tekston("konf2-sendanto-adreso"));
	$this->pdf->line(20,53,97,53);

	// falc- kaj truil-markiloj
	$this->pdf->line(4,100,9,100);
	$this->pdf->line(4,147,7,147);
	$this->pdf->line(4,198,9,198);
     

	// adreso de la partoprenanto
	$this->pdf->SetFont('','B',12);
	$this->pdf->setY(59);
	$this->pdf->write(5, $this->trans_eo($partoprenanto->datoj[personanomo]." ".$partoprenanto->datoj[nomo]));
	$this->pdf->ln();
	if ($partoprenanto->datoj[adresaldonajxo]!='')
	  {
		$this->pdf->write(5,$this->trans_eo($partoprenanto->datoj[adresaldonajxo]));
		$this->pdf->ln();
	  }
	$this->pdf->write(5,$this->trans_eo($partoprenanto->datoj[strato]));
	$this->pdf->ln();
	$this->pdf->write(5,$this->trans_eo($partoprenanto->datoj[posxtkodo]." ".$partoprenanto->datoj[urbo]));
	$this->pdf->ln();
	$this->pdf->write(5,$this->trans_eo($partoprenanto->loka_landonomo()));
 
	$this->pdf->SetFont('','',10);
	$this->pdf->setY(90);
	$this->pdf->write(5, "Saluton!");
	$this->pdf->ln();

	$this->pdf->write(5, $this->trans_eo(donu_tekston_lauxlingve("konf2-enkonduko",
													$lingvo, $renkontigxo)));

	$this->pdf->ln();



	$this->pdf->SetLeftMargin(40);

    $kotizo->montru_kotizon($lingvo == 'eo' ? 3 : 4, $this);

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
	if ($partopreno->datoj[domotipo]=='M')
	  {
		$domotipo='memzorgantejo';
		$en_domo = $this->dulingva("en la memzorgantejo",
                                   "im Memzorgantejo", $lingvo);
	  }
	else
	  {
		$domotipo='junulargastejo';
		$en_domo = $this->dulingva("en la junulargastejo",
                                   "in der Jugendherberge", $lingvo);
	  }
 
	//certigi, ke vere estas dulita cxambro

    


    //	$this->pdf->setY(130);
	$litoj = eltrovu_litojn($partopreno->datoj[ID]);
	//echo "Litoj: ".$litoj["sumo"] ;
	//echo "K:".$kotizo->antauxpago." and ".$kotizo->landakategorio;


    /* */
	if ($partopreno->datoj['partoprentipo']!='t' and
        $domotipo=='junulargastejo')
	  {
          $teksto = donu_tekston_lauxlingve("konf2-parttempa", $lingvo, $renkontigxo);
	  }
	else // TODO?: (Cxu ankaux en Wetzlar?) In Trier haben wir genügend Betten
	  if ($kotizo->krom_surloka > 5)
		{
		  $teksto = anstatauxu(donu_tekston_lauxlingve("konf2-mankas-antauxpago",
													   $lingvo, $renkontigxo),
							   array("{{sumo}}" =>
									 $kotizo->minimuma_antauxpago() - $kotizo->antauxpago));
		}
	  else if ($litoj["sumo"] < $kotizo->partoprentagoj and $domotipo=='junulargastejo') 
		{
		  if ($litoj["sumo"]!='0') // oni erare donis malgxustan nombron da litoj
			{
			  erareldono("Malg^usta litonombro. Mankus noktoj: (noktonombro:".$litoj["sumo"].")");
			  halt();
			}
		  else
			{
			  $teksto = donu_tekston_lauxlingve("konf2-mankas-cxambro", $lingvo, $renkontigxo);
			}

		}
	  else 
		{ //se cxio enordas
		  $teksto = anstatauxu(donu_tekston_lauxlingve("konf2-cxio-enordas",
													   $lingvo, $renkontigxo),
							   array("{{en_domo}}" => $en_domo));
		  $cioenordo = 'jes';
		}

	echo "<!-- teksto: $teksto -->\n";


    // la granda teksto, kiu konfirmas la aligxon.
	$this->pdf->SetFont('','B',10);
	$this->pdf->write(4, $this->trans_eo($teksto));
	$this->pdf->ln();


    if (($pagenda = $kotizo->restas_pagenda()) > 0) {
        $teksto = anstatauxu(donu_tekston_lauxlingve("konf2-kunportu-reston",
                                                      $lingvo,
                                                      $renkontigxo),
                              array("{{sumo}}" => $pagenda));

        // atentigo pri kunportado de mono
        $this->pdf->SetFont('','B',8);
        $this->pdf->write(3.8, $this->trans_eo($teksto));
        $this->pdf->ln();
    }


	//$this->pdf->setY(155);
	$this->pdf->SetFont('','B',11);
	$this->pdf->cell(20,5, $this->dulingva("Gravaj informoj:",
									"Wichtige Informationen", $lingvo),0,2);
	$this->pdf->SetFont('','',9);
	$this->pdf->setX(25);

	$teksto = donu_tekston_lauxlingve("konf2-gravaj-informoj", $lingvo, $renkontigxo);

	if ($partopreno->datoj['agxo']< 18)
	  $teksto .= " " . donu_tekston_lauxlingve("konf2-junulo", $lingvo, $renkontigxo);
	if ($domotipo=='junulargastejo' and $cioenordo == 'jes')
	  {
		$teksto .= " " . donu_tekston_lauxlingve("konf2-21a-horo", $lingvo, $renkontigxo);
		//aus der DB zaubern
	  }
	else if ($domotipo=='memzorgantejo')
	  {
		$teksto .= " " . donu_tekston_lauxlingve("konf2-memzorganto", $lingvo, $renkontigxo);
	  }
	if ($partoprenanto->datoj['lando']==HEJMLANDO) //germanio
	  {
		$teksto .= "\n" . donu_tekston_lauxlingve("konf2-membreco-averto", $lingvo, $renkontigxo);
	  }

	if ($partopreno->datoj['tejo_membro_kontrolita'] == 'j')
	{
		$teksto .= "\n" . donu_tekston_lauxlingve("konf2_tejo_estos_membro", $lingvo, $renkontigxo);
	}
	else if ($partopreno->datoj['tejo_membro_laudire'] == 'j')
	{
		$teksto .= "\n" . donu_tekston_lauxlingve("konf2_tejo_ne_jam", $lingvo, $renkontigxo);
	}
 
	$teksto.=' ';
	$this->pdf->multicell(170, 3.8,
                          $this->trans_eo($teksto), 0, "L");

	$this->pdf->SetFontSize(10);

	// $this->pdf->ln(5);
	// $this->pdf->setY(200);
 
	// TODO: cxu sencas absoluta pozicio?
	$this->pdf->setY(240);
   
	$this->pdf->write(5, $this->trans_eo(donu_tekston_lauxlingve("konf2-elkonduko",
													 $lingvo, $renkontigxo)));
 
	$this->pdf->Image($GLOBALS['prafix'] . '/bildoj/subskribo-julia-2.png', 100, 251, 80); // TODO: allgemein (el konfiguro aux datumbazo)

	$this->pdf->Ln(10.0);

	$this->pdf->SetFont('','B',12);
	$this->pdf->cell(20,5, $this->dulingva("Enhavo:", "Inhalt", $lingvo),0,2);
	$this->pdf->SetFont('','',10);
 
	$enhavo = $this->dulingva("- tiu c^i konfirmilo\n".
					   "- la 2a informilo",
					   "- Diese Bestätigung\n" .
					   "- Die Esperanto-Version dieser Bestätigung\n" .
					   "- Das zweite Informilo", $lingvo);
	if ($this->germane and $lingvo == "eo")
	  {
          $enhavo .= $this->trans_eo("\n- la germanlingva versio de tiu c^i konfirmilo");
	  }
	if ($partopreno->datoj['agxo']<'18') 
	  $enhavo .= $this->dulingva("\n- gepatra permeso de via IS-partopreno",
						  "\n- Elterliche Erlaubnis deiner IS-Teilnahme", $lingvo);
	// $this->pdf->setXY(25,205);
	$this->pdf->multicell(170,5, $enhavo);

  }


  function kreu_konfirmilon($partoprenoID, $partoprenantoID, $savu='NE',
                            $renkontigxoobjekto = null)
  {
      if (is_object($partoprenoID)) {
          $partopreno = $partoprenoID;
      }
      else {
          $partopreno = new Partopreno($partoprenoID);
      }

      if (is_object($partoprenantoID)) {
          $partoprenanto = $partoprenantoID;
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

	if($partopreno->datoj['germanakonfirmilo']{0} == 'J')
	  {
		$this->germane = true;
	  }
	else
	  {
		$this->germane = false;
	  }

 
    $kotizosistemo = new Kotizosistemo($renkontigxo->datoj['kotizosistemo']);

	$kotizo = new Kotizokalkulilo($partoprenanto,$partopreno,$renkontigxo,
                                  $kotizosistemo);
    

	$this->kreu_konfirmilon_unulingve($partopreno, $partoprenanto,
									  $renkontigxo, $kotizo, "eo");

	if ($this->germane)
	  {
		$this->kreu_konfirmilon_unulingve($partopreno, $partoprenanto,
										  $renkontigxo, $kotizo, "de");
	  }

	if ($partopreno->datoj['agxo']<'18') //(Gepatra klarigo mit ranhängen)
	  {
		$this->kreu_permesilon($partoprenanto, $renkontigxo);
	  }

 
	if ($savu=='J')
	  {
		$partopreno->datoj['2akonfirmilosendata']=date("Y-m-d");
		$partopreno->skribu();
	  }

  }

  /**
   * kreas unupagxan permesilon subskribindaj de la gepatroj.
   * $partoprenanto - la partoprenantoobjekto. Se donita, gxi
   *				  estos uzita por eltrovi la sekson de la
   *                  partoprenanto (por uzi li aux sxi ktp.)
   *                  kaj la nomon (por enmeti gxin en tauxga
   *                  loko).
   * $renkontigxo  - la renkontigxo-objekto. Gxi estos uzata
   *                 por eltrovi kaj enmeti la gxustan daton.
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
