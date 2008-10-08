<?

/**
 * Ilo por produkti la dosierujan arbon en Javascript-formo
 *  por elekti la dosieron redaktendan.
 *
 * Ni kreas JS-kodon, kiu vokas funkciojn el la Treeview-komponento de
 * http://www.treeview.net (kiu mem estas en {@link chenlisto.php}.
 *
 * @author Paul Ebermann (lastaj sxangxoj) + teamo E@I (ikso.net)
 * @version $Id$
 * @package aligilo
 * @subpackage tradukilo
 * @copyright 2005-2008 Paul Ebermann, ?-2005 E@I-teamo
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */

/**
 */


header("Content-type: text/javascript; charset=utf-8");

    require_once("iloj.php");
    kontrolu_uzanton();
?>
// You can find instructions for this file here:
// http://www.treeview.net

// Decide if the names are links or just the icons
USETEXTLINKS = 1  //replace 0 with 1 for hyperlinks

// Decide if the tree is to start all open or just showing the root folders
STARTALLOPEN = 0 //replace 0 with 1 to show the whole tree

ICONPATH = 'grafikajhoj/' //change if the gif's folder is a subfolder, for example: 'images/'

<?
	$trovitaj = array();
	$patroj = array();
	$nombroj = array();
?>
<?
	$db = konektu();
	$tabelo = $agordoj["db_tabelo"];
	$chefa = $agordoj["chefa_lingvo"];
	if ($montru == "chion") {
		$query = "SELECT dosiero, COUNT(cheno) AS nombro FROM $tabelo WHERE iso2='$chefa' GROUP BY dosiero";
	} else if ($montru == "retradukendajn") {
		$query = "SELECT dosiero, COUNT(cheno) AS nombro FROM $tabelo WHERE iso2='$lingvo' AND stato=1 GROUP BY dosiero";
	} else if ($montru == "tradukendajn" or $montru == "ambau") {
		$query = "CREATE TEMPORARY TABLE db_trad_esperanto ( dosiero VARCHAR(100), cheno VARCHAR(255), PRIMARY KEY(dosiero, cheno) )";
		mysql_query($query)
            or die(mysql_error());
		$query = "INSERT INTO esperanto SELECT dosiero, cheno FROM $tabelo WHERE iso2='$chefa'";
		mysql_query($query)
            or die(mysql_error());
		$query = "CREATE TEMPORARY TABLE db_trad_nacia_lingvo ( dosiero VARCHAR(100), cheno VARCHAR(255), PRIMARY KEY(dosiero, cheno) )";
		mysql_query($query)
            or die(mysql_error());
		$query = "INSERT INTO nacia_lingvo SELECT dosiero, cheno FROM $tabelo WHERE iso2='$lingvo'";
		mysql_query($query)
            or die(mysql_error());
		$query = "CREATE TEMPORARY TABLE db_trad_diferenco ( dosiero VARCHAR(100), cheno VARCHAR(255), PRIMARY KEY(dosiero, cheno) )";
		mysql_query($query)
            or die(mysql_error());
		$query = "INSERT INTO diferenco SELECT a.* FROM esperanto=a LEFT OUTER JOIN nacia_lingvo=b ON a.dosiero = b.dosiero AND a.cheno = b.cheno WHERE b.dosiero IS NULL";
		mysql_query($query)
            or die(mysql_error());
        if ($montru == "ambau") {
        	$query = "INSERT INTO diferenco SELECT dosiero, cheno FROM $tabelo WHERE iso2='$lingvo' AND stato=1";
        	mysql_query($query)
                or die(mysql_error());
		}
		$query = "SELECT dosiero, COUNT(cheno) AS nombro FROM diferenco GROUP BY dosiero ORDER BY dosiero";
	}
	$result = mysql_query($query)
        or die(mysql_error());

	while ($row = mysql_fetch_array($result)) {
		$parts = explode("/", $row["dosiero"]);
		$cheno = "";
        $antaua_cheno="";
		for ($i = 0; $i < count($parts) - 1; $i++) {
			$cheno .= $parts[$i] . "/";
			if (!in_array($cheno, $trovitaj)) {
				array_push($trovitaj, $cheno);
				if ($antaua_cheno=="") {
					array_push($patroj, -1);
				} else {
					array_push($patroj, array_search($antaua_cheno, $trovitaj));
				}
				array_push($nombroj, 0);
			}
			$nombroj[array_search($cheno, $trovitaj)] += $row["nombro"];
			$antaua_cheno = $cheno;
		}
        array_push($trovitaj, $row["dosiero"]);
        array_push($patroj, array_search($cheno, $trovitaj));
        array_push($nombroj, $row["nombro"]);
	}

	$query = "DROP TABLE esperanto";
	mysql_query($query);
	$query = "DROP TABLE nacia_lingvo";
	mysql_query($query);
	$query = "DROP TABLE diferenco";
	mysql_query($query);

// echo "
// /* trovitaj: " . var_export($trovitaj, true) . "
//    patroj:   " . var_export($patroj, true) . "
//    nombroj:  " . var_export($nombroj, true) . "
// */ ";
?>

foldersTree = gFld("<?= $tradukoj["chiuj-dosieroj"] ?> (<?= $nombroj[0] ?>)")
fld0 = foldersTree

<?
	// Dosierujoj venu unuaj
	for ($i = 1; $i < count($trovitaj); $i++) {
		$parts = explode("/", $trovitaj[$i]);
		if (substr($trovitaj[$i], -1) == "/") {
?>
fld<?= $i ?> = insFld(fld<?= $patroj[$i] ?>, gFld("<?= $parts[count($parts)-2] ?> (<?= $nombroj[$i] ?>)"))
<?
		}
	}
?>

<?
	// Poste venu dosieroj
	for ($i = 1; $i < count($trovitaj); $i++) {
		$parts = explode("/", $trovitaj[$i]);
		if (substr($trovitaj[$i], -1) != "/") {
?>
insDoc(fld<?= $patroj[$i] ?>, gLnk("R", "<?= $parts[count($parts)-1] ?> (<?= $nombroj[$i] ?>)", "redaktilo.php?lingvo=<?= $lingvo ?>&dosiero=<?= $trovitaj[$i] ?>&montru=<?= $montru ?>"))
<?
			if ($trovitaj[$i] == $dosiero) {
				echo "var dosierujoj = new Array()\n";
				$numero = $i;
				$nombrilo = 0;
				while ($numero = $patroj[$numero]) {
?>
dosierujoj[<?= $nombrilo++ ?>] = fld<?= $numero?>

<?
				}
			}
		}
	}
?>