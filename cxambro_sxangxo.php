<?php

require_once ('iloj/iloj.php');
require_once ('iloj/iloj_cxambroj.php');

session_start();
malfermu_datumaro();

if (!rajtas("cxambrumi"))
{
  ne_rajtas();
}

if ($sendu=="Nun!")
{
  $ppID = $_SESSION["partopreno"]->datoj["ID"];
  eoecho ("S^ang^o de #$ppID al #$al farita");
  //    sql_faru("update litonoktoj set partopreno='XXXXX' where partopreno='".$_SESSION["partopreno"]->datoj[ID]."'");
  //    sql_faru("update litonoktoj set partopreno='".$_SESSION["partopreno"]->datoj[ID]."' where partopreno='$al'");
  //    sql_faru("update litonoktoj set partopreno='".$al."' where partopreno='XXXXX'");

  $tempID = rand(-10000, -1000);

  // intersxangxu la cxambrojn ...
  sxangxu_datumbazon("litonoktoj",
					 array("partopreno" => $tempID),
					 array("partopreno" => $ppID));
  sxangxu_datumbazon("litonoktoj",
					 array("partopreno" => $ppID),
					 array("partopreno" => $al));
  sxangxu_datumbazon("litonoktoj",
					 array("partopreno" => $al),
					 array("partopreno" => $tempID));

}

{
  HtmlKapo();
  
  //  $cxam_sql = "select p.ID,pn.ID,nomo,personanomo,l.ID,partopreno,nokto_de,nokto_gxis,rezervtipo from litonoktoj as l,partoprenoj as pn, partoprenantoj as p where l.partopreno=pn.ID and pn.partoprenantoID=p.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' order by personanomo,nomo";
  
  $cxam_sql = datumbazdemando(array("p.ID", "pn.ID", "nomo,personanomo", "l.ID",
									"partopreno,nokto_de", "nokto_gxis", "rezervtipo"),
							  array("litonoktoj" => "l",
									"partoprenoj" => "pn",
									"partoprenantoj" => "p"),
							  array("l.partopreno = pn.ID",
									"pn.partoprenantoID = p.ID"),
							  "renkontigxoID",
							  array("order" => "personanomo, nomo")
							  );

  $cxam_rezulto = sql_faru($cxam_sql);
  
  echo "<form action=\"cxambro_sxangxo.php\" method=\"post\">";
  eoecho ($_SESSION["partoprenanto"]->datoj[personanomo]." ".$_SESSION["partoprenanto"]->datoj[nomo]." volas s^ang^i kun: ");

  echo "<select name=\"al\" size=\"5\">\n";
  while  ($row = mysql_fetch_array($cxam_rezulto, MYSQL_BOTH))
  {
    eoecho ("<option value = \"".$row[1]."\">".$row[personanomo]." ".$row[nomo]." (".$row[nokto_de]."-".$row[nokto_gxis]."/".$row[rezervtipo].")");
  }
  echo " </select>";
  send_butono("Nun!");



  HtmlFino();

}
?>