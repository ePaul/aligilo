<?php

  /**
   * Kelkaj ĝeneralaj funkcioj kaj ŝarĝo de aliaj iloj-dosieroj.
   *
   * Tiu dosiero vokas ĉiujn aliajn bibliotekajn dosierojn,
   * ankaŭ ŝarĝas konfigurojn kaj konektas al la datumbazo.
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


if (!isset($prafix)) {
  /**
   * prefikso por la dosiernomo de ĉiuj bibliotekaj dosieroj.
   *
   * Defaŭlta valoro estas la dosierujo, el kiu estis startita la programo.
   *
   * @global string $prafix
   */
    $prafix = ".";
 }


/**
 * @link moduso.php
 */
require_once ($prafix.'/konfiguro/moduso.php');
/**
 * @link opcioj.php
 */
require_once ($prafix.'/konfiguro/opcioj.php');

session_name("IS_Admin_".MODUSO."_SessioID");

if(!defined("DEBUG")) {
/**
 * Indiko pri sencimiga moduso.
 *
 * Se la konstanto DEBUG estas true, ni estas en la sencimiga-moduso,
 * kaj pluraj funkcioj tiam eldonas pli da informoj. Kutima valoro
 *  (t.e. se ne la vokanta paĝo jam difinis ĝin) estas FALSE.
 */
	 define("DEBUG", FALSE);
 }

/**
 * @link datumaro.php
 */
require_once ($prafix.'/konfiguro/datumaro.php');
/**
 * @link iloj_sql.php
 */
require_once ($prafix.'/iloj/iloj_sql.php');
/**
 * @link iloj_cxambroj.php
 */
require_once ($prafix.'/iloj/iloj_cxambroj.php');  //TODO:? BITTE nochmal überdenken und hochschieben.
// [respondo de Martin:] Die Einteilung in Ilo_cxambroj. bzw. das gesamte Konzept wie die Funktionen auf Dateien verteilt sind und sich diese gegenseitig verlinken.

/**
 * @link iloj_sesio.php
 */
require_once ($prafix.'/iloj/iloj_sesio.php');
/**
 * @link iloj_html.php
 */
require_once ($prafix.'/iloj/iloj_html.php');
/**
 * @link objektoj.php
 */
require_once ($prafix.'/iloj/objektoj.php');
/**
 * @link objektoj_diversaj.php
 */
require_once ($prafix.'/iloj/objektoj_diversaj.php');
/**
 * @link objekto_partopreno.php
 */
require_once ($prafix.'/iloj/objekto_partopreno.php');
/**
 * @link objekto_partoprenanto.php
 */
require_once ($prafix.'/iloj/objekto_partoprenanto.php');
/**
 * @link objekto_renkontigxo.php
 */
require_once ($prafix.'/iloj/objekto_renkontigxo.php');
/**
 * @link objekto_invitpeto.php
 */
require_once ($prafix.'/iloj/objekto_invitpeto.php');

/**
 * nova kotizosistemo
 * @link iloj_kotizo.php
 */
require_once($prafix.'/iloj/iloj_kotizo.php');

/**
 * malnovaj retmesaĝaj funkcioj.
 * @link iloj_mesagxoj.php
 */
require_once ($prafix.'/iloj/iloj_mesagxoj.php');
// require_once ($prafix.'/iloj/kreu_konfirmilon.php');
/**
 * @link iloj_tekstoj.php
 */
require_once ($prafix.'/iloj/iloj_tekstoj.php');
/**
 * @link iloj_sercxo_rezulto.php
 */
require_once ($prafix.'/iloj/iloj_sercxo_rezulto.php');

/**
 * @link kondicxoj.php
 */
require_once ($prafix.'/konfiguro/kondicxoj.php');
/**
 * @link ma-kondicxoj.php
 */
require_once ($prafix.'/konfiguro/ma-kondicxoj.php');


/**
 * kontrolas, ĉu $lakodnomo estas kun $lakodvorto 
 * en la datumbazo.
 *
 * @param string $lakodnomo uzantonomo de la entajpanto.
 * @param string $lakodvorto pasvorto de la entajpanto, kiel
 *                           ĝi estis entajpita.
 * @return boolean true, falls Login erfolgreich,
 *                 false sonst.
 * @global array _SESSION['kkren'] igxos (je sukceso) array kun <samp>
 *                   'entajpanto' => uzanto-ID,
 *                   'entajpantonomo' => uzantonomo,
 *                   'partoprenanto_id' => ID de la rilata
 *                                         partoprenanto,</samp>
 *                  alikaze <samp>null</samp>
 */
function kontrolu_entajpanton($lakodnomo,$lakodvorto)
{
    $sql = datumbazdemando(array("ID", "nomo", 'partoprenanto_id', 'kodvorto'),
                           "entajpantoj",
                           array("nomo = '$lakodnomo'"),
						 "",
						 array("order" => "id"));
    $row = mysql_fetch_assoc(sql_faru($sql));
    
    if ($row and
        $row['kodvorto'] == $lakodvorto)
      {
          $_SESSION['kkren'] =
              array("entajpanto" => $row['ID'],
                    "entajpantonomo" => $row['nomo'],
                    "partoprenanto_id" => $row['partoprenanto_id']);
          return true;
      }
  else
      {
          $_SESSION['kkren'] = null;
          return false;
      }
}

/**
 * Kontrolas iun rajton de la aktuala uzanto.
 *
 * Faras demandon al la datumbazo tiucele.
 * @param $ago nomo de kolumno en la rajto-tabelo.
 * @return boolean true, se la uzanto havas tiun rajton,
 *                 false alikaze (ankaŭ se la uzanto ne
 *                  ekzistas aŭ pasvorto malĝustas).
 * @global string _SESSION["kodvorto"]  la pasvorto de la
 *                 uzanto, uzata por kontroli.
 * @global string _SESSION["kodnomo"] la uzantonomo por
 *                 kontroli la rajton.
 */
function rajtas($ago)
{
    $sql = datumbazdemando(array($ago, "kodvorto"),
                           "entajpantoj",
                           "nomo = '" . $_SESSION["kodnomo"] . "'",
                           "",
                           array("order" => "id"));
    $row = mysql_fetch_assoc(sql_faru($sql));

    return 
        $row
        and ($row['kodvorto'] == $_SESSION['kodvorto']) 
        and ('J' == $row[$ago] );
}


/**
 * donas erarmesaĝon, ke la uzanto ne rajtas fari ion,
 * kaj finas la skripton.
 *
 * @param string $ago kiun rajton oni bezonus.
 * @todo prenu la nomon, kie plendi el la konfiguro.
 * @todo ĉu iel taŭge fini la HTML-strukturon?
 */
function ne_rajtas($ago="?")
{
  eoecho ("Malg^usta kodvorto au^ nomo ne ekzistas, au^ eble vi ne rajtas uzi tiu c^i pag^on ($ago)<BR>");
  eoecho ("Se vi pensas, ke vi devus rajti, kaj ke vi donis la g^ustan kodvorton, plendu c^e Pau^lo."); // TODO: Pauxlo -> el konfiguro
  ligu("index.php","<-- reen al la komenca pag^o","_top");

  // TODO: exit() finas la tutan skripton, sen zorgi, ke la HTML estas ie en la mezo ...
  // Eble iom helpus voki htmlFino().
  exit();
}

/**
 * Certigas, ke la nuna uzanto rajtas fari ion.
 * 
 * Se la uzanto rajtas, nenio okazos.
 * Se la nuna uzanto ne havas la rajton, ni eldonas
 * erarmesaĝon kaj finos la skripton.
 * @param string $ago
 */
function kontrolu_rajton($ago)
{
  if (! rajtas($ago) )
	{
	  ne_rajtas($ago);
	}
}


/**
 * transkodigas tekston por simpla PDF-eldono.
 *
 * @param string $teksto, en UTF-8, kun esperanta c^-kodigo.
 * @return la sama teksto, kun transkodigo de la c^-koditaj
 *         eo-signoj al la respektivaj lokoj en la PDF-kodigo.
 * @see zeichensatz.php
 */
function eo($teksto)
{
    $trans = array ("C^" => chr(195), "c^" => chr(164),
					"G^" => chr(207), "g^" => chr(223),
					"H^" => chr(176), "h^" => chr(167),
					"J^" => chr(165), "j^" => chr(162),
					"S^" => chr(163), "s^" => chr(217),
					"U^" => chr(186), "u^" => chr(170),
					chr(223) => chr(175), // ß estas en iom speciala loko.
					//					"E^" => chr(128));
					"E^" => "EUR");

    return (strtr(utf8_decode($teksto), $trans));
}

/**
 * transkodigas tekston por unikoda PDF-eldono.
 *
 * transkodigas tekston en UTF-8 kun ^ al UTF-8
 * kun veraj supersignoj.
 * Tio estas uzata por la unikoda PDF-eldono.
 * @param string $teksto, en UTF-8, kun esperanta c^-kodigo.
 * @return la sama teksto, kun ankaŭ esperantaj signoj en UTF-8.
 */
function uni($teksto)
{
  return eotransformado($teksto, "utf-8");
}


/**
 * formatas datumbazajn jes-ne-valorojn.
 *
 * @param string $jn io el 'j', 'J', 'n', 'N'.
 * @return string "jes" aŭ "ne".
 */
function jes_ne($jn)
{
  switch($jn)
	{
	case 'j':
	case 'J':
	  return 'jes';
	case 'n':
	case 'N':
	  return 'ne';
	default:
	  return "? (".$jn.")";
	}
}

  /**
   * eltrovas, ĉu unikoda teksto estas kodigebla en nia PDF-kodigo.
   *
   * Kontrolas, ĉu unu el la signoj de $teksto, interpretita kiel
   * UTF-8-teksto, estas ekster nia varianto de la kodigo Latina-1.
   *
   * @param string $teksto UTF-8-kodita teksto, eble kun la esperantaj signoj
   *                      en c^-kodigo.
   * @return boolean true, se $teksto enhavas almenaŭ unu signon, kiu
   *                       ne aperas en nia speciala PDF-kodigo (varianto
   *                       de UTF-8, kun Eo-signoj kaj eĉ la Eo-ovo.),
   *                       alikaze false.
   */
function estas_ekster_latin1($teksto) {
  // TODO: pripensu, ĉu ankaŭ eblas tion
  // legi el la UTF-8 versio. (Tamen ne tiom gravas.)
  $cxiujdatoj_utf16 = mb_convert_encoding($teksto, "UTF-16", "UTF-8");

  // tiuj signoj, kiuj mankas en nia speciala
  // PDF-varianto de Latin-1
  $malpermesitaj = array(162, 163, 164, 165, 167, 170, 175, 176,
                         186, 188, 195, 207, 217);

  for ($i = 0; $i < strlen($cxiujdatoj_utf16); $i += 2)
	{
        if (ord($cxiujdatoj_utf16{$i}) > 0
            // -> litero > 256, t.e. ne en ISO-8859-1
             or in_array(ord($cxiujdatoj_utf16{$i+1}), $malpermesitaj)
            // unu el la malpermesitaj
            )
		return true;
	}
  return false;
}


/**
 * eltrovas, ĉu partoprenanto bezonas unikodan PDF-kreadon.
 *
 * @param Partoprenanto $partoprenanto
 * @return boolean true, se bezonas, false alikaze.
 */
function bezonas_unikodon($partoprenanto)
{
  $cxiujdatoj =
	$partoprenanto->datoj['nomo'].
	$partoprenanto->datoj['personanomo'].
	$partoprenanto->datoj['adresaldonajxo'].
	$partoprenanto->datoj['strato'].
	$partoprenanto->datoj['posxtkodo'].
	$partoprenanto->datoj['urbo'];
  return estas_ekster_latin1($cxiujdatoj);
}


/**
 * eltrovas, ĉu la unua parametro estas
 * unu el la pliaj parametroj.
 *
 * eblas doni al ĉi tiu funkcio kiom ajn da parametroj,
 * la funkcio komparas ilin per == al $sercxita.
 * @param mixed $sercxita la unua parametro estos serĉota inter la aliaj.
 * @param mixed $resto,... en la listo de la aliaj parametro ni serĉos
 *                         la unuan.
 * @return boolean true, se trovita, alikaze false.
 */
function estas_unu_el($sercxita, $resto=null) {
    $listo = func_get_args();
    // ne rigardu la unuan, nur la aliajn.
    array_shift($listo);

    foreach($listo AS $element)
        {
            if ($element == $sercxita) {
                return true;
            }
        }
    return false;
}


/**
 * plilongigas tekston al iu longeco.
 *
 * @param string $teksto teksto en UTF-8.
 * @param int $longeco la miniuma longeco de la rezulto.
 * @param int $tipo unu el STR_PAD_RIGHT (aldonas spacojn dekstre),
 *                  STR_PAD_LEFT (aldonas spacojn maldekstre) kaj
 *                  STR_PAD_BOTH (aldonas spacojn ambaŭflanke).
 * @return string teksto, kies longeco (en signoj, ne bitokoj) estas
 *                almenaŭ $longeco.
 */
function plilongigu($teksto, $longeco, $tipo = STR_PAD_RIGHT) {
    $len = mb_strlen($teksto, 'utf-8');
    if ($len < $longeco) {
        switch($tipo) {
        case STR_PAD_RIGHT:
            return $teksto . str_repeat(' ', $longeco - $len);
        case STR_PAD_LEFT:
            return str_repeat(' ', $longeco - $len) . $teksto;
        case STR_PAD_BOTH:
            $maldekstre = ($longeco - $len) / 2;
            $dekstre = $longeco - $len - $dekstre;
            return
                str_repeat(' ', $maldekstre) .
                $teksto .
                str_repeat(' ', $dekstre);
        }
    }
    else
        return $teksto;
}



if(!function_exists('http_redirect'))
{

    /**
     * funkcio laŭ
     * {@link PHP_MANUAL#http_redirect la samnoma PHP-standard-funkcio},
     * kiu ne ekzistas en nia servilo, sed iom simpligita.
     *
     * ni uzas nur $uri kaj $status.
     *
     * Se  $uri ne komenciĝas per 'http' (do aŭ 'https://' aŭ 'http://'),
     * ni aldonas protokolon  (aŭ https:// aŭ http://, depende, ĉu la
     * aktuala paĝo estis vokita per HTTPS aŭ ne) kaj servilon (nian).
     *
     * @param string $uri
     * @return boolean false, se ne plu eblas fari redirektigon.
     */
	function http_redirect($uri, $params=null, $session=false,$status)
	{
        if (headers_sent())
            {
                return false;
            }
		if (substr($uri, 0, 4) != 'http')
		{
			$komputilo =  $_SERVER['HTTP_HOST'];
            if ($_SERVER['HTTPS'] and $_SERVER['HTTPS'] != 'off')
                {
                    $skemo = 'https://';
                }
            else
                {
                    $skemo =  'http://';
                }

			if ($uri{0} == '/')
			{
				$uri = $skemo . $komputilo . $uri;
			}
			else
			{
				$dosierujo  = rtrim(dirname($_SERVER['REQUEST_URI']), '/\\');
				$uri = $skemo . $komputilo . $dosierujo . '/' . $uri;
			}
		}

		header("Location: " . $uri, true, $status);
        echo "<html><body>Redirecting to <a href=" . $uri .
            "</a></body></html>";
        exit();
        
	}

}

if (!function_exists('array_combine')) {

    /**
     * Kombinas array-on el ŝlosiloj kaj valoroj en du array-oj.
     *
     * funkcio laŭ {@link http://de.php.net/manual/de/function.array_combine.php}.
     * Tiu funkcio estas nur difinita, se ne jam ekzistas samnoma funkcio.
     * @author "Khaly" ({@link http://de.php.net/manual/de/function.array-combine.php#78244 kontribuo}
     * en la dokumentaĵo por la PHP-5-array_combine, de 4a de oktobro 2007,
     * 11:11).
     * @param array $arr1 ŝlosiloj
     * @param array $arr2 valoroj
     * @return array nova array, kiu enhavas la valorojn de $arr1
     *               kiel ŝlosiloj, la korespondaj el $arr2 kiel valoroj.
     */
    function array_combine($arr1,$arr2) {
        $out = array();
        foreach($arr1 as $key1 => $value1)    {
            $out[$value1] = $arr2[$key1];
        }
        return $out;
    }
 }


/**
 * Eldonas tekston nur en DEBUG-moduso.
 *
 * Se DEBUG-moduso estas enŝaltita, ni eldonas la tekston (kiel {@link echo}).
 * Alikaze ni faras nenion.
 * @param string $teksto Iu debug-mesaĝo.
 */
if (DEBUG) {
    function debug_echo($teksto) {
        echo $teksto;
    }
 } else {
    /**
     * @ignore
     */
    function debug_echo() {}
}


?>