<?php

  /*
   * La tabelnomoj cxi tie cxiam estas
   * la abstraktaj tabelnomoj. La traduko
   * al la konkretaj nomoj okazas en
   * iloj_sql.
   */

  /**
   * La superklaso de cxiuj niaj klasoj
   * por objektoj en/el la datumbazo.
   */
class Objekto
{

    /**
     * La atributoj de la objekto, por enmeti en
     * aux elmeti el la datumbazo(n)
     */
    var $datoj = array();

    /* La nomo de la tabelo, en kiu povus trovigxi la objekto */
    var $tabelnomo;

    /**
     * prenas la enhavon de la objekto el la datumbazo.
     */
    function prenu_el_datumbazo($id="")
    {
        if ($id == "")
            $id = $this->datoj["ID"];
     
        $sql = datumbazdemando("*", $this->tabelnomo, "ID = '$id'");
        $this->datoj = mysql_fetch_assoc( sql_faru($sql) );  
    }


    /**
     * Konstruilo.
     *
     * Se $id == 0, kreas novan (malplenan) objekton
     * (la strukturon gxi prenas el la datumbazo),
     * alikaze prenas la jam ekzistan objekton (kun
     * tiu identifikilo) el la datumbazo.
     *
     *  $id - la identifikilo (aux 0).
     *  $tn - la (abstrakta) nomo de la tabelo.
     */
    function Objekto($id, $tn)
    {

        $this->tabelnomo = $tn;
        if ($id == 0)
            {
                /* prenu nur la strukturon el la datumbazo */
                $sql = datumbazdemando("*", $tn, "", "",
                                       array("limit" => "1,1"));
                $rezulto = sql_faru($sql);
                for ($i = 0; $i < mysql_num_fields($rezulto); $i++)
                    {
                        $this->datoj[mysql_field_name($rezulto, $i)] = "";
                    }
            }
        else
            {
                /* prenu tutan tabel-linion el la datumbazo */
                $this->prenu_el_datumbazo($id);
            }
    }

    /**
     * montras la objekton kiel
     * HTML-tabelo.
     * uzata por debugado
     */
    function montru()
    {
        echo "<table>";
        foreach($this->datoj AS $nomo => $valoro)
            {
                echo "<tr><td>$nomo</td><td>$valoro</td></tr>";
            }
        echo "</table>";
    }

    /**
     * Kopias el $_POST al la datoj
     * de tiu cxi objekto (nur tiuj eroj,
     * kiuj jam ekzistas en la datoj, ricevas
     * novan valoron).
     *
     * TODO: Por kio oni bezonas la funkcion?
     *  -> ekzemple por la aligxatkontrolo.
     */
    function kopiu()
    {

        //TODO: Cxi tie estas iomete  malsekura punkte, sed
        // mi gxis nun ne trovis pli bonan solvon.
        foreach($_POST AS $nomo => $valoro)
            {
                if ( isset($this->datoj[$nomo]) )
                    {
      
                        $this->datoj[$nomo] = /*stripslashes*/(str_replace("'","`",$valoro));
                    }
            }
    }

    /**
     * Aldonas objekton al la gxusta tabelo
     * kaj prenas la ID de tie.
     */
    function kreu()
    {
        //  sql_faru("insert into {$this->tabelnomo} set id='0'");
        aldonu_al_datumbazo($this->tabelnomo, array("id"=>"0"));
        $this->datoj[ID] = mysql_insert_id();
    }

    /**
     * aldonas la tutan objekton al la datumbazo,
     * inkluzive de identifikilo kaj cxiuj datoj.
     *
     * Tiu funkciu estu uzata por cxiu objekto po maksimume unufoje,
     * kaj nur, kiam oni ne antauxe uzis kreu() aux la konstruilon kun ID.
     */
    function skribu_kreante()
    {
        aldonu_al_datumbazo($this->tabelnomo, $this->datoj);
        $this->prenu_el_datumbazo();
    }


    /**
     * Skribas la objekton al la tabelo,
     * anstatauxante la antauxan valoron
     * de la atributoj tie.
     */
    function skribu()
    {
        if (! EBLAS_SKRIBI)
            return "Datenbank darf nicht ge&auml;ndert werden";
  
        sql_faru($this->sql_eksport());

        // poste ni re-prenos la datojn el la datumbazo, por vidi, kio alvenis.
        $this->prenu_el_datumbazo();
    }



    function sql_eksport()
    {
        return datumbazsxangxo($this->tabelnomo,
                               $this->datoj,
                               array("ID" => $this->datoj["ID"]));
    }

} // objekto


/**********************************
 * la datumoj de iu partoprenanto. Tabelo "partoprenanto".
 *
 * ID
 * nomo
 * personanomo
 * sxildnomo
 * sekso
 * naskigxdato
 * // TODO:agxo
 * agxo        ----  agxo je la komenco de renkontigxo.
 *                - tio estas sxovita nun al partopreno, do ne plu uzigxas
 *                  (kaj estos forigota baldaux)
 * adresaldonajxo
 * strato
 * posxtkodo
 * urbo
 * provinco
 * lando
 * sxildlando
 * okupigxo
 * okupigxteksto
 * telefono
 * telefakso
 * retposxto
 * retposxta_varbado - j (sendu ikse), n (ne sendu), u (sendu unikode)
 * ueakodo
 * rimarkoj   - ne plu uzata TODO: forigu
 * kodvorto   ???
 * malnova   (ankoraux el 2001, kiam ne ekzistis "partopreno" - ne plu uzata).
 */

class Partoprenanto extends Objekto
{

    /** persona pronomo: "s^i"/"li"/"ri" */
    var $personapronomo;
    /* sekso: "ina"/"vira"/"ielsekse" */
    var $sekso;

    /**
     * Konstruilo.
     *
     * Kreas/elprenas partoprenanton kaj
     * eltrovas sekson/personan pronomon.
     */
    function Partoprenanto($id=0)
    {
        // super-konstruilo
        $this->Objekto($id,"partoprenantoj");

        switch ($this->datoj[sekso])
            {
            case "i": $this->personapronomo = "s^i";$this->sekso = "ina";break;
            case "v": $this->personapronomo = "li";$this->sekso = "vira";break;
            default:  $this->personapronomo = "ri";$this->sekso = "ielsekse";
            }
    }

    /**
     * Montras la partoprenanton kiel HTML-tabelo.
     */
    function montru_aligxinto($sen_bla = FALSE)
    {
        if(! $sen_bla)
            {
                eoecho ("Informado pri partoprenantoj....");
	  
                // TODO: senhxaosigi ...
	  	  
                rajtligu("partrezultoj.php?dis_ago=estingi","estingi","anzeige","estingi",'n');
            }
        echo  "<table>\n";
        kampo("ID:",$this->datoj[ID]);
        kampo("nomo:", $this->tuta_nomo() . " (".$this->datoj[sekso].")");
        // 	if ($this->datoj[sxildnomo]!='')
        // 	  {
        // 		kampo("nomo:",$this->datoj[personanomo]." (".$this->datoj[sxildnomo].") ".$this->datoj[nomo]." (".$this->datoj[sekso].")");
        // 	  }
        // 	else
        // 	  kampo("nomo:",$this->datoj[personanomo]." ".$this->datoj[nomo]." (".$this->datoj[sekso].")");
        if ($this->datoj[adresaldonajxo])
            {
                kampo("",$this->datoj[adresaldonajxo]);
            }
        kampo("strato:",$this->datoj[strato]);
        kampo("loko:",$this->datoj[posxtkodo].", ".$this->datoj[urbo]);
        if ($this->datoj[provinco]) {kampo("provinco:", $this->datoj[provinco]);}
        kampo("lando:",
              eltrovu_landon($this->datoj[lando]).
              ' ('.eltrovu_landokategorion($this->datoj[lando]).')');
        if ($this->datoj[sxildlando]!='') {kampo("s^ildlando:", $this->datoj[sxildlando]);}
  
        if (okupigxo_eblas == 'jes')
            {
                kampo($this->personapronomo." ".okupigxtipo($this->datoj[okupigxo]),
                      $this->datoj[okupigxteksto]);
            }
        kampo("naskita:",$this->datoj[naskigxdato]);
        if ($this->datoj[telefono])
            {
                kampo("telefono:",$this->datoj[telefono]);
            }
        if ($this->datoj[telefakso])
            {
                kampo("telefakso:",$this->datoj[telefakso]);
            }
        if ($this->datoj['retposxto'])
            {
                kampo("retpos^to:",$this->datoj['retposxto']);

                switch($this->datoj['retposxta_varbado'])
                    {
                    case 'n':
                        kampo("-", "ne volas retpos^tajn informojn");
                        break;
                    case 'j':
                        kampo('x', "volas retpos^tajn informojn x-kode");
                        break;
                    case 'u':
                        kampo ('u', "volas retpos^tajn informojn unikode");
                        break;
                    }
            }
        if ($this->datoj['ueakodo'])
            {
                kampo("UEA-kodo:", $this->datoj['ueakodo']);
            }
        echo "</table>\n";
    }

    /**
     * redonas la tutan nomon de la partoprenanto ("personanomo nomo").
     */
    function tuta_nomo()
    {
        if ($this->datoj['sxildnomo'])
            {
                return $this->datoj['personanomo'] . " (" .$this->datoj['sxildnomo']. ") "
                    . $this->datoj['nomo'];
            }
        return $this->datoj['personanomo'] . " " . $this->datoj['nomo'];
    }

} // partoprenanto



/**
 * Datumoj rilataj al petado de invitletero/vizo
 * (en aparta tabelo, cxar ne cxiu bezonas gxin)
 *
 *  ID            (= partoprenoID)
 *  pasportnumero
 *  pasporta_familia_nomo
 *  pasporta_persona_nomo
 *  pasporta_adreso
 *  senda_adreso
 *  senda_faksnumero
 *
 *  invitletero_sendenda    ?/j/n
 *  invitletero_sendodato
 */
class Invitpeto extends Objekto
{
    
    /* Konstruilo */
    function Invitpeto($id=0)
    {
        $this->Objekto($id,"invitpetoj");
    }
    
    
    function montru_detalojn()
    {
        echo "<table>\n";
        kampo("ID:", $this->datoj['ID']);
        kampo("PP-numero:", $this->datoj['pasportnumero']);
        kampo("PPa familia nomo:", $this->datoj['pasporta_familia_nomo']);
        kampo("PPa persona nomo:", $this->datoj['pasporta_persona_nomo']);
        kampo("PPa adreso:", nl2br($this->datoj['pasporta_adreso']));
        kampo("Senda adreso:", nl2br($this->datoj['senda_adreso']));
        kampo("Senda faksnumero:", $this->datoj['senda_faksnumero']);

        switch($this->datoj['invitletero_sendenda'])
            {
            case '?':
                kampo("[?]", "ankorau^ decidenda, c^u sendi invitleteron");
                break;
            case 'j':
                kampo("[X]", "Sendu invitleteron");
                break;
            case 'n':
                kampo("[-]", "Ne sendu invitleteron");
                break;
            default:
                kampo("Invitletero sendenda?",
                      "eraro: '" . $this->datoj['invitletero_sendenda'] . "'");
            }
        kampo("Sendodato:", $this->datoj['invitletero_sendodato']);
        echo "</table>";
    }



} // invitpeto


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
 * tejo_membro_kontrolita j/n/?/i  - kion ni kontrolis per TEJO-funkciulo/per
 *                                   membrokarto/...
 *                                 j = estas membro
 *                                 n = ne estas membro
 *                                 ? = ni ne jam kontrolis (defauxlto).
 *                                 i = igxas nova TEJO-membro dum tiu cxi renkontigxo
 * tejo_membro_kotizo          - alteco de la TEJO-kotizo (nur uzata, se *_kontrolita = i).
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
 * listo - J/N  (volas aperi en interreta listo, ne volas aperi en interreta listo)
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
 * cxambrotipo
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
 * venos          - ?
 * alvenis        - ?
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
     * Montras la aligxdatojn en HTML-tabelo
     */
    function montru_aligxo($sen_bla = false)
    {
        $renkontigxo = new renkontigxo($this->datoj[renkontigxoID]);
        $partoprenanto = new partoprenanto($this->datoj['partoprenantoID']);
        if(! sen_bla)
            {
                eoecho( "partoprendatumoj por la <strong>".$renkontigxo->datoj[nomo]."</strong> en ".$renkontigxo->datoj[loko]);
            }
        echo ("<table  valign=top>\n");
        kampo("ID:",$this->datoj[ID]);
        if ($this->datoj[komencanto][0]=="J")
            {
                kampo("[X]","estas novulo / komencanto");
            }
        if ($this->datoj[havas_asekuron] == "N")
            {
                kampo("[X]", "bezonas asekuron pri malsano");
            }

        if ($this->datoj[invitletero][0]=="J")
            {
                kampo("[X]","bezonas invitlereron por pasportnumero: ".$this->datoj['pasportnumero']);
                if ($this->datoj[invitilosendata]!="0000-00-00")
                    kampo("","sendata je la: ".$this->datoj[invitilosendata]);
  
            }
  
        if ($this->datoj[retakonfirmilo][0]=="J")
            {
                kampo("[X]","deziras retan konfirmilon");
            }
        if ($this->datoj["germanakonfirmilo"]{0}=="J")
            {
                kampo("[X]","deziras germanlingvan konfirmilon");
            }
        if ($this->datoj[litolajxo][0]=="J")
            {
                kampo("[X]","mendas litolajxon");
            }
        if ($this->datoj[partoprentipo][0]=="t")
            {
                kampo("","partoprenos tuttempe (de: ".$this->datoj[de]." g^is: ".$this->datoj[gxis].")");
            }
        elseif ($this->datoj[partoprentipo][0]=="p")
            {
                kampo("","partoprenos partatempe (de: ".$this->datoj[de]." g^is: ".$this->datoj[gxis].")");
            }
        else
            {
                kampo("","partoprenos tute ne?? io eraro okazis - bonvolu kontaktu nin");
                // MAcht das skript dann automatisch :))
            }

        if($this->datoj['listo']{0} == 'J')
            {
                kampo("[X]", "volas aperi en la interreta listo.");
            }
        else if ($this->datoj['listo']{0} == 'N')
            {
                kampo("[_]", "ne volas aperi en la interreta listo.");
            }
        else
            {
                kampo("?", 'interreta listo: "' . $this->datoj['listo'] . '"');
            }


        if ($this->datoj[vegetare][0]=="J")
            {
                kampo("[X]","estas <em>vegetarano</em>");
            }
        else if ($this->datoj[vegetare][0]=="A")
            {
                kampo("[X]", "estas <em>vegano</em>");
            }
        else
            {
                kampo("[X]","estas <em>viandmang^anto</em>");
            }
        if ($this->datoj[GEJmembro][0]!="J")
            {
                kampo("","ne estas membro de GEJ");
            }
        else
            {
                kampo("[X]","estas membro de GEJ");
            }
        kampo($this->datoj['surloka_membrokotizo'],
              $this->membrokotizo());

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
                kampo("[?]", "asertis esti membro de TEJO (ne jam kontrolita)");
                break;
            case 'nn':
            case 'n?':
                kampo("-", "ne estas membro de TEJO");
                break;
            case 'ni':
            case 'ji':
                kampo("[I]", "ig^as nova membro de TEJO surloke");
            default:
                kampo("?", "eraro okazis pri la TEJO-membreco: ".
                      "laudire=" .$this->datoj['tejo_membro_laudire'] .
                      ", kontrolita=" . $this -> datoj['tejo_membro_kontrolita']);
            }
        if ($this->datoj[KKRen][0]=="J")
            {
                kampo("[X]","estas KKRenano");
            }
        $vosto .= "kaj ";
        if ($this->datoj[domotipo][0]=="M")
            {
                $vosto .= "memzorgas ";
                if ($this->datoj[kunmangxas][0]=="J")
                    {
                        $vosto .= "sed kunmang^as ";
                    }
            }
        else if ($this->datoj[domotipo][0]=="J")
            {
                $vosto .= "junulargastejumas en ";
                if ($this->datoj[dulita][0]=="J")
                    {
                        $vosto .= "(eble) dulita ";
                    }
                if ($this->datoj[cxambrotipo][0]=="u")
                    {
                        $vosto .= "unuseksa ";
                    }
                if ($this->datoj[cxambrotipo][0]=="g")
                    {
                        $vosto .= "gea ";
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
                        $vosto .= " (eble) kun <A href=partrezultoj.php?partoprenantoidento=".$this->datoj[kunkiuID].
                            " onClick=\"doSelect(".$kunlogxanto->datoj[ID].");\">".$kunlogxanto->datoj[personanomo]." ".$kunlogxanto->datoj[nomo]."</A>";
                    }
                if ($this->datoj[kunkiu]!="")
                    {
                        $vosto .= " (".$this->datoj[kunkiu].")";
                    }
            }
        kampo("",$vosto);
    
        if ($this->datoj[ekskursbileto][0]=="J")
            {
                kampo("[X]","mendis bileton por la tutaga ekskurso");
            }

        if ($this->datoj[tema])
            {
                kampo("[X]","kontribuos al la tema programo per: ".$this->datoj[tema]);
            }
        if ($this->datoj[distra])
            {
                kampo("[X]","kontribuos al la distra programo per: ".$this->datoj[distra]);
            }
        if ($this->datoj[vespera])
            {
                kampo("[X]","kontribuos al la vespera programo per: ".$this->datoj[vespera]);
            }
        if ($this->datoj[muzika])
            {
                kampo("[X]","kontribuas al la muzika vespero: ".$this->datoj[muzika]);
            }
        if ($this->datoj[nokta])
            {
                kampo("[X]","kontribuas al la nokta programo per: ".$this->datoj[nokta]);
            }
  
        if ($this->datoj[rimarkoj])
            {
                kampo("rimarkoj:",$this->datoj[rimarkoj]);    
            }
        if ($this->datoj['aligxdato'])
            {
                kampo("alveno de la alig^o:",$this->datoj['aligxdato']);
            }

        if ($this->datoj['malaligxdato'] != "0000-00-00")
            {
                kampo("alveno de la malalig^o:",$this->datoj['malaligxdato']);
            }
	
        kampo("1a konf.:",$this->datoj['1akonfirmilosendata']);
        kampo("2a konf.:",$this->datoj['2akonfirmilosendata']);
        echo "</table>\n";
    }


    function domotipo()
    {
        switch($this->datoj['domotipo']{0})
            {
            case 'J':
                return "junulargastejo";
            case 'M':
                return "memzorgantejo";
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


    function cxambrotipo()
    {
        switch($this->datoj['cxambrotipo']{0})
            {
            case 'u':
                return "unuseksa";
            case 'g':
                return "gea";
            }
    }

    /**
     * storita invitpeto-objekto por reuzo.
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

/* ###################################### */
 /* redonas la datumoj de iu renkontigxo */
 /* ###################################### */

  // TODO:
  // Auswahl des Treffens, im Moment nur eines, später vielleicht ueber eine Vorauswahl
  // Hmm, cxi tie oni devas elekti renkontigxnumeron, eble mi trovos alian solvon.


  /**
   * Ecoj de renkontigxo (tabelo "renkontigxo")
   * -------------------------------------------
   * Gxenerale
   *  - ID
   *       interna identifikilo
   *  - nomo
   *       oficiala nomo (ekz-e "45 a Internacia Seminario")
   *  - mallongigo
   *      interna mallongigo, gxis nun
   *      uzata nur por la partoprenanto-listo
   *      (ekzemple "IS 2003")
   *  - temo
   *  - loko
   * -----------------------------------
   * Por kotizokalkulo
   *  - de
   *      alventago
   *  - gxis
   *      forirtago
   *  - plej_frue
   *      fino de unua aligxperiodo (ekz-e 2003-10-01)
   *  - meze
   *      fino de dua aligxperiodo (ekz-e 2003-12-01)
   *  - parttemppartoprendivido
   *      Se partoprenanto partoprenas nur parttempe, li
   *      pagas laux la formulo "tagoj/divido * normala kotizo"
   *      (ekz-e 6)
   *  - juna
   *      la limagxo por junuloj - se ies agxo estas <=,
   *      li estas en la plej malmultekosta kategorio.
   *      (ekz-e 20)
   *  - maljuna
   *     la limagxo por maljunuloj - se ies agxo estas >,
   *     li estas en la plej alta kategorio. (La krompago
   *     por >= 40 ankoraux ne enestas.)
   * -----------------------------------
   * respond(ec)uloj
   *      ili ricevas retmesagxojn, kiam iu aligxas
   *      kiu povas kontribui al la programo, bezonas
   *      invitleteron ktp.
   *      ...respond(ec)ulo estas la nomo, ...retadreso
   *      estas la retadreso de la ulo.
   *      La adminrespondeculo ricevas retmesagxon pri cxiu
   *      nova aligxinto.
   *  - adminrespondeculo
   *  - adminretadreso
   *  - invitleterorespondeculo
   *  - invitleteroretadreso
   *  - temarespondulo
   *  - temaretadreso
   *  - distrarespondulo
   *  - distraretadreso
   *  - vesperarespondulo
   *  - vesperaretadreso
   *  - muzikarespondulo
   *  - muzikaretadreso
   *
   * Atentu: la nomojn de tiuj datumbazkampoj uzas la
   * funkcioj "funkciulo" kaj "funkciuladreso" (kaj
   * ties uzantoj) (sube).
   */

 class Renkontigxo extends Objekto
{
  
    /* konstruilo */
    function Renkontigxo($id)
    {
        //$this->datoj = mysql_fetch_assoc(sql_faru("Select * from renkontigxo where ID=$id"));
        $this->Objekto($id,"renkontigxo");
    }
  
}

/*
 * Elekto de la renkontigxo.
 *
 * Se oni elektis renkontigxon per
 * la elektilo (= estas io en $_REQUEST["formrenkontigxo"]),
 * ni uzas tiun.
 * Alikaze, se en la $_SESSION["renkontigxo"]
 * estas ankoraux renkontigxo, ni elektas
 * tiun.
 * Alikaze, ni elektas la defauxltan
 * renkontigxon (-> DEFAUXLTA_RENKONTIGXO)
 *
 * La funkcio redonas la renkontigxo-objekton.
 */
function kreuRenkontigxon()
{
    if ($_REQUEST["formrenkontigxo"])
        {
            if (is_array($_REQUEST["formrenkontigxo"]))
                {
                    if (DEBUG) echo "<!-- renkontigxo el formrenkontigxo=" . $_REQUEST["formrenkontigxo"][0] . " -->";
                    $renkontigxo = new Renkontigxo($_REQUEST["formrenkontigxo"][0]);
                }
            else
                {
                    if (DEBUG) echo "<!-- renkontigxo el formrenkontigxo=" . $_REQUEST["formrenkontigxo"] . " -->";
                    $renkontigxo = new Renkontigxo($_REQUEST["formrenkontigxo"]);
                }
        }
    else if ($_SESSION["renkontigxo"])
        {
            if (DEBUG) echo "<!-- renkontigxo el sesio -->";
            $renkontigxo = $_SESSION["renkontigxo"];
        }
    else
        {
            if (DEBUG) echo "<!-- defauxlta renkontigxo! -->";
            $renkontigxo = new Renkontigxo(DEFAUXLTA_RENKONTIGXO);
        }
    return $renkontigxo;
}



/**
 * donas retadreson de funkciulo pri ... de la aktuala renkontigxo.
 */
function funkciuladreso($funkcio)
{
    return $_SESSION["renkontigxo"]->datoj[$funkcio . "retadreso"];
}

/**
 * Redonas la nomon de la respondeculo pri iu funkcio.
 */
function funkciulo($funkcio)
{
    $datoj = $_SESSION["renkontigxo"]->datoj;
    if (array_key_exists($funkcio . "respondulo", $datoj))
        {
            return $datoj[$funkcio . "respondulo"];
        }
    else
        {
            return $datoj[$funkcio . "respondeculo"];
        }
}



/**
 * Noto - tabelo "notoj".
 *
 * ID              - identifikilo por cxiu noto
 * partoprenantoID - la partoprenanto, al kiu rilatas la noto
 * kiu             - kiu skribis la noto (simpla teksto)
 * kunKiu          - komunikpartnero (al kiu aux de kiu oni ricevis la informojn)
 * tipo            - tipo de la noto: 
 *                       telefon
 *                       persone
 *                       letere
 *                       rete
 *                       rimarko
 * dato            - dato de kreo de la noto (sed tamen sxangxebla)
 * subjekto        - temo/titolo de la noto
 * enhavo          - libera teksto
 * prilaborata     - aux '' (ne prilaborata) aux 'j' (prilaborata)
 * revido          - revidu la noton ekde tiu dato.
 */
class Noto extends Objekto
{

    /* konstruilo */
    function Noto($id=0)
    {
        $this->Objekto($id,"notoj");
    }
}

/**
 * TODO: traduku: Zahlungen der einzelnen Teilnehmer.
 * Tabelo "pagoj".
 */
class Pago extends Objekto
{

    /* konstruilo */
    function Pago($id=0)
    {
        $this->Objekto($id,"pagoj");
    }
}
/**
 * TODO: traduku: Kassenführung ...
 * tabelo "monujo".
 */
class Monujo extends Objekto
{

    function Monujo($id=0)
    {
        $this->Objekto($id,"monujo");
    }
}

/**
 * TODO: traduku:
 * Rabatte der einzelnen Teilnehmer (pro Teilnahme)
 * - mit Grund (KKRen/distra/tema/nokta/alia),
 *  ID der Teilnahme, Betrag
 * tabelo "rabatoj".
 */
class Rabato extends Objekto
{

    /* konstruilo */
    function Rabato($id=0)
    {
        $this->Objekto($id,"rabatoj");
    }
}

/**
 * Ecoj de la cxambro (tabelo "cxambroj")
 * - parte fiksitaj (unufoje entajpendaj
 *   antaux la renkontigxo, el datoj
 *   de la junulargastejo)
 *    - ID
 *    - renkontigxo
 *    - nomo
 *    - etagxo
 *    - litonombro
 * - parte sxangxeblaj dum la administrado/cxambrodisdono:
 *    - tipo (i/v/g)
 *    - dulita (J/N)
 *    - rimarkoj (iu teksto)
 */
class Cxambro extends Objekto
{

    /* konstruilo */
    function Cxambro($id=0)
    {
        $this->Objekto($id,"cxambroj");
    }
}


/**
 * Deziroj de kunlogxado
 *
 * ID           - interna identifikilo
 * partoprenoID - ID de la partopreno de tiu ulo, kiu deziras kunlogxi
 * alKiuID      - ID de la partopreno de tiu ulo, kiu estas dezirata por kunlogxado
 * stato      - cxu eblas, ne eblas, ...
 */
class Kunlogxdeziro extends Objekto
{

    function Kunlogxdeziro($id = 0)
    {
        $this->Objekto($id, "kunlogxdeziroj");
    }

    function stato()
    {
        return kunlogx_stato($this->datoj['stato']);
    }

}


/**
 * specialaj nomsxildoj (por nepartoprenantoj)
 *
 ****** 
 CREATE TABLE `is_nomsxildoj` (
 `ID` INT NOT NULL AUTO_INCREMENT ,
 `titolo_lokalingve` VARCHAR( 15 ) NOT NULL ,
 `titolo_esperante` VARCHAR( 15 ) NOT NULL ,
 `nomo` VARCHAR( 20 ) NOT NULL ,
 `funkcio_lokalingve` VARCHAR( 30 ) NOT NULL ,
 `funkcio_esperante` VARCHAR( 30 ) NOT NULL ,
 PRIMARY KEY ( `ID` ) 
 ) TYPE = MYISAM COMMENT = 'por specialaj nomsxildoj (por nepartopenantoj)';
 ******
 *
 */
class Speciala_Nomsxildo extends Objekto
{

  function Speciala_Nomsxildo($id = 0)
  {
	$this->Objekto($id, "nomsxildoj");
  }

}


?>
