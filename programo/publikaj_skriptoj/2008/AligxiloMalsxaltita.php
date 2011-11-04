<?php


  /**
   * Aligxilo - pagxo por malsxaltita stato 
   *
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id: Aligxilo1.php 274 2008-11-11 22:45:05Z epaul $
   * @copyright 2006-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */

$lingvoj = array();
$fintrad = CH_mult('aligxilo#fintradukita');
foreach($fintrad AS $lin => $jesne)
{
    if ($jesne == 'jes')
        $lingvoj[]= $lin;
}

simpla_aligxilo_komenco(0,
                 CH('malsxaltita-aligxilo-titolo'),
                 $lingvoj);




?>
<tr><td style='min-width: 10em;'/><td colspan='2'>
    <p style='font-size: 150%'>
<?php

echo nl2br(CH("aligxilo-malsxaltita-teksto"));

?>
</p>
</td><td  style='min-width: 10em;'/>
        </tr>
	</table>
   </form>
</body>
</html>
