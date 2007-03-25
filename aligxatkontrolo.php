<?php

/* ################################################################################### */
/* Cxi tie okazas la kontrolo de la aligxado de PARTOPRENANTOJ, k.e. nomo, adreso ktp. */
/* ################################################################################### */

require_once ('iloj/iloj.php');
session_start();

malfermu_datumaro();

if (!rajtas("aligi"))
{
  ne_rajtas();
}

//kontrolado de la datoj
    if ($nekontrolu!="JES")
    {
      $parto = "kontroli";
      if ( ( !kontrolu_daton($naskigxdato) )
           or ($sekso!="ina" and $sekso!="vira")
          )
        {
          $parto="korektigi";
        }
      malplentesto($nomo);
      malplentesto($personanomo);
      malplentesto($strato);
      malplentesto($urbo);
      //malplentesto($posxtkodo);
    }
    $_SESSION["partoprenanto"]->kopiu();

    if ($parto == "korektigi")
    {
        require ("partoprenanto.php");
    }
    else
    {
      //Enmeti la datumojn en la datumaro
      if ($_SESSION["ago"] != "sxangxi")
      {
        $_SESSION["partoprenanto"]->kreu();
      }
      $_SESSION["partoprenanto"]->skribu();

	  rekalkulu_agxojn("partoprenanto");
      $_SESSION["partoprenanto"] = new Partoprenanto($_SESSION["partoprenanto"]->datoj[ID]);

      if (!$_SESSION["sekvontapagxo"])
      {
        $_SESSION["sekvontapagxo"]="partopreno.php";
      }
      unset($parto);

	   http_redirect($_SESSION["sekvontapagxo"], null, false, 303);
     }

?>
