<?php

  /**
   * La noto-objekto, kaj funkcioj rilate al notoj.
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
   * Noto - tabelo "notoj".
   *
   * - ID              - identifikilo por ĉiu noto
   * - partoprenantoID - la partoprenanto, al kiu rilatas la noto
   * - kiu             - kiu skribis la noto (simpla teksto)
   * - kunKiu          - komunikpartnero (al kiu aŭ de kiu oni ricevis la informojn)
   * - tipo            - tipo de la noto: 
   *                     -  telefon
   *                     -  persone
   *                     -  letere
   *                     -  rete
   *                     -  rimarko
   * - dato            - dato de kreo de la noto (sed tamen ŝanĝebla)
   * - subjekto        - temo/titolo de la noto
   * - enhavo          - libera teksto
   * - prilaborata     - aŭ '' (ne prilaborata) aŭ 'j' (prilaborata)
   * - revido          - revidu la noton ekde tiu dato.
   *
   * @author Martin Sawitzki, Paul Ebermann 
   * @version $Id$
   * 
   */
class Noto extends Objekto
{

    /**
     * konstruilo
     */
    function Noto($id=0)
    {
        $this->Objekto($id,"notoj");
    }

    function listu_entajpantojn_por_noto() {
        $sql = datumbazdemando('entajpantoID',
                               'notoj_por_entajpantoj',
                               array("notoID = '" . $this->datoj['ID'] . "'"));
        $listo = array();
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            $listo[]= $linio['entajpantoID'];
        }
        return $listo;
    }


    /**
     * @param array $noto_por listo de la formo id => JES/NE.
     */
    function sxangxu_entajpantojn_por_noto($noto_por) {
        $jam_estas = $this->listu_entajpantojn_por_noto();
        foreach($noto_por AS $id => $estu) {
            $estas = in_array($id, $jam_estas);
            echo "<!-- id: " . $id . ", estu: " . $estu . ", estas: " . $jam_estas . "\n -->";
            if ($estu == 'JES' and !$estas) {
                // aldonu, la noto nun estas (ankaŭ) por li
                aldonu_al_datumbazo('notoj_por_entajpantoj',
                                    array('notoID' => $this->datoj['ID'],
                                          'entajpantoID' => $id));
            }
            else if ($estu == 'NE' and $estas) {
                // forigu, la noto ne plu estas por li
                forigu_el_datumbazo('notoj_por_entajpantoj',
                                    array('notoID' => $this->datoj['ID'],
                                          'entajpantoID' => $id));
            }
            else {
                // jam estas en ordo, nenio farenda.
            }
        }
    }


    /**
     * donas liston de ĉiuj entajpantoj, kun nomo kaj la informo,
     * ĉu la aktuala noto estas por ili aŭ ne.
     * @return array array de la formo
     *           entajpantoID => array(nomo, noto_por_li)
     *           Tie noto_por_li estas true, se tiu ĉi noto estas por
     *           li, alikaze false.
     */
    function listu_entajpantojn() {
        $niaj_entajpantoj = $this->listu_entajpantojn_por_noto();
        $sql = datumbazdemando(array('ID', 'nomo'),
                               'entajpantoj');
        $listo = array();
        $rez = sql_faru($sql);
        while($linio = mysql_fetch_assoc($rez)) {
            $listo[$linio['ID']] = array($linio['nomo'],
                                         in_array($linio['ID'],
                                                  $niaj_entajpantoj));
        }
        return $listo;
    }


}  // class Noto



/**
 * kreas tabelon de ĉiuj notoj de la partoprenanto kun menciita ID.
 *
 * @param int  $ppID  identigilo de  la partoprenanto.
 * @param string $kapteksto - se ne "", kreas tutan HTML-dokumenton kaj uzas
 *                      tiun tekston kiel enkondukan tekston pri la tabelo.
 *                      Alikaze nur eldonas la tabelon (por uzo ene de alia
 *                      dokumento).
 */
function listu_notojn($ppID, $kapteksto="") {

    $sercxilo = kreu_NotoTabelilon('notoj_pri_listo', false,
                                   "", 0,
                                   "n.partoprenantoID = '$ppID'");

    if ($kapteksto) {
        $sercxilo->metu_antauxtekston($kapteksto);
        $sercxilo->montru_rezulton_en_HTMLdokumento();
    }
    else {
        $sercxilo->montru_rezulton_en_HTMLtabelo();
    }

    
}   // listu_notojn



/**
 * @param string  $identigilo
 *           identigilo por la serĉilo-objekto (uzata por ordigoj).
 *            ne estu la sama kiel por aliaj serĉoj sur la sama
 *            paĝo.
 * @param boolean $kunPartoprenanto se true, ni ankaŭ montras la
 *                 nomon de la partoprenanto, pri kiu estas la noto
 *                  (kun ligo al la
 *                {@link partrezultoj.php PP-detalo-pagxo}).
 * @param string $aktualtipo 
 *        nur montru iun elekton:
 *       - remontrendaj:  neprilaboritaj, kun revidu-dato estinte.
 *       - remontrotaj:   neprilaboritaj, kun revidu-dato estonte.
 *       - neprilaboritaj: (nu, kion la nomo diras)
 *       - prilaboritaj:   (nu, kion la nomo diras)
 *       - ĉiuj:          montru ĉiujn, sendepende de
 *                             prilaboro-stato.
 * @param int $porEntajpanto se ne 0, montras nur tiajn notojn, kiuj estas
 *                 por la indikita entajpanto.
 * @param int $aldona_kondicxo se ne "", aldona kondiĉo por la elekto.
 */
function kreu_NotoTabelilon($identigilo,
                            $kunPartoprenanto,
                            $aktualTipo="",
                            $porEntajpanto = 0,
                            $aldona_kondicxo="")
{
    $sercxilo = new Sercxilo();
    $sercxilo->metu_identigilon($identigilo);
    $sumoj = array(array(array('#', '*', 'd'),
                         array('XX','A', 'c'),
                         '', '',
                         array('aliaj formatoj?', 'X', 'c')));
    
    
    $sercx_kampoj = array('n.ID' => 'notoID',
                          'n.kiu', 'n.kunKiu', 'n.tipo',
                          'n.dato', 'n.subjekto', 'n.revidu',
                          'n.prilaborata', 'n.partoprenantoID');
    $sercx_tabeloj = array('notoj' => 'n');
    $kolumnoj = array(array('kampo' => 'notoID', 'titolo' => "",
                            'tekstosxablono' => "&ndash;&gt;",
                            'ligilsxablono' => "notoj.php?notoID=XXXXX",
                            'menuidkampo' => 'partoprenantoID'),
                      array('kampo' => 'dato'), 
                      array('kampo' => 'subjekto'), 
                      array('kampo' => "kiu"), 
                      array("kunKiu","kun",'XXXXX','l','','-1'), 
                      array("tipo","tipo",'XXXXX','l','','-1'),
                      array('kampo' => 'prilaborata',
                            'titolo' => 'prilaborita?',
                            'tekstosxablono' => 'XXXXX',
                            'arangxo' => 'z',
                            'anstatauxilo'
                            => array('j'=>'<strong class="malaverto">prilaborita</strong>',
                                     '' =>'<strong class="averto">neprilaborita</strong>',
                                     'n'=>'<strong class="averto">neprilaborita</strong>')),
                      );

    $kondicxoj = array();
    if ($aldona_kondicxo) {
        $kondicxoj[]= $aldona_kondicxo;
    }

    if ($kunPartoprenanto) {
        $sercx_tabeloj['partoprenantoj'] = 'p';
        $sercx_kampoj[]= "CONCAT(personanomo, ' ', nomo) AS tuta_nomo";
        //        $sercx_kampoj[]= 'nomo';
        //        $sercx_kampoj[]= 'personanomo';
        array_splice($kolumnoj, 2, 0,
                     array( array('kampo' => 'partoprenantoID',
                                  'titolo' => "pri",
                                  'tekstosxablono' => "# XXXXX",
                                  'ligilsxablono' =>
                                  'partrezultoj.php?partoprenantoidento=XXXXX',
                                  'ligilkampo' => 'partoprenantoID',
                                  'menuidkampo' => 'partoprenantoID'
                                  ),
                           array('kampo' => 'tuta_nomo',
                                  'titolo' => "pri-nomo")));
        array_splice($sumoj[0], 2, 0, array('', ''));
        $kondicxoj[]= "p.ID = n.partoprenantoID";
    }

    switch($aktualTipo) {
    case 'remontrendaj':
        $kondicxoj[] = "prilaborata <> 'j'";
        $kondicxoj[] = "revidu <= NOW()";
        break;
    case 'remontrotaj':
        $kondicxoj[] = "prilaborata <> 'j'";
        $kondicxoj[] = "NOW() < revidu";
        break;
    case 'neprilaboritaj':
        $kondicxoj[] = "prilaborata <> 'j'";
        break;
    case 'prilaboritaj':
        $kondicxoj[] = "prilaborata = 'j'";
        break;
    case 'cxiuj':
    default:
        // neniu kondiĉoj
        break;
    }


    if ($porEntajpanto) {
        $sercx_tabeloj['notoj_por_entajpantoj'] = 'ne';
        $kondicxoj[]= "ne.entajpantoID = '$porEntajpanto'";
        $kondicxoj[]= "ne.notoID = n.ID";
    }


    $sercxilo->metu_kolumnojn($kolumnoj);
    $sercxilo->metu_sumregulojn($sumoj);
    $sercxilo->metu_datumbazdemandon($sercx_kampoj,
                                    $sercx_tabeloj,
                                    $kondicxoj);
    $sercxilo->metu_ordigon('dato', 'desc');

    return $sercxilo;
}


$GLOBALS['notomontrotipoj'] =
    array( 'remontrendaj' => array('teksto' => "jam remontrendajn notojn",),
           'remontrotaj' => array('teksto' => "notojn por poste",),
           'neprilaboritaj' => array('teksto' => "c^iujn neprilaboritajn notojn",),
           'cxiuj' => array('teksto' => "c^iujn notojn",),
           'prilaboritaj' => array('teksto' => "jam prilaboritajn notojn",));



?>
