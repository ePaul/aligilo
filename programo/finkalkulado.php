<?php


  // ĉĝĵĥŝŭ

  /**
   * Montras la pago-bilancojn de la unuopaj partoprenantoj
   * en grandega tabelo.
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki,
   *            2004-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   */


// TODO!: unikodigo por la nomoj, adapto al nova kotizosistemo

require_once ("iloj/iloj.php");

session_start();
malfermu_datumaro();


require_once($GLOBALS['prafix'] . '/iloj/tcpdf_php4/tcpdf.php');


if (!rajtas("administri"))
{
  ne_rajtas();
}

class finkalkulado
{


    var $kotizosistemo;

	var $pdf, $font;




  /**
   * array(
   *     kodo => array(titolo, kampo-larĝeco, klarigo, formato),
   *     ...
   *      )
   * formato povas esti 'teksto' (t.e. ni ne traktas ĝin kiel numeron,
   *  kaj ne kreas sumojn), 'int' (t.e. ni traktas gxin kiel entjero, 
   *  kun alerto, se tamen estas post-komaj partoj) aux forlasita
   *  (nombro, ni formatas gxin kun du post-komaj decimaloj).
   */
  var $kampoj = array("alvenstato" => array("?",5,"La alvenstato: v = venos, a = akceptig^is, m = malalig^is., i = vidita, sed ne akceptig^is, n = ne venis/venos, sed ne malalig^is. ", 'teksto'),
                      "nomo_pers" => array("p. nomo",18, 0, 'teksto'),
                      "nomo_fam" => array("fam. nomo",20, 0, 'teksto'),
                      "noktoj" => array("N",3.5,
										"La nombro de partoprennoktoj.",
										'teksto'),
                      "lando" => array("log^lando",16, 0, 'teksto'),
                      /*                      "invitilo" => array("I",4, "C^u li/s^i ricevis invitilon? (J = Jes, malplena = Ne)", 'teksto'), */
                      "antauxpago"
					  => array("APago",17.5,
							   "La antau^pagoj (= c^iuj ne-surlokaj pagoj antau^ la fino de la partopreno)."),
                      "surlokaPago"
					  => array("SPago",17.5,
							   "La 'surlokaj' pagoj (c^iuj pagoj al kaj repagoj el la kaso)."),
                      "postaPago"
					  => array("PPago",13,
							   "Pagoj post la fino de la partopreno."),
                      "pagoSumo"
					  => array("PSumo",19,
							   "sumo de 'APago', 'SPago' kaj 'PPago'."),
                      "kotizo"
					  => array("kot.", 13,
							   "La baza kotizo por la lando- kaj alig^kategorio kaj partoprennoktoj, sen rabatoj kaj krompagoj.", 'int'),
					  "mangxoj"
					  => array("mang^.", 13, 
							   "Kostoj pro mang^ado", 'int'),
					  "logxado"
					  => array("log^.", 13,
							   "Kostoj pro log^ado", 'int'),
                      "krompagoj_gxeneralaj"
					  => array("al.krp.",11,
							   "Krompagoj arang^o-rilataj (ekzemple invitilo ktp.)", 'int'),
                      "TEJOkotizo"
					  => array("TEJO-k.",16,
							   "Membrokotizo au^ aliaj pagoj al TEJO/UEA, kiun oni pagis surloke al la arang^a kaso."),
					  /*                      "GEAkotizo"
					  => array("GEA-k.",15,"Membrokotizo por GEA (sekva jaro), kiun li pagis surloke al la IS-kaso."),
                      "GEJkotizo" => array("GEJ-k.",15,"Membrokotizo por GEJ (sekva jaro), kiun li pagis surloke al la IS-kaso."),
                      "punpago" => array("punpago",15,"Li/s^i rifuzis membrig^i en GEJ au^ GEA kaj pro tio devis krompagi."),
					  */
                      "rabatoj"
					  => array("rabato",18, "Rabatoj entute."),
                      "kSumo"
					  => array("kot.-sum.",19,
							   "sumo de kotizo, krompagoj (kun membrokotizoj) kaj mang^oj, minus rabatoj."),
                      "restas"
					  => array("restas",18,
							   "difereco de 'k.-sumo' kaj 'PSumo'. Estu 0 post la postrenkontig^a prilaborado."),
                      );
  var $pagxsumoj = array();
  var $tutsumoj = array();



  /**
   * konstruilo por la objekto.
   * Gxi kreas novan $pdf-objekton kaj aldonas
   * al gxi tiparojn.
   */
  function finkalkulado()
  {
	$sql = datumbazdemando(array('ID'),
						   'renkontigxaj_konfiguroj',
						   array('tipo'=> 'valuto'),
						   'renkontigxoID');
	$rez = sql_faru($sql);
	while($linio = mysql_fetch_assoc($rez)) {
	  // TODO: metu tiun eltrovadon de rongigo en alian funkcion
	  $valutoObj = new Renkontigxa_konfiguro($linio['ID']);
	  $valuto = $valutoObj->datoj['interna'];
	  $rimarko = $valutoObj->datoj['aldona_komento'];
	  // echo "rimarko: '$rimarko'\n";
	  preg_match('/\[(?:.*,)? *fajfu *= *(\d+(?:\.\d+)?) *(,.*)?\]/',
				 $rimarko, $trovaĵoj);
	  $fajfu = (float)($trovaĵoj[1]);
	  if ($fajfu == round($fajfu)) {
		$formato = 'int';
		$largxo = 13;
	  }
	  else {
		$formato = null;
		$largxo = 17;
	  }
	  
	  $this->kampoj['restas_' . $valuto] = array("en " . $valuto, $largxo,
												 "Kiom restas pagenda en " .
												 $valutoObj->datoj['teksto'] .
												 ", post rondigo",
												 $formato);
	}

	$this->listo_de_malaligxtipoj();
                                   

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

	  $this->pdf->setFillColor(255, 255, 0);
      
      $this->komenca_largxeco = 0;
      foreach($this->kampoj AS $kodo => $ero) {
          if ($ero[3]=='teksto') {
              $this->komenca_largxeco += $ero[1];
          }
          else
              break;
      }

  }



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

  function nf_int($io)
  {
	if ($io)
	  return number_format($io, 0, '', '');
	else
	  return "0";
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
	  switch($ero[3]) {
	  case'teksto':
		break;
	  case 'int':
		$this->tabelcxelo_int($this->pagxsumoj[$kodo],
							  $ero);
		$this->tutsumoj[$kodo] += $this->pagxsumoj[$kodo];
		$this->pagxsumoj[$kodo] = 0;
		break;
	  default:
		$this->tabelcxelo_nombro($this->pagxsumoj[$kodo],
								 $ero);
		$this->tutsumoj[$kodo] += $this->pagxsumoj[$kodo];
		$this->pagxsumoj[$kodo] = 0;
      }
	}
    $this->pdf->ln();
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

  function tabelcxelo_int($nombro, $ero) {
	if ($nombro != (double)(int)($nombro))
	  $fill = 1;
	else 
	  $fill = 0;
	
	$this->pdf->Cell($ero[1], 5,
					 $this->nf_int($nombro),
					 1, 0, 'R', $fill);
					 
  }


  function tabellinio($partoprenanto, $partopreno)
  {
    $ko = new Kotizokalkulilo($partoprenanto,$partopreno,
                              $_SESSION["renkontigxo"],
                              $this->kotizosistemo);

	$this->pdf->SetFont('', '',9);

	$informoj = $ko->donu_informojn();

    foreach($this->kampoj AS $kodo => $ero) {
        $datumo = $informoj[$kodo];
		switch ($ero[3]) {
		case 'teksto':
		  $this->tabelcxelo_teksto($datumo, $ero);
		  break;
		case 'int':
		  $this->tabelcxelo_int($datumo, $ero);
		  $this->pagxsumoj[$kodo] += $datumo;
		  break;
		default:
		  $this->tabelcxelo_nombro($datumo, $ero);
		  $this->pagxsumoj[$kodo] += $datumo;
        }
    }

    $this->pdf->ln();
  } // tabellinio


  function fina_sumo()
  {


	$this->pdf->SetFont('','B',10);

	$this->pdf->Cell($this->komenca_largxeco, 5 ,uni('entute'), 1,0,R);

    //	$this->pdf->Cell(17, 5 ,uni('pag^sumo '), 'TBR',0,R);
	$this->pdf->SetFont('', 'B',9.5);

    foreach($this->kampoj AS $kodo => $ero) {
	  switch($ero[3]) {
	  case 'teksto':
		// nenio
		break;
	  case 'int':
		$this->tabelcxelo_int($this->tutsumoj[$kodo],
							  $ero);          
		break;
	  default:
		$this->tabelcxelo_nombro($this->tutsumoj[$kodo],
								 $ero);          
	  }
	}
    $this->pdf->ln();
  }  // fina_sumo

  function listo_de_malaligxtipoj() {
      $sql = datumbazdemando(array("mallongigo", "nomo"),
                             "malaligxkondicxotipoj",
                             "uzebla = 'j'");
      $rez = sql_faru($sql);
      $tekstoj = array();
      while($linio = mysql_fetch_assoc($rez)) {
          $tekstoj[]= $linio['mallongigo'] . " = " .$linio['nomo'];
      }
      $this->kampoj['alvenstato'][2] .= " En kazo de malalig^o, dua litero mencias la traktadon de la malalig^o, depende de la malalig^dato: " .
          implode(", ", $tekstoj);
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


  HtmlKapo();


$kalk = new finkalkulado();
$kalk->kreu_pdf($GLOBALS['prafix'] . "/dosieroj_generitaj/finkalkulo.pdf");

ligu("finkalkulado.php", "nova kalkulado");


HtmlFino();