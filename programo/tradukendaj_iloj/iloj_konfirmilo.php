<?php
  // ĉĝĥĵŝŭ

  /**
   * Iloj por krei unuan aŭ duan konfirmilon, kaj rilataj aferoj.
   *
   *@todo Ebligu alilingvajn variantojn. ==> nun parte jam eblas.
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

function kreu_konfirmilan_kontroltabelon(&$partoprenanto,
                                         &$partopreno, $kodigo)
{
    $tabelformatilo = new teksta_Tabelformatilo($kodigo);
    kreu_kontroltabelon($partoprenanto, $partopreno,
                        $tabelformatilo);
    return $tabelformatilo->donuTekston();
}


  /**
   */
function kreu_aligxilan_kontroltabelon(&$partoprenanto,
                                       &$partopreno)
{

    $tabelformatilo = new HTML_Tabelformatilo();
    kreu_kontroltabelon($partoprenanto, $partopreno,
                        $tabelformatilo);
    return $tabelformatilo->donuTekston();



//     eniru_dosieron();

//     $invitpeto = $partopreno->sercxu_invitpeton();

//     $datumoj = array("anto" => $partoprenanto,
//                      "eno" => $partopreno,
//                      "peto" => $invitpeto);

//      echo "<pre>";
//      var_export($datumoj);
//      echo "</pre>";

//     $teksto ="";
    
//     $teksto .= "<table class='kontroltabelo'>\n";
    
//     $teksto .= aligxilo_formatu_subtabelon( CH("kontroltabelo-persono"),
//                                             $datumoj,
//                                             CH("Personaj-datumoj"));

//     if ($invitpeto) {
//         $teksto .= aligxilo_formatu_subtabelon( CH("kontroltabelo-vizo"),
//                                                 $datumoj, CH("Vizo"));
//     }

//     $teksto .= aligxilo_formatu_subtabelon( CH("kontroltabelo-partopreno"),
//                                             $datumoj, CH("Partopreno"));

//     $teksto .= aligxilo_formatu_subtabelon( CH("kontroltabelo-kontribuoj"),
//                                             $datumoj, CH("Kontribuoj"));

//     $teksto .= aligxilo_formatu_subtabelon( CH("kontroltabelo-diversajxoj"),
//                                             $datumoj, CH("Diversajxoj"));

//     $teksto .= "</table>\n";

//     eliru_dosieron();
//     return $teksto;
}


function kreu_kontroltabelon(&$partoprenanto,
                             &$partopreno,
                             &$tabelformatilo)
{

//     echo("<!-- kreu_kontroltabelon(" .
//          var_export(compact('partoprenanto', 'partopreno', 'tabelformatilo'), true) . ") \n-->");
    eniru_dosieron();
    $invitpeto = $partopreno->sercxu_invitpeton();

    $diversaj_informoj =
        array('landonomo' => $partoprenanto->landonomo_en_lingvo());

    $tabelformatilo->metu_datumojn(array("anto" => $partoprenanto,
                                         "eno" => $partopreno,
                                         "peto" => $invitpeto,
                                         'div' => $diversaj_informoj));
   
    

    $tabelformatilo->formatu_subtabelon( CH("kontroltabelo-persono"),
                                            CH("Personaj-datumoj"));

    $tabelformatilo->formatu_subtabelon( CH("kontroltabelo-adreso"),
                                         CH("adreso"));


    if ($invitpeto) {
        $tabelformatilo->formatu_subtabelon( CH("kontroltabelo-vizo"),
                                             CH("Vizo"));
    }

    $tabelformatilo->formatu_subtabelon( CH("kontroltabelo-partopreno"),
                                         CH("Partopreno"));

    $tabelformatilo->formatu_subtabelon( CH("kontroltabelo-kontribuoj"),
                                         CH("Kontribuoj"));

    $tabelformatilo->formatu_subtabelon( CH("kontroltabelo-diversajxoj"),
                                         CH("Diversajxoj"));
    eliru_dosieron();

//     echo("<!-- fino de kreu_kontroltabelon( ..., " . var_export($tabelformatilo, true) . ") \n-->");

    
}

// function aligxilo_formatu_subtabelon($sxablono, $datumoj, $titolo) {
//     $teksto = "";
//     $teksto .= "<tr><th colspan='3' class='titolo'>" . $titolo .
//         "</th></tr>\n";
//     //    $teksto .= "<table class='kontroltabelo'>";
//     $linioj = explode("\n", $sxablono);
//     foreach($linioj AS $linio) {
//         echo("<!-- linio: [$linio] -->");
//         list($titolo, $kamponomo, $loko) = explode("|", $linio);
//         $kamponomo = trim($kamponomo);
//         if ($kamponomo == "") {
//             continue;
//         }
//         $teksto .= "<tr><th>" . $titolo . "</th>";
//         $valoro = teksttransformo_donu_datumon($kamponomo,
//                                                $datumoj);
//         // TODO: traduku la valoron
//         $teksto .= "<td>" . nl2br($valoro) . "</td>";
//         // TODO: butono por iri al la gxusta loko
//         $teksto .= "<td>". CH("pagxo"). " " . $loko ."</td>";
//         $teksto .= "</tr>\n";
//     }
//     //    $teksto .= "</table>";
//     return $teksto;
// }

function tekste_formatu_subtabelon($sxablono, $datumoj, $titolo) {
    $teksto = "";
    
}


class Tabelformatilo {

    var $teksto;
    var $datumoj;
    var $kodigo;

    function Tabelformatilo($kodigo) {
        $this->kodigo = $kodigo;
    }

    /**
     * @param array $datumoj
     */
    function metu_datumojn($datumoj) {
        $this->datumoj = $datumoj;
    }

    /**
     * farenda en subklaso
     *
     * @param eostring $sxablono
     * @param eostring $titolo
     */
    function formatu_subtabelon($sxablono, $titolo) {
        
        return NULL;
    }

    function donuTekston() {
        // TODO
    }

}

class HTML_Tabelformatilo extends Tabelformatilo {
    function HTML_Tabelformatilo($kodigo="unikodo") {
        $this->Tabelformatilo($kodigo);
        $this->teksto = "<table class='kontroltabelo'>\n";
    }

    /**
     * farenda en subklaso
     *
     * @param eostring $sxablono
     * @param eostring $titolo
     */
    function formatu_subtabelon($sxablono, $titolo) {
        $teksto = "";
        $teksto .= "<tr><th colspan='3' class='titolo'>" .
            eotransformado($titolo, $this->kodigo) .
            "</th></tr>\n";
        $linioj = explode("\n", $sxablono);
        foreach($linioj AS $linio) {
            list($titolo, $kamponomo, $loko) = explode("|", $linio);
            $kamponomo = trim($kamponomo);
            if (!$kamponomo) {
                continue;
            }
            $teksto .= "<tr><th>" . eotransformado($titolo,
                                                   $this->kodigo) . "</th>";
            $valoro = teksttransformo_donu_datumon($kamponomo,
                                                   $this->datumoj);
            $teksto .= "<td>" . nl2br(eotransformado($valoro,
                                                     $this->kodigo))
                . "</td>";
            // TODO: butono por iri al la gxusta loko
            $teksto .= "<td>". CH("pagxo"). " " . $loko ."</td>";
            $teksto .= "</tr>\n";
        }
        $this->teksto .= $teksto;
    }

    function donuTekston() {
        return $this->teksto . "</table>\n";
        // TODO
    }

}

class teksta_Tabelformatilo extends Tabelformatilo {

    function teksta_Tabelformatilo($kodigo) {
        $this->Tabelformatilo($kodigo);
        $this->teksto = "";
    }

    function formatu_subtabelon($sxablono, $titolo) {
//         echo("<!-- formatu_subtabelon( ..., " . var_export($titolo, true) .
//              ")\n-->");

        $teksto = "\n";
        $titolo = eotransformado($titolo, $this->kodigo);
        $teksto .= "\n " . $titolo . " ";
        $teksto .= "\n-" . str_repeat('-', mb_strlen($titolo, "UTF-8")) . "-";
        $teksto .= "\n";
        $linioj = explode("\n", $sxablono);
        $tabellinioj = array();
        $largxo = 0;

        foreach ($linioj AS $linio) {
            list($titolo, $kamponomo) = explode("|", $linio);
            $titolo = eotransformado($titolo, trim($this->kodigo));
            $len = mb_strlen($titolo, "UTF-8");
            $tabellinio = array ($titolo, $kamponomo, $len);
            $tabellinioj[]= $tabellinio;
            $largxo = max($largxo, $len);
        }
//         echo "<!-- " . var_export($tabellinioj, true) . "-->";

        foreach($tabellinioj AS $tabellinio) {
            list($titolo, $kamponomo, $len) = $tabellinio;
            $teksto .= "\n" . $titolo . " " .
                str_repeat(" ", $largxo - $len);
            $kamponomo = trim($kamponomo);
            $valoro = teksttransformo_donu_datumon($kamponomo, $this->datumoj);
            $valoro = implode(str_repeat(" ", $largxo + 1),
                              explode("\n",
                                      eotransformado($valoro,
                                                     $this->kodigo)));
            $teksto .= $valoro;
        }
        $this->teksto .= $teksto . "\n";
    }

    function donuTekston() {
        return $this->teksto;
    }
    
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


//     echo "<!-- " . var_export(compact('partoprenanto', 'partopreno', 'renkontigxo', 'kodigo'), true) . "-->";





    if (KAMPOELEKTO_IJK) {
        // ebligu ali-lingvajn variantojn
        $eo_teksto =
            kreu_unuan_konfirmilan_tekston_nova('eo',
                                                $partoprenanto,
                                                $partopreno,
                                                $renkontigxo,
                                                $kodigo);
        
        $lingvo = $partopreno->datoj['konfirmilolingvo'];
        if($lingvo != 'eo') {
            $loka_teksto =
                kreu_unuan_konfirmilan_tekston_nova($lingvo,
                                                    $partoprenanto,
                                                    $partopreno,
                                                    $renkontigxo,
                                                    $kodigo);
            return
                CH_lau('~#konf1-vialingvo-sube', $lingvo) . "\n" .
                $eo_teksto . "\n\n" .
                CH_lau('~#konf1-jen-vialingvo', $lingvo) . "\n" .
                $loka_teksto;
        }
        else {
            return $eo_teksto;
        }



    }
    else {

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

}

function kreu_informmesagxan_tekston($partoprenanto,
                                     $partopreno,
                                     $renkontigxo,
                                     $kodigo)
{
    $eo_teksto =
        kreu_informmesagxan_tekston_en('eo',
                                       $partoprenanto,
                                       $partopreno,
                                       $renkontigxo,
                                       $kodigo);
    $lingvo = $partopreno->datoj['konfirmilolingvo'];
    if ($lingvo != 'eo') {
        $loka_teksto =
            kreu_informmesagxan_tekston_en($lingvo,
                                           $partoprenanto,
                                           $partopreno,
                                           $renkontigxo,
                                           $kodigo)
            or // -> se la informmesagxa teksto mankas,
            // prenu la tekston por unua konfirmilo.
            $loka_teksto =
            kreu_unuan_konfirmilan_tekston_nova($lingvo,
                                                $partoprenanto,
                                                $partopreno,
                                                $renkontigxo,
                                                $kodigo);
        return
            CH_lau('~#konf1-vialingvo-sube', $lingvo) . "\n" .
            "----------------------------------------------\n\n" .
            $eo_teksto . "\n\n" .
            "----------------------------------------------\n" .
            CH_lau('~#konf1-jen-vialingvo', $lingvo) . "\n\n" .
            $loka_teksto;
    }
    else {
        return $eo_teksto;
    }
}


/**
 * specifa por IJK 2009, pro manko da tempo.
 * @todo prenu la tekstojn el la datumbazo.
 */
function kreu_lastan_informmesagxan_tekston($partoprenanto,
											$partopreno,
											$renkontigxo,
											$kodigo) {
  $enkonduko = "Kara ". $partoprenanto->datoj['personanomo'] . ",

la organiza teamo g^ojas, ke vi venos al la 65a Internacia
Junulara Kongreso en Liberec'.
Jen la lasta informa retmesag^o kun gravaj informoj por
c^iuj partoprenantoj. Bonvolu legi g^in zorge.

 Enhavo
========

";
  $enhavo[1] = array("Antau^kongresa informilo.", "
Se vi ne scias, kiel veni al la IJK, vi vers^ajne maltrafis la
antau^kongresan informilon, kiun ni sendis lastan semajnon. G^i
estas els^utebla en PDF-formato (811 KB) tie:
   http://ijk.esperanto.cz/dokumentoj/antaukongresilo_ijk2009.pdf
");

  $enhavo[] = array("Prago: S^ang^o de bus-kajo", "
Atentigo por tiuj kiuj veturos al Liberec' el Prago:
Okazis s^ang^o en la busa stacidomo Černý Most. Auxtobusoj al
Liberec' nun foriras el haltejo numero 6 (ne plu 7). Tamen,
se vi alveninte per metroo al Černý Most elserc^os tie nian
bonveniganton, tiu volonte asistos al vi pri c^io.");

  $enhavo[] = array("Kultur-Lingva Festivalo", "
Ni memorigas vin ke tradicie dum IJK okazas KLF, la Kultur-Lingva
Festivalo, malfermita por la urbo kaj neesperantistoj. Tio estas bonega
s^anco montri al homoj, kiel funkcias Esperanto kunigante diversajn
naciojn.

C^i-foje KLF okazos 23-an de Julio (j^au^do), en la urboplaco de Liberec'.

Prezentu vian landon dum KLF!
Montru specialaj^ojn de via lando al lokaj urbanoj kaj pli ol 300
esperantistoj de diversaj landoj, kiuj certe malsatas je ekscio pri
aliaj kulturoj kaj kutimoj.

Por prezenti vian landon kaptu materialojn, mangxaj^ojn, vestaj^ojn, c^ion
lau^ via plac^o, kio montras vian kulturon. Estus pli bone se vi povus
prezenti nacian kanton au^ dancon, kiu reprezentas vian regionon.

Se vi havas demandojn - simple kontaktu ijk.klf@esperanto.cz.  ");

  $enhavo[] = array("Ludoj por la ludejo","
Bonvolu atenti ke ankau^ c^ijare estos ludejo dum IJK. Bonvolu kunporti
viajn plej s^atatajn ludojn por provi ilin kun viaj amikoj el la tuta mondo.");

  $kotizo = new Kotizokalkulilo($partoprenanto, $partopreno, $renkontigxo,
								new Kotizosistemo($renkontigxo->datoj['kotizosistemo']));
  
  debug_echo ("<!-- kotizokalkulilo: \n" . var_export($kotizo, true) . "-->");
  
  $kotForm = new TekstaKotizoFormatilo($lingvo, $kodigo);
  $kotizo->tabelu_kotizon($kotForm);
  
  $enhavo[] = array("Via kotizokalkulado", "
Jen la tabelo de via kotizo, lau^ c^iuj antau^pagoj, kiuj
g^is nun alvenis en nia datumbaza sistemo.

Se mankas antau^pago de vi, bonvolu sendi noton al
ijk.kasisto@esperanto.cz kaj ijk.admin@esperanto.cz,
kaj kunportu pruvilon pri via pago al la IJK, kaj ni
povos solvi tion surloke.

(Tia mankanta antau^pago ankau^ povas influi vian alig^kategorion,
 do ne necesas pri tio aparte plendi.)

" . $kotForm->preta_tabelo."\n");

  switch ($partopreno->datoj['studento']) {
  case 'n':
	break;
  case 'j':
	$enhavo[] = array("Studenta legitimilo", "
Vi indikis, ke vi estos lernanto au^ studento dum IJK.
Tio bonas, c^ar ni tiel povas iom s^pari pri la c^ambrokostoj,
kaj eble ankau^ iu ekskurso ig^os malpli kosta por vi.

Bonvolu ne forgesi kunporti iun studentan legitimilon.
");
	break;
  case '?':
	$enhavo[] = array("Studento?", "
Se vi estos studento au^ lernanto dum IJK, bonvolu
kunporti vian studentan legitimilon. Tio por ni ebligas
s^pari iomete pri la c^ambrokostoj, kaj eble ankau^ iu
ekskurso ig^os malpli kosta por vi.
");
	break;
  default:
	darf_nicht_sein($partopreno->datoj['studento']);
  }

  switch($partopreno->datoj['tejo_membro_kontrolita'] .
		 $partopreno->datoj['tejo_membro_laudire']) {
  case 'jn':
  case 'jj':
	// nenio farenda
	break;
  case 'nn':

	$ne_estas = "
Lau^ nia kontrolo, vi g^is nun ne estas membro por 2009.
Vi povas alig^i (kaj pagi vian kotizon) surloke dum la
akceptado.

Se vi jam pagis vian kotizon, bonvolu kunporti pruvilon
pri tio al IJK.
(Membrokarto kun aktuala membromarko ankau^ sufic^as.) ";

	if ($partoprenanto->datoj['naskigxdato'] >= "1979-01-01") {
	  $enhavo[]=array("TEJO-membreco","
Se vi estos individua membro de UEA por 2009 en kategorioj MA-T
au^ MJ-T (au^ DM-T respektive DMJ-T), t.e. individua membro de
TEJO, vi ricevos rabaton depende de via landokategorio dum IJK." .
					  $ne_estas);
	}
	else {
	  $enhavo[] = array("UEA-membreco", "
Se vi estos individua membro de UEA por 2009 en kategorioj MA
au^ MJ (au^ DM respektive DMJ), t.e. individua membro de TEJO,
vi ricevos rabaton depende de via landokategorio dum IJK." .
						$ne_estas);
						
	}
	break;
  case 'nj':
	$ne_membro = "
vi povas alig^i (kaj pagi vian kotizon) surloke dum la akceptado.

Se vi jam pagis vian kotizon, bonvolu kunporti pruvilon
pri tio al IJK.
(Membrokarto kun aktuala membromarko ankau^ sufic^as.) ";

	if ($partoprenanto->datoj['naskigxdato'] >= "1979-01-01") {
	  $enhavo[] = array("TEJO-membreco", "
Vi asertis esti membro de TEJO por 2009, sed nia kontrolo
donis kontrau^an rezulton. Por tamen ricevi la UEA-rabaton," .
						$ne_membro);
	}
	else {
	  $enhavo[] = array("UEA-membreco", "
Vi asertis esti membro de UEA por 2009, sed nia kontrolo
donis kontrau^an rezulton. Por tamen ricevi la UEA-rabaton," .
						$ne_membro);
	}
	break;
  default:
	echo "nevalida membrec-kombino: [" .
	  $partopreno->datoj['tejo_membro_kontrolita'] .
	  $partopreno->datoj['tejo_membro_laudire'] . ']';
  }

  $malenkonduko = "
G^is la IJK en Liberec' post malpli ol unu semajno!
Nome de la organiza teamo salutas
Pau^lo Ebermann
(Administranto)
";

  $teksto = eotransformado($enkonduko, $kodigo);

  foreach($enhavo AS $i => $ero) {
	$teksto .= "($i) " . eotransformado($ero[0], $kodigo) . "\n";
  }
  foreach($enhavo AS $i => $ero) {
	$titolo = "($i) " . eotransformado($ero[0], $kodigo);

	$teksto .= "\n\n\n";
	$teksto .= " " . $titolo . "\n";
	$teksto .= str_repeat("=", mb_strlen($titolo, 'utf-8') + 2);
	$teksto .= "\n";
	$teksto .= eotransformado($ero[1], $kodigo);
  }

  $teksto .= "\n\n\n" . eotransformado($malenkonduko, $kodigo);

  return $teksto;
}


function kreu_informmesagxan_tekston_en($lingvo,
                                        $partoprenanto,
                                        $partopreno,
                                        $renkontigxo,
                                        $kodigo)
{
    eniru_lingvon($lingvo);
    $tabelo = kreu_konfirmilan_kontroltabelon($partoprenanto,
                                              $partopreno,
                                              $kodigo);
    $sxablono = CH_lau("~#informmesagxo-marto-sxablono", $lingvo);
    if (!$sxablono)
        return null;
    //	$sxablono = preg_replace('/\r/m', '', $sxablono);
    $sxablono = transformu_x_al_eo(utf8_al_iksoj($sxablono));

    $kotizo = new Kotizokalkulilo($partoprenanto, $partopreno, $renkontigxo,
                                  new Kotizosistemo($renkontigxo->datoj['kotizosistemo']));

    debug_echo ("<!-- kotizokalkulilo: \n" . var_export($kotizo, true) . "-->");

    $kotForm = new TekstaKotizoFormatilo($lingvo, $kodigo);
    $kotizo->tabelu_kotizon($kotForm);


    //    debug_echo( "<!-- kotizotabelo X : \n" . 
    //                $kotForm->preta_tabelo . "\n -->");

    $invitpeto = $partopreno->sercxu_invitpeton();
    if (!$invitpeto) {
      $vizoteksto = CH("~#vizo-ne-mendis");
    }
    else {
      if ($kotizo->detalolisto['pagoj']['sumo'] <= 0) {
	$vizoteksto = CH("~#vizo-ne-antauxpagis");
      }
      else if ($invitpeto->datoj['invitletero_sendenda'] == 'n') {
	$vizoteksto = CH("~#vizo-ne-sendos");
      }
      else {
	$vizoteksto = CH("~#vizo-ja-sendos");
      }
    }

    switch ($partopreno->datoj['studento']) {
    case 'j':
        $studentoteksto = CH("~#vi-estos-studento");
        break;
    case 'n':
        $studentoteksto = CH("~#vi-ne-estos-studento");
        break;
    default:
        $studentoteksto = CH("~#se-studento-bonvolu-informi");
        break;
    }

    $speciala = array("detaltabelo" => $tabelo,
                      "vizoteksto" => $vizoteksto,
                      "studentoteksto" => $studentoteksto,
                      "kotizotabelo" => $kotForm->preta_tabelo,
                      );

    debug_echo("<!-- speciala: " .
               var_export($speciala, true) . "-->");


    $datumoj = array('anto' => $partoprenanto,
                     'eno' => $partopreno,
                     'igxo' => $renkontigxo,
                     'speciala' => $speciala);

    $teksto = transformu_tekston($sxablono, $datumoj);
    $teksto = eotransformado($teksto, $kodigo);

    eliru_lingvon();
    return $teksto;
}


function kreu_unuan_konfirmilan_tekston_nova($lingvo, $partoprenanto, $partopreno, $renkontigxo, $kodigo) {
    eniru_lingvon($lingvo);
    $tabelo = kreu_konfirmilan_kontroltabelon($partoprenanto, $partopreno,
                                              $kodigo);
    $sxablono = CH_lau("~#konf1-sxablono", $lingvo);
    //	$sxablono = preg_replace('/\r/m', '', $sxablono);
    debug_echo("<!-- sxablono: [". $sxablono . "]-->");

//    echo "<!--" ;
//    echo(strtr($sxablono, array("\r" => "[CR]\r", "\n" => "[LF]\n")));
//    echo "-->";

//    echo "<!-- sxablono 1:" ;
//    var_dump ($sxablono);
//    echo "-->";
//    $sxablono = strtr($sxablono, array("\r", ""));
//    echo "<!-- sxablono 2:" ;
//    var_dump ($sxablono);
//    echo "-->";

    // kotizotabelo

    $kotizo = new Kotizokalkulilo($partoprenanto, $partopreno, $renkontigxo,
                                  new Kotizosistemo($renkontigxo->datoj['kotizosistemo']));

//     echo ("<!-- kotizokalkulilo: \n" . var_export($kotizo, true) . "-->");

    $kotForm = new TekstaKotizoFormatilo($lingvo, $kodigo);
    $kotizo->tabelu_kotizon($kotForm);


    //    debug_echo( "<!-- kotizotabelo X : \n" . 
    //                $kotForm->preta_tabelo . "\n -->");



    $speciala = array("detaltabelo" => $tabelo,
                      "kotizotabelo" => $kotForm->preta_tabelo );

    debug_echo("<!-- speciala: " .
               var_export($speciala, true) . "-->");


    $datumoj = array('anto' => $partoprenanto,
                     'eno' => $partopreno,
                     'igxo' => $renkontigxo,
                     'speciala' => $speciala);

    $teksto = transformu_tekston($sxablono, $datumoj);
//    echo "<!--" ;
//    $teksto = strtr($teksto, array("\r", ""));
//    echo(strtr($teksto, array("\r" => "[CR]\r", "\n" => "[LF]\n")));
//    echo "-->";
    eliru_lingvon();
    return $teksto;

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
              'http://ijk.esperanto.cz/dokumentoj/antaukongresilo_ijk2009.pdf',
              'informilograndeco' => "809 KB",
              'subskribo' => donu_tekston_lauxlingve('konf1-subskribo',
                                                     $lingvo,
                                                     $renkontigxo),
              );

    if ($partopreno->datoj['agxoj'] < 18) {
        $speciala['sub18'] = true;
    }
	// ne dum IJK
	$speciala['sub18'] = false;


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


