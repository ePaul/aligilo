<?php

$lingvoj = array('eo', 'de');

kontrolu_lingvojn($lingvoj);

simpla_aligxilo_komenco(5,
                 CH('/2007/aligxilo#titolo'),
                 $lingvoj);


?>
        <tr>
<?php

if ($_POST['pagokvanto'] != "ne")
{
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

            echo "</p><p>";
            echo CH('aliaj_pageblecoj', "<a href='kontoj' target='_blank'>", "</a>");
        }


?></p></td>
  </tr>
  <tr>
<?php
}

tabelelektilo('retakonfirmilo',
              CH('dua-konfirmilo-formo')/*
					array('eo' => "La dua konfirmilo estu ...",
							'de' => "Form der zweiten Best&auml;tigung:"
							)*/,
				  array('N', 'J'),
              array('J' => CH('retposxte')/*array('eo' => '... retpo&#349;ta',
                                'de' => 'E-Mail')*/,
                    'N' => CH('paperposxte')/*array('eo' => '... paperpo&#349;ta',
                                 'de' => "Papierpost")*/),
				  'J');

tabelelektilo('germanakonfirmilo',
              CH('konfirmiloj-lingvoj')/*
				  array('eo' => "La konfirmiloj estu ...",
                  'de' => "Sprache der Best&auml;tigungen:")*/,
				  array('J', 'N'),
              array('J' => CH('ankaux-germane')/*array('eo' => '... anka&#365; germanlingvaj',
                                'de' => "auch Deutsch")*/,
                    'N' => CH('nur-esperante') /*array('eo' => '... nur esperantaj',
                                                'de' => "nur Esperanto"))*/,
                    'N'));

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
              echo CH('aligxinto_listo_klarigo', "<a href='listo'>", "</a>");
?>

         </td>
<?php
	tabelelektilo('listo',
                  CH('listo'),
                  array('J', 'N'),
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
	tabelelektilo('intolisto',
                  CH('into-listo'),
                  array('J', 'N'),
                  array('J' => CH('listo-jes'),
                        'N' => CH('listo-ne')),
                  'J');
?>
</tr><tr>
		<td colspan='2'>
<?php

	$kondicxo_ligo = CH('kondicxo-ligo');


    switch($_POST['konsento'])
        {
            case 'Nl':

		        echo CH('kondicxoj-ne-legis-plendo',
				          "<a target='_blank' href='" . $kondicxo_ligo . "'>", "</a>");
            break;
            case 'Nk':
            echo CH('kondicxoj-ne-konsentas-plendo',
				          "<a target='_blank' href='" . $kondicxo_ligo . "'>", "</a>");
            break;
            default:
            echo CH('kondicxoj-demando',
				          "<a target='_blank' href='" . $kondicxo_ligo . "'>", "</a>");
	}

?>
         </td>
<?php

	tabelelektilo('konsento',
                  CH('kondicx-konsento',
				          "<a target='_blank' href='" . $kondicxo_ligo . "'>", "</a>"),
						array('J', 'Nl', 'Nk'),
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
              /*
	echo lauxlingve(array(
		'de' => "Wir wollen in Zukunft gelegentlich (max. 3 mal j&auml;hrlich) Informationen &uuml;ber die n&auml;chsten ISs per E-Mail verschicken. Willst du diese Informationen erhalten? (Du kannst sie jederzeit abbestellen.)",
		'eo' => "Ni estonte volas sendi malregule (maksimume 3 foje jare) informojn pri la sekvaj ISoj per retpo&#349;to. &#264;u vi volas ricevi ilin? (Vi &#265;iam povos malmendi ilin.)",
			));
              */

?></td><?php 
tabelelektilo('retposxta_varbado',
              CH('retposxtaj-informoj')/*
              array('eo' => "retpo&#349;taj informoj",
              'de' => "E-Mail-Informationen")*/,
              array('j', 'u', 'n'),
              array('j' => CH('retposxtaj-informoj-ikse')
                    /* array('de' => "Ja, bitte x-kodiert",
                     'eo' => "Jes, bonvole en x-kodigo")*/,
                    'u' => CH('retposxtaj-informoj-unikode')/*
                    array('de' => "Ja, bitte in Unicode",
                    'eo' => "Jes, bonvole unikode")*/,
                    'n' => CH('retposxtaj-informoj-ne') /*array('de' => "Nein, ich will keine E-Mails bekommen",
                                                         'eo' => "Ne, ne sendu retmesa&#285;ojn al mi")*/),
              'j');
?>
  </tr>	
<?php

if ($_POST['lando'] == 47) // alia lando
{
	$rimarko_komento .=
        CH('alia-lando-rimarko');
        /*
        lauxlingve(array(
		'eo' => "Vi indikis <em>Alia lando</em>, &#265;ar via lando mankis en la listo.
					Bonvolu mencii la nomon de la lando, tiel ni povos aldoni &#285;in al la
					listo. ",
		'de' => "Du hast <em>Anderes Land</em> ausgew&auml;hlt, weil dein Land in der Liste
					 fehlte. Bitte gebe es hier an, damit wir es zur Liste hinzuf&uuml;gen
					k&oumnl;nnen. "
                    ));*/
}


if ($rimarko_komento)
{
	echo "<tr><td colspan='4'>" . $rimarko_komento . "</td></tr>\n";
}


granda_tabelentajpilo('rimarkoj',
                      CH('rimarkoj-titolo') /*
		array('eo' => '&#284;eneralaj rimarkoj:',
        'de' => "Allgemeine Bemerkungen:")*/);

?>
  <tr>
		<td /><td colspan='2'>
<?php
              echo CH('sekven-vere-aligas');
              /*
	echo lauxlingve(array('eo' => "Per la butono <em>Sekven</em> vi nun vere ali&#285;as al IS. "
											."Anta&#365;e kontrolu &#265;u &#265;io &#285;ustas!"));
              */
?>
         </td><td />
  </tr>
<?php


simpla_aligxilo_fino(5);

?>