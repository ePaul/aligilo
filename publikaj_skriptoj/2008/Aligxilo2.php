<?php

$lingvoj = array('eo', 'de');

kontrolu_lingvojn($lingvoj);

simpla_aligxilo_komenco(2,
                 CH('aligxilo#titolo'),
                 $lingvoj);

/*
echo "<!-- POST:";
var_export($_POST);
echo "-->";
*/

	echo "<tr>\n";
tabelentajpilo('personanomo',
               CH('persona-nomo'),
               40, 1);
tabelentajpilo('telefono', CH('telefono'),
               "30", "",
                CH('internacia-formato'));
?>
        </tr>
        <tr>
<?php
	tabelentajpilo('nomo',
                   CH('familia-nomo') ,
						'40', 1);
	tabelentajpilo('telefakso',
                   CH('telefakso'),
                   '30', '',
                   CH('internacia-formato'));
?>
        </tr>
        <tr>
<?php
        tabelelektilo('sekso',
                      CH('sekso'),
                      array('-', 'i', 'v'),
					  array('-' => "",
                            'i' => CH('ina'),
                            'v' => CH('vira')),
					  '-', 1);

tabelentajpilo('retposxto',
               CH('retposxto'),
               30);

?>
        </tr>
        <tr>
<?php
	tabelentajpilo('adresaldonajxo',
                   CH('adresaldonajxo'),
						30, 1);

$tejo_rabato_ligo = CH('tejo_rabato_ligo');
$tejo_titolo = CH('TEJO', '<a href="' . $tejo_rabato_ligo . '">', '</a>');
	if (strcmp($_POST['naskigxdato'], $GLOBALS['TEJO_membro_limdato']) <= 0)
	{
		tabelkasxilo('tejo_membro_laudire',
                     $tejo_titolo,
                     'n',
                     CH('tejo-tro-agxa')
							);
	}
	else
	{
		tabelelektilo('tejo_membro_laudire',
                      $tejo_titolo,
                      array('j', 'n'),
                      array('j' => CH('tejo-membros'),
                            'n' => CH('tejo-ne-membros')),
                      "n");
	}
?>
        </tr>
        <tr>
<?php
	tabelentajpilo('strato',
                   CH('strato'),
                   '35', '1');
$gej_ligo = CH('ligo-nemembroj');

	if (strcmp($_POST['naskigxdato'], $GLOBALS['GEJ_membro_limdato']) <= 0)
        // tro agxa por esti membro de GEJ
	{
		tabelelektilo('GEJmembro',
                      CH('gea', '<a href="' . $gej_ligo . '">', '</a>'),
						  array('J', 'N'),
                      array('J' => CH('gea-membros'),
                            'N' => CH('gea-ne-membros')),
                      'N');
	}
	else
	{
		tabelelektilo('GEJmembro',
                      CH('gej', '<a href="' . $gej_ligo . '">', '</a>'),
                      array('J', 'N'),
                      array('J' => CH('gej-membros'),
                            'N' => CH('gej-ne-membros')),
                      'N');
	}
?>
        </tr>
        <tr>
<?php
        tabelentajpilo('provinco',
                       CH('provinco'),
						20, 1);


$cxambro_titolo = CH('cxambro');
	if ($_REQUEST['domotipo'] == 'J')
	{
		tabelelektilo('cxambrotipo',
                      $cxambro_titolo,
                      array('u', 'g', 'd'),
                      array('u' => CH('cxambro-unuseksa'),
                            'g' => CH('cxambro-ambauxseksa'),
                            'd' => CH('dulita', 20)),
                      'g');
	}
	else
	{
		tabelkasxilo('cxambrotipo',
                     $cxambro_titolo,
                     'g',
                     CH('cxambro-amaslogxejo')
                     );
	}
?>
        </tr>
        <tr>
<?php
        tabelentajpilo('posxtkodo',
                       CH('posxtkodo'),
                       10, 1);

	if ($_REQUEST['domotipo'] == 'J')
	{
		tabelentajpilo('kunkiu',
                       CH('kunkiu'),
                       30);
	}
	else
	{
		tabelkasxilo('kunkiu', "", '', "");
	}

?>
        </tr>
        <tr>
<?php

        tabelentajpilo('urbo', CH('urbo'),
                       30, 1);


tabelelektilo('havas_asekuron', CH('san-asekuro'),
					  array('J', 'N'),
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
              tabelelektilo('invitletero', CH('invitletero'),
					  array('N', 'J'),
                            array('N' => CH('invit-ne-bezonas'),
                                  'J' => CH('invit-bezonas', $invitkotizo) ),
					  'N');
?>
        </tr>
        <tr>
<?php


              tabelelektilo('vegetare', CH('Mangxado') ,
                      array('-', 'N', 'J', 'A'),
                      array('-' => "",
                            'N' => CH('mangxas-cxion-ajn') ,
                            'J' => CH('vegetare') ,
                            'A' => CH('vegane')),
					  '-', 1);

              tabelelektilo('nivelo', CH('lingva-nivelo'),
              array('-', 'f', 'p', 'k'),
              array('-' => "",
                    'f' => CH('lingvo-flua'),
                    'p' => CH('lingvo-parol'),
                    'k' => CH('lingvo-komencanto')),
              '-');


	simpla_aligxilo_fino(2);

?>