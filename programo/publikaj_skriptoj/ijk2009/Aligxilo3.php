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
aliĝilo_granda_tabelentajpilo('lingva_festivalo',
                              CH('kultur-lingva-festivalo'));
aliĝilo_granda_tabelentajpilo('helpo', CH('helpo'));
simpla_aliĝilo_fino(3);

?>
