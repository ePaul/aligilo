<?php


  /**
   * Dua paĝo de la aliĝilo.
   *
   * Ĝi demandas pri personaj datoj kaj partopreno-detaloj.
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


$lingvoj = array('eo', 'de');

simpla_aligxilo_komenco(2,
                 CH('aligxilo#titolo'),
                 $lingvoj);

echo "<!-- POST:";
var_export($_POST);
echo "-->";

	echo "<tr>\n";
aliĝilo_tabelentajpilo('personanomo',
               CH('persona-nomo'),
               40, 1);
aliĝilo_tabelentajpilo('telefono', CH('telefono'),
               "30", "",
                CH('internacia-formato'));
?>
        </tr>
        <tr>
<?php
	aliĝilo_tabelentajpilo('nomo',
                   CH('familia-nomo') ,
						'40', 1);
	aliĝilo_tabelentajpilo('telefakso',
                   CH('telefakso'),
                   '30', '',
                   CH('internacia-formato'));
?>
        </tr>
        <tr>
<?php
        aliĝilo_tabelelektilo('sekso',
                      CH('sekso'),
					  array('-' => "",
                            'i' => CH('ina'),
                            'v' => CH('vira')),
					  '-', 1);

aliĝilo_tabelentajpilo('retposxto',
               CH('retposxto'),
               30);

?>
        </tr>
        <tr>
<?php
	aliĝilo_tabelentajpilo('adresaldonajxo',
                   CH('adresaldonajxo'),
						30, 1);

$tejo_rabato_ligo = CH('tejo_rabato_ligo');
$tejo_titolo = CH('TEJO', '<a href="' . $tejo_rabato_ligo . '">', '</a>');
	if (strcmp($_POST['naskigxdato'], $GLOBALS['TEJO_membro_limdato']) <= 0)
	{
		aliĝilo_tabelkaŝilo('tejo_membro_laudire',
                     $tejo_titolo,
                     'n',
                     CH('tejo-tro-agxa')
							);
	}
	else
	{
		aliĝilo_tabelelektilo('tejo_membro_laudire',
                      $tejo_titolo,
                      array('j' => CH('tejo-membros'),
                            'n' => CH('tejo-ne-membros')),
                      "n");
	}
?>
        </tr>
        <tr>
<?php
	aliĝilo_tabelentajpilo('strato',
                   CH('strato'),
                   '35', '1');
$gej_ligo = CH('ligo-nemembroj');

	if (strcmp($_POST['naskigxdato'], $GLOBALS['GEJ_membro_limdato']) <= 0)
        // tro agxa por esti membro de GEJ
	{
		aliĝilo_tabelelektilo('GEJmembro',
                      CH('gea', '<a href="' . $gej_ligo . '">', '</a>'),
                      array('J' => CH('gea-membros'),
                            'N' => CH('gea-ne-membros')),
                      'N');
	}
	else
	{
		aliĝilo_tabelelektilo('GEJmembro',
                      CH('gej', '<a href="' . $gej_ligo . '">', '</a>'),
                      array('J' => CH('gej-membros'),
                            'N' => CH('gej-ne-membros')),
                      'N');
	}
?>
        </tr>
        <tr>
<?php
        aliĝilo_tabelentajpilo('provinco',
                       CH('provinco'),
						20, 1);


$cxambro_titolo = CH('cxambro');
	if ($_REQUEST['domotipo'] == 'J')
	{
        // TODO: pripensu, kiel anstatauxi
		tabelelektilo('cxambrotipo',
                      $cxambro_titolo,
                      array('u', 'g' /*, 'd'*/),
                      array('u' => CH('cxambro-unuseksa'),
                            'g' => CH('cxambro-ambauxseksa'),
                            'd' => CH('dulita', 20)),
                      'g',
                      "",
                      CH('cxambro-dulita-nehavebla'));
	}
	else
	{
		aliĝilo_tabelkaŝilo('cxambrotipo',
                     $cxambro_titolo,
                     'g',
                     CH('cxambro-amaslogxejo')
                     );
	}
?>
        </tr>
        <tr>
<?php
        aliĝilo_tabelentajpilo('posxtkodo',
                       CH('posxtkodo'),
                       10, 1);

	if ($_REQUEST['domotipo'] == 'J')
	{
		aliĝilo_tabelentajpilo('kunkiu',
                       CH('kunkiu'),
                       30);
	}
	else
	{
		aliĝilo_tabelkaŝilo('kunkiu', "", '', "");
	}

?>
        </tr>
        <tr>
<?php

        aliĝilo_tabelentajpilo('urbo', CH('urbo'),
                       30, 1);


aliĝilo_tabelelektilo('havas_asekuron',
                      CH('san-asekuro'),
                      array('J' => CH('havas-asekuron'),
                            'N' =>  CH('ne-havas-asekuron')),
					  'J');
?>
        </tr>
        <tr>
<?php
              
if ($_POST['domotipo'] == 'M')
    {
        ?><td colspan='2'>
            <?php
            echo  CH('mangxado-memzorgantoj');
        ?></td><?php
     }

              
	if (strcmp($_POST['naskigxdato'], $GLOBALS['invitletero_agxo_limdato']) <= 0)
	{
		 // pli ol 30 jaroj je komenco de IS
		$invitkotizo = 10;
	}
	else
	{
		$invitkotizo = 5;
	}
aliĝilo_tabelelektilo('invitletero', CH('invitletero'),
					  array('N', 'J'),
                      array('N' => CH('invit-ne-bezonas'),
                            'J' => CH('invit-bezonas', $invitkotizo) ),
                      'N', 1);
?>
        </tr>
        <tr>
<?php

        aliĝilo_tabelelektilo('vegetare',
                              CH('Mangxado') ,
                              array('-' => "",
                                    'N' => CH('mangxas-cxion-ajn') ,
                                    'J' => CH('vegetare') ,
                                    'A' => CH('vegane')),
                              '-', 1);

aliĝilo_tabelelektilo('nivelo', CH('lingva-nivelo'),
                      array('-' => "",
                            'f' => CH('lingvo-flua'),
                            'p' => CH('lingvo-parol'),
                            'k' => CH('lingvo-komencanto')),
                      '-');

?>
        </tr>
<?php


	simpla_aligxilo_fino(2);

?>