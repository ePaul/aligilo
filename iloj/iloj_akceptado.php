<?php



  /**
   * kelkaj aferoj, kiuj nur necesas dum la akceptada proceduro.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @since ?
   * @copyright 2007-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

/**
 *
 */


  /*
   * Jen ĉiuj paŝoj de la akceptilo.
   * La ŝlosiloj estas la identigiloj
   * de la paŝoj, la valoroj la nomoj.
   * Ili estu en la ĝusta sinsekvo.
   */

$PASXO_NOMOJ = array(
		'datoj' => "Datumoj",
		'kontroloj' => "Kontroloj",
		'uea' => "UEA-membreco",
		// TODO: laŭ konfiguro decidi, ĉu havi devigan membrecon.
		/*        'lokaasocio' => 'Membreco en '. deviga_membreco_nomo, */
        'cxambro' => 'C^ambroj',
        'pago' => 'Pago',
        'fino' => "Fino",
	);


/**
 * kalkulas, kiuj paŝoj (el la eblaj) estas necesaj, kiuj eblaj
 *
 * $aktuala - la identigilo de la aktuala paŝo.
 */
function kalkulu_necesajn_kaj_eblajn_pasxojn($aktuala, $sekva_eblas=false)
{
    $rez = array();
    $ebla = true;
    $onta = false;
    $lasta = "##";
    $index = 1;
    foreach($GLOBALS['PASXO_NOMOJ'] AS $id => $nomo)
        {
            $nova = array('id' => $id,
                          'nomo' => $nomo,
                          'ebla' => $ebla,
                          'necesa' => false, 
                          'onta' => $onta,
                          'aktuala' => false,
                          'sekva' => false,
                          'index' => $index,
                          );
            if (necesas_pasxo($id))
                {
                    $nova['necesa'] = true;
                    if ($lasta == $aktuala)
                        {
                            // tiu cxi estos la sekva paŝo.
                            $nova['sekva'] = true;
                        }
                    $lasta = $id;
                }
            if($id == $aktuala)
                {
                    $nova['aktuala'] = true;
                    // postaj paŝoj ankoraŭ ne eblas
                    $ebla = false;
                    $nova['ebla'] = false;
                    $onta = true;
                    $lasta = $id;
                }
            $rez [$index]= $nova;
            $index ++;
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
 * eltrovas la sekvan akceptado-paŝon, kaj
 * alligas ĝin (inkluzive de teksto donita,
 * kiel "ĉi tie ne plu necesas fari ion".
 */
function ligu_sekvan($teksto= "C^io en ordo.")
{
    $pasxo_detaloj = sekva_pasxo();
    $pasxo_detaloj['eblas'] = true;
    echo "<!-- ligu_sekvan ... pasxo_detaloj:" .
        var_export($pasxo_detaloj, true) . "-->";

    // igu la sekvajn paŝojn eblaj
    for ($i = 1; $i <= $pasxo_detaloj['index']; $i++)
        $GLOBALS['pasxolisto_detala'][$i]['ebla'] = true;
    akceptada_instrukcio( donu_ligon("akceptado-" . $pasxo_detaloj['id'] .
                                     ".php",
                                     $teksto . " Plu al <em>" .
                                     $pasxo_detaloj['nomo'] . "</em>"));
}


/**
 * kreas listeron (<li>) pri tiu paŝo por la paŝo-navigilo.
 *
 * @param array $pasxo_dato  array() kun informoj pri unu paŝo, kiel kreita
 *  de necesaj_kaj_eblaj_pasxoj.
 *    'id' => identifigilo
 *    'nomo' => nomo
 *    'ebla' => ĉu eblas atingi tiun paŝon nun
 *    'necesa' => ĉu necesas trairi tiun paŝon
 *    'aktuala' => ĉu tiu estas la aktuala paŝo
 *    'sekva'   => ĉu tiu estas la sekva paŝo
 *    'onta'    => ĉu tiu estas unu el la ontaj paŝoj
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
    // nur necesas, se en junulargastejo (en memzorgantejo ne estas ĉambroj).
    // TODO: adaptu, kiam aldoniĝos opcioj por pluraj domotipoj.

    return $_SESSION['partopreno']->datoj['domotipo'] == 'J';
}


function necesas_lokaasocio_traktado()
{
    // ĉu tro simpla?
    return
        $_SESSION['partopreno']->datoj['surloka_membrokotizo'] =='?';
}

function necesas_tejo_traktado()
{
  /*
	// TODO: konfigurebla

	if (TEJO_RABATO == 0)
	{
		// ne estas TEJO-rabato por tiu renkontiĝo,
		// do ne necesas okupiĝi pri TEJO-membrecoj.
		return false;
	}
	$partoprenanto = $_SESSION['partoprenanto'];
	if ($partoprenanto->datoj['naskigxdato'] < TEJO_AGXO_LIMDATO)
	{
		// la partoprenanto estas tro aĝa por iĝi membro
		// de TEJO.
		return false;
	}
  */
	$partopreno = $_SESSION['partopreno'];
	if ($partoprenanto->datoj['tejo_membro_kontrolita'] == 'j')
	{
		// ni jam antaŭe eltrovis, ke la ulo estas TEJO-membro.
		return false;
	}
	return true;
}



/**
 * kolektas punkton por la akceptadaj instrukcioj supre de la dokumento.
 */
function akceptada_instrukcio($teksto)
{
    $GLOBALS['akceptadaj_instrukcioj'][] = $teksto;
}


function akceptado_kesto_fino()
{
    /* TODO */
    if (necesas_pasxo($GLOBALS['aktuala_pasxo']))
        {
    eoecho("<div class='akceptado-instrukcioj'>\n");
        }
    else
        {
            eoecho("<div class='akceptado-instrukcioj nenecesa'>\n");
        }
    if (DEBUG) {
        echo "<!--";
        var_export($GLOBALS['pasxolisto_detala']);
        echo "-->";
    }
    echo "<div class='akceptado-pasxolisto'><ul>\n";
    foreach($GLOBALS['pasxolisto_detala'] AS $ero)
        {
            echo formatu_pasxon($ero) . "\n";
        }
    echo "</ul></div>";

    eoecho("<ul>\n<li>" .
           implode("</li>\n<li>", $GLOBALS['akceptadaj_instrukcioj']) .
           "</li>\n</ul>\n" .
           "</div>");

	$partoprenanto = $_SESSION['partoprenanto'];
    $partopreno = $_SESSION['partopreno'];

    eoecho ("<p>Ni nun akceptas ");
    ligu("partrezultoj.php?partoprenidento=".$partopreno->datoj['ID'], $partoprenanto->tuta_nomo());
    eoecho (" (#".$partoprenanto->datoj['ID']."/#".$partopreno->datoj['ID'].") al la <b>".$_SESSION["renkontigxo"]->datoj['nomo']."</b>.</p>\n");

    eoecho("<h2>Akceptada proceduro &ndash; Pas^o <em>" .
           $GLOBALS['PASXO_NOMOJ'][$GLOBALS['aktuala_pasxo']] .
           "</em></h2>\n");

}


/**
 * metas la HTML-kapon kun ioma informo pri la
 *  stato de la akceptado.
 * $pasxo - la nomo de la aktuala paŝo.
 */
function akceptado_kapo($pasxo)
{
	HtmlKapo();
    kalkulu_necesajn_kaj_eblajn_pasxojn($pasxo);
    $GLOBALS['aktuala_pasxo'] = $pasxo;

    $GLOBALS['akceptadaj_instrukcioj'] = array();

    // listo de antaŭaj kaj postaj paŝoj -> nun en akceptado-fino.
    
    
}


?>