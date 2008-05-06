<?php

  /**
   * Forigo de partoprenantoj kaj partoprenoj el la datumbazo.
   */

require_once ('iloj/iloj.php');


  session_start();
  malfermu_datumaro();


kontrolu_rajton("estingi");

HtmlKapo();  

eoecho("<h1>Forigo de partoprenantoj au^ partoprenoj</h1>");

    echo "<!--";
    var_export($_REQUEST);
    echo "\n_____________________________________________________ -->\n";



switch($_REQUEST['sendu']) {

 case 'forigu':

     if ($_REQUEST['kion'] == 'partoprenanto') {

         $sql = datumbazdemando(array("pp.ID" => "enoID"),
                                array("partoprenoj" => "pp"),
                                array("pp.partoprenantoID = '" . $_REQUEST['panto']. "'"),
                                "",
                                array("order" => "pp.ID"));
         $rez = sql_faru($sql);
         if (mysql_num_rows($rez) > 0) {
             eoecho("<p>Forigo de la partoprenoj ...</p>\n<ul>\n");
             while($linio = mysql_fetch_assoc($rez)) {
                 eoecho("<li>Forigo de Partopreno #" . $linio['enoID'] .
                        "...</li>");
                 forigu_el_datumbazo("partoprenoj",
                                     $linio['enoID']);
             }
             echo "</ul>";
         }
         else {
             eoecho("<p>Ne estas partoprenoj por forigi</p>");
         }

         $sql = datumbazdemando("ID",
                                "notoj",
                                "partoprenantoID = '". $_REQUEST['panto'] .
                                "'");
         $rez = sql_faru($sql);
         if(mysql_num_rows($rez) > 0) {
             eoecho("<p>forigas notojn ...</p>\n<ul>\n");
             while($linio = mysql_fetch_assoc($rez)) {
                 eoecho("<li>forigo de noto #" . $linio['ID'] . "...</li>");
                 forigu_el_datumbazo("notoj", $linio['ID']);
             }
         }
         else {
             eoecho ("<p>ne estas notoj por forigi</p>");
         }


         eoecho("<p>Forigo de la partoprenanto #" . $_REQUEST['panto'] . " ...</p>");
         forigu_el_datumbazo("partoprenantoj",
                             $_REQUEST['panto']);

         eoecho ("<p>Finita.</p>");
         HtmlFino();
         exit();
     }
     else if ($_REQUEST['kion'] == 'nenion') {
         break;
     }
     else if (is_numeric($_REQUEST['kion'])) {
         // forigu unuopan partoprenon
         $ppeno = new Partopreno($_REQUEST['kion']);

         if ($ppeno->datoj['partoprenantoID'] != $_REQUEST['panto']) {
             eoecho("<h2>Eraro!</h2>\n");
             eoecho("<p>La partopreno #" . $ppeno->datoj['ID'] .
                    " ne apartenas al la partoprenanto #" .$_REQUEST['panto'].
                    ", sed al #" . $ppeno->datoj['partoprenantoID'] .
                    "!</p>\n");
             HtmlFino();
             exit();
         }
         $ppeno->montru_aligxo("sen bla");
         eoecho("<p>Forigo de Partopreno #" . $ppeno->datoj['ID'] .
                "...</p>");
         forigu_el_datumbazo("partoprenoj", $ppeno->datoj['ID']);
         eoecho ("<p>Finita.</p>");
         ligu("partrezultoj.php?partoprenantoidento=" .
              $ppeno->datoj['partoprenantoID'], "Reen");
         HtmlFino();
         exit();
     }

     

     break;

 default:
     // faru nenion.

 }



sesio_aktualigo_laux_get();


$panto = $_SESSION['partoprenanto'];

$panto->montru_aligxinto("sen bla");


eoecho("<p>Kion vi volas forigi?</p>");

echo "<form action='partoprenforigo.php?panto=" . $panto->datoj['ID'] . "' method='POST'>\n";

entajpbutono("<ul>\n<li>",
             'kion', "", "partoprenanto", "partoprenanto",
             "La partoprenanton #" . $panto->datoj['ID'] 
             . " (" . $panto->tuta_nomo() . ") kun c^iuj " 
             . $panto->personapronomo . "aj partoprenoj kaj la sekvaj notoj:");

listu_notojn($panto->datoj['ID']);

echo "</li>\n";

$sql = datumbazdemando(array("pp.ID" => "enoID", "r.ID" => "rID",
                             "r.mallongigo", "pp.de", "pp.gxis"),
                       array("partoprenoj" => "pp",
                             "renkontigxo" => "r"),
                       array("pp.renkontigxoID = r.ID",
                             "pp.partoprenantoID = '" . $panto->datoj['ID'] . "'"),
                       "",
                       array("order" => "pp.ID"));
$rez = sql_faru($sql);
while($linio = mysql_fetch_assoc($rez)) {
    entajpbutono("<li>",
                 'kion', "", $linio['enoID'], $linio['enoID'],
                 "La partoprenon "
                 . donu_ligon("partrezultoj.php?partoprenidento="
                              . $linio['enoID'],
                              "#" . $linio['enoID'] . " en " 
                              . $linio['mallongigo'])
                 . " (" . $linio['de'] . "&mdash;" . $linio['gxis']
                 . ") </li>\n");
 }


entajpbutono("<li>",
             'kion', "", "nenion", "nenion",
             "Mi nenion volas forigi nun.</li>\n</ul>\n", "kutima");

eoecho ("<p>Atentu, ne eblos restarigi iam forigitajn datumojn!</p>\n<p>");


butono('forigu', "Forigu!");

// TODO: eble uzu $_SESSION['sekvontapagxo'] ?
ligu("partrezultoj.php?partoprenantoidento=" . $anto->datoj['ID'],
     "Reen");

echo "</p>\n</form>\n";



HtmlFino();



?>