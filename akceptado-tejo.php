<?php


/*
 * Akceptado de partoprenantoj
 *
 * Pasxo 3 - TEJO-membreco
 *
 */


require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

kontrolu_rajton("akcepti");
require_once('iloj/iloj_akceptado.php');


  $partoprenanto = $_SESSION["partoprenanto"];
  $partopreno = $_SESSION['partopreno'];
  if($_REQUEST['partoprenidento'] and
	  $_REQUEST['partoprenidento'] != $partopreno->datoj['ID'])
	{
		// iu malgxusta okazis - ni rekreu la $partoprenon.
		$partopreno = new Partopreno($_REQUEST['partoprenidento']);
		$_SESSION['partopreno'] = $partopreno;
		$partoprenanto = new Partoprenanto($partopreno->datoj['partoprenantoID']);
		$_SESSION['partoprenanto'] = $partoprenanto;
	}

  // la persona pronomo (li aux sxi)
  $ri = $partoprenanto->personapronomo;
  $Ri = ucfirst($ri);

akceptado_kapo("tejo");

if ($_POST['sendu'])
{
	$antauxa_kontrolstato = $partopreno->datoj['tejo_membro_kontrolita'];

	switch($ago)
	{
		case 'ne':
			sxangxu_datumbazon('partoprenoj',
			                   array('tejo_membro_kontrolita' => 'n'),
			                   array('ID' => $partopreno->datoj['ID'])
			                  );
		break;
		case 'igxu':
			sxangxu_datumbazon('partoprenoj',
			                   array('tejo_membro_kontrolita' => 'i',
			                         'tejo_membro_kotizo' => $_REQUEST['kotizo']),
			                   array('ID' => $partopreno->datoj['ID'])
			                  );
		break;
		case 'jam':
			sxangxu_datumbazon('partoprenoj',
			                   array('tejo_membro_kontrolita' => 'j'),
			                   array('ID' => $partopreno->datoj['ID'])
			                  );
		break;
		default:
			darf_nicht_sein();
	}
	$partopreno = new Partopreno($partopreno->datoj['ID']);
	eoecho ("<p>S^ang^is <code>tejo_membro_kontrolita</code> de <code>" .
           $antauxa_kontrolstato . "</code> al <code>" .
           $partopreno->datoj['tejo_membro_kontrolita']  . "</code>.</p>");
}


	// ###############################################################################

	if(!necesas_tejo_traktado())
	{
		eoecho ("<p>Lau^ la opinio de la programo ne necesas " .
		        "TEJO-traktado por tiu ulo. Vi (kiel homo) " .
		        "kompreneble tamen rajtas fari tion.</p>");
	}

	echo "<form action='akceptado-tejo.php' method='post'>";

	eoecho ("<ul><li>Se {$ri} estos membro de TEJO por la jaro " .
	         TEJO_MEMBRO_JARO . ", {$ri} ricevos rabaton" .
	        " de " . TEJO_RABATO . " E^.\n");
	if ($partoprenanto->datoj['naskigxdato'] < TEJO_AGXO_LIMDATO)
	{
		eoecho ("<br />Lau^ nia kalkulo, {$ri} estas <strong>tro ag^a</strong> por ig^i" .
				 "TEJO-membro.</li>");
	}
	eoecho ("<li>Lau^ nia datumbazo, ");
	switch ($partopreno->datoj['tejo_membro_laudire'] . $partopreno->datoj['tejo_membro_kontrolita'])
	{
		case 'jj':
		case 'nj':
			eoecho ("${ri} estas konfirmita membro de TEJO por " . TEJO_MEMBRO_JARO . ".");
			$stato = 'jam';
			break;
		case 'jn':
			eoecho ("${ri} asertis esti membro de TEJO por " . TEJO_MEMBRO_JARO .
					  ", sed kontrolo donis kontrau^an rezulton.");
			$stato = 'igxu';
			break;
		case 'j?':
			eoecho ("${ri} asertis esti membro de TEJO por " . TEJO_MEMBRO_JARO .
					  ", kaj kontrolo ne jam okazis.");
			$stato = 'igxu';
			break;
		case 'ji':
		case 'ni':
			eoecho ("<p>{$ri} decidis ig^i surloke membro de TEJO por " . TEJO_MEMBRO_JARO .
                 " kaj pagis au^ pagos la kotizon de " .
			          $partopreno->datoj['tejo_membro_kotizo'] . " E^.");
			$stato = 'igxu';
			break;
		case 'nn':
		case 'n?':
			eoecho ("${ri} ne estas TEJO-membro por " . TEJO_MEMBRO_JARO . ".");
			$stato = 'ne';
		default:
			darf_nicht_sein("illegaler Zustand von <code>tejo_membro_laudire</code> (" .
			                $partopreno->datoj['tejo_membro_laudire'] .
			                ") oder <code>tejo_membro_kontrolita</code (" .
			                $partopreno->datoj['tejo_membro_kontrolita'] .
			                ").");
	}
	eoecho (" Kion ni faru?</p>");
	entajpbutono("<p>", 'ago', 'igxu', $stato, 'igxu',
					 "{$Ri} ig^as TEJO-membro kaj pagas ");
	entajpejo("la kotizon de ", 'kotizo', $partopreno->datoj['tejo_membro_kotizo'],
	          "10", "", "", " E^ kun la renkontig^a kotizo.</p>");
	entajpbutono("<p>", 'ago', 'jam', $stato, 'jam',
					 "{$Ri} jam estas TEJO-membro por " . TEJO_MEMBRO_JARO .
					 " kaj povis pruvi tion, do ricevos rabaton sen pagi".
	             "  apartan TEJO-kotizon.</p>");
	entajpbutono("<p>", 'ago', 'ne', $stato, 'ne',
					 "{$Ri} nek estas TEJO-membro nek volas au^ povas ig^i, kaj rezignas" .
					 " pri la TEJO-rabato.</p>");

	echo "</li></ul><p>";
	tenukasxe('partoprenidento', $partopreno->datoj['ID']);
	send_butono("S^ang^u");

	if (necesas_lokaasocio_traktado())
	{
		ligu("akceptado-lokaasocio.php", "TEJO-kotizo klaras, plu al <em>membreco c^e " .
		      deviga_membreco_nomo . "</em>.");
	}
	else
	{
		ligu("akceptado-cxambro.php", "TEJO-kotizo klaras, plu al <em>cxambroj</em>.");
	}
	echo "</p></form>";


HtmlFino();

?>
