<?php

  //$lingvoj = array('eo', 'de');

  //kontrolu_lingvojn($lingvoj);

simpla_aligxilo_komenco(3,
                        CH('aligxilo#titolo'),
                        /*					  array('eo' => "50a IS &ndash; ali&#285;ilo",
                         'de' => "50. IS &ndash; Anmeldeformular"),*/
					 $lingvoj);

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
tabelentajpilo('ueakodo',
               CH('uea-kodo'),
               //				   array('eo' => "UEA-kodo",
               //						   'de' => "UEA-Code"),
					6);
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

granda_tabelentajpilo('distra',  CH('distra-programo'));
                      // array('eo' => "Taga distra (a&#365; movada) programo"));
granda_tabelentajpilo('tema',  CH('tema-programo'));
                      //array('eo' => "Taga tema programo"));
granda_tabelentajpilo('vespera', CH('vespera-programo')/* array('eo' => "Vespera programo")*/);
granda_tabelentajpilo('nokta',  CH('nokta-programo') /*array('eo' => "Nokta programo")*/);
granda_tabelentajpilo('muzika', CH('muzika-vespero') /*array('eo' => "Muzika vespero")*/);

simpla_aligxilo_fino(3);

?>
