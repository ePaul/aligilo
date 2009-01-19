<?php

// define(DEBUG, true);

/**
 * Grava administrado.
 *
 * Montras unue elektformularon, kiu post klako al "faru"
 * vokas sin mem per kelkaj (HTTP-Post-)parametroj, kaj due ligilojn al
 * aliaj programpartoj.
 *
 * Kiam oni donis taŭgan $kio-parametron, ĝi montras
 * sube ankaŭ la rezulto de la ago.
 * <pre>
 *  $kio  - kio farindas
 *          n     - kreu nomŝildojn
 *          s     - kreu specialajn nomŝildojn (ekzemple por junulargastej-dungitoj)
 *          m     - kreu manĝkuponojn
 *          k     - kreu konfirmilojn
 *          a     - kreu akceptfoliojn
 *                                (ĉiam PDF-e por elŝuti,
 *                                 kaj nur por tiuj partoprenantoj,
 *                                 kiuj ankoraŭ ne havas.)
 *          adres - kreu adresaron
 *                               (PDF-e)
 *
 *          backup          - kreu sekurkopiojn de la datumbazo kaj alŝutu
 *                            ilin ĉe GMX. (ne funkcias sen retkonekto!)
 *          backup_programo - kreu sekurkopiojn de la programo kaj alŝutu
 *                            ilin ĉe GMX.
 *   (fakte tiuj du nun tute ne funkcias.)
 *
 * $tipo  - se manĝkuponoj, kiajn?
 *          N     - viajndajn
 *          A     - vegane (vegetaĵe)
 *          J     - vegetarajn
 *
 * $nombro - kiom da paĝoj? (Elekteblecoj estas 1, 5, 20, 999 (ĉiuj)
 *           (Estas uzata ĉe n, m, a, k)
 *
 * $kiuj_homoj - por kiuj homoj kreu nomŝildojn?
 * 
 *
 * $savu
 *          J -  memoru en la datumbazo, ke vi sendis konfirmilon al
 *               aŭ printis nomŝildojn/manĝkuponon/akceptfolion por
 *               la partoprenantoj.
 *          NE - simple forgesu.
 *
 * $sen    - Ĉu kreu foliojn sen datumoj (por mane plenigi)?
 *          s
 *          NE
 * $bunta  - ĉu kreu buntan adresaron aŭ nigran?
 *           JES  (bunta)
 *           NE   (nigra)
 * $granda - ĉu kreu grandan adresaron por korektigi?
 *</pre>
 *
 * @author Martin Sawitzki, Paul Ebermann
 * @version $Id$
 * @package aligilo
 * @subpackage pagxoj
 * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */


  /**
   */
require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

kontrolu_rajton("administri");

/**
 * Montras la formularon kun diversaj opcioj gravaj
 * por administrantoj.
 */
function montru_administradan_formularon()
{
  eoecho("<h2>Grava Administrado</h2>");
  eoecho ("<form action='administrado.php' method='post'>\n");
  eoecho ("<p>Printu:<BR>");
  entajpbutono ("",'kio',$_POST['kio'],"n",n,"noms^ildojn","kutima");
  entajpbutono ("(", "nkkren", $_POST['nkkren'], "cxiuj", "cxiuj", "c^iuj",
                "kutima");
  entajpbutono ("|", "nkkren", $_POST['nkkren'], "nur", "nur",
                "nur ". organizantoj_nomo);
  entajpbutono ("|", "nkkren", $_POST['nkkren'], "sen", "sen",
                "sen " . organizantoj_nomo . " )");

  //  entajpejo("(Nur por: ", "kiuj", $_POST['kiuj'], "", "", "", ")");
  echo "<br/>";
  entajpbutono ("",     'kio', $_POST['kio'], 's', 's',
                "specialajn noms^ildojn");
  entajpbutono ("<br/>",'kio',$_POST['kio'],"m",'m',"mang^kuponojn");
  entajpbutono ("(",     'tipo',$_POST['tipo'],"N",'N','viande', "kutima");
  entajpbutono ("",      'tipo',$_POST['tipo'],"A",'A','vegane');
  entajpbutono ("",      'tipo',$_POST['tipo'],"J",'J',"vegetare)");
  entajpbutono ("<br/>",'kio',$_POST['kio'],"k",'k','konfirmilojn');
  entajpbutono ("<br/>",'kio',$_POST['kio'],"a",'a','akceptofoliojn');
  
  entajpbutono ("</p><p>Por ",'nombro',$_POST['nombro'],1,1," 1 pag^o",
                "kutima");
  entajpbutono (" ",'nombro',$_POST['nombro'],5,5," 5 pag^oj");
  entajpbutono (" ",'nombro',$_POST['nombro'],20,20," 20 pag^oj");
  entajpbutono (" ",'nombro',$_POST['nombro'],999,999," c^iuj");


  entajpbokso ("<p>",'savu',$_POST['savu'],J,J,
               "Savu ke vi premis/sendis en la partoprendatumoj");
  entajpbokso ("<BR>",'sen',$_POST['sen'],s,s,"malplenaj folioj</p>");

  entajpbutono ("<p>",'kio',$_POST['kio'],"adres",'adres',
                "elprintu adresaron ");
  entajpbokso  ("(", 'bunta', $_POST['bunta'], 'JES', 'JES', "buntan,",
                "kutima");
  entajpbokso  (" ", 'granda', $_POST['granda'], 'JES', 'JES',
                "grandan (por korekti))");

  entajpbutono ("<p>",'kio', $_POST['kio'], "gepatra_permeso",
                "gepatra_permeso",
                "kreu malplenajn gepatrajn permesilojn<br/>");

  /*

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
  */

  send_butono('Faru!');
  echo "</p></form>\n";
}


/**
 * montras ligojn al aliaj administraj paĝoj, depende de la 
 * rajtoj de la aktuala uzanto.
 */
function montru_aliajn_ligojn()
{
  eoecho("<h2>Aliaj gravaj aferoj</h2>");

  rajtligu("landoj.php", "rigardu kaj eble s^ang^u la landoliston", "",
           "administri");
  eoecho ("<br/>");

  eoecho ("<p>Elprintu partoprenstatistikon:<br/>\n");
  rajtligu("demandoj.php","partopren statistikojn","","administri");
  echo "(das ist leider zur Zeit etwas kaputt) <br/>";

  
  rajtligu("finkalkulado.php","Finkalkulado","","administri");
  echo "<br/>";
  rajtligu("cxambrostatistiko.php",
		   "montru la c^ambrostatistikon kaj la mang^statistikon","",
           "administri");
  echo "</p>";

  if(!rajtas("teknikumi"))
	{
        echo "<hr/>\n";
        return;
    }

  echo ("<hr/>\n");
  eoecho("<h2>Nur por teh^nikistoj</h2>\n<p>");

  if(mangxotraktado == "libera") {
      ligu("mangxredakto.php", "Mang^o-listo");
  }


  
  rajtligu("entajpantoj.php",
           "rigardu kaj eble s^ang^u la entajpantoliston", "",
           "teknikumi");
  eoecho ("<br/>");
  rajtligu("renkontigxo.php", 
           "redaktu la renkontig^o-datumojn", "", "teknikumi");
  eoecho ("<br/>");
  rajtligu("kreu_cxambron.php", "kreu novan c^ambron",
           "", "teknikumi");
  
  eoecho ("<br/>Internaj dosierujoj: ");
  
  echo "<span class='speciala'>";
  rajtligu("specialaj_skriptoj/", "specialaj skriptoj", "", "teknikumi");
  echo "</span>";
  rajtligu("dosieroj/", "dosieroj", "", "teknikumi");
  rajtligu("instalilo/", "instalilo", "", "teknikumi");
  rajtligu("dosieroj_generitaj/", "dosieroj generitaj", "", "teknikumi");
  rajtligu("doku/", "dokumentaj^oj", "", "teknikumi");
  eoecho("</p>");

  eoecho("
  <h3 id='tekstoj'>Tekstoj</h3>
  <p>
    La <em>tekstoj</em> estas uzataj ekzemple por
    havi retmesag^tekst(er)ojn kaj similajn aferojn, kiuj varias
    lau^ renkontig^o,
    ne en la programo sed en la datumbazo.
  </p>");

  $sql = datumbazdemando(array('count(*)' => 'nombro'),
						 'tekstoj',
						 "renkontigxoID = '".$_SESSION['renkontigxo']->datoj['ID']."'");
  $rez = sql_faru($sql);
  $linio = mysql_fetch_assoc($rez);

  eoecho ("
<p>
   Nuntempe ekzistas " . $linio['nombro'] . " tekstoj por la aktuala
   renkontig^o.
");

  ligu("tekstoj.php", "Vidu la liston (kaj eble redaktu kelkajn)");
  
  ligu("nova_teksto.php", "Aldonu novan tekston");

  echo "</p><p>";

  echo "<hr/>\n";

}

/**
 * kreas PDF-dosierojn kun plenigenda gepatra permesilo por partoprenantoj
 * sub 18 jaroj.
 *
 * La funkcio kreas kaj inan kaj viran varianton en po unu PDF-dosiero, kaj
 * alligas ambaŭ. (Ili poste estas enretigendaj por alŝuto el la retpaĝo.)
 *
 */
function printu_gepatran_permesilon()
{
    require_once ('tradukendaj_iloj/kreu_konfirmilon.php');
    
    foreach (array("vira", "ina") AS $perm_tipo) {
        $kon = new Konfirmilo();
        $kon->kreu_permesilon(0,$_SESSION['renkontigxo'],$perm_tipo == "vira");
        $dosiernomo = "dosieroj_generitaj/permesilo_".$perm_tipo . ".pdf";
        $kon->sendu($dosiernomo);
        hazard_ligu($dosiernomo, "els^utu ".$perm_tipo."n permesilon");
    }
}

/**
 * Kreas PDF-dosieron kun nomŝildoj por partoprenantoj (aŭ kunorganizantoj).
 *
 * La organizantaj nomŝildoj (el programa vidpunkto) ne aspektas alie, ili
 * kutime estas tamen printitaj sur alispeca papero (kaj pro tio aparte
 * printendaj).
 *
 * @param int $nombro kiom da paĝoj?
 * @param string $savu  "J" = memoru, ke ni kreis/printis/sendis,
 *                        alikaze "NE" (ekzemple por kontroli,
 *                        ĉu aspektas bone)
 * @param string $sen se "s", printas nur malplenajn nomŝildojn.
 * @param string $nkkren ĉu organizantaj nomŝildoj?
 *                   - "nur" - nur homoj kun "kkren = J",
 *                   - "sen" - nur homoj kun "kkren <> J",
 *                   - "cxiuj" - cxiuj partoprenantoj
 *
 * @todo ebligu facilan ŝanĝon de nomŝildo-aspekto.
 */
function printu_nomsxildojn($nombro, $savu, $sen, $nkkren) {
    require_once ('iloj/kreu_nomsxildojn.php');
    eoecho ("Kreas la noms^ildojn por:<BR>");

  // kiam ni ne volas presi ĉiujn, sed nur la unuajn paĝojn
  $nombroperpagxo=10;
  $nombro = $nombro * $nombroperpagxo;


  /* if ($kiuj == "") */
{
  $kkrenkondicxoj = array("cxiuj" => 1,
						 "sen" => "kkren <> 'J'",
						 "nur" => "kkren = 'J'");
  $kkrenkondicxo = $kkrenkondicxoj[$nkkren];
  
  $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo"),
                             array("partoprenantoj" => "p",
                                   "partoprenoj" => "pn"),
                             array("pn.partoprenantoID = p.ID",
                                   "alvenstato = 'v' OR alvenstato = 'a' "
                                   .                "OR alvenstato = 'i'", 
                                   $kkrenkondicxo,
                                   "havasNomsxildon = 'N'" ),
                             "renkontigxoID",
                             array("order" => "personanomo, nomo",
                                   "limit" => "0, $nombro")
						 );
  
}
/*
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
						       "limit" => "0, $nombro")
						 );
}
*/
  $nom = new Nomsxildo();
  if ($sen=="s")
  {
    // printu nomŝildojn sen nomo

    eoecho ("g^enerala uzo");
    if ($nombro>100) $nombro=100;
    for ($i=1;$i<$nombro;$i++)
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
  {
    $nom->sendu();       
    hazard_ligu("dosieroj_generitaj/nomsxildoj.pdf",
                "els^uti la noms^ildojn.");
  }
 // else
  {
    //send_butono("Faru!");    
  }
}

// ############ specialaj nomŝildoj ##############################


/**
 * Kreas PDF-dosieron kun nomŝildoj por homoj, kiuj ne estas
 * partoprenantoj, el la tabelo 'nomsxildoj'.
 *
 * @param int $nombro kiom da paĝoj?
 * @param string $savu  "J" = memoru, ke ni kreis/printis/sendis,
 *                        alikaze "NE" (ekzemple por kontroli,
 *                        ĉu aspektas bone)
 */
function printu_specialajn_nomsxildojn($nombro, $savu)
{
    require_once ('iloj/kreu_nomsxildojn.php');

  eoecho ("Kreas la noms^ildojn por:<BR>");

  // kiam ni ne volas presi ĉiujn, sed nur la unuajn paĝojn
  $nombroperpagxo=10;
  $nombro = $nombro * $nombroperpagxo;


  $demando = datumbazdemando(array("ID", "titolo_esperante", "nomo"),
							 array("nomsxildoj"),
							 array("havasNomsxildon = 'N'"),
							 "renkontigxoID",
							 array("order" => "nomo, titolo_esperante",
								   "limit" => "0, $nombro")
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
  hazard_ligu("dosieroj_generitaj/nomsxildoj.pdf",
              "els^uti la noms^ildojn.");

}

/**
 * kreas PDF-dosieron kun akceptofolioj.
 *
 * @param int $nombro kiom da akceptofolioj?
 * @param string $savu  "J" = memoru, ke ni kreis/printis/sendis,
 *                        alikaze "NE" (ekzemple por kontroli,
 *                        ĉu aspektas bone)
 * @param string $sen se "s", printas malplenajn foliojn,
 *                            alikaze el la datumbazo.
 */

function printu_akceptofoliojn($nombro, $savu, $sen) {
  require_once ('iloj/kreu_akceptofolion.php');
  eoecho ("Elprintu la akceptfoliojn por:<BR>");
  $nombroperpagxo=1;
  $nombro = $nombro * $nombroperpagxo;  

  
  /* if ($kiuj != "")
 {
     // tiuj, kiuj estas aparte menditaj

   $kiuj_arr = split(",", $kiuj);
   $idoj = "pn.ID = " . join(" or pn.ID = ", $kiuj_arr);
   $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo"),
							  array("partoprenantoj" => "p", "partoprenoj" => "pn"),
							  array("pn.partoprenantoID = p.ID",
									$idoj)
							  );
 }
 else
  */
     {
         // ĉiuj, kiuj ankoraŭ ne alvenis
         $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo",
                                          "personanomo"),
                                    array("partoprenantoj" => "p",
                                          "partoprenoj" => "pn"),
                                    array("pn.partoprenantoID = p.ID",
                                          "alvenstato = 'v' OR " .
                                          " alvenstato = 'i'"),
							  "renkontigxoID",
							  array("order" => "personanomo, nomo",
									"limit" => "0, $nombro")
							  );
     }
  
  $af = new Akceptofolio();
  if ($sen=="s")
  {
    eoecho ("g^enerala uzo (malplenaj)");
    if ($nombro>100)
	  $nombro=100;
    for ($i=0;$i<$nombro;$i++)
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
    $af->sendu();       
    hazard_ligu("dosieroj_generitaj/akceptofolioj.pdf",
                "els^uti la akceptofoliojn.");
}


/**
 * kreas PDF-dosieron da manĝkuponoj.
 *
 * @param int $nombro kiom da paĝoj?
 * @param string $savu  "J" = memoru, ke ni kreis/printis/sendis,
 *                        alikaze "NE" (ekzemple por kontroli,
 *                        ĉu aspektas bone)
 * @param string $sen se "s", printas malplenajn foliojn,
 *                            alikaze el la datumbazo.
 * @param string $tipo unu el la manĝ-tipoj 'J' (vegetare),
 *                            'N' (viande) kaj 'A' (vegane)
 */
function printu_mangxkuponojn($nombro, $savu, $sen, $tipo) {
    require_once ('iloj/mangxkuponoj.php');
  $nombroperpagxo=4;
  $nombro = $nombro * $nombroperpagxo;  
  if ($tipo=='J')
	$vego='vegetarajn';
  else if ($tipo == 'A')
	$vego = 'veganajn';
  else
	$vego='viandajn';

  $kunmangxas = "kunmangxas <> 'N'";

  $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo"),
							 array("partoprenantoj" => "p",
                                   "partoprenoj" => "pn"),
							 array("pn.partoprenantoID = p.ID",
								   $kunmangxas,
								   "vegetare = '$tipo'",
								   "alvenstato = 'v' OR alvenstato = 'a'"
                                   .       "         OR alvenstato = 'i'",
								   "havasMangxkuponon = 'n'"),
							 "renkontigxoID",
							 array("order" => "personanomo, nomo",
								   "limit" => "0, $nombro")
							 );


  eoecho ("<B><BR><BR>Printu la $vego mang^kuponojn por:</B><BR>");
  


  $kup = new Mangxkupono($_SESSION['renkontigxo']);
  if ($sen=="s")
  {
    eoecho ("g^enerala uzo");
    if ($nombro>100)
	  $nombro=100;
    for ($i=1; $i < $nombro; $i++)
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
  {
    $kup->sendu($tipo);       
    hazard_ligu("dosieroj_generitaj/mangxkuponoj.pdf",
                "els^utu la kuponojn.");
  }
}


/**
 * kreas PDF-dokumenton kun duaj informiloj por tiuj, kiuj petis
 * paperan duan informilon.
 *
 * @param  int   $nombro kiom da ni kreu nun?
 * @param string $savu  "J" = memoru, ke ni kreis/printis/sendis,
 *                        alikaze "NE" (ekzemple por kontroli,
 *                        ĉu aspektas bone)
 */
function printu_duajn_konfirmilojn($nombro, $savu)
{
  require_once ('tradukendaj_iloj/kreu_konfirmilon.php');


  // paperaj konfirmiloj

  
  //  $demando = "select p.ID,pn.ID,nomo, personanomo from partoprenantoj as p, partoprenoj as pn where pn.partoprenantoID=p.ID and retakonfirmilo!='J' and 2akonfirmilosendata='0000-00-00' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and kontrolata='J' and alvenstato='v'  limit 0,$nombro";
  $demando = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo"),
							 array("partoprenantoj" => "p", "partoprenoj" => "pn"),
							 array("pn.partoprenantoID = p.ID",
                                   "retakonfirmilo!='J' or p.retposxto=''",
								   "2akonfirmilosendata='0000-00-00'",
								   "kontrolata='J'",
								   "alvenstato = 'v'"
								   ),
							 "renkontigxoID",
							 array("order" => "personanomo, nomo",
								   "limit" => "0, $nombro")
							 );
  
  eoecho ("<B><BR><BR>Kreas la konfirmilon por:</B><BR>");

  $kon = new Konfirmilo("unikode");
  {
    $rezulto = sql_faru($demando);
    while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
    {     
      eoecho($row['personanomo']." ".$row['nomo']."<BR>");  
      $kon ->kreu_konfirmilon($row[1],$row[0],$savu);
    }
  }
    $kon->sendu();  
    hazard_ligu("dosieroj_generitaj/konfirmilo.pdf",
                "els^uti la konfirmilojn.");
}


// -----------------------------------------------------------------


/*
 * Jen la agado
 */

  HtmlKapo();

montru_administradan_formularon();
montru_aliajn_ligojn();

if ($kio == 'gepatra_permeso')
{
    printu_gepatran_permesilon();
}

if ($kio=='n')
{
    printu_nomsxildojn($_POST['nombro'], $_POST['savu'],
                       $_POST['sen'], $_POST['nkkren']);
}



if ($kio=='s') {
    printu_specialajn_nomsxildojn($_POST['nombro'], $_POST['savu']);
 }


// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
if ($kio=='a')
{
    printu_akceptofoliojn($_POST['nombro'], $_POST['savu'],
                       $_POST['sen']);
}


//   MANĜKUPONOJ
if ($kio=='m')
{
    printu_mangxkuponojn($_POST['nombro'], $_POST['savu'],
                         $_POST['sen'], $_POST['tipo']);
 }
  //   KONFIRMILOJ
if ($kio=='k')
{
    printu_duajn_konfirmilojn($_POST['nombro'], $_POST['savu']);
}

if ($kio=='adres')
{
  require('iloj/kreu_adresaron_tcpdf.php');
  kreu_adresaron($_POST['granda'], $_POST['bunta']);
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