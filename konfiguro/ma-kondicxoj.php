<?php
  /**
   * cxi tie diversaj tipoj de malaligx-traktadoj.
   */


$GLOBALS['ma_kondicxolisto'] = array('ne_eblas',
                                     'ni_tenos_minimuman_antauxpagon',
                                     'neniu_repago',
                                     'repago_krom_X');

/**
 * La funkcioj havu la prefikson "malaligxkotizo_".
 *
 * Cxiuj malaligx-traktado-funkcioj estos vokataj
 * per la sekvaj parametroj:
 * - $partoprenanto
 * - $partopreno
 * - $renkontigxo
 * - $kotizokalkulilo  - la kalkulilo-objekto, kun cxiuj pagoj
 *                       kaj la kutima kotizo jam kalkulita.
 * - $parametro        - la parametro de la kondicxtipo-objekto.
 *
 *
 * La funkcio redonu novan valoron por la kotizo en la kazo de malaligxo.
 */


/*
 * ne eblas malaligxi (= oni cxiuokaze devas pagi cxion).
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
 * ni tenos la minimuman antauxpagon, se la ulo tiom antauxpagis,
 * kaj repagos la reston. (Se la ulo antauxpagis malpli, ni tenos
 * cxion.)
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