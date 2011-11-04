<?php

/*
 * Por dissendado de duaj informiloj.
 */






$prafix = "..";
require_once ($prafix . "/iloj/iloj.php");
require_once ($prafix . '/tradukendaj_iloj/kreu_konfirmilon.php');
require_once($prafix . '/iloj/retmesagxiloj.php');
require_once($prafix . '/tradukendaj_iloj/iloj_konfirmilo.php');
require_once($prafix . '/iloj/diversaj_retmesagxoj.php');

session_start();

malfermu_datumaro();

HtmlKapo("speciala");

kontrolu_rajton("retumi");

 die("Vi ne rajtas uzi tiun dosieron. Se vi ne scias, kial, demandu Pauxlon.");

if ($_POST['sendu'] == 'sendu') {
    $komenco = $_POST['komenco'];
    $nombro = $_POST['nombro'];
	$unikodaj = jesne_al_boolean($_POST['unikodaj']);

    echo "<p>\n";

	$sql = datumbazdemando(array("anto.ID" => "partoprenantoID",
								 "eno.ID" => "partoprenoID"),
						   array("partoprenantoj" => "anto",
								 "partoprenoj" => "eno"),
						   array("anto.ID = eno.partoprenantoID"),
						   "renkontigxoID",
						   array("order" => "eno.ID ASC",
								 "limit" => "$komenco, $nombro"));
	
	echo "<p>Demando: <code>" . $sql . "</code></p>";

    eoecho( "dato: " . date("Y-m-d H:i:s") . "<br/>\n");

	$rezulto = sql_faru($sql);


	$i = $komenco;


	eoecho ("<p> sendante " . ($unikodaj ? "unikodajn" : "neunikodajn") . " konfirmilojn.</p><p>");

	while ($linio = mysql_fetch_assoc($rezulto))
	  {
		$partopreno = new Partopreno($linio['partoprenoID']);
		$partoprenanto = new Partoprenanto($linio['partoprenantoID']);

		eoecho("<br/>" . $i . ": " . $partoprenanto->tuta_nomo() . " (#" . $partoprenanto->datoj['ID'] . "/" . $partopreno->datoj['ID'] . ")\n");

		$i++;

		if ($partopreno->datoj['alvenstato'] != 'v') {
		  eoecho($partopreno->alvenstato());
		}
		else if ($partopreno->datoj['2akonfirmilosendata'] and
				 $partopreno->datoj['2akonfirmilosendata'] != "0000-00-00") {
		  eoecho("(jam sendita antau^e)");
		}
		else if ($partoprenanto->datoj['retposxto']) {
		  if (bezonas_unikodon($partoprenanto, $partopreno) == $unikodaj) {
			if ($_POST['vere'] == 'jes') {
			  sendu_duan_informilon($partoprenanto, $partopreno,
									$_SESSION['renkontigxo'], "J");
			}
			else {
			  eoecho ("(nur elprovo)");
			}
			flush();
			usleep(200);
		  } else {
			eoecho ("(malg^usta unikodeco)");
		  }
		}
		else {
		  eoecho ("(mendis paperan)");
		}

		echo "\n";

	  }

    eoecho ("<br/>Fino.<br/>\n");
    eoecho ("dato: " . date("Y-m-d H:i:s") . "</p>\n");


}

eoecho( "<h2>Sendado de duaj konfirmiloj</h2>\n");

echo "<form action='sendu_duan_informilon.php' method='POST'>\n<table>";
tabelentajpejo("Nombro en unu pas^o:", 'nombro', $_POST['nombro'], 10, "", "",
               1);
tabelentajpejo("Komencu c^e:", 'komenco', $i, 10, "", "", 0);
tabel_entajpbutono("", "vere", $_POST['vere'], 'jes',  "vere sendu");
tabel_entajpbutono("", "vere", $_POST['vere'], 'ne', "nur listigu ricevontojn",
                   "kutima");
tabel_entajpbutono("", "unikodaj", $_POST['unikodaj'], 'jes', "unikodaj");
tabel_entajpbutono("", "unikodaj", $_POST['unikodaj'], 'ne', "neunikodaj",
				   "kutima");

echo "</table>\n<p>";
butono("sendu", "Sendu");
echo "</p></form>";

HtmlFino();