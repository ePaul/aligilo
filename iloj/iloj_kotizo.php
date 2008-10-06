<?php

  /**
   * Nova konfigurebla kotizosistemo.
   * 
   * Kotizo-datumoj:
   * - landokategorioj
   * - aĝkategorioj
   * - loĝkategorioj (junulargastejo/amasloĝejo/...)
   *    (- manĝado (aparte aŭ kun loĝado))
   * - aliĝtempo-kategorioj (kun limdatoj)
   *
   * - kotizoj por ĉiuj eblecoj (4/5-dimensia tabelo, ŝajne)
   *
   * La celo estas, ke oni (la decidanto) povu simple krei novan
   * kotizosistemon kaj elprovi ĝiajn efikon je ekzistantaj
   * partopreno-datumoj.
   * Kune kun apartaj difinoj de kostoj eblos prognosi la financan
   * rezulton de renkontiĝo, kaj analizi profitodonajn kaj
   * malprofitodonajn partoprenantajn grupojn.
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage iloj
   * @copyright 2007-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */


require_once($prafix . '/iloj/iloj_kotizo_kategorioj.php');
require_once($prafix . '/iloj/iloj_kotizo_krompagoj.php');
require_once($prafix . '/iloj/iloj_kostoj.php');
require_once($prafix . '/iloj/iloj_kotizo_malaligxo.php');
require_once($prafix . '/iloj/iloj_kotizo_formatado.php');



  /*
   * kotizosistemo:
   *
   *   - ID
   *   - nomo
   *   - priskribo
   *   - entajpanto
   *   - landokategorisistemo
   *   - logxkategorisistemo
   *   - agxkategorisistemo
   *   - aligxkategorisistemo
   *   - parttempdivisoro
   *   - malaligxkondicxosistemo
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
     * donas liston de ĉiuj krompagoj, kiuj estas relevantaj
     * en tiu ĉi kotizosistemo.
     * @return array
     *  array()  el elementoj de la formo
     *     array('tipo' =>       Krompagotipo-objekto
     *           'krompago' =>   la krompago en ĉi tiu kotizosistemo.
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
     * de tiu ĉi kotizosistemo.
     */
    function donu_kategorisistemon($tipo) {
        return donu_katsistemon($this->datoj[$tipo."kategorisistemo"],
                                $tipo);
    }

    function donu_ma_kondicxo_sistemon() {
        // TODO: eble iam metu en cache.
        return new Malaligxkondicxsistemo($this->datoj['malaligxkondicxsistemo']);
    }

    /**
     * kalkulas la bazan kotizon, se tiu ĉi kotizosistemo estus
     * relevanta.
     *
     * @param Partoprenanto $partoprenanto
     * @param Partopreno $partopreno
     * @param Renkontigxo $renkontigxo
     * @return number baza kotizo
     */
    function kalkulu_bazan_kotizon($partoprenanto, $partopreno, $renkontigxo)
    {
        $kategorioj = $this->eltrovu_kategoriojn($partoprenanto,
                                                 $partopreno,
                                                 $renkontigxo);
        return
            $this->eltrovu_bazan_kotizon($kategorioj);
    }

    /**
     *
     *  eltrovas la kategoriojn por iu partoprenanto en iu renkontiĝo.
     *
     * @param Partoprenanto $partoprenanto
     * @param Partopreno $partopreno
     * @param Renkontigxo $renkontigxo
     * @return array
     * redonas  array() en la formo
     *               agx => ...,   
     *               lando => ..., 
     *               logx => ...,  
     *               aligx => ...,
     *  kie ĉiu valoro estas
     *        array('ID' => identifikilo de la kategorio,
     *              'kialo' => iu teksto aŭ array(de => ..., eo => ...)).
     *
     */
    function eltrovu_kategoriojn($partoprenanto, $partopreno, $renkontigxo)
    {
        //        debug_echo("<!-- kotizosistemo->eltrovukategoriojn(" . $partoprenanto . ", "
        //             . $partopreno . ", " . $renkontigxo . ") -->");
             
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
     * @param int $landokategorioID - identigilo de la landokategorio.
     * @return array  redonas 
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


    /**
     *  eltrovas kaj redonas la bazan kotizon por tiu kategorio.
     *
     * @param array $kategorioj  array() en la formo
     *               agx => ...,   // id de agxkategorio
     *               lando => ..., // id de landokategorio
     *               logx => ...,  // id de logxkategorio
     *               aligx => ..., // id de aligxkategorio
     * aux kie la valoroj (por samaj sxlosiloj) estas de la
     * formo
     *         array(ID => ..., ...).
     * @return number la baza kotizo.
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
     * formatas cxelon en la kotizotabelo.
     *
     * @param funkcio $elementa_funkcio  - funkcio, kiu faru la veran eldonon - vidu
     *                       metu_kotizotabelon() por priskribo.
     * @param array $identigiloj  array() en la formo
     *               agx => ...,   // id de agxkategorio
     *               lando => ..., // id de landokategorio
     *               logx => ...,  // id de logxkategorio
     *               aligx => ..., // id de aligxkategorio
     * @param mixed $aldone aliaj parametroj por $elementa_funkcio
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


    /**
     * kreas la kotizo-tabelon en array-formo.
     *
     * TODO!: pli bona dokumentado (vidu paperon)
     */
    function kreu_kotizotabelparton($tipoj_farendaj,
                                    $identigiloj_jam_elektitaj) {
        if (!count($tipoj_farendaj)) {
            return
                array("kotizo" =>
                      $this->eltrovu_bazan_kotizon($identigiloj_jam_elektitaj));
        }
        else {
            $tipo = array_pop($tipoj_farendaj);
            $rez =
                sql_faru(datumbazdemando(array("nomo", 'ID'),
                                         $tipo . "kategorioj",
                                         "sistemoID = '" .
                                         $this->datoj[$tipo.'kategorisistemo']
                                         . "'",
                                         "",
                                         array("order" => "ID")));
            $rezulto = array();
            while($linio = mysql_fetch_assoc($rez)) {
                $identigiloj_jam_elektitaj[$tipo] = $linio['ID'];
                 $subtabelo =
                     $this->kreu_kotizotabelparton($tipoj_farendaj,
                                           $identigiloj_jam_elektitaj);
                 $rezulto["{$linio['ID']}"] =
                     (
                      array('titolo' => $linio['nomo']) +
                      $subtabelo);
            }
            return $rezulto;
        }
    }


    function kreu_kotizotabelon(){
        //        echo "<!--" . var_export($GLOBALS['kategoriotipoj'], true) . "-->";
        return $this->kreu_kotizotabelparton($GLOBALS['kategoriotipoj'],
                                             array());
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


function preparu_surlokkotizkondicxon() {
    if (!$GLOBALS['surloka_kotizo_kondicxo']) {
        $kondicxo = array();
        $tekstoj = explode("|", surlokaj_pagotipoj);
        foreach(explode("|", surlokaj_pagotipoj) AS $tipo) {
            $kondicxo[]= "(tipo = '$tipo')";
        }
        $GLOBALS['surloka_kotizo_kondicxo'] = '(' . implode(' OR ', $kondicxo) . ')';
        debug_echo( "<!-- kondicxo: " . $GLOBALS['surloka_kotizo_kondicxo'] . "-->");
    }
}




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

    // kotizo post ebla trakto de malaligxo.
    var $rezultakotizo = 0;

    // TODO: prenu tion el la malaligxkondicxo.
    var $malaligxteksto,
        $malaligxmallongigo;

    
    var $partoprennoktoj /* nombro */,
        $partoprentempo /* teksto */;
    
    var $surlokaj_pagoj = 0, $antauxpagoj = 0, $postpagoj = 0, $pagoj = 0;

    var $diversaj_rabatoj = 0, $tejo_rabato = 0, $rabatoj = 0;

    var $krompagolisto = array(),
        $krompagolisto_diversaj = array(),
        $krompagoj_diversaj = 0,
        $krom_loka_membrokotizo = 0,
        $krom_nemembro = 0,
        $krom_tejo_membrokotizo = 0,
        $krompagoj = 0;

    var $pagenda;
    
    /**
     * konstruilo por la kotizokalkulilo
     *
     * @param Partoprenanto $partoprenanto
     * @param Partopreno $partopreno
     * @param Renkontigxo $renkontigxo
     * @param Kotizosistemo|null $kotizosistemo
     */
    function Kotizokalkulilo($partoprenanto, $partopreno,
                             $renkontigxo, $kotizosistemo=null)
    {
        $this->partoprenanto = &$partoprenanto;
        $this->partopreno = &$partopreno;
        $this->renkontigxo = &$renkontigxo;

        if (!$kotizosistemo) {
            $kotizosistemo =
                new Kotizosistemo($renkontigxo->datoj['kotizosistemo']);
        }

        debug_echo( "<!-- renkontigxo: " . var_export($renkontigxo, true) .
            ", kotizosistemo: " . var_export($kotizosistemo, true) . "-->");

        if (!$kotizosistemo->datoj['ID']) {
            // la renkontigxo ne havas kotizosistemon
            //  (cxe malnovaj, ekzemple).
            
            $this->pagenda = "<strong class='averto'>Kotizokalkulado ne ".
                "eblas, c^ar la renkontig^o ne havas kotizosistemon!</strong>";
            return;
        }

        $this->kotizosistemo = &$kotizosistemo;

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

        $this->traktu_malaligxon();


        $this->pagenda =
            $this->rezultakotizo + $this->krompagoj
            - $this->rabatoj - $this->pagoj;


    }


    /****************** internaj funkcioj de la kotizokalkulilo **********/


    /**
     * Je la fino de la kotizokalkulado vokita, traktas la
     * eblan malaligxon de partoprenanto.
     *
     * Poste la kotizo estas en $this->rezultakotizo.
     */
    function traktu_malaligxon() {
        switch ($this->partopreno->datoj['alvenstato']) {
        case 'v':
        case 'a':
        case 'i':
            $this->rezultakotizo = $this->partakotizo;
            return;
        case 'n':
        case 'm':
            
            $malaligxdato = $this->partopreno->datoj['malaligxdato'];
            debug_echo("<!-- malaligxdato: " . $malaligxdato . "-->");
            if (!$malaligxdato or $malaligxdato == '0000-00-00') {
                // TODO: cxu erarmesagxo?
                $malaligxdato = $this->renkontigxo->datoj['gxis'];
            }
            debug_echo("<!-- malaligxdato: " . $malaligxdato . "-->");

            $mak_sistemo =
                $this->kotizosistemo->donu_ma_kondicxo_sistemon();
            if (DEBUG) {
                echo "<!-- mak_sistemo: " .
                    var_export($mak_sistemo, true) . 
                    "-->";
            }
            $alKatSis = $mak_sistemo->donu_aligxkategorisistemon();
            if (DEBUG) {
                echo "<!-- alKatSis: " .
                    var_export($alKatSis, true) . 
                    "-->";
            }
            $alKatID =
                $alKatSis->trovu_kategorion_laux_dato($this->renkontigxo,
                                                      $malaligxdato);
            debug_echo( "<!-- alKatID: " . $alKatID . "-->");
            $kondicxtipo = $mak_sistemo->donu_kondicxon($alKatID);
            if (DEBUG) {
                echo "<!-- kondicxtipo: " .
                    var_export($kondicxtipo, true) . 
                    "-->";
            }
            $nova_kotizo = $kondicxtipo->traktu($this->partoprenanto,
                                                $this->partopreno,
                                                $this->renkontigxo,
                                                $this);
            debug_echo( "<!-- nova_kotizo: " . var_export($nova_kotizo, true) . "-->");
            if (isset($nova_kotizo)) {
                $this->rezultakotizo = $nova_kotizo;
                $this->malaligxteksto = $kondicxtipo->datoj['nomo'];
                $this->malaligxmallongigo = $kondicxtipo->datoj['mallongigo'];
            }
            else {
                $this->rezultakotizo = $this->partakotizo;
            }
        }
    }


    function kalkulu_parttempan_kotizon()
    {
        if ($this->partopreno->datoj['partoprentipo'] == 't') {
            $this->partakotizo = 
                $this->bazakotizo;
            $this->partoprennoktoj = $this->renkontigxo->renkontigxonoktoj();
            $this->partoprentempo= array('eo' => "tuttempa",
                                        'de' => "Vollzeit");
        }
        else {
            // partotempa partopreno
            $this->partoprennoktoj =
                $this->partopreno->partoprennoktoj();

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

        if (estas_unu_el($this->partopreno->datoj['alvenstato'],
                         'm', 'n')) {
            $this->diversaj_rabatoj = 0;
            $this->tejo_rabato = 0;
            $this->rabatoj = 0;
            return;
        }


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
        preparu_surlokkotizkondicxon();
        $de = $this->renkontigxo->datoj['de'];
        $gxis = $this->renkontigxo->datoj['gxis'];
        $ppID = $this->partopreno->datoj['ID'];

        // surlokaj pagoj:
        $sql = datumbazdemando(array("SUM(kvanto)" => "num"),
                               "pagoj",
                               array($GLOBALS['surloka_kotizo_kondicxo'],
                                     "partoprenoID = '$ppID'" ));
//         $sql = datumbazdemando(array("SUM(kvanto)" => "num"),
//                                "pagoj",
//                                array("'$de' <= dato", "dato <= '$gxis'",
//                                      "partoprenoID = '$ppID'" ));
        $linio = mysql_fetch_assoc(sql_faru($sql));
        debug_echo( "<!-- surlokaj: " . $linio['num'] . "-->");
        $this->surlokaj_pagoj =
            $linio ? $linio['num'] : 0;
        // antauxpagoj
        $sql = datumbazdemando(array("SUM(kvanto)" => "num"),
                               "pagoj",
                               array("NOT(" . $GLOBALS['surloka_kotizo_kondicxo'] . ")",
                                     "dato < '$de'",
                                     "partoprenoID = '$ppID'" ));
        $linio = mysql_fetch_assoc(sql_faru($sql));
        debug_echo ("<!-- antauxaj: " . $linio['num'] . "-->");
        $this->antauxpagoj =
            $linio ? $linio['num'] : 0;
        // postaj pagoj
        $sql = datumbazdemando(array("SUM(kvanto)" => "num"),
                               "pagoj",
                               array("NOT(" . $GLOBALS['surloka_kotizo_kondicxo'] . ")",
                                     "'$de' <= dato",
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
                $krompagoj[] =
                    array('tipo' =>
                          array('eo' => $ero['tipo']->datoj['nomo'],
                                'de' =>
                                $ero['tipo']->datoj['nomo_lokalingve']),
                          'krompago' => $kp);
                $this->krompagolisto_diversaj[] = array('tipo' => $ero['tipo'],
                                                        'pago' => $kp);
                $sumo += $kp;
            }
            else {
                $this->krompagolisto_diversaj[] = array('tipo' => $ero['tipo'],
                                                        'pago' => 0);
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
                array('tipo'=> array('eo' => "membrokotizo por ". deviga_membreco_nomo,
                                     'de' => "Mitgliedsbeitrag ". deviga_membreco_nomo),
                      'krompago' => $this->partopreno->datoj['membrokotizo']);
            break;
        case 'k':
            $this->krom_nemembro =
                $this->partopreno->datoj['membrokotizo'];
            $this->krompagolisto[]=
                array('tipo' => array('eo' => "nemembro de "
                                      .          deviga_membreco_nomo,
                                      'de' => "Nichtmitgliedschaft "
                                      .          deviga_membreco_nomo),
                      'krompago' => $this->partopreno->datoj['membrokotizo']);
            break;
        }
    }
    function kalkulu_tejo_kotizon()
    {
        debug_echo( "<!-- TEJO-kotizo? -->");
        if ($this->partopreno->datoj['tejo_membro_kontrolita'] == 'i' or
            $this->partopreno->datoj['tejo_membro_kontrolita'] == 'p')
            {
                $this->krom_tejo_membrokotizo =
                    $this->partopreno->datoj['tejo_membro_kotizo'];
                $this->krompagolisto[]=
                    array('tipo' => array('eo' => "TEJO-membrokotizo",
                                          'de'=> "TEJO-Mitgliedsbeitrag"),
                          'krompago'
                          => $this->partopreno->datoj['tejo_membro_kotizo']);
                debug_echo( "<!-- jes! krompagolisto: ".
                            var_export($this->krompagolisto, true) . "-->");
            }
    }




    /**
     * kreas datum-strukturon por la tabelo de kotizoj.
     *
     *    array() el lini-grupoj, kiuj po havas la formon
     *       array('titolo' => titolo de linigrupo,
     *             'enhavo' => array() el unu gxis pluraj
     *                         du- aux tri-elementaj array()-oj,
     *                         kiuj po enhavas la enhavon de unu
     *                         linio laux kampoj.
     *                         Tiuj enhavo-elementoj povas mem esti
     *                          aux cxeno, numero, aux
     *                          array('eo' => ..., 'de' => ..., ...)
     */
    function kreu_kotizotabelon() {
        $tabelo = array();
        
        // kategorioj:

        $kottab = array();
        if ($tipo == 0 and DEBUG) {
            debug_echo("<!-- this->kotizo: " . var_export($this->kategorioj, true) . "-->");
        }
        foreach($this->kategorioj AS $katTipo => $katDatoj) {
            
            $kat = donu_kategorion($katTipo, $katDatoj['ID']);
            $kattab[] = array(array('de' => donu_dekatnomon($katTipo),
                                    'eo' => donu_eokatnomon($katTipo)),
                              ($kat->datoj['nomo_lokalingve'] ?
                               array('de' => $kat->datoj['nomo_lokalingve'],
                                     'eo' => $kat->datoj['nomo']) :
                               " " . $kat->datoj['nomo']),
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
        
        
        if ($this->malaligxteksto) {
            if ($this->partakotizo != $this->bazakotizo) {
                $tabelo[] = array('titolo' => array('eo' => "kotizo",
                                                    'de' => "Beitrag"),
                                  'enhavo' => array(array(array('eo'=> "baza",
                                                                'de' => "Basis"),
                                                          $this->bazakotizo),
                                                    array(array('eo' => "parttempa partopreno",
                                                                'de' => "Teilzeitteilnahme"),
                                                          $this->partakotizo),
                                                    array(array('eo' => "malalig^o (" . $this->malaligxteksto . ")",
                                                                'de' => "Abmeldung"),
                                                          $this->rezultakotizo,
                                                          $this->rezultakotizo,
                                                          "grava" => true)
                                                    )
                                  );
                             
            }
            else {
                $tabelo[] = array('titolo' => array('eo' => "kotizo",
                                                    'de' => "Beitrag"),
                                  'enhavo' => array(array(array('eo'=> "baza",
                                                                'de' => "Basis"),
                                                          $this->bazakotizo),
                                                    array(array('eo' => "malalig^o (" . $this->malaligxteksto . ")",
                                                                'de' => "Abmeldung"),
                                                          $this->rezultakotizo,
                                                          $this->rezultakotizo,
                                                          'grava' => true))
                                  );
            }
        }
        else if ($this->partakotizo != $this->bazakotizo) {
            $tabelo[] = array('titolo' => array('eo' => "kotizo",
                                                'de' => "Beitrag"),
                              'enhavo' => array(array(array('eo'=> "baza",
                                                            'de' => "Basis"),
                                                      $this->bazakotizo),
                                                array(array('eo' => "parttempa partopreno",
                                                            'de' => "Teilzeitteilnahme"),
                                                      $this->partakotizo,
                                                      $this->rezultakotizo,
                                                      'grava' => true))
                              );
                             
        }
        else {
            $tabelo[]= array('titolo' => array('eo' => "kotizo",
                                               'de' => "Beitrag"),
                             'enhavo' => array(array(array('eo'=> "baza",
                                                           'de' => "Basis"),
                                                     $this->bazakotizo,
                                                     $this->rezultakotizo,
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
                $rabatolisto[] = array(array('eo' => "diversaj",
                                             'de' => "verschiedene"),
                                       $this->diversaj_rabatoj);
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
        return $tabelo;
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


    function tabelu_kotizon(&$kotizoFormatilo) {
        $tabelo = $this->kreu_kotizotabelon();
        //        echo "<!-- tabelu_kotizon: " .
        //         var_export($tabelo, true) . "-->"; 
        $kotizoFormatilo->formatu_tabelon($tabelo);
    }


    /**
     * redonas, kiom da mono ankoraux estas pagenda.
     */
    function restas_pagenda() {
        return $this->pagenda;
    }


    function limdato() {
        $aligxKat = new Aligxkategorio($this->kategorioj['aligx']['ID']);
        echo("<!-- " . var_export($aligxKat, true) . "-->");
        return $aligxKat->limdato_por_renkontigxo($this->renkontigxo);
    }


    /**
     * informoj por la finkalkulado
     */
    function donu_informon($kamponomo) {
        switch($kamponomo) {
        case 'alvenstato':
            return $this->partopreno->datoj['alvenstato'] .
                $this->malaligxmallongigo;
        case 'nomo_pers':
            return $this->partoprenanto->datoj['personanomo'];
        case 'nomo_fam':
            return $this->partoprenanto->datoj['nomo'];
        case 'noktoj':
            return $this->partoprennoktoj;
        case 'lando':
            return $this->partoprenanto->landonomo();
        case 'antauxpago':
            return $this->antauxpagoj;
        case 'surlokaPago':
            return $this->surlokaj_pagoj;
        case 'postaPago':
            return $this->postpagoj;
        case 'pagoSumo':
            return $this->pagoj;
        case 'kotizo':
            return $this->rezultakotizo;
        case 'rabatoj':
            return $this->rabatoj;
        case 'krompagoj_gxeneralaj':
            return $this->krompagoj_diversaj;
        case 'TEJOkotizo':
            return $this->krom_tejo_membrokotizo;
        case 'GEAkotizo':
            return
                // TODO!: metu la limdaton en la datumbazon
                ($this->partoprenanto->datoj['naskigxdato'] < '1981-01-01') ?
                $this->krom_loka_membrokotizo : 0;
        case 'GEJkotizo':
            return
                // TODO: metu la limdaton en la datumbazon (sama kiel antauxe!)
                ($this->partoprenanto->datoj['naskigxdato'] < '1981-01-01') ?
                0 : $this->krom_loka_membrokotizo ;
        case 'punpago':
            return $this->krom_nemembro;
        case 'kSumo':
            return $this->rezulta - $this->rabatoj
                + $this->krompagoj;
        case 'restas':
            return $this->pagenda;
        }
    }



    /**
     * eltrovas la minimuman antauxpagon.
     *
     */
    function minimuma_antauxpago() {
        $landoKat = $this->kategorioj['lando']['ID'];
        $minAP = 
            $this->kotizosistemo->minimumaj_antauxpagoj($landoKat);
        return $minAP['oficiala_antauxpago'];
    }

    /*********** nur unufoje uzata (kreu_konfirmilon)
     *********** - eble sxovu aliloken                 ************/


    function formatu_agxkategorion($renkontigxo) {
        return "(mankas)";
        // TODO
    }

}

?>