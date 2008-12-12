<?php

  /**
   * Funkcioj por uzo de la aligxilo.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   * Analizas la POST-datumojn kaj el tio kreas
   * Partopreno- kaj partoprenanto-objektojn.
   *
   * (Ne metas ilin en la datumbazon.)
   *
   * @return &array
   *     array({@link Partoprenanto}, {$link Partopreno}, {$link Invitpeto})
   */
function &mangxu_Aligxilajn_datumojn($renkontigxo=null)
{
    $renkontigxo = kreuRenkontigxon($renkontigxo);

    $partoprenanto = new Partoprenanto();
    $partoprenanto->kopiu();



    $partopreno = new Partopreno();

    debug_echo ("<!-- kreita partopreno: " .
                var_export($partopreno, true) . "\n -->");

    $partopreno->kopiu();

    debug_echo ("<!-- kreita partopreno: " .
                var_export($partopreno, true) . "\n -->");



    if ($partopreno->datoj['de'] == $renkontigxo->datoj['de'] and
        $partopreno->datoj['gxis'] == $renkontigxo->datoj['gxis'])
        {
            $partopreno->datoj['partoprentipo']="t";
        }
    else
        {
            $partopreno->datoj['partoprentipo']="p";
        }

    if (mangxotraktado == 'ligita') {
        if ( $domotipo[0] == "J" )
            {
                $partopreno->datoj['kunmangxas'] = "J";
            }
        else
            {
                $partopreno->datoj['kunmangxas'] = "N";
            }
    }
    else if (mangxotraktado == 'libera') {
        //// TODO: kiel eblas trakti la mangxomendojn sen meti ilin
        //// jam nun en la datumbazon?
        //
        // traktu_mangxomendojn($partopreno, $_POST['mangxmendo']);
    }
    else {
        darf_nicht_sein(mangxotraktado);
    }
      
    $partopreno->datoj['aligxdato'] = date("Y-m-d");

// 	if($_POST['cxambrotipo'] == 'd') // dulita
// 	{
// 		// gea cxambro
// 		$partopreno->datoj['cxambrotipo'] = 'g';
// 		$partopreno->datoj['dulita'] = 'J';
// 	}

      
    $partopreno->datoj['renkontigxoID']=$renkontigxo->datoj["ID"];
    $partopreno->datoj['partoprenantoID']=$partoprenanto->datoj['ID'];

    $partopreno->datoj['alvenstato']='v';
    //    $partopreno->datoj['traktstato']='N';
    $partopreno->datoj['havasNomsxildon']='N';
    $partopreno->datoj['havasMangxkuponon']='N';
    $partopreno->datoj['KKRen'] = 'n';
    //    $partopreno->datoj['surloka_membrokotizo'] = 'n';
    
    $partopreno->datoj['tejo_membro_kontrolita'] = '?';
    
    if ($partopreno->datoj['tejo_membro_laudire']{0} != 'j')
        {
            // TODO: igxos?
            $partopreno->datoj['tejo_membro_laudire'] = 'n';
        }
    
    
    if ($_POST['invitletero']=='J')
         {
             $partopreno->mia_invitpeto =& new Invitpeto();
             $partopreno->mia_invitpeto->kopiu();
             //             $partopreno->mia_invitpeto->datoj['ID'] = $partopreno->datoj['ID'];
             $partopreno->mia_invitpeto->datoj['invitletero_sendenda'] = '?';
             $partopreno->mia_invitpeto->datoj['invitletero_sendodato'] = '0000-00-00';
         }

    debug_echo ("<!-- kreita partopreno: " .
                var_export($partopreno, true) . "\n -->");


    return array(&$partoprenanto, &$partopreno, &$partopreno->mia_invitpeto);
}


