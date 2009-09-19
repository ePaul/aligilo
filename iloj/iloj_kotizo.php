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
   * Aldone estas apartaj tabeloj por individuaj kaj regulaj krompagoj/rabatoj.
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
   */


require_once($prafix . '/iloj/objektoj_kotizo.php');
require_once($prafix . '/iloj/iloj_kotizo_kategorioj.php');
require_once($prafix . '/iloj/iloj_kotizo_krompagoj.php');
require_once($prafix . '/iloj/iloj_kostoj.php');
require_once($prafix . '/iloj/iloj_kotizo_malaligxo.php');
require_once($prafix . '/iloj/iloj_kotizo_formatado.php');

require_once($prafix . '/tradukendaj_iloj/iloj_kotizo_tabeloj.php');


/**
 * preparas SQL-kondicxon uzata por distingi la
 * surlokajn de la ne-surlokaj pagoj, por la finkalkulado.
 */
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
 * Kalkulas kotizon, rabatojn, krompagojn, antauxpagojn, kaj
 * pagorestajxojn.
 */
class Kotizokalkulilo {

    var $partoprenanto, $partopreno, $renkontigxo, $kotizosistemo;

    var $kategorioj = array();

    // kotizo post ebla trakto de malaligxo.
    var $rezultakotizo = 0;

    // TODO: prenu tion el la malaligxkondicxo.
    var $malaligxteksto,
        $malaligxmallongigo;

    
    var $partoprennoktoj /* nombro */,
        $partoprentempo /* teksto */;
    

    /**
     * enhavas iun staton por uzo de vokitaj programoj.
     */
    var $stato = null;

    /**
     * listo/tabelo kun cxiuj detaloj de la kotizokalkulado.
     *<code>
     *  array(
     *     grupo1 =>
     *         array(
     *            'titolo' => "Baza kotizo",
     *            'signo' => '+',
     *            'speciala' => ...,
     *            array(
     *               'titolo' =>
     *                    array(
     *                      'eo' => ...,
     *                      'de' => ...,
     *                      'pl' => ...,
     *                      ...
     *                         ),
     *               'detaloj' =>
     *                     array(...),
     *               'valoro' =>
     *                    array(
     *                       'kvanto' => 66.57,
     *                       'valuto' => 'EUR',
     *                       'dato' => '2009-07-18'
     *                         ),
     *               'valoro_oficiala' => 1822.6866
     *                 ),
     *            array(
     *                ...
     *                 )
     *              ),
     *     grupo2 => 
     *         array(
     *             ...
     *              ),
     *      ...
     *       )
     *</code>
     *
     * @var array
     */
    var $detalolisto = array();

    var $tuttdetaloj;
    var $parttdetaloj;

    var $tuta_sumo;


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
					", kotizosistemo: " . var_export($kotizosistemo, true) .
					"-->");

        if (!$kotizosistemo->datoj['ID']) {
            // la renkontigxo ne havas kotizosistemon
            //  (cxe malnovaj, ekzemple).
            
            $this->pagenda = "<strong class='averto'>Kotizokalkulado ne ".
                "eblas, c^ar la renkontig^o ne havas kotizosistemon!</strong>";
            return;
        }


        $this->partoprennoktoj =
            $this->partopreno->partoprennoktoj();

        $this->kotizosistemo = &$kotizosistemo;

        $this->preparu_detaloliston();

        $this->kalkulu_kotizon();
    }


    

    function kalkulu_kotizon() {


        $this->kalkulu_bazan_kotizon();



        if (mangxotraktado == 'libera') {
            $this->kalkulu_mangxojn();
        }

        $this->kalkulu_individuajn_pseuxdopagojn("rabato");
        $this->kalkulu_individuajn_pseuxdopagojn("krom");
        $this->kalkulu_individuajn_pseuxdopagojn("pago");

        $this->traktu_malaligxon();

        $this->adiciu_cxion();


        $this->pagenda =
            $this->rezultakotizo + $this->krompagoj
            - $this->rabatoj - $this->pagoj;
    }



    /****************** internaj funkcioj de la kotizokalkulilo **********/


    function adiciu_cxion()
    {
        $tutalisto = & $this->detalolisto;

        $tutasumo = 0;
        foreach(array_keys($tutalisto) AS $grupoID) {
            $tutasumo += $this->adiciu_grupon($tutalisto[$grupoID]);
        }
        $this->tuta_sumo = (float)number_format($tutasumo, 2, ".", "");
    }


    function adiciu_grupon(&$grupo)
    {
        switch($grupo['speciala'])
            {
            case 'min':
                $idxlisto = array_filter(array_keys($grupo), 'is_numeric');
//                 echo "<pre>";
//                 var_export($idxlisto);
//                 echo "</pre>";
                $unua_idx = array_pop($idxlisto);
                $minval = $this->adiciu_gruperon($grupo[$unua_idx]);
                foreach($idxlisto AS $idx) {
                    $minval = min($minval,
                                  $this->adiciu_gruperon($grupo[$idx]));
                }
                $gruposumo = $minval;
                break;
            default:
                
                $gruposumo = 0;
                
                foreach(array_keys($grupo) AS $eroID) {
                    if (is_numeric($eroID)) {
                        $gruposumo += $this->adiciu_gruperon($grupo[$eroID]);
                    }
                } // foreach
            } // switch

        $grupo['sumo'] = $gruposumo;
        if ($grupo['signo'] == '-') {
            $grupo['signa_sumo'] = - $gruposumo;
        }
        else {
            $grupo['signa_sumo'] = $gruposumo;
        }
        return $grupo['signa_sumo'];
    }

    function adiciu_gruperon(&$ero)
    {
        $val = &$ero['valoro'];
        
        $dato = $val['dato']
            or $dato = $val['dato'] = date('Y-m-d');

        list($kurzo, $kdato) =
            eltrovu_kurzon($val['valuto'], $dato);

        if (!$kurzo) {
            darf_nicht_sein("mankas kurzo por " . $val['valuto'] . " je " .
                            $dato);
        }
        
        $val['kurzo'] = $kurzo;
           
        if ($kdato) {
            $val['kdato'] = $kdato;
        }
        
        $ero['valoro_oficiala'] = $val['kvanto'] * $kurzo;
        return $ero['valoro_oficiala'];
    }




    /**
     * Je la fino de la kotizokalkulado vokita, traktas la
     * eblan malaligxon de partoprenanto.
     *
     * Poste la kotizo estas en $this->rezultakotizo.
     */
    function traktu_malaligxon() {
        // TODO: faru ion por la nova kotizokalkulado.

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


    function kalkulu_mangxojn()
    {

        require_once($GLOBALS['prafix'].'/iloj/iloj_mangxoj.php');

        $mangxdetaloj = array("titolo" => kotizo_mangxoj_titolo(),
                              "signo" => '+');

        $sql = datumbazdemando('ID',
                               'mangxtipoj',
                               array('renkontigxoID' =>
                                     $this->renkontigxo->datoj['ID'])
                               );
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            $tipo = new Mangxtipo($linio['ID']);
            $num = kalkulu_mangxojn($this->partopreno, $tipo->datoj['mangxotipo']);
            if ($num > 0) {
                // mendis $num oble $tipo.
                $sumo = $num * $tipo->datoj['prezo'];
                $mangxdetaloj[]=
                    array('titolo' => 
                          ($num . " x ".$tipo->tradukita('priskribo')),
                          // TODO: detaloj
                          'valoro' =>
                          array('kvanto' => $sumo,
                                'valuto' => $tipo->datoj['valuto'],
                                'dato' => $this->renkontigxo->datoj['de']));
            }
        }
            if (count($mangxdetaloj) > 2)
            {
                // almenaux unu linio aldonita
                $this->detalolisto['mangxoj'] = $mangxdetaloj;
            }
    }  // kalkulu_mangxojn()



    function traduku($teksto) {
        return '"' . $teksto . '"';
    }

    /**
     * preparas la detalan liston, kun la individuaj kategorioj.
     */
    function preparu_detaloliston()
    {
        $this->partoprentempo =
            kotizo_partoprentempo_teksto($this->partopreno->datoj['partoprentipo'],
                                         $this->partoprennoktoj);


        $this->detalolisto =
            array('baza' =>  array('titolo' => kotizo_programo_titolo(),
                                   'signo' => '+'),
                  'mangxoj' => array('titolo' => kotizo_mangxoj_titolo(),
                                     "signo" => '+'),
                  'krompago' => array('titolo' => kotizo_krompagoj_titolo(),
                                      'signo' => '+'),
                  'rabato' => array('titolo' => kotizo_rabatoj_titolo(),
                                    'signo' => '-'),
                  'pagoj' => array('titolo' => kotizo_pagoj_titolo(),
                                   'signo' => '-')
                  );
    }


    /**
     * kalkulas la bazan kotizon kaj la regulajn krompagojn kaj rabatojn.
     *
     * La ideo estas, ke ni por parttempaj partoprenantoj uzas la tuttempajn
     * kotizojn, se tiuj (kun rabatoj kaj krompagoj) estas malpli kostaj
     * ol la parttempaj.
     *
     */
    function kalkulu_bazan_kotizon()
    {
        // kotizo por tuttempa partopreno
        $tutkalkulilo = new Tuttempa_subkalkulilo($this);
        $tutsumo = $tutkalkulilo->kalkulu_kotizon();

        $detaloj = $tutkalkulilo->detalolisto;

        if ($this->partopreno->datoj['partoprentipo'] == 'p')
            {
                // kotizo por parttempa partopreno
                $partkalkulilo = new Parttempa_subkalkulilo($this);
                $partsumo = $partkalkulilo->kalkulu_kotizon();

                // kaj ni prenas la minimumon de ambaux.
                if (is_numeric($partsumo) and
                    $partsumo <= $tutsumo)
                    {
                        $detaloj = $partkalkulilo->detalolisto;
                    }
            }
        
        $this->kategorioj = $detaloj['baza'][0]['detaloj']['kategorioj'];

        // detaloj nun enhavas la detalo-liston de aux part- aux tuttempaj
        // partopreno. Gxiajn sublistojn ni nun aldonas al la gxeneralaj
        // sublistoj.
        
        foreach($detaloj AS $nomo => $sublisto) {
            array_append($this->detalolisto[$nomo], $sublisto);
        }
        
    }  // kalkulu_bazajn_kotizojn()




    /**
     * kalkulas pseuxdopagojn kiel pagoj, individuaj rabatoj
     * kaj individuaj krompagoj.
     */
    function kalkulu_individuajn_pseuxdopagojn($tipo) {
        $tabelnomo = $GLOBALS['pp_tabelnomoj'][$tipo];

        $sql = datumbazdemando(array('kvanto', 'valuto', 'dato', 'tipo'),
                               $tabelnomo,
                               array('partoprenoID' =>
                                     $this->partopreno->datoj['ID']));
        $rez = sql_faru($sql);
        while ($linio = mysql_fetch_assoc($rez)) {
            $titolo =
                donu_konfiguran_tekston($tipo . 'tipo',
                                        $linio['tipo'],
                                        $this->renkontigxo->datoj['ID']);
            
            $this->detalolisto[$GLOBALS['pp_kotizokalkulkategorio'][$tipo]]
                [] = array('titolo' => $titolo,
						   'tipo' => $linio['tipo'],
                           'valoro' => array('kvanto' => $linio['kvanto'],
                                             'dato' => $linio['dato'],
                                             'valuto' => $linio['valuto']));
        } // while
    }


    function kreu_kategoriotabelon() {
        $kottab = array();

        foreach($this->kategorioj AS $katTipo => $katDatoj) {
            $kat = donu_kategorion($katTipo, $katDatoj['ID']);
            
            $kattab[]= array(kotizo_kategorio_titolo($katTipo),
                             $kat->tradukita('nomo'),
                             $this->aldonu_krampojn($katDatoj['kialo']));
        }
        

        $kattab[]= array(kotizo_partoprentempo_titolo(),
                         $this->partoprentempo,
                         "(" . substr($this->partopreno->datoj['de'], 5) .
                         "–" . substr($this->partopreno->datoj['gxis'], 5) .
                         ")");
        
        return array('titolo' => kotizo_kategorioj_titolo(),
                     'enhavo' => $kattab);

        
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


        // kategorioj
        $tabelo[]= $this->kreu_kategoriotabelon();


        foreach ($this->detalolisto AS $grupo) {
            $grupolinio = array('titolo' => $grupo['titolo']);
            $enhavo = array();
            foreach($grupo AS $index => $linio) {
                if (is_int($index)) {
                    $enhavo[]=
                        array($linio['titolo'],
                              $linio['valoro']['kvanto'] . " " .
                              $linio['valoro']['valuto'],
                              $linio['valoro_oficiala'] . " " .
                              CXEFA_VALUTO);
                }
            }
            if (count($enhavo)) {
                switch($grupo['speciala']) {
                case 'min':
                    $titolo = kotizo_minimumo_titolo();
                    break;
                default:
                    $titolo = kotizo_sumo_titolo();
                }
                $enhavo[]=
                    array('grava' => true,
                          $titolo,
                          "",
                          $grupo['sumo'] . " " . CXEFA_VALUTO,
                          $grupo['signa_sumo'] . " " . CXEFA_VALUTO);
                $grupolinio['enhavo'] = $enhavo;
                $tabelo[]= $grupolinio;
            }
        }
        $tabelo[] =
            array('titolo' => kotizo_restas_pagenda_titolo(),
                  'enhavo' =>
                  array(array('grava' => true,
                              "",
                              "",
                              "",
                              number_format($this->tuta_sumo, 2, ".", "")
							  . " " . CXEFA_VALUTO)
                        ));
        return $tabelo;
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
//     function kreu_kotizotabelon_malnova() {
//         $tabelo = array();
//         
//         // kategorioj:
// 
//         $kottab = array();
//         if ($tipo == 0 and DEBUG) {
//             debug_echo("<!-- this->kotizo: " . var_export($this->kategorioj, true) . "-->");
//         }
//         foreach($this->kategorioj AS $katTipo => $katDatoj) {
//             
//             $kat = donu_kategorion($katTipo, $katDatoj['ID']);
//             $kattab[] = array(array('de' => donu_dekatnomon($katTipo),
//                                     'eo' => donu_eokatnomon($katTipo)),
//                               ($kat->datoj['nomo_lokalingve'] ?
//                                array('de' => $kat->datoj['nomo_lokalingve'],
//                                      'eo' => $kat->datoj['nomo']) :
//                                " " . $kat->datoj['nomo']),
//                               $this->aldonu_krampojn($katDatoj['kialo']));
//         }
//         $kattab[]= array(array('eo'=>"partoprentempo",
//                                'de' => "Teilnahmezeit"),
//                          $this->partoprentempo,
//                          "(" . substr($this->partopreno->datoj['de'], 5) .
//                          " - " . substr($this->partopreno->datoj['gxis'], 5) .
//                          ")");
//         
//         $tabelo[] = array('titolo' => array('eo' => "kategorioj",
//                                             'de' => "Kategorien"),
//                           'enhavo' => $kattab);
// 
//         // baza kotizo
//         
//         
//         if ($this->malaligxteksto) {
//             if ($this->partakotizo != $this->bazakotizo) {
//                 $tabelo[] = array('titolo' => array('eo' => "kotizo",
//                                                     'de' => "Beitrag"),
//                                   'enhavo' => array(array(array('eo'=> "baza",
//                                                                 'de' => "Basis"),
//                                                           $this->bazakotizo),
//                                                     array(array('eo' => "parttempa partopreno",
//                                                                 'de' => "Teilzeitteilnahme"),
//                                                           $this->partakotizo),
//                                                     array(array('eo' => "malalig^o (" . $this->malaligxteksto . ")",
//                                                                 'de' => "Abmeldung"),
//                                                           $this->rezultakotizo,
//                                                           $this->rezultakotizo,
//                                                           "grava" => true)
//                                                     )
//                                   );
//                              
//             }
//             else {
//                 $tabelo[] = array('titolo' => array('eo' => "kotizo",
//                                                     'de' => "Beitrag"),
//                                   'enhavo' => array(array(array('eo'=> "baza",
//                                                                 'de' => "Basis"),
//                                                           $this->bazakotizo),
//                                                     array(array('eo' => "malalig^o (" . $this->malaligxteksto . ")",
//                                                                 'de' => "Abmeldung"),
//                                                           $this->rezultakotizo,
//                                                           $this->rezultakotizo,
//                                                           'grava' => true))
//                                   );
//             }
//         }
//         else if ($this->partakotizo != $this->bazakotizo) {
//             $tabelo[] = array('titolo' => array('eo' => "kotizo",
//                                                 'de' => "Beitrag"),
//                               'enhavo' => array(array(array('eo'=> "baza",
//                                                             'de' => "Basis"),
//                                                       $this->bazakotizo),
//                                                 array(array('eo' => "parttempa partopreno",
//                                                             'de' => "Teilzeitteilnahme"),
//                                                       $this->partakotizo,
//                                                       $this->rezultakotizo,
//                                                       'grava' => true))
//                               );
//                              
//         }
//         else {
//             $tabelo[]= array('titolo' => array('eo' => "kotizo",
//                                                'de' => "Beitrag"),
//                              'enhavo' => array(array(array('eo'=> "baza",
//                                                            'de' => "Basis"),
//                                                      $this->bazakotizo,
//                                                      $this->rezultakotizo,
//                                                      'grava' => true)),
//                              );
//         }
// 
//         // krompagoj
// 
//         if ($this->krompagoj != 0) {
//             $kromtab = array();
//             foreach($this->krompagolisto AS $ero) {
//                 $kromtab[] = array_values($ero);
//             }
//             $kromtab[] = array(array('eo'=>"sumo",'de'=>"Summe"),
//                                $this->krompagoj,
//                                $this->krompagoj,
//                                'grava' => true);
//             $tabelo[] = array('titolo' => array('eo'=>"krompagoj",
//                                                 'de' => "Zuzahlungen"),
//                               'enhavo' => $kromtab);
//         }
//         if ($this->rabatoj != 0) {
//             $rabatolisto = array();
//             if ($this->diversaj_rabatoj) {
//                 $rabatolisto[] = array(array('eo' => "diversaj",
//                                              'de' => "verschiedene"),
//                                        $this->diversaj_rabatoj);
//             }
//             if ($this->tejo_rabato) {
//                 $rabatolisto[] = array(array('eo' =>"TEJO-membreco",
//                                              'de' => "TEJO-Mitgliedschaft"),
//                                        $this->tejo_rabato);
//             }
//             $rabatolisto[] = array(array('eo'=>"sumo",
//                                          'de'=> "Summe"),
//                                    $this->rabatoj,
//                                    - $this->rabatoj,
//                                    'grava' => true);
//             $tabelo[] = array('titolo' => array('eo' => "rabatoj",
//                                                 'de' => "Rabatte"),
//                               'enhavo' => $rabatolisto);
//         }
//         if ($this->pagoj != 0) {
//             $pagolisto = array();
//             if ($this->antauxpagoj) {
//                 $pagolisto []= array(array('eo' => "antau^pagoj",
//                                            'de' => "Anzahlungen"),
//                                      $this->antauxpagoj);
//             }
//             if ($this->surlokaj_pagoj) {
//                 $pagolisto []= array(array('de' => "surlokaj pagoj",
//                                            'eo' => "Zahlungen vor Ort"),
//                                      $this->surlokaj_pagoj);
//             }
//             if ($this->postaj_pagoj) {
//                 $pagolisto[]= array(array('eo' => "postaj pagoj",
//                                           'de' => "Spätere Zahlungen"),
//                                     $this->postaj_pagoj);
//             }
//             $pagolisto[] = array(array('eo'=>"sumo",
//                                          'de'=> "Summe"),
//                                  $this->pagoj,
//                                  - $this->pagoj,
//                                  'grava' => true);
//             $tabelo[] = array('titolo' => array('eo' => "pagoj",
//                                                 'de' => "Zahlungen"),
//                               'enhavo' => $pagolisto);
//         }
//         
//         // restas pagenda
//         $tabelo[] = array('titolo' => array('eo' => "Restas pagenda",
//                                             'de' => "Bleibt zu zahlen"),
//                           'enhavo' => array(array("", "",
//                                                   $this->pagenda,
//                                                   'grava' => true)));
//         return $tabelo;
//     }
// 



    /***************** Kelkaj funkcioj uzendaj de ekstere ***************/

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
        return $this->tuta_sumo;
    }

	function restas_pagenda_en_valutoj() {
	  $listo = array();
	  $pagenda_cxef = $this->restas_pagenda();
	  $ni_fajfas = false;
	  $repagenda = ($pagenda_cxef < 0);

	  $sql = datumbazdemando(array('ID'),
							 'renkontigxaj_konfiguroj',
							 array('tipo'=> 'valuto',
								   'renkontigxoID' => 
								   $this->renkontigxo->datoj['ID']));
	  $rez = sql_faru($sql);
	  while($linio = mysql_fetch_assoc($rez)) {
		$valutoObj = new Renkontigxa_konfiguro($linio['ID']);
		$valuto = $valutoObj->datoj['interna'];
		list($kurzo, $dato) = eltrovu_kurzon($valuto);
		$kurzo = (float)$kurzo;
		$pagenda = $pagenda_cxef / $kurzo;

		$rimarko = $valutoObj->datoj['aldona_komento'];
		// echo "rimarko: '$rimarko'\n";
		preg_match('/\[(?:.*,)? *fajfu *= *(\d+(?:\.\d+)?) *(,.*)?\]/',
				   $rimarko, $trovaĵoj);
		$fajfu = (float)($trovaĵoj[1]);
		//		echo "trovaĵoj: " . var_export($trovaĵoj, true);
		if (0 <  $pagenda and $pagenda < $fajfu) {
		  $ni_fajfas = true;
		  $vere_pagenda = 0;
		} else if ($pagenda == 0) {
		  $vere_pagenda = 0;
		} else if ($pagenda < 0) {
		  // TODO: eble ankaŭ rondigu
		  $vere_pagenda = $fajfu * floor($pagenda / $fajfu);
		} else if ($fajfu > 0)  { // $fajfu <= $pagenda 
		  $resto = fmod($pagenda, $fajfu);
		  $vere_pagenda = $pagenda - $resto;
		}
		else {
		  $vere_pagenda = $pagenda;
		}
		$listo[$valuto]=
		  array('valuto' => $valuto,
				'valutoteksto' => $valutoObj->datoj['teksto'],
				'kurzo' => $kurzo,
				'kurzo-dato' => $dato,
				'pagenda' => number_format($pagenda, 2, ".", ""),
				'fajfu' => $fajfu,
				'vere_pagenda' => $vere_pagenda,
				);
	  }  // while
	  $traktenda = ($repagenda or
					( $pagenda_cxef != 0 and !$ni_fajfas));
	  return
		array('ni_fajfas' => $ni_fajfas,
			  'repagenda' => $repagenda,
			  'pagenda_cxef' => $pagenda_cxef,
			  'traktenda' => $traktenda,
			  'listo' => $listo);
			  
	} // function restas_pagenda_en_valutoj()


    function limdato() {
        $aligxKat = new Aligxkategorio($this->kategorioj['aligx']['ID']);
        debug_echo("<!-- " . var_export($aligxKat, true) . "-->");
        return $aligxKat->limdato_por_renkontigxo($this->renkontigxo);
    }


	/**
	 * donas informojn por la finkalkula tabelo.
	 * @return array
	 */
	function donu_informojn() {
	  $listo = array();

	  $listo['alvenstato'] =
		$this->partopreno->datoj['alvenstato'] .
		$this->malaligxmallongigo;
	  $listo['nomo_pers'] = $this->partoprenanto->datoj['personanomo'];
	  $listo['nomo_fam'] = $this->partoprenanto->datoj['nomo'];
	  $listo ['noktoj'] = $this->partoprennoktoj;
	  $listo['lando'] = $this->partoprenanto->landonomo();
	   
	  if($this->detalolisto['mangxoj']) {
		$listo['mangxoj'] = $this->detalolisto['mangxoj']['sumo'];
	  }
	  else
		$listo['mangxoj'] = 0;

	  $pagoj = $this->detalolisto['pagoj'];
	  $sumo_surlok = 0;
	  $sumo_antaux = 0;
	  $sumo_post = 0;
	  $sumo_entute = 0;
	  foreach($pagoj AS $idx => $ero) {
		if (!is_int($idx))
		  continue;
		$sumo_entute += $ero['valoro_oficiala'];
		$tipkomenco = substr($ero['tipo'], 0, 6);
		if ($tipkomenco == 'surlok')
		  {
/* 			echo $this->partoprenanto->tuta_nomo() . ": " . $ero['tipo'] */
/* 			  . ":" . $ero['valoro_oficiala'] . "              <br/>\n"; */
			$sumo_surlok += $ero['valoro_oficiala'];
		  }
		else if ($ero['valoro']['dato'] <= $this->partopreno->datoj['gxis'])
		  {
			$sumo_antaux += $ero['valoro_oficiala'];
		  }
		else
		  {
			$sumo_post += $ero['valoro_oficiala'];
		  }
	  }
	  $listo['antauxpago'] = $sumo_antaux;
	  $listo['surlokaPago'] = $sumo_surlok;
	  $listo['postaPago'] = $sumo_post;
	  //$listo['pagoSumo'] = $sumo_entute;
	  $listo['pagoSumo'] = $this->detalolisto['pagoj']['sumo'];

	  $listo['kotizo'] = $this->detalolisto['baza']['sumo'];
	  $listo['rabatoj'] = $this->detalolisto['rabato']['sumo'];
	  
	  $krompagoj = $this->detalolisto['krompago'];
	  $sumo_resto = 0;
	  $sumo_tejo = 0;
	  $sumo_logx = 0;
	  foreach($krompagoj AS $idx => $ero) {
		if (!is_int($idx))
		  continue;

		$tipokomenco = substr($ero['tipo'], 0, 7);		  
		// echo  "[" . $tipokomenco . "] "; 
		switch($tipokomenco) {
		case 'tejokot':
		  $sumo_tejo += $ero['valoro_oficiala'];
		  break;
		case 'log^ado':
		  $sumo_logx += $ero['valoro_oficiala'];
		  break;
		case '':
		  echo "<pre>" . var_export($ero, true) . "</pre>";
		default:
		  $sumo_resto += $ero['valoro_oficiala'];
		}
	  }
	  echo "\n";
	  $listo['TEJOkotizo'] = $sumo_tejo;
	  $listo['logxado'] = $sumo_logx;
	  $listo['krompagoj_gxeneralaj'] = $sumo_resto;
	   
	  $listo['kSumo'] =
		$this->detalolisto['baza']['sumo'] +
		$this->detalolisto['mangxoj']['sumo'] +
		$this->detalolisto['krompago']['sumo'] +
		- $this->detalolisto['rabato']['sumo'];

	  $listo['restas'] =
		$this->tuta_sumo;

	  $restas_informoj = $this->restas_pagenda_en_valutoj();
	  foreach($restas_informoj['listo'] AS $ero) {
		if (!$restas_informoj['ni_fajfas']) {
		  $listo['restas_' . $ero['valuto']] = $ero['vere_pagenda'];
		}
		else {
		  $listo['restas_' . $ero['valuto']] = 0;
		}
	  }

	  return $listo;
	}

    /**
     * informoj por la finkalkula tabelo
	 * TODO: refari
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
		case 'mangxoj':
		  if($this->detalolisto['mangxoj']) {
			return $this->detalolisto['mangxoj']['sumo'];
		  }
		  else
			return 0;
        case 'antauxpago':
            return $this->antauxpagoj;
        case 'surlokaPago':
		  $pagoj = $this->detalolisto['pagoj'];
		  $sumo = 0;
		  foreach($pagoj as $ero) {
			if (substr($ero['tipo'], 6) == 'surlok') {
			  $sumo += $ero['valoro_oficiala'];
			}
		  }
		  return $sumo;
        case 'postaPago':
            return $this->postpagoj;

			
        case 'pagoSumo':  // (korektita)
		  return $this->detalolisto['pagoj']['sumo'];
            return $this->pagoj;

        case 'kotizo':
		  return $this->detalolisto['baza']['sumo'];
        case 'rabatoj':
		  return $this->detalolisto['rabato']['sumo'];
        case 'krompagoj_gxeneralaj':
		  $krompagoj = $this->detalolisto['krompago'];
		  $sumo = 0;
		  foreach($krompagoj AS $ero) {
			if ($ero['tipo'] != 'tejokotizo') {
			  $sumo += $ero['valoro_oficiala'];
			}
		  }
		  return $sumo;
        case 'TEJOkotizo':
		  $krompagoj = $this->detalolisto['krompago'];
		  $tejokotizo = 0;
		  foreach($krompagoj AS $ero) {
			if ($ero['tipo'] == 'tejokotizo') {
			  $tejokotizo += $ero['valoro_oficiala'];
			}
		  }
		  return $tejokotizo;
//         case 'GEAkotizo':
//             return
//                 // TODO!: metu la limdaton en la datumbazon
//                 ($this->partoprenanto->datoj['naskigxdato'] < '1981-01-01') ?
//                 $this->krom_loka_membrokotizo : 0;
//         case 'GEJkotizo':
//             return
//                 // TODO: metu la limdaton en la datumbazon (sama kiel antauxe!)
//                 ($this->partoprenanto->datoj['naskigxdato'] < '1981-01-01') ?
//                 0 : $this->krom_loka_membrokotizo ;
//         case 'punpago':
//             return $this->krom_nemembro;
        case 'kSumo':
		  return
			$this->detalolisto['baza']['sumo'] +
			$this->detalolisto['mangxoj']['sumo'] +
			$this->detalolisto['krompago']['sumo'] +
			- $this->detalolisto['rabato']['sumo'];
        case 'restas':
            return $this->tuta_sumo;
        }
        
    }  // donu_informon
    



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
    
}  // class kotizokalkulilo



/**
 * kalkulas bazan kotizon, regulaj krompagoj kaj regulajn rabatojn
 * por iu partoprenanto, lauxbezone por tuttempa aux parttempa kotizo.
 */
class Subkalkulilo {


    var $kotizokalkulilo;
    var $kotizosistemo;
    var $objektolisto;

    var $kategorioj;

    /**
     * sama formato kiel Kotizokalkulilo->detalolisto.
     */
    var $detalolisto;
    var $sumo;

    /**
     * @var boolean
     */
    var $tuttempa;

    function Subkalkulilo($kotizokalkulilo) {
        $this->kotizokalkulilo = $kotizokalkulilo;
        $this->kotizosistemo = $kotizokalkulilo->kotizosistemo;
    }

    /**
     * kalkulas bazan kotizon kun regulaj krompagoj kaj rabatoj.
     * @return float la suma kotizo.
     */
    function kalkulu_kotizon() {
        $this->detalolisto = array();
        $this->sumo = 0;
        $this->objektolisto =
            kreu_objektoliston(&$this->kotizokalkulilo->partoprenanto,
                               &$this->kotizokalkulilo->partopreno,
                               &$this->kotizokalkulilo->renkontigxo,
                               &$this->kotizokalkulilo,
                               &$this);
        $this->kalkulu_bazan_kotizon();
        $this->kalkulu_regulajn_pseuxdopagojn('krompago', '+');
        $this->kalkulu_regulajn_pseuxdopagojn('rabato', '-');
        return $this->sumo;
    }

    /**
     * anstatauxenda en subklasoj.
     *
     * La funkcio metu la informojn pri la baza kotizo en $this->detalolisto,
     * kaj plialtigu $this->sumo per gxi.
     * Ankaux metu la uzitajn kategoriojn en $this->kategorioj.
     */
    function kalkulu_bazan_kotizon() {
        darf_nicht_sein();
    }

    /**
     * Metas la informojn pri la pago al $this->detalolisto,
     * kaj plialtigas (aux malplialtigas) $this->sumo laux gxi.
     *
     * @param asciistring $tipo aux "krompago" aux "rabato".
     */
    function kalkulu_regulajn_pseuxdopagojn($tipo, $signo) {
        $pagolisto = array('signo' => $signo);
        $listo = $this->kotizosistemo->listu_regulajn_pseuxdopagojn($tipo);
        foreach($listo AS $regPP) {
            $regulo = $regPP->donu_regulon();
            debug_echo( "<!-- regulo: " . $regulo->datoj['nomo'] . "-->");
            if ($regulo->aplikigxas($this->objektolisto)) {
			  debug_echo( "<!-- ==> regulo " . $regulo->datoj['nomo'] .
						  " aplikigxas! -->");
                if ($regulo->datoj['lauxnokte'] == 'j') {
                    $val = $regPP->datoj['kvanto'] *
                        $this->kotizokalkulilo->partoprennoktoj;
                    debug_echo ("<!-- * " .
                                $this->kotizokalkulilo->partoprennoktoj .
                                " = " . $kp . "-->");
                }
                else {
                    $val = $regPP->datoj['kvanto'];
                }
                $valuto = $regPP->datoj['valuto'];

                $pagolisto[] =
                    array('titolo' => // TODO: tradukota
                               $regulo->datoj['nomo'],
						  'tipo' => $regulo->datoj['nomo'],
                          'valoro' => array('kvanto' => $val,
                                            'valuto' => $valuto,
                                            'dato' =>
                                            $this->renkontigxo->datoj['de'])
                          );
            }
        }
        $this->detalolisto[$tipo] = $pagolisto;
        /*
         * adiciu_grupon transkalkulas al CXEFA_VALUTO.
         */
        $sumo = $this->kotizokalkulilo->adiciu_grupon($pagolisto);
        $this->sumo += $sumo;
    }

}  // class Subkalkulilo


class Tuttempa_subkalkulilo extends Subkalkulilo {

    function Tuttempa_subkalkulilo($kotizokalkulilo) {
        $this->Subkalkulilo($kotizokalkulilo);
        $this->tuttempa = true;
    }

    /**
     * anstatauxenda en subklasoj.
     *
     * La funkcio metu la informojn pri la baza kotizo en $this->detalolisto,
     * kaj plialtigu $this->sumo per gxi.
     */
    function kalkulu_bazan_kotizon() {
        $kategorioj =
            $this->kotizosistemo->eltrovu_kategoriojn($this->kotizokalkulilo->partoprenanto,
                                                      $this->kotizokalkulilo->partopreno,
                                                      $this->kotizokalkulilo->renkontigxo);
        $bazakotizo =
            $this->kotizosistemo->eltrovu_bazan_kotizon($kategorioj);

        $this->kategorioj = $kategorioj;
        $this->detalolisto['baza'] = array(
            array('titolo' => kotizo_baza_titolo(),
                  'detaloj' => array('kategorioj' => $kategorioj,
                                     'dauxro' =>
                                     $this->kotizokalkulilo->partoprentempo),
                  'valoro' => array('kvanto' => $bazakotizo,
                                    'valuto' => CXEFA_VALUTO)));
        $this->sumo += $bazakotizo;
    }


}  // class Tuttempa_subkalkulilo

class Parttempa_subkalkulilo extends Subkalkulilo {

    function Parttempa_subkalkulilo($kotizokalkulilo)
    {
        $this->Subkalkulilo($kotizokalkulilo);
        $this->tuttempa = false;
    }

    /**
     * anstatauxenda en subklasoj.
     *
     * La funkcio metu la informojn pri la baza kotizo en $this->detalolisto,
     * kaj plialtigu $this->sumo per gxi.
     */
    function kalkulu_bazan_kotizon()
    {
        $trovita = null;
        $minimumo = "xxx";

        $sql = datumbazdemando("ID",
                               'parttempkotizosistemoj',
                               array('baza_kotizosistemo' =>
                                     $this->kotizosistemo->datoj['ID']));
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            $ptksis = new Parttempkotizosistemo($linio['ID']);
            if($ptksis->aplikigxas($this->objektolisto)) {
                $kategorioj =
                    $ptksis->eltrovu_kategoriojn($this->kotizokalkulilo->partoprenanto,
                                                 $this->kotizokalkulilo->partopreno,
                                                 $this->kotizokalkulilo->renkontigxo);
                $kotizo =
                    $ptksis->eltrovu_bazan_kotizon($kategorioj);
                if ($kotizo < $minimumo or
                    "xxx" == $minimumo) {
                    $minimumo = $kotizo;
                    $trovita = array($ptksis, $kategorioj, $kotizo);
                }
            }
        }

        $this->detalolisto['baza'] =  array(
                  array('titolo' => kotizo_parttempa_titolo(),
                        'detaloj' => array('kategorioj' => $trovita[1],
                                           'dauxro' => $this->partoprentempo),
                        'valoro' => array('kvanto' => $minimumo,
                                          'valuto' => CXEFA_VALUTO))
                  );
        $this->sumo += $minimumo;
        
    }  // kalkulu_bazan_kotizon();


}  // class Parttempa_subkalkulilo


/**
 * kalkulas, kiom alta estus la TEJO/UEA-rabato, se tiu
 * persono estus TEJO/UEA-membro. Pli precize, kalkulas la diferencon
 * inter la kotizo, se li estus ne-membro, kaj se li estus membro.
 *
 * @param Partoprenanto $partoprenanto
 * @param Partopreno $partoprenao
 * @param Renkontigxo $renkontigxo
 * return number
 */
function kalkulu_tejo_rabaton($partoprenanto, $partopreno, $renkontigxo)
{
  $org_val = $partopreno->datoj['tejo_membro_kontrolita'];

  $partopreno->datoj['tejo_membro_kontrolita'] = 'j';
  $kalk_membro = new Kotizokalkulilo($partoprenanto, $partopreno, $renkontigxo);
  $kotizo_membro = $kalk_membro->restas_pagenda();

  $partopreno->datoj['tejo_membro_kontrolita'] = 'n';
  $kalk_nemembro = new Kotizokalkulilo($partoprenanto, $partopreno,
									   $renkontigxo);
  $kotizo_nemembro = $kalk_nemembro->restas_pagenda();

  // restarigo de la originalo ...
  $partopreno->datoj['tejo_membro_kontrolita'] = $org_val;

  return $kotizo_nemembro - $kotizo_membro;
}