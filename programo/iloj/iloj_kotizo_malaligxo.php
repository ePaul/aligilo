<?php

  /**
   * Kiel trakti malaligxintojn (laux kotizo).
   */



  /**
   * sistemo de malaligxkondicxoj
   * 
   * - ID
   * - nomo
   * - priskribo
   * - aligxkategorisistemo
   *
   * unuopa malaligxkondicxo, por unu (mal)aligxkategorio
   *  en unu malaligxkondicxsistemo.
   *
   * - sistemo        (ID) | (kune sxlosilo)
   * - aligxkategorio (ID) |
   * - kondicxotipo   (ID) 
   *
   */
class Malaligxkondicxsistemo extends Objekto {

    function Malaligxkondicxsistemo($id =0) {
        $this->Objekto($id, "malaligxkondicxsistemoj");
    }


    /**
     * trovas kaj redonas la kondicxo-objekton por tiu cxi
     *  malaligxkondicxosistemo kaj la menciita aligxkategori-objekto.
     */
    function donu_kondicxon($aligxkategorio) {
        if (is_object($aligxkategorio)) {
            $aligxkategorio = $aligxkategorio->datoj['ID'];
        }
        
        $sql = datumbazdemando("kondicxtipo",
                               "malaligxkondicxoj",
                               array("sistemo = '" . $this->datoj['ID'] . "'",
                                     "aligxkategorio = '"
                                     . $aligxkategorio . "'"));
        $linio = mysql_fetch_assoc(sql_faru($sql));
        if (!$linio) {
            return null;
        }
        return new Malaligxkondicxotipo($linio['kondicxtipo']);
    }

    function donu_aligxkategorisistemon() {
        // TODO: pripensu, cxu stori rezulton
        return new Aligxkategorisistemo($this->datoj['aligxkategorisistemo']);
    }

    function montru_tabeleron() {
        eoecho("<tr><td>" . $this->datoj['ID'] . "</td><td>");
        ligu("malaligxkondicxsistemo?id=". $this->datoj['ID'],
             $this->datoj['nomo']);
        $aligxkatsistemo =
            donu_katsistemon($this->datoj['aligxkategorisistemo'], "aligx");
        eoecho("</td><td>". $aligxkatsistemo->datoj['nomo'] . "</td></tr>\n");;
    }


}


  /**
   * tipo de Malaligxtraktado.
   *
   * malaligxtipoj:
   *  - ID
   *  - nomo
   *  - mallongigo
   *  - priskribo
   *  - funkcio
   *  - parametro - opcia: iu parametra nombro (DECIMAL(6,2))
   *  - uzebla  - j (estos montrata en la listo por elekti)
   *            - n (nur montrata por teknikistoj, por redakti gxin)
   */
class Malaligxkondicxotipo extends Objekto {

    function Malaligxkondicxotipo($id=0) {
        $this->Objekto($id, "malaligxkondicxotipoj");
    }

    function korektu_kopiitajn() {
        // se la parametro estas malplena cxeno, prenu NULL anstatauxe.
        // (alikaze gxi estos konvertita de Mysql al 0.00)
        if ($this->datoj['parametro'] === '') {
            $this->datoj['parametro'] = null;
        }
    }


    function traktu( $partoprenanto, $partopreno, $renkontigxo,
                     $kotizokalkulilo) {
        $funk = "malaligxkotizo_" .($this->datoj['funkcio']);
        //        echo "<!-- traktu: funk = " . $funk . "-->";
        return $funk($partoprenanto, $partopreno, $renkontigxo,
                     $kotizokalkulilo, $this->datoj['parametro']);
    }

    function montru_tabeleron() {
        eoecho("<tr><td>" . $this->datoj['ID'] . "</td><td>");
        ligu("malaligxkondicxotipo.php?id=". $this->datoj['ID'],
             $this->datoj['nomo']);
        eoecho("</td><td>". $this->datoj['mallongigo'] ."</td><td>".
               $this->datoj['funkcio'] . "</td><td>" .
               $this->datoj['parametro'] ."</td><td>" .
               $this->datoj['uzebla'] . "</td></tr>\n");;
    }



}




?>