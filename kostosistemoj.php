<?php

  /**
   * ebligas kreadon, redaktadon (kaj elprovadon?) de kostosistemoj.
   */


require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');


  session_start();
  malfermu_datumaro();


kontrolu_rajton("vidi");

HtmlKapo();

eoecho("<h1>Kostosistemoj</h2>");




eoecho("<p>Jen listo de ekzistantaj kostosistemoj.</p>");

$rez = sql_faru(datumbazdemando("ID",
                                'kostosistemoj'));
while($linio = mysql_fetch_assoc($rez)) {
    $sistemo = new Kostosistemo($linio['ID']);
    eoecho ("<h2>" . $sistemo->datoj['nomo'] . "</h2>\n");
    eoecho("<p>" . $sistemo->datoj['priskribo'] . " (");
    ligu("kostosistemo.php?id=" . $sistemo->datoj['ID'],
         "detaloj");
    echo(" )</p>");
    // TODO: ioma superrigardo pri la kostosistemo
 }

eoecho ("<hr/>\n<p>");

ligu("kategorisistemoj.php", "C^iuj kategorisistemoj");
ligu("kotizosistemoj.php", "C^iuj kotizosistemoj");
ligu("kotizoj.php", "C^io pri kotizoj");


eoecho ("</p>\n");

HtmlFino();


?>