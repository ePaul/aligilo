<?php

  /**
   * Funkcioj por trakti cxambrojn.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   * @todo ordigado de la tuta dosiero
   */




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
 * @param int $id partopreno-idento.
 * @return mysqlres La rezulto enhavos la jenajn kampojn:
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
 * @todo La nomo ne tute tauxgas, eble elpensu pli bonan.
 *
 * @param int $id identigilo de partoprenanto
 * @return array array() de la formo
 *                              0 => sumo,
 *                              1 => 1/0 (cxu lito en nokto 1?),
 *                              2 => 1/0 (cxu lito en nokto 2?),
 *                              ...,
 *                              'sumo' => sumo
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
 * Montras tabelon de cxiuj noktoj de la renkontigxo, en kiu
 *  tiu partopreno jam havas kaj ankoraux ne havas liton.
 *
 * @param int $ppenoID identigilo de Partopreno - gxi estu de la aktuala
 *                      renkontigxo, alikaze la rezulto estas
 *                      stultajxo.
 */
function montru_litojn_de_ppeno($ppenoID) {

    $manko=eltrovu_litojn($ppenoID);

    $dauxro = $_SESSION['renkontigxo']->renkontigxonoktoj();

    for ($i=1;$i<=$dauxro;$i++)
        {
            if ($manko[$i]=='1')
				echo "<td>X</td>";
            else
				echo "<td>-</td>";
			}
		  echo "<td>";
		  rajtligu ("cxambroj.php?cx_ago=forgesu&partoprenidento=".$ppenoID,
                    "serc^u","","cxambrumi");
		  echo "</td>";
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

function formularo_por_bazaj_cxambroinformoj($cxambro) {
    echo "<form action='cxambro-detaloj.php?cxambronumero=" . $cxambro .
        "' method='post'>\n";
    entajpbutono("(","tipo",$row[tipo],"g","g",'gea');
    entajpbutono("","tipo",$row[tipo],"v","v",'vira');
    entajpbutono("","tipo",$row[tipo],"i","i",'ina)');
    entajpbokso  ("[",dulita,$row[dulita],J,J,"dulita c^ambro]");
    entajpejo("<BR>Rimarkoj:","rimarkoj",$row[rimarkoj],20);
    butono('cxambrotipsxangxo', "S^ang^u");
    echo "</form>";
}


/**
 * Montras la aktualan staton de cxambro.
 *  $grandeco: - se ne donita aux "malgranda", montras nur 
 *             - se "granda", ...
 * verda se estas tauxga
 * rugxa se ne tauxgas (pro sekso)
 *
 * @todo transformu en pli bone uzeblan funkcio(j)n
 */
function montru_cxambron($cxambro, $renkontigxo, $partoprenanto,
                         $partopreno, $grandeco="malgranda", $reenligo="")
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
      formularo_por_bazaj_cxambroinformoj();
  }
  
  $renkontigxdauxro = $renkontigxo->renkontigxonoktoj();
  $partoprentagoj   =
      is_object($partopreno) ? $partopreno->partoprennoktoj() : 0;

  echo "<form action='cxambroago.php' method='POST'>\n";

  tenukasxe("cxambronumero", $cxambro);

  if (!$reenligo) {
      if ($_SERVER['REQUEST_METHOD'] == "GET") {
          $reenligo = $_SERVER['PHP_SELF'];
          if ($_SERVER['QUERY_STRING']) {
              $reenligo .= "?" . $_SERVER['QUERY_STRING'];
          }
      }
  }

  if ($reenligo) {
      tenukasxe("reiru", $reenligo);
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
                          $uzata = true;
                          $noktoj +=
                              metu_partoprenant_litan_keston($r,
                                                             $noktoj,
                                                             $partopreno->datoj['ID'],
                                                             $grandeco);
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
 * metas tabelcxelon por lito-uzo de unu partoprenanto.
 *
 * @param array  $rezervinformoj (rezulto de {@link uzata_cxambro})
 * @param int    $nokto         numero de la nokto
 * @param int    $partoprenoID  identigilo de tiu partopreno, por kiu
 *                             ni estas sercxanta liton (aux kiun ni
 *                             rigardas)
 * @param string $grandeco     aux "granda" aux io alia.
 */
function metu_partoprenant_litan_keston($rezervinformoj, $nokto,
                                        $partoprenoID, $grandeco) {
    if($rezervinformoj['rezervtipo'] == 'd')
        {
            $klaso = 'disdonita';
        }
    else if ($rezervinformoj['rezervtipo'] == 'r')
        {
            $klaso = 'rezervita';
        }
    else
        {
            darf_nicht_sein("rezervtipo: '" .
                            $rezervinformoj['rezervtipo'] . "'");
        }

    $diferenco = $rezervinformoj['nokto_gxis']-$nokto;
    
    if ($rezervinformoj['ID'] == $partoprenoID)
        {
            $klaso .= " mialito";
        }
    
    echo "<td class='".$klaso."' colspan='".($diferenco + 1)."'>";

    $loka_partoprenanto =
        new Partoprenanto($rezervinformoj['partoprenantoID']);
    $loka_partopreno =
        new Partopreno($rezervinformoj['ID']);


    if ($grandeco == 'granda' or $diferenco > 3)
        {
            $teksto = $loka_partoprenanto->tuta_nomo() .
                " (".$loka_partoprenanto->landonomo()."/".
                $loka_partoprenanto->datoj['sekso']."/".
                $loka_partopreno->datoj['agxo']."/".
                $loka_partopreno->datoj['cxambrotipo'].")"; 
            ligu("partrezultoj.php?partoprenidento=".$rezervinformoj['ID'],
                 $teksto);
            if ($grandeco == 'granda') {
                // ecx pli granda ...
                                  
                // ni eluzas, ke nia CSS-klaso samtempe estas
                // la gxusta vorto (:-) 
                echo '<br/> ('.$klaso.')';
                $forgesu_butono =
                    $rezervinformoj['rezervtipo'] == 'r'?
                    "malrezervu" : "elj^etu";
                $disdonu_butono = "disdonu";
                                  
            }
            else {
                // mezgranda
                $forgesu_butono = "for";
                $disdonu_butono = 'donu';
            }
        }
    else
        {
            // malgranda
                        
            ligu("partrezultoj.php?partoprenidento=".$rezervinformoj['ID'],
                 $rezervinformoj['rezervtipo']);
            $forgesu_butono = 'x';
            $disdonu_butono = 'd';
        }
    echo " ";
    butono($rezervinformoj['litoID'], $forgesu_butono,
           'forgesu_liton');
    if ($rezervinformoj['rezervtipo'] == 'r') {
        butono($rezervinformoj['litoID'], $disdonu_butono,
               'disdonu_rezervitan_liton');
    }
                
    echo "</td>";
    return $diferenco;

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
      //    $listo[$row['ID']] = $row['nomo'];
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
      "<a href='cxambro-detaloj.php?cxambronumero=".$row[ID]."'>".$row[nomo].
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

  montru_cxambrointersxangxilon();

//   reset($listo);
//   echo "<form action=\"cxambroj.php?cxambronombro=$cxambro\" method=\"post\">\n";
//   eoecho ("S^ang^u de c^ambro:\n");
//   echo "<select name=\"de\" size=1>\n";
//   while  (list($k, $v) = each($listo))
//   {
//     eoecho( "  <option value = \"$v\">$k</option>\n");
//   }
//   echo "</select>\n";
//   eoecho ("al:\n");
//   reset($listo);
//   echo "<select name=\"al\" size=1>\n";
//   while  (list($k, $v) = each($listo))
//   {
//     eoecho("  <option value = \"$v\">$k</option>\n");
//   }
//   echo "</select>\n";
//   send_butono("Nun!");

}

/**
 * montras formulareton por intersxangxi la logxantojn de du cxambroj.
 * @param string $unua identigilo de la unua cxambro. Se mankas,
 *                    ni montras ankaux por tiu elektilon.
 */
function montru_cxambrointersxangxilon($unua=0) {
  echo "<form action='cxambroago.php' method='post'>\n";
  eoecho ("<p>S^ang^u la log^antojn inter c^ambro:\n");

  $restriktoj = array("renkontigxo = '" . $_SESSION['renkontigxo']->datoj['ID'] . "'");

  if ($unua) {
      tenukasxe('de', $unua);
      echo $unua;
  }
  else {
      elektilo_simpla_db('de', 'cxambroj', 'nomo', 'ID', '', $restriktoj);
      /*
      echo "<select name=\"de\" size=1>\n";
  while  (list($k, $v) = each($listo))
  {
    eoecho( "  <option value = \"$v\">$k</option>\n");
  }
  echo "</select>\n";
  }
      */
  }
  eoecho (" kaj \n");
      elektilo_simpla_db('al', 'cxambroj', 'nomo', 'ID', '', $restriktoj);
  butono('intersxangxo', "Nun!");
  echo "</p></form>\n";

}


//cxu la cxambro tauxgas por la partoprenanto
function tauxgas($deztipo,$sekso,$tipo)
{
  //echo "$deztipo,$sekso,$tipo";
  return ($deztipo=='u' and $tipo==$sekso[0])
	or ($deztipo=='g'and ($tipo=='g' or $tipo==$sekso[0]));
}


?>
