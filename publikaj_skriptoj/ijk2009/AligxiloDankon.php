<?php


  /**
   * Lasta paĝo de la aliĝilo.
   *
   * Ĝi enskribas la donitaĵojn en la datumbazon, sendas
   * informajn retmesaĝojn, kaj montras la unuan konfirmilon.
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id$
   * @since Revision 35.
   * @copyright 2001-2004 Martin Sawitzki (paĝo 'publikkontrolo.php')
   *            2004-2006 Paul Ebermann   (paĝo 'publikkontrolo.php')
   *            2006-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */


simpla_aliĝilo_komenco(6, CH('aligxilo#titolo'));

define("echo_sendis_mesagxon", false);

require_once ($prafix . '/iloj/iloj.php');
require_once ($prafix . '/iloj/iloj_mangxoj.php');

$renkontigxo = new Renkontigxo($GLOBALS['renkontigxoID']);



// kontrolado okazis en kontrolu.php

protokolu('aligxo');


require_once($GLOBALS['prafix'] . "/iloj/iloj_aligxilo.php");
require_once($GLOBALS['prafix'] . "/tradukendaj_iloj/iloj_konfirmilo.php");

list($partoprenanto, $partopreno) =
    mangxu_Aligxilajn_datumojn($GLOBALS['renkontigxoID']);

$partoprenanto->skribu_kreante();

$partopreno->datoj['partoprenantoID'] = $partoprenanto->datoj['ID'];

$partopreno->skribu_kreante();

$invitpeto = &$partopreno->sercxu_invitpeton();
if ($invitpeto) {
    $invitpeto->datoj['ID'] = $partopreno->datoj['ID'];
    $invitpeto->skribu_kreante_kun_ID();
 }

if (mangxotraktado == 'libera') {
    // TODO: kontrolu
    traktu_mangxomendojn($partopreno, $_POST['mangxmendo']);
 }
rekalkulu_agxojn($partopreno->datoj['ID']);

require_once($prafix . '/iloj/retmesagxiloj.php');
require_once($prafix . '/tradukendaj_iloj/iloj_konfirmilo.php');
require_once($prafix . '/iloj/diversaj_retmesagxoj.php');


sendu_auxtomatajn_mesagxojn($partopreno, $partoprenanto, $renkontigxo);


sendu_invitilomesagxon($partoprenanto, $partopreno,
                       $renkontigxo,
                       "alig^ilo");

sendu_informmesagxon_pri_programero($partoprenanto, $partopreno,
                                    $renkontigxo,
                                    "alig^ilo");

sendu_sekurkopion_de_aligxinto($partoprenanto, $partopreno, $renkontigxo,
                               "Alig^ilo");

?>
        <tr>
          <td colspan='4'>
				<h1>
<?php
        echo CH('gratulojn');
?></h1>
<?php

	if ($_POST['retposxto'])
	{
        echo "<p>" . CH('konfirmilo-sendita', "<em>" . $_POST['retposxto'] . "</em>") . "</p>\n";
	}
	else
	{
        echo "<p>" . CH('konfirmilo-por-konservado') . "</p>\n";
    }

// sendu (kopion) ecx, se li ne donis retadreson.
$konfirmilo_teksto = kreu_kaj_sendu_unuan_konfirmilon($partoprenanto,
                                                      $partopreno,
                                                      $renkontigxo);

echo "<pre>" . eotransformado($konfirmilo_teksto, 'utf-8') . "</pre>\n";

?>
</td>
        </tr>
	</table>
   </form>
</body>
</html>
