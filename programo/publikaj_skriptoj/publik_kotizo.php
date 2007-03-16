<?php

/**
 *
 * Publika kotizo-antauxkalkulilo.
 *
 * TODO: Cxu ni vere bezonas kaj publikan kaj
 * privatan antauxkalkulilon (-> kotizo.php)?
 */

$prafix='./../admin/';
require_once ($prafix . "iloj.php");



malfermu_datumaro();


$renkontigxo = kreuRenkontigxon();


/* ?>

  <HTML>
  <head>
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel=stylesheet href="../2002/stilo.css" type="text/css">
  </head>

  <body background="../2002/isbirdo3.jpg">
<?php
*/

if ( isset($sendu) )
{
  $partopreno = new Partopreno();
  $partoprenanto = new Partoprenanto();

  $partoprenanto->datoj[naskigxdato] = $naskigxdato;
  $partoprenanto->datoj[lando] = $lando;
  $partopreno->datoj[aligxkategoridato] = $aligxdato;
  $partopreno->datoj[invitletero] = $invitilo;
  $partopreno->datoj[dulita] = $dulita;
  $partopreno->datoj[GEJmembro] = $GEJmembro;

  if ($studento[0]!="J")
  {
    $partopreno->datoj[okupigxo] = 10;
  }
  else
  {
    $partopreno->datoj[okupigxo] = 0;
  }
  $partopreno->datoj[domotipo] = $domotipo;
  $partopreno->datoj[de] = $de;
  $partopreno->datoj[gxis] = $gxis;
  $partopreno->datoj[partoprentipo] = $partoprentipo;

  $kot = new Kotizo($partopreno,$partoprenanto,$renkontigxo);
  echo "<table id='rezulto'>\n";
  $kot->montru_kotizon(0,$partopreno,$partoprenanto,$renkontigxo);
  echo "</table>\n";
}
eoecho ("<h1>Antau^kalkuli la kotizon</h1><p>por la ".$renkontigxo->datoj[nomo]." en ".$renkontigxo->datoj[loko].".</p>");

echo "<div style='margin-left: 1em;'><form method='post' action='{$_SERVER['REDIRECT_SCRIPT_URL']}#rezulto' >";

entajpejo("",naskigxdato,$naskigxdato,10,"","1900-01-01","&nbsp;naskig^dato (jaro-monato-tago)");
entajpejo("",aligxdato,$aligxdato,10,"",$renkontigxo->datoj[de]," alig^-/antaupagdato (jaro-monato-tago)");

echo "Hejmlando: \n";


montru_landoelektilon(1);

entajpbutono("",partoprentipo,$partoprentipo[0],"t",tuttempa,tuttempa,"kutima");
echo "<BR>";
entajpbutono("",partoprentipo,$partoprentipo[0],"p",partatempa,partatempa);

echo "partopreno de:\n";

echo "<select name=\"de\" size=1>\n";

  //TODO: (dateloop) Geht kürzer, oder als Fkt ==> später mal.
    $dateloop = $renkontigxo->datoj[de];
    do
    {
      echo "<option";
      if ($de == $dateloop) echo " selected ";
      echo "> $dateloop\n";
      $dateloop=sekvandaton ($dateloop);
    } while ($dateloop != $renkontigxo->datoj[gxis])
  ?>
  </select>
   <?php eoecho ("g^is:");?>
   <select name="gxis" size=1>
   <?php $dateloop = $renkontigxo->datoj[de];
    do
    {
      $dateloop=sekvandaton ($dateloop);
      echo "<option";
      if (($gxis == $dateloop) or ((!$gxis)and
               ($dateloop == $renkontigxo->datoj[gxis]))) echo " selected ";
      echo "> $dateloop\n";
    } while ($dateloop != $renkontigxo->datoj[gxis]);
   echo "</select> <BR>\n";


entajpbokso("","invitilo",$invitilo[0],"J","JES"," bezonas invitilon");
entajpbokso("<BR>","dulita",$dulita[0],"J","JES"," dulitan c^ambron");
entajpbokso("<BR>","studento",$studento[0],"J","JES"," estas studento/senlaborulo");

entajpbutono("<BR>GEJ-membro: ",GEJmembro,$GEJmembro[0],"J",JES,jes,kutima);
entajpbutono("",GEJmembro,$GEJmembro[0],"N",NE,"ne<BR>");

entajpbutono("",domotipo,$domotipo[0],"J",JunularGastejo," dormas en la seminariejo<BR>",kutima);
entajpbutono("",domotipo,$domotipo[0],"M",MemZorganto," memzorganto en amaslog^ejo<BR><BR>");

send_butono("Faru!");

echo "</form></div>";

HtmlFino();

?>