<?php


  /**
   * kreado + redaktado/administrado de unuopaj kategoriaj sistemoj
   * (aligxtempo, lando, agxo, logxado).
   */

require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');


session_start();
malfermu_datumaro();


kontrolu_rajton("vidi");
// TODO: cxiuj krom teknikisto (aux administranto?) nur
// rajtu redakti sian proprajn kategorisistemojn.


if (!in_array($_REQUEST['tipo'], $GLOBALS['kategoriotipoj'])) {
    // mankas tipo, aux nevalida tipo.
    http_redirect("kategorisistemoj.php", null, false, 303);
    return;
 }
     
$tipo = $_REQUEST['tipo'];


HtmlKapo();



switch($_REQUEST['sendu']) {
 case '':
     // (neniu buton-premado, simpla
     //   voko de la pagxo)
     break;


 case 'sxangxu':  // ------ Sxangxoj en nomo/priskribo
     //                     de iu kotizosistemo        ----------
     {
         sxangxu_datumbazon($tipo . "kategorisistemoj",
                            array('nomo' => $_REQUEST['nomo'],
                                  'priskribo' => $_REQUEST['priskribo']),
                            array('ID' => $_REQUEST['ID']));
         $_REQUEST['id'] = $_REQUEST['ID'];
         eoecho ("<p>S^ang^is la bazajn datojn de la " . donu_eokatsisnomon($tipo)
                 . " #" . $_REQUEST['id'] . ".</p>\n");
     }
     break;


 case 'kreu':
 case 'kopiu':  // ------- Nova kategoriosistemo, kun kopio
     //                    de la kategorioj de alia sistemo   --------
     {
         aldonu_al_datumbazo($tipo. "kategorisistemoj",
                             array('nomo' => $_REQUEST['nomo'],
                                   'entajpanto' => $_REQUEST['entajpanto'],
                                   'priskribo' => $_REQUEST['priskribo']));
         $id = mysql_insert_id();
         $sistemo = donu_katsistemon($id, $tipo);
         $_REQUEST['id'] = $id;
         
         if ($_REQUEST['sendu'] == 'kopiu') {
             $sistemo->kopiu_kategoriojn_el($_REQUEST['ID']);
             eoecho ("<p>Kreis novan " . donu_eokatsisnomon($tipo) .
                     " #" . $id . " kiel kopio de #" . $_REQUEST['ID'] .
                     ".</p>\n");
         }
         else {
             eoecho ("<p>Kreis novan " . donu_eokatsisnomon($tipo) .
                     " #" . $id . ".</p>\n");
         }
     }
     break;

     
 case 'katSxangxu': // -------------- Sxangxo de unuopaj kategorioj --------

     if (DEBUG) {
         echo "<!-- GET: ";
         var_export($_GET);
         echo "POST: ";
         var_export($_POST);
         echo "-->";

     }

     foreach($_REQUEST['kategorio'] AS $katID => $datumoj) {
         // TODO: kontrolu, cxu necesas sxangxo
         sxangxu_datumbazon($tipo."kategorioj",
                            $datumoj,
                            $katID);
     }
     $katSistemo = donu_katsistemon($_REQUEST['id'], $tipo);
     $katSistemo->mangxu_aliajn_kategorisxangxojn();
     eoecho("<p>S^ang^is plurajn " . donu_eokatnomon($tipo) . "jn por sistemo #" . $_REQUEST['id'] . "</p>\n");
     
     break;


 case 'katNova': // -------- nova kategorio ene de la aktuala sistemo. -------
     {
         // kreas malplenan kategorio-objekton.
         $kategorio = donu_kategorion($tipo, 0);
         $kategorio->kopiu();
         $kategorio->datoj['sistemoID'] = $_REQUEST['id'];
         $kategorio->skribu_kreante();
     }
     break;


 default:
     darf_nicht_sein("sendu: '" . $_REQUEST['sendu'] . "'");
 }



if ($_REQUEST['id']) {
    $katsistemo = donu_katsistemon($_REQUEST['id'], $tipo);
    
    eoecho("<h1>Redaktado de " . donu_eokatsisnomon($tipo) ."</h1>\n");
    
 }
 else {
     //         $katsistemo = donu_katsistemon(0, $tipo);
         
     eoecho("<h1>Nova " . donu_eokatsisnomon($tipo) . "</h1>\n");
 }

 


eoecho("<h2>Bazaj datumoj</h2>\n");

echo "<form action='kategorisistemo.php?tipo=".$tipo."' method='POST'>\n";
 
echo("<table>\n");
tabela_kasxilo("ID", "ID", $katsistemo->datoj['ID']);
tabelentajpejo("nomo", 'nomo', $katsistemo->datoj['nomo'], 20);

if ($_REQUEST['id']) {
    $entajpanto = $katsistemo->datoj['entajpanto'];
    $ent_nomo = eltrovu_entajpanton($entajpanto);
 }
 else {
     $entajpanto = $_SESSION['kkren']['entajpanto'];
     $ent_nomo = $_SESSION['kkren']['entajpantonomo'];
 }
tabela_kasxilo("Posedanto", "entajpanto", $entajpanto, $ent_nomo);
granda_tabelentajpejo("priskribo", "priskribo",
                      $katsistemo->datoj['priskribo'], 40, 5);
echo "</table>\n";

echo "<p>";

if (!$_REQUEST['id'])
    {
        butono("kreu", "Kreu");
        echo "</p>\n</form>\n";
        HtmlFino();
        exit();
    }

butono("sxangxu", "S^ang^u");
butono("kopiu", "Kopiu"); eoecho ("(tiam donu novan nomon!)");
    
echo "</p>\n</form>\n";



echo "<hr />\n";


eoecho("<h2>" . donu_eokatnomon($tipo) . "j en <em>" .
       $katsistemo->datoj['nomo'] . "</em></h2>\n");

echo ("<form action='kategorisistemo.php?tipo=".$tipo."&amp;id=".
      $_REQUEST['id']."' method='POST'>\n");

$katsistemo->listu_kategoriojn('redaktebla');

echo "<p>";
butono("katSxangxu", "S^ang^u kategoriojn");

echo "</p>\n</form>\n";

echo "<hr />\n";

eoecho("<h2> Nova " . donu_eokatnomon($tipo) . " en <em>" .
       $katsistemo->datoj['nomo'] . "</em></h2>\n");

echo ("<form action='kategorisistemo.php?tipo=".$tipo."&amp;id=".
      $_REQUEST['id']."' method='POST'>\n");


echo "<table>\n";

$katsistemo->kreu_kategorikreilon();

echo "</table>\n<p>";
butono("katNova", "Nova kategorio");
echo "</p>\n</form>\n";


echo "<hr />\n<p>";

ligu("kategorisistemoj.php", "Reen al la listo");

HtmlFino();

?>