<?php

/**
 * Montrilo kaj redaktilo por la konfiguroj de iu renkontiĝo.
 *
 * @author Martin Sawitzki, Paul Ebermann
 * @version $Id$
 * @package aligilo
 * @subpackage pagxoj
 * @copyright 2001-2004 Martin Sawitzki, 2004-2010 Paul Ebermann.
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */


  /**
   */
require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();

Htmlkapo();

kontrolu_rajton("teknikumi");

$renk = ($_SESSION['renkontigxo']->datoj);

$GLOBALS['konfiguraj_tipoj'] =
    array('pagotipo' => array("Pagotipoj", "pagotipo"),
          'valuto' => array("Valutoj", "valuto"),
          'rabatotipo' => array("Kialoj por (individuaj) rabatoj",
                                "rabatkialo"),
          'kromtipo' => array("Kialoj por (individuaj) krompagoj",
                                   "krompagokialo"),
          'logxtipo' => array ("Manieroj log^i", "log^tipo"));




function montru_konfiguran_liston()
{
    eoecho("
<h2>Renkontig^o-konfiguroj</h2>
<p>Jen maniero konfiguri detalojn de via renkontig^o.</p>
<ul>
");

    //       TODO: ligo al helpoteksto

    foreach($GLOBALS['konfiguraj_tipoj'] AS $tipo => $titolo) {
        echo("  <li>");
        ligu("#" . $tipo, $titolo[0]);
        echo "</li>\n";
    }
    echo "</ul>\n";

    foreach($GLOBALS['konfiguraj_tipoj'] AS $tipo => $titolo) {
        eoecho ("<h3 id='" . $tipo. "'>" . $titolo[0] . "</h3>");

        eoecho("<table>
   <tr>
      <th>ID</th><th>grupo</th><th>interna</th>
      <th>teksto</th><th>rimarko</th>
   </tr>
");
        $sql = datumbazdemando(array('ID', 'grupo', 'interna',
                                     'teksto', 'aldona_komento'),
                               'renkontigxaj_konfiguroj',
                               array('tipo' => $tipo),
                               'renkontigxoID',
                               array('order' => 'grupo ASC, interna ASC'));

        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            echo("<tr><td>");
            ligu("renkontigxaj_konfiguroj.php?id=" . $linio['ID'],
                 $linio['ID']);
            eoecho("</td><td>" .
                   $linio['grupo'] . "</td><td>" .
                   $linio['interna'] . "</td><td>" . 
                   $linio['teksto'] . "</td><td>" .
                   $linio['aldona_komento'] . "</td></tr>\n");
        }
        echo "</table>\n";
        
        echo "<p>";
        ligu("renkontigxaj_konfiguroj.php?id=nova&tipo=" . $tipo,
             "kreu novan " . $titolo[1] . "n");
        echo "</p>";
    } // foreach tipo/titolo

}  // montru_konfiguran_liston()


function sxangxu_konfiguron($id) {
    $konfiguro = new Renkontigxa_konfiguro($id);
    $konfiguro->kopiu();
    $konfiguro->skribu();
    return $konfiguro;
}

function aldonu_konfiguron() {
    $konfiguro = new Renkontigxa_konfiguro();
    $konfiguro->kopiu();
    $konfiguro->skribu_kreante();
    eoecho("<p>Kreis konfiguron #" . $konfiguro->datoj['ID'] . "</p>");
    return $konfiguro;
}

function forigu_konfiguron($id) {
    forigu_el_datumbazo('renkontigxaj_konfiguroj',
                        $id);
}

function montru_sxangxoformularon($konfiguro) {
    if ($konfiguro->datoj['ID']) {
        eoecho("<h2>Konfiguro-s^ang^o</h2>");
    }
    else {
        eoecho("<h2>Nova konfiguro</h2>");
    }
    echo "<form action='renkontigxaj_konfiguroj.php' method='POST'>\n";
    echo "<table>\n";
    tabela_kasxilo("renkontig^o-ID", "renkontigxoID",
                   $konfiguro->datoj['renkontigxoID']);
    tabela_kasxilo("ID", 'ID', $konfiguro->datoj['ID']);

    $elektoj = array();
    foreach($GLOBALS['konfiguraj_tipoj'] AS $tipo => $titolo) {
        $elektoj[$tipo] = $titolo[1];
    }
    
    tabela_elektilo("tipo", 'tipo', $elektoj,
                    $konfiguro->datoj['tipo']);

    tabelentajpejo("interna nomo", 'interna',
                     $konfiguro->datoj['interna'], 20);
    tabelentajpejo("grupo", 'grupo', 
                     $konfiguro->datoj['grupo'], 5);
    tabelentajpejo("teksto", 'teksto',
                     $konfiguro->datoj['teksto'], 50);
    tabelentajpejo("rimarko", 'aldona_komento',
                     $konfiguro->datoj['aldona_komento'], 50);
    echo "</table>\n";
    echo "<p>";
    if ($konfiguro->datoj['ID']) {
        butono('sxangxu', "S^ang^u");
        butono("kreu", "Kreu (kun nova ID)");
    }
    else {
        butono('kreu', "Kreu");
    }
    ligu("renkontigxaj_konfiguroj.php",
         "Reen al la listo");
    echo "</p>";
}


unset($konfiguro);

switch(valoro($_POST['sendu'], "-")) {
 case 'sxangxu':
     $konfiguro = sxangxu_konfiguron($_POST['ID']);
     break;
 case 'kreu':
     $konfiguro = aldonu_konfiguron();
     break;
 }


if (isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
    if (is_numeric($id)) {
        $konfiguro = new Renkontigxa_konfiguro($id);
    }
    else {
        $konfiguro = new Renkontigxa_konfiguro();
        $konfiguro->datoj['renkontigxoID'] =
            $_SESSION['renkontigxo']->datoj['ID'];
        $konfiguro->datoj['tipo'] = $_GET['tipo'];
    }
 }

if (isset($konfiguro) && is_object($konfiguro)) {
    montru_sxangxoformularon($konfiguro);
 }
 else {
     montru_konfiguran_liston();
 }

HtmlFino();
return;

