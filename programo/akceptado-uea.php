<?php

  // ĉĝĥĵŝŭ

/*
 * Akceptado de partoprenantoj
 *
 * Paŝo 3 - UEA-membreco
 *
 */


require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

kontrolu_rajton("akcepti");
require_once('iloj/iloj_akceptado.php');


sesio_aktualigu_laux_get();
$partoprenanto = $_SESSION["partoprenanto"];
$partopreno = $_SESSION['partopreno'];

if ($_REQUEST['pagoID']) {
  $uea_krompago = new Individua_Krompago($_REQUEST['krompagoID']);
}
else {
  $uea_krompago = new Individua_Krompago(array('partoprenoID' =>
											   $partopreno->datoj['ID'],
											   'tipo' => 'tejokotizo'));
}

// la persona pronomo (li aux sxi)
$ri = $partoprenanto->personapronomo;
$Ri = ucfirst($ri);

akceptado_kapo("uea");



if ($_POST['sendu'])
{
	$antauxa_kontrolstato = $partopreno->datoj['tejo_membro_kontrolita'];


	switch($_POST['ago'])
	{
		case 'ne':
		  $uea_krompago->malaperu_el_datumbazo();
		  sxangxu_datumbazon('partoprenoj',
							 array('tejo_membro_kontrolita' => 'n'),
							 array('ID' => $partopreno->datoj['ID'])
							 );

		break;
		case 'igxu':
		  // aldonu/ŝanĝu krompagon kun la kotizo
		  
		  $uea_krompago->datoj['kvanto'] = $_POST['kotizo'];
		  $uea_krompago->datoj['valuto'] = $_POST['kotizo_valuto'];
		  $uea_krompago->datoj['entajpantoID'] =
			$_SESSION['kkren']['entajpanto'];
		  $uea_krompago->datoj['dato'] = date("y-m-d");

		  if ($uea_krompago->datoj['ID']) {
			$uea_krompago->skribu();
		  }
		  else {
			$uea_krompago->datoj['partoprenoID'] = $partopreno->datoj['ID'];
			$uea_krompago->datoj['tipo'] = 'tejokotizo';
			$uea_krompago->skribu_kreante();
		  }
		  // ŝanĝu la partoprenon
		  sxangxu_datumbazon('partoprenoj',
							 array('tejo_membro_kontrolita' => 'i'),
							 array('ID' => $partopreno->datoj['ID'])
			                  );
            $bla = "<strong>$Ri plenigu la TEJO-alig^ilon por " . TEJO_MEMBRO_JARO . ".</strong>";
		break;
/* 		case 'pagas': */
/* 			sxangxu_datumbazon('partoprenoj', */
/* 			                   array('tejo_membro_kontrolita' => 'p', */
/* 			                         'tejo_membro_kotizo' => $_REQUEST['krompago']), */
/* 			                   array('ID' => $partopreno->datoj['ID']) */
/* 			                  ); */
/*             $bla = "<strong>$Ri plenigu la TEJO-alig^ilon por " . TEJO_MEMBRO_JARO . ".</strong>"; */
/* 		break; */
		case 'jam':
		  $uea_krompago->malaperu_el_datumbazo();

		  sxangxu_datumbazon('partoprenoj',
							 array('tejo_membro_kontrolita' => 'j'),
							 array('ID' => $partopreno->datoj['ID'])
							 );
		  break;
		default:
			darf_nicht_sein("ago = " . $_POST['ago']);
	}
	$partopreno =
	  $_SESSION['partopreno'] =
	  new Partopreno($partopreno->datoj['ID']);
	
	eoecho ("<p>S^ang^is <code>tejo_membro_kontrolita</code> de <code>" .
           $antauxa_kontrolstato . "</code> al <code>" .
           $partopreno->datoj['tejo_membro_kontrolita']  . "</code>.</p>");
    if ($bla)
        {
            eoecho ("<p>" . $bla . "</p>");
        }
}


	// ###############################################################################

$uea_rabato = kalkulu_tejo_rabaton($partoprenanto, $partopreno, $_SESSION['renkontigxo']);
// TODO: anstataŭu la CZK-menciojn per iu ĝenerala.



switch ($partopreno->datoj['tejo_membro_laudire'] . $partopreno->datoj['tejo_membro_kontrolita'])
	{
    case 'jj':
    case 'nj':
        $statoteksto = "${ri} estas konfirmita membro de UEA por " .
            TEJO_MEMBRO_JARO . ", kaj ricevos la rabaton de " . $uea_rabato . " CZK.";
        $stato = 'jam';
        akceptada_instrukcio("Nenio plu necesas pri UEA-membreco.");
        break;
    case 'jn':
    case 'j?':
        $statoteksto = "${ri} asertis esti membro de TEJO por " .
            TEJO_MEMBRO_JARO . ", sed kontrolo donis kontrau^an rezulton.";

		akceptada_instrukcio("Demandu, c^u ${ri} havas pruvilon pri pago ".
							 "de la TEJO-kotizo. Se jes, elektu la punkton".
							 " <em>jam estas</em> kaj klaku <em>S^ang^u</em>.");
        akceptada_instrukcio("Se ne, demandu {$ri}n, c^i $ri volas ig^i ".
							 " TEJO-membro por " . TEJO_MEMBRO_JARO . ", pagi ".
                             "la kotizon nun kaj ricevi rabaton.");
        akceptada_instrukcio("Se jes, donu al $ri UEA-membrig^ilon, kiun" .
                             " $ri plenigu (se ${ri} ne jam havas)." .
							 " (Tie ankau^ trovig^os {$ri}a kotizo.)");
        akceptada_instrukcio("Elektu sube la g^ustan punkton kaj entajpu la ".
                             " kotizon, poste <em>S^ang^u</em>.");
        $stato = 'igxu';
		break;
    case 'ji':
    case 'ni':
	  $statoteksto = "{$ri} decidis ig^i surloke membro de UEA por " .
        TEJO_MEMBRO_JARO . ", pagis au^ pagos la kotizon de <strong>" .
        $uea_krompago->datoj['kvanto'] . " " . $uea_krompago->datoj['valuto']
		." </strong> kaj ricevos la rabaton de " . $uea_rabato . " CZK";
	  $stato = 'igxu';
	  akceptada_instrukcio("Nenio plu farendas pri UEA.");
	  break;
    case 'nn':
    case 'n?':
        $statoteksto = "${ri} ne estas UEA-membro por " .
            TEJO_MEMBRO_JARO . ".";
        akceptada_instrukcio("Demandu {$ri}n, c^i $ri volas ig^i UEA-membro".
                             " por " . TEJO_MEMBRO_JARO . ", pagi " .
                             "la kotizon nun kaj ricevi rabaton de " .
							 $uea_rabato . " CZK");
        akceptada_instrukcio("Se jes, donu al $ri UEA-membrig^ilon, kiun" .
                             " $ri plenigu. (Tie ankau^ trovig^os {$ri}a" .
                             " kotizo.)");
        akceptada_instrukcio("Elektu sube la punkton <em> kaj entajpu la ".
                             " kotizon, poste <em>S^ang^u</em>.");
        $stato = 'ne';
        break;
/*     case 'np': */
/*     case 'jp': */
/*         $statoteksto = "${ri} ne estas TEJO-membro por " . */
/*             TEJO_MEMBRO_JARO . " (= ne ricevas rabaton), sed tamen ial" . */
/*             " pagas " .$partopreno->datoj['tejo_membro_kotizo'] . */
/*             " E^ al TEJO/UEA (ekzemple membrokotizo por troag^ulo," . */
/*             " kategorio MG, au^ por alia persono)."; */
/*         akceptada_instrukcio("Nenio plu farendas pri TEJO."); */
/*         $stato = "pagas"; */
/*         break; */
    default:
        darf_nicht_sein("illegaler Zustand von <code>tejo_membro_laudire</code> (" .
                        $partopreno->datoj['tejo_membro_laudire'] .
                        ") oder <code>tejo_membro_kontrolita</code> (" .
                        $partopreno->datoj['tejo_membro_kontrolita'] .
                        ").");
	}


// 	if ($partoprenanto->datoj['naskigxdato'] < TEJO_AGXO_LIMDATO)
// 	{
//         akceptada_instrukcio("Lau^ nia kalkulo, {$ri} estas " .
//                              "<strong>tro ag^a</strong> por ig^i " .
//                              "TEJO-membro.");
// 	}


ligu_sekvan("TEJO-kotizo klaras.");
akceptado_kesto_fino();

	if(!necesas_tejo_traktado())
	{
		eoecho ("<p>Lau^ la opinio de la programo ne necesas " .
		        "TEJO-traktado por tiu ulo. Vi (kiel homo) " .
		        "kompreneble tamen rajtas fari tion.</p>");
	}


eoecho("<p>Se {$ri} estas membro de TEJO por la jaro " .
       TEJO_MEMBRO_JARO . ", {$ri} ricevas rabaton" .
       " de " . $uea_rabato . " CZK.\n</p>");

eoecho("<h3>Aktuala stato</h3>\n");
/*
if ($partoprenanto->datoj['naskigxdato'] < TEJO_AGXO_LIMDATO)
	{
        
        eoecho("<p>Lau^ nia kalkulo, {$ri} estas " .
               "<strong>tro ag^a</strong> por ig^i " .
               "TEJO-membro. Do ne eblas ricevi TEJO-rabaton.</p>\n");
	}
*/
eoecho("<p>Lau^ la datumbazo, " . $statoteksto . "</p>\n");

eoecho ("<h3>Nova stato</h3>\n");

echo "<form action='akceptado-uea.php' method='post' class='elekto-listo'>";

entajpbutono("<p>", 'ago', 'igxu', $stato, 'igxu',
             "{$Ri} ig^as UEA-membro kaj pagos la UEA-kotizon kun la" .
			 " renkontig^a kotizo.");

simpla_entajpejo("<br/>Kotizo: ", 'kotizo', $uea_krompago->datoj['kvanto'], 10);

$valuto = $uea_krompago->datoj['valuto'] or
  $valuto = 'EUR';
simpla_elektolisto_el_konfiguroj('kotizo_valuto', 'valuto',
								 $valuto);

entajpbutono("<p>", 'ago', 'jam', $stato, 'jam',
             "{$Ri} jam estas UEA-membro por " . TEJO_MEMBRO_JARO .
             " kaj povis pruvi tion, do ricevos rabaton sen pagi".
             "  apartan UEA-kotizon.</p>");
entajpbutono("<p>", 'ago', 'ne', $stato, 'ne',
             "{$Ri} nek estas UEA-membro nek volas au^ povas ig^i, kaj ".
			 "rezignas pri la UEA-rabato.</p>");
/*
entajpbutono("<p>", 'ago', 'pagas', $stato, 'pagas',
             "{$Ri} nek estas UEA-membro nek volas au^ povas ig^i, kaj".
             " rezignas pri la UEA-rabato.<br/> {$Ri} ial tamen pagas ");
simpla_entajpejo("", 'krompago', $partopreno->datoj['tejo_membro_kotizo'],
                 "10", "", " E^ kun la renkontig^a kotizo al TEJO/UEA," .
                 " ekzemple por UEA-membreco (kvankam troag^a por TEJO)," .
                 " membreco en kategorio MG au^ membrokotizo por alia " .
                 " persono. Certigu, ke vi notos sur tau^ga papero (ekzemple".
                 " la alig^ilo), kiom $ri pagis por kio.</p>");
*/
echo ("<p>\n");
tenukasxe('partoprenidento', $partopreno->datoj['ID']);
send_butono("S^ang^u");
echo "\n</p></form>\n";


HtmlFino();
