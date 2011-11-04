<?php

  /**
   * ĉi tie diversaj tipoj de malaliĝ-traktadoj.
   * 
 * La funkcioj havu la prefikson "malaligxkotizo_".
 *
 * Ĉiuj malaliĝ-traktado-funkcioj estos vokataj
 * per la sekvaj parametroj:
 * - $partoprenanto
 * - $partopreno
 * - $renkontigxo
 * - $kotizokalkulilo  - la kalkulilo-objekto, kun ĉiuj pagoj
 *                       kaj la kutima kotizo jam kalkulita.
 * - $parametro        - la parametro de la kondiĉtipo-objekto.
 *
 *
 * La funkcio redonu novan valoron por la kotizo en la kazo de malaliĝo.
 *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage konfiguro
   * @copyright 2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */


$GLOBALS['ma_kondicxolisto'] = array('ne_eblas',
                                     'ni_tenos_minimuman_antauxpagon',
                                     'neniu_repago',
                                     'repago_krom_X');



/*
 * ne eblas malaliĝi (= oni ĉiuokaze devas pagi ĉion).
 */
function malaligxkotizo_ne_eblas($partoprenanto, $partopreno, $renkontigxo,
                                 $kotizokalkulilo, $parametro)
{
    return
        $kotizokalkulilo->partakotizo;
}

function malaligxkotizo_neniu_repago($partoprenanto, $partopreno, $renkontigxo,
                                     $kotizokalkulilo)
{
    return
        $kotizokalkulilo->pagoj;
}


/**
 * ni tenos la minimuman antaŭpagon, se la ulo tiom antaŭpagis,
 * kaj repagos la reston. (Se la ulo antaŭpagis malpli, ni tenos
 * ĉion.)
 */
function malaligxkotizo_ni_tenos_minimuman_antauxpagon($partoprenanto,
                                                       $partopreno,
                                                       $renkontigxo,
                                                       $kotizokalkulilo)
{
    $minAP = $kotizokalkulilo->minimuma_antauxpago();
    $pagoj = $kotizokalkulilo->pagoj; 
    //    echo "<!-- minimuma Antauxpago: " . var_export($minAP, true) . ", pagoj: " . $pagoj . "-->";
    return min($pagoj, $minAP);
}


function malaligxkotizo_repago_krom_X($partoprenanto, $partopreno,
                                      $renkontigxo, $kotizokalkulilo,
                                      $parametro)
{
    return min($parametro, $kotizokalkulilo->pagoj);
}



/*
function malaligxkotizo_($partoprenanto, $partopreno, $renkontigxo,
                         $kotizokalkulilo, $parametro)
{
    return
        ;
}
*/

?>