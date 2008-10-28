<?php

  /**
   * Kelkaj funkcioj rilataj al kondicxoj por regulaj rabatoj,
   * krompagoj aux kostoj.
   *
   * Jen gramatiko por kondicxoj:
   *<pre>
   *  kondiĉo -> aŭ-kondiĉo
   *  aŭ-kondiĉo -> kaj-kondiĉo
   *              -> kaj-kondiĉo aŭ aŭ-kondiĉo
   *  kaj-kondiĉo -> ne-kondiĉo
   *              -> ne-kondiĉo kaj kaj-kondiĉo 
   *  ne-kondiĉo  -> simplakondiĉo
   *              -> ne ne-kondiĉo
   *  simplakondiĉo -> kondiĉonomo
   *                -> "(" kondiĉo ")"
   *                -> komparkondiĉo
   *  komparkondiĉo -> valoro komparoresto
   *  komparoresto -> komprilato valoro
   *               -> en aro
   *  aro -> "{" listo "}"
   *  listo -> 
   *        -> valoro
   *        -> valoro "," listo
   *  valoro -> ĉeno
   *         -> nombro
   *         -> objekt-eco
   *</pre>
   * Jen la bazaj analiz-unuoj de tio (difinitaj per regulaj esprimoj).
   * Inter la bazaj unuoj povas (ofte devas) esti blanka spaco aŭ
   * komentoj por distingi ilin.
   *<pre>
   *  komento     -> <em>/[*]([^/]|/[^*])*[*]/</em>
   *              -> <em>(//|#).*$</em>
   *  aŭ          -> <em>[|]|or|aŭ|aux?</em>
   *  kaj         -> <em>&|and|kaj</em>
   *  ne          -> <em>!|ne|not</em>
   *  en          -> <em>in|en</em>
   *  komprilato  -> <em>=|<|>|<=|>=|<>|!=</em>
   *  kondiĉonomo -> <em>[a-zĉĝĵŝŭ_]+</em>
   *  ĉeno        -> <em>"[^"]*"</em>
   *              -> <em>'[^']*'</em>
   *  nombro      -> <em>-?[0-9]+</em>
   *  objekt-eco  -> <em>[a-z]+\.[a-zĉĝĵŝŭ_-]+</em>
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
   * esploras, cxu la kondicxo validas por la donitaj
   * objektoj.
   * @param $kondicxo
   */
function kontrolu_kondicxon($kondicxo, $partoprenanto,
                            $partopreno, $renkontigxo,
                            $kotizokalkulilo=null)
{
    $objektoj = compact('partoprenanto', 'partopreno',
                        'renkontigxo', 'kotizokalkulilo');
    // mallongaj nomoj
    $objektoj['anto'] = $partoprenanto;
    $objektoj['eno'] = $partopreno;
    $objektoj['igxo'] = $renkontigxo;
    $objektoj['kot'] = $kotizokalkulilo;

    return $kondicxo->estas_plenumita_de($objektoj);
}


/**
 * @params string $cxeno kondicxo-esprimo, sed kun aldonaj \.
 */
function analizu_kondicxon($cxeno) {
    $analizilo = new sintaksa_kondicxo_analizilo(stripslashes($cxeno));
    return $analizilo->analizu_kondicxon();
}

/**
 * @todo dokumentajxo
 * @return u8string la kondicxo-esprimo uzenda.
 */
function eltrovu_kondicxon()
{
    if ($_REQUEST['kondicxo'] and
        $_REQUEST['kondicxo'] != '---')
        {
            return $_REQUEST['kondicxo'];
        }
    else if ($_REQUEST['alt_kondicxo']) {
        $kondicxo = analizu_kondicxon($_REQUEST['alt_kondicxo']);
        // se ne funkciis, okazis eraro.

        if (! $kondicxo)
            return null;
        return 
            $_REQUEST['alt_kondicxo'];
    }
    else {
        darf_nicht_sein("vi devas au^ elekti au^ entajpi kondic^on.");
        return null;
    }
}

  /**
   * Gxenerala Leksika analizilo.
   */
class leksika_analizilo {


    var $legita;
    var $legota;
    var $lasta_simbolo;
    var $nuna_simbolo;

    var $leksikeroj;

    /**
     * konstruilo.
     * Kreas novan leksikan analizilon.
     * @param string $cxeno la analizenda cxeno.
     * @param array $leksikeroj array en la formo
     *       {@link simboltipo} => {$link uregexp}.
     */
    function leksika_analizilo($cxeno, $leksikeroj) {
        $this->legota = $cxeno;
        $this->legita = "";
        $this->leksikeroj = $leksikeroj;
        $this->nuna_simbolo = null;
        $this->lasta_simbolo = null;
    }

    /**
     * @access private
     */
    function legis_simbolon($tipo, $trovajxoj)
    {
        echo "<!-- legis_simbolon('" . $tipo . "', " . var_export($trovajxoj, true) . ") -->";
        $this->legota =
            substr($this->legota, strlen($trovajxoj[0]));
        $this->legita .=
            $this->lasta_simbolo['trovajxoj'][0];
        $simbolo = 
            array('tipo' => $tipo,
                  'trovajxoj' => $trovajxoj);
        $this->lasta_simbolo = $simbolo;
        $this->nuna_simbolo = $simbolo;
    }

    /**
     * @access private
     */
    function sercxu_sekvan_simbolon()
    {
        while (true) {
            foreach($this->leksikeroj AS $tipo => $esprimo) {
                $trafoj = array();
                if (preg_match($esprimo . 'uiA', 
                               $this->legota, $trafoj, 0))
                    {
                        $this->legis_simbolon($tipo, $trafoj);

                        if ($tipo == 'spaco' or
                            $tipo == 'komento') {
                            // nova trairo de la while-ripetajxo.
                            // tio estus kazo por "goto" (:-)
                            continue 2;
                        }
                        return;
                    } // if
            }
            // nenio trovita
            if (strlen($this->legota) == 0) {
                $tipo = 'fino';
            }
            else {
                $tipo = 'eraro';
            }
            $this->legis_simbolon($tipo, array($this->legota));
            return;
        }
    }  // sercxu_sekvan_simbolon

    /**
     * legas unu simbolon kaj donas la tipon de tiu.
     * La sama simbolo poste denove povas esti legita.
     *
     * @return simboltipo la tipo de la simbolo.
     */
    function rigardu_sekvan() {
        if (!isset($this->nuna_simbolo)) {
            $this->sercxu_sekvan_simbolon();
        }
        return $this->nuna_simbolo['tipo'];

    }

    /**
     * redonas la sekvan simbolon.
     *
     * @return array <code>
     *  array('tipo' => {@link simboltipo},
     *        'trovajxoj' => array(...))
     * </code>
     */
    function donu_sekvan() {
        echo ("<!-- donu_sekvan() \n-->");
        $rez = $this->rigardu_sekvan();
        $ero = $this->nuna_simbolo;
        $this->nuna_simbolo = null;
        return $ero;
    }

    /**
     * montras la aktualan statuson de la legilo.
     * @param boolean $html se true, formatas gxin buntan en HTML-formo.
     *    alikaze donas simplan ASCII-grafikon (kvankam unikoda).
     * @return htmlstring|u8string
     */
    function montru_statuson($html) {
        $legita = $this->legita;
        $aktuala = $this->lasta_simbolo['trovajxoj'][0];
        $legota = $this->legota;
        $lasta_linio = ltrim(strrchr('\n' . $legita, '\n'), '\n');
        $lllen = mb_strlen($lasta_linio, "UTF-8");
        $aktlen = mb_strlen($aktuala, "UTF-8");

        echo "<!--\n";
        var_export(array($this, $aktuala, $legita, $legota, $lasta_linio, $lllen));
        echo "-->";
        if ($html) {
            return "<pre>\n" .
                htmlspecialchars($legita) . "\n".
                str_repeat(" ", $lllen) .
                "<span style='background: " . ($this->nuna_simbolo ? 'yellow' : 'orange') . "'>" .
                htmlspecialchars($aktuala) . "</span>\n" . 
                str_repeat(" ", $lllen+$aktlen) .
                htmlspecialchars($legota) .
                "</pre>\n";
        }
        else {
            return $legita . "\n" .
                '>' . str_repeat('-', $lllen - 2) . ">" . "\n" .
                str_repeat(' ', $lllen) . 
                $legota;
        }
    }

}  // leksika_analizilo


/**
 * baza klaso por sintaksa analizilo.
 */
class Sintaksa_Analizilo {
    var $leksilo;

    function sintaksa_analizilo($cxeno, $leksikeroj) {
        $this->leksilo =& new leksika_analizilo($cxeno, $leksikeroj);
    }

    /**
     * Kontrolas, cxu la sekva simbolo havas la menciitan tipon.
     *
     * funkcio uzenda de subklasoj.
     * @param simboltipo $tipo
     * @return boolean
     */
    function sekva_estas($tipo) {
        echo "<!-- sekva_estas('" . $tipo ."')? -->";
        $sekva = $this->leksilo->rigardu_sekvan();
        echo "<!-- sekva: '" . $sekva . "'\n-->";
        return $tipo == $sekva;
    }

    /**
     * Forigas unu simbolon de menciita tipo.
     *
     * funkcio uzenda de subklasoj.
     *
     * Donas erarmesagxon, se la sekva simbolo
     * ne estas de gxusta tipo.
     * @param simboltipo $tipo
     */
    function mangxu($tipo) {
        $ero = $this->leksilo->donu_sekvan();
        if ($ero['tipo'] == $tipo)
            return;
        darf_nicht_sein("atendis simbolon de tipo '$tipo', ".
                        " sed trovis tipon '{$ero['tipo']}' anstatauxe:" .
                        $this->leksilo->montru_statuson(true));
    }


}  // Sintaksa_Analizilo



/**
 * sintaksa analizilo por kondicxoj, unu-simbola antauxrigardo (LL1, cxu?)
 *
 * (ankoraux ne preta)
 */
class sintaksa_kondicxo_analizilo extends Sintaksa_Analizilo {
    
    function sintaksa_kondicxo_analizilo($cxeno) {
        $this->sintaksa_analizilo($cxeno, $GLOBALS['kondicxo_leksikeroj']);
    }


    /**
     * @return Kondicxo 
     */
    function analizu_kondicxon() {
        $kondicxo = $this->legu_kondicxon();
        if (!$this->sekva_estas('fino')) {
            darf_nicht_sein("Mi atendis la finon de cxeno, sed venas" .
                            " ankoraux pli:\n" .
                            $this->leksilo->montru_statuson(true));
        }
        return $kondicxo;
    }

    /**
     * legado de unu kondicxo.
     * @return Kondicxo
     */
    function legu_kondicxon() {
        return $this->legu_aux_kondicxon();
    }

    /**
     * @return Kondicxo
     */
    function legu_aux_kondicxon() {
        $unua_parto = $this->legu_kaj_kondicxon();
        if ($this->sekva_estas('aux'))
            {
                $this->mangxu('aux');
                $dua_parto = $this->legu_aux_kondicxon();
                return new aux_Kondicxo($unua_parto, $dua_parto);
            }
        return $unua_parto;
    }

    /**
     * @return Kondicxo
     */
    function legu_kaj_kondicxon() {
        $unua_parto = $this->legu_ne_kondicxon();
        if ($this->sekva_estas('kaj'))
            {
                $this->mangxu('kaj');
                $dua_parto = $this->legu_kaj_kondicxon();
                return new kaj_Kondicxo($unua_parto, $dua_parto);
            }
        return $unua_parto;
    }

    
    /**
     * @return Kondicxo
     */
    function legu_ne_kondicxon() {
        if ($this->sekva_estas('ne')) {
            $this->mangxu('ne');
            $resto = $this->legu_ne_kondicxon();
            return new ne_Kondicxo($resto);
        }
        return $this->legu_simplan_kondicxon();
    }

    /**
     * @return Kondicxo
     */
    function legu_simplan_kondicxon() {
        if ($this->sekva_estas('(')) {
            $this->mangxu('(');
            $kond = $this->legu_kondicxon();
            $this->mangxu(')');
            return $kond;
        }
        else if ($this->sekva_estas('kondicxonomo')) {
            return $this->legu_nomitan_kondicxon();
        }
        else {
            return $this->legu_komparkondicxon();
        }
    }

    /**
     * @return Kondicxo
     */
    function legu_nomitan_kondicxon() {
        //        eoecho("antau^e:");
        //        echo $this->leksilo->montru_statuson(true);
        $simbolo = $this->leksilo->donu_sekvan();
        //        eoecho("poste:");
        //        echo $this->leksilo->montru_statuson(true);
        $kondnomo = $simbolo['trovajxoj'][1];
        return new nomita_Kondicxo($kondnomo);
    }

    /**
     * @return Kondicxo
     */
    function legu_komparkondicxon() {
        $maldekstra_valoro = $this->legu_valoron();
        if($this->sekva_estas('en')) {
            $this->mangxu('en');
            $this->mangxu('{');
            $aro = $this->legu_liston();
            $this->mangxu('}');
            return new en_Kondicxo($maldekstra_valoro, $aro);
        }
        // alikaze temas pri vera komparo (aux sintaksa eraro)
        $komparilo = $this->leksilo->donu_sekvan();
        if($komparilo['tipo'] != 'komprilato') {
            darf_nicht_sein("atendis komparrilaton, ne " .
                            var_export($komparilo, true) . "!\n" .
                            $this->leksilo->montru_statuson(true));
        }
        $dekstra_valoro = $this->legu_valoron();
        return new Komparkondicxo($maldekstra_valoro,
                                  $komparilo['trovajxoj'][1],
                                  $dekstra_valoro);
    }

    /**
     * @return array (el {@link Valoro})
     */
    function legu_liston() {
        $listo = array();
        while(true) {
            if ($this->sekva_estas('}')) {
                break;
            }
            $val = $this->legu_valoron();
            $listo[]= $val;
            if (!$this->sekva_estas(',')) {
                break;
            }
            $this->mangxu(',');
        }
        return $listo;
    }

    /**
     * @return Valoro
     */
    function legu_valoron() {
        $simbolo = $this->leksilo->donu_sekvan();
        switch($simbolo['tipo']) {
        case 'cxeno':
            // la enhavo estas aux en [1] (por ""), aux en
            // [1] (por ''), la alia estas null. Do ni simple
            // kunigas ambaux.
            return new literala_Valoro($simbolo['trovajxoj'][1] .
                                       $simbolo['trovajxoj'][2]);
        case 'nombro':
            return new literala_Valoro((int)($simbolo['trovajxoj'][1]));
        case 'objekt-eco':
            // trovajxoj[1] estas la nomo de la objekto,
            // trovajxoj[2] la nomo de la eco.
            return new objekteca_Valoro($simbolo['trovajxoj'][1],
                                        $simbolo['trovajxoj'][2]);
        }  // switch
        darf_nicht_sein("Legis " . var_export($simbolo, true) .
                        ", sed atendis valoron.\n" . 
                        $this->leksilo->montru_statuson(true) );
    }  // legu_valoron()

    
} // class sintaksa_kondicxo_analizilo


/**
 * abstrakta baza klaso por kondicxoj.
 */
class Kondicxo {

    /**
     * kontrolas, cxu tiu kondicxo validas por iu certa
     *  kombino de objektoj.
     *
     * @abstract
     * @param array $objektoj estu
     *    array(nomo => ...
     *          ... )
     * @return boolean
     */
    function estas_plenumita_de($objektoj) {
        darf_nicht_sein("tiu funkcio estu anstatauxita en subklaso.");
    }

}

/**
 * kondicxo1 aux kondicxo2
 */
class aux_Kondicxo extends Kondicxo {

    var $unua, $dua;

    function aux_Kondicxo($maldekstra, $dekstra) {
        $this->unua = $maldekstra;
        $this->dua = $dekstra;
    }

    function estas_plenumita_de($objektoj) {
        return $this->unua->estas_plenumita_de($objektoj) or
            $this->dua->estas_plenumita_de($objektoj);
    }
}

/**
 * kondicxo1 aux kondicxo2
 */
class kaj_Kondicxo extends Kondicxo {

    var $unua, $dua;

    function kaj_Kondicxo($maldekstra, $dekstra) {
        $this->unua = $maldekstra;
        $this->dua = $dekstra;
    }

    function estas_plenumita_de($objektoj) {
        return $this->unua->estas_plenumita_de($objektoj) and
            $this->dua->estas_plenumita_de($objektoj);
    }
}


/**
 * ne kondicxo
 */
class ne_Kondicxo extends Kondicxo {
    var $subkondicxo;
    function ne_Kondicxo($sub) {
        $this->subkondicxo = $sub;
    }

    function estas_plenumita_de($objektoj) {
        return ! ($this->subkondicxo->estas_plenumita_de($objektoj));
    }
} // ne_Kondicxo

/**
 * nomita kondicxo. Gxi vokas iun el la antaux-kreitaj kondicxo-funkcioj.
 * @see kondicxoj.php
 */
class nomita_Kondicxo extends Kondicxo {
    var $nomo;

    /**
     * @param asciistring|u8string $kondicxonomo la nomo de la funkcio, sen
     *            la prefikso 'kondicxo_', kaj eble kun supersignaj literoj
     *             anstataux x-konvencio.
     */
    function nomita_Kondicxo($kondicxonomo) {
        $this->nomo = utf8_al_iksoj($kondicxonomo);
    }

    function estas_plenumita_de($objektoj) {
        $funkcio = "kondicxo_" . $this->nomo;
        $renkontigxo = $objektoj['renkontigxo'];
        $partoprenanto = $objektoj['partoprenanto'];
        $partopreno = $objektoj['partopreno'];
        $kotizokalkulilo = $objektoj['kotizokalkulilo'];
        $aldonajxo = $objektoj['aldonajxo'];

        return $funkcio($partoprenanto, $partopreno,
                        $renkontigxo, $aldonajxo);
    }
}  // nomita_Kondicxo

class en_Kondicxo extends Kondicxo {

    var $valoro, $aro;

    function en_Kondicxo($valoro, $aro) {
        $this->valoro = $valoro;
        $this->aro = $aro;
    }

    function estas_plenumita_de($objektoj)
    {
        $val = $this->valoro->aktuala_valoro($objektoj);
        foreach($this->aro AS $elemento) {
            $el = $elemento->aktuala_valoro($objektoj);
            if ($val == $el)
                return true;
        }
        return false;
    }

}  // en_Kondicxo

class Komparkondicxo extends Kondicxo {

    var $maldekstra, $komp, $dekstra;

    function Komparkondicxo ($maldekstra, $komp, $dekstra) {
        $this->maldekstra = $maldekstra;
        $this->dekstra = $dekstra;
        $this->komp = $komp;
    }

    function estas_plenumita_de($objektoj) {
        $mal = $this->maldekstra->aktuala_valoro($objektoj);
        $dek = $this->dekstra->aktuala_valoro($objektoj);
        if (!isset($mal) ) {
            darf_nicht_sein($this);
        }
        if (!isset($dek)) {
            darf_nicht_sein($this);
        }
        switch($this->komp) {
        case '=':
            return $mal == $dek;
        case '<':
            return $mal < $dek;
        case '>':
            return $mal > $dek;
        case '<=':
            return $mal <= $dek;
        case '>=':
            return $mal >= $dek;
        case '<>':
        case '!=':
            return $mal != $dek;
        }
    }
}  // Komparkondicxo


/**
 * @abstract
 */
class Valoro {

    /**
     * eltrovas la aktualan valoron de tiu valor-esprimo por
     * la menciitaj objektoj.
     * @return string|number
     * @param array $objektoj estu
     *    array(nomo => object|array,
     *          ... )
     * @abstract
     */
    function aktuala_valoro($objektoj) {
        darf_nicht_sein("tiu funkcio estu anstatauxita en subklaso.");
    }
}  // Valoro

class literala_Valoro extends Valoro
{
    var $val;
    /**
     * @param string|number $val
     */
    function literala_Valoro($val) {
        $this->val = $val;
    }

    function aktuala_valoro() {
        return $this->val;
    }
}  // literala_Valoro


class objekteca_Valoro extends Valoro
{
    var $objnomo;
    var $eco;

    /**
     * @param u8string $objekto
     * @param u8string $eco
     */
    function objekteca_Valoro($objekto, $eco) {
        $this->objnomo = utf8_al_iksoj($objekto);
        $this->eco = utf8_al_iksoj($eco);
    }

    function aktuala_valoro($objektoj)
    {
        $objekto = $objektoj[$this->objnomo];
        $eco = $this->eco;
        if (is_object($objekto)) {
            if (isset($objekto->$eco)) {
                return $objekto->$eco;
            }
            // por niaj datumbazaj objektoj
            $objekto = $objekto->datoj;
        }
        // alikaze ni supozas, ke $objekto estas array.
        return $objekto[$eco];
    }
}  // objekteca_Valoro


/**
 * @global array $kondicxo_leksikeroj
 */
$GLOBALS['kondicxo_leksikeroj'] =
      array('spaco' => '~\s+~',
            'komento' => '~/[*]((?:[^/]|/[^*])*)[*]/|(?://|#)(.*)$~m',
            '(' => '~\(~',
            ')' => '~\)~',
            '{' => '~\{~',
            '}' => '~\}~',
            ',' => '~,~',
            // la elprovado de la alternativoj iras de maldekstre dekstren.
            // Do ni devas meti pli longajn antauxojn, por de ili ne estu
            // trovita nur la komenco.
            // Kaj samtempe tiu listo devas esti antaux la 'ne'-listo,
            // por ke '!' ne kaptu '!='.
            'komprilato' => '~(=|<>|<=|>=|!=|<|>)~',
            // la vortaj ajxoj devas esti antaux 'kondicxonomo',
            // cxar ekzemple 'and' ankaux tauxgus kiel nomo de kondicxo.
            'aux' =>'~([|]|or\b|aŭ\b|aux?\b)~',
            'kaj' => '~(&|and\b|kaj\b)~',
            'ne' => '~(!|ne\b|not\b)~',
            'en' => '~(in|en)\b~',
            'objekt-eco' => '~([a-zĉĝĵĥŝŭ_]+)\.([a-zĉĝĵĥŝŭ_-]+)~',
            'kondicxonomo' => '~([a-zĉĝĵĥŝŭ_]+)~',
            'cxeno' =>  '~"([^"]*)"|' . "'([^']*)'~",
            'nombro' => '~(-?[0-9]+)~',
            );






?>