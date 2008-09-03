<?php

  /**
   * Granda serĉ-funkcio.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



/**
 * Ĝenerala serĉ-funkcio.
 *
 * Serĉas en la datumbazo kaj montras la rezulton en HTML-tabelo.
 *
 * @param string $sql - la SQL-demando, ekzemple kreita de
 *              {@link datumbazdemando()} (sen ordigo).
 *
 * @param array $ordigo  array(),
 *   - $ordigo[0]:  laŭ kiu kolumno la rezultoj ordiĝu
 *   - $ordigo[1]:  ĉu la rezultoj ordiĝu pligrandiĝanta ("ASC") aŭ
 *                malpligrangiĝanta ("DESC")?
 *
 * @param array $kolumnoj
 *     array() de array-oj, por la unuopaj kolumnoj. Por ĉiu kolumno,
 *      la array enhavu la sekvajn ses komponentojn (ĉiuj ĉeestu, eĉ se malplenaj):
 *   - [0] - aŭ nomo aŭ numero de kampo de la SQL-rezulto.
 *          Prefere uzu nomon, ĉar per numero la ordigo ne funkcias.
 *   - [1] - la titolo de la kolumno
 *   - [2] - La teksto, kiu aperu en la tabelo. Se vi uzas XXXXX (jes, 5 iksoj),
 *          tie aperas la valoro el la SQL-rezulto.
 *   - [3] - aranĝo: ĉu la valoroj aperu dekstre ("r"), meze ("z") aŭ
 *             maldekstre ("l") en la tabelkampo?
 *   - [4] - se ne "", la celo de ligilo. (Alikaze ne estos ligilo.)
 *   - [5] - Se estas ligilo, kaj ĉi tie ne estas -1, dum klako al
 *          la ligilo en la menuo elektiĝas la persono, kies identifikilo
 *          estas en la kampo, kies nomo/numero estas ĉi tie.
 *
 * @param array $sumoj
 *          por ĉiu sum-linio ekzistas array (en $sumoj). En ĉiu linio-array
 *      estas po element-array por kolono, kun tri elementoj:
 *   - [0] - La teksto de la kampo. Se vi uzas XX, tie aperos la rezulto
 *         de la sumado.
 *   - [1] - La speco de la sumado. eblecoj:
 *            --  A - simple nur kalkulu, kiom da linioj estas.
 *            --  J - kalkulu, kiom ofte aperas 'J' en la koncerna kampo
 *            --  E - kalkulu, kiom ofte enestas io en la koncerna kampo
 *            --  N - adiciu la numerojn en la koncerna kampo.
 *   - [3] - arangxo ('l', 'r', 'z' - vidu ĉe $kolumnoj - [3].)
 *
 * @param string $identifikilo
 *           estas uzata kiel identigilo por memori la parametrojn de
 *           iu serĉado en la sesio. Por ĉiu $identifikilo ni memoras
 *           po la lastan opon da parametroj, kiuj estos uzata poste por
 *           aliaj ordigoj de la rezulto-tabelo.
 *
 * @param string $extra  aldonaj parametroj. Se tiaj ne ekzistas, eblas uzi 0.
 *      Alikaze estu array, kies sxlosiloj estu iuj el la sekve
 *      menciitaj. La valoroj havas ĉiam apartajn signifojn.
 *    - <samp>[Zeichenersetzung]</samp>
 *                 ebligas la anstataŭigon
 *                  de la valoroj per iu ajn teksto (aŭ HTML-kodo).
 *                la valoro estu array, kiu enhavu por ĉiu kolumno, kie
 *                okazu tia anstataŭigo (sxlosilo=numero de la kolumno,
 *                komencante per 0), plian array, kiu enhavu ĉiun
 *                anstataŭotan valoron kiel sxlosilo, la anstataŭontan
 *                valoron kiel valoro. Ekzemplo:<code>
 *       array('1' => array('j'=>'&lt;b><font color=green>prilaborata',
 *                          ''=>'&lt;b>&lt;font color=red>neprilaborata',
 *                          'n'=>'&lt;b>&lt;font color=red>neprilaborata'))</code>
 *          En kolumno 1 (en la teksto enmetota por XXXXX) ĉiu 'j' estas
 *          anstataŭita per "prilaborata", ĉiu '' kaj 'n' per "neprilaborata".
 *          En aliaj kolumnoj ne okazos tia anstataŭo.
 *    - [anstatauxo_funkcio]
 *               funkcias simile kiel "Zeichenersetzung",
 *               sed anstataŭ anstataŭa array() estu nomo de funkcio,
 *               kio estos vokata por eltrovi la valoron.
 *               Ĝi nur estos vokota unufoje por la tuta kampo, ne por
 *               ĉiu litero de ĝi.
 *    - [okupigxtipo]
 *               anstataŭigu en iu kolumno la okupiĝtipvaloron per
 *                    la nomon de tiu tipo.
 *               La valoro estu kolumnonumero. La valoro de la koncerna
 *               datumbazkampo estos donita al la funkcio okupigxtipo()
 *               (en iloj_sql), kaj ties rezulto estas la teksto en tiu
 *               kolumno.
 *            <strong>Tiu funkcio malaperos</strong>, anstataux 
 *               <code>'okupigxtipo' => 7</code>
 *            uzu (samefike):
 *               <code>anstatauxo_funkcio => (7 => 'okupigxtipo')</code>
 *
 *    - [litomanko]
 *               montru aparte, en kiuj noktoj ankoraŭ mankas litoj.
 *               La valoro estu kamponomo aŭ -numero.
 *               La valoro de tiu kampo estu partoprenidento.
 *               Je la fino de la linio (post la aliaj kolumnoj) estos
 *               montrita, en kiuj noktoj tiu partoprenanto jam havas
 *               liton, kaj en kiuj noktojn ankoraŭ mankas.
 *               Poste aperos ligilo "serĉu" al la ĉambrodisdono.
 *    - [tutacxambro]
 *               La valoro estu kamponomo aŭ -numero de kampo kun partopreno-ID.
 *               En aparta linio post ĉiu rezultlinio estos montrataj la
 *               datoj de la unua ĉambro, en kiu tiu partoprenanto loĝas.
 * @param int $csv - tipo de la rezulto. Eblaj valoroj:
 *   - 0 - HTML kun bunta tabelo
 *   - 1 - CSV (en HTML-ujo)
 *   - 2 - CSV por elsxuti, en Latina-1
 *   - 3 - CSV por elsxuti, en UTF-8
 * @param string $antauxteksto - teksto, kiu estu montrata antaŭ la tabelo.
 *                 (Ĝi estas uzata nur kun $proprakapo == 'jes').
 * @param string $almenuo se ĝi ne estas "", post la tabelo aperas ligo
 *                 "Enmeti en la maldekstra menuo", kies alklako
 *                 aldonas la rezulton en la maldekstra menuo.
 *                 Por ke tio funkciu, la sql-serĉfrazu redonu
 *                 kampojn nomitaj 'nomo', 'personanomo', 'renkNumero'
 *                 kaj 'ID' (kiu estu partoprenanto-ID).
 *               la valoro de $almenuo estos uzata kiel atentigo-teksto
 *                super la menuo.
 * @param string $proprakapo   - montras la tabelon ene de <html><body>-kadro, kun
 *                 ebla antaŭteksto. (Estas uzata nur, se $csv < 2.)
 * @todo $kolumnoj[$i][4] enhavas ligon inkluzive de la citiloj.
 *         La citilojn ni mem metu, eble ecx uzu iujn el la
 *          {@link ligu()}-funkcioj.
 * @todo Transformu en objekt-orientigitan klason
 */
function sercxu($sql, $ordigo, $kolumnoj, $sumoj, $identifikilo,
                $extra, $csv, $antauxteksto, $almenuo, $proprakapo = "jes")
{
  if ($csv<2 and $proprakapo=='jes')
	{
	  HtmlKapo();
	  eoecho ("<p>$antauxteksto</p>"); 
	}
  else if ($csv>='2')
	{
	  header("Content-Type: application/octet-stream"); //csv als Download anbieten 
	  header("Content-Disposition: attachment; filename='csv_export.txt'"); 
	}
    $eigenlink = "sercxrezultoj.php?elekto=memligo&id=$identifikilo";

	$_SESSION["memligo"][$identifikilo]["sql"] = $sql;
	$_SESSION["memligo"][$identifikilo]["antauxteksto"] = $antauxteksto; 
	$_SESSION["memligo"][$identifikilo]["kolumnoj"] = $kolumnoj;
	$_SESSION["memligo"][$identifikilo]["sumoj"] = $sumoj;
	$_SESSION["memligo"][$identifikilo]["aldone"] = $extra;
	$_SESSION['memligo'][$identifikilo]["almenuo"] = $almenuo;

	$klaso = array("para", "malpara");
	$arangxo = array("r" => "dekstren",
					 "d" => "dekstren",
					 "l" => "maldekstren",
					 "m" => "maldekstren",
					 "z" => "centren",
					 "c" => "centren");
	$inversa = array("asc" => "desc",
					 "desc" => "asc");

	if ($csv==0) 
	  { 
		echo "<table>\n"; 
		echo ("<tr class='titolo'>");
	  }

	$i = 0;
	while(isset($kolumnoj[$i]))    
	  { 
		if ($csv==0) 
		  { 
			echo "<th class='" . $arangxo[$kolumnoj[$i][3]] ."'>";
			if ($ordigo[0]==$kolumnoj[$i][0])
			  {
				ligu($eigenlink."&orderby=".$kolumnoj[$i][0]."&asc=" . $inversa[$ordigo[1]],
					 $kolumnoj[$i][1] . "<img src='bildoj/" . $ordigo[1] .
                     "_order.gif' alt='(". $ordigo[1].")' />");
			  }
			else if (is_numeric($kolumnoj[$i][0]))
			  {
				echo $kolumnoj[$i][1];
			  }
			else 
			  {
				ligu($eigenlink. "&orderby=".$kolumnoj[$i][0]."&asc=asc", $kolumnoj[$i][1]);
			  }
			echo "</th>";
		  }
		else
		  eoecho ($kolumnoj[$i][0].";");
		$i++;
	}
	if ($csv>='2')
	  echo "\n";
	else if ($csv == 1)
	  echo "<br/>\n";
	else
	  echo "</tr>\n";
	
	$result = sql_faru($sql." order by " . $ordigo[0]." ".$ordigo[1]); 

	if (DEBUG)
	{
		echo "<!-- sql-rezulto: " . var_export($result, true) . "-->";
	}
	$linionumero = 0;
	
	while ($row = mysql_fetch_array($result, MYSQL_BOTH))
	{
	  $linionumero += 1;
	  
	  if ($csv==0)
		{
		  echo "<TR  onmouseover='marku(this)' onmouseout='malmarku(this)' " .
			" class='".$klaso[$linionumero % 2]."'>\n"; // TODO: Javascript ausprobieren
		}

	  $i=0;
	  while(isset($kolumnoj[$i]))
		{
		  $kolumno = $kolumnoj[$i];
		  $temp = $kolumno[0];
		  $textinhalt = $kolumno[2];
		  $aus = $kolumno[3];
		  $linkk = $kolumno[4];
		  if ($kolumno[5]!='-1')
			$doselectWert = $row[$kolumno[5]];
		  else
			$doselectWert='-1';	
		  //echo "<TD align=$ausrichtung[$aus]>$row[$temp]</TD>"; 
		  if ($csv==0)
              echo "<td class='" . $arangxo[$aus] . "'>"; 
		  if ($linkk!='' and $csv==0)  
			{ 
			  $ausgeben = str_replace('XXXXX',$row[$temp],$linkk);  
              // TODO: metu citilojn!
			  echo "<A href=".$ausgeben;
			  if ($doselectWert && $doselectWert != '-1') 
				echo " onClick='doSelect(" . $doselectWert. ");'"; 
			  echo ">"; 
			}
		  $ze = $extra['Zeichenersetzung'];
		  if (isset($ze[$i][$row[$temp]]))
			{
			  $ausgeben = str_replace('XXXXX',
									  $ze[$i][$row[$temp]],
									  $textinhalt);	    
			}
		  else if (isset($extra['anstatauxo_funkcio'][$i]))
			{
			  $nova_teksto = $extra['anstatauxo_funkcio'][$i]($row[$temp]);
			  $ausgeben = str_replace('XXXXX',
									  $nova_teksto,
									  $textinhalt);	    
			}
		  else
			$ausgeben = str_replace('XXXXX',$row[$temp],$textinhalt); 
		  if (isset($extra['okupigxtipo'])and $extra['okupigxtipo']==$i)
			{$ausgeben = okupigxtipo($row[$temp]);}
		 
		  if ($csv!='2')
			eoecho ($ausgeben); // eldonas en UTF-8 (?)
		  else
			echo (utf8_decode($ausgeben)); // eldonas en ISO-8859-1
		  if ($csv!=0)
			echo ";";
		  else if ($linkk!='')
			echo "</a></td>\n";
		  else
			echo "</td>\n";
		  //Zusammenzählen 
		 

		  $SummenIndex=0; 
		  while (isset($sumoj[$SummenIndex]))
			{ 
			  $ii = $i;
			  switch($sumoj[$SummenIndex][$i][1])
				{
				case 'A':
				  $summe[$SummenIndex][$i] += 1;
				  break;
				case 'J':
				  if ($row[$temp] == 'J')
					{
					  $summe[$SummenIndex][$i]+=1;
					}
				  break;
				case 'Z':
				  if ($row[$temp] != 0)
				  {
					$summe[$SummenIndex][$i] += 1;
				  }
				  break;
				case 'E':
				  if ($row[$temp]!='')
					{
					  $summe[$SummenIndex][$i]+=1;
					}
				  break;
				case 'N':
				  if ($row[$temp]!='')
					{
					  $summe[$SummenIndex][$i]+=$row[$temp];
					}
				  break;
				}
			  $SummenIndex+=1;
			}

		  $i ++; 
		}


	  if ($csv==0 and isset($extra['litomanko']))
		{
            // TODO: eble metu en apartan funkcion (kiu povus esti aparte uzebla)
		  $manko=eltrovu_litojn($row[$extra['litomanko']]);
		  for ($i=1;$i<=7;$i++) // TODO: 7 (= Anzahl der Nächte) aus Datenbank ziehen
			{
			  if ($manko[$i]=='1')
				echo "<td>X</td>";
			  else
				echo "<td>-</td>";
			}
		  echo "<td>";
		  rajtligu ("cxambroj.php?cx_ago=forgesu&partoprenidento=".$row[$extra['litomanko']],"serc^u","","cxambrumi");
		  echo "</td>";
		}
	  if ($csv==0 and isset($extra['tutacxambro']))
		{  
		  $partoprenanto = new Partoprenanto($row[0]);
		  $partopreno = new Partopreno($row[1]);
		  // echo "CX: ".eltrovu_cxambrojn($row[$extra['tutacxambro']]);
		  $cxambro = mysql_fetch_array(eltrovu_cxambrojn($row[$extra['tutacxambro']]));
		  if ($cxambro[0]!='')
			{
			  echo "</tr>\n<tr><td colspan='".(count($kolumnoj)) . "'>";
			  montru_kunlogxantojn($cxambro[0]);
			  montru_cxambron($cxambro[0],$_SESSION["renkontigxo"],$partoprenanto,
							  $partopreno,'granda');
			  echo "</td>\n";
			}
		}
   
 
	  if ($csv==0)
		echo "</tr>\n"; 
	  else if ($csv==1)
		echo "<br/>\n";
	  else
		echo "\n"; 		
	} 


  if ($csv==0)  
	{ 

	  // sumoj
	  // TODO: rerigardu indeksojn

	  $SummenIndex=0; 
	  while (isset($sumoj[$SummenIndex][0]))
		{ 
		  echo "<tr class='sumoj'>";
		  $i=0; 
		  while(isset($sumoj[$SummenIndex][$i]))
			{ 
			  $aus = $sumoj[$SummenIndex][$i][2]; 
			  echo "<td class='$arangxo[$aus]'>".
				str_replace('XX',$summe[$SummenIndex][$i],$sumoj[$SummenIndex][$i][0]).
				"</td>";
			  $i+=1; 
			}
		  echo "</tr>\n";
		  $SummenIndex+=1;      
		}     
	  echo "</table>\n";       
	  if ($almenuo!="")
		{ 
            ligu("menuo.php?sercxfrazo=".$sql."&listotitolo=".$almenuo,
                 "Enmeti en la maldekstran menuon",
                 "is-aligilo-menuo");
		}
	  if ($proprakapo == "jes")
		{
		  echo "</body>\n</html>"; 
		}
    } 

}  // sercxu()


?>
