<?php


  /**
   * PHP-dosiero por krei JS-funkcion uzatan en la mangxmendilo.
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */
header("Content-Type", "text/javascript; charset=UTF-8");


require_once($prafix . "/tradukendaj_iloj/trad_htmliloj.php");
$GLOBALS['enkodo'] = "utf-8";

  //
  // provizore ne necesas.
  //malfermu_datumaro();


kreu_mangxmendilan_JS();

?>