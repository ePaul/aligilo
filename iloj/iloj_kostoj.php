<?php



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
   *  - lauxnokte - j (krompago por cxiu nokto, kiun oni tranoktas)
   *                n (unufoja krompago)
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

    function kreu_kondicxon() {
        $this->kondicxo =
            create_function('$partoprenanto,$partopreno,$renkontigxo',
                            $this->datoj['kondicxo']);
    }

    /**
     * versio de la funkcio el Objekto, por poste rekrei
     * la kondicxo-funkcion.
     */
    function prenu_el_datumbazo($id="") {
        parent::prenu_el_datumbazo($id);
        $this->kreu_kondicxon();
    }



    /**
     * eltrovas, cxu tiu personkostotipo aplikigxas en iu specifa
     * situacio.
     *  -> true: jes, la partoprenanto devos pagi la personkoston
     *  -> false: ne, ...
     */
    function aplikigxas($partoprenanto, $partopreno, $renkontigxo)
    {
        $funk = $this->kondicxo;
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


    var $krompagolisto;

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
        $sql = datumbazdemando(array("tipo", "personkosto"),
                               "personkostoj",
                               "kostosistemo = '{$this->datoj['ID']}'");
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            $listo[] = array('tipo' => new Personkostotipo($linio['tipo']),
                             'personkosto' => $linio['personkosto']);
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




?>