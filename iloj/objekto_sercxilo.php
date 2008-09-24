<?php

  /**
   * Serĉilo-klaso.
   *
   * La celo de tiu klaso estas anstataŭigi {@link sercxu()}.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @since revizo 201 (2008-09-01)
   * @version $Id$
   * @copyright 2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   * Ĝenerala serĉ-funkcio.
   *
   * (Preskaŭ kompatibla anstataŭaĵo
   *   por {@link sercxu()}, de tie ankaŭ kopiita
   *   priskribo.)
   *
   * Serĉas en la datumbazo kaj montras la rezulton en HTML-tabelo
   * aŭ unu el diversaj CSV-variantoj.
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
   *   nova formato (de la antaŭaj valoroj)
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
   *                         por la teksto. Se mankas, ni uzas kampo anstataŭe.
   *      - [menuidkampo] - Se estas ligilo, kaj ĉi tie ne estas -1, dum
   *                        klako al la ligilo en la menuo elektiĝas la
   *                        persono, kies identifikilo estas en la kampo,
   *                        kies nomo/numero estas ĉi tie.
   *                        TODO: pli bona priskribo!
   *
   *   aldone:
   *      - [anstatauxilo] - aŭ array() aŭ nomo de funkcio vokinda (vidu ĉe
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
   *   - [3] - aranĝo ('l', 'r', 'z' - vidu ĉe $kolumnoj - [3].)
   *
   * @param string $identifikilo
   *           estas uzata kiel identigilo por memori la parametrojn de
   *           iu serĉado en la sesio. Por ĉiu $identifikilo ni memoras
   *           po la lastan opon da parametroj, kiuj estos uzata poste por
   *           aliaj ordigoj de la rezulto-tabelo.
   *
   * @param string $extra  aldonaj parametroj. Se tiaj ne ekzistas, eblas uzi 0.
   *      Alikaze estu array, kies ŝlosiloj estu iuj el la sekve
   *      menciitaj. La valoroj havas ĉiam apartajn signifojn.
   *    - <samp>[Zeichenersetzung]</samp>
   *                 ebligas la anstataŭigon
   *                  de la valoroj per iu ajn teksto (aŭ HTML-kodo).
   *                la valoro estu array, kiu enhavu por ĉiu kolumno, kie
   *                okazu tia anstataŭigo (ŝlosilo=numero de la kolumno,
   *                komencante per 0), plian array, kiu enhavu ĉiun
   *                anstataŭotan valoron kiel ŝlosilo, la anstataŭontan
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
   *            <strong>Tiu funkcio malaperos</strong>, anstataŭ 
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
   *   - 2 - CSV por elŝuti, en Latina-1
   *   - 3 - CSV por elŝuti, en UTF-8
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
   * @param string $proprakapo   se "jes" (defaŭlto), montras la tabelon ene
   *                   de <html><body>-kadro, kun ebla antaŭteksto. (Estas
   *                   uzata nur, se $csv < 2.)
   *
   * @uses Sercxilo
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
            $sercxilo->montru_rezulton_en_csvHTMLdokumento();
            break;
        case 2:
            $sercxilo->montru_rezulton_en_Latin1csv();
            break;
        case 3:
            $sercxilo->montru_rezulton_en_UTF8csv();
            break;
        }
}


  /**
   * Serĉilo-klaso.
   *
   * La celo de tiu klaso estas anstataŭigi {@link sercxu()}.
   *
   * @package aligilo
   * @subpackage iloj
   * @since revizo 201 (2008-09-01)
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */
class Sercxilo {

    /**
     * la uzenda SQL-kodo (sen ordigo)
     * @access private
     * @var sqlstring
     */
    var $sql;
    /**
     * teksto montrenda antaŭ la rezulto.
     * @access private
     * @var eostring
     */
    var $antauxteksto;
    /**
     * reguloj por difini la kolumnojn.
     * @see difinu_kolumnon()
     * @access private
     * @var array
     */
    var $kolumnoj;
    /**
     * reguloj por krei sumojn.
     * @see Sumilo
     * @access private
     * @var array
     */
    var $sumoj;
    /**
     * instrukcioj por aldonaj funkcioj.
     * @access private
     * @var array
     */
    var $extra;
    /**
     * teksto por montri super la elektolisto en la ĉefa menuo,
     * se ni tie montras tiun ĉi elekton.
     * @access private
     * @var eostring
     */
    var $almenuo;
    /**
     * identigilo por ebligi reordigadon de tabelo.
     * @access private
     * @var string
     */
    var $identigilo;
    /**
     * ordigo-maniero. $ordigo[0] estas la kamponomo, $ordigo[1] la direkto.
     * @access private
     * @var array
     */
    var $ordigo;


    /**
     * Cxu ni montru la mem-ligojn?
     * @var boolean|null Se null/nedifinita, la HTML-dokumentaj
     *  montrofunkcioj metos al true, la aliaj traktas null kiel false.
     */
    var $montras_memligojn;


    /**
     * kreas novan serĉilon.
     */
    function Sercxilo()
    {
    }


    /**
     * @param sqlstring $sql la SQL-ĉeno (sen ordigo), kiu estu uzata
     *  ĉi tie.
     */
    function metu_sql($sql) {
        $this->sql = $sql;
    }

    /**
     * kreas datumbazdemandon (kiel per {@link datumbazdemando()}) kaj
     *  uzas la rezulton kiel SQL-ĉeno por tiu ĉi objekto.
     * @param mixed $... la parametroj kiel por {@link datumbazdemando()}.
     */
    function metu_datumbazdemandon() {
        $argumentoj = func_get_args();
        $this->sql = call_user_func_array("datumbazdemando", $argumentoj);
    }

    /**
     * difinas la tekston montrota antaŭ la rezulto, se en propra HTML-paĝo.
     * @param eostring $antauxteksto
     */
    function metu_antauxtekston($antauxteksto) {
        $this->antauxteksto = $antauxteksto;
    }

    /**
     * metas (la) kolumno-regulojn.
     * @param array $kolumnoj listo de kolumnoj,
     *              po en la formato de {@link difinu_kolumnon()}.
     *   Anstataux unu array() ankaux eblas doni la kolumnojn
     *   unuope kiel pluraj parametroj.
     */
    function metu_kolumnojn($kolumnoj) {
        if (func_num_args() > 1) {
            $kolumnoj = func_get_args();
        }
        else if (!is_array(reset($kolumnoj))) {
            $kolumnoj = array($kolumnoj);
        }
        foreach($kolumnoj AS $i => $kol) {
            $this->difinu_kolumnon($i, $kol);
        }
    }


    /**
     * difinas la regulojn por unu kolumno.
     *
     * @param int $kolnum la numero de la kolumno, komenciĝante je 0.
     * @param array $reguloj la reguloj. Aŭ array() kun la jenaj eroj:
     *      - [kampo] - aŭ nomo aŭ numero de kampo de la SQL-rezulto.
     *                 Prefere uzu nomon, ĉar per numero la ordigo ne funkcias.
     *      - [titolo]  - la titolo de la kolumno
     *      - [tekstosxablono] - La teksto, kiu aperu en la tabelo. Se
     *                  vi uzas XXXXX (jes, 5 iksoj) tie aperas la valoro
     *                  el la SQL-rezulto. Se estas malplena, la valoro estos
     *                  rekte uzata.
     *      - [arangxo]  - aranĝo: ĉu la valoroj aperu dekstre ("r"),
     *                     meze ("z") aŭ maldekstre ("l") en la tabelkampo?
     *      - [ligilsxablono] - se ne "", la celo de ligilo.
     *                         (Alikaze ne estos ligilo.) Povas enhavi XXXXX,
     *                         tiam ni tie enmetas la valoron de la kampo
     *                         indikita per ligilkampo (aŭ kampo, se
     *                         ligilkampo mankas).
     *      - [ligilkampo]  - kiel kampo, sed nur uzata por la ligo, ne
     *                         por la teksto. Se mankas, ni uzas kampo
     *                        anstataŭe.
     *      - [menuidkampo] - Se estas ligilo, kaj ĉi tie ne estas -1, dum
     *                        klako al la ligilo en la menuo elektiĝas la
     *                        persono, kies identifikilo estas en la kampo,
     *                        kies nomo/numero estas ĉi tie.
     *                        TODO: pli bona priskribo!
     *      - [anstatauxilo] - aŭ array() aŭ nomo de funkcio vokinda, por
     *                         konverti la valoron al io alia antaŭ enmeti
     *                         ĝin en la ŝablonon.
     *  aŭ array() kun numeraj indeksoj, kiuj estas same traktitaj kiel
     *        la tekstaj. Ankaŭ miksite eblas, tiam la numeraj estas uzataj
     *        laŭvice, kiam teksta mankas.
     *   Tiu ĉi funkcio konvertas la regulojn al la interne uzata
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
                if (isset($reguloj[$i])) {
                    $kol[$nomo] = $reguloj[$i];
                }
                $i++;
            }
        }
        $this->kolumnoj[$kolnum] = $kol;
    }

    /**
     * difinas regulojn por la sumigoj.
     * @param array $sumoj
     */
    function metu_sumregulojn($sumoj) {
     
        $this->sumoj = $sumoj;
    }

    /**
     * difinas aldonajn regulojn, kion fari pri la rezulto.
     * @param array $extra
     */
    function metu_ekstrajxojn($extra) {
        $this->extra = $extra;
    }

    /**
     * difinas la tekston montrota super la elektolisto,
     * kiam ni metos tiun ĉi elekton en la menuon.
     * (nur en la HTML-varianto.)
     * @param eostring $almenuo se "", ne eblos meti ĝin en la menuon.
     */
    function metu_menutitolon($almenuo) {
        $this->almenuo = $almenuo;
    }

    /**
     * difinas identigilon, uzata por povi
     * reordigi la rezulton.
     * @param string $id identigilo
     */
    function metu_identigilon($id) {
        $this->identigilo = $id;
    }


    /**
     * difinas la ordigon uzotan.
     * @param string|array $kampo nomo de la kampo, laŭ kiu ni ordigu.
     *                     Alternative: array(kamponomo, direkto)
     * @param string $direkto ĉu komencante je la malgrandaj ("asc") aŭ
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

    /**
     * difinas, cxu (en la HTMLaj versioj) la mem-ligoj komence aperu aux ne.
     * @param boolean $montru se jes, ni montras, alikaze ne.
     */
    function metu_memligomontradon($montru) {
        $this->montras_memligojn = $montru;
    }

    /* ************ Rezult-montriloj *********** ************ ************ */

    /**
     * gxenerala montrilo.
     * @param string $tipo unu el la sekvaj valoroj:
     *       - <val></val>
     * 
     */
    function montru_rezulton_en_tipo($tipo) {
        switch($tipo) {
        case 'HTMLcsvDoc':
            $this->montru_rezulton_en_csvHTMLdokumento();
            return;
        case 'HTMLcsvPar':
            $this->montru_rezulton_en_HTMLcsv();
            return;
        case 'HTMLcsvDiv':
            echo "<div class='csvWrap'>\n";
            $this->montru_rezulton_en_HTMLcsv();
            echo "</div>\n";
            return;
        case 'HtmlTabelo':
            $this->montru_rezulton_en_HTMLtabelo();
            return;
        case 'HTMLtabeloDoc':
        case '':
            $this->montru_rezulton_en_HTMLdokumento();
            return;
        case 'UTF8csv':
            $this->montru_rezulton_en_UTF8csv();
            exit();
        case 'Latin1csv':
            $this->montru_rezulton_en_Latin1csv();
            exit();
        case 'puraCSV':
            $this->montru_rezulton_en_pura_CSV();
            exit();
        }
    }


    /**
     * Kreas tutan HTML-dokumenton kun {@link antauxtektsto} kaj CSVeca enhavo.
     *
     * Uzebla por kopii rekte el la retumilo.
     * @uses montru_rezulton_en_HTMLcsv()
     */
    function montru_rezulton_en_csvHTMLdokumento() {
        if (!isset($this->montras_memligojn)) {
            $this->montras_memligojn = true;
        }

        HtmlKapo();
        eoecho("<p>" . $this->antauxteksto . "</p>");
        $this->montru_rezulton_en_HTMLcsv();
        HtmlFino();
    }

    /**
     * Kreas HTML-paragrafon kun CSV-eca teksto (t.e. dividita per ";"
     * kaj novaj linioj por ĉiu CSV-linio.
     *
     * en aparta paragrafo estos ligoj por meti al menuo, kaj por
     * montri la rezultojn en diversaj formatoj.
     *
     * @uses kreu_csv_rezulton()
     */
    function montru_rezulton_en_HTMLcsv() {
        $elementformatilo = create_function('$a', 'eoecho("$a;");');
        $linfino = create_function('', 'echo "<br/>\n";');
        echo("<p>\n");
        $this->kreu_csv_rezulton($elementformatilo, $linfino);
        echo("</p>");
        $this->printu_memligojn();
    }

    /**
     * printas HTML-an paragrafon kun ligoj al
     * alternativaj rezulto-tipoj.
     */
    function printu_memligojn()
    {
        if ($this->montras_memligojn) {
            $klaso = "memligoj-montrataj";
        }
        else {
            $klaso = "memligoj-kasxitaj";
        }
        echo "<p class='". $klaso ."' id='memligoj-" .$this->identigilo . "'>\n";
        if ($this->almenuo) {
            // TODO: pripensi uzi la serĉilo-objekton (via sesia
            //        variablo) por tio.
            ligu("menuo.php?sercxfrazo=". $this->sql .
                 "&listotitolo=" . $this->almenuo,
                 "Enmeti la personojn en la maldekstran menuon",
                 "is-aligilo-menuo");
        }
        $memligo = $this->donu_memligon();
        foreach(array('HTMLtabeloDoc' => "en Tabelo",
                      'UTF8csv' => "en CSV (por els^uti)",
                      'HTMLcsvDoc' => "en CSV (por kopii)")
                AS $tipo => $teksto) {
            ligu($memligo . "&tipo=" . $tipo,
                 "la sama rezulto " . $teksto);
        }
        echo "</p>\n";
    }


    /**
     * Kreas CSV-dokumenton koditan en UTF-8 kaj ofertas
     * ĝin por elŝutado.
     *
     * @uses kreu_csv_rezulton()
     */
    function montru_rezulton_en_UTF8csv() {
        header("Content-Type: text/csv; charset=UTF-8");
        header("Content-Disposition: attachment; filename=csv_export.txt");

        $elementformatilo = create_function('$a', 'echo uni("$a;");');
        $linfino = create_function('', 'echo "\n";');
        $this->kreu_csv_rezulton($elementformatilo, $linfino);
    }


    /**
     * Kreas CSV-dokumenton koditan en UTF-8 (sed Eo-signoj en
     * c^-kodigo restas tiaj) kaj ofertas ĝin por elŝutado.
     *
     * @uses kreu_csv_rezulton()
     */
    function montru_rezulton_en_pura_csv() {
        header("Content-Type: text/csv; charset=UTF-8");
        header("Content-Disposition: attachment; filename=csv_export.txt");

        $elementformatilo = create_function('$a', 'echo ("$a;");');
        $linfino = create_function('', 'echo "\n";');
        $this->kreu_csv_rezulton($elementformatilo, $linfino);
    }


    /**
     * Kreas CSV-dokumenton koditan en Latin-1 (ISO-8859-1) kaj ofertas
     * ĝin por elŝutado. (Eblaj ^c-koditaj eosignoj ne estos
     * transformitaj, sed cxiuj unikodajxoj estas transformitaj
     * al Latina-1.)
     *
     * @uses kreu_csv_rezulton()
     */
    function montru_rezulton_en_Latin1csv() {
        header("Content-Type: text/csv; charset=ISO-8859-1");
        header("Content-Disposition: attachment; filename=csv_export.txt");

        $elementformatilo =
            create_function('$a', 'echo iconv("utf-8", "iso-8859-1", "$a;");');
        $linfino = create_function('', 'echo "\n";');
        $this->kreu_csv_rezulton($elementformatilo, $linfino);
    }


    /**
     * Montras la rezulton de la serĉo en HTML-tabelo.
     *
     * @uses metu_HTMLtitollinion()
     * @uses metu_HTMLlinion()
     */
    function montru_rezulton_en_HTMLtabelo() {
        
        echo "<table class='sercxrezulto'>\n";
        $this->metu_HTMLtitollinion();

        $sumigilo = &new Sumigilo($this->sumoj, $this);
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
        $this->printu_memligojn();
    }


    /**
     * Montras la rezulton de serĉo en formo de
     * kompleta HTML-dokumento.
     * @uses montru_rezulton_en_HTMLtabelo()
     */
    function montru_rezulton_en_HTMLdokumento() {
        if (!isset($this->montras_memligojn)) {
            $this->montras_memligojn = true;
        }
        HtmlKapo();
        eoecho("<p>" . $this->antauxteksto . "</p>\n");
        $this->montru_rezulton_en_HTMLtabelo();
        HtmlFino();
    }
  

    /* privataj funkcioj */


    /**
     * Tre ĝenerala CVS-kreilo.
     *
     * Por formati la unuopajn elementojn kaj dividi la liniojn,
     * ni uzas po unu funkcio, donita de la vokanto (kiel funkcinomo).
     *
     * @param string $elementformatilo nomo de funkcio por formati
     *                  (kaj eldoni) la elementojn.
     * @param string $linfino nomo de funkcio por krei linfinojn.
//     * @access protected
     * @uses kreu_csv_titollinion()
     * @uses kreu_csv_linion()
     * @uses sercxu()
     */
    function kreu_csv_rezulton($elementformatilo, $linfino)
    {
        $this->kreu_csv_titollinion($elementformatilo, $linfino);
        $rez = $this->sercxu();
        while($linio = mysql_fetch_assoc($rez)) {
            $this->kreu_csv_linion($linio, $elementformatilo, $linfino);
        }
    }

    /**
     * kreas la titollinion de CSV-dokumento.
     * @param string $elementformatilo nomo de funkcio por formati
     *                  (kaj eldoni) la elementojn.
     * @param string $linfino nomo de funkcio por krei linfinojn.
     * @access private
     */
    function kreu_csv_titollinion($elementformatilo, $linfino) {
        foreach($this->kolumnoj AS $kolumno) {
            if (isset($kolumno['titolo'])) {
                $elementformatilo($kolumno['titolo']);
            } else {
                $elementformatilo($kolumno['kampo']);
            }
        }
        $linfino();
    }


    /**
     * kreas linion de CSV.
     * @param array $linio la SQL-rezulta kolekto.
     * @param string $elementformatilo nomo de funkcio por formati
     *                  (kaj eldoni) la elementojn.
     * @param string $linfino nomo de funkcio por krei linfinojn.
     * @access private
     */
    function kreu_csv_linion($linio, $elementformatilo, $linfino) {
        foreach($this->kolumnoj AS $i => $kolumno)
            {
                $valoro = $linio[$kolumno['kampo']];
                $tekstsxablono = $kolumno['tekstosxablono'];
                $teksto = $this->formatu_tekston($i, $valoro, $tekstsxablono);
                $elementformatilo($teksto);
            }
        $linfino();
    }


    function donu_memligon($kun_ordigo = false) {
        $ligo = "sercxrezultoj.php?elekto=lasta_sercxo&id="
            . $this->identigilo;
        if ($kun_ordigo) {
            $ligo .= "&ordigo=" . $this->ordigo[0] .
                "&direkto=". $this->ordigo[1];
        }
        $_SESSION['lasta_sercxo'][$this->identigilo] = $this;

        return $ligo;

    }


    /**
     * metas titollinion por HTML-tabelo.
     *
     * Ĝi enhavas ligojn por ŝanĝi la ordigon.
     * @access private
     */
    function metu_HTMLtitollinion() {
        $memligo =$this->donu_memligon();

        $inversa = array("asc" => "desc",
                         "desc" => "asc");


        echo "<tr class='titolo'>\n";
        foreach($this->kolumnoj AS $kolumno)
            {
                $kampo = $kolumno['kampo'];
                if (isset($kolumno['titolo'])) {
                    $titolo = $kolumno['titolo'];
                } else {
                    $titolo = $kampo;
                }

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
     * @access private
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
     * montras la unuan ĉambron (laŭ ajna sistemo) de 
     * partoprenanto, kun la kunloĝdeziroj de ĉiuj
     * enloĝantoj.
     *
     * @param int $ppenoID identigilo de partopreno-objekto.
     * @access private
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


    /**
     * formatas tekston per ŝablono.
     * Antaŭe ni eble faras iujn anstataŭojn, laŭ la reguloj donitaj en
     * la kolumnoj aŭ extraĵoj.
     *
     * @access private
     * @param int $kolumnonumero
     * @param mixed $valoro
     * @param eostring $sxablono teksta ŝablono, enhavu XXXXX, kie la
     *                 anstataŭita teksto estos enmetota.
     * @return la finformatita teksto.
     */
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
        
        if ($sxablono)
            return str_replace('XXXXX', $valoro, $sxablono);
        else
            return $valoro;
    }


    /**
     * serĉas, kaj redonas la rezultan MySQL-objekton.
     *
     * @return mysqlres MySQL-resulta objekto.
//     * @access protected kutime nur indas uzi tion ene de la klaso,
//     *                   sed eble vi ja trovas iun bonan kialon fari tion
//     *                   ekstere.
     */
    function sercxu()
    {
        $sql = $this->sql .
            " ORDER BY " . $this->ordigo[0] . " " . $this->ordigo[1];

        debug_echo( "<!-- sql: " . var_export($sql, true) . "-->");

        $rez = sql_faru($sql);
        if (DEBUG)
            {
                echo "<!-- sql-rezulto: " . var_export($rez, true) . "-->";
            }
        return $rez;
    }

  
} // class sercxilo



/**
 * helpa klaso de  {@link Sercxilo}.
 *
 * Uzata por sumigi valorojn kaj krei la sumo-liniojn en la HTML-versio.
 * @package aligilo
 * @subpackage iloj
 * @since revizo 201 (2008-09-01)
 * @author Martin Sawitzki, Paul Ebermann
 * @version $Id$
 * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */
class Sumigilo {

    /**
     * reguloj por krei la sumojn.
     * @access private
     * @var array 
     */
    var $reguloj;

    /**
     * la ĝisnunaj sumoj, en dudimensia kampo.
     * @access private
     * @var array
     */
    var $sumoj;


    /**
     * La sercxilo, por uzo en ligoj ktp.
     * @var Sercxilo
     */
    var $sercxilo;

    /**
     * kreas novan sumigilon.
     * @param array $reguloj la sumigo-reguloj.
     *          cxiu elemento de $reguloj korespondas al
     *          unu sumlinio (kiel array()), tie cxiu elemento
     *          (koresponde al unu cxelo) estu aux "" (= la cxelo
     *          estas malplena) aux  array(), kiu
     *           enhavas la tri elementojn:
     *              - [0]: tekstosxablono (XX por la loko,
     *                       kien enmeti la rezulton de la sumado
     *              - [1]: kiun sumigan agon oni faru:
     *                      - A - kalkulu kvanton de linioj
     *                      - J - kalkulu la aperojn de 'J' en la kampo.
     *                      - E, Z - kalkulu, kiom ofte la kampo ne estas
     *                                    malplena
     *                      - N - kalkulu sumon de la nombroj tie
     *                      - * - ne kalkulu ion ajn (uzebla, se la teksto
     *                            ne enhavas XX)
     *                      - X - ne sumigu ion ajn, kaj montru anstataux la
     *                            sumo en tiu kampo iun ilon por
     *                            kasxi/malkasxi la memligojn.
     *              - [2]: la arangxo-direkto, unu el 'm' (maldekstre),
     *                     'd' (dekstre), 'c' (centre) (aux 'l', 'r', 'z').
     * @param Sercxilo $sercxilo la sercxilo-objekto, uzata por
     *        eltrovi ankoraux nececajn konfigurojn
     *     (kiel {@link Sercxilo::identigilo identigilo},
     *     {@link Sercxilo::montras_memligojn montras_memligojn})
     */
    function Sumigilo($reguloj, $sercxilo) {
        $this->reguloj = $reguloj;
        $this->sercxilo = $sercxilo;
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
                    case '*':
                    case 'X':
                        // neniu sumado necesas.
                        break;
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
                        // tiuj du kodoj faris kvazaŭ la samon en sercxu().
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
     * Montras la sum-liniojn taŭgajn por HTML-tabelo.
     */
    function montru_HTMLsumojn() {
        foreach($this->reguloj AS $linio => $regullinio) {
            echo "<tr class='sumoj'>\n";
            foreach($regullinio AS $kolumno => $regulo) {
                if ($regulo) {
                
                    if ($regulo[1] == 'X') {
                        echo "  <td class='" . $GLOBALS['arangxklaso'][$regulo[2]] .
                    
                            " travidebla'>";
                        debug_echo("<!-- sercxilo: " . var_export($this->sercxilo, true) . "-->");
                        debug_echo("<!-- identigilo: " . $this->sercxilo->identigilo .
                                   ", montras: " . $this->sercxilo->montras_memligojn . "\n -->");
                        jes_ne_bokso('montru-memligojn-' .
                                     $this->sercxilo->identigilo,
                                     $this->sercxilo->montras_memligojn,
                                     'malkasxu("montru-memligojn-' . $this->sercxilo->identigilo . '", ' 
                                     .         '"memligoj-' .  $this->sercxilo->identigilo . '");');
                        eoecho($regulo[0]);
                    }
                    else {
                        echo "  <td class='" . $GLOBALS['arangxklaso'][$regulo[2]] .
                            "'>";
                        eoecho(str_replace('XX', $this->sumoj[$linio][$kolumno],
                                           $regulo[0]));
                    }
                    echo "</td>\n";
                }
                else {
                    echo "<td class='travidebla'/>\n";
                }
            }
            echo "</tr>\n";
        }
    }

}  // class Sumigilo



/**
 * La HTML-klaso (por CSS-uzo) de tabelĉeloj, kiel
 * funkcio de unuliteraj mallongigoj (germanaj kaj esperantaj).
 *
 * @global array $GLOBALS['arangxklaso']
 */
$GLOBALS['arangxklaso'] = array("r" => "dekstren",
                                "d" => "dekstren",
                                "l" => "maldekstren",
                                "m" => "maldekstren",
                                "z" => "centren",
                                "c" => "centren");



?>