<?php

/*
 * Ebligas sercxi "similajn" partoprenantojn kaj transfero de cxiuj
 * gxisnunaj partoprenoj kaj notoj de unu partoprenanto al alia.
 *
 */


if ($sendu == "detaloj")
{
  $_REQUEST['partoprenantoidento'] = $_REQUEST['fonto'];
  require("partrezultoj.php");
  return;
}


require_once ("iloj/iloj.php");
session_start();
malfermu_datumaro();


kontrolu_rajton("vidi");


HtmlKapo();


if($sendu == "vidu")
{
    $_SESSION["partoprenanto"] = new Partoprenanto($fonto);
    unset($_SESSION["partopreno"]);
}

if ($sendu == "transferuAl" || $sendu == "transferuDe" ||
	$sendu == "transferuDeMenuo" || $sendu == "transferuAlMenuo")
{
  kontrolu_rajton("estingi");

  switch ($sendu)
	{
	case "transferuAl":
	  $de = $_SESSION["partoprenanto"];
	  $al = new Partoprenanto($fonto);
	  break;
	case "transferuDe":
	  $al = $_SESSION["partoprenanto"];
	  $de = new Partoprenanto($fonto);
	  break;
	case "transferuDeMenuo":
	  $al = $_SESSION["partoprenanto"];
	  $de = new Partoprenanto($kune);
	  break;
	case "transferuAlMenuo":
	  $de = $_SESSION["partoprenanto"];
	  $al = new Partoprenanto($kune);
	  break;
	default:
	  darf_nicht_sein();
	}

  $sql = datumbazdemando(array("ID", "renkontigxoID"),
						 "partoprenoj",
						 "partoprenantoID = '" . $de->datoj["ID"] ."'"
						 );
  $result = sql_faru($sql);
  $pprenoj = array();
  
  eoecho("<h1>Transfero de partoprenoj</h1>");

  eoecho("<p>Vi nun transferos la subajn partoprenojn kaj notojn:");
  
  echo "<table valign=top border=2>\n";
  echo "<TR><TD valign=top>\n";
  eoecho(" <em>De:</em><br/>");
  $de->montru_aligxinto(true);


  eoecho ("</td><td> <em>Al:</em><br/>");
  $al->montru_aligxinto(true);
  echo "</td></tr>\n";
  
  echo "<tr><td colspan='2'>\n";

  eoecho("Partoprenoj:");
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    echo "<br>";
    ligu("partrezultoj.php?partoprenidento=".$row["ID"],"#". $row["ID"] . " - ". eltrovu_renkontigxo($row["renkontigxoID"]));
	$pprenoj[] = $row["ID"];
  }
  
  eoecho("<p>Notoj:</p>");
  
  $sql = datumbazdemando(array("ID", "prilaborata", "dato", "partoprenantoID",
							   "subjekto","kiu", "kunKiu","tipo"),
						 "notoj",
						 array("partoprenantoID = '{$de->datoj['ID']}'"));
  
  sercxu($sql, 
		array("dato","desc"), 
		array(array('0','','->','z','"notoj.php?wahlNotiz=XXXXX"','-1'), 
			  array('prilaborata','prilaborata?','XXXXX','z','','-1'), 
			  array('dato','dato','XXXXX','l','','-1'), 
			  array('subjekto','subjekto','XXXXX','l','','-1'), 
			  array("kiu","kiu",'XXXXX','l','','-1'), 
			  array("kunKiu","kun Kiu?",'XXXXX','l','','-1'), 
			  array("tipo","tipo",'XXXXX','l','','-1')
			  ), 
		array(array('', array('&sum; XX','A','z'))),
		"notoj-transfero",
		array('Zeichenersetzung'=>
			  array('1'=>array('j'=>'<strong class="malaverto">prilaborata</strong>',
							   '' =>'<strong class="averto">neprilaborata</strong>',
							   'n'=>'<strong class="averto">neprilaborata</strong>')
					),
			  ),
		0,'','','ne');
 
  $sql = datumbazdemando(array("ID", "nomo", "retposxtadreso"),
						 "entajpantoj",
						 "partoprenanto_id = '{$de->datoj['ID']}'");
  $rez = sql_faru($sql);
  if (mysql_num_rows($rez) > 0)
	{
	  eoecho ("<p>Entajpantoj:</p>");
	  eoecho ("<table>\n<tr><th>ID</th><th>Nomo</th><th>Retpos^tadreso</th></tr>\n");
	  while($linio = mysql_fetch_assoc($rez))
		{
		  eoecho("<tr><td>{$linio['ID']}</td><td>{$linio['nomo']}</td><td>{$linio['retposxtadreso']}</td></tr>\n");
		}
	  echo ("</table>");
	}

  echo "</td></tr>";
  echo "</table>\n";


  echo "<form action='transferi.php' method='GET'>\n";
  tenukasxe("de", $de->datoj["ID"]);
  tenukasxe("al", $al->datoj["ID"]);

  eoecho ("<p><input type='checkbox' name='forigu' value='jes' />Tuj forigu la maldekstran personon!</p>");

  send_butono("faru");
  echo "</form>";
  
  HtmlFino();
	
  exit();
}
if ($sendu == "faru" && $de && $al)
{
  if ($de == $al)
	{
	  eoecho ("Vi ne povas s^ovi de iu persono al la sama persono!");
	  HtmlFino();
	  exit();
	}

  
  sxangxu_datumbazon("partoprenoj",
					 array("partoprenantoID" => $al),
					 array("partoprenantoID" => $de));
  sxangxu_datumbazon("notoj",
					 array("partoprenantoID" => $al),
					 array("partoprenantoID" => $de));
  sxangxu_datumbazon("entajpantoj",
					 array("partoprenanto_id" => $al),
					 array("partoprenanto_id" => $de));

  if ($forigu == "jes")
	{
	  forigu_el_datumbazo("partoprenantoj", $de);
	  eoecho("<p>Vi s^ovis c^iujn partoprenojn de {$de} al ");
	  ligu("partrezultoj.php?partoprenantoidento={$al}", "#" . $al);
	  eoecho(", kaj tuj forigis {$de}.</p>");
	}
  else
	{
	  eoecho("<p>Vi s^ovis c^iujn partoprenojn de ");
	  ligu("partrezultoj.php?partoprenantoidento={$de}", "#" . $de);
	  eoecho (" al ");
	  ligu("partrezultoj.php?partoprenantoidento={$al}", "#" . $al);
	  eoecho(".</p>");
	}

  HtmlFino();
  exit();
}



//eoecho ("<h1>Atentu!</h1><p> C^i tiu pag^o estas ankorau^ provizora, bonvolu ne uzi, se vi ne estas Pau^lo.</p><hr/>");

{
  $p_anto = $_SESSION["partoprenanto"];
  $priskribo =  $p_anto->datoj["personanomo"] . " " . $p_anto->datoj["nomo"] .
	" (#" . $p_anto->datoj["ID"] . ")";
  eoecho ("<p>Jen c^iuj (iamaj) partoprenantoj, kiuj similas al ");
  ligu("partrezultoj.php?partoprenantoidento=".$p_anto->datoj["ID"], $priskribo );
  echo ":\n</p>\n";

  if ($p_anto->datoj["retposxto"])
	{
	  $retkomparo = "(pp.retposxto = '" . $p_anto->datoj["retposxto"] . "')";
	}
  else
	{
	  // ne sencas sercxi cxiujn homojn, kiuj ankaux ne donis retadreson.
	  $retkomparo = "0";
	}



  $sql = datumbazdemando(array("pp.ID", "pp.nomo", "personanomo",
							   "max(renkontigxoID) as renkNumero" ),
						 array("partoprenantoj" => "pp",
							   "partoprenoj" => "pn" ),
						 array("pn.partoprenantoID = pp.ID",
							   "(pp.nomo = '" . $p_anto->datoj["nomo"] . "') or " .
							   "(pp.personanomo = '" . $p_anto->datoj["personanomo"] ."') or ".
							   "(pp.naskigxdato = '". $p_anto->datoj["naskigxdato"] . "') or ".
							   $retkomparo
							   ),
						 "",
						 array("group" => "pp.ID",
							   "order" => "personanomo, nomo")
						 );

  // la nomo estas "peter", por ke la jxavoskripto povas uzi gxin.
  echo '<form name="peter" action="transferi.php" method="GET">';

  echo '<table><tr><td>';
  partoprenanto_elektilo($sql, 10, "fonto", "kun_identifikilo");
  echo '</td><td>';
  eoecho('<button name="sendu" value="vidu" type="submit" >Vidu</button> la ' .
		 'partoprenantojn, kiuj similas al la elektita persono!' . "\n<br />\n");
  eoecho('<button name="sendu" value="detaloj" type="submit" >Rigardu</button> la ' .
		 'detalojn de la elektita persono!' . "\n<br/>\n");
  echo '</td></tr></table>';

  echo '<input type="hidden" name="celo" value="' . $p_anto->datoj["ID"] . '" />';

  echo "<p>";
  if (rajtas('estingi'))
	{
	  eoecho('<button name="sendu" value="transferuDe" type="submit" >Transferu</button>'.
			 ' c^iujn partopren-datojn de la elektita persono <strong>al ' .
			 $priskribo . "</strong>! \n<br />\n");
	  eoecho('<button name="sendu" value="transferuAl" type="submit" >Transferu</button>'.
			 ' c^iujn partoprenojn-datojn de ' .
			 $priskribo . " <strong>al la elektita persono</strong>!\n</p>\n");

	  eoecho("<p> Se la persono ne trovig^as tiel, vi povas elekti lin el la maldekstra menuo.<br/>\n");
	  // cxi tien la Jxavoskripto metas la valoron el la maldekstra menuo
	  tenukasxe("kune", "0");

	  // kiam oni alklakas iun de la du butonon, la jxavoskripto en "cxiujpagxoj.js"
	  // estas vokita por enmeti la valoron el la menuo.

	  eoecho('<button name="sendu" value="transferuDeMenuo" type="submit"' .
			 ' onclick="reindamit()">Transferu</button> '.
			 "de la persono elektita en la listo sub la maldekstra menuo <strong>al " . $priskribo . "</strong>!<br/>\n");
	  eoecho('<button name="sendu" value="transferuAlMenuo" type="submit"' .
			 ' onclick="reindamit()">Transferu</button> '.
			 "de " . $priskribo . " <strong>al la persono elektita en la".
			 " listo sub la maldekstra menuo</strong>!</p>\n");
	}
  else
	{
	  eoecho ("<p> Vi ne povas s^ang^i ion ajn, c^ar vi ne havas la necesajn rajtojn. " .
			  "Se vi pensas, ke vi havu, plendu c^e Pau^lo.</p>\n");
	}
  

  echo "\n</form>\n";
}

HtmlFino();

 


?>