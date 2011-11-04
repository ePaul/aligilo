<?php


  /**
   * Redaktado de kurzoj.
   *
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   * debug-moduso.
   */
  // define("DEBUG", true);


  /**
   * la kutimaj iloj.
   */
require_once ('iloj/iloj.php');
malfermu_datumaro();

session_start();

kontrolu_rajton("mono");
kontrolu_rajton("administri");


HtmlKapo();


function listu_kurzojn()
{
    
    eoecho("<h2>Kurzoj de Valutoj</h2>\n");
    
    
    $sql = datumbazdemando(array('interna', 'teksto'),
                           'renkontigxaj_konfiguroj',
                           array('tipo' => 'valuto'),
                           'renkontigxoID');
    
    $rez_valuto = sql_faru($sql);
    
    while ($linio_valuto = mysql_fetch_assoc($rez_valuto)) {

        eoecho("<h3>Kurzoj de " . $linio_valuto['interna'] ." &ndash; "  .  $linio_valuto['teksto'] . "</h3>\n");
        if ($linio_valuto['interna'] == CXEFA_VALUTO) {
            eoecho( "<p>" . $linio_valuto['interna'] .
                    " estas la c^efa valuto de tiu instalaj^o. Do principe " .
                    " ne sencas havi kurzojn por tiu valuto. Anstatau^e c^iuj".
                    " aliaj kurzoj estu relative al tiu c^i valuto. " .
                    "</p>\n");
        }
        listu_kurzojn_por($linio_valuto['interna']);
    
        ligu("kurzoj.php?id=nova&valuto=" . $linio_valuto['interna'],
             "nova kurzo");

    }  // while

} // listu_kurzojn


function listu_kurzojn_por($valuto) {
        $sql = datumbazdemando(array('dato', 'kurzo', 'ID'),
                               'kurzoj',
                               array('valuto' => $valuto),
                               "",
                               array('order' => 'dato ASC'));
        $rez = sql_faru($sql);
        echo "<table class='kurzolisto'>\n";
        while($linio = mysql_fetch_assoc($rez)) {
            echo "  <tr><td>";
            ligu("kurzoj.php?id=" . $linio['ID'], "red.");
            echo "</td><td>" . $linio['dato'] . "</td><td>" .
                $linio['kurzo'] . "</td></tr>\n";
        }
        echo "</table>\n";

} // listu_kurzojn_por


function redaktilo_por_kurzo($kurzo) {
    
    if ($kurzo->datoj['ID']) {
        eoecho( "<h2>Redakto de kurzo</h2>");
    }
    else {
        eoecho("<h2>Nova kurzo</h2>");
    }

    echo "<form action='kurzoj.php' method='POST'>\n";
    
    echo "<table>\n";
    tabela_kasxilo("ID", 'ID', $kurzo->datoj['ID']);
    tabela_elektolisto_el_konfiguroj("valuto", 'valuto',
                                     'valuto', $kurzo->datoj['valuto'],
                                     $ppRenk);
    tabelentajpejo("dato", 'dato', $kurzo->datoj['dato'], 10);
    tabelentajpejo("kurzo", 'kurzo', $kurzo->datoj['kurzo'], 10);
    echo "</table>\n";

    echo "<p>\n";
    if ($kurzo->datoj['ID']) {
        butono('sxangxu', "S^ang^u!");
    }
    else {
        butono('kreu', "Enmetu!");
    }
    ligu("kurzoj.php","Reen");
    echo "</p>\n";
    


    echo "</form>";
}


function savu_kurzon() {
    if ($_REQUEST['sendu'] == 'sxangxu')
        {
            $kurzo = new Kurzo($_REQUEST['ID']);
            $kurzo->kopiu();
            $kurzo->skribu();
        }
    else
        {
            // kreu
            $kurzo = new Kurzo();
            $kurzo->kopiu();
            $kurzo->skribu_kreante();
        }
    return $kurzo;
}



if ($_REQUEST['sendu']) {
    savu_kurzon();
 }

if ($_REQUEST['id']) {
    if (is_numeric($_REQUEST['id'])) {
        $kurzo = new Kurzo($_REQUEST['id']);
    }
    else {
        $kurzo = new Kurzo();
        $kurzo->datoj['valuto'] = $_REQUEST['valuto'];
        $kurzo->datoj['dato'] = date('Y-m-d');
        echo ("<!-- kurzo: " . var_export($kurzo, true) . "-->");
    }
 }


if (is_object($kurzo)) {
    redaktilo_por_kurzo($kurzo);
 }
 else {
     listu_kurzojn();
 }