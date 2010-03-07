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
    if ($_REQUEST['nekontrolu']!="JES")
    {
      $parto = "kontroli";
      if ( ( !kontrolu_daton($_REQUEST['naskigxdato']) )
           or ($_REQUEST['sekso']!="ina" and $_REQUEST['sekso']!="vira")
          )
        {
          $parto="korektigi";
        }
      malplentesto($_REQUEST['nomo']);
      malplentesto($_REQUEST['personanomo']);
      if(KAMPOELEKTO_IJK) {
	malplentesto($_REQUEST['adreso']);
      }
      else {
          malplentesto($_REQUEST['strato']);
      }
      malplentesto($_REQUEST['urbo']);
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

      // kalkulas la agxojn cxe cxiu partopreno de $_SESSION['partoprenanto']:
      rekalkulu_agxojn("partoprenanto");

      $_SESSION["partoprenanto"] =
	new Partoprenanto($_SESSION["partoprenanto"]->datoj['ID']);

      if (!$_SESSION["sekvontapagxo"])
      {
        $_SESSION["sekvontapagxo"]="partopreno.php?sp=partrezultoj.php";
      }
      unset($parto);

	   http_redirect($_SESSION["sekvontapagxo"], null, false, 303);
     }

