<?php

// TODO: ordigado de la tuta dosiero

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
                               'ln.ID' => 'litoID',
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
 *
 * La rezulto enhavos la jenajn kampojn:
 *
 *   cxambro
 *   nokto_de
 *   nokto_gxis
 *   rezervtipo
 *   ID
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
  while ($row = mysql_fetch_assoc($rezulto))
  {
    for ($i=$row['nokto_de'];$i<=$row['nokto_gxis'];$i++)
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
 * redonas array() de la numeroj de tiuj noktoj, en
 * kiu $partopreno ankoraux ne havas liton.
 */
function eltrovu_litomankon($partopreno, $renkontigxo)
{
    $mankantaj_litoj = array();
    $de = kalkulu_tagojn($renkontigxo->datoj['de'],
                         $partopreno->datoj['de']) + 1;
    $gxis = kalkulu_tagojn($renkontigxo->datoj['de'],
                           $partopreno->datoj['gxis']);
    debug_echo("<!-- de: " . $de . ", gxis: " . $gxis . "-->");
    $noktoj_kun_lito = eltrovu_litojn( $partopreno->datoj['ID']);
    debug_echo("<!-- noktoj_kun_lito: " . var_export($noktoj_kun_lito, true) .
               "-->");
    for($i =$de ; $i <= $gxis ; $i++)
        {
            if (! $noktoj_kun_lito[$i])
                $mankantaj_litoj[]= $i;
        }
    return $mankantaj_litoj;
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
	$koloro="malaverto";
  else if ($row[tipo]=='')
	$koloro="";
  else
	$koloro="averto";
  if ($grandeco == "granda")
  {
    ligu ("cxambroj.php?etagxo=".$row[etagxo],"Etag^o ".$row[etagxo]);
    echo " |";
  }
  ligu ("cxambroj.php?cxambronombro=$cxambro","C^ambro: $row[nomo]");
  echo "<strong class='$koloro'>";
  if ($row['tipo'] == "v")
      echo " (vira)";
  else if ($row['tipo'] == "i")
      echo " (ina)";
  else if ($row['tipo'] == "g")
      echo " (gea)";
  else
      echo " (nedifinita)";
  echo "</strong>\n";

  rajtligu("kreu_cxambron.php?id=$cxambro", $grandeco =='granda' ? "redaktu bazajn informojn" : "red.", "", "teknikumi", "ne");
  
  if ($grandeco == "granda")
  {
    //formularo por sxangxi la cxambrotipon
    echo "<form action='cxambroj.php?cxambronombro=$cxambro' method='post'>";
    entajpbutono("(","tipo",$row[tipo],"g","g",'gea');
    entajpbutono("","tipo",$row[tipo],"v","v",'vira');
    entajpbutono("","tipo",$row[tipo],"i","i",'ina)');
    entajpbokso  ("[",dulita,$row[dulita],J,J,"dulita c^ambro]");
    entajpejo("<BR>Rimarkoj:","rimarkoj",$row[rimarkoj],20);
    send_butono("Ek!"); // TODO: ago-nomo (vidu cxambroj.php)
    echo "</form>";
  }
  
  $renkontigxdauxro = $renkontigxo->renkontigxonoktoj();
  $partoprentagoj   = $partopreno->partoprennoktoj();

  if ($grandeco == "granda")
      {
          echo "<form action='cxambroj.php?cxambronombro=$cxambro' method='POST'>\n";
      }
  else
      {
          echo "<form action='cxambroj.php' method='POST'>\n";
      }

  echo "<table class='cxambrolisto-$grandeco'><tr><th>Nokto:</th>";

  if ( $grandeco == "granda")
  {
    $manko = eltrovu_litojn( $partopreno->datoj['ID']);
    $ar=JMTdisigo($renkontigxo->datoj['de']);
    $tago=$ar['tago'];
    $estis_elektebleco = false; // cxu estis ebla elekti liton por nokto?

    for ($noktoj = 1; $noktoj <= $renkontigxdauxro; $noktoj++)
    {
      $ar = JMTdisigo( sekvandaton($renkontigxo->datoj['de'], $noktoj) );
      $sektago = $ar['tago'];
      echo "<th align='center'> $tago / $sektago </th>";
      $tago = $sektago;
    }
    if ( $partoprenanto )
        {
            // ebleco mendi tutan tempon
            echo "<td/><th>tuta tempo</th>";
        }
    echo "</th>";
  }
  else
  {
      // simpla noktolisto
    for ($noktoj = 1;$noktoj <= $renkontigxdauxro;$noktoj++)
    {
      echo "<th>$noktoj</th>";
    }
  }
  echo "</tr>";

  
  for ($litoj = 1; $litoj <= $row['litonombro']; $litoj++)
      {
          echo "<tr >\n<th class='litonomo'>Lito: ".$litoj . "</th>";
          $uzata = false;
          for ($noktoj = 1; $noktoj <= $renkontigxdauxro; $noktoj++)
              {
                  // detaloj pri la rezervado
                  $r = cxambro_uzata($cxambro,$noktoj,$litoj);
                  if ($r)
                      {
                          if($r['rezervtipo'] == 'd')
                              {
                                  $klaso = 'disdonita';
                              }
                          else if ($r['rezervtipo'] == 'r')
                              {
                                  $klaso = 'rezervita';
                              }
                          else
                              {
                                  darf_nicht_sein("rezervtipo: '" .
                                                  $r['rezervtipo'] . "'");
                              }
                          $diferenco = $r['nokto_gxis']-$noktoj;
                          $noktoj += $diferenco;

                          if ($r['ID'] == $partopreno->datoj['ID'])
                              {
                                  $klaso = $klaso . " mialito";
                              }

                      
                          echo "<td class='".$klaso."' colspan='".($diferenco + 1)."'>";

                          $loka_partoprenanto =
                              new Partoprenanto($r['partoprenantoID']);
                          $loka_partopreno =
                              new Partopreno($r['ID']);


                          if ($grandeco == 'granda' or $diferenco > 3)
                              {
                                  $teksto = $loka_partoprenanto->tuta_nomo() .
                                      " (".$loka_partoprenanto->landonomo()."/".
                                      $loka_partoprenanto->datoj['sekso']."/".
                                      $loka_partopreno->datoj['agxo']."/".
                                      $loka_partopreno->datoj['cxambrotipo'].")"; 
                                  ligu("partrezultoj.php?partoprenidento=".$r['ID'],
                                       $teksto);
                                  if ($grandeco == 'granda') {
                                      // ecx pli granda ...
                                  
                                      // ni eluzas, ke nia CSS-klaso samtempe estas
                                      // la gxusta vorto (:-) 
                                      echo '<br/> ('.$klaso.') ';
                                      $forgesu_butono =
                                          $r['rezervtipo'] == 'r'?
                                          "malrezervu" : "elj^etu";
                                      $disdonu_butono = "disdonu";
                                  
                                  }
                                  else {
                                      // mezgranda
                                      echo " ";
                                      $forgesu_butono = "for";
                                      $disdonu_butono = 'donu';
                                  }
                              }
                          else
                              {
                                  // malgranda
                        
                                  ligu("partrezultoj.php?partoprenidento=".$r['ID'],
                                       $r['rezervtipo']);
                                  echo " ";
                                  $forgesu_butono = 'x';
                                  $disdonu_butono = 'd';
                              }
                          butono($r['litoID'], $forgesu_butono, 'forgesu_liton');
                          if ($r['rezervtipo'] == 'r') {
                              butono($r['litoID'], $disdonu_butono,
                                     'disdonu_rezervitan_liton');
                          }
                
                          echo "</td>";
                      }
                  else
                      {
                          if ( ($partoprenanto)
                               and ($grandeco == "granda")
                               and ( sekvandaton($renkontigxo->datoj['de'], $noktoj-1)
                                     >= $partopreno->datoj['de'])
                               and ( sekvandaton($renkontigxo->datoj['de'], $noktoj)
                                     <= $partopreno->datoj['gxis'])
                               and ($manko[$noktoj] != "1")
                               )
                              {
                                  // ebligu mendi tiun liton por tiu nokto
                                  echo "<td class='elektebla'>";
                                  entajpbokso("","nokto[$noktoj]","falseoderso","",
                                              "$litoj","","","ne");
                                  echo "</td>";
                                  $estis_elektebleco = true;
                              }
                          else
                              {
                                  echo "<td class='malplena'>--</td>";
                              }
                      } // else

              } // for (noktoj)
          if ( ($partoprenanto) and ($grandeco == "granda") )
              {
                  // mendi cxiujn noktojn?
                  echo "<td>&nbsp;&nbsp;</td><td class='elektebla'>";
                  if ( ($uzata == false)
                       and ($partoprentagoj == $renkontigxdauxro)
                       and ($manko['sumo'] == 0))
                      {
                          entajpbokso("","tute","falseoderso","","$litoj","","","ne");
                      }
                  echo "</td>";
              }
          echo "</tr>";
      }  // for (litoj)
  echo "</table>";
  if ( ($partoprenanto->datoj['ID'])
       and ($grandeco == "granda")
       )
  {
      if ($estis_elektebleco)
          {
              // butono por rezervi
              butono('rezervu', "Rezervu elektitajn litojn");
              // butono por disdoni - TODO: aux cxu nur surloke?
              butono('disdonu', "Disdonu elektitajn litojn");
          }



//     echo "<select name='tipo' size='1'>\n";
//     echo "<option selected>rezervi\n";
//     echo "<option>disdoni\n";
//     echo "</select><BR>\n";

//     send_butono("Faru!");
  }
  echo "</form>";
  if ($grandeco != "granda")
      eoecho ($row[rimarkoj]);

} // montru_cxambron()

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
    // TODO: pleneco/malpleneco
    $sql = datumbazdemando(array("max(litonumero)" => "num"),
                           "litonoktoj",
                           array("cxambro = '" . $row['ID'] . "'",
                                 "rezervtipo")
                           );
    $linio = mysql_fetch_assoc(sql_faru($sql));
    echo "(" . ((int)$linio['num']) . ")";
                           


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
