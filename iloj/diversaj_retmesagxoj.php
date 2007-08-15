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
 */
function sendu_sekurkopion_de_aligxinto($ppanto, $ppeno, $igxo,
                                        $sendanto="nekonato")
{
    if (!($renkontigxo->datoj['sekurkopiojretadreso']))
        {
            if (DEBUG)
                {
                    echo "<!-- mankas sekurkopiojretadreso. -->";
                }
            return;
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
        // TODO: anstatauxu faru_aligxtekston
        faru_aligxtekston($ppanto->datoj['ID'], $ppanto->datoj['ID']);
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
    $mesagxo->auxtomata_teksto_estu($teksto, $igxo);
    // cxu io mankas?
    $mesagxo->eksendu();
}



function simpla_test_mesagxo()
{
    $mesagxo = kreu_auxtomatan_mesagxon();
    $mesagxo->ricevanto_estu('Paul.Ebermann@esperanto.de', "Pauxlo");
    $mesagxo->temo_estu("Testmesagxo");
    $mesagxo->auxtomata_teksto_estu("Saluton");
    // cxu io mankas?
    $mesagxo->eksendu();
}


?>