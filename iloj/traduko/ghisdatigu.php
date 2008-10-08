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

function traktu_tabelon($tabelo, $kampoj) {
    $tabelo_interna = traduku_tabelnomon($tabelo);
    foreach($kampoj AS $kampo) {
        traktu_kampon($tabelo, $tabelo_interna, $kampo);
    }
}

function traktu_kampon($tabelnomo, $tabelo_interna, $kamponomo)
{
    global $trovitaj, $tabelo, $chefa, $tradukoj;

    // pseŭdo-dosiernomo
    $dosiernomo = $agordoj["db-trad_prefikso"] .
        $tabelnomo . "/" . $kamponomo;

    /*
     * ideo: ni trairas ambaŭ samtempe ordigitaj laŭ ID, kaj
     * tiel trovas samtempe aldonendajn kaj forigendajn.
     */

    $sql_org = 
        "\n SELECT  `ID`, `" . $kamponomo . "` " .
        "\n   FROM `" . $tabelo_interna . "` " .
        "\n   ORDER BY `ID` ASC ";

    $sql_trad =
        "\n SELECT (0 + `cheno`) AS `ID`, `traduko` " .
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
        echo "<pre>org: $id_org, trad: $id_trad</pre>\n";

        if ($id_trad < $id_org) {
            // ni havas tradukon sen originalo - ne traktu nun, estos
            // trovita poste.

            // next(trad)
            $linio_trad = mysql_fetch_assoc($rez_trad);
        }
        else {
            // ni trovis linion en la originala tabelo, ĉu
            // kun aŭ sen traduko
            $trovitaj[]= $dosiernomo . "#" . $id_org;
            $trad_kampo = strtr($linio_org[$kamponomo], "^", "x");

            if ($id_org == $id_trad) {
                // linio kun jam ekzistanta traduko

                // ==> ni komparu ĝin nun.
                if ($trad_kampo != $linio_trad['traduko']) {
                    // TODO: proponu aktualigon
                    skatolo_por_cheno("aktualigu",
                                      $tradukoj["stato-aktualigenda-db"],
                                      "retradukenda", $dosiernomo, 1,
                                      $id_org, $chefa,
                                      $linio_trad['traduko'],
                                      $linio_org[$kamponomo]);
                }
                // next(trad)
                $linio_trad = mysql_fetch_assoc($rez_trad);
            }
            else {
                // ni havas linion en la originala tabelo sen tradukoj

                // TODO: proponu aldonon
                skatolo_por_cheno("aldonu", $tradukoj["stato-aldonenda-db"],
                                  "aldonenda", $dosiernomo, 1, $id_org,
                                  $chefa, "", $trad_kampo);
            }
            
            // next(org)
            $linio_org = mysql_fetch_assoc($rez_org);
            
        } // else

    }// while

}  // traktu_kampon


function traktu_dosierujon($dosierujo) {
    echo "traktas " . $dosierujo . " ... <br />\n";
    global $agordoj;
    $dir = @opendir($dosierujo);
    while ($file = @readdir($dir))
		{
			if (($file == ".") or ($file == "..") or
                (substr($file, -4) == 'test')) {
                // faru nenion
			} elseif (@is_dir($dosierujo . "/" . $file)) {
				traktu_dosierujon($dosierujo . "/" . $file);
			} else {
				$i = strrpos($file, ".");
				if ($i > 0 and in_array(substr($file, $i+1), $agordoj["sufiksoj"])) {
					traktu_dosieron($dosierujo . "/" . $file);
				}
			}
		}
}

function traktu_dosieron($dosiero) {
    //    echo "(traktas " . $dosiero . " ...) <br />\n";
    global $trovitaj, $tabelo, $chefa, $tradukoj;
		
    if (isset($_GET["parta"]) && (filemtime($dosiero) < time() - (60*60*24*7))) {
        return;
    }
		
    $tuto = join("\n", file($dosiero));
    preg_match_all("/CH(_lig|_lau|JS|_repl|_mult|)\s*\(\s*[\"']([^\"']*)[\"']\s*(,|\))/",
                   $tuto, $chenoj);
    $chenoj = $chenoj[2];
    for ($i = 0; $i < count($chenoj); $i++) {
        $cheno = $chenoj[$i];
        if (substr($cheno, 0, 1) == "/") {
            $loka_dosiero = strtok($cheno, "#");
            $loka_cheno = strtok("#");
        } else {
            $baza_dos = substr($dosiero, strlen($GLOBALS['cxef_dosierujo']));
            $listo = explode('#', $cheno);
            if (count($listo) > 1)
                {
                    $loka_dosiero = substr($baza_dos, 0, strrpos($baza_dos, '/')+1)
                        . $listo[0];
                    $loka_cheno = $listo[1];
                }
            else
                {
                    $loka_dosiero = $baza_dos;
                    $loka_cheno = $cheno;
                }

        }
        if (!in_array($loka_dosiero . "#" . $loka_cheno, $trovitaj)) {
            $trovitaj[] = $loka_dosiero . "#" . $loka_cheno;
            $query = "SELECT traduko FROM $tabelo WHERE "
                . "dosiero = '$loka_dosiero' AND cheno = '$loka_cheno' "
                . "AND iso2 = '$chefa'";
            $result = mysql_query($query);
            $row = mysql_fetch_array($result);
            if (!$row) {
                skatolo_por_cheno("aldonu", $tradukoj["stato-aldonenda"], "aldonenda", $loka_dosiero, 1, $loka_cheno, $chefa);
            }
        }
    }
}


	$db = konektu();
	$tabelo = $agordoj["db_tabelo"];
	$chefa = $agordoj["chefa_lingvo"];
	$trovitaj = array();
echo "<div>\n";
foreach($agordoj["dosierujo"] AS $dosierujo) {
    $cxef_dosierujo = realpath($dosierujo);
    traktu_dosierujon($cxef_dosierujo); 
}
traktu_tabelojn($db);
echo "</div>";


?>
<?
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
?>
<?
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