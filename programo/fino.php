<?php

  /**
   * Fina pagxo: forigas la session-informojn pri uzanto, poste
   * la uzanton mem kaj poste vokas index.php
   */

require_once("iloj/iloj.php");
session_start();


malfermu_datumaro();
protokolu('elsaluto');

$_SESSION["kodvorto"] = "";
$_SESSION["kodnomo"] = "";
session_destroy();


http_redirect("index.php", null, false, 303);

?>
