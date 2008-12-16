<?

/**
 * Tradukilo-interfaco al aliaj programpartoj.
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
 * @author Paul Ebermann (lastaj ŝanĝoj) + teamo E@I (ikso.net)
 * @version $Id$
 * @package aligilo
 * @subpackage tradukilo
 * @copyright 2005-2008 Paul Ebermann, ?-2005 E@I-teamo
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */



/**
 * @see iloj.php
 */


require_once(dirname(__FILE__) . "/iloj.php");
if ($agordoj["parametro_nomo"]) {
    lingvo($_GET[$agordoj["parametro_nomo"]]);
 }

$antaumontro_tradukendaj = 0;
    
    
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
    global $traduko_dosieroj;

    //    echo "<!--\n eniru_dosieron(" . $dosiero . "); antauxe: " . var_export($traduko_dosieroj, true) . " -->";

    if ($dosiero == "") {
        $dosiero = absoluta_dosiernomo_al_interna(eltrovu_vokantan_dosieron());
    }

    $traduko_dosieroj[]=
        kunmetu_uri_relative($dosiero, end($traduko_dosieroj));

    //    echo "<!-- eniru_dosieron(...), poste: " .
    //        var_export($traduko_dosieroj, true) . " -->";
}
   
/**
 * informas la traduksistemon, ke ni nun eliros/-is iun tradukendan
 * dosieron.
 *
 * La sistemo reŝaltas al la antaŭe uzita dosiero.
 * @see eniru_dosieron()
 */ 
function eliru_dosieron() {
    global $traduko_dosieroj;
    array_pop($traduko_dosieroj);
}


/**
 * indikas kaj eltrovas la aktuale uzatan lingvon.
 *
 * @param asciistring $iso2 kodo de la nova lingvo.
 *              se ne donita, ni nenion ŝanĝas.
 *              se malplena, ni ŝaltas al la ĉefa lingvo.
 * @return asciistring la nova uzata lingvo.
 */
function lingvo($iso2 = "nenio") {
    global $trad_lingvo, $agordoj;
    if ($iso2 == "nenio") {
        // Nenion ŝanĝu.
    } else {
        if (strlen($iso2) >= 2) {
            $trad_lingvo = $iso2;
        } else {
            $trad_lingvo = $agordoj["chefa_lingvo"];
        }
    }
    return ($trad_lingvo ? $trad_lingvo : $agordoj["chefa_lingvo"]);
}

$GLOBALS['lingvostoko'] = array();

function eniru_lingvon($lingvo) {
    global $lingvostoko, $trad_lingvo;
    $lingvostoko[]= $trad_lingvo;
    $trad_lingvo = $lingvo;

}

function eliru_lingvon() {
    global $lingvostoko, $trad_lingvo;
    $trad_lingvo = array_pop($lingvostroko);
}


/**
 * transformas ligon per aldono de parametro por
 * plutransdoni la lingvon.
 *
 * (Nur uzata en CH_lig.)
 */
function lig($ligilo) {
    global $agordoj;
    if ($agordoj["parametro_nomo"]) {
        if (strpos($ligilo, ":") === false) { // certigu ke ne temas pri ligilo al ekstera retejo
            $loko = strrpos($ligilo, "#");
            if ($loko === false) { // prenu "#parto" de la fino de la ligilo, se ekzistas
                // faru nenion
            } else {
                $interna_ligilo = substr($ligilo, $loko);
                $ligilo = substr($ligilo, 0, $loko);
            }
            if (strpos($ligilo, "?") === false) {
                $dividilo = "?";
            } else {
                $dividilo = "&";
            }
            $ligilo = $ligilo . $dividilo . $agordoj["parametro_nomo"] . "=" . lingvo() . $interna_ligilo;
        }
    }
    return $ligilo;
}
    
/**
 * (nekonata funkcio)
 *
 * Hmm, mi ne tute certas, kion faras tiu funkcio, sed ĝi ĉiuokaze
 * ne estas uzata de nia programo (2008-10-15, PE)
 *
 * @todo forĵetu, aŭ trovu uzon kaj bone dokumentu.
 */
function CH_lig($origina_cheno) {
    $args = func_get_args();
    $novaj_parametroj = array($origina_cheno);
        
    for ($i = 1; $i < count($args); $i++) {
        $ligilo = lig($args[$i]);
        if (substr($args[$i], 0, 7) == "http://") {
            array_push($novaj_parametroj, "<a href=\"$ligilo\" target=\"_blank\">");
        } else {
            array_push($novaj_parametroj, "<a href=\"$ligilo\">");
        }
        array_push($novaj_parametroj, "</a>");
    }
    return call_user_func_array("CH", $novaj_parametroj);
}
    
/**
 * redonas tradukitan ĉenon transformita por Javascript-uzo.
 *
 * Do, ni eskapigas danĝerajn signojn, kaj transformigas
 * linfinojn en iun literalan formon.
 *
 * chuck: Ĉi tiu funkcio ebligas ke tekstoj estu uzeblaj en JS.
 *
 * @uses CH()
 * @param tradcheno $origina_cheno
 * @param u8string $... aliaj parametroj por {@link CH()}.
 * @return u8string
 */
function CHJS($origina_cheno) {
    return str_replace("\n", "\\n",
                       str_replace("\r\n", "\\n",
                                   addslashes(CH($origina_cheno))));
}
    
/**
 * tradukas laŭ alia lingvo ol la kutima.
 *
 * la uzendan lingvon oni donu kiel lastan parametron, la resto
 * de la parametroj estas kiel en {@link CH()}.
 *
 * @uses CH()
 * @param tradcheno $origina_cheno
 * @param u8string $... aliaj parametroj por {@link CH()}.
 * @param asciistring $lingvo 
 * @return u8string
 */
function CH_lau($origina_cheno) {
    $args = func_get_args();
    // ni forigas la lastan parametron
    eniru_lingvon( array_pop($args));

    $ret = call_user_func_array("CH", $args);

    eliru_lingvon();
    return $ret;
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
 *
 * @uses CH()
 */
function CH_repl($origina_cheno, $listo)
{
    // ĉiuj parametroj
    $params = func_get_args();

    // ni forigas $listo el la parametroj, sed
    // ja transdonas $origina_cheno.
    array_shift($params);
    $params[0] = $origina_cheno;

    // kaj per tiuj parametroj vokas CH.
    $teksto = call_user_func_array('CH', $params);

    // en la rezulto ni faras ankoraŭ pli da anstataŭoj.
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
 * eltrovas, ĉu ekzistas traduko en la aktuala lingvo.
 *
 * @param tradcheno $origina_cheno
 * @return int  1, se ekzistas traduko, 0 alikaze.
 * @todo pripensu, ĉu ni bezonas tiun funkcion - se jes,
 *     pripensu pli bonan rezult-tipon (kaj plibeligu implementadon),
 *     se ne, forĵetu.
 */
function ekzistas($origina_cheno) {
    global $traduko_dosieroj, $trad_lingvo, $db, $agordoj;
        
    if ($_GET["antaumontro"])
        $trad_lingvo = $_GET["lingvo"];
    if (!$trad_lingvo)
        $trad_lingvo = $agordoj["chefa_lingvo"];

    // $cheno, $dosiero
    extract(analizu_chenon($origina_cheno));

//     $db = konektu();
//     $tabelo = $agordoj["db_tabelo"];
//     $query = "SELECT traduko FROM $tabelo WHERE dosiero"
//         . " = '$dosiero' AND cheno = '$cheno' AND iso2 = '$trad_lingvo'";

    return eltrovu_gxenerale("COUNT(*)",
                             'tradukoj',
                             array("dosiero" => $dosiero,
                                   "cheno" => $cheno,
                                   "iso2" => $trad_lingvo));
//     $result = mysql_query($query);
//     return mysql_num_rows($result);
}


/**
 * baza traduko-funkcio.
 * @param $tradcheno $cxeno la identigilo de ĉeno en unu el
 *      la formatoj priskribitaj en la
 *      {@link traduko.php dosiera dokumentaĵo}.
 * @param lingvokodo $lingvo
 */
function traduku_al($cxeno, $lingvo)
{
    global $traduko_dosieroj, $trad_lingvo, $db,
        $antaumontro_tradukendaj, $agordoj;
    global $nuna_dosiero, $nuna_trad_lingvo, $nunaj_chenoj;

    // $cheno, $dosiero
    extract(analizu_chenon($cxeno));

    //    echo ("<!--(CH) dosiero: " . $dosiero . ", cheno: " . $cheno . "\n-->");

        
    if (($dosiero == $nuna_dosiero) and ($lingvo == $nuna_trad_lingvo)) {
        // Jam ni havas la necesajn chenojn en $nunaj_chenoj.
    } else {
        $nunaj_chenoj = array();
        $nuna_dosiero = $dosiero;
        $nuna_trad_lingvo = $lingvo;
            
        $db = konektu();
        $tabelo = $agordoj["db_tabelo"];
        $query = "SELECT cheno, traduko FROM $tabelo WHERE dosiero"
            . " = '$dosiero' AND iso2 = '$lingvo'";
        $result = mysql_query($query);
        while ($row = mysql_fetch_array($result)) {
            $nunaj_chenoj[$row["cheno"]] = $row["traduko"];
        }
    }
    // Nun $nunaj_chenoj estas plena je tradukoj por tiu chi dosiero kaj lingvo.
    // Faru $row kiel antaue.
        
    unset($row);
    if ($nunaj_chenoj[$cheno]) {
        $row = array();
        $row["traduko"] = $nunaj_chenoj[$cheno];
    }

    if (!$row and !($_GET["antaumontro"] == "jes")) {
        $prenis_eo = 1;
        $db = konektu();
        $tabelo = $agordoj["db_tabelo"];
        $query = "SELECT traduko FROM $tabelo WHERE dosiero"
            . " = '$dosiero' AND cheno = '$cheno' AND iso2 = '" . $agordoj["chefa_lingvo"] . "'";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);
    }
        
    if (!$row) {
        $antaumontro_tradukendaj++;
        return "&lt;$nuna_dosiero#$cheno&gt;";
    } else {
        if ($lingvo == "eo" or $prenis_eo) {
            $row["traduko"] = al_utf8($row["traduko"]);
        }
        if ($prenis_eo) {
            $GLOBALS['bezonis-eo-tekston'] = true;
            if (marku_traduko_eo_anstatauxojn) {
                $row['traduko'] .= "¹";
            }
        }
        $args = func_get_args();
        $rezulto = preg_replace("/\{(\d*)\}/e", "\$args[\\1]",
                                $row["traduko"]);
        //            echo "<!-- dosiero: '$dosiero', cheno: '$cheno', rezulto: '$rezulto' -->";
        return $rezulto;
    }
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
function CH($origina_cheno) {
    global $traduko_dosieroj, $trad_lingvo, $db, $antaumontro_tradukendaj, $agordoj;
    global $nuna_dosiero, $nuna_trad_lingvo, $nunaj_chenoj;
    
    //        echo("<!--\n CH(" .$origina_cheno . ") kun traduko_dosieroj: " . var_export($traduko_dosieroj, true) . ", trad_lingvo: " . $trad_lingvo .
    //             ", vokanta dosiero: " . eltrovu_vokantan_dosieron() .
    //             "\n -->");

    if ($_GET["antaumontro"])
        $trad_lingvo = $_GET["lingvo"];
    if (!$trad_lingvo)
        $trad_lingvo = $agordoj["chefa_lingvo"];

    return traduku_al($origina_cheno, $trad_lingvo);

}
    
/**
 * redonas array() kun ĉiuj tradukoj.
 *
 * Ne okazas iuj anstataŭoj en la rezulto.
 *
 * @param tradcheno $origina_cheno
 * @return array ({@link lingvokodo} => {@link u8string})
 */
function CH_mult($origina_cheno) {
    global  $db, $agordoj;
   
    // $cheno, $dosiero
    extract(analizu_chenon($origina_cheno));
    
    $db = konektu();
    $tabelo = $agordoj["db_tabelo"];
    $query =
        "SELECT iso2, traduko ". 
        "  FROM $tabelo ".
        " WHERE dosiero = '$dosiero' ".
        "   AND cheno = '$cheno'";
    $result = mysql_query($query);
    $tradukoj = array();
    while ($row = mysql_fetch_array($result)) {
        $tradukoj[$row["iso2"]] = $row["traduko"];
    }
    return $tradukoj;
}
    
/**
 * kreas  HTML-an liston de ĉiuj tradukoj
 * de iu ĉeno.
 *
 * @todo eble forĵetu (ne estas uzata nun), alikaze uzu
 *     $agordoj['chefa_lingvo'] ktp.
 *
 * @param tradcheno $origina_cheno
 * @return u8string HTML-a listo de ĉiuj tradukoj.
 */
// por la pagho http://nova.ikso.net/filmo_eo_estas/index.php
function CH_chiuj($origina_cheno) {
    global $traduko_dosieroj, $db, $agordoj;
    $nur = $_GET["nur"]; // por montri nur unu el la tradukoj

    // $cheno, $dosiero
    extract(analizu_chenon($origina_cheno));

    $db = konektu();
    $tabelo = $agordoj["db_tabelo"];
        
    $query = "SELECT traduko FROM $tabelo WHERE dosiero"
        . " = '$dosiero' AND cheno = '$cheno' AND iso2 = 'eo'";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result);
    $tradukoj = "<b>eo</b>: <i>" . al_utf8($row["traduko"]) . "</i>";
        
    $query = "SELECT iso2, traduko FROM $tabelo WHERE dosiero"
        . " = '$dosiero' AND cheno = '$cheno'";
    if ($nur != "") {
        $query .= " AND iso2 = '$nur'";
    }
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result)) {
        if ($row["iso2"] == "eo") {
            continue;
        }
        $tradukoj .=  "<br /><b>" .  $row["iso2"] . "</b>: " . $row["traduko"];
    }
    return $tradukoj;
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

    $sql = datumbazdemando('traduko',
                           'tradukoj',
                           array('dosiero' => $dosiero,
                                 'iso2' => $lingvo,
                                 '(cheno+0)' => $id));
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