<?php

  /**
   * diversaj (malgrandaj) objektoj.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */




/**
 * superklaso de cxiuj pago-similaj klasoj.
 *
 * - ID
 * - partoprenoID
 * - kvanto
 * - valuto
 * - tipo  (kialo)
 * - dato
 * - entajpantoID
 *
 */
class PseuxdoPago extends Objekto {

    /**
     * identigilo, pri kiu klaso temas.
     * - krom
     * - pago
     * - rabato
     */
    var $klaso;

}


/**
 * Pagoj de la unuopaj partoprenantoj/partoprenoj -
 * kaj antaŭpagoj kaj surlokaj pagoj.
 *
 * Tabelo "pagoj".
 *
 * - ID
 * - partoprenoID
 * - kvanto       (kiom da)
 * - valuto
 * - dato
 * - tipo
 */
class Pago extends PseuxdoPago
{
    /* konstruilo */
    function Pago($id=0)
    {
        $this->Objekto($id,"pagoj");
        $this->klaso = 'pago';
    }
}


/**
 * Individuaj Rabatoj de unuopaj partoprenoj.
 *
 * - ID
 * - partoprenoID
 * - kvanto
 * - valuto
 * - tipo  (kialo)
 * - dato
 * - entajpantoID
 *
 * tabelo "rabatoj".
 */
class Rabato extends PseuxdoPago
{

    /** konstruilo */
    function Rabato($id=0)
    {
        $this->Objekto($id,"rabatoj");
        $this->klaso = 'rabato';
    }
}

/**
 * individuaj krompagoj de unuopaj partoprenantoj.
 *
 * - ID
 * - partoprenoID
 * - kvanto
 * - valuto
 * - tipo  (kialo)
 * - dato
 * - entajpantoID
 * 
 */
class Individua_Krompago extends PseuxdoPago {
    /**
     * konstruilo
     */
    function Individua_Krompago($id=0) {
        $this->Objekto($id, "individuaj_krompagoj");
        $this->klaso = 'krom';
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
 *   retpoŝtadreso
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
 * TODO: rigardu, kie/kiam ĝi estas uzata. Ĉu ni uzu ĝin
 *   pli ĝenerale (kun pluraj kasoj)?
 *
 * --> ŝajne ĝis nun uzata nur por pagoj dum la akceptado
 *     (kaj nun eĉ tio ne plu).
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
 * Ecoj de la ĉambro (tabelo "cxambroj")
 * - parte fiksitaj (unufoje entajpendaj
 *   antaŭ la renkontiĝo, el datoj
 *   de la junulargastejo)
 *    - ID
 *    - renkontiĝo
 *    - nomo
 *    - etaĝo
 *    - litonombro
 * - parte ŝanĝeblaj dum la administrado/ĉambrodisdono:
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

if(mangxotraktado == "libera") {

    /**
     * Eblaj manĝtempoj, kun siaj tipoj.
     *
     * - ID
     * - renkontigxoID
     * - dato
     * - mangxotipo
     * - komento
     */
    class Mangxtempo  extends Objekto {
        function Mangxtempo($id=0) {
            $this->Objekto($id, "mangxtempoj");
        }
    }

    /**
     * Eblaj mangxtipoj, kun prezoj.
     *
     * - ID
     * - renkontigxoID
     * - mangxtipo (M, T, V)
     * - prezo
     * - valuto
     * - priskribo (tradukebla)
     */
    class Mangxtipo extends Objekto {
        function Mangxtipo($id = 0) {
            $this->Objekto($id, "mangxtipoj");
        }
    }


 }


/**
 * Deziroj de kunloĝado.
 * (Nuntempe ne uzata, la korespondaj programpartoj
 *   estas ankoraŭ sub evoluo.)
 *
 * ID           - interna identifikilo
 * partoprenoID - ID de la partopreno de tiu ulo, kiu deziras kunloĝi
 * alKiuID      - ID de la partopreno de tiu ulo, kiu estas dezirata por kunloĝado
 * stato      - ĉu eblas, ne eblas, ...
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
 * specialaj nomŝildoj (por nepartoprenantoj)
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
 ) TYPE = MYISAM COMMENT = 'por specialaj nomŝildoj (por nepartopenantoj)';
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

/**
 * landoj de partoprenantoj.
 *
 * - ID
 * - nomo     (tradukebla)
 * - kodo
 */
class Lando extends Objekto
{
    function Lando($id = 0)
    {
        $this->Objekto($id, "landoj");
    }
}

/**
 * Renkontigxo-specifaj konfiguroj, kiel eblaj
 *  pagotipoj, valutoj, ktp.
 *
 * - ID
 * - renkontigxoID
 * - tipo   (ekzemple 'pagotipo', 'valuto', 'rabatkialo', ...)
 * - interna (interna identigilo de la opcio)
 * - grupo  (eble identigilo de grupo, por montri la opciojn en iuj listoj
 *           kun spacoj inter la grupoj. grupo-ID nur gravas ene de sama tipo.)
 * - teksto (esperantlingva teksto por tiu opcio - tradukebla)
 * - aldona_komento (komento, kiu nur aperas en la elektiloj ene de
 *                   la administrilo, ne en io ajn publika.)
 */
class Renkontigxa_konfiguro extends Objekto
{
    function Renkontigxa_konfiguro($id = 0)
    {
        $this->Objekto($id, "renkontigxaj_konfiguroj");
    }
}



/**
 * Kurzo de valuto relative al cxefa valuto.
 *
 * - ID
 * - valuto  (ISO 4217-kodo)
 * - dato    (dato, kiam tiu kurzo validis)
 * - kurzo   kiom da unuoj de la cxefa valuto valoris kiel unu
 *           unuo de tiu cxi valuto?
 */
class Kurzo extends Objekto
{
    function Kurzo($id = 0)
    {
        $this->Objekto($id, "kurzoj");
    }
}


?>
