<?php

/**
 * Aliro al la datumbazo.
 */
function malfermu_datumaro()
{
  if (MODUSO=="monde")
  {
    mysql_pconnect("localhost","uzantnomo","pasvorto") or die ("Datumaro ne trovebla");
    mysql_select_db("pagxaro") or die ("Ne eblas elekti gxustan datumaron");
  }
  else if (MODUSO=="teste")
  {
    mysql_pconnect("localhost","uzantnomo","pasvorto") or die ("Datumaro ne trovebla");
    mysql_select_db("test") or die ("Ne eblas elekti gxustan datumaron");
  }
  else if (MODUSO=="hejme")
  {
     mysql_pconnect("","","") 
	   or die ("Datumaro ne trovebla");
     mysql_select_db("")
	   or die ("Ne eblas elekti gxustan datumaron");
  }
  else
  {
    echo "estas eraro en datumara konstantdeklaracio (MODUSO = " . MODUSO . ").";
  }
}



?>
