<?php

/**
 * kelkaj aferoj, kiuj nur necesas dum la akceptada proceduro.
 *
 */


  /*
   * Jen cxiuj pasxoj de la akceptilo.
   * La sxlosiloj estas la identigiloj
   * de la pasxoj, la valoroj la nomoj.
   * Ili estu en la gxusta sinsekvo.
   */

$PASXO_NOMOJ = array(
		'datoj' => "Datumoj",
		'kontroloj' => "Kontroloj",
		'tejo' => "TEJO-membreco",
        'lokaasocio' => 'Membreco en '. deviga_membreco_nomo,
        'cxambro' => 'C^ambroj',
        'pago' => 'Pago',
        'fino' => "Fino",
	);


/**
 * kalkulas, kiuj pasxoj (el la eblaj) estas necesaj, kiuj eblaj
 *
 * $aktuala - la identigilo de la aktuala pasxo.
 */
function kalkulu_necesajn_kaj_eblajn_pasxojn($aktuala)
{
    $rez = array();
    $ebla = true;
    $onta = false;
    $lasta = "##";
    foreach($GLOBALS['PASXO_NOMOJ'] AS $id => $nomo)
        {
            $nova = array('id' => $id,
                          'nomo' => $nomo,
                          'ebla' => $ebla,
                          'necesa' => false, 
                          'onta' => $onta,
                          'aktuala' => false,
                          'sekva' => false,
                          );
            if (necesas_pasxo($id))
                {
                    $nova['necesa'] = true;
                    if ($lasta == $aktuala)
                        {
                            // tiu cxi estos la sekva pasxo.
                            $nova['sekva'] = true;
                            // postaj pasxoj ankoraux ne eblas
                            $ebla = false;
                        }
                    $lasta = $id;
                }
            if($id == $aktuala)
                {
                    $nova['aktuala'] = true;
                    $nova['ebla'] = false;
                    $onta = true;
                    $lasta = $id;
                }
            $rez []= $nova;
        }
    $GLOBALS['pasxolisto_detala'] = $rez;
}

function sekva_pasxo()
{
    $listo = $GLOBALS['pasxolisto_detala'];
    foreach($listo AS $ero)
        {
            if ($ero['sekva'])
                {
                    return $ero;
                }
        }
}

/**
 * eltrovas la sekvan akceptado-pasxon, kaj
 * alligas gxin (inkluzive de teksto donita,
 * kiel "cxi tie ne plu necesas fari ion".
 */
function ligu_sekvan($teksto= "C^io en ordo.")
{
    $pasxo_detaloj = sekva_pasxo();
    ligu("akceptado-" . $pasxo_detaloj['id'] . ".php",
         $teksto . " Plu al <em>" . $pasxo_detaloj['nomo'] . "</em>");
}


/**
 * kreas listeron (<li>) pri tiu pasxo por la pasxo-navigilo.
 *
 * $pasxo_datoj:
 * array() kun informoj pri unu pasxo, kiel kreita
 *  de necesaj_kaj_eblaj_pasxoj.
 *    'id' => identifigilo
 *    'nomo' => nomo
 *    'ebla' => cxu eblas atingi tiun pasxon nun
 *    'necesa' => cxu necesas trairi tiun pasxon
 *    'aktuala' => cxu tiu estas la aktuala pasxo
 *    'sekva'   => cxu tiu estas la sekva pasxo
 *    'onta'    => cxu tiu estas unu el la ontaj pasxoj
 */
function formatu_pasxon($pasxo_datoj)
{
    if (DEBUG) {
        echo "<!-- pasxo-datoj: " . var_export($pasxo_datoj, true) . "-->";
    }
    extract($pasxo_datoj);

    if ($ebla) {
        $ligoteksto = donu_ligon("akceptado-" . $id . ".php", $nomo);
    } else {
        $ligoteksto = eotransform($nomo);
    }

    if ($aktuala) {
        $stilo = 'aktuala';
    } else if ($onta) {
        $stilo = 'onta';
    } else {
        $stilo = 'inta';
    }


    if (!$necesa) {
        $ligoteksto = "(" . $ligoteksto . ")";
    }

    return "<li class='akceptado-pasxo-".$stilo."'>" . $ligoteksto . "</li>";
}


function necesas_pasxo($pasxo)
{
    switch($pasxo)
        {
        case 'lokaasocio':
            return necesas_lokaasocio_traktado();
        case 'tejo':
            return necesas_tejo_traktado();
        case 'cxambro':
            return necesas_cxambro_traktado();
        default:
            return true;
        }
}


function necesas_cxambro_traktado()
{
    // nur necesas, se en junulargastejo (en memzorgantejo ne estas cxambroj).
    // TODO: adaptu, kiam aldonigxos opcioj por pluraj domotipoj.

    return $_SESSION['partopreno']->datoj['domotipo'] == 'J';
}


function necesas_lokaasocio_traktado()
{
    // cxu tro simpla?
    return
        $_SESSION['partopreno']->datoj['surloka_membrokotizo'] =='?';
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
    $partopreno = $_SESSION['partopreno'];
	HtmlKapo();
    kalkulu_necesajn_kaj_eblajn_pasxojn($pasxo);

    // listo de antauxaj kaj postaj pasxoj
    echo "<div class='akceptado-pasxolisto'><ul>\n";
    foreach($GLOBALS['pasxolisto_detala'] AS $ero)
        {
            echo formatu_pasxon($ero) . "\n";
        }
    echo "</ul></div>";

    eoecho ("<p>Ni nun akceptas ");
    ligu("partrezultoj.php", $partoprenanto->tutanomo());
    eoecho (" (#".$partoprenanto->datoj['ID']."/#".$partopreno->datoj['ID'].") al la <b>".$_SESSION["renkontigxo"]->datoj['nomo']."</b>.</p>\n");

    eoecho("<h2>Akceptada proceduro &ndash; Pas^o <em>" .
           $GLOBALS['PASXO_NOMOJ'][$pasxo] .
           "</em></h2>\n");
    
    
}


?>