<?php
  require_once ("iloj/iloj.php");
  session_start();
  malfermu_datumaro();
 
 
 $farbe[0]="#00FFFF"; 
 $farbe[1]="#CCFFFF"; 
 $zaehler = 0; 
 
// (TODO: traduku:) Auswahl der gewuenschten Aktion 

if ('partoprenintoj_por_enketo' == $elekto)
    {
        /*
Por prepari la enketilon, jen listigo de iom pli teknikaj aferoj kiujn mi bezonas el la IS-datumbazo.

Esence mi bezonas nur iun txt-file (kun komoj por distingi kampojn kaj nova linio por sekva partoprenanto), aux excell-file.

Jen listo de kampo kiujn mi bezonus minimume:
-> Persona kodo por ligi (ID-key)
-> Nomo familia
-> Nomo persona
-> Retadreso

El posta analiza vidpunkto utilus aldonaj donitajxoj:
-> Lando
-> Landokategorio
-> Landokategorio
-> Naskigxdato          
         */
        $sql = datumbazdemando(array('pa.ID', 'pa.personanomo', 'pa.nomo',
                                     'pa.retposxto', 'pa.naskigxdato',
                                     'l.nomo' => 'landonomo',
                                     'l.kategorio' => 'landkat',
                                     ),
                               array('partoprenantoj' => 'pa',
                                     'partoprenoj' => 'p',
                                     'landoj' => 'l', 
                                     ),
                               array('pa.ID = p.partoprenantoID',
                                     'pa.lando = l.ID',
                                     "p.alvenstato = 'a'"),
                               'p.renkontigxoID'
                               );

 sercxu($sql,
		array("ID", "ASC"),
		array(
			  array("ID", "ID", "XXXXX", "", "", ""),
              array("personanomo", "persona nomo", "XXXXX", "", "", ""),
			  array("nomo", "familia nomo", "XXXXX", "", "", ""),
			  array("retposxto", "retposxta adreso", "XXXXX", "", "", ""),
			  array("naskigxdato", "naskig^dato", "XXXXX", "", "", ""),
			  array("landonomo", "Lando", "XXXXX", "", "", ""),
			  array("landkat", "Landokategorio", "XXXXX", "", "", ""),
			  ),
		array(array(0,array('&sum; XX','A','z'))),
		 "ilja_liste",
		 "", 2 /* 2 = CSV por elsxuti */, "", "", "");

    }

 else if('aligxintoj_laux_kotizokategorioj' == $elekto)
     {
         $sql =
             datumbazdemando(array('COUNT(p.ID) AS nombro',
                                   'l.kategorio' => 'landokategorio',
                                   'p.alvenstato',
                                   'p.domotipo',
                                   'p.agxo',
                                   'INTERVAL(p.aligxdato,
											 DATE_ADD(x.plej_frue, INTERVAL (r.ID - x.ID) YEAR),
											 DATE_ADD(x.meze, INTERVAL (r.ID - x.ID) YEAR),
											 r.de,
                                             r.gxis)' => 'aligxkategorio',
                                   'r.mallongigo' => "renkontigxo",
                                   'r.ID' => "renkID"),
                             array('landoj' => 'l',
                                   'partoprenoj' => 'p',
                                   'partoprenantoj' => 'pa',
                                   'renkontigxo AS r',
                                   'renkontigxo AS x'),
                             array('pa.ID = p.partoprenantoID',
                                   'r.ID = p.renkontigxoID',
                                   'r.ID > 0',
                                   'x.ID = 6',
                                   "p.alvenstato = 'a' OR "
                                   . "p.alvenstato = 'i'",
                                   'l.ID = pa.lando'),
                             "",
                             array ('group' => 'renkontigxo, domotipo, '
                                    .          'landokategorio, alvenstato, '
                                    .          'aligxkategorio, agxo')
										
                                );


			/// ------- jen laux agxkategorioj -----------

			

//			var_export($sql);
			$result = sql_faru($sql);
			$resumo = array();
			$aligxkategorioj = array(0,1,2,3,4);
			$landokategorioj = array('A', 'B', 'C');
			$domotipoj = array('J', 'M');
			$renkontigxoj = array(1,2,3,4,5,6);
			$agxkategorioj = array(0, 1,2,3,4);

			foreach($renkontigxoj AS $ren)
			{
				foreach($domotipoj AS $domo)
				{
					foreach($landokategorioj AS $landKat)
					{
						foreach($aligxkategorioj AS $aligxKat)
						{
							foreach($agxkategorioj AS $agxKat)
							{
								$rezultoj[$ren . '##' . $landKat . "##a##" . 
								          $aligxKat . '##' .$domo . '##' . $agxKat] =
									array('renkID' => $ren,
									      'landokategorio' => $landKat,
											'aligxkategorio' => $aligxKat,
											'agxkategorio' => $agxKat,
											'domotipo' => $domo,
											);
							}
						}
					}
				}
			}

			$kotizo = new Kotizo(null,null,null);
			while ($linio = mysql_fetch_array($result, MYSQL_ASSOC))
			{
				// la agxokategorioj laux la 6a IS
				$agxkategorio =
					 $kotizo->kalkulu_agx_kategorio($linio["agxo"], 6 );
				$linionomo =
					$linio['renkID'] . '##' .
					$linio['landokategorio'] . "##" .
					$linio['alvenstato'] . '##' . 
					$linio['aligxkategorio'] . '##' .
					$linio['domotipo'] . '##' .
					$agxkategorio;
				
				$jama_linio = $resumo[$linionomo];
				if (! $jama_linio)
				{
					$jama_linio = $linio;
					$jama_linio['agxkategorio'] = $agxkategorio;
				}
				else
				{
					$jama_linio['nombro'] += $linio['nombro'];
				}
				$resumo[$linionomo] = $jama_linio;
			}
//			var_export($resumo);
		  echo "<table>";
		  reset($resumo);
		  $linio = $resumo[key($resumo)];
		  echo "<tr>";
		  //eoecho("<th>AK#</th><th>Ag^kategorio</th><th>Landokategorio</th><th>Alvenstato</th><th>Alig^kategorio</th><th>renkontig^o</th><th>domotipo</th><th>nombro</th>");
		  eoecho("<th>Ag^kategorio</th><th>Landokategorio</th><th>Alig^kategorio</th><th>renkontig^o</th>" .
		         "<th>domotipo</th><th>nombro</th>");
		  echo "</tr>";
			$renkontigxo = new Renkontigxo(6);
			foreach($resumo AS $linio)
			{
			  echo "<tr><td>" /*. $linio['agxkategorio'] . "</td><td>"*/;
			  $kotizo->agxkategorio = $linio['agxkategorio'];
			  echo $kotizo->formatu_agxkategorion($renkontigxo);
			  echo "</td><td>" . $linio['landokategorio'] . "</td><td>" .
					/* $linio['alvenstato'] . "</td><td>" .*/ ($linio['aligxkategorio']+1) .
					"</td><td>" . $linio['renkontigxo'] . "</td><td>".$linio['domotipo'] ."</td><td>" . $linio['nombro'] . "</td>";
			  echo "</tr>";
			}
		  echo "</table><hr/>";

	// ------------- la bazaj datoj ------

        $vortext = "Sekvas la bazaj datoj, lau^ kiu estis farita la supra statistiko.
 </p>
 <p>
  Jen la alig^kategorioj:</p>
 <ul>
  <li>0: antau^ la unua limdato (unua kategorio)</li>
  <li>1: inter unua kaj dua limdato (dua kategorio)</li>
  <li>2: inter dua limdato kaj komenco de renkontig^o
     (g^is 2004: tria kategorio, poste: krompago pro surloka alig^o)</li>
  <li>3: inter komenco kaj fino de renkontig^o</li>
  <li>4: post la fino de la renkontig^o</li>
 </ul>
  <p>Jen la alvenstatoj:</p>
 <ul>
   <li>v: venos</li>
   <li>a: alvenis</li>
   <li>m: malalig^is</li>
 </ul>
<p>La ciferoj nur estas sencohavaj ekde 2002 (tiuj de 2002 estas importoj de antau^a sistemo). Kaj atentu la limdat-s^ovon inter 2004 kaj 2005.";


        sercxu($sql,
               array("renkontigxo","asc"), 
               array(array('renkontigxo', 'Renkontig^o', 'XXXXX', 'l', '', -1),
                     array('landokategorio','Lando&shy;kategorio','XXXXX','c','','-1'), 
				 array('alvenstato','Alvenstato','XXXXX','c','','-1'),
                     array('aligxkategorio','Alig^kategorio','XXXXX','l','','-1'), 
                     array('agxo','Ag^o','XXXXX','l','','-1'), 
                     array('domotipo','Domotipo','XXXXX','l','','-1'), 
                     array('nombro','nombro','XXXXX','l','','-1'), 
                     ),
               '',
               'aligxintoj_laux_kategorioj',
               0,$csv,$vortext, '');



}
else if ($elekto == 'aligxintoj_laux_kategorioj')
    {

        $sql = datumbazdemando(array('COUNT(p.ID) AS nombro',
                                     'l.kategorio' => 'landokategorio',
                                     'p.alvenstato',
                                     'INTERVAL(p.aligxdato, r.plej_frue, r.meze, r.de, r.gxis)' => 'aligxkategorio',
                                     'r.mallongigo' => "renkontigxo"),
                               array('landoj' => 'l',
                                     'partoprenoj' => 'p',
                                     'partoprenantoj' => 'pa',
                                     'renkontigxo' => "r"),
                               array('pa.ID = p.partoprenantoID',
                                     'r.ID = p.renkontigxoID',
                                     'r.ID > 0',
                                     'l.ID = pa.lando'),
                               "",
                               array ('group' => 'landokategorio, alvenstato, aligxkategorio, renkontigxo')
                               );
                                     
        
        $vortext = "Statistiko pri alig^ciferoj lau^ diversaj kategorioj.
 </p>
 <p>
  Jen la alig^kategorioj:</p>
 <ul>
  <li>0: antau^ la unua limdato (unua kategorio)</li>
  <li>1: inter unua kaj dua limdato (dua kategorio)</li>
  <li>2: inter dua limdato kaj komenco de renkontig^o
     (g^is 2004: tria kategorio, poste: krompago pro surloka alig^o)</li>
  <li>3: inter komenco kaj fino de renkontig^o</li>
  <li>4: post la fino de la renkontig^o</li>
 </ul>
  <p>Jen la alvenstatoj:</p>
 <ul>
   <li>v: venos</li>
   <li>a: alvenis</li>
   <li>m: malalig^is</li>
 </ul>
<p>La ciferoj nur estas sencohavaj ekde 2002 (tiuj de 2002 estas importoj de antau^a sistemo). Kaj atentu la limdat-s^ovon inter 2004 kaj 2005.";

        sercxu($sql,
               array("renkontigxo","asc"), 
               array(array('renkontigxo', 'Renkontig^o', 'XXXXX', 'l', '', -1),
                     array('landokategorio','Lando&shy;kategorio','XXXXX','c','','-1'), 
				 array('alvenstato','Alvenstato','XXXXX','c','','-1'),
                     array('aligxkategorio','Alig^kategorio','XXXXX','l','','-1'), 
                     array('nombro','nombro','XXXXX','l','','-1'), 
                     ),
               '',
               'aligxintoj_laux_kategorioj',
               0,$csv,$vortext, '');

    }
else if ($elekto=="laborontajnotoj") 
 { 
    if ($montro=='nur')
	  $wiederV = "revidu > NOW()"; 
    else if ($montro=='aktuala')
	  $wiederV = "revidu < NOW()"; 
    else if ($montro=='inkl')
	  $wiederV= "1";
     
    $vortext = "Montras c^iujn neprilaboratajn notojn"; 
	
	// "select p.ID,n.ID,n.partoprenantoID,kiu,kunKiu,tipo,dato,personanomo,subjekto,nomo,revidu from notoj as n, partoprenantoj as p ". 
	//   "where p.ID=n.partoprenantoID and prilaborata!='j'".$wiederV, 
	$sql = datumbazdemando(array("p.ID", "n.ID", "n.partoprenantoID", "kiu", "kunKiu",
								 "tipo", "dato", "personanomo", "subjekto", "nomo",
								 "revidu"),
						   array("notoj" => "n",
								 "partoprenantoj" => "p"),
						   array("p.ID = n.partoprenantoID",
								 "prilaborata != 'j'",
								 $wiederV)
						   );
    sercxu($sql,
		   array("dato","desc"), 
		   array(array('1','','->','z','"notoj.php?wahlNotiz=XXXXX"','0'), 
				 array('dato','dato','XXXXX','l','','-1'), 
				 array('personanomo','personanomo','XXXXX','l','','-1'), 
				 array('nomo','nomo','XXXXX','l','','-1'), 
				 array('subjekto','subjekto','XXXXX','l','','-1'), 
				 array("kiu","kiu",'XXXXX','l','','-1'), 
				 array("kunKiu","kun",'XXXXX','l','','-1'), 
				 array("tipo","tipo",'XXXXX','l','','-1') 
				 ),
		   array(array('',array('&sum; XX','A','z'))),
		   "notoj_listo",
		   0,$csv,$vortext, '');
 }
else if ("andiListe" == $elekto)
{
	// sonderanfertigung für AnDi. (Version für IS 2005)

	$sql = datumbazdemando(array('personanomo', 'pa.nomo' => 'nomo', 'sekso', 'naskigxdato',
										  'adresaldonajxo', 'strato', 'posxtkodo', 'urbo',
										  'l.lokanomo' => 'landonomo',
										  "FLOOR(( UNIX_TIMESTAMP('2005-12-27') - UNIX_TIMESTAMP(naskigxdato))/(365.25*24*60*60))" => 'agxo',
										 ), 
								  array('partoprenoj' => 'po', 'partoprenantoj' => 'pa', 'landoj' => 'l',),
								  array('po.partoprenantoID = pa.ID', 'pa.lando = l.id',
										"po.alvenstato = 'a'"),
								  'renkontigxoID'
								 );

  $rez = sql_faru($sql);
  
  sercxu($sql,
		array("personanomo", "ASC"),
		array(array("personanomo", "Vorname", "XXXXX", "", "", ""),
			  array("nomo", "Nachname", "XXXXX", "", "", ""),
			  array("agxo", "Alter", "XXXXX", "", "", ""),
			  array("naskigxdato", "Geburtsdatum", "XXXXX", "", "", ""),
			  array("adresaldonajxo", "Adresszusatz", "XXXXX", "", "", ""),
			  array("strato", "Str. und HNr.", "XXXXX", "", "", ""),
			  array("posxtkodo", "PLZ", "XXXXX", "", "", ""),
			  array("urbo", "Ort", "XXXXX", "", "", ""),
			  array("landonomo", "Land", "XXXXX", "", "", ""),
			  ),
		array(array(0,array('&sum; XX','A','z'))),
		 "andiListe",
		 "", 2 /* CSV por elsxuti */, "", "", "");


}
else if ('nenula_saldo' == $elekto)
{
  $sql = datumbazdemando(array('p.ID', 'p.partoprenantoID', 'pn.nomo', 'pn.personanomo'),
						 array('partoprenoj' => 'p', 'partoprenantoj' => 'pn' ),
						 array("p.alvenstato = 'a'",
							   'pn.ID = p.partoprenantoID'),
						 "renkontigxoID",
						 array("order" => "pn.personanomo, pn.nomo")
						 );
  $rez = sql_faru($sql);
  $renkontigxo = $_SESSION['renkontigxo'];
  $kotsistemo = new Kotizosistemo($renkontigxo->datoj['kotizosistemo']);
  HtmlKapo();
?>
<h1>Ne-nulaj saldoj</h1>
	 <p>Jen listo de cxiuj partoprenantoj de aktuala IS, kies pago-kotizo-saldo
	 estas ne-nula (t.e. pli ol 0.5 &euro;).</p>
<?php
	$sumo_pos = 0;
	$sumo_neg = 0;
  while($linio = mysql_fetch_assoc($rez))
	{
	  $prenanto = new Partoprenanto($linio['partoprenantoID']);
	  $preno = new Partopreno($linio['ID']);
	  $kot = new Kotizokalkulilo($prenanto, $preno, $renkontigxo, $kotsistemo);
	  $enda = $kot->restas_pagenda();
	  if (abs($enda) >= 1.0)
		{
		  ligu ("partrezultoj.php?partoprenantoidento=".$linio['partoprenantoID'] .
				"&partoprenidento=" . $linio['ID'] . "&montrukotizo=montru",
				$prenanto->datoj['personanomo'] . " " . $prenanto->datoj['nomo']);
		  eoecho (" pagis: " .($kot->pagoj).", kotizo: "
				  . $kot->partakotizo . ", ");
		  if ($enda > 0)
			{
			  echo "restas pagenda: <span style='color: red;'>" . $enda . "</span><br/>\n";
			  $sumo_pos += $enda;
			}
		  else
			{
			  echo "repagenda: <span style='color: darkgreen;'>" . (- $enda ) .
				"</span><br/>\n";
				$sumo_neg += (- $enda);
			}
		}
	}
	echo "<p>Entute restas pagenda <span style='color: red;'>$sumo_pos</span>, restas repagenda <span style='color: darkgreen;'>$sumo_neg</span></p>";
  HtmlFino();
  exit();
}
else if ('germanoj_laux_lando' == $elekto)
{
  $sql = datumbazdemando(array('pn.ID','personanomo', 'nomo', 'provinco', 'urbo', 'posxtkodo'),
						 array('partoprenoj' => 'p', 'partoprenantoj' => 'pn'),
						 array('pn.ID = p.partoprenantoID',
							   'pn.lando = 16', /* Germanio */
							   'p.agxo < 27'
							   ),
						 "renkontigxoID"
						 );
  sercxu($sql,
		 array("provinco", "ASC"),
		 array(array('ID','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','0'),
				array("personanomo", "persona_nomo", "XXXXX", "", "", ""),
			   array("nomo", "famila_nomo", "XXXXX", "", "", ""),
			   array("provinco", "provinco", "XXXXX", "", "", ""),
			   array("urbo", "loko", "XXXXX", "", "", ""),
			   array("posxtkodo", "pos^tkodo", "XXXXX", "", "", ""),
			   ),
		 array(),
		 "germanoj_laux_lando",
		 "", 0 /* tabelo */, "", "germanoj sub 27, lau^ lando, kun provincoj", "germanoj sub 27");
}
else if ('cxambrolisto' == $elekto)
{

  $sql = datumbazdemando(array('personanomo', 'pn.nomo' => 'nomo', 'c.nomo' => 'cxambro',
							   'l.nokto_de', 'l.nokto_gxis', ),
						 array('partoprenoj' => 'p', 'cxambroj' => 'c', 'litonoktoj' => 'l',
							   'partoprenantoj' => 'pn',),
						 array('pn.ID = p.partoprenantoID',
							   'p.ID = l.partopreno',
							   'l.cxambro = c.ID',
							   "rezervtipo = 'd'",
							   ),
						 "renkontigxoID"
						 );
  sercxu($sql,
		 array("personanomo", "asc"),
		 array(array("personanomo", "persona_nomo", "XXXXX", "", "", ""),
			   array("nomo", "famila_nomo", "XXXXX", "", "", ""),
			   array("cxambro", "c^ambro", "XXXXX", "", "", ""),
			   array("nokto_de", "de", "XXXXX", "", "", ""),
			   array("nokto_gxis", "gxis", "XXXXX", "", "", ""),
			   ),
		 array(),
		 "cxambrolisto",
		 "", 0 /* CSV por elsxuti */, "Homoj kun c^ambroj", "homoj kun c^ambroj", 'jes');
}
else if ("junulargastejolisto" == $elekto)
{
  // Sonderanfertigung für Jugendherberge Wetzlar


  $sql = datumbazdemando(array('personanomo', 'pa.nomo' => 'nomo', 'po.agxo', 'domotipo',
							   "KKRen", 'vegetare', 'alvenstato',
							   "sxildlando", 'l.nomo' => "landonomo"),
						 array("partoprenoj" => "po", "partoprenantoj" => "pa",
							   "landoj" => "l"),
						 array("po.partoprenantoID = pa.ID", "po.alvenstato <> 'm'",
							   "pa.lando = l.ID"),
						 "renkontigxoID",
						 array("order" => "personanomo ASC")
						 );
  $rez = sql_faru($sql);
  
  header("Content-Type: application/octet-stream"); //csv als Download anbieten 
  header('Content-Disposition: attachment; filename="teilnehmer.csv"');
  
  echo "Vorname;Nachname;Alter;Wohnort;Vegetarier;Essen;Land;KKRen;\n";

  while($linio = mysql_fetch_assoc($rez))
	  {
		if ($linio['domotipo'] == "M")
		{
		  $mangxo = "-";
		}
		else if ($linio['vegetare'] == 'J')
		{
		  $mangxo = 'V';
		}
		else
		{
		  $mangxo = 'F';
		}

		if ($linio['sxildlando'])
		{
		  $lando = $linio['sxildlando'];
		}
		else
		{
		  $lando = $linio['landonomo'];
		}

		if ($linio['KKRen'] == 'J')
		  {
			$kkren = 'J';
		  }
		else
		  {
			$kkren = 'N';
		  }

		echo eotransformado($linio['personanomo'] . ";" . $linio['nomo']. ";" . $linio['agxo'] .
							";" . $linio['domotipo']. ";" . $linio['vegetare'] . ";" .
							$mangxo . ";".  $lando .";" .$kkren. ';'. $linio['alvenstato'] .";\n" , "utf-8");
	  }


}
else if ("francoj" == $elekto)
{
  // SELECT pa.personanomo, pa.nomo, pa.retposxto FROM `is_partoprenoj` as po, is_partoprenantoj as pa  WHERE (po.partoprenantoID = pa.ID) and (pa.lando = 15) and (po.renkontigxoID = 4)

  $sql = datumbazdemando(array("personanomo", "nomo", "retposxto"),
						 array("partoprenoj" => "po", "partoprenantoj" => "pa"),
						 array("po.partoprenantoID = pa.ID", "pa.lando = 15"),
						 "renkontigxoID");
  sercxu($sql,
		array("personanomo", "ASC"),
		array(array("personanomo", "persona_nomo", "XXXXX", "", "", ""),
			  array("nomo", "famila_nomo", "XXXXX", "", "", ""),
			  array("retposxto", "retadreso", "XXXXX", "", "", ""),
			  ),
		array(),
		 "francoj_is_2004", "", 2 /* CSV por elsxuti */, "", "", "");
}
else if ($elekto=="notojn")  
{
    //
    // vokata de la listo en la menuo (per route.php), kaj ankaux 
    // rekte de iuj lokoj.


    $vortext = "Montras c^iun noton de partoprenanto " .
		"<a href='partrezultoj.php?partoprenantoidento=${partoprenantoidento}'>#" .
		$partoprenantoidento . "</a>.</p>\n".
	 	"<p><a href='notoj.php?elekto=bla&partoprenantoidento=${partoprenantoidento}'>Kreu novan noton!</a>"; 

    listu_notojn($partoprenantoidento, $vortext);
 	
 }
else if ($elekto == "kunmangxo")
{
  $sql = datumbazdemando(array("pn.ID", "p.ID" => "partoprenoIdento",
							   "pn.nomo" => "nomo", "personanomo",
							   "p.domotipo", "p.kunmangxas",
							   "'". $_SESSION['renkontigxo']->datoj['ID']."'" => "renkNumero"),
						 array("partoprenoj" => "p",
							   "partoprenantoj" => "pn"),
						 array("NOT (( p.domotipo = 'J' AND p.kunmangxas = 'J' ) OR ".
							   "( p.domotipo = 'M' AND p.kunmangxas = 'N' )) ",
							   "p.partoprenantoid = pn.ID"
							   ),
						 'p.renkontigxoID');

    sercxu($sql,
		  array("personanomo","asc"),
		  array(array('ID','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','0'), 
				array('personanomo','personanomo','XXXXX','l','',''), 
				array('nomo','nomo','XXXXX','l','','-1'), 
				array('kunmangxas','kunmang^as','XXXXX','z','','-1'),
				array('domotipo','domotipo','XXXXX','z','','-1'), 
				), 
		  array(array('',
					  array('&sum; XX','A','z'))),
		  "kumangxantoj-listo",
		  '',0,$vortext, "Amaslog^antaj kunmang^antoj"); 

}
else if ($elekto=="venantoj")  
 { 
    $vortext = "Montras c^iun partoprenanton de la ".$_SESSION["renkontigxo"]->datoj[nomo]; 

	// "select p.ID,pn.ID,p.nomo,personanomo,retakonfirmilo,aligxdato,lando,l.ID,l.nomo from partoprenantoj as p,partoprenoj as pn, landoj as l where l.ID=p.lando and pn.partoprenantoID=p.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."'"

	if ($ordo == "")
	  {
		$ordo = "personanomo";
	  }
	
	$sql = datumbazdemando(array("p.ID", "pn.ID" => "partoprenoIdento",
								 "p.nomo" => "nomo", "personanomo",
								 "retakonfirmilo", "aligxdato", /* "lando", */
								 /* "l.ID" =>'landoid', */
								 "l.nomo" => "landonomo",
                                 "'" . ($_SESSION['renkontigxo']->
                                        datoj['ID']) .
                                 "'" => "renkNumero"),
						   array("partoprenantoj" => "p",
								 "partoprenoj" => "pn",
								 "landoj" => "l"),
						   array("l.ID = p.lando",
								 "pn.partoprenantoID = p.ID"),
						   "renkontigxoID");

    sercxu($sql,
		   array($ordo,"asc"),
		   array(array('ID','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','0'),
				 array('personanomo','personanomo','XXXXX','l','','', ),
				 array('nomo','nomo','XXXXX','l','','-1', ),
				 array('landonomo','lando','XXXXX','r','','-1',),
				 array('aligxdato','aligxdato','XXXXX','l','','-1', ),
				 array("retakonfirmilo","retakonfirmilo",'XXXXX','z','','-1', ),
            ),
      array(array('',
				  array('&sum; XX','A','z'),
				  '',
				  '',
				  '',
				  array('&sum; XX','J','z'))),
		   "venontoj",
      '',0,$vortext, "Alig^intoj de ". $_SESSION["renkontigxo"]->datoj['mallongigo']); 
 
 } 
else if ($elekto=="profesioj")  
{ 
  $vortext = "Montras c^iujn profesiojn"; 
  
  
  // "select p.ID,pn.ID,p.nomo,personanomo,okupigxo,okupigxteksto,lando,l.ID,l.nomo from partoprenantoj as p,partoprenoj as pn, landoj as l where l.ID=p.lando and pn.partoprenantoID=p.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and okupigxo!=''", 
  $sql = datumbazdemando(array("p.ID", "pn.ID", "p.nomo", "personanomo",
							   "okupigxo", "okupigxteksto", "lando",
							   "l.ID", "l.nomo" => "landonomo"),
						 array("partoprenantoj" => "p",
							   "partoprenoj" => "pn",
							   "landoj" => "l"),
						 array("l.ID = p.lando",
							   "pn.partoprenantoID = p.ID",
							   "okupigxo != ''"),
						 "renkontigxoID");
  require_once('iloj/iloj_sercxo_rezulto.php');
  sercxu($sql,
		 array("okupigxo,okupigxteksto","asc"),
		 array(array('0','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','0'),
			   array('personanomo','personanomo','XXXXX','l','',''),
			   array('nomo','nomo','XXXXX','l','','-1'),
			   array('landonomo','lando','XXXXX','r','','-1',),
			   array('okupigxo','','XXXXX','l','','', ),
			   array('okupigxteksto','','XXXXX','l','',''),
			   ),
		 array(array(0,array('&sum; XX','A','z'),0,0,array('&sum; XX','E','z'))),
		 "okupigxtipoj",
		 array('okupigxtipo'=>'4'),
		 0,
		 $vortext, "homoj kun okupig^o", 'jes'); 
  
} 
 else if ($elekto=="cxambrodisdonado")  
 { 

     // TODO: falls mehrere Anzahlungen einer Person, diese zusammenfassen
     // TODO:  ... (Group by, SUM). am besten noch herausfinden, zu welchem
     // TODO:  ...  Zeitpunkt die Mindestanzahlung überschritten wurde.

    if ($AB=='nur')
	  {
		$kaj = array("dato", "kvanto");
		$kaj3= array("pagoj");
		$kaj2= array("partoprenoID = pn.ID");
		$order="dato";
        $menutitolo = "c^ambremuloj kun antau^pago";
	  }
	else
	  {
		$kaj = array();
		$kaj2 = array();
		$kaj3 = array();
		$order="aligxdato";
        $menutitolo = "c^iuj c^ambremuloj";
	  }
    //if ($AB=='C') {$kaj2=" and l.kategorio='C' ";}
    
	//    $demando = "select p.ID,pn.ID,p.nomo,personanomo,aligxdato,lando,l.ID,l.nomo,l.Kategorio,kvanto,dulita,kunkiu,cxambrotipo".$kaj." from partoprenantoj as p,partoprenoj as pn, landoj as l".$kaj3." where l.ID=p.lando and pn.partoprenantoID=p.ID and domotipo='J' ".$kaj2."and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."'";// group by partoprenantoID";
	$demando = datumbazdemando(array_merge(array("p.ID", "pn.ID", "p.nomo", "personanomo",
												 "aligxdato", "lando", "l.ID", "l.nomo",
												 "l.Kategorio", "kvanto", "dulita",
												 "kunkiu", "cxambrotipo"),
										   $kaj),
							   array_merge(array("partoprenantoj" => "p",
												 "partoprenoj" => "pn",
												 "landoj" => "l"),
										   $kaj3),
							   array_merge(array("l.ID = p.lando",
												 "pn.partoprenantoID = p.ID",
												 "domotipo = 'J'"),
										   $kaj2),
							   "renkontigxoID");
    
    $vortext = "Montras c^iun c^ambrohavemulo lau^orde, respektante antau^pagojn kaj C-landanojn";
    sercxu($demando, 
		  array($order,"asc"), 
		  array(array('0','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','0'),
				array('personanomo','personanomo','XXXXX','l','',''),
				array('2','nomo','XXXXX','l','','-1'),
				array('7','lando','XXXXX','r','','-1'),
				array('aligxdato','aligxdato','XXXXX','l','','-1'),
				array('8','kat.','XXXXX','r','','-1'),
				array('9','kvanto','XXXXX','r','','-1'),
				array('dato','dato','XXXXX','r','','-1'),
				array('cxambrotipo','c^t','XXXXX','z','','-1'),
				array('dulita','dulita','XXXXX','z','','-1'),
				array('kunkiu','kunkiu','XXXXX','z','','-1')
				),
		  array(array('',array('&sum; XX','A','z'),'','',array('&sum; XX','J','z'))),
		  "cxambrohavemuloj",
		  array('litomanko'=>'1'),
		  0,$vortext, $menutitolo);
 }
else if ($elekto=="skribuagxon")  
 {  

   rekalkulu_agxojn();
   eoecho( "Ag^oj rekalkulitaj!" );
 }
 else if ($elekto=="kunlogxantoj")  
 {  
   HtmlKapo();
    eoecho('<form action="partrezultoj.php" name="peter" method="POST">'.
	  '<input type="hidden" name="kune" value="0"><h2>Kunlog^antoj</h2>');
	eoecho("<p>Se A deziras log^i kun B, tiam elektu B en la listo sub la maldekstra menuo,".
		   " kaj alklaku la butonon en la tabellinio de A.</p>");

   // "select p.ID,pn.ID,nomo,personanomo,kunkiu,kunkiuID from partoprenantoj as p,partoprenoj as pn where pn.partoprenantoID=p.ID and kunkiu!='' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."'", 
   $sql = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo", "kunkiu", "kunkiuID"),
						  array("partoprenantoj" => "p",
								"partoprenoj" => "pn"),
						  array("pn.partoprenantoID = p.ID",
								"kunkiu != ''"),
						  "renkontigxoID");
   sercxu($sql,
		  array("personanomo","asc"), 
		  array(array('0','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','0'),
				array('personanomo','personanomo','XXXXX','l','',''),
				array('nomo','nomo','XXXXX','l','','-1'),
				array('kunkiu','kunkiu','XXXXX','l','','-1'),
				array('kunkiuID','kunkiuID','XXXXX','l','','-1'),
				array('1','Disdonu',
					  '<input name="partoprenidento" value="XXXXX" ' .
					  'type="submit"  onclick="reindamit()"> ',
					  'l','','')
				),
		  array(array('',array('&sum; XX','A','z'),'','',array('&sum; XX','Z','z'))),
		  "kunlogxanto-listo",
		  '',0,'', 'Kunlog^dezirantoj', 'ne');
   echo "</form>";
   HtmlFino();
 } 
 else if ($elekto=="restaspagenda")
 {
   HTMLkapo();
   // "select p.ID,pn.ID from partoprenantoj as p, partoprenoj as pn where pn.partoprenantoID=p.ID and pn.renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and alvenstato!='m'"
   $rezulto = sql_faru(datumbazdemando(array("p.ID", "pn.ID"),
									   array("partoprenantoj" => "p",
											 "partoprenoj" => "pn"),
									   array("pn.partoprenantoID = p.ID",
											 "alvenstato != 'm'",
                                             "alvenstato != 'n'"),
									   "renkontigxoID"));
  eoecho ("<table border=1><TR><TD>personanomo<td>nomo<td>pagendas<td>antau^pagis<td>restas");
  while ($row = mysql_fetch_array($rezulto, MYSQL_NUM))
  {
    $partoprenanto = new Partoprenanto($row[0]);
    $partopreno = new Partopreno($row[1]);

    $ko = new Kotizo($partopreno,$partoprenanto,$_SESSION["renkontigxo"]);
    echo "<TR><TD>".$partoprenanto->datoj[personanomo]."<TD>".$partoprenanto->datoj[nomo]."<td>".$ko->kotizo."<td>".$ko->antauxpago."<td>".$ko->pagenda;

  }
 
 }
 else if ("pliaj" == $elekto)  // la detala sercxado
 { 
   $kaj = array();
   $kolonoj = array(array('ID','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"',
						  '0'), 
					array('personanomo','personanomo','XXXXX','l','',''),
					array('nomo','nomo','XXXXX','l','','-1'),
					array('agxo','ag^o','XXXXX','r','',''),
					array('retposxto','retpos^to','XXXXX','l','',''),
					array('landonomo','lando','XXXXX','l','','-1'),
					array('urbo','urbo','XXXXX','l','','-1'),
					array('aligxdato','aligxdato','XXXXX','l','','-1'),
					);
    if ($abc[0]!='a')
    {
      $kaj[]= " l.kategorio='".$abc[0]."'";            
    }
    if ($lando!='')
    {
      $helpa = "l.ID"; //='".$lando."'";
      if ($malellando=='J')
		$helpa .= ' <> ';
	  else
		$helpa .= ' = ';
      $helpa .= "'" . $lando . "'";
	  $kaj[] = $helpa;
    }
    
    
    if ($_REQUEST['alvenstato'] !='?')
    {
      $kaj[] = "pn.alvenstato = '".$_REQUEST['alvenstato']."'";
    }
    if ($traktstato[0]!='a')
    {
      $kaj[] = "pn.traktstato = '".$traktstato."'";
    }
    
	if ($havasAsekuron[0]!='a')
	  {
		$kaj[] = "pn.havas_asekuron = '".$havasAsekuron."'";
	  }

    if ($asekuri[0]!='a')
    {
      $kaj[] = "pn.asekuri = '".$asekuri."'";
    }
    
    if ($sekso[0]!='a')
    {
      $kaj[] = "p.sekso = '".$sekso[0]."'";
    }
    if ($agxode!='')  // das mit dem Alter und Heimatland kommt später hinein
    {

      $kaj[] = "pn.agxo >= '".$agxode."'";
	  $kaj[] = "pn.agxo <= '".$agxogxis."'";
      //$select_kaj .= "FLOOR((TO_DAYS('".$_SESSION["renkontigxo"]->datoj[de]."')-TO_DAYS(naskigxdato))/365.25),";

      //array_push($kolonoj,"agxo","ag^o",'XXXXX','z','','-1');
    }
    if ($komencanto[0]!='a')
    {
      $kaj[] = "pn.komencanto ".$komencanto." 'J'";
    }
    if ($vegetare!='?')
    {
      $kaj[] = "pn.vegetare = '". $vegetare ."'";
    }
    if ($gejmembro[0]!='a')
    {
      $kaj[] = "pn.GEJmembro = '".$gejmembro[0]."'";
    }
    if ($surlkotizo[0]!='-')
    {
      $kaj[] = "pn.surloka_membrokotizo = '".$surlkotizo[0]."'";
    }
    if ($tejomembrolaux[0]!='a')
    {
      $kaj[] = "pn.tejo_membro_laudire = '".$tejomembrolaux[0]."'";
    }
    if ($tejomembropost[0]!='-')
    {
      $kaj[] = "pn.tejo_membro_kontrolita = '".$tejomembropost[0]."'";
    }
    if ($KKRen[0]!='a')
    {
      $kaj[] = "pn.KKRen = '".$KKRen[0]."'";
    } 
    if ($partoprentipo[0]!='a')
    {
      $kaj[] = "pn.partoprentipo = '".$partoprentipo[0]."'";
    } 
    if ($domotipo[0]!='a')
    {
      $kaj[] = "pn.domotipo = '".$domotipo[0]."'";
    }     
    if ($kunmangxas[0]!='a')
    {
      $kaj[] = "pn.kunmangxas = '".$kunmangxas[0]."'";
    }  
    if ($ekskursbileto[0]!='a')
    {
      $kaj[] = "pn.ekskursbileto = '".$ekskursbileto[0]."'";
    }     
    if ($cxambrotipo[0]!='a')
    {
      $kaj[] = "pn.cxambrotipo = '".$cxambrotipo[0]."'";
    }     
    if ($dulita[0]!='a')
    {
        if ($dulita == 'N') {
            $kaj[] = "pn.dulita = '' or pn. dulita = 'N'";
        }
        else {
            $kaj[] = "pn.dulita = '$dulita'";
        }
    } 
    if ($kuncxambroj=='J')
    {
      $extra['litomanko']= '1';
    }
    if ($tutacxambro=='T')
    {
      $extra['tutacxambro']='1';
    }
    if ($kunadreso=='J')
    {
	  array_push($kolonoj,array("adresaldonajxo","adresaldonajxo",'XXXXX','l','','-1'));
	  array_push($kolonoj,array("strato","strato",'XXXXX','l','','-1'));
	  array_push($kolonoj,array("provinco","provinco",'XXXXX','r','','-1'));
	  array_push($kolonoj,array("posxtkodo","pos^tkodo",'XXXXX','r','','-1'));
	  array_push($kolonoj,array("naskigxdato","naskd",'XXXXX','z','','-1'));

    }
    if ($invitletero[0]!='a')
    {
      $kaj[] = "pn.invitletero ".$invitletero." 'J'";
      array_push($kolonoj,array("invitletero","invitletero",'XXXXX','z','','-1'));
      array_push($kolonoj,array("invitilosendata","invitosendata",'XXXXX','l','','-1'));
    }

    if ($retakonfirmilo[0]!='a')
    {
      $kaj[] = "pn.retakonfirmilo ".$retakonfirmilo." 'J'";
      array_push($kolonoj,array("retakonfirmilo","retakonfirmilo",'XXXXX','z','','-1'));
    }
    if ($germanakonfirmilo[0]!='a')
    {
      $kaj[] = "pn.germanakonfirmilo ".$germanakonfirmilo." 'J'";
      array_push($kolonoj,array("germanakonfirmilo","germanaakonfirmilo",'XXXXX','z','','-1'));
    }
    if ($kontrolata[0]!='a')
    {
      $kaj[] = "pn.kontrolata ".$kontrolata." 'J'";
      array_push($kolonoj,array("kontrolata","kontrolata",'XXXXX','z','','-1'));
    }
    
    if ($konf1a[0]!='a')
    {
      $kaj[] = "pn.1akonfirmilosendata ".$konf1a." '0000-00-00'";
      array_push($kolonoj,array("1akonfirmilosendata","1akonfirmilosendata",'XXXXX','z','','-1'));
    }

    if ($konf2a[0]!='a')
    {
      $kaj[] = "pn.2akonfirmilosendata ".$konf2a." '0000-00-00'";
      array_push($kolonoj,array("2akonfirmilosendata","2akonfirmilosendata",'XXXXX','z','','-1'));
    }
    
    if ($mangxkupo[0]!='a')
    {
      $kaj[] = "pn.havasMangxkuponon=' ".$mangxkupo." '";
      array_push($kolonoj,array("havasMangxkuponon","havasMangxkuponon",'XXXXX','z','','-1'));
    }

    if ($nomsxildo[0]!='a')
    {
      $kaj[] = "pn.havasNomsxildon = '".$nomsxildo."'";
      array_push($kolonoj,array("havasNomsxildon","havasNomsxildon",'XXXXX','z','','-1'));
    }


    if ($distra=="J")
    {
      $kaj[] = "pn.distra != ''";
      array_push($kolonoj, array('distra','distrakontribuo','XXXXX','l','',''));
    }       
    if ($tema=="J")
    {
      $kaj[] = "pn.tema != ''";
      array_push($kolonoj, array('tema','temakontribuo','XXXXX','l','',''));
    }
    if ($vespera=="J")
    {
      $kaj[] = "pn.vespera != ''";
      array_push($kolonoj, array('vespera','vesperakontribuo','XXXXX','l','',''));
    }
    if ($muzika=="J")
    {
      $kaj[] = "pn.muzika != ''";
      array_push($kolonoj, array('muzika','muzikakontribuo','XXXXX','l','',''));
    }
    $vortext = "Montras c^iun partoprenanton lau^vole: [(" . implode(") kaj (", $kaj) . ")]";
    

	$sercxfrazo =
        datumbazdemando(array("p.ID", "pn.ID" => "partoprenoIdento",
                              "p.nomo" => "nomo", "personanomo",
                              "retakonfirmilo", "aligxdato", "lando",
                              //"l.ID" => "landoID",
                              "l.nomo" => "landonomo", "pn.agxo",
                              "urbo", "strato", 
                              "'" . $_SESSION["renkontigxo"]->datoj["ID"] . "'"
                              => "renkNumero",
                              "provinco",
                              "adresaldonajxo", "posxtkodo", "naskigxdato",
                              "retposxto", "kontrolata", "distra", "tema",
                              "vespera", "muzika", "invitletero", "invitilosendata",
                              "2akonfirmilosendata", "1akonfirmilosendata",
                              "alvenstato", "traktstato", "asekuri", "domotipo",
                              "komencanto", "partoprentipo", "havasMangxkuponon",
                              "havasNomsxildon", "GEJmembro", "KKRen"),
                        array("partoprenantoj" => "p",
                              "partoprenoj" => "pn",
                              "landoj" => "l"),
                        array_merge(array("l.ID = p.lando",
                                          "pn.partoprenantoID = p.ID"),
                                    $kaj),
                        "renkontigxoID");


	sercxu($sercxfrazo,
		   array("personanomo", "asc"),
		   $kolonoj,
		   array(array('',array('&sum; XX','A','z'))),
		   "detalasercxo",
		   $extra,$csv,$vortext,"(el detala serc^o)");
 }
 else if ($elekto=="antauxpagoj")
 {

   $sql = datumbazdemando(array("p.ID", "p.partoprenoID", "pp.ID", "pp.partoprenantoID",
								"pt.ID", "nomo", "personanomo", "kvanto", "dato", "tipo"),
						  array("pagoj" => "p",
								"partoprenoj" => "pp",
								"partoprenantoj" => "pt"),
						  array("p.partoprenoID = pp.ID",
								"pp.partoprenantoID = pt.ID"),
						  "renkontigxoID");
   sercxu($sql,
		  array("tipo,dato","asc"),
		  array(array('3','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','3'),
				array('tipo','tipo','XXXXX','l','',''),
				array('personanomo','personanomo','XXXXX','l','',''), 
				array('nomo','nomo','XXXXX','l','','-1'), 
				array('kvanto','kvanto','XXXXX','l','',''), 
				array('dato','dato','XXXXX','l','','-1')),
		  0,
		  "antauxpago-listo",
		  0,0, "C^iuj antau^pagoj:", "c^iuj antau^pagintoj");

 //Einzelsummen Anzahlungen
   // "select SUM(kvanto),tipo from pagoj as p,partoprenoj as pn where p.partoprenoID=pn.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' group by tipo"


   $sql = datumbazdemando(array("SUM(kvanto)" => "kvantsumo", "tipo"),
						  array("pagoj" => "p",
								"partoprenoj" => "pn"),
						  "p.partoprenoID = pn.ID",
						  "renkontigxoID",
						  array("group" => "tipo"));
   sercxu($sql,
		  array("tipo","asc"),
		  array(array('tipo','tipo','XXXXX','l','',''),
				array('kvantsumo','kvanto','XXXXX','l','','') 
				),
		  array(array(array('# XX', 'A', 'z'), array('&sum; XX', 'N', 'z'))),
		  "antauxpagoj-laux-tipo",
		  0,0, "Sumoj lau^ la antau^pagmanieroj:", '');
 }
 else if ($elekto=="rabatoj")
 {
   // "select r.ID,r.partoprenoID,pp.ID,pp.partoprenantoID,pt.ID,nomo,personanomo,kvanto,kauzo from rabatoj as r, partoprenoj as pp, partoprenantoj as pt where r.partoprenoID=pp.ID and pp.partoprenantoID=pt.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."'"


   $sql = datumbazdemando(array("r.ID", "r.partoprenoID", "pp.ID", "pp.partoprenantoID",
								"pt.ID", "nomo", "personanomo", "kvanto", "kauzo"),
						  array("rabatoj" => "r",
								"partoprenoj" => "pp",
								"partoprenantoj" => "pt"),
						  array("r.partoprenoID = pp.ID",
								"pp.partoprenantoID = pt.ID"),
						  "renkontigxoID");
   sercxu($sql,
		  array("kauzo","asc"),
		  array(array('3','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','3'),
				array('kauzo','kau^zo','XXXXX','l','',''),
				array('personanomo','personanomo','XXXXX','l','',''),
				array('nomo','nomo','XXXXX','l','','-1'),
				array('kvanto','kvanto','XXXXX','l','',''), 
				),
		  0,
		  "rabato-listo",
		  0,0, "C^iuj rabatoj:", 'C^iuj rabatitoj');


   // "select SUM(kvanto),kauzo,renkontigxoID,r.partoprenoID,pn.ID from rabatoj as r,partoprenoj as pn where r.partoprenoID=pn.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' group by kauzo",
   $sql = datumbazdemando(array("SUM(kvanto)" => "kvantsumo", "kauzo", "renkontigxoID",
								"r.partoprenoID", "pn.ID"),
						  array("rabatoj" => "r", "partoprenoj" => "pn"),
						  "r.partoprenoID = pn.ID",
						  "renkontigxoID",
						  array("group" => "kauzo")
						  );
   sercxu($sql,
		  array("kauzo", "asc"),
		  array(array('kauzo','kau^zo','XXXXX','l','',''),
				array('kvantsumo','kvanto','XXXXX','l','','')
				),
		  array(array(array('# XX', 'A', 'z'), array('&sum; XX', 'N', 'z'))),
		  "rabatoj-laux-kauxzo",
		  0,0, "Sumoj lau^ la unuopaj rabatkau^zoj", '');
   
 }
 else if ($elekto=="rimarkoj")
 {
   // "select pp.ID,pp.partoprenantoID,pt.ID,pp.rimarkoj,nomo,personanomo from partoprenoj as pp, partoprenantoj as pt where pp.partoprenantoID=pt.ID and pp.rimarkoj!='' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."'",

   $sql = datumbazdemando(array("pp.ID", "pp.partoprenantoID", "pt.ID",
								"pp.rimarkoj", "nomo", "personanomo"),
						  array("partoprenoj" => "pp", "partoprenantoj" => "pt"),
						  array("pp.partoprenantoID = pt.ID",
								"pp.rimarkoj != ''"),
						  "renkontigxoID");

   sercxu($sql,
		  array("personanomo","asc"),
		  array(array('1','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','1'),
				array('personanomo','personanomo','XXXXX','l','',''), 
				array('nomo','nomo','XXXXX','l','','-1'), 
				array('3','rimarkoj','XXXXX','l','',''), 
				),
		  0,
		  "rimarko-listo",
		  0,
		  0, "C^iuj rimarkoj de la partoprenantoj.", 'rimarkintoj');
 }
 else if ("kotizokomparo" == $elekto) {


     $kotizosistemo =
         new Kotizosistemo($_SESSION['renkontigxo']->datoj['kotizosistemo']);


     $sql = datumbazdemando(array("pn.ID" => "eno", "pt.ID" => "anto"),
                            array("partoprenoj" => "pn",
                                  "partoprenantoj" => "pt"),
                            array("pn.partoprenantoID = pt.ID"),
                            "renkontigxoID");
     $rez = sql_faru($sql);

     HtmlKapo();
     eoecho ("<table>\n".
             "<tr><th>p-enoID</th><th>nomo</th><th>nova kotizo</th>".
             "<th>malnova kotizo</th><th>diferenco</th></tr>\n");
     while ($linio = mysql_fetch_assoc($rez)) {
         $pprenanto = new Partoprenanto($linio['anto']);
         $ppreno = new Partopreno($linio['eno']);

         // malnova kotizosistemo
         $kot = new Kotizo($ppreno,
                           $pprenanto,
                           $_SESSION['renkontigxo']);

         // nova kotizosistemo
         $kotkal = new Kotizokalkulilo($pprenanto,
                                    $ppreno,
                                    $_SESSION['renkontigxo'],
                                    $kotizosistemo);

         $malnova = $kot->restas_pagenda();
         $nova = $kotkal->restas_pagenda();

         if (abs($nova - $malnova) < 1) {
             echo
                 "<!-- " . $pprenanto->tuta_nomo() . "(" . $nova . "/" . $malnova . ") -->";
         }
         else {
             eoecho ("<tr><td>".
                     donu_ligon("partrezultoj.php?partoprenidento=" .
                                $ppreno->datoj['ID'],
                                $ppreno->datoj['ID']) . "</td><td>" .
                     $pprenanto->tuta_nomo() . "</td><td>" .
                     $nova . "</td><td>" . $malnova . "</td><td>" .
                     ($nova - $malnova).  "</td></tr>");
                
         }

     }
     echo ("</table>");
     HtmlFino();
 } // kotizokomparo
else if ("memligo" == $elekto)
{
  // por ebligi varian ordigadon en tabeloj.
  // nova varianto de $elekto == "eigenlink",
  // uzata de sercxu() anstataux de Suche().

  sercxu($_SESSION['memligo'][$id]['sql'],
		 array($orderby, $asc),
		 $_SESSION["memligo"][$id]["kolumnoj"],
		 $_SESSION["memligo"][$id]["sumoj"],
		 $id,
		 $_SESSION["memligo"][$id]["aldone"],
		 0 /* csv == 0 -> HTML-tabelo */,
		 $_SESSION["memligo"][$id]["antauxteksto"],
		 $_SESSION['memligo'][$id]['almenuo'],
		 "jes");
}
// else if ($elekto=="eigenlink") 
// {
//   //fuer das variable Sortieren benoetigt 
//   //echo "Eigenlink: $orderby asc: $asc"; 
//   //echo session_encode(); 
//   Suche($_SESSION["Eigenlink_sql"],
// 		$orderby,
// 		$asc,
// 		$_SESSION["Eigenlink_anzeige"],
// 		$_SESSION["Eigenlink_summen"],
// 		$_SESSION["Eigenlink_extra"],
// 		0,
// 		$_SESSION["Eigenlink_vortext"]);
//   //,stripslashes($extra),stripslashes($csv) 
// }
 else 
 { 
   echo "Irgendwas ist schiefgelaufen...."; 
 } 

 
?>