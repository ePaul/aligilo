<?

/**
 * Redaktilo por ĉenoj en unu dosieroj.
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

	function montru_unu($row) {
		global $dosiero, $lingvo, $montru, $tabelo, $chefa, $tradukoj;
		
        $cheno = $row["cheno"];
		if ($lingvo != $chefa) {
			$query2 = "SELECT traduko, stato, tradukinto FROM $tabelo WHERE iso2='$lingvo' "
				. "AND dosiero='$dosiero' AND cheno='$cheno'";
			$result2 = mysql_query($query2);
			$row2 = mysql_fetch_array($result2);
			if ($row2) {
				if ($montru == "chion" or (($montru == "retradukendajn" or $montru == "ambau") and $row2["stato"] == 1)) {
					skatolo_por_cheno("redaktu", $row2["stato"] == 1 ? $tradukoj["stato-retradukenda"] : $tradukoj["stato-ghisdata"], $row2["stato"] == 1 ? "retradukenda" : "gxisdata", $dosiero, 0, $cheno, $lingvo, $row["traduko"], $row2["traduko"], $row["komento"], $row2["tradukinto"]);
				}
			} else {
				if ($montru == "chion" or $montru == "tradukendajn" or $montru == "ambau") {
					skatolo_por_cheno("aldonu", $tradukoj["stato-tradukenda"], "tradukenda", $dosiero, 0, $cheno, $lingvo, $row["traduko"], "", $row["komento"], $row2["tradukinto"]);
				}
			}
		} else {
			skatolo_por_cheno("redaktu", $tradukoj["stato-ghisdata"], "gxisdata", $dosiero, 0, $cheno, $lingvo, "", $row["traduko"], $row["komento"], $row["tradukinto"]);
		}
	}
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
<?
	if ($dosiero) {
?>
<h1><?= $dosiero ?></h1>
<form method="post" action="konservu.php">
<?
		$db = konektu();
		$tabelo = $agordoj["db_tabelo"];
		$chefa = $agordoj["chefa_lingvo"];
		$chiuj_chenoj = array();
		$query = "SELECT cheno, traduko, komento, tradukinto FROM $tabelo WHERE iso2='$chefa' "
			. "AND dosiero='$dosiero'";
		$result = mysql_query($query);

		while ($row = mysql_fetch_array($result)) {
			$chiuj_chenoj[$row["cheno"]] = $row;
		}
		
        if (file_exists("..$dosiero")) {
	        $tuto = join("", file("..$dosiero"));
			preg_match_all("/CH\(\"([^\"]*)\"/", $tuto, $chenoj);
			$chenoj = $chenoj[1];
			for ($i = 0; $i < count($chenoj); $i++) {
				if ($chiuj_chenoj[$chenoj[$i]]) {
					montru_unu($chiuj_chenoj[$chenoj[$i]]);
					unset($chiuj_chenoj[$chenoj[$i]]);
				}
			}
		}
		
		while($ero = each($chiuj_chenoj)) {
			$row = $ero["value"];
			montru_unu($row);
		}
?>
<p>
<input type="submit" name="Konservu" value="<?= $tradukoj["konservu-butono"] ?>" />
<input type="hidden" name="lingvo" value="<?= $lingvo ?>" />
<input type="hidden" name="dosiero" value="<?= $dosiero ?>" />
<input type="hidden" name="de_kie_venis" value="redaktilo.php" />
<input type="hidden" name="montru" value="<?= $montru ?>" />
</p>
</form>
<?
	}
?>
</body>
</html>
