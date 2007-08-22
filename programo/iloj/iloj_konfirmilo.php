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
    // TODO: ebligu nacilingvan varianton
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
            $speciala['mangxmaniero'] = "vegetarano";
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
                "log^os en la amaslog^ejo kaj mang^os memzorge";
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
            $invitpeto->konfirmilaj_detaloj() . "\n\n" .
            // TODO: uzu tekston "konf1-invitilo" (aux ion similan).
            donu_tekston('konf1-invitilo', $renkontigxo);
    }
    else {
        // ne petis invitleteron, do ne necesas ion pri tio skribi
        $speciala['invitpeto'] = "";
    }
    // TODO - aldonu kiel teksto, cxu ne?
    $speciala['dissendolisto'] = "" ;
    $speciala['subskribo'] = $renkontigxo->funkciulo('admin') .
        ", en la nomo de " . organizantoj_nomo . ", la organiza teamo.";
    
    $datumoj = array('anto' => $partoprenanto->datoj,
                     'eno' => $partopreno->datoj,
                     'igxo' => $renkontigxo->datoj,
                     'speciala' => $speciala);

    $sxablono = file_get_contents($GLOBALS['prafix'].'/sxablonoj/unua_konfirmilo_eo.txt');

    return eotransformado(transformu_tekston($sxablono, $datumoj),
                          $kodigo);

}





?>