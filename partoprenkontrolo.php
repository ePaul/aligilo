<?php

  /** partoprenkontrolo */

require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();

echo "<!--\n";
var_export($_POST);
echo "-->";

if (!rajtas("aligi"))
{
  ne_rajtas();
}

    if ($nekontrolup!="JES")
    {
      if ( ($de > $gxis)
		   || (!kontrolu_daton($aligxdato))
		   || ($malaligxdato != "0000-00-00" &&
			   !kontrolu_daton($malaligxdato)
			   || (kalkulu_tagojn($aligxdato, $malaligxdato) < 0 ) )
		   || ($domotipo=="MemZorganto" and $cxambrotipo[0]!="g")
		   || ($domotipo=="MemZorganto" and $dulita=="JES")
		   || ($konsento[0]!="J")
		   )
        {
          $parto="korektigi";          
        }
      depend_malsxargxi_kaj_korekti($invitletero,$pasportnumero);
      depend_malsxargxi_kaj_korekti($kunekun,$kunkiu);
      depend_malsxargxi_kaj_korekti($vesperabokso,$vespera);
      depend_malsxargxi_kaj_korekti($distrabokso,$distra);
      depend_malsxargxi_kaj_korekti($temabokso,$tema);
      depend_malsxargxi_kaj_korekti($muzikabokso,$muzika);
      depend_malsxargxi_kaj_korekti($nokta,$nokta);
      //HTMLsekurigi($rabatkialo);
    }

if ($listo{0} != 'N')
{
  $_POST['listo'] = 'J';
}

echo "<!--\n";
var_export($_POST);
echo "-->";


    $_SESSION["partopreno"]->kopiu();
    
    if ($partoprentipo[0]=="t")
    {
      $_SESSION["partopreno"]->datoj[de] = $_SESSION["renkontigxo"]->datoj[de];
      $_SESSION["partopreno"]->datoj[gxis] = $_SESSION["renkontigxo"]->datoj[gxis];
    }
    if ( $domotipo[0] == "J" )
    {
      $_SESSION["partopreno"]->datoj[kunmangxas] = "JES";
    }
    if ($parto == "korektigi")
    {
      require ("partopreno.php");
    }
    else
    {
      if ($ago != "sxangxi")
      {
        $_SESSION["partopreno"]->kreu();
      }
      if ($_SESSION["partopreno"]->datoj[alvenstato]=='')
        $_SESSION["partopreno"]->datoj[alvenstato]='v';
      if ($_SESSION["partopreno"]->datoj[traktstato]=='')  
        $_SESSION["partopreno"]->datoj[traktstato]='N';
      if ($_SESSION["partopreno"]->datoj[havasNomsxildon]=='')    
      $_SESSION["partopreno"]->datoj[havasNomsxildon]='N';
      if ($_SESSION["partopreno"]->datoj[havasMangxkuponon]=='')    
      $_SESSION["partopreno"]->datoj[havasMangxkuponon]='N';
      
      $_SESSION["partopreno"]->skribu();
      $_SESSION["partopreno"] = new Partopreno($_SESSION["partopreno"]->datoj[ID]);

	  rekalkulu_agxojn($_SESSION["partopreno"]->datoj[ID]);

      require ("partrezultoj.php");
     }
?>
