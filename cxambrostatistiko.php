<?php

  /**
   * Statistiko pri uzado de ĉambroj, uzado de manĝoj ktp. dum la tuta tempo.
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki, 2004-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   */
  //define("DEBUG", true);


require_once ("iloj/iloj.php");
session_start();

malfermu_datumaro();

kontrolu_rajton("statistikumi");

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


function metu_tabelkapon($renkontigxodauxro, $komenctago) {
    echo "<!-- renkontigxodauxro: " . $renkontigxodauxro . "-->";
    echo "<table>\n";
    echo "<tr><td />\n";
    
    
    $ar=JMTdisigo($komenctago);
    $tago=$ar['tago'];
    
    for ($noktoj = 1; $noktoj <= $renkontigxodauxro; $noktoj++)
        {
            $ar = JMTdisigo(sekvandaton($komenctago,
                                         $noktoj));
            $sektago = $ar['tago'];
            echo "<th>" . $tago."/".$sektago."</th>";
            $tago = $sektago;
        }
    echo "</tr>\n";
}

function metu_mangxtabelkapon($renkontigxodauxro, $komenctago) {
    echo "<table>\n";
    echo "<tr><td/><td/>";
    $listo = array();
    
    for ($tagoj = 0; $tagoj <= $renkontigxodauxro; $tagoj++)
        {
            $dato = sekvandaton($komenctago, $tagoj);
            $listo[]= $dato;
            $ar = JMTdisigo($dato);
            $tago = $ar['tago'];
            echo "<th>" . $tago."</th>";
        }
    echo "</tr>\n";
    return $listo;
}


function montru_diversajn_laux_alvenstato($renkontigxdauxro,
                                          $komenctago,
                                          $alvenstatesprimo)
{
    metu_tabelkapon($renkontigxdauxro, $komenctago);

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
    montru_laux_tage("bezonas liton", $renkontigxdauxro,
                     datumbazdemando("count(*)",
                                     array("partoprenoj" => "p"),
                                     array("de <= DATE_ADD('$komenctago', ".
                                           "               INTERVAL ({{nokto}}-1) DAY)",
                                           "gxis > DATE_ADD('$komenctago', ".
                                           "                INTERVAL ({{nokto}}-1) DAY)",
                                           "domotipo" => "J",
                                           $alvenstatesprimo,
                                           ),
                                     "renkontigxoID"),
                     "para");

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
				 "malpara");

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
				 "para");
 if (mangxotraktado == 'ligita') {
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
                 "malpara");
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
				 "para");

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
				 "malpara");

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
				 "para");
 }
 echo "</table>";

 if (mangxotraktado=='libera') {
     
     $tagolisto = metu_mangxtabelkapon($renkontigxdauxro,
                                       $komenctago);
     $para = array("para", "malpara");
     montru_mangxojn_laux_tage("entute",
                               array($alvenstatesprimo),
                               $tagolisto,
                               $para);
     montru_mangxojn_laux_tage("viandmang^antoj",
                               array($alvenstatesprimo,
                                     'vegetare' => "N"),
                               $tagolisto,
                               $para);
     montru_mangxojn_laux_tage("vegetaranoj",
                               array($alvenstatesprimo,
                                     'vegetare' => "J"),
                               $tagolisto,
                               $para);
     montru_mangxojn_laux_tage("veganoj",
                               array($alvenstatesprimo,
                                     'vegetare' => "A"),
                               $tagolisto,
                               $para);
     echo "</table>";
 }

}

/**
 * @param array $tagolisto
 * @param array $para
 */
function montru_mangxojn_laux_tage($titolo, $kondicxoj,
                                   $tagolisto, &$para)
{
    $kondicxoj[]= "t.ID = mangxtempoID";
    $kondicxoj[]= "p.ID = partoprenoID";
    $tabeloj = array("mangxtempoj" => "t",
                     "mangxmendoj" => "m",
                     "partoprenoj" => "p");

    $linioj = eltrovu_gxenerale("count(DISTINCT mangxotipo)",
                                $tabeloj,
                                $kondicxoj,
                                "t.renkontigxoID");
    
    $sql = datumbazdemando(array("dato", "mangxotipo",
                                 "count(partoprenoID)" => "num"),
                           $tabeloj,
                           $kondicxoj,
                           "t.renkontigxoID",
                           array("group" =>
                                 "mangxotipo ASC, dato ASC"));

    $parindex = 0;
    if ($linioj > 0) {
        echo "<tr class='{$para[0]}'>";
        $para = array_reverse($para);
    
    // TODO: auxtomate kalkuli, kiom da linioj ni bezonas,
    // aux meti 0-liniojn.
        echo "<th rowspan='$linioj'>";
        eoecho($titolo);
        echo "</th>";
        $rez = sql_faru($sql);
        $lasta_tipo = "#";
        while($linio = mysql_fetch_assoc($rez)) {
            if ($linio['mangxotipo'] != $lasta_tipo) {
                if ($lasta_tipo != "#") {
                    while ($dato != null) {
                        echo "<td/>";
                        $dato = next($tagolisto);
                    }
                    echo "</tr><tr class='{$para[0]}'>";
                    $para = array_reverse($para);
                }
                echo "<th>" . $linio['mangxotipo'] . "</th>";
                $lasta_tipo = $linio['mangxotipo'];
                $dato = reset($tagolisto);
            }
            // TODO: kontroli, cxu la aktuala dato estas
            //  la gxusta el la listo, alikaze lasu spacon.
            
            while ($dato and $dato != $linio['dato']) {
                echo "<td/>";
                $dato = next($tagolisto);
            }
            echo "<td>";
            debug_echo("<!--" . $linio['dato'] . "/" .
                       $linio['mangxotipo'] . ": -->");
            eoecho ($linio['num'] . "</td>");
            $dato = next($tagolisto);
        }
        while ($dato != null) {
            echo "<td/>";
            $dato = next($tagolisto);
        }
        echo "</tr>";
    }
}


$renkontigxdauxro = $_SESSION['renkontigxo']->renkontigxonoktoj();
$komenctago=$_SESSION["renkontigxo"]->datoj[de];

echo "<p>";
ligu("statistikoj.php", "Landostatistiko");
echo "</p>";


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
