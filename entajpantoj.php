<?php

/*
 * Administrado de la entajpantoj.
 *
 */

//define("DEBUG", TRUE);
require_once ("iloj/iloj.php");
session_start();

malfermu_datumaro();

kontrolu_rajton("teknikumi");

HtmlKapo();

{

    $tmplisto = array(array($x = "aligi",       $x,              "al&shy;igi"),
                      array($x = "vidi",        $x,              "vi&shy;di"),
                      array(     "sxangxi",$x = "s^ang^i",     "s^an&shy;g^i"),
                      array(     "cxambrumi",   "c^ambrumi",     "c^ambr."),
                      array(     "ekzporti",    "eksporti",      "eksp."),
                      array($x = "statistikumi", $x,             "stat."),
                      array(     "mono",        "entajpi monon", "mo&shy;no"),
                      array($x = "estingi",      $x,             "est."),
                      array($x = "retumi",       $x,             "ret."),
                      array($x = "rabati",       $x,             "rab."),
                      array($x = "inviti",       $x,             "inv."),
                      array($x = "administri",   $x,            "ad&shy;min."),
                      array($x = "akcepti",      $x,             "akc."),
                      array($x = "teknikumi",    $x,             "tekn."));
    //    echo "<!--";
    //    var_export($tmplisto);
    //    echo "-->";
  foreach($tmplisto AS $ero)
	{
        $rajtolisto[]= array("rajto" => $ero[0],
                             "alias" => $ero[1],
                             "mallongigo" => $ero[2]);
	}
  unset($tmplisto);
}



// echo "<!--\n";
// var_export($rajtolisto);
// echo "-->\n";

if($forigu)
{
  if($vere)
	{
	  forigu_el_datumbazo("entajpantoj", $forigu);
	  eoecho("<p>Vi j^us forigis la entajpanton #".$forigu.".</p>");
	}
  else
	{
	  eoecho("<h1>Forigo de entajpanto</h1>\n");
	  $sql = datumbazdemando('*',
							 'entajpantoj',
							 "ID = '$forigu'");
	  $rez = sql_faru($sql);
	  $linio = mysql_fetch_assoc($rez);
	  
	  echo "<table>\n";
	  eoecho("<tr><th>ID</th><td>{$linio['ID']}</td></tr>\n");
	  eoecho("<tr><th>Salutnomo</th><td>{$linio['nomo']}</td></tr>\n");
	  eoecho("<tr><th>Retadreso</th><td>{$linio['retposxtadreso']}</td>\n");
	  eoecho("<tr><th>Partoprenanto-ID</th><td>{$linio['partoprenanto_id']}</td>\n");
	  eoecho("<tr><th>Sendantonomo</th><td>{$linio['sendanto_nomo']}</td>\n");
	  foreach($rajtolisto AS  $ero)
		{
		  eoecho("<tr><th>" . $ero['alias']. "</th><td>" .
                 ($linio[$ero['rajto']] == 'J' ? "[X]" : "[_]")
				 ."</td>\n");
		}
	  eoecho("</table>\n");
	  eoecho("<p>C^u vi vere volas forigi tiun c^i entajpanton?");
	  ligu("entajpantoj.php?forigu=$forigu&vere=jes", "Jes");
	  ligu("entajpantoj.php?redaktu=$forigu", "Ne");
	  HtmlFino();
	  return;
	}
}

if ($sendu)
{

//   echo "<pre>";
//   var_export($_POST);
//   echo "</pre>";

  $sxangxlisto = array();
  foreach(array("nomo", "retposxtadreso", "partoprenanto_id", 'sendanto_nomo') AS $tipo)
	{
	  if ($_POST[$tipo])
		{
		  $sxangxlisto[$tipo] = $_POST[$tipo];
		}
	}
  foreach($rajtolisto AS $ero)
	{
	  if($_POST[$ero['rajto']])
		{
		  $sxangxlisto[$ero['rajto']] = $_POST[$ero['rajto']]{0};
		}
	}
  if ($_POST['pasvortsxangxo']=='jes')
	{
	  if ($_POST['kodvorto'])
		{
		  $sxangxlisto['kodvorto'] = $_POST['kodvorto'];
		}
	  else
		{
		  erareldono("Vi petis pri s^ang^o de pasvorto, sed ne donis novan!");
		}
	}

  if($_POST['ID'] == 'nova')
	{
	  aldonu_al_datumbazo("entajpantoj", $sxangxlisto);
	  $num = mysql_insert_id();
	  eoecho ("<p>Mi aldonis linion #" . $num . " al la tabelo.</p>");
	  if ($redaktu == 'nova')
		$redaktu = $num;
	}
  else
	{
	  sxangxu_datumbazon("entajpantoj",
						 $sxangxlisto,
						 array("ID" => $_POST['ID']));
	  eoecho ("<p>Mi s^ang^is linion #" . $_POST['ID'] . " en la tabelo.</p>");
	}
}


if($redaktu)
{

  eoecho("<h1>Redakto de entajpanto</h1>");
  echo "<form method='POST' action='entajpantoj.php'>\n";

  if ($redaktu == 'nova')
	{
	  $linio = array("ID" => 'nova');
	  eoecho("<p> Ni kreas novan entajpanton\n");
	}
  else
	{
	  $sql = datumbazdemando('*',
							 'entajpantoj',
							 "ID = '$redaktu'");
	  
	  $rez = sql_faru($sql);
	  $linio = mysql_fetch_assoc($rez);
	  
	  eoecho("<p>ID: {$linio['ID']}\n" );
	}
  tenukasxe("ID", $linio['ID']);
  echo("<br/>\n");
  
  entajpejo("Salutnomo:", "nomo", $linio['nomo'], 20);
  entajpejo("Retpos^ta adreso:", "retposxtadreso", $linio['retposxtadreso'], 20);
  entajpejo("Retpos^tsenda nomo:", "sendanto_nomo", $linio['sendanto_nomo'], 30);

  entajpbokso("", "pasvortsxangxo", "", "jes", "jes");
  entajpejo("Nova pasvorto: ", "kodvorto", "", 20, "","","","j");

	//  entajpboksokajejo("pasvortsxangxo", "", "jes", "jes",
	//					  "Nova pasvorto: ", '', 'kodvorto', '', 20, 'Mankas pasvorto.');

  //  echo("<br/>\n");
  entajpejo("Partoprenanto-ID: ", "partoprenanto_id", $linio['partoprenanto_id'], 6);
  
  eoecho ("</p>\n<p>Li/s^i havu la rajton ...");
  foreach($rajtolisto AS $ero)
	{
	  echo "<br/>\n";
	  entajpbokso("", $ero['rajto'], $linio[$ero['rajto']],
                  'J', 'J',  $ero['alias']);
	}
  echo "<br/>\n";
  eoecho (" ... en la datumbazo</p>");

  entajpbokso("<p>", "redaktu", "", "jes", $linio['ID'],
			  "Pluredaktu tiun c^i entajpanton.", "", "sen kasxa");
  echo "<br/>\n";
  send_butono("S^ang^u");
  ligu("entajpantoj.php", "Reen");
  if($redaktu != "nova")
	ligu("entajpantoj.php?forigu=$redaktu", "Forigu tiun c^i entajpanton!");
  echo "</p>";
  echo "</form>\n";

  HtmlFino();
  return;
}



// montru tabelon de cxiuj entajpantoj


$sql = datumbazdemando(array_merge(array("ID", "nomo", "retposxtadreso",
                                         "partoprenanto_id", 'sendanto_nomo'),
								   array_map("reset", $rajtolisto)),
					   "entajpantoj");

$kruco = array('J' => "<strong>X</strong>",
				'N' => " _ ");

$anstatauxoj = array_fill(4, count($rajtolisto), &$kruco);

$kolumnoj = array(/* kolumnoj */
			 array('ID', '', 'red.','z', 'entajpantoj.php?redaktu=XXXXX',
				   'partoprenanto_id'),
			 array('nomo', 'nomo', 'XXXXX', 'l','',''),
			 array('retposxtadreso', 'ret&shy;pos^to','@','z','mailto:XXXXX', -1),
             array('sendanto_nomo','Plena nomo', 'XXXXX', 'l', '', ''),
			 array('partoprenanto_id', 'p-anto', 'XXXXX', 'r',
				   'partrezultoj.php?partoprenantoidento=XXXXX',
                   'partoprenanto_id'));

foreach($rajtolisto AS $ero) {
    $kolumnoj[]= array($ero['rajto'], $ero['mallongigo'],
                       "XXXXX", 'z', '', '');
}



sercxu($sql,
	   array("nomo", "asc"),
       $kolumnoj,
	   array(/*sumoj*/),
	   "entajpantoj",
	   array(/* pliaj parametroj */
			 "Zeichenersetzung" => $anstatauxoj),
	   0 /* formato de la tabelo */,
	   "Jen listo de c^iuj entajpantoj.", 0, "ne");

ligu("entajpantoj.php?redaktu=nova", "Kreu novan entajpanton");


HtmlFino();


?>