<?php

  /*
   * Iloj rilate al sesio-variabloj.
   *
   */



  /**
   * zorgas, ke _SESSION['partopreno'] kaj
   *            _SESSION['partoprenanto']
   * kongruu al _REQUEST['partoprenantoidento'] kaj
   *            _REQUEST['partoprenidento'].
   */
function sesio_aktualigo_laux_get() {
    if ($_REQUEST['partoprenantoidento'])
        {
            $_SESSION["partoprenanto"] =
                new Partoprenanto($_REQUEST['partoprenantoidento']);
            unset($_SESSION["partopreno"]);
            if(DEBUG) {
                echo "<!-- nova partoprenanto-objekto -->";
            }
        }

    if ($_REQUEST['partoprenidento'])
        {
            $_SESSION["partopreno"] = new Partopreno($_REQUEST['partoprenidento']);
            debug_echo( "<!-- nova partopreno-objekto -->");
            if ($_SESSION['partopreno']->datoj['partoprenantoID'] != 
                $_SESSION['partoprenanto']->datoj['ID'])
                {
                    $_SESSION['partoprenanto'] =
                        new Partoprenanto($_SESSION['partopreno']->datoj['partoprenantoID']);
                    if(DEBUG) {
                        echo "<!-- nova partoprenanto-objekto -->";
                    }
                }
        }


    /*
     * ni difinas $partopreno_renkontigxo por uzi anstataux
     * $_SESSION['renkontigxo'], cxar gxi ja povus esti io alia
     * (se oni rigardas malnovan partoprenon, ekzemple).
     */ 
    if ($_SESSION['partopreno'] and
        $_SESSION['partopreno']->datoj['renkontigxoID'] != $_SESSION['renkontigxo']->datoj['ID'])
        {
            $GLOBALS['partopreno_renkontigxo'] = new Renkontigxo($_SESSION['partopreno']->datoj['renkontigxoID']);
            if(DEBUG) {
                echo "<!-- nova renkontigxo-objekto -->";
            }
        }
    else
        {
            $GLOBALS['partopreno_renkontigxo'] = $_SESSION['renkontigxo'];
        }

    /**
     * - por ke la partoprenanto estu tiu, kiu
     *   rilatas al la partopreno.
     */
    if ($_SESSION['partopreno'] and
        $_SESSION["partopreno"]->datoj['partoprenantoID'] !=
        $_SESSION['partoprenanto']->datoj['ID'])
        {
            $_SESSION['partoprenanto'] =
                new Partoprenanto($_SESSION["partopreno"]->datoj['partoprenantoID']);
            if(DEBUG) {
                echo "<!-- nova partoprenanto-objekto -->";
            }
        }


}





?>