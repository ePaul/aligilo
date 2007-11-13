<?php

  /**
   * ebligas kreadon kaj redaktadon de personkostotipoj.
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

     $tipo = new Personkostotipo();
     $tipo->kopiu();
     $tipo->skribu_kreante();

     $_REQUEST['id'] = $tipo->datoj['ID'];
     break;

 case 'sxangxu':
     echo "<!-- " . var_export($_REQUEST, true) . "-->";
     $tipo = new Personkostotipo($_REQUEST['ID']);
     $tipo->kopiu();
     $tipo->skribu();

     $_REQUEST['id'] = $tipo->datoj['ID'];
     break;

 default:
     darf_nicht_sein("sendu: " . $_REQUEST['sendu']);
 }


// TODO


if ($_REQUEST['id']) {
    $personkostotipo = new Personkostotipo($_REQUEST['id']);

    eoecho("<h1>Redakto de personkostotipo <em>" . $personkostotipo->datoj['nomo'] ."</em></h1>");
 }
 else {
     eoecho("<h1>Kreado de nova personkostotipo</h1>");
 }

echo "<form action='personkostotipo.php' method='POST'>\n";
echo "<table>\n";

tabela_kasxilo("ID", "ID", $personkostotipo->datoj['ID']);
tabelentajpejo("nomo", "nomo", $personkostotipo->datoj['nomo'], 20);
granda_tabelentajpejo("priskribo", "priskribo",
                      $personkostotipo->datoj['priskribo'],
                      40, 4);
granda_tabelentajpejo("kondic^o", "kondicxo",
                      $personkostotipo->datoj['kondicxo'],
                      60, 5,
                      "Jen iom da PHP-programokodo, kiu redonu au^" .
                      " <code>true</code> au^ <code>false</code>. " .
                      "G^i povas uzi la variablojn " .
                      '<code>$partoprenanto</code>, <code>$partopreno</code>' .
                      ' kaj <code>$renkontig^o</code>, kiuj po estas ' .
                      " objektoj de la respektivaj klasoj. Ne uzu ".
                      "'-citilojn, ili difektig^as dum la sendado.");
tabela_elektilo("uzebla", "uzebla", array('j' => 'jes',
                                          'n' => 'ne'),
                $personkostotipo->datoj['uzebla'],
                "C^u montri en la g^enerala listo?");
tabela_elektilo("lau^nokte", 'lauxnokte', array('j' => 'lau^ nokto',
                                                'n' => 'nur unufoje'),
                $personkostotipo->datoj['lauxnokte'],
                "C^u lau^nokta personkosto, c^u unufoja?");

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