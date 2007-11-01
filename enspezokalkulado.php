<?php

  // define("DEBUG", true);

  /**
   * ebligas elprovadon de kotizosistemo.
   */
require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');
  session_start();
  malfermu_datumaro();

// TODO: pripensu pli bonan rajton
kontrolu_rajton("vidi");

HtmlKapo();

eoecho("<h1>Enspezo-kalkulado</h1>");

if ($_REQUEST['sendu']) {

    // kalkulu

 }


echo "<form action='enspezokalkulado.php' method='POST'>\n";

// TODO


echo "</form>";


HtmlFino();