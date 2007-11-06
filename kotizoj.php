<?php


require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();





HtmlKapo();

eoecho("<h1>Kotizoj kaj kalkulado de ili</h1>\n<p>");


ligu("kotizo.php", "antau^kalkuli kotizon (malnova kotizosistemo, " .
     "por aktuala renkontig^o)");

  echo "<br/>\n";

ligu("kotizosistemoj.php", "Listu c^iujn kotizosistemojn");

eoecho (" (tie eblas redakti ilin.)<br/>\n");

ligu("kategorisistemoj.php", "Listu kategorisistemojn");

  echo "<br/>\n";


ligu("enspezokalkulado.php", "Elprovu kotizosistemon");

echo "</p>";


HtmlFino();


?>