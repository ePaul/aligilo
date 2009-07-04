<?php

  /**
   * Aliĝilo - paĝo 5 (diversaj konfirmoj kaj detal-elektoj)
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


simpla_aliĝilo_komenco(5, CH('aligxilo#titolo'));

?>
      <tr>
    <td colspan='4'>
<?php

      echo CH("bonvolu-kontroli");

?>
    </td>
        </tr>
		  <tr>
   <td colspan='4'>
<?php

    ;
// eoecho ("<em>Ĉi tie aperos listo de ĉio, kio estis" .
//         " entajpita ĝis nun (ankoraŭ ne pretas).</em>");

require_once($GLOBALS['prafix'] . "/iloj/iloj_aligxilo.php");
require_once($GLOBALS['prafix'] . "/tradukendaj_iloj/iloj_konfirmilo.php");


$listo =& mangxu_Aligxilajn_datumojn($GLOBALS['renkontigxoID']);

/*
echo "<!-- ";
var_export($listo);
echo "-->";
*/

list($partoprenanto, $partopreno) = $listo;

if (DEBUG) {
  echo "<!-- ";
  var_export($partoprenanto);
  var_export($partopreno);
  echo "-->";
}


eoecho( kreu_aligxilan_kontroltabelon($partoprenanto, $partopreno));


?>
      </td>
    </tr>
    <tr>
<?php

$lingvolisto = array('eo' => CH("nur-esperante"),
                     'cs' => CH("ankaux-cxehxe"),
                     'de' => CH("ankaux-germane"),
                     'pl' => CH("ankaux-pole"),
                     // TODO ... eble pliaj lingvoj
                     );

// TODO: pripensu oferti la konfirmilon en pli ol du lingvoj.

aliĝilo_tabelelektilo('konfirmilolingvo',
                      CH('konfirmiloj-lingvoj'),
                      $lingvolisto,
                      $lingvo);

?>
  </tr>	
<?php


  $rimarko_piednoto = "";

// TODO - ĉu "Alia lando" ĉiam estu numero 47?

if ($_POST['lando'] == 47) // alia lando
{
	$rimarko_piednoto .=
        aliĝilo_aldonu_piednoton(CH('alia-lando-rimarko'));
}


aliĝilo_granda_tabelentajpilo('rimarkoj',
                              CH('rimarkoj-titolo').$rimarko_piednoto);


?>
		  <tr>
<th>
<?php

        ;
$kondicxo_ligo = CH('kondicxo-ligo');
echo CH('kondicx-konsento');

echo "</th>";

debug_echo("<!-- mankas: " . var_export($GLOBALS['mankas'], true) . "-->");

if (is_array($GLOBALS['mankas']) and
             in_array('konsento', $GLOBALS['mankas']))
    {
        echo "<td class='mankas'><p>";
        echo CH("kondicxo-necesas", "<a href='".$kondicxo_ligo."'>", "</a>");
        echo "</p>";
    }
 else
     {
         echo "<td>";
     }
jes_ne_bokso('konsento', false);
echo CH('jes-mi-konsentas', "<a href='".$kondicxo_ligo."'>", "</a>");
             
echo "</td>
        </tr>";







simpla_aliĝilo_fino(5, array('sekven-butono' => CH("Aligxu")));

?>