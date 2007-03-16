<?php
//Endseite; löscht die Session, logt den Benutzer dadurch aus und ruft index.php auf.

require_once("iloj/iloj.php");
session_start();

$_SESSION["kodvorto"] = "";
$_SESSION["kodnomo"] = "";
session_destroy();

include("index.php");

php?>
