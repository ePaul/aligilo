<?php


  /**
   * La Partopreno-klaso.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /*
   * La tabelnomoj ĉi tie ĉiam estas
   * la abstraktaj tabelnomoj. La traduko
   * al la konkretaj nomoj okazas en
   * iloj_sql.
   */


/**
 * Partopren-datumoj de iu partoprenanto
 * ĉe iu renkontiĝo. Tabelo "partoprenoj".
 *
 * <pre>
 * ID
 * renkontigxoID
 * partoprenantoID
 * ordigoID          - uzata por ordigo anstataŭ la ID, se ordigoID > 0.0.
 * agxo             - aĝo je la komenco de la renkontiĝo (en jaroj).
 *                    Estas kalkulita el renkontigxo.de kaj
 *                     partoprenanto.naskigxdato.
 * komencanto        - ne plu uzata en 2007 - anstataŭe 'nivelo'.
 * nivelo            - Lingva nivelo:
 *                     ? - ne elektis
 *                     f - flua parolanto
 *                     p - parolanto
 *                     k - komencanto
 * rimarkoj           kion la ulo menciis en la rimarko-kampo dum la aliĝo.
 * retakonfirmilo     - J/N (volas retpoŝtan konfirmilon)
 * germanakonfirmilo  - J/N (volas ankaŭ germanlingvan konfirmilon)
 * 1akonfirmilosendata  - dato (estu ...ita)
 * 2akonfirmilosendata  - dato (estu ...ita)
 * partoprentipo        - p/t - parttempa/tuttempa
 * de                   - dato
 * gxis                 - dato
 * vegetare   - J/N/A - Vegetarano,Viandmanĝanto,Vegano.
 * GEJmembro  - (laŭ la aliĝilo)
 * tejo_membro_laudire    j/n    - kion la homo asertis pri
 *                                  TEJO-membreco en la formularo.
 * tejo_membro_kontrolita j/n/?/i/p  - kion ni kontrolis per TEJO-funkciulo/per
 *                                   membrokarto/...
 *                                   j = estas membro
 *                                   n = ne estas membro (kaj ne iĝas)
 *                                   ? = ni ankoraŭ ne kontrolis (defaŭlto,
 *                                       ne plu aperu post la akceptado)
 *                                   i = iĝas nova TEJO-membro dum tiu
 *                                       ĉi renkontiĝo
 *                                   p = pagis al TEJO/UEA ion, sed ne ricevas
 *                                       rabaton (ekzemple tro aĝa UEA-membro,
 *                                       kategorio MG, aŭ pago por alia
 *                                       membro.)
 * tejo_membro_kotizo          - alteco de la TEJO-kotizo aŭ aliaj
 *                               pagoj al TEJO tra la IS-kaso (nur uzata,
 *                               se *_kontrolita = i aŭ = p. - alikaze
 *                               estu 0.).
 * surloka_membrokotizo - j/n/k  (ĝis 2006)
 *                               -j = pagas kotizon surloke
 *                                n = ne necesas(jam pagis/enkasigrajto/
 *                                               eksterlandano)
 *                                k = elektis punan krompagon anstataŭ membriĝi
 * surloka_membrokotizo - j/i/h/n/a/k/? (ekde 2007)
 *              j = jam estis membro, kaj nun rekotizas
 *              i = iĝis nova membro kaj nun kotizas
 *              h = iĝis nova membro, sed ne devas kotizi
 *              n = ne devas membri (ekzemple eksterlandano)
 *              a = membro, jam antaŭe pagis aŭ ne devas pagi
 *              k = devus membriĝi, sed preferas krompagi
 *              ? = ne jam traktita (tio ne okazu post la akceptado)
 *
 * membrokotizo         - alteco de (GEJ/GEA-) membrokotizo aŭ krompago
 * KKRen                - J/N - ĉu membro de la teamo 
 * domotipo    - J/M - Junulargastejo / Memzorgantejo
 * litolajxo
 * kunmangxas  - J/N
 *               J - sen aldona pago kunmanĝas
 *               N - ne kunmanĝas
 *               K - krompagas por kunmanĝi
 * listo - J/N  (volas aperi en interreta listo,
 *               ne volas aperi en interreta listo)
 * intolisto - J/N  (volas aperi en la post-renkontiĝa partoprenintolisto,
 *                   ne volas aperi tie.)
 * pagmaniero   - Pagmaniero laŭ aliĝilo
 *                 - uea  (UEA-konto de GEJ)
 *                 - gej  (GEJ-bsnkkonto)
 *                 - paypal
 *                 - persone (al KKRen-membro)
 *                Aliaj asocioj:
 *                 - hej
 *                 - jeb
 *                 - jefo
 *                 - iej
 * kunkiu         teksto (el aliaĝilo: kun kiu vi volas loĝi)
 * kunkiuID       partoprenanto-ID de dezirata kunloĝanto
 * cxambrotipo     - g = gea, u = unuseksa, (n = negravas),
 *                 '' (kelkaj malnovaj)
 * dulita
 * ekskursbileto
 * tema     -.
 * distra    |
 * vespera   |-- propono kiel programkontribuo
 * muzika    | 
 * nokta    -'
 * donaco    -- TODO: ĉu ankoraŭ uzata?
 * aligxdato     - alvenodato de la aliĝo.
 * malaligxdato  - alvenodato de la malaliĝo, se entute
 * alvenstato - tri eblecoj: 'a', 'v', 'm'.  
 *   [respondo de Martin:] alvenis / venos / malaliĝis.
 *              ekde 2008:
 *               a = akceptita
 *               v = venos
 *               m = malaliĝis
 *               i = vidita, sed ne akceptiĝis
 *               n = ne venis/venos, sen malaliĝi
 * traktstato
 * asekuri
 *    - Por kio necesas "asekuri"?
 *   [respondo de Martin:] Muß versichert werden / muß nicht versichert werden.
 *     
 * havas_asekuron  - (en la aliĝformularo eblas diri "mi havas asekuron pri malsano" (--> J)
 *                    aŭ "mi ne havas taŭgan asekuron" (--> N)).
 * rabato        |
 * kialo         |-  (ne plu estas uzataj) 
 * surlokpago    |
 * aligxkategoridato   - uzu por doni alian daton ol la antaŭpagdaton
 *                         por kalkuli la aliĝkategorion
 * forgesu        - ?
 * kontrolata      - J/N
 * havasMangxkuponon - N/P/J  (Ne printita/printita/ricevis)
 * havasNomsxildon   - N/P/J  (Ne printita/printita/ricevis)
 *
 *Ne plu:
 * invitletero        - J/N   (ne plu uzata)
 * invitilosendata    - J/N   (ne plu uzata)
 * pasportnumero              (ne plu uzata)
 *
 * </pre>
 * @todo trarigardu la liston de kampoj!
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
            jes_ne($this->datoj['retakonfirmilo']);
        if(KAMPOELEKTO_IJK) {
            $teksto .=
                "\nkonfirmilo-lingvo:          " . $this->datoj['konfirmilolingvo'];
        }
        else {
            $teksto .=
                "\ngermana konfirmilo:        " .
                jes_ne($this->datoj['germanakonfirmilo']);
        }
        $teksto .=
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
             // TODO: unulita ĉambro
             "\ndulita:                    " . jes_ne($this->datoj['dulita']) .
             "\nkun kiu                    " . $this->datoj['kunkiu'] 
             : ""
             ) .
            "\nalig^dato:                  " . $this->datoj['aligxdato'];
        if(!KAMPOELEKTO_IJK) {
            $teksto .=
                "\nhavas asekuron (malsano):  " .
                jes_ne($this->datoj['havas_asekuron']);
        }
        $teksto .= "\n";
            
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

    /**
     * helpa funkcio por la tabelo
     * @todo dokumentado
     */
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
    /**
     * helpa funkcio por la tabelo
     * @todo dokumentado
     */
    function simpla_kampo1($kamponomo, $kondicxo, $kampo1, $kampo2) {
        if ($this->datoj[$kamponomo] == $kondicxo) {
            kampo($kampo1, $kampo2);
        }
    }


    /**
     * Montras la aliĝdatojn en HTML-tabelo
     */
    function montru_aligxo($sen_bla = false)
    {

        // TODO: tiu funkcio ankaŭ ŝajnas multe tro longa kaj
        // nesuperrigardebla por mi ...

        $renkontigxo = new renkontigxo($this->datoj['renkontigxoID']);
        $partoprenanto = new partoprenanto($this->datoj['partoprenantoID']);
        if(! $sen_bla)
            {
                eoecho( "partoprendatumoj por la <strong>".$renkontigxo->datoj['nomo']."</strong> en ".$renkontigxo->datoj['loko']. ":");
            }
        echo ("<table  valign='top'>\n");
        kampo("ID:",$this->datoj['ID']);
        if ($this->datoj['ordigoID'] != '0.000') {
            kampo("ordigo-ID:", $this->datoj['ordigoID']);
        }
        kampo("Lingva nivelo:", $this->nivelo());
        $this->simpla_kampo1('havas_asekuron',"N",
                             "[X]","bezonas asekuron pri malsano");

        /*
         TODO: indiko pri invitpeto-datoj
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
        if (deviga_membreco_tipo != 'nenia') {
            $this->simpla_kampo("GEJmembro",
                                array(array('J', "[X]","estas membro de " . deviga_membreco_nomo)),
                                array('N', "[_]", "ne estas membro de " . deviga_membreco_nomo));
        
            kampo($this->datoj['surloka_membrokotizo'],
                  $this->membrokotizo());
		}
        // TODO: pripensi, ĉu ankaŭ eblas fari simile kiel la antaŭaj.
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

        $this->simpla_kampo1("KKRen", "J", "[X]",
                             "estas " .organizantoj_nomo . "-ano");
        if (mangxotraktado == 'ligita') {
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
        }
        else if (mangxotraktado == 'libera') {
        	$this->simpla_kampo("domotipo",
                                array(array('J', "J", "log^as en junulargastejo"),
                                       array('M', 'M', "log^as memzorge (ekster niaj ejoj)"),
                                       array('A', "A", "log^as en amaslog^ejo"),
                                       array('T', "T", "log^as tendo")
                                       ),
                                array($this->datoj['domotipo'], "<em>nekonata domotipo</em>"));
        	if ($this->datoj['kunKiuID'] != 0) {
                $kunlogxanto=new Partoprenanto($this->datoj['kunkiuID']);
        		kampo("+", "volas log^i kun " .
        		      donu_ligon("partrezultoj.php?partoprenantoidento=" . $this->datoj['kunKiuID'],
        		                 $kunlogxanto->tuta_nomo()) . " (" . $this->datoj['kunKiu'] . ')'); 
        	} else if ($this->datoj['kunKiu']) {
        		kampo("+", "volas log^i kun " .
        		       " (" . $this->datoj['kunKiu'] . ')');
        	}
        	$this->simpla_kampo("cxambrotipo",
        	                    array(array('g', "g", "en ajna c^ambro"),
        	                           array('u', "u", "en unuseksa c^ambro")),
        	                    array($this->datoj['domotipo'], "<em>nekonata domotipo</em>"));
        	// TODO: manĝo-mendo-listo
        	// TODO: dulita
        }
        else {
        	kampo("????", "nekonata mangxotraktado-konfiguro: " . mangxotraktado);
        }
    
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
     *  la sekvaj funkcioj po donas tutan vorton pri tiu eco anstataŭ
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
     * uzebla kun aldona -a aŭ -e.
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


    /**
     * surloka_membrokotizo - j/i/h/n/a/k/? (ekde 2007)
     *              j = jam estis membro, kaj nun rekotizas
     *              i = iĝis nova membro kaj nun kotizas
     *              h = iĝis nova membro, sed ne devas kotizi
     *              n = ne devas membri (ekzemple eksterlandano)
     *              a = membro, jam antaŭe pagis aŭ ne devas pagi
     *              k = devus membriĝi, sed preferas krompagi
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
     * memorita invitpeto-objekto por reuzo.
     */
    var $mia_invitpeto;

    /**
     * esploras, ĉu ekzistas invitpeto por tiu partopreno.
     * Se jes, kreas invitpeto-objekto kaj redonas ĝin,
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

        if (!$this->datoj['ID']) {
            $this->mia_invitpeto="-";
            return false;
        }
        $peto = new Invitpeto($this->datoj['ID']);
        if ($peto->datoj['ID'] == $this->datoj['ID'])
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



} // class Partopreno


/**
 * @global array $GLOBALS['alvenstatonomoj']
 * @name $alvenstatonomoj
 */
$GLOBALS['alvenstatonomoj'] = array('a' => 'akceptita',
                                    'i' => 'vidita',
                                    'm' => 'malalig^is',
                                    'n' => 'ne venis/-os',
                                    'v' => 'venos',);



