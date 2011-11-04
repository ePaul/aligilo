<?php

  /**
   * Iloj por trakti mangxa-mendadon.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */



  /**
   * eltrovas, cxu la partoprenanto por iu specifa mangxtempo
   * mendis mangxojn.
   */
function cxuMangxas($partoprenoID, $mangxtempoID) {
    $num = eltrovu_gxenerale('count(*)',
                             'mangxmendoj',
                             array("partoprenoID = '" .
                                   $partoprenoID . "'",
                                   "mangxtempoID = '" .
                                   $mangxtempoID . "'"));
    debug_echo( "<!-- cxuMangxas($partoprenoID, $mangxtempoID) = $num -->");
    return (boolean)(intval($num));
}


/**
 *
 */
function kalkulu_mangxmendojn($mangxtempoID)
{
    return
        eltrovu_gxenerale('count(partoprenoID)',
                          'mangxmendoj',
                          array("mangxtempoID = '" .
                                $mangxtempoID . "'"));
}



/*
function transformu_mangxoliston_en_tabelon() {
}
*/


/**
 * Eltrovas, kiujn mangxojn iu partoprenanto povus partopreni.
 *
 * @param Partopreno $partopreno partopreno-objekto, por eltrovi
 *                   renkontigxon, komenco- kaj fino-datojn.
 *
 *                   Se mankas, ni prenas $_SESSION['renkontigxo'] kaj
 *                   ties fin- kaj komenco-datojn.
 * @return array listo de cxiuj mangxoj, kiuj okazas dum la partoprentempo.
 */
function listu_eblajn_mangxojn($partopreno=null) {
    if ($partopreno) {
        $de = $partopreno->datoj['de'];
        $gxis = $partopreno->datoj['gxis'];
        $renkID = $partopreno->datoj['renkontigxoID'];
    }
    else {
        $renkontigxo = kreuRenkontigxon();
        debug_echo("<!-- sen partopreno, uzas renkontigxon: " .
                   var_export($renkontigxo, true) . "-->");
        $de = $renkontigxo->datoj['de'];
        $gxis = $renkontigxo->datoj['gxis'];
        $renkID = $renkontigxo->datoj['ID'];
    }


    $sql = datumbazdemando('ID',
                           'mangxtempoj',
                           array("renkontigxoID = '" . $renkID . "'",
                                 "'" . $de . "' <= dato",
                                 "dato <= '" . $gxis . "'"
                                 ));
    $rez = sql_faru($sql);
    $listo = array();
    while($linio = mysql_fetch_assoc($rez)) {
        $listo[]= $linio['ID'];
    }
    debug_echo("<!--" . var_export($listo, true) . "-->");
    return $listo;
}

/**
 * @param Partopreno partopreno pp-objekto.
 * @param array $mangxmendo $_POST['mangxmendo']
 * @todo: atentu, kiam iu malplilongigas sian partoprentempon.
 */
function traktu_mangxomendojn($partopreno, $mangxmendo) {
    $mangxolisto = listu_eblajn_mangxojn($partopreno);

    foreach($mangxolisto AS $mangxoID) {
        $antauxe_mendis = cxuMangxas($partopreno->datoj['ID'], $mangxoID);
        $nun_mendas = jesne_al_boolean($mangxmendo[$mangxoID]);
        if ($antauxe_mendis and !$nun_mendas) {
            forigu_el_datumbazo('mangxmendoj',
                                array('partoprenoID' =>
                                      $partopreno->datoj['ID'],
                                      'mangxtempoID' => $mangxoID));
        }
        else if ($nun_mendas and !$antauxe_mendis) {
            aldonu_al_datumbazo('mangxmendoj',
                                array('partoprenoID' =>
                                      $partopreno->datoj['ID'],
                                      'mangxtempoID' => $mangxoID));
        }
    }
}


function kalkulu_mangxojn($partopreno, $mangxotipo) {

    $num = eltrovu_gxenerale('COUNT(ID)',
                             array('mangxtempoj' => 't',
                                   'mangxmendoj' => 'm'),
                             array("m.mangxtempoID = t.ID",
                                   'm.partoprenoID' =>
                                   $partopreno->datoj['ID'],
                                   't.mangxotipo' => $mangxotipo));
    return intval($num);

}

