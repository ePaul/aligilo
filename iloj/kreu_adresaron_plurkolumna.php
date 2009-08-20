<?php // ĉĝĥĵŝŭ


  /**
   * Funkcio por krei la adresaron en PDF-a kaj CSV-a formo. 
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   *
   */
require_once ($GLOBALS['prafix'] . '/iloj/tcpdf_php4/tcpdf.php');
require_once ($GLOBALS['prafix'] . '/iloj/sqlobjektoj.php');


/**
 * klaso por kreado de adresaro.
 * 
 */
class Adresaro {

  var $pdf;
  var $komencoY;

  var $linialteco = 3.95;
  var $spaco_inter_kestoj = 1.4;

  var $margxenoMD = 10;

  function Adresaro() {
	$pdf = new TCPDF();
	$tiparo = "freesans";
	$pdf->AddFont($tiparo, '',  $tiparo . ".php");
	$pdf->AddFont($tiparo, 'B', $tiparo . "b.php");
	$pdf->setFont($tiparo);
	
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->setAutoPageBreak(false);


	$pdf->Open();
	$this->pdf =& $pdf;
  }

  function metu_enkondukon() {
	$this->pdf->AddPage();
	$this->pdf->Image($GLOBALS['prafix'] . "/bildoj/ijk-emblemo.png",
					  $this->pdf->getX(), $this->pdf->getY(),
					  50);
	$this->pdf->setLeftMargin(65);
	
	
	$this->pdf->setFontSize(15);
	$this->pdf->write(7, uni("Listo de Partoprenantoj\n" .
					   $_SESSION["renkontigxo"]->datoj["nomo"] .
                       " en " . $_SESSION["renkontigxo"]->datoj["loko"] .
                       " (" . $_SESSION["renkontigxo"]->datoj["de"] . " g^is " .
                       $_SESSION["renkontigxo"]->datoj["gxis"] . ")\n"));
	$this->pdf->SetFont('', 'B', 9);
	$this->pdf->write(3.7, uni("Vi rajtas uzi tiun adresaron nur por" .
						 " personaj celoj. Vi ne rajtas" .
						 " uzi g^in por amasaj leteroj au^".
						 " retmesag^oj (ankau^ ne por informi".
						 " pri via Esperanto-renkontig^o), kaj".
						 " ne rajtas pludoni g^in (ec^ ne parte). "));
	$this->pdf->SetFont('', '');
	$this->pdf->write(3.7, uni(" Se amiko de vi (kiu partoprenis la " .
						 $_SESSION['renkontigxo']->datoj['mallongigo'].
						 ") ne ricevis la adresaron," .
						 " li povas mendi propran c^e " .
						 $_SESSION['renkontigxo']->datoj['adminretadreso'] .
						 ". La sama validas por vi, se vi perdos ".
						 " g^in.\n\n" .
						 "Atentu, la g^usta sinsekvo de la" .
						 " adres-partoj sur leteroj - depende de" .
						 " la lando -" .
						 " ofte ne estas la sama kiel tiu en tiu".
						 " c^i listo. Informig^u antau^ eksendado" .
						 " de letero (ekzemple per retpos^to al la" .
						 " ricevonto).\n\n".
							   "La unua linio estas la nomo (unue persona, ".
							   "poste familia – en kelkaj landoj estu ".
							   "s^ang^ita), sekva unu au^ pluraj linioj estas ".
							   "adresindiko. Sekvas pos^tkodo (kiu bedau^rinde".
							   " mankas c^e multaj), urbo kaj lando. \n".
							   "Post unu malplena linio sekvas elektronikaj ".
							   "kontaktebloj: retpos^tadreso, telefonnumero ".
							   "kaj diversaj tujmesag^ilaj kontaktoj. (Tiuj".
							   " linioj mankas, kie ili estus malplenaj.)" .
							   " \n\n" .
							   "Mi pardonpetas pro la foje iom neortodoksa" .
							   " ordigado, c^efe de nomoj kun ne-latinaj" .
							   " literoj. La datumbazo, kiun ni havas c^e" .
							   " esperanto.cz, bedau^rinde havas iujn".
							   " strangajn ideojn kiel ordigi tiujn. Se iu" .
							   " povas klarigi al mi, kial 'Μαυροματακησ' ".
							   " aperas inter 'Igor' kaj 'Ilona', mi aparte" .
							   " g^ojos.                 –– Pau^lo Ebermann," .
							   " administranto de IJK (ijk.admin@esperanto.cz)."
							   ));
	$this->pdf->setLeftMargin($this->margxenoMD);
	$this->pdf->ln(9);

	$this->komencoY = $this->pdf->getY();
  }


  function kreu_gxin() {
	$this->metu_enkondukon();
	$this->metu_la_liston();
    $this->pdf->Output($GLOBALS['prafix'] . "/dosieroj_generitaj/adresaro.pdf");
    echo "<br/><br/>";
    hazard_ligu("dosieroj_generitaj/adresaro.pdf",
                "els^uti la adresaron (PDF).");
  }

  function metu_la_liston() {
	$listilo = new SQL_alternate_merge("personanomo", "sxildnomo");
	// tiuj estas por la plenaj adresoj:
	$listilo->maldekstra_datumbazdemando(array('personanomo',
											   'p.nomo' => 'fnomo',
											   'sxildnomo', 'adreso',
											   'posxtkodo', 'urbo',
											   'l.nomo' => 'landonomo',
											   'sxildlando',
											   'telefono', 'tujmesagxiloj',
											   'retposxto',
											   '"1"' => 'plena'),
										 array('partoprenantoj' => 'p',
											   'partoprenoj' => 'pp',
											   'landoj' => 'l'),
										 array('p.ID = pp.partoprenantoID',
											   'p.lando = l.ID',
											   'pp.alvenstato' => 'a',
											   'pp.intolisto' => 'J'),
										 array("renkontigxo"
											   => "pp.renkontigxoID",
											   ),
										 array("order" => "personanomo ASC"));
	// por la plusendoj ni bezonas nur sxildnomon kaj veran nomon.
	$listilo->dekstra_datumbazdemando(array("sxildnomo", "personanomo",
											"nomo" => "fnomo"),
									  array('partoprenantoj' => 'p',
											'partoprenoj' => 'pp'),
									  array('p.ID = pp.partoprenantoID',
											"p.sxildnomo <> ''",
											'pp.alvenstato' => 'a'),
									  array('renkontigxo'
											=> "pp.renkontigxoID"),
									  array("order" => "sxildnomo ASC"));
	metu_ordigolokalajxon('eo');

	while($linio = $listilo->sekva()) {
	  $this->aldonu_adreson($linio);
	}
  }

  var $kol_largxeco = 60;

  /**
   * aldonas keston kun la adreso al la dokumento.
   * @param array $linio
   */
  function aldonu_adreson($linio) {
	if ($linio['plena']) {
	  $bla = $linio['personanomo'] . " (" . $linio['sxildnomo'] .") " .
		$linio['fnomo'];

	  if ($linio['sxildnomo']) {
		$adresteksto = $linio['personanomo'] . " (" .
		  $linio['sxildnomo'] . ") " . $linio['fnomo'];
	  }	else {
		$adresteksto = $linio['personanomo'] . " " . $linio['fnomo'];
	  }
	  // TODO: (iam) formatu la adreson laŭ la lando.
	  $adresteksto .= "\n" . $linio['adreso'] . "\n" .
		$linio['posxtkodo'] . "\n" . $linio['urbo'] . "\n";
	  if ($linio['sxildlando']) {
		  $adresteksto .=
			"»" . $linio['sxildlando'] . "« (" . $linio['landonomo'] .")";
		}
		else {
		  $adresteksto .= $linio['landonomo'];
		}

	  $adresteksto .= "\n";
	  if ($linio['retposxto']) {
		$adresteksto .= "\n" . $linio['retposxto'];
	  }
	  if ($linio['telefono']) {
		$adresteksto .= "\n" .$linio['telefono'];
	  }
	  if($linio['tujmesagxiloj']){
		$adresteksto .= "\n" . $linio['tujmesagxiloj'];
	  }

	  

	}
	else {
	  $bla = $linio['sxildnomo'] ." :=> " . $linio['personanomo'] . " " .
		$linio['fnomo'];
	  $adresteksto =
		$linio['sxildnomo'] . " ⇒ " .
		$linio['personanomo'] . " " .
		$linio['fnomo'];
	}

	$numlin = substr_count($adresteksto, "\n");

	$this->certiguSpacon($numlin);
	
	$this->pdf->MultiCell($this->kol_largxeco, $this->linialteco,
						  uni($adresteksto),
						  $numlin ? '1': '0',
						  'L', 0, 1);
	

	// provizore, por testi la reston.
	echo $bla . "<br/>\n";
	
	//	$this->pdf->write($linlargxo, uni($bla));
	$this->pdf->ln($this->spaco_inter_kestoj);
  }

  function certiguSpacon($numlin) {
	if ($this->pdf->getY() > (283 - $this->linialteco*$numlin)) {
	  $this->nova_kolumno();
	}
  }

  function nova_kolumno() {
	$x = $this->pdf->getX();
	$nova_x = $x + $this->kol_largxeco +5;

	if ($nova_x + $this->kol_largxeco > 200) {
	  
	  // nova paĝo
	  $this->pdf->setLeftMargin($this->margxenoMD);
	  $this->pdf->AddPage();
	  $this->komencoY = $this->pdf->getY();
	  debug_echo("<!-- nova paĝo: {$this->komencoY} -->");
	}
	else {
	  debug_echo("<!-- nova_kolumno: $x => $nova_x, {$this->komencoY} -->");
	  $this->pdf->setLeftMargin($nova_x);
	  $this->pdf->setY($this->komencoY);
	}
  }


}


return;

/**
 * Kreas adresaron en PDFa formo, kaj paralele en CSVa formo.
 * La dosieroj estos metataj en 'dosieroj_generitaj', kaj ni fine
 * montros ligon al tiuj. Dum la kreado montras liston de nomoj
 * prilaborataj.
 * 
 * @param string $granda se <samp>"JES"</samp>, la adresaro estos farita
 *           en pli granda versio por korektlegi.
 * @param string $bunta se <samp>"JES"</samp>, la linioj de la adresaroj
 *                sxangxos inter kvar koloroj - tiel estas pli facile distingi
 *                la unuopajn adresojn, se kelkaj estas plurliniaj.
 */
function kreu_adresaron($granda, $bunta) {
    echo "<p>\n";

    $fp = fopen($GLOBALS['prafix'] . "/dosieroj_generitaj/adresaro.csv",
                "w"); //por la .csv versio
    $font='freesans';

    $pdf=new TCPDF();
    $pdf->AddFont($font,'',$font.'.php');
    $pdf->AddFont($font,'B',$font.'b.php');
    $pdf->SetFont($font,'',15);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    $pdf->Open();

    $pdf->AddPage();


    $pdf->write(7, uni("Listo de Partoprenantoj\n" .
                       $_SESSION["renkontigxo"]->datoj["nomo"] .
                       " en " . $_SESSION["renkontigxo"]->datoj["loko"] .
                       " (" . $_SESSION["renkontigxo"]->datoj["de"] . " g^is " .
                       $_SESSION["renkontigxo"]->datoj["gxis"] . ")\n"));
	if ('JES' == $granda)
        {
            $pdf -> SetFont($font,'',12);
            $pdf->write(8,
                        uni("Bonvolu kontroli (kaj eble korekti) vian".
                            " adreson en la adresaro, por ke en" .
                            " la fina versio estu g^ustaj datumoj."));
            $pdf->ln(12);
            $linlargxo = 7;
            $interlinspaco = 13;
        }
	else
        {
            $pdf->SetFont($font,'B',9);
            // TODO: metu tiun tekston en la datumbazon.
            $pdf->write(3.7, uni("Vi rajtas uzi tiun adresaron nur por" .
                                 " personaj celoj. Vi ne rajtas" .
                                 " uzi g^in por amasaj leteroj au^".
                                 " retmesag^oj (ankau^ ne por informi".
                                 " pri via Esperanto-renkontig^o), kaj".
                                 " ne rajtas pludoni g^in (ec^ ne parte). "));
            $pdf->SetFont('', '');
            $pdf->write(3.7, uni(
                                 " Se amiko de vi (kiu partoprenis la " .
                                 $_SESSION['renkontigxo']->datoj['mallongigo'].
                                 ") ne ricevis la adresaron," .
                                 " li povas mendi propran c^e " .
                                 $_SESSION['renkontigxo']->datoj['adminretadreso'] .
                                 ". La sama validas por vi, se vi perdos ".
                                 " g^in.\n" .
                                 "Atentu, la g^usta sinsekvo de la" .
                                 " adres-partoj sur leteroj - depende de" .
                                 " la lando -" .
                                 " ofte ne estas la sama kiel tiu en tiu".
                                 " c^i listo. Informig^u antau^ eksendado" .
                                 " de letero (ekzemple per retpos^to al la" .
                                 " ricevonto)."));
            $pdf->ln(7);
            $linlargxo = 3.0;
            $interlinspaco = 4.05;
        }
    
    $kampoj = array("p.ID", "pn.ID",
                    "p.nomo" => "famnomo",
                    "personanomo",
                    "sxildnomo", 
                    "l.nomo" => "landonomo", "retposxto",
                    "posxtkodo", "urbo", "lando",
                    "telefono");
    $kampolisto = "nomo;  retpos^to; pos^tkodo; urbo; lando; telefono;";
    if (KAMPOELEKTO_IJK) {
        $kampolisto .= "adreso; tujmesag^iloj; ";
        $kampoj[]= "adreso";
        $kampoj[]= "tujmesagxiloj";
    }
    else {
        $kampolisto .= " adresaldonaj^o; strato; telefakso; ";
        $kampoj[]= "adresaldonajxo";
        $kampoj[]= "strato";
        $kampoj[]= "telefakso";
    }
    
    $pdf->write(($linlargxo*1.7),
                uni($kampolisto));
    $pdf -> ln($interlinspaco);
    $pdf -> ln($interlinspaco);

    
    $demando = datumbazdemando($kampoj,
                               array("partoprenantoj" => "p",
                                     "partoprenoj" => "pn",
                                     "landoj" => "l"),
                               array("pn.partoprenantoID = p.ID",
                                     "l.ID = lando",
                                     "pn.intolisto = 'J'", 
                                     "alvenstato = 'a'"
                                     // nur uloj. kiuj estis akceptitaj
                                     ),
                               "renkontigxoID", // aktuala renkontigxo
                               array("order" => "personanomo, famnomo")
                               );

    echo "<BR><BR>";
    $rezulto = sql_faru($demando);
	$koloro = 0;
    while ($row = mysql_fetch_assoc($rezulto))
        {
            if ($row['sxildnomo']) {
                $tutanomo = $row['personanomo'] . ' (' .
                    $row['sxildnomo'] .') ' . $row['famnomo'];
            }
            else {
                $tutanomo = $row['personanomo'] . ' ' . $row['famnomo'];
            }
            eoecho($tutanomo."<br/>");
            if ($bunta == "JES")
                {
                    switch($koloro % 4)
                        {
                        case 0:
                            $pdf->SetTextColor(200,0,0);
                            break;
                        case 1:
                            $pdf->SetTextColor(0,0,255);
                            break;
                        case 2:
                            $pdf->SetTextColor(0,150,0);
                            break;
                        default:
                            $pdf->SetTextColor(0,0,0);
                            break;
                        }
                    $koloro ++;
                }


            $datumoj = array($tutanomo, $row['retposxto'],
                             $row['posxtkodo'], $row['urbo'],
                             $row['lando'], $row['telefono']);
            if(KAMPOELEKTO_IJK) {
                // TODO: pripensu, kiel trakti plurlinian adreson.
                array_push($datumoj, $row['adreso'], $row['tujmesagxiloj']);
            }
            else {
                array_push($datumoj, $row['adresaldonajxo'],
                           $row['strato'], $row['telefakso']);
            }

            $csv_teksto = "'" . implode("';'", $datumoj) . "'";
            $pdf_teksto = implode("; ", $datumoj);

            $pdf->write($linlargxo,uni($pdf_teksto));
            $pdf->ln($interlinspaco);
      
            

            // TODO: pripensu, ĉu ni ne ankaŭ por la CSV-versio restu ĉe UTF-8
            fputs($fp, utf8_decode($csv_teksto ."\n"));
        }
    $pdf->Output($GLOBALS['prafix'] . "/dosieroj_generitaj/adresaro.pdf");
    fclose($fp);
    echo "<br/><br/>";
    hazard_ligu("dosieroj_generitaj/adresaro.pdf",
                "els^uti la adresaron (PDF).");
	hazard_ligu("dosieroj_generitaj/adresaro.csv",
                "els^uti la adresaron (CSV).");
    echo "</p>";
}


?>