<?php

/* #################################################### */
/* Tiu cxi dosiero enhavas multajn bezonatajn funkciojn */
/* #################################################### */



if (!isset($prafix)) $prafix=".";
require_once ($prafix.'/konfiguro/moduso.php');
require_once ($prafix.'/konfiguro/opcioj.php');

session_name("IS_Admin_".MODUSO."_SessioID");

if(!defined("DEBUG"))
	 define("DEBUG", FALSE);

require_once ($prafix.'/konfiguro/datumaro.php');
require_once ($prafix.'/konfiguro/objektoj_kotizo.php');

require_once ($prafix.'/iloj/iloj_sql.php');
require_once ($prafix.'/iloj/iloj_cxambroj.php');  //TODO:? BITTE nochmal überdenken und hochschieben.
// [respondo de Martin:] Die Einteilung in Ilo_cxambroj. bzw. das gesamte Konzept wie die Funktionen auf Dateien verteilt sind und sich diese gegenseitig verlinken.

require_once ($prafix.'/iloj/iloj_html.php');
require_once ($prafix.'/iloj/objektoj.php');
require_once ($prafix.'/iloj/objektoj_diversaj.php');
require_once ($prafix.'/iloj/objekto_partopreno.php');
require_once ($prafix.'/iloj/objekto_partoprenanto.php');
require_once ($prafix.'/iloj/objekto_renkontigxo.php');
require_once ($prafix.'/iloj/objekto_invitpeto.php');

require_once ($prafix.'/iloj/iloj_mesagxoj.php');
// require_once ($prafix.'/iloj/kreu_konfirmilon.php');
require_once ($prafix.'/iloj/iloj_tekstoj.php');
require_once ($prafix.'/iloj/iloj_sercxo_rezulto.php');

/**
 * kontrolas, cxu $lakodnomo estas kun $lakodvorto 
 * en la datumbazo.
 * se jes, la funkcio metas la nomon kaj identifikilon
 *  en la SESSION-variablo $kkren kaj redonas TRUE,
 * alikaze la funkcio redonas FALSE.
 */
function kontrolu_entajpanton($lakodnomo,$lakodvorto)
{
  $sql = datumbazdemando(array("ID", "nomo", 'partoprenanto_id'), "entajpantoj",
						 array("nomo = '$lakodnomo'", "kodvorto = '$lakodvorto'"),
						 "",
						 array("order" => "id"));
  $result = sql_faru($sql);

  if ($row = mysql_fetch_array($result, MYSQL_BOTH))
  {
    $_SESSION["kkren"] =
	    array("entajpanto" => $row[ID],
              "entajpantonomo" => $row[nomo],
			  "partoprenanto_id" => $row['partoprenanto_id']);
    return true;
  }
  else
  {
    return false;
  }
}

/**
 * kontrolas, cxu la nuna uzanto (identifikata per la
 * globalaj variabloj $kodnomo kaj $kodvorto) havas la
 * rajton $ago.
 * redonas TRUE aux FALSE.
 */
function rajtas($ago)
{
  $sql = datumbazdemando($ago, "entajpantoj",
						 array("nomo = '" . $_SESSION["kodnomo"] . "'",
							   "kodvorto = '" . $_SESSION["kodvorto"] . "'"),
						 "",
						 array("order" => "id"));
  $result = sql_faru($sql);

  if ( ($row = mysql_fetch_array($result, MYSQL_BOTH) )
        and ($row[$ago] == "J")
        )
  {
    return true;
  }
  else
  {
    return false;
  }
}


/**
 * donas erarmesagxon, ke la uzanto ne rajtas fari ion,
 * kaj finas la skripton.
 *
 * $ago - kiun rajton oni bezonus.
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
 * Kontrolas, cxu la nuna uzanto rajtas fari ion.
 * Se ne, donas erarmesagxon kaj finas la skripton.
 */
function kontrolu_rajton($ago)
{
  if (! rajtas($ago) )
	{
	  ne_rajtas($ago);
	}
}

/**
 * enkodas kaj printas cxiujn informojn de la aktuala sesio
 * nur uzata en debug
 * se necesas gxi eldonas cxiujn session datumojn al la ekrano
 */
function es()
{
  echo session_encode();
}

/**
 * transkodigas UTF-8-tekston kun ^ al Latina-1
 * kun Eo-signoj laux Latina-3.
 * Tio estas uzita por la PDF-eldono en kelkaj
 * programeroj.
 */
function eo($teksto)
{
    $trans = array ("C^" => chr(195), "c^" => chr(164),
					"G^" => chr(207), "g^" => chr(223),
					"H^" => chr(176), "h^" => chr(167),
					"J^" => chr(165), "j^" => chr(162),
					"S^" => chr(163), "s^" => chr(217),
					"U^" => chr(186), "u^" => chr(170),
					"ß"=> chr(175),
					//					"E^" => chr(128));
					"E^" => "EUR");

    return (strtr(utf8_decode($teksto), $trans));
}

/**
 * transkodigas tekston en UTF-8 kun ^ al UTF-8
 * kun veraj supersignoj.
 * Tio estas uzata por la unikoda PDF-eldono.
 */
function uni($teksto)
{
  return eotransformado($teksto, "utf-8");
}



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

if(!function_exists('http_redirect'))
{

	// funkcio laux http://www.php.net/manual/de/function.http-redirect.php,
	// kiu ne ekzistas en nia servilo, sed iom simpligita.
	//
	// ni uzas nur $uri kaj $status.
    //
    // Se  $uri ne komencigxas per 'http' (do aux 'https://' aux 'http://'),
    // ni uzas aux https:// aux http://, depende, cxu la aktuala pagxo
    // estis vokita per HTTPS aux ne.
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


?>