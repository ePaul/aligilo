<?php

require_once ('iloj/iloj.php');
session_start();
HtmlKapo("maldekstren");

malfermu_datumaro();
if (rajtas('vidi'))
{
   // rapida salto al la detaloj-pagxo laux PP-ID

    // TODO!: en surloka moduso saltu tuj al la akceptado-datoj,
    //       se la ulo ankoraux ne akceptigxis - aux almenaux
    //       havigu apartan akceptu-butonon.
?>
<form method="post" id="entajpu" name="entajpu"
      action="route.php" target="anzeige"
		style="float:left; text-align: center; display: block; margin: 2pt; border: outset thin; padding:1pt;">
	<p style='margin: 1pt; font-size: small;'>partopreno-ID:</p>
	<p style='margin: 1pt;'>
        <?php
        tenukasxe('elekto', 'Montru!');
        simpla_entajpejo("", 'partoprenidento', '', 5);
        ?>
	</p>
</form>
<?php
}
  // TODO?: später kürzer, via session;

  echo "<div style='text-align:right;margin:3pt;'>";
  eoecho ("Saluton, kara " . $_SESSION["kkren"]["entajpantonomo"] . ".\n");
  echo "<BR>\n";
  eoecho ("Kion vi deziras fari?\n");
  echo "<BR>\n";

  echo "<P class='granda' style='clear:left;'>\n";
  rajtligu("partoprenanto.php?sp=forgesu","Aligi partoprenantojn","anzeige","aligi");
  echo "<BR>\n";
  rajtligu("partsercxo.php","Serc^i partoprenantojn","anzeige","vidi");
  echo "<BR>\n";
  ligu("kotizo.php","Antau^kalkuli kotizon","anzeige");
  echo "<BR>\n";
  rajtligu("cxambroj.php?cx_ago=forgesu","Disdoni c^ambrojn","anzeige","cxambrumi");
  echo "<BR>\n";
 /* rajtligu("ekzporti.php","ekzporti datumojn","anzeige","ekzporti");
  echo "<BR>\n";*/
  rajtligu("statistikoj.php","vidi statistikojn","anzeige","statistikumi");
  echo "<BR>\n";
//  rajtligu("demandoj.php","pliaj statistikojn","anzeige","vidi");
  rajtligu("administrado.php","grava administrado","anzeige","administri");
  echo "<BR>\n";
  ligu("menuo.php","revoku la menuon","is-aligilo-menuo","jes");
  echo "<BR>\n";

  rajtligu("index.php","PHPMyAdmin","top","teknikumi", "ne");
  echo "<BR>\n";
  rajtligu("probieren/", "Elprovejo", "", "teknikumi", "ne");
  echo "<BR>\n";
  ligu("fino.php","au^ forlasi c^i tie","_top","jes");

  echo "</P>\n";

  if (rajtas(vidi))
  {

	// Kial cxiam kalkuli la rezultojn, se oni nur
	// kelkfoje montras ilin? -> mi sxovis la kalkuladon
	// en la interon de la "if".

	if (isset($sercxfrazo))
	  {
		$sql = stripslashes($sercxfrazo) . " order by personanomo, nomo"; 
	  }
	else
	  {
		//		$rezulto = sql_faru("select pp.ID,Malnova,nomo,personanomo,max(renkontigxoID) from partoprenantoj as pp,partoprenoj as pn where pn.partoprenantoID = pp.ID group by pp.ID order by personanomo,nomo"); 
		$sql = datumbazdemando(array("pp.ID", "pp.nomo", "personanomo",
									 "max(renkontigxoID)" => "renkNumero" ),
							   array("partoprenantoj" => "pp",
									 "partoprenoj" => "pn" ),
							   "pn.partoprenantoID = pp.ID",
							   "",
							   array("group" => "pp.ID",
									 "order" => "personanomo, nomo")
							   );

		//sql_faru("select ID,renkontigxoID,partoprenantoID from partoprenoj where partoprenantoID='".$row[ID]."' order by renkontigxoID desc");
	  }
		?>
		<hr id="elektilo-anker" />
		   <form method="post" id="elektu" name="elektu" action="route.php" target="anzeige">
		<?php
             if (isset($sercxfrazo))
			 {
                 eoecho( "\n  <em>(limigita elekto: " .$_GET['listotitolo'] . " )</em><br/>\n");
			 }
          partoprenanto_elektilo($sql,menuoalteco);  ?>
	  <br /><input type="submit" name="elekto" value="Montru!"></input>
		 <input type="submit" name="elekto" value="novan noton"></input>
		 <input type="submit" name="elekto" value="notojn"></input>
		 </form>
		 <?php
		   }



if ($_SESSION["kkren"]["partoprenanto_id"]!=0)
  {
    ligu('sercxrezultoj.php?elekto=notojn&partoprenantoidento=' .
		 $_SESSION["kkren"]["partoprenanto_id"] ,"viaj notoj",'anzeige');
  }

  echo "<BR><hr>\n";
  echo "<P class=\"mezen\">\n";
  echo "<I style=\"font-size:200%;\">IS - Aligilo</I> <BR>\n";
  echo "<img src=\"bildoj/eoei-kl.gif\" alt=\"eo-bildo\" width=88 height=50 align=\"center\" border=0>\n";
  eoecho ("<BR>\n ".$_SESSION["renkontigxo"]->datoj[nomo]." en ".$_SESSION["renkontigxo"]->datoj[loko]."\n");
  echo "</P>\n";

  echo "<hr><BR>\n";

  eoecho ("Informoj, rimarkoj, insultoj <BR>(au^ se io simple ne funkcias kiel dezirata):\n");
  ligu ("mailto:".teknika_administranto_retadreso,"informu min!","","ne");
  echo "</DIV>";
  echo "<HR><BR>";
  //echo  "<font size=60%>Farita de: Martin B. Sawitzki, Paul Ebermann</font>";
  HtmlFino();

?>
