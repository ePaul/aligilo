<?php

/**
 * Montrilo kaj redaktilo por la bazaj informoj de
 * la aktuala renkontiĝo, ankaŭ por krei novan renkontiĝon
 * (la lasta ankoraŭ ne tute funkcias).
 *
 * @author Paul Ebermann
 * @version $Id$
 * @package aligilo
 * @subpackage pagxoj
 * @copyright 2008 Paul Ebermann.
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */


  /**
   */
define("DEBUG", true);

require_once ('iloj/iloj.php');
require_once('iloj/iloj_mangxoj.php');

session_start();
malfermu_datumaro();

Htmlkapo();

kontrolu_rajton("teknikumi");


if(mangxotraktado != "libera") {
    erareldono("En la konfiguro por ligita mang^traktado ne eblas " .
               "krei apartajn mang^ojn.");
    HtmlFino();
    exit();
 }



/**
 * montras tabelon kun cxiuj mangxtempoj
 * por la aktuala renkontigxo, kun ligo por
 * redakti unuopajn.
 */
function montru_MangxtempoListon() {
    $sercxilo = new Sercxilo();
    $sercxilo->metu_datumbazdemandon(array('ID', 'dato', 'mangxotipo',
                                          'prezo', 'komento'),
                                    'mangxtempoj',
                                    "",
                                    "renkontigxoID");
    $sercxilo->metu_kolumnojn(array('kampo' => 'ID',
                                    'titolo' => "",
                                    'tekstosxablono' => "&ndash;>",
                                    'ligilsxablono'
                                    => "mangxredakto.php?id=XXXXX"),
                              array('kampo' => 'dato'),
                              array('kampo' => 'mangxotipo',
                                    'anstatauxilo' => $GLOBALS['mangxotipoj']),
                              array('kampo' => 'ID',
                                    'titolo' => 'mendoj',
                                    'anstatauxilo' => 'kalkulu_mangxmendojn',
                                    // TODO: ligu al listo de mendantaj
                                    // personoj
                                    ),
                              array('kampo' => 'prezo'),
                              array('kampo' => 'komento'));
    $sercxilo->metu_sumregulojn(array(array(array('#', '*', 'd'),
                                            array('XX', 'A', 'm'),
                                            array('&sum;', '*', 'd'),
                                            array('XX', 'NT', 'm')
                                            )));
    $sercxilo->metu_ordigon('dato', 'asc');
    $sercxilo->metu_antauxtekston("Mang^otempoj en " .
                                  $_SESSION['renkontigxo']->datoj['nomo']);
    $sercxilo->montru_rezulton_en_HTMLtabelo();
}


/**
 * @param Mangxtempo $mangxtempo
 */
function montru_mangxredaktilon($mangxtempo) {
    echo "<form action='mangxredakto.php' method='POST'>\n<table>";
    $linio = $mangxtempo->datoj;
    if (!$linio['renkontigxoID']) {
        $linio['renkontigxoID'] = $_SESSION['renkontigxo']->datoj['ID'];
    }

    tabela_kasxilo("ID", 'ID', $linio['ID']);
    tabela_kasxilo("renkontig^o-ID", 'renkontigxoID', $linio['renkontigxoID']);

    // TODO: pli bona dato-entajpilo (ekzemple simile al komenco-/findatoj)
    tabelentajpejo("Dato", 'dato', $linio['dato'], 20);
    //    echo( "<!-- mangxotipoj: " . var_export($GLOBALS['mangxotipoj'], true) . "-->");
    if ($linio['mangxotipo']) {
        $tipoj = $GLOBALS['mangxotipoj'];
    }
    else {
        $tipoj = array_merge (array('-' => ""),
                              $GLOBALS['mangxotipoj']);
    }
    //    echo( "<!-- tipoj: " . var_export($tipoj, true) . "-->");
    tabela_elektilo("mang^otipo", 'mangxotipo', $tipoj,
                    $linio['mangxotipo']);
    tabelentajpejo("Prezo", 'prezo', $linio['prezo'], 10);
    tabelentajpejo("Komento", 'komento', $linio['komento'], 20);
    echo "</table>\n<p>\n";
    if ($linio['ID']) {
        butono( "sxangxu", "S^ang^u");
    }
    else {
        butono("kreu", "Kreu");
    }

    ligu("mangxredakto.php", "Reen al la listo");
    // butono + reen
    echo "</p>\n</form>\n";
}

/**
 *
 */
function kreu_aux_sxangxu() {
    if (is_numeric($_REQUEST['ID'])) {
        $mangxo = new Mangxtempo($_REQUEST['ID']);
    }
    else {
        $mangxo = new Mangxtempo();
    }
    $mangxo->kopiu();

    // TODO: kontroloj

    switch($_REQUEST['sendu']) {
    case 'sxangxu':
        $mangxo->skribu();
        eoecho("<p>S^ang^is mang^on #" . $mangxo->datoj['ID']. "</p>\n");
        break;
    case 'kreu':
        $mangxo->skribu_kreante();
        eoecho("<p>Kreis mang^on #" . $mangxo->datoj['ID']."</p>\n");
        break;
    }
    return $mangxo;
}



if ($_REQUEST['sendu']) {
    $mangxo = kreu_aux_sxangxu();
 }
else if ($_REQUEST['id']) {
    if (is_numeric($_REQUEST['id'])) {
        $mangxo = new Mangxtempo($_REQUEST['id']);
    }
    else {
        $mangxo = new Mangxtempo();
    }
 }

eoecho("<h2>Administrado de mang^otempoj</h2>");


if ($mangxo) {
    montru_mangxredaktilon($mangxo);
    echo "<p>";
 }
 else {
     montru_MangxtempoListon();

     echo "<p>";
     ligu("mangxredakto.php?id=nova",
          "Kreu novan mang^tempon");
     
     // TODO: ligo por krei novan, reen-ligo
 }

ligu("administrado.php", "Reen al grava administrado");

echo "</p>\n";

HtmlFino();


?>