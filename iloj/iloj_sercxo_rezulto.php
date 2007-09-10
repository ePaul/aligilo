<?php

/**
 * funkcios preskaux same kiel sercxu() (el iloj_html), sed kun nova implementado.
 *
 * Diferencoj:
 *
 * $sumoj - por cxiu sum-linio ekzistas array (en $sumoj). En cxiu linio-array
 *      estas po element-array por kolono, kun tri elementoj:
 *   [0] - La teksto de la kampo. Se vi uzas XX, tie aperos la rezulto
 *         de la sumado.
 *   [1] - La speco de la sumado. eblecoj:
 *              A - simple nur kalkulu, kiom da linioj estas.
 *              J - kalkulu, kiom ofte aperas 'J' en la koncerna kampo
 *              E - kalkulu, kiom ofte enestas io en la koncerna kampo
 *              N - adiciu la numerojn en la koncerna kampo.
 *   [3] - arangxo ('l', 'r', 'z' - vidu cxe $kolumoj - [3].)
 * $identifikilo - estas uzata por la ligoj por reordigi, tiel cxe pluraj
 *                 tabeloj en la sama pagxo la gxusta estos montrita.
 * $extra - ['Spaltenrechnung'] ne plu funkcias.
 *
 */
function sercxu_nova($sql, $ordigo, $kolumnoj, $sumoj, $identifikilo,
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
					 $kolumnoj[$i][1] . "<img src='bildoj/".$ordigo[1]."_order.gif' />");
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
		var_export($result);
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
		  if ($csv==0) echo "<td align=$ausrichtung[$aus]>"; 
		  if ($linkk!='' and $csv==0)  
			{ 
			  $ausgeben = str_replace('XXXXX',$row[$temp],$linkk);  
			  echo "<A href=".$ausgeben;	  
			  if ($doselectWert && $doselectWert != '-1') 
				echo " onClick='doSelect(" . $doselectWert. ");'"; 
			  echo ">"; 
			}
		  $ez &= $extra['Zeichenersetzung'];
		  if (isset($ez[$i][$row[$temp]]))
			{
			  $ausgeben = str_replace('XXXXX',
									  $extra['Zeichenersetzung'][$i][$row[$temp]],
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
		  $manko=eltrovu_litojn($row[$extra['litomanko']]);
		  for ($i=1;$i<=7;$i++) // TODO: 7 (= Anzahl der Nächte) aus Datenbank ziehen
			{
			  if ($manko[$i]=='1')
				echo "<td>X</td>";
			  else
				echo "<td>-</td>";
			}
		  echo "<td>";
		  rajtligu ("cxambroj.php?cx_ago=forgesu&partoprenID=".$row[$extra['litomanko']],"serc^u","","cxambrumi");
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

}  // sercxu_nova()


?>
