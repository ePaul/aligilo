<?php

  /**
   * kreado, redaktado (kaj elprovado?) de kondicxo.
   *
   * @package aligilo
   * @subpackage pagxoj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2007-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  // TODO

  /**
   */

require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');


  session_start();
  malfermu_datumaro();


// TODO: pripensu pli bonan rajton
kontrolu_rajton("teknikumi");


HtmlKapo();

function mangxu_kondicxosenditajxojn()
{
    switch($_REQUEST['sendu']) {
    case '':
        return $_REQUEST['id'];
    case 'kreu':
        $kondicxo = new Kondicxo();
        $kondicxo->kopiu();
        $kondicxo->validumu();
        $kondicxo->datoj['entajpanto'] = $_SESSION['kkren']['entajpanto'];
        $kondicxo->skribu_kreante();
        eoecho("<p>Kreis kondic^on #" . $kondicxo->datoj['ID'] . "</p>");
        return $kondicxo->datoj['ID'];

    case 'sxangxu':
        $kondicxo = new Kondicxo($_REQUEST['ID']);
        $kondicxo->kopiu();
        $kondicxo->validumu();
        if (!$kondicxo->datoj['entajpanto']) {
            $kondicxo->datoj['entajpanto'] = $_SESSION['kkren']['entajpanto'];
        }
        $kondicxo->skribu();
        eoecho("<p>S^ang^is kondic^on #" . $kondicxo->datoj['ID'] . "</p>");
        return $kondicxo->datoj['ID'];
    default:
        darf_nicht_sein($_REQUEST);
    }
}


function montru_kondicxoredaktilon($kondicxo)
{
    eoecho("<h2>" . ($kondicxo->datoj['ID']? "S^ang^o" : "Kreo") .
           " de kondic^o</h2>\n");
    echo "<form action='kondicxo.php' method='POST'>\n";
    echo "<table>\n";
    tabela_kasxilo("ID", 'ID', $kondicxo->datoj['ID']);
    tabelentajpejo("Nomo", 'nomo', $kondicxo->datoj['nomo'],
                   30);
    $panto = $kondicxo->datoj['entajpanto'];
    tabela_kasxilo("Entajpanto", "entajpanto", $panto,
                   $panto ? eltrovu_entajpanton($panto) : "-");
    granda_tabelentajpejo("Priskribo", "priskribo",
                          $kondicxo->datoj['priskribo'],
                          60, 4);
    granda_tabelentajpejo("Kondic^okodo", "kondicxoteksto",
                          $kondicxo->datoj['kondicxoteksto'],
                          60, 4);
    granda_tabelentajpejo("J^avaskripta formo", "jxavaskripta_formo",
                          $kondicxo->datoj['jxavaskripta_formo'],
                          60, 4);
    echo "</table>\n<p>";

    if ($kondicxo->datoj['ID']) {
        butono('sxangxu', "S^ang^u");
    } else {
        butono('kreu', "Kreu");
    }
    echo "</form>\n";
}

eoecho("<h1> Kondic^o </h1>\n");


//  TODO: legu senditajxojn

$id = mangxu_kondicxosenditajxojn();
$kondicxo = new Kondicxo($id);


montru_kondicxoredaktilon($kondicxo);

echo "<p>";
ligu("kategorisistemoj.php#kondicxoj", "Reen al la listo");
ligu("kotizoj.php", "C^io pri kotizoj");
echo "</p>";

HtmlFino();