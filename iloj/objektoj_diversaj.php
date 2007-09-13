<?php

  /**
   * diversaj (malgrandaj) objektoj.
   */



/**
 * Noto - tabelo "notoj".
 *
 * ID              - identifikilo por cxiu noto
 * partoprenantoID - la partoprenanto, al kiu rilatas la noto
 * kiu             - kiu skribis la noto (simpla teksto)
 * kunKiu          - komunikpartnero (al kiu aux de kiu oni ricevis la informojn)
 * tipo            - tipo de la noto: 
 *                       telefon
 *                       persone
 *                       letere
 *                       rete
 *                       rimarko
 * dato            - dato de kreo de la noto (sed tamen sxangxebla)
 * subjekto        - temo/titolo de la noto
 * enhavo          - libera teksto
 * prilaborata     - aux '' (ne prilaborata) aux 'j' (prilaborata)
 * revido          - revidu la noton ekde tiu dato.
 */
class Noto extends Objekto
{

    /* konstruilo */
    function Noto($id=0)
    {
        $this->Objekto($id,"notoj");
    }
}

/**
 * (TODO: traduku:) Zahlungen der einzelnen Teilnehmer.
 * Tabelo "pagoj".
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
 * (TODO: traduku:) Kassenführung ...
 * tabelo "monujo".
 * TODO: rigardu, kie/kiam gxi estas uzata. Cxu ni uzu gxin
 *   pli gxenerale (kun pluraj kasoj)?
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
