<?php
/* #################################################################### */
/* Cxi tie okazas la aligxado de PARTOPRENANTOJ, k.e. nomo, adreso ktp. */
/* #################################################################### */
require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

if (!rajtas("aligi"))
{
  ne_rajtas();
}
          // TODO: bitte anders machen
if ($sp == "forgesu")
{
	unset($_SESSION["partoprenanto"]);
	unset($_SESSION['partopreno']);

//  session_unregister("ago");
//  unset($ago);
  unset($_SESSION["ago"]);

  // TODO: Cxu intenco, ke sekvontapagxo != parto ?
  session_unregister("sekvontapagxo");
  unset($parto);
}

if ( ($_GET["ago"] != "sxangxi")
      and (!$parto)
     )
{
  $_SESSION["partoprenanto"] = new Partoprenanto();
}
 else if ( ($_GET["ago"] == "sxangxi"))
     {
         if ($_GET['sp'])
             {
                 $_SESSION["sekvontapagxo"]=$_GET['sp'];
             }
         $_SESSION["ago"] = "sxangxi";
}

HtmlKapo();

echo "<div align='center'>\n";

eoecho ("<p>Bonvole uzu nur anglajn literojn, kun malantau^a c^apelo post specialaj literoj \n");
echo "(ekz. C^,c^, U^, u^).</p>\n";


if ($parto == "korektigi")
{
    echo "<p>";
    erareldono ("Hmm, io malg^usta okazis.");
    echo "</p>";
}

  echo "</div>\n";
  echo "<form action='aligxatkontrolo.php' method='POST'>\n";

?>

  <table valign='center' align='center' width='100%'>
  <tr>
  <td width=10%></td>
  <TD width='*' align='left'>
  <p>
    <?php
    if ($_SESSION["partoprenanto"]->datoj[ID])
    {
      echo "<p>Vi redaktas la Partoprenanton numero: ".$_SESSION["partoprenanto"]->datoj[ID]." </p>\n";
    }


    entajpejo ("Persona nomo:",personanomo,$_SESSION["partoprenanto"]->datoj[personanomo],30,"personan nomon");
    entajpejo ("S^ildnomo (se alia):",sxildnomo,$_SESSION["partoprenanto"]->datoj[sxildnomo],30,"");
    entajpbutono ("Mia sekso estas:",sekso,$_SESSION["partoprenanto"]->datoj['sekso'][0],i,ina,ina);
    entajpbutono ("",sekso,$_SESSION["partoprenanto"]->datoj['sekso'][0],v,vira,vira);


    echo "<BR>\n";

    if ( $parto == "korektigi"
         and $_SESSION["partoprenanto"]->datoj[sekso]!="ina"
         and $_SESSION["partoprenanto"]->datoj[sekso]!="vira"
         )
    {
      erareldono ("Bonvole indiku vian sekson");
    }

echo "<hr/>";

    entajpejo ("Familia nomo:",nomo,$_SESSION["partoprenanto"]->datoj[nomo],30,"nomon");



    entajpejo ("Naskig^dato:",naskigxdato,$_SESSION["partoprenanto"]->datoj[naskigxdato],12,"","1900-01-01","(jaro-monato-tago)");
    if ( ($_SESSION["partoprenanto"]->datoj[naskigxdato] != "")
         and ( !kontrolu_daton($_SESSION["partoprenanto"]->datoj[naskigxdato]) )
         )
    {
      erareldono ("La daton vi entajpis ne ekzistas au^ estis malg^uste");
    }
    echo "<hr/>\n";
    entajpejo ("Adresaldonaj^o:",adresaldonajxo,$_SESSION["partoprenanto"]->datoj[adresaldonajxo],30,"");
    entajpejo ("Strato:",strato,$_SESSION["partoprenanto"]->datoj[strato],35,"straton");

    entajpejo ("Pos^tkodo:",posxtkodo,$_SESSION["partoprenanto"]->datoj[posxtkodo],13,"");
    entajpejo ("Provinco:",provinco,$_SESSION["partoprenanto"]->datoj[provinco],20,"");
    entajpejo ("Urbo:",urbo,$_SESSION["partoprenanto"]->datoj[urbo],20,"urbon");
    

    eoecho ("Log^lando: \n");

montru_landoelektilon(5, $_SESSION["partoprenanto"]->datoj["lando"]);

    entajpejo ("S^ildlando (se alia):",sxildlando,$_SESSION["partoprenanto"]->datoj[sxildlando],30,"","","");
    echo "<hr/>";
    
    entajpejo ("Telefono:",telefono,$_SESSION["partoprenanto"]->datoj[telefono],30,"","","(internacie)");
    entajpejo ("Telefakso:",telefakso,$_SESSION["partoprenanto"]->datoj[telefakso],30,"","","(internacie)");
    entajpejo ("Retpos^to:",retposxto,$_SESSION["partoprenanto"]->datoj[retposxto],40);

echo "<hr/>\n";

entajpejo("UEA-kodo:", "ueakodo",
          $_SESSION['partoprenanto']->datoj['ueakodo'], 6);

echo "<hr/>\n";


entajpbutono ("sendu informmesag^ojn:",'retposxta_varbado',
              $_SESSION["partoprenanto"]->datoj['retposxta_varbado'],
              'j','j','en x-kodo', 'defauxlto');
entajpbutono ("",'retposxta_varbado',
              $_SESSION["partoprenanto"]->datoj['retposxta_varbado'],
              'u','u','en unikodo');
entajpbutono ("",'retposxta_varbado',
              $_SESSION["partoprenanto"]->datoj['retposxta_varbado'],
              'n','n','tute ne');


echo "<hr/>\n";


    entajpbokso("","nekontrolu",$nekontrolu,"JES","JES","Se vi maldeziras datkontroladon pro problemojn, marku c^i tie.");
    echo "<br/>\n";
    echo "</td><td width=10%></td></tr></table>\n";
    echo "<div align=center>\n";

    if ($_SESSION["ago"] == "sxangxi")
    {
		if (strpos($_SESSION['sekvontapagxo'],
					  '?') === false)
		{
			$aldono = '?';
		}
		else
		{
			$aldono = '&';
		}
      ligu($_SESSION["sekvontapagxo"] . $aldono . "partoprenantoidento=" 
		   . $_SESSION["partoprenanto"]->datoj[ID],
		   "ne s^ang^u kaj reen&nbsp;");
      send_butono("S^ang^u!");  //sqlago=forgesu&
    }
    else
    {
      send_butono("Aligu!");
    }

    echo "</div>\n";

echo "</form>\n";

HtmlFino();

?>
