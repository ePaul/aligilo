<?php
  // ĉĝĥĵŝŭ

/*
 * Akceptado de partoprenantoj
 *
 *  Paŝo 6: Pago
 *
 * TODO!: pretigi, elprovi
 */

require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

kontrolu_rajton("akcepti");
require_once('iloj/iloj_akceptado.php');

sesio_aktualigu_laux_get();

$partoprenanto = $_SESSION["partoprenanto"];
$partopreno = $_SESSION['partopreno'];

// la persona pronomo (li aux sxi)
$ri = $partoprenanto->personapronomo;
$Ri = ucfirst($ri);



if ($_POST['sendu'] == 'pagas') {
    $pago = new Pago();
    $pago->kreu();
	$pago->datoj['valuto'] = $_POST['valuto'];
    $pago->datoj['partoprenoID'] = $partopreno->datoj['ID'];
    $pago->datoj['kvanto'] = $_POST['pago'];
    $pago->datoj['tipo'] = 'surlokpago';
	 $pago->datoj['dato'] = date('Y-m-d');
    $pago->skribu();

    //// TODO: pripensu novan uzon de Monujo.
    //         $mono = new Monujo();
    //         $mono->kreu();
    //         // TODO
    //         $mono->skribu();

        
 }
 else if ($_POST['sendu'] == 'donacu' or $_POST['sendu'] == 'repagu') {

    $pago = new Pago();
    $pago->kreu();
	$pago->datoj['valuto'] = $_POST['valuto'];
    $pago->datoj['partoprenoID'] = $partopreno->datoj['ID'];
    $pago->datoj['kvanto'] = - $_POST['malpago'];
	 $pago->datoj['dato'] = date('Y-m-d');
    $pago->datoj['tipo'] =
        ($_POST['sendu'] == 'donacu' ? 'donaco' : 'repago');
    $pago->skribu();

    // TODO: monujo (nur cxe repago)
     
 }
 else {
     // ni nun unuan fojon alvenis ...
     $ne_pluiru = true;
 }

$kot = new Kotizokalkulilo($partoprenanto, $partopreno,
                  $_SESSION['renkontigxo']);
$restas = $kot->restas_pagenda();

if ($restas == 0.0 and !$ne_pluiru) {
    kalkulu_necesajn_kaj_eblajn_pasxojn('pago');
    $pasxo = sekva_pasxo();
    http_redirect('akceptado-'.$pasxo['id'].'.php', null,
                  false, 303);
    exit();
 }

$informoj = $kot->restas_pagenda_en_valutoj();
if (DEBUG) {
  echo "<pre>";
  var_export($informoj);
  echo "</pre>";
}

akceptado_kapo("pago");

akceptada_instrukcio("Komparu la kalkulon kun tiu sur la akceptofolio. ".
                     "Se necesas, s^ang^u la akceptofolion. Se io estas".
                     " neklara, voku la c^efadministranton.");

if ($informoj['ni_fajfas']) {
  akceptada_instrukcio("$Ri devus ankorau^ pagi " .
					   $informoj[$pagenda_cxef] . " " .CXEFA_VALUTO .
					   ", sed tio estas tiom malmulte, ke ni fajfas pri tio.");

  ligu_sekvan("Plu al la fino!");
} else if($informoj['pagenda_cxef'] == 0) {
  akceptada_instrukcio("$Ri pagis precize sian tutan kotizon.");

  ligu_sekvan("Plu al la fino!");
}
else if($informoj['repagenda']) {
  

     akceptada_instrukcio("$Ri jam <strong>pagis pli</strong> ol sian tutan".
                          " kotizon. Demandu {$ri}n, c^u $ri volas".
                          " donaci la kromaj^on " .
                          " CZK, au^ rehavi g^in (au^ poste decidi).</li>");
     akceptada_instrukcio("Entajpu la donacon au^ repagon sube, notu g^in en".
                          " la akceptofolio kaj uzu la respektivan butonon.".
                          " (Se $ri volas parte repagigi kaj parte donaci, ".
                          " entajpu unu post la alia.)");
     akceptada_instrukcio("(En kazo de <em>repago</em>, kompreneble donu al".
                          "  $ri la monon.)");
     ligu_sekvan("Ne, $ri volas poste decidi, kion fari per la mono, kaj" .
                 " venos tiam al la oficejo.");
     
}
else {
     akceptada_instrukcio("Kolektu pagon de $ri. Se estas malpli ol la" . 
						  " menciita sumo, prenu garantiaj^on de $ri" .
						  " kaj metu g^in kun noto-slipeto en la kason. Au^ ".
						  " simple sendu {$ri}n nun al la banko por reveni " .
						  " poste, kaj dume traktu alian partoprenanton.");
	 akceptada_instrukcio("Enmetu la pagon sube en la ĝustan kampon, kaj" .
						  " ankau^ notu g^in en la akceptofolio (kun la" .
						  " valuto).");
    akceptada_instrukcio("Premu la butonon <em>Enmetu pagon</em>.");

    ligu_sekvan("Mi prenis garantiaj^on kaj akceptos ${ri}n sen ".
                "kompleta pago.");

 }



akceptado_kesto_fino();



// #########################################################################




eoecho("<h3>Kotizokalkulado:</h3>\n");

$kot->tabelu_kotizon(new HTMLKotizoFormatilo());

eoecho("<h3>Pagado</h3>");

if ($informoj['traktenda']) {
echo "<div class='pagu-formularoj'>\n";
if ($informoj['repagenda']) {
  echo "<form action='akceptado-pago.php' method='POST'>\n";
  tenukasxe('valuto', CXEFA_VALUTO);
  eoecho("<h4>Donaco al la IJK-kaso</h4>");
  simpla_entajpejo("<p>$Ri donacas: ", 'repago', $informoj['pagenda_cxef'],
				   10, "", CXEFA_VALUTO.". "); 
  butono('donacu', "Enmetu donacon");
  
  echo "</p>\n</form>\n";
}

foreach($informoj['listo'] AS $listero) { 
  echo "<form action='akceptado-pago.php' method='POST'>\n";
  tenukasxe('valuto', $listero['valuto']);
  if ($informoj['repagenda']) {
	eoecho("<h4>Repago en " . $listero['valutoteksto'] ."</h4>");
	if ($listero['valuto'] == CXEFA_VALUTO) {
	  if ($listero['pagenda']==$listero['vere_pagenda']) {
		eoecho("<p>Lau^ la supra kalkulo, $ri pagis <strong>" .
			   (-$listero['pagenda']) . "&nbsp;" . $listero['valuto'] .
			   "</strong> tro, kaj povas rericevi nun.</p>");
	  } else {
		eoecho("<p>Lau^ la supra kalkulo, $ri pagis " .
			   (-$listero['pagenda']) . "&nbsp;" . $listero['valuto'] .
			   " tro. </p>\n" . 
			   "<p> Por simpligi, ni povas redoni <strong>" .
			   (-$listero['vere_pagenda']) . "&nbsp;" . $listero['valuto'] .
			   "</strong>.</p>");
	  }
	  
	}
	else {
	  eoecho("<p>Ni uzas la kurzon de " . $listero['kurzo']. " ".
			 CXEFA_VALUTO . "/" . $listero['valuto'] .
			 " (" . $listero['kurzo-dato'] . "). <br/>\n");
	  
	  if ($listero['pagenda']==$listero['vere_pagenda']) {
		eoecho("lau^ tio, $ri pagis <strong>" .
			   (-$listero['pagenda']) . "&nbsp;" . $listero['valuto'] .
			   "</strong> tro, kaj povas rericevi nun.</p>");
	  } else {
		eoecho("Lau^ tio, $ri pagis " .
			   (-$listero['pagenda']) . "&nbsp;" . $listero['valuto'] .
			   " tro.</p>\n".
			   "<p>Por simpligi, ni povas redoni <strong>" .
			   (-$listero['vere_pagenda']) . "&nbsp;" . $listero['valuto'] .
			   "</strong>.</p>");
	  
	  }
	}
	  simpla_entajpejo("<p>$Ri ricevas: ", 'repago', "", 10, "", $listero['valuto'].". "); 
	  butono("repagas", "Enmetu repagon");
	  echo "</p>\n";
  }
  else {
	// pagenda
	eoecho("<h4>Pago en " . $listero['valutoteksto'] ."</h4>");
	if ($listero['valuto'] == CXEFA_VALUTO) {

	  if ($listero['pagenda'] == $listero['vere_pagenda']) {
		eoecho("<p>Lau^ la supra kalkulo, restas pagenda <strong>" .
			   $listero['pagenda'] . "&nbsp;" . $listero['valuto'] .
			   "</strong></p>");
	  }
	  else {
		eoecho("<p>Lau^ la supra kalkulo, restas pagenda " .
			   $listero['pagenda'] . "&nbsp;" . $listero['valuto'] . "</p>");

		eoecho("<p>Sed por simpligi, ni nur volas <strong>" .
			   $listero['vere_pagenda'] . "&nbsp;" . $listero['valuto'] .
			   "</strong>.</p>");
	  }

	}
	else { // ne-cxefa valuto
	  eoecho("<p>Ni uzas la kurzon de " . $listero['kurzo']. " ".
			 CXEFA_VALUTO . "/" . $listero['valuto'] .
			 " (" . $listero['kurzo-dato'] . "). <br/>\n");
	  if ($listero['pagenda'] == $listero['vere_pagenda']) {
		eoecho("Lau^ tio, restas pagenda <strong>" . $listero['vere_pagenda'] .
			   "&nbsp;" . $listero['valuto'] . "</strong></p>");
	  }
	  else {
		eoecho( "Lau^ tio, restas pagenda " . $listero['pagenda'] . " " .
				$listero['valuto'] . ".</p>");
		eoecho("<p>Sed por simpligi, ni nur volas <strong>" .
			   $listero['vere_pagenda'] . "&nbsp;" . $listero['valuto'] .
			   "</strong>.</p>");
	  }
	} 	// ne-cxefa valuto
	
	simpla_entajpejo("<p>$Ri pagas: ", 'pago', "", 10, "", $listero['valuto'].". "); 
	butono('pagas', "Enmetu pagon");
	echo "</p>";
  } // pagenda
	echo "</form>\n";
  }
  echo "</div>\n";
}


HtmlFino();

