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

echo "<form action='krompago.php' method='POST'>\n";
echo "<table>\n";

tabela_kasxilo("ID", "ID", $krompagotipo->datoj['ID']);
tabelentajpejo("nomo", "nomo", $krompagotipo->datoj['nomo'], 20);
granda_tabelentajpejo("priskribo", "priskribo",
                      $krompagotipo->datoj['priskribo'],
                      40, 4);
granda_tabelentajpejo("kondic^o", "kondicxo",
                      $krompagotipo->datoj['kondicxo'],
                      40, 4,
                      "Jen iom da PHP-programokodo, kiu redonu au^" .
                      " <code>true</code> au^ <code>false</code>. " .
                      "G^i povas uzi la variablojn " .
                      '<code>$partoprenanto</code>, <code>$partopreno</code>' .
                      ' kaj <code>$renkontig^o</code>, kiuj po estas ' .
                      " objektoj de la respektivaj klasoj.");
tabela_elektilo("uzebla", "uzebla", array('j' => 'jes',
                                          'n' => 'ne'),
                $krompagotipo->datoj['uzebla'],
                "C^u montri en la g^enerala listo?");

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