<?php

  /**
   * Kotizokalkulilo en javaskript, por la aliĝilo.
   *
   * La .php-dosiero enhavas parte rekte programitajn
   * JS-funkciojn, kaj parte PHP-generitajn kotizodatumojn.
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2006-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */

  // TODO: Ĉu preni el la ĝenerala konfiguro, aŭ el iu alia mekanismo?
$renkNumero = 8; 


header("Content-Type: text/javascript; charset=UTF-8");
?>
/** kotizo-kalkulado por la 52a IS (en la aliĝilo) */
<?php

/**
 * truko, por ke la lininumeroj en la erarmesaĝoj denove estu ĝustaj.
 *
 * la vokon alĝustigu, kiam vi ŝanĝas ion en la HTML-kodo
 * super la voko.
 */
function aldonu_liniojn($nombro)
{
    echo str_repeat("\n", $nombro);
}

aldonu_liniojn(__LINE__ - 1);
?>


/* ************************************************************************
 * La sekvaj funkcioj estas rekte programitaj, do la lini-numeroj en la
 * kreita dosiero espereble kongruas kun tiuj en la .php-dosiero.
 */


window.onload = function() {
    var elektiloj = document.getElementsByTagName("select");
    for (var e = 0; e < elektiloj.length; e++) {
        elektiloj[e].onchange = rekalkulu_kotizon;
    }
    rekalkulu_kotizon();
};

/**
 * rekalkuligas la kotizon,
 * kaj ĝisdatigas la enhavon.
 */
function rekalkulu_kotizon()
{
    //	alert("rekalkulos kotizon ...");
	var kotizo = kalkulu_kotizon();
    //    alert("kotizo: " + kotizo);
	var kotizocxelo = document.getElementById('kotizokalkulo');
    var kotizokampo = document.getElementById('kotizocifero');
	while(kotizokampo.firstChild) {
		kotizokampo.removeChild(kotizokampo.firstChild);
    }
	if (! kotizo)
	{
		kotizocxelo.className = 'nevidebla';
		return;
	}
	else
	{
		kotizocxelo.className = 'videbla';
		kotizokampo.appendChild(document.createTextNode(kotizo +
									/* ' ' + euro */ " \u20AC"));
	}
}

/**
 * donas la unuan <select>-Elementon kun name=<nomo>.
 * (Kutime estu nur unu tia.)
 */
function donuSelectLauxNomo(nomo)
{
	var listo = document.getElementsByName(nomo);
	for(var i = 0; i < listo.length; i++)
	{
		if (listo[i].tagName.toLowerCase() == 'select')
		{
			return listo[i];
		}
	}
	return null;
}

/**
 * eltrovas, kiom da tagoj partoprenas la ulo.
 */
function eltrovu_partoprentempon()
{
	var de_kampo = donuSelectLauxNomo('de');
	var gxis_kampo = donuSelectLauxNomo('gxis');
	return (Date.parse(gxis_kampo.value) - Date.parse(de_kampo.value))
        / (1000 * 60 * 60 * 24);
}


/**
 * kalkulas la kotizon (por eble parttempa partopreno).
 */
function kalkulu_kotizon()
{
	var partoprentagoj = eltrovu_partoprentempon();
    // alert("partoprentagoj: " + partoprentagoj);
	var baza_kotizo = kalkulu_bazan_kotizon();
	if (!baza_kotizo)
	{
		return null;
	}

    //    alert("baza kotizo: " + baza_kotizo);

	if (partoprentagoj == 0)
	{
        // TODO! - trakto por unutaguloj.
		return 20;
	}
	if (partoprentagoj < 0)
	{
		return "???";
	}

    // TODO: trakto de parttempa partopreno estas ĝeneraligenda.
    //       Ekzemple prenu tiun numeron 6 aŭ 7 el la kotizosistemo.
	if (partoprentagoj < 7)
	{
        // TODO: prenu informojn el kotizositemo.
		// auf halbe Euros runden
		return Math.floor(baza_kotizo/3.0 * partoprentagoj)/2.0;
	}
	else
		return baza_kotizo;
}

/**
 * kreas kaj redonas Date-objekton por la elektita naskiĝdato.
 */
function eltrovu_naskigxdaton()
{
	var jarokampo = donuSelectLauxNomo('naskigxo[jaro]');
	var monatokampo = donuSelectLauxNomo('naskigxo[monato]');
	var tagokampo = donuSelectLauxNomo('naskigxo[tago]');
	if ((jarokampo.value != "-#-#-") &&
        (monatokampo.value != "-#-#-") &&
        (tagokampo.value != "-#-#-"))
	{
//		alert("jaro: " + jarokampo.value + ", monato: " + monatokampo.value +
//			   ", tago: " + tagokampo.value + ".")
		return new Date(jarokampo.value*1, monatokampo.value*1 -1, tagokampo.value*1);
	}
	return null;

}



/**
 * kalkulas la bazan kotizon (por plentempa partopreno),
 * depende de kiun landon, naskiĝdaton kaj loĝvarianton
 * elektis la aliĝonto.
 */
function kalkulu_bazan_kotizon()
{
	var logxkategorio =
        eltrovu_logxkategorion( donuSelectLauxNomo('domotipo').value);
    
    //    alert("loĝkategorio: " + logxkategorio);

    if (!logxkategorio) 
        return null;

	var naskigxdato = eltrovu_naskigxdaton();
    //    alert("naskiĝdato: " + naskigxdato);
	if(!naskigxdato)
		return null;
    var agxoEnMilisekundoj = komencodato.getTime() - naskigxdato.getTime();

	var agxo = agxoEnMilisekundoj / (1000.0 * 60 * 60 * 24 * 365.25);
    var agxoInt = Math.floor(agxo);
    var agxKat =
        eltrovu_agxkategorion(Math.floor(  (komencodato.getTime() -
                                            naskigxdato.getTime())
                                           /
                                           (1000.0 * 60 * 60 * 24 * 365.25)
                                         ));
    //    alert("komencodato: " + komencodato + ",\n naskiĝdato: " + naskigxdato + ",\naĝo En Milisekundoj: " + agxoEnMilisekundoj + ",\naĝo: " + agxo + ",\naĝoInt: " + agxoInt + " aĝKat: " + agxKat);
    if (!agxKat) 
        return null;
    
    
    landokategorio = landokategorioj[donuSelectLauxNomo('lando').value];
    //    alert("landokategorio: " + landokategorio);
    if (!landokategorio)
        return null;
    //    alert("kategorioj: [" + logxkategorio + "][" + agxKat + "][" + landokategorio + "]");
    return kotizoj[logxkategorio][agxKat][landokategorio];

}


/* ********************************************
 * La funkcioj kaj datumoj ekde ĉi tie estas generitaj,
 * depende de la kotizosistemo. Do ĉi tie verŝajne
 * la lini-numeroj ne plu kongruas.
 **/


<?php

require_once($prafix . "/iloj/iloj.php");
malfermu_datumaro();


$renkontigxo = new Renkontigxo($renkNumero);
$kotizosistemo = new Kotizosistemo($renkontigxo->datoj['kotizosistemo']);

// echo "/* ";

$tabelo = $kotizosistemo->kreu_kotizotabelon();
// var_export($tabelo);

// echo "*/";

$aligxKatSistemo = $kotizosistemo->donu_kategorisistemon("aligx");
$katID = $aligxKatSistemo->trovu_kategorion_laux_dato($renkontigxo, date('Y-m-d'));

echo "/* aligxkategorio: $katID */";
echo "/* la tabelo de la kotizoj en tiu aliĝkategorio: */";

$komandoKomenco = "var kotizoj = ";

$formatilo = new JSONKotizoSistemFormatilo();

echo ("\n". $komandoKomenco
      . $formatilo->formatu_liston($tabelo[$katID],
                                   str_repeat(' ', strlen($komandoKomenco)))
      . ";\n");
      

?>
  /* la dato, kiam la renkontiĝo komenciĝos: */

var komencodato = new Date(<?php
                           // Hmm, firefox ne komprenas 
                           //       new Date("2008-12-27").
                           // stulta programo!
                           list($jaro, $monato, $tago) = explode("-", $renkontigxo->datoj['de']);
                           echo $jaro . ", " . ($monato - 1) . ", " . $tago;
                           ?>);


  /**
   * kalkulas la identigilon de la aĝkategorio
   * el la aĝo (en jaroj). (Estas generita funkcio,
   * depende de la uzata kotizosistemo.)
   */
function eltrovu_agxkategorion(agxo)
{
<?php
        
        $rez = sql_faru(datumbazdemando(array('ID', 'limagxo'),
                                        'agxkategorioj',
                                        "sistemoID = '" . 
                                        $kotizosistemo->
                                        datoj['agxkategorisistemo'] . "'",
                                        "",
                                        array("order" => "limagxo ASC")));
    while($linio = mysql_fetch_assoc($rez)) {
        ?>
    if (agxo <= <?php echo $linio['limagxo']; ?>) {
        return <?php echo $linio['ID']; ?>;
    }
<?php
    }
?>
    return null;
}

  /**
   * kalkulas la identigilon de la loĝkategorio
   * el la ŝlosillitero. (Estas generita funkcio,
   * depende de la uzata kotizosistemo.)
   */
function eltrovu_logxkategorion(sxlosillitero) {
    switch(sxlosillitero) {
<?php
        
        $rez = sql_faru(datumbazdemando(array('ID', 'sxlosillitero'),
                                        'logxkategorioj',
                                        "sistemoID = '" . 
                                        $kotizosistemo->
                                        datoj['logxkategorisistemo'] . "'"));
    while($linio = mysql_fetch_assoc($rez)) {
?>
    case "<?php echo $linio['sxlosillitero'] ?>":
        return <?php echo $linio['ID'] ?>;
<?php
      }
?>
    default:
        return null;
    }
}

<?php

$sql = datumbazdemando(array('landoID', 'kategorioID'),
                       'kategorioj_de_landoj',
                       "sistemoID = '" .
                       $kotizosistemo->datoj['landokategorisistemo'] . "'"
                       );
$rez = sql_faru($sql);

$tekstoj = array();
$i = 0;
while($linio = mysql_fetch_assoc($rez)) {
    $i++;
    if ($i % 10 == 0) {
        $prefix = "
                        ";
    }
    else {
        $prefix = "";
    }
    $tekstoj []= $prefix . '"' . $linio['landoID'] . '" : ' . $linio['kategorioID'];
 }
?>
var landokategorioj = { <?php echo implode(', ', $tekstoj ); ?> };


