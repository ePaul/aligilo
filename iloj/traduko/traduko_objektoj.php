<?php

/**
 * Tradukilo-interfaco al aliaj programpartoj.
 *
 * (anstatauxajxo por traduko.php.)
 *
 * Tiu dosiero estas vokita de aliaj partoj de la programo, kaj
 * proponas diversajn funkciojn por traduki tekstojn.
 *
 * Por traduki iun tekston en iu dosiero, uzu ekzemple
 * <code>
 *   CH("ĉenoid")
 * </code>
 * La formato de ĉenoid estas klarigita en la pseŭdoklaso {@link tradcheno}.
 *
 * La tradukilo traserĉos la fonto-dosierojn (laŭ ĝia konfiguro-dosiero),
 * kaj bezonas en la fontoteksto mem tiun formon, do ne uzu variablojn
 * ktp. por la ĉenoid. (uzo de ''-citiloj eblas.)
 * Trovitaj funkcio-nomoj:
 *  - {@link CH()}
 *  - {@link CHJS()}
 *  - {@link CH_chiuj()}
 *  - {@link CH_lau()}
 *  - {@link CH_lig()}
 *  - {@link CH_mult()}
 *  - {@link CH_repl()}
 * Ĉiuj tiuj funkcioj prenas kiel unua parametro la cheno-identigilon
 * laŭ supraj difino, kelkaj akceptas (aŭ eĉ bezonas) ankoraŭ pliajn
 * parametrojn.
 *
 * Por ke dum la labor-tempo la tradukilo sciu la aktualan
 * dosiernomojn, uzu aŭ {@link eniru_dosieron()}/{@link eliru_dosieron()},
 * permane manipulu $GLOBALS['traduko_dosieroj'], aŭ uzu kompletajn
 * URI-ojn aŭ la ~#-formon en ĉiu ĉeno. (Por la aliĝilo,
 *   {@link retpagxo.php} konfiguras ĉion ĝuste.)
 *
 *
 * @author Paul Ebermann, bazita sur origina laboro de la E@I-teamo.
 * @version $Id$
 * @package aligilo
 * @subpackage tradukilo
 * @copyright 2008-2009 Paul Ebermann,
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */



/**
 * @see iloj.php
 */

require_once(dirname(__FILE__) . "/iloj.php");



/**
 * @author Paul Ebermann, bazita sur origina laboro de la E@I-teamo.
 * @version $Id$
 * @package aligilo
 * @subpackage tradukilo
 * @copyright 2008 Paul Ebermann,
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */
class Tradukilo {

    /**
     * listo de lingvoj.
     *   La lasta estas la aktuale prilaborita.
     */
    var $lingvostoko;
    /**
     * listo de dosieroj.
     *   La lasta estas la aktuale prilaborita.
     */
    var $dosierstoko;

    /**
     * loko por konservi jam trovitajn tradukojn, por sxpari DB-alirojn.
     */
    var $tradukmemoro;

    /**
     * ilo por meti piednotojn.
     * @var Piednotilo
     */
    var $piednotilo;

    /**
     * konstruilo
     */
    function Tradukilo()
    {
        $this->lingvostoko = array($GLOBALS['agordoj']['chefa_lingvo']);
        $this->dosierstoko = array();
        $this->tradukmemoro = array();
        konektu();
    }
    /**
     * eltrovas la aktualan dosieron.
     */
    function aktuala_dosiero() {
        return end($this->dosierstoko);
    }

    /**
     * eltrovas la aktualan lingvon.
     */
    function aktuala_lingvo() {
        return end($this->lingvostoko);
    }

    /**
     * informas la traduksistemon, ke ni nun eniris/eniros novan dosieron.
     *
     * @param urlstring $dosiero la (interna) nomo de la nova dosiero,
     *                       eble ankaŭ relativa al la antaŭa loko.
     *                  se ne donita, ni prenas la nomon de tiu dosiero,
     *                  kiu vokas tiun funkcion.
     * @see eliru_dosieron()
     */
    function eniru_dosieron($dosiero) {
        if ($dosiero == "") {
            $dosiero =
                absoluta_dosiernomo_al_interna(eltrovu_vokantan_dosieron());
        }
        else {
            $dosiero = kunmetu_uri_relative($dosiero,
                                            $this->aktuala_dosiero());
        }
        $this->dosierstoko[]= $dosiero;
    }

    /**
     * informas la traduksistemon, ke ni nun eliros/-is iun tradukendan
     * dosieron.
     *
     * La sistemo reŝaltas al la antaŭe uzita dosiero.
     * @see eniru_dosieron()
     */ 
    function eliru_dosieron() {
        array_pop($this->dosierstoko);
    }



    function metu_piednotsistemon(&$piednotilo) {
        $this->piednotilo =& $piednotilo;
    }


    /**
     * informas la traduksistemon, ke ni nun volas tradukon en
     * alia lingvo.
     * @param lingvokodo $lingvo
     */
    function eniru_lingvon($lingvo) {
        $this->lingvostoko[]= $lingvo;
    }

    /**
     * informas la traduksistemon, ke ni ne plu volas tradukojn
     * al la lasta lingvo, do resxaltas al antauxa lingvo.
     */
    function eliru_lingvon() {
        array_pop($this->lingvostoko);
    }


    /**
     * sercxas ajnan tradukon por la cxeno.
     * Por tio ni trairas la lingvo-stokon.
     * 
     * @return array(traduko, lingvo)
     */
    function donu_ajnan_tradukon($cxeno)
    {
        $¢enokaj = analizu_chenon($cxeno, $this->dosierstoko);
        $cxeno = $¢enokaj['cheno'];
        $dosiero =$¢enokaj['dosiero'];

        for ($indekso = count($this->lingvostoko) -1;
             $indekso >= 0;
             $indekso --)
            {
                $lingvo = $this->lingvostoko[$indekso];
                $traduko = $this->donu_tradukon_en($dosiero, $cxeno, $lingvo);
                if ($traduko) {
                    return array($traduko, $lingvo);
                }
            }
        return null;
    }

    function donu_ajnan_tradukon_aux_erarindikon($cxeno) {
        $traduko = $this->donu_ajnan_tradukon($cxeno);
        if ($traduko == null) {
            return array($this->indiku_tradukmankon($cxeno, $dosiero), "");
        }
        return $traduko;
    }

    /**
     * redonas array() kun ĉiuj tradukoj.
     *
     * Ne okazas iuj anstataŭoj en la rezulto.
     *
     * @param tradcheno $origina_cheno
     * @return array ({@link lingvokodo} => {@link u8string})
     */
    function donu_cxiujn_tradukojn($cxeno)
    {
        $¢enokaj = analizu_chenon($cxeno, $this->dosierstoko);
        $cxeno = $¢enokaj['cheno'];
        $dosiero =$¢enokaj['dosiero'];

        $sql = datumbazdemando(array("iso2", "traduko"),
                               "tradukoj",
                               array("dosiero" => $dosiero,
                                     "cheno" => $cxeno));
        $rez = sql_faru($sql);
        $tradukoj = array();
        while ($linio = mysql_fetch_assoc($rez)) {
            $tradukoj[$linio["iso2"]] = $linio["traduko"];
        }
        return $tradukoj;
        
    }


    /**
     * kreas cxenon por indiki tutan mankon de traduko.
     */
    function indiku_tradukmankon($cheno, $dosiero) {
        return "&lt;" . $dosiero."#".$cheno."&gt;";
    }

    /**
     * 
     */
    function traduko_mankas_piednoto($nova_lingvo) {
        if (is_object($this->piednotilo)) {
            $teksto = ne_tradukita_piednoto($nova_lingvo,
                                            $this->aktuala_lingvo());
            return $this->piednotilo->kreu_piednoton($teksto, $nova_lingvo);
        }
        return "!!piednotilo mankas!!";
    }

    /**
     * donas tradukon al la aktuala lingvo.
     * @return null se mankas la traduko, alikaze la traduko.
     */
    function donu_aktualan_tradukon($cxeno) {
        return $this->donu_tradukon_en_lingvo($cxeno,
                                    $this->aktuala_lingvo());
    }

    /**
     * @return null, se mankas la traduko.
     */
    function donu_tradukon_en_lingvo($cxeno, $lingvo) {
        $¢enokaj = analizu_chenon($cxeno, $this->dosierstoko);
        $cxeno = $¢enokaj['cheno'];
        $dosiero =$¢enokaj['dosiero'];
        return $this->donu_tradukon_en($dosiero, $cxeno, $lingvo);
    }

    /**
     * sercxas tradukon por la cxeno en la indikita lingvo.
     * @return u8string|null Se mankas la traduko en tiu lingvo, redonas null,
     *                       alikaze la traduko.
     */
    function donu_tradukon_en($dosiero, $cxeno, $lingvo)
    {
        debug_echo("<!-- tradukilo::donu_tradukon_en(" . $dosiero . ", ".
                   $cxeno . ", " . $lingvo . ")\n -->");
        $listo = $this->preparu_cxiujn_tradukojn($dosiero, $lingvo);
        //        debug_echo("<!-- listo: " . var_export($listo, true) . "\n-->");
        if ($listo[$cxeno]) {
            return $listo[$cxeno];
        }
        return null;
    }

    /**
     * eltrovas cxiujn tradukojn por cxenoj en la aktuala dosiero kaj lingvo,
     * por sxpari poste datumbazalirojn.
     * @return array array(cxeno => traduko)
     */
    function preparu_cxiujn_tradukojn($dosiero, $lingvo)
    {
        $listo = &$this->tradukmemoro[$lingvo.'-'.$dosiero];
        if (is_array($listo))
            return $listo;

        $listo = array();
        $rez = sql_faru(datumbazdemando(array("cheno", "traduko"),
                                        "tradukoj",
                                        array("dosiero" => $dosiero,
                                              "iso2" => $lingvo)
                                        ));
        while($linio = mysql_fetch_assoc($rez))
            {
                if ($lingvo = 'eo') {
                    $listo[$linio['cheno']] = al_utf8($linio['traduko']);
                }
                else {
                    $listo[$linio['cheno']] = $linio['traduko'];
                }
            }
        mysql_free_result($rez);
        return $listo;
    }



} // class Tradukilo


/**
 * @abstract
 */
class Piednotilo {

    /**
     * Kreas novan piednoton.
     * @param u8string $teksto la teksto de la piednoto.
     * @param u8string $signo_propono proponita signo. Se "" (aux ne donita),
     *                 kreas mem signon.
     * @return u8string la signo uzenda por la piednoto.
     * @abstract
     */
    function kreu_piednoton($teksto, $signo_propono="") {
        darf_nicht_sein();
    }

}


function metu_piednotsistemon(&$piednotilo) {
    $ilo = &kreuTradukilon();
    $ilo->metu_piednotsistemon($piednotilo);
}





/**
 * redonas la tradukilo-objekton.
 *
 * Se tia ankoraux ne ekzistas, kreas novan.
 * @return Tradukilo
 */
function &kreuTradukilon() {
    if (!is_object($GLOBALS['kutima_tradukilo'])) {
        $GLOBALS['kutima_tradukilo'] = & new Tradukilo();
    }
    return $GLOBALS['kutima_tradukilo'];
}


/**
 * tradukas laŭ alia lingvo ol la kutima.
 *
*
 * @param tradcheno $origina_cheno
 * @param asciistring $lingvo 
 * @return u8string
 */
function CH_lau($cxeno, $lingvo) {
    $ilo = &kreuTradukilon();
    $traduko = $ilo->donu_tradukon_en_lingvo($cxeno, $lingvo);
    return $traduko;
}



/**
 * serĉas tradukon el la traduk-datumbazo en la aktuala lingvo.
 *
 * @param tradcheno $origina_cheno la identigilo de ĉeno en unu el
 *      la formatoj priskribitaj en la
 *      {@link traduko.php dosiera dokumentaĵo}.
 * @param u8string $... anstataŭaĵoj - ili estos enmetitaj
 *         en lokoj, kie aperas {1} ktp. en la tradukita teksto.
 * @return u8string
 */
function CH($cxeno) {
    $ilo = &kreuTradukilon();
    //    debug_echo ("<!-- " . var_export($ilo, true) . "-->");
    $traduko = $ilo->donu_ajnan_tradukon_aux_erarindikon($cxeno);
    // debug_echo("<!-- traduko: " . var_export($traduko, true) . "-->");
    $args = func_get_args();
    unset($args[0]);
    $rezulto = anstatauxu_numere($traduko[0], $args);

    // piednoto pri la anstatauxo-lingvo, se necesas:
    if ($traduko[1] and
        $traduko[1] != $ilo->aktuala_lingvo())
        {
            $rezulto .= $ilo->traduko_mankas_piednoto($traduko[1]);
        }
    return $rezulto;
}

/**
 * sercxas lingvodependan version de ligo kaj kreas
 * HTML-ligon el gxi.
 *
 * @return array("<a href='..'>", "</a>");
 */
function CH_ligo($cxeno) {
    $ilo = &kreuTradukilon();
    $traduko = $ilo->donu_ajnan_tradukon_aux_erarindikon($cxeno);
    if ($traduko[1] == "") {
        // tute mankas tradukoj
        return array($traduko[0]."[<a>",
                     "</a>]");
    }
    else {
    // TODO: piednoto pri la anstatauxo-lingvo, se necesas.
        return array("<a href='" . $traduko[1] . "'>",
                     "</a>");
    }
}

/**
 * trovas tradukon kaj anstataŭas en la rezulto
 *  nomitajn variablojn.
 *
 * @param tradcheno $origina_cheno
 *         identigilo por la tradukenda cheno.
 * @param array $listo array de la formo
 *           ŝlosilo => valoro
 * aŭ       
 *           globvar
 *     -  Ĉiu apero de {$ŝlosilo} en la traduk-rezulto estos
 *          anstataŭota per valoro.
 *     -  Ĉiu apero de {$globvar} estas anstataŭota per
 *          $GLOBALS[globvar].
 *     - la anstataŭoj okazos en la sinsekvo de $listo.
 *
 * @param u8string $...
 *     Aldone (fakte antaŭ la menciitaj ŝanĝoj) la kutimaj
 *     anstataŭoj de {@link CH()} de {1} ktp. per la restantaj
 *     argumentoj estos farotaj.
 * @uses Tradukilo::donu_ajnan_tradukon()
 */
function CH_repl($cxeno, $listo) {
    $ilo = &kreuTradukilon();
    $traduko = $ilo->donu_ajnan_tradukon($cxeno);

    $args = func_get_args();
    array_shift($args);
    unset($args[0]);

    $teksto = anstatauxu_numere($traduko[0], $args);
    $teksto = anstatauxu_tekste($teksto, $listo);

    // TODO: piednoto pri la anstatauxo-lingvo, se necesas.

    return $teksto;
}



function CH_lau_repl($cxeno, $lingvo, $listo) {
    $ilo = &kreuTradukilon();
    $traduko = $ilo->donu_tradukon_en_lingvo($cxeno, $lingvo);
    return anstatauxu_tekste($traduko, $listo);
}

/**
 * Anstatauxas esprimojn de la formo {1}, {2}, ...
 * per la lauxa elemento de la listo.
 *
 * @param u8string $teksto
 * @param array $listo - indeksita de 1
 */
function anstatauxu_numere($teksto, $listo) {
    
    /* e = PREG_REPLACE_EVAL:
     * interpretu la anstatauxajxon kiel
     * PHP-esprimo post trakto de \\1.
     */
    return preg_replace("/\{(\d*)\}/e",
                          "\$listo[\\1]", $teksto);
}

/**
 * Anstatauxas esprimojn de la formo {$var}
 * en la teksto laux la listo.
 *
 * @param array $listo array de la formo
 *           ŝlosilo => valoro
 * aŭ       
 *           globvar
 *     -  Ĉiu apero de {$ŝlosilo} en la traduk-rezulto estos
 *          anstataŭota per valoro.
 *     -  Ĉiu apero de {$globvar} estas anstataŭota per
 *          $GLOBALS['globvar'].
 *     - la anstataŭoj okazos en la sinsekvo de $listo.
 *
 */
function anstatauxu_tekste($teksto, $listo) {
    foreach($listo AS $sxlosilo => $valoro)
        {
            if (is_string($sxlosilo))
                {
                    $teksto = str_replace('{$'. $sxlosilo . '}',
                                          $valoro,
                                          $teksto);
                }
            else
                {
                    $teksto = str_replace('{$'. $valoro . '}',
                                          $GLOBALS[$valoro],
                                          $teksto);
                }
        }
    return $teksto;
 }





/**
 * redonas array() kun tradukoj de tiu cxeno en cxiuj lingvoj.
 *
 * Ne okazas iuj anstataŭoj en la rezulto.
 *
 * @param tradcheno $origina_cheno
 * @return array ({@link lingvokodo} => {@link u8string})
 */
function CH_mult($cxeno) {
    $ilo = &kreuTradukilon();
    return $ilo->donu_cxiujn_tradukojn($cxeno);
}


/**
 * informas la traduksistemon, ke ni nun eniris/eniros novan dosieron.
 *
 * @param urlstring $dosiero la (interna) nomo de la nova dosiero,
 *                       eble ankaŭ relativa al la antaŭa loko.
 *                  se ne donita, ni prenas la nomon de tiu dosiero,
 *                  kiu vokas tiun funkcion.
 * @see eliru_dosieron()
 */
function eniru_dosieron($dosiero="") {
    $ilo = &kreuTradukilon();
    $ilo->eniru_dosieron($dosiero);
}


/**
 * informas la traduksistemon, ke ni nun eliros/-is iun tradukendan
 * dosieron.
 *
 * La sistemo reŝaltas al la antaŭe uzita dosiero.
 * @see eniru_dosieron()
 */ 
function eliru_dosieron() {
    $ilo = &kreuTradukilon();
    $ilo->eliru_dosieron();
}

/**
 * informas la traduksistemon, ke ni nun volas tradukon en
 * alia lingvo.
 * @param lingvokodo $lingvo
 */
function eniru_lingvon($lingvo) {
    $ilo = &kreuTradukilon();
    $ilo->eniru_lingvon($lingvo);
}

/**
 * informas la traduksistemon, ke ni ne plu volas tradukojn
 * al la lasta lingvo, do resxaltas al antauxa lingvo.
 */
function eliru_lingvon() {
    $ilo = &kreuTradukilon();
    $ilo->eliru_lingvon();
}



/**
 * donas tradukon de datumbazero laŭ lingvo.
 *
 * @param string $tabelo (abstrakta) tabelnomo
 * @param string $kampo kamponomo
 * @param string $id identigilo de la datumbaza objekto
 * @param string $lingvo la ISO-kodo de la lingvo.
 * @return string la tradukon de tiu valoro.
 */
function traduku_datumbazeron($tabelo, $kampo, $id, $lingvo) {

    $dosiero = $GLOBALS['agordoj']["db-trad-prefikso"] . ':/' . $tabelo."/".$kampo;
    
//     $query =
//         "SELECT traduko FROM `". $GLOBALS['agordoj']['db_tabelo'] . "` " .
//         " WHERE (dosiero = '$dosiero') " .
//         "   AND (iso2 = '$lingvo') " .
//         // ĉeno + 0: estas stranga maniero konverti cheno al numero, uzante
//         //         nur la komencon (kie estas ciferoj), forĵetante la reston.
//         "   AND (cheno+0 = '$id')";

//     debug_echo("<!--" . $query . "-->");

    if (is_numeric($id)) {
        $sql = datumbazdemando('traduko',
                               'tradukoj',
                               array('dosiero' => $dosiero,
                                     'iso2' => $lingvo,
                                     '(cheno+0)' => $id));
    }
    else {
        $sql = datumbazdemando('traduko',
                               'tradukoj',
                               array('dosiero' => $dosiero,
                                     'iso2' => $lingvo,
                                     'cheno' => $id));
    }
    $rez = sql_faru($sql);
    switch(mysql_num_rows($rez)) {
    case 0:
        // traduko mankas
        return null;
    case 1:
        $linio = mysql_fetch_assoc($rez);
        return
            $lingvo == 'eo' ?
            transformu_x_al_eo($linio['traduko']):
            $linio['traduko'];
    default:
        // pluraj tradukoj por sama dosiero + lingvo + ĉeno - ne okazu.
        darf_nicht_sein("pluraj tradukoj por " . $dosiero . " # " .$id .
            " [" + $lingvo + "]");
    }

}


?>