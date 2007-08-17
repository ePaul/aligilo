<?php


/*
 * Akzeptado de partoprenantoj
 *
 * - Diversaj kontrolajxoj
 * - kotizkalkulado, kolekto de la pagenda mono
 * - montro kaj disdono de la cxambro
 * - disdono de malgrandajxoj
 *
 * Sube sur la pagxo estas butono kiu
 * ebligas aldoni la pagon.
 * Tiam la vokigxos la pagxo per
 *  $sendu - nomo de la butono (signo, ke oni sendis)
 *  $pago - la valoro de la pago
 *
 */



require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

if (!rajtas("akcepti"))
{
  ne_rajtas();
}

$pasxo = $_REQUEST['pasxo'];
if (!$pasxo)
{
	$pasxo = 'datumoj'; // unua pasxo
}

if ($sendu == "Akceptu!")
{
  $pago = new Pago(0);
  $pago->datoj[partoprenoID] = $_SESSION["partopreno"]->datoj[ID];
  $pago->datoj[kvanto]=$kvanto;
  $pago->datoj[dato]=date("Y-m-d");
  $pago->datoj[tipo]='surlokpago';
  $pago->kreu(); 
  $pago->skribu(); 
  
  $mono = new Monujo(0);
  $mono->datoj[renkontigxo]=$_SESSION["renkontigxo"]->datoj[ID];
  $mono->datoj[kvanto]=$kvanto;
  $mono->datoj[kauzo]="surlokpago de ";
  $mono->datoj[tempo]=date("Y-m-d H:M:s");
  $mono->datoj[kvitanconumero]='0';                    
  $mono->datoj[alKiu]='';
  $mono->datoj[kiaMonujo]='kaso 1';
  $mono->kreu();
  $mono->skribu();

  if ($_SESSION["partopreno"]->datoj[domotipo]=='J')
  {


	sxangxu_datumbazon("litonoktoj", array("rezervtipo" => "d"), "", "partopreno");
  }
  // TODO:? Kial ne rekte sxangxi $_SESSION["partopreno"] kaj ->skribu()?
  sxangxu_datumbazon("partoprenoj",
					 array("alvenstato" => "a"),
					 "",
					 array("ID" => "partopreno"));
  sxangxu_datumbazon("partoprenoj",
					 array("havasMangxkuponon" => "J"),
					 "",
					 array("ID" => "partopreno", "havasMangxkuponon" => "P"));
  sxangxu_datumbazon("partoprenoj",
					 array("havasNomsxildon" => "J"),
					 "",
					 array("ID" => "partopreno", "havasNomsxildon" => "P"));
  
}
if($sendu == "sxangxu_membrokotizon")
{
  $_SESSION['partopreno']->datoj['surloka_membrokotizo'] = $surloka_membrokotizo;
  if ($surloka_membrokotizo == 'n')
	{
	  $_SESSION['partopreno']->datoj['membrokotizo'] = 0;
	}
  else
	{
	  $_SESSION['partopreno']->datoj['membrokotizo'] = $membrokotizo;
	}
  $_SESSION['partopreno']->skribu();
  $_SESSION['partopreno'] = new Partopreno($_SESSION['partopreno']->datoj['ID']);
}


HtmlKapo();

  $partoprenanto = $_SESSION["partoprenanto"];
  $partopreno = $_SESSION['partopreno'];

  // la persona pronomo (li aux sxi)
  $ri = $partoprenanto->personapronomo;
  $Ri = ucfirst($ri);
  
  eoecho ("<p>Ni nun akceptas <b>".$partoprenanto->datoj[personanomo]." ".
		  $partoprenanto->datoj[nomo]." </b>(".$partoprenanto->datoj[ID].
		  ") al la <b>".$_SESSION["renkontigxo"]->datoj[nomo]."</b>.</p>");
  

  // Demandendaxjoj ...

switch($pasxo)
{

	// #####################################################################################

	case 'datumoj':
?>

<h2>Akceptada proceduro &ndash; Pa&#349;o 1 (Datumoj)</h2>
<ul>
<?php

if (ministeriaj_listoj == 'jes')
{
	eoecho("<li>Donu al {$ri} la ministerian liston por ");
	if ($partoprenanto->datoj["lando"] == HEJMLANDO)
	{
		eoecho(ministeriaj_listoj_hejmlando);
	}
	else
	{
		eoecho(ministeriaj_listoj_eksterlando);
	}
	eoecho (", kaj igu {$ri}n enskribi {$ri}ajn datojn. " .
			  "(Dume eblas dau^rigi per la sekva punkto.)</li>");
}

  eoecho("<li><p>C^u {$ri} s^ang^is personajn au^ partoprenajn datumojn sur" .
			" la akceptofolio? </p>\n");
	echo "<table><col style='' /><col />";
	eoecho ("<tr><th>Personaj datumoj</th><th>Partoprenaj datumoj</th></tr>\n");
	echo "<tr><td>";
   $partoprenanto->montru_aligxinto(true);
	echo "</td><td>";
   $partopreno->montru_aligxo(true);	
   echo "</td></tr></table>\n";

  ligu("partoprenanto.php?ago=sxangxi&sp=akceptado.php?pasxo=datumoj", "nur personajn");
  ligu("partopreno.php?ago=sxangxi&sp=akceptado.php?pasxo=datumoj", "nur partoprenajn");
  ligu("partoprenanto.php?ago=sxangxi&sp=partopreno.php?ago=sxangxi%26sp=akceptado.php?pasxo=datumoj", "ambau^");
  ligu("akceptado.php?pasxo=kontroloj", "neniujn");
  eoecho ("</li>");
?>
</ul>
<?php

	break; // case 'datoj'
	// ###############################################################################
	case 'kontroloj':

?>
<h2>Akceptada proceduro &ndash; Pa&#349;o 2 (Kontroloj)</h2>
<p>(Reen al <a href='akceptado.php?pasxo=datumoj'>datumoj</a>)</p>
<ul>
<?php
eoecho("	<li>Eble kontrolu {$ri}an log^landon (estu " .
       $partoprenanto->landonomo() ." / " .
       $partoprenanto->loka_landonomo() ." &ndash; ");
ligu("partoprenanto.php?ago=sxangxi&sp=akceptado.php?pasxo=datumoj", "s^ang^u!");
echo (").</li>\n");

// if($partopreno->datoj['agxo'] < 36) // TODO: prenu limagxon el datumbazo
{
	eoecho("<li> Eble kontrolu lian ag^on, {$ri} asertis esti naskita je " .
				 $partoprenanto->datoj['naskigxdato'] . " (nun " .
				 $partopreno->datoj['agxo']."-jara) (");
	ligu("partoprenanto.php?ago=sxangxi&sp=akceptado.php?pasxo=kontroloj", "s^ang^u!");
	echo (") </li>\n");
}

  if ($partopreno->datoj['agxo'] < 18)
	{
	  eoecho ("<li>Kolektu la gepatran permeson (se g^i mankas donu faksnumeron de" .
 			    " la ejo, kaj insistu ke {$ri} donos g^in.).</li>\n");
	}


	echo "<p>";
	ligu ("akceptado.php?pasxo=tejo", "C^io estas en ordo.");

	break; // case 'kontroloj'
	// ###############################################################################
	case 'tejo':

	if(TEJO_RABATO > 0 and
		TEJO_AGXO_LIMDATO <= $partoprenanto->datoj['naskigxdato'])
	{
		eoecho ("<p>(Reen al: ");
	  ligu('akceptado.php?pasxo=datumoj', "datumoj")
	  ligu('akceptado.php?pasxo=Kontroloj', "Kontroloj")
		echo ")</p>";

		// ebla TEJO-membro

?>
	<h2>Akceptada proceduro &ndash; Pa&#349;o 3 (TEJO)</h2>

<?php

		// TODO

		break;

	}
	else
	{
		eoecho("<p>Pas^o 3 (TEJO) ne necesas, c^ar {$ri} estas jam tro ag^a.</p>");
		// kaj tuj montru la sekvan pasxon.
	}

	// ###############################################################################
	case 'gea':
	if (deviga_membreco_tipo == 'monda' or
		 (deviga_membreco_tipo == 'landa' and
		  $partoprenanto->datoj['lando'] == HEJMLANDO))
	{
		eoecho("<h2>Akceptada proceduro &ndash; Pas^o 4 (GEA/GEJ)</h2>");
		eoecho ("<p>(Reen al: ");
	  ligu('akceptado.php?pasxo=datumoj', "datumoj")
	  ligu('akceptado.php?pasxo=Kontroloj', "Kontroloj")
		echo ")</p>";

		// TODO



		break;
	}
	else if (deviga_membreco_tipo == 'landa')
	{
		eoecho("<p>Pas^o 4 (GEA) ne necesas, c^ar {$ri} estas eksterlandano.</p>");
		// kaj tuj montru la sekvan pasxon.
	}
	else
	{
		eoecho("<p>Pas^o 4 (GEA) ne necesas, c^ar ne estas deviga membreco por iu ajn.</p>");
	}

	echo "</ul><hr style='border-bottom: solid;'/>";


  eoecho ("<h2>Demandendas:</h2>\n<UL>\n");
  

  
  
  // Krompago por ne-membroj
  
  echo "<li>\n";
  if (deviga_membreco_tipo=='landa' and
		($partoprenanto->datoj["lando"] == HEJMLANDO )) // kutime Hejmlando = Germanio
	{
	  eoecho("$Ri log^as en ".renkontigxolando.". C^u $ri jam estas membro de ".deviga_membreco_nomo."?<br />\n");
	  $membrokontrolo = true;
	}
  else if (deviga_membreco_tipo=='monda')
    {
      eoecho("C^u $ri jam estas membro de ".deviga_membreco_nomo."?<br />\n");
	  $membrokontrolo = true;
    }

  if($membrokontrolo)
	{

// 	  eoecho("Lau^ {$ri}a alig^formularo, ");
// 	  if($_SESSION["partopreno"]->datoj["GEJmembro"]{0} == 'J')
// 		{
// 		  eoecho("$ri jam estas membro. Kontrolu! (kaj eble");
// 		  ligu("partopreno.php?ago=sxangxi", "s^ang^u"); eoecho(".)");
// 		}
// 	  else
// 		{
// 		  eoecho("$ri ankorau^ ne estas membro.<br />");
// 		  eoecho("$Ri "); ligu("partopreno.php?ago=sxangxi", "ig^u membro");
// 		  eoecho(" (kaj pagos la kotizon por 2005)." .
// 				 "Alikaze $ri pagos samgrandan krompagon.");
// 		}
	  eoecho("Atentu, kion skribis la membroadministranto sur la folion!<br/>");

	  eoecho ("<form action='akceptado.php' method='POST'>\n");
	  entajpbutono("", "surloka_membrokotizo",
				   $_SESSION['partopreno']->datoj['surloka_membrokotizo'],
				   'n', 'n', "$Ri ne devos pagi por la sekva jaro ($ri jam ".
				   "antau^e pagis au^ donis enkasigrajton).<br/>", "kutima");
	  entajpbutono("", "surloka_membrokotizo",
				   $_SESSION['partopreno']->datoj['surloka_membrokotizo'],
				   'j', 'j', "$Ri estas membro kaj pagu la membrokotizon por".
				   " la sekva jaro ...<br/>");
	  entajpbutono("", "surloka_membrokotizo",
				   $_SESSION['partopreno']->datoj['surloka_membrokotizo'],
				   "k", "k", "$Ri rifuzas membrig^i kaj pro tio pagu krompagon... <br/>");
	  entajpejo ("... de ",'membrokotizo',$_SESSION['partopreno']->datoj['membrokotizo'],
				 5,"",""," E^.");
	  eoecho("<button name='sendu' value='sxangxu_membrokotizon'>S^ang^u</button>");
	  echo "</form>\n";
	}
  else
	{
	  eoecho ("Li ne log^as en Germanio kaj tial ne devas membrig^i.");
	  ligu("akceptado.php?membrokontrolo=jes","$Ri tamen estas membro!","");
	}
  eoecho("</li>\n");
  
  eoecho ("<li>$Ri suskribu la subtenliston</li>");

  // Listo de notoj - eble io estas ankoraux farenda

  {
	eoecho ("<li>Se ekzistas neprilaborata noto, rigardu g^in</li>");
	echo "</ul>";
	
	// "select ID,prilaborata,dato,partoprenantoID,subjekto,kiu,kunKiu,tipo from notoj where partoprenantoID='".$partoprenanto->datoj[ID]."'"
	
	$sql = datumbazdemando(array("ID", "prilaborata", "dato", "partoprenantoID",
								 "subjekto","kiu", "kunKiu","tipo"),
						   "notoj",
						   "",
						   array("partoprenanto" => "partoprenantoID"));
	
	sercxu($sql, 
		  array("dato","desc"), 
		  array(array('ID','','->','z','"notoj.php?wahlNotiz=XXXXX"','-1'), 
				array('prilaborata','prilaborata?','XXXXX','z','','-1'), 
				array('dato','dato','XXXXX','l','','-1'), 
				array('subjekto','subjekto','XXXXX','l','','-1'), 
				array("kiu","kiu",'XXXXX','l','','-1'), 
				array("kunKiu","kun Kiu?",'XXXXX','l','','-1'), 
				array("tipo","tipo",'XXXXX','l','','-1')
				), 
		  array(array('',array('&sum; XX','A','z'))),
		  "notoj-akceptado",
		  array('Zeichenersetzung'=>
				array('1'=>array('j'=>'<strong class="malaverto">prilaborata</strong>',
								 ''=>'<strong class="averto">neprilaborata</strong>',
								 'n'=>'<strong class="averto">neprilaborata</strong>')
					  ),
				),
		  0,'','','ne');
  }

  
  // Kotizokalkulado kaj kotizkolektado

  echo "<table>";
  $kot = new Kotizo($_SESSION["partopreno"],$partoprenanto,$_SESSION["renkontigxo"]);
  $kot->montru_kotizon(0,$_SESSION["partopreno"],$partoprenanto,$_SESSION["renkontigxo"]);
  echo "</table>";

  echo "<form action='akceptado.php' method='post'>";

  eoecho ("<h2>Farendas:</h2><ul>");
  echo "<li>Kolektu la pagon de:";

  entajpejo ("",kvanto,$nenio,5,"",""," E^ (Se mankas mono nepre prenu garantiaj^on!)");
  echo "</li>";

  /************** cxambromontrado kaj -disdono ************/

  if ($_SESSION["partopreno"]->datoj[domotipo]=='J')
	{
	  $row = mysql_fetch_array(eltrovu_cxambrojn($_SESSION["partopreno"]->datoj[ID]),
							   MYSQL_NUM);
	  echo "<li>";
	  montru_cxambron($row[0],$_SESSION["renkontigxo"],
					 $partoprenanto,$_SESSION["partopreno"],"malgranda");
	  eoecho ("<br />Notu la c^ambronumero sur {$ri}a bros^uro</li>");
	}
  else
	{
	  eoecho ("<li>Notu 'M' kiel c^ambronumero sur {$ri}a bros^uro</li>");
	}

  /******** Disdono de diversajxoj *************/

  eoecho ("<li>Eldonu la broschuron.</li>");
  if ($_SESSION["partopreno"]->datoj[domotipo]=='J' or
	  $_SESSION["partopreno"]->datoj[kunmangxas]=='J')
	{
	  eoecho ("<li>Eldonu la mang^kuponon.</li>");
	}
 
  eoecho ("<li>Eldonu la noms^ildon.</li>");
 
  if ($_SESSION["partopreno"]->datoj[komencanto]=='J')
	eoecho ("<li>Donu 'kiel funkcias IS'.</li>");
  else if (!jampartoprenis($partoprenanto,$_SESSION["renkontigxo"]))
	{
	  eoecho ("<li>C^u estas via unua IS? (Se jes, donu <em>Kiel funkcias IS</em>)</li>");
	}
  eoecho ("</ul>");

  /********** Jen la akceptbutono *************/
  
  send_butono("Akceptu!");
  ligu("partrezultoj.php","reen","");
  echo "</form>";

  break;
  default:
	eoecho ("pasxo: " . $pasxo);

} // switch($pasxo)



HtmlFino();

?>
