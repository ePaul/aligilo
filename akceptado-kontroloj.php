<?php


/*
 * Akzeptado de partoprenantoj
 *
 * Pasxo 2: kontrolado de notoj,
 * agxo, lando, ktp.
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

akceptado_kapo("kontroloj");
  
	// ###############################################################################

// <p>(Reen al <a href='akceptado.php?pasxo=datumoj'>datumoj</a>)</p>

echo "<ul>\n";

eoecho("<li><p>Jen c^iuj notoj pri {$ri}. Kontrolu, c^u io estas neprilaborita.</p>\n");

	$sql = datumbazdemando(array("ID", "prilaborata", "dato", "partoprenantoID",
								 "subjekto","kiu", "kunKiu","tipo"),
						   "notoj",
						   "",
						   array("partoprenanto" => "partoprenantoID"));
	
	sercxu($sql, 
		  array("dato","desc"), 
		  array(array('ID','','->','z','"notoj.php?wahlNotiz=XXXXX"','-1'), 
				array('prilaborata','prilaborata?','XXXXX','z','','-1'), 
				array('dato','dato','XXXXX','l','','-1'), 
				array('subjekto','subjekto','XXXXX','l','','-1'), 
				array("kiu","kiu",'XXXXX','l','','-1'), 
				array("kunKiu","kun Kiu?",'XXXXX','l','','-1'), 
				array("tipo","tipo",'XXXXX','l','','-1')
				), 
		  array(array('',array('&sum; XX','A','z'))),
		  "notoj-akceptado",
		  array('Zeichenersetzung'=>
				array('1'=>array('j'=>'<strong class="malaverto">prilaborata</strong>',
								 ''=>'<strong class="averto">neprilaborata</strong>',
								 'n'=>'<strong class="averto">neprilaborata</strong>')
					  ),
				),
		  0,'','','ne');

eoecho("<p>Se estas iuj gravaj aferoj, prilaboru tuj (au^ voku respondeculon).</p>\n</li>");
$_SESSION['sekvontapagxo'] = 'akceptado-kontroloj.php';

	eoecho("<li>Eble kontrolu {$ri}an log^landon (estu " .
	eltrovu_landon($partoprenanto->datoj['lando']) . "/" .
	eltrovu_landon_lokalingve($partoprenanto->datoj['lando']) . " &ndash;");
	ligu("partoprenanto.php?ago=sxangxi", "s^ang^u!");
	echo (").</li>\n");



// if($partopreno->datoj['agxo'] < 36) // TODO: prenu limagxon el datumbazo
{
	eoecho("<li> Eble kontrolu lian ag^on, {$ri} asertis esti naskita je " .
				 $partoprenanto->datoj['naskigxdato'] . " (nun " .
				 $partopreno->datoj['agxo']."-jara)");
	ligu("partoprenanto.php?ago=sxangxi", "(s^ang^u!)");
	echo ("</li>\n");
}

  if ($partopreno->datoj['agxo'] < 18)
	{
	  eoecho ("<li>Kolektu la gepatran permeson (se g^i mankas donu faksnumeron de" .
 			    " la ejo, kaj insistu ke {$ri} donos g^in.).</li>\n");
	}


	echo "</ul><p>";

ligu_sekvan();


	echo "</p>";


HtmlFino();

?>