<?php

  /**
   * Iloj por krei unuan aux duan konfirmilon.
   */



  /**
   * $kodigo - aux 'x-metodo' aux 'utf-8'.
   */
function kreu_unuan_konfirmilan_tekston($partoprenanto,
                                        $partopreno,
                                        $renkontigxo,
                                        $kodigo='utf-8')
{
    $speciala = array();
    $speciala['landonomo'] = eltrovu_landon($partoprenanto->datoj['lando']);
    $speciala['tejojaro'] = TEJO_MEMBRO_JARO;
    $speciala['tejorabato'] = TEJO_RABATO;
    $speciala['asekuro'] =
        ($partopreno->datoj['havas_asekuron'] == 'J') ?
        "Vi havas asekuron pri malsano kaj kunportos la necesajn paperojn." :
        "Vi ne havas tauxgan asekuron pri malsano.";
        
    $speciala['partopreno'] =
        ($partopreno->datoj['partoprentipo'] == 't') ? 
        "tuttempe" :
        "parttempe";

    switch($partopreno->datoj['vegetare'])
        {
        case 'J':
            $speciala['mangxmaniero'] = "vegeterano";
            break;
        case 'N':
            $speciala['mangxmaniero'] = "viandmang^anto";
            break;
        case 'A':
            $speciala['mangxmaniero'] = "vegano";
            break;
        default:
            $speciala['mangxmaniero'] = "nekonata mang^anto";
        }

    if ($partopreno->datoj['domotipo'] == 'M')
        {
            $speciala['domotipo'] =
                "log^os en la amaslog^ejo kaj mang^os memzorge.";
            $speciala['cxambro'] = "";
        }
    else
        {
            $speciala['domotipo'] =
                "log^os kaj mang^os en la junulargastejo";
            switch($partopreno->datoj['cxambrotipo'])
                {
                case 'u':
                    $cxambrosekso = "unuseksan c^ambron";
                case 'g':
                    $cxambrosekso = "gean c^ambron";
                default:
                    $cxambrosekso =
                        "(strang-seksan: '{$partopreno->datoj['cxambrotipo']}')".
                        " c^ambron";
                }

            $speciala['cxambro'] =
                "\n Vi mendis " .
                (($partopreno->datoj['dulita']=="J") ?
                "dulitan " :
                 "").
                $cxambrosekso .
                ($partopreno->datoj['kunkiu'] ?
                 " kun (eble) " . $partopreno->datoj['kunkiu'] : "");
        }
    // TODO: kunmangxas

    $kotizo = new Kotizo($partopreno, $partoprenanto, $renkontigxo);
    $speciala['antauxpago'] = $kotizo->minimuma_antauxpago();
    $speciala['pageblecoj'] = pageblecoj_retpagxo;
    $invitpeto = $partopreno->sercxu_invitpeton();
    if ($invitpeto) {
        $speciala['invitpeto'] = 
            "\n Detaloj por la Invitilo" . 
            "\n-------------------------" .
            "\n" .
            $invitpeto->konfirmilaj_detaloj() . "\n";
    }
    else {
        $speciala['invitpeto'] = "";
    }
    $speciala['dissendolisto'] = "" ; // TODO
    $speciala['subskribo'] = $renkontigxo->datoj['adminrespondulo'] .
        ", en la nomo de " . organizantoj_nomo . ", la organiza teamo.";
    
    $datumoj = array('anto' => $partoprenanto->datoj,
                     'eno' => $partopreno->datoj,
                     'igxo' => $renkontigxo->datoj,
                     'speciala' => $speciala);

    $sxablono = file_get_contents($prafix.'sxablonoj/unua_konfirmilo_eo.txt');

    return transformu_tekston($sxablono, $datumoj);

}





?>