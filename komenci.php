<?php

/*
 * Pagxo por ensaluti.
 *
 * Oni ankaux povas elekti la renkontigxon kaj la
 * kodigon.
 *
 * Kiam oni entajpis, la retumilo sendas la informojn
 * al index.php, kiu siavice kontrolas la kodvortojn
 * kaj komencas la PHP-sesion.
 */

require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();

HtmlKapo();

echo "<div style='text-align:center'>";
echo "<P class='granda'>\n";
if ($_GET['malgxusta'])
    {
        erareldono("Bedau^rinde via kombino de uzantnomo kaj pasvorto ne tau^gas por la datumbazo");
        eoecho("Se vi supozas, ke estu tiel, plendu c^e " . teknika_administranto . " (" . teknika_administranto_retadreso . "). <br />");
    }
eoecho ("Vi j^us atingis la pag^on por la ".renkontigxo_nomo."-administrado. \n<BR>\n");
echo "La uzado estas permesata nur al ".organizantoj_nomo."anoj, do bonvolu identigi vin. (Jen la persona uzantonomo kaj pasvorto.) \n<BR>\n<BR>\n";

echo "<form action='index.php' target='_top' method='POST'>\n";
entajpejo("Via alig^nomo: ","lakodnomo",$_SESSION["kodnomo"],10);
entajpejo("Via kodvorto: ","lakodvorto","",10,"","","","j");
echo "<BR>\n";

entajpbutono("Enkodo: ","laenkodo", $_SESSION["enkodo"],"utf-8","utf-8","unikodo","");
entajpbutono("","laenkodo",$_SESSION["enkodo"],"x-metodo","x-metodo","x-kodo<BR>","kutima");
eoecho ("Bonvolu elekti la enkodmanieron por a supersignoj. \n<BR>\n");
eoecho ("(Se vi povas legi la menuon, elektu unikodo, se mankas leteroj prenu la x-kodon.)\n<BR>\n<BR>\n");


// Elektilo por la renkontigxo
montru_renkontigxoelektilon(DEFAUXLTA_RENKONTIGXO);

send_butono("Preta");

echo "<div>";

HtmlFino();

?>
