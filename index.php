<?php

/* ####################################################################################### */
// IS Aligilo
// Farita de Martin B. Sawitzki,

// dauxrigas (en 2004) Paul Ebermann
//
// kaj multaj aliaj
//
// Versio de 20-a de majo 2004
//
// Dank' al la homoj kiuj faris MySQL kaj PHP
//
// Se vi deziras uzi gxin por viajn arangxojn,
// bonvolu subtenu nian laboron per donaco (ekz. 50 Euro) al la Germana Esperanto Junularo
// kontonumero: 8424901,
// banknumero: 25120510 (Bank für Sozialwirtschaft) en Germanio
// aux UEA-konto "geju-h"
//
// Kontakto: Martin_Sawitzki@gmx.de
//
// Rigardu ankaux dokumento.txt.
//
// -GPL- Verweis
/* ####################################################################################### */

require_once ('iloj/iloj.php');
session_start();

echo '<!--
 $_SESSION["kodnomo"]: [' . $_SESSION["kodnomo"] . "] \n-->";


if ($_SESSION["enkodo"] == "")
{
  $_SESSION["enkodo"] = "unikodo";
}
 
malfermu_datumaro();

php?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="author" content="Martin B. Sawitzki, etc.">
    <meta http-equiv="content-language" content="eo">
    <meta http-equiv="expires" content="0">
    <link rel="stylesheet" href="stilo.css" type="text/css">
    <title>
	  DEJ - Aligilo<?php /*
	  (estas intence aligilo kaj ne aligxilo,
	  cxar ni per gxi _igas_ la homojn _al_ la
	  renkontigxo, ne mem aligxas per gxi ...)
	  */
// ni eldonas la moduson, por ke oni facile
// rekonu, en kia maniero ni nun laboras.
echo " [" . MODUSO . "]" ;
    ?></title>
  </head>
  <frameset cols="300,*">
    <frame src="menuo.php" name="is-aligilo-menuo">
<?php


if ($lakodnomo)
{
  $_SESSION["kodnomo"] = $lakodnomo;
  $_SESSION["kodvorto"] = $lakodvorto;
  protokolu();
}

if ($laenkodo)
{
  $_SESSION["enkodo"] = $laenkodo;
}

if ($formrenkontigxo)
{
  $_SESSION["renkontigxo"] = new Renkontigxo($formrenkontigxo);  // TODO: später dynamisch
}
if (($_SESSION["kodnomo"]) and
    ( kontrolu_entajpanton($_SESSION["kodnomo"],$_SESSION["kodvorto"])))
{
?>
    <frame src="statistikoj.php" name="anzeige">
<?php
}
else
{
?>
    <frame src="komenci.php" name="anzeige">
<?php
}

?>

  </frameset>
</html>
<?php
  echo "<!-- ";
  echo "\nSESSION['kkren']: ";
  var_export($_SESSION['kkren']);
// session_write_close(); 
// echo "\n(closed)\nSESSION: ";
//  var_export($_SESSION);
// $_SESSION = array();
// echo "\n(deleted)\nSESSION: ";
//  var_export($_SESSION);
// session_start();
// echo "\n(started)\nSESSION: ";
//  var_export($_SESSION);
 echo " -->";
  ?>