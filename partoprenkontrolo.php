<?php

  /** partoprenkontrolo */

require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();

if (DEBUG)
{
echo "<!-- POST:\n";
var_export($_POST);
echo "-->";
}

if (!rajtas("aligi"))
{
  ne_rajtas();
}

    if ($_REQUEST['nekontrolup']!="JES")
    {
      if ( ($_REQUEST['de'] > $_REQUEST['gxis'])
		   || (!kontrolu_daton($_REQUEST['aligxdato']))
		   || ($_REQUEST['malaligxdato'] != "0000-00-00" &&
		       !kontrolu_daton($_REQUEST['malaligxdato'])
			   || (kalkulu_tagojn($_REQUEST['aligxdato'], $_REQUEST['malaligxdato']) < 0 ) )
		   || ($_REQUEST['domotipo']=="MemZorganto" and $_REQUEST['cxambrotipo'][0]!="g")
		   || ($_REQUEST['domotipo']=="MemZorganto" and $_REQUEST['dulita']=="JES")
		   || ($_REQUEST['konsento'][0]!="J")
		   )
        {
          $parto="korektigi";          
        }
      //      depend_malsxargxi_kaj_korekti($invitletero,$pasportnumero);
//      depend_malsxargxi_kaj_korekti($kunekun,$kunkiu);
      depend_malsxargxi_kaj_korekti($_REQUEST['vesperabokso'],$_REQUEST['vespera']);
      depend_malsxargxi_kaj_korekti($_REQUEST['distrabokso'],$_REQUEST['distra']);
      depend_malsxargxi_kaj_korekti($_REQUEST['temabokso'],$_REQUEST['tema']);
      depend_malsxargxi_kaj_korekti($_REQUEST['muzikabokso'],$_REQUEST['muzika']);
      depend_malsxargxi_kaj_korekti($_REQUEST['noktabokso'],$_REQUEST['nokta']);
      //HTMLsekurigi($rabatkialo);
    }

if(DEBUG)
{
echo "<!--POST: \n";
var_export($_POST);
echo "-->";
}

    $_SESSION["partopreno"]->kopiu();

    if ($_SESSION['renkontigxo']->datoj['de'] == $_SESSION['partopreno']->datoj['de'] AND
        $_SESSION['renkontigxo']->datoj['gxis'] == $_SESSION['partopreno']->datoj['gxis'])
    {
        $_SESSION['partopreno']->datoj['partoprentipo'] = 't';
    }
    else
    {
        $_SESSION['partopreno']->datoj['partoprentipo'] = 'p';
    }

    
//    if ($partoprentipo[0]=="t")
//    {
//      $_SESSION["partopreno"]->datoj['de'] =
//          $_SESSION["renkontigxo"]->datoj['de'];
//      $_SESSION["partopreno"]->datoj['gxis'] =
//          $_SESSION["renkontigxo"]->datoj['gxis'];
//    }
    if ($parto == "korektigi")
    {
      require ("partopreno.php");
    }
    else
    {
        if ($ago != "sxangxi")
            {
                $_SESSION["partopreno"]->kreu();
            }
        if (mangxotraktado == "libera") {
            require_once($prafix . "/iloj/iloj_mangxoj.php");
            traktu_mangxomendojn($_SESSION['partopreno'],
                                 $_POST['mangxmendo']);
        }
        else if (mangxotraktado == "ligita") {
            if ( $_REQUEST['kunmangxas'] == "?" )
                {
                    //                echo "domotipo: " . $_REQUEST['domotipo'];
                    // kunmangxas = ?: junulargastejuloj kunmangxas,
                    // aliaj ne.      TODO: faru konfigurebla
                    $_SESSION["partopreno"]->datoj['kunmangxas'] = 
                        $_REQUEST['domotipo'] == 'J' ? 'J' : 'N';
                }
        }
        if ($_SESSION["partopreno"]->datoj['alvenstato']=='')
            $_SESSION["partopreno"]->datoj['alvenstato']='v';
        //        if ($_SESSION["partopreno"]->datoj['traktstato']=='')  
        //            $_SESSION["partopreno"]->datoj['traktstato']='N';
        if ($_SESSION["partopreno"]->datoj['havasNomsxildon']=='')    
            $_SESSION["partopreno"]->datoj['havasNomsxildon']='N';
        if ($_SESSION["partopreno"]->datoj['havasMangxkuponon']=='')    
            $_SESSION["partopreno"]->datoj['havasMangxkuponon']='N';
        if (!KAMPOELEKTO_IJK) {
            if ($_SESSION['partopreno']->datoj['surloka_membrokotizo'] == '')
                $_SESSION['partopreno']->datoj['surloka_membrokotizo'] = '?';
        }
        if ($_SESSION['partopreno']->datoj['nivelo'] == '')
            $_SESSION['partopreno']->datoj['nivelo'] = '?';
      
      $_SESSION["partopreno"]->skribu();
	  rekalkulu_agxojn($_SESSION["partopreno"]->datoj['ID']);
      $_SESSION["partopreno"] = new Partopreno($_SESSION["partopreno"]->datoj['ID']);

      $sekvapagxo = $_SESSION["sekvontapagxo"] or
          $sekvapagxo = 'partrezultoj.php';

      //      echo "HTTP-Redirect: ". $sekvapagxo;
      http_redirect($sekvapagxo, null, false, 303);
    }
