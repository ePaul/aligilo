<?php

/**
 * Redaktado de la partopreno-datoj (renkontigxo-specifaj).
 *
 * Kunlaboras kun {@link partoprenkontrolo.php} por
 * savi la datumojn.
 * 
 * parametroj:
 *
 * - $ago = sxangxi  sxangxu ekzistantan partoprenon.
 *                  Uzu aux $_SESSION['partopreno'] aux
 *                  $partoprenidento por eltrovi kiun.
 *
 * - $sp = forgesi  - forgesu la nunan $_SESSION['partopreno']
 *                   (kaj la lastan $ago) kaj anstatauxe kreu
 *                   novan partoprenon. Uzas $partoprenantoidento
 *                   aux $_SESSION['partoprenanto'] por eltrovi,
 *                   por kiun partoprenanton.
 *
 * - $partoprenidento    - la ID de la redaktenda partopreno.
 * - $partoprenantoidento - la ID de la partoprenanto, por kiu
 *                         oni kreu partoprenon.
 *
 * - $parto = korektigi - montru erarojn en la datoj.
 *
 * La Partoprenanto-datumoj ne-renkontigxo-specifaj estas en
 * {@link partoprenanto.php}.
 *
 * @author Martin Sawitzki, Paul Ebermann
 * @version $Id$
 * @package aligilo
 * @subpackage pagxoj
 * @copyright 2001-2004 Martin Sawitzki, 2004-2009 Paul Ebermann.
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 * @todo la formularo igxu tabela.
 * @todo tuta reverkado.
 */

  /**
   */


  //define("DEBUG", true);

require_once ('iloj/iloj.php');



session_start();
malfermu_datumaro();

if (!rajtas("aligi"))
{
  ne_rajtas();
}




// TODO: ////////////////////////Immer gleich, mach mal 'ne Funktion draus//////
// dafuer gibt es bessere loesungen!!
if ($sp == "forgesi")
{
  
//  session_unregister("ago");
//  unset($ago);
  unset($_SESSION["ago"]);
}
else if ($sp)
{
	$_SESSION['sekvontapagxo'] = $sp;
}

if($_REQUEST["ago"])
{
  $_SESSION["ago"] = $_REQUEST["ago"];
}

//TODO: kontrolu, cxu eblas uzi la funkciojn el iloj_sesio

if ($_REQUEST['partoprenidento'])
{
  $_SESSION['partopreno'] = new Partopreno($_REQUEST['partoprenidento']);
  $_SESSION['partoprenanto'] =
	new Partoprenanto($_SESSION['partopreno']->datoj['partoprenantoID']);

  $GLOBALS['partopreno_renkontigxo'] = 
    kreuRenkontigxon($_SESSION['partopreno']->datoj['renkontigxoID']);
}

if ($_REQUEST['partoprenantoidento'])
{
  $_SESSION['partoprenanto'] = new Partoprenanto($_REQUEST['partoprenantoidento']);
}


if ($_SESSION['ago'] == 'sxangxi' and
	$_SESSION['partopreno'] and
	($_SESSION['partopreno']->datoj['partoprenantoID'] !=
	 $_SESSION['partoprenanto']->datoj['ID']))
{
  echo "<!-- eraro: malgxusta partoprenanto (#{$_SESSION['partoprenanto']->datoj['ID']}).".
	" Uzas pli tauxgan (#{$_SESSION['partopreno']->datoj['partoprenantoID']})! \n-->";
  $_SESSION['partoprenanto'] =
	new Partoprenanto($_SESSION['partopreno']->datoj['partoprenantoID']);
}


// sxangpreparado
if (($_SESSION["ago"] != "sxangxi") and (!$parto))
{
    // nova partopreno

  $_SESSION["partopreno"] = new Partopreno();
  $_SESSION["partopreno"]->datoj['partoprenantoID']=$_SESSION["partoprenanto"]->datoj[ID];
  $_SESSION["partopreno"]->datoj['renkontigxoID']=$_SESSION["renkontigxo"]->datoj[ID];
}

HtmlKapo();


  if ($parto == "korektigi")
  {
      echo "<div align='center'>\n";
      erareldono ("Hmm, io malg^usta okazis.");
      echo "</div>\n";
  }
?>

<form action="partoprenkontrolo.php" method="post">
<?php
  eoecho ("<p align='center'>Bonvole uzu nur anglan literojn, kun malantau^a c^apelo post speciala litero\n");
  echo "(kiel C^,c^, U^, u^).</p>\n";
  ?>
  <table valign='center' align='center' width='100%'>
  <tr>
  <td width='10%'></td>
  <td width='*' align='left'>
      <p>
  <?php
  if ($_SESSION["partopreno"]->datoj[ID]=='')
  {
      eoecho ("Ni aligas: <strong>".$_SESSION["partoprenanto"]->datoj[personanomo]." ".$_SESSION["partoprenanto"]->datoj[nomo]." </strong> (".
      $_SESSION["partoprenanto"]->datoj[ID].") al la <strong>".$_SESSION["renkontigxo"]->datoj[nomo]);      
     eoecho (" en ".$_SESSION["renkontigxo"]->datoj[loko]."</strong>\n");
  }
  else
  {
     eoecho ("Ni s^ang^as la partoprenon (ID ".
			 $_SESSION["partopreno"]->datoj["ID"].
			 ") de: <strong>" .
			  $_SESSION["partoprenanto"]->datoj["personanomo"] . " " .
			  $_SESSION["partoprenanto"]->datoj["nomo"] . " </strong>(ID ".
			  $_SESSION["partoprenanto"]->datoj["ID"] . ") al la <strong>" .
			  eltrovu_renkontigxon($_SESSION["partopreno"]->datoj["renkontigxoID"]) .
			  "</strong>.\n");
  }
echo "</p>";

entajpejo("<p><strong>ordigo-ID:</strong>",
          'ordigoID', $_SESSION['partopreno']->datoj['ordigoID'],
          10, "", "",
          " Por uzo en ordigo-celoj (ekzemple alig^into-listo en la ".
          " retpag^aro). Se estas 0.000, ni uzas ID (" .
          $_SESSION["partopreno"]->datoj["ID"] . ") anstatau^e.</p>");

entajpbokso("<BR><BR>","retakonfirmilo",$_SESSION["partopreno"]->datoj[retakonfirmilo][0],"J","J","Mi deziras retan konfirmilon.","");
  echo "<HR>";

//  entajpbutono("",partoprentipo,$_SESSION["partopreno"]->datoj[partoprentipo][0],"t",'t',"tuttempa partopreno (de ".$_SESSION["renkontigxo"]->datoj[de]." g^is ".$_SESSION["renkontigxo"]->datoj[gxis].")","kutima");
//  echo "<BR>";
//  entajpbutono("",partoprentipo,$_SESSION["partopreno"]->datoj[partoprentipo][0],"p",'p',partatempa);

    if ($_SESSION['renkontigxo']->datoj['de'] == $_SESSION['partopreno']->datoj['de'] AND
        $_SESSION['renkontigxo']->datoj['gxis'] == $_SESSION['partopreno']->datoj['gxis']) {
        eoecho("tuttempa partopreno ");
    }
    else if (strcmp($_SESSION['renkontigxo']->datoj['de'], $_SESSION['partopreno']->datoj['de']) <= 0 AND
              strcmp($_SESSION['renkontigxo']->datoj['gxis'], $_SESSION['partopreno']->datoj['gxis']) >= 0) {
    	eoecho("parttempa partopreno ");
    }
    else {
        eoecho("stranga/nova partopreno ");
    }
    eoecho ("de " . $_SESSION['partopreno']->datoj['de'] . " g^is " . $_SESSION['partopreno']->datoj['gxis'] . "<br/>\n");

  echo "partopreno de:\n";

  echo "<select name=\"de\" size=1>\n";

echo "<!--\n Renkontigxo:";
var_export($_SESSION["renkontigxo"]);
echo "-->";

  // TODO: GEht kuerzer, oder als Fkt.
    $dateloop = $_SESSION["renkontigxo"]->datoj[de];
    do
    {
      echo "<option";
      if ($_SESSION["partopreno"]->datoj[de] == $dateloop) echo " selected ";
      echo ">$dateloop";
      $dateloop=sekvandaton ($dateloop);
    } while ($dateloop != $_SESSION["renkontigxo"]->datoj[gxis]);
?>
  </select>
   <?php eoecho ("g^is:");?>
   <select name="gxis" size=1>
   <?php $dateloop = $_SESSION["renkontigxo"]->datoj[de];
    do
    {
      $dateloop=sekvandaton ($dateloop);
      echo "<option";
      if (($_SESSION["partopreno"]->datoj[gxis] == $dateloop) or ((!$_SESSION["partopreno"]->datoj[gxis])and
               ($dateloop == $_SESSION["renkontigxo"]->datoj[gxis]))) echo " selected ";
      echo ">$dateloop";
    } while ($dateloop != $_SESSION["renkontigxo"]->datoj[gxis]);
   echo "</select>\n ";
	echo  "<br/>\n";
   if (($parto=="korektigi") and (($_SESSION["partopreno"]->datoj[de])>($_SESSION["partopreno"]->datoj[gxis])))
   {
     erareldono("Via 'gis' Dato estas antau^ au^ je la 'de' dato");
   }
  echo "<hr/>";  





if (KAMPOELEKTO_IJK) {

  $logxlisto = listu_konfigurojn('logxtipo',
				 $GLOBALS['partopreno_renkontigxo']);

  $kutima = "kutima";
  foreach ($logxlisto AS $konf) {
    entajpbutono("<p>", 'domotipo',
		 $_SESSION['partopreno']->datoj['domotipo'],
		 $konf->datoj['interna'], $konf->datoj['interna'],
		 $konf->datoj['teksto'], $kutima);
    $kutima = false;
  }
  /*

  entajpbutono("<p>",domotipo,$_SESSION["partopreno"]->datoj[domotipo][0],
                 "J",'J',"Mi volas log^i en la <strong>junulargastejo</strong> </p>",kutima);
  entajpbutono("<p>",'domotipo', $_SESSION["partopreno"]->datoj['domotipo'],
               "A",'A',
               "Mi volas log^i en <strong>amaslog^ejo</strong>, se ekzistas tia</p>");
  entajpbutono("<p>",'domotipo', $_SESSION["partopreno"]->datoj['domotipo'],
               "T",'T',
               "Mi volas log^i en propra <strong>tendo</strong></p>");
  entajpbutono("<p>",'domotipo', $_SESSION["partopreno"]->datoj['domotipo'],
               "M",'M',
               "Mi log^os tute <strong>memzorge</strong> (ekster viaj ejoj)</p>");
  */

}
else {
  entajpbutono("<p>",domotipo,$_SESSION["partopreno"]->datoj[domotipo][0],
                 "J",'J',"Mi volas log^i en la <strong>junulargastejo</strong> </p>",kutima);

  entajpbutono("<p>",'domotipo', $_SESSION["partopreno"]->datoj['domotipo'],
               "M",'M',
               "Mi volas log^i en <strong>amaslog^ejo</strong>, se ekzistas tia");
}

echo "<blockquote>\n";

  entajpbutono("Mi preferas log^i en:&nbsp;",cxambrotipo,$_SESSION["partopreno"]->datoj[cxambrotipo][0],"u","u","unuseksa c^ambro");
  entajpbutono("",cxambrotipo,$_SESSION["partopreno"]->datoj[cxambrotipo][0],"g","gea","gea (ajna) c^ambro",kutima);
  //entajpbutono("",cxambrotipo,$partopreno->datoj[cxambrotipo][0],"n","negravas","ne gravas<BR>",kutima);

  if($domotipo=="M" and $_SESSION["partopreno"]->datoj[cxambrotipo]=="u")
  {
    erareldono ("<BR>Ne haveblas unuseksan c^ambrojn memzorge ");
  }
  echo "<BR>";
  entajpejo("Mi s^atus log^i kun", "kunKiu", $_SESSION["partopreno"]->datoj[kunkiu],25);
//  entajpboksokajejo(kunekun,$kunekun,"JES","JES","Mi s^atus log^i kune kun:","",kunkiu,
//         $_SESSION["partopreno"]->datoj[kunkiu],25,"Kun kiun vi s^atus log^i kune?");

eoecho("<p>Litoj:");
// if (!CXAMBROELEKTO_IJK) {
simpla_entajpbutono("dulita",
                    $_SESSION["partopreno"]->datoj['dulita'],
                    'N', "kutima");
eoecho ("plurlita c^ambro &nbsp; ");
// }

simpla_entajpbutono("dulita",
                    $_SESSION["partopreno"]->datoj['dulita'],
                    'J');
eoecho("dulita c^ambro  &nbsp; ");
simpla_entajpbutono("dulita",
                    $_SESSION["partopreno"]->datoj['dulita'],
                    'U');
eoecho("unulita c^ambro  &nbsp; <br/>\n");


  if( $domotipo == "M"
      and $_SESSION["partopreno"]->datoj[dulita]=="J"
      )
  {
    erareldono ("<BR>Ne haveblas dulitaj c^ambroj memzorge");
  }

echo "</p></blockquote>\n";

  echo "<hr/>\n";

if (mangxotraktado == "ligita") {


debug_echo("<!-- kunmangxas: " . $_SESSION['partopreno']->datoj['kunmangxas']. "-->");


$kunmangxas = $_SESSION['partopreno']->datoj['kunmangxas'];
if (($kunmangxas == 'J') AND
    ($_SESSION['partopreno']->datoj['domotipo'] == 'J')) {
    $kunmangxas = '?';
 }
if (($kunmangxas == 'N') AND
    ($_SESSION['partopreno']->datoj['domotipo'] == 'M')) {
    $kunmangxas = '?';
 }

debug_echo( "<!-- kunmangxas: " . $kunmangxas. "-->");


entajpbutono("<strong>Mang^ado:</strong> ",
             'kunmangxas', $kunmangxas,
             'J', 'J', "jes (sen krompago) &nbsp; ");
entajpbutono("", 'kunmangxas', $kunmangxas,
             'K', 'K', "krompagas por mang^i &nbsp; ");
entajpbutono("", 'kunmangxas', $kunmangxas,
             'N', 'N', "ne &nbsp; ");
entajpbutono("", 'kunmangxas', $kunmangxas,
             '?', '?', " lau^ domotipo (junulargastejo: J, memzorgantejo: N)",
             "kutima");

 }
 else if (mangxotraktado == "libera") {
     require_once($prafix . "/iloj/iloj_mangxoj.php");
     require_once($prafix . "/tradukendaj_iloj/trad_htmliloj.php");
     echo "</p>";
     eoecho("<p>Mang^mendoj:</p>");
     montru_mangxomendilon($_SESSION['partopreno']);
     echo "<p>";
 }
 else {
     darf_nicht_sein("nesubtenita mangxotraktado: '" . mangxotraktado . "'");
 }


entajpbutono("<br/>Mi s^atus mang^i ... <br/>",
			 'vegetare',$_SESSION['partopreno']->datoj['vegetare']{0},"N", 'N nevegetare',
			  "nevegetare | ", "kutima");
entajpbutono("", 'vegetare',$_SESSION['partopreno']->datoj['vegetare']{0},"J", 'J vegetare',
			  " vegetare | ");
entajpbutono("", 'vegetare',$_SESSION['partopreno']->datoj['vegetare']{0},"A", 'A vegane',
			 "vegane. <br/>");

echo "<hr/>\n";

//  entajpbokso("<BR>","littolajxo",$partopreno->datoj[litolajxo][0],"J",
  //     "JES","Mi mendas litolajxon");
if (KAMPOELEKTO_IJK) {
    debug_echo("<!-- konflin-elekto: " . var_export($GLOBALS['konfirmilolingvoj_elekto'], true) . "-->");
    $temp_listo = array('eo' => "nur Esperanto");
    foreach($GLOBALS['konfirmilolingvoj_elekto'] AS $kodo => $nomo)
        {
            $temp_listo[$kodo] = "Ankau^ " . $nomo;
        }
    eoecho("<br/> Konfirmilo-lingvo:");
    // TODO: radiaj butonoj
    elektilo_simpla('konfirmilolingvo', $temp_listo,
                    $_SESSION['partopreno']->datoj['konfirmilolingvo'],
                    "", 4);
    echo "<br/>";
 }
 else {
  entajpbokso("<BR>","germanakonfirmilo",$_SESSION["partopreno"]->datoj[germanakonfirmilo]{0},"J","J","Mi deziras (ankau^) germanan konfirmilon.","");
  entajpbokso("<BR>","komencanto",$_SESSION["partopreno"]->datoj[komencanto][0],"J","J","Mi estas novulo / komencanto (ne plu uzu).<BR>");
 }

entajpbutono("Lingva nivelo: ", 
			 'nivelo',$_SESSION['partopreno']->datoj['nivelo'],"f", 'f',
			  "flua parolanto &nbsp; ");
entajpbutono("", 'nivelo',$_SESSION['partopreno']->datoj['nivelo'],"p", 'p',
			  " parolanto &nbsp; ");
entajpbutono("", 'nivelo',$_SESSION['partopreno']->datoj['nivelo'],"k", 'k',
			 "komencanto. <br/>\n");

entajpbutono("Studento: ", 'studento', $_SESSION['partopreno']->datoj['studento'],
             'j', 'j', "jes");
entajpbutono(" | ", 'studento', $_SESSION['partopreno']->datoj['studento'],
             'n', 'n', "ne");
entajpbutono(" | ", 'studento', $_SESSION['partopreno']->datoj['studento'],
             '?', '?', "ni ne scias", "kutima");


echo "<hr/>\n";

if (!KAMPOELEKTO_IJK) {

  entajpbokso("<BR>","ekskursbileto",$_SESSION["partopreno"]->datoj[ekskursbileto][0],"J",
       "JES","Mi mendas bileton por la tutaga ekskurso (krompago de 7 E^)");

echo "<hr/>\n";
 }
  
  echo "Mi volas kontribui<BR>\n";
  entajpboksokajejo(temabokso,$temabokso,"JES","JES","per:","&nbsp;al la tema programo",tema,$_SESSION["partopreno"]->datoj[tema],40,"Kiel vi deziras kontribui?");
  entajpboksokajejo(distrabokso,$distrabokso,"JES","JES","per:","&nbsp;al la distra programo",distra,$_SESSION["partopreno"]->datoj[distra],40,"Kiel vi deziras kontribui?");
  entajpboksokajejo(vesperabokso,$vesperabokso,"JES","JES","per:","&nbsp;al la vespera programo ",vespera,$_SESSION["partopreno"]->datoj[vespera],40,"Kiel vi deziras kontribui?");
  entajpboksokajejo(muzikabokso,$muzikabokso,"JES","JES","per:","&nbsp;al la muzika programo",muzika,$_SESSION["partopreno"]->datoj[muzika],40,"Kiel vi deziras muziki?");
  entajpboksokajejo(noktabokso,$noktabokso,"JES","JES","per:","&nbsp;al la nokta programo",nokta,$_SESSION["partopreno"]->datoj[nokta],40,"Kiel vi deziras kontribui?");

  eoecho ("Se vi faros bonvolu rekte kontaktu ankau^ niajn programordigantojn.<br/>");

echo "<hr/>\n";

  entajpbutono("<BR>Asekuro: ", 'havas_asekuron',
               $_SESSION["partopreno"]->datoj['havas_asekuron'][0],
               "J",'J',
               "Mi <em>havas</em> asekuron pri malsano kaj kunportos la" .
               " bezonatajn paperojn.<br/>", "kutima");
entajpbutono("",'havas_asekuron',
             $_SESSION["partopreno"]->datoj['havas_asekuron'][0],
             "N",'N',
             "Mi <em>ne havas</em> tau^gan asekuron. (En tiu c^i kazo".
             " GEJ asekuros vin.)");

echo "<hr/>\n";

// TODO: invitleterolimdato aus DB nehmen (ne tiom
//        urgxas en la interna formularo)

//==> estas nun en aparta invitpeto-objekto.
//
// entajpboksokajejo('invitletero',
//                   $_SESSION["partopreno"]->datoj['invitletero'],
//                   "JES","JES",
//                   "Mi bezonas oficialan invitleteron por Germanio <BR>(Nepre petu antau^ 200x/11/01! kaj estas krompago de 5 E^)<BR>Mia pasportnumero:",
//                   "",
//                   'pasportnumero',
//                   $_SESSION["partopreno"]->datoj['pasportnumero'],25,
//                   "Se vi bezonas invitilon, ni bezonas vian pasportnumeron.");
// if ($_SESSION["partopreno"]->datoj['invitletero']=="J")
//     entajpejo ("La invitilo estis sendata je la:",'invitilosendata',
//                $_SESSION["partopreno"]->datoj['invitilosendata'],11,"","",
//                " (jaro-monato-tago)");

// echo "<hr/>\n";

entajpbutono("TEJO-membro lau^dire: ",'tejo_membro_laudire',
             $_SESSION["partopreno"]->datoj['tejo_membro_laudire'][0],
             "j",'j','jes');
entajpbutono("",'tejo_membro_laudire',
             $_SESSION["partopreno"]->datoj['tejo_membro_laudire'][0],
             "n",'n',"ne","kutima");
echo "<br/>\n";

    entajpbutono("TEJO-membro kontrolita: ",'tejo_membro_kontrolita',
                 $_SESSION["partopreno"]->datoj['tejo_membro_kontrolita'][0],
                 "j",'j','membro');
entajpbutono(" | ",'tejo_membro_kontrolita',
             $_SESSION["partopreno"]->datoj['tejo_membro_kontrolita'][0],
             "?",'?', 'ne jam kontrolita', 'kutima');
entajpbutono(" | ",'tejo_membro_kontrolita',
             $_SESSION["partopreno"]->datoj['tejo_membro_kontrolita'][0],
             "n",'n', 'ne membro');
entajpbutono(" | ", 'tejo_membro_kontrolita',
              $_SESSION["partopreno"]->datoj['tejo_membro_kontrolita'][0],
              'i', 'i', "ig^os membro surloke kaj pagas por tio");
entajpbutono(" | ", 'tejo_membro_kontrolita',
              $_SESSION["partopreno"]->datoj['tejo_membro_kontrolita'][0],
              'p', 'p', "pagas al TEJO/UEA, sed ne ig^as TEJO-membro" .
              " (= ne ricevas rabaton).");

entajpejo("<br/>TEJO-membrokotizo (au^ aliaj pagoj al UEA):",
          "tejo_membro_kotizo",
          $_SESSION['partopreno']->datoj['tejo_membro_kotizo'],
          5);

if (!KAMPOELEKTO_IJK) {

    // TODO: faru individuan krompagon el tio.

    echo "<hr/>";


    entajpbutono(deviga_membreco_nomo."-membro (lau^ alig^ilo): ",'GEJmembro',
                 $_SESSION["partopreno"]->datoj['GEJmembro'][0],
                 "J",'J','jes');
    entajpbutono(" &nbsp; ",'GEJmembro',
                 $_SESSION["partopreno"]->datoj['GEJmembro'][0],
                 "N",'N',"ne","kutima");

    eoecho ("<br/>(Estas krompago por " . nemembreculoj .
            ", kiuj ne estas membroj de " . deviga_membreco .
            ", sed eblas membrig^i surloke)\n");
    echo "<table>\n";        
    tabel_entajpbutono("C^u surloka membrokotizo?", 'surloka_membrokotizo',
                       $_SESSION["partopreno"]->datoj['surloka_membrokotizo'],
                       '?', "? - ne jam traktita (au^ antaukontrolo donis rezulton, ke ankorau^ ne pagis)", 'kutima', true);
    tabel_entajpbutono("",  'surloka_membrokotizo',
                       $_SESSION["partopreno"]->datoj['surloka_membrokotizo'],
                       'n',  "n - ne estas membro kaj ne devas esti (ekzemple eksterlandanoj)",
                       "", true);
    tabel_entajpbutono("",  'surloka_membrokotizo',
                       $_SESSION["partopreno"]->datoj['surloka_membrokotizo'],
                       'a', "a - jam membro, ne devas pagi nun (antau^e pagis/senpaga membro/enkasigrajtigo)",
                       "", true);
    tabel_entajpbutono("",  'surloka_membrokotizo',
                       $_SESSION["partopreno"]->datoj['surloka_membrokotizo'],
                       'j', "j - jam estas membro, surloke rekotizas",
                       "", true);
    tabel_entajpbutono("",  'surloka_membrokotizo',
                       $_SESSION["partopreno"]->datoj['surloka_membrokotizo'],
                       'i',  "i - ig^as nova membro kaj surloke pagas",
                       "", true);
    tabel_entajpbutono("",  'surloka_membrokotizo',
                       $_SESSION["partopreno"]->datoj['surloka_membrokotizo'],
                       'h',  "h - nova membro, ne pagas nun (senkosta membreco au^ enkasigrajtigo",
                       "", true);
    tabel_entajpbutono("",  'surloka_membrokotizo',
                       $_SESSION["partopreno"]->datoj['surloka_membrokotizo'],
                       'k', "k - devus membri, sed anstatau^e krompagas",
                       "", true);
    tabelentajpejo("membrokotizo/krompago", 'membrokotizo', $_SESSION["partopreno"]->datoj['membrokotizo'], 6, "E^");
    echo "</table>\n";

 }

echo "<hr/>\n";


  //entajpejo ("rimarkoj:",partoprenrimarkoj,$partopreno->datoj[rimarkoj],30,"","","");
  ?>
  <br/><BR>
    <b>Rimarkoj:</b> Mi havas la jenajn rimarkojn:<BR>
      <textarea name="rimarkoj" cols="57" rows="5" wrap="soft"><?php print $_SESSION["partopreno"]->datoj[rimarkoj]; ?></textarea>

  <hr/>
   <?php

  //   entajpbokso("<BR>","ekskursbileto",$_SESSION["partopreno"]->datoj[ekskursbileto][0],"J",
  //       "JES","Mi mendas bileton por la tutaga ekskurso (krompago de 7 E^)");


      entajpbutono("interreta listo:", 'listo',
                   $_SESSION['partopreno']->datoj['listo'],
                   'J', 'J', "Mi volas aperi", "kutima");
      entajpbutono(" &nbsp; ", 'listo',
                   $_SESSION['partopreno']->datoj['listo'],
                   'N', 'N', "Mi ne volas aperi");


      entajpbutono("<br/>postrenkontig^a partoprenintolisto:", 'intolisto',
                   $_SESSION['partopreno']->datoj['intolisto'],
                   'J', 'J', "Mi volas aperi", "kutima");
      entajpbutono(" &nbsp; ", 'intolisto',
                   $_SESSION['partopreno']->datoj['intolisto'],
                   'N', 'N', "Mi ne volas aperi");


entajpejo("<br/>Pagmaniero lau^ alig^ilo:", 'pagmaniero',
          $_SESSION["partopreno"]->datoj['pagmaniero'], 20);


entajpbokso("<hr/>","KKRen",$_SESSION["partopreno"]->datoj['KKRen'][0],
            "J", "J",
            "estas ".organizantoj_nomo.
            "ano (validas por la 1a kategorio).<BR>");

echo "<hr/>";

  
  $vosto = date("Y-m-d");
  entajpejo ("<br> alvenodato (de la alig^ilo):",aligxdato,$_SESSION["partopreno"]->datoj[aligxdato],11,"","$vosto"," (jaro-monato-tago)");

  if ( ($_SESSION["partopreno"]->datoj[aligxdato] != "")
       and ( !kontrolu_daton($_SESSION["partopreno"]->datoj[aligxdato]) )
       )
  {
    erareldono ("La dato kion vi entajpis ne ekzistas au^ estis malg^uste.");
  }

  if ($_SESSION["partopreno"]->datoj[aligxkategoridato] == "0000-00-00")
  {
    $_SESSION["partopreno"]->datoj[aligxkategoridato] = "";
  }

  entajpejo ("<br> relevanta dato por la alig^kategorio:",aligxkategoridato,$_SESSION["partopreno"]->datoj[aligxkategoridato],11,"","",
      " (jaro-monato-tago)<BR>(Nur uzu por specialaj rabatoj)");

  if ( ($_SESSION["partopreno"]->datoj[aligxkategoridato])
       and ( !kontrolu_daton($_SESSION["partopreno"]->datoj[aligxkategoridato]) )
       )
  {
    erareldono ("La dato kion vi entajpis ne ekzistas au^ estis malg^uste.");
  }   
  
  entajpejo ("<br> alvenodato (de la malalig^ilo):",'malaligxdato',$_SESSION["partopreno"]->datoj['malaligxdato'],11,"","0000-00-00"," (jaro-monato-tago)");
  if ( (is_null($_SESSION["partopreno"]->datoj['malaligxdato']))
       and ( !kontrolu_daton($_SESSION["partopreno"]->datoj['malaligxdato']) )
       )
  {
    erareldono ("La dato kion vi entajpis ne ekzistas au^ estis malg^uste.");
  }

  if  (kalkulu_tagojn($_SESSION["partopreno"]->datoj["aligxdato"], $_SESSION["partopreno"]->datoj["malaligxdato"]) < 0 )
     
  {
    erareldono ("La malalig^dato estas antau^ la alig^dato.");
  }
  
  echo "<BR>";

  if (rajtas("administri"))
  {
    entajpejo ("<br> 1a konfirmilo sendata:",'1akonfirmilosendata',$_SESSION["partopreno"]->datoj['1akonfirmilosendata'],11,"","",
      " (jaro-monato-tago)");
    entajpejo ("<br> 2a konfirmilo sendata:",'2akonfirmilosendata',$_SESSION["partopreno"]->datoj['2akonfirmilosendata'],11,"","",
      " (jaro-monato-tago)");
  }


  if ($_SESSION["partopreno"]->datoj[ID])
    {
      echo "partopreno-ID: ".$_SESSION["partopreno"]->datoj[ID]." <BR>\n";
    } // muss noch ge䮤ert werden


    echo "<p align=center>\n";
    entajpbokso("","nekontrolup",$nekontrolup,"JES","JES","Se vi maldeziras datkontroladon pro problemojn, marku c^i tie.<BR>");


  echo "<hr/><p><b>Por A- kaj B-Landanoj: </b>\n";
  eoecho ("Precipe mi konscias, ke mia alig^o validas nur ekde la ".
          "alveno de mia antau^pago c^e GEJ.</p>");



    entajpbokso("","konsento",$konsento[0],"J","JES","Mi legis kaj agnoskas la suprajn kondic^ojn.<br/>","J");

    if ($_SESSION["ago"] == "sxangxi")
    {
		if ($_SESSION['sekvontapagxo'])
		{
			ligu ($_SESSION['sekvontapagxo'], "ne s^ang^u kaj pluen");
		}
		else
		{
	      ligu("partrezultoj.php?partoprenantoidento=" .
				$_SESSION["partoprenanto"]->datoj[ID] .
			   "&partoprenidento=" . $_SESSION["partopreno"]->datoj[ID],
		   	"ne s^ang^u kaj reen");
		}
      tenukasxe("ago",$_SESSION["ago"]);     //sqlago=forgesu&
      send_butono("S^ang^u!");
    }
    else
    {
      send_butono("Aligu!");
    }
    echo "</p>";

    echo "</TD><TD width=20%></TD></TR></TABLE>\n";
    echo "</form>\n";

    HtmlFino();

?>
