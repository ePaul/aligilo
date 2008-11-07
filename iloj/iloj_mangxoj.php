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
    echo "<!-- cxuMangxas($partoprenoID, $mangxtempoID) = $num -->";
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
 * montras mendilon por mangxoj.
 *
 * @param Partopreno $partopreno la partopreno-objekto, por kiu ni
 *                               montru la mendilon. Se mankas, tiam
 *                               montru mendilon por nova partopreno.
 */
function montru_mangxomendilon($partopreno=null)
{
    $mangxolisto = listu_eblajn_mangxojn($partopreno);

    $tabelo = array();
    $tagolisto = array();
    foreach($mangxolisto AS $mangxoID) {
        $mtempo = new Mangxtempo($mangxoID);
        if ($partopreno) {
            $mendita = cxuMangxas($partopreno->datoj['ID'], $mangxoID);
        }
        else {
            if (isset($_REQUEST['mangxmendo'])) {
                $mendita =
                    jesne_al_boolean($_REQUEST['mangxmendo'][$mangxoID]);
            }
            else {
                $mendita = true;
            }
        }
        $tabelo[$mtempo->datoj['mangxotipo']][$mtempo->datoj['dato']] =
            array('mtempo' => $mtempo,
                  'mendita' => $mendita);
        $tagolisto[] = $mtempo->datoj['dato'];
    }
    $tagolisto = array_values(array_unique($tagolisto));

    ksort($tabelo, SORT_STRING);
    
    echo "<table class='mangxmendilo'>\n";
    foreach($tabelo AS $tipo => $tabellinio) {
        eoecho("  <tr id='mendillinio-". $tipo. "'>\n    <th>" . $GLOBALS['mangxotipoj'][$tipo] .
               "</th>\n");
        $cxiu_elektita = true;
        foreach($tagolisto AS $dato) {
            $ero = $tabellinio[$dato];
            if ($ero) {
                echo "    <td>";
                jes_ne_bokso("mangxmendo[".$ero['mtempo']->datoj['ID']."]",
                             $ero['mendita']);
                $cxiu_elektita = $cxiu_elektita && (boolean) $ero['mendita'];
                eoecho($dato);
                echo "</td>\n";
            } else {
                echo "    <td/>\n";
            }
        }
        echo("    <td class='cxiuj-ligo'>");
        ligu("javascript:menduCxiujn(this, '".$tipo."')",
             $cxiu_elektita? "malmendu c^iujn" : "mendu-cxiujn", '',
             array('class' => $cxiu_elektita ?
                   'malmendu-cxiujn' : 'mendu-cxiujn'));
        echo "</td>\n";
//         jes_ne_bokso("cxiuj-mangxoj-" . $tipo,
//                      false, "menduCxiujn(this, '".$tipo."')");
//         eoecho ("c^iuj </td>\n");
        echo "  </tr>\n";
    }
    echo "</table>\n";

}

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
        $renkontigxo = $_SESSION['renkontigxo'];
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



$GLOBALS['mangxotipoj'] = array('M' => "matenmang^o",
                                'T' => "tagmang^o",
                                'V' => "vespermang^o");


?>