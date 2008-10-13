<?

/**
 * Serĉilo por novaj tradukendaj tekstoj
 *      (kaj superfluaj tradukitaj tekstoj).
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
    require_once("iloj.php");
    kontrolu_uzanton();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?= $tradukoj["tradukejo-titolo"] ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="tradukado.css" />
</head>
<body>
<h1><?= $tradukoj["ghisdatigo-titolo"] ?></h1>
<h2><?= $tradukoj["necesas-aldoni"] ?></h2>
<form method="post" action="konservu.php">
<?

function sercxu_db_tradukojn_en_dosiero($dosiero)
{
    $tuto = file($dosiero);
    foreach($tuto AS $linio) {
        if ($linio[0] == '#')
            continue;
        //        echo "<code>$linio</code><br/>\n";
        $subexpr = '\s+([a-zA-Z]+):\s+"([^"]+)"';
        if (preg_match('/tradukuKampon: "([^"]+)"\s+en:\s+"([^"]+)"((' . $subexpr . ')*);/',
                       $linio,
                       $rezultoj)) {
            list(, $kampo, $tabelo, $resto) = $rezultoj;
            //            echo "<pre>$resto</pre>\n";
            
            preg_match_all('/'. $subexpr . '/', $resto,
                           $atribtrovitaj, PREG_SET_ORDER);
            $atributoj = null;
            foreach($atribtrovitaj AS $trovajxo) {
                //                var_export($trovajxo);
                $atributoj[$trovajxo[1]] = $trovajxo[2];
            }
            //            echo "trovis: ($tabelo, $kampo, " . var_export($atributoj, true) . ")<br />";
            traktu_tabelon($tabelo, array($kampo), $atributoj);
        }
    }
}



function traktu_tabelojn() {
    echo "traktas datumbazajn tabelojn tradukendajn ...<br/>\n";
    // TODO: eble iom malgrandigu tiun dependecon
    //   (ni nur bezonas traduku_tabelnomon, ne
    //    la tutan ilo-dosieron, kaj ankaŭ ne ĉiujn
    //    opciojn.)
    require_once("../../konfiguro/opcioj.php");
    require_once("../../iloj/iloj_sql.php");

    //    echo "<pre> agordoj: " . var_export($GLOBALS['agordoj'], true ) . "</pre>";

    foreach($GLOBALS['agordoj']['datumbazo_tradukenda'] AS $tabelo => $kampoj) {
        traktu_tabelon($tabelo, $kampoj);
    }
}

function traktu_tabelon($tabelo, $kampoj, $atributoj) {
    $tabelo_interna = traduku_tabelnomon($tabelo);
    foreach($kampoj AS $kampo) {
        traktu_kampon($tabelo, $tabelo_interna, $kampo, $atributoj);
    }
}

function traktu_kampon($tabelnomo, $tabelo_interna,
                       $kamponomo, $atributoj)
{
//     echo ("traktu_kampon ( $tabelnomo, $kamponomo, ".
//           var_export($atributoj, true) . ")<br/>\n");

    global $trovitaj, $tabelo, $chefa, $tradukoj;

    require_once($GLOBALS['prafix'] . "/iloj/konvertiloj.php");

    //    echo "<!-- prefikso: " . $GLOBALS['agordoj']["db-trad-prefikso"] . "-->";

    // pseŭdo-dosiernomo
    $dosiernomo = $GLOBALS['agordoj']["db-trad-prefikso"] .':/'.
        $tabelnomo . "/" . $kamponomo;


    $helpValSQL = "";

    if ($atributoj['helpoteksto']) {
        $helpValSQL .= ", " . $atributoj['helpoteksto'] . " AS helpoteksto";
    }
    if ($atributoj['helpeDe']) {
        $helpValSQL .= ", " . $atributoj['helpeDe'] . " AS helpo";
    }

    /*
     * ideo: ni trairas ambaŭ samtempe ordigitaj laŭ ID, kaj
     * tiel trovas samtempe aldonendajn kaj forigendajn.
     */

    $sql_org = 
        "\n SELECT  `ID`, `" . $kamponomo . "` AS org" . $helpValSQL .
        "\n   FROM `" . $tabelo_interna . "` " .
        "\n   ORDER BY `ID` ASC ";

    $sql_trad =
        "\n SELECT (0 + `cheno`) AS `ID`, `cheno`, `traduko` " .
        "\n   FROM `" . $tabelo . "`  " .
        "\n   WHERE `dosiero` = '" . $dosiernomo . "' " .
        "\n     AND `iso2` = '" . $chefa . "' " .
        "\n     ORDER BY `ID` ASC ";

    echo "<pre>$sql_org</pre><pre>$sql_trad</pre>";

    $rez_org = mysql_query($sql_org);
    $rez_trad = mysql_query($sql_trad);

    $linio_org = mysql_fetch_assoc($rez_org);
    $linio_trad = mysql_fetch_assoc($rez_trad);
    while (true) {
        if (null == $linio_org) {
            // ne plu estas linioj en la originala tabelo
            if (null == $linio_trad) {
                break;
            }
            // TODO: listu la restantajn
            $id_org = PHP_INT_MAX;
            $id_trad = (int) $linio_trad['ID'];
        }
        else if (null == $linio_trad) {
            // ne plu estas linioj en la traduktabeloj, sed ja en la originala
            // TODO: listu la restantajn
            $id_org = (int) $linio_org['ID'];
            $id_trad = PHP_INT_MAX;
        }
        else {
            $id_org = (int) $linio_org['ID'];
            $id_trad = (int) $linio_trad['ID'];
        }
        //        echo "<pre>org: $id_org, trad: $id_trad</pre>\n";

        if ($id_trad < $id_org) {
            // ni havas tradukon sen originalo - ne traktu nun, estos
            // trovita poste.

            // next(trad)
            $linio_trad = mysql_fetch_assoc($rez_trad);
        }
        else {
            // ni trovis linion en la originala tabelo, ĉu
            // kun aŭ sen traduko
            
            $trad_kampo = eotransformado($linio_org['org'],
                                         "por-tradukilo");

            if ($id_org == $id_trad) {
                // linio kun jam ekzistanta traduko
                $cheno = $linio_trad['cheno'];

                // ==> ni komparu ĝin nun.
                if ($trad_kampo != $linio_trad['traduko']) {
                    // proponu aktualigon
                    skatolo_por_cheno("aktualigu",
                                      $tradukoj["stato-aktualigenda-db"],
                                      "retradukenda", $dosiernomo, 1,
                                      $cheno, $chefa,
                                      $linio_trad['traduko'],
                                      $trad_kampo);
                }
                // next(trad)
                $linio_trad = mysql_fetch_assoc($rez_trad);
            }
            else {
                // ni havas linion en la originala tabelo sen tradukoj
                $trovitaj[]= $dosiernomo . "#" . $id_org;

                if ($atributoj['helpoteksto']) {
                    $cheno =
                        ((string)$id_org) .
                        " (" . $linio_org['helpoteksto'] . ")";
                }
                if ($atributoj['helpeDe']) {
                    if ($atributoj['klarigoj']) {
                        
                        require_once($GLOBALS['prafix'] .
                                     "/iloj/iloj_tekstoj.php");
                        $helpdosiero =
                            $GLOBALS['prafix'] . $atributoj['klarigoj'];

                        $informoj =
                            donu_tekstpriskribon($linio_org['helpo'],
                                                 $helpdosiero);
                        $komento = eotransformado($informoj['priskribo'],
                                                  "por-tradukilo");
//                         echo "<pre>";
//                         //                        var_export();
//                         var_export($helpdosiero);
//                         var_export($komento);
//                         echo "</pre>";
                    }
                    else {
                        $komento = "";
                    }
                }
                else  {
                    $cheno = (string)$id_org;
                    $komento = "";
                }

                // proponu aldonon
                skatolo_por_cheno("aldonu", $tradukoj["stato-aldonenda-db"],
                                  "aldonenda", $dosiernomo, 1,
                                  $cheno,
                                  $chefa, "", $trad_kampo, $komento);
            }

            // por la listo de trovitajxoj
            $trovitaj[]= $dosiernomo . "#" . $cheno;
            // next(org)
            $linio_org = mysql_fetch_assoc($rez_org);
            
        } // else

    }// while

}  // traktu_kampon

/**
 * sercxas en  dosierujo pri cxenoj tradukendaj.
 * @param string $dosierujo dosierujnomo (sur disko), sen fina /
 * @param string $interna dosierujnomo (en datumbazo), sen fina /
 */
function traktu_dosierujon($dosierujo, $interna) {
    echo "traktas " . $dosierujo . " ... <br />\n";
    global $agordoj;
    $dir = @opendir($dosierujo);
    while ($file = @readdir($dir))
        {
            if (($file == ".") or ($file == "..") or
                (substr($file, -4) == 'test')) {
                // faru nenion
            } elseif (@is_dir($dosierujo . "/" . $file)) {
                traktu_dosierujon($dosierujo . "/" . $file,
                                  $interna . "/". $file);
            } else {
                $i = strrpos($file, ".");
                if ($i > 0 and in_array(substr($file, $i+1),
                                        $agordoj["sufiksoj"])) {
                    traktu_dosieron($dosierujo . "/" . $file,
                                    $interna . "/" . $file);
                }
            }
        }
}

/**
 * sercxas en dosiero pri cxenoj tradukendaj.
 * @param string $dosiero dosiernomo (sur disko)
 * @param string $interna dosiernomo (en datumbazo)
 */
function traktu_dosieron($abs_dosiero, $interna) {
    //    echo "(traktas " . $dosiero . " ...) <br />\n";
    global $trovitaj, $tabelo, $chefa, $tradukoj;
        
    if (isset($_GET["parta"]) && (filemtime($dosiero) < time() - (60*60*24*7))) {
        return;
    }
        
    $tuto = join("", file($abs_dosiero));
    preg_match_all("/CH(_lig|_lau|JS|_repl|_mult|)\s*\(\s*[\"']([^\"']*)[\"']\s*(,|\))/",
                   $tuto, $chenoj);
    $chenoj = $chenoj[2];
    for ($i = 0; $i < count($chenoj); $i++) {
        
        extract(analizu_chenon($chenoj[$i], $interna));
        
         if (!in_array($dosiero . "#" . $cheno, $trovitaj)) {
            $trovitaj[] = $dosiero . "#" . $cheno;
            $query = "SELECT traduko FROM $tabelo WHERE "
                . "dosiero = '$dosiero' AND cheno = '$cheno' "
                . "AND iso2 = '$chefa'";
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            if (!$row) {
                // mankas en la datumbazo
                skatolo_por_cheno("aldonu", $tradukoj["stato-aldonenda"],
                                  "aldonenda", $dosiero, 1, $cheno, $chefa);
            }
        }
    }
}


$db = konektu();
$tabelo = $agordoj["db_tabelo"];
$chefa = $agordoj["chefa_lingvo"];
$trovitaj = array();
echo "<div>\n";
foreach($agordoj["dosierujo"] AS $interna => $dosierujo) {
    traktu_dosierujon(realpath($dosierujo), $interna . ":"); 
}
traktu_tabelojn($db);
foreach($agordoj["db-trad-dosieroj"] AS $dosiero) {
    sercxu_db_tradukojn_en_dosiero($dosiero);
}
echo "</div>";


if (!isset($_GET["parta"])) {
    ?>
    <h2><?= $tradukoj["necesas-forigi"] ?></h2>
        <?  
        $query = "SELECT dosiero, cheno, traduko FROM $tabelo WHERE iso2 = '$chefa'";
    $result = mysql_query($query, $db);
    while ($row = mysql_fetch_array($result)) {
        if (!in_array($row["dosiero"] . "#" . $row["cheno"], $trovitaj)) {
            skatolo_por_cheno("forigu", $tradukoj["stato-forigenda"], "forigenda", $row["dosiero"], 1, $row["cheno"], $chefa, $row["traduko"]);
        }
    }
	}
?>
<p>
<input type="submit" name="Konservu" value="<?= $tradukoj["konservu-butono"] ?>" />
<!--input type="hidden" name="dosiero" value="<?= $dosiero ?>" /-->
<input type="hidden" name="de_kie_venis" value="ghisdatigu.php" />
<input type="hidden" name="lingvo" value="<?= $chefa ?>" />
<input type="hidden" name="montru" value="chion" />
</p>
</form>
</body>
</html>