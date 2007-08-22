<?php

  /**
   * Fina pagxo: forigas la session-informojn pri uzanto, poste
   * la uzanton mem kaj poste vokas index.php
   */

require_once("iloj/iloj.php");
session_start();

$_SESSION["kodvorto"] = "";
$_SESSION["kodnomo"] = "";
session_destroy();

require("index.php");

?>
