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
<?php

if ($_POST['pagokvanto'] != "ne")
{

    // TODO: enmetu en nian programon, aŭ 
    // kreu pli ĝeneralan bibliotekon vokeblan.
	require_once($_SERVER['DOCUMENT_ROOT'] . '/phplibraro/retadreso.php');
    $k = new Kasxilo(CH('/gxenerale#cxe'));

	$limdato = $_POST['antauxpago_limdato'];
	$antaupago = $_POST['minimuma_antauxpago'];

	echo "<td colspan='4'>\n<p>";

    $kiel = compact('antaupago', 'limdato');
    if ($antaupago > 0)
        {
            switch($_POST['pagmaniero'])
                {
                case 'uea':
                    echo CH_repl('pagu_al_uea', $kiel,
                                 "<a href='http://www.uea.org/alighoj/pag_manieroj.html'>",
                                 '</a>');
                    break;
                case 'persone':
                    echo CH_repl('pagu_kkrenano', $kiel);
                case 'gej':
                    echo CH_repl('pagu_gej_konto', $kiel);
                    break;
                case 'paypal':
                    $retadreso = $k->liguAlInterne('gej.kasko');
                    echo CH_repl('pagu_per_paypal',
                                 compact('antaupago', 'limdato', 'retadreso'),
                                 "<a href='http://www.paypal.com/' target='_blank'>",
                                 "</a>");
                    break;
                case 'hej':
                    echo CH_repl('pagu_per_hej', $kiel,
                                 "<a href='http://ijs.hu/index.php?lang=magyar&section=szovetseg&content=ueagxiro' target='_blank'>",
                                 "</a>");
                    break;
                case 'jefo':
                    $retadreso = $k->liguAlInterne('kasisto@esperanto-jeunes.org');
                    echo CH_repl('pagu_per_jefo',
                                 compact('antaupago', 'limdato', 'retadreso'),
                                 "<a href='http://esperanto-jeunes.org/kasisto/'
							 target='_blank'>",
                                 "</a>");
                    break;
                case 'iej':
                    echo CH_repl('pagu_per_iej', $kiel,
                                 "<a href='http://iej.esperanto.it/'
							 target='_blank'>",
                                 "</a>");
                    break;
                case 'jeb':
                    $retadreso = $k->liguAlInterne('rolffantom@yahoo.co.uk');
                    echo CH_repl('pagu_per_jeb',
                                 compact('antaupago', 'limdato', 'retadreso'),
                                 "<a href='http://www.jeb.org.uk/' target='_blank'>",
                                 "</a>");
                    break;
                }


            // TODO: prenu el datumbazo, aŭ simile.
            $kontoligo = "https://is.esperanto.de/2008/01/22/pageblecoj/";

            echo "</p><p>";
            echo CH('aliaj_pageblecoj', "<a href='" . $kontoligo .
                    "' target='_blank'>", "</a>");
        }


?></p></td>
  </tr>
  <tr>
<?php
}

aliĝilo_tabelelektilo('retakonfirmilo',
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
	echo
              CH('unua-cxiam-retposxte');
?>
         </td>
		</tr>
  <tr>
		<td colspan='2'>
<?php
      $listoligo = CH('listoligo');

              echo CH('aligxinto_listo_klarigo', "<a href='$listoligo'>", "</a>");
?>

         </td>
<?php
	aliĝilo_tabelelektilo('listo',
                  CH('listo'),
                  array('J' => CH('listo-jes'),
                        'N' => CH('listo-ne')),
                  'J');

?>
</tr><tr>
		<td colspan='2'>
<?php
              echo CH('partopreninto_listo_klarigo');
?>

         </td>
<?php
	aliĝilo_tabelelektilo('intolisto',
                  CH('into-listo'),
                  array('J' => CH('listo-jes'),
                        'N' => CH('listo-ne')),
                  'J');
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