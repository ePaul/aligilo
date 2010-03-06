<?php

  /**
   * Instalilo por la programo - parto por plenigi kelkajn tabelojn per
   * komencaj datumoj.
   *
   * Ĝis nun ni nur printas la SQL-ordonojn por krei la datumbazstrukturon,
   * anstataŭ fari ion.
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage instalilo
   * @copyright 2010 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

$prafix = "..";
require_once($prafix . "/iloj/iloj.php");



function faru_SQL($sql) {
  echo $sql . "\n";
  if (INSTALA_MODUSO) {
    eoecho ("faranta ...");
    flush();
    sql_faru($sql);
    eoecho("farita!\n");
  }
}



HtmlKapo("speciala");
malfermu_datumaro();
?>

<h1>Instalilo por la aligilo.</h1>

<pre>
<?

switch($_REQUEST['sendu']) {
case 'uzanto':
  faru_SQL(datumbazaldono("entajpantoj",
			  array("nomo" => $_POST['nomo'],
				"kodvorto" => $_POST['kodvorto'],
				"vidi" => "J",
				"administri" => "J",
				"teknikumi" => "J")));
  break;
case 'renkontigxo':
  faru_SQL(datumbazaldono("renkontigxo",
			  array('nomo' => $_POST['nomo'],
				'mallongigo' => $_POST['mallongigo'],
				'kotizosistemo' => 1,
				)));
  break;
}

?></pre>

<form id="uzanto" method="POST" action="uzanto_renkontigxo.php">
<h2>Uzanto</h2>
<p>Kreas unuan uzanton. (Pliajn poste eblas krei ene de la
			programo per "grava administrado".)</p>
<table>
<?php
tabelentajpejo("uzantnomo", "nomo", "", 20, "Salutnomo de la uzanto");
tabelentajpejo("pasvorto", "kodvorto", "", 20, "Pasvorto por ensaluti", "", "", "j");
?>
</table>
<p>Tiujn informojn bone memoru, vi bezonas ilin por ensaluti.</p>
<p><? butono("uzanto", "Kreu"); ?></p>
</form>


<form id="renkontigxo" method="POST" action="uzanto_renkontigxo.php">
<h2>Renkontiĝo</h2>
  <p>Kreas renkontiĝon. (Detaloj pri la renkontiĝo eblas meti poste ene de
			 la programo.)</p>
<table>
<?php
  tabelentajpejo("nomo", "nomo", "", 20, "Nomo de la renkontiĝo");
  tabelentajpejo("mallongigo", "mallongigo", "", 8, "mallongigo de la renkontiĝo (maks. ~ 8 signoj)");
?>
</table>
<p><? butono("renkontigxo", "Kreu"); ?></p>
</form>

<p><?php
ligu("./#instalilo", "Reen al la instalilo-superrigardo");
echo "</p>";

HtmlFino();

