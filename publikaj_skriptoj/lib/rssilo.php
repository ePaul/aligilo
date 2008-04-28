<?php

/**
 * Libraro por kreado de dulingva RSS-2.0-fluo.
 *
 * Ioma dokumentado:
 *    http://www.esperanto.de/dej/aktivikio.pl?Retejo-Funkciado/RSS
 * kaj pri la formato de $mesagxoj:
 *    http://www.esperanto.de/dej/aktivikio.pl?Retejo-Funkciado/Aktualajxoj
 *
 * Jen la varianto por la IS-pagxaro (plurlingva)
 *
 *
 * la indiko "lauxlingve" signifas, ke estu tauxga parametro
 * por la funkcio lauxlingve() en sxablono.php.
 */


/**
 * $mesagxoj - array() de array()-oj kun
 *             la mesagxoj en certa formato (-> mesagxlisto.php).
 * $bazaj_datoj - array() kun la bazaj informoj
 *                de la RSS-fluo (-> donu_channel_datojn()).
 * redonas la RSS-dokumenton kiel string.
 */
function rss_kreu_dokumenton($mesagxoj, $bazaj_datoj)
{
  $kodigo = $bazaj_datoj['kodigo'];
  $rezulto = <<<DATOFINO
<?xml version="1.0" encoding="{$kodigo}" ?>
<rss version="2.0">
  <channel>

DATOFINO;
  //  $rezulto .= "<!-- bazaj_datoj: " .  var_export($bazaj_datoj, true) . "-->";
  //  $rezulto .= "<!-- mesagxoj: " .  var_export($mesagxoj, true) . "-->";
  $rezulto .= rss_donu_channel_datojn($bazaj_datoj);
  $rezulto .= rss_kreu_cxiujn_itemojn($mesagxoj,
                                      $bazaj_datoj['ligobazo']);
  $rezulto .= <<<DATOFINO
  </channel>
</rss>
DATOFINO;
  return $rezulto;
}

/**
 * $bazaj datoj:
 *  - titolo (lauxlingve)
 *  - ligo   (lauxlingve)
 *  - priskribo (lauxlingve)
 *
 *  - novajxfontoj (array kun dosiernomoj,
 *      estas uzata por kalkuli la
 *      sxangxdaton)
 *  uzataj de aliaj funkcioj:
 *  - kodigo ("UTF-8", "ISO-8859-1", ...)
 *  - ligobazo (lauxlingve)
 *  nenecesaj:
 *  - ttl  (defauxlto: 360 = 6 horoj)
 *  - kopirajto (lauxlingve)
 *  - bildo: array()  (-> rss_formatu_bildon)
 */
function rss_donu_channel_datojn($bazaj_datoj)
{
  $rezulto .= '<title>' . lauxlingve($bazaj_datoj['titolo']) . '</title>
    <link>' . lauxlingve($bazaj_datoj['ligo']) . '</link>
    <description>' . lauxlingve($bazaj_datoj['priskribo']) . '</description>
    <language>' . $GLOBALS['lingvo'] . '</language>
';
  $dato = filemtime(__FILE__);
  foreach($bazaj_datoj['novajxfontoj'] AS $dosiero)
	{
		$dato = max($dato, filemtime($dosiero));
	}
  $sxangxDato = date("r", $dato);
  $ttl = $bazaj_datoj['ttl']
    or $ttl = '360';
  $rezulto .= <<<DATOFINO
    <generator>RSS-Generilo de Pa&#365;lo por IS 2.0<!-- http://www.esperanto.de/dej/aktivikio.pl?Retejo-Funkciado/RSS --></generator>
    <ttl>{$ttl}</ttl>
    <docs>http://blogs.law.harvard.edu/tech/rss</docs>
    <lastBuildDate>{$sxangxDato}</lastBuildDate>

DATOFINO;
  if (lauxlingve($bazaj_datoj['kopirajto']))
    {
      $rezulto .= '    <copyright>'.lauxlingve($bazaj_datoj['kopirajto']).'</copyright>
';
    }

  if ($bazaj_datoj['bildo'])
    {
      $rezulto .= rss_formatu_bildon($bazaj_datoj['bildo']);
    }
  return $rezulto;
}


/**
 * TODO: priskribo
 */
function rss_formatu_bildon($bildo)
{
  // TODO: implementado
  return "";
}


/**
 */
function rss_kreu_cxiujn_itemojn($mesagxoj, $ligobazo)
{
  //  $rezulto .= "<!-- mesagxoj: " .  var_export($mesagxoj, true) . "-->";
  $pubDato = strtotime("2006-10-01");
  $aktuala_dato = date("Y-m-d");
  $rezulto .= "<!-- $aktuala_dato -->";
  foreach($mesagxoj AS $elemento)
    {
		if ($elemento['titolo'][$GLOBALS['lingvo']] and
            (strcmp($elemento['dato'], $aktuala_dato) <= 0) // ne montru estontajn erojn
            )
		{
	      //      $rezulto .= "<!-- elemento: " . var_export($elemento, true) . "-->";
	      $rezulto .= rss_kreu_item($elemento, $ligobazo);
	      $pubDato = max($pubDato, strtotime($elemento['dato']));
		}
    }
  return "    <pubDate>" . date("r", $pubDato) . "</pubDate>\n" . $rezulto;
}

function rss_retadrestransformo($teksto)
{
    $rezulto = "";
    $indekso = 0;
    while(true)
      {
        $pos = strpos($teksto, "{{", $indekso);
        if ($pos === FALSE)
          {
            // ne plu aperas "{{";
            $rezulto .= substr($teksto, $indekso);
            break;
          }
        $rezulto .= substr($teksto, $indekso, $pos-$indekso);
        $fino = strpos($teksto, "}}", $indekso);
        if ($fino === false)
          {
            // ne okazu!
            $rezulto .= "<strong>ERARO</strong>"
              . substr($teksto, $pos+2);
            break;
          }
        $adreso = substr($teksto, $pos+2, $fino - ($pos+2));
        $rezulto .= rss_konvertu_adreson($adreso);
        $indekso = $fino + 2;
      }
    return $rezulto;

}

function rss_konvertu_adreson($adreso)
{
    list($konto, $servilo) = split("@", $adreso);
    if ($servilo == "")
      { 
        $servilo = "esperanto.de";
      }
	 $at = ($GLOBALS['lingvo'] == 'eo') ? '&#265;e' : 'at';
	 
    return "[".$konto . " (" . $at. ") " . $servilo."]";
}


function rss_kreu_item($elemento, $ligobazo)
{
  $guid = lauxlingve($ligobazo) . $elemento['id'];
  $titolo = lauxlingve($elemento['titolo']);
  $description = htmlspecialchars(lauxlingve($elemento['teksto']), ENT_QUOTES);
  $ligilo = lauxlingve($elemento['ligo'])
    or $ligilo = $guid;

  // relative URLs auflösen
  $description = str_replace('<a href="/', '<a href="http://www.esperanto.de/',
                             $description);
  $description = rss_retadrestransformo($description);
  $dato = date("r", strtotime($elemento['dato']));
  $rezulto = <<<DATOFINO
    <item>
      <title>{$titolo}</title>
      <link>{$ligilo}</link>
      <description>{$description}</description>
      <pubDate>{$dato}</pubDate>
      <guid>{$guid}</guid>
    </item>

DATOFINO;
  return $rezulto;
}



?>