<?

/**
 * Ilo por produkti la dosierujan arbon en Javascript-formo
 *  por elekti la dosieron redaktendan.
 *
 * Ni kreas JS-kodon, kiu vokas funkciojn el la Treeview-komponento de
 * http://www.treeview.net (kiu mem estas en {@link chenlisto.php} kaj
 * kelkaj Javascript-dosieroj).
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


header("Content-type: text/javascript; charset=utf-8");

require_once("iloj.php");
kontrolu_uzanton();
?>
// You can find instructions for this file here:
// http://www.treeview.net

// Decide if the names are links or just the icons
USETEXTLINKS = 1;  //replace 0 with 1 for hyperlinks

// Decide if the tree is to start all open or just showing the root folders
STARTALLOPEN = 0; //replace 0 with 1 to show the whole tree

// konservu la staton
PRESERVESTATE = 1;

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
    $query = "CREATE TEMPORARY TABLE IF NOT EXISTS db_trad_esperanto ( dosiero VARCHAR(100), cheno VARCHAR(255), PRIMARY KEY(dosiero, cheno) )";
    mysql_query($query)
        or die(mysql_error());
    mysql_query("TRUNCATE db_trad_esperanto")
        or die(mysql_error());
    $query = "INSERT INTO db_trad_esperanto SELECT dosiero, cheno FROM $tabelo WHERE iso2='$chefa'";
    mysql_query($query)
        or die(mysql_error());
    $query = "CREATE TEMPORARY TABLE IF NOT EXISTS db_trad_nacia_lingvo ( dosiero VARCHAR(100), cheno VARCHAR(255), PRIMARY KEY(dosiero, cheno) ) ";
    mysql_query($query)
        or die(mysql_error());
    mysql_query("TRUNCATE db_trad_nacia_lingvo")
        or die(mysql_error());
    $query = "INSERT INTO db_trad_nacia_lingvo SELECT dosiero, cheno FROM $tabelo WHERE iso2='$lingvo'";
    mysql_query($query)
        or die(mysql_error());
    $query = "CREATE TEMPORARY TABLE IF NOT EXISTS db_trad_diferenco ( dosiero VARCHAR(100), cheno VARCHAR(255), PRIMARY KEY(dosiero, cheno) )";
    mysql_query($query)
        or die(mysql_error());
    mysql_query("TRUNCATE db_trad_diferenco")
        or die(mysql_error());
    $query = "INSERT INTO db_trad_diferenco SELECT a.* FROM db_trad_esperanto=a LEFT OUTER JOIN db_trad_nacia_lingvo=b ON a.dosiero = b.dosiero AND a.cheno = b.cheno WHERE b.dosiero IS NULL";
    mysql_query($query)
        or die(mysql_error());
    if ($montru == "ambau") {
        $query = "INSERT INTO db_trad_diferenco SELECT dosiero, cheno FROM $tabelo WHERE iso2='$lingvo' AND stato=1";
        mysql_query($query)
            or die(mysql_error());
    }
    $query = "SELECT dosiero, COUNT(cheno) AS nombro FROM db_trad_diferenco GROUP BY dosiero ORDER BY dosiero";
 }
$result = mysql_query($query)
    or die(mysql_error());


$trovitaj[0] = "";
$nombroj[0] = 0;
//$trovitaj[1] = $agordoj['db-trad-prefikso']. ':/';
//$nombroj[1] = 0;
//$patroj[1] = 0;

// TODO: kalkulu sumon por "".

$akt_num = 2;
while ($row = mysql_fetch_array($result)) {
    $parts = explode("/", $row["dosiero"]);
    $cheno = "";
    $antaua_cheno="";
    $antaua_indekso = 0;
    for ($i = 0; $i < count($parts) ; $i++) {
        $cheno .= $parts[$i] . "/";
        $index = array_search($cheno, $trovitaj);
        if ($index === false) {
            $parto = rtrim($parts[$i], ":");
            $trovitaj[$akt_num] = $cheno;
            $patroj[$akt_num] = $antaua_indekso;
            $nombroj[$akt_num] = 0;
            $tekstoj[$akt_num] =
                $tradukoj["chefdosierujo"][$cheno]
                or $tekstoj[$akt_num] = al_utf8($parto);
            $index = $akt_num;
            $akt_num++;
        }
        $nombroj[$index] += $row["nombro"];
        $antaua_cheno = $cheno;
        $antaua_indekso = $index;
    }
    // regxustigu la nomon, gxi antauxe igxis $row["dosiero"] . "/".
    $nombroj[0] += $row["nombro"];
    $trovitaj[$index] = $row["dosiero"];
//     $patroj[$akt_num] = array_search($cheno, $trovitaj);
//     $nombroj[$akt_num] = $row["nombro"];
//     $tekstoj[$akt_num] = al_utf8($parts[count($parts) - 1]);
//     $akt_num++;
 } // while

$query = "DROP TABLE IF EXISTS db_trad_esperanto";
mysql_query($query);
$query = "DROP TABLE IF EXISTS db_trad_nacia_lingvo";
mysql_query($query);
$query = "DROP TABLE IF EXISTS db_trad_diferenco";
mysql_query($query);

//  echo "
//   /* trovitaj: " . var_export($trovitaj, true) . "
//      patroj:   " . var_export($patroj, true) . "
//      nombroj:  " . var_export($nombroj, true) . "
//      tekstoj:  " . var_export($tekstoj, true) . "
//    */ ";

$tekstoj[0] = $tradukoj['chio-tradukenda'];
// $tekstoj[1] = $tradukoj['chiuj-datumbaztabeloj'];



/* fld0 = gFld("<?= $tradukoj['chio-tradukenda']; ?> (<?= $nombroj[0] ?>)"); */


foreach($trovitaj AS $i => $trovo) {
    //for ($i = 0; $i < count($trovitaj); $i++) {
    if ("" == $trovitaj[$i] or substr($trovitaj[$i], -1) == "/") {
        echo ("\n".
              "fld" . $i. " = gFld('" . $tekstoj[$i] . " (" .
              $nombroj[$i] .")');\n");
        // node-ID
        echo("fld" . $i. ".xID = '" . $trovitaj[$i]. "';\n");
        if (isset($patroj[$i])) { 
            echo ("insFld(fld" . $patroj[$i] . ", fld" . $i . ");\n");
        }
    }
    else {
        echo ("tmp = insDoc(fld" . $patroj[$i] .
              ", gLnk('R', '" . $tekstoj[$i] . " (" . $nombroj[$i] .
              ")', 'redaktilo.php?lingvo=" . $_REQUEST['lingvo'] .
              "&dosiero=" . $trovitaj[$i] . "&montru=" .
              $_REQUEST['montru']."'));\n");
        // node-ID
        echo ("tmp.xID = '" .$trovitaj[$i]. "';\n");
        if ($trovitaj[$i] == $dosiero) {
            echo "var dosierujoj = new Array();\n";
            $numero = $i;
            $nombrilo = 0;
            while ($numero = $patroj[$numero]) {
                echo ("dosierujoj[". $nombrilo++ . "] = fld"
                      . $numero . ";\n");
            }
        }
    }
 }

?>

foldersTree = fld0;
