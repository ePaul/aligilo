<?php


/*
 * Akceptado de partoprenantoj
 *
 *  Pasxo 1: kontrolo de datumoj
 *
 */

require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

kontrolu_rajton("akcepti");
require_once('iloj/iloj_akceptado.php');


  $partoprenanto = $_SESSION["partoprenanto"];
  $partopreno = $_SESSION['partopreno'];

  // la persona pronomo (li aux sxi)
  $ri = $partoprenanto->personapronomo;
  $Ri = ucfirst($ri);

akceptado_kapo("datoj");
  

	// #####################################################################################

echo "<ul>\n";

if (ministeriaj_listoj == 'jes')
{
	eoecho("<li>Donu al {$ri} la ministerian liston por ");
	if ($partoprenanto->datoj["lando"] == HEJMLANDO)
	{
		eoecho(ministeriaj_listoj_hejmlando);
	}
	else
	{
		eoecho(ministeriaj_listoj_eksterlando);
	}
	eoecho (", kaj igu {$ri}n enskribi {$ri}ajn datojn. " .
			  "(Dume eblas dau^rigi per la sekva punkto.)</li>\n");
}

  eoecho("<li><p>C^u {$ri} s^ang^is personajn au^ partoprenajn datumojn sur" .
			" la akceptofolio? </p>\n");
	echo "<table>";
	eoecho ("<tr><th>Personaj datumoj</th><th>Partoprenaj datumoj</th></tr>\n");
	echo "<tr><td>";
   $partoprenanto->montru_aligxinto(true);
	echo "</td><td>";
   $partopreno->montru_aligxo(true);	
   echo "</td></tr><tr><td>";
   ligu("partoprenanto.php?ago=sxangxi", "S^ang^u personajn datumojn");
	echo "</td><td>";
   ligu("partopreno.php?ago=sxangxi", "S^ang^u partoprenajn datumojn");
	echo "</td></tr></table>\n<p>";

	// por uzo de partoprenanto.php kaj partopreno.php 
	// (respektive partoprenkontrolo.php kaj aligxatkontrolo.php)
  $_SESSION['sekvontapagxo'] = 'akceptado-datoj.php';

  ligu("akceptado-kontroloj.php", "Ne (plu) estas korektendaj s^ang^oj, plu al la <em>kontroloj</em>.");
  eoecho ("</p></li>");
?>
</ul>
<?php

HtmlFino();

php?>
