<?php

  /*
   * Iloj rilate al sesio-variabloj.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */




  /**
   * zorgas, ke $_SESSION['partoprenanto'] kongruas al $ppantoID.
   *
   * @param int $ppantoID (identigilo de partoprenanto)
   * @param int $ppenoID  identigilo de partopreno. Se 0, ni provas
   *                      mem serĉi la tauĝan partoprenon.
   * @uses sesio_trovu_ppenon()
   */
function sesio_aktualigu_ppanton($ppantoID, $ppenoID = 0)
{
    debug_echo( "<!-- aktualigu_ppanton(" . $ppantoID . ", " . $ppenoID . ") -->");

    if (!$ppantoID) {
        // nenio farebla.
        return;
    }

    if (!$_SESSION['partoprenanto'] or
        $_SESSION['partoprenanto']->datoj['ID'] != $ppantoID)
        {
            $_SESSION['partoprenanto'] =
                new Partoprenanto($ppantoID);
        }

    sesio_trovu_ppenon($ppantoID, $ppenoID);
}  // sesio_aktualigu_ppanton


/**
 * serĉas partoprenon por partoprenanto, kaj metas ĝin
 * en la sesion.
 * @param int $ppantoID Partoprenanto-identigilo.
 * @param int $ppenoID Partopreno-identigilo. Se ne 0,
 *           ni simple vokas {@link sesio_aktualigu_ppenon()}.
 *           Alikaze ni serĉas taŭgan partoprenon por $ppantoID,
 *           kaj metas tiun en la sesion.
 * @uses sesio_aktualigu_ppenon()
 * @return int se ni sukcesis, 1, alikaze la nombron de diversaj
 *             kandidatoj.
 */
function sesio_trovu_ppenon($ppantoID, $ppenoID=0) {
    debug_echo ("<!-- trovu_ppenon(" . $ppantoID . ", " . $ppenoID . ") -->");


    if ($ppenoID) {
        sesio_aktualigu_ppenon($ppenoID);
        return 1;
    }
    else {
        if ($_SESSION['partopreno'] and
            $_SESSION['partopreno']->datoj['partoprenantoID'] == $ppantoID)
            {
                // ni havas jam unu partoprenon, kaj ĝi estas
                //  por la ĝusta partoprenanto.
                sesio_aktualigu_ppenon($_SESSION['partopreno']->datoj['ID']);
                return 1;
            }
        // malfacila kazo

        $sql = datumbazdemando("ID",
                               "partoprenoj",
                               array("partoprenantoID = '".$ppantoID."'"),
                               array("renkontigxo" => "renkontigxoID"));
        $rez = sql_faru($sql);
        $num_pp = mysql_num_rows($rez);
        if ($num_pp == 1)
            {
                // precize unu partopreno por la aktuala
                // renkontiĝo - prenu tiun.
                $linio = mysql_fetch_assoc($rez);
                sesio_aktualigu_ppenon($linio['ID']);
            }
        else
            {
                // ne estas ununura taŭga partopreno por tiu ĉi
                // partoprenanto.
                unset($_SESSION['partopreno']);
            }
        // eble la vokanto volas scii, kiom da aliĝoj estis.
        return $num_pp;
    }
}   // sesio_trovu_ppenon


/**
 * zorgas, ke $_SESSION['partopreno'] estu tiu kun $ppenoID.
 *
 * @param int $ppenoID la partopreno-identigilo.
 * @uses sesio_aktualigu_ppanton()
 */
function sesio_aktualigu_ppenon($ppenoID)
{
    debug_echo( "<!-- aktualigu_ppenon(" . $ppenoID . ") -->");

    if (!$_SESSION['partopreno'] or
        $_SESSION['partopreno']->datoj['ID'] != $ppenoID)
        {
            $_SESSION['partopreno'] = new Partopreno($ppenoID);
        }
    if ($_SESSION['partoprenanto']->datoj['ID'] != 
        $_SESSION['partopreno'] ->datoj['partoprenantoID'])
        {
            sesio_aktualigu_ppanton($_SESSION['partopreno']
                                    ->datoj['partoprenantoID'],
                                    $ppenoID);
        }


}

/**
 * ni difinas $partopreno_renkontigxo por uzi anstataŭ
 * $_SESSION['renkontigxo'], ĉar ĝi ja povus esti io alia
 * (se oni rigardas malnovan partoprenon, ekzemple).
 */
function globaligu_pprenk()
{
    if ($_SESSION['partopreno'] and
        $_SESSION['partopreno']->datoj['renkontigxoID']
        != $_SESSION['renkontigxo']->datoj['ID'])
        {
            $GLOBALS['partopreno_renkontigxo'] =
                new Renkontigxo($_SESSION['partopreno']->datoj['renkontigxoID']);
            if(DEBUG) {
                echo "<!-- nova renkontigxo-objekto -->";
            }
        }
    else
        {
            $GLOBALS['partopreno_renkontigxo'] = $_SESSION['renkontigxo'];
        }
}



  /**
   * zorgas, ke $_SESSION['partopreno'] kaj
   *            $_SESSION['partoprenanto']
   * kongruu al $_REQUEST['partoprenantoidento'] kaj
   *            $_REQUEST['partoprenidento'].
   *
   * @uses sesio_aktualigu_ppanton()
   * @uses sesio_aktualigu_ppenon()
   */
function sesio_aktualigu_laux_get() {

    if ($_REQUEST['partoprenantoidento'])
        {
            sesio_aktualigu_ppanton($_REQUEST['partoprenantoidento'],
                                    $_REQUEST['partoprenidento']);
        }
    else if ($_REQUEST['partoprenidento'])
        {
            sesio_aktualigu_ppenon($_REQUEST['partoprenidento']);
        }

    globaligu_pprenk();

}





?>