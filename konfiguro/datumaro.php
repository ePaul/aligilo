<?php

/**
 * Aliro al la datumbazo.
 */
function malfermu_datumaro()
{
  if (MODUSO=="monde")
  {
    mysql_pconnect("localhost","pagxaro","ZHdnnwtVk3dOa") or die ("Datumaro ne trovebla");
    mysql_select_db("pagxaro") or die ("Ne eblas elekti gxustan datumaron");
  }
  else if (MODUSO=="teste")
  {
    mysql_pconnect("localhost","pagxaro","ZHdnnwtVk3dOa") or die ("Datumaro ne trovebla");
    mysql_select_db("test") or die ("Ne eblas elekti gxustan datumaron");
  }
  else if (MODUSO=="hejme")
  {
    mysql_pconnect("localhost","pagxaro","ZHdnnwtVk3dOa") or die ("Datumaro ne trovebla");
    mysql_select_db("pagxaro") or die ("Ne eblas elekti gxustan datumaron");
  }
  else
  {
      die( "estas eraro en datumara konstantdeklaracio (MODUSO = " . MODUSO . ").");
  }
  // kodigo:
  mysql_query("SET NAMES 'utf8'");


}



?>
