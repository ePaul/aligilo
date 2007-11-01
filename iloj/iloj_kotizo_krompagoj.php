<?php



  /**
   * tipo de krompago
   *
   * krompagotipoj:
   *  - ID
   *  - nomo
   *  - priskribo
   *  - kondicxo - kodo de anonima funkcio, vokota per la parametroj
   *                $partoprenanto, $partopreno, $renkontigxo (po la objekto)
   *              gxi redonu true aux false.
   *          TODO: !! Tio estas iometa sekureca risko. Atentu, ke   !!!
   *                !!   nur teknikistoj povos sxangxi tiun tekston. !!!
   *  - uzebla  - 1 (estos montrata en listoj por elekti)
   *            - 0 (nur montrata por teknikistoj, por redakti gxin)
   *
   * krompagoj:
   *   - kotizosistemo   (ID)
   *   - krompagotipo    (ID)
   *   - krompago        nombro
   */
class Krompagotipo extends Objekto {

    var $kondicxo;

    function Krompagotipo($id=0) {
        $this->Objekto($id, "krompagotipoj");
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
     * eltrovas, cxu tiu krompagotipo aplikigxas en iu specifa
     * situacio.
     *  -> true: jes, la partoprenanto devos pagi la krompagon
     *  -> false: ne, ...
     */
    function aplikigxas($partoprenanto, $partopreno, $renkontigxo)
    {
        $funk = $this->kondicxo;
        return
            $funk($partoprenanto, $partopreno, $renkontigxo);
    }

}

function listu_cxiujn_krompagotipojn() {
    $rezulto = array();

    $sql = datumbazdemando("ID",
                           "krompagotipoj",
                           "uzebla");
    $rez = sql_faru($sql);
    while($linio = mysql_fetch_assoc($rez)) {
        $rezulto[]= new Krompagotipo($linio['ID']);
    }
    
    return $rezulto;
}




?>