<?php

  /**
   *
   * Cxi tie ni difinos plurajn funkciojn, kiuj estas uzeblaj kiel
   * kondicxoj por krompagoj aux kromkostoj.
   */


  // jen listo de cxiuj kondicxoj, por uzo en elektilo.
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


/**
 * Cxiuj kondicxo-funkcio estos vokata per la
 * sekvaj parametroj:
 *
 * $partoprenanto  - ppanto-objekto
 * $partopreno     - ppeno-objekto
 * $renkontigxo    - renkontigxo-objekto
 * $kotizokalkulilo - la kotizokalkulilo, kiu volas kontroli
 *                    la kondicxojn. Tiun eblas demandi ekzemple
 *                    pri antauxpagoj kaj kategorioj.
 *
 * (ne necesas uzi cxiujn parametrojn, superfluaj simple estas
 *  forjxetataj.)
 */



function kondicxo_havas_dulitan_cxambron($partoprenanto,
                                         $partopreno,
                                         $renkontigxo) {
    if ($partopreno->datoj["dulita"] != "J") {
        // ne mendis dulitan cxambron
        return false;
    }
    if ($partopreno->datoj['alvenstato'] == 'm') {
        // malaligxis
        return false;
    }
    $rez = eltrovu_cxambrojn($partopreno->datoj['ID']);
    if (mysql_num_rows($rez) > 0) {
        // ricevis cxambron
        while ($linio1 = mysql_fetch_assoc($rez)) {
            $sql = datumbazdemando(array("litonombro", "dulita"),
                                   "cxambroj",
                                   "ID = '" . $linio1['cxambro'] . "'");
            $linio2 = mysql_fetch_assoc(sql_faru($sql));
            if ( // vere du- aux ecx unulita cxxambro:
                $linio2['litonombro'] <= 2 or
                // ni deklaris la cxambron kiel dulita:
                $linio2['dulita'] == 'J' or $linio2['dulita'] == 'U')
                {
                    return true;
                }
        }
        // ni ne trovis dulitan cxambron, kvankam ri mendis
        return false;
    }
    else {
        // ankoraux ne havas cxambron,
        // ~~> versxajne ricevos dulitan
        return true;
    }
}

function kondicxo_havas_unulitan_cxambron($partoprenanto,
                                 $partopreno,
                                          $renkontigxo) {
    if ($partopreno->datoj["dulita"] != "U") {
        // ne mendis unulitan cxambron
        return false;
    }
    if ($partopreno->datoj['alvenstato'] == 'm') {
        // malaligxis
        return false;
    }
    if ($partopreno->datoj['domotipo'] != 'J') {
        return false;
    }
    $rez = eltrovu_cxambrojn($partopreno->datoj['ID']);
    if (mysql_num_rows($rez) > 0) {
        // ricevis cxambron
        while ($linio1 = mysql_fetch_assoc($rez)) {
            $sql = datumbazdemando(array("litonombro", "dulita"),
                                   "cxambroj",
                                   "ID = '" . $linio1['cxambro'] . "'");
            $linio2 = mysql_fetch_assoc(sql_faru($sql));
            if ( // vere unulita cxambro:
                $linio2['litonombro'] <= 1 or
                // ni deklaris la cxambron kiel unulita:
                $linio2['dulita'] == 'U')
                {
                    return true;
                }
        }
        // ni ne trovis dulitan cxambron, kvankam ri mendis
        return false;
    }
    else {
        // ankoraux ne havas cxambron,
        // ~~> versxajne ricevos dulitan
        return true;
    }
}


function kondicxo_invitletero_sub30($partoprenanto,
                                    $partopreno,
                                    $renkontigxo) {
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
    return
        $partopreno->datoj["kunmangxas"] == "K";
}

function kondicxo_kunmangxas($partoprenanto,
                             $partopreno,
                             $renkontigxo) {
    return
        $partopreno->datoj['kunmangxas'] != 'N';
}


function kondicxo_agxo_ekde27($partoprenanto,
                              $partopreno,
                              $renkontigxo)
{
    return $partopreno->datoj["agxo"] > 26;
}

function kondicxo_logxas_en_junulargastejo($partoprenanto,
                                           $partopreno,
                                           $renkontigxo)
{
    return $partopreno->datoj["domotipo"] == "J";
}

/**
 * ankoraux du trivialaj kondicxoj por kompletigi
 * la elekton ...
 */


function kondicxo_cxiam() {
    return true;
}

function kondicxo_neniam() {
    return false;
}


?>