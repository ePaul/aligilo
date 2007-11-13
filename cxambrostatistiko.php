<?php

define("DEBUG", true);

/* ############################ */
/* Montras kelkajn statistikojn */
/* ############################ */

// Jes, la nomo estu cxambrostatistiko, ne cxambrosxtatistiko.
// Estas historia kialo.
// TODO: eble iam renomu al cxambrostatistiko

require_once ("iloj/iloj.php");
require_once('iloj/fpdf/fpdf.php');
session_start();

malfermu_datumaro();

if (!rajtas("administri"))
{
  ne_rajtas();
}

HtmlKapo();
echo "<Table border=1>";
echo "<TR><TD>";

$renkontigxdauxro = kalkulu_tagojn( $_SESSION["renkontigxo"]->datoj[de], $_SESSION["renkontigxo"]->datoj[gxis] );
$ar=JMTdisigo($_SESSION["renkontigxo"]->datoj[de]);
$tago=$ar[tago];

for ($noktoj = 1; $noktoj <= $renkontigxdauxro; $noktoj++)
{
   $ar = JMTdisigo( sekvandaton($_SESSION["renkontigxo"]->datoj[de], $noktoj) );
   $sektago = $ar[tago];
   echo "<TD align=center> $tago/$sektago";
   $tago = $sektago;
}


/**
 *
 */
function montru_laux_tage($nomo, $noktonombro, $sql, $klaso)
{
  eoecho( "<tr class='" . $klaso . "'><th>" . $nomo . "</th>");

  //  echo "<!-- noktonombro: $noktonombro -->";

  for ($noktoj = 1; $noktoj <= $noktonombro; $noktoj++)
	{
	  //	  echo "<!-- noktoj: $noktoj -->";
	  $vera_sql = str_replace('{{nokto}}', $noktoj, $sql);
	  echo "<!-- vera_sql: [$vera_sql] -->\n";
	  $row = mysql_fetch_array(sql_faru($vera_sql));
	  echo "<td>". $row[0] . "</td>";
	}
  echo "</tr>\n";
}

montru_laux_tage("rezervitaj litoj", $renkontigxdauxro,
				 datumbazdemando("count(*)",
								 array("litonoktoj" => "l",
									   "cxambroj" => "cx"),
								 array("cx.ID = l.cxambro",
									   "nokto_de <= '{{nokto}}'",
									   "nokto_gxis >= '{{nokto}}'",
									   "rezervtipo = 'r'"),
								 "renkontigxo"),
				 "para");

montru_laux_tage("disdonitaj litoj", $renkontigxdauxro,
				 datumbazdemando("count(*)",
								 array("litonoktoj" => "l",
									   "cxambroj" => "cx"),
								 array("cx.ID = l.cxambro",
									   "nokto_de <= '{{nokto}}'",
									   "nokto_gxis >= '{{nokto}}'",
									   "rezervtipo = 'd'"),
								 "renkontigxo"
								 ),
				 "malpara");
$komenctago=$_SESSION["renkontigxo"]->datoj[de];

montru_laux_tage("mang^antoj entute", $renkontigxdauxro,
				 datumbazdemando("count(*)", "partoprenoj",
								 array("kunmangxas <> 'N'",
									   "de <= DATE_ADD('$komenctago', ".
									   "               INTERVAL ({{nokto}}-1) DAY)",
									   "gxis > DATE_ADD('$komenctago', ".
									   "                INTERVAL ({{nokto}}-1) DAY)",
									   "alvenstato = 'v' or alvenstato = 'a'"),
								 "renkontigxoID"
								 ),
				 "para");
montru_laux_tage("viandmang^antoj", $renkontigxdauxro,
				 datumbazdemando("count(*)", "partoprenoj",
								 array("kunmangxas <> 'N'",
									   "vegetare = 'N'",
									   "de <= DATE_ADD('$komenctago', ".
									   "               INTERVAL ({{nokto}}-1) DAY)",
									   "gxis > DATE_ADD('$komenctago', ".
									   "                INTERVAL ({{nokto}}-1) DAY)",
									   "alvenstato = 'v' or alvenstato = 'a'"),
								 "renkontigxoID"
								 ),
				 "malpara");

montru_laux_tage("vegetaranoj", $renkontigxdauxro,
				 datumbazdemando("count(*)", "partoprenoj",
								 array("kunmangxas <> 'N'",
									   "vegetare = 'J'",
									   "de <= DATE_ADD('$komenctago', ".
									   "               INTERVAL ({{nokto}}-1) DAY)",
									   "gxis > DATE_ADD('$komenctago', ".
									   "                INTERVAL ({{nokto}}-1) DAY)",
									   "alvenstato = 'v' or alvenstato = 'a'"),
								 "renkontigxoID"
								 ),
				 "para");

montru_laux_tage("veganoj", $renkontigxdauxro,
				 datumbazdemando("count(*)", "partoprenoj",
								 array("kunmangxas <> 'N'",
									   "vegetare = 'A'",
									   "de <= DATE_ADD('$komenctago', ".
									   "               INTERVAL ({{nokto}}-1) DAY)",
									   "gxis > DATE_ADD('$komenctago', ".
									   "                INTERVAL ({{nokto}}-1) DAY)",
									   "alvenstato = 'v' or alvenstato = 'a'"),
								 "renkontigxoID"
								 ),
				 "malpara");

echo "</table>";

HtmlFino();

?>