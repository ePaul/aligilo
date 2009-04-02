<?php

  //define("DEBUG", true);

  /**
   * Speciala pagxo por dissendado de la adresaro.
   *
   * @todo: Pripensu igi la retmesagxon multlingva.
   *
   * @package aligilo
   * @subpackage specialaj_pagxoj
   * @author Paul Ebermann
   * @version $Id$
   * @since Revision 180.
   * @copyright 2006-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */




  /**
   * Sendas retpoŝte adresaron al unu persono.
   *
   * @param array $row pliaj informoj uzebla de la ŝablono.
   * @param string $to_name la kompleta nomo de la ricevonto.
   * @param string $to_adres la retpoŝtadreso de la ricevonto.
   */
function sendu_adresaron($row,$to_name,$to_address)
{

    $sxablono = file_get_contents($GLOBALS['prafix'] .
                                  "/sxablonoj/adresaro_retposxto.txt");

    $datumoj =
        array('db' => $row,
              'renk' => $_SESSION['renkontigxo']->datoj,
              'tekstoj' => trovu_necesajn_tekstojn($sxablono,
                                                   "tekstoj.",
                                                   "adr-msgx-"),
              );
        
    if ($row['retposxta_varbado'] != 'u') {
        $kodigo = 'x-metodo';
    }
    else {
        $kodigo  = 'utf-8';
    }

    $teksto = transformu_tekston($sxablono, $datumoj);
  
    $retmesagxo = kreu_auxtomatan_mesagxon();
    $retmesagxo->auxtomata_teksto_estu($teksto, $kodigo,
                                       $_SESSION['kkren']['entajpantonomo']);
    $retmesagxo->aldonu_dosieron_el_disko($GLOBALS['prafix'].
                                          '/dosieroj_generitaj/adresaro.pdf');
    $retmesagxo->ricevanto_estu($to_address, $to_name);
  
    $retmesagxo->temo_estu("Adresaro kaj pliaj informoj pri pasinta " .
                           $_SESSION['renkontigxo']->datoj['mallongigo'] );
    $retmesagxo->eksendu();

    erareldono (" Messag^o sendita! ");

}


$prafix = "..";
require_once ($prafix . "/iloj/iloj.php");
require_once ($prafix . "/iloj/retmesagxiloj.php");
session_start();

malfermu_datumaro();

kontrolu_rajton('retumi');

HtmlKapo('speciala');

die("Vi ne rajtas uzi tiun dosieron. Se vi ne scias, kial, demandu Pauxlon.");

if ($_POST['sendu'] == 'sendu') {
    $komenco = $_POST['komenco'];
    $nombro = $_POST['nombro'];

    echo "<p>\n";

    $demando = datumbazdemando(array("p.ID", "nomo", "personanomo", "retposxto",
                                     "sekso", "retposxta_varbado",
                                     "pn.agxo" ),
                               array("partoprenantoj" => "p", "partoprenoj" => "pn"),
                               array("pn.partoprenantoID = p.ID",
                                     "retposxto <> ''",
                                     "alvenstato = 'a'",
                                     ),
                               "renkontigxoID",
                               array("limit" => "$komenco, $nombro",
                                     "order" => "pn.ID ASC")
                               );
						   
    eoecho( "Demando: [<code>" . $demando . "</code>]<br/>\n");

    eoecho( "dato: " . date("Y-m-d H:i:s") . "<br/>\n");
						   
    $rezulto = sql_faru($demando);

    $i = $komenco;

    while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
        {
            eoecho($i . " " . $row[personanomo]." ".$row[nomo]."<br/>\n");
            $i++;
         
            $to_name = $row[personanomo]." ".$row[nomo];
         
            $to_address = teknika_administranto_retadreso;
            // $to_address = $row['retposxto'];
         
            if($_POST['vere'] == 'jes') {
                sendu_adresaron($row,$to_name,$to_address,$bcc);
            }
         
            flush();
            usleep(200);
        }

    eoecho ("Fino.<br/>\n");
    eoecho ("dato: " . date("Y-m-d H:i:s") . "</p>\n");

 }

echo "<h2>Sendado de adresaroj</h2>";

echo "<form action='sendu_adresaron_gxenerale.php' method='POST'>\n<table>";
tabelentajpejo("Nombro en unu pas^o:", 'nombro', $_POST['nombro'], 10, "", "",
               1);
tabelentajpejo("Komencu c^e:", 'komenco', $i, 10, "", "", 0);
tabel_entajpbutono("", "vere", $_POST['vere'], 'jes',  "vere sendu");
tabel_entajpbutono("", "vere", $_POST['vere'], 'ne', "nur listigu ricevontojn",
                   "kutima");
echo "</table>\n<p>";
butono("sendu", "Sendu");
echo "</p></form>";


HtmlFino();

?>