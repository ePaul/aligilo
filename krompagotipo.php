<?php
  /**
   * kreado kaj redaktado de krompagotipoj.
   *
   * @package aligilo
   * @subpackage pagxoj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2007-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */

require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');
require_once('iloj/iloj_kondicxoj.php');

  session_start();
  malfermu_datumaro();


kontrolu_rajton("teknikumi");


HtmlKapo();


switch($_REQUEST['sendu']) {
 case '':
     debug_echo( "<!-- " . var_export($_REQUEST, true) . "-->");
     break;

 case 'kreu':
     debug_echo( "<!-- " . var_export($_REQUEST, true) . "-->");
     
     $tipo = new Krompagotipo();
     $tipo->kopiu();
     $tipo->datoj['entajpanto'] = $GLOBALS['kkren']['entajpanto'];
     $tipo->skribu_kreante();

     $_REQUEST['id'] = $tipo->datoj['ID'];
     break;

 case 'sxangxu':
     debug_echo( "<!-- " . var_export($_REQUEST, true) . "-->");
     $tipo = new Krompagotipo($_REQUEST['ID']);
     $tipo->kopiu();
     $tipo->datoj['entajpanto'] = $GLOBALS['kkren']['entajpanto'];
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
tabelentajpejo("nomo_lokalingve", "nomo_lokalingve",
               $krompagotipo->datoj['nomo_lokalingve'], 20);
tabelentajpejo("Mallongigo", "mallongigo",
               $krompagotipo->datoj['mallongigo'], 10);
granda_tabelentajpejo("Priskribo", "priskribo",
                      $krompagotipo->datoj['priskribo'],
                      40, 4);



tabela_kondicxoelektilo("Elektu c^i tie la g^ustan kondic^on por tiu" .
                        " krompagotipo. (Se vi bezonas aliajn, aldonu per " .
                        donu_ligon("kondicxo.php", "la kondic^oredaktilo") .
                        ".)",
                        $krompagotipo->datoj['kondicxo']);

// // TODO: priskribo de la esprimoj.
// granda_tabelentajpejo("Alternativa Kondic^o", "alt_kondicxo",
//                       $krompagotipo->datoj['kondicxo'],
//                       60, 5,
//                       "Por pli komplikaj kondic^oj ol la simplaj, " .
//                       "en la supra listo, eblas c^i tie meti " .
//                       "kondic^o-esprimon. Por tio elektu supre ".
//                       "<em>(Bonvolu elekti)</em>.");


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