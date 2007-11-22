<?php

  // atentu: por trovu_kategorion() (kun kotizosistemo->eltrovu_kategoriojn())
  // necesas, ke 'lando' estas antaux 'aligx'.

$kategoriotipoj = array(
                        'lando',
                        'agx',
                        'logx',
                        'aligx',
                        );

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


/**************************************************************************/

/**
 * superklaso por kategorisistemoj.
 */
class Kategorisistemo extends Objekto {

    var $tipo;

    function Kategorisistemo($id, $tipo) {
        $this->tipo = $tipo;
        $this->Objekto($id, $tipo . "kategorisistemoj");
    }

    function donu_eoklasnomon() {
        return donu_eokatsisnomon($this->tipo);
    }

    function katKlasnomo() {
        return ucfirst($this->tipo) . "kategorio";
    }

    /**
     * implementenda de subklasoj.
     *
     * eltrovas la kategorio-IDon en tiu cxi kategorisistemo,
     * en kiu estus la $partoprenanto per sia $partopreno en $renkontigxo.
     *   redonas:
     *        array('ID' => identifikilo de la kategorio,
     *              'kialo' => iu teksto aux array(de => ..., eo => ...)).
     */
    function trovu_kategorion($partoprenanto, $partopreno, $renkontigxo) {
        return NULL;
    }

    /**
     * kopias cxiujn kategoriojn de alia kategorisistemo
     * por tiu sistemo. Tiu funkcio estu nur unufoje vokata post
     * kreo de nova sistemo, se entute.
     *
     * $alia_sistemoID  - ID de alia kategorisistemo (de sama tipo)
     */
    function kopiu_kategoriojn_el($alia_sistemoID) {

        // versxajne rekta insert ... select estus pli efika. Tio
        // tamen (nun) ne eblas per niaj datumbaz-funkcioj.

        $katklaso = $this->katKlasnomo();

        $sql = datumbazdemando("ID",
                               $this->tipo . "kategorioj",
                               "sistemoID = '" . $alia_sistemoID."'");
        $rez = sql_faru($sql);
        while ($linio = mysql_fetch_assoc($rez)) {
            // la malnova kategorio
            $kat = new $katklaso($linio['ID']);
            $katID = $kat->datoj['ID'];
            // ni sxangxu la informojn
            $kat->datoj['sistemoID'] = $this->datoj['ID'];
            // nova kategorio-ero
            $kat->skribu_kreante();
            // kopiu rilatajn objektojn en aliaj tabeloj, se necesas
            $kat->finu_kopiadon_el($katID, $alia_sistemoID);
        }
        
    }
    
    /**
     * eldonas liston de la kategorioj de tiu cxi kategoriosistemo.
     * 
     * $versio - "simpla" - kreas tekstan tabelon
     *           "redaktebla" - kreas tabelon kun redaktiloj.
     */
    function listu_kategoriojn($versio="simpla")
    {
        $katklaso = $this->katKlasnomo();

        echo ("<table class='kategoriolisto'>\n<tr>");
        $this->kreu_kategoritabelkapon($versio);
        echo ("</tr>\n");
        
        $sql = datumbazdemando("ID",
                               $this->tipo . "kategorioj",
                               "sistemoID = '" . $this->datoj['ID']."'");
        $rez = sql_faru($sql);
        while ($linio = mysql_fetch_assoc($rez)) {
            $kat = new $katklaso($linio['ID']);
            echo "<tr>";
            $kat->kreu_tabellinion($versio);
            echo "</tr>\n";
        }
        echo "</table>";
    }

    /**
     * sxangxenda en subklasoj, kongrue al kategorio->kreu_tabellinion.
     */
    function kreu_kategoritabelkapon() {
        eoecho("<th>ID</th><th>nomo</th><th>Priskribo</th>");
    }


    /**
     * funkcio vokata de kategorisistemo.php post sxangxo de kategorioj.
     * Se listu_kategoriojn() kreis pliajn redaktilojn, kiuj ne havas nomojn
     * la formo "kategorio[id][kampo]", tiam necesas cxi (t.e. en subklasoj)
     * tie prilabori iliajn rezultojn.
     */
    function mangxu_aliajn_kategorisxangxojn() {
        // NOOP.
    }

    /**
     * kreas formularerojn por krei novan kategorion en tiu sistemo.
     *
     * adaptenda en subklasoj.
     */
    function kreu_kategorikreilon() {
        tabelentajpejo("nomo", "nomo", "", 20);
        granda_tabelentajpejo("priskribo", "priskribo", "", 40, 4);
    }

} // class kategorisistemo

function donu_katsisnomon($tipo) {
   return $tipo . "kategorisistemo";
}

/**
 * kreas kaj redonas kategorisistemo-objekton.
 *
 *  $id   - la identigilo de la sistemo ene de la tipo.
 *  $tipo - unu el la tipoj en $GLOBALS['kategoriotipoj'];
 */
function donu_katsistemon($id, $tipo) {
    $klaso = ucfirst($tipo). "kategorisistemo";
    return new $klaso($id);
}


/**
 * Redonas la nomon de kategorisistemoj, en formo por montri
 * al la uzanto (do en la g^-kodigo, sen x-oj).
 *
 *  $tipo - unu el la tipoj en $GLOBALS['kategoriotipoj'];
 */
function donu_eokatsisnomon($tipo) {
    return
        strtr($tipo, 'xX', '^^') . "kategorisistemo";
}


/**
 * Redonas la nomon de kategorio, en formo por montri
 * al la uzanto (do en la g^-kodigo, sen x-oj).
 *
 *  $tipo - unu el la tipoj en $GLOBALS['kategoriotipoj'];
 */
function donu_eokatnomon($tipo) {
    return
        strtr($tipo, 'xX', '^^') . "kategorio";
}

/**
 * dekodas la koncizan formon de kategori-listo produktita
 * de enkodu_kategoriojn().
 *  $kat_kodita
 *     teksto de la formo 3=1=5=6
 * redonas
 *    array('agx' => 1,
 *          'aligx' => 6,
 *          'lando' => 3,
 *          'logx' => 5)
 * (aux simile)
 */
function dekodu_kategoriojn($kat_kodita) {
    return array_combine($katnomoj, explode("=", $nomo));
}

/**
 * kodas la kategorio-liston en koncizan formon por uzi
 * ekzemple kiel array-sxlosilo. (Inversa al dekodu_kategoriojn)
 *
 * $kategorioj
 *   array('agx' => ID de agxkategorio,
 *         'logx' => ID de logxkategorio,
 *         ...
 *        )
 *
 * redonas
 *   koditan tekston de la formo
 *     1=3=5=2
 */
function enkodu_kategoriojn($kategorioj) {
    $idoj = array();
    // por ke la sxlosiloj estu en gxusta sinsekvo
    foreach($GLOBALS['kategoriotipoj'] AS $tipo) {
        $idoj[] = $kategorioj[$tipo];
    }
    return implode("=", $idoj);

}


/**
 * kreas kaj redonas kategorio-objekton.
 *
 *  $tipo - la kategorio-tipo, ekzemple "agx".
 *  $id   - la identigilo de la kategorio-objekto
 *           ene de la tipo.
 */
function donu_kategorion($tipo, $id) {
    debug_echo( "<!-- donu_kategorion('$tipo', $id); -->");
    $klaso = ucfirst($tipo). "kategorio";
    $kat = new $klaso($id);
    debug_echo("<!-- " . var_export($kat, true) . "-->");
    return $kat;
}


class Kategorio extends Objekto {

    var $tipo;

    function Kategorio($id, $tipo) {
        $this->tipo = $tipo;
        $this->Objekto($id, $tipo . "kategorioj");
    }

    /**
     *
     * $versio - aux "simpla" aux "redaktebla".
     */
    function kreu_tabellinion($versio)
    {
        switch($versio) {
        case 'simpla':
            eoecho(
                "<td>" . /* donu_ligon("kategorio.php?tipo=" . $this->tipo .
                          "&id=" . $this->datoj['ID'], */
                $this->datoj['ID'] //)
                . "</td>" .
                "<td>" . $this->datoj['nomo'] . "</td>" .
                "<td>" . $this->datoj['priskribo'] . "</td>"
                );
            break;
        case 'redaktebla':
            echo("<td>" . $this->datoj['ID'] . "</td><td>");
            simpla_entajpejo("", 'kategorio['.$this->datoj['ID']. '][nomo]',
                             $this->datoj['nomo'], 20);
            granda_entajpejo("</td><td>",
                             'kategorio['.$this->datoj['ID']. '][priskribo]',
                             $this->datoj['priskribo'],
                             40, 4, "", "", "</td>");
            break;
        }
    }

    /**
     * finas la kopiadon de kategorio de unu sistemo
     * al alia (nova) sistemo.
     * Tiu funkcio estas vokata de
     *     kategorisistemo::kopiu_kategoriojn_el(),
     * post la kreado de nova kategorio-objekto en
     * la datumbazo. Cxi tie eblas kopii rilatajn datojn,
     * kiuj ne estas parto de la sama tabelo.
     *
     * implementenda en subklasoj (se necesa), la versio en
     * tiu cxi klaso faras nenion.
     */
    function finu_kopiadon_el($antauxa_katID, $antauxa_sistemoID)
    {
        // noop
    }

}


/************ kategoriado laux lando ***********/


/**
 * landokategorisistemoj:
 * 
 * - nomo
 * - entajpanto
 * (- defauxlta_kategorio)
 *
 * kategorioj_de_landoj:
 *  - kategorioID  - ID de la kategorio
 *  - sistemoID    - identigilo por la kategoriosistemo
 *  - landoID      - identigilo por la lando
 *  la lastaj du kune estas sxlosilo.
 *
 */
class Landokategorisistemo extends Kategorisistemo {


    function Landokategorisistemo($id=0) {
        $this->Kategorisistemo($id, "lando");
    }

    /**
     * eltrovas la kategorion, kiun iu lando havas en tiu cxi
     * landokategorisistemo.
     *
     * $landoID  - aux la identigilo de iu lando, aux
     *             partoprenanto-objekto (kies lando estas uzata).
     */
    function donu_kategorion_por($landoID) {
        debug_echo("<!-- landosistemo: donu_kategorion_por(" . var_export($landoID, true) . ")-->");
        if (is_object($landoID)) {
            $landoID = $landoID->datoj['lando'];
        }
        $sql = datumbazdemando("kategorioID",
                               'kategorioj_de_landoj',
                               array("sistemoID = '".$this->datoj['ID']."'",
                                     "landoID = '" . $landoID . "'")
                               );
        $rez = sql_faru($sql);
        $linio = mysql_fetch_assoc($rez);
        return new Landokategorio($linio['kategorioID']);
    }


    /**
     * eltrovas la landokategorio-IDon en tiu cxi kategorisistemo,
     * en kiu estus la $partoprenanto per sia $partopreno en $renkontigxo.
     * redonu:
     *        array('ID' => identifikilo de la kategorio,
     *              'kialo' => iu teksto aux array(de => ..., eo => ...)).
     */
    function trovu_kategorion($partoprenanto, $partopreno, $renkontigxo)
    {
        debug_echo("<!-- trovu_kategorion[lando](): ppanto: " . var_export($partoprenanto, true) . "-->");
        $kat = $this->donu_kategorion_por($partoprenanto->datoj['lando']);
        // TODO: iom pli eficienta implementado.
        return array('ID' => $kat->datoj['ID'],
                     'kialo' =>
                     array('eo' =>
                           eltrovu_landon($partoprenanto->datoj['lando']),
                           'de' =>
                           eltrovu_landon_lokalingve($partoprenanto->datoj['lando'])
                           )
                     );
    }


    /**
     * varianto de la funkcio por gxeneralaj kategoriosistemoj,
     * kun redaktilo por la landokategorioj (en versio "redaktebla").
     */
    function listu_kategoriojn($versio) {
        parent::listu_kategoriojn($versio);

        if ($versio != 'redaktebla') 
            return;

        // aldone listo de la landoj 

        $katlisto = array();
        $landolisto = array();
        $sql = datumbazdemando(array('ID', 'nomo'),
                               'landokategorioj',
                               "sistemoID = '" . $this->datoj['ID'].
                               "'");
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            $katlisto[]=$linio;
        }

        eoecho("<h3>Kategorioj de landoj</h3>");
        eoecho ("<table class='kategorioj_de_landoj'>\n".
                "<tr><th>landonomo</th><th>lokanomo</th><th>kodo</th>");
        foreach($katlisto AS $katLinio) {
            eoecho("<th>" . $katLinio['nomo'] . "</th>");
        }
        echo "</tr>\n";
        $sql = datumbazdemando(array('kategorioID', 'ID', 'nomo',
                                     'lokanomo', 'kodo'),
                               array('kategorioj_de_landoj', 'landoj'),
                               array('ID = landoID',
                                     "sistemoID = '" . $this->datoj['ID'] .
                                     "'"),
                               "",
                               array('order' => 'kodo ASC'));
        $rez = sql_faru($sql);
        while($landLinio = mysql_fetch_assoc($rez)) {
            $landolisto[$landLinio['ID']] = true;
            eoecho("<tr><td>" . $landLinio['nomo'] . "</td><td>" .
                   $landLinio['lokanomo'] . "</td><td>" .
                   $landLinio['kodo']. "</td>");
            foreach($katlisto AS $katLinio) {
                echo "<td>";
                simpla_entajpbutono('landokategorio['.$landLinio['ID'].']',
                                    $landLinio['kategorioID'],
                                    $katLinio['ID']);
                echo "</td>";
            }
            echo "</tr>\n";
        }
        $sql = datumbazdemando(array('ID', 'nomo', 'lokanomo', 'kodo'),
                               'landoj', "", "",
                               array('order' => 'kodo ASC'));
        $rez = sql_faru($sql);
        if (mysql_num_rows($rez) > count($landolisto)) {
            if (DEBUG)
                echo "<!-- " . var_export($landolisto, true) . "-->";
            $len = 3 + count($katlisto);
            eoecho("<tr><th class='titolo-sen-kat' colspan='" .
                   $len."'> Landoj sen kategorio:</th></tr>\n");
            while($landLinio = mysql_fetch_assoc($rez)) {
                if (!array_key_exists($landLinio['ID'], $landolisto)) {
                    eoecho("<tr><td>" . $landLinio['nomo'] . "</td><td>" .
                           $landLinio['lokanomo'] . "</td><td>" .
                           $landLinio['kodo']. "</td>");
                    foreach($katlisto AS $katLinio) {
                        echo "<td>";
                        simpla_entajpbutono('landokategorio['.$landLinio['ID'].']',
                                            false,
                                            $katLinio['ID']);
                        echo "</td>";
                    }
                    echo "</tr>\n";
                }
                else {
                    debug_echo("<!-- ekzistas: " . $landLinio['ID'] . "-->");
                }
            }
            
            echo "</table>\n<p>(";
            rajtligu("landoj.php", "Redaktu landoliston", "", "administri");
            echo ")</p>";
            
        }
    }

    /**
     * mangxas la rezulton de la formulareroj, kiujn produktis
     * listu_kategoriojn (en la redaktebla-varianto).
     */
    function mangxu_aliajn_kategorisxangxojn() {
        foreach($_REQUEST['landokategorio'] AS $landoID => $katID) {
            $sxlosilo = array("sistemoID = '" . $this->datoj['ID'] . "'",
                              "landoID = '". $landoID . "'");
            $sql = datumbazdemando("kategorioID",
                                   "kategorioj_de_landoj",
                                   $sxlosilo);
            $rez = sql_faru($sql);
            if ($linio = mysql_fetch_assoc($rez)) {
                if ($linio['kategorioID'] != $katID) {
                    sxangxu_datumbazon("kategorioj_de_landoj",
                                       array("kategorioID" => $katID),
                                       array('sistemoID' => $this->datoj['ID'],
                                             'landoID' => $landoID));
                }
                else {
                    debug_echo("<!-- jam ekzistas: (lando: " . $landoID .
                               ", sistemo: " . $sistemoID . ", kategorio: "
                               . $kategorioID . ") -->");
                }
            } else {
                aldonu_al_datumbazo("kategorioj_de_landoj",
                                    array('sistemoID' => $this->datoj['ID'],
                                          'landoID' => $landoID,
                                          'kategorioID' => $katID));
            }
            mysql_free_result($rez);
        }
    }


    /**
     * varianto de kreu_kategoritabelkapon, por aldoni kampon "landoj",
     * sed nur en la simpla varianto.
     */
    function kreu_kategoritabelkapon($versio) {
        parent::kreu_kategoritabelkapon();
        if ($versio == 'simpla') {
            eoecho("<th>landoj</th>");
        }
    }




}



/**
 * landokategorioj:
 *   - nomo       
 *   - klarigo    (iu legebla teksto)
 *   - ID         
 *   - sistemoID  (-> landokategorisistemo)
 *   (- minimuma_antauxpago  - cxu?)
 */
class Landokategorio extends Kategorio {
    function Landokategorio($id=0) {
        $this->Kategorio($id, "lando");
    }


    /**
     * kopiado de landokategorio (la listo de la landoj por tiu kategorio)
     */
    function finu_kopiadon_el($antauxa_katID, $antauxa_sistemoID)
    {
        // ankaux estus pli bona per insert ... select.
        $sql = datumbazdemando('landoID',
                               'kategorioj_de_landoj',
                               array("sistemoID = '" .$antauxa_sistemoID . "'",
                                     "kategorioID = '" . $antauxa_katID . "'")
                               );
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            aldonu_al_datumbazo('kategorioj_de_landoj',
                                array('kategorioID' => $this->datoj['ID'],
                                      'sistemoID' => $this->datoj['sistemoID'],
                                      'landoID' => $linio['landoID']));
        }
    }

    /**
     * kreas liston de la landoj en tiu cxi kategorio.
     */
    function listu_landojn($kampo) {
            $landolisto = array();
            $sql = datumbazdemando($kampo,
                                   array('landoj', 'kategorioj_de_landoj'),
                                   array("ID = landoID",
                                         "kategorioID = '" .
                                         $this->datoj['ID']."'"));
            $rez = sql_faru($sql);
            while($linio = mysql_fetch_assoc($rez)) {
                $landolisto[]= $linio[$kampo];
            }
            mysql_free_result($rez);
            return $landolisto;
    }

    /**
     * varianto de kreu_tabellinion() de Kategorio,
     * kiu aldonas landoliston.
     */
    function kreu_tabellinion($versio)
    {
        parent::kreu_tabellinion($versio);
        if ($versio == 'simpla') {
            $landolisto = $this->listu_landojn('kodo');
            echo "<td>" . implode(", ", $landolisto) . "</td>";
        }
    }


}


/******* kategoriado laux aligxtempoj *******/


/**
 * aligxkategorisistemo:
 * 
 * - nomo
 * - entajpanto
 */
class Aligxkategorisistemo extends Kategorisistemo {
    function Aligxkategorisistemo($id = 0) {
        $this->Kategorisistemo($id, "aligx");
    }

    /**
     * eltrovas la aligxkategorio-IDon en tiu cxi kategorisistemo,
     * en kiu estus la $partoprenanto per sia $partopreno en $renkontigxo.
     */
    function trovu_kategorion($partoprenanto, $partopreno, $renkontigxo,
                              &$kotizosistemo, $kategorioj) {
        if (DEBUG) {
            echo "<!-- partopreno: " . var_export($partopreno, true) . " -->";
        }

        $renkDato = $renkontigxo->datoj['de'];
        list($aligxDato, $kialo) =
            kalkulu_kotizorelevantan_daton($partopreno,
                                           $kotizosistemo,
                                           $kategorioj['lando']['ID']);
        if(! $aligxDato) {
            // ankoraux ne antauxpagis suficxe
            $aligxDato = $renkDato;
            $kialo = array('eo' => "sen antau^pago",
                           'de' => "ohne Anzahlung");
        }
        if (! $kialo) {
            $kialo = $aligxDato;
        }
        $rez = sql_faru(datumbazdemando(array("ID", "limdato"),
                                        "aligxkategorioj",
                                        array(/* nur la aktuala
                                               * aligxkategoriosistemo */
                                              "sistemoID = '" .
                                              $this->datoj['ID']."'",
                                              /* nur kategorioj, kies limdato
                                               * ankoraux ne pasis */
                                              "limdato < ".
                                              "  TO_DAYS('{$renkDato}') ".
                                              "- TO_DAYS('{$aligxDato}')"
                                              ),
                                        "",
                                        /* elektu la plej grandan el tiuj */
                                        array('order' => 'limdato DESC',
                                              "limit" => '1')));
        $linio = mysql_fetch_assoc($rez);
        return array('ID' => $linio['ID'], 'kialo' => $kialo);
    }

    function kreu_kategoritabelkapon() {
        parent::kreu_kategoritabelkapon();
        eoecho("<th>limdato</th>");
    }


    function kreu_kategorikreilon() {
        parent::kreu_kategorikreilon();
        tabelentajpejo("limdato", "limdato", "", 5, "(Fino de la periodo, en tagoj antau^ komenco de la renkontig^o.)");
    }


}

/**
 * redonas:
 *   array(kotizoreldato,
 *         [kialo]).
 */
function kalkulu_kotizorelevantan_daton($partopreno,
                                        $kotizosistemo,
                                        $landoKatID) {
    if ($partopreno->datoj['aligxkategoridato'] and
        $partopreno->datoj['aligxkategoridato'] != "0000-00-00")
        {
            return array($partopreno->datoj['aligxkategoridato']);
        }
    else if ($partopreno->datoj['KKRen'] == 'J')
        {
            // tio devus esti suficxe frua por esti en la unua
            //  kategorio de cxiu renkontigxo administrota, cxu ne?
            return array("2000-01-01", organizantoj_nomo);
        }
    else
        {
            $aligxDato = $partopreno->datoj['aligxdato'];
            $min_ap = $kotizosistemo->minimumaj_antauxpagoj($landoKatID);
            $min_antauxpago = $min_ap['interna_antauxpago'];
            $sql = datumbazdemando(array('kvanto', 'dato'),
                                   'pagoj',
                                   "partoprenoID = '"
                                   .  $partopreno->datoj['ID']."'",
                                   "",
                                   array('order' => "dato ASC"));
            $sumo = 0;
            $rez = sql_faru($sql);
            // se la virtuala pago de 0 dum la aligxo jam suficxas,
            // prenu la aligxdaton.
            $linio = array('kvanto' => 0, 'dato' => $aligxDato);
            do {
                $sumo += $linio['kvanto'];
                if ($sumo > $min_antauxpago) {
                    return array((strcmp($aligxDato, $linio['dato']) < 0) ?
                                 $linio['dato'] : $aligxDato);
                }
            } while ($linio = mysql_fetch_assoc($rez));
            return array(null);
        }
}


/**
 * aligxkategorioj:
 *   - nomo       (ia ajn teksto)
 *   . limdato    (tagoj antaux komenco de la renkontigxo)
 *   - ID         (interna)
 *   - sistemoID  (-> aligxkategorisistemo)
 */
class Aligxkategorio extends Kategorio {
    function Aligxkategorio($id=0) {
        $this->Kategorio($id, "aligx");
    }

    function kreu_tabellinion($versio) {
        parent::kreu_tabellinion($versio);
        switch($versio) {
        case 'simpla':
            echo("<td>" . $this->datoj['limdato'] . "</td>");
            break;
        case 'redaktebla':
            simpla_entajpejo("<td>",
                             'kategorio['.$this->datoj['ID'].'][limdato]',
                             $this->datoj['limdato'],
                             5, "",
                             "</td>");
            break;
        }
    }

    
}

/******* kategoriado laux agxo *******/

/**
 * agxkategorisistemo:
 * 
 * - nomo
 * - entajpanto
 */
class Agxkategorisistemo extends Kategorisistemo {
    function Agxkategorisistemo($id = 0) {
        $this->Kategorisistemo($id, "agx");
    }

    /**
     * eltrovas la agxkategorio-IDon en tiu cxi kategorisistemo,
     * en kiu estus la $partoprenanto per sia $partopreno en $renkontigxo.
     * redonas:
     *        array('ID' => identifikilo de la kategorio,
     *              'kialo' => iu teksto aux array(de => ..., eo => ...)).
     */
    function trovu_kategorion($partoprenanto, $partopreno, $renkontigxo) {
        if (DEBUG) {
            echo "<!-- trovu[agx]kategorion(). partopreno: " . var_export($partopreno, true) . "-->";
        }
        $agxo = $partopreno->datoj['agxo'];
        $rez = sql_faru(datumbazdemando(array("ID", "limagxo"),
                                        "agxkategorioj",
                                        array(/* nur la aktuala
                                               * aligxkategoriosistemo */
                                              "sistemoID = '" .
                                              $this->datoj['ID']."'",
                                              /* nur kategorioj, kies limagxo
                                               * nia partoprenanto ankoraux
                                               * ne trapasis
                                               */
                                              "'{$agxo}' <= limagxo"
                                              ),
                                        "",
                                        /* elektu la plej malgrandan el tiuj */
                                        array('order' => 'limagxo ASC',
                                              "limit" => '1')));
        $linio = mysql_fetch_assoc($rez);
        return array('ID' => $linio['ID'],  'kialo' => $agxo);
    }

    function kreu_kategoritabelkapon() {
        parent::kreu_kategoritabelkapon();
        eoecho("<th>limag^o</th>");
    }


    function kreu_kategorikreilon() {
        parent::kreu_kategorikreilon();
        tabelentajpejo("limag^o", "limagxo", "", 5,
                       "(maksimuma ag^o (en jaroj) por esti en".
                       " tiu ag^kategorio).");
    }


}

/**
 * agxkategorioj:
 *   - nomo       
 *   - klarigo    (iu legebla teksto)
 *   - ID         (interna)
 *   - sistemoID  (-> agxkategorisistemo)
 *   - limagxo
 */
class Agxkategorio extends Kategorio {
    function Agxkategorio($id=0) {
        $this->Kategorio($id, "agx");
    }

    function kreu_tabellinion($versio) {
        parent::kreu_tabellinion($versio);
        switch($versio) {
        case 'simpla':
            echo("<td>" . $this->datoj['limagxo'] . "</td>");
            break;
        case 'redaktebla':
            simpla_entajpejo("<td>",
                             'kategorio['.$this->datoj['ID'].'][limagxo]',
                             $this->datoj['limagxo'],
                             5, "",
                             "</td>");
            break;
        }
    }
}



/******* kategoriado laux logxado dum la renkontigxo *******/

/**
 * logxkategorisistemo:
 * 
 * - nomo
 * - entajpanto
 */
class Logxkategorisistemo extends Kategorisistemo {
    function Logxkategorisistemo($id = 0) {
        $this->Kategorisistemo($id, "logx");
    }


    /**
     * eltrovas la logxkategorio-IDon en tiu cxi kategorisistemo,
     * en kiu estus la $partoprenanto per sia $partopreno en $renkontigxo.
     */
    function trovu_kategorion($partoprenanto, $partopreno, $renkontigxo) {
        $domotipo = $partopreno->datoj['domotipo'];
        $rez = sql_faru(datumbazdemando(array("ID"),
                                        "logxkategorioj",
                                        array(/* nur la aktuala
                                               * aligxkategoriosistemo */
                                              "sistemoID = '" .
                                              $this->datoj['ID']."'",
                                              /* nur tiu kategorio, kies
                                               sxlosillitero gxustas */
                                              "sxlosillitero = '{$domotipo}'"
                                              )
                                        ));
        $linio = mysql_fetch_assoc($rez);
        if ($linio)
            return array('ID' => $linio['ID'], 'kialo' => "");
        return array('ID' => NULL, 'kialo' => "");
    }

    function kreu_kategoritabelkapon() {
        parent::kreu_kategoritabelkapon();
        eoecho("<th>s^losillitero</th>");
    }

    function kreu_kategorikreilon() {
        parent::kreu_kategorikreilon();
        tabelentajpejo("s^losillitero", "sxlosillitero", "", 2,
                       "(por retrovo kiel 'domotipo' en la partopreno.).");
    }


}


/**
 * logxkategorioj:
 *   - ID         (interna)
 *   - nomo       
 *   - sistemoID  (-> logxkategorisistemo)
 *   - sxlosillitero
 *
 * La lastaj du kune estu unikaj.
 */
class Logxkategorio extends Kategorio {
    function Logxkategorio($id=0) {
        $this->Kategorio($id, "logx");
    }
    function kreu_tabellinion($versio) {
        parent::kreu_tabellinion($versio);
        switch($versio) {
        case 'simpla':
            echo("<td>" . $this->datoj['sxlosillitero'] . "</td>");
            break;
        case 'redaktebla':
            simpla_entajpejo("<td>",
                             'kategorio['.$this->datoj['ID'].'][sxlosillitero]',
                             $this->datoj['sxlosillitero'],
                             5, "",
                             "</td>");
            break;
        }
    }
}


/************************************************************************/




?>