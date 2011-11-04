<?php

  /**
   * Reguloj por krompago aŭ rabato.
   * 
   * @see iloj_kotizoj.php
   * @see iloj_kondiĉoj.php
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage iloj
   * @copyright 2007-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */



  /**
   * superklaso por krompago- kaj rabatreguloj.
   *
   *  - ID
   *  - nomo
   *  - mallongigo (por la finkalkulada tabelo, eble ankoraŭ aliloke)
   *  - priskribo
   *  - entajpanto - entajpanto-ID de la verkinto
   *  - kondicxo   - ID de kondiĉo-objekto
   *  - uzebla  - j (estos montrata en listoj por elekti)
   *            - n (nur montrata por teknikistoj, por redakti ĝin)
   *  - lauxnokte - j (krompago por ĉiu nokto, kiun oni tranoktas)
   *                n (unufoja krompago)
   */
class Pseuxdoregulo extends Objekto
{


    /**
     * @param int $id
     * @param cheno $tipo - aŭ "rabat" aŭ "krompago", la komenca
     *     parto de la tabelnomo.
     */
    function Pseuxdoregulo($id, $tipo)
    {
        $this->Objekto($id, $tipo . "reguloj");
        $this->tipo = $tipo;
        $this->regulovorto = $tipo . "regulo";
    }

    var $kondicxo = 0;
    var $tipo;
    var $regulovorto;
    

    function korektu_kopiitajn()
    {
        unset($this->kondicxo);
    }


    /**
     * formatas la nomon de tiu regulo.
     *
     * depende tio, cxu la uzanto havas la rajton 'teknikumi',
     * ni metas ligon por redakti tiun objekton, aux simple nur
     * la nomon.
     * @return eostring
     */
    function formatu_nomon()
    {
        if(rajtas("teknikumi"))
            {
                return donu_ligon("regulo.php?tipo=" . $this->tipo .
                                  "&id=" . $this->datoj['ID'],
                                  $this->datoj['nomo']);
            }
        else
            {
                return $this->datoj['nomo'];
            }
    }


    /**
     * eltrovas, ĉu tiu krompagotipo aplikiĝas en iu specifa
     * situacio.
     * @param array $objektoj la kutima listo el
     *    partopreno, partoprenanto, renkontiĝo, kotizokalkulilo.
     * @return boolean
     *  -> true: jes, la partoprenanto devos pagi la krompagon
     *  -> false: ne, ...
     */
    function aplikigxas($objektoj)
    {
        if (!is_object($this->kondicxo)) {
            $this->kondicxo = new Kondicxo($this->datoj['kondicxo']);
        }

        return $this->kondicxo->validas_por($objektoj);
    }



    /**
     * donas la specifan pagon por iu kotizosistemo.
     *
     * @param Kotizosistemo|int $kotizosistemo
     * @return Regula_Pseuxdopago 
     */
    function donu_regulan_pseuxdopagon($kotizosistemo) {
        if (is_numeric($kotizosistemo)) {
            $sistemoID = $kotizosistemo;
        }
        else {
            $sistemoID = $kotizosistemo->datoj['ID'];
        }
        $klasonomo = "Regula_" . ucfirst($this->tipo) ;
        $pago = new $klasonomo(array('kotizosistemo' => $sistemoID,
                                     'regulo' => $this->datoj['ID']));
        if ($pago->datoj['ID'])
            return $pago;
        else
            return null;
    }


}  // class Pseuxdoregulo



  /**
   * Regulo por krompagoj.
   *
   *  - ID
   *  - nomo
   *  - mallongigo (por la finkalkulada tabelo, eble ankoraŭ aliloke)
   *  - priskribo
   *  - entajpanto - entajpanto-ID de la verkinto/lasta redaktanto
   *  - kondicxo   - ID de kondiĉo-objekto
   *  - uzebla  - j (estos montrata en listoj por elekti)
   *            - n (nur montrata por teknikistoj, por redakti ĝin)
   *  - lauxnokte - j (krompago por ĉiu nokto, kiun oni tranoktas)
   *                n (unufoja krompago)
   *
   */
class Krompagoregulo extends Pseuxdoregulo {


    function Krompagoregulo($id=0) {
        $this->Pseuxdoregulo($id, "krompago");
    }


}  // Krompagoregulo


  /**
   * Regulo por rabatoj
   *
   *  - ID
   *  - nomo
   *  - mallongigo (por la finkalkulada tabelo, eble ankoraŭ aliloke)
   *  - priskribo
   *  - entajpanto - entajpanto-ID de la verkinto/lasta redaktanto
   *  - kondicxo   - ID de kondiĉo-objekto
   *  - uzebla  - j (estos montrata en listoj por elekti)
   *            - n (nur montrata por teknikistoj, por redakti ĝin)
   *  - lauxnokte - j (krompago por ĉiu nokto, kiun oni tranoktas)
   *                n (unufoja krompago)
   *
   */
class Rabatoregulo extends Pseuxdoregulo
{

    function Rabatoregulo($id=0) {
        $this->Pseuxdoregulo($id, "rabato");
    }

}  // class Rabatoregulo

/**
 * kreas kaj redonas regulo-objekton.
 * @return Pseuxdoregulo
 */
function donu_regulon($tipo, $id) {
    $klasonomo = ucfirst($tipo)."regulo";
    return new $klasonomo($id);
}

/**
 * kreas kaj redonas Objekton por regula Pseŭdopago.
 * @param asciistring $tipo aŭ "krompago" aŭ "rabato".
 * @param int $id
 * @return Regula_Pseuxdopago
 */
function donu_regulan_pseuxdopagon($tipo, $id) {
    $klasonomo = "Regula_" . ucfirst($tipo);
    return new $klasonomo($id);
}


/**
 * donas liston de ĉiuj krompagotipo-objektoj.
 * @param string $tipo - aŭ "rabato" aŭ "krompago".
 * @param string|array $kondicxo - iu aldona SQL-kondiĉo (en la formato
 *        por {@link donu_where_kondicxon()}).
 *
 * @return array listo de ĉiuj regulo-objektoj de menciita tipo,
 * kiu plenumas la kondiĉon.
 */
function listu_cxiujn_regulojn($tipo, $kondicxo = "uzebla = 'j'")
{
    $tabelnomo = $tipo."reguloj";
    $klasonomo = ucfirst($tipo)."regulo";

    $rezulto = array();

    $sql = datumbazdemando("ID",
                           $tabelnomo,
                           $kondicxo);
    $rez = sql_faru($sql);
    while($linio = mysql_fetch_assoc($rez)) {
        $rezulto[]= new $klasonomo($linio['ID']);
    }
    return $rezulto;
}



/**
 * komuna superklaso por (regulaj) krompagoj kaj rabatoj.
 *
 * - ID
 * - regulo
 * - kotizosistemo
 * - kvanto
 * - valuto
 *
 */
class Regula_Pseuxdopago extends Objekto
{

    var $tipo;
    var $regulo;

    function Regula_Pseuxdopago($id, $tipo)
    {
        $this->Objekto($id, "regulaj_" . $tipo . "j");
        $this->tipo = $tipo;
        $this->regulo = null;
    }

    /**
     * redonas la regulo-objekton por tiu cxi
     * regula pseuxdopago.
     *
     * @return Pseuxdoregulo
     */
    function donu_regulon() {
        if (!is_object($this->regulo)) {
            $this->regulo = donu_regulon($this->tipo,
                                         $this->datoj['regulo']);
        }
        return $this->regulo;
    }

} // class Regula_Pseuxdopago

/**
 * regula rabato.
 */
class Regula_Rabato extends Regula_Pseuxdopago
{

    function Regula_Rabato($id = 0)
    {
        $this->Regula_Pseuxdopago($id, "rabato");
    }

}

/**
 * regula krompago.
 */
class Regula_Krompago extends Regula_Pseuxdopago
{
    function Regula_Krompago($id = 0)
    {
        $this->Regula_Pseuxdopago($id, "krompago");
    }

}  // class Regula_Krompago



