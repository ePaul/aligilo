<?php

/* ################################################### */
/* Cxi tie mi ekzportas partoprenantojn el la datumaro */
/* ################################################### */

require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();





HtmlKapo("kalkulilo");


$renkontigxo = kreuRenkontigxon();

echo "<form method=\"post\" action=\"$PHP_SELF \">";
montru_renkontigxoelektilon($renkontigxo->datoj["ID"]);
send_butono("Elektu renkontig^on!");
echo "</form>";
echo "<hr />";



if ( $sendu == "Faru!" )
{
  $partopreno =  new Partopreno();
  $partoprenanto = new Partoprenanto();

  $partoprenanto->datoj[naskigxdato] = $naskigxdato;
  $partoprenanto->datoj[lando] = $lando;
  $partopreno->datoj[aligxkategoridato] = $aligxdato;
  $partopreno->datoj[invitletero] = $invitilo;
  $partopreno->datoj[invitilosendata] = "2000-01-01"; // TODO: Kial fiksa datumo?
  $partopreno->datoj[dulita] = $dulita[0];
  $partopreno->datoj[ekskursbileto] = $ekskursbileto[0];
  $partopreno->datoj[GEJmembro] = $GEJmembro[0];

//   if ($studento[0]!="J")
//   {
//     $partopreno->datoj[okupigxo] = 10;
//   }
//   else
//   {
//     $partopreno->datoj[okupigxo] = 0;
//   }

  $partopreno->datoj[domotipo] = $domotipo;
  $partopreno->datoj[de] = $de;
  $partopreno->datoj[gxis] = $gxis;
  //$partopreno->datoj[partoprentipo] = $partoprentipo;

  $kot = new Kotizo($partopreno,$partoprenanto,$renkontigxo);
  echo "<table id='rezulto'>\n";
  $kot->montru_kotizon(0,$partopreno,$partoprenanto,$renkontigxo);
  echo "</table>\n";
  echo "<hr />\n";
}


echo "<form method='post' action='{$PHP_SELF}#rezulto'>";

tenukasxe("formrenkontigxo", $renkontigxo->datoj["ID"]);
eoecho ("<BR>Antau^kalkuli la kotizon por: la ".$renkontigxo->datoj["nomo"]." en ".$renkontigxo->datoj["loko"]);
eoecho ("<BR>Se vi alig^us hodiau^!");
echo "<BR><BR>";


entajpejo("",naskigxdato,$naskigxdato,13,"","1900-01-01","&nbsp;naskig^dato (jaro-monato-tago)");
entajpejo("",aligxdato,$aligxdato,13,"",date( "Y-m-d", time() )/*$renkontigxo->datoj[de]*/,"&nbsp;relevanta alig^dato (jaro-monato-tago)");


eoecho ("Log^lando: \n");

montru_landoelektilon(5, $lando);

/*entajpbutono("",partoprentipo,$partoprentipo[0],"t",tuttempa,tutempa,"kutima");
echo "<BR>";
entajpbutono("",partoprentipo,$partoprentipo[0],"p",partatempa,partatempa);
*/
echo "partopreno de:\n";

echo "<select name=\"de\" size=1>\n";

  // TODO: Geht kürzer, oder als Fkt ==> später mal.
    $dateloop = $renkontigxo->datoj[de];
    do
    {
      echo "<option";
      if ($de == $dateloop) echo " selected ";
      echo ">$dateloop\n";
      $dateloop=sekvandaton ($dateloop);
    } while ($dateloop != $renkontigxo->datoj[gxis])
  ?>
  </select>
   <?php eoecho ("g^is:");?>
   <select name="gxis" size="1">
   <?php
 $dateloop = $renkontigxo->datoj[de];
 do
    {
      $dateloop=sekvandaton ($dateloop);
      echo "<option";
      if (($gxis == $dateloop) or ((!$gxis)and
               ($dateloop == $renkontigxo->datoj[gxis]))) echo " selected ";
      echo ">$dateloop\n";
    } while ($dateloop != $renkontigxo->datoj[gxis]);
   echo "</select> <BR>\n";

entajpbokso("","invitilo",$invitilo[0],"J","JES","bezonas invitilon");
// TODO: ripari "dulita"
entajpbokso("<BR>","dulita",$dulita[0],"J","JES","dulitan c^ambron //c^i - tie ne funkcias");
entajpbokso("<BR>","ekskursbileto",$ekskursbileto[0],"J","JES","ekskursbileto");
//entajpbokso("<BR>","studento",$studento[0],"J","JES","&nbsp;estas studento kun internacia legetimilo"); // im Moment nicht berücksichtigt.

entajpbutono("<BR>".deviga_membreco_nomo.'-membro: ',GEJmembro,$GEJmembro[0],"J",JES,jes,kutima);
entajpbutono("",GEJmembro,$GEJmembro[0],"N",NE,"ne<BR>");

entajpbutono("",domotipo,$domotipo[0],"J",JunularGastejo," seminariejo<BR>",kutima);
entajpbutono("",domotipo,$domotipo[0],"M",MemZorganto," memzorganto en amaslog^ejo<BR><BR>");

send_butono("Faru!");

HtmlFino();

?>
