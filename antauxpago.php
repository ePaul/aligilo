<?php

  /**
   * Entajpo de pagoj (krom pagoj dum la akceptado)
   *
   * Tiuj parametroj estas uzenda por voko de ekstero:
   * - $id - identigilo de la 
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki, 2004-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   * debug-moduso.
   */
  // define("DEBUG", true);


  /**
   * la kutimaj iloj.
   */
require_once ('iloj/iloj.php');
malfermu_datumaro();

session_start();

kontrolu_rajton("mono");


if ($_POST['sendu']) {
    $pago = new Pago($_REQUEST['ID']);
    $pago->kopiu();
    $pago->datoj['entajpantoID'] = $_SESSION['kkren']['entajpanto'];
    if (kontrolu_daton($pago->datoj['dato'])) {
        if ($_REQUEST['ID']) {
            $pago->skribu();
        }
        else {
            $pago->skribu_kreante();
        }
        $_REQUEST['id'] = $pago->datoj['ID'];
    }
    else {
        $parto="korekti";
    }
 }

// kreu Pago-objekton.

if ($_REQUEST['id']) {
    $pago = new Pago($_REQUEST['id']);
    if ($pago->datoj['partoprenoID'] != $_SESSION['partopreno']->datoj['ID']) {
        $_SESSION['partopreno'] =
            new Partopreno($pago->datoj['partoprenoID']);
        $_SESSION['partoprenanto'] =
            new Partoprenanto($_SESSION['partopreno']->datoj['partoprenantoID']);
    }
 }
 else {
     $pago = new Pago();
     $pago->datoj['partoprenoID'] =
         $_SESSION["partopreno"]->datoj['ID'];
 }

/* nun cxiuokaze $_SESSION['partopreno'] kaj $pago kongruas.  */


{
    HtmlKapo();

    eoecho("<h2>(Antau^)Pago</h2>");

    if ($parto=="korekti")
    {
        echo "<center>";
        erareldono ("Hmm, io malg^usta okazis.");
        echo "</center>";
    }


  /*
   * trovu la renkontigxon de la partopreno: 
   */  
  if ($_SESSION['partopreno']->datoj['renkontigxoID'] ==
      $_SESSION['renkontigxo']->datoj['ID']) {
      $ppRenk = $_SESSION['renkontigxo'];
  }
  else {
      $ppRenk =
          new Renkontigxo($_SESSION['partopreno']->datoj['renkontigxoID']);
  }

  eoecho("<p>G^isnunaj pagoj de " . $_SESSION['partoprenanto']->tuta_nomo() .
         " en " . $ppRenk->datoj['mallongigo']. ":</p>");

  sercxu(datumbazdemando(array("p.ID", 'p.partoprenoID', 'p.valuto',
                               "p.kvanto", "p.tipo", "p.dato", "e.nomo"),
						 array("pagoj" => 'p', 'entajpantoj' => 'e'),
                         array("p.entajpantoID = e.ID"),
                         array('partopreno' => 'p.partoprenoID')),
		 array("dato", "desc"),
		 array(array('ID', 'ID','XXXXX',
                     'z','antauxpago.php?id=XXXXX','partoprenoID'),
			   array('dato','dato','XXXXX','l','','-1'),
			   array('kvanto','sumo','XXXXX','r','','-1'),
               array('valuto',"val.", "XXXXX", 'l', '', '-1'),
               array('nomo', 'entajpinto', 'XXXXX', 'l', '', '-1'),
			   array("tipo","tipo",'XXXXX','l','','-1'),
			   ), 
		 array(array('','',array('&sum; XX','N','z'))), 
		 0,0,0,"",'', "ne"); 
  
  echo "<form action='antauxpago.php' method='POST'>";

  if ( $pago->datoj['ID']) {
      $ago = "<strong>redaktas</strong> ";
  }
  else {
      $ago = "<strong>entajpas novan</strong> ";
  }


  eoecho ("<p>Vi nun " . $ago . " pagon de " .
          $_SESSION["partoprenanto"]->tuta_nomo() .
          " (".$_SESSION["partoprenanto"]->datoj['ID'] .
		  ") por la ".$ppRenk->datoj['nomo']." en ".
		  $ppRenk->datoj['loko'].".</p>\n");
     
  if ( $pago->datoj['dato']
       and  ! kontrolu_daton($pago->datoj['dato']) 
       )
  {
    erareldono ("La dato, kiun vi entajpis, ne ekzistas au^ estis malg^usta.");
  }

  echo "<table>";

  tabela_kasxilo("ID", 'ID', $pago->datoj['ID']);
  tabela_kasxilo("partopreno-ID", 'partoprenoID',
                 $pago->datoj['partoprenoID']);

  tabelentajpejo ("alvenodato",'dato',$pago->datoj['dato'],
                  11," (jaro-monato-tago)", "",date("Y-m-d"));
  
  tabelentajpejo ("kvanto",'kvanto',$pago->datoj['kvanto'], 7);
  tabela_elektolisto_el_konfiguroj("valuto", 'valuto',
                                   'valuto', $pago->datoj['valuto'],
                                   $ppRenk);

//   tabela_elektilo_radie_db("Valuto", 'valuto',
//                      'renkontigxaj_konfiguroj',
//                      "CONCAT(' ', teksto, ' <em>', aldona_komento, '</em>')",
//                      'interna',
//                      $pago->datoj['valuto'],
//                      array('renkontigxoID' =>
//                            $_SESSION['renkontigxo']->datoj['ID'],
//                            'tipo' => 'valuto'));


  $panto = new Entajpanto($pago->datoj['entajpantoID']);

  tabela_montrilo('entajpanto', $panto->datoj['nomo']);

  // TODO

  tabela_elektolisto_el_konfiguroj("tipo", "tipo",
                                   "pagotipo", $pago->datoj['tipo'],
                                   $ppRenk);


//   eoecho("<tr><th>tipo</th><td>\n");

//   montru_elekto_liston("antauxpaguloj", $pago->datoj['tipo'],
//                        'tipo','antau^pago al ', $ppRenk);
//   echo ("</td></tr>\n");

  echo "</table>\n";

  echo "<p>";
  send_butono("Enmetu!");
  ligu("partrezultoj.php","reen");
  echo "</p></form>";

  HtmlFino();

}

