<?php

define('DEBUG', true);

  /**
   * ebligas kreadon kaj redaktadon de malaligxkondicxotipoj.
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
     $tipo = new Malaligxkondicxotipo();
     $tipo->kopiu();
     $tipo->skribu_kreante();

     $_REQUEST['id'] = $tipo->datoj['ID'];
     break;

 case 'sxangxu':
     echo "<!-- " . var_export($_REQUEST, true) . "-->";
     $tipo = new Malaligxkondicxotipo($_REQUEST['ID']);
     $tipo->kopiu();
     $tipo->skribu();

     $_REQUEST['id'] = $tipo->datoj['ID'];
     break;

 default:
     darf_nicht_sein("sendu: " . $_REQUEST['sendu']);
 }


// TODO


if ($_REQUEST['id']) {
    $malaligxkondicxotipo = new Malaligxkondicxotipo($_REQUEST['id']);

    eoecho("<h1>Redakto de malalig^kondic^otipo <em>" . $malaligxkondicxotipo->datoj['nomo'] ."</em></h1>");
 }
 else {
     eoecho("<h1>Kreado de nova malalig^kondic^otipo</h1>");
 }

echo "<form action='malaligxkondicxotipo.php' method='POST'>\n";
echo "<table>\n";

tabela_kasxilo("ID", "ID", $malaligxkondicxotipo->datoj['ID']);
tabelentajpejo("nomo", "nomo", $malaligxkondicxotipo->datoj['nomo'], 20);
/*tabelentajpejo("nomo_lokalingve", "nomo_lokalingve",
 $malaligxkondicxotipo->datoj['nomo_lokalingve'], 20); */
tabelentajpejo("mallongigo", "mallongigo",
               $malaligxkondicxotipo->datoj['mallongigo'], 10);
granda_tabelentajpejo("priskribo", "priskribo",
                      $malaligxkondicxotipo->datoj['priskribo'],
                      40, 4);


tabela_ma_kondicxoelektilo("<span style='display:inline-block; vertical-align:text-top; max-width:70ex;'> Elektu c^i tie la g^ustan funkcion por la".
                           " kotizokalkulado por tiu" .
                           " malalig^kondic^otipo. (Se vi bezonas alian" .
                           " funkcion, necesas programi kaj aldoni g^in en".
                           " <code>konfiguroj/ma-kondicxoj.php</code>.)</span>",
                           $malaligxkondicxotipo->datoj['funkcio']);
tabelentajpejo("parametro", "parametro",
               (string)$malaligxkondicxotipo->datoj['parametro'],
               10, "Kelkaj funkcioj bezonas specialan parametron. Se vi".
               " ne scias, lasu malplena.");


tabela_elektilo("uzebla", "uzebla", array('j' => 'jes',
                                          'n' => 'ne'),
                $malaligxkondicxotipo->datoj['uzebla'],
                "C^u montri en la g^enerala listo?");
/*tabela_elektilo("lau^nokte", 'lauxnokte', array('j' => 'lau^ nokto',
                                                'n' => 'nur unufoje'),
                $malaligxkondicxotipo->datoj['lauxnokte'],
                "C^u lau^nokta krompago, c^u unufoja?");*/

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

echo "</p>";

HtmlFino();


?>