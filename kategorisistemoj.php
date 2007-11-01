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

foreach($GLOBALS['kategoriotipoj'] AS $tipo) {
    eoecho("<h2>" . ucfirst(donu_eokatsisnomon($tipo)) . "j</h2>\n");

    // TODO: ligoj por krei tute novan kategorisistemon de tiu speco

    $rez = sql_faru(datumbazdemando("ID",
                                    $tipo . "kategorisistemoj"));
    while($linio = mysql_fetch_assoc($rez)) {
        $sis = donu_katsistemon($linio['ID'], $tipo);
        eoecho("<h3>" . $sis->datoj['nomo'] . "</h3>\n");
        eoecho("<p>" . $sis->datoj['priskribo'] . "</p>");
        // TODO: ligoj por kopii tiun kategorisistemon
        //  (nova nomo, kopio de cxiuj kategorioj), aux
        //   por redakti gxin (nomo + priskribo)

        eoecho($sis->listu_kategoriojn());
    } // while
} // for



HtmlFino();

?>