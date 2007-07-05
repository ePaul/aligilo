<?php

require_once ('iloj/iloj.php');
malfermu_datumaro();

session_start();

if (!rajtas("mono"))
{
  ne_rajtas();
}

if (isset ($jena))
{
   $pago = new Pago($jena);
}
elseif (isset($sendu))
{
   $pago = $_SESSION["pago"];
    // estos remetita en $_SESSION["pago"] je la fino.

   $pago->kopiu();
   //$pago->montru();
   if ( $pago->datoj[ID] == "" ) 
   { 
     $pago->kreu(); 
   } 
  if (kontrolu_daton($pago->datoj[dato]))
  {
    $pago->skribu();
 //   echo "<font color=red> pagon sekurata</font>"; 
    $pago = new Pago($pago->datoj[ID]);
  }
  else $parto="korekti";
}
else
{
  $pago = new Pago(0);
  $pago->datoj[partoprenoID] = $_SESSION["partopreno"]->datoj[ID];
}

{

  HtmlKapo();
  //echo "<BR><BR>";
  echo "<center>";

    if ($parto=="korekti")
    {
      erareldono ("Hmm, ion malg^ustan okazis.");
    }

  echo "</center>";
  
  sercxu(datumbazdemando(array("ID", "partoprenoID", "kvanto", "tipo", "dato"),
						 "pagoj",
						 "partoprenoID = '" . $_SESSION["partopreno"]->datoj[ID] . "'"),
		 array("dato","desc"),
		 array(array('0','','->','z','"antauxpago.php?jena=XXXXX"','1'),
			   array('dato','dato','XXXXX','l','','-1'),
			   array('kvanto','sumo','XXXXX','r','','-1'), 
			   array("tipo","tipo",'XXXXX','l','','-1')
			   ), 
		 array(array('','',array('&sum; XX','N','z'))), 
		 0,0,0,"G^isnunaj antau^pagoj:",'', "ne"); 
  
  echo "<form ACTION=\"antauxpago.php\" METHOD=\"POST\">";

  eoecho ("Vi nun entajpas pagon de: " . $_SESSION["partoprenanto"]->datoj[personanomo]." ".
		  $_SESSION["partoprenanto"]->datoj[nomo]." (".$_SESSION["partoprenanto"]->datoj[ID] .
		  ") por la ".$_SESSION["renkontigxo"]->datoj[nomo]." en ".
		  $_SESSION["renkontigxo"]->datoj[loko]."<BR>\n");
     
  if ( !$pago->datoj[dato] )
  {
    $pago->datoj[dato] = date("Y-m-d");
  }
  entajpejo ("<BR>alvenodato:",dato,$pago->datoj[dato],11,"",""," (jaro-monato-tago)");
  if ( (!$pago->datoj[dato])
      and ( !kontrolu_daton($pago->datoj[dato]) )
      )
  {
    erareldono ("La dato kion vi entajpis ne ekzistas au^ estis malg^uste");
  }
  
  entajpejo ("sumo:",kvanto,$pago->datoj[kvanto],5,"",""," E^");

  montru_elekto_liston("antauxpaguloj",$pago->datoj['tipo'],'tipo','antau^pago al ');

/*  entajpbutono("<BR><BR><BLOCKQUOTE>",tipo,$pago->datoj[tipo],"GEJ",GEJ,"antau^pago per GEJ-konto<BR>","");
  entajpbutono("",'tipo',$pago->datoj['tipo'],"BerO",'BerO',"antau^pago al BerO-kaso<BR>");
  entajpbutono("",'tipo',$pago->datoj['tipo'],"UEA",'UEA',"antau^pago per UEA-konto<BR>");
  entajpbutono("",'tipo',$pago->datoj['tipo'],"JEFO",'JEFO',"antau^pago al JEFO<br/>");
  entajpbutono("",'tipo',$pago->datoj['tipo'],"HEJ",'HEJ',"antau^pago al HEJ<br/>");
  entajpbutono("",'tipo',$pago->datoj['tipo'],"IEJ",'IEJ',"antau^pago al IEJ<br/>");
  entajpbutono("",'tipo',$pago->datoj['tipo'],"Martin",'Martin',"antau^pago al Martin<BR>");  
  entajpbutono("",'tipo',$pago->datoj['tipo'],"Julia",'Julia',"antau^pago al Julia<BR>");  
  entajpbutono("",'tipo',$pago->datoj['tipo'], "Andreas", "Andreas",
			   "antau^pago al Andreas<br/>");
  entajpbutono("",'tipo',$pago->datoj['tipo'],"alia",'alia',"alia antau^pago (faru noton!)<BR/><BR/>");  
  entajpbutono("",'tipo',$pago->datoj['tipo'],"surlokpago",'surlokpago',"surloka pago<BR>","kutima");
  entajpbutono("",'tipo',$pago->datoj['tipo'],"donaco",'donaco',"doncao<BR>");
  entajpbutono("",'tipo',$pago->datoj['tipo'],"repago",'repago',"repago</BLOCKQUOTE>");

  echo "<BR><BR>";
  */
  tenukasxe('parto',"kontroli");
  send_butono("Enmetu!");
  ligu("partrezultoj.php","reen","");
  echo "</form>";

  HtmlFino();

}

$_SESSION["pago"] = $pago;
?>
