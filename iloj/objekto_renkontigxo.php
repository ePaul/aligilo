<?php


  /**
   * La renkontiĝo-objekto kaj rilataj funkcioj.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2010 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /*
   * La tabelnomoj ĉi tie ĉiam estas
   * la abstraktaj tabelnomoj. La traduko
   * al la konkretaj nomoj okazas en
   * iloj_sql.
   */


  /**
   * Ecoj de renkontiĝo (tabelo "renkontigxo")
   * -------------------------------------------
   * Ĝenerale
   *  - ID
   *       interna identifikilo
   *  - nomo
   *       oficiala nomo (ekz-e "45 a Internacia Seminario")
   *  - mallongigo
   *      interna mallongigo, ĝis nun
   *      uzata nur por la partoprenanto-listo
   *      (ekzemple "IS 2003")
   *  - temo
   *  - loko
   *  - de
   *      alventago
   *  - gxis
   *      forirtago
   * -----------------------------------
   * Por kotizokalkulo (nun ne plu estas uzataj)
   *  - plej_frue
   *      fino de unua aliĝperiodo (ekz-e 2003-10-01)
   *  - meze
   *      fino de dua aliĝperiodo (ekz-e 2003-12-01)
   *  - parttemppartoprendivido
   *      Se partoprenanto partoprenas nur parttempe, li
   *      pagas laŭ la formulo "tagoj/divido * normala kotizo"
   *      (ekz-e 6)
   *  - juna
   *      la limaĝo por junuloj - se ies aĝo estas <=,
   *      li estas en la plej malmultekosta kategorio.
   *      (ekz-e 20)
   *  - maljuna
   *     la limaĝo por maljunuloj - se ies aĝo estas >,
   *     li estas en la plej alta kategorio. (La krompago
   *     por >= 40 ankoraŭ ne enestas.)
   *
   *  --> ne plu uzataj en la nova kotizokalkulilo. TODO: forigu
   * -----------------------------------
   * respond(ec)uloj
   *      ili ricevas retmesaĝojn, kiam iu aliĝas
   *      kiu povas kontribui al la programo, bezonas
   *      invitleteron ktp.
   *      ...respond(ec)ulo estas la nomo, ...retadreso
   *      estas la retadreso de la ulo.
   *      La adminrespondeculo ricevas retmesaĝon pri ĉiu
   *      nova aliĝinto.
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
   *   --> TODO: ŝovu eble al aparta tabelo.
   *
   * Atentu: la nomojn de tiuj datumbazkampoj uzas la
   * funkcioj "funkciulo" kaj "funkciuladreso" (kaj
   * ties uzantoj) (sube).
   *
   */

class Renkontigxo extends Objekto
{
  
    /* konstruilo */
    function Renkontigxo($id)
    {
        //$this->datoj = mysql_fetch_assoc(sql_faru("Select * from renkontigxo where ID=$id"));
        $this->Objekto($id,"renkontigxo");
    }

    /**
     * donas retadreson de funkciulo pri ... de tiu renkontiĝo.
     *
     */
    function funkciuladreso($funkcio)
    {
        return $this->datoj[$funkcio . "retadreso"];
    }

    /**
     * Redonas la nomon de la respondeculo pri iu funkcio.
     */
    function funkciulo($funkcio)
    {
        $datoj = $this->datoj;
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
     * kalkulas, kiom da noktoj tiu renkontiĝo daŭras.
     *
     * @return int
     */
    function renkontigxonoktoj() {
        return kalkulu_tagojn($this->datoj['de'], $this->datoj['gxis']);
    }

    /**
     * redonas la kotizosistemo-objekton, kiu apartenas
     * al tiu ĉi renkontiĝo.
     *
     * @return Kotizosistemo
     */
    function donu_kotizosistemon() {
        // TODO: cache
        return new Kotizosistemo($this->datoj['kotizosistemo']);
    }

  
}

/**
 * Elekto de la renkontiĝo.
 *
 * Se oni elektis renkontiĝon per
 * la elektilo (= estas io en $_REQUEST["formrenkontigxo"]),
 * ni uzas tiun.
 * Alikaze, se en la $_SESSION["renkontigxo"]
 * estas ankoraŭ renkontiĝo, ni elektas
 * tiun.
 * Alikaze, ni elektas la defaŭltan
 * renkontiĝon (-> DEFAUXLTA_RENKONTIGXO)
 *
 * La funkcio redonas la renkontiĝo-objekton.
 *
 * @param Renkontigxo|int $renkontigxo Se ĝi estas Renkontiĝo-objekto,
 *                        ni simple redonas ĝin.
 *                       Se ĝi estas ID de tia, ni kreas objekton kaj
 *                       redonas tiun. Alikaze estos pli komplika serĉo
 *                       priskribita supre.
 * @return Renkontigxo
 */
function kreuRenkontigxon($renkontigxo=0)
{
    if (is_object($renkontigxo)) {
        debug_echo("<!-- renkontigxo el parametro -->");
        return $renkontigxo;
    }
    else if (is_numeric($renkontigxo) and 0 < (int)$renkontigxo) {
        $renkNum = (int)$renkontigxo;
        debug_echo("<!-- renkontigxo el parametro: " . $renkNum. " -->");
    }
    else if (isset($_REQUEST["formrenkontigxo"]))
        {
            if (is_array($_REQUEST["formrenkontigxo"]))
                {
                    $renkNum = (int)($_REQUEST["formrenkontigxo"][0]);
                }
            else
                {
                    $renkNum = (int)($_REQUEST["formrenkontigxo"]);
                }
            debug_echo( "<!-- renkontigxo el formrenkontigxo = "
                        . $renkNum . " -->");
        }
    else if (is_object($_SESSION["renkontigxo"]))
        {
            debug_echo( "<!-- renkontigxo el sesio -->");
            return  $_SESSION["renkontigxo"];
        }
    else if (is_object($GLOBALS['renkontigxo']))
        {
            debug_echo("<!-- renkontigxo el globala variablo -->");
            return $GLOBALS['renkontigxo'];
        }
    else
        {
            debug_echo("<!-- defauxlta renkontigxo! -->");
            $renkNum = DEFAUXLTA_RENKONTIGXO;
        }

    // se ni venis gxis cxi tie, ni devas krei renkontigxon el tiu
    // $renkNum.

    return new Renkontigxo($renkNum);
}



/**
 * donas retadreson de funkciulo pri ... de la aktuala renkontigxo.
 */
function funkciuladreso($funkcio)
{
    return $_SESSION["renkontigxo"]->funkciuladreso($funkcio);
}

/**
 * Redonas la nomon de la respondeculo pri iu funkcio.
 */
function funkciulo($funkcio)
{
    return $_SESSION["renkontigxo"]->funkciulo($funkcio);
}



?>
