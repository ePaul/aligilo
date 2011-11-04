<?php

  /**
   * kreado kaj redaktado de kotizosistemo.
   *
   * @package aligilo
   * @subpackage pagxoj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2007-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */
define("DEBUG", true);

  /**
   */


require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');


  session_start();
  malfermu_datumaro();


// TODO: pripensu pli bonan rajton
kontrolu_rajton("vidi");


/**
 * sxangxas la bazajn datumojn de kotizosistemo.
 */
function novaj_kategorioj() {
         if (DEBUG) {
         echo "<!-- POST:";
         var_export($_POST);
         echo "-->";
     }

     // TODO: kontrolu rajton: nur posedanto aux
     // administrantoj povu redakti la kotizosistemon.

     $sistemo = new Kotizosistemo($_REQUEST['id']);
     $sistemo->kopiu();
     $sistemo->datoj['entajpanto'] = $_SESSION['kkren']['entajpanto'];
     $sistemo->skribu();
}

/**
 * kreas novan kotizo-sistemon el la datumoj entajpitaj.
 */
function kopiu_sistemon()
{
     $malnova_sistemo = new Kotizosistemo($_REQUEST['id']);

     $nova_sistemo = new Kotizosistemo();
     $nova_sistemo->kopiu();
     if ($nova_sistemo->datoj['nomo'] == $malnova_sistemo->datoj['nomo'])
         {
             erareldono("Ne povas esti du sistemoj kun sama nomo!");
         }
     else{
         $nova_sistemo->skribu_kreante();
         // TODO: kopiu kotizotabelon, krompagojn/rabatojn kaj
         // antauxpagojn, se eblas.
         
         // poste montru la novan sistemon
         $_REQUEST['id'] = $nova_sistemo->datoj['ID'];
     }

}  // kopiu_sistemon


/**
 * montras la bazajn datumojn de iu kotizosistemo.
 *
 * kun du butonoj, por redakti aux krei novan.
 */
function redaktilo_por_bazaj_datumoj($sistemo)
{
    eoecho("<h2>Bazaj datumoj</h2>\n");



    echo "<form action='kotizosistemo.php' method='POST'>\n";

    eoecho ("<table>\n<tr><th>ID</tr><td>" . $sistemo->datoj['ID']);
    tenukasxe('id', $sistemo->datoj['ID']);
    echo("</td></tr>\n");

    $entajpanto = eltrovu_entajpanton($sistemo->datoj['entajpanto']);
    eoecho ("<tr><th>entajpanto</tr><td>" . $entajpanto . "</td></tr>\n");


    tabelentajpejo("nomo", 'nomo', $sistemo->datoj['nomo'], 30);
    granda_tabelentajpejo("priskribo", 'priskribo',
                          $sistemo->datoj['priskribo'],
                          40, 4);


    foreach($GLOBALS['kategoriotipoj'] AS $tipo) {
        $katsis = $sistemo->donu_kategorisistemon($tipo);
    
        eoecho("<tr><th>" . $katsis->donu_eoklasnomon() ."</th><td>\n");
        elektilo_simpla_db(donu_katsisnomon($tipo),
                           donu_katsisnomon($tipo) . "j",
                           "nomo", "ID", $katsis->datoj['ID']);
        echo("</td></tr>\n");
    }

    // // TODO: eble elpensu pli gxeneralan sistemon
    // tabelentajpejo("Malfaktoro por parttempaj kotizoj",
    //                'parttempdivisoro', 
    //                $sistemo->datoj['parttempdivisoro'],
    //                5);

    tabela_elektilo_db("Malalig^kondic^o-sistemo",
                       'malaligxkondicxsistemo',
                       'malaligxkondicxsistemoj',
                       'nomo', 'ID',
                       $sistemo->datoj['malaligxkondicxsistemo'],
                       '',
                       "Elektu sistemon por trakti malalig^intojn.");

    eoecho ("</table>\n<p>");

    // TODO: opcioj por kopii ekzistantajn kotizo-valorojn
    butono('novaj_kategorioj', "S^ang^u");
    butono("kopiu" , "Kopiu"); eoecho ("(tiam nepre necesas nova nomo)");
    echo "</p></form>\n";

    echo "<hr/>\n"; // -----------------------------------------------------
}


/**
 * sxangxas la datumojn de la ekzistantaj parttempo-sistemoj laux
 * la entajpitajxoj.
 */
function sxangxu_parttempsistemojn()
{
    foreach($_POST['parttempa'] AS $id => $informoj)
        {
            $ptksis = new Parttempkotizosistemo($id);
            $ptksis->kopiu($informoj);
            // TODO: kontrolu
            $ptksis->skribu();
        }
}


function nova_parttempsistemo()
{
    $ptksis = new Parttempkotizosistemo();
    $ptksis->kopiu();
    // TODO: kontrolu
    $ptksis->skribu_kreante();
}


/**
 * montras redaktilon por la parttempajxoj.
 */
function redaktilo_por_parttempsistemoj($sistemo)
{
    eoecho("<h2>Traktado de parttempuloj</h2>");

    echo "<form action='kotizosistemo.php?id=" . $sistemo->datoj['ID'] .
        "' method='POST'>\n";

    $sql =
        datumbazdemando('ID',
                        'parttempkotizosistemoj',
                        array('baza_kotizosistemo' => $sistemo->datoj['ID']),
                        "",
                        array("order" => "por_noktoj ASC"));

    $rez = sql_faru($sql);

    echo "<table>\n";
    eoecho("<tr><th>ID</th><th>noktoj</th><th>faktoro</th><th>kot.-sistemo</th><th>kondic^o</th></tr>\n");

    while($linio = mysql_fetch_assoc($rez)) {
        $id = $linio['ID'];
        $partsistemo = new Parttempkotizosistemo($id);
        echo("<tr><td>" . $id . "</td><td>");
        simpla_entajpejo("", 'parttempa[' . $id . '][por_noktoj]',
                         $partsistemo->datoj['por_noktoj'],
                         4);
        echo ("</td><td>");
        simpla_entajpejo("", 'parttempa[' . $id . '][faktoro]',
                         $partsistemo->datoj['faktoro'],
                         4);

        echo ("</td><td>");
        elektilo_simpla_db('parttempa[' . $id . '][sub_kotizosistemo]',
                           'kotizosistemoj', 'nomo', 'ID',
                           $partsistemo->datoj['sub_kotizosistemo']);
        echo ("</td><td>");
        simpla_kondicxoelektilo('parttempa[' . $id . '][kondicxo]',
                                $partsistemo->datoj['kondicxo']);
        echo ("</td></tr>\n");
    } // while
    echo ("</table>");


    butono("sxangxu_parttempsistemojn", "S^ang^u");

    echo "</form>\n";

    eoecho("<h3>Nova parttempsistemo</h3>");

    echo "<form action='kotizosistemo.php?id=" . $sistemo->datoj['ID'] .
        "' method='POST'>\n";

    echo "<table>";
    tabela_kasxilo("en sistemo", 'baza_kotizosistemo',
                   $sistemo->datoj['ID']);
    tabelentajpejo("noktoj", 'por_noktoj', "", 4,
                   "tiom da noktoj oni rajtas resti en tiu tarifo");
    tabelentajpejo("faktoro", 'faktoro', "1", 6,
                   "ni obligas la kotizojn de la elektita sistemo" .
                   " per tiu faktoro.");
    tabela_elektilo_db("uzata kotizosistemo",
                       'sub_kotizosistemo',
                       'kotizosistemoj', "nomo", "ID",
                       "", "",
                       "la kotizoj de tiu sistemo estos uzataj.");
    tabela_kondicxoelektilo("Tiu kondic^o aldone devas esti plenumita",
                            7 // 7 = cxiuj
                            );
    echo "</table>";

    butono("nova_parttempsistemo", "Nova");


    echo "</form>\n";
    

    // TODO
    echo "<hr/>\n"; // -----------------------------------------------------
}

        
/**
 * sxangxas la kotizojn laux la entajpitajxoj.
 *
 */
function novaj_kotizoj()
{
     if (DEBUG) {
         echo "<!--";
         var_export($_POST['kotizo']);
         echo "-->";
     }
     $katnomoj = array();
     foreach($GLOBALS['kategoriotipoj'] AS $tipo) {
         $katnomoj[]= $tipo.'kategorio';
     }

     // sxangxu kotizojn
     foreach($_POST['kotizo'] AS $nomo => $kotizero) {
         if (DEBUG) {
             echo "<!-- nomo: " . $nomo . ", kotizero: " . $kotizero . " -->";
         }
         if ($kotizero !== "") {
             $idoj = dekodu_kategoriojn($nomo, $katnomoj);
             $idoj['kotizosistemo'] = $_REQUEST['id'];

             $rez = sql_faru(datumbazdemando("kotizo",
                                             "kotizotabeleroj",
                                             $idoj));
             if ($linio = mysql_fetch_assoc($rez)) {
                 if ($linio['kotizo'] != $kotizero) {
                     if (DEBUG) {
                         echo "<!-- sxangxas ...  -->";
                     }
                     sxangxu_datumbazon("kotizotabeleroj",
                                        array("kotizo" => $kotizero),
                                        $idoj);
                 }
                 else {
                     if (DEBUG) {
                         echo "<!-- ne necesas sxangxo ... -->";
                     }
                 }
             }
             else {
                 if (DEBUG) {
                     "<!-- aldono ... -->";
                 }
                 $idoj['kotizo'] = $kotizero;
                 aldonu_al_datumbazo("kotizotabeleroj",
                                     $idoj);
             }
             
         } // if $kotizero
     } // foreach

}  // novaj_kotizoj


/**
 * montras formularon por enmeti aux sxangxi multajn kotizojn ene
 * de la diversaj kotizo-kategorioj.
 */
function redaktilo_por_unuopaj_kotizoj($sistemo) {

    eoecho("<h2>Kotizoj en la kategorioj</h2>");

    echo "<form action='kotizosistemo.php' method='POST'>\n";

    tenukasxe('id', $sistemo->datoj['ID']);

    $sistemo->metu_kotizotabelon("entajpa_kotizocxelo");

    echo "<p>";
    butono("novaj_kotizoj", "S^ang^u la kotizojn");
    echo "</p>\n</form>\n";


    echo "<hr/>\n"; // --------------------------------------------------------
}



/**
 * aldonas aux sxangxas minimumajn antauxpagojn laux la entajpitajxo.
 */
function novaj_antauxpagoj()
{
     if (DEBUG) {
         echo "<!-- REQUEST: ";
         var_export($_REQUEST);
         echo "-->";
     }

     foreach($_REQUEST['antauxpago'] AS $lando => $pagoj) {
         $sql = datumbazdemando(array('oficiala_antauxpago',
                                      'interna_antauxpago'),
                                'minimumaj_antauxpagoj',
                                array("kotizosistemo = '".$_REQUEST['id']."'",
                                      "landokategorio = '" . $lando . "'"));
         if ($linio = mysql_fetch_assoc(sql_faru($sql))) {
             if (count(array_diff_assoc($pagoj, $linio)) > 0) {
                 // iu sxangxo
                 sxangxu_datumbazon("minimumaj_antauxpagoj",
                                    $pagoj,
                                    array("kotizosistemo" => $_REQUEST['id'],
                                          'landokategorio' => $lando));
             }
             else {
                 // neniu sxangxo (aux mankanta valoro?)
             }
         }
         else {
             // ankoraux ne ekzistas -> aldonu
             aldonu_al_datumbazo("minimumaj_antauxpagoj",
                                 array_merge($pagoj,
                                             array("kotizosistemo" => $_REQUEST['id'],
                                                   'landokategorio' => $lando))
                                 );
         }
         
     }

}  // novaj_antauxpagoj



/**
 * montras redaktilon por enmeti/sxangxi la minimumajn
 * antauxpagojn por la unuopaj landokategorioj.
 */
function redaktilo_por_minimumaj_antauxpagoj($sistemo) {
    eoecho("<h2>Minimumaj antau^pagoj</h2>\n");

    echo "<form action='kotizosistemo.php' method='POST'>\n";
    tenukasxe('id', $sistemo->datoj['ID']);

    echo "<table>\n";
    eoecho("<tr><th>Landokategorio</th><th>minimuma antau^pago (oficiale)</th><th>minimuma antau^pago (interne)</th></tr>");

    $sql = datumbazdemando(array("nomo", "ID"),
                           "landokategorioj",
                           "sistemoID = '" .
                           $sistemo->datoj['landokategorisistemo'] . "'");
    $rez = sql_faru($sql);

    while($linio = mysql_fetch_assoc($rez)) {
        eoecho("<tr><td>" . $linio['nomo'] . "</td><td>");
        $sql2 = datumbazdemando(array('oficiala_antauxpago', 'interna_antauxpago'),
                                'minimumaj_antauxpagoj',
                                array("kotizosistemo = '" .
                                      $sistemo->datoj['ID'] ."'",
                                      "landokategorio = '" . $linio['ID'] . "'"));
        $antaux = mysql_fetch_assoc(sql_faru($sql2));
        simpla_entajpejo("", 'antauxpago['.$linio['ID'].'][oficiala_antauxpago]',
                         $antaux['oficiala_antauxpago'], 5);
        echo "</td><td>";
        simpla_entajpejo("", 'antauxpago['.$linio['ID'].'][interna_antauxpago]',
                         $antaux['interna_antauxpago'], 5);
        echo "</td></tr>\n";
    }



    echo "</table>\n<p>";
    butono("novaj_antauxpagoj", "S^ang^u la antau^pagojn");
    echo "</p>\n</form>\n";

    echo "<hr/>\n"; // --------------------------------------------------------
}




/**
 * aldonas novan krompagon al la datumbazo, laux la senditajxoj.
 */
function nova_pago($tipo)
{
    $regpago = donu_regulan_pseuxdopagon($tipo, 0);
    $regpago->kopiu();
    $regpago->datoj['kotizosistemo'] = $_REQUEST['id'];
    $regpago->skribu_kreante();

    eoecho("<p>Aldonis novan " . $tipo . "n #" . $regpago->datoj['ID'] .
           ".</p>\n");
}


/**
 * sxangxas la ekzistantajn (regulajn) krompagojn
 * laux la entajpitajxoj.
 */
function sxangxu_pagojn($tipo)
{
    foreach($_POST[$tipo] AS $id => $informoj) {
        $regpago = donu_regulan_pseuxdopagon($tipo, $id);
        if ($regpago->sxangxus_ion($informoj)) {
            $regpago->kopiu($informoj);
            $regpago->skribu();
            eoecho( "<p>S^ang^is la regulan " . $tipo . "n #" .
                    $regpago->datoj['ID'] . ".</p>");
        }
    }

}

function redaktilo_por_regulaj_pseuxdopagoj($sistemo, $tipo)
{
    eoecho("<h2 id='regulaj_". $tipo."j'>Regulaj " . $tipo . "j</h2>\n");
    echo("<form action='kotizosistemo.php?id=" . $sistemo->datoj['ID'] .
         "&tipo=". $tipo."' method='POST'>\n");

    echo("<table class='pseuxdopagoj'>\n");
    eoecho("  <tr><th>ID</th><th>tipo</th><th>kvanto</th><th>valuto</th>"
           .     "<th>priskribo</th></tr>\n");
    
    $regulolisto = listu_cxiujn_regulojn($tipo);
    $neuzitaj = array();

    foreach($regulolisto AS $regulo) {
        $pseuxdopago = $regulo->donu_regulan_pseuxdopagon($sistemo);
        if ($pseuxdopago) {
            eoecho("<tr><td>" .$pseuxdopago->datoj['ID'] .
                   "</td><td>" . $regulo->formatu_nomon());
            simpla_entajpejo( "</td><td>",
                             $tipo.'['.$pseuxdopago->datoj['ID'].'][kvanto]',
                              $pseuxdopago->datoj['kvanto'], 6);
            echo "</td><td>";
            simpla_elektolisto_el_konfiguroj($tipo.'['.
                                             $pseuxdopago->datoj['ID'].
                                             '][valuto]',
                                             'valuto',
                                             $pseuxdopago->datoj['valuto']);
            eoecho("</td><td>" . $regulo->datoj['priskribo'] . "</td></tr>\n");
        }
        else {
            $neuzitaj[]= $regulo;
        }
    }  // foreach

    echo "</table>\n<p>";
    butono("sxangxu_pagojn", "S^ang^u " . $tipo. "jn");
    echo "</p>\n</form>";

    if (count($neuzitaj)) {

        eoecho("<h3 id='regulaj_". $tipo."j'>Nova " . $tipo . "</h3>\n");

        echo("<form action='kotizosistemo.php?id=" . $sistemo->datoj['ID'] .
             "&tipo=". $tipo."' method='POST'>\n");

        echo "<table>\n";
        eoecho("<tr><th>regulo</th></tr>\n");
        foreach($neuzitaj AS $regulo) {
            tabel_entajpbutono('',
                               'regulo', "",
                               $regulo->datoj['ID'],
                               $regulo->formatu_nomon() . " – " .
                               $regulo->datoj['priskribo'],
                               "", true);
        }
        tabelentajpejo("kvanto", 'kvanto', "", 6);
        tabela_elektolisto_el_konfiguroj("valuto", 'valuto', 'valuto',
                                         "");
        echo "</table>\n<p>";
        butono("nova_pago", "Aldonu!");
        rajtligu('regulo.php?tipo=' . $tipo , "Nova " . $tipo . "regulo",
                 '', 'teknikumi');
        echo "<p></form>\n";

    } // if


}


// /**
//  * montras redaktilon por enmeti/sxangxi regulajn krompagojn.
//  */
// function redaktilo_por_krompagoj($sistemo) {

//     eoecho("<h2>regulaj Krompagoj</h2>");

//     $neuzitaj = array();

//     echo("<form action='kotizosistemo.php' method='POST'>\n");

//     tenukasxe('id', $sistemo->datoj['ID']);

//     eoecho("<table class='krompagotabelo'>\n".
//            "<tr><th>tipo</th><th>krompago</th><th>priskribo</th></tr>");

//     $tipolisto = listu_cxiujn_krompagotipojn();


//     foreach($tipolisto AS $kromtipo) {
//         $sql = datumbazdemando("krompago",
//                                "krompagoj",
//                                array("kotizosistemo = '".$sistemo->datoj['ID']."'",
//                                      "tipo = '" . $kromtipo->datoj['ID']."'"));
//         $linio = mysql_fetch_assoc(sql_faru($sql));
//         if ($linio) {
//             tabelentajpejo(formatu_krompagotipon($kromtipo),
//                            "krompago[" . $kromtipo->datoj['ID']."]",
//                            $linio['krompago'],
//                            5,
//                            "</td><td>" . $kromtipo->datoj['priskribo'] );
        
//         }
//         else {
//             $neuzitaj[] = $kromtipo;
//         }
//     }
//     echo("</table>\n<p>");

//     butono("sxangxu_krompagojn", "S^ang^u krompagojn");

//     echo "</p></form>\n";


//     if (count($neuzitaj)) {
//         echo "<hr/>\n";  // ------------------------------------------------

//         eoecho("<h2>Nova krompago</h2>");


//         echo("<form action='kotizosistemo.php' method='POST'>\n");

//         tenukasxe('id', $sistemo->datoj['ID']);

//         eoecho ("<table>\n<tr><th>tipo</th><td/><th>priskribo</th></tr>\n");


//         foreach($neuzitaj AS $kromtipo) {
//             tabel_entajpbutono(formatu_krompagotipon($kromtipo),
//                                'tipo', "",
//                                $kromtipo->datoj['ID'],
//                                $kromtipo->datoj['priskribo']);
//         }

//         echo("</table>");


//         simpla_entajpejo("<p>krompago: ", 'krompago', "", 6, "", " ");
//         butono("nova_krompago", "Aldonu!");
//         echo "</p></form>\n";

//     }

//     if (rajtas("teknikumi")) {
//         ligu("krompagotipo.php", "Nova krompagotipo");
//     }

//     echo "<hr/>\n";  // ----------------------------------------------------
// }



HtmlKapo();


switch($_REQUEST['sendu']) {
 case '':
     break;

 case  'sxangxu_pagojn':
     sxangxu_pagojn($_REQUEST['tipo']);
     break;  // sxangxu_krompagojn

 case 'nova_pago':
     nova_pago($_REQUEST['tipo']);
     break;  // nova_krompago


 case 'novaj_kategorioj':
     novaj_kategorioj();
     break; // novaj_kategorioj


 case 'sxangxu_parttempsistemojn':
     sxangxu_parttempsistemojn();
     break;

 case 'nova_parttempsistemo':
     nova_parttempsistemo();
     break;
 
 case 'novaj_kotizoj':
     
     novaj_kotizoj();

     break; // novaj_kotizoj


 case 'kopiu':
     kopiu_sistemon();

     break; // kopiu


 case 'novaj_antauxpagoj':
     novaj_antauxpagoj();

     break; // novaj_antauxpagoj

 default:
     darf_nicht_sein("sendu = '" . $_REQUEST['sendu'] . "'");

 }  // switch


eoecho("<h1>Redaktado de kotizosistemo</h1>\n");

$sistemo = new Kotizosistemo($_REQUEST['id']);


redaktilo_por_bazaj_datumoj($sistemo);
redaktilo_por_parttempsistemoj($sistemo);
redaktilo_por_unuopaj_kotizoj($sistemo);
redaktilo_por_minimumaj_antauxpagoj($sistemo);
redaktilo_por_regulaj_pseuxdopagoj($sistemo, "krompago");
redaktilo_por_regulaj_pseuxdopagoj($sistemo, "rabato");
//redaktilo_por_krompagoj($sistemo);


echo "<p>\n";
ligu("kotizosistemoj.php", "listo de c^iuj kotizosistemoj");
ligu("kotizoj.php", "C^io pri kotizoj");

echo "</p>";


HtmlFino();

