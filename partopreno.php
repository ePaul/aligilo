<?php

/***
 * Redaktado de la partopreno-datoj.
 * 
 * parametroj:
 *
 *  $ago = sxangxi  sxangxu ekzistantan partoprenon.
 *                  Uzu aux $_SESSION['partopreno'] aux
 *                  $partoprenidento por eltrovi kiun.
 *
 *  $sp = forgesi  - forgesu la nunan $_SESSION['partopreno']
 *                   (kaj la lastan $ago) kaj anstatauxe kreu
 *                   novan partoprenon. Uzas $partoprenantoidento
 *                   aux $_SESSION['partoprenanto'] por eltrovi,
 *                   por kiun partoprenanton.
 *
 *  $partoprenidento    - la ID de la redaktenda partopreno.
 *  $partoprenantoidento - la ID de la partoprenanto, por kiu
 *                         oni kreu partoprenon.
 *
 *  $parto = korektigi - montru erarojn en la datoj.
 */


require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();

if (!rajtas("aligi"))
{
  ne_rajtas();
}


// TODO: ////////////////////////Immer gleich, mach mal 'ne Funktion draus//////
// dafür gibt es besser lösungen!!
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

if ($_REQUEST['partoprenidento'])
{
  $_SESSION['partopreno'] = new Partopreno($_REQUEST['partoprenidento']);
  $_SESSION['partoprenanto'] =
	new Partoprenanto($_SESSION['partopreno']->datoj['partoprenantoID']);
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
			  eltrovu_renkontigxo($_SESSION["partopreno"]->datoj["renkontigxoID"]) .
			  "</strong>.\n");
  }
echo "</p>";

  entajpbokso("<BR><BR>","retakonfirmilo",$_SESSION["partopreno"]->datoj[retakonfirmilo][0],"J","JES","Mi deziras retan konfirmilon.","");
// TODO: invitleterolimdato auf 2004 umstellen / aus DB nehmen
  echo "<HR>";

  entajpbutono("",partoprentipo,$_SESSION["partopreno"]->datoj[partoprentipo][0],"t",tuttempa,"tuttempa partopreno (de ".$_SESSION["renkontigxo"]->datoj[de]." g^is ".$_SESSION["renkontigxo"]->datoj[gxis].")","kutima");
  echo "<BR>";
  entajpbutono("",partoprentipo,$_SESSION["partopreno"]->datoj[partoprentipo][0],"p",partatempa,partatempa);

  echo "partopreno de:\n";

  echo "<select name=\"de\" size=1>\n";

  // TODO GEht kürzer, oder als Fkt.
echo "<!--\n Renkontigxo:";
var_export($_SESSION["renkontigxo"]);
echo "-->";

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
   echo "</select> <BR>\n";
   if (($parto=="korektigi") and (($_SESSION["partopreno"]->datoj[de])>($_SESSION["partopreno"]->datoj[gxis])))
   {
     erareldono("Via 'gis' Dato estas antau^ au^ je la 'de' dato");
   }
  echo "<hr/>";  




  entajpbutono("<BR>",domotipo,$_SESSION["partopreno"]->datoj[domotipo][0],"J",JunularGastejo,"Mi volas log^i en la seminariejo <p>",kutima);

  entajpbutono("Mi preferas log^i en:&nbsp;",cxambrotipo,$_SESSION["partopreno"]->datoj[cxambrotipo][0],"u","unuseksa","unuseksa c^ambro");
  entajpbutono("",cxambrotipo,$_SESSION["partopreno"]->datoj[cxambrotipo][0],"g","gea","gea c^ambro",kutima);
  //entajpbutono("",cxambrotipo,$partopreno->datoj[cxambrotipo][0],"n","negravas","ne gravas<BR>",kutima);

  if($domotipo=="MemZorganto" and $_SESSION["partopreno"]->datoj[cxambrotipo]!="gea")
  {
    erareldono ("<BR>Ne haveblas unuseksa c^ambrojn memzorge ");
  }
  echo "<BR>";
  entajpboksokajejo(kunekun,$kunekun,"JES","JES","Mi s^atus log^i kune kun:","",kunkiu,
         $_SESSION["partopreno"]->datoj[kunkiu],25,"Kun kiun vi s^atus log^i kune?");

  entajpbokso("","dulita",$_SESSION["partopreno"]->datoj[dulita][0],"J",                    //aus der DB holen!!
        "JES","Mi mendas du-litan c^ambron. <br> (limigita kvanto - krompago 20 E^ po persono)");

  if( $domotipo == "MemZorganto"
      and $_SESSION["partopreno"]->datoj[dulita]=="JES"
      )
  {
    erareldono ("<BR>Ne haveblas dulitajn c^ambrojn memzorge");
  }

  entajpbutono("</p>",domotipo,$_SESSION["partopreno"]->datoj[domotipo][0],"M",MemZorganto,"Mi volas log^i kiel memzorganto en amaslog^ejo, se ekzistas tia");
  echo "<hr/>\n";


	//  entajpbokso("","vegetare",$_SESSION["partopreno"]->datoj[vegetare][0],"J","JES","Mi s^atus mang^i vegetare.");
entajpbutono("Mi s^atus mang^i ... <br/>",
			 'vegetare',$_SESSION['partopreno']->datoj['vegetare']{0},"N", 'N nevegetare',
			  "nevegetare | ", "kutima");
entajpbutono("", 'vegetare',$_SESSION['partopreno']->datoj['vegetare']{0},"J", 'J vegetare',
			  " vegetare | ");
entajpbutono("", 'vegetare',$_SESSION['partopreno']->datoj['vegetare']{0},"A", 'A vegane',
			 "vegane. <br/>");

echo "<hr/>\n";

//  entajpbokso("<BR>","littolajxo",$partopreno->datoj[litolajxo][0],"J",
  //     "JES","Mi mendas litolajxon");

  entajpbokso("<BR>","germanakonfirmilo",$_SESSION["partopreno"]->datoj[germanakonfirmilo]{0},"J","JES","Mi deziras (ankau^) germanan konfirmilon.","");
  entajpbokso("<BR>","komencanto",$_SESSION["partopreno"]->datoj[komencanto][0],"J","JES","Mi estas novulo / komencanto (ne plu uzu).<BR>");

entajpbutono("Lingva nivelo: ", 
			 'nivelo',$_SESSION['partopreno']->datoj['nivelo'],"f", 'f',
			  "flua parolanto | ");
entajpbutono("", 'nivelo',$_SESSION['partopreno']->datoj['nivelo'],"p", 'p',
			  " parolanto | ");
entajpbutono("", 'nivelo',$_SESSION['partopreno']->datoj['nivelo']},"k", 'k',
			 "komencanto. <br/>");


echo "<hr/>\n";

  entajpbokso("<BR>","ekskursbileto",$_SESSION["partopreno"]->datoj[ekskursbileto][0],"J",
       "JES","Mi mendas bileton por la tutaga ekskurso (krompago de 7 E^)");

echo "<hr/>\n";
  
  echo "Mi volas kontribui<BR>\n";
  entajpboksokajejo(temabokso,$temabokso,"JES","JES","per:","&nbsp;al la tema programo",tema,$_SESSION["partopreno"]->datoj[tema],40,"Kiel vi deziras kontribui?");
  entajpboksokajejo(distrabokso,$distrabokso,"JES","JES","per:","&nbsp;al la distra programo",distra,$_SESSION["partopreno"]->datoj[distra],40,"Kiel vi deziras kontribui?");
  entajpboksokajejo(vesperabokso,$vesperabokso,"JES","JES","per:","&nbsp;al la vespera programo ",vespera,$_SESSION["partopreno"]->datoj[vespera],40,"Kiel vi deziras kontribui?");
  entajpboksokajejo(muzikabokso,$muzikabokso,"JES","JES","per:","&nbsp;al la muzika programo",muzika,$_SESSION["partopreno"]->datoj[muzika],40,"Kiel vi deziras muziki?");
  entajpboksokajejo(noktabokso,$noktabokso,"JES","JES","per:","&nbsp;al la nokta programo",nokta,$_SESSION["partopreno"]->datoj[nokta],40,"Kiel vi deziras kontribui?");

  eoecho ("Se vi faros bonvolu rekte kontaktu ankau^ niajn programordigantojn.<br/>");

echo "<hr/>\n";

  entajpbutono("<BR>Asekuro: ",havas_asekuron,$_SESSION["partopreno"]->datoj[havas_asekuron][0],"J",JES,'Mi <em>havas</em> asekuron pri malsano kaj kunportos la bezonatajn paperojn.<br/>', "kutima");
  entajpbutono("",havas_asekuron,$_SESSION["partopreno"]->datoj[havas_asekuron][0],"N",NE,"Mi <em>ne havas</em> tau^gan asekuron. (En tiu c^i kazo GEJ asekuros vin.)");

echo "<hr/>\n";

  entajpboksokajejo(invitletero,$_SESSION["partopreno"]->datoj[invitletero],"JES","JES",
    "Mi bezonas oficialan invitleteron por Germanio <BR>(Nepre petu antau^ 2003/11/01! kaj estas krompago de 5 E^)<BR>Mia pasportnumero:","",
    pasportnumero,$_SESSION["partopreno"]->datoj[pasportnumero],25,"Se vi bezonas invitilon, ni bezonas vian pasportnumeron.");
  //TODO: dieses Datum auch noch aus der DB ziehen.
  if ($_SESSION["partopreno"]->datoj[invitletero]=="J")
    entajpejo ("La invitilo estis sendata je la:",invitilosendata,$_SESSION["partopreno"]->datoj[invitilosendata],11,"","",
      " (jaro-monato-tago)");

echo "<hr/>\n";

    entajpbutono("TEJO-membro lau^dire: ",'tejo_membro_laudire',$_SESSION["partopreno"]->datoj['tejo_membro_laudire'][0],"j",j,jes);
  entajpbutono("",'tejo_membro_laudire',$_SESSION["partopreno"]->datoj['tejo_membro_laudire'][0],"n",ne,"ne","kutima");
echo "<br/>\n";

    entajpbutono("TEJO-membro kontrolita: ",'tejo_membro_kontrolita',$_SESSION["partopreno"]->datoj['tejo_membro_kontrolita'][0],"j",'j','membro');
entajpbutono(" | ",'tejo_membro_kontrolita',$_SESSION["partopreno"]->datoj['tejo_membro_kontrolita'][0],"?",'?', 'ne jam kontrolita', 'kutima');
    entajpbutono(" | ",'tejo_membro_kontrolita',$_SESSION["partopreno"]->datoj['tejo_membro_kontrolita'][0],"n",'n', 'ne membro');


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


  entajpbokso("interreta Listo: ", 'listo', $_SESSION['partopreno']->datoj[listo]{0},
			  "N", "NE", "Mi ne volas aperi en la interreta listo de la partoprenantoj.", "", "ne");

entajpejo("Pagmaniero lau^ alig^ilo:", 'pagmaniero', $_SESSION["partopreno"]->datoj['pagmaniero'], 20);
echo "<hr/>";

  entajpbutono(deviga_membreco_nomo."-membro: ",GEJmembro,$_SESSION["partopreno"]->datoj[GEJmembro][0],"J",JES,jes);
  entajpbutono("",GEJmembro,$_SESSION["partopreno"]->datoj[GEJmembro][0],"N",NE,"ne","kutima");
  eoecho ("<BR>(Estas krompago de ".$_SESSION["renkontigxo"]->datoj[nemembrecpunpago]." E^ por ".nemembreculoj." kiuj ne estas membro de ".deviga_membreco.", sed eblas membrig^i surloke)\n");

  entajpbokso("<br/>","kunmangxas",$_SESSION["partopreno"]->datoj[kunmangxas][0],"J","JES","kunmang^as (nur por specila uzo)");
  entajpbokso("<BR>","KKRen",$_SESSION["partopreno"]->datoj[KKRen][0],"J","JES","estas ".organizantoj_nomo."ano (validas por la 1a kategorio).<BR>");      
 

  //<!-- fino ---- nur por KKRenanoj ---- -->

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
      echo "partoprenID: ".$_SESSION["partopreno"]->datoj[ID]." <BR>\n";
    } // muss noch geändert werden

    /*if ($partopreno->datoj[entajpanto])
    {
      if ($partopreno->datoj[sxangxanto])
      {
        eoecho ("s^ang^ata de: ".eltrovu_entajpanto($partopreno->datoj[sxangxanto])."(".$partopreno->datoj[sxangxdato]."<BR>\n");
      }
      $partopreno->datoj[sxangxdato] = date("Y-m-d");
      $partopreno->datoj[sxangxanto] = $kkren[entajpanto];
      echo "entajpata de: ".eltrovu_entajpanto($partopreno->datoj[entajpanto])." (".
      $partopreno->datoj[entajpdato].")\n";
    }
    else
    {
      eoecho ("(j^us)\n");
      $partopreno->datoj[entajpanto] = $kkren[entajpanto];
      $partopreno->datoj[entajpdato] = date("Y-m-d");
      echo "entajpata de: ".eltrovu_entajpanto($partopreno->datoj[entajpanto])." (".
          $partopreno->datoj[entajpdato].")\n";
    } */

 /*   echo "<div style=\"text-align:right;margin-right:5%;\">";
    entajpejo ("rabato:",rabato,$partopreno->datoj[rabato],5,"","","&nbsp;E^");
    entajpejo ("rabatkialo:",kialo,$partopreno->datoj[kialo],30,"","","");

    if ( $partopreno->datoj[rabato] != 0
         and $partopreno->datoj[kialo] == ""
         )
    {
      erareldono("Kial ".$_SESSION["partoprenanto"]->personapronomo." ricevas rabaton?");
    }

    echo "</div>";
*/
    echo "<p align=center>\n";
    entajpbokso("","nekontrolup",$nekontrolup,"JES","JES","Se vi maldeziras datkontroladon pro problemojn, marku c^i tie.<BR>");

    //<!-- /KKRen ---- -->

  echo "<hr/><p><b>Por A- kaj B-Landanoj: </b>\n";
  eoecho ("Precipe mi konscias, ke mia alig^o validas nur ekde la ".
          "alveno de mia antau^pago c^e GEJ.</p>");



    entajpbokso("","konsento",$konsento[0],"J","JES","Mi legis kaj agnoskas la suprajn kondic^ojn.<BR>","J");

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
