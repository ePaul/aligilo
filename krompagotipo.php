<?php

  /**
   * ebligas kreadon kaj redaktadon de krompagotipoj.
   */

require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');

  session_start();
  malfermu_datumaro();


kontrolu_rajton("teknikumi");


HtmlKapo();

switch($_REQUEST['sendu']) {
 case '':
     echo "<!-- " . var_export($_REQUEST, true) . "-->";
     break;

 case 'kreu':
     echo "<!-- " . var_export($_REQUEST, true) . "-->";
     
     if ($_REQUEST['kondicxo'] == '---') {
         erareldono("Nevalida elekto de kondic^o!");
         break;
     }
     $tipo = new Krompagotipo();
     $tipo->kopiu();
     $tipo->skribu_kreante();

     $_REQUEST['id'] = $tipo->datoj['ID'];
     break;

 case 'sxangxu':
     echo "<!-- " . var_export($_REQUEST, true) . "-->";
     $tipo = new Krompagotipo($_REQUEST['ID']);
     $tipo->kopiu();
     $tipo->skribu();

     $_REQUEST['id'] = $tipo->datoj['ID'];
     break;

 default:
     darf_nicht_sein("sendu: " . $_REQUEST['sendu']);
 }


// TODO


if ($_REQUEST['id']) {
    $krompagotipo = new Krompagotipo($_REQUEST['id']);

    eoecho("<h1>Redakto de krompagotipo <em>" . $krompagotipo->datoj['nomo'] ."</em></h1>");
 }
 else {
     eoecho("<h1>Kreado de nova krompagotipo</h1>");
 }

echo "<form action='krompagotipo.php' method='POST'>\n";
echo "<table>\n";

tabela_kasxilo("ID", "ID", $krompagotipo->datoj['ID']);
tabelentajpejo("nomo", "nomo", $krompagotipo->datoj['nomo'], 20);
granda_tabelentajpejo("priskribo", "priskribo",
                      $krompagotipo->datoj['priskribo'],
                      40, 4);


tabela_kondicxoelektilo("Elektu c^i tie la g^ustan kondic^on por tiu" .
                        " krompagotipo. (Se vi bezonas alian kondic^on," .
                        " necesas programi kaj aldoni g^in en".
                        " konfiguroj/kondicxoj.php.)",
                        $krompagotipo->datoj['kondicxo']);


tabela_elektilo("uzebla", "uzebla", array('j' => 'jes',
                                          'n' => 'ne'),
                $krompagotipo->datoj['uzebla'],
                "C^u montri en la g^enerala listo?");
tabela_elektilo("lau^nokte", 'lauxnokte', array('j' => 'lau^ nokto',
                                                'n' => 'nur unufoje'),
                $krompagotipo->datoj['lauxnokte'],
                "C^u lau^nokta krompago, c^u unufoja?");

echo "</table>\n<p>";

if ($_REQUEST['id'])
    {
        butono('sxangxu', "S^ang^u");
    }
 else
    {
        butono('kreu', "Kreu");
    }

echo "</p>\n</form>";

echo "<hr/>\n<p>";

ligu("kotizosistemoj.php", "C^iuj kotizosistemoj");
ligu("kategorisistemoj.php#kromtipoj", "C^iuj kategoriosistemoj");
ligu("kotizoj.php", "c^io rilate al kotizoj");

HtmlFino();


?>