<?php

/* #########################################
 *
 * Cxi tie okazas la kontrolo de la aligxado de
 * PARTOPRENANTOJ, k.e. nomo, adreso ktp,
 * post kiam ili aligxis per la interreta formularo.
 * ######################################### */

if (!$prafix)
{
    die("Fehlerhafte Einbindung des Programmes.");
}
else
{
    echo "<!--\n";
    echo "  prafix: $prafix \n";
    echo "  aligxilonomo: $aligxilonomo \n-->";
}

require_once ($prafix . "iloj/iloj.php");
require_once ($prafix . "iloj/formulareroj.php");

$enkodo="unikodo";
//session_start();
/*if (empty($HTTP_GET_VARS[enkodo]))
{
  $enkodo="x-metodo";
}
else
{
  $enkodo = $HTTP_GET_VARS[enkodo];
}*/

malfermu_datumaro();


//   echo "<!-- POST: \n";
//   var_export($_POST);
//   echo "-->\n";


$renkontigxo = new Renkontigxo(DEFAUXLTA_RENKONTIGXO);

//kontrolado de la datoj

      $parto = "kontroli";
      if ((!kontrolu_daton($naskigxdato))
        || ($sekso!="ina" and $sekso!="vira"))
        {
          $parto="korektigi";
        }
if ($lando == "-#-") // ne elektis landon
{
    $parto = "korektigi";
}
      malplentesto($nomo);
      malplentesto($personanomo);
      malplentesto($strato);
      malplentesto($urbo);
      //malplentesto($posxtkodo);

    //if ($nekontrolup!="JES")
    {
      if (($de > $gxis)
        || ($domotipo=="MemZorganto" and $cxambrotipo!="gea")
        || ($domotipo=="MemZorganto" and $dulita=="JES")
        || ($konsento[0]!="J"))
        {
          $parto="korektigi";
        }
      depend_malsxargxi_kaj_korekti($invitletero,$pasportnumero);
      depend_malsxargxi_kaj_korekti($kunekun,$kunkiu);
      depend_malsxargxi_kaj_korekti($vesperabokso,$vespera);
      depend_malsxargxi_kaj_korekti($distrabokso,$distra);
      depend_malsxargxi_kaj_korekti($temabokso,$tema);
      depend_malsxargxi_kaj_korekti($muzikabokso,$muzika);
      // // eble ( ne, ne sencas - foje homoj ne scias sian UEA-kodon)
      // depend_malsxargxi_kaj_korekti($tejo_membro_laudire, $ueakodo);

       //HTMLsekurigi($rabatkialo);
    }

    if ($parto == "korektigi")
    {
      require("publik.php");

    }
    else
    {

	  protokolu();

      //Enmeti la datumojn en la datumaro

      $partoprenanto = new Partoprenanto();
      $partoprenanto->kreu();
      $partoprenanto->kopiu();

	  //	  echo "<!-- partoprenanto: \n";
	  //	  var_export($partoprenanto->datoj);
	  //	  echo "-->\n";


      $partopreno = new Partopreno();
      $partopreno->kreu();
      $partopreno->kopiu();

	  //	  echo "<!-- partopreno: \n";
	  //	  var_export($partopreno->datoj);
	  //	  echo "-->\n";

         //$partopreno->montru();
      if ($partoprentipo[0]=="t")
      {
        $partopreno->datoj[de] = $renkontigxo->datoj[de];
        $partopreno->datoj[gxis] = $renkontigxo->datoj[gxis];
      }
      if ( $domotipo[0] == "J" )
		{
		  $partopreno->datoj[kunmangxas] = "JES";
		}
	  else
		{
		  $partopreno->datoj['kunmangxas'] = "NE";
		}

	  if ( $partopreno->datoj['listo']{0} != 'N' )
		{
		  $partopreno->datoj['listo'] = 'J';
		}

     /* $partoprenanto->datoj[entajpanto] = 3; //ri mem  //entfällt
      $partoprenanto->datoj[entajpdato] = date("Y-m-d");
      $partopreno->datoj[entajpanto] = 3;  //ri mem
      $partopreno->datoj[entajpdato] = date("Y-m-d");*/
      
      $partopreno->datoj['aligxdato'] = date("Y-m-d");
		switch($partopreno->datoj['vegetare']{0})
		{
			case 'J': // vegetare
			case 'N': // viande
			case 'A': // vegane
				break;
			default:
				$partopreno->datoj['vegetare'] = 'N';
		}

      $partoprenanto->skribu();

      
      $partopreno->datoj[renkontigxoID]=$renkontigxo->datoj["ID"];
      $partopreno->datoj[partoprenantoID]=$partoprenanto->datoj[ID];

      $partopreno->datoj['1akonfirmilosendata']=date("Y-m-d");
      $partopreno->datoj[alvenstato]='v';
      $partopreno->datoj[traktstato]='N';
      $partopreno->datoj[havasNomsxildon]='N';
      $partopreno->datoj[havasMangxkuponon]='N';
	  $partopreno->datoj['KKRen'] = 'n';
	  $partopreno->datoj['surloka_membrokotizo'] = 'n';

      $partopreno->datoj['tejo_membro_kontrolita'] = '?';

      if ($partopreno->datoj['tejo_membro_laudire']{0} != 'j')
          {
              $partopreno->datoj['tejo_membro_laudire'] = 'n';
          }


	  if ($partopreno->datoj['komencanto']{0} != 'J')
		{
		  $partopreno->datoj['komencanto'] = 'N';
		}
	  if ($partopreno->datoj['invitletero']{0} != 'J')
		{
		  $partopreno->datoj['invitletero'] = 'N';
		}
	  if ($partopreno->datoj['ekskursbileto']{0} != 'J')
		{
		  $partopreno->datoj['ekskursbileto'] = 'N';
		}
	  if ($partopreno->datoj['retakonfirmilo']{0} != 'J')
		{
		  $partopreno->datoj['retakonfirmilo'] = 'N';
		}
	  if ($partopreno->datoj['germanakonfirmilo']{0} != 'J')
		{
		  $partopreno->datoj['germanakonfirmilo'] = 'N';
		}
	  if ($partopreno->datoj['dulita']{0} != 'J')
		{
		  $partopreno->datoj['dulita'] = 'N';
		}
 
      $partopreno->skribu();

	  rekalkulu_agxojn($partopreno->datoj['ID']);

	  $partopreno = new Partopreno($partopreno->datoj['ID']);

       // TODO: Etwa hier sollten wir auch nötige Mails verschicken
       // TODO: können wir mit partoprenkontrolo zusammenlegen.

	  sendu_auxtomatajn_mesagxojn($partopreno, $partoprenanto, $renkontigxo);


      //$vosto = $sekvontapagxo."?&enkodo=$enkodo&kodnomo=$kodnomo&kodvorto=$kodvorto&partoprenantoidento=$partoprenantoidento&partoprenidento=$partoprenidento";
          

      //automatisches Backup

      sendu_ekzport($partoprenanto,$partopreno, $renkontigxo);
      
      granda_kesto_komenco();
      
      geoecho ("<p>", "Du hast dich erfolgreich angemeldet <br/>",
               "Vi sukcese alig^is</p>");
      if ($partoprenanto->datoj['retposxto'])
		{
            geoecho("<p>",
                    "Das folgende <em>Konfirmilo</em> wurde an deine" .
                    "E-Mail-Adresse geschickt.<br/>",
                    "La suba konfirmilo estas sendata al via retadreso</p>");
		  sendu_konfirmilon($partoprenanto,$partopreno,$renkontigxo, $teksto);
		}
	  else
		{
		  $teksto = faru_1akonfirmilon($partoprenanto,$partopreno,
                                       $renkontigxo);
		  geoecho("<p>",
                  "Bitte speichere (oder drucke) den folgenden Text. <br/>",
                  "Bonvolu sekurigu la suban tekston.</p>");
		}
	  echo "<hr />";
      echo "<div>";
      echo nl2br($teksto);
	  echo "</div>";
	  echo "<hr />";
      granda_kesto_fino();

      //$partoprenanto->montru_aligxinto();
      //$partopreno->montru_aligxo();

     }

?>
