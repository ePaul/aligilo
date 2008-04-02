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
	$antaupago = $_POST['minumuma_antauxpago'];

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
            /*
			echo lauxlingve(array(
				'de' => "Bitte zahle deine Anzahlung von mindestens {$antauxpago} &euro; bis
							zum {$limdato} an das UEA-Konto 'geju-h'. Beachte, dass UEA
							oft etwas l&auml;nger braucht, um die Zahlungen zu bearbeiten.
							(Wie das geht, steht (auf Esperanto) auf der
							<a href='http://www.uea.org/alighoj/pag_manieroj.html'>UEA-Webseite</a>.)
							Vergesse nicht, als Verwendungszweck 'IS 2006' + deinen Namen anzugeben.",
				'eo' => "Bonvolu pagi vian anta&#365;pagon de almena&#365; {$antauxpago} &euro; 							&#285;is la {$limdato} al la UEA-konto 'geju-h'. (La
						 <a href='http://www.uea.org/alighoj/pag_manieroj.html'>UEA-retpa&#285;o</a>
							enhavas informojn kiel pagi al UEA.) Ne forgesu indiki 'IS 2006' +
							vian nomon en la &#285;irilo.",
				));
            */
			break;
		case 'persone':
            echo CH_repl('pagu_kkrenano', $kiel);
            /*
			echo lauxlingve(array(
				'eo' => "Vi indikis, ke vi pagis a&#365; pagos persone al KKRen-membro. Bonvolu
							mencii la nomon &#265;e <em>&#284;eneralaj rimarkoj</em>. (Se vi ne
							jam pagis: pagu almena&#365; {$antauxpago} &euro; &#285;is la {$limdato},
							por resti en la aktuala ali&#285;kategorio.)",
				'de' => "Du hast angegeben, dass du pers&ouml;nlich an ein KKRen-Mitglied gezahlt
							hast oder zahlen wirst. Bitte nenne den Namen bei den
							<em>Allgemeinen Bemerkungen</em>. (Falls du noch nicht gezahlt hast:
							Zahle mindestens {$antauxpago} &euro; bis zum {$limdato}, um in der
							aktuellen Anmelde-Kategorie zu bleiben.)",
				));
			break;
            */
		case 'gej':
            echo CH_repl('pagu_gej_konto', $kiel);
			break;
		case 'paypal':
            $retadreso = $k->liguAlInterne('gej.kasko');
            echo CH_repl('pagu_per_paypal',
                    compact('antaupago', 'limdato', 'retadreso'),
                    "<a href='http://www.paypal.com/' target='_blank'>",
                    "</a>");
            /*
			echo $k->transformu_tekston(lauxlingve(array(
				'de' => "Bitte melde dich bei <a href='http://www.paypal.de/'
							 target='_blank'>PayPal</a> an (falls du das noch nicht getan hast)
							 und &uuml;berweise die Anzahlung von {$antauxpago} &euro; bis zum
							 {$limdato} an die E-Mail-Adresse {{gej.kasko}}.",
				'eo' => "Bonvolu krei konton &#265;e <a href='http://www.paypal.com/'
							target='_blank'>PayPal</a> (a&#365; la vialanda versio, se vi ne jam
							faris tion) kaj sendu la anta&#365;pagon de {$antauxpago} &euro; &#285;is
							{$limdato} an die E-Mail-Adresse {{gej.kasko}}.",
			)));
            */
			break;
		case 'hej':
            echo CH_repl('pagu_per_hej', $kiel,
                         "<a href='http://ijs.hu/index.php?lang=magyar&section=szovetseg&content=ueagxiro' target='_blank'>",
                         "</a>");
            /*
				echo lauxlingve(array(
						'de' => "Bitte zahle an die Ungarische Esperanto-Jugend (HEJ) bis zum
							{$limdato} mindestens {$antauxpago} &euro;.
							 Wie das geht, ist (auf ungarisch) auf der
					<a href='http://ijs.hu/index.php?lang=magyar&section=szovetseg&content=ueagxiro'
							 target='_blank'>HEJ-Webseite</a> erkl&auml;rt. Vergesse nicht
							 den Verwendungszweck 'IS 2006' und deinen Namen.",
						'eo' => "Bonvolu pagi al la Hungara Esperanto-Junularo (HEJ) &#285;is la
							{$limdato} almena&#365; {$antauxpago} &euro;. Kiel funkcias, tion
							klarigas (en la hungara) la
					<a href='http://ijs.hu/index.php?lang=magyar&section=szovetseg&content=ueagxiro'
							 target='_blank'>HEJ-Retpa&#285;o</a>. Ne forgesu indiki 'IS 2006'
							 kaj vian nomon.",
					));
            */
			break;
		case 'jefo':
            $retadreso = $k->liguAlInterne('kasisto@esperanto-jeunes.org');
            echo CH_repl('pagu_per_jefo',
                         compact('antaupago', 'limdato', 'retadreso'),
                         "<a href='http://esperanto-jeunes.org/kasisto/'
							 target='_blank'>",
                         "</a>");
            /*
				echo $k->transformu_tekston(lauxlingve(array(
					'de' => "Bitte zahle bis zum {$limdato} mindestens {$antauxpago} &euro; an
							<a href='http://esperanto-jeunes.org/kasisto/'
							 target='_blank'>Esp&#233;eranto Jeunes</a> (JEFO). Vergesse nicht den
							Verwendungszweck 'IS 2006' und deinen Namen.",
					'eo' => "Bonvolu pagi &#285;is la {$limdato} almena&#365; {$antauxpago} &euro;
							 al <a href='http://esperanto-jeunes.org/kasisto/'
							 target='_blank'>Esp&#233;eranto Jeunes</a> (JEFO). Ne forgesu la
							indikon 'IS 2006' kaj vian nomon. Kaj rememoru la kasiston
							({{kasisto@esperanto-jeunes.org}}) iom anta&#365; la limdato informi
							la kasiston de GEJ pri la anta&#365;pagoj."
					)));
            */
			break;
		case 'iej':
            echo CH_repl('pagu_per_iej', $kiel,
                         "<a href='http://iej.esperanto.it/'
							 target='_blank'>",
                         "</a>");
            /*
				echo $k->transformu_tekston(lauxlingve(array(
					'de' => "Bitte zahle bis zum {$limdato} mindestens {$antauxpago} &euro; an
							die <a href='http://iej.esperanto.it/'
							 target='_blank'>Italienische Esperanto-Jugend</a> (IEJ). Vergesse nicht 
							den Verwendungszweck 'IS 2006' und deinen Namen.",
					'eo' => "Bonvolu pagi &#285;is la {$limdato} almena&#365; {$antauxpago} &euro;
							 al la <a href='http://iej.esperanto.it/'
							 target='_blank'>Itala Esperantista Junularo</a> (IEJ). Ne forgesu la
							indikon 'IS 2006' kaj vian nomon."
					)));
            */
			break;
		case 'jeb':
            $retadreso = $k->liguAlInterne('rolffantom@yahoo.co.uk');
            echo CH_repl('pagu_per_jeb',
                         compact('antaupago', 'limdato', 'retadreso'),
                         "<a href='http://www.jeb.org.uk/' target='_blank'>",
                         "</a>");
            /*
				echo $k->transformu_tekston(lauxlingve(array(
					'de' => "Bitte zahle bis zum {$limdato} mindestens {$antauxpago} &euro; an
							die <a href='http://www.jeb.org.uk/'
							 target='_blank'>Britische Esperanto-Jugend</a> (JEB). Vergesse nicht 
							den Verwendungszweck 'IS 2006' und deinen Namen. Dazu schicke einen
							 Scheck an <pre>   Rolf Fantom,
   12 Concrete Street
   Lee Mount
   Halifax
   West Yorkshire
   England
   HX3 5DA,</pre> und erfrage bei ihm ({{rolffantom@yahoo.co.uk}}) den entsprechenden Kurs
	(oder &uuml;berweise einfach etwas mehr).",
					'eo' => "Bonvolu pagi &#285;is la {$limdato} almena&#365; {$antauxpago} &euro;
							 al la <a href='http://www.jeb.org.uk/'
							 target='_blank'>Junularo Esperantista Brita</a> (JEB). Ne forgesu la
							indikon 'IS 2006' kaj vian nomon. Sendu &#265;ekon al
				<pre>
   Rolf Fantom,
   12 Concrete Street
   Lee Mount
   Halifax
   West Yorkshire
   England
   HX3 5DA
</pre>
		Eble anta&#365;e demandu retpo&#349;te ({{rolffantom@yahoo.co.uk}}) pri la uzenda
		kurzo, a&#365; simple sendu iom pli (vi rericevos la tropagon dum IS, a&#365; devos
		pagi malpli surloke)."
					)));
            */
			break;
	}

	echo "</p><p>";
    echo CH('aliaj_pageblecoj', "<a href='kontoj' target='_blank'>", "</a>");
    /*
	echo lauxlingve(array(
			'de' => "Falls du dich doch f&uuml;r eine andere Zahlungsart entscheidest:
					Weitere Zahlungsm&ouml;glichkeiten findest du auf der
					 <a href='kontoj' target='_blank'>Konten-Seite.</a>.",
			'eo' => "Se vi tamen decidas pagi alimaniere, &#265;iujn pageblecojn vi
						trovas en la <a href='kontoj' target='_blank'>Kontoj</a>-pa&#285;o."
		));
    */

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

              /*              lauxlingve(array(
		'eo' => "La unua konfirmilo &#265;iuokaze estos (tuj post la ali&#285;o) sendota retpo&#349;te, se vi donis retpo&#349;tadreson.",
		'de' => "Die erste Best&auml;tigung wird jedenfalls (sofort nach der Anmeldung) per E-Mail versandt (falls du eine E-Mail-Adresse angegeben hast).",
			));
              */

?>

         </td>
		</tr>
  <tr>
		<td colspan='2'>
<?php
              echo CH('aligxinto_listo_klarigo', "<a href='listo'>", "</a>");
              /* lauxlingve(array(
		'eo' => "Ekzistas <a href='listo'>listo de ali&#285;intoj</a> &ndash; &#265;u vi volas aperi en &#285;i?",
		'de' => "Es gibt eine <a href='listo'>Liste der Angemeldeten</a> &ndash; willst du dort erscheinen?",
				));
              */
?>

         </td>
<?php
	tabelelektilo('listo',
                  CH('listo')/*
					  array('eo' => "Ali&#285;into-listo:",
                      'de' => "Angemeldeten-Liste:") */,
						array('J', 'N'),
                  array('J' => CH('listo-jes') /*array('eo' => "Jes, mi volas aperi.",
                                                'de' => "Ja, ich will erscheinen.")*/,
                        'N' => CH('listo-ne') /*array('eo' => "Ne, mi ne volas aperi.",
                                               'de' => "Nein, ich will nicht erscheinen.")*/),
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