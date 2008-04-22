<?php


  /**
   * kreado + redaktado/administrado de diversaj kategoriaj sistemoj
   * (aligxtempo, lando, agxo, logxado).
   */



require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');


  session_start();
  malfermu_datumaro();


kontrolu_rajton("vidi");



HtmlKapo();


echo "<ul>";
foreach($GLOBALS['kategoriotipoj'] AS $tipo)
{
    eoecho ("<li><a href='#".$tipo."'>" . ucfirst(donu_eokatsisnomon($tipo)) . "j</a></li>\n");
}
eoecho("<li><a href='#kromtipoj'>Krompagotipoj</a></li>\n");
eoecho("<li><a href='#pktipoj'>Personkostotipoj</a></li>\n");
eoecho("<li><a href='#malaligxsistemoj'>Malalig^kondic^sistemoj</a></li>\n");
eoecho("<li><a href='#malaligxkondicxotipoj'>Malalig^kondic^otipoj</a></li>\n");
echo "</ul>\n";

eoecho("<p>Jen listo de la diversaj kategori-sistemoj kaj similaj^oj," .
       "   kiujn oni povas uzi por krei kotizosistemojn.</p>\n");

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

eoecho("<h2 id='kromtipoj'>Krompagotipoj</h2>\n");



if(rajtas("teknikumi")) {
    echo "<p>";
    ligu("krompagotipo.php", "Nova krompagotipo");
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
$tipolisto = listu_cxiujn_krompagotipojn("1");

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
$tipolisto = listu_cxiujn_personkostotipojn("1");

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

?>