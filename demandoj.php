<?php

/* ##################################
 * Montras kelkajn statistikojn
 * kaj kreas PDF-on da ili.
/* ################################## */

  /**
   * TODO: riparu.
   */


require_once ("iloj/iloj.php");
require_once('iloj/fpdf/fpdf.php');
session_start();

malfermu_datumaro();

if (!rajtas("administri"))
{
  ne_rajtas();
}

 define('FPDF_FONTPATH','./font/');
 
 $font='TEMPO';
 
 $pdf=new FPDF();
 $pdf->AddFont($font,'',$font.'.php');

 $pdf->Open();
 $pdf->AddPage();
 
 $pdf->SetFont($font,'',12);
 $pdf->text(15,17, eo("kelkajn statistikojn pri la ".$_SESSION["renkontigxo"]->datoj[nomo]." en ".$_SESSION["renkontigxo"]->datoj[loko]));
 $pdf->text(15,23, "Dato:".date('Y-m-d'));
  
/*  eoecho ("cxiu vegetarano: <BR>\n");
  $grr = $renkontigxo->datoj[de];
//FLOOR((TO_DAYS('$renkontigxo->datoj[de]') - TO_DAYS(naskigxdato))/365.25)
  $sql  = "Select personanomo,nomo ";
  $sql .= "from partoprenantoj as pa,partoprenoj as pp ";
  $sql .= "where  pa.id = pp.partoprenantoID and pp.vegetare = 'J' and alvenstato='a'";
  $sql .= "order by personanomo";

  sql_farukajmontru($sql);
*/
  //echo "</TD></TR></TABLE>\n";
 //la diverskategoria ABC statistiko
 //$this->agxkategorio = $this->kalkulu_agx_kategorio($agxo,$renkontigxo);

  //$this->landakategorio = eltrovu_landakategorio($partoprenanto->datoj[lando]);
 //$row = sql_faru("select count(*) from partoprenantoj as p, partoprenoj as pn where p.ID=pn.partoprenantoID and ago<='20' and (");
// echo "<20: ".$row[0];
 
 $pdf->SetFontSize(12);
 $pagantoj=array(); //array(array())
 $seksagxo=array();

// $rezulto = sql_faru("select p.ID,pn.ID from partoprenantoj as p, partoprenoj as pn where p.ID=pn.partoprenantoID  and alvenstato='a' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and partoprentipo='t'");
$rezulto = sql_faru(datumbazdemando(array("p.ID", "pn.ID"),
									array("partoprenantoj" => "p", "partoprenoj" => "pn"),
									array("alvenstato = 'a'",
										  "partoprentipo = 't'"),
									"renkontigxoID"));
 while ($row = mysql_fetch_array($rezulto, MYSQL_NUM))
  {
    $gesamt+=1;
    $partoprenanto = new Partoprenanto($row[0]);
    
    $seksagxo[$partoprenanto->datoj[sekso]][$partoprenanto->datoj[agxo]]+=1;
    
    $partopreno = new Partopreno($row[1]);
    $ko = new Kotizo($partopreno,$partoprenanto,$_SESSION["renkontigxo"]);
    $kotizo += $ko->kotizo;
    $pagantoj[$ko->agxkategorio][$ko->aligxkategorio][$ko->landakategorio][$partopreno->datoj[domotipo]]+=1;
    //echo $ko->agxkategorio." / ".$ko->aligxkategorio." / ".$ko->landakategorio." / ".$partopreno->datoj[domotipo]." <BR>";
    //echo "Ko: ".$ko->kotizo;
  }
  //echo "entute: $gesamt<br>";
  $pdf->setY(30);
  $pdf->text(20,65,"entute: $gesamt");

  $pdf->cell(20,4,"domo",1,0,C);
  $pdf->cell(21,4,"<1.10.",1,0,C);  //bei Bedarf aus der DB holen
  $pdf->cell(21,4,"<1.12.",1,0,C);
  $pdf->cell(21,4,">=1.12.",1,1,C);
  for ($i=2;$i>=0;$i--)
  {
     $pdf->cell(20,4,$i,1,0,C);
     for ($j=2;$j>=0;$j--)  
     {        
        for ($k=A;$k<=C;$k++)  
        {
           $pdf->cell(7,4, $pagantoj[$i][$j][$k][J],1,0,C);;
        }
     }
     $pdf->ln();  //echo "<BR> ";
  }
//  echo "MEM:<BR>";
  $pdf->cell(20,4,"mem",1,0,C);
  $pdf->cell(21,4,"<1.10.",1,0,C);  //bei Bedarf aus der DB holen
  $pdf->cell(21,4,"<1.12.",1,0,C);
  $pdf->cell(21,4,">=1.12.",1,1,C);
  for ($i=2;$i>=0;$i--)
  {
     $pdf->cell(20,4,$i,1,0,C);
     for ($j=2;$j>=0;$j--)  
     {        
        for ($k=A;$k<=C;$k++)  
        {
           $pdf->cell(7,4, $pagantoj[$i][$j][$k][M],1,0,C);
        }
     }
     $pdf->ln();  //echo "<BR> ";
  }

  //$pdf->text(50,65,"sumo: ".$kotizo); //besser erstmal rauslassen, stimmt ohnehin noch nicht.
  $pdf->text(31,76,"inoj viroj");
 
  $pdf->SetFontSize(7);          
 //la sekso-agxo statistiko 
  for ($i=60;$i>=10;$i--)
  {
     //echo "$i: ".$seksagxo[i][$i]." | ".$seksagxo[v][$i]."<BR>";
     $pdf->rect (37-$seksagxo[i][$i],50+$i*3,$seksagxo[i][$i],-2,'FD');
     $pdf->rect (40,50+$i*3,$seksagxo[v][$i],-2,'FD');
     $pdf->text(10,50+$i*3,$i);
     $inoj+=$seksagxo[i][$i];
     $viroj+=$seksagxo[v][$i];
  }
  $pdf->SetFontSize(12);          
  $pdf->text(31,240,$inoj);
  $pdf->text(41,240,$viroj);
  
function kalkulu_partoprenantojn($speco, $valoro)
{
  // TODO: Esploru, cxu la GROUP BY vere necesas.

  $row = mysql_fetch_array(sql_faru(datumbazdemando(array($speco, "count(*)" => "c"),
													array("partoprenoj" => "p"),
													array("$speco = '$valoro'",
														  "alvenstato = 'a'"),
													"renkontigxoID",
													array("group" => "$speco")
													)
									),
						   MYSQL_BOTH);
  return $row["c"];
}


  //nombro de vegetaranoj:
//// "Select vegetare, count(*) as c from partoprenoj as p where vegetare='J'  and alvenstato='a' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' group by vegetare"
  $pdf->setXY(110,40);
  $pdf->write(9,'vegetaranoj: ' . kalkulu_partoprenantojn("vegetare", "J"));

						 //  $row=mysql_fetch_array(sql_faru("Select partoprentipo, count(*) as c from partoprenoj as p where partoprentipo='p'  and  alvenstato='a' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' group by partoprentipo"),MYSQL_NUM);

  $pdf->setXY(110,45);
  $pdf->write(9,'partpartoprenantoj:'. kalkulu_partoprenantojn("partoprentipo", "p"));
  
//  $row=mysql_fetch_array(sql_faru("Select komencanto, count(*) as c from partoprenoj as p where komencanto='J'  and  alvenstato='a' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' group by komencanto"),MYSQL_NUM);
  $pdf->setXY(110,50);
  $pdf->write(9,'komencantoj:'. kalkulu_partoprenantojn("komencanto", "J"));

//  $row=mysql_fetch_array(sql_faru("Select invitletero, count(*) as c from partoprenoj as p where invitletero='J' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' group by invitletero"),MYSQL_NUM);
  $pdf->setXY(110,55);
  $pdf->write(9,'invitleteroj:'. kalkulu_partoprenantojn("invitletero", "J"));
  
// TODO: Kial GROUP BY ?
//  $row=mysql_fetch_array(sql_faru("Select invitilosendata, count(*) as c from partoprenoj as p where invitilosendata<>'0000-00-00' and  alvenstato='a' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' group by renkontigxoID"),MYSQL_NUM);
$row = mysql_fetch_array(sql_faru(datumbazdemando(array("invitilosendata", "count(*)" => "c"),
													array("partoprenoj" => "p"),
													array("invitilosendata <> '0000-00-00'",
														  "alvenstato = 'a'"),
													"renkontigxoID",
													array("group" => "renkontigxoID"))
								  ),
						 MYSQL_BOTH);
  $pdf->setXY(110,60);
  $pdf->write(9,'(elsendata:'.$row["c"].')');

//  $row=mysql_fetch_array(sql_faru("Select retakonfirmilo, count(*) as c from partoprenoj as p where retakonfirmilo='J'  and alvenstato='a' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' group by retakonfirmilo"),MYSQL_NUM);
  $pdf->setXY(110,65);
  $pdf->write(9,'retakonfirmilo:' . kalkulu_partoprenantojn("retakonfirmilo", "J"));

 //la aligx/antauxpagtempo diagramo 
 
 //gesamt Kotizo -> nein, lieber woanders anzeigen
 

   $pdf->Output("dosieroj_generitaj/statistikoj.pdf");
   hazard_ligu("dosieroj_generitaj/statistikoj.pdf","els^uti la statistikojn.","_top","jes");  

?>
