<?

/**
 * Parto de la redaktilo por konservi ŝanĝojn (aldonojn,
 *  forigojn, redaktojn) al/de la traduko-tabelo.
 *
 * @author Paul Ebermann (lastaj ŝanĝoj) + teamo E@I (ikso.net)
 * @version $Id$
 * @package aligilo
 * @subpackage tradukilo
 * @copyright 2005-2008 Paul Ebermann, ?-2005 E@I-teamo
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 * @todo adaptu la uzanto-nomojn al nia aligilo-situacio.
 */

/**
 */


    require_once("iloj.php");
    kontrolu_uzanton();

function estis_eraro() {
    echo "<pre>" . $GLOBALS['query'] . "</pre>";
    echo "<pre>" . mysql_error() . "</pre>";
    $GLOBALS['nombro_da_eraroj']++;
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
<?
    $db = konektu();
    $tabelo = $agordoj["db_tabelo"];
    $chefa = $agordoj["chefa_lingvo"];
    $nombro_da_aldonoj = 0;
    $nombro_da_redaktoj = 0;
    $nombro_da_forigoj = 0;
    $nombro_da_eraroj = 0;
    
foreach($_POST AS $nomo => $valoro) {
    list($ordono, $numero) = explode('-', $nomo, 2);
        switch($ordono) {
        case "aldonu":
            $loka_dosiero = $_POST["dosiero-$numero"];
            $loka_cheno = $_POST["cheno-$numero"];
            $loka_iso2 = $_POST["iso2-$numero"];
            $loka_traduko = $_POST["traduko-$numero"];
            $loka_komento = $_POST["komento-$numero"];
            $query =
                "INSERT INTO $tabelo " .
                "   SET dosiero    ='$loka_dosiero', " .
                "       cheno      = '$loka_cheno', " .
                "       iso2       = '$loka_iso2', " .
                "       traduko    = '$loka_traduko', ".
                // TODO: pli bona uzo de tradukinto
                "       tradukinto = '{$_SERVER['PHP_AUTH_USER']}', " .
                "       komento    = '$loka_komento'";
            $result = mysql_query($query);
            if ($result)
                $nombro_da_aldonoj++;
            else
                estis_eraro();
            break;
        case "redaktu":
        case "aktualigu":
            $loka_dosiero = $_POST["dosiero-$numero"];
            $loka_cheno = $_POST["cheno-$numero"];
            $loka_iso2 = $_POST["iso2-$numero"];
            $loka_traduko = $_POST["traduko-$numero"];
            $loka_komento = $_POST["komento-$numero"];
            $query =
                "UPDATE $tabelo " .
                "   SET traduko    = '$loka_traduko', ".
                "       tradukinto = '{$_SERVER['PHP_AUTH_USER']}'," .
                "       komento    = '$loka_komento', ".
                "       stato      =  0 ".
                "   WHERE dosiero = '$loka_dosiero'".
                "     AND cheno   = '$loka_cheno'" .
                "     AND iso2    = '$loka_iso2'";
            $result = mysql_query($query);
            if ($result) {
                $nombro_da_redaktoj++;
                if ($loka_iso2 == $chefa) {
                    $query =
                        "UPDATE $tabelo ".
                        "   SET stato = 1" .
                        "   WHERE dosiero = '$loka_dosiero' ".
                        "     AND cheno   = '$loka_cheno'" .
                        "     AND iso2   <> '$chefa'";
                    $result = mysql_query($query);
                    if (!$result)
                        estis_eraro();
                }
            }
            else
                estis_eraro();
            break;
        case "forigu":
            $numero = substr($nomo, 7);
            $loka_dosiero = $_POST["dosiero-$numero"];
            $loka_cheno = $_POST["cheno-$numero"];
            $query =
                "DELETE FROM $tabelo ".
                "   WHERE dosiero = '$loka_dosiero' " .
                "     AND cheno   = '$loka_cheno'";
            $result = mysql_query($query);
            if ($result)
                $nombro_da_forigoj++;
            else
                estis_eraro();
        } // switch
    } // while
?>
<h1><?= $tradukoj["sukceson"] ?></h1>
<p><?= $tradukoj["sukcese-konservighis"] ?> <?= $nombro_da_aldonoj ?> <?= $tradukoj["aldonoj"] ?>, <?= $nombro_da_redaktoj ?> <?= $tradukoj["redaktoj"] ?>, <?= $tradukoj["kaj"] ?> <?= $nombro_da_forigoj ?> <?= $tradukoj["forigoj"] ?>.</p>
<p><?= $tradukoj["okazis"] ?> <?= $nombro_da_eraroj ?> <?= $tradukoj["eraroj"] ?>.</p>
<?
    if (!$dosiero) $dosiero = $loka_dosiero;
?>
<p><a href='<?= $de_kie_venis ?>?dosiero=<?= $dosiero ?><?= $de_kie_venis == "redaktilo.php" ? "&amp;lingvo=$lingvo&amp;montru=$montru" : "" ?>'><?= $tradukoj["reredaktu"] ?></p>
<script type="text/javascript">
        parent.chenlisto.location = "chenlisto.php?lingvo=<?= $lingvo ?><?= $dosiero ? "&dosiero=$dosiero" : "" ?>&montru=<?= $montru ?>&random=" + Math.random();
</script>
</body>
</html>