<?php
  /**
   * kreado kaj redaktado de krompago- kaj rabatreguloj.
   *
   * @package aligilo
   * @subpackage pagxoj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2007-2009 Paul Ebermann.
   *            (de 2009 sub nomo regulo.php, antauxe krompagotipo.php)
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



function sxangxu_regulon($tipo)
{
    $regulo = donu_regulon($tipo, $_REQUEST['ID']);
    $regulo->kopiu();
    $regulo->datoj['entajpanto'] = $GLOBALS['kkren']['entajpanto'];
    $regulo->skribu();
    eoecho( "<p>S^ang^is " . $regulo->regulovorto . "n #" .$regulo->datoj['ID']
            . ".</p>");
    return $regulo;
}


function kreu_regulon($tipo)
{
    $regulo = donu_regulon($tipo, 0);
    $regulo->kopiu();
    $regulo->datoj['entajpanto'] = $GLOBALS['kkren']['entajpanto'];
    $regulo->skribu_kreante();

    eoecho( "<p>kreis " . $regulo->regulovorto . "n #" .$regulo->datoj['ID'] .
            ".</p>");
    return $regulo;
    
}




function montru_reguloformularon($regulo) 
{

    if ($regulo->datoj['ID']) {
        eoecho("<h1>Redakto de " .$regulo->regulovorto . " <em>" .
               $regulo->datoj['nomo'] ."</em></h1>");
    }
    else {
        eoecho("<h1>Kreado de nova " . $regulo->regulovorto ."</h1>");
        // TODO: eltrovu defauxltojn pli gxenerale.
        $regulo->datoj['nurPor'] = '-';
    }
    
    echo "<form action='regulo.php?tipo=" . $regulo->tipo . "' method='POST'>\n";
    echo "<table>\n";
    
    tabela_kasxilo("ID", "ID", $regulo->datoj['ID']);
    tabelentajpejo("nomo", "nomo", $regulo->datoj['nomo'], 20);
    tabelentajpejo("Mallongigo", "mallongigo",
                   $regulo->datoj['mallongigo'], 10);
    granda_tabelentajpejo("Priskribo", "priskribo",
                          $regulo->datoj['priskribo'],
                          40, 4);
    
    tabela_kondicxoelektilo("Elektu c^i tie la g^ustan kondic^on por tiu " .
                            "krompagotipo. (Se vi bezonas aliajn, aldonu per ".
                            donu_ligon("kondicxo.php",
                                       "la kondic^oredaktilo") .
                            ".)",
                            $regulo->datoj['kondicxo']);
    
    tabela_elektilo("uzebla", "uzebla", array('j' => 'jes',
                                          'n' => 'ne'),
                $regulo->datoj['uzebla'],
                "C^u montri en la g^enerala listo?");
    tabela_elektilo("lau^nokte", 'lauxnokte', array('j' => 'lau^ nokto',
                                                    'n' => 'nur unufoje'),
                    $regulo->datoj['lauxnokte'],
                    "C^u lau^nokta krompago, c^u unufoja?");
    
    tabela_elektilo("nur por",  'nurPor',
                    array('p' => "parttempuloj",
                          't' => "tuttempuloj",
                          "-" => "c^iuj"),
                    $regulo->datoj['nurPor'],
                    "C^u g^i validas nur por homoj, kiuj pagas tuttempan" .
                    " au^ parttempan kotizon?");

    echo "</table>\n<p>";
    
    if ($regulo->datoj['ID'])
        {
            butono('sxangxu', "S^ang^u");
        }
    else
        {
            butono('kreu', "Kreu");
        }
    
    echo "</p>\n</form>";
    
}




HtmlKapo();

debug_echo( "<!-- " . var_export($_REQUEST, true) . "-->");



switch($_REQUEST['sendu']) {
 case '':
     break;

 case 'kreu':
     $regulo = kreu_regulon($_REQUEST['tipo']);
     break;

 case 'sxangxu':
     $regulo = sxangxu_regulon($_REQUEST['tipo']);
     break;

 default:
     darf_nicht_sein("sendu: " . $_REQUEST['sendu']);
 }


// TODO

if (!is_object($regulo)) {

    // se $_REQUEST['id'] == 0, tio kreas malplenan objekton.
    $regulo = donu_regulon($_REQUEST['tipo'], (int)$_REQUEST['id']);
 }


montru_reguloformularon($regulo);


echo "<hr/>\n<p>";

ligu("kotizosistemoj.php", "C^iuj kotizosistemoj");
ligu("kategorisistemoj.php#" . $regulo->tabelnomo,
     "C^iuj kategoriosistemoj (kaj " . $regulo->regulovorto . "j)");
ligu("kotizoj.php", "c^io rilate al kotizoj");

echo "</p>";

HtmlFino();


?>