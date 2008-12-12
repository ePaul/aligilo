<?php

/** kelkaj iloj por trovi, kunigi, ktp.
 * kunlogxantojn.
 */

/*
  CREATE TABLE `kunlogxdeziroj` (
  `ID` INT NOT NULL AUTO_INCREMENT ,
  `partoprenoID` INT NOT NULL ,
  `kunKiuID` INT NOT NULL ,
  `stato` CHAR( 1 ) NOT NULL ,
  PRIMARY KEY ( `ID` ) 
  ) TYPE = MYISAM COMMENT = 'deziroj de kunlogxado kaj ties statoj';
*/



/**
 * Montras por unu deziro A->B la detalojn
 * de A kaj B, kaj la statuson.
 */
function montru_kunlogxdezirdetalojn($deziro)
{
  $dezirant_eno = new Partopreno($deziro->datoj['partoprenoID']);
  $deziranto = new Partoprenanto($dezirant_eno->datoj['partoprenantoID']);

  $dezirat_eno = new Partopreno($deziro->datoj['kunKiuID']);
  $dezirato = new Partoprenanto($dezirat_eno->datoj['partoprenantoID']);

  eoecho("<h2>Kunlog^deziraj detaloj</h2>");

  eoecho ("<table>\n".
		  "<tr><th /><th>deziranto</th><th>dezirato</th></tr>\n");

  kampoj("ID",
		 array(donu_ligon("partrezultoj.php?partoprenidento=".$dezirant_eno->datoj['ID'],
						  $dezirant_eno->datoj['ID']),
			   donu_ligon("partrezultoj.php?partoprenidento=".$dezirat_eno->datoj['ID'],
						  $dezirat_eno->datoj['ID'])));
  kampoj("nomo",
		 array($deziranto->tuta_nomo(),
			   $dezirato->tuta_nomo()));
  kampoj("domotipo",
		 array($dezirant_eno->domotipo(),
			   $dezirat_eno->domotipo()));
  kampoj("ag^o",
		 array($dezirant_eno->datoj['agxo'],
			   $dezirat_eno->datoj['agxo']));

  // testas, cxu seksoj kaj deziritaj cxambrotipoj harmonias.

  if ($deziranto->seksa != $dezirato->seksa and
	  $dezirant_eno->datoj['cxambrotipo'] == 'u' and
	  $dezirat_eno->datoj['cxambrotipo'] == 'u')
	{
	  kampoj("sekso",
			 array($deziranto->seksa => "averto",
				   $dezirato->seksa => "averto"));
	  kampoj("c^ambrotipo:",
			 array($dezirant_eno->cxambrotipo() => "averto",
				   $dezirat_eno->cxambrotipo() => "averto"));
	}
  else if ($deziranto->seksa != $dezirato->seksa and
	  $dezirant_eno->datoj['cxambrotipo'] == 'u')
	{
	  kampoj("sekso",
			 array($deziranto->seksa,
				   $dezirato->seksa => "averto"));
	  kampoj("c^ambrotipo:",
			 array($dezirant_eno->cxambrotipo() => "averto",
				   $dezirat_eno->cxambrotipo()));
	}
  else if ($deziranto->seksa != $dezirato->seksa and
		   $dezirat_eno->datoj['cxambrotipo'] == 'u')
	{
	  kampoj("sekso",
			 array($deziranto->seksa => "averto",
				   $dezirato->seksa));
	  kampoj("c^ambrotipo:",
			 array($dezirant_eno->cxambrotipo(),
				   $dezirat_eno->cxambrotipo() => "averto"));
	}
  else
	{
	  kampoj("sekso",
			 array($deziranto->seksa,
				   $dezirato->seksa));
	  kampoj("c^ambrotipo:",
			 array($dezirant_eno->cxambrotipo(),
				   $dezirat_eno->cxambrotipo()));
	}

  // se unu volas dulitan cxambron, tiam la alia ankaux volu.
  // TODO: unulita cxambro ne eblas

  if($dezirant_eno->datoj['dulita'] != $dezirat_eno->datoj['dulita'])
	{
	  kampoj("dulita",
			 array(jes_ne($dezirant_eno->datoj['dulita']) => "averto",
				   jes_ne($dezirat_eno->datoj['dulita']) => "averto"));
	}
  else
	{
	  kampoj("dulita c^.?",
			 array(jes_ne($dezirant_eno->datoj['dulita']),
				   jes_ne($dezirat_eno->datoj['dulita'])));
	}
  eoecho ("</table>\n");

  $sql = datumbazdemando(array("ID"),
						 "kunlogxdeziroj",
						 array("partoprenoID = '".$deziro->datoj['kunKiuID']."'",
							   "kunKiuID = '".$deziro->datoj['partoprenoID']."'"));
  $rez = sql_faru($sql);
  if ($linio = mysql_fetch_assoc($rez)) // intence = kaj ne ==.
	{
	  $alia_deziro = new Kunlogxdeziro($linio['ID']);
	  eoecho("<p>Estas ankau^ ");
	  ligu("kunlogxado.php?kunlogxID=".$linio['ID'],
		   "kunlog^deziro en la inversa direkto");
	  eoecho(", kies stato nun estas <em>" . $alia_deziro->stato() . "</em>");
	}
  else
	{
	  eoecho ("<p>Sed ".$dezirato->tuta_nomo()." ne indikis, ke " .
			  $dezirato->personapronomo . " volas log^i kun " .
			  $deziranto->tuta_nomo() .".</p>\n");
	}

  eoecho ("<p>Aktuala stato de la kunlog^deziro: <em>" . $deziro->stato() . "</em>.</p>\n");

  echo "<form action='kunlogxado.php' method='POST'>\n";
  tenukasxe("kunlogxID", $deziro->datoj['ID']);
  butono("forvisxhu", "Forvis^u", "ago");
  butono("ne_eblas", "Ne eblas", "ago");
  butono("eblas", "Eblas", "ago");
  echo "</form>\n";

  eoecho ("<p>C^iuj kunlog^deziroj de ". $deziranto->tuta_nomo(). ":</p>");
  montru_kunlogxdezirojn($dezirant_eno);

}



function montru_kunlogxdezirojn($partopreno)
{
  $sql = datumbazdemando(array("k.ID", "k.kunKiuID", 'k.stato',
							   "pa.nomo", "pa.personanomo", "p.kunKiu", "p.partoprenantoID"),
						 array("kunlogxdeziroj" => "k",
							   "partoprenoj" => "p",
							   "partoprenantoj" => "pa"),
						 array("k.partoprenoID = '".$partopreno->datoj['ID']."'",
							   "k.kunKiuID = p.ID",
							   "p.partoprenantoID = pa.ID")
						 );
  sercxu($sql, array("pa.personanomo, pa.nomo", "ASC"),
		 array(array("ID", "", "->", "z", "kunlogxado.php?kunlogxID=XXXXX",
					 "partoprenantoID"),
			   array("kunKiuID", "ID", "XXXXX", "r", "partrezultoj.php?partoprenidento=XXXXX",
					 "partoprenantoID"),
			   array("personanomo", "pers. nomo","XXXXX", "l"),
			   array("nomo", "nomo","XXXXX", "l"),
			   array("stato", "stato", "XXXXX", "l"),
			   array("kunKiu", "volas log^i kun","XXXXX", "l"),
			   ),
		 array(),
		 "kunlogxantoj_de_ulo",
		 array("anstatauxo_funkcio" => array(4 => 'kunlogx_stato')),
		 0, "", "", "ne");
  $rez = sql_faru(datumbazdemando("kunKiuID",
								   "kunlogxdeziroj",
								   "partoprenoID = '".$partopreno->datoj['ID']."'"));

  $kunlogxantoj = array();
  while($linio = mysql_fetch_assoc($rez))
	{
	  $kunlogxantoj[] = $linio['kunKiuID'];
	}
  return $kunlogxantoj;
}


function montru_kunlogxdezirojn_inversajn($partopreno)
{
  $sql = datumbazdemando(array("k.ID", "k.partoprenoID", 'k.stato',
							   "pa.nomo", "pa.personanomo", "p.kunKiu", "p.partoprenantoID"),
						 array("kunlogxdeziroj" => "k",
							   "partoprenoj" => "p",
							   "partoprenantoj" => "pa"),
						 array("k.kunKiuID = '".$partopreno->datoj['ID']."'",
							   "k.partoprenoID = p.ID",
							   "p.partoprenantoID = pa.ID")
						 );
  sercxu($sql, array("pa.personanomo, pa.nomo", "ASC"),
		 array(array("ID", "", "->", "z", "kunlogxado.php?kunlogxID=XXXXX",
					 "partoprenantoID"),
			   array("kunKiuID", "ID", "XXXXX", "r", "partrezultoj.php?partoprenidento=XXXXX",
					 "partoprenantoID"),
			   array("personanomo", "p. nomo","XXXXX", "l"),
			   array("nomo", "p.nomo","XXXXX", "l"),
			   array("stato", "stato", "XXXXX", "l"),
			   array("kunKiu", "volas log^i kun","XXXXX", "l"),
			   ),
		 array(),
		 "kunlogxantoj_de_ulo_inversaj",
		 array("anstatauxo_funkcio" => array(4 => 'kunlogx_stato')),
		 0, "", "", "ne");



}


function montru_kunlogxdezirojn_ambauxdirekte($partopreno, $nomo = "mi")
{
  $sql = datumbazdemando(array("count(k.ID)", "p.ID" => "partoprenoID", "pa.nomo",
							   "pa.personanomo", "p.partoprenantoID",
							   "SUM(IF( k.kunKiuID = p.ID, '1', IF( k.partoprenoID = p.ID,".
							   " '2', '0')))" => "direkto" ),
						 array("kunlogxdeziroj" => "k",
							   "partoprenoj" => "p",
							   "partoprenantoj" => "pa"),
						 array("( k.partoprenoID = '".$partopreno->datoj['ID'].
							   "' AND k.kunKiuID = p.ID ) OR ( k.kunKiuID = '".
							   $partopreno->datoj['ID']."' AND k.partoprenoID = p.ID )",
							   "p.partoprenantoID = pa.ID"),
						 "",
						 array("group" => "p.ID")
						 );
  sercxu($sql, array("pa.personanomo, pa.nomo", "ASC"),
		 array(array("ID", "", "->", "z", "kunlogxado.php?kunlogxID=XXXXX",
					 "partoprenantoID"),
			   array("partoprenoID", "ID", "XXXXX", "r",
					 "partrezultoj.php?partoprenidento=XXXXX",
					 "partoprenantoID"),
			   array("personanomo", "pers. nomo","XXXXX", "l"),
			   array("nomo", "nomo","XXXXX", "l"),
			   array("direkto", "kiu deziras", "XXXXX", "l"),
			   ),
		 array(),
		 "kunlogxantoj_de_ulo_ambaux",
		 array("Zeichenersetzung" => array(4 => array('1' => $nomo,
													  '2' => "&larr;",
													  '3' => "&larr; kaj ".$nomo))),
		 0, "", "", "ne");
//   $rez = sql_faru(datumbazdemando("kunKiuID",
// 								   "kunlogxdeziroj",
// 								   "partoprenoID = '".$partopreno->datoj['ID']."'"));

//   $kunlogxantoj = array();
//   while($linio = mysql_fetch_assoc($rez))
// 	{
// 	  $kunlogxantoj[] = $linio['kunKiuID'];
// 	}
//   return $kunlogxantoj;
}


function sercxu_eblajn_kunlogxantojn($partopreno, $nomo, $nemontru = "")
{
  if (DEBUG)
	{
	  echo "<!-- renkontigxo: ";
	  var_export($_SESSION['renkontigxo']);
	  echo "-->";
	}
  $landoradiko =
	"IF( RIGHT(l.nomo, 2) = 'io',"
."         LEFT (l.nomo, length(l.nomo)-2),"
."         IF( RIGHT(l.nomo, 5) = 'lando',"
."             LEFT(l.nomo, LENGTH(l.nomo) - 5),"
."             LEFT(l.nomo, length(l.nomo)-1 )"
."          ) "
."       )";

  $kondicxoj = array("'".$partopreno->datoj['kunkiu'] 
					 . "' LIKE concat('%',pa.nomo,'%') OR "
					 . "'".$partopreno->datoj['kunkiu'] 
					 . "' LIKE concat('%',pa.personanomo,'%') OR " 
					 . "'".$partopreno->datoj['kunkiu'] 
					 . "' LIKE concat('%', ".$landoradiko.",'%')",
					 "p.partoprenantoID = pa.ID",
					 "pa.lando = l.ID",
					 "p.ID != '".$partopreno->datoj['ID']."'"
					 );
	
  if (is_array($nemontru))
	{
	  $nemontru []= $partopreno->datoj['ID'];
	}
  else
	{
	  $nemontru = array($partopreno->datoj['ID']);
	}

  foreach($nemontru AS $elemento)
	{
	  $kondicxoj []= "p.ID != '". $elemento . "'";
	}


  $sql = datumbazdemando(array("p.ID" => "ID", "pa.nomo"=>"nomo", "personanomo",
							   "pa.ID" => "partoprenantoID", "l.nomo" => "landonomo",
							   $landoradiko => "mallonga"),
						 array("partoprenantoj" => "pa", "partoprenoj" => "p",
							   "landoj" => "l"),
						 $kondicxoj,
						 "p.renkontigxoID"
						 );
  sercxu($sql, array("personanomo, nomo", "ASC"),
		 array(array("ID", "", "->", "z", "partrezultoj.php?partoprenidento=XXXXX",
					 "partoprenantoID"),
			   array("personanomo", "persona nomo", "XXXXX", "l"),
			   array("nomo", "nomo", "XXXXX", "l"),
			   array("landonomo", "lando", "XXXXX", "l"),
			   //			   array("mallonga", "lando", "XXXXX", "l"),
			   array("ID", "konektu", "kunlog^igu kun ". $nomo, "l",
					 "kunlogxado.php?ago=kunigu&partoprenoID=".$partopreno->datoj['ID']
					 ."&kunkiuID=XXXXX"),
			   ),
		 array(),
		 "kunlogxantoj_kandidatoj",
		 "", 0, "", "", "ne"
		 );
  
}


?>