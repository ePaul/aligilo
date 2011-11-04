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
    $kostosistemo = new Kostosistemo($_REQUEST['kostosistemo']);
    $kostokalkulilo = new Kostokalkulilo($kostosistemo, $nia_renkontigxo);


    $cxiuj_datumoj = array();
    $mankajxoj = array();

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
    $nombro = 0;
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

        $datumtenilo['kostoj'] =
            $kostokalkulilo->kalkulu_personkostojn($partoprenanto, $partopreno, $nia_renkontigxo);

        if ($kalkulilo->kategorioj_kompletaj())
            {
                $nomo = enkodu_kategoriojn($kalkulilo->kategorioj);
                $cxiuj_datumoj[$nomo][] = $datumtenilo;
            }
        else
            {
                $mankajxoj[] = $kalkulilo;
            }
        $nombro ++;

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
        eoecho("<span class='pp-nombro'>" . $nombro_pp . "</span> &times; ");
        eoecho("<span class='kotizo'>" . number_format($kotizo) .
               "</span><br/>");
        eoecho("<span class='kotizosumo'>" . $baza_kotizosumo . "</span> (");
        eoecho("<span class='partkotizosumo'>" . $parta_kotizosumo .
               "</span>)");

        // TODO: kostoj, krompagoj ktp.
    }

    function elspezoprognoza_cxelo($kotizosistemo, $kategorioj, $datumoj) {
        $niaj_datumoj = $datumoj[enkodu_kategoriojn($kategorioj)];
        // TODO
    }



    eoecho("<p>Jen la rezulto de la kalkulado kun kotizosistemo <em>" .
           $kotizosistemo->datoj['nomo'] . "</em> en renkontig^o <em>" .
           $nia_renkontigxo->datoj['mallongigo'] . "</em> (entute " .
           $nombro . " partoprenoj):</p>\n");


    if (count($mankajxoj) > 0) {
        erareldono("la sekvaj kotizokalkuladoj ne funkciis pro".
                   " nekompletaj kategorioj!");
        echo "<pre>";
        var_export($mankajxoj);
        echo "</pre><hr/>\n";
        
    }
    //    erareldono("Atentu: La kalkulado de parttempaj kotizoj ankorau^ ne " .
    //               "bone funkcias.");

    echo "<div style='display:none;'>\n";

    eoecho("<h2>Detalaj rezultoj</h2>\n");
    

    $kotizosistemo->metu_kotizotabelon('enspezoprognoza_cxelo',
                                       $cxiuj_datumoj);

    echo "</div>\n";

    eoecho("<h2>Sumoj</h2>\n");
    echo ("<table>\n");
    eoecho("<tr><th>Partoprenantonombro:</th><td>" . $ppnombro .
           "</td></tr>\n");
    eoecho("<tr><th>Sumo de bazaj kotizoj:</th><td>" . $baza_kotizosumo .
           "</td></tr>\n");
    eoecho("<tr><th>Sumo de partaj kotizoj:</th><td>" . $parta_kotizosumo .
           "</td></tr>\n");
    // TODO: kostoj
    echo "</table>\n";

    echo "<hr/>\n";


    $ppnombro = 0;
    $baza_kotizosumo = 0;
    $parta_kotizosumo = 0;


    function partoprenantonombra_cxelo($kotizosistemo, $kategorioj, $datumoj) {
        $niaj_datumoj = $datumoj[enkodu_kategoriojn($kategorioj)];
        $nombro_pp = count($niaj_datumoj);
        $GLOBALS['ppnombro'] += $nombro_pp;

        eoecho("<span class='pp-nombro'>" . $nombro_pp . "</span>");
    }

    function partkotiza_cxelo($kotizosistemo, $kategorioj, $datumoj) {
        $niaj_datumoj = $datumoj[enkodu_kategoriojn($kategorioj)];
        $parta_kotizosumo = 0;
        if ($niaj_datumoj) {
            foreach($niaj_datumoj AS $ero) {
                $parta_kotizosumo += $ero['partakotizo'];
            }
            $GLOBALS['parta_kotizosumo'] += $parta_kotizosumo;
        }
        eoecho($parta_kotizosumo);
    }



    eoecho("<h2>En unuopaj tabeloj</h2>\n");

    echo "<div class='tabeloj-apudaj'>\n";

    $kotizosistemo->metu_kotizotabelon('partoprenantonombra_cxelo',
                                       $cxiuj_datumoj, "Partoprenantonombroj");

    $kotizosistemo->metu_kotizotabelon('simpla_kotizocxelo',
                                       $cxiuj_datumoj, "Kotizoj");

    $kotizosistemo->metu_kotizotabelon('partkotiza_cxelo',
                                       $cxiuj_datumoj, "Sumoj de (eble parttempaj) kotizoj");


    echo "</div>\n";

    eoecho("<h2>Sumoj</h2>\n");
    echo ("<table>\n");
    eoecho("<tr><th>Partoprenantonombro:</th><td>" . $ppnombro .
           "</td></tr>\n");
    //    eoecho("<tr><th>Sumo de bazaj kotizoj:</th><td>" . $baza_kotizosumo .
    //           "</td></tr>\n");
    eoecho("<tr><th>Sumo de partaj kotizoj:</th><td>" . $parta_kotizosumo .
           "</td></tr>\n");
    // TODO: kostoj
    echo "</table>\n";


    echo "<hr/>\n";


    eoecho("<h2>Nova kalkulado</h2>");

 }



echo "<form action='enspezokalkulado.php' method='POST'>\n";

eoecho("<p>Jen eblas elprovi kotizosistemojn, kalkulante la enspezojn" .
       "   (kaj estonte ankau^ la elspezojn) por c^iu unuopa".
       "   kotizo-kategorio.</p>");

eoecho("<table>\n");
tabela_elektilo_db("Kotizosistemo",
                   'kotizosistemo',
                   "kotizosistemoj");

tabela_elektilo_db("Kostosistemo",
                   'kostosistemo',
                   "kostosistemoj");


tabela_elektilo_db("Renkontig^o",
                   'renkID',
                   "renkontigxo");


eoecho("</table>");

echo "<p>"; 
butono("kalkulu", "Kalkulu");
echo "</p>";

echo "</form>";


HtmlFino();