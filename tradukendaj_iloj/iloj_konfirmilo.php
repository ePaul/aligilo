<?php
  /**
   * Iloj por krei unuan aux duan konfirmilon.
   *
   *@todo Ebligu alilingvajn variantojn.
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
   */
function kreu_aligxilan_kontroltabelon(&$partoprenanto,
                                       &$partopreno)
{
    eniru_dosieron();

    $invitpeto = $partopreno->sercxu_invitpeton();

    $datumoj = array("anto" => $partoprenanto,
                     "eno" => $partopreno,
                     "peto" => $invitpeto);

     echo "<pre>";
     var_export($datumoj);
     echo "</pre>";

    $teksto ="";
    
    $teksto .= "<table class='kontroltabelo'>\n";
    
    $teksto .= aligxilo_formatu_subtabelon( CH("kontroltabelo-persono"),
                                            $datumoj,
                                            CH("Personaj-datumoj"));

    if ($invitpeto) {
        $teksto .= aligxilo_formatu_subtabelon( CH("kontroltabelo-vizo"),
                                                $datumoj, CH("Vizo"));
    }

    $teksto .= aligxilo_formatu_subtabelon( CH("kontroltabelo-partopreno"),
                                            $datumoj, CH("Partopreno"));

    $teksto .= aligxilo_formatu_subtabelon( CH("kontroltabelo-kontribuoj"),
                                            $datumoj, CH("Kontribuoj"));

    $teksto .= aligxilo_formatu_subtabelon( CH("kontroltabelo-diversajxoj"),
                                            $datumoj, CH("Diversajxoj"));

    $teksto .= "</table>\n";

    eliru_dosieron();
    return $teksto;
}

function aligxilo_formatu_subtabelon($sxablono, $datumoj, $titolo) {
    $teksto = "";
    $teksto .= "<tr><th colspan='3' class='titolo'>" . $titolo .
        "</th></tr>\n";
    //    $teksto .= "<table class='kontroltabelo'>";
    $linioj = explode("\n", $sxablono);
    foreach($linioj AS $linio) {
        list($titolo, $kamponomo, $loko) = explode("|", $linio);
        $teksto .= "<tr><th>" . $titolo . "</th>";
        $kamponomo = trim($kamponomo);
        $valoro = teksttransformo_donu_datumon($kamponomo,
                                               $datumoj);
        // TODO: traduku la valoron
        $teksto .= "<td>" . nl2br($valoro) . "</td>";
        // TODO: butono por iri al la gxusta loko
        $teksto .= "<td>". CH("pagxo"). " " . $loko ."</td>";
        $teksto .= "</tr>\n";
    }
    //    $teksto .= "</table>";
    return $teksto;
}




  /**
   * @param Partoprenanto $partoprenanto
   * @param Partopreno $partopreno
   * @param Renkontigxo $renkontigxo
   * @param string $kodigo aŭ 'x-metodo' aŭ 'utf-8' (aŭ 'ne-kodigu').
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
    eniru_dosieron();
    eniru_lingvon($lingvo);

    $speciala = array();
    $speciala['landonomo'] = 
        traduku_datumbazeron('landoj', 'nomo', $partoprenanto->datoj['lando'], $lingvo);
    //        eltrovu_landon($partoprenanto->datoj['lando']);
    
    $speciala['tejojaro'] = TEJO_MEMBRO_JARO;
    $speciala['tejorabato'] = TEJO_RABATO;

    if (ASEKURO_EBLAS) {
        if($partopreno->datoj['havas_asekuron'] == 'J') {
            $speciala['asekuro'] = CH("konf1-havas-asekuron");
        }
        else {
            $speciala['asekuro'] = CH("konf1-ne-havas-asekuron");
        }
    }
    if ($partopreno->datoj['partoprentipo'] == 't') {
        $speciala['partopreno'] = CH("tuttempe");
    }
    else {
        $speciala['partopreno'] = CH("parttempe");
    }

    switch($partopreno->datoj['vegetare']) {
    case 'J':
        $speciala['mangxmaniero'] = CH("vegetara");
        break;
    case 'N':
        $speciala['mangxmaniero'] = CH("vianda");
        break;
    case 'A':
        $speciala['mangxmaniero'] = CH("vegana");
        break;
    default:
        $speciala['mangxmaniero'] = CH("mangxmaniero-?",
                                       $partopreno->datoj['vegetare']);
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

    $kotizo = new Kotizokalkulilo($partoprenanto, $partopreno, $renkontigxo,
                                  new Kotizosistemo($renkontigxo->datoj['kotizosistemo']));
    $speciala['antauxpago'] = $kotizo->minimuma_antauxpago();
    $speciala['pageblecoj'] = pageblecoj_retpagxo;

    $kotForm = new TekstaKotizoFormatilo($lingvo, $kodigo);
    $kotizo->tabelu_kotizon($kotForm);
    debug_echo( "<!-- kotizotabelo: \n" . 
                $kotForm->preta_tabelo . "\n -->");
    $speciala['kotizotabelo'] = $kotForm->preta_tabelo;

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

    $sxablono = CH('unua-konfirmilo-sxablono');

//     $sxablono = file_get_contents($GLOBALS['prafix'].'/sxablonoj/unua_konfirmilo_' . $lingvo . '.txt');

    if (DEBUG) {
        echo "<!-- " . var_export($datumoj, true) . "-->";
    }

    eliru_dosieron();
    eliru_lingvon();

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

    eliru_dosieron();

    return eotransformado(transformu_tekston($sxablono, $datumoj),
                          $kodigo);


}


