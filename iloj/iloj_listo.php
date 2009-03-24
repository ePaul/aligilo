<?php

  /**
   * Kelkaj funkcioj por krei la aligxinto-liston.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */





  /**
   */


  /**
   * kreas liston de la aligxintoj en array-formo.
   *
   * @param int $renkontigxoID
   * @param string $ordigo
   * @param lingvokodo $lingvo
   *
   * @return array du-dimensia array de la formo: <code>
   *   array( array('sxildnomo' => ...,
   *                'personanomo' => ...,
   *                'fam' => ...,
   *                'urbo' => ...,
   *                'landoid' => (ID de la lando),
   *                'sxildlando' => (eble alia lando indikita de la aligxinto),
   *                'partoprenoID' => (identigilo de la partopreno),
   *                'ordigoID' => (numero por uzo eble anstataux
   *                               la partoprenoID),
   *                'lando' => (la lando-objekto),
   *                'landonomo' => (nomo de la lando post traduko),
   *                'ordigo' => (aux ordigoID aux partoprenoID),
   *               ),
   *          array(...),
   *          ...)
   *   </code>
   * Gxi jam estas ordigita laux la petata maniero.
   */
function &kreu_aligxintoliston($renkontigxoID,  $ordigo, $lingvo)
{
    
    $sql = datumbazdemando(array("p.sxildnomo",
                                 "p.personanomo",
                                 "p.nomo" => "fam",
                                 "p.urbo" => 'urbo',
                                 "p.lando" => "landoid",
                                 "p.sxildlando" => 'sxildlando',
                                 "pn.ID" => 'partoprenoID',
                                 "pn.ordigoID"),
                           array("partoprenantoj" => "p",
                                 "partoprenoj" => "pn"),
                           array("p.ID = pn.partoprenantoID",
                                 "alvenstato = 'v'",
                                 "pn.listo = 'J'",
                                 "pn.renkontigxoID" => $renkontigxoID
                                 ),
                           "",
                           array("order" => "pn.ID")
                           );
    $rez = sql_faru($sql);
    $landolisto = array();
    $listo = array();
    while($linio = mysql_fetch_assoc($rez)) {
        if($linio['ordigoID'] != 0.0) {
            $linio['ordigo'] = (float)$linio['ordigoID'];
        }
        else {
            $linio['ordigo'] = (float)$linio['partoprenoID'];
        }
        
        if (!isset($landolisto[$linio['landoid']])) {
            $landolisto[$linio['landoid']] =& new Lando($linio['landoid']);
        }
        $linio['lando'] =& $landolisto[$linio['landoid']];
        $linio['landonomo'] = $linio['lando']->tradukita('nomo', $lingvo);
        
        $listo[]= $linio;
    }

    $komparilo = donu_komparilon($ordigo);

    metu_ordigolokalajxon($lingvo);

    if (!usort($listo, $komparilo)) {
        darf_nicht_sein("ordigado ne funkciis");
    }
    
    debug_echo("<!--" . var_export($listo, true) . "-->\n");
    
    $nombro_entute = eltrovu_gxenerale("COUNT(*)",
                                       "partoprenoj",
                                       array("renkontigxoID" =>
                                             $renkontigxoID,
                                             "alvenstato" => 'v'));
    $nombro_landoj = eltrovu_gxenerale("COUNT(DISTINCT p.lando)",
                                       array("partoprenoj" => "pn",
                                             "partoprenantoj" => "p"),
                                       array("renkontigxoID" =>
                                             $renkontigxoID,
                                             "alvenstato" => 'v',
                                             "pn.partoprenantoID = p.ID"));
    return array($listo, $nombro_entute, $nombro_landoj);
}

/**
 * redonas por ordigo-identigilo la 
 * korespondan ordigo-funkcion por uzo kun usort().
 */
function donu_komparilon($ordigo, $lingvo="") {
    switch($ordigo) {
    case 'sxildo':
        return 'komparilo_sxildnomo';
    case 'fam':
        return 'komparilo_famnomo';
    case 'pers':
        return 'komparilo_persnomo';
    case 'urbo':
        return 'komparilo_urbo';
    case 'lando':
    case 'lando_eo':
        return 'komparilo_lando_eo';
    case 'landokodo':
        return 'komparilo_landokodo';
    case 'landoloka':
        return 'komparilo_landoloka';
    case 'normala':
    default:
        return 'komparilo_ordigoID';
    }

}


/**
 * komparas du tabelliniojn laux ordigo-ID aux ID.
 *
 * @param array $unua la unua tabellinio
 * @param array $dua la dua tabellinio
 * @return int -1 (se $unua < $dua), 0 (se $unua == $dua), 1 (se $unua > $dua)
 */
function komparilo_ordigoID($unua_listero, $dua_listero) {
    return numcmp($unua_listero['ordigo'], $dua_listero['ordigo']);
}

/**
 * komparas du tabelliniojn laux sxildnomo.
 *
 * @param array $unua la unua tabellinio
 * @param array $dua la dua tabellinio
 * @return int -1 (se $unua < $dua), 0 (se $unua == $dua), 1 (se $unua > $dua)
 */
function komparilo_sxildnomo($unua_listero, $dua_listero) {
    $unua_sxildnomo = $unua_listero['sxildnomo'] or
        $unua_sxildnomo = $unua_listero['personanomo'];
    $dua_sxildnomo = $dua_listero['sxildnomo'] or
        $dua_sxildnomo = $dua_listero['personanomo'];
    $rez = strcmp_lok($unua_sxildnomo, $dua_sxildnomo);
    if ($rez)
        return $rez;
    else
        return strcmp_lok($unua_listero['fam'],
                         $dua_listero['fam']);
}

/**
 * komparas du tabelliniojn laux persona nomo.
 *
 * @param array $unua la unua tabellinio
 * @param array $dua la dua tabellinio
 * @return int -1 (se $unua < $dua), 0 (se $unua == $dua), 1 (se $unua > $dua)
 */
function komparilo_persnomo($unua_listero, $dua_listero) {
    $rez = strcmp_lok($unua_listero['personanomo'],
                     $dua_listero['personanomo']);
    if ($rez) 
        return $rez;
    else
        return strcmp_lok($unua_listero['fam'],
                         $dua_listero['fam']);
}

/**
 * komparas du tabelliniojn laux familia nomo.
 *
 * @param array $unua la unua tabellinio
 * @param array $dua la dua tabellinio
 * @return int -1 (se $unua < $dua), 0 (se $unua == $dua), 1 (se $unua > $dua)
 */
function komparilo_famnomo($unua, $dua) {
    $rez = strcmp_lok($unua['fam'], $dua['fam']);
    if(! $rez)
        $rez = strcmp_lok($unua['personanomo'], $dua['personanomo']);
    return $rez;
}

/**
 * komparas du tabelliniojn laux esperanta landonomo.
 *
 * @param array $unua la unua tabellinio
 * @param array $dua la dua tabellinio
 * @return int -1 (se $unua < $dua), 0 (se $unua == $dua), 1 (se $unua > $dua)
 */
function komparilo_lando_eo($unua, $dua) {
    $rez = strcmp_lok($unua['lando']->datoj['nomo'],
                     $dua['lando']->datoj['nomo']);
    if (! $rez) 
        $rez = strcmp_lok($unua['urbo'], $dua['urbo']);
    if (! $rez) 
        $rez = strcmp_lok($unua['personanomo'], $dua['personanomo']);
    return $rez;
}

/**
 * komparas du tabelliniojn laux ISO-landokodo.
 *
 * @param array $unua la unua tabellinio
 * @param array $dua la dua tabellinio
 * @return int -1 (se $unua < $dua), 0 (se $unua == $dua), 1 (se $unua > $dua)
 */
function komparilo_landokodo($unua, $dua) {
    $rez = strcmp($unua['lando']->datoj['kodo'],
                  $dua['lando']->datoj['kodo']);
    if (! $rez) 
        $rez = strcmp_lok($unua['urbo'], $dua['urbo']);
    if (! $rez) 
        $rez = strcmp_lok($unua['personanomo'], $dua['personanomo']);
    return $rez;
}

/**
 * komparas du tabelliniojn laux montro-lingva landokodo.
 *
 * @param array $unua la unua tabellinio
 * @param array $dua la dua tabellinio
 * @return int -1 (se $unua < $dua), 0 (se $unua == $dua), 1 (se $unua > $dua)
 */
function komparilo_landoloka($unua, $dua) {
    $rez = strcmp_lok($unua['landonomo'],
                     $dua['landonomo']);
    if (! $rez) 
        $rez = strcmp_lok($unua['urbo'], $dua['urbo']);
    if (! $rez) 
        $rez = strcmp_lok($unua['personanomo'], $dua['personanomo']);
    return $rez;
}


/**
 * komparas du tabelliniojn laux urbo.
 *
 * @param array $unua la unua tabellinio
 * @param array $dua la dua tabellinio
 * @return int -1 (se $unua < $dua), 0 (se $unua == $dua), 1 (se $unua > $dua)
 */
function komparilo_urbo($unua, $dua) {
    $rez = strcmp_lok($unua['urbo'], $dua['urbo']);
    if (! $rez) 
        $rez = strcmp_lok($unua['lando']->datoj['nomo'],
                         $dua['lando']->datoj['nomo']);
    if (! $rez) 
        $rez = strcmp_lok($unua['personanomo'], $dua['personanomo']);
    return $rez;
}

/**
 * komparas du cxenojn laux esperanta alfabeto
 *
 * @todo implementu Eo-komparon (kaj aliaj supersignoj)
 * @return -1, 0, 1
 */
function strcmp_eo($teksto1, $teksto2) {
    // TODO: eo-signoj, akcentitaj signoj, ktp.
    return strcasecmp($teksto1, $teksto2);
}

function strcmp_lok($teksto1, $teksto2) {
    return strcoll($teksto1, $teksto2);
}


/**
 * komparas du numerojn
 * @return int -1, 0, 1
 */
function numcmp($num1, $num2) {
    $num1 = (float)$num1;
    $num2 = (float)$num2;
    if ($num1 < $num2) {
        return -1;
    }
    else if ($num1 > $num2) {
        return 1;
    }
    return 0;
}

