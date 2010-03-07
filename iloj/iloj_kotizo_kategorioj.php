<?php

  /**
   * Konfigurebla kotizosistemo.
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
   *
   * Kune kun apartaj difinoj de kostoj eblos prognozi la financan
   * rezulton de renkontigxo, kaj analizi profitodonajn kaj
   * malprofitodonajn partoprenantajn grupojn.
   *
   * @package aligilo
   * @see kondicxoj.php
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2007-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   * La haveblaj kategori-tipoj.
   *
   * atentu: por trovu_kategorion() (kun kotizosistemo->eltrovu_kategoriojn())
   * necesas, ke 'lando' estas antaux 'aligx'.
   */

$GLOBALS['kategoriotipoj'] = array(
                                   'lando',
                                   'agx',
                                   'logx',
                                   'aligx',
                                   );

$GLOBALS['kategoriotipoj_por_tabelo'] = array(
                                              'agx',
                                              'aligx',
                                              'lando',
                                              'logx',
                                              );

/**
 * germanaj tradukoj de la nomoj.
 * @todo: internaciigu.
 */

$GLOBALS['de_katnomoj'] = array('lando' => "Landeskategorie",
                                'agx' => "Alterskategorie",
                                'logx' => "Wohnkategorie",
                                'aligx' => "Anmeldekategorie");



/**************************************************************************/

/**
 * superklaso por kategorisistemoj.
 */
class Kategorisistemo extends Objekto {

    var $tipo;

    /**
     * listo de la kategorioj, por ne dauxre denove
     * devi peti la datumbazon.
     */
    var $katListo;


    function Kategorisistemo($id, $tipo) {
        $this->tipo = $tipo;
        $this->Objekto($id, $tipo . "kategorisistemoj");
    }

    /**
     * donas liston de cxiuj kategorioj de tiu kotizosistemo.
     *
     * @return array de la formo
     *              id => {@link Kategorio}
     */
    function &donu_KatListon() {
        $katListo = &$this->katListo;

        if (!is_array($katListo)) {
            $sql = datumbazdemando('ID',
                                   $this->tipo.'kategorioj',
                                   array('sistemoID' => $this->datoj['ID']));
            $rez = sql_faru($sql);
            while($linio = mysql_fetch_assoc($rez)) {
                $id = $linio['ID'];
                $katListo[$id] = & donu_kategorion($this->tipo, $id);
            }
        }
        return $katListo;
            
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
        
        $ordigo = $this->donu_kategorian_ordigon();
        if ($ordigo) {
            $aldonSQL = array("order" => $ordigo);
        }
        else {
            $aldonSQL = "";
        }

        $sql = datumbazdemando("ID",
                               $this->tipo . "kategorioj",
                               array("sistemoID" => $this->datoj['ID']),
                               "",
                               $aldonSQL);
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
     * redonas nomon de kampo, laux kiu ordigxu la kategorioj.
     *
     * anstatauxenda en subklasoj, se tiuj volas iun specifan ordigon inter
     * siaj kategorioj.
     */
    function donu_kategorian_ordigon() {
        // ne ordigu
        return 'ID';
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
    debug_echo("<!-- donu_katsistemon(" . $id . ", " . $tipo . ") -->");
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
 * @todo uzu plurlingvan varianton
 */
function donu_dekatnomon($tipo) {
    return $GLOBALS['de_katnomoj'][$tipo];
}


/**
 * dekodas la koncizan formon de kategori-listo produktita
 * de enkodu_kategoriojn().
 *
 * @param asciistring $kat_kodita
 *     teksto de la formo 3=1=5=6
 * @return array 
 *    array('agx' => 1,
 *          'aligx' => 6,
 *          'lando' => 3,
 *          'logx' => 5)
 * (aux simile)
 * @param array $sxlosiloj se donita, alternativaj
 *       sxlosiloj uzendaj.
 */
function dekodu_kategoriojn($kat_kodita, $sxlosiloj="") {
    if (! $sxlosiloj) {
        $sxlosiloj = $GLOBALS['kategoriotipoj'];
    }
    return array_combine($sxlosiloj, explode("=", $kat_kodita));
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
 * Alternative la valoroj povas esti
 *                 array('ID' => ID de ...,
 *                       'kialo' => ...),
 * kie la kialo estos simple forjxetata.
 *
 * redonas
 *   koditan tekston de la formo
 *     1=3=5=2
 */
function enkodu_kategoriojn($kategorioj) {
    $idoj = array();
    // por ke la sxlosiloj estu en gxusta sinsekvo
    foreach($GLOBALS['kategoriotipoj'] AS $tipo) {
        if (is_array($kategorioj[$tipo])) {
            $idoj[] = $kategorioj[$tipo]['ID'];
        }
        else {
            $idoj[] = $kategorioj[$tipo];
        }
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

    var $sistemo;

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
			   // TODO: prenu el tradukilo
			   //                           'de' =>
			   //                           eltrovu_landon_lokalingve($partoprenanto->datoj['lando'])
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

        // en la redaktebla versio: aldone listo de la landoj 

        $katlisto = array();
        $landolisto = array();
        $sql = datumbazdemando(array('ID', 'nomo'),
                               'landokategorioj',
                               array("sistemoID" =>  $this->datoj['ID']));
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            $katlisto[]=$linio;
        }

        eoecho("<h3>Kategorioj de landoj</h3>");
        eoecho ("<table class='kategorioj_de_landoj'>\n".
                "<tr><th>landonomo</th>".
                //                "<th>lokanomo</th>".
                "<th>kodo</th>");
        foreach($katlisto AS $katLinio) {
            eoecho("<th>" . $katLinio['nomo'] . "</th>");
        }
        echo "</tr>\n";
        $sql = datumbazdemando(array('kategorioID', 'ID', 'nomo',
                                     //'lokanomo',
                                     'kodo'),
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
                   //                   $landLinio['lokanomo'] . "</td><td>" .
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
        $sql = datumbazdemando(array('ID', 'nomo',
                                     // 'lokanomo',
                                     'kodo'),
                               'landoj', "", "",
                               array('order' => 'kodo ASC'));
        $rez = sql_faru($sql);
        if (mysql_num_rows($rez) > count($landolisto)) {
            if (DEBUG)
                echo "<!-- " . var_export($landolisto, true) . "-->";
            $len =
                // 3 + count($katlisto);
                2 + count($katlisto);
            eoecho("<tr><th class='titolo-sen-kat' colspan='" .
                   $len."'> Landoj sen kategorio:</th></tr>\n");
            while($landLinio = mysql_fetch_assoc($rez)) {
                if (!array_key_exists($landLinio['ID'], $landolisto)) {
                    eoecho("<tr><td>" . $landLinio['nomo'] . "</td><td>" .
                           //  $landLinio['lokanomo'] . "</td><td>" .
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
        }
            
        echo "</table>\n<p>(";
        rajtligu("landoj.php", "Redaktu landoliston", "", "administri");
        echo ")</p>";
        
    }


    function donu_kategorian_ordigon() {
        // ordigu laux nomo.
        return 'nomo';
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


/**
 * Eltrovas la landokategoriobjekton de iu lando por
 * iu renkontigxo.
 *
 * $id - la identigilo de iu lando.
 * $renkontigxo Renkontigxo-objekto, kies kotizosistemo estas
 *              uzenda. Defauxlto estas $_SESSION['renkontigxo']
 *              aux $GLOBALS['renkontigxo'].
 */
function eltrovu_landokategorion($id, $renkontigxo=null)
{
    if (!$renkontigxo) {
        $renkontigxo = $_SESSION['renkontigxo'] or
            $renkontigxo = $GLOBALS['renkontigxo'];
    }
    $kotizosistemo = $renkontigxo->donu_kotizosistemon();
    //    echo "<!-- kotizosistemo: " . var_export($kotizosistemo, true) . "-->";

    $landoKatSistemo = $kotizosistemo->donu_kategorisistemon("lando");
    //    echo "<!-- landoKatSistemo: " . var_export($landoKatSistemo, true) . "-->";
    
    return $landoKatSistemo->donu_kategorion_por($id);
}


/******* kategoriado laux aligxtempoj *******/

/*
 * La aligxkategorioj estas ankaux uzataj por
 * la malaligxoj - oni povas uzi la saman aligxkategorisistemon
 * ankaux por la malaligxoj, aux uzi apartan sistemon por tio.
 */


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

        list($aligxDato, $kialo) =
            kalkulu_kotizorelevantan_daton($partopreno,
                                           $kotizosistemo,
                                           $kategorioj['lando']['ID']);
        if(! $aligxDato) {
            // ankoraux ne antauxpagis suficxe
            $aligxDato = $renkontigxo->datoj['de'];
            $kialo = array('eo' => "sen antau^pago",
                           'de' => "ohne Anzahlung");
        }
        if (! $kialo) {
            $kialo = $aligxDato;
        }
        return array('ID' => $this->trovu_kategorion_laux_dato($renkontigxo,
                                                               $aligxDato),
                     'kialo' => $kialo);
    }


    /**
     * donas identigilon de kategorio laux dato (por iu renkontigxo.)
     */
    function trovu_kategorion_laux_dato($renkontigxo, $aligxDato) {
        $renkDato = $renkontigxo->datoj['de'];
        $rez = sql_faru(datumbazdemando(array("ID", "limdato"),
                                        "aligxkategorioj",
                                        array(/* nur la aktuala
                                               * aligxkategoriosistemo */
                                              "sistemoID = '" .
                                              $this->datoj['ID']."'",
                                              /* nur kategorioj, kies limdato
                                               * ankoraux ne pasis */
                                              "limdato <= ".
                                              "  TO_DAYS('{$renkDato}') ".
                                              "- TO_DAYS('{$aligxDato}')"
                                              ),
                                        "",
                                        /* elektu la plej grandan el tiuj */
                                        array('order' => 'limdato DESC',
                                              "limit" => '1')));
        $linio = mysql_fetch_assoc($rez);
        return $linio['ID'];
    }


    function kreu_kategoritabelkapon() {
        parent::kreu_kategoritabelkapon();
        eoecho("<th>limdato</th><th>Limdato por " . $_SESSION['renkontigxo']->datoj['mallongigo'] . "</th>");
    }


    function kreu_kategorikreilon() {
        parent::kreu_kategorikreilon();
        tabelentajpejo("limdato", "limdato", "", 5, "(Fino de la periodo, en tagoj antau^ komenco de la renkontig^o.)");
    }


    function donu_kategorian_ordigon() {
        // ordigu laux nomo.
        return 'limdato DESC';
    }


    /**
     * Kreas liston de cxiuj kotizokategorioj kun limdatoj, kie ties limdato
     * ankoraux estas en la estonteco.
     *
     * @return array de la formo
     *    kategorioID => limdato
     */
    function listu_limdatojn($surloke, $renkontigxo=null, $ekde=null) {
        if (!$ekde) {
            $ekde = date("Y-m-d");
        }
        $renkontigxo = kreuRenkontigxon($renkontigxo);

        $renkDato = $renkontigxo->datoj['de'];
        $listo = array();
        $rez = sql_faru(datumbazdemando(array("ID", "limdato",
                                              "DATE_SUB('". $renkDato.
                                              "', INTERVAL limdato DAY)"
                                              => "limdato_abs"),
                                        "aligxkategorioj",
                                        array('sistemoID'
                                              => $this->datoj['ID']),
                                        "",
                                        array("order" => "limdato_abs ASC")
                                        ));
        while($linio = mysql_fetch_assoc($rez)) {
            if ($linio['limdato_abs'] > $ekde) {
                if ($linio['limdato'] > 0) {
                    $listo[$linio['ID']] = $linio['limdato_abs'];
                }
                else {
                    $listo[$linio['ID']] = $surloke;
                }
            }
        }
                                        
        return $listo;
    }
}

/**
 * @param u8string $surloke
 * @param Renkontigxo $renkontigxo
 */
function listu_limdatojn($surloke, $renkontigxo=null, $ekde=null) {
    $renkontigxo = kreuRenkontigxon($renkontigxo);
    $kotizosistemo = $renkontigxo->donu_kotizosistemon();
    $katsistemo = $kotizosistemo->donu_kategorisistemon("aligx");
    return $katsistemo->listu_limdatojn($surloke, $renkontigxo, $ekde);
}


/**
 * redonas:
 *   array(kotizoreldato,
 *         [kialo]).
 */
function kalkulu_kotizorelevantan_daton($partopreno,
                                        $kotizosistemo,
                                        $landoKatID) {
    debug_echo("<!-- kalkulu_kotizorelevantan_daton() -->");
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
            $sql = datumbazdemando(array('ID', 'dato'),
                                   'pagoj',
                                   array('partoprenoID' =>
                                         $partopreno->datoj['ID']),
                                   "",
                                   array('order' => "dato ASC"));
            $sumo = 0;
            $rez = sql_faru($sql);
            // se la virtuala pago de 0 dum la aligxo jam suficxas,
            // prenu la aligxdaton.
            $pago = new Pago();
            $pago->datoj['dato'] = $aligxDato;
            $pago->datoj['kvanto'] = 0;
            $pago->datoj['valuto'] = CXEFA_VALUTO;

            do {
                $sumo += $pago->enCxefaValuto();
                if ($sumo >= $min_antauxpago) {
                    return array((strcmp($aligxDato,
                                         $linio['dato']) < 0) ?
                                 $linio['dato'] :
                                 $aligxDato);
                }
                $linio = mysql_fetch_assoc($rez);
            } while ($linio && $pago=new Pago($linio['ID']));
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
            eoecho("<td>" . $this->datoj['limdato'] . "</td><td>" .
                   $this->limdato_por_renkontigxo() . "</td>");
            break;
        case 'redaktebla':
            simpla_entajpejo("<td>",
                             'kategorio['.$this->datoj['ID'].'][limdato]',
                             $this->datoj['limdato'],
                             5, "",
                             "</td>");
            echo "<td>" . $this->limdato_por_renkontigxo() . "</td>";
            break;
        }
    }

    function limdato_por_renkontigxo($renkontigxo=null) {
        if (!$renkontigxo) {
            $renkontigxo = $_SESSION['renkontigxo'];
        }
        return kalkulu_per_datumbazo("DATE_SUB('" . $renkontigxo->datoj['de']
                                     ."', INTERVAL " . $this->datoj['limdato']
                                     . " DAY)");
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
 * - priskribo
 */
class Logxkategorisistemo extends Kategorisistemo {


    function Logxkategorisistemo($id = 0) {
        $this->Kategorisistemo($id, "logx");
    }


    /**
     * eltrovas la logxkategorio-IDon en tiu cxi kategorisistemo,
     * en kiu estus la $partoprenanto per sia $partopreno en $renkontigxo.
     * 
     */
    function trovu_kategorion($partoprenanto, $partopreno, $renkontigxo)
    {
        $katListo = & $this->donu_katListon();
        $objektoj = kreu_objektoliston($partoprenanto, $partopreno,
                                       $renkontigxo);
        foreach($katListo AS $id => $kategorio) {
            if ($kategorio->aplikigxas($objektoj)) {
                return array('ID' => $kategorio->datoj['ID'],
                             'kialo' => "");
            }
        }
        return null;
    }

    function kreu_kategoritabelkapon() {
        parent::kreu_kategoritabelkapon();
        eoecho("<th>Kondic^o</th>");
    }

    function kreu_kategorikreilon() {
        parent::kreu_kategorikreilon();
        tabela_kondicxoelektilo("Kondic^o por esti en tiu kategorio.");

    }


}


/**
 * logxkategorioj:
 *   - ID         (interna)
 *   - nomo       
 *   - sistemoID  (-> logxkategorisistemo)
 *   - kondicxo (ID de kondicxo)
 *
 */
class Logxkategorio extends Kategorio {

    var $kondicxo = null;

    function Logxkategorio($id=0) {
        $this->Kategorio($id, "logx");
    }

    function &donu_kondicxon() {
        if (!$this->kondicxo) {
            $this->kondicxo =& new Kondicxo($this->datoj['kondicxo']);
        }
        return $this->kondicxo;
    }

    function aplikigxas($objektoj) {
        $kondicxo =& $this->donu_kondicxon();
        return $kondicxo->validas_por($objektoj);
    }

    function kreu_tabellinion($versio) {
        parent::kreu_tabellinion($versio);
        switch($versio) {
        case 'simpla':
            $kondicxo =& $this->donu_kondicxon();
            echo("<td>" . $kondicxo->datoj['nomo'] . "</td>");
            break;
        case 'redaktebla':
            echo ("<td>");
            simpla_kondicxoelektilo('kategorio['. $this->datoj['ID'].
                                    '][kondicxo]',
                                    $this->datoj['kondicxo']);
            echo ("</td>");

//             simpla_entajpejo("<td>",
//                              'kategorio['.$this->datoj['ID'].'][sxlosillitero]',
//                              $this->datoj['sxlosillitero'],
//                              5, "",
//                              "</td>");
            break;
        }
    }
}


/************************************************************************/




?>