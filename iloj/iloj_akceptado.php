<?php

/**
 * kelkaj aferoj, kiuj nur necesas dum la akceptada proceduro.
 *
 */

$PASXO_NOMOJ = array(
		'datoj' => "Datumoj",
		'kontroloj' => "Kontroloj",
		'tejo' => "TEJO-membreco",
	);


function necesas_lokaasocio_traktado()
{
	// TODO
	return true;
}

function necesas_tejo_traktado()
{
	if (TEJO_RABATO == 0)
	{
		// ne estas TEJO-rabato por tiu renkontigxo,
		// do ne necesas okupigxi pri TEJO-membrecoj.
		return false;
	}
	$partoprenanto = $_SESSION['partoprenanto'];
	if ($partoprenanto->datoj['naskigxdato'] < TEJO_AGXO_LIMDATO)
	{
		// la partoprenanto estas tro agxa por igxi membro
		// de TEJO.
		return false;
	}
	$partopreno = $_SESSION['partopreno'];
	if ($partoprenanto->datoj['tejo_membro_kontrolita'] == 'j')
	{
		// ni jam antauxe eltrovis, ke la ulo estas TEJO-membro.
		return false;
	}
	return true;
}


/**
 * metas la HTML-kapon kun ioma informo pri la
 *  stato de la akceptado.
 * $pasxo - la nomo de la aktuala pasxo.
 */
function akceptado_kapo($pasxo)
{
	$partoprenanto = $_SESSION['partoprenanto'];
	HtmlKapo();
   eoecho ("<p>Ni nun akceptas <b>".$partoprenanto->datoj[personanomo]." ".
           $partoprenanto->datoj[nomo]." </b>(".$partoprenanto->datoj[ID].
	        ") al la <b>".$_SESSION["renkontigxo"]->datoj[nomo]."</b>.</p>\n");

   eoecho("<h2>Akceptada proceduro &ndash; Pas^o <em>" .
			 $GLOBALS['PASXO_NOMOJ'][$pasxo] .
			 "</em></h2>\n");


	// TODO: reen-ligoj al antauxaj pasxoj

}


?>