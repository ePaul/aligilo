<?php

  /**
   * cxi tie estas konfiguroj, kiuj estas validaj
   * por cxiuj IS-pagxaroj, ne nur la 2006-aj.
   * Vidu 2006/konfiguro.php por aliaj.
   */

  // la publika loko de la IS-pagxaro.
$pagxo_prefikso = "/(?:is|ali|is/testo?)/";  // Anpassen!


// /** prefikso por atingi la is-admin-dosierujon
//  *   (bezonata ekzemple por la aligxilo)
//  */
// $prafix = "../../admin";


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