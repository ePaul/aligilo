<?php

require_once ('iloj/iloj.php');
require_once ('iloj/iloj_cxambroj.php');

session_start();
malfermu_datumaro();

kontrolu_rajton("cxambrumi");

$renkontigxodauxro = kalkulu_tagojn($_SESSION["renkontigxo"]->datoj[de], $_SESSION["renkontigxo"]->datoj[gxis]);

if ($partoprenID)
{
  $_SESSION["partopreno"]=new Partopreno($partoprenID);
  $_SESSION["partoprenanto"]=new Partoprenanto($partopreno->datoj[partoprenantoID]);
}
// TODO:? Cxambrotipo nochmal �berarbeiten
// [respondo de Martin:] Fr�her gab es 'u', 'g' und 'n'. Das 'n' f�r negravas wurde irgendwann rausgenommen. Das wollte ich nochmal �berdenken und ggf. anpassen.


/*
//sql_farukajmontru("select count(*) from litonoktoj where cxambro = '$cxambronombro'");
$row = mysql_fetch_array(sql_faru("select count(*) from litonoktoj where cxambro = '$cxambronombro'"));
if ($row[0] == 0)
{
  if ($_SESSION["partopreno"]->datoj[cxambrotipo] == 'u')
  {
    sql_faru("update cxambroj set tipo = '".$_SESSION["partoprenanto"]->datoj[sekso]."'");
  }
  else
  {
    sql_faru("update cxambroj set tipo = 'g'");
  }
}*/

if ( $sendu=="Ek!" )
{
    //echo "Typenupdate".$rimarkoj;
  //    sql_faru("update cxambroj set tipo = '".$tipo."', rimarkoj='".$rimarkoj."',dulita='".$dulita."' where ID='".$cxambronombro."'");
  sxangxu_datumbazon("cxambroj",
					 array("tipo" => $tipo,
						   "rimarkoj" => $rimarkoj,
						   "dulita" => $dulita),
					 array("ID" => $cxambronombro));
}

if ( $sendu=="Nun!" )  //sxangxu cxambrojn
{
    eoecho ("Ni s^ang^as c^ambro ".$de." kun c^ambro ".$al);
    //cxu suficas la litoj?
    $cxambrode=new Cxambro($de);
    $cxambroal=new Cxambro($al);
	//    $row = mysql_fetch_array(sql_faru("select max(litonumero),cxambro from litonoktoj where cxambro='$de' group by cxambro"),MYSQL_NUM);

	// TODO: Kial max(...) _kaj_ group by, kiam ni cxiuokaze nur
	//       uzas unu linion? Cxu ne
	//             SELECT MAX(litonumero) from litonoktoj where cxambro=$de
	// donus la saman rezulton?

	// TODO: Kial max(litonumero) donu la nombron da uzataj litoj?
	//  -> kiam oni lauxsekve disdonas la litonumerojn,
	//     MAX(...) = count(DISTINCT ...).

	$row = mysql_fetch_array(sql_faru(datumbazdemando(array("MAX(litonumero)", "cxambro"),
													  "litonoktoj",
													  "cxambro = '$de'",
													  "",
													  array("group" => "cxambro"))),
							 MYSQL_NUM);
    echo "<BR>Estas $row[0] litoj uzata en $de,";
	//    $row2 = mysql_fetch_array(sql_faru("select max(litonumero),cxambro from litonoktoj where cxambro='$al' group by cxambro"),MYSQL_NUM);

	// TODO: (dito ^)
	$row2 = mysql_fetch_array(sql_faru(datumbazdemando(array("max(litonumero)", "cxambro"),
													   "litonoktoj",
													   "cxambro='$al'",
													   "",
													   array("group" => "cxambro"))),
							 MYSQL_NUM);
    echo "kaj $row2[0] litoj uzata en $al.<BR>"; 
    echo "$de enhavas ".$cxambrode->datoj[litonombro]." litojn ";
    echo "$al enhavas ".$cxambroal->datoj[litonombro]." litojn<BR>";
    if (($row[0]>$cxambroal->datoj[litonombro]) or ($row2[0]) > ($cxambrode->datoj[litonombro]))
    {      
      erareldono("tro la litoj uzata");
      exit();
    }

    //    $row = mysql_fetch_array(sql_faru( "select ID from litonoktoj where cxambro='$de' and rezervtipo!='r'"),MYSQL_NUM);
	//    $row2 = mysql_fetch_array(sql_faru("select ID from litonoktoj where cxambro='$al' and rezervtipo!='r'"),MYSQL_NUM);

    $row =
	  mysql_fetch_array(sql_faru(datumbazdemando("ID",
												 "litonoktoj",
												 array("cxambro = '$de' or cxambro = '$al'",
													   "rezervtipo != 'r'")
												 )),
						MYSQL_NUM);
    if ($row)
    {
      erareldono("Iu lito estas jam disdonata.");
      exit();
    }
    //intersxangxo
//     sql_faru("update litonoktoj set cxambro = 'XXXXX' where cxambro='".$de."'");
//     sql_faru("update litonoktoj set cxambro = '$de' where cxambro='".$al."'");
//     sql_faru("update litonoktoj set cxambro = '$al' where cxambro='XXXXX'");    
	sxangxu_datumbazon("litonoktoj",
					   array("cxambro" => "XXXXX"),
					   array("cxambro" => $de));
	sxangxu_datumbazon("litonoktoj",
					   array("cxambro" => $de),
					   array("cxambro" => $al));
	sxangxu_datumbazon("litonoktoj",
					   array("cxambro" => $al),
					   array("cxambro" => "XXXXX"));
	

    
/*
    $row = mysql_fetch_array(sql_faru("select tipo,rimarkoj from cxambroj where cxambro='$de'"),MYSQL_NUM);
    $row2= mysql_fetch_array(sql_faru("select tipo,rimarkoj from cxambroj where cxambro='$al'"),MYSQL_NUM);
    sql_faru("update cxambroj set tipo = '".$row[tipo]."', rimarkoj='".$row[rimarkoj]."' where cxambro='$al'");    
    sql_faru("update cxambroj set tipo = '".$row[tipo]."', rimarkoj='".$row2[rimarkoj]."' where cxambro='$de'");
*/
    $tipo=$cxambrode->datoj[tipo];
    $cxambrode->datoj[tipo]=$cxambroal->datoj[tipo];    
    $cxambroal->datoj[tipo]=$tipo;    
    $rimarkoj=$cxambrode->datoj[rimarkoj];
    $cxambrode->datoj[rimarkoj]=$cxambroal->datoj[rimarkoj];
    $cxambroal->datoj[rimarkoj]=$rimarkoj;    
    $cxambroal->skribu();
    $cxambrode->skribu();
}


// kontrolparto
if ( $sendu=="Faru!" )
{

  $valoroj = array("cxambro" => $cxambronombro,
				   "partopreno" => $_SESSION["partopreno"]->datoj["ID"],
				   "rezervtipo" => $tipo);

  //  $sql = "insert into litonoktoj (cxambro,partopreno,rezervtipo,litonumero,nokto_de,nokto_gxis)";
  //  $sql .= "values ('$cxambronombro','".$_SESSION["partopreno"]->datoj[ID]."','$tipo',";

  if ($tute[0] != "")
  {
	//    $sql .= "'$tute[0]','1','$renkontigxodauxro')";
	//    sql_faru($sql);
	$valoroj["litonumero"] = $tute[0];
	$valoroj["nokto_de"] = "1";
	$valoroj["nokto_gxis"] = $renkontigxodauxro;
	aldonu_al_datumbazo("litonoktoj", $valoroj);
  }
  else
  {
    $banto = 1;
    while ($banto <= $renkontigxodauxro)
    {
	  
	  // kopio de $valoroj
	  // TODO: eltrovu: eble $valoroj2 = $valoroj jam suficxas?
      $valoroj2 = array_merge(array(), $valoroj); 
      if (!$nokto[$banto])
      {
        $lito = "manko";
      }
      else
      {
        $lito = $nokto[$banto];
        $de = $banto;
      }
      do
      {
        $banto++;
      }
      while ( ($nokto[$banto] == $lito)
              and ($banto<=$renkontigxodauxro)
            );
      if ($lito != "manko")
      {
//         $sql2 .= " '$lito','$de','".($banto-1)."')";
//         sql_faru($sql2);
		$valoroj2["litonumero"] = $lito;
		$valoroj2["nokto_de"] = $de;
		$valoroj2["nokto_gxis"] = $banto - 1;
		aldonu_al_datumbazo("litonoktoj", $valoroj2);
      }
    }

  }
}


HtmlKapo();


if ($_SESSION["partoprenanto"])
{
  eoecho ("Ni serc^as c^ambron por: <b>" . $_SESSION["partoprenanto"]->datoj[personanomo] .
          " " . $_SESSION["partoprenanto"]->datoj[nomo] .
	      " [" . $_SESSION["partoprenanto"]->datoj[sekso] .
          "/" . $_SESSION["partopreno"]->datoj[cxambrotipo] .
	      "/" . $_SESSION["partopreno"]->datoj['agxo'] .
	      "] </b> de: " . $_SESSION["partopreno"]->datoj[de] .
	      " g^is: ".$_SESSION["partopreno"]->datoj[gxis]."<BR>\n");
  if ($_SESSION["partopreno"]->datoj[renkontigxoID]!=$_SESSION["renkontigxo"]->datoj[ID]) 
  {
    erareldono("malg^usta renkontig^o!");
    exit();
  }
  
}

// provizore nur tiel
if ($cxambronumero)
{
  $cxambronombro = $cxambronumero;
}

if ($cxambronombro == "")
{
  eoecho ("Listo de la c^ambroj lau^ la etag^oj:<BR><BR>");
  
  if ($etagxo=='')
	{
	  // montru cxiujn etagxojn

	  montru_laux_etagxoj($_SESSION["partopreno"]->datoj[cxambrotipo][0],
					 $_SESSION["partoprenanto"]->datoj[sekso][0]);
	}
  else
	{
	  // montru la etagxon $etagxo

	  //    $cxam_sql = "select ID from cxambroj where renkontigxo=".$_SESSION["renkontigxo"]->datoj[ID]." and etagxo='".$etagxo."' order by nomo";
	  $cxam_sql = datumbazdemando("ID",
								  "cxambroj",
								  array("etagxo = '".$etagxo ."'"),
								  "renkontigxo",
								  array("order" => "nomo"));
	  
	  $cxam_rezulto = sql_faru($cxam_sql);
	  
	  echo "<table valign=\"top\">";
	  while ($row = mysql_fetch_array($cxam_rezulto, MYSQL_BOTH))
		{
		  if ($kalk%3==0)   //TODO:? auch einstellbar machen (kion? cxu la 3?)
			// [respondo de Martin:] Ich hatte vor eine Art Konfiguration f�r jeden Benutzer und / oder jedes Treffen zu erm�glichen, die solche Sachen einstellbar macht.

			echo "<tr>";
		  $kalk++;
		  echo "<td>";
		  montru_cxambron($row[ID],$_SESSION["renkontigxo"],
						 $_SESSION["partoprenanto"],$_SESSION["partopreno"]);
		  echo "</td>";
		  if($kalk%3 == 0)
			echo "</tr>";
		}
	  if ($kalk%3 != 0)
		echo "</tr>";
	  echo "</table>";
	}
}
else
{
  // montru, kiu sxatas kunlogxi kun kiu
  montru_kunlogxantojn($cxambronombro);
  // montru nur la cxambron mem.
  montru_cxambron($cxambronombro,$_SESSION["renkontigxo"],
				 $_SESSION["partoprenanto"],$_SESSION["partopreno"],"granda");
}

HtmlFino();

?>
