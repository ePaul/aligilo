<?php

  /**
   * ebligas kreadon, redaktadon kaj elprovadon de kotizosistemoj.
   */


require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');


  session_start();
  malfermu_datumaro();


kontrolu_rajton("vidi");

HtmlKapo();

eoecho("<h1>Kotizosistemoj</h2>");


eoecho("<p>Jen listo de ekzistantaj kotizosistemoj.</p>");

$rez = sql_faru(datumbazdemando("ID",
                                'kotizosistemoj'));
while($linio = mysql_fetch_assoc($rez)) {
    $sistemo = new Kotizosistemo($linio['ID']);
    eoecho ("<h2>" . $sistemo->datoj['nomo'] . "</h2>\n");
    eoecho("<p>" . $sistemo->datoj['priskribo'] . " (");
    ligu("kotizosistemo.php?id=" . $sistemo->datoj['ID'],
         "detaloj");
    echo(")</p>");
    eoecho("<table>\n");
    foreach($GLOBALS['kategoriotipoj'] AS $tipo) {
        $katsis = $sistemo->donu_kategorisistemon($tipo);

        eoecho("<tr><th>" . $katsis->donu_eoklasnomon() ."</th><td>".
               $katsis->datoj['nomo']. "</td></tr>\n");
    }
    eoecho ("</table>\n");
 }

eoecho ("<hr/>\n<p>");

ligu("kategorisistemoj.php", "C^iuj kategorisistemoj");

eoecho ("</p>\n");

HtmlFino();


?>