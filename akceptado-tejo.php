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


	switch($_POST['ago'])
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
            $bla = "<strong>$Ri plenigu la TEJO-alig^ilon por " . TEJO_MEMBRO_JARO . ".</strong>";
		break;
		case 'jam':
			sxangxu_datumbazon('partoprenoj',
			                   array('tejo_membro_kontrolita' => 'j'),
			                   array('ID' => $partopreno->datoj['ID'])
			                  );
		break;
		default:
			darf_nicht_sein("ago = " . $_POST['ago']);
	}
	$partopreno = new Partopreno($partopreno->datoj['ID']);
	eoecho ("<p>S^ang^is <code>tejo_membro_kontrolita</code> de <code>" .
           $antauxa_kontrolstato . "</code> al <code>" .
           $partopreno->datoj['tejo_membro_kontrolita']  . "</code>.</p>");
    if ($bla)
        {
            eoecho ("<p>" . $bla . "</p>");
        }
}


	// ###############################################################################





switch ($partopreno->datoj['tejo_membro_laudire'] . $partopreno->datoj['tejo_membro_kontrolita'])
	{
    case 'jj':
    case 'nj':
        $statoteksto = "${ri} estas konfirmita membro de TEJO por " .
            TEJO_MEMBRO_JARO . ".";
        $stato = 'jam';
        akceptada_instrukcio("Nenio plu necesas pri TEJO-membreco.");
        break;
    case 'jn':
        $statoteksto = "${ri} asertis esti membro de TEJO por " .
            TEJO_MEMBRO_JARO . ", sed kontrolo donis kontrau^an rezulton.";

        akceptada_instrukcio("Demandu {$ri}n, c^i $ri volas ig^i TEJO-membro".
                             " por " . TEJO_MEMBRO_JARO . ", pagi " .
                             "la kotizon nun kaj ricevi rabaton.");
        akceptada_instrukcio("Se jes, donu al $ri UEA-membrig^ilon, kiun" .
                             " $ri plenigu. (Tie ankau^ trovig^os {$ri}a" .
                             " kotizo.)");
        akceptada_instrukcio("Elektu sube la g^ustan punkton kaj entajpu la ".
                             " kotizon, poste <em>S^ang^u</em>.");
        $stato = 'igxu';
        break;
    case 'j?':
        $statoteksto = "${ri} asertis esti membro de TEJO por " .
            TEJO_MEMBRO_JARO .
            ", kaj kontrolo ankorau^ ne okazis.";
        akceptada_instrukcio("Demandu {$ri}n, c^i $ri volas ig^i TEJO-membro".
                             " por " . TEJO_MEMBRO_JARO . ", pagi " .
                             "la kotizon nun kaj ricevi rabaton.");
        akceptada_instrukcio("Se jes, donu al $ri UEA-membrig^ilon, kiun" .
                             " $ri plenigu. (Tie ankau^ trovig^os {$ri}a" .
                             " kotizo.)");
        akceptada_instrukcio("Elektu sube la g^ustan punkton kaj entajpu la ".
                             " kotizon, poste <em>S^ang^u</em>.");
        $stato = 'igxu';
        break;
    case 'ji':
    case 'ni':
    $statoteksto = "{$ri} decidis ig^i surloke membro de TEJO por " .
        TEJO_MEMBRO_JARO . " kaj pagis au^ pagos la kotizon de <strong>" .
        $partopreno->datoj['tejo_membro_kotizo'] . " E^</strong>.";
        $stato = 'igxu';
        akceptada_instrukcio("Nenio plu farendas pri TEJO.");
        break;
    case 'nn':
    case 'n?':
        $statoteksto = "${ri} ne estas TEJO-membro por " .
            TEJO_MEMBRO_JARO . ".";
        akceptada_instrukcio("Demandu {$ri}n, c^i $ri volas ig^i TEJO-membro".
                             " por " . TEJO_MEMBRO_JARO . ", pagi " .
                             "la kotizon nun kaj ricevi rabaton.");
        akceptada_instrukcio("Se jes, donu al $ri UEA-membrig^ilon, kiun" .
                             " $ri plenigu. (Tie ankau^ trovig^os {$ri}a" .
                             " kotizo.)");
        akceptada_instrukcio("Elektu sube la g^ustan punkton kaj entajpu la ".
                             " kotizon, poste <em>S^ang^u</em>.");
        $stato = 'ne';
        break;
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


eoecho("<p>Se {$ri} estos membro de TEJO por la jaro " .
       TEJO_MEMBRO_JARO . ", {$ri} ricevos rabaton" .
       " de " . TEJO_RABATO . " E^.\n</p>");

eoecho("<h3>Aktuala stato</h3>\n");
if ($partoprenanto->datoj['naskigxdato'] < TEJO_AGXO_LIMDATO)
	{
        
        eoecho("<p>Lau^ nia kalkulo, {$ri} estas " .
               "<strong>tro ag^a</strong> por ig^i " .
               "TEJO-membro.</p>\n");
	}
eoecho("<p>Lau^ la datumbazo, " . $statoteksto . "</p>\n");

eoecho ("<h3>Nova stato</h3>\n");

echo "<form action='akceptado-tejo.php' method='post'>";

entajpbutono("<p>", 'ago', 'igxu', $stato, 'igxu',
             "{$Ri} ig^as TEJO-membro kaj pagos ");
simpla_entajpejo("la kotizon de ", 'kotizo', $partopreno->datoj['tejo_membro_kotizo'],
                 "10", "", " E^ kun la renkontig^a kotizo.</p>");
entajpbutono("<p>", 'ago', 'jam', $stato, 'jam',
             "{$Ri} jam estas TEJO-membro por " . TEJO_MEMBRO_JARO .
             " kaj povis pruvi tion, do ricevos rabaton sen pagi".
             "  apartan TEJO-kotizon.</p>");
entajpbutono("<p>", 'ago', 'ne', $stato, 'ne',
             "{$Ri} nek estas TEJO-membro nek volas au^ povas ig^i, kaj rezignas" .
             " pri la TEJO-rabato.</p>");

echo ("<p>");
tenukasxe('partoprenidento', $partopreno->datoj['ID']);
send_butono("S^ang^u");
echo "</p></form>";


HtmlFino();

?>
