<?

/**
 * La agordoj por la tradukilo.
 *
 * @author Paul Ebermann (lastaj ŝanĝoj) + teamo E@I (ikso.net)
 * @version $Id$
 * @package aligilo
 * @subpackage tradukilo
 * @copyright 2005-2008 Paul Ebermann, ?-2005 E@I-teamo
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */

/**
 */


// Tiu chi dosiero enhavas la agordojn por la traduk-sistemo.
// -------------------------------------------------------------

// Se vi volas uzi GET-parametrojn por memori la lingvon de la 
// uzanto, forigu la "//" komence de la sekva linio, kaj elektu
// la nomon de la GET-parametro.  Se vi ne uzos GET-parametron,
// vi devos mem voki la funkcion lingvo() komence de ĉiu paĝo.
// $agordoj["parametro_nomo"] = "lingvo";

// Tiu chi lingvo estas la "origina" lingvo de kiu oni tradukos.
$agordoj["chefa_lingvo"] = "eo";

// La dosier-sufiksoj, kiujn la traduksistemo atentos.
$agordoj["sufiksoj"] = array("php", "xml");

$agordoj["db_tabelo"] = "db_tradukoj";

// Se vi volas uzi salutnomojn/pasvortojn de datumbaza tabelo,
// forigu la "//"-ojn komence de la sekva linio, kaj redaktu
// lauplache la funkcion kontrolu_uzanton() en iloj.php.
//$agordoj["salutado"] = "jes";

// Tiu chi estas la dosierujo, en kiu estas la tradukotaj dokumentoj.
// (Ofte "/".  Ghi komencighu kaj finighu per suprenstreko, kaj estu
// aŭ absoluta aŭ relativa al la dosierujo de la ĉefa dosiero vokita.

if ($GLOBALS['prafix']) {
    $agordoj["dosierujo"] = array($GLOBALS['prafix'] .'/publikaj_skriptoj/');
 }
 else {
     $agordoj["dosierujo"] = array(dirname(__FILE__) . '/../../publikaj_skriptoj/');
 }

$agordoj["datumbazo_tradukenda"] = array('landoj' => array('nomo'));
$agordoj["db-trad_prefikso"] = "/[datumbazo]/";

// Tradukoj de chenoj uzataj en la traduksistemo.
$tradukoj["tradukejo-titolo"] = "Tradukejo";
$tradukoj["bonveniga-mesagho"] = "Bonvenon al la tradukejo!";
$tradukoj["ne-konektis"] = "Ne sukcesis konekti al la datumbazo.  Kontrolu la agordojn en agordoj.php.";
$tradukoj["nova-tabelo"] = "La tabelo ".$agordoj["db_tabelo"]." ne jam ekzistas.  Nun ĝi kreiĝas.";
$tradukoj["kreis-tabelon"] = "La tabelo sukcese kreiĝis.";
$tradukoj["ne-kreis-tabelon"] = "Pro ia eraro, la tabelo ne kreiĝis.  Kontrolu la agordojn en agordoj.php.";
$tradukoj["elektu-lingvon"] = "Elektu lingvon ĉi-sube por ektraduki:";
$tradukoj["aldonu-lingvon"] = "Aldonu novan lingvon:";
$tradukoj["elektu-lingvon-menuero"] = "--- Elektu lingvon ---";
$tradukoj["ek-butono"] = "Ek!";
$tradukoj["redaktejo-titolo"] = "Redaktejo";
$tradukoj["lingvo"] = "Lingvo:";
$tradukoj["elektu-alian-lingvon"] = "Elektu alian lingvon.";
$tradukoj["ghisdatigu-1"] = "Ĝisdatigu";
$tradukoj["ghisdatigu-2"] = "la datumbazon post aldonoj/modifoj al PHP-dosieroj.";
$tradukoj["ghisdatigu-3"] = "Rapide ĝisdatigu";
$tradukoj["ghisdatigu-4"] = "(nur aldonojn al laste modifitaj dosieroj).";
$tradukoj["revoku-chenliston"] = "Revoku ĉenliston";
$tradukoj["montru"] = "Montru:";
$tradukoj["nur-tradukendajn"] = "tradukendajn";
$tradukoj["nur-retradukendajn"] = "nur retradukendajn";
$tradukoj["tradukendajn-kaj-retraukendajn"] = "tradukendajn kaj retradukendajn";
$tradukoj["chion"] = "ĉion";
$tradukoj["ghisdatigo-titolo"] = "Ĝisdatigo";
$tradukoj["necesas-aldoni"] = "Necesas aldoni la ĉi-subajn ĉenojn:";
$tradukoj["necesas-forigi"] = "Necesas forigi la ĉi-subajn ĉenojn:";
$tradukoj["konservu-butono"] = "Konservu";
$tradukoj["aldonu-ordono"] = "Aldonu";
$tradukoj["redaktu-ordono"] = "Redaktu";
$tradukoj["forigu-ordono"] = "Forigu";
$tradukoj["aktualigo-ordono"] = "Aktualigu";
$tradukoj["cheno"] = "Ĉeno:";
$tradukoj["stato"] = "Stato:";
$tradukoj["chefa-lingvo"] = "Esperanto:";
$tradukoj["komento"] = "Komento:";
$tradukoj["tradukinto"] = "Laste tradukis/redaktis:";
$tradukoj["sukceson"] = "Sukceson!";
$tradukoj["sukcese-konservighis"] = "Sukcese konserviĝis";
$tradukoj["aldonoj"] = "aldonoj";
$tradukoj["redaktoj"] = "redaktoj";
$tradukoj["kaj"] = "kaj";
$tradukoj["forigoj"] = "forigoj";
$tradukoj["okazis"] = "Okazis";
$tradukoj["eraroj"] = "eraroj";
$tradukoj["vidu-tradukitan"] = "Vidu la tradukitan paĝon.";
$tradukoj["reredaktu"] = "Reredaktu la saman paĝon.";
$tradukoj["stato-ghisdata"] = "ĝisdata";
$tradukoj["stato-retradukenda"] = "retradukenda";
$tradukoj["stato-tradukenda"] = "tradukenda";
$tradukoj["stato-aldonenda"] = "aldonenda";
$tradukoj["stato-aldonenda-db"] = "aldonenda DB-originalo";
$tradukoj["stato-aktualigenda-db"] = "aktualigenda DB-originalo";
$tradukoj["stato-forigenda"] = "forigenda";
$tradukoj["chiuj-dosieroj"] = "Ĉiuj dosieroj";

// Listo de lingvoj kaj ties ISO-kodoj.  Vi povas aldoni pliajn, se vi deziras.
$trad_lingvoj["aa"] = "afara";
$trad_lingvoj["ab"] = "abĥaza";
$trad_lingvoj["af"] = "afrikansa";
$trad_lingvoj["am"] = "amhara";
$trad_lingvoj["ar"] = "araba";
$trad_lingvoj["as"] = "asama";
$trad_lingvoj["ay"] = "ajmara";
$trad_lingvoj["az"] = "azerbajĝana";
$trad_lingvoj["ba"] = "baŝkira";
$trad_lingvoj["be"] = "belorusa";
$trad_lingvoj["bg"] = "bulgara";
$trad_lingvoj["bh"] = "bihara";
$trad_lingvoj["bi"] = "bislamo";
$trad_lingvoj["bn"] = "bengala";
$trad_lingvoj["bo"] = "tibeta";
$trad_lingvoj["br"] = "bretona";
$trad_lingvoj["bs"] = "bosna";
$trad_lingvoj["ca"] = "kataluna";
$trad_lingvoj["co"] = "korsika";
$trad_lingvoj["cs"] = "ĉeĥa";
$trad_lingvoj["cy"] = "kimra";
$trad_lingvoj["da"] = "dana";
$trad_lingvoj["de"] = "germana";
$trad_lingvoj["dz"] = "dzonko";
$trad_lingvoj["el"] = "greka";
$trad_lingvoj["en"] = "angla";
$trad_lingvoj["eo"] = "esperanto";
$trad_lingvoj["es"] = "hispana";
$trad_lingvoj["et"] = "estona";
$trad_lingvoj["eu"] = "eŭska";
$trad_lingvoj["fa"] = "persa";
$trad_lingvoj["fi"] = "finna";
$trad_lingvoj["fj"] = "fiĝia";
$trad_lingvoj["fo"] = "feroa";
$trad_lingvoj["fr"] = "franca";
$trad_lingvoj["fy"] = "frisa";
$trad_lingvoj["ga"] = "irlanda";
$trad_lingvoj["gd"] = "gaela";
$trad_lingvoj["gl"] = "galega";
$trad_lingvoj["gn"] = "gvarania";
$trad_lingvoj["gu"] = "guĝarata";
$trad_lingvoj["ha"] = "haŭsa";
$trad_lingvoj["he"] = "hebrea";
$trad_lingvoj["hi"] = "hinda";
$trad_lingvoj["hr"] = "kroata";
$trad_lingvoj["hu"] = "hungara";
$trad_lingvoj["hy"] = "armena";
$trad_lingvoj["ia"] = "interlingvao";
$trad_lingvoj["id"] = "indonezia";
$trad_lingvoj["ie"] = "okcidentalo";
$trad_lingvoj["ik"] = "eskima";
$trad_lingvoj["is"] = "islanda";
$trad_lingvoj["it"] = "itala";
$trad_lingvoj["iu"] = "inuita";
$trad_lingvoj["ja"] = "japana";
$trad_lingvoj["jw"] = "java";
$trad_lingvoj["ka"] = "kartvela";
$trad_lingvoj["kk"] = "kazaĥa";
$trad_lingvoj["kl"] = "gronlanda";
$trad_lingvoj["km"] = "kmera";
$trad_lingvoj["kn"] = "kanara";
$trad_lingvoj["ko"] = "korea";
$trad_lingvoj["ks"] = "kaŝmira";
$trad_lingvoj["ku"] = "kurda";
$trad_lingvoj["ky"] = "kirgiza";
$trad_lingvoj["la"] = "latino";
$trad_lingvoj["ln"] = "lingala";
$trad_lingvoj["lo"] = "laŭa";
$trad_lingvoj["lt"] = "litova";
$trad_lingvoj["lv"] = "latva";
$trad_lingvoj["mg"] = "malagasa";
$trad_lingvoj["mi"] = "maoria";
$trad_lingvoj["mk"] = "makedona";
$trad_lingvoj["ml"] = "malajalama";
$trad_lingvoj["mn"] = "mongola";
$trad_lingvoj["mo"] = "moldava";
$trad_lingvoj["mr"] = "marata";
$trad_lingvoj["ms"] = "malaja";
$trad_lingvoj["mt"] = "malta";
$trad_lingvoj["my"] = "birma";
$trad_lingvoj["na"] = "naura";
$trad_lingvoj["ne"] = "nepala";
$trad_lingvoj["nl"] = "nederlanda";
$trad_lingvoj["no"] = "norvega";
$trad_lingvoj["oc"] = "okcitana";
$trad_lingvoj["om"] = "oroma";
$trad_lingvoj["or"] = "orijo";
$trad_lingvoj["pa"] = "panĝaba";
$trad_lingvoj["pl"] = "pola";
$trad_lingvoj["ps"] = "paŝtua";
$trad_lingvoj["pt"] = "portugala";
$trad_lingvoj["qu"] = "keĉua";
$trad_lingvoj["rm"] = "romanĉa";
$trad_lingvoj["rn"] = "burunda";
$trad_lingvoj["ro"] = "rumana";
$trad_lingvoj["ru"] = "rusa";
$trad_lingvoj["rw"] = "ruanda";
$trad_lingvoj["sa"] = "sanskrito";
$trad_lingvoj["sd"] = "sinda";
$trad_lingvoj["sg"] = "sangoa";
$trad_lingvoj["sh"] = "serbo-kroata";
$trad_lingvoj["si"] = "sinhala";
$trad_lingvoj["sk"] = "slovaka";
$trad_lingvoj["sl"] = "slovena";
$trad_lingvoj["sm"] = "samoa";
$trad_lingvoj["sn"] = "ŝona";
$trad_lingvoj["so"] = "somala";
$trad_lingvoj["sq"] = "albana";
$trad_lingvoj["sr"] = "serba";
$trad_lingvoj["ss"] = "svazia";
$trad_lingvoj["st"] = "sota";
$trad_lingvoj["su"] = "sunda";
$trad_lingvoj["sv"] = "sveda";
$trad_lingvoj["sw"] = "svahila";
$trad_lingvoj["ta"] = "tamila";
$trad_lingvoj["te"] = "telugua";
$trad_lingvoj["tg"] = "taĝika";
$trad_lingvoj["th"] = "taja";
$trad_lingvoj["ti"] = "tigraja";
$trad_lingvoj["tk"] = "turkmena";
$trad_lingvoj["tl"] = "filipina";
$trad_lingvoj["tn"] = "cvana";
$trad_lingvoj["to"] = "tongaa";
$trad_lingvoj["tr"] = "turka";
$trad_lingvoj["ts"] = "conga";
$trad_lingvoj["tt"] = "tatara";
$trad_lingvoj["tw"] = "akana";
$trad_lingvoj["ug"] = "ujgura";
$trad_lingvoj["uk"] = "ukrajna";
$trad_lingvoj["ur"] = "urduo";
$trad_lingvoj["uz"] = "uzbeka";
$trad_lingvoj["vi"] = "vjet-nama";
$trad_lingvoj["vo"] = "volapuko";
$trad_lingvoj["wo"] = "volofa";
$trad_lingvoj["xh"] = "ksosa";
$trad_lingvoj["yi"] = "jida";
$trad_lingvoj["yo"] = "joruba";
$trad_lingvoj["za"] = "ĝuanga";
$trad_lingvoj["zh-cn"] = "simpligita ĉina";
$trad_lingvoj["zh-tw"] = "tradicia ĉina";
$trad_lingvoj["zu"] = "zulua";

?>