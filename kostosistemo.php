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

 case 'nova_personkosto':

     // TODO: testu, cxu la senditajxoj estas en ordo.
     aldonu_al_datumbazo("personkostoj",
                         array('kostosistemo' => $_REQUEST['id'],
                               'tipo' => $_REQUEST['tipo'],
                               'maks_haveblaj' => $_REQUEST['maks_haveblaj'],
                               'min_uzendaj' => $_REQUEST['min_uzendaj'],
                               'kosto_neuzata' => $_REQUEST['kosto_neuzata'],
                               'kosto_uzata' => $_REQUEST['kosto_uzata']));

     unset($_REQUEST['krompago']);
    
     break;  // nova_krompago



 case 'sxangxu_baze':
     if (DEBUG) {
         echo "<!-- POST:";
         var_export($_POST);
         echo "-->";
     }

     // TODO: kontrolu rajton: nur posedanto aux
     // administrantoj povu redakti la kostosistemon.

     $sistemo = new Kostosistemo($_REQUEST['id']);
     $sistemo->kopiu();
     $sistemo->datoj['entajpanto'] = $_SESSION['kkren']['entajpanto'];
     $sistemo->skribu();

     break; // sxangxu_baze


 case 'kopiu':

     $malnova_sistemo = new Kostosistemo($_REQUEST['id']);

     $nova_sistemo = new Kostosistemo();
     $nova_sistemo->kopiu();
     if ($nova_sistemo->datoj['nomo'] == $malnova_sistemo->datoj['nomo'])
         {
             erareldono("Ne povas esti du sistemoj kun sama nomo!");
         }
     else{
         $nova_sistemo->skribu_kreante();
         // TODO: kopiu kostotabelon, krompagojn kaj antauxpagojn, se eblas.
         
         // poste montru la novan sistemon
         $_REQUEST['id'] = $nova_sistemo->datoj['ID'];
     }

     break; // kopiu

 case 'nova_fikskosto':
     aldonu_al_datumbazo("fikskostoj",
                         array("nomo" => $_REQUEST['nomo'],
                               "kosto" => $_REQUEST['kosto'],
                               "kostosistemo" => $_REQUEST['id']));
     break; // nova_fikskosto


 default:
     echo "<pre>";
     var_export($_REQUEST);
     echo "</pre>";
     // TODO
 }


// TODO


eoecho("<h1>Redaktado de kostosistemo</h1>\n");

eoecho("<h2>Bazaj datumoj</h2>\n");

$sistemo = new Kostosistemo($_REQUEST['id']);


echo "<form action='kostosistemo.php' method='POST'>\n";

// TODO: priskribo + nomo
eoecho ("<table>\n<tr><th>ID</tr><td>" . $sistemo->datoj['ID']);
tenukasxe('id', $sistemo->datoj['ID']);
echo("</td></tr>\n");

$entajpanto = eltrovu_entajpanton($sistemo->datoj['entajpanto']);
eoecho ("<tr><th>entajpanto</tr><td>" . $entajpanto . "</td></tr>\n");


tabelentajpejo("nomo", 'nomo', $sistemo->datoj['nomo'], 30);
granda_tabelentajpejo("priskribo", 'priskribo', $sistemo->datoj['priskribo'],
                      40, 4);

eoecho ("</table>\n<p>");

// TODO: opcioj por kopii ekzistantajn kosto-valorojn
butono('sxangxu_baze', "S^ang^u");
butono("kopiu" , "Kopiu"); eoecho ("(tiam nepre necesas nova nomo)");
echo "</p></form>\n";



echo "<hr/>\n"; // --------------------------------------------------------

eoecho("<h2>Personkostoj</h2>");


if(rajtas("teknikumi")) {

    function formatu_personkostotipon($tipo) {
        return donu_ligon("personkostotipo.php?id=" . $tipo->datoj['ID'],
                          $tipo->datoj['nomo']);
    }
 }
 else {
    function formatu_personkostotipon($tipo) {
        return $tipo->datoj['nomo'];
    }
 }


$neuzitaj = array();

echo("<form action='kostosistemo.php' method='POST'>\n");

tenukasxe('id', $sistemo->datoj['ID']);

eoecho("<table class='personkostotabelo'>\n".
       "<tr><th>tipo</th><th>priskribo</th></tr>");

$tipolisto = listu_cxiujn_personkostotipojn();

$informoj = array("maks_haveblaj"=>"maksimume haveblaj",
                  "min_uzendaj" => "minimume uzendaj",
                  "kosto_uzata" => "kosto uzata",
                  "kosto_neuzata" => "kosto neuzata");

eoecho("<table class='personkostotabelo'>\n".
       "<tr><th>tipo</th><th>" .
       implode("</th><th>", $informoj) .
       "</th><th>priskribo</th></tr>");


foreach($tipolisto AS $kostotipo) {
    $sql = datumbazdemando(array_keys($informoj),
                           "personkostoj",
                           array("kostosistemo = '".$sistemo->datoj['ID']."'",
                                 "tipo = '" . $kostotipo->datoj['ID']."'"));
    $linio = mysql_fetch_assoc(sql_faru($sql));
    if ($linio) {
        eoecho ("<tr><td>" . formatu_personkostotipon($kostotipo) .
                "</td>");
        foreach($linio AS $nomo => $valoro) {
            simpla_entajpejo("<td>",
                             "personkosto[".$kostotipo->datoj['ID']."][".
                             $nomo."]",
                             $valoro, 6);
            // TODO: pripensu pli bonan kriterion:
            if (substr($nomo, 0, 6) == "kosto_") {
                if ($kostotipo->datoj['lauxnokte'] == 'j') {
                    eoecho ("E^/nokto</td>");
                }
                else {
                    eocho ("E^</td>");
                }
            }
        }
        eoecho("<td>" . $kostotipo->datoj['priskribo'] . "</td></tr>\n");
//         tabelentajpejo(formatu_personkostotipon($kromtipo),
//                        "personkosto[" . $kromtipo->datoj['ID']."]",
//                        $linio['personkosto'],
//                        5,
//                        "</td><td>" . $kromtipo->datoj['priskribo'] );
        
    }
    else {
        $neuzitaj[] = $kostotipo;
    }
}
echo("</table>\n<p>");

butono("sxangxu_personkostojn", "S^ang^u personkostojn");

echo "</p></form>\n";


if (count($neuzitaj)) {
    echo "<hr/>\n";  // ------------------------------------------------

    eoecho("<h2>Nova personkosto</h2>");

    if (rajtas("teknikumi")) {
        ligu("personkostotipo.php", "Nova personkostotipo");
    }



    echo("<form action='kostosistemo.php' method='POST'>\n");

    tenukasxe('id', $sistemo->datoj['ID']);

    eoecho ("<table>\n<tr><th>tipo</th><td/><th>priskribo</th></tr>\n");


    foreach($neuzitaj AS $kromtipo) {
        tabel_entajpbutono(formatu_personkostotipon($kromtipo),
                           'tipo', "",
                           $kromtipo->datoj['ID'],
                           $kromtipo->datoj['priskribo']);
    }

    echo("</table>");

    echo "<table>";
    foreach($informoj AS $nomo => $titolo) {
        tabelentajpejo($titolo, $nomo, "", 6);
    }
    
    echo "</table><p>";
    butono("nova_personkosto", "Aldonu!");
    echo "</p></form>\n";

 }



echo "<hr/>\n"; // --------------------------------------------------------

eoecho("<h2>Fikskostoj</h2>");



echo("<form action='kostosistemo.php' method='POST'>\n");

tenukasxe('id', $sistemo->datoj['ID']);


eoecho("<table class='fikskostotabelo'>\n".
       "<tr><th>nomo</th><th>kosto</th></tr>");


$sql = datumbazdemando(array("ID", "nomo", "kosto"),
                       "fikskostoj",
                       "kostosistemo = '".$sistemo->datoj['ID']."'");
$rez = sql_faru($sql);
while($linio = mysql_fetch_assoc($rez)) {
    simpla_entajpejo("<tr><td>",
                     "fikskostoj[". $linio['ID'] . "][nomo]",
                     $linio['nomo'], 30, "</td>");
    simpla_entajpejo("<td>",
                     "fikskostoj[". $linio['ID'] . "][kosto]",
                     $linio['kosto'], 6, "</td>");
    // TODO: ligo por forigi la kosto-linion
    echo "</tr>\n";
 }

echo("</table>\n<p>");

butono("sxangxu_fikskostojn", "S^ang^u fikskostojn");

echo "</p></form>\n";

echo("<form action='kostosistemo.php' method='POST'>\n");

tenukasxe('id', $sistemo->datoj['ID']);

unset($_REQUEST['nomo'], $_REQUEST['kosto']);

echo "<table>";
tabelentajpejo("nomo", "nomo", "", 30);
tabelentajpejo("kosto", "kosto", "", 6);
echo "</table>";

butono("nova_fikskosto", "Nova fikskosto");

echo "</p></form>\n";




echo "<hr/><p>";// -------------------------------------------------------
ligu("kostosistemoj.php", "listo de c^iuj kostosistemoj");
ligu("kotizoj.php", "C^io pri kotizoj");
echo "</p>\n";


HtmlFino();



?>