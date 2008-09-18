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
require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();

Htmlkapo();

kontrolu_rajton("teknikumi");


if(mangxtraktado != "libera") {
    erareldono("En la konfiguro por ligita mang^traktado ne eblas " .
               "krei apartajn mang^ojn.");
    HtmlFino();
    exit();
 }


function montru_MangxtempoListon() {
    $sercxilo = new Sercxilo();
    $sercxio->metu_datumbazdemandon(array('ID', 'dato', 'mangxotipo',
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
                                    'anstatauxo' => $GLOBALS['mangxotipoj']),
                              array('kampo' => 'ID',
                                    'titolo' => 'mendoj',
                                    'anstatauxo' => 'kalkulu_mangxmendojn',
                                    // TODO: ligu al listo de mendantaj
                                    // personoj
                                    ),
                              array('kampo' => 'prezo'),
                              array('kampo' => 'komento'));
    $sercxilo->metu_sumregulojn(array(array(array('#', '*', 'd'),
                                            array('XX', 'A', 'm'),
                                            array('&sum;', '*', 'd'),
                                            array('XX', 'N', 'm'
                                            )));
    $sercxilo->metu_ordigon('dato', 'asc');
    $sercxilo->montru
}




eoecho("<h2>Administrado de mang^otempoj</h2>");






HtmlFino();


?>