<?php

// define('DEBUG', true);

/*
 * Noto por "estingi":
 *   ?dis_ago=estingi
 * forigas unue nur la montritan partoprenon,
 * kaj (se oni denove klakas cxe senpartoprena
 *  partoprenanto) poste la partoprenanton mem.
 */


require_once ('iloj/iloj.php');


  session_start();
  malfermu_datumaro();


kontrolu_rajton("vidi");


HtmlKapo();  
            

//kunigu kun la kunlogxanto (vokita el sercxrezultoj.php,
// la kunlogxanto-sercxo). 
if ($kune and $partoprenidento)
{  
  sxangxu_datumbazon("partoprenoj", array("kunkiuID" => $kune),
					 array("ID" => $partoprenidento));
}


if ( $forgesendalito )
{
  kontrolu_rajton("cxambrumi");
  forigu_el_datumbazo("litonoktoj", $forgesendalito);
}


if ($_REQUEST['partoprenantoidento'])
{
  $_SESSION["partoprenanto"] = new Partoprenanto($partoprenantoidento);
  unset($_SESSION["partopreno"]);
}

if ($_REQUEST['partoprenidento'])
{
  $_SESSION["partopreno"] = new Partopreno($partoprenidento);
  if ($_SESSION['partopreno']->datoj['partoprenantoID'] != 
		$_SESSION['partoprenanto']->datoj['ID'])
	{
		$_SESSION['partoprenanto'] =
			new Partoprenanto($_SESSION['partopreno']->datoj['partoprenantoID']);
	}
}


/*
 * ni difinas $partopreno_renkontigxo por uzi anstataux
 * $_SESSION['renkontigxo'], cxar gxi ja povus esti io alia
 * (se oni rigardas malnovan partoprenon, ekzemple).
 */ 
if ($_SESSION['partopreno'] and
	$_SESSION['partopreno']->datoj['renkontigxoID'] != $_SESSION['renkontigxo']->datoj['ID'])
{
  $partopreno_renkontigxo = new Renkontigxo($_SESSION['partopreno']->datoj['renkontigxoID']);
}
else
{
  $partopreno_renkontigxo = $_SESSION['renkontigxo'];
}

/**
 * - por ke la partoprenanto estu tiu, kiu
 *   rilatas al la partopreno.
 */
if ($_SESSION['partopreno'] and
	$_SESSION["partopreno"]->datoj['partoprenantoID'] !=
	$_SESSION['partoprenanto']->datoj['ID'])
{
  $_SESSION['partoprenanto'] =
	new Partoprenanto($_SESSION["partopreno"]->datoj['partoprenantoID']);
}





if ($kontrolata=='nova')
{
  $_SESSION["partoprenanto"]->datoj['malnova']='N';
  $_SESSION["partopreno"]->datoj['kontrolata']='J';
  $_SESSION["partopreno"]->skribu();
  $_SESSION["partoprenanto"]->skribu();
}
else if ($kontrolata=='mal')
{
  if ($_SESSION["partopreno"]->datoj['kontrolata']=='J')
	$_SESSION["partopreno"]->datoj['kontrolata']='N';
  else
	$_SESSION["partopreno"]->datoj[kontrolata]='J';
   $_SESSION["partopreno"]->skribu();
}

if ($venos=='mal')
{
  if ($_SESSION["partopreno"]->datoj[alvenstato]=='v')
	$_SESSION["partopreno"]->datoj[alvenstato]='m';
  else if ($_SESSION["partopreno"]->datoj[alvenstato]=='m')
	$_SESSION["partopreno"]->datoj[alvenstato]='a';
  else
	$_SESSION["partopreno"]->datoj[alvenstato]='v';
  $_SESSION["partopreno"]->skribu();
}
if ($trakti=='mal')
{
  if ($_SESSION["partopreno"]->datoj[traktstato]=='P') $_SESSION["partopreno"]->datoj[traktstato]='N';
  else if ($_SESSION["partopreno"]->datoj[traktstato]=='N') $_SESSION["partopreno"]->datoj[traktstato]='S';
  else $_SESSION["partopreno"]->datoj[traktstato]='P';
  $_SESSION["partopreno"]->skribu();
}

if ($asekuri=='mal')
{
  if ($_SESSION["partopreno"]->datoj[asekuri]=='N') $_SESSION["partopreno"]->datoj[asekuri]='E';
  else if ($_SESSION["partopreno"]->datoj[asekuri]=='E') $_SESSION["partopreno"]->datoj[asekuri]='J';
  else $_SESSION["partopreno"]->datoj[asekuri]='N';
  $_SESSION["partopreno"]->skribu();
}


if ($mangxkup=='mal')
{
  if ($_SESSION["partopreno"]->datoj[havasMangxkuponon]=='J') $_SESSION["partopreno"]->datoj[havasMangxkuponon]='N';
  else if ($_SESSION["partopreno"]->datoj[havasMangxkuponon]=='N') $_SESSION["partopreno"]->datoj[havasMangxkuponon]='P';
  else $_SESSION["partopreno"]->datoj[havasMangxkuponon]='J';
  $_SESSION["partopreno"]->skribu();
}

if ($nomsxildo=='mal')
{
  if ($_SESSION["partopreno"]->datoj[havasNomsxildon]=='J') $_SESSION["partopreno"]->datoj[havasNomsxildon]='N';
  else if ($_SESSION["partopreno"]->datoj[havasNomsxildon]=='N') $_SESSION["partopreno"]->datoj[havasNomsxildon]='P';
  else $_SESSION["partopreno"]->datoj[havasNomsxildon]='J';
  $_SESSION["partopreno"]->skribu();
}

if ($sendu=='Tiu')
{
  echo "Partopreno nun apartenas al #$kune";
  $_SESSION["partopreno"]->datoj[partoprenantoID]=$kune;
  $_SESSION["partopreno"]->skribu();
}
if ($sendu=='Transferu')
{
  // TODO: Umstellen auf bessere Auswahl - siehe unten bei "peter"
  // (eventuell muss dass hier gar nicht geändert werden.)
  echo "C^io nun apartenas al #$kune";
  sxangxu_datumbazon("partoprenoj",
					 array("partoprenantoID" => $kune),
					 array("partoprenantoID" => $antauxa));
  sxangxu_datumbazon("notoj",
					 array("partoprenantoID" => $kune),
					 array("partoprenantoID" => $antauxa));
}

  if ($faru==sendukonfirmo)
  {
	$teksto = "";
    sendu_konfirmilon($_SESSION["partoprenanto"],$_SESSION["partopreno"],$partopreno_renkontigxo, $teksto);
    echo "Konfirmilo sendata al ".$_SESSION["partoprenanto"]->datoj[retposxto];
	$_SESSION['partopreno']->datoj['1akonfirmilosendata']=date("Y-m-d");
	$_SESSION['partopreno']->skribu();

  }
  if ($faru=="2konfirmi")
  {
	require_once ('iloj/kreu_konfirmilon.php');
	
	if (DEBUG)
	  {
		echo "<!-- bezonas_unikodon: [" . (string)bezonas_unikodon($_SESSION['partoprenanto']) .
		  "] -->";
	  }
	$kon = new Konfirmilo(bezonas_unikodon($_SESSION['partoprenanto']));
	$kon-> kreu_konfirmilon($_SESSION["partopreno"]->datoj[ID],
							$_SESSION["partoprenanto"]->datoj[ID]);
	// kreas PDF-dosieron, ne sendas, spite la nomo.
	$kon->sendu();
  }
  if ($faru=='2konfirmelsendo')
  {
	kontrolu_rajton("retumi");
	
	$to_name=$_SESSION["partoprenanto"]->datoj[personanomo]." ".$_SESSION["partoprenanto"]->datoj[nomo]; 
	$to_address = $_SESSION["partoprenanto"]->datoj[retposxto];
	
	// TODO: Übergabeparameter verschönern
	sendu_2ankonfirmilon(array('0'=>$_SESSION["partoprenanto"]->datoj[ID],
							   '1'=>$_SESSION["partopreno"]->datoj[ID],
							   'agxo'=>$_SESSION["partopreno"]->datoj['agxo'],
							   "germane"=>$_SESSION['partopreno']->datoj['germanakonfirmilo']),
						 'J',
						 $to_name,
						 $to_address/*,'is.admin@esperanto.de'*/);    //TODO: dann ändern  
	
  }
if ($faru=='2konfirm_papere')
{
    // nur notu en la datumbazo, ke ni nun sendas gxin papere
    $_SESSION['partopreno']->datoj['2akonfirmilosendata'] = date('Y-m-d');
    $_SESSION['partopreno']->skribu();
    $_SESSION['partopreno'] = new Partopreno($_SESSION['partopreno']->datoj['ID']);
}


  echo "<table border=2>\n";
  echo "<TR><TD >\n";
	// TODO: estingi-ligo
  $_SESSION["partoprenanto"]->montru_aligxinto();
  
  rajtligu ("partoprenanto.php?ago=sxangxi&sp=partrezultoj.php","--> s^ang^i personajn datojn","","sxangxi","jes");
  echo "<BR>\n";
  rajtligu ("partopreno.php?sp=forgesi&partoprenantoidento=".$_SESSION['partoprenanto']->datoj['ID'],"--> aligi al renkontig^o","","aligi","jes"); // TODO:? später auch noch dynamisch ;)
  echo "<BR>\n";
  //ligu ("partrezultoj.php?partoprenantoidento=" . $_SESSION["partoprenanto"]->datoj[ID],"--> vidu c^iu partopreno");
  //echo "<BR>\n";
  rajtligu("sendumesagxon.php","--> preparu mesag^on","","retumi","ne");
  echo "<BR>\n";
  

/* TODO: stattdessen Suche nach ähnlichen Namen,
         Auswahl des passenden
   TODO: alte Teilnahmen zuschlagen.
*/

// TODO:
  rajtligu("transferi.php", "--> serc^u similajn partoprenantojn kaj (eble) transferu partoprenojn", "", 'vidi');

  echo "<BR>\n";

     
  
  //Montras cxiujn partoprenojn
  //  $sql = "Select id, renkontigxoid, de,gxis from partoprenoj where partoprenantoID='".$_SESSION["partoprenanto"]->datoj[ID]."' order by renkontigxoID";
  $sql = datumbazdemando(array("id", "renkontigxoID"),
						 "partoprenoj",
						 "",
						 array("partoprenanto" => "partoprenantoID"),
						 array("order" => "renkontigxoID"));
  $result = sql_faru($sql);
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    echo "<BR>";
    ligu("partrezultoj.php?partoprenantoidento=".$_SESSION["partoprenanto"]->datoj['ID']."&partoprenidento=".$row['id'],eltrovu_renkontigxo($row["renkontigxoID"]));
  }

echo "<br/>";

$sql = datumbazdemando(array('COUNT(ID)' => 'nombro'),
					   "notoj",
					   "",
					   array("partoprenanto" => "partoprenantoID"));

$rez= sql_faru($sql);
$linio = mysql_fetch_assoc($rez);
$notojentute = $linio['nombro'];
						
$sql = datumbazdemando(array('COUNT(ID)' => 'nombro'),
					   "notoj",
						array("prilaborata = ''"),
						array("partoprenanto" => "partoprenantoID")
						);
$rez= sql_faru($sql);
$linio = mysql_fetch_assoc($rez);
$notojfarendaj = $linio['nombro'];

$sql = datumbazdemando(array('COUNT(ID)' => 'nombro'),
					   "notoj",
						array("prilaborata = ''",
							  "revidu <= NOW()"),
						array("partoprenanto" => "partoprenantoID")
						);
$rez= sql_faru($sql);
$linio = mysql_fetch_assoc($rez);
$notojaktualaj = $linio['nombro'];



eoecho("<p>Estas entute {$notojentute} " . 
	   "<a href='sercxrezultoj.php?elekto=notojn&partoprenantoidento={$_SESSION['partoprenanto']->datoj['ID']}'>notoj pri " .
	   "{$_SESSION['partoprenanto']->datoj['personanomo']}</a>, el " .
	   "tiuj ankorau^ {$notojfarendaj} neprilaboritaj, el tiuj " .
	   ($notojaktualaj > 0 ?
		"estas <strong class='averto'>{$notojaktualaj}&nbsp;jam remontrendaj</strong>." :
		"estas neniuj jam remontrendaj." ) .
	   "</p>");

  
  echo "</TD><TD>\n";
  
  if (empty($partoprenidento) && empty($_SESSION['partopreno']))
  {
	// sercxu partoprenon de la aktuala renkontigxo por la partoprenanto,
	// kaj elektu tiun kiel $_SESSION['partopreno'].

	$sql = datumbazdemando(array("id", "renkontigxoid", "de", "gxis", "venos"),
						   "partoprenoj",
						   "",
						   array("renkontigxo" => "renkontigxoID",
								 "partoprenanto" => "partoprenantoID"),
						   array("limit" => "0, 10"));
	$result = sql_faru($sql);
	
	if (mysql_num_rows($result)==1)
	  {
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$_SESSION["partopreno"] = new Partopreno($row[0]);
		session_register("partopreno");
	  }
  }


   
  if ((empty($_SESSION["partopreno"])) and (mysql_num_rows($result)!=1))
  {

    if (mysql_num_rows($result)>1)
    {
      echo "<TABLE border=1>\n";
      eoecho ("<tr> <th> vidu </th>\n");
      echo "     <th> ID </th>\n";
      eoecho ("     <th> renkontig^o </th>\n");
      //echo "     <th> partoprenantoID </th>";
      echo "     <th> de </th>\n";
      echo "     <th> gxis </th>\n";
      echo "     <th> venos </th></TR>\n";

      while ($row = mysql_fetch_array($result, MYSQL_NUM))
      {
        $row[1]=eltrovu_renkontigxo($row[1]);
        echo "<TR> <TD>\n";
        ligu ("partrezultoj.php?partoprenidento=$row[0]","-->");
        echo "</TD><TD>\n";
        eoecho (implode(" </TD><TD> ",$row));
        echo "</TR>\n";
      }
      echo "</TABLE>\n";
    }
    else if($dis_ago=='estingi')
    {
	  forigu_laux_sesio("partoprenantoj", "partoprenanto");
      echo "<font color=red>Partoprenanto #".$_SESSION["partoprenanto"]->datoj[ID]." estingata</font>";
    }
    else eoecho ("Ri g^is nun ne alig^is\n");
  }
  
  else if($dis_ago=='estingi')
    {
	  forigu_laux_sesio("partoprenoj", "partopreno");
      echo "<font color=red>Partopreno estingata</font>";
      unset($_SESSION["partopreno"]);
    }
  else
  {
    $_SESSION["partopreno"]->montru_aligxo();
	//// ne plu necesas, ni ne plu havas malnovajn partoprenantojn.
	//     if ($_SESSION["partoprenanto"]->datoj[malnova]=='J')
	//       rajtligu ("partrezultoj.php?kontrolata=nova","Malnova: ".$_SESSION["partoprenanto"]->datoj[malnova],'',"estingi");
    // else

    $invitpeto = $_SESSION['partopreno']->sercxu_invitpeton();
    if($invitpeto)
        {
            if ($_REQUEST['montru_invitpeton'])
                {
                    $invitpeto->montru_detalojn();
                }
            else
                {
                    ligu("partrezultoj.php?montru_invitpeton=jes",
                         "Montru invitpeto-detalojn");
                }
            rajtligu("invitpeto.php", "redaktu invitpeto-datojn", "", "inviti");
            echo "<br/>";
        }
    else
        {
            rajtligu("invitpeto.php", "aldonu invitpeto-datojn", "", "inviti");
        }


	rajtligu ("partrezultoj.php?kontrolata=mal","kontrolata: ".$_SESSION["partopreno"]->datoj['kontrolata'],'',"estingi");
	
	switch($_SESSION["partopreno"]->datoj[alvenstato])
	  {
	  case 'a': 
		$alvenstato = 'alvenis'; break;
	  case 'v':
		$alvenstato = 'venos'; break;
	  case 'm':
		$alvenstato = 'malalig^is'; break;
	  default:
		$alvenstato = '? ['.$_SESSION["partopreno"]->datoj[alvenstato] .']';
	  }

    rajtligu ("partrezultoj.php?venos=mal",
			  "alvenstato:", '',"estingi");
	eoecho ("&nbsp;" .$alvenstato);

    rajtligu ("partrezultoj.php?trakti=mal",
			  "trakto: ".$_SESSION["partopreno"]->datoj[traktstato],'',"estingi",'ne');
 rajtligu ("partrezultoj.php?asekuri=mal","asekuri: ".$_SESSION["partopreno"]->datoj[asekuri],'',"estingi",'ne');
    echo "<BR>\n";
    rajtligu ("partrezultoj.php?mangxkup=mal","Mang^kupono: ".$_SESSION["partopreno"]->datoj[havasMangxkuponon],'',"estingi",'ne');
    rajtligu ("partrezultoj.php?nomsxildo=mal","Noms^ildo: ".$_SESSION["partopreno"]->datoj[havasNomsxildon],'',"estingi",'ne');
    echo "<BR>\n";
    rajtligu ("partopreno.php?partoprenidento=" . $_SESSION['partopreno']->datoj['ID']
			  . "&ago=sxangxi",
			  "--> s^ang^i la partoprenon",
			  "",
			  "sxangxi",
			  "jes");
    echo "<BR>\n";
    ligu ("partrezultoj.php?faru=konfirmi","--> produkti 1an konfirmilon");
    echo "<BR>\n";
	rajtligu ("partrezultoj.php?faru=ekzporti", "--> sendu sekurkopion retpos^te", "", "retumi");
    echo "<br />\n";
	rajtligu ("partrezultoj.php?faru=programmesagxoj", "--> sendu au^tomatajn mesag^ojn al programrespondeculoj ktp.", "", "retumi");
    echo "<br />\n";
    rajtligu ("partrezultoj.php?faru=2konfirmi","--> produkti 2an konfirmilon","","administri","jes");
    rajtligu ("partrezultoj.php?faru=2konfirm_papere","(paperpos^te sendita)", "", "administri", "jes");
    if ($faru=="2konfirmi")
    {    
      hazard_ligu ("dosieroj_generitaj/konfirmilo.pdf","(els^uti g^in)");
      rajtligu ("partrezultoj.php?faru=2konfirmelsendo","--> elsendi 2an konfirmilon","","administri","jes");
    }
    echo "<BR>\n";

    /*if ($_SESSION["partoprenanto"]->datoj[retposxto])
    {
      ligu ("partrezultoj.php?faru=sendukonfirmo","--> sendi 1an konfirmilon");
      echo "<BR>\n";
    }*/

/*    if ($partopreno->datoj[antauxpago] != 0)
    {
      rajtligu ("antauxpago.php","--> s^ang^i la antau^pago","","mono");
    }
    else
    {*/
            
   // }
     echo "<table><tr><td>";
     rajtligu ("antauxpago.php","--> entajpi pagon","","mono","ne");
     echo "</td><td>";
     rajtligu ("rabato.php","--> entajpi rabaton","","rabati","ne"); 
     echo "</td></tr><tr><td>";      
	 $sql = datumbazdemando(array("ID", "partoprenoID", "kvanto", "tipo", "dato"),
							"pagoj",
							"",
							array("partopreno" => "partoprenoID"));
     sercxu($sql,
		   array("dato","desc"),
			array(array('0','','->','z','"antauxpago.php?jena=XXXXX"',''),
				  array('dato','dato','XXXXX','l','','-1'), 
				  array('kvanto','sumo','XXXXX','r','','-1'), 
				  array("tipo","tipo",'XXXXX','l','','-1')
				  ),
      array(array('','',array('&sum; XX','N','z'))),
			"pagoj-partrezultoj",
			0,0,"",'','ne'); 
	 echo "</td><td>";
	 $sql = datumbazdemando(array("ID", "partoprenoID", "kvanto", "kauzo"),
							"rabatoj", "",
							array("partopreno" => "partoprenoID"));
	 sercxu($sql, 
			array("kauzo","desc"),
			array(array('0','','->','z','"rabato.php?jena=XXXXX"',''),
				  array('kvanto','sumo','XXXXX','r','','-1'),
				  array("kauzo","kauzo",'XXXXX','l','','')
				  ), 
			array(array('',array('&sum; XX','N','z'))),
			"rabatoj-partrezultoj",
			0, 0, "",'','ne');
	  echo "</td></tr></table>\n";

	  if (!$_SESSION["partoprenanto"]->datoj['lando'])
		{
		  erareldono("Mankas la lando, pro tio la kotizokalkulo estas iom necerta!");
		}
	  $kot = new Kotizo($_SESSION["partopreno"],
						$_SESSION["partoprenanto"],
						$partopreno_renkontigxo);
	  
	  eoecho("Restas pagenda: ". $kot->restas_pagenda() . " E^");

	  echo " </td></tr>\n";

    echo "<tr><td class='kalkulilo' colspan=1>";
	//	<A href=partrezultoj.php?montrukotizo=";
    if ($montrukotizo!="montru")
    {
      ligu( "partrezultoj.php?montrukotizo=montru", "montru kotizkalkuladon....");;
    }
    else
    {
	  ligu ("partrezultoj.php?montrukotizo=kasxu", "kas^u kotizkalkuladon....");
	  echo "<table id='rezulto'>\n ";

	  $kot->montru_kotizon(0,$_SESSION["partopreno"],$_SESSION["partoprenanto"],
						   $partopreno_renkontigxo);

	  echo "</table>\n";
    }
    echo "</td><td>";
    if ($_SESSION['partopreno']->datoj['alvenstato'] == 'v' and
		  $_SESSION['partopreno']->datoj['renkontigxoID'] ==
			 $_SESSION['renkontigxo']->datoj['ID']
			 // nur permesu akceptigxi al la aktuala renkontigxo
		  )
	 {
       rajtligu("akceptado-datoj.php","akcepti","","akcepti");
	 }

     echo "</td></tr>\n";
    // gehört eigentlich nach montru_aligxo; -> Nee.

    echo "<tr><td>";
	
	/** kiam ri estas en kiu cxambro? */
 
    $rezulto = eltrovu_cxambrojn($_SESSION["partopreno"]->datoj[ID]);
    while ($row = mysql_fetch_array($rezulto, MYSQL_NUM))
    {
	  $sql = datumbazdemando("nomo", "cxambroj", "id = '$row[0]'");
      $cxambronomo = mysql_fetch_array(sql_faru($sql),MYSQL_NUM);

      eoecho (sekvandaton($partopreno_renkontigxo->datoj[de], $row[1]-1).
			  " - ".
			  sekvandaton($partopreno_renkontigxo->datoj[de],$row[2]).
			  " ($row[3])\n");
	  if (rajtas("cxambrumi"))
		{
		  ligu ("cxambroj.php?cxambronombro=$row[0]","c^ambro: $cxambronomo[0]");
		}
	  else
		{
		  eoecho( "c^ambro: ".$cxambronomo[0]. " ");
		}
      rajtligu ("partrezultoj.php?forgesendalito=$row[4]", "forgesu", "", "cxambrumi",
				"jes"); echo "<br/>";
      $valoro = "true";
    }

    // TODO: Anzeigen, ob für jede Nacht ein Zimmer da ist.      FEHLT Im Moment noch
    $manko = eltrovu_litojn( $_SESSION["partoprenanto"]->datoj[ID]);

    if (($_SESSION["partopreno"]->datoj[domotipo]=="J"))
    {
      if (!$valoro)
      {
        eoecho ($_SESSION["partoprenanto"]->personapronomo." g^is nun ne havas c^ambron.<BR>");
        rajtligu ("cxambroj.php?cx_ago=forgesu","elektu unu", "", "cxambrumi", "jes");
      }
      else
      {
        rajtligu ("cxambroj.php?cx_ago=forgesu","elektu plian", "", "cxambrumi");
      }
      rajtligu ("cxambro_sxangxo.php","s^ang^i kun aliulo", "", "cxambrumi", "");
    }
    echo "</TD><TD>";

	if (nova_kunlogxado)
	  {
		$ri = $_SESSION['partoprenanto']->personapronomo;
		$Ri = ucfirst($ri);
		eoecho("Kunlog^deziroj de kaj pri $ri:");  // !!!!!!!!!!!!!!!!
		require_once($prafix . "/iloj/iloj_kunlogxadoj.php");
		montru_kunlogxdezirojn_ambauxdirekte($_SESSION['partopreno'],
											 //$_SESSION['partoprenanto']->tuta_nomo()
											 "&uarr;");
		
		// provizore nur por teknikumistoj, por elprovi:
		rajtligu("kunlogxado.php?ago=sercxu", "serc^u kunlog^dezirojn", "", "teknikumi");
	  }

  }
  echo "</TD></TR></TABLE>\n";


if ($faru==konfirmi)
{
  echo nl2br(faru_1akonfirmilon($_SESSION["partoprenanto"],$_SESSION["partopreno"],$partopreno_renkontigxo));
  echo "<BR><BR>";
  if (($_SESSION["partoprenanto"]->datoj[retposxto])and(rajtas(retumi)))
	ligu ("partrezultoj.php?faru=sendukonfirmo","--> sendi 1an konfirmilon");
}
if ($faru == "junaMaljuna")
{
  kontrolu_rajton("retumi");

  sendu_mesagxon_se_troagxa($_SESSION["partopreno"], $_SESSION["partoprenanto"], $partopreno_renkontigxo);
  sendu_mesagxon_se_juna_aux_nova($_SESSION["partopreno"], $_SESSION["partoprenanto"], $partopreno_renkontigxo);
  eoecho("<p>Mi testis. c^u necesas sendi mesag^ojn pro agxo, ".
		 "kaj eble sendis.</p>\n");
}
if ($faru == "ekzporti")
{
  sendu_ekzport($_SESSION["partoprenanto"],$_SESSION["partopreno"], $partopreno_renkontigxo);
  echo "<p> Sekurkopio sendita al la administranto. </p>";
}
if ($faru == "programmesagxoj")
{
  sendu_auxtomatajn_mesagxojn($_SESSION['partopreno'], $_SESSION['partoprenanto'], $partopreno_renkontigxo);
  echo "<p> Informaj mesagxoj senditaj al program- kaj aliaj responduloj</p>";
}

  // kommt bald
  //$rez = faru_konfirmilon($_SESSION["partoprenanto"],$partopreno,$renkontigxo);
    //echo nl2br(htmlentities($rez));


HtmlFino();

?>
