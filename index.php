<?php

  /**
   * Enirpaĝo por la renkontiĝo-administrilo.
   *
   * Ĝi kontrolas pasvortojn kaj aŭ montras la ensalutilon
   * aŭ iun statistikan paĝon en la dekstra kadro, la menuon
   * en la maldekstra.
   * @see komenci.php
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


/* ########################################################### */
// Renkontiĝo-aligilo
// (c) 2001-2004, Martin B. Sawitzki
// (c) 2004-2007 Paul Ebermann
//
// Vi rajtas uzi la programon laŭ la kondiĉoj de la
// GNU Ĝenerala Publika Permesilo, aldonita en gpl.txt.
// (Neoficiala traduko de la permesilo al Esperanto
//  troviĝas en gpl-eo.html.)
//
// Se vi volas danki al la kreintoj de la programo, ni
// bonvenigas rabatojn en renkontiĝoj, kiuj uzas la
// programon.
// Vi ankaŭ povos donaci al la Germana Esperanto-Junularo
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
// Rigardu ankaŭ http://aligilo.berlios.de/
//
/* ########################################################### */

  //define("DEBUG", true);


  /**
   * Ni bezonas la kutimajn ilojn.
   */
require_once ('iloj/iloj.php');
session_start();

echo '<!--
 $_SESSION["kodnomo"]: [' . $_SESSION["kodnomo"] . "]
 register_globals: [" . ini_get('register_globals') . "]
-->";


 
malfermu_datumaro();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="author" content="Martin B. Sawitzki, Paul Ebermann, etc.">
    <meta http-equiv="Content-Language" content="eo">
    <meta http-equiv="Expires" content="0">
<?php metu_stilfolion_kaj_titolon(); ?>
  </head>
  <frameset cols="300,*">
    <frame src="menuo.php" name="is-aligilo-menuo">
<?php


        /**
         * @global string $_SESSION["kodnomo"]
         * @name $kodnomo
         */
if (!empty($_POST['lakodnomo']))
{
  $_SESSION["kodnomo"] = $_POST['lakodnomo'];
  /**
   * @global string $_SESSION["kodvorto"]
   * @name $kodvorto
   */
  $_SESSION["kodvorto"] = $_POST['lakodvorto'];
}
else
    {
        echo "<!-- (sen kodnomo) -->";
    }

/**
 * enkodo - la kodigo de la Eo-signoj en la paĝoj.
 * Estos prenita de la formularo, aŭ per 
 * @global string $_SESSION["enkodo"]
 * @name $enkodo
 */
$_SESSION["enkodo"] = empty($_POST['laenkodo']) ? "unikodo" : $_POST['laenkodo'];



/**
 * @global string $_SESSION["renkontigxo"]
 */
if (!empty($_POST['formrenkontigxo']))
{
  $_SESSION["renkontigxo"] = new Renkontigxo($_POST['formrenkontigxo']);
  // TODO: später dynamisch (?)
}
if (!empty($_SESSION["kodnomo"]))
    {
        if (kontrolu_entajpanton($_SESSION["kodnomo"],$_SESSION["kodvorto"]))
            {
                protokolu('ensaluto sukcesa');
?>
    <frame src="statistikoj.php" name="anzeige">
<?php
         }
        else
            {
                protokolu('ensaluto malsukcesa');
?>
    <frame src="komenci.php?malgxusta=true" name="anzeige">
<?php
}
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
