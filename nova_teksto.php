<?php
require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();

Htmlkapo();

kontrolu_rajton("teknikumi");

eoecho("<h2>Aldono de nova teksto</h2>");
eoecho("<p>Vi nun aldonos tekston al la renkontig^o " . $_SESSION['renkontigxo']->datoj['mallongigo'] . " (#" . $_SESSION['renkontigxo']->datoj['ID'] . ").</p>");

echo "<form action='renkontigxo.php' method='POST'>\n";

echo "<table class='tekstoj-redaktilo'>";

tabelentajpejo("Identifikilo", 'mesagxoID', $_REQUEST['mesagxoID'], 30);
granda_tabelentajpejo("Teksto", 'teksto', $_REQUEST['teksto'], '30', '5');


echo "</table>";

butono("aldonu", "Aldonu");

echo "</form>";



HtmlFino();

?>