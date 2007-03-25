<?php

/**************************************
 * Diversaj opcioj de la programo.
 * Ideale oni nur cxi tie devas ion
 * sxangxi pro diversaj renkontigxoj.
 * (Fakte ankoraux ne funkcias tiel,
 *  necesas sxangxi ankaux aliloke.)
 **************************************/

// por debugado en la programo ensxaltu tion TRUE/FALSE
//define("DEBUG", TRUE);

define("teknika_administranto",'Pau^lo');
define("teknika_administranto_retadreso",'Paul.Ebermann@esperanto.de');



// kiom granda estu la partoprenantoliste en la maldekstra menuo
define("menuoalteco",'10');

/**
 * La identifikilo de la defauxlta
 * renkontigxo (kiu estas uzata de la
 * renkontigxoelektiloj kiel defauxlto,
 * kaj cxe la publika aligxilo).
 */
define("DEFAUXLTA_RENKONTIGXO", 7);
//  5 = IS Xanten, 2005
//  4 = IS Wetzlar, 2004
// -2 = Testa renkontigxo
//  6 = Wewelsburg 2006
//  7 = Würzburg 2007

/**
 * La lando, kiu estu antauxelektitaj en la
 * diversaj landoelektiloj.
 */
define("HEJMLANDO", 16);  // 16 = Germanio

// cxefe por invitletero deziro, iam sxangxi al la HEJMLANDO
define ("renkontigxolando","germanio");

// por karavanoj ktp, ankoraux malbona solvo.
// !! ankoraux ne uzata
define ("transportado_eblas","ne");

// difinas la ligo de mangxagxo al la logxloko
// ligita -> por IS, junulargastejo kaj mangxajxo nur eblas kune
// tiam la kampo kunmangxas automate estas plenumata se oni logxas en la junulargastejo

// libera -> por IJK ili ne estas ligata

define ("mangxotraktado","ligita");

// difinas la logxeblecojn
// IS_JM estas du junulargastejo kaj memzorgantejo
// IJK_4 estas pensiono, junulargastejo, amaslogxejo sur planko aux matraco

define ("logxeblecoj","IS_JM");

// ebligi dulitajn cxambrojn
define ("dulita_eblas","jes");

// ebligi ekskursbiletojn cxe la alixado
define ("ekskursbiletoj_eblas","jes");

//kiel nomigxas la organiza teamo LKK aux KKRen, aux ion ajn
define ("organizantoj_nomo","KKRen");

// landa se partoprenantoj el HEJMLANDO lando devas membrigxi
// monda se partoprenantoj el cxiu lando devas membrigxi
// nenia - se ne estas deviga membreco

define ("deviga_membreco_tipo","landa");

// kiel nomigxas la organizacio en kiu oni devas membrigxi

define ("deviga_membreco_nomo","GEA/GEJ");

// nomo de la personoj kiuj devas membrigxi en la asocio
// ekz. germanoj por IS, homoj por IJK
define ("nemembreculoj","germanoj");



// la organizo kiu organizas la arangxon. ekz. GEJ aux PEJ aux TEJO
define ("organiza_asocio","GEJ");

// se 'jes', menciu en akceptada proceduro, ke la
// homoj enskribigxu en la ministeria listo.
// (Se alia enhavo, faru nenion.)
define("ministeriaj_listoj", 'jes');
// nomoj de unuopaj lisoj
define("ministeriaj_listoj_hejmlando", "germanoj");
define("ministeriaj_listoj_eksterlando", "eksterlandanoj");


// la teksto ke AB devas antauxpagi jes/ne
define ("AB_antauxpago","jes");

// se nur estas unu renkontigxo en la datumbazo, forigi kelkajn aferojn kiu necesas
// ekz. por IS kie ni savas pliajn renkontigxojn en la datumbazo
// jes/ne
define ("nur_unu_renkontigxo","ne");


// rabato por TEJO-membroj - se 0, ni
// ne traktas TEJO-membrigxojn.
define('TEJO_RABATO', 5.0);

// la plej alta agxo por povi ankoraux esti TEJO-membro
// kaj tiel akiri la TEJO-rabaton.
// Sxangxenda cxiujare.
define('TEJO_AGXO_LIMDATO', '1978-01-01');

// en kiu jaro oni devas esti TEJO-membro?
// plej tau^gas jaro-numero.
define('TEJO_MEMBRO_JARO', '2008');


// la mallongigo por la pagxtitolo kaj diversaj lokoj
// !!eble prenu el la datumaro

define("renkontigxo_nomo","IS");

// eblibas la punkton germanakonfirmilo jes/ne
define("germanakonfirmilo_eblas","jes");

// ebligi la punkton komencanto / novulo
define("komencantoj_eblas","jes");


// la okupigxkampo jes/ne
define ("okupigxo_eblas","ne");

// unu kontribuo kampo aux kvin diversaj?
define("kontribuo_formato","kvin");

// cxu ni jam uzu la duon-pretan novan kunlogx-sistemon?
define("nova_kunlogxado", false);


/**
 * redonas la plenan tekston por la kunlogx-stato-mallongigo.
 */
function kunlogx_stato($mallongigo)
{
	switch($mallongigo)
	  {
	  case '?':
		return "neprilaborata";
	  case '':
		return "nekonata";
	  default:
		return "nekonata stato (".$this->datoj['stato'].")";
	  }
}

/**
 * eltrovas "tekstan" okupigxtipon el la
 * okupigxtipnumero.
 *
 * TODO: eble metu en datumbazon.
 * 
 * nunatempe pli tauxgas cxe opcioj
 */
function okupigxtipo($numero)
{
  switch ($numero)
  {
    case "0": return "ne elektis";break;
    case "1": return "estas lernanto";break;
    case "2": return "faras edukadon kiel";break;
    case "3": return "studas";break;
    case "4": return "estas sen laboro";break;
    case "10": return "ne diras ";break;
    case "11": return "laboras kiel";break;

    default:  return "ne indikis";   //restu konstanta, cxar uzata alie
  }

}

/**
 * Eble definenda. (Rigardu traduku_tabelnomon en iloj_sql.)
 *
 * Tradukilo por la tabelnomoj de la datumbazo.
 */

// tabelnomtradukilo($tabelnomo)
// {
//   // TODO
// }
//
//
// $tabelnomtradukilo = array(...);
//
// $tabelnompostfikso = ...;
$tabelnomprefikso = "is_";


?>
