<?

/**
 * Baza biblioteko por la tradukilo, uzata de ĉiuj
 * aliaj dosieroj.
 *
 * @author Paul Ebermann (lastaj ŝanĝoj) + teamo E@I (ikso.net)
 * @version $Id$
 * @package aligilo
 * @subpackage tradukilo
 * @copyright 2005-2008 Paul Ebermann, ?-2005 E@I-teamo
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */

/**
 */

if (!isset($GLOBALS['prafix'])) {
    $GLOBALS['prafix'] = dirname(__FILE__) . "/../..";
 }

require_once($GLOBALS['prafix'] . "/iloj/iloj.php");

//echo "<!-- " . __FILE__ . "-->";
//echo "<!-- " . dirname(__FILE__) . "-->";

    // Pretigu $agordoj, $tradukoj, kaj $trad_lingvoj.
require_once(dirname(__FILE__) . "/agordoj.php");


// defaulxta "protokolo", se oni donis ne liston de pluraj.
if (!is_array($agordoj["dosierujo"])) {
    $agordoj["dosierujo"] = array('ujo' => $agordoj["dosierujo"]);
 }

// ni absolutigas cxiujn dosierujo-nomojn, por povi pli bone
// kompari.
foreach($GLOBALS['agordoj']['dosierujo'] AS $xxx_id => $xxx_loko) {
    $GLOBALS['agordoj']['dosierujo'][$xxx_id] = realpath($xxx_loko);
}


if (!function_exists("konektu")) {

    /**
     * konektas al la datumbazo kaj redonas la konekto-objekton.
     *
     * @return resource MySQL-konekto-objekto.
     */
    function konektu() {
        return malfermu_datumaro();
    }
 }

// require_once($GLOBALS['prafix'] ."/konfiguro/moduso.php");
// require_once($GLOBALS['prafix'] ."/konfiguro/datumaro.php");

// require_once($GLOBALS['prafix'] . "/konfiguro/opcioj.php");
// require_once($GLOBALS['prafix'] . "/iloj/iloj_sql.php");

$GLOBALS['agordoj']['db_tabelo'] = traduku_tabelnomon("tradukoj");


/*
    eval('
    function konektu()
    {
        global $agordoj;
        static $result;
        if ($result) return $result;

        $result = mysql_pconnect($agordoj["db_host"], $agordoj["db_uzanto"], $agordoj["db_pasvorto"]);
        if (!$result)
            return false;
        if (!mysql_select_db($agordoj["db_nomo"]))
            return false;
        mysql_query("set names utf8"); 
        return $result;
    }
    ');
}
*/
        
if (!function_exists("al_utf8")) {

    /**
     * konvertas (esperantan) tekston en x-metoda kodigo al UTF-8.
     * 
     */
    function al_utf8($cxeno)
    {
        $cxeno = str_replace("CX", "Ĉ", $cxeno);
        $cxeno = str_replace("GX", "Ĝ", $cxeno);
        $cxeno = str_replace("HX", "Ĥ", $cxeno);
        $cxeno = str_replace("JX", "Ĵ", $cxeno);
        $cxeno = str_replace("SX", "Ŝ", $cxeno);
        $cxeno = str_replace("UX", "Ŭ", $cxeno);

        $cxeno = str_replace("Cx", "Ĉ", $cxeno);
        $cxeno = str_replace("Gx", "Ĝ", $cxeno);
        $cxeno = str_replace("Hx", "Ĥ", $cxeno);
        $cxeno = str_replace("Jx", "Ĵ", $cxeno);
        $cxeno = str_replace("Sx", "Ŝ", $cxeno);
        $cxeno = str_replace("Ux", "Ŭ", $cxeno);

        $cxeno = str_replace("cx", "ĉ", $cxeno);
        $cxeno = str_replace("gx", "ĝ", $cxeno);
        $cxeno = str_replace("hx", "ĥ", $cxeno);
        $cxeno = str_replace("jx", "ĵ", $cxeno);
        $cxeno = str_replace("sx", "ŝ", $cxeno);
        $cxeno = str_replace("ux", "ŭ", $cxeno);

        $cxeno = str_replace("C'x", "Cx", $cxeno);
        $cxeno = str_replace("G'x", "Gx", $cxeno);
        $cxeno = str_replace("H'x", "Hx", $cxeno);
        $cxeno = str_replace("J'x", "Jx", $cxeno);
        $cxeno = str_replace("S'x", "Sx", $cxeno);
        $cxeno = str_replace("U'x", "Ux", $cxeno);

        $cxeno = str_replace("c'x", "cx", $cxeno);
        $cxeno = str_replace("g'x", "gx", $cxeno);
        $cxeno = str_replace("h'x", "hx", $cxeno);
        $cxeno = str_replace("j'x", "jx", $cxeno);
        $cxeno = str_replace("s'x", "sx", $cxeno);
        $cxeno = str_replace("u'x", "ux", $cxeno);

        return $cxeno;
    }
}



/**
 * Analizas ĉenon.
 *
 * Eblaj formato de ĉenoj:
 *
 * - ĉeno    (prenas ĝin el la lasta per
 *               {@link eniru_dosieron} anoncita dosiero)
 * - #ĉeno   (identa)
 * - dosiero#ĉeno  (prenas la dosieron en la sama dosierujo)
 * - dosierujo/dosiero#ĉeno  (iras al alia dosierujo, relative)
 * - /dosierujo/dosiero#ĉeno  (duon-absoluta, uzas la saman
 *                               "protokolon")
 * - proto:/dosierujo/dosiero#ĉeno  (indikas absolutan lokon)
 *
 * - ~#ĉeno  malatentas la dosieron de {@link eniru_dosieron},
 *             sed provas mem eltrovi, kiu dosiero vokas nin.
 *
 * @param tradcheno $origina_cheno la ĉeno analizenda
 * @param string|array  $baza_dosiero Se estas cxeno, uzenda en
 *                         ghisdatigo-moduso, tiam ni uzas tiun
 *                        (internan) nomon kiel bazon (por ĉiuj
 *                         formoj krom proto:/...)
 *                      Se estas array, ni uzas la lastan elementon
 *                      de gxi, krom en la '~#'-kazo, kiam ni prenas
 *                      la vokantan dosieron.
 *                      Se "" (aux ne donita), ni prenas la lastan 
 *                      elementon de $GLOBALS['traduko_dosieroj']
 *                      kiel bazo.
 * @return array  <code>
 *   array('dosiero' => tuta_dosiernomo,
 *         'cheno' => ĉeno),
 * </code>
 *    kie tuta_dosiernomo enhavas proto:/dosierujo/dosiero.
 */
function analizu_chenon($origina_cheno, $baza_dosiero="")
{
    debug_echo("<!-- analizu_chenon('" . $origina_cheno. "', " .
               var_export($baza_dosiero, true) . ")-->");

    //     echo ("<!--(ac) origina_cheno: " . $origina_cheno .
    //           ($baza_dosiero? ", baza_dosiero: " . $origina_cheno : "") .
    //           " \n-->");

    list($dosiero, $cxeno) = explode('#', $origina_cheno, 2);

    debug_echo ("<!--(ac) dosiero: " . $dosiero . ", cxeno: " . $cxeno . "\n-->");
    if ($dosiero == '~') {
        // formo "~#cheno"
        if (is_string($baza_dosiero)) {
            return array('dosiero' => $baza_dosiero,
                         'cheno' => $cxeno);
        } 
        $abs_dosiero = eltrovu_vokantan_dosieron();
        $dosiero = absoluta_dosiernomo_al_interna($abs_dosiero);
        debug_echo("<!--(ac) dosiero: " . $dosiero . ", cxeno: " .
                   $cxeno . "\n-->");
        return array('dosiero' => $dosiero,
                     'cheno' => $cxeno);
    }  // if ~

    if (is_array($baza_dosiero)) {
        $baza_dosiero = end($baza_dosiero);
    }
    if (!$baza_dosiero) {
        darf_nicht_sein();
        $baza_dosiero = end($GLOBALS['traduko_dosieroj'])
            or $baza_dosiero = 'nedifinita:/nedifinita';
    }

    debug_echo ("<!--(ac) baza_dosiero: " . $baza_dosiero . "\n-->");


    if ($cxeno == "") {
        // formo "ĉeno" sen #, do ĉeno estas en $dosiero
        return array('dosiero' => $baza_dosiero,
                     'cheno' => $dosiero);
    }
    return array('dosiero' => kunmetu_uri_relative($dosiero, $baza_dosiero),
                 'cheno' => $cxeno);
}

/**
 * konvertas dosiernomon (absoluta en la dosiersistemo) al
 * interna URI-formo.
 *
 * @param urlstring $abs_dosiero
 * @return urlstring la sama dosiero en formo interna formo.
 */
function absoluta_dosiernomo_al_interna($abs_dosiero) {
    // ni trasercxu la antaux-difinitajn dosierujojn
    $abs_dosiero = realpath($abs_dosiero);
    foreach($GLOBALS['agordoj']['dosierujo'] AS $sxlosilo => $loko) {
        $loko .= "/";
        $loklen = strlen($loko);
        if (substr($abs_dosiero, 0, $loklen) == $loko) {
            return  $sxlosilo . ":/" . substr($abs_dosiero, $loklen);
        }
    } // foreach
    return 'abs:' . $abs_dosiero;
    
}


/**
 * kreas el relativa URI absolutan.
 *
 * Ne estas tuta implementado de la algoritmo de RFC 2396:
 *  Gxi tute gxuste traktas URIojn sen "authority"- kaj query-parto.
 * (Pli detale jen la problemoj:
 *  - se $dosiero estas absoluta (sen authority), ni
 *    forjxetas la authority de la baza URI.
 *  - se baza havas authority-parton, kaj $dosiero enhavas suficxe
 *    multajn /../, tio ankaux povas sxangxi la authority-parton.
 *  - se $baza_dosiero enhavas query-part, kaj tiu enhavas '/',
 *    ni uzas la parton gxis tie kiel bazan dosierujon, al kiu
 *    aldonigxas la relativa $dosiero.
 *  - se $dosiero enhavas query-part, ankaux en tiu ni
 *    simpligas /../ ktp (eble ecx transirante la limon en
 *    kazo kiel   bla/hallo?xy/../bb,
 *    kiu igxas   bla/bb.
 *  )
 *  Cxiuj tiuj limigoj ne gravas por nia uzo, kie estas nur URIoj
 *  sen authority kaj query-part (kaj ankaux sen ;-parametroj.)
 *
 * @param urlstring $dosiero URI, eble relativa
 * @param urlstring $baza_dosiero absoluta URI, kiu estas uzata
 *               kiel baza URI dum la absolutigado.
 */
function kunmetu_uri_relative($dosiero, $baza_dosiero) {

    debug_echo("<!-- kunmetu_uri_relative(". $dosiero . ", ".$baza_dosiero .")-->");

    if ($dosiero == "") {
        return $baza_dosiero;
    }
    if (strpos($dosiero, ':/')) {
        // $dosiero estas jam absoluta loko
        return $dosiero;
    }

    if ($dosiero[0] == '/') {
        list($baza_protokolo,$resto) = explode(':/', $baza_dosiero, 2);
        //        echo ("<!-- baza_protokolo: " . $baza_protokolo .
        //              ", resto: " . $resto . "\n -->");
        return $baza_protokolo . ':' . $dosiero;
    }
  
    $lastastreko = strrpos($baza_dosiero, '/');
    $baza_dosierujo = substr($baza_dosiero, 0, $lastastreko);

    // eble $dosiero komenciĝas per ../.
    $dosiero = simpligu_dosiernomon($baza_dosierujo . '/' . $dosiero);

    debug_echo("<!-- ==> " . $dosiero . "\n-->");

    return $dosiero;
    
}



/**
 * simpligas dosiernomon aŭ URIon.
 *
 * - forigo de superfluaj /./, /../, '//'.
 *
 *  @param urlstring $nomo
 * @return urlstring
 */
function simpligu_dosiernomon($nomo) {
    $malnova = "";
    $sercxo = array('#/\./#', '#/([^/]{3,}|[^/.][^/]|\.[^/.])/\.\./#', '#//#');
    $anstatauxo = array('/', '/', '/');
    while ($nomo != $malnova) {
        $malnova = $nomo;
        $nomo = preg_replace($sercxo, $anstatauxo, $nomo);
    }
    // se nun ankoraux restis '/../', (kiun ne kaptis la antauxa sercxo,
    //  ekzemple en  'datumbazo:/../ekzemplo' , tiam ni forigos gxin
    //  entute.
    $nomo = preg_replace('#/../#', '/', $nomo);
    return $nomo;
}





/**
 * eltrovas la unuan dosieron vokintan, kiu ne estas la sama
 * kiel tiu, el kiu nia vokanto estis vokita.
 */
function eltrovu_vokantan_dosieron() {
    $listo = debug_backtrace();
    //    echo "<!-- ". var_export($listo, true) . "-->";
    array_shift($listo); // ni forĵetas nian vokon
    $nia_vokanto = $listo[0]['file']; // kiu vokis nian vokanton
    foreach($listo AS $vokanto_informoj) {
        if ($nia_vokanto != $vokanto_informoj['file']) {
            return $vokanto_informoj['file'];
        }
    }
}



    
function listigu_chiujn_lingvojn() {
    global $agordoj;
    $db = konektu();
    $tabelo = $agordoj["db_tabelo"];
    $query = "SELECT DISTINCT iso2 FROM $tabelo";
    $result = mysql_query($query);
        
    $arejo = array();
    while ($row = mysql_fetch_array($result)) {
        array_push($arejo, $row["iso2"]);
    }
        
    return $arejo;
}
    
function petu_ensaluton($mesagxo, $petu_denove = 1) {
    global $tradukoj;
    if ($petu_denove) {
        header('WWW-Authenticate: Basic realm="lernu!"');
        header('HTTP/1.0 401 Unauthorized');
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?= $tradukoj["tradukejo-titolo"] ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<h1><?= $tradukoj["tradukejo-titolo"] ?></h1>
<p><?= $mesagxo ?></p>
</body>
</html>
<?
     exit;
}

/**
 * @todo sendependigu de lernu-aferoj, eble uzu la aligilo-uzantnomojn.
 */
function kontrolu_uzanton() {
    global $agordoj;
    if ($agordoj["salutado"]) {
        if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SESSION['informoj'])) {
            petu_ensaluton("Por ensaluti, bonvolu uzi la samajn salutnomon kaj pasvorton, kiujn vi uzas por ensaluti ĉe <i>lernu!</i>.");
        } else {
            $db = konektu();
            $query = "SELECT * FROM lernu_uzanto WHERE salutnomo = '{$_SERVER['PHP_AUTH_USER']}'";
            $result = mysql_query($query);
            if (mysql_num_rows($result) > 0) {
                $row = mysql_fetch_array($result);
                if ($_SERVER['PHP_AUTH_PW'] != $row["pasvorto"]) {
                    petu_ensaluton("Vi entajpis nevalidan salutnomon aŭ pasvorton.  Por ensaluti, bonvolu uzi la samajn salutnomon kaj pasvorton, kiujn vi uzas por ensaluti ĉe <i>lernu!</i>.");
                } else {
                    if ($row["tradukanto"] != "jes") {
                        petu_ensaluton("Vi ne havas tradukrajton.  Por fariĝi tradukanto, bonvolu skribi al tradukado@lernu.net.", 0);
                    } 
                    session_start();
                    $_SESSION["informoj"] = $row;

                }
            } else {
                petu_ensaluton("Vi entajpis nevalidan salutnomon aŭ pasvorton.  Por ensaluti, bonvolu uzi la samajn salutnomon kaj pasvorton, kiujn vi uzas por ensaluti ĉe <i>lernu!</i>.");
            }
        }
    }
}


/**
 */

define("PRESKAU_LASTA_CHENO", "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff");


/**
 * kreas redaktileron por redakti unu ĉenon.
 *
 * @param string $ordono
 * @param u8string $stato (teksto por montri)
 * @param string $class (por CSS-identigo)
 * @param string $dosiero
 * @param boolean $montru_dosieron ĉu montri la nomon de la dosiero en la kadro (true) aŭ nur la ĉenon (false)?
 * @param string $cheno
 * @param string $lingvo
 * @param tradstring $originalo
 * @param tradstring $traduko
 * @param tradstring $komento
 * @param string $tradukinto
 */
function skatolo_por_cheno($ordono, $stato, $class,
                           $dosiero, $montru_dosieron, $cheno, $lingvo,
                           $originalo = "", $traduko = "",
                           $komento = "", $tradukinto = "",
                           $pre_formata=0, $preredaktilo="") {
    global $tradukoj, $agordoj;
    static $nombrilo = 0;
    $nombrilo++;
    //      if ($ordono == "aldonu") $ordono_teksto = $tradukoj["aldonu-ordono"];
    //      elseif ($ordono == "redaktu") $ordono_teksto = $tradukoj["redaktu-ordono"];
    //      elseif ($ordono == "forigu") $ordono_teksto = $tradukoj["forigu-ordono"];
    $ordono_teksto = $tradukoj[$ordono . "-ordono"];
?>

<table class="<?= $class ?>">
<tr>
<td><input type="hidden" name="dosiero-<?= $nombrilo ?>" value="<?= $dosiero ?>" />
<input type="hidden" name="cheno-<?= $nombrilo ?>" value="<?= $cheno ?>" />
<input type="hidden" name="iso2-<?= $nombrilo ?>" value="<?= $lingvo ?>" />
<?= $tradukoj["cheno"] ?> <?= $montru_dosieron ? $dosiero . "#" : "" ?><?= $cheno ?></td>
<td align="right" valign="top"><?= $tradukoj["stato"] ?>&nbsp;<span class="<?= $class ?>"><?= $stato ?></span></td>
</tr>
<tr>
<td><?php ;
    echo $tradukoj["chefa-lingvo"];
    if ($pre_formata)
        echo "<pre>";
    else
        echo "<b>";
    echo htmlspecialchars(al_utf8($originalo));
    if ($pre_formata)
        echo "</pre>";
    else
        echo "</b>";
 ?></td>
<td align="right" valign="bottom"><?= $ordono_teksto ?>:&nbsp;<input type="checkbox" name="<?= $ordono ?>-<?= $nombrilo ?>" value="jes" onclick="a = document.getElementById('traduko-<?= $nombrilo ?>'); if (a) a.disabled = !this.checked; a = document.getElementById('komento-<?= $nombrilo ?>'); if (a) a.disabled = !this.checked;" /><br />
<?php;
    if ($preredaktilo) {
        echo $tradukoj['pre-formatu'];
        jes_ne_bokso('preformatu-' . $nombrilo, (boolean)$pre_formata);
    }


?>
</td>
</tr>
<?
   if ($ordono != "forigu") {
       if (strlen($traduko) == 0) {
           $vicoj = 4;
       } else {
           $vicoj = min(3 + substr_count($traduko, "\n"), 10);
       }
?>
<tr><td colspan="2"><textarea id="traduko-<?= $nombrilo ?>" name="traduko-<?= $nombrilo ?>" cols="60" rows="<?= $vicoj ?>" disabled="disabled"><?= htmlspecialchars($traduko) ?></textarea></td></tr>
<?
            if ($lingvo == $agordoj["chefa_lingvo"]) {
                $komentovicoj = min(max(2,
                                        1+ substr_count($komento, "\n")),
                                    8);
?>
<tr><td colspan="2"><?= $tradukoj["komento"] ?></td></tr>
<tr><td colspan="2"><textarea id="komento-<?= $nombrilo ?>" name="komento-<?= $nombrilo ?>" cols="60" rows="<?= $komentovicoj ?>" disabled="disabled"><?= htmlspecialchars($komento) ?></textarea></td></tr>
<?
            } else {
                if ($komento) {
?>
<tr><td colspan="2"><?= $tradukoj["komento"] ?> <b><?= al_utf8($komento) ?></b></td></tr>
<?
                }
            }
            
            if ($tradukinto) {
?>
<tr><td colspan="2"><?= $tradukoj["tradukinto"] ?> <b><?= $tradukinto ?></b></td></tr>
<?
            }
                                                                   }
?>
</table>

<?
      }
