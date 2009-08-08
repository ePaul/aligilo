<?php

  /**
   * Ilo por sendi serian mesaĝon al ĉiuj aliĝintoj.
   *
   *
   * @package aligilo
   * @subpackage specialaj_pagxoj
   * @author Paul Ebermann
   * @version $Id$
   * @since Revision 329.
   * @copyright 2006-2009 Paul Ebermann (ekde aŭgusto 2009 en
   *                       sendu_adresaro_anoncon.php, antaŭe ekde 
   *                       aprilo 2009 en sendu_informmesagxojn.php,
   *                       antaŭe en sendu_adresaron_gxenerale.php).
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */




/*
         1         2         3         4         5         6         7         8
12345678901234567890123456789012345678901234567890123456789012345678901234567890
*/


  /**
   */


$prafix = "..";
require_once ($prafix . "/iloj/iloj.php");
require_once ($prafix . "/iloj/retmesagxiloj.php");
require_once ($prafix . "/tradukendaj_iloj/iloj_konfirmilo.php");
require_once ($prafix . "/iloj/diversaj_retmesagxoj.php");
session_start();

malfermu_datumaro();
kontrolu_rajton("retumi");

HtmlKapo('speciala');

die("Vi ne rajtas uzi tiun dosieron. Se vi ne scias, kial, demandu Paŭlon.");



if ($_POST['sendu'] == 'sendu') {
    $komenco = $_POST['komenco'];
    $nombro = $_POST['nombro'];

    echo "<p>\n";

    $demando = datumbazdemando(array("p.ID" => "antoID",
                                     "pn.ID" => "enoID"),
                               array("partoprenantoj" => "p",
                                     "partoprenoj" => "pn"),
                               array("pn.partoprenantoID = p.ID",
                                     "p.retposxto <> ''",
									 "pn.alvenstato" => 'a',
                                     ),
                               "renkontigxoID",
                               array("limit" => "$komenco, $nombro",
                                     "order" => "pn.ID ASC")
                               );
						   
    eoecho( "Demando: [<code>" . $demando . "</code>]</p><p>\n");

    eoecho( "dato: " . date("Y-m-d H:i:s") . "<br/>\n");
						   
    $rezulto = sql_faru($demando);

    $i = $komenco;

    while ($row = mysql_fetch_assoc($rezulto))
        {
            $anto = new Partoprenanto($row['antoID']);
            $eno = new Partopreno($row['enoID']);
            eoecho($i . " " . $anto->tuta_nomo()."<br/>\n");
            $i++;
			/*
			  if($_POST['vere'] == 'jes') */ {
                sendu_adresaro_demandon($anto,
										$eno,
										$_SESSION['renkontigxo'],
										"seria sendilo (" . $kkren['entajpantonomo'].")",
										$_POST['vere']=="jes");
            }
         
            flush();
            usleep(200);
        }

    eoecho ("Fino.<br/>\n");
    eoecho ("dato: " . date("Y-m-d H:i:s") . "</p>\n");

 }



eoecho("<h2>Sendado de informmesag^oj</h2>");

echo "<form action='" . $_SERVER['PHP_SELF'] . "' method='POST'>\n<table>";
tabelentajpejo("Nombro en unu pas^o:", 'nombro', $_POST['nombro'], 10, "", "",
               1);
tabelentajpejo("Komencu c^e:", 'komenco', $i, 10, "", "", 0);
tabel_entajpbutono("", "vere", $_POST['vere'], 'jes',  "vere sendu");
tabel_entajpbutono("", "vere", $_POST['vere'], 'ne', "sendu nur al kopio-ricevantoj",
                   "kutima");
echo "</table>\n<p>";
butono("sendu", "Sendu");
echo "</p></form>";

HtmlFino();