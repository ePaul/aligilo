<?php
/* #################################################################### *
 * Cxi tie okazas la aligxado de PARTOPRENANTOJ, k.e. nomo, adreso ktp.
 *
 * Tiu pagxo estas lauxmezure kudrita por IS/GEJ kaj la
 * dulingva sistemo de Pauxlo.
 * Gxin inkludas /is/dulingva/2006/aligxilo.php,
 * kiu jam metas la kapon kaj poste la piedon. Ankaux
 * eblas uzi la funkciojn de /is/dulingva/lib/dulingva.php.
 *
 * La malnovan dosieron vi trovas cxe publik-2003.php.
 * #################################################################### */

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

//session_start();

// $enkodo="unikodo";
$_SESSION["enkodo"]="unikodo";

/*if (empty($HTTP_GET_VARS[enkodo]))
{
  $enkodo="x-metodo";
}
else
{
  $enkodo = $HTTP_GET_VARS[enkodo];
}*/

malfermu_datumaro();

/**
 * prenas informojn pri la aktuala Renkontigxo
 * el la datumbaztabelo
 */

$renkontigxo = new Renkontigxo(DEFAUXLTA_RENKONTIGXO);

echo "<!-- renkontigxo: " . $renkontigxo->datoj["mallongigo"] . " -->";


t(<<<ENDE
<h1>50. IS &mdash; Anmeldeformular</h1>

<h2>27.12.2006 &mdash; 3.01.2007 en Wewelsburg</h2>

  <p>Bitte lies dir <strong>zuerst</strong> die <a href="kondicxoj">Teilnahme&shy;bedingungen</a>
  durch &ndash; auch wenn du meinst, sie schon von fr&uuml;her zu kennen.</p>

ENDE
, <<<FINO
<h1>50a IS &mdash; ali&#285;ilo</h1>
<h2>27.12.2006 &mdash; 3.01.2007 en Wewelsburg</h2>

  <p>
  Bonvolu legi <strong>anta&#365;e</strong> la <a href="kondicxoj">partopren&shy;kondi&#265;ojn</a> &ndash; e&#265; se vi pensas, ke vi jam konas ilin de la lastaj jaroj.
  </p>

FINO
);




granda_kesto_komenco();
?>
    <form method="post" action="<?php echo $aligxilonomo; ?>?ago=kontrolu">
			<?php /* Im Standalone-Betrieb doch wieder aligxilo.php */ ?>
    <table>
    <?php
    if ($parto == "korektigi")
    {
      erareldono_geo ("Bitte &uuml;berpr&uuml;fe deine Daten","Bonvolu kontroli viajn datojn");
    }
    entajpejoB ("Vorname", "Persona nomo:",personanomo,$personanomo,40,"Vornamen","personan nomon");
    entajpejoB ("Nachname", "Familia nomo:",nomo,$nomo,40,"Nachnamen", "familian nomon");


    echo "<tr><td >";
    geoecho ("<b>", "Mein Geschlecht ist:<br/>", "Mia sekso estas:</b></td><td >");
    entajpbutonoB ("sekso", $sekso[0], "i", "ina", "weiblich", "ina ");
    entajpbutonoB ("sekso",$sekso[0],"v","vira","m&auml;nnlich","vira ");
    if ($parto=="korektigi" and $sekso!="ina" and $sekso!="vira")
    {
      erareldono_geo ("Bitte gib dein Geschlecht an!", "Bonvolu indiku vian sekson!");
    }
    echo "</td></tr>\n";

    entajpejoB ("Geburtsdatum", "Naskig^dato:",naskigxdato,$naskigxdato,12,"","","1900-00-00","Jahr-Monat-Tag", "jaro-monato-tago");
//    echo "<tr><td class=\"green\"/><td class=\"green\">";
    if (($naskigxdato!="") and (!kontrolu_daton($naskigxdato)))
    {
      erareldono_geo ("Das von dir eingegebene Geburtsdatum existiert nicht oder ist falsch.", "La naskig^dato, kiun vi entajpis, ne ekzistas au^ estis malg^uste.");
    }

    entajpejoB ("Adresszusatz:", "Adresaldonaj^o:",adresaldonajxo,$adresaldonajxo,30);
    entajpejoB ("Stra&szlig;e:", "Strato:",strato,$strato,35,"Stra&szlig;e","straton");
    entajpejoB ("Bundesland:","Provinco:",provinco,$partoprenanto->datoj[provinco],20);
    entajpejoB ("Stadt", "Urbo:",urbo,$urbo,20,"Stadt", "urbon");
    entajpejoB ("PLZ", "Pos^tkodo:",posxtkodo,$posxtkodo,7,"");

	// TODO: DE
    geoecho ("<tr><td ><b>","Wohn-Land:<br/>", "Log^lando:</b></td><td >");

montru_landoelektilon(5, $lando ? $lando : "-#-");

    geoecho ("(", "'Deutschland' ist 'Germanio'. Wenn dein Land fehlt, w&auml;hle 'Alia Lando' und gib es bei 'Bemerkungen' ein. <br/>", "Se mankas via lando, uzu 'Alia Lando' kaj entajpu g^in al 'rimarkoj')");
    echo "</td></tr>\n";

    entajpejoB ("Telefon", "Telefono:",telefono,$telefono,30,"","", "",  "international", "internacie");
    entajpejoB ("Telefax:", "Telefakso:",telefakso,$telefakso,30,"","", "", "international", "internacie");
    entajpejoB ("E-Mail", "Retpos^ta adreso:",retposxto,$retposxto,40);

				  // TODO: DE
//     echo "<tr><td class=\"green\"><b>Profesio:</b></td><td class=\"green\"><select name=\"okupigxo\" size=\"1\">\n";
//     for ($i=0;$i<13;$i++)
//     {
//       if (okupigxtipo($i)!="ne indikis")
//       {
//         echo "<option";
//         if ($i == $okupigxo) echo " selected=\"selected\"";
//         echo " value = \"$i\">";
//         eoecho ("Mi ".okupigxtipo($i));
//         echo "</option>\n";
//       }
//     }
//     echo " </select>";
//     entajpejo ("",okupigxteksto,$okupigxteksto,20,"","","");
//     echo "</td></tr>\n";

geoecho ("<tr><td><strong>", "DEJ/DEB-Mitglied:<br/>", "GEJ/GEA-membro: </strong></td><td>");
    entajpbutonoB("GEJmembro",$GEJmembro[0],"J","JES","Ja", "jes");
    entajpbutonoB("GEJmembro",$GEJmembro[0],"N","NE","Nein","ne","kutima");
    echo "<br/><font size=\"-1\">";
    geoecho ("(","Einwohner von Deutschland, welche (f&uuml;r 2007) keine Mitglieder von DEJ" .
			 " bzw. DEB sind, zahlen ".
			     "einen Zuschlag in H&ouml;he des Mitgliedbeitrages. Es ist aber vor Ort m&ouml;glich, ".
     			 "Mitglied zu werden. / ", "Estas krompago (same alta kiel la membrokotizo)".
                 " por enlog^antoj de Germanio, kiuj ne estas (en 2007) membroj de GEJ/GEA,".
                 " sed eblas membrig^i surloke.)");
    echo "</font></td></tr>\n";

//    echo "<tr><td class=\"green\"><b>Invitletero:</b></td><td class=\"green\"><table class=\"green\">";
geoecho( "<tr><td><strong>",
         "TEJO-Mitglied: <br/>",
         "TEJO-membro</strong></td><td><table>");

    entajpboksokajejoB('tejo_membro_laudire',$tejo_membro_laudire,"jes","jes",
    "Ich werde 2007 individuelles Mitglied der Welt-Esperanto-Jugend <a href='http://www.tejo.org/'>TEJO</a> sein und m&ouml;chte daher den <a href='http://www.esperanto.de/is/de/2006/kondicxoj#tejo_rabato'>Rabatt</a> von 5 &euro; erhalten. Mein UEA-Code ist:<br/>",
                       "Mi estos (en 2007) individua membro de <a href='http://www.tejo.org/'>TEJO</a> kaj deziras ricevi <a href='http://www.esperanto.de/is/de/2006/kondicxoj#tejo_rabato'>rabaton</a> de 5 &euro;. Mia UEA-kodo estas:",
                       'ueakodo',
                       $ueakodo,
                       6, "", "");

echo "</table></td></tr>\n";

	geoecho("<tr><td ><b>", "Teilnahme <br/> ",
			"Partopreno: </b></td><td >");
    entajpbutonoB(partoprentipo,$partoprentipo[0],"t",tuttempa,
                  "Vollzeit-Teilnahme (von " . $renkontigxo->datoj[de] . " bis " .
                  $renkontigxo->datoj[gxis] . ")",
                 "tuttempa partopreno (de " . $renkontigxo->datoj[de] . " g^is " .
                  $renkontigxo->datoj[gxis].")","kutima");
    echo "</td></tr>\n<tr><td ></td><td >";
    entajpbutonoB(partoprentipo,$partoprentipo[0],"p",partatempa,"Teilzeit-Teilnahme von", "partatempa partopreno de ");

    // la datoj estas prenataj el la datumaro
    echo "<select name=\"de\" size=\"1\"> ";

    $dateloop = $renkontigxo->datoj[de];
    do
    {
      echo "<option";
      if ($de == $dateloop) echo " selected=\"selected\" ";
      echo ">$dateloop</option>";
      $dateloop=sekvandaton ($dateloop);
    } while ($dateloop != $renkontigxo->datoj[gxis])
  ?>
  </select>
   <? geoecho ("", "bis / ", "g^is:");?>
   <select name="gxis" size="1">
   <?$dateloop = $renkontigxo->datoj[de];
    do
    {
      $dateloop=sekvandaton ($dateloop);
      echo "<option";
      if (($gxis == $dateloop) or ((!$gxis)and ($dateloop == $renkontigxo->datoj[gxis]))) echo " selected=\"selected\" ";
      echo ">$dateloop";
      echo "</option>";
    } while ($dateloop != $renkontigxo->datoj[gxis]);
   echo "</select> </td></tr>\n";
   if (($parto=="korektigi") and ($de>$gxis))
   {
     echo "<tr><td ><td >";
     // TODO
     erareldono_geo("Dein 'bis'-Datum ist vor dem 'von'-Datum.", "Via 'g^is' Dato estas antau^ au^ je la 'de' dato.");
	 echo "</td></tr>\n";
   }

    //    entajpboksoB("Essen:", "Mang^aj^o:","vegetare",$vegetare[0],"J","JES","Ich will vegetarisch essen. ", "Mi s^atus mang^i vegetare.");

geoecho('<tr><td ><b>', "Essen:<br/>",'Mang^ado:</b></td><td >');
entajpbutonoB('vegetare',$vegetare{0},"N", 'N nevegetare', "Ich will nicht vegetarisch essen.",
			  "Mi volas mang^i nevegetare. <br/>", "kutima");
entajpbutonoB('vegetare',$vegetare{0},"J", 'J vegetare', "Ich will vegetarisch essen.",
			  "Mi volas mang^i vegetare. <br/>");
entajpbutonoB('vegetare',$vegetare{0},"A", 'A vegane', "Ich will vegan essen.",
			  "Mi volas mang^i vegane/vegetaj^e. <br/>");
echo ("</td></tr>");



    geoecho ("<tr><td ><b>", "Wohnung:<br/>","Log^ado:</b></td><td >");
    entajpbutonoB(domotipo,$domotipo[0],"J",JunularGastejo,"Ich will in der Jugendherberge wohnen.", "Mi volas log^i en la seminariejo.",kutima);
    echo "</td></tr>\n<tr><td /><td >";
    entajpbutonoB(domotipo,$domotipo[0],"M",MemZorganto,"Ich will in der Massenunterkunft wohnen, sofern eine solche existiert.", "Mi volas log^i kiel memzorganto en amaslog^ejo, se ekzistas tia.");
    echo "</td></tr>\n";

	geoecho ("<tr><td ><b>", "Zimmer:<br/>", "C^ambro:</b></td>");
    geoecho ("<td >", "Ich bevorzuge ein / ", "mi preferas<br/>");
    entajpbutonoB(cxambrotipo,$cxambrotipo[0],"u","unuseksa","gleichgeschlechtliches Zimmer",
				  "unuseksan c^ambron.<br/>");
    entajpbutonoB(cxambrotipo,$cxambrotipo[0],"g","gea","gemischtgeschlechtliches Zimmer", "gean c^ambron.<br/>",kutima);
    //entajpbutonoB(cxambrotipo,$cxambrotipo[0],"n","negravas", "unwichtig","ne gravas<br/>",kutima);
   echo "</td></tr>\n";

    if($domotipo=="MemZorganto" and $cxambrotipo!="gea")
    {
      echo "<tr><td ><td >";
      erareldono_geo ("In der Massenunterkunft gibt es keine gleichgeschlechtlichen Zimmer",
                      "Ne haveblas unuseksa c^ambro memzorge");
    }
    entajpboksoB("","","dulita",$dulita[0],"J","JES","Ich bestelle ein Zwei-Personen-Zimmer <font size=\"-1\">(begrenzte Anzahl, Zuzahlung 20 E^ pro Person)</font>. ", "Mi mendas du-personan c^ambron. <font size=\"-1\">(limigita kvanto, krompago po 20 E^ por c^iu persono)</font>.");
    if($domotipo=="MemZorganto" and $dulita=="JES")
    {
      echo "<tr><td ><td >";
      erareldono_geo ("In der Massenunterkunft gibt es keine Zwei-Personen-Zimmer. ",
					  "Ne haveblas dulitajn c^ambrojn memzorge.");
	  echo "</td></tr>\n";
    }
    echo "<tr><td ></td><td ><table id='x12345' class='green' style='width: 100%;'>";
    entajpboksokajejoB("kunekun",$kunekun,"JES","JES","Ich m&ouml;chte zusammenwohnen mit:<br/>", "Mi s^atus log^i kune kun:","kunkiu",$kunkiu,25,"Mit wem willst du zusammenwohnen?","Kun kiun vi s^atus log^i kune?");
	 echo "</table></td></tr>\n";
    echo "<tr><td ><b>Invitletero:</b></td><td ><table >";
    entajpboksokajejoB('invitletero',$invitletero,"JES","JES",
    "Ich brauche einen offiziellen <a href=\"kondicxoj#invitilo\">Einladungsbrief</a> nach Deutschland, daher die Nummer meines Reisepasses:<br/><font size=\"-1\">(Unbedingt vor dem 1.11. erbitten - Zusatzkosten von 5 E^ bzw. 10 E^)</font><br/>", "Mi bezonas oficialan <a href=\"kondicxoj#invitilo\">invitleteron</a> por Germanio, pro tio mia pasportnumero: <br/><font size=\"-1\">(Nepre petu antau^ la unua de novembro! Kaj estas krompago de 5 E^/10 E^)</font>",
    'pasportnumero',$pasportnumero,25,"Wenn du einen Einladungsbrief brauchst, brauchen wir deine Reisepassnummer.", "Se vi bezonas invitilon, ni bezonas vian pasportnumeron.");
    echo "</table></td></tr>\n";

    entajpboksoB("Ausflug", "Ekskurso:","ekskursbileto",$ekskursbileto[0],"J","JES","Ich bestelle eine Karte f&uuml;r den ganzt&auml;gigen Ausflug (Zuzahlung 7 E^). ", "Mi mendas bileton por la tuttaga ekskurso (krompago 7 E^).");

    geoecho("<tr><td ><b>","Ich m&ouml;chte beitragen zum: /<br/>", "Mi volas kontribui al la:</b></td>");
    geoecho("<td  >","Es gibt <a href=\"kondicxoj#rabatoj\">Rabatte</a> f&uuml;r Programmbeitragende. ", "Estos <a href=\"kondicxoj#rabatoj\">rabatoj</a> por programkontribuantoj.</td></tr>");
    entajpboksokajejoB("temabokso",$temabokso,"JES","JES","thematischen Programm durch:<br/>", "tema programo per:",tema,$tema,40,"Wie m&ouml;chtest du beitragen?", "Kiel vi deziras kontribui?");
    entajpboksokajejoB("distrabokso",$distrabokso,"JES","JES","Unterhaltungsprogramm durch:<br/>", "distra programo per:",distra,$distra,40,"Wie m&ouml;chtest du beitragen?", "Kiel vi deziras kontribui?");
    entajpboksokajejoB("vesperabokso",$vesperabokso,"JES","JES","Abendprogramm durch:<br/>", "vespera programo per:",vespera,$vespera,40,"Wie m&ouml;chtest du beitragen?", "Kiel vi deziras kontribui?");
    entajpboksokajejoB("muzikabokso",$muzikabokso,"JES","JES","Musikprogramm durch:<br/>", "muzika programo per:",muzika,$muzika,40,"Wie m&ouml;chtest du muzizieren?", "Kiel vi deziras muziki?");

    entajpboksokajejoB("noktabokso",$noktaabokso,"JES","JES","Nachtprogramm durch:<br/>", "nokta programo per:",nokta,$nokta,40,"Was m&ouml;chtest du zum Nachtprogramm beitragen?", "Kiel vi deziras kontribui al la nokta programo?");

    geoecho("<tr><td class='green'/><td class='green' >","Falls der Platz hier nicht reicht, trage mehr bei <em>Bemerkungen</em> ein. /", "Se c^i tie la loko ne sufic^as, aldonu plian informon c^e <em>rimarkoj</em>.</td></tr>");


    entajpboksoB("Anf&auml;nger:", "komencanto:","komencanto",$komencanto[0],"J","JES","Ich bin Neuling / Anf&auml;nger (bei Esperanto). ", "Mi estas novulo / komencanto (&#265;e Esperanto).");

/* ############## */

    geoecho ("<tr><td ><b>", "Versicherung:<br/>","Asekuro:</b></td><td >");
    entajpbutonoB("havas_asekuron",$havas_asekuron[0],"J","JES","Ich habe eine Krankenversicherung und bringe die notwendigen Papiere mit.", "Mi havas asekuron pri malsano kaj kunportos la bezonatajn paperojn.","kutima");
    echo "</td></tr>\n<tr><td /><td >";
    entajpbutonoB("havas_asekuron",$havas_asekuron[0],"N","NE","Ich habe keine passende Versicherung. (In diesem Fall wird GEJ dich versichern.)", "Mi ne havas tau^gan asekuron. (En tiu c^i kazo GEJ asekuros vin.)");
    echo "</td></tr>\n";



// entajpboksoB("Best&auml;tigung", "konfirmilo:","retakonfirmilo",$retakonfirmilo[0],"J","JES","Ich m&ouml;chte eine E-Mail-Best&auml;tigung (anstatt Papier). ", "Mi deziras retan konfirmilon (anstatau^ paperan).<br/>","J");

geoecho ('<tr><td rowspan="2" ><b>', "Best&auml;tigung:<br/>","Konfirmilo:</b></td><td >");
entajpbokseroB("retakonfirmilo",$retakonfirmilo{0},"J","JES","Ich m&ouml;chte eine E-Mail-Best&auml;tigung (anstatt Papier). ", "Mi deziras retan konfirmilon (anstatau^ paperan).<br/>","J");
echo '</td></tr><tr><td >';
entajpbokseroB("germanakonfirmilo",$germanakonfirmilo{0},"J","JES","Ich m&ouml;chte meine Best&auml;tigung auch auf Deutsch (nicht nur auf Esperanto). ", "Mi deziras mian konfirmilon ankau^ germane (anstatau^ nur en Esperanto).<br/>");
echo "</td></tr>\n";

entajpboksoB("Liste der Angemeldeten", "Listo de alig^intoj", "listo", $listo, "N", "N",
			 "Ich will <em>nicht</em> in der <a href='listo'>Liste der Angemeldeten</a>".
			 " im Netz erscheinen.",
			 "Mi <em>ne</em> volas aperi en la reta <a href='listo'>listo de alig^intoj</a>.");


    geoecho("<tr><td ><b>Rimarkoj:</b></td><td >", "Ich habe folgende Bemerkungen:<br/>", 'Mi havas la jenajn rimarkojn:<br/>
      <textarea name="rimarkoj" cols="57" rows="5">');
  echo( $rimarkoj ."</textarea></td></tr>\n");
    entajpboksoB("Bedingungen", "kondic^oj:","konsento",$konsento[0],"J","JES","Ich habe die <a href=\"kondicxoj\" target=\"nova\">Bedingungen</a> gelesen und stimme ihnen zu. ", "Mi legis kaj konsentas kun la <a href=\"kondicxoj\" target=\"nova\">jenaj kondic^oj</a>.<br/>","J");

geoecho('<tr><td /><td ><strong>',
		"Insbesondere</strong> bin ich einverstanden, dass die DEJ meine Daten verarbeitet".
		" und am Ende des Treffens eine Teilnehmerliste mit allen Namen und Adressen".
		" ausgeteilt wird. / <strong>",
		"Precipe</strong> mi konsentas, ke la GEJ tralaboras miajn datojn kaj je la fino".
		" de la renkontig^o disdonas liston de c^iuj nomoj kaj adresoj.</td></tr>");

   geoecho('<tr><td /><td ><strong>', "F&uuml;r Leute aus A- und B-L&auml;ndern / ", "Por A- kaj B-Landanoj:</strong> ");

    geoecho ("", "Insbesondere bin ich mir bewusst, dass meine Anmeldung nur nach Ankunft".
               " der Anzahlung bei DEJ g&uuml;ltig wird. / ", "Precipe mi konscias, ke" .
               " mia alig^o validas nur ekde la alveno de mia antau^pago c^e GEJ.</td></tr>");
    
    if ($parto=="korektigi" and $konsento[0]!="J") 
	{
      echo "<tr><td /><td >";
      erareldono_geo("Wenn du nicht einverstanden bist, kannst du sich nicht anmelden.",
                     "Se vi ne konsentas, vi ne povas alig^i!");
	  echo "</tr>\n";
    }
    ?>
</table><br/><br/>

		  <p>
		  <input name="Sendu" value="Jes, mi ali&#285;as! " size="18" type="submit" /> <input name="Reset"
			value="Ne, mi ne volas. " type="reset" /><br/>
		  <br/>
Se vi entajpis retan adreson vi tuj ricevos la unuan konfirmilon,
 la dua venos je la kutima tempo en novembro.
		</p>
</form>
<?php

granda_kesto_fino();

?>
