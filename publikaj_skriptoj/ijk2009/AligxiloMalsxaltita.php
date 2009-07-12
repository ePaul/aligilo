<?php


  /**
   * Aliĝilo - paĝo por malŝaltita stato 
   *
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id: Aligxilo1.php 274 2008-11-11 22:45:05Z epaul $
   * @copyright 2006-2009 Paul Ebermann.
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

simpla_aliĝilo_komenco(0,
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
<?php
                                 ;
  if (marku_traduko_eo_anstatauxojn and $GLOBALS['bezonis-eo-tekston']) {
      aliĝilo_aldonu_piednoton(CH("~#informo-pri-1"), "¹");
  }

  $GLOBALS['aliĝilo_piednotilo']->montru_piednotojn();
    

?>
</body>
</html>
