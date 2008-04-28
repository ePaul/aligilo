<?php

  /**
   * cxi tie estas konfiguroj, kiuj estas validaj
   * por cxiuj IS-pagxaroj, ne nur la 2006-aj.
   * Vidu 2006/konfiguro.php por aliaj.
   */

  // la publika loko de la IS-pagxaro.
$pagxo_prefikso = "/is/";  // Anpassen!


/** prefikso por atingi la is-admin-dosierujon
 *   (bezonata ekzemple por la aligxilo)
 */
$prafix = "../../admin";


/** la nomoj de la lingvoj en la lingvoj mem. */
$lingvonomoj =
    CH_mult('/gxenerale#lingvonomo');
/*
	array(
		'de' => "Deutsch",
		'eo' => "Esperanto",
		'fr' => "Francais",
	   'en' => "English",
		'es' => "Espa&ntilde;ol");
*/


// nomo de la kontakto-ligo
$kontaktonomo = CH('/gxenerale#kontakto-nomo');
    /*
	array('de' => "Kontakt",
			'eo' => "Kontakto",
			'en' => "Contact");
    */

// nomo de pagxarlisto-ligo
$pagxarlistonomo = CH('/gxenerale#pagxarlisto');
/*
	array('de' => "Sitemap",
			'en' => "Sitemap",
			'eo' => "Pa&#285;arlisto"
			);*/

// prefikso por la landaj kategorioj (kategorio A, kategorio B, , ...)
// en la kotizotabeloj.
// $kategorioNomo = CH('/kotizoj#kategorinomo');
 /* array(
			'eo' => "kategorio",
			'de' => "Kategorie",
			'en' => "category",
				);
                                              */

$kotizolisto =
	array("MemzFrua" => CH('/kotizoj#MemzFrua')
          /*array('eo' => "Memzorgantoj, frua ali&#285;o",
           'de' => "Selbstversorger, fr&uuml;he Anmeldung")*/,
          "MemzMalfrua" => CH('/kotizoj#MemzMalfrua')
          /*array('eo' => "Memzorgantoj, malfrua ali&#285;o",
           'de' => "Selbstversorger, sp&auml;te Anmeldung")*/,
          "PlenFrua" => CH('/kotizoj#PlenFrua')
 /*array('eo' => "Plenpagantoj, frua ali&#285;o",
                                                 'de' => "Vollzahler, fr&uuml;he Anmeldung")*/,
          "PlenMalfrua" => CH('/kotizoj#PlenMalfrua') /*array('eo' => "Plenpagantoj, malfrua ali&#285;o",
                                                       'de' => "Vollzahler, sp&auml;te Anmeldung")*/);

/*
$kotizoklarigoj = array(
		'eo' => "La limdato por la frua kategorio estas la 31a de oktobro.
					Necesas kaj alveno de ali&#285;o kaj de anta&#365;pago (por la
					landaj kategorioj A kaj B). Pri pageblecoj rigardu la
				    <a href='kontoj'>kontoj-pa&#285;on</a>. Vian lando-kategorion
					vi povas eltrovi en la <a href='aligxilo'>Ali&#285;ilo</a>, kiu
					anka&#365; estas kotizo-kalkulilo.",
		'de' => "Die Frist f&uuml;r die fr&uuml;he Kategorie endet am 31. Oktober. Es
				   z&auml;hlt der Eingang von Anmeldung und Anzahlung (au&szlig;er in
					Landeskategorie C, da reicht die Anmeldung). F&uuml;r
					Zahlungsm&ouml;glichkeiten sieh dir die <a href='kontoj'>Konten-Seite</a>
					an. Deine Landeskategorie kannst du mit dem
					<a href='aligxilo'>Anmeldeformular</a> herausfunden &ndash; dort kannst
					du auch den kompletten Preis berechnen lassen."
						);
*/

$pagxomankas_mesagxo =
    CH('/gxenerale#lingvomankas');
/*
	array(
		'de' => "Leider gibt es diese Seite noch nicht auf Deutsch, sondern nur in den folgenden Sprachen:",
		'eo' => "Beda&#365;rinde tiu pa&#285;o ne jam ekzistas en Esperanto, sed nur en la sekvaj lingvoj:",
		'en' => "Sorry, your page doesn't yet exist in English, but only in the following languages:"
	);
*/


?>