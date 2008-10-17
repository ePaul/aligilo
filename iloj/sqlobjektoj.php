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
   *
   * La kunigado mem estas farata en subklasoj.
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   */
class SQLMergxilo {

    var $dekstra_rez;
    var $maldekstra_rez;

    var $dekstraj_linioj = array();
    var $maldekstraj_linioj = array();

    var $fino;


    /**
     * konstruilo.
     */
    function SQLMergxilo() {
    }


    /**
     * konfiguras, kio estu la SQL-demando por la "dekstra"
     * rezulto.
     * @param sqlstring $sql
     */
    function maldekstra_sql($sql){
        $this->maldekstra_rez = sql_faru($sql);
    }

    /**
     * konfiguras, kio estu la SQL-demando por la "maldekstra"
     * rezulto.
     * @param sqlstring $sql
     */
    function dekstra_sql($sql){
        $this->dekstra_rez = sql_faru($sql);
    }
    

    /**
     * Subklasoj devas redifini tiun metodon.
     *
     * @return mixed la sekva rezulto. NULL, se ne estas plia rezulto.
     * @abstract
     */
    function sekva() {
        darf_nicht_sein("tiu funkcio estu anstatauxigota en subklaso");
    }



    // por uzo en subklasoj

    /**
     * donas la sekvan rezulton el la dekstra fluo.
     *
     * (Nur por uzo de subklasoj)
     * @return array
     */
    function legu_dekstran() {
        if (count($this->dekstraj_linioj)) {
            return array_pop($this->dekstraj_linioj);
        }
        return mysql_fetch_assoc($this->dekstra_rez);
    }

    /**
     * donas la sekvan rezulton el la maldekstra fluo.
     *
     * (Nur por uzo de subklasoj)
     * @return array
     */
    function legu_maldekstran() {
        if (count($this->maldekstraj_linioj)) {
            return array_pop($this->maldekstraj_linioj);
        }
        return mysql_fetch_assoc($this->maldekstra_rez);
    }

    /**
     * remetas unu eron al stoko por dekstraj rezultoj.
     * Tiu estos redonita je la sekva voko de {@link remetu_dekstran()}.
     *
     * @param array $valoro
     */
    function remetu_dekstran($valoro) {
        array_push($this->dekstraj_linioj, $valoro);
    }

    /**
     * remetas unu eron al stoko por maldekstraj rezultoj.
     * Tiu estos redonita je la sekva voko de {@link remetu_maldekstran()}.
     *
     * @param array $valoro
     */
    function remetu_maldekstran() {
        array_push($this->maldekstraj_linioj, $valoro);
    }

}

/**
 * kunigilo, kiu uzas ĉiujn rezultojn el la maldekstra fluo,
 * kaj aldonas tiujn el la dekstra, kiuj kongruas al unu dekstra.
 */
class SQL_Outer_left_join extends SQLMergxilo {

    /**
     * @access private
     */
    var $komparkampo;

    /**
     * konstruilo.
     * @param sqlstring $komparkampo nomo de kampo aperanta
     *        en ambaŭ rezultoj, kiun ni uzas por kompari.
     *        La rezultoj estu ordigitaj laŭ tiu kampo.
     */
    function SQL_Outer_left_join($komparkampo) {
        $this->komparkampo = $komparkampo;
    }


    /**
     * redonas la sekvan rezulton
     * @return array|null  aŭ null (kiam ne plu estas rezultoj)
     *    aŭ unu tabellinio el la maldekstra rezulto (kiam ne ekzistas
     *    koresponda tabellinio en la dekstra), aŭ unu kunigita tabellinio
     *    el dekstra kaj maldekstra.
     */
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
                debug_echo( "<!-- mankas dekstra -->");
                return $maldekstra;
            }
            
            $mdID = ($maldekstra[$this->komparkampo]);
            $dID  = (   $dekstra[$this->komparkampo]);
           
            if ($mdID < $dID) {
                debug_echo( "<!-- remetos dekstran -->");
                $this->remetu_dekstran($dekstra);
                return $maldekstra;
            } else if ($mdID == $dID) {
                debug_echo( "<!-- kunigas ambaux -->");
                return array_merge($maldekstra, $dekstra);
            } else {
                debug_echo( "<!-- forjxetas dekstran -->");
                // forĵetu la dekstran, prenu novan
                continue;
            }
        }

    }


}




?>