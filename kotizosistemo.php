<?php

  // define("DEBUG", true);

  /**
   * ebligas kreadon, redaktadon kaj elprovadon de kotizosistemo.
   */


require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');


  session_start();
  malfermu_datumaro();


// TODO: pripensu pli bonan rajton
kontrolu_rajton("vidi");

$tipolisto = listu_cxiujn_krompagotipojn();

HtmlKapo();


if ($_REQUEST['sendu'] == 'sxangxu_krompagojn') {

    foreach($_REQUEST['krompago'] AS $tipo => $sumo) {
        // TODO: eble kontrolu, kie necesas sxangxoj
        sxangxu_datumbazon("krompagoj",
                           array("krompago" => $sumo),
                           array("tipo" => $tipo,
                                 "kotizosistemo" => $_REQUEST['id']));
    }
 }


if($_REQUEST['sendu'] == 'nova_krompago') {
    // TODO: testu la senditajxojn
    aldonu_al_datumbazo("krompagoj",
                        array('kotizosistemo' => $_REQUEST['id'],
                              'tipo' => $_REQUEST['tipo'],
                              'krompago' => $_REQUEST['krompago']));
    unset($_REQUEST['krompago']);
 }

if ($_REQUEST['sendu'] == 'novaj_kategorioj') {
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
 else if($_REQUEST['sendu'] == 'novaj_kotizoj') {
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
             $idoj = dekodu_kategoriojn($nomo);
             $idoj['kotizosistemo'] = $_REQUEST['id'];

             $restriktoj = array();
             foreach($idoj as $nomo => $id) {
                 $restriktoj[]= "{$nomo} = '{$id}'";
             }
             $rez = sql_faru(datumbazdemando("kotizo",
                                             "kotizotabeleroj",
                                             $restriktoj));
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
             
         }
     }
 }


if ($_REQUEST['sendu'] == 'kopiu') {

     $malnova_sistemo = new Kotizosistemo($_REQUEST['id']);

     $nova_sistemo = new Kotizosistemo();
     $nova_sistemo->kopiu();
     if ($nova_sistemo->datoj['nomo'] == $malnova_sistemo->datoj['nomo'])
         {
             erareldono("Ne povas esti du sistemoj kun sama nomo!");
         }
     else{
         $nova_sistemo->skribu_kreante();
         // TODO: kopiu kotizotabelon, se eblas.

         
         // poste montru la novan sistemon
         $_REQUEST['id'] = $nova_sistemo->datoj['ID'];
     }

   

 }


/**
 * teknikumistoj havu eblecon redakti la krompagojn,
 * do ni formatos la krompagonomon alimaniere.
 */

if(rajtas("teknikumi")) {
    function formatu_krompagotipon($tipo) {
        return donu_ligon("krompago.php?id=" . $tipo->datoj['ID'],
                          $tipo->datoj['nomo']);
    }
 }
 else {
    function formatu_krompagotipon($tipo) {
        return $tipo->datoj['nomo'];
    }
 }     


eoecho("<h1>Redaktado de kotizosistemo</h1>\n");

eoecho("<h2>Bazaj datumoj</h2>\n");

$sistemo = new Kotizosistemo($_REQUEST['id']);


echo "<form action='kotizosistemo.php' method='POST'>\n";

// TODO: priskribo + nomo
eoecho ("<table>\n<tr><th>ID</tr><td>" . $sistemo->datoj['ID']);
tenukasxe('id', $sistemo->datoj['ID']);
echo("</td></tr>\n");

$entajpanto = eltrovu_entajpanton($sistemo->datoj['entajpanto']);
eoecho ("<tr><th>entajpanto</tr><td>" . $entajpanto . "</td></tr>\n");


tabelentajpejo("nomo", 'nomo', $sistemo->datoj['nomo'], 30);
granda_tabelentajpejo("priskribo", 'priskribo', $sistemo->datoj['priskribo'], 30);


foreach($GLOBALS['kategoriotipoj'] AS $tipo) {
    $katsis = $sistemo->donu_kategorisistemon($tipo);
    
    eoecho("<tr><th>" . $katsis->donu_eoklasnomon() ."</th><td>\n");
    elektilo_simpla_db(donu_katsisnomon($tipo),
                       donu_katsisnomon($tipo) . "j",
                       "nomo", "ID", $katsis->datoj['ID']);
    
    echo("</td></tr>\n");
}
eoecho ("</table>\n");

// TODO: opcioj por kopii ekzistantajn kotizo-valorojn
butono('novaj_kategorioj', "S^ang^u");
butono("kopiu" , "Kopiu"); eoecho ("(tiam nepras nova nomo)");
echo "</form>\n";

echo "<hr/>\n";

eoecho("<h2>Kotizoj en la kategorioj</h2>");

echo "<form action='kotizosistemo.php' method='POST'>\n";

tenukasxe('id', $sistemo->datoj['ID']);

$sistemo->metu_kotizotabelon("entajpa_kotizocxelo");

butono("novaj_kotizoj", "S^ang^u la kotizojn");
echo "</form>\n";


echo "<hr/>\n";

eoecho("<h2>Krompagoj</h2>");

$neuzitaj = array();

echo("<form action='kotizosistemo.php' method='POST'>\n");

tenukasxe('id', $sistemo->datoj['ID']);

eoecho("<table class='krompagotabelo'>\n".
       "<tr><th>tipo</th><th>krompago</th><th>priskribo</th></tr>");

foreach($tipolisto AS $kromtipo) {
    $sql = datumbazdemando("krompago",
                           "krompagoj",
                           array("kotizosistemo = '".$sistemo->datoj['ID']."'",
                                 "tipo = '" . $kromtipo->datoj['ID']."'"));
    $linio = mysql_fetch_assoc(sql_faru($sql));
    if ($linio) {
        tabelentajpejo(formatu_krompagotipon($kromtipo),
                       "krompago[" . $kromtipo->datoj['ID']."]",
                       $linio['krompago'],
                       5,
                       "</td><td>" . $kromtipo->datoj['priskribo'] );
        
    }
    else {
        $neuzitaj[] = $kromtipo;
    }
}
echo("</table>");

butono("sxangxu_krompagojn", "S^ang^u krompagojn");

echo "</form>\n";


if (count($neuzitaj)) {
    echo "<hr/>\n";

    eoecho("<h2>Nova krompago</h2>");


    echo("<form action='kotizosistemo.php' method='POST'>\n");

    tenukasxe('id', $sistemo->datoj['ID']);

    eoecho ("<table>\n<tr><th>tipo</th><td/><th>priskribo</th></tr>\n");


    foreach($neuzitaj AS $tipo) {
        tabel_entajpbutono(formatu_krompagotipon($kromtipo),
                           'tipo', "",
                           $kromtipo->datoj['ID'],
                           $kromtipo->datoj['priskribo']);
    }

    echo("</table>");


    simpla_entajpejo("<p>krompago: ", 'krompago', "", 6, "", " ");
    butono("nova_krompago", "Aldonu!");
    echo "</p></form>\n";

 }

if (rajtas("teknikumi")) {
    ligu("krompago.php?id=nova", "Nova krompagotipo");
 }

HtmlFino();


?>