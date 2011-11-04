<?php

  /**
   * Aliĝilo - paĝo 1 (Bazaj informoj bezonataj por la kotizo).
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2006-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */


echo "<!-- defren: " . DEFAUXLTA_RENKONTIGXO . "-->";

  //$renkontigxonumero = 7;

$lingvoj = array();
$fintrad = CH_mult('aligxilo#fintradukita');
foreach($fintrad AS $lin => $jesne)
{
    if ($jesne == 'jes')
        $lingvoj[]= $lin;
}


$skriptoHTML = "";
$skriptoHTML .= "<script src='mangxmendilo.js' type='text/javascript'></script>";
$skriptoHTML .= "<script src='kotizokalkulo.js' type='text/javascript'></script>";

simpla_aliĝilo_komenco(1,
                       CH('aligxilo#titolo'),
                       $lingvoj,
                       $skriptoHTML);


echo "<!-- prafix: $prafix -->";

require_once($prafix . '/iloj/iloj.php');
require_once($prafix . '/iloj/iloj_mangxoj.php');
require_once($prafix . '/tradukendaj_iloj/trad_htmliloj.php');

$renkontigxo = new Renkontigxo(DEFAUXLTA_RENKONTIGXO);


?>
        <tr>
          <th><?php
        echo CH('logxlando') . deviga();
?></th>
          <td>
<?php
$germanlingve = ($lingvo == 'de');
$mankasklaso = (is_array($GLOBALS['mankas']) and in_array('lando', $GLOBALS['mankas'])) ?
			 " class='mankas'" :
			 "";

montru_landoelektilon(5 /* linioj en la elektiloj */,
                      $_POST['lando'] ? $_POST['lando'] : "-#-"
                      /* la elektita lando */,
                      $lingvo,
                      $mankasklaso /* cxu mankis la enhavo */,
                      $renkontigxo);
?>
</td>
          <td rowspan="5" colspan='2' style='border:solid thin blue' class='nevidebla' id='kotizokalkulo'>
    <p>
<?php
  // forlasita, gxis ni adaptos la kotizokalkulilo.
   echo CH('Aligxilo1.php#jen-baza-kotizo') . "\n";
  
?></p><p id='kotizocifero' class='kotizocifero'>&nbsp;</p>
<p><?php
;
 list($kurzo, $kurzodato) = eltrovu_kurzon("EUR", date("Y-m-d"));


echo CH("tio estas en euxroj", $kurzodato); ?></p>
<p id='euxrovaloro' class='euxrovaloro'>
</p>
</td>
        </tr>
        <tr>
          <th><?php
echo CH('naskigxdato') . deviga();
?></th>
          <td>
<?php

require_once($prafix . "/tradukendaj_iloj/trad_htmliloj.php");
simpla_datoelektilo('naskigxo');

?>
</td>
        </tr>
        <tr>
			<th><?php


        echo CH('partoprentempo');
          /*lauxlingve(array('eo' => "Partoprentempo",
      'de' => "Teilnahmezeit"));*/

?></th>
<?php

if (is_array($mankas) and in_array('de', $mankas)) {
    echo "<td class='mankas'>\n";
    echo CH("de-ne-post-gxis");
    echo "<br/>";
 }
 else {
     echo "<td>\n";
 }
	 
$dateloop = $renkontigxo->datoj['de'];
do
    {
        $de_ebloj[] = $dateloop;
        $gxis_ebloj[] = $dateloop;
        $dateloop=sekvandaton ($dateloop);
    }
 while ($dateloop <= $renkontigxo->datoj['gxis']);

elektilo_simpla('de', $de_ebloj, $renkontigxo->datoj['de']);

echo CH('gxis');
//	 echo lauxlingve(array('eo' => ' &#285;is ', 'de' => " bis "));

elektilo_simpla('gxis', $gxis_ebloj, $renkontigxo->datoj['gxis']);

?>
 </td>
</tr>
        <tr>
<?php

aliĝilo_tabelelektilo_radie('domotipo',
                            CH('logxado'),
                            array('J' => CH('junulargastejo'),
				  'J1' => CH('unulita'),
				  //// amasloĝejo ankaŭ elĉerpiĝis.
                                  // 'A' => CH('amaslogxejo'),
                                  //// Tendoj elĉergiĝis.
                                  // 'T' => CH('propra tendo'),
                                  'M' => CH('memzorge')),
                            'J');

?>
        </tr>
        <tr>
<?php

		aliĝilo_tabelelektilo_radie('cxambrotipo',
                              CH('cxambrotipo'),
                              array('u' => CH('cxambro-unuseksa'),
                                    'g' => CH('cxambro-negravas'),
				    ),
                              'g', 3,
                              false, false);

// TODO: alia entajpilo, kaj fino de tabelĉelo
//aliĝilo_tabelentajpilo('kunkiu',
//                       CH('kunkiu'),
//                       30);
echo "<br/>";

simpla_entajpejo(CH('kunkiu'), 'kunKiu', '', 30);

echo "</td>\n";

?>
        </tr>
        <tr>
<?php

 echo "<th>" . CH('mangxmendoj') . "</th>";
echo "<td colspan='3'>";

montru_mangxomendilon();

echo "</td>";

?>
        </tr>
        <tr>
<?php

        ;
aliĝilo_tabelelektilo_radie('vegetare',
                      CH('Mangxado') ,
                      array('N' => CH('mangxas-cxion-ajn') ,
                            'J' => CH('vegetare') ,
			    //// vegana manĝaĵo ne estas havebla.
                            // 'A' => CH('vegane')
			    ),
                      'N');

?>
        </tr>
        <tr>
<?php


$tejo_rabato_ligo = CH('tejo_rabato_ligo');
$tejo_titolo = CH('TEJO', '<a href="' . $tejo_rabato_ligo . '">', '</a>');

aliĝilo_tabelelektilo_radie('tejo_membro_laudire',
                            $tejo_titolo,
                            array('j' => CH('tejo-membros'),
                                   'n' => CH('tejo-ne-membros')),
                            "n", 3);


?>
        </tr>
        <tr>
<?php

        ;
// TODO: prenu kvanton kaj valuton el datumbazo
$invitkotizo = 300;
aliĝilo_tabelelektilo_radie('invitletero',
                            CH('invitletero'),
                            array('N' => CH('invit-ne-bezonas'),
                                  'J' => CH('invit-bezonas', $invitkotizo) ),
                            'N');

?>
        </tr>
        <tr>
<?php



        ;
// TODO: ligo al pagmanieroj
echo "<th>" . CH("pagmaniero") . deviga() . "</th>\n";

if (is_array($GLOBALS['mankas']) and in_array('pagmaniero_1', $GLOBALS['mankas'])) {
    $klaso = "class='mankas'";
    echo "<td colspan='1' class='mankas'>";
 }
 else {
     $klaso = "";
echo "<td colspan='1'>";
 }
echo CH("antauxpagos-gxis");

$limdatoj = listu_limdatojn(CH("surloke"), $renkontigxo, "2009-01-01");

// trovu la sekvan limdaton, por uzi tiun kiel defauxlto
$nun = date("Y-m-d");
foreach($limdatoj AS $ld) {
    if(strcmp($ld, $nun) > 0) {
        break;
    }
}

// echo "<!-- limdatoj: " . var_export($limdatoj, true) . "-->";

elektilo_simpla('antauxpago_gxis', $limdatoj, $ld, "", 1, 1);

entajpbutono("<br/> ", 'pagmaniero_1', $_POST['pagmaniero_1'],
             'peranto', 'peranto', CH("al-peranto"), "", $klaso);

if (is_array($GLOBALS['mankas']) and in_array('pagmaniero_2', $GLOBALS['mankas'])) {
    simpla_entajpejo("", 'pagmaniero_2', "", "", "", "", "",
                     "class='mankas'");
 }
else {
    simpla_entajpejo("", 'pagmaniero_2');
 }

entajpbutono("<br/> ", 'pagmaniero_1', $_POST['pagmaniero_1'],
             'ueakonto', 'ueakonto', CH("al-uea-konto"), "", $klaso);

entajpbutono("<br/> ", 'pagmaniero_1', $_POST['pagmaniero_1'],
             'organizajxo', 'organizajxo', CH("al-bankkonto-de"), "", $klaso);


/**
 * TODO: kiuj landaj asocioj fakte estas perantoj?
 * La sama listo trovigxas cxe 'kontrolu_elekton', eble
 *  trovu manieron havi gxin nur unufoje.
 */
$pagmaniertradukoj = array('cxej' => CH('konto-CxEJ'),
                           'gej' => CH('konto-GEJ'),
                           'pej' => CH('konto-PEJ'),
                           'hej' => CH('konto-HEJ'),
                           'iej' => CH('konto-IEJ'),
                           'jefo' => CH('konto-JEFO')
                           );


elektilo_simpla('pagmaniero_3', $pagmaniertradukoj,
                "");

// entajpbutono("<br/> ", 'pagmaniero_1', $_POST['pagmaniero_1'],
//             'paypal', 'paypal', CH("per-paypal"), "", $klaso);

entajpbutono("<br/> ", 'pagmaniero_1', $_POST['pagmaniero_1'],
             'ne-scias', 'ne-scias', CH("al-ne-scias"), "", $klaso);


/*
aliĝilo_tabelelektilo('pagmaniero',
                      CH('pagmaniero', "<a href='$pagmanierojligo'>", "</a>"),
                      $pagolisto,
                      $pagodefauxlto);
*/

echo ("</td>");

?>
</tr>
<?php




simpla_aliĝilo_fino(1);

?>
