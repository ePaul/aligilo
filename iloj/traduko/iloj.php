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
    $GLOBALS['prafix'] = dirname(___FILE__) . "/../..";
 }

//echo "<!-- " . __FILE__ . "-->";
//echo "<!-- " . dirname(__FILE__) . "-->";

    // Pretigu $agordoj kaj $trad_lingvoj.
require_once(dirname(__FILE__) . "/agordoj.php");

if (!is_array($agordoj["dosierujo"])) {
    $agordoj["dosierujo"] = array($agordoj["dosierujo"]);
 }

foreach($GLOBALS['agordoj']['dosierujo'] AS $id => $loko) {
    $GLOBALS['agordoj']['dosierujo'][$id] = realpath($loko);
}


if (!function_exists("konektu")) {
    function konektu() {
        require_once($GLOBALS['prafix'] ."/konfiguro/moduso.php");
        require_once($GLOBALS['prafix'] ."/konfiguro/datumaro.php");
        return malfermu_datumaro();
    }
 }

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

function alghustigu_dosiernomon($dosiero) {
    global $agordoj;
    if(preg_match( ':/[^/.]+$:', $dosiero))
        $dosiero .= ".php";

    // fortranĉu dosierujan nomon de la komenco
    foreach($agordoj["dosierujo"] AS $dosierujo) {
        $dosierujo = realpath($dosierujo);
        if (substr($dosiero, 0, strlen($dosierujo)) == $dosierujo)
            return substr($dosiero, strlen($dosierujo) - 1);
    }
    return $dosiero;
}

/**
 * Analizas cxenon.
 *
 * Eblaj formato de cxenoj:
 *
 * - cxeno    (prenas gxin el la lasta per
 *               {@link eniru_dosieron} anoncita dosiero)
 * - #cxeno   (identa)
 * - dosiero#cxeno  (prenas la dosieron en la sama dosierujo)
 * - dosierujo/dosiero#cxeno  (iras al alia dosierujo, relative)
 * - /dosierujo/dosiero#cxeno  (duon-absoluta, uzas la saman
 *                               "protokolon")
 * - proto:/dosierujo/dosiero#cxeno  (indikas absolutan lokon)
 *
 * - ~#cxeno  malatentas la dosieron de {@link eniru_dosieron},
 *             sed provas mem eltrovi, kiu dosiero vokas nin.
 *
 * @param asciistring $origina_cheno la cxeno analizenda
 * @param string      $baza_dosiero uzenda en ghisdatigo-moduso,
 *                         tiam ni uzas tiun (internan) nomon kiel
 *                         bazon (por cxiuj formoj krom proto:/...)
 *                      anstataux tiu donita al {@link eniru_dosieron}
 *                      aux la vokanta dosiero (kiu estus ghisdatigu.php).
 * @return array  <code>
 *   array('dosiero' => tuta_dosiernomo,
 *         'cheno' => cxeno),
 * </code>
 *    kie tuta_dosiernomo enhavas proto:/dosierujo/dosiero.
 */
function analizu_chenon($origina_cheno, $baza_dosiero="")
{
//     echo ("<!--(ac) origina_cheno: " . $origina_cheno .
//           ($baza_dosiero? ", baza_dosiero: " . $origina_cheno : "") .
//           " \n-->");

    list($dosiero, $cxeno) = explode('#', $origina_cheno, 2);

    //    echo ("<!--(ac) dosiero: " . $dosiero . ", cxeno: " . $cxeno . "\n-->");
    if ($dosiero == '~') {
        // formo "~#cheno"
        if ($baza_dosiero) {
            return array('dosiero' => $baza_dosiero,
                         'cheno' => $cxeno);
        } 
        $abs_dosiero = eltrovu_vokantan_dosieron();
        foreach($GLOBALS['agordoj']['dosierujo'] AS $sxlosilo => $loko) {
            $loko .= "/";
            $loklen = strlen($loko);
            if (substring($abs_dosiero, 0, $loklen) == $loko) {
                $dosiero = $sxlosilo . ":/" .
                    substring($abs_dosiero, $loklen);
                return array('dosiero' => $dosiero,
                             'cheno' => $cxeno);
            }
        } // foreach
        return array('dosiero' => 'abs:/' . $abs_dosiero,
                     'cheno' => $cxeno);
    }  // if ~

    if (!$baza_dosiero) {
        $baza_dosiero = end($GLOBALS['traduko_dosieroj'])
            or $baza_dosiero = 'nedifinita:/nedifinita';
    }

    //    echo ("<!--(ac) baza_dosiero: " . $baza_dosiero . "\n-->");
    

    if ($cxeno == "") {
        // formo "cxeno" sen #, do cxeno estas en $dosiero
        return array('dosiero' => $baza_dosiero,
                     'cheno' => $dosiero);
    }

    if ($dosiero == "") {
        // formo "#cxeno"
        return array('dosiero' => $baza_dosiero,
                     'cheno' => $cxeno);
    }

    if (strpos($dosiero, ':/')) {
        // $dosiero estas jam absoluta loko
        return array('dosiero' => $dosiero,
                     'cheno' => $cxeno);
    }

    if ($dosiero[0] == '/') {
        list($baza_protokolo,$resto) = explode(':/', $baza_dosiero, 2);
        //        echo ("<!-- baza_protokolo: " . $baza_protokolo .
        //              ", resto: " . $resto . "\n -->");
        return array('dosiero' => $baza_protokolo . ':' . $dosiero,
                     'cheno' => $cxeno);
    }

    
    $lastastreko = strrpos($baza_dosiero, '/');
    $baza_dosierujo = substr($baza_dosiero, 0, $lastastreko);

    // eble la dosiero komencigxas per ../.
    $dosiero = simpligu_dosiernomon($baza_dosierujo . '/' . $dosiero);

    return array('dosiero' => $dosiero,
                 'cheno' => $cxeno);

}



/**
 * simpligas dosiernomon aux URIon.
 *
 * - forigo de superfluaj /./, /../, '//'.
 *
 *  @param urlstring $nomo
 * @return urlstring
 */
function simpligu_dosiernomon($nomo) {
    $malnova = "";
    $sercxo = array('#/./#', '#/([^/]{3,}|[^/.][^/]|\.[^/.])/../#', '#//#');
    $anstatauxo = array('/', '/', '/');
    while ($nomo != $malnova) {
        $malnova = $nomo;
        $nomo = preg_replace($sercxo, $anstatauxo, $nomo);
    }
    return $nomo;
}





/**
 * eltrovas la unuan dosieron vokintan, kiu ne estas la sama
 * kiel tiu, el kiu nia vokanto estis vokita.
 */
function eltrovu_vokantan_dosieron() {
    $listo = debug_backtrace();
    //    echo "<!-- ". var_export($listo, true) . "-->";
    array_shift($listo); // ni forjxetas nian vokon
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
 * @todo: sendependigu de lernu-aferoj, eble uzu la aligilo-uzantnomojn.
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
function skatolo_por_cheno($ordono, $stato, $class, $dosiero, $montru_dosieron, $cheno, $lingvo, $originalo = "", $traduko = "", $komento = "", $tradukinto = "") {
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
<td><?= $tradukoj["chefa-lingvo"] ?> <b><?= htmlspecialchars(al_utf8($originalo)) ?></b></td>
<td align="right" valign="bottom"><?= $ordono_teksto ?>:&nbsp;<input type="checkbox" name="<?= $ordono ?>-<?= $nombrilo ?>" value="jes" onclick="a = document.getElementById('traduko-<?= $nombrilo ?>'); if (a) a.disabled = !this.checked; a = document.getElementById('komento-<?= $nombrilo ?>'); if (a) a.disabled = !this.checked;" /><br /></td>
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
?>