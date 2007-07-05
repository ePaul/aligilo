<?php

/* ########################################################### */
// IS Aligilo
// (c) 2001-2004, Martin B. Sawitzki
// (c) 2004-2007 Paul Ebermann
//
// Vi rajtas uzi la programon laux la kondicxoj de la
// GNU Gxenerala Publika Permesilo, aldonita en gpl.txt.
// (Neoficiala traduko de la permesilo al Esperanto
//  trovigxas en gpl-eo.html.)
//
// Se vi volas danki al la kreintoj de la programo, ni
// bonvenigas rabatojn en renkontigxoj, kiuj uzas la
// programon.
// Vi ankaux povos donaci al la Germana Esperanto-Junularo
// (por kiu la programo origine estis kreita), ekzemple
// per la UEA-konto geju-h.
// Kaj venu al IS!
//
// Kontakto: Martin.Sawitzki@esperanto.de
//           Paul.Ebermann@esperanto.de
//
// Ni dankas al la homoj kiuj kreis MySQL kaj PHP.
//
//
// Rigardu ankaux dokumento.txt.
//
/* ########################################################### */

require_once ('iloj/iloj.php');
session_start();

echo '<!--
 $_SESSION["kodnomo"]: [' . $_SESSION["kodnomo"] . "]
 register_globals: [" . ini_get('register_globals') . "]
-->";


if ($_SESSION["enkodo"] == "")
{
  $_SESSION["enkodo"] = "unikodo";
}
 
malfermu_datumaro();

?>
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
else
    {
        echo "<!-- (sen kodnomo) -->";
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
    kontrolu_entajpanton($_SESSION["kodnomo"],$_SESSION["kodvorto"]))
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