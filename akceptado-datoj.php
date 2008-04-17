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


// TODO!: metu alvenstato al 'i' (vidita) jam komence de la akceptado.


akceptado_kapo("datoj");

akceptada_instrukcio("<strong>Bonvenon en la " . renkontigxo_nomo . "!</strong>");


	// #####################################################################################

// kontrolu, cxu jam alvenis (= estis akceptita antauxe)

if ($partopreno->datoj['alvenstato']=='a') {
    akceptada_instrukcio("Lau^ la datumbazo, <strong>$ri jam antau^e estis" .
                         " akceptita, do ne " .
                         "necesas akcepti {$ri}n denove</strong>. Bonvolu" .
                         " demandi la c^efadministranton pri tio.");
 }
 else if ($partopreno->datoj['alvenstato']=='m') {
     akceptada_instrukcio("Lau^ la datumbazo, <strong>$ri malalig^is</strong>,".
                          " do estas iom strange, ke $ri tamen venas. (Nu," .
                          " eble $ri redecidis.)</li>");
 }


if (ministeriaj_listoj == 'jes')
{
    akceptada_instrukcio("Donu al {$ri} la ministerian liston por " .
                         ($partoprenanto->datoj["lando"] == HEJMLANDO ?
                          ministeriaj_listoj_hejmlando :
                          ministeriaj_listoj_eksterlando) .
                         ", kaj igu {$ri}n enskribi {$ri}ajn datumojn. " .
                         "(Dume eblas dau^rigi per la sekva punkto.)");
// 	eoecho("<li>Donu al {$ri} la ministerian liston por ");
// 	if ($partoprenanto->datoj["lando"] == HEJMLANDO)
// 	{
// 		eoecho(ministeriaj_listoj_hejmlando);
// 	}
// 	else
// 	{
// 		eoecho(ministeriaj_listoj_eksterlando);
// 	}
// 	eoecho (", kaj igu {$ri}n enskribi {$ri}ajn datumojn. " .
// 			  "(Dume eblas dau^rigi per la sekva punkto.)</li>\n");
}

akceptada_instrukcio("C^u {$ri} s^ang^is personajn au^ partoprenajn" .
                     " datumojn sur la akceptofolio? Se jes, s^ang^u tion" .
                     " en la datumbazo.");

ligu_sekvan("Ne (plu) estas korektendaj s^ang^oj.");

akceptado_kesto_fino();

//   eoecho("<li><p>C^u {$ri} s^ang^is personajn au^ partoprenajn datumojn sur" .
// 			" la akceptofolio? </p>\n");
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


HtmlFino();

?>
