<?php

  /**
   * Ĉi tie ni difinas plurajn funkciojn, kiuj estas uzeblaj kiel
   * kondiĉoj por krompagoj aŭ kromkostoj.
   * 
   *
 * Ĉiuj kondiĉo-funkcio estos vokata per la
 * sekvaj parametroj:
 *
 * - $partoprenanto  - ppanto-objekto
 * - $partopreno     - ppeno-objekto
 * - $renkontigxo    - renkontiĝo-objekto
 * - $kotizokalkulilo - la kotizokalkulilo, kiu volas kontroli
 *                    la kondiĉojn. Tiun eblas demandi ekzemple
 *                    pri antaŭpagoj kaj kategorioj.
 *
 * (ne necesas uzi ĉiujn parametrojn, superfluaj simple estas
 *  forĵetataj.)
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage konfiguro
   * @copyright 2007-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   *
   */


  // jen listo de ĉiuj kondiĉoj, por uzo en elektilo.
$kondicxolisto = array('havas_dulitan_cxambron',
                       'havas_unulitan_cxambron',
                       'invitletero_sub30',
                       'invitletero_ekde30',
                       'surloka_aligxo',
                       'mangxkupona_krompago',
                       'kunmangxas',
                       'agxo_ekde27',
                       'logxas_en_junulargastejo',
                       'neniam',
                       'cxiam');




function kondicxo_havas_dulitan_cxambron($partoprenanto,
                                         $partopreno,
                                         $renkontigxo) {
    if ($partopreno->datoj["dulita"] != "J") {
        // ne mendis dulitan ĉambron
        return false;
    }
    if (estas_unu_el($partopreno->datoj['alvenstato'], 'm', 'n')) {
        // malaliĝis / ne venis
        return false;
    }
    $rez = eltrovu_cxambrojn($partopreno->datoj['ID']);
    if (mysql_num_rows($rez) > 0) {
        // ricevis ĉambron
        while ($linio1 = mysql_fetch_assoc($rez)) {
            $sql = datumbazdemando(array("litonombro", "dulita"),
                                   "cxambroj",
                                   "ID = '" . $linio1['cxambro'] . "'");
            $linio2 = mysql_fetch_assoc(sql_faru($sql));
            if ( // vere du- aŭ eĉ unulita ĉxambro:
                $linio2['litonombro'] <= 2 or
                // ni deklaris la ĉambron kiel dulita:
                $linio2['dulita'] == 'J' or $linio2['dulita'] == 'U')
                {
                    return true;
                }
        }
        // ni ne trovis dulitan ĉambron, kvankam ri mendis
        return false;
    }
    else {
        // ankoraŭ ne havas ĉambron,
        // ~~> verŝajne ricevos dulitan
        return true;
    }
}

function kondicxo_havas_unulitan_cxambron($partoprenanto,
                                 $partopreno,
                                          $renkontigxo) {
    if ($partopreno->datoj["dulita"] != "U") {
        // ne mendis unulitan ĉambron
        return false;
    }

    if (estas_unu_el($partopreno->datoj['alvenstato'], 'm', 'n')) {
        // malaliĝis / ne venis
        return false;
    }
    if ($partopreno->datoj['domotipo'] != 'J') {
        return false;
    }
    $rez = eltrovu_cxambrojn($partopreno->datoj['ID']);
    if (mysql_num_rows($rez) > 0) {
        // ricevis ĉambron
        while ($linio1 = mysql_fetch_assoc($rez)) {
            $sql = datumbazdemando(array("litonombro", "dulita"),
                                   "cxambroj",
                                   "ID = '" . $linio1['cxambro'] . "'");
            $linio2 = mysql_fetch_assoc(sql_faru($sql));
            if ( // vere unulita ĉambro:
                $linio2['litonombro'] <= 1 or
                // ni deklaris la cxambron kiel unulita:
                $linio2['dulita'] == 'U')
                {
                    return true;
                }
        }
        // ni ne trovis dulitan ĉambron, kvankam ri mendis
        return false;
    }
    else {
        // ankoraŭ ne havas ĉambron,
        // ~~> verŝajne ricevos dulitan
        return true;
    }
}


function kondicxo_invitletero_sub30($partoprenanto,
                                    $partopreno,
                                    $renkontigxo,
                                    $kotizokalkulilo) {

    if (estas_unu_el($partopreno->datoj['alvenstato'], 'm', 'n')
        // nur fakturu, se la homo antaŭpagis ion ajn (de kio ni povas
        // depreni la monon.
        and  $kotizokalkulilo->pagoj <= 0) {
            return false;
    }


    //    debug_echo("<!-- partopreno: " . var_export($partopreno, true) . "-->");
    $invitpeto = $partopreno->sercxu_invitpeton();
    //    debug_echo("<!-- invitpeto: " . var_export($invitpeto, true) . "-->");
    return
        ($invitpeto and
         $invitpeto->datoj["invitletero_sendenda"] == "j"
         and $partopreno->datoj["agxo"] < 30);
}

function kondicxo_invitletero_ekde30($partoprenanto,
                                    $partopreno,
                                    $renkontigxo) {

    if (estas_unu_el($partopreno->datoj['alvenstato'], 'm', 'n') 
        // nur fakturu, se la homo antaŭpagis ion ajn (de kio ni povas
        // depreni la monon.
        and $kotizokalkulilo->pagoj <= 0) {
        return false;
    }

    
    $invitpeto = $partopreno->sercxu_invitpeton();
    
    return
        ($invitpeto and
         $invitpeto->datoj["invitletero_sendenda"] == "j"
         and $partopreno->datoj["agxo"] >= 30);
}


function kondicxo_surloka_aligxo($partoprenanto,
                                 $partopreno,
                                 $renkontigxo,
                                 $kotizokalkulilo)
{
    if (estas_unu_el($partopreno->datoj['alvenstato'], 'm', 'n')) {
        return false;
    }
    $aligxkat = donu_kategorion("aligx",
                                $kotizokalkulilo->kategorioj['aligx']['ID']);
    debug_echo("<!-- surloka aligxo? : " . $aligxkat->datoj['nomo'] . " -->");
    // TODO: pripensu pli bonan sistemon identigi
    // la surlokan kategorion.
    return $aligxkat->datoj['nomo'] == 'surloka';
}

function kondicxo_mangxkupona_krompago($partoprenanto,
                                       $partopreno,
                                       $renkontigxo) {
    if (estas_unu_el($partopreno->datoj['alvenstato'], 'm', 'n')) {
        return false;
    }
    return
        $partopreno->datoj["kunmangxas"] == "K";
}

function kondicxo_kunmangxas($partoprenanto,
                             $partopreno,
                             $renkontigxo) {
    if (estas_unu_el($partopreno->datoj['alvenstato'], 'm', 'n')) {
        return false;
    }
    return
        $partopreno->datoj['kunmangxas'] != 'N';
}


function kondicxo_agxo_ekde27($partoprenanto,
                              $partopreno,
                              $renkontigxo)
{
    if (estas_unu_el($partopreno->datoj['alvenstato'], 'm', 'n')) {
        return false;
    }
    return $partopreno->datoj["agxo"] > 26;
}

function kondicxo_logxas_en_junulargastejo($partoprenanto,
                                           $partopreno,
                                           $renkontigxo)
{
    if (estas_unu_el($partopreno->datoj['alvenstato'], 'm', 'n')) {
        return false;
    }
    return $partopreno->datoj["domotipo"] == "J";
}

/*
 * ankoraŭ du trivialaj kondiĉoj por kompletigi
 * la elekton ...
 */


function kondicxo_cxiam() {
    return true;
}

function kondicxo_neniam() {
    return false;
}


?>