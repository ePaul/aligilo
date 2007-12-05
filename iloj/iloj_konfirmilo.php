<?php

  /**
   * Iloj por krei unuan aux duan konfirmilon.
   */



  /**
   * $kodigo - aux 'x-metodo' aux 'utf-8' (aux 'ne-kodigu').
   */
function kreu_unuan_konfirmilan_tekston($partoprenanto,
                                        $partopreno,
                                        $renkontigxo,
                                        $kodigo='utf-8')
{
    // TODO: ebligu ali-lingvajn variantojn


    //    echo "<!-- " . var_export(compact('partoprenanto', 'partopreno', 'renkontigxo', 'kodigo'), true) . "-->";

    $eo_teksto = kreu_unuan_konfirmilan_tekston_unulingve('eo',
                                                          $partoprenanto,
                                                          $partopreno,
                                                          $renkontigxo,
                                                          $kodigo);


    if ($partopreno->datoj['germanakonfirmilo'] == 'J') {
        $de_teksto = kreu_unuan_konfirmilan_tekston_unulingve('de',
                                                              $partoprenanto,
                                                              $partopreno,
                                                              $renkontigxo,
                                                              $kodigo);
        return
            donu_tekston('konf1-germane-sube', $renkontigxo) . "\n" .
            $eo_teksto . "\n\n" .
            donu_tekston('konf1-jen-germana-teksto', $renkontigxo) . "\n" .
            $de_teksto ;
    }
    else {
        return $eo_teksto;
    }


}

function kreu_unuan_konfirmilan_tekston_unulingve($lingvo,
                                                  $partoprenanto,
                                                  $partopreno,
                                                  $renkontigxo,
                                                  $kodigo)
{

    $speciala = array();
    $speciala['landonomo'] = eltrovu_landon($partoprenanto->datoj['lando']);
    $speciala['tejojaro'] = TEJO_MEMBRO_JARO;
    $speciala['tejorabato'] = TEJO_RABATO;
    $speciala['asekuro'] =
        donu_tekston_lauxlingve(($partopreno->datoj['havas_asekuron'] == 'J') ?
                                'konf1-havas-asekuron' :
                                'konf1-ne-havas-asekuron',
                                $lingvo, $renkontigxo);
    
    $speciala['partopreno'] =
        donu_tekston_lauxlingve(($partopreno->datoj['partoprentipo'] == 't') ? 
                                "gxen-tuttempe" : "gxen-parttempe",
                                $lingvo, $renkontigxo);

    if (in_array($partopreno->datoj['vegetare'], array('J', 'N', 'A')))
        {
            $speciala['mangxmaniero'] =
                donu_tekston_lauxlingve('mangxmaniero-'.$partopreno->datoj['vegetare'],
                                        $lingvo, $renkontigxo);
        }
    else
        {
            $speciala['mangxmaniero'] =
                donu_tekston_lauxlingve('mangxmaniero-?',
                                        $lingvo, $renkontigxo);
        }

    $speciala['domotipo'] =
        donu_tekston_lauxlingve('domotipo-'. $partopreno->datoj['domotipo'],
                                $lingvo, $renkontigxo);

    if ($partopreno->datoj['domotipo'] == 'M')
        {
            $speciala['cxambro'] = "";
        }
    else
        {
            // TODO!: tradukebligu
            // TODO: unulita
            $speciala['cxambro'] =
                "\n Vi mendis " .
                (($partopreno->datoj['dulita']=="J") ?
                "dulitan " :
                 "").
                $partopreno->cxambrotipo() . "n c^ambron" .
                ($partopreno->datoj['kunkiu'] ?
                 " kun (eble) " . $partopreno->datoj['kunkiu'] : "");
        }
    // TODO: kunmangxas (laux opcio)

    $kotizo = new Kotizo($partopreno, $partoprenanto, $renkontigxo);
    $speciala['antauxpago'] = $kotizo->minimuma_antauxpago();
    $speciala['pageblecoj'] = pageblecoj_retpagxo;
    $invitpeto = $partopreno->sercxu_invitpeton();
    if ($invitpeto) {
        $speciala['invitpeto'] = 
            donu_tekston_lauxlingve('konf1-invitpeto-titolo',
                                    $lingvo, $renkontigxo) .
            $invitpeto->konfirmilaj_detaloj() . "\n\n\n" .
            donu_tekston_lauxlingve('konf1-invitilo', $lingvo, $renkontigxo) . "\n\n";
    }
    else {
        // ne petis invitleteron, do ne necesas ion pri tio skribi
        $speciala['invitpeto'] = "";
    }

    $speciala['dissendolisto'] =
        donu_tekston_lauxlingve('konf1-dissendolisto', $lingvo,
                                $renkontigxo) ;
    $speciala['subskribo'] = donu_tekston_lauxlingve('konf1-subskribo',
                                                     $lingvo, $renkontigxo);
//     $speciala['subskribo'] = $renkontigxo->funkciulo('admin') .
//         ", en la nomo de " . organizantoj_nomo . ", la organiza teamo.";
    
    $datumoj = array('anto' => $partoprenanto->datoj,
                     'eno' => $partopreno->datoj,
                     'igxo' => $renkontigxo->datoj,
                     'speciala' => $speciala);

    $sxablono = file_get_contents($GLOBALS['prafix'].'/sxablonoj/unua_konfirmilo_' . $lingvo . '.txt');

    if (DEBUG) {
        echo "<!-- " . var_export($datumoj, true) . "-->";
    }

    return eotransformado(transformu_tekston($sxablono, $datumoj),
                          $kodigo);

}


function kreu_duan_konfirmilan_tekston($partoprenanto,
                                       $partopreno,
                                       $renkontigxo,
                                       $kodigo='utf-8') {
    
    $eo_teksto = kreu_duan_konfirmilan_tekston_unulingve('eo',
                                                         $partoprenanto,
                                                         $partopreno,
                                                         $renkontigxo,
                                                         $kodigo);

    $ambaux_teksto = donu_tekston("konf2_dua-informilo-teksto");


    if ($partopreno->datoj['germanakonfirmilo'] == 'J') {
        $de_teksto = kreu_duan_konfirmilan_tekston_unulingve('de',
                                                             $partoprenanto,
                                                             $partopreno,
                                                             $renkontigxo,
                                                             $kodigo);
        return
            donu_tekston('konf1-germane-sube', $renkontigxo) . "\n" .
            $eo_teksto . "\n\n" .
            donu_tekston('konf1-jen-germana-teksto', $renkontigxo) . "\n" .
            $de_teksto . "\n\n" .
            $ambaux_teksto;
    }
    else {
        return $eo_teksto . "\n\n" .
            $ambaux_teksto;
    }
}


function kreu_duan_konfirmilan_tekston_unulingve($lingvo,
                                                 $partoprenanto,
                                                 $partopreno,
                                                 $renkontigxo,
                                                 $kodigo='utf-8') {

    // TODO: meti en datumbazon aux konfiguron
    $speciala =
        array('informiloadreso' =>
              'http://www.esperanto.de/dej/elshutoj/is/duaInformilo2007.pdf',
              'informilograndeco' => "570 KB",
              'subskribo' => donu_tekston_lauxlingve('konf1-subskribo',
                                                     $lingvo,
                                                     $renkontigxo),
              );
    if ($partopreno->datoj['agxoj'] < 18) {
        $speciala['sub18'] = true;
    }


    $sxablono = file_get_contents($GLOBALS['prafix'].
                                  '/sxablonoj/dua_konfirmilo_retposxto_' .
                                  $lingvo . '.txt');

    $datumoj = array('anto' => $partoprenanto->datoj,
                     'eno' => $partopreno->datoj,
                     'igxo' => $renkontigxo->datoj,
                     'speciala' => $speciala);


    if (DEBUG) {
        echo "<!-- " . var_export($datumoj, true) . "-->";
    }

    return eotransformado(transformu_tekston($sxablono, $datumoj),
                          $kodigo);


}



?>