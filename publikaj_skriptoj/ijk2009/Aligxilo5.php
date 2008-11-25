<?php

  /**
   * Aliĝilo - paĝo 5 (diversaj konfirmoj kaj detal-elektoj)
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


simpla_aliĝilo_komenco(5, CH('aligxilo#titolo'));

?>
      <tr>
    <td colspan='2'>
<?php

      echo CH("bonvolu-kontroli");

?>
    </td>
        </tr>
		  <tr>
   <td colspan='4'>
<?php

    eoecho ("<em>Cxi tie aperos listo de cxio, kio estis" .
            " entajpita gxis nun (ankoraux ne pretas).</em>");

?>
      </td>
    </tr>
    <tr>
<?php

aliĝilo_tabelelektilo_radie('retakonfirmilo',
                            CH('dua-konfirmilo-formo'),
                            array('J' => CH('retposxte'),
                                  'N' => CH('paperposxte')),
                            'J');




aliĝilo_tabelelektilo('germanakonfirmilo',
                      CH('konfirmiloj-lingvoj'),
                      array('J' => CH('ankaux-germane'),
                            'N' => CH('nur-esperante')),
                      'N');

?>
        </tr>
		  <tr>
			<td colspan='2'>
<?php
          //	echo

?>
</tr><tr>
<?php

	$kondicxo_ligo = CH('kondicxo-ligo');


switch($_POST['konsento'])
    {
    case 'Nl':
        
        echo "		<td colspan='2' class='mankas'>
<em>" .
            CH('kondicxoj-ne-legis-plendo',
               "<a target='_blank' href='" . $kondicxo_ligo . "'>", "</a>")
            . "</em>";
        break;
    case 'Nk':
        echo "		<td colspan='2'  class='mankas'>
<strong>" .
            CH('kondicxoj-ne-konsentas-plendo',
               "<a target='_blank' href='" . $kondicxo_ligo . "'>", "</a>") .
            "</strong>";
        break;
    default:
        echo "		<td colspan='2'>" .
            CH('kondicxoj-demando',
                "<a target='_blank' href='" . $kondicxo_ligo . "'>", "</a>");
	}

echo "</td>\n";

         aliĝilo_tabelelektilo('konsento',
                               CH('kondicx-konsento',
                                  "<a target='_blank' href='" .
                                  $kondicxo_ligo . "'>", "</a>"),
                               array('J' => CH('konsento-jes'),
                                     'Nl' => CH('konsento-nelegis'),
                                     'Nk' => CH('konsento-ne')),
                               'Nl');

?>
  </tr>
  <tr>
	<td colspan='2'>
<?php
              CH('informmesagxo-klarigo');

?></td><?php 
aliĝilo_tabelelektilo('retposxta_varbado',
                      CH('retposxtaj-informoj'),
                      array('j' => CH('retposxtaj-informoj-ikse'),
                            'u' => CH('retposxtaj-informoj-unikode'),
                            'n' => CH('retposxtaj-informoj-ne')),
                      'j');
?>
  </tr>	
<?php

    // TODO - ĉu "Alia lando" ĉiam estu numero 47?

if ($_POST['lando'] == 47) // alia lando
{
	$rimarko_komento .=
        CH('alia-lando-rimarko');
}


if ($rimarko_komento)
{
	echo "<tr><td colspan='4'>" . $rimarko_komento . "</td></tr>\n";
}


aliĝilo_granda_tabelentajpilo('rimarkoj',
                              CH('rimarkoj-titolo'));

?>
  <tr>
		<td /><td colspan='2'><strong style="font-size: 120%"><?php
              echo CH('sekven-vere-aligas');
?></strong>
         </td><td />
  </tr>
<?php


simpla_aliĝilo_fino(5);

?>