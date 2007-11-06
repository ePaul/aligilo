<?php

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

    /**
     * implementenda de subklasoj.
     *
     * eltrovas la kategorio-IDon en tiu cxi kategorisistemo,
     * en kiu estus la $partoprenanto per sia $partopreno en $renkontigxo.
     */
    function trovu_kategorion($partoprenanto, $partopreno, $renkontigxo) {
        return NULL;
    }
    

    function listu_kategoriojn()
    {
        $katklaso = ucfirst($this->tipo) . "kategorio";
        $teksto = "<table class='kategoriolisto'>\n<tr>" . $this->donu_kategoritabelkapon() .
            "</tr>\n";
        

        
        $sql = datumbazdemando("ID",
                               $this->tipo . "kategorioj",
                               "sistemoID = '" . $this->datoj['ID']."'");
        $rez = sql_faru($sql);
        while ($linio = mysql_fetch_assoc($rez)) {
            $kat = new $katklaso($linio['ID']);
            $teksto .= "<tr>" . $kat->donu_tabellinion() . "</tr>\n";
        }
        $teksto .= "</table>";
        return $teksto;
    }

    function donu_kategoritabelkapon() {
        // implementenda en subklasoj, kongrue al kategorio->donu_tabellinion
        return "<th>ID</th><th>nomo</th><th>Priskribo</th>";
    }
} // class kategorisistemo

function donu_katsisnomon($tipo) {
   return $tipo . "kategorisistemo";
}

function donu_katsistemon($id, $tipo) {
    $klaso = ucfirst($tipo). "kategorisistemo";
    return new $klaso($id);
}


function donu_eokatsisnomon($tipo) {
    return
        strtr($tipo, 'xX', '^^') . "kategorisistemo";
}


/**
 * Redonas la nomon de kategorio, en formo por montri
 * al la uzanto (do en la g^-kodigo, sen xoj).
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

    // implementenda en subklasoj
    function donu_tabellinion()
    {
        return
            "<td>" . donu_ligon("kategorio.php?tipo=" . $this->tipo .
                                "&id=" . $this->datoj['ID'],
                                $this->datoj['ID'])
            . "</td>" .
            "<td>" . $this->datoj['nomo'] . "</td>" .
            "<td>" . $this->datoj['priskribo'] . "</td>";
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
     */
    function trovu_kategorion($partoprenanto, $partopreno, $renkontigxo)
    {
        debug_echo("<!-- trovu_kategorion[lando](): ppanto: " . var_export($partoprenanto, true) . "-->");
        $kat = $this->donu_kategorion_por($partoprenanto->datoj['lando']);
        return $kat->datoj['ID'];
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
    function trovu_kategorion($partoprenanto, $partopreno, $renkontigxo) {
        $renkDato = $renkontigxo->datoj['de'];
        if ($partopreno->datoj['aligxkategoridato'] != "0000-00-00") {
            $aligxDato = $partopreno->datoj['aligxkategoridato'];
        }
        else {
            // TODO: KKRen = unua kategorio
            // TODO: rigardu pagojn
            $aligxDato = $partopreno->datoj['aligxdato'];
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
        if ($linio) {
            return $linio['ID'];
        }
        return NULL;
    }

    function donu_kategoritabelkapon() {
        return parent::donu_kategoritabelkapon() . "<th>limdato</th>";
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

    function donu_tabellinion() {
        $teksto = parent::donu_tabellinion();
        $teksto .= "<td>" . $this->datoj['limdato'] . "</td>";
        return $teksto;
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
     */
    function trovu_kategorion($partoprenanto, $partopreno, $renkontigxo) {
        if (DEBUG) {
            echo "<!-- trovu[agx]kategorion(). partopreno: " . var_export($partopreno, true) . "-->";
        }
        // TODO
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
        if ($linio) {
            return $linio['ID'];
        }
        return NULL;
    }

    function donu_kategoritabelkapon() {
        return parent::donu_kategoritabelkapon() . "<th>limag^o</th>";
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

    function donu_tabellinion() {
        $teksto = parent::donu_tabellinion();
        $teksto .= "<td>" . $this->datoj['limagxo'] . "</td>";
        return $teksto;
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
            return $linio['ID'];
        return NULL;
    }

    function donu_kategoritabelkapon() {
        return parent::donu_kategoritabelkapon() . "<th>s^losillitero</th>";
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
    function donu_tabellinion() {
        $teksto = parent::donu_tabellinion();
        $teksto .= "<td>" . $this->datoj['sxlosillitero'] . "</td>";
        return $teksto;
    }
}


/************************************************************************/




?>