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

$renkNumero = $GLOBALS['renkontigxoID']; 


header("Content-Type: text/javascript; charset=UTF-8");
?>
/** kotizo-kalkulado por la 65a IJK (en la aliĝilo) */
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
    elektiloj = document.getElementsByTagName("input");
    for (var e = 0; e < elektiloj.length; e++) {
        //        if (elektiloj[e].type == 'radio')
        {
            elektiloj[e].onchange = rekalkulu_kotizon;
        }
    }
    rekalkulu_kotizon();
};

/**
 * rekalkuligas la kotizon,
 * kaj ĝisdatigas la enhavon.
 */
function rekalkulu_kotizon()
{
    //  alert("rekalkulos kotizon ...");
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
                                                        valutoSigno));
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

function donuRadioValoron(nomo)
{
    var listo = document.getElementsByName(nomo);
    for(var i = 0; i < listo.length; i++) {
//          alert("listo[" + i + "]: " + listo[i] +
//                "\n   tagName: " + listo[i].tagName +
//                "\n   type: " + listo[i].type +
//                "\n   checked: " + listo[i].checked);
        if ((listo[i].tagName.toLowerCase() == 'input') &&
            (listo[i].type == 'radio') &&
            listo[i].checked) {
//             alert("listo[" + i + "]: " + listo[i] +
//                   "\n   tagName: " + listo[i].tagName +
//                   "\n   type: " + listo[i].type +
//                   "\n   checked: " + listo[i].checked +
//                   "\n ==> gefunden: " + listo[i].value);
            return listo[i].value;
        }
    }
    return null;
}

function parsu_daton(teksto) {
    var listo = /^(\d{4})-(\d\d)-(\d\d)$/.exec(teksto);
    var d = new Date(listo[1], listo[2]+1, listo[3]);
    return d.getTime();
}


/**
 * eltrovas, kiom da tagoj partoprenas la ulo.
 */
function eltrovu_partoprentempon()
{
    var de_kampo = donuSelectLauxNomo('de');
    var gxis_kampo = donuSelectLauxNomo('gxis');

    var gxis_num = parsu_daton(gxis_kampo.value);
    var de_num = parsu_daton(de_kampo.value);

    // Stulta sinteno de Firefox: Date.parse() ne
    // akceptas la formaton de ISO 8601, sed nur
    // iun strangan usonan formaton.

    return ((gxis_num - de_num)
        / (1000 * 60 * 60 * 24)) ;
}


/**
 * kalkulas la kotizon (por eble parttempa partopreno).
 */
function kalkulu_kotizon()
{
    var partoprennoktoj = eltrovu_partoprentempon();


    //    alert("partoprennoktoj: " + partoprennoktoj);

    if (partoprennoktoj < 0)
    {
        return "???";
    }


    // alert("partoprentagoj: " + partoprentagoj);
    var bazaj_kotizoj = kalkulu_bazan_kotizon(partoprennoktoj);

    if (!bazaj_kotizoj)
    {
        return null;
    }

    //    alert("baza kotizo: " + bazaj_kotizoj.join("/"));

    var tutkotizo = bazaj_kotizoj[0];

    // speciale por IJK 2009: TEJO-rabato estas nur por plentempuloj, por la aliaj gxi jam estas en la kategorioj.
    tutkotizo -= kalkulu_tejorabaton();

    var partkotizo = bazaj_kotizoj[1];

    var kotizo;
    if (partkotizo == null) {
        kotizo = tutkotizo;
    }
    else {
        kotizo = Math.min(tutkotizo, partkotizo);
    }

    //    alert("kotizo vor mangxado: " + kotizo);

    kotizo += kalkulu_mangxadon();

    //    alert("kotizo nach mangxado: " + kotizo);

    kotizo += kalkulu_tranokton(partoprennoktoj);

    //    alert("kotizo nach tranoktado: " + kotizo);

    // TODO: aldonu logxadon kaj mangxadon

    

     return kotizo;
}

/**
 * speciala traktado por IJK.
 * pli gxenerala solvo prenu la informojn el la datumbazo.
 */
function kalkulu_tranokton(partoprennoktoj) {
    var domotipo = donuRadioValoron("domotipo");
    //    alert("domotipo: " + domotipo);
    switch(domotipo) {
    case 'J':
        // TODO: unulita cxambro
        return 230 * partoprennoktoj;
    case 'A':
        return 120 * partoprennoktoj;
    case 'T':
        return 75 * partoprennoktoj;
    case 'M': 
        return 0;
    default:
        return Math.NaN;
    }
}


function kalkulu_mangxadon() {

    var kostoj = [
        {'tipo': "M", 'kosto': 60},
        {'tipo': "T", 'kosto': 90},
        {'tipo': "V", 'kosto': 90}
        ];
    var sumo = 0;

    for (var i = 0; i < kostoj.length; i++) {
        var tipo = kostoj[i].tipo;
        var tabellinio = document.getElementById("mendillinio-" + tipo);
        var inputListo = tabellinio.getElementsByTagName("input");
        for (var j = 0 ; j < inputListo.length; j++) {
            if (inputListo[j].type == 'checkbox' &&
                inputListo[j].checked) {
                sumo += kostoj[i].kosto;
            }
        }
    }

    //    alert("mangxsumo: " + sumo);

    return sumo;
}


/**
 * speciale por IJK ...
 * pli gxenerala traktado elpensenda (kaj la valoroj venu el la datumbazo)
 */
function kalkulu_tejorabaton() {
    var tejo_elektilo = document.getElementById("tejo_membro_laudire=j");
    if(tejo_elektilo.checked) {
        landokategorio = landokategorioj[donuSelectLauxNomo('lando').value];
        switch(landokategorio) {
        case 2: // A 
            return 625;
        case 3: // B 
            return 500;
        case 4: // C 
            return 375;
        }
    }
    return 0;

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
//      alert("jaro: " + jarokampo.value + ", monato: " + monatokampo.value +
//             ", tago: " + tagokampo.value + ".")
        return new Date(jarokampo.value*1, monatokampo.value*1 -1, tagokampo.value*1);
    }
    return null;

}



/**
 * kalkulas la bazan kotizon (por plentempa partopreno),
 * depende de kiun landon, naskiĝdaton kaj loĝvarianton
 * elektis la aliĝonto.
 */
function kalkulu_bazan_kotizon(partoprennoktoj)
{
    // en IJK 2009 la baza kotizo ne plu dependas de la logxkategorio.
    var logxkategorio = 1;
        //        eltrovu_logxkategorion( donuSelectLauxNomo('domotipo').value);
    
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

    var tuttempa = kotizoj[logxkategorio][agxKat][landokategorio];

    var parttempa = null;
//     alert("partkotizoj: " + partkotizoj.join("\n   ") + "\n" +
//           "partoprennoktoj: " + partoprennoktoj + "\n"+
//           "tuttempa: " + tuttempa);
    for (var i = 0; i < partkotizoj.length; i++) {
        obj = partkotizoj[i];
//         alert("obj[noktoj]: " + obj["noktoj"] + "\n" +
//               "obj[kondicxo]: " + obj['kondicxo'] + "\n" +
//               "obj[tabelo]: " + obj['tabelo'] + "\n" +
//               "obj.noktoj: " + obj.noktoj + "\n" +
//               "obj.kondicxo: " + obj.kondicxo + "\n" +
//               "obj.tabelo: " + obj.tabelo + "\n");
        if ((obj['noktoj'] >= partoprennoktoj) &&
            kondicxoj[obj['kondicxo']]()) {
            var pkot = obj['tabelo'][logxkategorio][agxKat][landokategorio];
            //            alert("pkot:
            if ((parttempa == null) ||
                (pkot < parttempa)) {
                parttempa = pkot;
            }
        }
    }
    //    alert("Parttempa: " + parttempa);

    //    parttempa = partkotizoj[partoprentagoj][logxkategorio][agxKat][landokategorio];

    return [ tuttempa, parttempa ];

}



/* ********************************************
 * La funkcioj kaj datumoj ekde ĉi tie estas generitaj,
 * depende de la kotizosistemo. Do ĉi tie verŝajne
 * la lini-numeroj ne plu kongruas.
 **/


<?php

require_once($prafix . "/iloj/iloj.php");
malfermu_datumaro();


echo "var valutoSigno = ' " . CXEFA_VALUTO . "';\n";

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
      

echo "var kondicxoj = [];\n";

$kondicxolisto = array();

$sql = datumbazdemando(array('k.ID' => 'ID', 'jxavaskripta_formo'),
                       array('parttempkotizosistemoj' => 'p',
                             'kondicxoj' => 'k'),
                       array('baza_kotizosistemo' =>
                             $kotizosistemo->datoj['ID'],
                             "p.kondicxo = k.ID"),
                       "",
                       array('group' => 'kondicxo'));
$rez = sql_faru($sql);
while ($linio = mysql_fetch_assoc($rez)) {
    echo
        "kondicxoj[" . $linio['ID'] . "] =\n   function() {\n      " .
        str_replace("\n", "\n      ",
                    $linio['jxavaskripta_formo']) .
        "\n   };\n";
 }


foreach($kondicxolisto AS $index => $kodo) {
    echo
        "kondicxoj[" . $linio['ID'] . "] =\n   function() {\n      " .
        str_replace("\n", "\n      ",
                    $linio['jxavaskripta_formo']) .
        "\n   };\n";

}




$sql = datumbazdemando('ID',
                       'parttempkotizosistemoj',
                       array('baza_kotizosistemo' => $kotizosistemo->datoj['ID']));
$rez = sql_faru($sql);

echo "var partkotizoj =\n   [";
$unua = true;
while ($linio = mysql_fetch_assoc($rez)) {
    if (!$unua) {
        echo ",\n";
    } else {
        echo "\n";
        $unua = false;
    }
    $ptksis = new Parttempkotizosistemo($linio['ID']);
    echo ("     {\n");
    echo ('        "noktoj"   : ' . $ptksis->datoj['por_noktoj'] . ",\n");
    echo ('        "kondicxo" : ' . $ptksis->datoj['kondicxo'] . ",\n");
    $komandoKomenco = '        "tabelo"   : ';
    $tabelo = $ptksis->kreu_kotizotabelon();
    //    echo "<!-- +++++++ \n" . var_export($tabelo, true) . " +++++++ \n-->";
    $aligxKatSistemo = $ptksis->donu_kategorisistemon("aligx");
    $katID = $aligxKatSistemo->trovu_kategorion_laux_dato($renkontigxo, date('Y-m-d'));
    
    echo "/* aligxkategorio: $katID */\n";
    echo "/* la tabelo de la kotizoj en tiu aliĝkategorio: */\n";
    echo ( $komandoKomenco .
           $formatilo->formatu_liston($tabelo[$katID],
                                      str_repeat(' ', strlen($komandoKomenco)))
           . "\n");
    echo ("     }");
 }
if ($unua) {
    echo "]";
 } 
 else {
     echo "\n   ];\n";
 }


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


