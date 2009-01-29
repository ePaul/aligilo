<?php


  /**
   * Nova konfigurebla kotizosistemo - datumbazaj objektoj.
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
   * Aldone estas apartaj tabeloj por krompagoj.
   *
   * @see iloj_kotizoj_krompagoj.php
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage iloj
   * @copyright 2007-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


/**
 * La kotizosistemo-objekto.
 *
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
   * Rilataj tabeloj:
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
   *
   * @package aligilo
   * @subpackage iloj
   */
class Kotizosistemo extends Objekto {


    var $krompagolisto;

    function Kotizosistemo($id=0)
    {
        $this->Objekto($id, "kotizosistemoj");
    }

    /**
     * donas liston de ĉiuj regulaj krompagoj, kiuj estas relevantaj
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
        $katsistemo = $this->donu_kategorisistemon($tipo);

        $ordigo = $katsistemo->donu_kategorian_ordigon();

        $rez =
            sql_faru(datumbazdemando("ID",
                                     $tipo . "kategorioj",
                                     array('sistemoID'  =>
                                           $katsistemo->datoj['ID']),
                                     "",
                                     array("order" => $ordigo)));
        while($linio = mysql_fetch_assoc($rez)) {
            $identigiloj[$tipo] = $linio['ID'];
            $this->metu_kotizocxelon($redaktebla, $identigiloj, $aldone);
        }
        echo "</tr>\n";
    }

    function metu_kotizotitollinion($tipo)
    {
        eoecho("<tr><td/>");
        $katsistemo = $this->donu_kategorisistemon($tipo);
        $ordigo = $katsistemo->donu_kategorian_ordigon();
        $rez =
            sql_faru(datumbazdemando("nomo",
                                     $tipo . "kategorioj",
                                     array('sistemoID' =>
                                           $katsistemo->datoj['ID']),
                                     "",
                                     array("order" => $ordigo)));
        while($linio = mysql_fetch_assoc($rez)) {
            eoecho("<th>" . $linio['nomo'] . "</th>");
        }
        echo "</tr>\n";
    }

    function metu_bazan_kotizotabelon($redaktebla, $tipoj, $identigiloj,
                                      $aldone, $titolo)
    {
        $tipo = array_pop($tipoj);
        $katsistemo = $this->donu_kategorisistemon($tipo);
        $ordigo = $katsistemo->donu_kategorian_ordigon();
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
                                     array("sistemoID" => 
                                           $katsistemo->datoj['ID']),
                                     "",
                                     array("order" => $ordigo)));
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
        $katsistemo = $this->donu_kategorisistemon($tipo);
        $ordigo = $katsistemo->donu_kategorian_ordigon();
        echo "<tr>";
        $rez =
            sql_faru(datumbazdemando(array("nomo", 'ID'),
                                     $tipo . "kategorioj",
                                     array("sistemoID" => 
                                           $katsistemo->datoj['ID']),
                                     "",
                                     array("order" => $ordigo)));
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
        $katsistemo = $this->donu_kategorisistemon($tipo);
        $ordigo = $katsistemo->donu_kategorian_ordigon();

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
                                     array("sistemoID" => 
                                           $katsistemo->datoj['ID']),
                                     "",
                                     array("order" => $ordigo)));
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
        $tipoj = $GLOBALS['kategoriotipoj_por_tabelo']
            or $tipoj = $GLOBALS['kategoriotipoj'];
        $this->metu_grandan_kotizotabelon($elementa_funkcio,
                                          $tipoj,
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


/**
 * Tabelo por stoki la traktadon de parttempuloj.
 *
 * - ID
 * - baza_kotizosistemo        (estas uzata por tiu kotizosistemo.)
 * - por_noktoj                (kiom da noktoj oni rajtas resti)
 * - kondicxo                  (aldona kondicxo, kiu devas esti plenumita)
 *
 * - sub_kotizosistemo         (ni prenas la kotizojn el tiu tabelo, kaj ...)
 * - faktoro                   (... obligas ilin per tiu faktoro.)
 */
class Parttempkotizosistemo extends Objekto {

    var $kondicxo = 0;
    var $kotizosistemo = 0;


    function Parttempkotizosistemo($id = 0) {
        $this->Objekto($id, "parttempkotizosistemoj");
    }


    function korektu_kopiitajn() {
        unset($this->kondicxo);
        unset($this->kotizosistemo);
    }

    /**
     * eltrovas, ĉu tiu parttempo-sistemo aplikiĝas en iu specifa
     * situacio.
     * @return boolean
     *  -> true: jes, la partoprenanto povas havi tiun parttempo-"rabaton".
     *  -> false: ne, ...
     */
    function aplikigxas($partoprenanto, $partopreno, $renkontigxo,
                        $kotizokalkulilo)
    {

        if ($this->datoj['por_noktoj'] < $kotizokalkulilo->partoprennoktoj)
            {
                return false;
            }

        if (!is_object($this->kondicxo)) {
            $this->kondicxo = new Kondicxo($this->datoj['kondicxo']);
        }

        return kontrolu_kondicxon($this->kondicxo, $partoprenanto, $partopreno,
                                  $renkontigxo, $kotizokalkulilo);
    }

    function donu_subkotizosistemon()
    {
        if (!is_object($this->kotizosistemo)) {
            $this->kotizosistemo =
                new Kotizosistemo($this->datoj['sub_kotizosistemo']);
        }
        return $this->kotizosistemo;
    }

    function eltrovu_kategoriojn($partoprenanto,
                                 $partopreno,
                                 $renkontigxo)
    {
        return $this->donu_subkotizosistemon()
            ->eltrovu_kategoriojn($partoprenanto, $partopreno, $renkontigxo);
    }

    function eltrovu_bazan_kotizon($kategorioj)
    {
        $kotizo =
            $this->donu_subkotizosistemon()->eltrovu_bazan_kotizon($kategorioj);
        return $kotizo * $this->datoj['faktoro'];
    }
    

}  // class Parttempkotizosistemo



    /************* helpaj funkcioj **********/


function entajpa_kotizocxelo($kotizosistemo, $kategorioj) {
    $kotizo = $kotizosistemo->eltrovu_bazan_kotizon($kategorioj);
    $nomo = "kotizo[". enkodu_kategoriojn($kategorioj) . "]";
    simpla_entajpejo("", $nomo, $kotizo, 7);
}

function simpla_kotizocxelo($kotizosistemo, $kategorioj) {
    // fortrancxas la post-komajn ciferojn!
    echo number_format($kotizosistemo->eltrovu_bazan_kotizon($kategorioj));
}

/**************************************************************************/

