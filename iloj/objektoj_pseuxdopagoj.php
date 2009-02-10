<?php

  /**
   * diversaj objektoj rilataj al (individuaj) Pagoj, rabatoj, krompagoj,
   * komune nomitaj "pseŭdopagoj".
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki,
   *            2004-2009 Paul Ebermann.
   *     (de februaro 2009 sub nomo objektoj_pseuxdopagoj.php, antaŭe
   *      objektoj_diversaj.php, iam eĉ pli frue objektoj.php.)
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */




/**
 * superklaso de ĉiuj pago-similaj klasoj.
 *
 * - ID
 * - partoprenoID
 * - kvanto
 * - valuto
 * - tipo  (kialo)
 * - dato
 * - entajpantoID
 *
 */
class PseuxdoPago extends Objekto {

    /**
     * identigilo, pri kiu klaso temas.
     * - krom
     * - pago
     * - rabato
     */
    var $klaso;

    function PseuxdoPago($id, $klaso){
        $tabelnomo = $GLOBALS['pp_tabelnomoj'][$klaso];
        $this->Objekto($id, $tabelnomo);
        $this->klaso = $klaso;
    }
}


/**
 * Pagoj de la unuopaj partoprenantoj/partoprenoj -
 * kaj antaŭpagoj kaj surlokaj pagoj.
 *
 * Tabelo "pagoj".
 *
 * - ID
 * - partoprenoID
 * - kvanto       (kiom da)
 * - valuto
 * - dato
 * - tipo
 */
class Pago extends PseuxdoPago
{
    /* konstruilo */
    function Pago($id=0)
    {
        $this->PseuxdoPago($id,"pago");
    }
}


/**
 * Individuaj Rabatoj de unuopaj partoprenoj.
 *
 * - ID
 * - partoprenoID
 * - kvanto
 * - valuto
 * - tipo  (kialo)
 * - dato
 * - entajpantoID
 *
 * tabelo "rabatoj".
 */
class Individua_rabato extends PseuxdoPago
{

    /** konstruilo */
    function Individua_rabato($id=0)
    {
        $this->PseuxdoPago($id,"rabato");
    }
}

/**
 * individuaj krompagoj de unuopaj partoprenantoj.
 *
 * - ID
 * - partoprenoID
 * - kvanto
 * - valuto
 * - tipo  (kialo)
 * - dato
 * - entajpantoID
 * 
 */
class Individua_krompago extends PseuxdoPago {
    /**
     * konstruilo
     */
    function Individua_krompago($id=0) {
        $this->PseuxdoPago($id, "krom");
    }
}


function donu_pseuxdopagon($tipo, $id) {
    $klasonomo = $GLOBALS['pp_klasonomoj'][$tipo];
    return new $klasonomo($id);
}



$GLOBALS['pp_klasonomoj'] =
    array('pago' => 'Pago',
          'rabato' => 'Individua_rabato',
          'krom' => 'Individua_krompago');

$GLOBALS['pp_kotizokalkulkategorio'] =
    array('pago' => "pagoj",
          'rabato' => "rabato",
          "krom" => "krompago");

$GLOBALS['pp_tabelnomoj'] =
    array('pago' => "pagoj",
          'rabato' => "individuaj_rabatoj",
          'krom' => "individuaj_krompagoj");


