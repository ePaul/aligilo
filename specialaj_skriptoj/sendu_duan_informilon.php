<?php

/*
 * Por dissendado de varbmesagxo al partoprenintoj
 */






$prafix = "..";
require_once ($prafix . "/iloj/iloj.php");
require_once ($prafix . '/iloj/kreu_konfirmilon.php');
require_once($prafix . '/iloj/retmesagxiloj.php');
require_once($prafix . '/iloj/iloj_konfirmilo.php');
require_once($prafix . '/iloj/diversaj_retmesagxoj.php');

session_start();

malfermu_datumaro();



$unikodaj = false;

$komenco = 200;
$nombro = 50;
// por elprovi:
// $nombro = 2;

$sql = datumbazdemando(array("anto.ID" => "partoprenantoID",
                             "eno.ID" => "partoprenoID"),
                       array("partoprenantoj" => "anto",
                             "partoprenoj" => "eno"),
                       array("anto.ID = eno.partoprenantoID"),
                       "renkontigxoID",
                       array("order" => "eno.ID ASC",
                             "limit" => "$komenco, $nombro"));

echo "<p><code>" . $sql . "</code></p>";


$rezulto = sql_faru($sql);

 die("Vi ne rajtas uzi tiun dosieron. Se vi ne scias, kial, demandu Pauxlon.");

$i = $komenco;


eoecho ("<p> sendante " . ($unikodaj ? "unikodajn" : "neunikodajn") . " konfirmilojn.</p><p>");

while ($linio = mysql_fetch_assoc($rezulto))
{
    $partopreno = new Partopreno($linio['partoprenoID']);
    $partoprenanto = new Partoprenanto($linio['partoprenantoID']);

    eoecho("<br/>" . $i . ": " . $partoprenanto->tuta_nomo() . " (#" . $partoprenanto->datoj['ID'] . "/" . $partopreno->datoj['ID'] . ")\n");

    $i++;

    if ($partopreno->datoj['alvenstato'] != 'v') {
        eoecho("(malaligxis: " . $partopreno->datoj['alvenstato']. ")");
    }
    else if ($partopreno->datoj['2akonfirmilosendata'] and
        $partopreno->datoj['2akonfirmilosendata'] != "0000-00-00") {
        eoecho("(jam sendita antau^e)");
    }
    else if ($partoprenanto->datoj['retposxto'] and
        $partopreno->datoj['retakonfirmilo'] == 'J') {
        if (bezonas_unikodon($partoprenanto) == $unikodaj) {

            sendu_duan_informilon($partoprenanto, $partopreno,
                                  $_SESSION['renkontigxo'], "J");
        } else {
            eoecho ("(malgxusta unikodeco)");
        }
    }
    else {
        eoecho ("(mendis paperan)");
    }

    echo "\n";

}

echo "<br/><a name='fino'>Fino</a>.</p>\n";


?>
