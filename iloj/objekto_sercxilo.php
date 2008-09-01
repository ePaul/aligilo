<?php

  /**
   * Sercxilo-klaso.
   *
   * La celo de tiu klaso estas anstatauxigi {@link sercxu()}.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   * Ĝenerala serĉ-funkcio.
   *
 * Serĉas en la datumbazo kaj montras la rezulton en HTML-tabelo.
 *
 * @param string $sql - la SQL-demando, ekzemple kreita de
 *              {@link datumbazdemando()} (sen ordigo).
 *
 * @param array $ordigo  array(),
 *   - $ordigo[0]:  laŭ kiu kolumno la rezultoj ordiĝu
 *   - $ordigo[1]:  ĉu la rezultoj ordiĝu pligrandiĝanta ("ASC") aŭ
 *                malpligrangiĝanta ("DESC")?
 *
 * @param array $kolumnoj
 *     array() de array-oj, por la unuopaj kolumnoj. Por ĉiu kolumno,
 *      la array enhavu la sekvajn ses komponentojn (ĉiuj ĉeestu, eĉ
 *       se malplenaj):
 *   - [0] - aŭ nomo aŭ numero de kampo de la SQL-rezulto.
 *          Prefere uzu nomon, ĉar per numero la ordigo ne funkcias.
 *   - [1] - la titolo de la kolumno
 *   - [2] - La teksto, kiu aperu en la tabelo. Se vi uzas XXXXX (jes, 5 iksoj),
 *          tie aperas la valoro el la SQL-rezulto.
 *   - [3] - aranĝo: ĉu la valoroj aperu dekstre ("r"), meze ("z") aŭ
 *             maldekstre ("l") en la tabelkampo?
 *   - [4] - se ne "", la celo de ligilo. (Alikaze ne estos ligilo.)
 *   - [5] - Se estas ligilo, kaj ĉi tie ne estas -1, dum klako al
 *          la ligilo en la menuo elektiĝas la persono, kies identifikilo
 *          estas en la kampo, kies nomo/numero estas ĉi tie.
 *
 *   nova formato (de la antauxaj valoroj)
 *      - [kampo] - aŭ nomo aŭ numero de kampo de la SQL-rezulto.
 *                 Prefere uzu nomon, ĉar per numero la ordigo ne funkcias.
 *      - [titolo]  - la titolo de la kolumno
 *      - [tekstosxablono] - La teksto, kiu aperu en la tabelo. Se
 *                  vi uzas XXXXX (jes, 5 iksoj), tie aperas la valoro
 *                  el la SQL-rezulto.
 *      - [arangxo]  - aranĝo: ĉu la valoroj aperu dekstre ("r"),
 *                     meze ("z") aŭ maldekstre ("l") en la tabelkampo?
 *      - [ligilsxablono] - se ne "", la celo de ligilo.
 *                         (Alikaze ne estos ligilo.)
 *      - [ligilkampo]  - kiel kampo, sed nur uzata por la ligo, ne
 *                         por la teksto. Se mankas, ni uzas kampo anstatauxe.
 *      - [menuidkampo] - Se estas ligilo, kaj ĉi tie ne estas -1, dum
 *                        klako al la ligilo en la menuo elektiĝas la
 *                        persono, kies identifikilo estas en la kampo,
 *                        kies nomo/numero estas ĉi tie.
 *                        TODO: pli bona priskribo!
 *
 *   aldone:
 *      - [anstatauxilo] - aux array() aux nomo de funkcio vokinda (vidu cxe
 *                             $extra pri la funkciado de tio)
 *
 * @param array $sumoj
 *          por ĉiu sum-linio ekzistas array (en $sumoj). En ĉiu linio-array
 *      estas po unu element-array por kolono, kun tri elementoj:
 *   - [0] - La teksto de la kampo. Se vi uzas XX, tie aperos la rezulto
 *         de la sumado.
 *   - [1] - La speco de la sumado. eblecoj:
 *            --  A - simple nur kalkulu, kiom da linioj estas.
 *            --  J - kalkulu, kiom ofte aperas 'J' en la koncerna kampo
 *            --  E - kalkulu, kiom ofte enestas io en la koncerna kampo
 *            --  N - adiciu la numerojn en la koncerna kampo.
 *   - [3] - arangxo ('l', 'r', 'z' - vidu ĉe $kolumnoj - [3].)
 *
 * @param string $identifikilo
 *           estas uzata kiel identigilo por memori la parametrojn de
 *           iu serĉado en la sesio. Por ĉiu $identifikilo ni memoras
 *           po la lastan opon da parametroj, kiuj estos uzata poste por
 *           aliaj ordigoj de la rezulto-tabelo.
 *
 * @param string $extra  aldonaj parametroj. Se tiaj ne ekzistas, eblas uzi 0.
 *      Alikaze estu array, kies sxlosiloj estu iuj el la sekve
 *      menciitaj. La valoroj havas ĉiam apartajn signifojn.
 *    - <samp>[Zeichenersetzung]</samp>
 *                 ebligas la anstataŭigon
 *                  de la valoroj per iu ajn teksto (aŭ HTML-kodo).
 *                la valoro estu array, kiu enhavu por ĉiu kolumno, kie
 *                okazu tia anstataŭigo (sxlosilo=numero de la kolumno,
 *                komencante per 0), plian array, kiu enhavu ĉiun
 *                anstataŭotan valoron kiel sxlosilo, la anstataŭontan
 *                valoron kiel valoro. Ekzemplo:<code>
 *       array('1' => array('j'=>'&lt;b><font color=green>prilaborata',
 *                          ''=>'&lt;b>&lt;font color=red>neprilaborata',
 *                          'n'=>'&lt;b>&lt;font color=red>neprilaborata'))</code>
 *          En kolumno 1 (en la teksto enmetota por XXXXX) ĉiu 'j' estas
 *          anstataŭita per "prilaborata", ĉiu '' kaj 'n' per "neprilaborata".
 *          En aliaj kolumnoj ne okazos tia anstataŭo.
 *    - [anstatauxo_funkcio]
 *               funkcias simile kiel "Zeichenersetzung",
 *               sed anstataŭ anstataŭa array() estu nomo de funkcio,
 *               kio estos vokata por eltrovi la valoron.
 *               Ĝi nur estos vokota unufoje por la tuta kampo, ne por
 *               ĉiu litero de ĝi.
 *    - [okupigxtipo]
 *               anstataŭigu en iu kolumno la okupiĝtipvaloron per
 *                    la nomon de tiu tipo.
 *               La valoro estu kolumnonumero. La valoro de la koncerna
 *               datumbazkampo estos donita al la funkcio okupigxtipo()
 *               (en iloj_sql), kaj ties rezulto estas la teksto en tiu
 *               kolumno.
 *            <strong>Tiu funkcio malaperos</strong>, anstataux 
 *               <code>'okupigxtipo' => 7</code>
 *            uzu (samefike):
 *               <code>anstatauxo_funkcio => (7 => 'okupigxtipo')</code>
 *
 *    - [litomanko]
 *               montru aparte, en kiuj noktoj ankoraŭ mankas litoj.
 *               La valoro estu kamponomo aŭ -numero.
 *               La valoro de tiu kampo estu partoprenidento.
 *               Je la fino de la linio (post la aliaj kolumnoj) estos
 *               montrita, en kiuj noktoj tiu partoprenanto jam havas
 *               liton, kaj en kiuj noktojn ankoraŭ mankas.
 *               Poste aperos ligilo "serĉu" al la ĉambrodisdono.
 *    - [tutacxambro]
 *               La valoro estu kamponomo aŭ -numero de kampo kun partopreno-ID.
 *               En aparta linio post ĉiu rezultlinio estos montrataj la
 *               datoj de la unua ĉambro, en kiu tiu partoprenanto loĝas.
 * @param int $csv - tipo de la rezulto. Eblaj valoroj:
 *   - 0 - HTML kun bunta tabelo
 *   - 1 - CSV (en HTML-ujo)
 *   - 2 - CSV por elsxuti, en Latina-1
 *   - 3 - CSV por elsxuti, en UTF-8
 * @param string $antauxteksto - teksto, kiu estu montrata antaŭ la tabelo.
 *                 (Ĝi estas uzata nur kun $proprakapo == 'jes').
 * @param string $almenuo se ĝi ne estas "", post la tabelo aperas ligo
 *                 "Enmeti en la maldekstra menuo", kies alklako
 *                 aldonas la rezulton en la maldekstra menuo.
 *                 Por ke tio funkciu, la sql-serĉfrazu redonu
 *                 kampojn nomitaj 'nomo', 'personanomo', 'renkNumero'
 *                 kaj 'ID' (kiu estu partoprenanto-ID).
 *               la valoro de $almenuo estos uzata kiel atentigo-teksto
 *                super la menuo.
 * @param string $proprakapo   - montras la tabelon ene de <html><body>-kadro, kun
 *                 ebla antaŭteksto. (Estas uzata nur, se $csv < 2.)
   */
function sercxu_objekte($sql, $ordigo, $kolumnoj, $sumoj, $identifikilo,
                $extra, $csv, $antauxteksto, $almenuo, $proprakapo = "jes")
{
    $sercxilo =& new Sercxilo();
    $sercxilo->metu_sql($sql);
    $sercxilo->metu_ordigon($ordigo);
    $sercxilo->metu_antauxtekston($antauxtekston);
    $sercxilo->metu_kolumnojn($kolumnoj);
    $sercxilo->metu_sumregulojn($sumoj);
    $sercxilo->metu_identigilon($identifikilo);
    $sercxilo->metu_ekstrajxojn($extra);
    $sercxilo->metu_antauxtekston($antauxteksto);
    $sercxilo->metu_menutitolon($almenuo);

    switch($csv)
        {
        case 0:
            if ($proprakapo) {
                $sercxilo->montru_rezulton_en_HTMLdokumento();
            }
            else {
                $sercxilo->montru_rezulton_en_HTMLtabelo();
            }
            break;
        case 1:
            // TODO: CSV
            $sercxilo->montru_rezulton_en_HTMLcsv();
            break;
        case 2:
            $sercxilo->montru_rezulton_en_Latin1Lcsv();
            break;
        case 3:
            $sercxilo->montru_rezulton_en_UTF8csv();
            break;
        }
}


  /**
   * Sercxilo-klaso.
   *
   * La celo de tiu klaso estas anstatauxigi {@link sercxu()}.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */
class Sercxilo {


    var $sql;
    var $antauxteksto;
    var $kolumnoj;
    var $sumoj;
    var $extra;
    var $almenuo;

    var $identigilo;
    var $ordigo;

//     /**
//      * @var array
//      */
//     var $trovajxoj;

    /**
     * kreas novan sercxilon.
     */
    function Sercxilo()
    {
    }


    /**
     * @param sqlstring $sql la SQL-cxeno (sen ordigo), kiu estu uzata
     *  cxi tie.
     */
    function metu_sql($sql) {
        $this->sql = $sql;
    }

    /**
     * kreas datumbazdemandon (kiel per {@link datumbazdemando()}) kaj
     *  uzas la rezulton kiel SQL-cxeno por tiu cxi objekto.
     * @param mixed $... la parametroj kiel por {@link datumbazdemando()}.
     */
    function datumbazdemando() {
        $argumentoj = func_get_args();
        $this->sql = call_user_func_array("datumbazdemando", $argumentoj);
    }

    function metu_antauxtekston($antauxteksto) {
        $this->antauxteksto = $antauxteksto;
    }

    /**
     * metas (la) kolumno-regulojn.
     */
    function metu_kolumnojn($kolumnoj) {
        foreach($kolumnoj AS $i => $kol) {
            $this->difinu_kolumnon($i, $kol);
        }
    }


    /**
     * difinas la regulojn por unu kolumno.
     *
     * @param int $kolnum la numero de la kolumno, komencigxante je 0.
     * @param array $reguloj la reguloj. Aux array() kun la jenaj eroj:
     *      - [kampo] - aŭ nomo aŭ numero de kampo de la SQL-rezulto.
     *                 Prefere uzu nomon, ĉar per numero la ordigo ne funkcias.
     *      - [titolo]  - la titolo de la kolumno
     *      - [tekstosxablono] - La teksto, kiu aperu en la tabelo. Se
     *                  vi uzas XXXXX (jes, 5 iksoj), tie aperas la valoro
     *                  el la SQL-rezulto.
     *      - [arangxo]  - aranĝo: ĉu la valoroj aperu dekstre ("r"),
     *                     meze ("z") aŭ maldekstre ("l") en la tabelkampo?
     *      - [ligilsxablono] - se ne "", la celo de ligilo.
     *                         (Alikaze ne estos ligilo.) Povas enhavi XXXXX,
     *                         tiam ni tie enmetas la valoron de la kampo
     *                         indikita per ligilkampo (aux kampo, se
     *                         ligilkampo mankas).
     *      - [ligilkampo]  - kiel kampo, sed nur uzata por la ligo, ne
     *                         por la teksto. Se mankas, ni uzas kampo
     *                        anstatauxe.
     *      - [menuidkampo] - Se estas ligilo, kaj ĉi tie ne estas -1, dum
     *                        klako al la ligilo en la menuo elektiĝas la
     *                        persono, kies identifikilo estas en la kampo,
     *                        kies nomo/numero estas ĉi tie.
     *                        TODO: pli bona priskribo!
     *      - [anstatauxilo] - aux array() aux nomo de funkcio vokinda, por
     *                         konverti la valoron al io alia antaux enmeti
     *                         gxin en la sxablonon.
     *  aux array() kun numeraj indeksoj, kiuj estas same traktitaj kiel
     *        la tekstaj. Ankaux miksite eblas, tiam la numeraj estas uzataj
     *        lauxvice, kiam teksta mankas.
     *   Tiu cxi funkcio konvertas la regulojn al la interne uzata
     *    teksta formo.
     */
    function difinu_kolumnon($kolnum, $reguloj) {
        $kol = array();

        $i = 0;
        $nomoj = array('kampo', 'titolo', 'tekstosxablono',
                        'arangxo', 'ligilsxablono', 'menuidkampo',
                       'ligilkampo', 'anstatauxilo');

        foreach($nomoj AS $nomo) {
            if (isset($reguloj[$nomo])) {
                $kol[$nomo] = $reguloj[$nomo];
            }
            else {
                $kol[$nomo] = $reguloj[$i];
                $i++;
            }
        }
        $this->kolumnoj[$kolnum] = $kol;
    }


    function metu_sumregulojn($sumoj) {
        $this->sumoj = $sumoj;
    }

    function metu_ekstrajxojn($extra) {
        $this->extra = $extra;
    }

    function metu_menutitolon($almenuo) {
        $this->almenuo = $almenuo;
    }

    function metu_identigilon($id) {
        $this->identigilo = $id;
    }


    /**
     * difinas la ordigon uzotan.
     * @param string|array $kampo nomo de la kampo, laux kiu ni ordigu.
     *                     Alternative: array(kamponomo, direkto)
     * @param string $direkto cxu komencante je la malgrandaj ("asc") aux
     *                        je la grandaj ("desc")? (Ne necesa, se en unua
     *                        parametro jam estis array().)
     */
    function metu_ordigon($kampo, $direkto="") {
        if (is_array($kampo)) {
            $this->ordigo = array($kampo[0], strtolower($kampo[1]));
        }
        else {
            $this->ordigo = array($kampo, strtolower($direkto));
        }
    }

    /* Rezult-montriloj */


    /**
     * @todo implementado
     */
    function montru_rezulton_en_HTMLcsv() {
    }

    /**
     * @todo implementado
     */
    function montru_rezulton_en_UTF8csv() {
    }

    /**
     * @todo implementado
     */
    function montru_rezulton_en_Latin1csv() {
    }

    /**
     * Montras la rezulton de la sercxo en HTML-tabelo.
     */
    function montru_rezulton_en_HTMLtabelo() {
        
        echo "<table class='sercxrezulto'>\n";
        $this->metu_HTMLtitollinion();

        $sumigilo = &new Sumigilo($this->sumoj);
        $rez = $this->sercxu();
        $lininumero = 0;
        while($linio = mysql_fetch_array($rez))
            {
                debug_echo( "<!-- " . var_export($linio, true) . "-->");
                $lininumero++;
                $this->metu_HTMLtabellinion($linio, $lininumero, $sumigilo);
            }
        // TODO
        $sumigilo->montru_HTMLsumojn();
        echo "</table>\n";
        if ($this->almenuo) {
            // TODO: pripensi uzi la sercxilo-objekton (via sesia
            //        variablo) por tio.
            echo "<p>";
            ligu("menuo.php?sercxfrazo=". $this->sql .
                 "&listotitolo=" . $this->almenuo,
                 "Enmeti la personojn en la maldekstran menuon",
                 "is-aligilo-menuo");
            echo "</p>\n";
        }
    }


    /**
     * Montras la rezulton de sercxo en formo de
     * kompleta HTML-dokumento.
     * @uses montru_rezulton_en_HTMLtabelo()
     */
    function montru_rezulton_en_HTMLdokumento() {
        HtmlKapo();
        eoecho("<p>" . $this->antauxteksto . "</p>\n");
        $this->montru_rezulton_en_HTMLtabelo();
        HtmlFino();
    }
  

    /* privataj funkcioj */


    /**
     * metas titollinion por HTML-tabelo.
     *
     * Gxi enhavas ligojn por sxangxi la ordigon.
     */
    function metu_HTMLtitollinion() {
        $memligo =
            "sercxrezultoj.php?elekto=lasta_sercxo&id=" . $this->identigilo;
        $_SESSION['lasta_sercxo'][$this->identigilo] = $this;

        $inversa = array("asc" => "desc",
                         "desc" => "asc");


        echo "<tr class='titolo'>\n";
        foreach($this->kolumnoj AS $kolumno)
            {
                $kampo = $kolumno['kampo'];
                $titolo = $kolumno['titolo'];

                echo "  <th class='" .
                    $GLOBALS['arangxklaso'][$kolumno['arangxo']] ."'>";

                $direkto = $this->ordigo[1];
                if ($this->ordigo[0]==$kampo)
                    {
                        ligu($memligo .
                             "&ordigo=" . $kampo .
                             "&direkto=" . $inversa[$direkto],
                             $titolo .
                             " <img src='bildoj/" . $direkto .
                             "_order.gif' alt='(". $direkto.")' />");
                    }
                else if (is_numeric($kampo))
                    {
                        echo $titolo;
                    }
                else 
                    {
                        ligu($memligo . "&ordigo=" . $kampo . "&direkto=asc",
                             $titolo);
                    }
                echo "</th>\n";
            }  // foreach
        echo "</tr>\n";
    }


    /**
     * metas unu linion de la tabelo.
     *
     * @param array $linio la datumoj el la datumbazo
     * @param int $lininumero la numero de la linio (nur uzota
     *                        por kolorigi la tabelon)
     * @param Sumigilo $sumigilo objekto por kalkuli sumojn.
     */ 
    function metu_HTMLtabellinion($linio, $lininumero, &$sumigilo) {
        $klaso = array("para", "malpara");
        echo "<tr onmouseover='marku(this)' onmouseout='malmarku(this)' " .
            " class='".$klaso[$lininumero%2] ."'>\n";
        foreach($this->kolumnoj AS $i => $kolumno) {
            debug_echo( "<!-- kolumno: " . var_export($kolumno, true) . "-->");

            $kamponomo     = $kolumno['kampo'];
            $tekstsxablono = $kolumno['tekstosxablono'];
            $arangxdirekto = $kolumno['arangxo'];
            $ligilsxablono = $kolumno['ligilsxablono'];
            $idkampo       = $kolumno['menuidkampo'];
            $ligilkampo    = $kolumno['ligilkampo'];

            $valoro = $linio[$kamponomo];

            debug_echo( "<!-- valoro: " . $valoro . ", tekstsxablono: ".
                        $tekstsxablono . " -->");
            
            $ligilvaloro = $linio[$ligilkampo]
                or $ligilvaloro = $valoro;

            if ($idkampo and $idkampo != -1) {
                $idkampovaloro = $linio[$idkampo];
            }
            else {
                $idkampovaloro = false;
            }

            $sumigilo->aldonu_kampon($i, $valoro);

            echo "  <td class='" . $GLOBALS['arangxklaso'][$arangxdirekto] .
                "'>";

            $teksto = $this->formatu_tekston($i, $valoro, $tekstsxablono);

            debug_echo( "<!-- teksto: " . $teksto . " -->" );


            if ($ligilsxablono) {
                // uzu ligon
                $ligilcelo = str_replace('XXXXX',
                                         $ligilvaloro,
                                         $ligilsxablono);
                if ($idkampovaloro) {
                    // aldone elektu en la maldekstra kadro
                    $ceteraj = array('onclick'
                                     => "doSelect('" .$idkampovaloro. "');");
                }
                else {
                    $ceteraj = "";
                }
                
                ligu($ligilcelo, $teksto, '', $ceteraj);
            }
            else {
                eoecho($teksto);
            }
            echo "</td>\n";
        }
        if (isset($this->extra['litomanko']))
            {
                montru_litojn_de_ppeno($linio[$this->extra['litomanko']]);
            }
        if (isset($this->extra['tutacxambro']))
            {
                echo "</tr>\n<tr class='" . $klaso[$lininumero%2] ."'>\n";
                echo "  <td colspan='". count($this->kolumnoj)."'>";
                $this->montru_cxambron($linio[$this->extra['tutacxambro']]);
                echo "</td>\n";
            }
        echo "</tr>";
    }

    /**
     * montras la unuan cxambron (laux iu ajn regulo) de 
     * partoprenanto, kun la kunlogxdeziroj de cxiuj
     * enlogxantoj.
     *
     * @param int $ppenoID identigilo de partopreno-objekto.
     */
    function montru_cxambron($ppenoID)
    {
        //        $partoprenanto = new Partoprenanto($row[0]);
        $partopreno = new Partopreno($ppenoID);
        $partoprenanto =
            new Partoprenanto($partopreno->datoj['partoprenantoID']);
        // echo "CX: ".eltrovu_cxambrojn($row[$extra['tutacxambro']]);
        $cxambroinformoj = mysql_fetch_assoc(eltrovu_cxambrojn($ppenoID));
        $cxambro = $cxambroinformoj['cxambro'];
        if ($cxambro)
			{
                montru_kunlogxantojn($cxambro);
                montru_cxambron($cxambro,$_SESSION["renkontigxo"],
                                $partoprenanto, $partopreno,
                                'granda');
			}
    }

    function formatu_tekston($kolumnonumero, $valoro, $sxablono) {
        $ze_i = $this->extra['Zeichenersetzung'][$kolumnonumero];
        $af_i = $this->extra['anstatauxo_funkcio'][$kolumnonumero];
        $k_i_a = $this->kolumnoj[$kolumnonumero]['anstatauxilo'];
        
        if (is_array($k_i_a) and isset($k_i_a[$valoro])) {
            $valoro = $k_i_a[$valoro];
        }
        else if (function_exists($k_i_a)) {
            $valoro = $k_i_a($valoro);
        }
        else if (isset($ze_i[$valoro])) {
            $valoro = $ze_i[$valoro];
        }
        else if ($af_i) {
            $valoro = $af_i($valoro);
        }
        
        return str_replace('XXXXX', $valoro, $sxablono);
    }


    /**
     * sercxas, kaj redonas la rezultan MySQL-objekton.
     *
     * @return mysqlres MySQL-resulta objekto.
     */
    function sercxu()
    {
        $sql = $this->sql .
            " ORDER BY " . $this->ordigo[0] . " " . $this->ordigo[1];

        echo "<!-- sql: " . var_export($sql, true) . "-->";

        $rez = sql_faru($sql);
        //        if (DEBUG)
            {
                echo "<!-- sql-rezulto: " . var_export($rez, true) . "-->";
            }
        return $rez;
    }

  
} // class sercxilo


class Sumigilo {

    var $reguloj;
    var $sumoj;

    function Sumigilo($reguloj) {
        $this->reguloj = $reguloj;
        $this->sumoj = array();
        foreach($reguloj AS $i => $regullinio) {
            if (count($regullinio)) {
                $this->sumoj[$i] = array_fill(0, count($regullinio), 0);
            }
        }
    }

    /**
     * aldonas kampon al la sumoj.
     *
     * @param int   $kolumno la numero de la kolumno sumigenda
     * @param mixed $valoro la valoro de la kampo
     */
    function aldonu_kampon($kolumno, $valoro)
    {
        foreach ($this->reguloj AS $linio => $reguloj)
            {
                $sumo =& $this->sumoj[$linio][$kolumno];
                switch($reguloj[$kolumno][1])
                    {
                    case 'A':
                        $sumo += 1;
                        break;
                    case 'J':
                        if ($valoro == 'J') {
                            $sumo += 1;
                        }
                        break;
                    case 'Z': 
                    case 'E':
                        // tiuj du kodoj faris kvazaux la samon en sercxu().
                        if ($valoro) {
                            $sumo += 1;
                        }
                        break;
                    case 'N':
                        if (is_numeric($valoro)) {
                            $sumo += $valoro;
                        }
                        break;
                    }   // switch
            } // foreach
    } // aldonu_kampon()
    

    /**
     * Montras la sum-liniojn tauxgajn por HTML-tabelo.
     */
    function montru_HTMLsumojn() {
        foreach($this->reguloj AS $linio => $regullinio) {
            echo "<tr class='sumoj'>\n";
            foreach($regullinio AS $kolumno => $regulo) {
                echo "  <td class='" . $GLOBALS['arangxoklaso'][$regulo[2]] .
                    "'>";
                eoecho(str_replace('XX', $this->sumoj[$linio][$kolumno],
                                   $regulo[0]));
                echo "</td>\n";
            }
            echo "</tr>\n";
        }
    }

}




/**
 * La HTML-klaso (por CSS-uzo) de tabelcxeloj, kiel
 * funkcio de unuliteraj mallongigoj.
 * @global array $GLOBALS['arangxklaso']
 */
$GLOBALS['arangxklaso'] = array("r" => "dekstren",
                                "d" => "dekstren",
                                "l" => "maldekstren",
                                "m" => "maldekstren",
                                "z" => "centren",
                                "c" => "centren");



?>