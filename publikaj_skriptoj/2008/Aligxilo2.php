<?php

$lingvoj = array('eo', 'de');

kontrolu_lingvojn($lingvoj);

simpla_aligxilo_komenco(2,
                 CH('/2007/aligxilo#titolo'),
                 $lingvoj);

/*
echo "<!-- POST:";
var_export($_POST);
echo "-->";
*/

	echo "<tr>\n";
tabelentajpilo('personanomo',
               CH('persona-nomo'),/*
                                   array('eo' => "Persona nomo",
                                   'de' => "Vorname"),
                                  */
               40, 1);
tabelentajpilo('telefono', CH('telefono')
               /* array('eo' => "Telefono", 'de' => "Telefon")*/,
               "30", "",
                CH('internacia-formato')
               /* array('eo' => "(en internacia formato)",
                'de' => "(in internationalem Format)")*/);
?>
        </tr>
        <tr>
<?php
	tabelentajpilo('nomo',
                   CH('familia-nomo') /*,
						array('eo' => 'Familia nomo',
                        'de' => "Familienname")*/,
						'40', 1);
	tabelentajpilo('telefakso',
                   CH('telefakso') /* array('eo' => "Telefakso", 'de' => "Telefax")*/,
                   '30', '',
                   CH('internacia-formato')
                   /* array('eo' => "(en internacia formato)",
                    'de' => "(in internationalem Format)")*/);
?>
        </tr>
        <tr>
<?php
        tabelelektilo('sekso',
                      CH('sekso')
                      /*array('eo' => "Sekso", 'de' => "Geschlecht")*/,
                      array('-', 'i', 'v'),
					  array('-' => "",
                            'i' => CH('ina') /*array('eo' => 'ina', 'de' => "weiblich")*/,
                            'v' => CH('vira') /* array('eo' => 'vira', 'de' => "m&auml;nnlich")*/),
					  '-', 1);

tabelentajpilo('retposxto',
               CH('retposxto')
               /*array('eo' => 'Retpo&#349;to', 'de' => "E-Mail")*/,
               30);

?>
        </tr>
        <tr>
<?php
	tabelentajpilo('adresaldonajxo',
                   CH('adresaldonajxo') /*
						 array('eo' => "Adresaldona&#309;o",
                         'de' => "Adresszusatz")*/,
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
                      array('j' => CH('tejo-membros')
                            /*array('eo' => "Mi membros en TEJO",
                             'de' => "Ich werde TEJO-Mitglied sein")*/,
                            'n' => CH('tejo-ne-membros')
                            /* array('eo' => "Mi ne membros en TEJO",
                             'de' => "Ich werde kein TEJO-Mitglied sein")*/),
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


$cxambro_titolo = CH('cxambro'); /* array('eo' => "&#264;ambro", 'de' => "Zimmer"), */
	if ($_REQUEST['domotipo'] == 'J')
	{
		tabelelektilo('cxambrotipo',
                      $cxambro_titolo,
                      array('u', 'g', 'd'),
                      array('u' => CH('cxambro-unuseksa')
                            /*array('eo' => "Nur mia sekso",
                             'de' => "Nur mein Geschlecht")*/,
                            'g' => CH('cxambro-ambauxseksa')
                            /*array('eo' => "Amba&#365;seksa",
                             'de' => "Beide Geschlechter")*/,
                            'd' => CH('dulita', 20)
                            /*array('eo' => "Dulita (+ 20 &euro;)",
                             'de' => "Zweibettzimmer (+ 20 &euro;)")*/),
                      'g');
	}
	else
	{
		tabelkasxilo('cxambrotipo',
                     $cxambro_titolo,
                     'g',
                     CH('cxambro-amaslogxejo')
                     );
            /*
                     array('eo' => "(En la memzorgantejo ne estas elekteblaj &#265;ambroj.)",
                           'de' => "(In der Massenunterkunft gibt es keine w&auml;hlbaren Zimmer.)"));
            */
	}
?>
        </tr>
        <tr>
<?php
        tabelentajpilo('posxtkodo',
                       CH('posxtkodo')
                       /*array('eo' => "Po&#349;tkodo", 'de' => "PLZ")*/,
                       10, 1);

	if ($_REQUEST['domotipo'] == 'J')
	{
		tabelentajpilo('kunkiu',
                       CH('kunkiu')
                       /*array('eo' => "kune kun", 'de' => "zusammen mit")*/,
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

        tabelentajpilo('urbo', CH('urbo') /* array('eo' => "Urbo", 'de' => "Ort")*/,
                       30, 1);


tabelelektilo('havas_asekuron', CH('san-asekuro') /*array('eo' => "San-Asekuro",
                                                   'de' => "Kranken&shy;versicherung")*/,
					  array('J', 'N'),
              array('J' => CH('havas-asekuron')/*array('eo' => 'Mi havas propran asekuron',
												'de' => "Ich habe eine eigene Versicherung")*/,
                    'N' =>  CH('ne-havas-asekuron')/*array('eo' =>'Mi volas ke GEJ asekuru min',
                                                    'de' => "Ich will, dass DEJ mich versichert")*/),
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
              tabelelektilo('invitletero', CH('invitletero')/*array('eo' => "Invitletero",
                                                             'de' => "Einladungs&shy;brief")*/,
					  array('N', 'J'),
                            array('N' => CH('invit-ne-bezonas') /*array('eo' => 'mi ne bezonas',
                                                                 'de' => "brauche ich nicht")*/,
                                  'J' => CH('invit-bezonas', $invitkotizo) /*array('eo' => "mi bezonas (+ $invitkotizo &euro;)",
                                                                              'de' => "brauche ich (+ $invitkotizo &euro;)")*/),
					  'N');

/*
 * Ekskursoj ne plu bezonatas en la aligxilo, cxar ni
 * enkondukos alian sistemon.
 *

	tabelelektilo('ekskursbileto', array('eo' => "Ekskurso",
													 'de' => "Ausflug"),
					  array('J', 'N'),
					  array('J' => array('eo' => "mi volas ekskursi (+7 &euro;)",
												'de' => "Ich will am Ausflug teilnehmen (+ 7&euro;)"),
					        'N' => array('eo' => "mi ne volas ekskursi",
												'de' => "Ich nehme nicht am Ausflug teil")));
*/

?>
        </tr>
        <tr>
<?php


              tabelelektilo('vegetare', CH('Mangxado') /*array('eo' => "Man&#285;ado",
                                                        'de' => "Essen")*/,
                      array('-', 'N', 'J', 'A'),
                      array('-' => "",
                            'N' => CH('mangxas-cxion-ajn') /*array('eo' => "Mi man&#285;as &#265;ion ajn.",
                                                   'de' => "Ich esse alles m&ouml;gliche")*/,
                            'J' => CH('vegetare') /* array('eo' => "vegetare",
                                                   'de' => "Vegetarisch")*/,
                            'A' => CH('vegane') /* array('eo' => "vegane/vegeta&#309;e",
                                                 'de' => "Vegan")*/),
					  '-', 1);

              tabelelektilo('nivelo', CH('lingva-nivelo') /*array('eo' => "Lingva nivelo",
                                                           'de' => "Sprachniveau")*/,
              array('-', 'f', 'p', 'k'),
              array('-' => "",
                    'f' => CH('lingvo-flua') /*array('eo' => "flua parolanto",
                                              'de' => 'flie&szlig;ender Sprecher')*/,
                    'p' => CH('lingvo-parol')/*array('eo' => "parolanto",
                            'de' => "Sprecher")*/,
                    'k' => CH('lingvo-komencanto')/*array('eo' => "komencanto",
                            'de' => "Anf&auml;nger")*/),
              '-');


	simpla_aligxilo_fino(2);

?>