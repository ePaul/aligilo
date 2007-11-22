<?php


require_once($prafix . '/iloj/iloj_kotizo_kategorioj.php');
require_once($prafix . '/iloj/iloj_kotizo_krompagoj.php');
require_once($prafix . '/iloj/iloj_kostoj.php');

  /**
   * Nova konfigurebla kotizosistemo.
   * 
   * Kotizo-datumoj:
   * - landokategorioj
   * - agxkategorioj
   * - logxkategorioj (junulargastejo/amaslogxejo/...)
   *    (- mangxado (aparte aux kun logxado))
   * - aligxtempo-kategorioj (kun limdatoj)
   *
   * - kotizoj por cxiuj eblecoj (4/5-dimensia tabelo, sxajne)
   *
   * La celo estas, ke oni (la decidanto) povu simple krei novan
   * kotizosistemon kaj elprovi ĝiajn efikon je ekzistantaj
   * partopreno-datumoj.
   * Kune kun apartaj difinoj de kostoj eblos prognosi la financan
   * rezulton de renkontigxo, kaj analizi profitodonajn kaj
   * malprofitodonajn partoprenantajn grupojn.
   */


  /*
   * kotizosistemo:
   *   - ID
   *   - nomo
   *   - priskribo
   *   - entajpanto
   *   - landokategorisistemo
   *   - logxkategorisistemo
   *   - agxkategorisistemo
   *   - aligxkategorisistemo
   *   - parttempdivisoro
   *
   *
   * kotiztabelero:
   *  - kotizosistemo
   *  - aligxkategorio
   *  - landokategorio
   *  - agxkategorio
   *  - logxkategorio
   *  - kotizo
   *
   *
   * minimumaj_antauxpagoj:
   *  - kotizosistemo
   *  - landokategorio
   *  - oficiala_antauxpago   (tion ni oficiale postulas)
   *  - interna_antauxpago    (per tiu ni kalkulas)
   */
class Kotizosistemo extends Objekto {


    var $krompagolisto;

    function Kotizosistemo($id=0)
    {
        $this->Objekto($id, "kotizosistemoj");
    }

    /**
     * donas liston de cxiuj krompagoj, kiuj estas relevantaj
     * en tiu cxi kotizosistemo.
     * redonas:
     *  array()  el elementoj de la formo
     *     array('tipo' =>       Krompagotipo-objekto
     *           'krompago' =>   la krompago en cxi tiu kotizosistemo.
     */
    function donu_krompagoliston()
    {
        if($this->krompagolisto)
            {
                return $this->krompagolisto;
            }
        $listo = array();
        $sql = datumbazdemando(array("tipo", "krompago"),
                               "krompagoj",
                               "kotizosistemo = '{$this->datoj['ID']}'");
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            $listo[] = array('tipo' => new Krompagotipo($linio['tipo']),
                             'krompago' => $linio['krompago']);
        }
        $this->krompagolisto = $listo;
        return $listo;
    }


    /**
     * redonas la kategorisistemon de tipo $tipo
     * de tiu cxi kotizosistemo.
     */
    function donu_kategorisistemon($tipo) {
        return donu_katsistemon($this->datoj[$tipo."kategorisistemo"],
                                $tipo);
    }

    /**
     * kalkulas la bazan kotizon, se tiu cxi kotizosistemo estus
     * relevanta.
     *
     * $partoprenanto
     * $partopreno
     * $renkontigxo
     */
    function kalkulu_bazan_kotizon($partoprenanto, $partopreno, $renkontigxo)
    {
        $kategorioj = $this->eltrovu_kategoriojn($partoprenanto,
                                                 $partopreno,
                                                 $renkontigxo);
        return
            $this->eltrovu_bazan_kotizon($kategorioj);
    }

    /*
     * redonas  array() en la formo
     *               agx => ...,   
     *               lando => ..., 
     *               logx => ...,  
     *               aligx => ...,
     *  kie cxiu valoro estas
     *        array('ID' => identifikilo de la kategorio,
     *              'kialo' => iu teksto aux array(de => ..., eo => ...)).
     *
     *  eltrovas la kategoriojn por iu partoprenanto en iu renkontigxo.
     */
    function eltrovu_kategoriojn($partoprenanto, $partopreno, $renkontigxo)
    {
        debug_echo("<!-- kotizosistemo->eltrovukategoriojn(" . $partoprenanto . ", "
             . $partopreno . ", " . $renkontigxo . ") -->");
             
        $kategorioj = array();
        foreach ($GLOBALS['kategoriotipoj'] AS $tipo) {
            $katsistemo = $this->donu_kategorisistemon($tipo);
            debug_echo( "<!-- katsistemo[$tipo]: " . var_export($katsistemo, true) . "-->");
            $kategorioj[$tipo] =
                $katsistemo->trovu_kategorion($partoprenanto, $partopreno,
                                              $renkontigxo, $this,
                                              $kategorioj);
        }
        return $kategorioj;
    }

    /**
     * eltrovas la minimumajn antauxpagojn por iu landokategorio.
     * 
     * $landokategorioID - identigilo de la landokategorio.
     *
     * redonas
     *     array('oficiala_antauxpago' => ...,
     *           'interna_antauxpago' => ...)
     */
    function minimumaj_antauxpagoj($landokategorioID) {
        $sql = datumbazdemando(array("oficiala_antauxpago",
                                     "interna_antauxpago"),
                               "minimumaj_antauxpagoj",
                               array("kotizosistemo = " . $this->datoj['ID'],
                                     "landokategorio = " . $landokategorioID));
        return mysql_fetch_assoc(sql_faru($sql));
    }


    /*
     * $kategorioj  array() en la formo
     *               agx => ...,   // id de agxkategorio
     *               lando => ..., // id de landokategorio
     *               logx => ...,  // id de logxkategorio
     *               aligx => ..., // id de aligxkategorio
     * aux kie la valoroj (por samaj sxlosiloj) estas de la
     * formo
     *         array(ID => ..., ...).
     *
     *  eltrovas kaj redonas la bazan kotizon por tiu kategorio.
     */
    function eltrovu_bazan_kotizon($kategorioj) {
        $restriktoj = array("kotizosistemo = '" . $this->datoj['ID'] . "'");
        foreach($kategorioj as $nomo => $id) {
            if (is_array($id)) {
                $id = $id['ID'];
            }
            $restriktoj[]= "{$nomo}kategorio = '{$id}'";
        }
        $rez = sql_faru(datumbazdemando('kotizo',
                                        'kotizotabeleroj',
                                        $restriktoj));
        $linio = mysql_fetch_assoc($rez);
        if ($linio) {
            return $linio['kotizo'];
        }
        else {
            return "";
        }
        
    }



    /************ formatado de la kotizotabelo *************
     *
     * de ekstere oni voku nur metu_kotizotabelon().
     **/


    /**
     * $elementa_funkcio  - funkcio, kiu faru la veran eldonon - vidu
     *                       metu_kotizotabelon() por priskribo.
     * $identigiloj -  array() en la formo
     *               agx => ...,   // id de agxkategorio
     *               lando => ..., // id de landokategorio
     *               logx => ...,  // id de logxkategorio
     *               aligx => ..., // id de aligxkategorio
     * 
     */
    function metu_kotizocxelon($elementa_funkcio, $identigiloj, $aldone) {
        echo "<td>";
        $elementa_funkcio($this, $identigiloj, $aldone);
        echo "</td>";
    }

    /**
     */
    function metu_bazan_kotizolinion($redaktebla, $tipo,
                                     $identigiloj, $titolo, $aldone)
    {
        eoecho("<tr><th>" . $titolo . "</th>");
        $rez =
            sql_faru(datumbazdemando("ID",
                                     $tipo . "kategorioj",
                                     "sistemoID = '".
                                     $this->datoj[$tipo.'kategorisistemo']."'",
                                     "",
                                     array("order" => "ID")));
        while($linio = mysql_fetch_assoc($rez)) {
            $identigiloj[$tipo] = $linio['ID'];
            $this->metu_kotizocxelon($redaktebla, $identigiloj, $aldone);
        }
        echo "</tr>\n";
    }

    function metu_kotizotitollinion($tipo)
    {
        eoecho("<tr><td/>");
        $rez =
            sql_faru(datumbazdemando("nomo",
                                     $tipo . "kategorioj",
                                     "sistemoID = '".
                                     $this->datoj[$tipo.'kategorisistemo']."'",
                                     "",
                                     array("order" => "ID")));
        while($linio = mysql_fetch_assoc($rez)) {
            eoecho("<th>" . $linio['nomo'] . "</th>");
        }
        echo "</tr>\n";
    }

    function metu_bazan_kotizotabelon($redaktebla, $tipoj, $identigiloj,
                                      $aldone, $titolo)
    {
        $tipo = array_pop($tipoj);
        $sekva_tipo = array_pop($tipoj);
        if(count($tipoj)) {
            darf_nicht_sein("pli ol 0: " . count($tipoj));
        }
        eoecho ("<table class='kotizotabelo-baza'><caption>".
                $titolo . "</caption>");
        $this->metu_kotizotitollinion($sekva_tipo);
        $rez =
            sql_faru(datumbazdemando(array("nomo", 'ID'),
                                     $tipo . "kategorioj",
                                     "sistemoID = '".
                                     $this->datoj[$tipo.'kategorisistemo']."'",
                                     "",
                                     array("order" => "ID")));
        while($linio = mysql_fetch_assoc($rez)) {
            $identigiloj[$tipo] = $linio['ID'];
            $this->metu_bazan_kotizolinion($redaktebla, $sekva_tipo,
                                           $identigiloj, $linio['nomo'],
                                           $aldone);
        }
        
        echo "</table>";
    }

    function metu_grandan_kotizolinion($redaktebla, $tipoj, $identigiloj,
                                       $aldone) {
        $tipo = array_pop($tipoj);
        echo "<tr>";
        $rez =
            sql_faru(datumbazdemando(array("nomo", 'ID'),
                                     $tipo . "kategorioj",
                                     "sistemoID = '".
                                     $this->datoj[$tipo.'kategorisistemo']."'",
                                     "",
                                     array("order" => "ID")));
        while($linio = mysql_fetch_assoc($rez)) {
            echo "<td>";
            /*            eoecho( $linio['nomo']); */
            $identigiloj[$tipo] = $linio['ID'];
            $this->metu_bazan_kotizotabelon($redaktebla, $tipoj,
                                            $identigiloj, $aldone,
                                            $linio['nomo']);
            eoecho("</td>");
        }
        
        echo "</tr>\n";
    }

    function metu_grandan_kotizotabelon($redaktebla,$tipoj, $aldone, $titolo) {
        $identigiloj = array();
        $tipo = array_pop($tipoj);
        $sekva_tipo = end($tipoj);
        echo "<table class='kotizotabelo-granda'>\n";
        if ($titolo) {
            eoecho("<caption>" . $titolo . "</caption>\n");
        }
        $rez = sql_faru(datumbazdemando(array("count(ID)" => "nombro"),
                                        $sekva_tipo . "kategorioj",
                                        "sistemoID = '".
                                        $this->datoj[$sekva_tipo .
                                                     'kategorisistemo']."'"
                                        ));
        $linio = mysql_fetch_assoc($rez);
        $sekvaj = $linio['nombro'];

        $rez =
            sql_faru(datumbazdemando(array("nomo", 'ID'),
                                     $tipo . "kategorioj",
                                     "sistemoID = '".
                                     $this->datoj[$tipo.'kategorisistemo']."'",
                                     "",
                                     array("order" => "ID")));
        while($linio = mysql_fetch_assoc($rez)) {
            echo "<tr><th colspan='{$sekvaj}'>";
            eoecho( $linio['nomo']);
            echo "</th></tr>\n";
            $identigiloj[$tipo] = $linio['ID'];
            $this->metu_grandan_kotizolinion($redaktebla, $tipoj,
                                             $identigiloj, $aldone);
        }
        echo "</table>\n";
    }

    

    /**
     * kreas tabelon de cxiuj bazaj kotizoj.
     *
     * $elemtenta_funkcio:
     *       nomo de funkcio vokota por cxiu cxelo.
     *     Tiu funkcio havu la formon
     *        function elementa_funkcio($kotizosistemo,
     *                                  $kategorioj,
     *                                  $aldonaj_datumoj)
     *     $kotizosistemo estas la kotizosistemo-objekto,
     *     $kategorioj estas array() de la kategorioj-identigiloj
     *                 (laux tipoj)
     *     $aldonaj_datumoj - iuj aldonaj datoj, kiujn ricevis tiu cxi
     *                    funkcio.
     * $aldonaj_datumoj -
     *    iuj ajn datumoj, kiuj estos pludonotaj al la
     *    elementa funkcio por uzi ilin.
     */
    function metu_kotizotabelon($elementa_funkcio, $aldonaj_datumoj="", $titolo="") {
        $this->metu_grandan_kotizotabelon($elementa_funkcio,
                                          $GLOBALS['kategoriotipoj'],
                                          $aldonaj_datumoj, $titolo);
    }
    

}  // class kotizosistemo

    /************* helpaj funkcioj **********/


function entajpa_kotizocxelo($kotizosistemo, $kategorioj) {
    $kotizo = $kotizosistemo->eltrovu_bazan_kotizon($kategorioj);
    $nomo = "kotizo[". enkodu_kategoriojn($kategorioj) . "]";
    simpla_entajpejo("", $nomo, $kotizo, 5);
}

function simpla_kotizocxelo($kotizosistemo, $kategorioj) {
    // fortrancxas la post-komajn ciferojn!
    echo number_format($kotizosistemo->eltrovu_bazan_kotizon($kategorioj));
}

/**************************************************************************/


/**
 * Anstatauxajxo por Kotizo (en konfiguro/objektoj_kotizo.php).
 *
 * Kalkulas kotizon, rabatojn, krompagojn, antauxpagojn, kaj
 * pagorestajxojn.
 */
class Kotizokalkulilo {

    var $partoprenanto, $partopreno, $renkontigxo, $kotizosistemo;

    var $kategorioj = array();
    var $bazakotizo = 0, $partakotizo = 0;

    var $partoprennoktoj, $partoprentempo;
    var $surlokaj_pagoj = 0, $antauxpagoj = 0, $postpagoj = 0, $pagoj = 0;

    var $diversaj_rabatoj = 0, $tejo_rabato = 0, $rabatoj = 0;

    var $krompagolisto = array(),
        $krompagoj_diversaj = 0,
        $krom_loka_membrokotizo = 0,
        $krom_nemembro = 0,
        $krom_tejo_membrokotizo = 0,
        $krompagoj = 0;

    var $pagenda;
    
    /**
     *
     */
    function Kotizokalkulilo($partoprenanto, $partopreno,
                             $renkontigxo, $kotizosistemo)
    {
        $this->partoprenanto = $partoprenanto;
        $this->partopreno = $partopreno;
        $this->renkontigxo = $renkontigxo;
        $this->kotizosistemo = $kotizosistemo;

        $this->kategorioj =
            $kotizosistemo->eltrovu_kategoriojn($partoprenanto,
                                                $partopreno,
                                                $renkontigxo);
        $this->bazakotizo =
            $kotizosistemo->eltrovu_bazan_kotizon($this->kategorioj);

        $this->kalkulu_parttempan_kotizon();


        $this->kalkulu_pagojn();
        $this->kalkulu_rabatojn();
        $this->kalkulu_krompagojn();

        $this->pagenda =
            $this->partakotizo + $this->krompagoj
            - $this->rabatoj - $this->pagoj;
    }


    /****************** internaj funkcioj de la kotizokalkulilo **********/
    

    function kalkulu_parttempan_kotizon()
    {
        if ($this->partopreno->datoj['partoprentipo'] == 't') {
            $this->partakotizo = 
                $this->bazakotizo;
            $this->partoprennoktoj =
                kalkulu_tagojn($this->renkontigxo->datoj['de'],
                               $this->renkontigxo->datoj['gxis']);
            $this->partoprentempo= array('eo' => "tuttempa",
                                        'de' => "Vollzeit");
        }
        else {
            // partotempa partopreno
            $this->partoprennoktoj =
                kalkulu_tagojn($this->partopreno->datoj['de'],
                               $this->partopreno->datoj['gxis']);

            $this->partoprentempo =
                array('eo' => "parttempa (" .$this->partoprennoktoj . " n-oj)",
                      'de' => "Teilzeit (" .$this->partoprennoktoj . " N.)");

            

            // la magia formulo
            // TODO: eble la "cxu floor" estu ankaux konfigurebla.
            $this->partakotizo = floor(
                $this->bazakotizo
                * $this->partoprennoktoj
                / $this->kotizosistemo->datoj['parttempdivisoro']);

            // sed ne pagu pli ol la bazan kotizon!
            if ($this->partakotizo > $this->bazakotizo) {
                $this->partakotizo = $this->bazakotizo;
            }
        }
        
    }


    function kalkulu_rabatojn() {
        $ppID = $this->partopreno->datoj['ID'];

        // diversaj rabatoj
        $sql = datumbazdemando(array("SUM(kvanto)" => "num"),
                               "rabatoj",
                               "partoprenoID = '$ppID'");
        $linio = mysql_fetch_assoc(sql_faru($sql));
        if ($linio) {
            $this->diversaj_rabatoj = $linio['num'];
        }
        // TEJO-rabato

        switch($this->partopreno->datoj['tejo_membro_kontrolita'])
            {
            case 'i':
            case 'j':
                $this->tejo_rabato = TEJO_RABATO;
            }

        $this->rabatoj = $this->tejo_rabato + $this->diversaj_rabatoj;
    }


    function kalkulu_pagojn()
    {
        $de = $this->renkontigxo->datoj['de'];
        $gxis = $this->renkontigxo->datoj['gxis'];
        $ppID = $this->partopreno->datoj['ID'];

        // surlokaj pagoj:
        $sql = datumbazdemando(array("SUM(kvanto)" => "num"),
                               "pagoj",
                               array("'$de' <= dato", "dato <= '$gxis'",
                                     "partoprenoID = '$ppID'" ));
        $linio = mysql_fetch_assoc(sql_faru($sql));
        debug_echo( "<!-- surlokaj: " . $linio['num'] . "-->");
        $this->surlokaj_pagoj =
            $linio ? $linio['num'] : 0;
        // antauxpagoj
        $sql = datumbazdemando(array("SUM(kvanto)" => "num"),
                               "pagoj",
                               array("dato < '$de'",
                                     "partoprenoID = '$ppID'" ));
        $linio = mysql_fetch_assoc(sql_faru($sql));
        debug_echo ("<!-- antauxaj: " . $linio['num'] . "-->");
        $this->antauxpagoj =
            $linio ? $linio['num'] : 0;
        // postaj pagoj
        $sql = datumbazdemando(array("SUM(kvanto)" => "num"),
                               "pagoj",
                               array("'$gxis' < dato",
                                     "partoprenoID = '$ppID'" ));
        $linio = mysql_fetch_assoc(sql_faru($sql));
        debug_echo ("<!-- postaj: " . $linio['num'] . "-->");
        $this->postpagoj = 
            $linio ? $linio['num'] : 0;
        // cxiuj pagoj
        $this->pagoj = $this->antauxpagoj + $this->surlokaj_pagoj
            + $this->postpagoj;
        
    }


    function kalkulu_krompagojn()
    {
        $this->kalkulu_diversajn_krompagojn();
        $this->kalkulu_lokaasociopagon();
        $this->kalkulu_tejo_kotizon();

        $this->krompagoj =
            $this->krompagoj_diversaj +
            $this->krom_loka_membrokotizo +
            $this->krom_nemembro +
            $this->krom_tejo_membrokotizo;

    }

    function kalkulu_diversajn_krompagojn()
    {
        $krompagoj = array();
        $sumo = 0;
        $krompagolisto = $this->kotizosistemo->donu_krompagoliston();
        //    debug_echo("<pre> krompagolisto: " . var_export($krompagolisto, true) . "</pre>");
        foreach($krompagolisto AS $ero) {
            if($ero['tipo']->aplikigxas($this->partoprenanto,
                                        $this->partopreno,
                                        $this->renkontigxo,
                                        $this)) {
                debug_echo ("<!-- aplikigxas: <em>" . $ero['tipo']->datoj['nomo'] . " (" . $ero['krompago'] . ")</em> -->");
                if ($ero['tipo']->datoj['lauxnokte'] == 'j') {
                    $kp = $ero['krompago'] * $this->partoprennoktoj;
                    debug_echo ("<!-- * " . $this->partoprennoktoj . " = " . $kp . "-->");
                }
                else {
                    $kp = $ero['krompago'];
                }
                $krompagoj[] = array('tipo' => $ero['tipo']->datoj['nomo'],
                                     'krompago' => $kp);
                $sumo += $kp;
            }
        }
        $this->krompagolisto = array_merge($this->krompagolisto,
                                           $krompagoj);
        $this->krompagoj_diversaj = $sumo;
    }

    function kalkulu_lokaasociopagon()
    {
        switch($this->partopreno->datoj['surloka_membrokotizo']) {
        case 'j':
        case 'i':
            $this->krom_loka_membrokotizo =
                $this->partopreno->datoj['membrokotizo'];
            $this->krompagolisto[]=
                array('tipo'=>"membrokotizo por ". deviga_membreco_nomo,
                      'krompago' => $this->partopreno->datoj['membrokotizo']);
            break;
        case 'k':
            $this->krom_nemembro =
                $this->partopreno->datoj['membrokotizo'];
            $this->krompagolisto[]=
                array('tipo' => "krompago por nemembro de "
                      .          deviga_membreco_nomo,
                      'krompago' => $this->partopreno->datoj['membrokotizo']);
            break;
        }
    }
    function kalkulu_tejo_kotizon()
    {
        debug_echo( "<!-- TEJO-kotizo? -->");
        if ($this->partopreno->datoj['tejo_membro_kontrolita'] == 'i')
            {
                $this->krom_tejo_membrokotizo =
                    $this->partopreno->datoj['tejo_membro_kotizo'];
                $this->krompagolisto[]=
                    array('tipo' => "TEJO-membrokotizo",
                          'krompago'
                          => $this->partopreno->datoj['tejo_membro_kotizo']);
                debug_echo( "<!-- jes! krompagolisto: ". var_export($this->krompagolisto, true) . "-->");
            }
    }


    /*
    function tkampo($tipo, $titolo, $teksto, $mono, $pdf="") {
        switch($tipo)
            {
            case 0:
                eoecho("<tr><th>" . $titolo . "</th><td>" . $teksto .
                       "</td><td>");
                if (is_numeric($mono)) {
                    eoecho( number_format($mono, 2, ",", "") . " E^");
                }
                else {
                    eoecho ($mono);
                }
                echo "</td></tr>";
                break;
            case 1:
                // TODO
            case 2:
            case 3:
                // TODO
            }
    }
    */


    /**
     * $linio - array():
     *           [0] => iu teksto
     *           [1] => teksto aux nombro - se nombro, gxi estos formatita kiel mono
     *           [3] => (eble) plia nombro - estos formatita kiel mono kun + aux -.
     */
    function html_formatu_linireston($linio) {
        $rez = "";
        if (DEBUG) {
            $rez .= "<!-- html_formatu_linireston(" . var_export($linio, true) . "-->";
        }

        $prefikso = $postfikso = "";
        if ($linio['grava']) {
            $prefikso = "<strong>";
            $postfikso = "</strong>";
        }
        foreach($linio AS $index => $cxelo) {
            if (! is_int($index))
                continue;
            if (is_array($cxelo)) {
                $cxelo = $cxelo['eo'];
            }
            if (is_numeric($cxelo)) {
                $cxelo = number_format($cxelo, 2, ".", "") . " E^";
            }
            $rez .= "<td>" . $prefikso .$cxelo . $postfikso. "</td>";
        }
        return $rez;

        $rez .= "<td>" . $linio[0]['eo'] . "</td><td>";
        if (is_numeric($linio[1])) {
            $rez .= number_format($linio[1], 2, ".", "") . " E^</td>";
        }
        else {
            $rez .= $linio[1] . "</td>";
        }
        if (isset($linio[2])) {
            $nombro = number_format($linio[2], 2, ".", "");
            if ($nombro[0]!= '-') {
                $nombro = '+ ' . $nombro;
            }
            $rez .= "<td>" . $nombro . " E^</td>";
        }
        return $rez;
    }


    /**
     * formatas tabelon kreitan en montru_kotizon().
     *  $tabelo
     *    array() el linioj, kiuj po havas la formon
     *       array('titolo' => titolo de linigrupo,
     *             'enhavo' => array() el unu gxis pluraj
     *                         du- aux tri-elementaj array()-oj,
     *                         kiuj po enhavas la enhavon de unu
     *                         linio laux kampoj.
     *  $tipo
     *    0 - HTML-tabelo
     *    3 - al PDF-objekto (por dua konfirmilo, en eo.)
     *    4 - al PDF-objekto (por dua konfirmilo, en de.)
     *   estonte:
     *      1 - teksta tabelo
     *      2 - al PDF-objekto (por la akceptofolio)
     * 
     *  $helpilo  - uzata por meti tien la tabelon - en kazoj 2 kaj 3.
     */
    function formatu_tabelon($tabelo, $tipo, &$helpilo) {
        debug_echo("<!-- tipo: " . $tipo . "--> ");
        debug_echo( "<!-- tabelo: " .  var_export($tabelo, true ) .
                    "-->");
        switch($tipo)
            {
            case 0:
                $html = "<table class='rezulto'>\n";
                foreach($tabelo AS $linio) {
                    $titolo = $linio['titolo'];
                    if (is_array($titolo)) {
                        $titolo = $titolo['eo'];
                    }
                    $enhavo = $linio['enhavo'];
                    $html .= "<tr><th rowspan='" . count($enhavo) . "'>" . $titolo . "</th>";
                    $html .= $this->html_formatu_linireston(array_shift($enhavo)) . "</tr>\n";
                    foreach($enhavo AS $linio) {
                        $html .= "<tr>" . $this->html_formatu_linireston($linio) . "</tr>\n";
                    }
                }
                $html .= "</table>\n";
                eoecho($html);
                break;
            case 1:
                // TODO
                break;
            case 2:
                // TODO
            case 3:
            case 4:
                $tuta_largxeco = 18;
                $largxecoj = array('titolo'=>25, 25, 25, 23);
                $alinadoj = array('L', 'R', 'R');
                $alteco = 4;
                $pdf =& $helpilo->pdf;
                $lingvo = ($tipo == 4 ? 'de' : 'eo');
                $grandaj_linioj = 0;
                $maks_grandaj_linioj = count($tabelo) - 1;
                $kadro = 0;
                $pdf->setFontSize(9);
                
                
                
                foreach($tabelo AS $granda_linio) {
                    if ($grandaj_linioj > 0) {
                        // supra linio
                        $kadro = "T";
                    }
                    $pdf->setFont('', 'B');
                    $pdf->cell($largxecoj['titolo'],
                               $alteco,
                               $helpilo->dulingva($granda_linio['titolo']['eo'],
                                                  $granda_linio['titolo']['de'],
                                                  $lingvo),
                               $kadro);
                    $pdf->setFont('', '');
                    $lasta = count($granda_linio['enhavo'])-1;
                    foreach($granda_linio['enhavo'] AS $linIndex => $linio) {

                        if ($linio['grava']) {
                            // dika tiparo
                            $pdf->setFont('', 'B');
                        }

                        for($index = 0; $index < 3; $index++) {
                            $cxelo = $linio[$index];
                            //                        foreach($linio AS $index => $cxelo) {
                            //                            if (!is_int($index))
                            //                                continue;
                            debug_echo ("<!-- (" . $index . ": " .
                                        var_export($cxelo, true) . ") -->");
                            if (is_array($cxelo)) {
                                $teksto = $helpilo->dulingva($cxelo['eo'],
                                                             $cxelo['de'],
                                                             $lingvo);
                                $allineado = "L";
                            }
                            else if (is_numeric($cxelo)) {
                                $teksto =
                                    number_format($cxelo, 2) .
                                    $helpilo->trans_eo(" E^");
                                $allineado = "R";
                                if ($index == 2) {
                                    if ($teksto[0] == '-') {
                                        // aldonu spaceton
                                        $teksto = "- " . substr($teksto, 1);
                                    }
                                    else {
                                        // aldonu +
                                        $teksto = "+ " . $teksto;
                                    }
                                }
                            }
                            else {
                                $teksto = $helpilo->trans_eo($cxelo);
                                $allineado = "L";
                            }
                            $pdf->cell($largxecoj[$index], $alteco,
                                       $teksto,
                                       $kadro, 0, $allineado);
                        }
                        // normala tiparo
                        $pdf->setFont("", "");
                        $pdf->ln();
                        $pdf->cell($largxecoj['titolo'], 0, "");
                        $kadro = 0;
                    }
                    $grandaj_linioj ++;
                    $pdf->ln();
                }

                // TODO
                break;
            }
    }


    /***************** kelkaj funkcioj uzendaj de ekstere ***************/

    function kategorioj_kompletaj() {
        foreach($GLOBALS['kategoriotipoj'] AS $tipo) {
            if (!$this->kategorioj[$tipo])
                return false;
        }
        return true;
    }


    function aldonu_krampojn($array) {
        if (is_array($array)) {
            $rez = array();
            foreach($array AS $nomo => $valoro) {
                $rez[$nomo] = "(" . $valoro . ")";
            }
            return $rez;
        }
        if ($array) {
            return "(" . $array . ")";
        }
        return $array;
    }

    /**
     * montras tabelon de la kotizokalkulado.
     *
     *  $tipo
     *    0 - HTML-tabelo
     *    1 - teksta tabelo (ankoraux farenda)
     *    2 - al PDF-objekto (por la akceptofolio)
     *    3 - al PDF-objekto (por dua konfirmilo, eo)
     *    4 - al PDF-objekto (por dua konfirmilo, de)
     * 
     *  $pdf  - uzata por meti tien la tabelon.
     */
    function montru_kotizon($tipo, &$pdf)
    {
        $tabelo = array();
        
        // kategorioj:

        $kottab = array();
        if ($tipo == 0 and DEBUG) {
            debug_echo("<!-- this->kotizo: " . var_export($this->kategorioj, true) . "-->");
        }
        foreach($this->kategorioj AS $katTipo => $katDatoj) {
            $kat = donu_kategorion($katTipo, $katDatoj['ID']);
            $kattab[] = array(donu_eokatnomon($katTipo),
                              " " . $kat->datoj['nomo'],
                              $this->aldonu_krampojn($katDatoj['kialo']));
        }
        $kattab[]= array(array('eo'=>"partoprentempo",
                               'de' => "Teilnahmezeit"),
                         $this->partoprentempo,
                         "(" . substr($this->partopreno->datoj['de'], 5) .
                         " - " . substr($this->partopreno->datoj['gxis'], 5) .
                         ")");
        
        $tabelo[] = array('titolo' => array('eo' => "kategorioj",
                                            'de' => "Kategorien"),
                          'enhavo' => $kattab);

        // baza kotizo

        if ($this->partakotizo != $this->bazakotizo) {
            $tabelo[] = array('titolo' => array('eo' => "kotizo",
                                               'de' => "Beitrag"),
                             'enhavo' => array(array(array('eo'=> "baza",
                                                           'de' => "Basis"),
                                                     $this->bazakotizo),
                                               array(array('eo' => "parttempa partopreno",
                                                           'de' => "Teilzeitteilnahme"),
                                                     $this->partakotizo,
                                                     $this->partakotizo,
                                                     'grava' => true))
                             );
                             
        }
        else {
            $tabelo[]= array('titolo' => array('eo' => "kotizo",
                                               'de' => "Beitrag"),
                             'enhavo' => array(array(array('eo'=> "baza",
                                                           'de' => "Basis"),
                                                     $this->bazakotizo,
                                                     $this->partakotizo,
                                                     'grava' => true)),
                              );
        }

        // krompagoj

        if ($this->krompagoj != 0) {
            $kromtab = array();
            foreach($this->krompagolisto AS $ero) {
                $kromtab[] = array_values($ero);
            }
            $kromtab[] = array(array('eo'=>"sumo",'de'=>"Summe"),
                               $this->krompagoj,
                               $this->krompagoj,
                               'grava' => true);
            $tabelo[] = array('titolo' => array('eo'=>"krompagoj",
                                                'de' => "Zuzahlungen"),
                              'enhavo' => $kromtab);
        }
        if ($this->rabatoj != 0) {
            $rabatolisto = array();
            if ($this->diversaj_rabatoj) {
                $rabatolisto[] = array("diversaj", $this->diversaj_rabatoj);
            }
            if ($this->tejo_rabato) {
                $rabatolisto[] = array(array('eo' =>"TEJO-membreco",
                                             'de' => "TEJO-Mitgliedschaft"),
                                       $this->tejo_rabato);
            }
            $rabatolisto[] = array(array('eo'=>"sumo",
                                         'de'=> "Summe"),
                                   $this->rabatoj,
                                   - $this->rabatoj,
                                   'grava' => true);
            $tabelo[] = array('titolo' => array('eo' => "rabatoj",
                                                'de' => "Rabatte"),
                              'enhavo' => $rabatolisto);
        }
        if ($this->pagoj != 0) {
            $pagolisto = array();
            if ($this->antauxpagoj) {
                $pagolisto []= array(array('eo' => "antau^pagoj",
                                           'de' => "Anzahlungen"),
                                     $this->antauxpagoj);
            }
            if ($this->surlokaj_pagoj) {
                $pagolisto []= array(array('de' => "surlokaj pagoj",
                                           'eo' => "Zahlungen vor Ort"),
                                     $this->surlokaj_pagoj);
            }
            if ($this->postaj_pagoj) {
                $pagolisto[]= array(array('eo' => "postaj pagoj",
                                          'de' => "Spätere Zahlungen"),
                                    $this->postaj_pagoj);
            }
            $pagolisto[] = array(array('eo'=>"sumo",
                                         'de'=> "Summe"),
                                 $this->pagoj,
                                 - $this->pagoj,
                                 'grava' => true);
            $tabelo[] = array('titolo' => array('eo' => "pagoj",
                                                'de' => "Zahlungen"),
                              'enhavo' => $pagolisto);
        }
        
        // restas pagenda
        $tabelo[] = array('titolo' => array('eo' => "Restas pagenda",
                                            'de' => "Bleibt zu zahlen"),
                          'enhavo' => array(array("", "",
                                                  $this->pagenda,
                                                  'grava' => true)));


        $this->formatu_tabelon($tabelo, $tipo, $pdf);

    }



    /**
     * redonas, kiom da mono ankoraux estas pagenda.
     */
    function restas_pagenda() {
        return $this->pagenda;
    }


    /*********** nur unufoje uzata (kreu_konfirmilon)
     *********** - eble sxovu aliloken                 ************/

    /**
     * eltrovas la minimuman antauxpagon.
     *
     */
    function minimuma_antauxpago() {
        // TODO
    }

    function formatu_agxkategorion($renkontigxo) {
        return "(mankas)";
        // TODO
    }

}

?>