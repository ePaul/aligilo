<?php

  /**
   * kreado + redaktado/administrado de diversaj kategoriaj sistemoj
   * (aliĝtempo, lando, aĝo, loĝado), kaj aliaj helpaj datumoj por
   * kotizosistemoj.
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2007-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   * @todo reordigu la dosieron.
   */




  /**
   */



require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');


  session_start();
  malfermu_datumaro();


kontrolu_rajton("vidi");



HtmlKapo();

eoecho ("<h1>Kategorisistemoj kaj aliaj bazoj por kotizosistemoj</h1>\n");

eoecho("<p>Jen listo de la diversaj kategori-sistemoj kaj similaj^oj," .
       "   kiujn oni povas uzi por krei ");
ligu("kotizosistemoj.php", "kotizosistemojn");
echo (".</p>\n");



echo "<ul>\n";
foreach($GLOBALS['kategoriotipoj'] AS $tipo)
{
    liligu("#" . $tipo, ucfirst(donu_eokatsisnomon($tipo)) . "j");
}
if (rajtas('teknikumi')) {
    liligu("#kondicxoj", "Kondic^oj");
 }
liligu("#kromreguloj", "Krompagoreguloj");
liligu("#rabatreguloj", "Rabatreguloj");

liligu("#pktipoj", "Personkostotipoj");
liligu("#malaligxsistemoj", "Malalig^kondic^sistemoj");
liligu("#malaligxkondicxotipoj", "Malalig^kondic^otipoj");

echo "</ul>\n";

echo "<hr/>\n";

foreach($GLOBALS['kategoriotipoj'] AS $tipo)
{
    eoecho("<h2 id='". $tipo ."'>" . ucfirst(donu_eokatsisnomon($tipo)) .
           "j</h2>\n<p>");

    // ligoj por krei tute novan kategorisistemon de tiu speco
    ligu("kategorisistemo.php?tipo=" . $tipo,
         "kreu novan " . donu_eokatsisnomon($tipo) . "n");
    echo "</p>";

    $rez = sql_faru(datumbazdemando("ID",
                                    $tipo . "kategorisistemoj"));
    while($linio = mysql_fetch_assoc($rez)) {
        $sis = donu_katsistemon($linio['ID'], $tipo);
        eoecho("<h3>" . $sis->datoj['nomo'] . "</h3>\n");
        eoecho("<p>Posedanto: " . eltrovu_entajpanton($sis->datoj['entajpanto'])
               . ". ");
        // ligo por redakti tiun kategorisistemon.
        ligu("kategorisistemo.php?tipo=" . $tipo . "&id=" . $linio['ID'],
             "Redaktu!");
        eoecho("</p><p>" . $sis->datoj['priskribo'] . "</p>");

        $sis->listu_kategoriojn("simpla");
    } // while

    echo "<hr/>\n";

} // for

if (rajtas('teknikumi')) {

    eoecho("<h2 id='kondicxoj'>Kondic^oj</h2>\n");
    
    echo "<p>";
    ligu('kondicxo.php', "Nova kondic^o");
    echo "</p>\n";

    eoecho("<table class='kondicxotabelo'>\n" .
           "<tr><th>ID</th><th>nomo</th><th>priskribo</th></tr>\n");
    $kondlisto = listu_cxiujn_kondicxojn();

    foreach($kondlisto AS $kondicxo) {
        eoecho("<tr><td>" . $kondicxo->datoj['ID'] . "</td><td>" .
               donu_ligon("kondicxo.php?id=" . $kondicxo->datoj['ID'],
                          $kondicxo->datoj['nomo'] ) . 
               "</td><td>" . $kondicxo->datoj['priskribo'] .
               "</td></tr>\n");
    }
    
echo "</table>";



echo "<hr/>";
 }
eoecho("<h2 id='kromreguloj'>Krompagoreguloj</h2>\n");



if(rajtas("teknikumi")) {
    echo "<p>";
    ligu("krompagotipo.php", "Nova krompagoregulo");
    echo "</p>";

    function formatu_krompagotipon($tipo) {
        return donu_ligon("krompagotipo.php?id=" . $tipo->datoj['ID'],
                          $tipo->datoj['nomo']);
    }
 }
 else {
    function formatu_krompagotipon($tipo) {
        return $tipo->datoj['nomo'];
    }
 }     



eoecho("<table class='krompagotabelo'>\n" .
       "<tr><th>ID</th><th>nomo</th><th>priskribo</th><th>uzebla</th>".
       "<th>lau^nokte</th></tr>\n");
$tipolisto = listu_cxiujn_krompagotipojn(array());

foreach($tipolisto AS $kromtipo) {
    eoecho("<tr><td>". $kromtipo->datoj['ID'] .
           "</td><td>" . formatu_krompagotipon($kromtipo) . 
           "</td><td>" . $kromtipo->datoj['priskribo'] .
           "</td><td>" . $kromtipo->datoj['uzebla'] . 
           "</td><td>" . $kromtipo->datoj['lauxnokte'] . 
           "</td></tr>\n");
}

echo "</table>";


echo "<hr/><p>";

eoecho("<h2 id='pktipoj'>Personkostotipoj</h2>\n");



if(rajtas("teknikumi")) {
    echo "<p>";
    ligu("personkostotipo.php", "Nova personkostotipo");
    echo "</p>";

    function formatu_personkostotipon($tipo) {
        return donu_ligon("personkostotipo.php?id=" . $tipo->datoj['ID'],
                          $tipo->datoj['nomo']);
    }
 }
 else {
    function formatu_personkostotipon($tipo) {
        return $tipo->datoj['nomo'];
    }
 }     



eoecho("<table class='personkostotabelo'>\n" .
       "<tr><th>ID</th><th>nomo</th><th>priskribo</th><th>uzebla</th>".
       "<th>lau^nokte</th></tr>\n");
$tipolisto = listu_cxiujn_personkostotipojn(array());

foreach($tipolisto AS $kromtipo) {
    eoecho("<tr><td>". $kromtipo->datoj['ID'] .
           "</td><td>" . formatu_personkostotipon($kromtipo) . 
           "</td><td>" . $kromtipo->datoj['priskribo'] .
           "</td><td>" . $kromtipo->datoj['uzebla'] . 
           "</td><td>" . $kromtipo->datoj['lauxnokte'] . 
           "</td></tr>\n");
}

echo "</table>";

echo "<hr/>";

eoecho("<h2 id='malaligxsistemoj'>Malalig^kondic^osistemoj</h2>\n");

$sql = datumbazdemando("ID",
                       "malaligxkondicxsistemoj");
$rez = sql_faru($sql);
eoecho("<table>
<tr><th>ID</th><th>nomo</th><th>alig^kat.-sistemo</th></tr>
");
while($linio = mysql_fetch_assoc($rez)) {
    $sistemo = new Malaligxkondicxsistemo($linio['ID']);
    $sistemo->montru_tabeleron();
 }
echo ("</table>");


ligu("malaligxkondicxsistemo.php", "Nova malalig^kondic^osistemo");

echo "<hr/>";


eoecho("<h2 id='malaligxkondicxotipoj'>Malalig^kondic^otipoj</h2>\n");

$sql = datumbazdemando("ID",
                       "malaligxkondicxotipoj");
$rez = sql_faru($sql);
eoecho("<table>
<tr><th>ID</th><th>nomo</th><th>mallongigo</th><th>funkcio</th><th>parametro</th><th>uzebla</th></tr>
");
while($linio = mysql_fetch_assoc($rez)) {
    $sistemo = new Malaligxkondicxotipo($linio['ID']);
    $sistemo->montru_tabeleron();
 }
echo ("</table>");


ligu("malaligxkondicxotipo.php", "Nova malalig^kondic^otipo");



echo "<hr/><p>";
ligu("kotizosistemoj.php", "Listo de kotizosistemoj");
ligu("kostosistemoj.php", "Listo de kostosistemoj");
ligu("kotizoj.php", "C^io pri kotizoj");
echo "</p>";



HtmlFino();

