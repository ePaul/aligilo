<?php


  /**
   * La kosto-kalkulado, por prognozi rezulton de renkontigxoj
   * laux informoj de antauxaj renkontigxoj.
   *
   * Ankoraux ne vere funkcias.
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage iloj
   * @copyright 2007-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   * tipo de personaj kostoj
   *
   * personkostotipoj:
   *  - ID
   *  - nomo
   *  - priskribo
   *  - entajpanto - entajpanto-ID de la verkinto
   *  - kondicxo - kodo de anonima funkcio, vokota per la parametroj
   *                $partoprenanto, $partopreno, $renkontigxo (po la objekto)
   *              gxi redonu true aux false.
   *          TODO: !! Tio estas iometa sekureca risko. Atentu, ke   !!!
   *                !!   nur teknikistoj povos sxangxi tiun tekston. !!!
   *  - uzebla  - j (estos montrata en listoj por elekti)
   *            - n (nur montrata por teknikistoj, por redakti gxin)
   *  - lauxnokte - j (kromkosto por cxiu nokto, kiun oni tranoktas)
   *                n (unufoja kromkosto)
   *
   * personkostoj:
   *   - kostosistemo   (ID)
   *   - tipo           (ID de personkostotipo)
   *   - maks_haveblaj
   *   - min_uzendaj
   *   - kosto_uzata
   *   - kosto_neuzata
   */
class Personkostotipo extends Objekto {

    var $kondicxo;

    function Personkostotipo($id=0) {
        $this->Objekto($id, "personkostotipoj");
    }

    /*    function kreu_kondicxon() {
        $this->kondicxo =
        create_function('$partoprenanto,$partopreno,$renkontigxo',
             $this->datoj['kondicxo']);
    }
    */

    /**
     * versio de la funkcio el Objekto, por poste rekrei
     * la kondicxo-funkcion.
     */
    function prenu_el_datumbazo($id="") {
        parent::prenu_el_datumbazo($id);
        /*        $this->kreu_kondicxon(); */
    }



    /**
     * eltrovas, cxu tiu personkostotipo aplikigxas en iu specifa
     * situacio.
     *  -> true: jes, la partoprenanto devos pagi la personkoston
     *  -> false: ne, ...
     */
    function aplikigxas($partoprenanto, $partopreno, $renkontigxo)
    {
        $funk = "kondicxo_" . $this->datoj['kondicxo'];
        return
            $funk($partoprenanto, $partopreno, $renkontigxo);
    }

}  // Personkostotipo

function listu_cxiujn_personkostotipojn($kondicxo = "uzebla = 'j'") {
    $rezulto = array();

    $sql = datumbazdemando("ID",
                           "personkostotipoj",
                           $kondicxo);
    $rez = sql_faru($sql);
    while($linio = mysql_fetch_assoc($rez)) {
        $rezulto[]= new Personkostotipo($linio['ID']);
    }
    
    return $rezulto;
}


/*************************************************************************/


  /*
   * kotizosistemo:
   *   - ID
   *   - nomo
   *   - priskribo
   *   - entajpanto
   *   - landokategorisistemo
   *   - logxkategorisistemo
   *   - agxkategorisistemo
   *   - aligxkategorisistemo
   *   - parttempdivisoro
   *
   */
class Kostosistemo extends Objekto {


    var $personkostolisto;

    function Kostosistemo($id=0)
    {
        $this->Objekto($id, "kostosistemoj");
    }

    /**
     * donas liston de cxiuj personkostoj, kiuj estas relevantaj
     * en tiu cxi kostosistemo.
     * redonas:
     *  array()  el elementoj de la formo
     *     array('tipo' =>       Personkostotipo-objekto
     *           'personkosto' =>   la personkosto en cxi tiu kostosistemo.
     */
    function donu_personkostoliston()
    {
        if($this->personkostolisto)
            {
                return $this->personkostolisto;
            }
        $listo = array();
        $sql = datumbazdemando(array("tipo", "kosto_uzata", "kosto_neuzata",
                                     "min_uzendaj", "maks_haveblaj"),
                               "personkostoj",
                               "kostosistemo = '{$this->datoj['ID']}'");
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            $listo[] = array('tipo' => new Personkostotipo($linio['tipo']),
                             'personkosto' => $linio);
        }
        $this->personkostolisto = $listo;
        return $listo;
    }


}  // class kostosistemo


/**
 * Tabelo por fiksaj kostoj de iu renkontigxo. Por plifaciligi la redaktadon
 * (kaj poste vidi la kalkuladon) eblas havi plurajn fikskostojn en unu
 * renkontigxo.
 *
 * fikskostoj:
 * - ID
 * - nomo
 * - kostosistemo
 */
class Fikskosto extends Objekto
{

    function Fikskosto($id = 0)
    {
        $this->Objekto($id, "fikskostoj");
    }


}


/**
 * klaso por kalkuli la kostojn de iu renkontigxo.
 */
class Kostokalkulilo {

    var $renkontigxo;
    var $kostosistemo;
    var $sumo_personaj_kostoj;


    /**
     * array( tipoID => array('tipo' => personkostotipo-Objekto,
     *                        'noktoj' => ...  (por lauxnoktaj)
     *                        'personkosto' => la detaloj el la personkostoj-tabelo.
     *                        'uzo' => ...     (por unufojaj)
     *                        ...
     *                       )
     */
    var $personaj_kostoj_laux_tipo;
    

    /**
     * kreas novan kostokalkulilon kun iu kostosistemo.
     */
    function Kostokalkulilo($kostosistemo, $renkontigxo) {
        $this->renkontigxo = $renkontigxo;
        $this->kostosistemo = $kostosistemo;
        $this->sumo_personaj_kostoj = 0;
        $this->personaj_kostoj_laux_tipo = array();

        $dauxro = $this->renkontigxo->renkontigxonoktoj();
        $noktoj_sxablono = array();
        for($i = 1; $i <= $dauxro; $i++)
            {
                $noktoj_sxablono[$i] = array('uzo' => 0);
            }

        foreach($this->kostosistemo->donu_personkostoliston() AS $listero) {
            if ($listero['tipo']->datoj['lauxnokte'] == 'j')
                {
                    $this->personaj_kostoj_laux_tipo[$listero['tipo']->datoj['ID']] =
                        array('tipo' => $listero['tipo'],
                              'noktoj' => $noktoj_sxablono,
                              'personkosto' => $listero['personkosto'] );
                }
            else
                {
                    $this->personaj_kostoj_laux_tipo[$listero['tipo']->datoj['ID']] =
                        array('tipo' => $listero['tipo'],
                              'uzo' => 0,
                              'personkosto' => $listero['personkosto']);
                }
        }
    }

    /**
     * kalkulas la personajn kostojn por iu partopreno en iu renkontigxo,
     * kaj krome aldonas gxin al la gxisnunaj internaj sumoj.
     */
    function kalkulu_personkostojn($partoprenanto, $partopreno) {
        $kostosumo = 0;
        $dauxro = $this->renkontigxo->renkontigxonoktoj();
        foreach($this->personaj_kostoj_laux_tipo AS $listero) {
            $tipo = $listero['tipo'];
            // iom pli komplika ...
            if ($tipo->aplikigxas($partoprenanto, $partopreno, $this->renkontigxo)) {
                if ($tipo->datoj['lauxnokte'] == 'j') {
                    $noktoj = $partopreno->partoprennoktoj();
                    $kosto = $listero['personkosto']['kosto_uzata'] * $noktoj;
                    $nokto_de =
                        kalkulu_tagojn($this->renkontigxo->datoj['de'], $partopreno->datoj['de'])
                        + 1;
                    $nokto_gxis =
                        kalkulu_tagojn($this->renkontigxo->datoj['de'], $partopreno->datoj['gxis']);
                    for($i = $nokto_de; $i <= $nokto_gxis; $i++) {
                        $this->personaj_kostoj_laux_tipo[$tipo->datoj['ID']]['noktoj'][$i]['uzo']
                            += 1;
                    }
                    $kostosumo += $kosto;
                }
                else {
                    $kosto = $listero['personkosto'];
                    $this->personaj_kostoj_laux_tipo[$tipo->datoj['ID']]['uzo'] += 1;
                    $kostosumo += $kosto;
                }
            }
        }
        return $kostosumo;
    }



    function finkalkulo($printu=false) {
        if ($printu) {
            // tabelkapo
            echo "<table>\n<tr><td colspan='5' />";
            $dauxro = $this->renkontigxo->renkontigxonoktoj();
            for($i = 1; $i <= $dauxro; $i++) {
                echo "<th>" . $i . "</th>";
            }
            echo "</tr>\n";
        }
        foreach($this->personaj_kostoj_laux_tipo AS $listero) {
            if ($printu) {
                eoecho("<th>" . $listero['tipo']->datoj['nomo'] ."</th>".
                       "<td>" . $listero['personkosto']['kosto_uzata'] . "</td>" .
                       "<td>" . $listero['personkosto']['kosto_neuzata'] . "</td>" .
                       "<td>" . $listero['personkosto']['min_uzenda'] . "</td>" .
                       "<td>" . $listero['personkosto']['max_haveblaj'] . "</td>");
            }
            if ($listero['tipo']->datoj['lauxnokte'] == 'j') {
                foreach($listero['noktoj'] AS $num => $nokto) {
                    $rez = finkalkulu_eron($listero['tipo'], $listero['personkostoj'],
                                           $nokto['uzo'], $printu);
                    $listero[$num] = array_merge($listero[$num], $rez);
                }
            }
            else {
                    $rez = finkalkulu_eron($listero['tipo'], $listero['personkostoj'],
                                           $listero['uzo'], $printu, $dauxro);
                    $this->personaj_kostoj_laux_tipo[$listero['tipo']->datoj['ID']]
                        = array_merge($listero, $rez);

            }
        }
        
    }

    /**
     *
     */
    function finkalkulu_eron($tipo, $personkostoj, $uzo, $printu, $kolumnoj=-1) {
        $kalkula_uzo = $uzo;
        $rez = array('tro' => false,
                     'maltro' => false,
                     'maltrouz_kostoj' => 0);
        $stilo = false;

        if ($uzo < $personkostoj['min_uzendaj']) {
            $rez['maltro'] = true;
            $kalkula_uzo = $personkostoj['min_uzendaj'];
            $rez['maltrouzkostoj'] =
                ($kalkula_uzo - $uzo) *
                ($personkostoj['kosto_uzata'] - $personkosto['kosto_neuzata']);
            $stilo = 'maltro';
        }
        else if ($personkostoj['max_haveblaj'] < $uzo) {
            $rez['tro'] = true;
            $stilo = $tro;
        }
        $kalkula_ne_uzo = $personkostoj['max_haveblaj'] - $kalkula_uzo;

        // TODO: pliajn partojn de kostoj kalkuli
        
        $rez['kostoj'] =
            $kalkula_uzo * $personkostoj['kosto_uzata'] +
            $kalkula_uzo * $personkostoj['kosto_neuzata'];

        if ($printu) {
            echo "<td ";
            if ($kolumnoj >= 1) {
                echo "colspan='" . $kolumnoj . "' ";
            }
            if ($stilo) {
                echo "class='$stilo' ";
            }
            eoecho(">" . $rez['kostoj'] . "</td>");
        }

        return $rez;
    }
    


}



