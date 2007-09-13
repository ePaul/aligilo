<?php

define(DEBUG, true);

/**
 * Grava administrado.
 *--------------------
 *
 * Montras unue elektformularo, kiu post klako al "faru"
 * vokas sin mem per kelkaj parametroj, kaj ligilojn al
 * aliaj programpartoj.
 *
 * Kiam oni donis tauxgan $kio-parametron, gxi montras
 * sube ankaux la rezulto de la ago.
 * 
 *  $kio  - kio farindas
 *          n     - kreu nomsxildojn
 *          s     - kreu specialajn nomsxildojn (ekzemple por junulargastej-dungitoj)
 *          m     - kreu mangxkuponojn
 *          k     - kreu konfirmilojn
 *          a     - kreu akceptfoliojn
 *                                (cxiam PDF-e por elsxuti,
 *                                 kaj nur por tiuj partoprenantoj,
 *                                 kiuj ne jam havas.)
 *          sendu - sendu retajn konfirmilojn (retposxte)
 *          adres - kreu adresaron
 *                               (PDF-e)
 *
 *          backup          - kreu sekurkopiojn de la datumbazo kaj alsxutu
 *                            ilin cxe GMX. (ne funkcias sen retkonekto!)
 *          backup_programo - kreu sekurkopiojn de la programo kaj alsxutu
 *                            ilin cxe GMX.
 *
 * $tipo  - se mangxkuponoj, kiajn?
 *          N     - viajndajn
 *          A     - vegane (vegetajxe)
 *          J     - vegetarajn
 *
 * $numero - kiom pagxoj? (Elekteblecoj estas 1, 5, 20, 999 (cxiuj)
 *           (Estas uzata cxe n, m, a, k)
 *
 * $kiuj_homoj - por kiuj homoj kreu nomsxildojn?
 * 
 * $eksendu - cxu la retajn konfirmilojn oni vere forsendu,
 *            aux nur al la administranto?
 *          J - vere eksendu
 *          NE - nur al la administranto
 *
 * $savu
 *          J -  memoru en la datumbazo, ke vi sendis konfirmilon al
 *               aux printis nomsxildojn/mangxkuponon/akceptfolion por
 *               la partoprenantoj.
 *          NE - simple forgesu.
 *
 * $sen    - Cxu kreu foliojn sen datumoj (por mane plenigi)?
 *          s
 *          NE
 * $bunta  - cxu kreu buntan adresaron aux nigran?
 *           JES
 *           NE
 * $granda - cxu kreu grandan adresaron por korektigi?
 */

require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

kontrolu_rajton("administri");

function esso($s)
{
   $ss = str_replace(utf8_encode('�'),"ss",$s);
   return $ss;
}

{
  HtmlKapo();
  eoecho("<h2>Grava Administrado</h2>");
  echo "<form action='administrado.php' method='post'>\n";
  eoecho ("<p>Elpremu:<BR>");
  entajpbutono ("",kio,$kio,"n",n,"noms^ildojn","kutima");
  entajpbutono ("(", "nkkren", $nkkren, "cxiuj", "cxiuj", "c^iuj", "kutima");
  entajpbutono ("|", "nkkren", $nkkren, "nur", "nur", "nur KKRen");
  entajpbutono ("|", "nkkren", $nkkren, "sen", "sen", "sen KKRen )");

  //  entajpejo("(Nur por: ", "kiuj", $kiuj, "", "", "", ")");
  echo "<br/>";
  entajpbutono ("", 'kio', $kio, 's', 's', "specialajn noms^ildojn");
  entajpbutono ("<br/>",kio,$kio,"m",m,"mang^kuponojn");
  entajpbutono ("(",tipo,$tipo,"N",'N','viande', "kutima");
  entajpbutono ("",tipo,$tipo,"A",'A','vegane');
  entajpbutono ("",tipo,$tipo,"J",'J',"vegetare)");
  entajpbutono ("<br/>",kio,$kio,"k",k,'konfirmilojn');
  entajpbutono ("<br/>",kio,$kio,"a",a,'akceptofoliojn');
  
  entajpbutono ("</p><p>Por ",numero,$numero,1,1," 1 pag^o","kutima");
  entajpbutono (" ",numero,$numero,5,5," 5 pag^oj");
  entajpbutono (" ",numero,$numero,20,20," 20 pag^oj");
  entajpbutono (" ",numero,$numero,999,999," c^iuj");
  entajpbutono ("</p><p>",kio,$kio,"sendu",sendu,"Sendu retajn konfirmilojn.");                          //TODO:? jau, auch dies kann man aus der DB ziehen.
  // [respondo de Martin:] F�llt mir im Moment nicht ein. Soll wom�glich bedeuten, da� der Button nur erscheint, wenn der betreffende auch reta ausgew�hlt wurde.

  entajpbokso ("<BR>",eksendu,$eksendu,P,P,"Vere eksendu ilin al la partoprenantoj. (Alikaze al ".funkciuladreso("admin") .")");  
  eoecho ("<br/>Atentu: Tiu c^i funkcio (amasa sendado da retmesag^oj) ankorau^ ne estas bone testita - Martin ne uzis g^in la lastaj jaroj. -- Pau^lo</p>");
  entajpbokso ("<p>",savu,$savu,J,J,"Savu ke vi premis/sendis en la partoprendatumoj");
  entajpbokso ("<BR>",sen,$sen,s,s,"malplenaj folioj</p>");

  entajpbutono ("<p>",'kio',$kio,"adres",adres,"elprintu adresaron ");
  entajpbokso  ("(", 'bunta', $bunta, 'JES', 'JES', "buntan,", "kutima");
  entajpbokso  (" ", 'granda', $granda, 'JES', 'JES', "grandan (por korekti))");

  entajpbutono ("<p>",'kio', $kio, "gepatra_permeso", "gepatra_permeso", "elprintu malplenan gepatran permesilon");
  entajpbutono("(", 'perm_tipo', $perm_tipo, "ina", "ina", "ina (filino) ");
  entajpbutono("|", 'perm_tipo', $perm_tipo, "vira", "vira", "vira (filo) )</p>");

  $dosiernomo = '../../../phplibraro/tmp/' .traduku_tabelnomon('partoprenantoj') . '.sql.gz';
  if (file_exists($dosiernomo))
	{
	  $dato = "de " . date("Y-m-d H:i", filemtime($dosiernomo) .".");
	}
  else
	{
	  $dato = "ne trovebla ($dosiernomo)!";
	}

  entajpbutono ("<p>","kio",$kio, "backup","backup",
				"Kreu sekurkopion de la datumbazo. (La lasta estas $dato)</p>");

  $dosiernomo = '../../../phplibraro/tmp/projekto-'. $tabelnomprefikso . '.sql.gz';
  if (file_exists($dosiernomo))
	{
	  $dato = "de " . date("Y-m-d H:i", filemtime($dosiernomo) .".");
	}
  else
	{
	  $dato = "ne trovebla ($dosiernomo)!";
	}


  entajpbutono ("<p>","kio",$kio, "backup_is","backup_is",
				"Kreu sekurkopion de la datumbazo (nur IS). (La lasta estas $dato)</p>");

  if (rajtas('teknikumi'))
	{
	  entajpbutono("<p>", "kio", $kio, "backup_programo", "backup_programo",
				   "Kreu sekurkopion de la programo.</p>");
	}

  send_butono('Faru!');
  echo "</FORM>\n";

  eoecho("<h2>Aliaj gravaj aferoj</h2>");

  rajtligu("landoj.php", "rigardu kaj eble s^ang^u la landoliston", "",
           "administri");
  eoecho ("<br/>");

  eoecho ("<p>Elprintu partoprenstatistikon:<br/>\n");
  rajtligu("demandoj.php","partopren statistikojn","","administri");
  echo "(das ist leider zur Zeit etwas kaputt) <br/>";
  rajtligu("finkalkulado.php","IS - Abrechnung","","administri");
  echo "<br/>";
  rajtligu("cxambrostatistiko.php",
		   "montru la c^ambrostatistikon kaj la mang^statistikon","","administri");
  echo "</p>";

  if(rajtas("teknikumi"))
	{
	  eoecho("<h2>Nur por teh^nikistoj</h2>\n<p>");

	  rajtligu("entajpantoj.php", "rigardu kaj eble s^ang^u la entajpantoliston", "",
			   "teknikumi");
	  eoecho ("<br/>");
	  rajtligu("renkontigxo.php", "redaktu la renkontig^o-datumojn", "", "teknikumi");
	  eoecho ("<br/>");
	  rajtligu("kreu_cxambron.php", "kreu novan c^ambron", "", "teknikumi");
	  eoecho("</p>");
	}

  echo "<hr/>\n";

}

if ($kio == 'gepatra_permeso')
{
  require_once ('iloj/kreu_konfirmilon.php');
  $kon = new Konfirmilo();
  $kon->kreu_permesilon(0,$_SESSION['renkontigxo'],$perm_tipo == "vira");
  $dosiernomo = "dosieroj_generitaj/permesilo_".$perm_tipo . ".pdf";
  $kon->sendu($dosiernomo);
  hazard_ligu($dosiernomo, "els^utu ".$perm_tipo."n permesilon");
}

if ($kio=='n')
{
require_once ('iloj/kreu_nomsxildojn.php');

  eoecho ("Elpremu la noms^ildojn por:<BR>");

  // kiam ni ne volas presi cxiujn, sed nur la unuajn pagxojn
  $nombroperpagxo=10;
  $numero = $numero * $nombroperpagxo;

  //  $demando = "select p.ID,pn.ID,nomo, personanomo from partoprenantoj as p, partoprenoj as pn where pn.partoprenantoID=p.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and alvenstato='v' and havasNomsxildon='N' order by personanomo,nomo limit 0,$numero";

if ($kiuj == "")
{
  $kkrenkondicxoj = array("cxiuj" => 1,
						 "sen" => "kkren <> 'J'",
						 "nur" => "kkren = 'J'");
  $kkrenkondicxo = $kkrenkondicxoj[$nkkren];
  
  $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo"),
						 array("partoprenantoj" => "p", "partoprenoj" => "pn"),
						 array("pn.partoprenantoID = p.ID",
							"alvenstato = 'v' OR alvenstato = 'a'", 
						        $kkrenkondicxo,
							"havasNomsxildon = 'N'" ),
						 "renkontigxoID",
						 array("order" => "personanomo, nomo",
						       "limit" => "0, $numero")
						 );
}
else
{
  $kiuj_arr = split(",", $kiuj);
  $idoj = "pn.ID = " . join(" or pn.ID = ", $kiuj_arr);
  $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo"),
			     array("partoprenantoj" => "p", "partoprenoj" => "pn"),
			     array("pn.partoprenantoID = p.ID",
				   $idoj,
				    ),
						 "",
						 array("order" => "personanomo, nomo",
						       "limit" => "0, $numero")
						 );
}
  $nom = new Nomsxildo();
  if ($sen=="s")
  {
    // printu nomsxildojn sen nomo

    eoecho ("g^enerala uzo");
    if ($numero>100) $numero=100;
    for ($i=1;$i<$numero;$i++)
	 $nom->kaju(0,0);
  }
  else
  {
    $rezulto = sql_faru($demando);
    while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
    {
      eoecho($row[personanomo]." ".$row[nomo]."<BR>");
      $kalkulilo++;
      if ($kalkulilo % $nombroperpagxo == 0)
		echo "<BR>";
      $nom -> kaju($row[0],$row[1],$savu);
     }
  }
  //if ($sendu=="Faru!")  
  {
    $nom->sendu();       
    hazard_ligu("dosieroj_generitaj/nomsxildoj.pdf","els^uti la noms^ildojn.","_top","jes");
  }
 // else
  {
    //send_butono("Faru!");    
  }


}
// ############ specialaj nomsxildoj ##############################

if ($kio=='s')
{
require_once ('iloj/kreu_nomsxildojn.php');

  eoecho ("Elpremu la noms^ildojn por:<BR>");

  // kiam ni ne volas presi cxiujn, sed nur la unuajn pagxojn
  $nombroperpagxo=10;
  $numero = $numero * $nombroperpagxo;

  //  $demando = "select p.ID,pn.ID,nomo, personanomo from partoprenantoj as p, partoprenoj as pn where pn.partoprenantoID=p.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and alvenstato='v' and havasNomsxildon='N' order by personanomo,nomo limit 0,$numero";

  $demando = datumbazdemando(array("ID", "titolo_esperante", "nomo"),
							 array("nomsxildoj"),
							 array("havasNomsxildon = 'N'"),
							 "renkontigxoID",
							 array("order" => "nomo, titolo_esperante",
								   "limit" => "0, $numero")
							 );
  $nom = new Nomsxildo();
  $rezulto = sql_faru($demando);
  while ($row = mysql_fetch_assoc($rezulto))
    {
      eoecho($row['titolo_esperante']." ".$row['nomo']."<BR>");
      $kalkulilo++;
      if ($kalkulilo % $nombroperpagxo == 0)
		echo "<BR />";
      $nom -> kaju($row['ID'],-1,$savu);
	}
  $nom->sendu();       
  hazard_ligu("dosieroj_generitaj/nomsxildoj.pdf","els^uti la noms^ildojn.","_top","jes");

}


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if ($kio=='a')
{
  require_once ('iloj/kreu_akceptofolion.php');
  eoecho ("Elprintu la akceptfoliojn por:<BR>");
  $nombroperpagxo=1;
  $numero = $numero * $nombroperpagxo;  

  
  //  $demando = "select p.ID,pn.ID,nomo, personanomo from partoprenantoj as p, partoprenoj as pn where pn.partoprenantoID=p.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and alvenstato='v' order by personanomo,nomo limit 0,$numero";

 if ($kiuj != "")
 {
   $kiuj_arr = split(",", $kiuj);
   $idoj = "pn.ID = " . join(" or pn.ID = ", $kiuj_arr);
   $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo"),
							  array("partoprenantoj" => "p", "partoprenoj" => "pn"),
							  array("pn.partoprenantoID = p.ID",
									$idoj)
							  );
 }
 else
   $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo"),
							  array("partoprenantoj" => "p", "partoprenoj" => "pn"),
							  array("pn.partoprenantoID = p.ID",
									"alvenstato = 'v'"),
							  "renkontigxoID",
							  array("order" => "personanomo, nomo",
									"limit" => "0, $numero")
							  );

  
  $af = new Akceptofolio();
  if ($sen=="s")
  {
    eoecho ("g^enerala uzo (malplenaj)");
    if ($numero>100)
	  $numero=100;
    for ($i=0;$i<$numero;$i++)
    {
        echo "($i) ";
		$af->kaju(0,0);
    }
  }
  else
  {
    $rezulto = sql_faru($demando);
    while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
    {     
      $kalkulilo++;
      eoecho("(".$kalkulilo.":) ".$row[personanomo]." ".$row[nomo] . " ");
      $af -> kaju($row[0],$row[1]);
	  echo "<br />\n";
     }
  }
  //if ($sendu=="Faru!")  
  {
    $af->sendu();       
    hazard_ligu("dosieroj_generitaj/akceptofolioj.pdf","els^uti la akceptofoliojn.",
				"_top","jes");
  }
}


//   MANGXKUPONOJ
if ($kio=='m')
{
require_once ('iloj/mangxkuponoj.php');
  $nombroperpagxo=4;
  $numero = $numero * $nombroperpagxo;  
  if ($tipo=='J')
	$vego='vegetarajn';
  else if ($tipo == 'A')
	$vego = 'veganajn';
  else
	$vego='viandajn';

  //  $demando = "select p.ID,pn.ID,nomo, personanomo from partoprenantoj as p, partoprenoj as pn where pn.partoprenantoID=p.ID and (kunmangxas='J' or domotipo='J') and vegetare='$tipo' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and alvenstato='v' and havasMangxkuponon='n' order by personanomo,nomo limit 0,$numero";


  $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo"),
							 array("partoprenantoj" => "p", "partoprenoj" => "pn"),
							 array("pn.partoprenantoID = p.ID",
								   "kunmangxas = 'J' or domotipo='J'",
								   "vegetare = '$tipo'",
								   "alvenstato = 'v' OR alvenstato = 'a'",
								   "havasMangxkuponon = 'n'"),
							 "renkontigxoID",
							 array("order" => "personanomo, nomo",
								   "limit" => "0, $numero")
							 );


  eoecho ("<B><BR><BR>Elpremu la $vego mang^kuponojn por:</B><BR>");
  


  $kup = new Mangxkupono($_SESSION['renkontigxo']);
  if ($sen=="s")
  {
    eoecho ("g^enerala uzo");
    if ($numero>100)
	  $numero=100;
    for ($i=1; $i < $numero; $i++)
	  $kup->kaju(0,0,'ne',$tipo);
  }
  else
  {
    $rezulto = sql_faru($demando);
    while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
    {     
      eoecho($row[personanomo]." ".$row[nomo]."<BR>");
      $kalkulilo++;
      if ($kalkulilo % $nombroperpagxo==0) echo "<BR>";
      $kup -> kaju($row[0],$row[1],$savu,$tipo);        
     }
  }
  //if ($sendu=="Faru!")  
  {
    $kup->sendu($tipo);       
    hazard_ligu("dosieroj_generitaj/mangxkuponoj.pdf","els^utu la kuponojn.","_blank");
  }
 // else
  {
    //send_butono("Faru!");    
  }
 }
  //   KONFIRMILOJ
if ($kio=='k')
{
  require_once ('iloj/kreu_konfirmilon.php');


  $nombroperpagxo=1;
  $numero = $numero * $nombroperpagxo;  
  
  //  $demando = "select p.ID,pn.ID,nomo, personanomo from partoprenantoj as p, partoprenoj as pn where pn.partoprenantoID=p.ID and retakonfirmilo!='J' and 2akonfirmilosendata='0000-00-00' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and kontrolata='J' and alvenstato='v'  limit 0,$numero";
  $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo"),
							 array("partoprenantoj" => "p", "partoprenoj" => "pn"),
							 array("pn.partoprenantoID = p.ID",
								   "retakonfirmilo!='J'",
								   "2akonfirmilosendata='0000-00-00'",
								   "kontrolata='J'",
								   "alvenstato = 'v'"
								   ),
							 "renkontigxoID",
							 array("order" => "personanomo, nomo",
								   "limit" => "0, $numero")
							 );
  
  eoecho ("<B><BR><BR>Elpremu la konfirmilon por:</B><BR>");

  $kon = new Konfirmilo();
  {
    $rezulto = sql_faru($demando);
    while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
    {     
      eoecho($row[personanomo]." ".$row[nomo]."<BR>");  
      $kon ->kreu_konfirmilon($row[1],$row[0],$savu);
    }
  }
    $kon->sendu();  
    hazard_ligu("dosieroj_generitaj/konfirmilo.pdf","els^uti la konfirmilojn.","_top","jes");
}
if ($kio=='sendu')   // TODO:? Cxu vere estas uzata, aux cxu ni auxtomate sendas  mesagxon post la (reta) aligxo?
	 // [respondo de Martin:] Das ist zum Aussenden der zweiten Konfirmiloj als Massenmail. Das hab ich bisher nicht benutzt und stattdessen einzeln verschickt. Das erste Konfirmilo kommt direkt beim anmelden.

    // TODO!: forigu aux plibonigu tiel, ke estas uzebla.

{
  
  //  $demando = "select p.ID,pn.ID,nomo, personanomo,retposxto,agxo from partoprenantoj as p, partoprenoj as pn where pn.partoprenantoID=p.ID and retakonfirmilo='J' and 2akonfirmilosendata='0000-00-00' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and alvenstato='v' limit 0,$numero";     

  $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo", "retposxto",
								   "pn.agxo", "germanakonfirmilo" => "germane"),
							 array("partoprenantoj" => "p", "partoprenoj" => "pn"),
							 array("pn.partoprenantoID = p.ID",
								   "retakonfirmilo = 'J'",
								   "2akonfirmilosendata = '0000-00-00'",
								   // "kontrolata='J'", // TODO:? Kial jen sen kontrolata = 'J'?
								   // [respondo de Martin:] Kann ich nicht rekonstruieren. In meiner alten Version fehlt das kontrolata komplett
								   "alvenstato = 'v'"
								   ),
							 "renkontigxoID",
							 array("limit" => "0, $numero")
							 );

  
  eoecho ("<B><BR><BR>Elsendu la konfirmilon por:</B><BR>");

  {
    
    $rezulto = sql_faru($demando);
    while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
    {     
      eoecho($row[personanomo]." ".$row[nomo]."");  

	  // TODO:? Kial ne rekte al la partoprenantoj?
	  // [respondo de Martin:] Reine Sicherheitsma�nahme. Das ist eine Funktion die mehrere Hundert eMails verschickt. Die hab ich totgelegt, damit da nicht versehentlich jemand draufdr�ckt.
	  // -> vielleicht mit Passwortschutz versehen, oder noch eine Sicherheitsabfrage.

      $to_name = funkciulo("admin");
      $to_address = funkciuladreso("admin");
    
      if ($eksendu=='J') 
      {
        echo "Das w�rs geworden!! Wenn Du diese Funktion benutzen willst, dann bitte im Quellcode anschalten";
        //$to_name=$row[personanomo]." ".$row[nomo];  // TODO: einkommentieren, wenn es losgehen soll && die Texte abgesegnet sind
        //$to_adress = $row[retposxto];
        $bcc = teknika_administranto_retadreso;
      }    
      sendu_2ankonfirmilon($row,$savu,$to_name,$to_address,$bcc);
     }
  }
}

if ($kio=='adres')
{
  require('iloj/kreu_adresaron.php');
}
if ($kio == "backup")
{
  // sekurkopioj de la tuta datumbazo

  chdir('../../../phplibraro/');
  require("backup.php");
  savu_datumbazon();
}
if ($kio == "backup_is")
{
  // sekurkopioj de la is-datumbazo

  chdir('../../../phplibraro/');
  require("backup.php");
  savu_projekton($tabelnomprefikso);
}



if ($kio == 'backup_programo')
{
  // sekurkopio de la programo

  $dir = getcwd();
  chdir('../../../phplibraro/');
  require_once('program-kopio.php');
  chdir($dir);
  chdir('..');
  kopiuProgramon("admin", "is-admin-" . date('Y-m-d-H-i'). ".tgz");
  // TODO
}

  
HtmlFino();

?>