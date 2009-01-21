<?php

  /**
   * La partopreno/partoprenanto-detalo-pagxo.
   *
   * Aldone estas diversaj funkcioj, kiuj apartenus aliloken.
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

  // define('DEBUG', true);


  /**
   */
require_once ('iloj/iloj.php');


  session_start();
  malfermu_datumaro();


kontrolu_rajton("vidi");


HtmlKapo();  

unset($_SESSION['sekvontapagxo']);
            

//kunigu kun la kunlogxanto (vokita el sercxrezultoj.php,
// la kunlogxanto-sercxo). 
if ($kune and $partoprenidento)
{  
  sxangxu_datumbazon("partoprenoj", array("kunkiuID" => $kune),
					 array("ID" => $partoprenidento));
}


sesio_aktualigu_laux_get();






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
    // TODO: cxu plu necesas? Cxu ni nun ne havas transferi.php?
  echo "C^io nun apartenas al #$kune";
  sxangxu_datumbazon("partoprenoj",
					 array("partoprenantoID" => $kune),
					 array("partoprenantoID" => $antauxa));
  sxangxu_datumbazon("notoj",
					 array("partoprenantoID" => $kune),
					 array("partoprenantoID" => $antauxa));
}

  if ($faru=="2konfirmi")
  {
	require_once ($prafix .'/tradukendaj_iloj/kreu_konfirmilon.php');
	
	if (DEBUG)
	  {
		echo "<!-- bezonas_unikodon: [" . (string)bezonas_unikodon($_SESSION['partoprenanto']) .
		  "] -->";
	  }
	$kon = new Konfirmilo(bezonas_unikodon($_SESSION['partoprenanto']));
	$kon-> kreu_konfirmilon($_SESSION["partopreno"]->datoj[ID],
							$_SESSION["partoprenanto"]->datoj[ID]);
	// kreas PDF-dosieron, ne sendas, malgraux la nomo.
	$kon->sendu();
  }
  if ($faru=='2konfirmelsendo')
  {
	kontrolu_rajton("retumi");
	
	require_once ($prafix .'/tradukendaj_iloj/kreu_konfirmilon.php');
    require_once($prafix . '/iloj/retmesagxiloj.php');
    require_once($prafix . '/tradukendaj_iloj/iloj_konfirmilo.php');
    require_once($prafix . '/iloj/diversaj_retmesagxoj.php');


    sendu_duan_informilon($_SESSION['partoprenanto'], $_SESSION['partopreno'],
                          $partopreno_renkontigxo, 'J');
    $_SESSION['partopreno']->prenu_el_datumbazo();
	
  }
if ($faru=='2konfirm_papere')
{
    // nur notu en la datumbazo, ke ni nun sendas gxin papere
    $_SESSION['partopreno']->datoj['2akonfirmilosendata'] = date('Y-m-d');
    $_SESSION['partopreno']->skribu();
    $_SESSION['partopreno'] = new Partopreno($_SESSION['partopreno']->datoj['ID']);
}




// ------------------------------------------
// komenco de la tabeloj 
// ------------------------------------------


  echo "<table border=2>\n";
  echo "<TR><TD >\n";

if (DEBUG)
    {
        echo "<!-- " . var_export($_SESSION, true) . "-->";
    }

  $_SESSION["partoprenanto"]->montru_aligxinto();
  
  rajtligu ("partoprenanto.php?ago=sxangxi&sp=partrezultoj.php","--> s^ang^i personajn datojn","","sxangxi","jes");
  echo "<BR>\n";
  rajtligu ("partopreno.php?sp=forgesi&partoprenantoidento=".$_SESSION['partoprenanto']->datoj['ID'],"--> aligi al renkontig^o","","aligi","jes"); // TODO:? später auch noch dynamisch ;) (?)
  echo "<BR>\n";
  //ligu ("partrezultoj.php?partoprenantoidento=" . $_SESSION["partoprenanto"]->datoj[ID],"--> vidu c^iu partopreno");
  //echo "<BR>\n";
  rajtligu("sendumesagxon.php","--> preparu mesag^on","","retumi","ne");
  echo "<BR>\n";
  

  rajtligu("transferi.php", "--> serc^u similajn partoprenantojn kaj (eble) transferu partoprenojn", "", 'vidi');

  echo "<BR>\n";

     
  
  //Montras cxiujn partoprenojn
  //  $sql = "Select id, renkontigxoid, de,gxis from partoprenoj where partoprenantoID='".$_SESSION["partoprenanto"]->datoj[ID]."' order by renkontigxoID";

	$sql = datumbazdemando(array("id", "renkontigxoid", "de", "gxis"),
						   "partoprenoj",
						   "",
						   array("renkontigxo" => "renkontigxoID",
								 "partoprenanto" => "partoprenantoID"),
						   array("limit" => "0, 10"));


$sql = datumbazdemando(array("ID", "renkontigxoID", 'de', 'gxis'),
                       "partoprenoj",
                       "",
                       array("partoprenanto" => "partoprenantoID"),
                       array("order" => "renkontigxoID"));
  $result = sql_faru($sql);
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
    echo "<BR>";
    ligu("partrezultoj.php?partoprenidento=".$row['ID'],
         eltrovu_renkontigxon($row["renkontigxoID"]));
    eoecho(" (#". $row['ID'] . ", " . $row['de'] . " &ndash; " . $row['gxis']
           . ")");
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
						array("prilaborata <> 'j'"),
						array("partoprenanto" => "partoprenantoID")
						);
$rez= sql_faru($sql);
$linio = mysql_fetch_assoc($rez);
$notojfarendaj = $linio['nombro'];

$sql = datumbazdemando(array('COUNT(ID)' => 'nombro'),
					   "notoj",
						array("prilaborata <> 'j'",
							  "revidu <= NOW()"),
						array("partoprenanto" => "partoprenantoID")
						);
$rez= sql_faru($sql);
$linio = mysql_fetch_assoc($rez);
$notojaktualaj = $linio['nombro'];



if ($notojentute > 0)
    {
        eoecho("<p>Estas entute " );

        ligu("sercxrezultoj.php?elekto=notojn&partoprenantoidento=" .
             $_SESSION['partoprenanto']->datoj['ID'],
             iom($notojentute, "noto"). " pri " .
             $_SESSION['partoprenanto']->datoj['personanomo']);

        eoecho( ", el tiuj ankorau^ " );

        if ( $notojfarendaj > 0 )
            {
                eoecho("<strong>" . iom($notojfarendaj, "neprilaborita") .
                       "</strong>, el tiuj " );
                if( $notojaktualaj > 0 )
                    {
                        eoecho("estas <strong class='averto'>" .
                               iom($notojaktualaj, "jam remontrenda") .
                               "</strong>.");
                    }
                else
                    {
                        eoecho("estas neniuj jam remontrendaj. " );
                    }
            }
        else
            {
                eoecho("c^iuj prilaboritaj. ");
            }
    }
 else
     {
         eoecho ("<p> Estas neniuj notoj pri " .
                 $_SESSION['partoprenanto']->datoj['personanomo'] .
                 ".");
     }

ligu("notoj.php?elekto=nova&partoprenantoidento=" .
     $_SESSION['partoprenanto']->datoj['ID'],
     "Kreu novan noton!");

echo ("</p>");

  
  echo "</td><td>\n";


  
  if (empty($_SESSION['partopreno']))
  {
	// sercxu partoprenon de la aktuala renkontigxo por la partoprenanto,
	// kaj elektu tiun kiel $_SESSION['partopreno'].

	$sql = datumbazdemando(array("id", "renkontigxoid", "de", "gxis"),
						   "partoprenoj",
						   "",
						   array("renkontigxo" => "renkontigxoID",
								 "partoprenanto" => "partoprenantoID"),
						   array("limit" => "0, 10"));
	$result = sql_faru($sql);
	
    $num_pp = mysql_num_rows($result);

    switch($num_pp) {
    case 1:
		$row = mysql_fetch_array($result, MYSQL_NUM);
		$_SESSION["partopreno"] = new Partopreno($row[0]);
		session_register("partopreno");
        break;
    case 0:
        eoecho ("${Ri} g^is nun ne alig^is al " . $_SESSION['renkontigxo']->dato['nomo']  . ".\n");
        break;
    default:
        eoecho("${Ri} jam plurfoje alig^is al " .
               $_SESSION['renkontigxo']->dato['nomo'] .
               ", elektu la g^ustan partoprenon maldekstre.");
    }

  }

   
if (!empty($_SESSION["partopreno"]))  { 

    $_SESSION["partopreno"]->montru_aligxo();

    eoecho("<div style='display: table'><div style='display:table-row'><p style='display:table-cell'>Statoj:</p><p style='display:table-cell'>");

	rajtligu ("partrezultoj.php?kontrolata=mal","kontrolata: ".$_SESSION["partopreno"]->datoj['kontrolata'],'',"estingi");
	
    echo "<!-- alvenstato: " .
        $_SESSION['partopreno']->datoj['alvenstato'] .
        "-->";


    elektilo_kun_butono(" Alvenstato: ",
                        "partoprensxangxo.php?partoprenidento="
                        .      ($_SESSION['partopreno']->datoj['ID']),
                        "alvenstato",
                        $GLOBALS['alvenstatonomoj'],
                        $_SESSION['partopreno']->datoj['alvenstato'],
                        "estingi");


    rajtligu ("partrezultoj.php?trakti=mal",
			  "trakto: ".$_SESSION["partopreno"]->datoj[traktstato],'',"estingi",'ne');
    rajtligu ("partrezultoj.php?asekuri=mal","asekuri: ".$_SESSION["partopreno"]->datoj[asekuri],'',"estingi",'ne');
    echo "<BR>\n";
    rajtligu ("partrezultoj.php?mangxkup=mal","Mang^kupono: ".$_SESSION["partopreno"]->datoj[havasMangxkuponon],'',"estingi",'ne');
    rajtligu ("partrezultoj.php?nomsxildo=mal","Noms^ildo: ".$_SESSION["partopreno"]->datoj[havasNomsxildon],'',"estingi",'ne');
    echo "</div></div>"; // statoj

    rajtligu ("partopreno.php?partoprenidento=" . $_SESSION['partopreno']->datoj['ID']
			  . "&ago=sxangxi",
			  "--> s^ang^i la partoprenon",
			  "",
			  "sxangxi",
			  "jes");
    echo "<BR>\n";
    ligu ("partrezultoj.php?faru=konfirmi","--> produkti 1an konfirmilon");
    echo "<BR>\n";
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
        }
    else
        {
            rajtligu("invitpeto.php", "aldonu invitpeto-datojn", "", "inviti");
        }
    echo "<br />\n";
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

    $kolumnoj = array(array('0','','->','z','"antauxpago.php?id=XXXXX"',''),
                      array('dato','dato','XXXXX','l','','-1'), 
                      array('kvanto','sumo','XXXXX','r','','-1'), 
                      array("tipo","tipo",'XXXXX','l','','-1')
                      );
    if (!rajtas("mono")) {
        array_shift($kolumnoj);
    }

    eoecho("pagoj:");

    sercxu($sql,
		   array("dato","desc"),
           $kolumnoj,
           array(array('','',array('&sum; XX','N','z'))),
           "pagoj-partrezultoj",
           0,0,"",'','ne'); 
    echo "</td><td>";
    $sql = datumbazdemando(array("ID", "partoprenoID", "kvanto", "kauzo"),
                           "rabatoj", "",
                           array("partopreno" => "partoprenoID"));
    $kolumnoj = array(array('0','','->','z','"rabato.php?jena=XXXXX"',''),
                      array('kvanto','sumo','XXXXX','r','','-1'),
                      array("kauzo","kauzo",'XXXXX','l','','')
                      );
    if (!rajtas("rabati")) {
        array_shift($kolumnoj);
    }
    eoecho("rabatoj:");
    sercxu($sql, 
           array("kauzo","desc"),
           $kolumnoj,
           array(array('',array('&sum; XX','N','z'))),
           "rabatoj-partrezultoj",
           0, 0, "",'','ne');
    echo "</td></tr></table>\n";

    if (!$_SESSION["partoprenanto"]->datoj['lando'])
		{
            erareldono("Mankas la lando, pro tio la kotizokalkulo estas iom necerta!");
		}
    // nova kotizokalkulilo
    $kotkal = new Kotizokalkulilo($_SESSION["partoprenanto"],
                                  $_SESSION["partopreno"],
                                  $partopreno_renkontigxo,
                                  new Kotizosistemo($partopreno_renkontigxo->datoj['kotizosistemo'])
                                  );
	  
    eoecho("Restas pagenda: " . $kotkal->restas_pagenda() . " E^");

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

            $kotkal->tabelu_kotizon(new HTMLKotizoFormatilo());

        }
    echo "</td><td>";
    if ((in_array($_SESSION['partopreno']->datoj['alvenstato'],
                  array('v', 'i'))) // nur "venos" aux "vidita" - TODO: cxu aliaj?
        and ( $_SESSION['partopreno']->datoj['renkontigxoID'] ==
              $_SESSION['renkontigxo']->datoj['ID'] )
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
    while ($row = mysql_fetch_assoc($rezulto))
        {
            $sql = datumbazdemando("nomo", "cxambroj", "id = '{$row['cxambro']}'");
            $cxambronomo = mysql_fetch_assoc(sql_faru($sql));

            eoecho (sekvandaton($partopreno_renkontigxo->datoj['de'],
                                $row['nokto_de']-1) .
                    " &ndash; ".
                    sekvandaton($partopreno_renkontigxo->datoj['de'],
                                $row['nokto_gxis']) .
                    " (" . $row['rezervtipo']. ")\n");
            if (rajtas("cxambrumi"))
                {
                    ligu ("cxambro-detaloj.php?cxambronumero=" .
                          $row["cxambro"],
                          "c^ambro: " . $cxambronomo['nomo']);
                    echo " ";
                    ligu_butone('cxambroago.php?sp=partrezultoj.php',
                                $row['rezervtipo']=='d' ? "forgesu" : "malrezervu",
                                array('sendu'=>'forgesu_liton',
                                      'forgesendalito'=>$row["ID"]));
                }
            else
                {
                    eoecho( "c^ambro: ".$cxambronomo['nomo']. " ");
                }
            echo "<br/>";
            $havas_cxambron = "true";
        } // while ($row)



    if (($_SESSION["partopreno"]->datoj['domotipo']=="J"))
        {

            if (!$havas_cxambron)
                {
                    eoecho ($_SESSION["partoprenanto"]->personapronomo." g^is nun ne havas c^ambron.<BR>");
                    rajtligu ("cxambroj.php","elektu unu", "", "cxambrumi", "jes");
                }
            else
                {
                    $mankantaj_litoj = eltrovu_litomankon($_SESSION['partopreno'],
                                                          $partopreno_renkontigxo);
                    if (count($mankantaj_litoj) > 0) {
                        eoecho ("<strong> Mankas lito en la sekvaj noktoj: " .
                                implode(", ", $mankantaj_litoj) . "</strong>");
            
                    }
                    rajtligu ("cxambroj.php","elektu plian", "", "cxambrumi");
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


if ('konfirmi' == $_REQUEST['faru'])
    {
        echo "<hr/><h3>La unua konfirmilo</h3>";
        require_once($prafix.'/tradukendaj_iloj/iloj_konfirmilo.php');
        echo "<pre>" . kreu_unuan_konfirmilan_tekston($_SESSION['partoprenanto'],
                                                      $_SESSION['partopreno'],
                                                      $partopreno_renkontigxo, 'utf-8') .
            "</pre><p>";
        ligu_butone("partrezultoj.php?partoprenidento=".
                    $partopreno->datoj['ID'], 
                    "sendi la unuan konfirmilon",
                    array('faru'=> 'sendu_unuan_konfirmilon'));
        echo "</p>\n";

    }
if ($_REQUEST['faru'] == 'sendu_unuan_konfirmilon')
    {
        kontrolu_rajton('retumi');
        require_once($prafix . '/iloj/retmesagxiloj.php');
        require_once($prafix . '/tradukendaj_iloj/iloj_konfirmilo.php');
        require_once($prafix . '/iloj/diversaj_retmesagxoj.php');
        $teksto = kreu_kaj_sendu_unuan_konfirmilon($_SESSION["partoprenanto"],
                                                   $_SESSION["partopreno"],
                                                   $partopreno_renkontigxo,
                                                   $_SESSION['kkren']['entajpantonomo']);
        echo "<p>Ni sendis la jenan unuan informilon:</p><pre>";
        echo eotransformado($teksto, 'utf-8');
        echo "</pre>";
    }

if ($faru == "ekzporti")
    {
        require_once($prafix . '/iloj/retmesagxiloj.php');
        require_once($prafix . '/iloj/diversaj_retmesagxoj.php');
        //  simpla_test_mesagxo();
        sendu_sekurkopion_de_aligxinto($_SESSION['partoprenanto'],
                                       $_SESSION['partopreno'],
                                       $partopreno_renkontigxo,
                                       $_SESSION['kkren']['entajpantonomo']);
        echo "<p> Sekurkopio sendita al la administranto. </p>";
    }
if ($faru == "programmesagxoj")
    {
        // por elprovi:
        require_once($prafix . '/iloj/retmesagxiloj.php');
        require_once($prafix . '/iloj/diversaj_retmesagxoj.php');
        sendu_invitilomesagxon($_SESSION['partoprenanto'],
                               $_SESSION['partopreno'],
                               $partopreno_renkontigxo,
                               $_SESSION['kkren']['entajpantonomo']);

    
        sendu_informmesagxon_pri_programero($_SESSION['partoprenanto'],
                                            $_SESSION['partopreno'],
                                            $partopreno_renkontigxo,
                                            $_SESSION['kkren']['entajpantonomo']);

    }



HtmlFino();
