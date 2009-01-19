<?php

require ('iloj/iloj.php');
malfermu_datumaro();

session_start();

if (!rajtas("rabati"))
{
  ne_rajtas();
}

if (isset ($jena))
{
   $rabato=new Rabato($jena);
}
elseif (isset($sendu))
{
  $rabato = $_SESSION["rabato"];
   $rabato->kopiu();
   //$rabato->montru();
   if ( $rabato->datoj[ID] == "" ) 
   { 
     $rabato->kreu(); 
   } 
   $rabato->skribu();
 //   echo "<font color=red> rabato sekurata</font>"; 

   // resxargxu el la datumbazo
    $rabato = new Rabato($rabato->datoj[ID]);
}
else
{
  $rabato = new Rabato(0);
  $rabato->datoj[partoprenoID] = $_SESSION["partopreno"]->datoj[ID];
}


// dauxrigu la rabaton por la onta fojo
$_SESSION["rabato"] = $rabato;

{

  HtmlKapo();
  //echo "<BR><BR>";
  echo "<center>";

    if ($parto=="korekti")
    {
      erareldono ("Hmm, io malg^usta okazis.");
    }

  echo "</center>";
  
  // "select ID,partoprenoID,kvanto,kauzo from rabatoj where partoprenoID='".$_SESSION["partopreno"]->datoj[ID]."'", 
  $sql = datumbazdemando(array("ID", "partoprenoID", "kvanto", "kauzo"),
						 "rabatoj",
						 "",
						 array("partopreno" => "partoprenoID"));
  sercxu($sql,
		 array("ID","asc"), 
		 array(array('0','','->','z','"rabato.php?jena=XXXXX"','1'),
			   array('kvanto','sumo','XXXXX','r','','-1'), 
			   array("kauzo","C^ar",'XXXXX','l','','-1')
			   ), 
		 array(array('',array('&sum; XX','N','z'))),
		 "rabatoj-partoprenanto",
		 0,
		 0,"G^isnunaj rabatoj:", '', 'ne');
 
  echo "<form action='rabato.php' method='POST'>\n";

  eoecho ("Vi nun entajpas rabaton de: ".$_SESSION["partoprenanto"]->datoj[personanomo]." ".
		  $_SESSION["partoprenanto"]->datoj[nomo]." (#".$_SESSION["partoprenanto"]->datoj[ID].
		  ") por la ".$_SESSION["renkontigxo"]->datoj[nomo]." en ".
		  $_SESSION["renkontigxo"]->datoj[loko]."<BR>\n");     
    
  entajpejo ("sumo:",kvanto,$rabato->datoj[kvanto],5,"",""," E^");

  montru_elekto_liston("rabatkauxzoj",$rabato->datoj['kauzo'],'kauzo');
  
  tenukasxe('parto', "kontroli");
  send_butono("Enmetu!");
  ligu("partrezultoj.php","reen","");
  echo "</form>";

  HtmlFino();

}
?>
