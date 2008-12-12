<?php

  /**
   * Diversaj opcioj de la programo.
   * Ideale oni nur ĉi tie devas ion
   * ŝanĝi pro diversaj renkontiĝoj.
   * (Fakte ankoraŭ ne funkcias tiel,
   *  necesas ŝanĝi ankaŭ aliloke.)
   *
   *  Tiu estas la varianto specifa al
   *  Internacia Seminario.
   * 
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage konfiguro
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */


// por debugado en la programo enŝaltu tion TRUE/FALSE
//define("DEBUG", TRUE);

# Kiu respondecas pri teknikaj problemoj (kaj
# povu solvi ilin).

define("teknika_administranto",'Pau^lo');
define("teknika_administranto_retadreso",'Paul.Ebermann'."@".'esperanto.de');

# Sendanto por aŭtomataj mesaĝoj.
define("auxtomataj_mesagxoj_sendanto", "IS-Aligilo");
define("auxtomataj_mesagxoj_retadreso", "is.admin".'@'."esperanto.de");

# kopioj de mesaĝoj al ... (retadreso) - se estas io sen '@', ne
#     sendu kopion.
# se nedifinita aŭ "", sendu kopion al la teknika administranto.
define("retmesagxo_kopio_al", "");

# kopioj de unuaj konfirmiloj sendiĝu al ...
# tio estu ','-disigita listo, povas esti malplena.
# Tiuj homoj ricevas la unuan konfirmilon eĉ tiam,
# kiam la aliĝinto ne donis retpoŝtadreson (kaj do
# ne ricevos ĝin).
define("unua_konfirmilo_kopioj_al", "rolffantom".'@'."yahoo.co.uk");

// kiom granda estu la partoprenantoliste en la maldekstra menuo
define("menuoalteco",'10');

/**
 * La identifikilo de la defaŭlta
 * renkontiĝo (kiu estas uzata de la
 * renkontiĝoelektiloj kiel defaŭlto,
 * kaj ĉe la publika aliĝilo).
 */
define("DEFAUXLTA_RENKONTIGXO", 7);
//  5 = IS Xanten, 2005
//  4 = IS Wetzlar, 2004
// -2 = Testa renkontiĝo
//  6 = Wewelsburg 2006
//  7 = Würzburg 2007
//  8 = Biedenkopf 2008

/**
 * retpaĝo kun la diversaj pageblecoj.
 * (provizore la 2006-a versio.)
 */
define('pageblecoj_retpagxo', 'http://www.esperanto.de/is/eo/2006/kontoj');

/**
 * La lando, kiu estu antaŭelektitaj en la
 * diversaj landoelektiloj.
 */
define("HEJMLANDO", 16);  // 16 = Germanio

// ĉefe por invitletero-deziro, iam ŝanĝi al la HEJMLANDO
// -- nuntempe estas uzata por la deviga membreco (vidu sube)
define ("renkontigxolando","germanio");

// por karavanoj ktp, ankoraŭ malbona solvo.
// !! ankoraŭ ne uzata
define ("transportado_eblas","ne");

/**
 * Ĉu manĝado estas ligita al loĝado?
 *
 * - <val>ligita</val> por IS, junulargastejo kaj manĝaĵo
 *     nur eblas kune.
 *     tiam la kampo kunmanĝas automate estas 'J', se oni
 *     loĝas en la junulargastejo.
 * - <val>libera</val> eblas mendi manĝojn aparte, tute
 *     sendepende de loĝado.
 *
 * (Ni uzos ĝin por la nun ekprogramita nova manĝotraktado-sistemo.)
 *
 * @todo miksita traktado: iuj domotipoj havas manĝojn inkluzive,
 *       sed ankaŭ eblas mendi aparte.
 */
define ("mangxotraktado","ligita");

// difinas la loĝeblecojn
// IS_JM estas du junulargastejo kaj memzorgantejo
// IJK_4 estas pensiono, junulargastejo, amasloĝejo sur planko aŭ matraco
// -- nuntempe ne uzata (kaj ne certas, ĉu ni uzos)

define ("logxeblecoj","IS_JM");

// ebligi dulitajn ĉambrojn
// -- nuntempe ne uzata (kaj ne certas, ĉu ni uzos)
define ("dulita_eblas","jes");

// ebligi ekskursbiletojn ĉe la aliĝado
// -- nuntempe ne uzata (kaj ne certas, ĉu ni uzos)
define ("ekskursbiletoj_eblas","jes");

//kiel nomiĝas la organiza teamo LKK aŭ KKRen, aŭ ion ajn
define ("organizantoj_nomo","KKRen");

// landa se partoprenantoj el HEJMLANDO lando devas membriĝi
// monda se partoprenantoj el ĉiu lando devas membriĝi
// nenia - se ne estas deviga membreco

define ("deviga_membreco_tipo","landa");

// kiel nomiĝas la organizacio en kiu oni devas membriĝi

define ("deviga_membreco_nomo","GEA/GEJ");

// la jaro, por kiu oni estu membro (ekzemple 2008)
define("deviga_membreco_jaro", '2008');

// nomo de la personoj kiuj devas membriĝi en la asocio
// ekz. germanoj por IS, homoj por IJK
define ("nemembreculoj","germanoj");

// la organizo kiu organizas la aranĝon. ekz. GEJ aŭ PEJ aŭ TEJO
// -- nuntempe ne uzata (kaj ne certas, ĉu ni uzos)
define ("organiza_asocio","GEJ");

// se 'jes', menciu en akceptada proceduro, ke la
// homoj enskribiĝu en la ministeria listo. 
// -- (Tio estas germana specialaĵo, do eble ŝovenda
//     al iu IS-specifa parto.)
// (Se alia enhavo, faru nenion.)
define("ministeriaj_listoj", 'jes');
// nomoj de unuopaj lisoj
define("ministeriaj_listoj_hejmlando", "germanoj");
define("ministeriaj_listoj_eksterlando", "eksterlandanoj");


// la teksto ke AB devas antaŭpagi jes/ne
define ("AB_antauxpago","jes");

// se nur estas unu renkontiĝo en la datumbazo, forigi kelkajn aferojn kiu necesas ekz. por IS kie ni 
// savas pliajn renkontiĝojn en la datumbazo
// jes/ne
// -- nuntempe ne uzata (kaj ne certas, ĉu ni uzos)
define ("nur_unu_renkontigxo","ne");

// rabato por TEJO-membroj - se 0, ni
// ne traktas TEJO-membriĝojn.
define('TEJO_RABATO', 5.0);

// la plej malalta naskiĝdato por povi ankoraŭ esti TEJO-membro
// kaj tiel akiri la TEJO-rabaton.
// Ŝanĝenda ĉiujare. Verŝajne pli bone en la datumbazo.
// -- nur uzata, se TEJO_RABATO > 0
define('TEJO_AGXO_LIMDATO', '1978-01-01');

// en kiu jaro oni devas esti TEJO-membro?
// plej tau^gas jaro-numero.
define('TEJO_MEMBRO_JARO', '2008');


// la mallongigo por la paĝtitolo kaj diversaj lokoj
// TODO: eble prenu el la datumaro
define("renkontigxo_nomo","IS");

// la nomo de la programo (aperanta en pluraj
//  punktoj (kvankam ankoraŭ ne estas ĉie uzata,
//  kiel ĝi estu)
define("programo_nomo", "IS-aligilo");

// eblibas la punkton germanakonfirmilo jes/ne
// -- nuntempe ne uzata (kaj ne certas, ĉu ni uzos)
define("germanakonfirmilo_eblas","jes");

// ebligi la punkton komencanto / novulo
// -- nuntempe ne uzata (kaj ne certas, ĉu ni uzos)
define("komencantoj_eblas","jes");


/**
 * por IJK 2009 ni havas iom alian elekton de kampoj uzataj.
 */
define("KAMPOELEKTO_IJK", false);


// la okupiĝkampo jes/ne
define ("okupigxo_eblas","ne");

// unu kontribuo kampo aŭ kvin diversaj?
// -- nuntempe ne uzata (kaj ne certas, ĉu ni uzos)
define("kontribuo_formato","kvin");

// ĉu ni jam uzu la duon-pretan novan kunloĝ-sistemon?
// -- nuntempe ne uzata (kaj ne certas, ĉu ni uzos)
define("nova_kunlogxado", false);


// ĉiuj pagotipoj, kiuj estas konsiderataj (por la finkalkulado)
// kiel surlokaj, t.e. kies mono iras al la surloka kaso, aŭ venas
// de tie.
define("surlokajPagotipoj", 'surlokpago|repago');

/**
 * Cxu ni volas marki la kazojn, kie mankis traduko kaj ni
 * anstatauxe uzis esperantan tradukon?
 */
define("marku_traduko_eo_anstatauxojn", true);


/**
 * redonas la plenan tekston por la kunloĝ-stato-mallongigo.
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
 * eltrovas "tekstan" okupiĝtipon el la
 * okupiĝtipnumero.
 *
 * TODO: eble metu en datumbazon.
 * 
 * nunatempe pli taŭgas ĉe opcioj
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

    default:  return "ne indikis";   //restu konstanta, ĉar uzata alie
  }

}



?>
