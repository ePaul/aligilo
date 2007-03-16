<?php

// TODO: mal aufräumen

/* ######################################################################### */
/* Tio cxi dosiero enhavas multajn bezonatajn funkciojn por trakti cxambrojn */
/* ######################################################################### */

/**
 * eltrovas, kiu jam rezervis/ricevis liton en nokto.
 *
 * La rezulto estas array kun la SQL-rezulto.
 * TODO: klarigo de la parametroj, klarigo de la rezulto.
 */
function cxambro_uzata($cxambro,$nokto,$litonumero)
{
  $sql = datumbazdemando(array("partoprenantoID", "rezervtipo", "nokto_gxis",
                               "partopreno", "p.ID"),
                         array("litonoktoj" => "ln",
                               "partoprenoj" => "p"),
                         array("p.ID = partopreno",
                               "ln.cxambro = '$cxambro'",
                               "litonumero = '$litonumero'",
                               "nokto_de <= '$nokto'",
                               "'$nokto' <= nokto_gxis")
                         );
  return mysql_fetch_array(sql_faru($sql), MYSQL_BOTH);
}

/**
 * Eltrovas, kiuj cxambroj (kaj litoj) por la partoprenanto
 * (laux partoprenidento) rezervigxis.
 * Redonas mysql-objekton kun farita demando, oni nur devas
 * akiri la rezultojn (per mysql_fetch_array k.s.).
 */
function eltrovu_cxambrojn($id)
{
  return sql_faru( datumbazdemando(array("cxambro", "nokto_de", "nokto_gxis",
                                         "rezervtipo", "ID"),
                                   "litonoktoj",
                                   "partopreno = '$id'",
                                   "",
                                   array("order" => "nokto_de")
                                   ));
}

/**
 * eltrovas, kiom da litoj por la donita partopreno
 * jam rezervigxis - laux nokto kaj entute.
 *
 * La nomo ne tute tauxgas ...
 */
function eltrovu_litojn($id)
{
  $rezulto = eltrovu_cxambrojn($id);

  $entute=0;
  while ($row = mysql_fetch_array($rezulto, MYSQL_BOTH))
  {
    for ($i=$row[nokto_de];$i<=$row[nokto_gxis];$i++)
    {
     $manko[$i]="1";
     $entute++;
    }
  }
  $manko["sumo"] = $entute;
  $manko[0] = $entute;

  return $manko;
}

/**
 * Montras cxiujn kunlogx-dezirojn de la
 * homoj, kiuj nun estas en la cxambro.
 */
function montru_kunlogxantojn($cxambro)
{
  $rezulto = sql_faru(datumbazdemando(array("partoprenantoID", "partopreno",
                                            "p.ID", "kunkiu", "kunkiuID", "cxambrotipo",
											"personanomo", "nomo", "sekso"),
                                      array("litonoktoj" => "ln",
											"partoprenantoj" => "pa",
                                            "partoprenoj" => "p"),
                                      array("p.ID = partopreno",
											"p.partoprenantoID = pa.ID",
                                            "ln.cxambro = '$cxambro'")));
  eoecho ("<BR><B>Kunlog^as:</B><BR> ");
  while ($row = mysql_fetch_array($rezulto, MYSQL_BOTH))
  {

    eoecho ($row[personanomo]." ".$row[nomo]." (".$row[sekso]);
	eoecho (" / ".$row[cxambrotipo].")");
    if ($row[kunkiu]!='')
    {
	  
	  $kunlogxanto=new Partoprenanto($row[kunkiuID]);
	  eoecho (" s^atas log^i kune kun: <em>".$row[kunkiu]." (".
			  $kunlogxanto->datoj[personanomo]." ".$kunlogxanto->datoj[nomo].")</em>");
    }
    echo "<br/>";
  }
  echo "<br/>";
}

/**
 * Montras la aktualan staton de cxambro.
 *  $grandeco: - se ne donita aux "malgranda", montras nur 
 *             - se "granda", ...
 * verda se estas tauxga
 * rugxa se ne tauxgas (pro sekso)
 */
function montru_cxambron($cxambro,$renkontigxo,$partoprenanto,$partopreno,$grandeco="malgranda")
{
  $cxam_sql = datumbazdemando(array("litonombro", "nomo", "tipo",
                                    "etagxo", "dulita", "rimarkoj"),
                              "cxambroj",
                              "ID = '$cxambro'");
  $cxam_rezulto = sql_faru($cxam_sql);

  $row = mysql_fetch_array($cxam_rezulto, MYSQL_BOTH);
  if (tauxgas($partopreno->datoj[cxambrotipo],$partoprenanto->datoj[sekso],$row[tipo]))
	$koloro="green";
  else if ($row[tipo]=='')
	$koloro="black";
  else
	$koloro="red";
  if ($grandeco == "granda")
  {
    ligu ("cxambroj.php?etagxo=".$row[etagxo],"Etag^o ".$row[etagxo]);
    echo " |";
  }
  ligu ("cxambroj.php?cxambronombro=$cxambro","C^ambro: $row[nomo]");
  echo "<font color=$koloro><strong>";
  if ($row[tipo] == "v") echo " (vira)";
  else if ($row[tipo] == "i") echo " (ina)";
  else if ($row[tipo] == "g") echo " (gea)";
  else echo " (nedifinita)";
  echo "</strong></font>\n";

  rajtligu("kreu_cxambron.php?id=$cxambro", $grandeco =='granda' ? "redaktu bazajn informojn" : "red.", "", "teknikumi", "ne");
  
  if ($grandeco == "granda")
  {
    //sxangxi la cxambrotipon
    echo "<form action='cxambroj.php?cxambronombro=$cxambro' method='post'>";
    entajpbutono("(","tipo",$row[tipo],"g","g",'gea');
    entajpbutono("","tipo",$row[tipo],"v","v",'vira');
    entajpbutono("","tipo",$row[tipo],"i","i",'ina)');
    entajpbokso  ("[",dulita,$row[dulita],J,J,"dulita c^ambro]");
    entajpejo("<BR>Rimarkoj:","rimarkoj",$row[rimarkoj],20);
    send_butono("Ek!");
    echo "</form>";
  }
  
  $renkontigxdauxro = kalkulu_tagojn( $renkontigxo->datoj[de], $renkontigxo->datoj[gxis] );
  $partoprentagoj   = kalkulu_tagojn( $partopreno->datoj[de], $partopreno->datoj[gxis] );

  if ( ($partoprenanto->datoj[ID])
       and ($grandeco == "granda")
       )
  {
    echo "<form ACTION='cxambroj.php?cxambronombro=$cxambro' METHOD='POST'>\n";
  }

  echo "<Table border><TR><TD>Nokto: ";

  if ( /*($partoprenanto->datoj[ID])
       and */
       ($grandeco == "granda")
       )
  {
    $manko = eltrovu_litojn( $partopreno->datoj[ID]);
    $ar=JMTdisigo($renkontigxo->datoj[de]);
    $tago=$ar[tago];

    for ($noktoj = 1; $noktoj <= $renkontigxdauxro; $noktoj++)
    {
      $ar = JMTdisigo( sekvandaton($renkontigxo->datoj[de], $noktoj) );
      $sektago = $ar[tago];
      echo "<TD align=\"center\"> $tago / $sektago";
      $tago = $sektago;
    }
    echo "<TD><TD align=\"center\">tuta tempo";
  }
  else
  {
    for ($noktoj = 1;$noktoj <= $renkontigxdauxro;$noktoj++)
    {
      echo "<TD>$noktoj</noktoj>";
    }
  }

  for ($litoj = 1; $litoj <= $row[litonombro]; $litoj++)
  {
    echo "<TR valign = center>\n<TD nowrap>Lito: ".$litoj;
    $uzata = false;
    for ($noktoj = 1; $noktoj <= $renkontigxdauxro; $noktoj++)
    {
      $r = cxambro_uzata($cxambro,$noktoj,$litoj);
      $diferenco = $r[nokto_gxis]-$noktoj;

      echo "<TD align = center ";
      if ($diferenco > 0)
      {
        $noktoj += $diferenco++; //TODO:? hehe [respondo de Martin:] Das ist einfach nur eine geniale Funktion.

        echo "colspan = $diferenco ";
      }
      if ($r[rezervtipo] == "d")
      {
        echo " bgcolor=green> ";
        $uzata = true;
      }
      else if ($r[rezervtipo]=="r")
            {
              echo " bgcolor=yellow> ";
              $uzata = true;
            }
      else if ( ($partoprenanto)
                and ($grandeco == "granda")
                and ( sekvandaton($renkontigxo->datoj[de], $noktoj-1) >= $partopreno->datoj[de])
                and ( sekvandaton($renkontigxo->datoj[de], $noktoj) <= $partopreno->datoj[gxis])
                and ($manko[$noktoj] != "1")
               )
           {
             echo "";
             entajpbokso(">","nokto[$noktoj]","falseoderso","","$litoj","","","ne");
           }
      else
      {
        echo " bgcolor=white>--";
      }
      echo "<A href = \"partrezultoj.php?partoprenidento=$r[3]&partoprenantoidento=$r[0]\" onClick=\"doSelect($r[0]);\">";
      
      if ( (($grandeco == "granda") or ($diferenco > 3))
           and ($r[partoprenantoID]))          
      {
        $loka_partoprenanto = new Partoprenanto($r[partoprenantoID]);
        $teksto = $loka_partoprenanto->datoj[personanomo] ." ".$loka_partoprenanto->datoj[nomo].
		  " (".eltrovu_landon($loka_partoprenanto->datoj[lando])." / ".$loka_partoprenanto->datoj[sekso]."/".$loka_partopreno->datoj[agxo].")"; 
        if ($grandeco =="granda") eoecho ($teksto."<BR> ");
          else eoecho ($teksto);       //eoecho (/*substr*/($teksto,0,$diferenco*3));       
      }
      if ((($diferenco > 3)and (!$r[partoprenantoID])) or  ($grandeco == "granda"))  // 4 tago devus esti suficxe largxe por plena skribado
      {
        switch ($r[rezervtipo])
        {
          case "d": echo "disdonita";break;
          case "r": echo "rezervita";break;
        }
      }
      else if ($diferenco < 4)
      {
        echo $r[rezervtipo];
      }
      
      echo "</A>";
    }
    if ( ($partoprenanto)
         and ($grandeco == "granda")
         )
    {
      echo "<TD>&nbsp;&nbsp;<TD align=center>";
      if ( ($uzata == false)
           and ($partoprentagoj == $renkontigxdauxro)
           and ($manko[sumo] == 0))
      {
        entajpbokso("","tute","falseoderso","","$litoj","","","ne");
      }
    }
  }
  echo "</Table>";
  if ( ($partoprenanto->datoj[ID])
       and ($grandeco == "granda")
       )
  {
    echo "<select name=\"tipo\" size=1>\n";
    echo "<option selected>rezervi\n";
    echo "<option>disdoni\n";
    echo "</select><BR>\n";

    send_butono("Faru!");
    echo "</FORM>";
  }
  if ($grandeco != "granda")
     eoecho ($row[rimarkoj]);

}

/**
 * Montras cxiujn cxambrojn lauxetagxe.
 *
 *  $deziratatipo - aux 'u' (unuseksa) aux 'g'  (gea)
 *  $sekso        - aux 'vira' aux 'malina'.
 *
 *  La parametroj estas uzata por kolorigi la
 *  cxambrojn laux tauxgeco.
 *
 */
function montru_laux_etagxoj($deziratatipo='',$sekso='')
{
  $klaso = array("para", "malpara");
  $zaehler = 0; 
  $etagxoj = 0;
  
  $cxam_sql = datumbazdemando(array("ID", "nomo", "litonombro",
                                    "etagxo", "rimarkoj", "tipo"),
                              "cxambroj",
                              "",
                              "renkontigxo",
                              array("order" => "etagxo, nomo")
                              );
  $cxam_rezulto = sql_faru($cxam_sql);
  $etagxoj_per_linio = 3;
  echo '<table border="0" valign="top" width="60%">'."\n<tr>\n";
  $et = '#';  // nomo de la aktuala etagxo
  while  ($row = mysql_fetch_array($cxam_rezulto, MYSQL_ASSOC))
  {
    $listo[$row[nomo]] = $row[ID];
    if ($row[etagxo]!=$et) // ni komencu novan etagxon
    {
      if ($et!='#')
        echo "</table></td>\n";  // sed antauxe finu la malnovan etagxon (kiu havas subtabelon).
      $zaehler=0;
      $et=$row[etagxo];
      $etagxoj ++;
      if ($etagxoj>$etagxoj_per_linio)
      {
        echo("</tr><tr>\n");  // post kelkaj subtabeloj ni komencu novan linion
        $etagxoj=1;
      }
      eoecho ("<td nowrap>\n".
              "<table border=1 width=100%>\n".
              '<tr><td nowrap="nowrap" colspan="2"><b>Etag^o');
      ligu ("cxambroj.php?etagxo=".$row[etagxo],$row[etagxo]);
      echo "</td></tr>\n";
    }

    if (tauxgas($deziratatipo,$sekso,$row[tipo]))
      $koloro=" tauxga";
    else if ($row[tipo]=='' or $deziratatipo == '')
      $koloro="";
    else
      $koloro=" maltauxga";
    eoecho( "<tr class='".$klaso[$zaehler % 2].$koloro."'>\n" .
      "  <td align=center>".
      "<a href='cxambroj.php?cxambronombro=".$row[ID]."'>".$row[nomo].
      "</a></td>\n".
      "  <td width=40>litoj: ".$row[litonombro]);
	rajtligu("kreu_cxambron.php?id=".$row[ID], "(red.)", "", "teknikumi", "ne");
	echo("</td><td><strong>");
    if ($row[tipo] == "v") echo " (vira)";
     else if ($row[tipo] == "i") echo " (ina)";
     else if ($row[tipo] == "g") echo " (gea)";
     else echo " (nedifinita)";
    eoecho ("</strong></td></tr>\n".'<tr class="'.$klaso[$zaehler % 2]. '"><td colspan="3">'.
            $row[rimarkoj]);
	echo ("</td></tr>\n");
    $zaehler++;
  }
  echo "</table></td>\n"; // finu la lastan subtabelon
  echo "</tr></table>\n"; // finu la cxeftabelon

  //sxangxu cxambrojn

  reset($listo);
  echo "<form action=\"cxambroj.php?cxambronombro=$cxambro\" method=\"post\">\n";
  eoecho ("S^ang^u de c^ambro:\n");
  echo "<select name=\"de\" size=1>\n";
  while  (list($k, $v) = each($listo))
  {
    eoecho( "  <option value = \"$v\">$k</option>\n");
  }
  echo "</select>\n";
  eoecho ("al:\n");
  reset($listo);
  echo "<select name=\"al\" size=1>\n";
  while  (list($k, $v) = each($listo))
  {
    eoecho("  <option value = \"$v\">$k</option>\n");
  }
  echo "</select>\n";
  send_butono("Nun!");

}
//cxu la cxambro tauxgas por la partoprenanto
function tauxgas($deztipo,$sekso,$tipo)
{
  //echo "$deztipo,$sekso,$tipo";
  return ($deztipo=='u' and $tipo==$sekso[0])
	or ($deztipo=='g'and ($tipo=='g' or $tipo==$sekso[0]));
}


?>
