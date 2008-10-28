<?php

  /**
   * Kelkaj funkcioj rilataj al kondicxoj por regulaj rabatoj,
   * krompagoj aux kostoj.
   *
   * Jen gramatiko por kondicxoj:
   *<pre>
   *  kondiĉo -> aŭ-kondiĉo
   *  aŭ-kondiĉo -> kaj-kondiĉo
   *              -> kaj-kondiĉo aux kaj-kondiĉo
   *  kaj-kondiĉo -> ne-kondiĉo
   *              -> ne-kondiĉo kaj kaj-kondiĉo 
   *  ne-kondicxo  -> simplakondicxo
   *              -> ne ne-kondicxo
   *  simplakondicxo -> kondicxonomo
   *                -> "(" kondicxo ")"
   *                -> komparkondicxo
   *  komparkondicxo -> valoro komparoresto
   *  komparoresto -> komprilato valoro
   *               -> en aro
   *  aro -> "{" listo "}"
   *  listo -> 
   *        -> valoro "," listo
   *  valoro -> cxeno
   *         -> nombro
   *         -> objekt-eco
   *</pre>
   * Jen la bazaj analiz-unuoj de tio (difinitaj per regulaj esprimoj).
   * Inter la bazaj unuoj povas (ofte devas) esti blanka spaco aux
   * komentoj por distingi ilin.
   *<pre>
   *  komento -> <em>/[*]([^/]|/[^*])*[*]/</em>
   *          -> <em>(//|#).*$</em>
   *  kondicxonomo -> <em>[a-zcxgxjxsxux_]+</em>
   *  cxeno -> <em>"[^"]*"</em>
   *        -> <em>'[^']*'</em>
   *  nombro -> <em>-?[0-9]+</em>
   *  objekt-eco -> <em>[a-z]+\.[a-zcxgxjxsxux_-]+</em>
   *  aux -> <em>\||or|aŭ|aux?</em>
   *  kaj -> <em>&|and|kaj</em>
   *  ne -> <em>!|ne|not</em>
   *  en -> <em>in|en</em>
   *  komprilato -> <em>=|<|>|<=|>=|<>|!=</em>
   *</pre>
   *
   * @package aligilo
   * @see kondicxoj.php
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   */


  /**
   * Leksika analizilo.
   */
class leksika_analizilo {

    var $cxeno;
    var $indekso;
    var $leksikeroj;
    var $ero;


    function leksika_analizilo($cxeno, $leksikeroj) {
        $this->cxeno = $cxeno;
        $this->indekso = 0;
        $this->ero = null;
    }

    /**
     *
     */
    function rigardu_sekvan() {
        if ($this->ero) {
            return $ero['tipo'];
        }

        while (true) {
            foreach($this->leksikeroj AS $tipo => $esprimo) {
                $trafoj = array();
                if (preg_match($esprimo . 'uiA', $this->cxeno,
                               $trafoj, 0, $this->indekso))
                    {
                        $len = strlen($trafoj[0]);
                        $this->indekso += $len;
                        if ($tipo == 'spaco' or
                            $tipo == 'komento') {
                            continue 2; // nova trairo de la while-ripetajxo.
                        }
                        $this->ero = array('tipo' => $tipo,
                                           'trovajxoj' => $trafoj);
                        return $tipo;
                    }
            }
            if (strlen($this->cxeno) == $this->indekso) {
                $this->ero = array('tipo' => 'fino',
                                   'trovajxoj' => array());
                return 'fino';
            }
            $this->ero = array('tipo' => 'eraro',
                               'trovajxoj' => substr($this->cxeno,
                                                     $this->indekso));
            return 'eraro';
        }
    }

    /**
     * redonas la sekvan leksikan elementon.
     *
     * @return array <code>
     *  array('tipo' => ...
     *        'trovajxoj' => array(...))
     * </code>
     */
    function donu_sekvan() {
        rigardu_sekvan();
        $ero = $this->ero;
        $this->ero = null;
        return $ero;
    }

}


/**
 * sintaksa analizilo por kondicxoj, unu-simbola antauxrigardo (LL1, cxu?)
 *
 * (ankoraux ne preta)
 */
class sintaksa_kondicxo_analizilo {
    
    var $leksilo;
    var $objektoj;

    function sintaksa_kondicxo_analizilo($cxeno, $objektoj) {
        $this->leksilo = new leksika_analizilo($cxeno, $GLOBALS['kondicxo_leksikeroj']);
        $this->objektoj = $objektoj;
    }

    function analizu_kondicxon() {
        // TODO
    }
    


}




$GLOBALS['kondicxo_leksikeroj'] =
      array('spaco' => '~\s+~',
            'komento' => '~/[*]((?:[^/]|/[^*])*)[*]/|(?://|#)(.*)$~m',
            'objekt-eco' => '~([a-z]+)\.([a-zĉĝĵĥŝŭ_-]+)~',
            'kondicxonomo' => '~([a-zĉĝĵĥŝŭ_]+)~',
            'cxeno' =>  '~"([^"]*)"|' . "'([^']*)'~",
            'nombro' => '~(-?[0-9]+)~',
            'aux' =>'~([|]|or|aŭ|aux?)~',
            'kaj' => '~(&|and|kaj)~',
            'ne' => '~(!|ne|not)~',
            'en' => '~(in|en)~',
            'komprilato' => '~(=|<|>|<=|>=|<>|!=)~');






?>