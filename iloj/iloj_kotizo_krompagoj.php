<?php

  /**
   * Konfigurebla krompago-kalkulado.
   * 
   * @see iloj_kotizoj.php
   * @see kondicxoj.php
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage iloj
   * @copyright 2007-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   * tipo de krompago aux rabato.
   *
   * krompagotipoj:
   *  - ID
   *  - nomo
   *  - nomo_lokalingve
   *  - mallongigo (por la finkalkulada tabelo, eble ankoraŭ aliloke)
   *  - priskribo
   *  - entajpanto - entajpanto-ID de la verkinto
   *  - kondicxo - nomo de kondiĉa funkcio (sen la prefikso 'kondicxo_').
   *  - uzebla  - j (estos montrata en listoj por elekti)
   *            - n (nur montrata por teknikistoj, por redakti ĝin)
   *  - lauxnokte - j (krompago por ĉiu nokto, kiun oni tranoktas)
   *                n (unufoja krompago)
   *
   * krompagoj:
   *   - kotizosistemo   (ID)
   *   - krompagotipo    (ID)
   *   - krompago        nombro
   */
class Krompagotipo extends Objekto {

    function Krompagotipo($id=0) {
        $this->Objekto($id, "krompagotipoj");
    }


    /**
     * eltrovas, ĉu tiu krompagotipo aplikiĝas en iu specifa
     * situacio.
     *  -> true: jes, la partoprenanto devos pagi la krompagon
     *  -> false: ne, ...
     */
    function aplikigxas($partoprenanto, $partopreno, $renkontigxo,
                        $kotizokalkulilo)
    {
        $funk = "kondicxo_" . $this->datoj['kondicxo'];
        return
            $funk($partoprenanto, $partopreno, $renkontigxo,
                  $kotizokalkulilo);
    }

}  // krompagotipo

function listu_cxiujn_krompagotipojn($kondicxo = "uzebla = 'j'") {
    $rezulto = array();

    $sql = datumbazdemando("ID",
                           "krompagotipoj",
                           $kondicxo);
    $rez = sql_faru($sql);
    while($linio = mysql_fetch_assoc($rez)) {
        $rezulto[]= new Krompagotipo($linio['ID']);
    }
    
    return $rezulto;
}




?>