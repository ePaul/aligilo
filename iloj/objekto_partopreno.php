<?php

  /*
   * La tabelnomoj cxi tie cxiam estas
   * la abstraktaj tabelnomoj. La traduko
   * al la konkretaj nomoj okazas en
   * iloj_sql.
   */


/**
 * Partopren-datumoj de iu partoprenanto
 * cxe iu renkontigxo. Tabelo "partoprenoj".
 *
 * ID
 * renkontigxoID
 * partoprenantoID
 * agxo             - agxo je la komenco de la renkontigxo (en jaroj).
 *                    Estas kalkulita el renkontigxo.de kaj partoprenanto.naskigxdato.
 * komencanto        - ne plu uzata en 2007 - anstatauxe 'nivelo'.
 * nivelo            - Lingva nivelo:
 *                     ? - ne elektis
 *                     f - flua parolanto
 *                     p - parolanto
 *                     k - komencanto
 * rimarkoj
 * invitletero        - J/N
 * invitilosendata    - J/N
 * pasportnumero
 * retakonfirmilo     - J/N
 * germanakonfirmilo  - J/N (volas ankaux germanlingvan konfirmilon)
 * 1akonfirmilosendata  -- (estu ...ita)
 * 2akonfirmilosendata  -- (estu ...ita)
 * partoprentipo  - p/t - parttempa/tuttempa
 * de             - dato
 * gxis           - dato
 * vegetare   - J/N/A - Vegetarano,Viandmangxanto,Vegano.
 * GEJmembro  - (en la aligxilo)
 * tejo_membro_laudire    j/n    - kion la homo asertis pri
 *                                  TEJO-membreco en la formularo.
 * tejo_membro_kontrolita j/n/?/i/p  - kion ni kontrolis per TEJO-funkciulo/per
 *                                   membrokarto/...
 *                                   j = estas membro
 *                                   n = ne estas membro (kaj ne igxas)
 *                                   ? = ni ankoraux ne kontrolis (defauxlto,
 *                                       ne plu aperu post la akceptado)
 *                                   i = igxas nova TEJO-membro dum tiu
 *                                       cxi renkontigxo
 *                                   p = pagis al TEJO/UEA ion, sed ne ricevas
 *                                       rabaton (ekzemple tro agxa UEA-membro,
 *                                       kategorio MG, aux pago por alia
 *                                       membro.)
 * tejo_membro_kotizo          - alteco de la TEJO-kotizo aux aliaj
 *                               pagoj al TEJO tra la IS-kaso (nur uzata,
 *                               se *_kontrolita = i aux = p. - alikaze
 *                               estu 0.).
 * surloka_membrokotizo - j/n/k  (gxis 2006)
 *                               -j = pagas kotizon surloke
 *                                n = ne necesas(jam pagis/enkasigrajto/
 *                                               eksterlandano)
 *                                k = elektis punan krompagon anstataux membrigxi
 * surloka_membrokotizo - j/i/h/n/a/k/? (ekde 2007)
 *              j = jam estis membro, kaj nun rekotizas
 *              i = igxis nova membro kaj nun kotizas
 *              h = igxis nova membro, sed ne devas kotizi
 *              n = ne devas membri (ekzemple eksterlandano)
 *              a = membro, jam antauxe pagis aux ne devas pagi
 *              k = devus membrigxi, sed preferas krompagi
 *              ? = ne jam traktita (tio ne okazu post la akceptado)
 *
 * membrokotizo         - alteco de membrokotizo aux krompago
 * KKRen
 * domotipo    - J/M - Junulargastejo / Memzorgantejo
 * litolajxo
 * kunmangxas  - J/N
 *               J - sen aldona pago kunmangxas
 *               N - ne kunmangxas
 *               K - krompagas por kunmangxi
 * listo - J/N  (volas aperi en interreta listo,
 *               ne volas aperi en interreta listo)
 * intolisto - J/N  (volas aperi en la post-renkontigxa partoprenintolisto,
 *                   ne volas aperi tie.)
 * pagmaniero   - Pagmaniero laux aligxilo
 *                 - uea  (UEA-konto de GEJ)
 *                 - gej  (GEJ-bsnkkonto)
 *                 - paypal
 *                 - persone (al KKRen-membro)
 *                Aliaj asocioj:
 *                 - hej
 *                 - jeb
 *                 - jefo
 *                 - iej
 * kunkiu
 * kunkiuID
 * cxambrotipo     - g = gea, u = unuseksa (n = negravas), '' (kelkaj malnovaj)
 * cxambro
 * dulita
 * ekskursbileto
 * tema     -.
 * distra    |
 * vespera   |-- propono kiel programkontribuo
 * muzika    | 
 * nokta    -'
 * donaco    -- TODO: cxu ankoraux uzata?
 * aligxdato     - alvenodato de la aligxo.
 * malaligxdato  - alvenodato de la malaligxo, se entute
 * alvenstato - tri eblecoj: 'a', 'v', 'm'.  
 *   [respondo de Martin:] alvenis / venos / malaligxis.
 *              ekde 2008:
 *               a = akceptita
 *               v = venos
 *               m = malaligxis
 *               i = vidita, sed ne akceptigxis
 *               n = ne venis/venos, sen malaligxi
 * traktstato
 * asekuri
 *    - Por kio necesas "asekuri"?
 *   [respondo de Martin:] Muß versichert werden / muß nicht versichert werden.
 *     
 * havas_asekuron  - (en la aligxformularo eblas diri "mi havas asekuron pri malsano" (--> J)
 *                    aux "mi ne havas tauxgan asekuron" (--> N)).
 * rabato        |
 * kialo         |-  (ne plu estas uzataj) 
 * surlokpago    |
 * aligxkategoridato   - uzu por doni alian daton ol la antauxpagdaton
 *                         por kalkuli la aligxkategorion
 * forgesu        - ?
 * kontrolata      - J/N
 * havasMangxkuponon - N/P/J  (Ne printita/printita/ricevis)
 * havasNomsxildon   - N/P/J  (Ne printita/printita/ricevis)
 */
class Partopreno extends Objekto
{


    /* Konstruilo */
    function Partopreno($id=0)
    {
        $this->Objekto($id,"partoprenoj");
    }


    /**
     * Detaloj en teksta formato por la konfirmilo.
     *
     */
    function konfirmilaj_detaloj()
    {
        $teksto =
            "\nlingva nivelo:             " . $this->nivelo() .
            "\nreta konfirmilo:           " .
            jes_ne($this->datoj['retakonfirmilo']) .
            "\ngermana konfirmilo:        " .
            jes_ne($this->datoj['germanakonfirmilo']) .
            "\npartoprentipo:             " . $this->partoprentipo() .
            ($this->datoj['partoprentipo'] != 't' ?
             ("\nde:                      " . $this->datoj['de'] .
              "\ng^is:                     " . $this->datoj['gxis']
              ) : "" ) .
            "\nmang^maniero:                " . $this->mangxmanier() .'e'.
            "\nTEJO-membro por " . TEJO_MEMBRO_JARO . ":   " .
            "\naperos en interreta listo:  " . jes_ne($this->datoj['listo']) .
            "\naperos en adresaro:         " . jes_ne($this->datoj['intolisto']) .
            "\ndomotipo:                  " . $this->domotipo() .
            ($this->datoj['domotipo'] != 'M' ?
             "\nc^ambrotipo:                " . $this->cxambrotipo() .
             // TODO: unulita cxambro
             "\ndulita:                    " . jes_ne($this->datoj['dulita']) .
             "\nkun kiu                    " . $this->datoj['kunkiu'] 
             : ""
             ) .
            "\nalig^dato:                  " . $this->datoj['aligxdato'] .
            "\nhavas asekuron (malsano):  " .
            jes_ne($this->datoj['havas_asekuron']) .
            "\n";
            
        return $teksto;
            // rimarkoj:
            // kontribuoj: distra/tema/vespera/muzika/nokta
    }

    /**
     * kalkulas, kiom da noktoj tiu partoprenanto partoprenas.
     *
     */
    function partoprennoktoj() {
        return kalkulu_tagojn($this->datoj['de'], $this->datoj['gxis']);
    }


    function simpla_kampo($kamponomo, $eblecoj, $else=null) {
        $valoro = $this->datoj[$kamponomo];
        $trovita = false;
        foreach($eblecoj AS $tekstoj) {
            if ($valoro == $tekstoj[0]) {
                kampo($tekstoj[1], $tekstoj[2]);
                $trovita = true;
            }
        }
        if (!$trovita and $else) {
            kampo($else[1], $else[2]);
        }
    }
    function simpla_kampo1($kamponomo, $kondicxo, $kampo1, $kampo2) {
        if ($this->datoj['kamponomo'] == $kondicxo) {
            kampo($kampo1, $kampo2);
        }
    }


    /**
     * Montras la aligxdatojn en HTML-tabelo
     */
    function montru_aligxo($sen_bla = false)
    {

        // TODO: tiu funkcio ankaux sxajnas multe tro longa kaj
        // nesuperrigardebla por mi ...

        $renkontigxo = new renkontigxo($this->datoj[renkontigxoID]);
        $partoprenanto = new partoprenanto($this->datoj['partoprenantoID']);
        if(! $sen_bla)
            {
                eoecho( "partoprendatumoj por la <strong>".$renkontigxo->datoj[nomo]."</strong> en ".$renkontigxo->datoj[loko]);
            }
        echo ("<table  valign=top>\n");
        kampo("ID:",$this->datoj[ID]);
        kampo("Lingva nivelo:", $this->nivelo());
        $this->simpla_kampo1('havas_asekuron',"N",
                             "[X]","bezonas asekuron pri malsano");

        /*
         TODO: indiko pri invitpeto-datoj.
        if ($this->datoj[invitletero][0]=="J")
            {
                kampo("[X]","bezonas invitlereron por pasportnumero: ".$this->datoj['pasportnumero']);
                if ($this->datoj[invitilosendata]!="0000-00-00")
                    kampo("","sendata je la: ".$this->datoj[invitilosendata]);
  
            }
        */
  
        $this->simpla_kampo1('retakonfirmilo', 'J',
                             "[X]","deziras retan konfirmilon");
        $this->simpla_kampo1("germanakonfirmilo", "J",
                             "[X]","deziras germanlingvan konfirmilon");
        $this->simpla_kampo1("litolajxo", "J",
                             "[X]","mendas litolajxon");
        $this->simpla_kampo("partoprentipo",
                            array(array("t", "t","partoprenos tuttempe (de: ".$this->datoj[de]." g^is: ".$this->datoj[gxis].")"),
                                  array("p", "","partoprenos partatempe (de: ".$this->datoj[de]." g^is: ".$this->datoj[gxis].")")),
                            array('?', "","partoprenos tute ne?? io eraro okazis - bonvolu kontaktu nin"));
        $this->simpla_kampo("listo",
                            array(array('J',"[X]",
                                        "volas aperi en la interreta listo."),
                                  array('N',"[_]",
                                        "ne volas aperi en la interreta listo.")),
                            array('?', "?",
                                  'interreta listo: "' . $this->datoj['listo'] . '"'));
        $this->simpla_kampo("intolisto",
                            array(array('J', "[X]",
                                        "volas aperi en la adresaro."),
                                  array('N', "[_]", "ne volas aperi en la adresaro.")),
                            array("?", "?", 'adresaro: "' . $this->datoj['listo'] . '"'));
        $this->simpla_kampo("vegetare",
                            array(array("J", "[X]",
                                        "estas <em>vegetarano</em>"),
                                  array("A", "[X]", "estas <em>vegano</em>"),
                                  array("N", "[X]",
                                        "estas <em>viandmang^anto</em>")),
                            array("", "?", "<em>nekonata mang^otipo</em>!"));
        $this->simpla_kampo("GEJmembro",
                            array(array('J', "[X]","estas membro de GEJ")),
                            array('N', "[_]", "ne estas membro de GEJ"));
        
        kampo($this->datoj['surloka_membrokotizo'],
              $this->membrokotizo());

        // TODO: pripensi, cxu ankaux eblas fari simile kiel la antauxaj.
        switch(($this->datoj['tejo_membro_laudire']) . ($this -> datoj['tejo_membro_kontrolita']))
            {
            case 'jj':
            case 'nj':
                kampo("[X]", "estas membro de TEJO (kontrolita)");
                break;
            case 'jn':
                kampo("-", "ne estas membro de TEJO (kvankam " .$partoprenanto->personapronomo. " asertis, ke jes)");
                break;
            case 'j?':
                kampo("[?]", "asertis esti membro de TEJO (ankorau^ ne kontrolita)");
                break;
            case 'nn':
            case 'n?':
                kampo("-", "ne estas membro de TEJO");
                break;
            case 'np':
            case 'jp':
                kampo("-", "ne estas membro de TEJO, sed tamen pagas ioman monon al TEJO/UEA");
                break;
            case 'ni':
            case 'ji':
                kampo("[I]", "ig^as nova membro de TEJO surloke");
                break;
            default:
                kampo("?", "eraro okazis pri la TEJO-membreco: ".
                      "laudire=" .$this->datoj['tejo_membro_laudire'] .
                      ", kontrolita=" . $this -> datoj['tejo_membro_kontrolita']);
            }

        $this->simpla_kampo1("KKRen", "J", "[X]","estas KKRenano");
        
        $vosto .= "kaj ";
        $komenco = "";
        if ($this->datoj[domotipo][0]=="M")
            {
                $komenco .= "M";
                $vosto .= "memzorgas";
                if ($this->datoj[kunmangxas][0]=="J")
                    {
                        $vosto .= ", sed kunmang^as (senpage)";
                        $komenco .= "J";
                    }
                else if ($this->datoj['kunmangxas'] == 'K')
                    {
                        $vosto .= ", sed krompagas por kunmang^i";
                        $komenco .= "K";
                    }
            }
        else if ($this->datoj[domotipo][0]=="J")
            {
                $vosto .= "junulargastejumas en ";
                $komenco .= "J";
                if ($this->datoj[dulita][0]=="J")
                    {
                        $komenco .= "2";
                        $vosto .= "(eble) dulita ";
                    }
                else if ($this->datoj['dulita'] == 'U') {
                    $vosto .= "(eble) unulita ";
                    $komenco .= "1";
                }
                if ($this->datoj[cxambrotipo][0]=="u")
                    {
                        $vosto .= "unuseksa ";
                        $komenco .= "u";
                    }
                if ($this->datoj[cxambrotipo][0]=="g")
                    {
                        $vosto .= "gea ";
                        $komenco .= "g";
                    }
                if ($this->datoj[cxambrotipo][0]=="n")
                    {
                        $vosto .= "negrava ";
                    }
                $vosto .= "c^ambro ";

                if ($this->datoj[kunkiuID])
                    {
                        //$vosto .= "(".$this->datoj[kunkiuID].")";// Verlinken mit anderem Teilnehmer
                        $kunlogxanto=new Partoprenanto($this->datoj[kunkiuID]);
                        $komenco .=  "+";
                        $vosto .= " (eble) kun <A href=partrezultoj.php?partoprenantoidento=".$this->datoj[kunkiuID].
                            " onClick=\"doSelect(".$kunlogxanto->datoj[ID].");\">".$kunlogxanto->datoj[personanomo]." ".$kunlogxanto->datoj[nomo]."</A>";
                    }
                if ($this->datoj[kunkiu]!="")
                    {
                        $vosto .= " (".$this->datoj[kunkiu].")";
                    }
                if ($this->datoj[kunmangxas][0]=="N")
                    {
                        $vosto .= ", sed ne kunmang^as";
                        $komenco .= "N";
                    }
                else if ($this->datoj['kunmangxas'] == 'K')
                    {
                        $vosto .= ", sed ial tamen krompagas por kunmang^i";
                        $komenco .= "K";
                    }
            }
        kampo($komenco, $vosto);
    
        $this->simpla_kampo1("ekskursbileto", "J", "[X]","mendis bileton por la tutaga ekskurso");
            
        foreach(array('tema', 'distra', 'vespera', 'muzika', 'nokta')
                AS $tipo) {
            if($this->datoj[$tipo]) {
                kampo("[X]","kontribuos al la " . $tipo . " programo per: " .
                      $this->datoj[$tipo]);
            }
        }
  
        if ($this->datoj['rimarkoj'])
            {
                kampo("rimarkoj:", $this->datoj['rimarkoj']);    
            }
        if ($this->datoj['aligxdato'])
            {
                kampo("alveno de la alig^o:", $this->datoj['aligxdato']);
            }

        if ($this->datoj['malaligxdato'] != "0000-00-00")
            {
                kampo("alveno de la malalig^o:", $this->datoj['malaligxdato']);
            }
	
        kampo("1a konf.:", $this->datoj['1akonfirmilosendata']);
        kampo("2a konf.:", $this->datoj['2akonfirmilosendata']);
        echo "</table>\n";
    }



    /*
     *
     *  la sekvaj funkcioj po donas tutan vorton pri tiu eco anstataux
     *  la unulitera mallongigo.
     *
     *  TODO: ebligu pliajn tipojn (kie sencas), kaj tradukojn.
     *
     ********************************************************************
     */



    /**
     * la domotipo en teksta formo.
     */
    function domotipo()
    {
        // TODO: konfigureblaj domo-elekto
        switch($this->datoj['domotipo']{0})
            {
            case 'J':
                return "junulargastejo";
            case 'M':
                return "memzorgantejo";
            default:
                return "(Nevalida domotipo)";
            }
    }

    /**
     * uzebla kun aldona -a aux -e.
     */
    function mangxmanier()
    {
        switch($this->datoj['vegetare'])
            {
            case 'J':
                return "vegetar";
            case 'A':
                return "vegan";
            case 'N':
                return "viand";
            }
    }

    function nivelo()
    {
        switch($this->datoj['nivelo'])
            {
            case 'f':
                return "flua parolanto";
            case 'p':
                return "parolanto";
            case 'k':
                return "komencanto";
            case '?':
                return "ne elektis";
            default:
                return "(erara nivelo: '" . $this->datoj['nivelo'] . "')";
            }
    }


    /*
     * surloka_membrokotizo - j/i/h/n/a/k/? (ekde 2007)
     *              j = jam estis membro, kaj nun rekotizas
     *              i = igxis nova membro kaj nun kotizas
     *              h = igxis nova membro, sed ne devas kotizi
     *              n = ne devas membri (ekzemple eksterlandano)
     *              a = membro, jam antauxe pagis aux ne devas pagi
     *              k = devus membrigxi, sed preferas krompagi
     *              ? = ne jam traktita (tio ne okazu post la akceptado)
     */
    function membrokotizo()
    {
        switch($this->datoj['surloka_membrokotizo'])
            {
            case '?':
                return "estas ne jam klasifikita lau^ membro-kotizo-devo";
            case 'j':
                return "jam estis membro, kaj surloke rekotizas " .
                    $this->datoj['membrokotizo'] . " &euro;";
            case 'a':
                return "jam estis membro, kaj ne surloke rekotizas";
            case 'i':
                return "nun ig^as nova membro, kaj surloke kotizas " .
                    $this->datoj['membrokotizo'] . " &euro;";
            case 'h':
                return "nun ig^as nova membro sen kotizi surloke";
            case 'n':
                return "ne devas kotizi (c^ar eble eksterlandano)";
            case 'k':
                return "devus ig^i membro, sed preferas krompagi " .
                    $this->datoj['membrokotizo'] . " &euro;";
            default:
                return "(nekonata stato '" .
                    $this->datoj['surloka_membrokotizo'] .
                    "', kotizo = '". $this->datoj['membrokotizo'] . "'.)";
            }
    }


    function alvenstato()
    {
        $nomo = $GLOBALS['alvenstatonomoj'][$this->datoj['alvenstato']];
        if ($nomo)
            return $nomo;
        else
            return "(nevalida alvenstato: '" .
                $this->datoj['alvenstato'] . "')";
    }


    function cxambrotipo()
    {
        switch($this->datoj['cxambrotipo'])
            {
            case 'u':
                return 'unuseksa';
            case 'g':
                return 'gea';
            default:
                return "(nevalida cxambrotipo)";
            }
    }

    function partoprentipo()
    {
        switch($this->datoj['partoprentipo'])
            {
            case 'p':
                return 'parttempa';
            case 't':
                return 'tuttempa';
            default:
                return "(nevalida partoprentipo)";
            }
    }


    /**
     * stokita invitpeto-objekto por reuzo.
     */
    var $mia_invitpeto;

    /**
     * esploras, cxu ekzistas invitpeto por tiu partopreno.
     * Se jes, kreas invitpeto-objekto kaj redonas gxin,
     * alikaze redonas false.
     */
    function sercxu_invitpeton()
    {
        if (is_object($this->mia_invitpeto))
            {
                return $this->mia_invitpeto;
            }
        if($this->mia_invitpeto == "-")
            {
                return false;
            }

        $peto = new Invitpeto($this->datoj['ID']);
        if ($peto->datoj)
            {
                // ekzistas datumbazero
                $this->mia_invitpeto = $peto;
                return $peto;
            }
        else
            {
                // ne ekzistas datumbazero
                $this->mia_invitpeto = '-';
                return false;
            }
    }



}



$GLOBALS['alvenstatonomoj'] = array('a' => 'akceptita',
                                    'i' => 'vidita',
                                    'm' => 'malalig^is',
                                    'n' => 'ne venis',
                                    'v' =>'venos',);




?>
