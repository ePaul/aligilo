<?php



require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');


session_start();
malfermu_datumaro();


kontrolu_rajton("administri");


HtmlKapo();

switch($_REQUEST['sendu']) {
 case '':
     break;
 case 'sxangxu':
     // TODO: sxangxu bazajn datumojn
     break;
 case 'kreu':
 case 'kopiu':  // ------- Nova kondicxosistemo, kun kopio
     {
         aldonu_al_datumbazo("malaligxkondicxsistemoj",
                             array('nomo' => $_REQUEST['nomo'],
                                   'priskribo' => $_REQUEST['priskribo'],
                                   'aligxkategorisistemo' =>
                                   $_REQUEST['aligxkategorisistemo']));
         $id =mysql_insert_id();
         $sistemo = new Malaligxkondicxsistemo($id);
         if ($_REQUEST['sendu'] == 'kopiu') {
             $sistemo->kopiu_kondicxojn_el($_REQUEST['ID']);
             eoecho ("<p>Kreis novan Malalig^kotizosistemon" .
                     " #" . $id . " kiel kopio de #" . $_REQUEST['ID'] .
                     ".</p>\n");
         }
         else {
             eoecho ("<p>Kreis novan Malalig^kotizosistemon #" . $id .
                     ".</p>\n");
         }
         $_REQUEST['id'] = $id;
     
     }  // case kreu/kopiu
     break;
 case 'sxangxuKond':
     {
         foreach($_REQUEST['kondicxtipo'] AS $kat => $tipo) {
             $sql = datumbazdemando("kondicxtipo",
                                    "malaligxkondicxoj",
                                    array("sistemo = '". $_REQUEST['ID'] ."'",
                                          "aligxkategorio = '".$kat."'"));
             $linio = mysql_fetch_assoc(sql_faru($sql));
             if ($linio) {
                 if ($linio['kondicxtipo'] != $tipo) {
                     sxangxu_datumbazon("malaligxkondicxoj",
                                        array("kondicxtipo" => $tipo),
                                        array("sistemo" => $_REQUEST['ID'],
                                              "aligxkategorio" => $kat));
                     eoecho( "<p>S^ang^is kondic^on por kategorio #" . $kat .
                             " de #" . $linio['kondicxtipo'] . " al #" .
                             $tipo. ".\n</p>");
                 }
             }
             else {
                 aldonu_al_datumbazo("malaligxkondicxoj",
                                     array("kondicxtipo" => $tipo,
                                           "sistemo" => $_REQUEST['ID'],
                                           "aligxkategorio" => $kat));
                 eoecho( "<p>Aldonis kondic^on #" . $tipo .
                         " por kategorio #" . $kat. ".\n</p>");
             }
         }
         $_REQUEST['id'] = $_REQUEST['ID'];
     }
     break;

 default:
     echo "<pre>";
     var_export($_POST);
     echo "</pre>";
 }

if ($_REQUEST['id']) {
    $kondicxsistemo = new Malaligxkondicxsistemo($_REQUEST['id']);
    eoecho("<h1>Redaktado de malalig^kondic^sistemo "
           . $kondicxsistemo->datoj['nomo'] . "</h1>\n");
    
 }
 else {
     eoecho("<h1>Nova malalig^kondic^sistemo</h1>");
 }

eoecho("<h2>Bazaj datumoj</h2>");

echo "<form action='malaligxkondicxsistemo.php' method='POST'>\n";
echo("<table>\n");
tabela_kasxilo("ID", "ID", $kondicxsistemo->datoj['ID']);
tabelentajpejo("nomo", 'nomo', $kondicxsistemo->datoj['nomo'], 20);
granda_tabelentajpejo("priskribo", "priskribo",
                      $kondicxsistemo->datoj['priskribo'], 40, 5);
tabela_elektilo_db("alig^kategorisistemo",
                   'aligxkategorisistemo',
                   'aligxkategorisistemoj',
                   'nomo', 'ID',
                   $kondicxsistemo->datoj['aligxkategorisistemo']);

echo("</table>\n");

echo "<p>";

if (!$_REQUEST['id']) {
    butono("kreu", "Kreu");
    echo "</p>\n</form>\n";
 }
 else {
     
     butono("sxangxu", "S^ang^u");
     butono("kopiu", "Kopiu"); eoecho ("(tiam donu novan nomon!)");
     
     echo "</p>\n</form>\n";
     
     

     echo "<hr/>\n";
 
    
     eoecho( "<h2>Kondic^oj por la unuopaj malalig^periodoj</h2>");

     echo "<form action='malaligxkondicxsistemo.php' method='POST'>\n";

     tenukasxe("ID", $kondicxsistemo->datoj['ID']);

     $sql = datumbazdemando(array("ID", "nomo", "limdato"),
                            "aligxkategorioj",
                            array("sistemoID = '" .
                                  $kondicxsistemo->datoj['aligxkategorisistemo']
                                  . "'"),
                            "",
                            array("order" => "limdato DESC"));
     $rez = sql_faru($sql);

     echo "<table>\n";
     eoecho("<tr><th>ID</th><th>nomo</th><th>limdato</th>".
            "<th>kondic^o</th></tr>\n");

     while($linio = mysql_fetch_assoc($rez)) {
         $sql = datumbazdemando("kondicxtipo",
                                "malaligxkondicxoj",
                                array("sistemo = '" .
                                      $kondicxsistemo->datoj['ID'] . "'",
                                      "aligxkategorio = '". $linio['ID'] ."'"));
         $lin2 = mysql_fetch_assoc(sql_faru($sql));
         eoecho("<tr><td>" . $linio['ID'] . "</td><td>" . $linio['nomo'] .
                "</td><td>" . $linio['limdato'] . "</td><td>");
         elektilo_simpla_db("kondicxtipo[{$linio['ID']}]",
                            "malaligxkondicxotipoj",
                            "nomo",
                            "ID",
                            $lin2['kondicxtipo'],
                            "uzebla = 'j'");
         echo( "</td></tr>\n");
     }
     echo "</table>\n<p>";

     butono("sxangxuKond", "S^ang^u kondic^ojn");


     echo "</p></form>";

 }

echo "<hr/>\n<p>";

ligu("kotizosistemoj.php", "C^iuj kotizosistemoj");
ligu("kategorisistemoj.php#malaligxsistemoj", "C^iuj kategoriosistemoj");
ligu("kotizoj.php", "c^io rilate al kotizoj");

echo "</p>";

HtmlFino();

?>