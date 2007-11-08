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

// TODO: iom da blabla

foreach($GLOBALS['kategoriotipoj'] AS $tipo)
{
    eoecho("<h2>" . ucfirst(donu_eokatsisnomon($tipo)) . "j</h2>\n<p>");

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

echo "<p>";
ligu("kotizosistemoj.php", "Listo de kotizosistemoj");
ligu("kotizoj.php", "C^io pri kotizoj");
echo "</p>";

HtmlFino();

?>