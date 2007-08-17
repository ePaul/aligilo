<?php

  /*
   * La tabelnomoj cxi tie cxiam estas
   * la abstraktaj tabelnomoj. La traduko
   * al la konkretaj nomoj okazas en
   * iloj_sql.
   */


  /**
   * Ecoj de renkontigxo (tabelo "renkontigxo")
   * -------------------------------------------
   * Gxenerale
   *  - ID
   *       interna identifikilo
   *  - nomo
   *       oficiala nomo (ekz-e "45 a Internacia Seminario")
   *  - mallongigo
   *      interna mallongigo, gxis nun
   *      uzata nur por la partoprenanto-listo
   *      (ekzemple "IS 2003")
   *  - temo
   *  - loko
   * -----------------------------------
   * Por kotizokalkulo
   *  - de
   *      alventago
   *  - gxis
   *      forirtago
   *  - plej_frue
   *      fino de unua aligxperiodo (ekz-e 2003-10-01)
   *  - meze
   *      fino de dua aligxperiodo (ekz-e 2003-12-01)
   *  - parttemppartoprendivido
   *      Se partoprenanto partoprenas nur parttempe, li
   *      pagas laux la formulo "tagoj/divido * normala kotizo"
   *      (ekz-e 6)
   *  - juna
   *      la limagxo por junuloj - se ies agxo estas <=,
   *      li estas en la plej malmultekosta kategorio.
   *      (ekz-e 20)
   *  - maljuna
   *     la limagxo por maljunuloj - se ies agxo estas >,
   *     li estas en la plej alta kategorio. (La krompago
   *     por >= 40 ankoraux ne enestas.)
   * -----------------------------------
   * respond(ec)uloj
   *      ili ricevas retmesagxojn, kiam iu aligxas
   *      kiu povas kontribui al la programo, bezonas
   *      invitleteron ktp.
   *      ...respond(ec)ulo estas la nomo, ...retadreso
   *      estas la retadreso de la ulo.
   *      La adminrespondeculo ricevas retmesagxon pri cxiu
   *      nova aligxinto.
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
   * Atentu: la nomojn de tiuj datumbazkampoj uzas la
   * funkcioj "funkciulo" kaj "funkciuladreso" (kaj
   * ties uzantoj) (sube).
   */

class Renkontigxo extends Objekto
{
  
    /* konstruilo */
    function Renkontigxo($id)
    {
        //$this->datoj = mysql_fetch_assoc(sql_faru("Select * from renkontigxo where ID=$id"));
        $this->Objekto($id,"renkontigxo");
    }
  
}

/*
 * Elekto de la renkontigxo.
 *
 * Se oni elektis renkontigxon per
 * la elektilo (= estas io en $_REQUEST["formrenkontigxo"]),
 * ni uzas tiun.
 * Alikaze, se en la $_SESSION["renkontigxo"]
 * estas ankoraux renkontigxo, ni elektas
 * tiun.
 * Alikaze, ni elektas la defauxltan
 * renkontigxon (-> DEFAUXLTA_RENKONTIGXO)
 *
 * La funkcio redonas la renkontigxo-objekton.
 */
function kreuRenkontigxon()
{
    if ($_REQUEST["formrenkontigxo"])
        {
            if (is_array($_REQUEST["formrenkontigxo"]))
                {
                    if (DEBUG) echo "<!-- renkontigxo el formrenkontigxo=" . $_REQUEST["formrenkontigxo"][0] . " -->";
                    $renkontigxo = new Renkontigxo($_REQUEST["formrenkontigxo"][0]);
                }
            else
                {
                    if (DEBUG) echo "<!-- renkontigxo el formrenkontigxo=" . $_REQUEST["formrenkontigxo"] . " -->";
                    $renkontigxo = new Renkontigxo($_REQUEST["formrenkontigxo"]);
                }
        }
    else if ($_SESSION["renkontigxo"])
        {
            if (DEBUG) echo "<!-- renkontigxo el sesio -->";
            $renkontigxo = $_SESSION["renkontigxo"];
        }
    else
        {
            if (DEBUG) echo "<!-- defauxlta renkontigxo! -->";
            $renkontigxo = new Renkontigxo(DEFAUXLTA_RENKONTIGXO);
        }
    return $renkontigxo;
}



/**
 * donas retadreson de funkciulo pri ... de la aktuala renkontigxo.
 */
function funkciuladreso($funkcio)
{
    return $_SESSION["renkontigxo"]->datoj[$funkcio . "retadreso"];
}

/**
 * Redonas la nomon de la respondeculo pri iu funkcio.
 */
function funkciulo($funkcio)
{
    $datoj = $_SESSION["renkontigxo"]->datoj;
    if (array_key_exists($funkcio . "respondulo", $datoj))
        {
            return $datoj[$funkcio . "respondulo"];
        }
    else
        {
            return $datoj[$funkcio . "respondeculo"];
        }
}



?>
