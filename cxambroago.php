<?php


  /**
   * Agoj pri unuopaj cxambroj.
   *
   * Jen la eblecoj por $_POST['sendu']:
   *   - 'forgesu_liton' - forigas la litonokton kun ID en
   *                    $_REQUEST['forgesendalito'].
   *   - 'Ek!'  - sxangxas tipon, rimarkon, dulitecon.
   *   - 'Nun!' - (malnova nomo por 'intersxangxo')
   *   - 'intersxangxo'
   *   - 'rezervu'/'disdonu' - rezervas aux disdonas liton el la elektilo.
   *
   * Aliaj ago-variantoj:
   *
   *   $_POST['forgesu_liton'] - donas litonokto-identigilon forigendan.
   *   $_POST['disdonu_rezervitan_liton'] - donas litonokto-identigilo
   *                                        disdonendan.
   *
   *  Post la agado ni reiras al la pagxo indikita de $_SESSION['sekvapagxo'].
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
require_once ('iloj/iloj_cxambroj.php');

session_start();
malfermu_datumaro();

kontrolu_rajton("cxambrumi");

$renkontigxodauxro = $_SESSION['renkontigxo']->renkontigxonoktoj();

if ($_REQUEST['sp'])
    {
        $_SESSION['sekvontapagxo'] = $_REQUEST['sp'];
    }

sesio_aktualigu_laux_get();



// TODO:? Cxambrotipo nochmal überarbeiten
// [respondo de Martin:] Früher gab es 'u', 'g' und 'n'. Das 'n' für negravas wurde irgendwann rausgenommen. Das wollte ich nochmal überdenken und ggf. anpassen.




if($_POST['sendu'] == 'forgesu_liton')
    {
        // forigo de lito-partopreno-konekto (nur per POST)

        forigu_el_datumbazo('litonoktoj', $_REQUEST['forgesendalito']);
        if ($_SESSION['sekvontapagxo'])
            {
                http_redirect($_SESSION['sekvontapagxo'], null, false, 303);
                exit();
            }
    }


if ($_POST['forgesu_liton'])
    {
        forigu_el_datumbazo('litonoktoj', $_POST['forgesu_liton']);
        if ($_SESSION['sekvontapagxo'])
            {
                http_redirect($_SESSION['sekvontapagxo'], null, false, 303);
                exit();
            }
        
    }

if ($_POST['disdonu_rezervitan_liton'])
    {
        sxangxu_datumbazon('litonoktoj',
                           array('rezervtipo' => 'd'),
                           array("ID" => $_POST['disdonu_rezervitan_liton'])
                           );
        
    }


// estis: "Faru!"
if ( $_POST['sendu']=="rezervu" or $_POST['sendu'] == 'disdonu' )
{

    $renkontigxodauxro = $_SESSION['renkontigxo']->renkontigxonoktoj();


    debug_echo("<!-- rezervu/disdonu liton -->");

    // rezervu aux disdonu cxambron por iu persono,
    // kiu ne antauxe rezervis gxin.

    // kontrolparto


    // atentu: funkcias nur kun 'rezervu' kaj 'disdonu'.
    $tipo = $_REQUEST['sendu'][0];

    $valoroj = array("cxambro" => $_REQUEST['cxambronumero'],
                     "partopreno" => $_SESSION["partopreno"]->datoj["ID"],
                     "rezervtipo" => $tipo);
    

  if ($tute != "")
  {
      // rezervu/disdonu cxambron por la tuta renkontigxo

	$valoroj["litonumero"] = $tute;
	$valoroj["nokto_de"] = "1";
	$valoroj["nokto_gxis"] = $renkontigxodauxro;
	aldonu_al_datumbazo("litonoktoj", $valoroj);
  }
  else
  {
      // eltrovu, kiujn litojn ni kiam rezervu

    $banto = 1;
    while ($banto <= $renkontigxodauxro)
    {
	  
      if (!$nokto[$banto])
      {
        $lito = "manko";
      }
      else
      {
        $lito = $nokto[$banto];
        $de = $banto;
      }
      do {
        $banto++;
      }  while ( ($nokto[$banto] == $lito)
                 and ($banto<=$renkontigxodauxro)
                 );
      if ($lito != "manko")
      {
//         $sql2 .= " '$lito','$de','".($banto-1)."')";
//         sql_faru($sql2);
		$valoroj["litonumero"] = $lito;
		$valoroj["nokto_de"] = $de;
		$valoroj["nokto_gxis"] = $banto - 1;
		aldonu_al_datumbazo("litonoktoj", $valoroj);
      }
    } // while (banto)

  }
}





// TODO: pli bona distingo inter la agoj ol "Ek!", "Faru!", "Nun!".




if ( $sendu=="Nun!" or
     $_REQUEST['sendu'] == "intersxangxo")
{
      //intersxangxu rezervojn de cxambroj
    eoecho ("<p>Ni inters^ang^as la log^antojn de c^ambro " . $_REQUEST['de'] .
            " kun c^ambro " . $_REQUEST['al'] . "</p>");
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
    //  kaj se la litoj ne estas disdonitaj laux sinsekvo, la
    //  intersxangxo cxiuokaze igxos multe pli komplika.

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
	

    // ankaux sxangxu la tipon kaj rimarkojn de la cxambroj
    
    $tipo=$cxambrode->datoj['tipo'];
    $cxambrode->datoj['tipo']=$cxambroal->datoj['tipo'];
    $cxambroal->datoj['tipo']=$tipo;
    $rimarkoj=$cxambrode->datoj['rimarkoj'];
    $cxambrode->datoj['rimarkoj']=$cxambroal->datoj['rimarkoj'];
    $cxambroal->datoj['rimarkoj']=$rimarkoj;
    $cxambroal->skribu();
    $cxambrode->skribu();
}




// fino de la farendajxoj.





if ($_REQUEST['reiru'])
    {
        http_redirect($_REQUEST['reiru'], null, false, 303);
        exit();
    }




if ($_SESSION['sekvontapagxo'])
    {
        http_redirect($_SESSION['sekvontapagxo'], null, false, 303);
        exit();
    }



return;

// malnovaj ... versxajne estu en alia dosiero.





// TODO - ni ne devus alveni cxi tie.

$sekvapagxo = $_SESSION["sekvontapagxo"] or
    $sekvapagxo = 'partrezultoj.php';

http_redirect($sekvapagxo, null, false, 303);


?>
