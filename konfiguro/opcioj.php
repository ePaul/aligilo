<?php

/**************************************
 * Diversaj opcioj de la programo.
 * Ideale oni nur cxi tie devas ion
 * sxangxi pro diversaj renkontigxoj.
 * (Fakte ankoraux ne funkcias tiel,
 *  necesas sxangxi ankaux aliloke.)
 *
 * Tiu estas la gxenerala varianto,
 * adaptenda por individuaj renkontigxoj.
 **************************************/

// por debugado en la programo ensxaltu tion TRUE/FALSE
//define("DEBUG", TRUE);


# Kiu respondecas pri teknikaj problemoj (kaj
# povu solvi ilin).

define("teknika_administranto",'Teknika administranto');
define("teknika_administranto_retadreso",'mankas@ankoraux');

# Sendanto por auxtomataj mesagxoj.
define("auxtomataj_mesagxoj_sendanto", "IS-Aligilo");
define("auxtomataj_mesagxoj_retadreso", "");

# kopioj de mesagxoj al ... (retadreso) - se estas io sen '@', ne
#     sendu kopion.
# se nedifinita aux "", sendu kopion al la teknika administranto.
define("retmesagxo_kopio_al", "");

// kiom granda estu la partoprenantoliste en la maldekstra menuo
define("menuoalteco",'10');

/**
 * La identifikilo de la defauxlta
 * renkontigxo (kiu estas uzata de la
 * renkontigxoelektiloj kiel defauxlto,
 * kaj cxe la publika aligxilo).
 */
define("DEFAUXLTA_RENKONTIGXO", 6);


/**
 * La lando, kiu estu antauxelektitaj en la
 * diversaj landoelektiloj.
 */
define("HEJMLANDO", 16);  // 16 = Germanio (se vi kopiis la landotabelon de IS)

// cxefe por invitletero-deziro, iam sxangxi al la HEJMLANDO
// -- nuntempe estas uzata por la deviga membreco (vidu sube)
define ("renkontigxolando","germanio");

// por karavanoj ktp, ankoraux malbona solvo.
// !! ankoraux ne uzata
define ("transportado_eblas","ne");

// difinas la ligo de mangxagxo al la logxloko
// ligita -> por IS, junulargastejo kaj mangxajxo nur eblas kune
// tiam la kampo kunmangxas automate estas plenumata se oni logxas en la junulargastejo

// libera -> por IJK ili ne estas ligata
// -- nuntempe ne uzata (kaj ne certas, cxu ni uzos)

define ("mangxotraktado","ligita");

// difinas la logxeblecojn
// IS_JM estas du junulargastejo kaj memzorgantejo
// IJK_4 estas pensiono, junulargastejo, amaslogxejo sur planko aux matraco
// -- nuntempe ne uzata (kaj ne certas, cxu ni uzos)

define ("logxeblecoj","IS_JM");

// ebligi dulitajn cxambrojn
// -- nuntempe ne uzata (kaj ne certas, cxu ni uzos)
define ("dulita_eblas","jes");

// ebligi ekskursbiletojn cxe la alixado
// -- nuntempe ne uzata (kaj ne certas, cxu ni uzos)
define ("ekskursbiletoj_eblas","jes");

//kiel nomigxas la organiza teamo LKK aux KKRen, aux ion ajn
define ("organizantoj_nomo","KKRen");

// landa - se partoprenantoj el iu lando devas membrigxi
// monda - se partoprenantoj el cxiu lando devas membrigxi
// nenia - se ne estas deviga membreco

define ("deviga_membreco_tipo","landa");

// kiel nomigxas la organizacio en kiu oni devas membrigxi

define ("deviga_membreco_nomo","GEA/GEJ");

// la organizo kiu organizas la arangxon. ekz. GEJ aux PEJ aux TEJO
// -- nuntempe ne uzata (kaj ne certas, cxu ni uzos)
define ("organiza_asocio","GEJ");

// se 'jes', menciu en akceptada proceduro, ke la
// homoj enskribigxu en la ministeria listo. 
// -- (Tio estas germana specialajxo, do eble sxovenda
//     al iu IS-specifa parto.)
// (Se alia enhavo, faru nenion.)
define("ministeriaj_listoj", 'ne');
// nomoj de unuopaj lisoj
define("ministeriaj_listoj_hejmlando", "germanoj");
define("ministeriaj_listoj_eksterlando", "eksterlandanoj");


// la teksto ke AB devas antauxpagi jes/ne
define ("AB_antauxpago","jes");

// se nur estas unu renkontigxo en la datumbazo, forigi kelkajn aferojn kiu necesas ekz. por IS kie ni 
// savas pliajn renkontigxojn en la datumbazo
// jes/ne
// -- nuntempe ne uzata (kaj ne certas, cxu ni uzos)
define ("nur_unu_renkontigxo","ne");

// rabato por TEJO-membroj - se 0, ni
// ne traktas TEJO-membrigxojn.
define('TEJO_RABATO', 5.0);

// la plej alta naskigxdato por povi ankoraux esti TEJO-membro
// kaj tiel akiri la TEJO-rabaton.
// Sxangxenda cxiujare.
// -- nur uzata, se TEJO_RABATO > 0
define('TEJO_AGXO_LIMDATO', '1978-01-01');


// nomo de la personoj kiuj devas membrigxi en la asocio
// ekz. germanoj por IS, homoj por IJK
define ("nemembreculoj","germanoj");


// la mallongigo por la pagxtitolo kaj diversaj lokoj
// TODO: eble prenu el la datumaro
define("renkontigxo_nomo","ER");  // ER = Ekzempla renkontigxo

// la nomo de la programo (aperanta en pluraj
//  punktoj (kvankam ankoraux ne estas cxie uzata,
//  kiel gxi estu)
define("programo_nomo", "ER-alig^ilo");

// eblibas la punkton germanakonfirmilo jes/ne
// -- nuntempe ne uzata (kaj ne certas, cxu ni uzos)
define("germanakonfirmilo_eblas","jes");

// ebligi la punkton komencanto / novulo
// -- nuntempe ne uzata (kaj ne certas, cxu ni uzos)
define("komencantoj_eblas","jes");


// la okupigxkampo jes/ne
define ("okupigxo_eblas","ne");

// unu kontribuo kampo aux kvin diversaj?
// -- nuntempe ne uzata (kaj ne certas, cxu ni uzos)
define("kontribuo_formato","kvin");

// cxu ni jam uzu la duon-pretan novan kunlogx-sistemon?
// -- nuntempe ne uzata (kaj ne certas, cxu ni uzos)
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
