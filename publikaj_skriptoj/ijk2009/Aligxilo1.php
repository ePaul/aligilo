<?php

  /**
   * Aligxilo - pagxo 1 (Bazaj informoj bezonataj por la kotizo).
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2006-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */


echo "<!-- defren: " . DEFAUXLTA_RENKONTIGXO . "-->";

  //$renkontigxonumero = 7;

$lingvoj = array();
$fintrad = CH_mult('/2008/aligxilo#fintradukita');
foreach($fintrad AS $lin => $jesne)
{
    if ($jesne == 'jes')
        $lingvoj[]= $lin;
}


simpla_aliĝilo_komenco(1,
                 CH('aligxilo#titolo'),
                 $lingvoj /*,
                           "<script src='kotizokalkulo.js' type='text/javascript'></script>"*/);


echo "<!-- prafix: $prafix -->";

require_once($prafix . '/iloj/iloj.php');

$renkontigxo = new Renkontigxo(DEFAUXLTA_RENKONTIGXO);


?>
        <tr>
          <th><?php
        echo CH('logxlando');
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
          <td rowspan="4" colspan='2' class='nevidebla' id='kotizokalkulo'><p>
<?php
echo CH('jen-baza-kotizo') . "\n";
?></p><span id='kotizocifero' class='kotizocifero'>&nbsp;</span></td>
        </tr>
        <tr>
          <th><?php
echo CH('naskigxdato');
?></th>
          <td>
<?php

require_once($prafix . "/tradukendaj_iloj/trad_htmliloj.php");
simpla_datoelektilo('naskigxo');

?>
</td>
        </tr>
        <tr>
<?php

aliĝilo_tabelelektilo('domotipo',
                       CH('logxado'),
                       array('J' => CH('junulargastejo'),
                             'M' => CH('memzorgantejo')
                             ));
?>
        </tr>
        <tr>
			<th><?php

        echo CH('partoprentempo');
          /*lauxlingve(array('eo' => "Partoprentempo",
      'de' => "Teilnahmezeit"));*/

?></th>
			<td>
<?php
	 
$dateloop = $renkontigxo->datoj['de'];
do
    {
        $de_ebloj[] = $dateloop;
        $dateloop=sekvandaton ($dateloop);
        $gxis_ebloj[] = $dateloop;
    }
 while ($dateloop != $renkontigxo->datoj['gxis']);

elektilo_simpla('de', $de_ebloj, $renkontigxo->datoj['de']);

echo CH('gxis');
//	 echo lauxlingve(array('eo' => ' &#285;is ', 'de' => " bis "));

elektilo_simpla('gxis', $gxis_ebloj, $renkontigxo->datoj['gxis']);

?>
 </td>
</tr>
<?php

 echo "<th>" . CH('mangxmendoj') . "</th>";

?>
</tr>
<?php

simpla_aliĝilo_fino(1);

?>
