<?php

  /**
   * Objektoj por rilati al datumbazo.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */




  /**
   * klaso por samtempe legi el du (ordigitaj) SQL-rezultoj
   * kaj kunigi la rezultojn.
   */
class SQLMergxilo {

    var $dekstra_rez;
    var $maldekstra_rez;

    var $dekstraj_linioj = array();
    var $maldekstraj_linioj = array();

    var $fino;

    function SQLMergxilo() {
    }


    function maldekstra_sql($sql){
        $this->maldekstra_rez = sql_faru($sql);
    }

    function dekstra_sql($sql){
        $this->dekstra_rez = sql_faru($sql);
    }
    

    // por uzo en subklasoj

    function legu_dekstran() {
        if (count($this->dekstraj_linioj)) {
            return array_pop($this->dekstraj_linioj);
        }
        return mysql_fetch_assoc($this->dekstra_rez);
    }

    function legu_maldekstran() {
        if (count($this->maldekstraj_linioj)) {
            return array_pop($this->maldekstraj_linioj);
        }
        return mysql_fetch_assoc($this->maldekstra_rez);
    }

    function remetu_dekstran($valoro) {
        array_push($this->dekstraj_linioj, $valoro);
    }

    function remetu_maldekstran() {
        array_push($this->maldekstraj_linioj, $valoro);
    }



    /**
     * @return array
     */
    function sekva() {
        darf_nicht_sein("tiu funkcio estu anstatauxigota en subklaso");
    }



}

/**
 */
class SQL_Outer_left_join extends SQLMergxilo {

    var $komparkampo;

    function SQL_Outer_left_join($komparkampo) {
        $this->komparkampo = $komparkampo;
    }


    function sekva() {

        $maldekstra = $this->legu_maldekstran();
        if (!$maldekstra)
            return null;

        while (true) {
            $dekstra = $this->legu_dekstran();

            if (DEBUG) {
                echo "<!-- legita: (" . var_export($maldekstra, true) . ", " .
                    var_export($dekstra, true) . ") -->";
            }

            if (!$dekstra) {
                echo "<!-- mankas dekstra -->";
                return $maldekstra;
            }
            
            $mdID = ($maldekstra[$this->komparkampo]);
            $dID  = (   $dekstra[$this->komparkampo]);
           
            if ($mdID < $dID) {
                echo "<!-- remetos dekstran -->";
                $this->remetu_dekstran($dekstra);
                return $maldekstra;
            } else if ($mdID == $dID) {
                echo "<!-- kunigas ambaux -->";
                return array_merge($maldekstra, $dekstra);
            } else {
                echo "<!-- forjxetas dekstran -->";
                // forjxetu la dekstran, prenu novan
                continue;
            }
        }

    }


}




?>