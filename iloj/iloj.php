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
   * @copyright 2001-2004 Martin Sawitzki, 2004-2009 Paul Ebermann.
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
 * @link konvertiloj.php
 */
require_once($prafix .'/iloj/konvertiloj.php');


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
 * @link iloj_rajtoj.php
 */
require_once($prafix.'/iloj/iloj_rajtoj.php');

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
 * Kondiĉoj ktp.
 * @link iloj_kondicxoj.php
 */
require_once($prafix . '/iloj/iloj_kondicxoj.php');

/**
 * nova kotizosistemo
 * @link iloj_kotizo.php
 */
require_once($prafix.'/iloj/iloj_kotizo.php');


/**
 * reguloj por krompagoj aux rabatoj
 * @link objektoj_reguloj.php
 */
require_once($prafix. '/iloj/objektoj_reguloj.php');

// require_once ($prafix.'/tradukendaj_iloj/kreu_konfirmilon.php');

/**
 * @link iloj_tekstoj.php
 */
require_once ($prafix.'/iloj/iloj_tekstoj.php');


require_once($prafix.'/iloj/iloj_kurzoj.php');
require_once($prafix.'/iloj/iloj_konfiguroj.php');


/**
 * @link diversaj_cxenoj.php
 */
require_once($prafix.'/tradukendaj_iloj/diversaj_cxenoj.php');


/**
 * @link iloj_sercxo_rezulto.php
 */
require_once ($prafix.'/iloj/iloj_sercxo_rezulto.php');

/**
 * @see
 */
require_once($prafix.'/iloj/objekto_sercxilo.php');

/**
 * @link objekto_noto.php
 */
require_once($prafix.'/iloj/objekto_noto.php');


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
    case true:
      return 'jes';
    case 'n':
    case 'N':
    case false:
      return 'ne';
    default:
      return "? (".$jn.")";
    }
}

/**
 * konvertas tekston kun "jes" aux "ne" al
 * true/false.
 * @param string $jn iu el "j", "jes", "J", "JES", "n",
 *        "ne", "N", "NE" aux similaj tekstoj. Ni atentas nur
 *        pri la unua litero.
 * @return boolean
 */
function jesne_al_boolean($jn) {
    if (is_string($jn)) {
        if(strtoupper($jn[0]) == 'J') {
            return true;
        } else if (strtoupper($jn[0]) == 'N') {
            return false;
        }
    }
    return (boolean)$jn;
}

/**
 * eltrovas, ĉu partoprenanto bezonas unikodan PDF-kreadon.
 *
 * @param Partoprenanto $partoprenanto
 * @return boolean true, se bezonas, false alikaze.
 * @todo sxovu al partoprenanto-objekto.
 */
function bezonas_unikodon($partoprenanto)
{
  $cxiujdatoj =
	$partoprenanto->datoj['nomo'].
	$partoprenanto->datoj['personanomo'].
	$partoprenanto->datoj['posxtkodo'].
	$partoprenanto->datoj['urbo'];
  if (KAMPOELEKTO_IJK) {
      $cxiujdatoj .=
          $partoprenanto->datoj['adreso'];
  }
  else {
      $cxiujdatoj .= 
          $partoprenanto->datoj['adresaldonajxo'].
          $partoprenanto->datoj['strato'];
  }
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
 * formatas kvanton, eble kun pluralo kaj/aux akuzativo.
 *
 * Ni uzas la regulon el {@link http://www.bertilow.com/pmeg/gramatiko/unu-nombro_multe-nombro/uzado/specialaj_okazoj.html#i-ds4 PMEG}, en la
 * varianto, ke cxio krom 1 (kaj -1) estas plurala.
 *
 * @param int          $kvanto kiom da ekzempleroj ni havas?
 * @param string|array $kio    da kio ni havas tiom?
 *                             aux unu aux pluraj vortoj en array().
 * @param boolean      $akuzativo se true, aldonu -n al cxiu vorto.
 * @return string kunmetita cxeno, kie lauxnecese la vorto(j) el $kio
 *                ricevas pluralon.
 */
function iom($kvanto, $kio, $akuzativo=false) {
    $rezulto = "$kvanto";
    if (! is_array($kio)) {
        $kio = array($kio);
    }
    foreach($kio AS $ero) {
        $rezulto .= " " . $ero;
        if (abs($kvanto) != 1) {
            $rezulto .= "j";
        }
        if ($akuzativo) {
            $rezulto .= "n";
        }
    }
    return $rezulto;
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