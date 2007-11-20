<?php

  //   define("DEBUG", true);

  /**
   * ebligas kreadon, redaktadon kaj elprovadon de kotizosistemo.
   */


require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');


  session_start();
  malfermu_datumaro();


// TODO: pripensu pli bonan rajton
kontrolu_rajton("vidi");


HtmlKapo();


switch($_REQUEST['sendu']) {
 case '':
     break;

 case  'sxangxu_krompagojn':
     foreach($_REQUEST['krompago'] AS $tipo => $sumo) {
         // TODO: eble kontrolu, kie necesas sxangxoj
         sxangxu_datumbazon("krompagoj",
                            array("krompago" => $sumo),
                            array("tipo" => $tipo,
                                  "kotizosistemo" => $_REQUEST['id']));
     }
     break;  // sxangxu_krompagojn

 case 'nova_krompago':

     // TODO: testu, cxu la senditajxoj estas en ordo.
     aldonu_al_datumbazo("krompagoj",
                         array('kotizosistemo' => $_REQUEST['id'],
                               'tipo' => $_REQUEST['tipo'],
                               'krompago' => $_REQUEST['krompago']));
     unset($_REQUEST['krompago']);
    
     break;  // nova_krompago


 case 'novaj_kategorioj':
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

     break; // novaj_kategorioj

 
 case 'novaj_kotizoj':
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
             
         } // if $kotizero
     } // foreach

     break; // novaj_kotizoj


 case 'kopiu':

     $malnova_sistemo = new Kotizosistemo($_REQUEST['id']);

     $nova_sistemo = new Kotizosistemo();
     $nova_sistemo->kopiu();
     if ($nova_sistemo->datoj['nomo'] == $malnova_sistemo->datoj['nomo'])
         {
             erareldono("Ne povas esti du sistemoj kun sama nomo!");
         }
     else{
         $nova_sistemo->skribu_kreante();
         // TODO: kopiu kotizotabelon, krompagojn kaj antauxpagojn, se eblas.
         
         // poste montru la novan sistemon
         $_REQUEST['id'] = $nova_sistemo->datoj['ID'];
     }

     break; // kopiu


 case 'novaj_antauxpagoj':
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



     break; // novaj_antauxpagoj

 default:
     darf_nicht_sein("sendu = '" . $_REQUEST['sendu'] . "'");

 }  // switch


/**
 * teknikumistoj havu eblecon redakti la krompagojn,
 * do ni formatos la krompagonomon alimaniere.
 */

if(rajtas("teknikumi")) {
    function formatu_krompagotipon($tipo) {
        return donu_ligon("krompagotipo.php?id=" . $tipo->datoj['ID'],
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

eoecho ("<table>\n<tr><th>ID</tr><td>" . $sistemo->datoj['ID']);
tenukasxe('id', $sistemo->datoj['ID']);
echo("</td></tr>\n");

$entajpanto = eltrovu_entajpanton($sistemo->datoj['entajpanto']);
eoecho ("<tr><th>entajpanto</tr><td>" . $entajpanto . "</td></tr>\n");


tabelentajpejo("nomo", 'nomo', $sistemo->datoj['nomo'], 30);
granda_tabelentajpejo("priskribo", 'priskribo', $sistemo->datoj['priskribo'],
                      40, 4);


foreach($GLOBALS['kategoriotipoj'] AS $tipo) {
    $katsis = $sistemo->donu_kategorisistemon($tipo);
    
    eoecho("<tr><th>" . $katsis->donu_eoklasnomon() ."</th><td>\n");
    elektilo_simpla_db(donu_katsisnomon($tipo),
                       donu_katsisnomon($tipo) . "j",
                       "nomo", "ID", $katsis->datoj['ID']);
    echo("</td></tr>\n");
}

// TODO: eble elpensu pli gxeneralan sistemon
tabelentajpejo("Malfaktoro por parttempaj kotizoj",
               'parttempdivisoro', 
               $sistemo->datoj['parttempdivisoro'],
               5);

eoecho ("</table>\n<p>");

// TODO: opcioj por kopii ekzistantajn kotizo-valorojn
butono('novaj_kategorioj', "S^ang^u");
butono("kopiu" , "Kopiu"); eoecho ("(tiam nepre necesas nova nomo)");
echo "</p></form>\n";

echo "<hr/>\n"; // -----------------------------------------------------

eoecho("<h2>Kotizoj en la kategorioj</h2>");

echo "<form action='kotizosistemo.php' method='POST'>\n";

tenukasxe('id', $sistemo->datoj['ID']);

$sistemo->metu_kotizotabelon("entajpa_kotizocxelo");

echo "<p>";
butono("novaj_kotizoj", "S^ang^u la kotizojn");
echo "</p>\n</form>\n";


echo "<hr/>\n"; // --------------------------------------------------------

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

eoecho("<h2>Krompagoj</h2>");

$neuzitaj = array();

echo("<form action='kotizosistemo.php' method='POST'>\n");

tenukasxe('id', $sistemo->datoj['ID']);

eoecho("<table class='krompagotabelo'>\n".
       "<tr><th>tipo</th><th>krompago</th><th>priskribo</th></tr>");

$tipolisto = listu_cxiujn_krompagotipojn();


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
echo("</table>\n<p>");

butono("sxangxu_krompagojn", "S^ang^u krompagojn");

echo "</p></form>\n";


if (count($neuzitaj)) {
    echo "<hr/>\n";  // ------------------------------------------------

    eoecho("<h2>Nova krompago</h2>");


    echo("<form action='kotizosistemo.php' method='POST'>\n");

    tenukasxe('id', $sistemo->datoj['ID']);

    eoecho ("<table>\n<tr><th>tipo</th><td/><th>priskribo</th></tr>\n");


    foreach($neuzitaj AS $kromtipo) {
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
    ligu("krompagotipo.php", "Nova krompagotipo");
 }

echo "<hr/>\n<p>";  // ----------------------------------------------------
ligu("kotizosistemoj.php", "listo de c^iuj kotizosistemoj");
ligu("kotizoj.php", "C^io pri kotizoj");

echo "</p>";


HtmlFino();


?>