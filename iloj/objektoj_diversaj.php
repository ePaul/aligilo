<?php

  /**
   * diversaj (malgrandaj) objektoj.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */



/**
 * Noto - tabelo "notoj".
 *
 * - ID              - identifikilo por cxiu noto
 * - partoprenantoID - la partoprenanto, al kiu rilatas la noto
 * - kiu             - kiu skribis la noto (simpla teksto)
 * - kunKiu          - komunikpartnero (al kiu aux de kiu oni ricevis la informojn)
 * - tipo            - tipo de la noto: 
 *                     -  telefon
 *                     -  persone
 *                     -  letere
 *                     -  rete
 *                     -  rimarko
 * - dato            - dato de kreo de la noto (sed tamen sxangxebla)
 * - subjekto        - temo/titolo de la noto
 * - enhavo          - libera teksto
 * - prilaborata     - aux '' (ne prilaborata) aux 'j' (prilaborata)
 * - revido          - revidu la noton ekde tiu dato.
 *
 * @author Martin Sawitzki, Paul Ebermann 
 * @version $Id$
 * 
 */
class Noto extends Objekto
{

    /**
     * konstruilo
     */
    function Noto($id=0)
    {
        $this->Objekto($id,"notoj");
    }

    function listu_entajpantojn_por_noto() {
        $sql = datumbazdemando('entajpantoID',
                               'notoj_por_entajpantoj',
                               array("notoID = '" . $this->datoj['ID'] . "'"));
        $listo = array();
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            $listo[]= $linio['entajpantoID'];
        }
        return $listo;
    }


    /**
     * @param array $noto_por listo de la formo id => JES/NE.
     */
    function sxangxu_entajpantojn_por_noto($noto_por) {
        $jam_estas = $this->listu_entajpantojn_por_noto();
        foreach($noto_por AS $id => $estu) {
            $estas = in_array($id, $jam_estas);
            echo "<!-- id: " . $id . ", estu: " . $estu . ", estas: " . $jam_estas . "\n -->";
            if ($estu == 'JES' and !$estas) {
                // aldonu, la noto nun estas (ankaux) por li
                aldonu_al_datumbazo('notoj_por_entajpantoj',
                                    array('notoID' => $this->datoj['ID'],
                                          'entajpantoID' => $id));
            }
            else if ($estu == 'NE' and $estas) {
                // forigu, la noto ne plu estas por li
                forigu_el_datumbazo('notoj_por_entajpantoj',
                                    array('notoID' => $this->datoj['ID'],
                                          'entajpantoID' => $id));
            }
            else {
                // jam estas en ordo, nenio farenda.
            }
        }
    }


    /**
     * donas liston de cxiuj entajpantoj, kun nomo kaj la informo,
     * cxu la aktuala noto estas por ili aux ne.
     * @return array array de la formo
     *           entajpantoID => array(nomo, noto_por_li)
     *           Tie noto_por_li estas true, se tiu cxi noto estas por
     *           li, alikaze false.
     */
    function listu_entajpantojn() {
        $niaj_entajpantoj = $this->listu_entajpantojn_por_noto();
        $sql = datumbazdemando(array('ID', 'nomo'),
                               'entajpantoj');
        $listo = array();
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            $listo[$linio['ID']] = array($linio['nomo'],
                                         in_array($linio['ID'],
                                                  $niaj_entajpantoj));
        }
        return $listo;
    }





}  // class Noto

/**
 * Pagoj de la unuopaj partoprenantoj/partoprenoj -
 * kaj antauxpagoj kaj surlokaj pagoj (inkluzive
 * pseuxdopagoj kiel donacoj).
 * Tabelo "pagoj".
 *
 * ID
 * partoprenoID
 * kvanto       (kiom da)
 * dato
 * tipo
 */
class Pago extends Objekto
{
    /* konstruilo */
    function Pago($id=0)
    {
        $this->Objekto($id,"pagoj");
    }
}


/**
 * uzantoj de la administrilo.
 *
 * <pre>
 *   ID
 *   nomo
 *   kodvorto
 *   sendanto_nomo
 *   retposxtadreso
 *   partoprenanto_id   - identigilo de la sama homo kiel partoprenanto
 *   
 *   + diversaj rajto-kolumnoj.
 * </pre>
 */
class Entajpanto extends Objekto
{
    /**
     * konstruilo.
     *
     */
    function Entajpanto($id = 0)
    {
        $this->Objekto($id, "entajpantoj");
    }


}


/**
 * (TODO: traduku:) Kassenführung ...
 * tabelo "monujo".
 * TODO: rigardu, kie/kiam gxi estas uzata. Cxu ni uzu gxin
 *   pli gxenerale (kun pluraj kasoj)?
 *
 * --> sxajne gxis nun uzata nur por pagoj dum la akceptado
 *     (kaj nun ecx tio ne plu).
 *
 *-----
 * ID
 * renkontigxo
 * kvanto
 * kauxzo
 * tempo   (kiam)
 * kvitanconumero
 * alKiu
 * kiaMonujo
 */
class Monujo extends Objekto
{

    function Monujo($id=0)
    {
        $this->Objekto($id,"monujo");
    }
}




/**
 * (TODO: traduku:)
 * Rabatte der einzelnen Teilnehmer (pro Teilnahme)
 * - mit Grund (KKRen/distra/tema/nokta/alia),
 *  ID der Teilnahme, Betrag
 * tabelo "rabatoj".
 */
class Rabato extends Objekto
{

    /* konstruilo */
    function Rabato($id=0)
    {
        $this->Objekto($id,"rabatoj");
    }
}

/**
 * Ecoj de la cxambro (tabelo "cxambroj")
 * - parte fiksitaj (unufoje entajpendaj
 *   antaux la renkontigxo, el datoj
 *   de la junulargastejo)
 *    - ID
 *    - renkontigxo
 *    - nomo
 *    - etagxo
 *    - litonombro
 * - parte sxangxeblaj dum la administrado/cxambrodisdono:
 *    - tipo (i/v/g)
 *    - dulita (J/N)
 *    - rimarkoj (iu teksto)
 */
class Cxambro extends Objekto
{

    /* konstruilo */
    function Cxambro($id=0)
    {
        $this->Objekto($id,"cxambroj");
    }
}


/**
 * Deziroj de kunlogxado
 *
 * ID           - interna identifikilo
 * partoprenoID - ID de la partopreno de tiu ulo, kiu deziras kunlogxi
 * alKiuID      - ID de la partopreno de tiu ulo, kiu estas dezirata por kunlogxado
 * stato      - cxu eblas, ne eblas, ...
 */
class Kunlogxdeziro extends Objekto
{

    function Kunlogxdeziro($id = 0)
    {
        $this->Objekto($id, "kunlogxdeziroj");
    }

    function stato()
    {
        return kunlogx_stato($this->datoj['stato']);
    }

}


/**
 * specialaj nomsxildoj (por nepartoprenantoj)
 *
 ****** 
 CREATE TABLE `is_nomsxildoj` (
 `ID` INT NOT NULL AUTO_INCREMENT ,
 `titolo_lokalingve` VARCHAR( 15 ) NOT NULL ,
 `titolo_esperante` VARCHAR( 15 ) NOT NULL ,
 `nomo` VARCHAR( 20 ) NOT NULL ,
 `funkcio_lokalingve` VARCHAR( 30 ) NOT NULL ,
 `funkcio_esperante` VARCHAR( 30 ) NOT NULL ,
 PRIMARY KEY ( `ID` ) 
 ) TYPE = MYISAM COMMENT = 'por specialaj nomsxildoj (por nepartopenantoj)';
 ******
 *
 */
class Speciala_Nomsxildo extends Objekto
{

  function Speciala_Nomsxildo($id = 0)
  {
	$this->Objekto($id, "nomsxildoj");
  }

}


?>
