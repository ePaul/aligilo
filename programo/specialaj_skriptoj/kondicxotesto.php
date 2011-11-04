<?php

  /**
   * Testa dosiero por la kondicxo-analizilo.
   *
   * @package aligilo
   * @see kondicxoj.php
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */
$prafix = "..";
require_once($prafix . "/iloj/iloj.php");
require_once($prafix . "/iloj/iloj_kondicxoj.php");

session_start();
malfermu_datumaro();

HtmlKapo();


// testo el novajxgrupo: http://groups.google.com/group/de.comp.lang.php.misc/browse_thread/thread/c724f84c447b5ab1/10e4eff65ddbfc54?show_docid=10e4eff65ddbfc54
//
// $string = 'abcfoobarfoo'; 
// $offset = 3; // entspricht dem ersten "f" 
// echo preg_match( '~foo~A', $string, $data, 0, $offset ); // 1 
// echo preg_match( '~bar~A', $string, $data, 0, $offset ); // 0 
// echo preg_match( '~abc~A', $string, $data, 0, $offset ); // 0
//
// ///


sesio_aktualigu_laux_get();

if ($_POST['teksta_kondicxo']) {
    $teksto = stripslashes($_POST['teksta_kondicxo']);
    $analizilo = new sintaksa_kondicxo_analizilo($teksto);
//     echo "<pre>";
//     var_export($analizilo);
//     echo "</pre>";

    $kondicxo = $analizilo->analizu_kondicxon();
    
    echo "<!--";
    var_export($kondicxo);
    echo "-->";

    if (isset($_SESSION['partopreno'])) {
        eoecho("<p>Rezulto kun " . $_SESSION['partoprenanto']->tuta_nomo() .
            " (#" . $_SESSION['partoprenanto']->datoj['ID'] . ") en " .
            $partopreno_renkontigxo->datoj['mallongigo'] . " (#" .
            $_SESSION['partopreno']->datoj['ID'] . "):</p>");
        echo "<p>" .
            ( kontrolu_kondicxon($kondicxo,
                               $_SESSION['partoprenanto'],
                               $_SESSION['partopreno'],
                               $partopreno_renkontigxo)
              ? "true" : "false" ) .
            "</p>\n";
    }

 }

eoecho( "<h2>Elprovo de kondic^oj</h2>\n");

echo "<form action='kondicxotesto.php' method='POST'>\n<p>";

granda_entajpejo("Kondic^o:<br/>", 'teksta_kondicxo',
                 $teksto, 60, 10);

echo "</p>\n<p>";

send_butono("elprovu");

echo "</p></form>";



HtmlFino();


?>