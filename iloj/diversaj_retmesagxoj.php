<?php

  /*
   * Tiu dosiero enhavu diversajn funkciojn por sendi retmesagxojn.
   * Gxi uzas la funkciojn el retmesagxiloj.php, kaj kune kun gxi
   * celas anstatauxi iloj_mesagxoj.php.
   */



/**
 * posteulo de sendu_ekzport().
 * Eltrovas cxiujn informojn pri partoprenanto kaj
 * ties partopreno kaj sendos ilin al la sekurkopioj-adreso
 * (el $igxo).
 * Krome sendas $_POST (kio utilas por trovi erarojn en la aligxilo).
 *
 * $ppanto    - Partoprenanto-Objekto
 * $ppeno     - Partopreno-Objekto
 * $igxo      - Renkontigxo-Objekto
 * $sendanto  - Sendanto la mesagxon,
 *              ekzemple "aligxo" aux la entajpanto-nomo.
 *
 * rezulto: true, se ni povis cxion sendi.
 *          false, se la sekurkopiojretadreso mankas.
 */
function sendu_sekurkopion_de_aligxinto($ppanto, $ppeno, $igxo,
                                        $sendanto="nekonato")
{
    if (!($igxo->datoj['sekurkopiojretadreso']))
        {
            if (DEBUG)
                {
                    echo "<!-- mankas sekurkopiojretadreso. -->";
                }
            return false;
        }


    $invitpeto = $ppeno->sercxu_invitpeton();

    $teksto = "-- Eksporto de datumoj de partoprenanto" .
        "\n-- kaj partopreno fare de '". $sendanto . "'." ;
    $teksto .=
        "\n--" .
        "\n--  Partoprenanto" .
        "\n-- ---------------" .
        "\n" . $ppanto->sql_eksport() .
        "\n--" .
        "\n-- " . implode("';'", $ppanto->datoj) ;

    $teksto .=
        "\n--" .
        "\n--  Partopreno" .
        "\n-- ------------" .
        "\n" . $ppeno->sql_eksport() .
        "\n--" .
        "\n-- " . implode("';'", $ppeno->datoj) ;
    
    if ($invitpeto)
        {
            $teksto .=
                "\n--" .
                "\n--  Invitpeto" .
                "\n-- -----------" .
                "\n" . $invitpeto->sql_eksport() .
                "\n--" .
                "\n-- " . implode("';'", $invitpeto->datoj) ;
        }

    $teksto .=
        "\n-- -----------------------------------" .
        "\n\n" .
        "\n Detaloj en legebla formo" .
        "\n--------------------------" .
        "\n" .
        $ppanto->gravaj_detaloj_tekste() . "\n" .
        $ppeno->konfirmilaj_detaloj() ;
    if ($invitpeto)
        {
            $teksto .= "\n\n" . $invitpeto->konfirmilaj_detaloj();
        }
        
    $teksto .= 
        "\n" .
        "\n ----------- POST-datoj ------------" .
        "\n" . var_export($_POST, true) .
        "\n";

    $mesagxo = kreu_auxtomatan_mesagxon();
    $mesagxo->ricevanto_estu($igxo->datoj['sekurkopiojretadreso'],
                             "Sekurkopio-ricevantoj");
    $mesagxo->temo_estu("Sekurkopio de " . $igxo->datoj['mallongigo'] .
                        ": #" . $ppanto->datoj[ID] . " + #" .
                        $ppeno->datoj[ID]);
    $mesagxo->auxtomata_teksto_estu($teksto, "utf-8", $sendanto, $igxo);
    $mesagxo->eksendu();
    return true;
}

/**
 * mesagxo al la invitilo-respondeculo,
 * kiam nova invit-petanto aligxas.
 */
function sendu_invitilomesagxon($partoprenanto, $partopreno,
                                $renkontigxo, $sendanto="nekonato")
{
    $invitpeto = $partopreno->sercxu_invitpeton();
    if (!$invitpeto)
        {
            // ne necesas.
        return;
        }

    $teksto =
        "\nSaluton ".antauxnomo($renkontigxo->funkciulo('invitletero'))."," .
        "\n" .
        "\nalig^is partoprenanto, kiu deziras invitleteron." .
        "\n" .
        "\n Personaj datumoj: " .
        "\n------------------" .
        "\n" . $partoprenanto->gravaj_detaloj_tekste() .
        "\n" .
        "\n Detaloj por la invitilo:" .
        "\n-------------------------" .
        "\n" .
        "\n" . $invitpeto->konfirmilaj_detaloj() .
        "\n";

    if ($partopreno->datoj['rimarkoj'])
        {
            $teksto .=
                "\n " . ucfirst($partoprenanto->personapronomo) . " rimarkis:".
                "\n -------------" .
                "\n" .
                "\n" . $partopreno->datoj['rimarkoj'];
        }
    
    

    $mesagxo = kreu_auxtomatan_mesagxon();
    $mesagxo->ricevanto_estu($renkontigxo->funkciuladreso('invitletero'),
                             $renkontigxo->funkciulo('invitletero'));
    $mesagxo->temo_estu("Invitpeto de " . $partoprenanto->tuta_nomo() );
    $mesagxo->auxtomata_teksto_estu($teksto, "x-metodo");
    $mesagxo->eksendu();
}


/**
 * kreas tekston por la unua konfirmilo,
 * sendas gxin al la partoprenanto (se tiu
 * donis retmesagxon) kaj eblaj kopioj-ricevantoj
 * kaj redonas la tekston.
 */
function kreu_kaj_sendu_unuan_konfirmilon($partoprenanto,
                                          &$partopreno, $renkontigxo,
                                          $sendanto = "Alig^ilo")
{
    // heuxristiko: Se la homoj volas retposxtan varbadon
    // en UTF-8-formato, ili versxajne ankaux volas la
    // konfirmilon en UTF-8.

    $kodigo =
        ($partoprenanto->datoj['retposxta_varbado'] == 'u') ?
        "utf-8" : "x-metodo";

    $teksto = kreu_unuan_konfirmilan_tekston($partoprenanto,
                                             $partopreno,
                                             $renkontigxo,
                                             $kodigo);
    $mesagxo = kreu_auxtomatan_mesagxon();
    


    $mesagxo->temo_estu("Unua konfirmilo por la " .
                        $renkontigxo->datoj['nomo']);
    if ($partoprenanto->datoj['retposxto'])
        {
            $mesagxo->ricevanto_estu($partoprenanto->datoj['retposxto'],
                                     $partoprenanto->tuta_nomo());
            $mesagxo->kopion_al(constant('unua_konfirmilo_kopioj_al'));
        }
    else
        {
            $mesagxo->ricevanto_estu(constant('unua_konfirmilo_kopioj_al'),
                                     "Aligxilo-Kopioj-ricevanto");
        }
    $mesagxo->auxtomata_teksto_estu($teksto, $kodigo,
                                    $sendanto, $renkontigxo);
    $mesagxo->eksendu();


    // memoru la sendodaton:

    sxangxu_datumbazon("partoprenoj",
                       array('1akonfirmilosendata' => date("Y-m-d")),
                       $partopreno->datoj['ID']);
    $partopreno->prenu_el_datumbazo();

    return $teksto;
                                    
        
}


/**
 * sendo de la dua informilo.
 *
 */
function sendu_duan_informilon($partoprenanto, $partopreno,
                               $renkontigxo, $savu = "NE")
{
    $mesagxo = kreu_auxtomatan_mesagxon();
    $kodigo =
        ($partoprenanto->datoj['retposxta_varbado'] == 'u') ?
        "utf-8" : "x-metodo";
    $sendanto = $_SESSION['kkren']['entajpantonomo'];

    $mesagxo->temo_estu("Dua konfirmilo kaj informilo por la " .
                        $renkontigxo->datoj['nomo'] );
    if (!$partoprenanto->datoj['retposxto']) {
        return;
    }


    $mesagxo->ricevanto_estu($partoprenanto->datoj['retposxto'],
                             $partoprenanto->tuta_nomo());

    //    // por testaj celoj ...
    //    $mesagxo->ricevanto_estu(teknika_administranto_retadreso,
    //                             $partoprenanto->tuta_nomo());



    $teksto = kreu_duan_konfirmilan_tekston($partoprenanto,
                                             $partopreno,
                                             $renkontigxo,
                                             "ne kodigu");

    $mesagxo->auxtomata_teksto_estu($teksto, $kodigo,
                                    $sendanto, $renkontigxo);



    $konfirmilo = new Konfirmilo(bezonas_unikodon($partoprenanto));
    $konfirmilo->kreu_konfirmilon($partopreno, $partoprenanto, $savu,
                                  $renkontigxo);
    $konfirmilo->sendu(); // kreas konfirmilo.pdf

    $mesagxo->aldonu_dosieron_el_disko($GLOBALS['prafix'] .
                                       "/dosieroj_generitaj/konfirmilo.pdf");

    // aldonu la duan informilon, se gxi ekzistas.
    $informilodosiero = $GLOBALS['prafix'] . "/dosieroj/2aInformilo.pdf";

    if (file_exists($informilodosiero)) {
        $mesagxo->aldonu_dosieron_el_disko($informilodosiero);
    }

                                       
    $mesagxo->eksendu();
}



/**
 * nu, kion la nomo diras ...
 */
function simpla_test_mesagxo()
{
    $mesagxo = kreu_auxtomatan_mesagxon();
    $mesagxo->ricevanto_estu($igxo->datoj['sekurkopiojretadreso'],
                             "Sekurkopio-ricevantoj");
    $mesagxo->temo_estu("Testmesagxo");
    $mesagxo->auxtomata_teksto_estu("Saluton");
    $mesagxo->eksendu();
}


?>