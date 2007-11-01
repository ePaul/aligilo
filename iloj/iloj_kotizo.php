<?php


require_once($prafix . '/iloj/iloj_kotizo_kategorioj.php');
require_once($prafix . '/iloj/iloj_kotizo_krompagoj.php');

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
   * kotizosistemon kaj elprovi gxiajn efikon je ekzistantaj
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
   *   - landokategorisistemo
   *   - logxkategorisistemo
   *   - agxkategorisistemo
   *   - aligxkategorisistemo
   *   - entajpanto
   *
   *
   * kotiztabelero:
   *  - kotizosistemo
   *  - aligxkategorio
   *  - landokategorio
   *  - agxkategorio
   *  - logxkategorio
   *  - kotizo
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
     *               agx => ...,   // id de agxkategorio
     *               lando => ..., // id de landokategorio
     *               logx => ...,  // id de logxkategorio
     *               aligx => ..., // id de aligxkategorio
     *
     *  eltrovas la kategoriojn por iu partoprenanto en iu renkontigxo.
     */
    function eltrovu_kategoriojn($partoprenanto, $partopreno, $renkontigxo)
    {
        echo("<!-- kotizosistemo->eltrovukategoriojn(" . $partoprenanto . ", "
             . $partopreno . ", " . $renkontigxo . ") -->");
             
        $kategorioj = array();
        foreach ($GLOBALS['kategoriotipoj'] AS $tipo) {
            $katsistemo = $this->donu_kategorisistemon($tipo);
            echo "<!-- katsistemo[$tipo]: " . var_export($katsistemo, true) . "-->";
            $kategorioj[$tipo] =
                $katsistemo->trovu_kategorion($partoprenanto, $partopreno,
                                              $renkontigxo);
        }
        return $kategorioj;
    }


    /*
     * $kategorioj  array() en la formo
     *               agx => ...,   // id de agxkategorio
     *               lando => ..., // id de landokategorio
     *               logx => ...,  // id de logxkategorio
     *               aligx => ..., // id de aligxkategorio
     *
     *  eltrovas kaj redonas la bazan kotizon por tiu kategorio.
     */
    function eltrovu_bazan_kotizon($kategorioj) {
        $restriktoj = array("kotizosistemo = '" . $this->datoj['ID'] . "'");
        foreach($kategorioj as $nomo => $id) {
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
     * $redaktebla  - cxu kun redaktilo aux nur montri ciferon
     * $identigiloj -  array() en la formo
     *               agx => ...,   // id de agxkategorio
     *               lando => ..., // id de landokategorio
     *               logx => ...,  // id de logxkategorio
     *               aligx => ..., // id de aligxkategorio
     * 
     */
    function metu_kotizocxelon($redaktebla, $identigiloj) {
        echo "<td>";
        $kotizo = $this->eltrovu_bazan_kotizon($identigiloj);

        if ($redaktebla) {
            $idoj = array();
            // por ke la sxlosiloj estu en gxusta sinsekvo
            foreach($GLOBALS['kategoriotipoj'] AS $tipo) {
                $idoj[] = $identigiloj[$tipo];
            }
            $nomo = "kotizo[".implode("=", $idoj) . "]";
            simpla_entajpejo("", $nomo, $kotizo, 5);
            // TODO
        }
        else {
            echo "$kotizo";
        }
        
        echo "</td>";
    }

    /**
     */
    function metu_bazan_kotizolinion($redaktebla, $tipo,
                                     $identigiloj, $titolo)
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
            $this->metu_kotizocxelon($redaktebla, $identigiloj);
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

    function metu_bazan_kotizotabelon($redaktebla, $tipoj, $identigiloj)
    {
        $tipo = array_pop($tipoj);
        $sekva_tipo = array_pop($tipoj);
        if(count($tipoj)) {
            darf_nicht_sein("pli ol 0: " . count($tipoj));
        }
        echo "<table class='kotizotabelo-baza'>";
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
                                    $identigiloj, $linio['nomo']);
        }
        
        echo "</table>";
    }

    function metu_grandan_kotizolinion($redaktebla, $tipoj, $identigiloj) {
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
            eoecho( $linio['nomo']);
            $identigiloj[$tipo] = $linio['ID'];
            $this->metu_bazan_kotizotabelon($redaktebla, $tipoj,
                                     $identigiloj);
            eoecho("</td>");
        }
        
        echo "</tr>\n";
    }

    function metu_grandan_kotizotabelon($redaktebla,$tipoj) {
        $identigiloj = array();
        $tipo = array_pop($tipoj);
        $sekva_tipo = end($tipoj);
        echo "<table class='kotizotabelo-granda'>";
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
                                     $identigiloj);
        }
        echo "</table>\n";
    }

    

    /**
     * kreas tabelon de cxiuj bazaj kotizoj.
     *
     * $redaktebla:
     *       true - metas entajpejojn por la unuopaj kotizoj
     *       false - montras nur la kotizon.
     */
    function metu_kotizotabelon($redaktebla) {
        $this->metu_grandan_kotizotabelon($redaktebla, $GLOBALS['kategoriotipoj']);
    }
    

}  // class kotizosistemo

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

        // TODO: partotempa partopreno
        $this->partakotizo = 
            $this->bazakotizo;

        $this->kalkulu_pagojn();
        $this->kalkulu_rabatojn();
        $this->kalkulu_krompagojn();

        $this->pagenda =
            $this->partakotizo + $this->krompagoj
            - $this->rabatoj - $this->pagoj;
    }


    /****************** internaj funkcioj de la kotizokalkulilo **********/
    

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
        echo "<!-- surlokaj: " . $linio['num'] . "-->";
        $this->surlokaj_pagoj =
            $linio ? $linio['num'] : 0;
        // antauxpagoj
        $sql = datumbazdemando(array("SUM(kvanto)" => "num"),
                               "pagoj",
                               array("dato < '$de'",
                                     "partoprenoID = '$ppID'" ));
        $linio = mysql_fetch_assoc(sql_faru($sql));
        echo "<!-- antauxaj: " . $linio['num'] . "-->";
        $this->antauxpagoj =
            $linio ? $linio['num'] : 0;
        // postaj pagoj
        $sql = datumbazdemando(array("SUM(kvanto)" => "num"),
                               "pagoj",
                               array("'$gxis' < dato",
                                     "partoprenoID = '$ppID'" ));
        $linio = mysql_fetch_assoc(sql_faru($sql));
        echo "<!-- postaj: " . $linio['num'] . "-->";
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
        foreach($krompagolisto AS $ero) {
            if($ero['tipo']->aplikigxas($this->partoprenanto,
                                        $this->partopreno,
                                        $this->renkontigxo)) {
                $krompagoj[] = array('tipo' => $ero['tipo']->datoj['nomo'],
                                     'krompago' => $ero['krompago']);
                $sumo += $ero['krompago'];
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
        echo "<!-- TEJO-kotizo? -->";
        if ($this->partopreno->datoj['tejo_membro_kontrolita'] == 'i')
            {
                $this->krom_tejo_membrokotizo =
                    $this->partopreno->datoj['tejo_membro_kotizo'];
                $this->krompagolisto[]=
                    array('tipo' => "TEJO-membrokotizo",
                          'krompago'
                          => $this->partopreno->datoj['tejo_membro_kotizo']);
                echo "<!-- jes! krompagolisto: ". var_export($this->krompagolisto, true) . "-->";
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
        $rez = "<!-- html_formatu_linireston(" . var_export($linio, true) . "-->";
        $rez .= "<td>" . $linio[0] . "</td><td>";
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

    function formatu_tabelon($tabelo, $tipo, $pdf) {
        echo "<!-- tabelo: " .  var_export($tabelo, true ) . "-->";
        switch($tipo)
            {
            case 0:
                $html = "<table class='rezulto'>\n";
                foreach($tabelo AS $linio) {
                    $titolo = $linio['titolo'];
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
            case 2:
            case 3:
                // TODO
            }
    }


    /***************** kelkaj funkcioj uzendaj de ekstere ***************/

    /**
     * montras tabelon de la kotizokalkulado.
     *
     *  $tipo
     *    0 - HTML-tabelo
     *    1 - teksta tabelo
     * (estonte:
     *    2 - al PDF-objekto (por la akceptofolio)
     *    3 - al PDF-objekto (por dua informilo)
     * )
     *  $pdf  - uzata por meti tien la tabelon.
     */
    function montru_kotizon($tipo, $pdf="")
    {
        $tabelo = array();
        
        // kategorioj:

        $kottab = array();
        echo "<!-- this->kotizo: " . var_export($this->kategorioj, true) . "-->";
        foreach($this->kategorioj AS $tipo => $katID) {
            $kat = donu_kategorion($tipo, $katID);
            $kattab[] = array(donu_eokatnomon($tipo),
                              " " . $kat->datoj['nomo']);
        }
        $tabelo[] = array('titolo' => "kategorioj",
                          'enhavo' => $kattab);

        // baza kotizo

        if ($this->partakotizo != $this->bazakotizo) {
            $tabelo[] = array('titolo' => "Kotizo",
                             'enhavo' => array(array("baza", $this->bazakotizo),
                                               array("parttempa partopreno",
                                                     $this->partakotizo, $this->partakotizo))
                             );
                             
        }
        else {
            $tabelo[]= array('titolo' => "kotizo",
                              'enhavo' => array(array("baza",
                                                      $this->bazakotizo,
                                                      $this->partakotizo)),
                              );
        }

        // krompagoj

        if ($this->krompagoj != 0) {
            $kromtab = array();
            foreach($this->krompagolisto AS $ero) {
                $kromtab[] = array_values($ero);
            }
            $kromtab[] = array("<strong>sumo</strong>", $this->krompagoj, $this->krompagoj);
            $tabelo[] = array('titolo' => "krompagoj",
                              'enhavo' => $kromtab);
        }
        if ($this->rabatoj != 0) {
            $rabatolisto = array();
            if ($this->diversaj_rabatoj) {
                $rabatolisto[] = array("diversaj", $this->diversaj_rabatoj);
            }
            if ($this->tejo_rabato) {
                $rabatolisto[] = array("TEJO-membreco", $this->tejo_rabato);
            }
            $rabatolisto[] = array("<strong>sumo</strong>", $this->rabatoj, - $this->rabatoj);
            $tabelo[] = array('titolo' => "rabatoj",
                              'enhavo' => $rabatolisto);
        }
        if ($this->pagoj != 0) {
            $pagolisto = array();
            if ($this->antauxpagoj) {
                $pagolisto []= array("antau^pagoj", $this->antauxpagoj);
            }
            if ($this->surlokaj_pagoj) {
                $pagolisto []= array("surlokaj pagoj", $this->surlokaj_pagoj);
            }
            if ($this->postaj_pagoj) {
                $pagolisto[]= array("postaj pagoj", $this->postaj_pagoj);
            }
            $pagolisto[]=array("<strong>sumo</strong>", $this->pagoj, - $this->pagoj);
            $tabelo[] = array('titolo' => "pagoj",
                              'enhavo' => $pagolisto);
        }
        
        // restas pagenda
        $tabelo[] = array('titolo' => "Restas pagenda",
                          'enhavo' => array(array("", "", $this->pagenda)));


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

    function formatu_agxkategorion($agxo, $renkontigxo) {
        // TODO
    }

}

?>