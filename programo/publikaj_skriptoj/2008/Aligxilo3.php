<?php

  /**
   * Aligxilo - pagxo 3 (Kontribuoj, eble TEJO-kodo).
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id$
   * @since Revizo 141 (antauxe parto de iloj_kotizo.php)
   * @copyright 2006-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */

simpla_aliĝilo_komenco(3, CH('aligxilo#titolo'));

if ($_POST['tejo_membro_laudire'] == 'j')
{
	?>
<tr><td colspan='4'>
<?php

$tejo_rabato_ligo = CH('Aligxilo2.php#tejo_rabato_ligo');

echo 
        CH('donu-uea-kodon', "<a href='" . $tejo_rabato_ligo . "'>", "</a>");

?></td>
</tr>
<tr><?php
aliĝilo_tabelentajpilo('ueakodo', CH('uea-kodo'), 6);
?>
</tr>
<?php
}

?>
<tr><td colspan='4'>
<?php
$rabato_ligo = CH('rabatoj-ligo');

echo CH('bonvolu-kontribui', "<a href='" . $rabato_ligo . "'>", "</a>");

?>
</td>
</tr>
<?php

aliĝilo_granda_tabelentajpilo('distra',  CH('distra-programo'));
aliĝilo_granda_tabelentajpilo('tema',  CH('tema-programo'));
aliĝilo_granda_tabelentajpilo('vespera', CH('vespera-programo'));
aliĝilo_granda_tabelentajpilo('nokta',  CH('nokta-programo'));
aliĝilo_granda_tabelentajpilo('muzika', CH('muzika-vespero'));

simpla_aliĝilo_fino(3);

?>
