<?php

  /**
   * Statistiko pri uzado de ĉambroj, uzado de manĝoj ktp. dum la tuta tempo.
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   */
define("DEBUG", true);


require_once ("iloj/iloj.php");
require_once('iloj/fpdf/fpdf.php');
session_start();

malfermu_datumaro();

if (!rajtas("administri"))
{
  ne_rajtas();
}

HtmlKapo();




/**
 * montras, kiel statas iu cifero por cxiuj unuopaj noktoj.
 *
 * @param eostring $nomo la tabellinia kapo
 * @param int $noktonombro la nombro de nokto por montri
 * @param string $sql SQL-cxeno, kiu povas enhavi la indikon
 *    <samp>{{noktoj}}</samp>, kiu estos anstatauxita per la
 *    koncerna nokto-numero.
 * @param string $klaro CSS-klaso por tiu tabellinio, ekzemple
 *          por kolorado.
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


function metu_tabelkapon($renkontigxodauxro) {
    echo "<!-- renkontigxodauxro: " . $renkontigxodauxro . "-->";
    echo "<table>\n";
    echo "<tr><td />\n";
    
    
    $ar=JMTdisigo($_SESSION["renkontigxo"]->datoj['de']);
    $tago=$ar['tago'];
    
    for ($noktoj = 1; $noktoj <= $renkontigxodauxro; $noktoj++)
        {
            $ar = JMTdisigo( sekvandaton($_SESSION["renkontigxo"]->datoj['de'],
                                         $noktoj) );
            $sektago = $ar['tago'];
            echo "<TD align=center> $tago/$sektago";
            $tago = $sektago;
        }
    echo "</tr>\n";
}


function montru_diversajn_laux_alvenstato($renkontigxdauxro,
                                          $komenctago,
                                          $alvenstatesprimo)
{
    metu_tabelkapon($renkontigxdauxro);

    montru_laux_tage("partoprenantoj entute", $renkontigxdauxro,
                     datumbazdemando("count(*)",
                                     array("partoprenoj" => "p"),
                                     array("de <= DATE_ADD('$komenctago', ".
                                           "               INTERVAL ({{nokto}}-1) DAY)",
                                           "gxis > DATE_ADD('$komenctago', ".
                                           "                INTERVAL ({{nokto}}-1) DAY)",
                                           $alvenstatesprimo,
                                           ),
                                     "renkontigxoID"),
                     "malpara");

montru_laux_tage("rezervitaj litoj", $renkontigxdauxro,
				 datumbazdemando("count(*)",
								 array("litonoktoj" => "l",
									   "cxambroj" => "cx",
                                       "partoprenoj" => "p"),
								 array("cx.ID = l.cxambro",
									   "nokto_de <= '{{nokto}}'",
									   "nokto_gxis >= '{{nokto}}'",
									   "rezervtipo = 'r'",
                                       "l.partopreno = p.ID",
                                       $alvenstatesprimo,
                                       ),
								 "renkontigxo"),
				 "para");

montru_laux_tage("disdonitaj litoj", $renkontigxdauxro,
				 datumbazdemando("count(*)",
								 array("litonoktoj" => "l",
									   "cxambroj" => "cx",
                                       "partoprenoj" => "p"),
								 array("cx.ID = l.cxambro",
									   "nokto_de <= '{{nokto}}'",
									   "nokto_gxis >= '{{nokto}}'",
									   "rezervtipo = 'd'",
                                       "l.partopreno = p.ID",
                                       $alvenstatesprimo,
                                       ),
								 "renkontigxoID"
								 ),
				 "malpara");
montru_laux_tage("mang^antoj entute", $renkontigxdauxro,
				 datumbazdemando("count(*)",
                                 array("partoprenoj" => "p"),
								 array("kunmangxas <> 'N'",
									   "de <= DATE_ADD('$komenctago', ".
									   "               INTERVAL ({{nokto}}-1) DAY)",
									   "gxis > DATE_ADD('$komenctago', ".
									   "                INTERVAL ({{nokto}}-1) DAY)",
                                       $alvenstatesprimo,
                                       ),
								 "renkontigxoID"
								 ),
                 "para");
montru_laux_tage("viandmang^antoj", $renkontigxdauxro,
				 datumbazdemando("count(*)", 
                                 array("partoprenoj" => "p"),
								 array("kunmangxas <> 'N'",
									   "vegetare = 'N'",
									   "de <= DATE_ADD('$komenctago', ".
									   "               INTERVAL ({{nokto}}-1) DAY)",
									   "gxis > DATE_ADD('$komenctago', ".
									   "                INTERVAL ({{nokto}}-1) DAY)",
									   $alvenstatesprimo),
								 "renkontigxoID"
								 ),
				 "malpara");

montru_laux_tage("vegetaranoj", $renkontigxdauxro,
				 datumbazdemando("count(*)", 
                                 array("partoprenoj" => "p"),
								 array("kunmangxas <> 'N'",
									   "vegetare = 'J'",
									   "de <= DATE_ADD('$komenctago', ".
									   "               INTERVAL ({{nokto}}-1) DAY)",
									   "gxis > DATE_ADD('$komenctago', ".
									   "                INTERVAL ({{nokto}}-1) DAY)",
									   $alvenstatesprimo),
								 "renkontigxoID"
								 ),
				 "para");

montru_laux_tage("veganoj", $renkontigxdauxro,
				 datumbazdemando("count(*)", 
                                 array("partoprenoj" => "p"),
								 array("kunmangxas <> 'N'",
									   "vegetare = 'A'",
									   "de <= DATE_ADD('$komenctago', ".
									   "               INTERVAL ({{nokto}}-1) DAY)",
									   "gxis > DATE_ADD('$komenctago', ".
									   "                INTERVAL ({{nokto}}-1) DAY)",
									   $alvenstatesprimo),
								 "renkontigxoID"
								 ),
				 "malpara");

    echo "</table>";

}

$renkontigxdauxro = $_SESSION['renkontigxo']->renkontigxonoktoj();
$komenctago=$_SESSION["renkontigxo"]->datoj[de];


eoecho("<h1>Partopren-, log^- kaj mang^statistikoj</h1>");

eoecho( "<h2>Alvenintoj kaj (vers^ajne) venontoj</h2>");

montru_diversajn_laux_alvenstato($renkontigxdauxro,
                                 $komenctago,
                                 "p.alvenstato = 'v' OR p.alvenstato = 'a'"
                                 .     "           OR p.alvenstato = 'i'");

eoecho( "<h2>Nur alvenintoj</h2>");

montru_diversajn_laux_alvenstato($renkontigxdauxro,
                                 $komenctago,
                                 "p.alvenstato = 'i' OR p.alvenstato = 'a'");

eoecho( "<h2>Nur akceptitoj</h2>");

montru_diversajn_laux_alvenstato($renkontigxdauxro,
                                 $komenctago,
                                 "p.alvenstato = 'a'");

eoecho("<h2>Viditaj, sed ne akceptitaj</h2>");

montru_diversajn_laux_alvenstato($renkontigxdauxro,
                                 $komenctago,
                                 "p.alvenstato = 'i'");

eoecho("<h2>Vers^ajne venontoj</h2>");

montru_diversajn_laux_alvenstato($renkontigxdauxro,
                                 $komenctago,
                                 "p.alvenstato = 'v'");


eoecho( "<h2>Malalig^intoj</h2>");

montru_diversajn_laux_alvenstato($renkontigxdauxro,
                                 $komenctago,
                                 "p.alvenstato = 'm'");

eoecho("<h2>Vers^ajne ne venos</h2>");

montru_diversajn_laux_alvenstato($renkontigxdauxro,
                                 $komenctago,
                                 "p.alvenstato = 'n'");




HtmlFino();

?>