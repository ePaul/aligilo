<?php

define("DEBUG", true);

  /**
   * ebligas elprovadon de kotizosistemo.
   */
require_once ('iloj/iloj.php');
require_once('iloj/iloj_kotizo.php');
  session_start();
  malfermu_datumaro();

// TODO: pripensu pli bonan rajton
kontrolu_rajton("vidi");

HtmlKapo();

eoecho("<h1>Enspezo-kalkulado</h1>");

if ($_REQUEST['sendu'] == 'kalkulu') {

    $kotizosistemo = new Kotizosistemo($_REQUEST['kotizosistemo']);
    $nia_renkontigxo = new Renkontigxo($_REQUEST['renkID']);

    $cxiuj_datumoj = array();

    $sql = datumbazdemando(array("enoj.ID" => "enoID",
                                 "antoj.ID" => "antoID"),
                           array("partoprenoj" => "enoj",
                                 "partoprenantoj" => "antoj"),
                           array("enoj.partoprenantoID = antoj.ID",
                                 "enoj.renkontigxoID = '" .
                                 $nia_renkontigxo->datoj['ID'] . "'",
                                 "enoj.alvenstato != 'm'" // ne malaligxis
                                 )
                           );
    $rez = sql_faru($sql);
    while($linio = mysql_fetch_assoc($rez)) {
        debug_echo ("<!-- linio: " . var_export($linio, true) . "-->");
        $partopreno = new Partopreno($linio['enoID']);
        $partoprenanto = new Partoprenanto($linio['antoID']);

        
        $datumtenilo = array();
        
        $kalkulilo = new Kotizokalkulilo($partoprenanto, $partopreno,
                                         $nia_renkontigxo, $kotizosistemo);

        $datumtenilo['partakotizo'] = $kalkulilo->partakotizo;
        $datumtenilo['bazakotizo'] = $kalkulilo->bazakotizo;

        // TODO: diversaj pliaj necesaj datumoj, ekzemple kostoj, krompagoj

        $nomo = enkodu_kategoriojn($kalkulilo->kategorioj);

        $cxiuj_datumoj[$nomo][] = $datumtenilo;

    } // while



    debug_echo("<!-- cxiuj_datumoj: " . var_export($cxiuj_datumoj, true) .
               "-->");


    function enspezoprognoza_cxelo($kotizosistemo, $kategorioj, $datumoj)
    {
        $niaj_datumoj = $datumoj[enkodu_kategoriojn($kategorioj)];
        $kotizo = $kotizosistemo->eltrovu_bazan_kotizon($kategorioj);
        $baza_kotizosumo = 0;
        $parta_kotizosumo = 0;
        $nombro_pp = 0;
        if ($niaj_datumoj) {
            foreach($niaj_datumoj AS $ero) {
                $nombro_pp ++;
                if ($ero['bazakotizo'] != $kotizo) {
                    darf_nicht_sein("ero: " . $ero['bazakotizo'] .
                                    ", gxenerale: " . $kotizo);
                }
                $parta_kotizosumo += $ero['partakotizo'];
                $baza_kotizosumo += $ero['bazakotizo'];
            }
        
            $GLOBALS['baza_kotizosumo'] += $baza_kotizosumo;
            $GLOBALS['parta_kotizosumo'] += $parta_kotizosumo;
            $GLOBALS['ppnombro'] += $nombro_pp;
        }
        eoecho("<span class='kotizo'>" . $kotizo . "</span> &times; ");
        eoecho("<span class='pp-nombro'>" . $nombro_pp . "</span><br/>");
        eoecho("<span class='kotizosumo'>" . $baza_kotizosumo . "</span> (");
        eoecho("<span class='partkotizosumo'>" . $parta_kotizosumo .
               "</span>)");

        // TODO: kostoj, krompagoj ktp.
    }



    eoecho("<p>Jen la rezulto de la kalkulado kun kotizosistemo <em>" .
           $kotizosistemo->datoj['nomo'] . "</em> en renkontig^o <em>" .
           $nia_renkontigxo->datoj['mallongigo'] . "</em>:</p>\n");

    erareldono("Atentu: La kalkulado de parttempaj kotizoj ankorau^ ne " .
               "bone funkcias.");

    eoecho("<h2>Detalaj rezultoj</h2>");

    $kotizosistemo->metu_kotizotabelon('enspezoprognoza_cxelo',
                                       $cxiuj_datumoj);

    eoecho("<h2>Sumoj</h2>");
    echo ("<table>\n");
    eoecho("<tr><th>Partoprenantonombro:</th><td>" . $ppnombro .
           "</td></tr>\n");
    eoecho("<tr><th>Sumo de bazaj kotizoj:</th><td>" . $baza_kotizosumo .
           "</td></tr>\n");
    eoecho("<tr><th>Sumo de partaj kotizoj:</th><td>" . $parta_kotizosumo .
           "</td></tr>\n");
    // TODO: kostoj
    echo "</table>";



    eoecho("<h2>Nova kalkulado</h2>");

 }



echo "<form action='enspezokalkulado.php' method='POST'>\n";

eoecho("<p>Jen eblas elprovi kotizosistemojn, kalkulante la enspezojn" .
       "   (kaj estonte ankau^ la elspezojn) por c^iu unuopa".
       "   kotizo-kategorio.</p>");

eoecho("<table>\n<tr><th>Kotizosistemo</th><td>");

elektilo_simpla_db("kotizosistemo",
                   "kotizosistemoj",
                   "nomo", "ID");

eoecho("</td></tr>\n<tr><th>Renkontig^o</th><td>");

elektilo_simpla_db("renkID", "renkontigxo");

eoecho("</td></tr>\n</table>");

echo "<p>"; 
butono("kalkulu", "Kalkulu");
echo "</p>";

echo "</form>";


HtmlFino();