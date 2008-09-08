<?php

  /**
   * Iloj, kiuj analizas la $valoroj-strukturon kreita de la formularo
   * en {@link gxenerala_sercxo.php} kaj kreas pli taŭgajn strukturojn
   * el ĝi.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2005-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   * @todo ie metu difinon de la formato de $valoroj.
   */





  /**
   * Kreas SQL-ordonon el la $valoroj-listo.
   *
   * @param array $valoroj la senditaĵo de la formularo de
   *    {@link gxenerala_sercxo.php}.
   * @return array  array($kampoj, $informoj, $sql), kie
   *      - $kampoj estas array() de kampoj montrenda en la rezulto,
   *          en la formo    tabelo.kamponomo => alias,
   *           en la formo uzebla de datumbazdemando().
   *          - $informoj estas array() de la formo<code>
   *               alias => array('kampo' => tabelo.kamponomo,
   *                             'titolo' => titolo)
   *             </code>
   *      - $sql estas la kreita SQL-esprimo (sen ordigo).
   * @uses kreuKondicxojn()
   * @uses kreuKampoliston()
   * @uses certiguCxiujnKonektojn()
   * @uses kreuKonektKondicxojn()
   * @uses datumbazdemando()
   */
function kreuSercxSQL($valoroj)
{

    if (DEBUG) {
        echo "<!-- valoroj: " . var_export($valoroj, true) . "-->";
    }

    /*
     * TODO: metu tiun liston aliloken, ekzemple la instalilo povus ĝin krei.
     */
    $cxiujtabeloj = array("renkontigxo",
                          "cxambroj",
                          "litonoktoj",
                          "partoprenoj",
                          "partoprenantoj",
                          "invitpetoj",
                          "rabatoj",
                          "pagoj",
                          "notoj",
                          "landoj");
    $uzatajtabeloj = array();
    foreach($cxiujtabeloj AS $tabelnomo)
        {
            if($valoroj['sercxo_tabelo_'.$tabelnomo.'_uzu'] == 'JES')
                {
                    $uzatajtabeloj[]= $tabelnomo;
                }
        }
    $kondicxoj = kreuKondicxojn($uzatajtabeloj, $valoroj);
    list($kampoj, $inversa) = kreuKampoliston($uzatajtabeloj, $valoroj);

    if (empty($kampoj))
        return array("", "", "");
    certiguCxiujnKonektojn($uzatajtabeloj);
    $kondicxoj = array_merge($kondicxoj, kreuKonektKondicxojn($uzatajtabeloj));

    if (DEBUG)
        {
            echo "<!--";
            echo "\n kampoj: ";
            var_export($kampoj);
            echo "\n kondiĉoj: ";
            var_export($kondicxoj);
            echo "\n uzatajtabeloj: ";
            var_export($uzatajtabeloj);
            echo "-->";
        }
  

    return array( $kampoj,
                  $inversa,
                  datumbazdemando( $kampoj,
                                   $uzatajtabeloj,
                                   $kondicxoj)
                  );
}


/**
 * kreas liston de kampoj aperendaj en la rezulto, kun aldonaj informoj.
 * @param array $uzatajtabeloj listo de la tabeloj, kies montrado estis
 *    mendita. Nur el tiuj tabeloj ni akceptos kampojn.
 * @param array $valoroj la la senditaĵo de la formularo de
 *    {@link gxenerala_sercxo.php}.
 * @return array <val>array($kampoj, $informoj)</val>, kie
 *          - $kampoj estas array() de la kamponomoj en la formo
 *             <val>tabelo.kamponomo => alias</val> (por uzo de
 *            {@link datumbazdemando()})
 *          - $informoj estas array() de la formo<code>
 *               alias => array('kampo' => tabelo.kamponomo,
 *                             'titolo' => titolo)
 *             </code>
 */
function kreuKampoliston($uzatajtabeloj, $valoroj)
{
    $listo = array();
    $inversa = array();
    foreach($valoroj AS $varnomo => $montru)
        {
            $rezultoj = array();
            if($montru == 'JES' and
               preg_match('/^sercxo_([^_]+)_(.+)_montru$/',
                          $varnomo, $rezultoj))
                {
                    $tabelnomo = $rezultoj[1];
                    $kamponomo = $rezultoj[2];
                    if (DEBUG)
                        {
                            echo "<!--  montras: " . $varnomo . "-->\n";
                        }
                    if (in_array($tabelnomo, $uzatajtabeloj))
                        {
                            // intence nur =.
                            ($alias = $valoroj["sercxo_{$tabelnomo}_{$kamponomo}_alias"])
                                or ($alias = $kamponomo); // TODO: eventuell besser $tabelnomo.$kamponomo ?

                            $listo = array_merge($listo,
                                                 array($tabelnomo .'.'.$kamponomo => $alias));
                            $inversa[$alias] = array('kampo' => $tabelnomo .'.'.$kamponomo);
                            if($valoroj["sercxo_{$tabelnomo}_{$kamponomo}_ligo"])
                                {
                                    $inversa[$alias]['ligo'] =
                                        $valoroj["sercxo_{$tabelnomo}_{$kamponomo}_ligo"];  
                                }
                            if($valoroj["sercxo_{$tabelnomo}_{$kamponomo}_titolo"])
                                {
                                    echo "<!-- valoroj[sercxo_{$tabelnomo}_{$kamponomo}_titolo]: " . $valoroj["sercxo_{$tabelnomo}_{$kamponomo}_titolo"] . "-->";
                                    $inversa[$alias]['titolo'] =
                                        $valoroj["sercxo_{$tabelnomo}_{$kamponomo}_titolo"];  
                                }
                            else
                                {
                                    $inversa[$alias]['titolo'] = $alias;
                                }

                        }
                    else
                        {
                            if (DEBUG)
                                {
                                    echo "<!-- nicht im Array: ";
                                    var_export($tabelnomo); echo "\n";
                                    var_export($uzatajtabeloj);
                                    echo "-->";
                                }
                        }
                } // if match
        } // foreach
    return array($listo, $inversa); 
}  // kreuKampoliston

/**
 * kreas liston de SQL-kondiĉoj el la $valoroj
 * (nur por kampoj de la uzataj tabeloj).
 *
 * @param array $uzatajtabeloj listo de la tabeloj, kies montrado estis
 *    mendita. Nur el tiuj tabeloj ni akceptos kampojn.
 * @param array $valoroj la la senditaĵo de la formularo de
 *    {@link gxenerala_sercxo.php}.
 *
 * @return array SQL-kondiĉoj por uzo de {@link datumbazdemando()}.
 */
function kreuKondicxojn($uzatajtabeloj, $valoroj)
{
    $kondicxoj = array();
    foreach($valoroj AS $varnomo => $jesNe)
        {
            $rezultoj = array();
            if('JES' == $jesNe and
               preg_match('/^sercxo_([^_]+)_(.+)_estasKriterio$/', $varnomo, $rezultoj))
                {
                    $tabelnomo = $rezultoj[1];
                    $kamponomo = $rezultoj[2];
                    if (!in_array($tabelnomo, $uzatajtabeloj))
                        {
                            // ni ne atentas kondiĉojn en neuzataj tabeloj
                            continue;
                        }
                    $tipo = $valoroj["sercxo_{$tabelnomo}_{$kamponomo}_tipo"];
                    $valoro = $valoroj["sercxo_{$tabelnomo}_{$kamponomo}_valoro"];
                    $nomo = $tabelnomo .".".$kamponomo;
                    switch($tipo)
                        {
                        case 'sama':
                            $kondicxoj []= ($nomo . " = '" . $valoro . "'");
                            break;
                        case 'malpli':
                            $kondicxoj []= ($nomo . " < '" . $valoro . "'");
                            break;
                        case 'pli':
                            $kondicxoj []= ($nomo . " > '" . $valoro . "'");
                            break;
                        case 'inter':
                            list($unua, $dua) = split('/', $valoro, 2);
                            $kondicxoj []= ("'" . $unua . "' < " . $nomo);
                            $kondicxoj []= ($nomo . " < '" . $dua . "'");
                            break;
                        case 'LIKE':
                            $kondicxoj []= ($nomo . " LIKE '" . $valoro . "'");
                            break;
                        case 'REGEXP':
                            $kondicxoj []= ($nomo . " RLIKE '" . $valoro . "'");
                            break;
                        case 'parto':
                            $kondicxoj []= ($nomo . " LIKE '%" . $valoro . "%'");
                            break;
                        case 'plena':
                            $kondicxoj []= ($nomo . " != ''");
                        case 'unu_el':
                            {
                                $elektolisto = $valoroj["sercxo_{$tabelnomo}_{$kamponomo}_elekto"];
                                if(is_null($elektolisto))
                                    {
                                        // nenio elektita -> nenio trovebla ...
                                        $kondicxo[] .= "1 = 0 /* $nomo: unu el neniuj */";
                                        break;
                                    }
                                if(!is_array($elektolisto))
                                    {
                                        $elektolisto = array($elektolisto);
                                    }
                                $variantoj = array();
                                foreach($elektolisto AS $elekto)
                                    {
                                        $variantoj[] .= "{$nomo} = '{$elekto}'";
                                    }
                                $kondicxoj[] = "/* unu el pluraj */ (" . join(") OR (", $variantoj) . ")";
                            }
                            break;
                        default:
                            // ne okazu!
                            darf_nicht_sein();
                        }  // switch
                }   // if(match)
        }  // foreach
    return $kondicxoj;
}   // kreuKondiĉojn

/**
 * Kreas ĉiujn necesajn JOIN-kondiĉojn kaj
 * redonas array() da ili.
 *
 * @param array $uzatajtabeloj listo de la tabeloj, kies montrado estis
 *    mendita. Nur tiujn tabeloj ni konektos per kondiĉoj.
 *
 * @return array SQL-kondiĉoj por uzo de {@link datumbazdemando()}.
 * @todo ankaŭ tiu informoj povus esti en aparta dosiero
 *       (eble kreita de la instalilo).
 * @uses kreuKonekton()
 */
function kreuKonektKondicxojn($uzatajtabeloj)
{
    $kondicxoj = array();
    kreuKonekton($kondicxoj, $uzatajtabeloj,
                 "notoj", "partoprenantoID", "partoprenantoj", "ID");
    kreuKonekton($kondicxoj, $uzatajtabeloj,
                 "partoprenoj", "partoprenantoID", "partoprenantoj", "ID");
    kreuKonekton($kondicxoj, $uzatajtabeloj,
                 "partoprenantoj", "lando", "landoj", "ID");
    kreuKonekton($kondicxoj, $uzatajtabeloj,
                 "pagoj", "partoprenoID", "partoprenoj", "ID");
    kreuKonekton($kondicxoj, $uzatajtabeloj,
                 "rabatoj", "partoprenoID", "partoprenoj", "ID");
    kreuKonekton($kondicxoj, $uzatajtabeloj,
                 "litonoktoj", "partopreno", "partoprenoj", "ID");
    kreuKonekton($kondicxoj, $uzatajtabeloj,
                 "litonoktoj", "cxambro", "cxambroj", "ID");
    kreuKonekton($kondicxoj, $uzatajtabeloj,
                 "partoprenoj", "renkontigxoID", "renkontigxo", "ID");
    kreuKonekton($kondicxoj, $uzatajtabeloj,
                 "partoprenoj", "ID", "invitpetoj", "ID");

    // nur konektu 'cxambro' rekte al 'renkontigxo', se ankoraŭ ne estas
    // konekto per la partopreno
    if(!in_array("partoprenoj", $uzatajtabeloj))
        {
            kreuKonekton($kondicxoj, $uzatajtabeloj,
                         "cxambroj", "renkontigxo", "renkontigxo", "ID");
        }

    
    if (DEBUG) {
    	echo "<!-- kondicxoj: \n";
    	var_export($kondicxoj);
    	echo "-->\n";
    }
    return $kondicxoj;
}

/**
 * kreas la JOIN-kondiĉon por konekti du tabelojn,
 *  se necesas (t.e. se ili ambaŭ estas uzataj).
 *
 * @param array $kondicxoj listo de SQL-kondiĉoj. Tie ni aldonos unu
 *     elementon, se necesas.
 * @param array $uzatajtabeloj listo de la tabeloj, kies montrado estis
 *    mendita. Ni nur konektas la tabelojn, se ili ambaŭ aperas ĉi tie.
 * @param string $tabelo1 la nomo de la unua tabelo.
 * @param string $kampo1  la nomo de la tabelkampo en $tabelo1, kiun ni uzas
 *          por la konekto.
 * @param string $tabelo2 la nomo de la dua tabelo.
 * @param string $kampo2
 *
 */
function kreuKonekton(&$kondicxoj, $uzatajtabeloj, $tabelo1, $kampo1, $tabelo2, $kampo2)
{
    debug_echo( "<!-- kreuKonekton($tabelo1, $kampo1, $tabelo2, $kampo2) -->\n");
    if(in_array($tabelo1, $uzatajtabeloj) and in_array($tabelo2, $uzatajtabeloj))
        {
            $aldono = ($tabelo1 . "." . $kampo1 . " = " . $tabelo2 . "." . $kampo2);
            debug_echo( "<!--   :: $aldono  -->\n");
            $kondicxoj []= $aldono;
        }
  
}


/*
 * Certigas, ke por JOIN-itaj tabeloj ankaŭ tiuj tabeloj
 * ĉeestos, kiuj estas inter tiuj tabeloj.
 *
 * @param array $uzatajtabeloj listo de la tabeloj, kies montrado estis
 *    mendita. Ni tie aldonos ĉiujn tabelojn, kiuj necesas por konekti
 *    la jam enhavitajn.
 * @uses certiguKonekton()
 * @todo tiuj listoj estu eble en iu dosiero ... eble
 *     kreita de la instalilo.
 *
 */
function certiguCxiujnKonektojn(&$uzatajtabeloj)
{
    certiguKonekton($uzatajtabeloj, "notoj", "landoj",
                    "partoprenantoj");
    certiguKonekton($uzatajtabeloj, "notoj", "partoprenoj",
                    "partoprenantoj");
    certiguKonekton($uzatajtabeloj, "notoj", "pagoj",
                    array("partoprenantoj", "partoprenoj"));
    certiguKonekton($uzatajtabeloj, "notoj", "rabatoj",
                    array("partoprenantoj", "partoprenoj"));
    certiguKonekton($uzatajtabeloj, "notoj", "renkontigxo",
                    array("partoprenantoj", "partoprenoj"));
    certiguKonekton($uzatajtabeloj, "notoj", "litonoktoj",
                    array("partoprenantoj", "partoprenoj"));
    certiguKonekton($uzatajtabeloj, "notoj", "cxambroj",
                    array("partoprenantoj", "partoprenoj", "litonoktoj"));
    certiguKonekton($uzatajtabeloj, "landoj", "partoprenoj",
                    "partoprenantoj");
    certiguKonekton($uzatajtabeloj, "landoj", "pagoj",
                    array("partoprenantoj", "partoprenoj"));
    certiguKonekton($uzatajtabeloj, "landoj", "rabatoj",
                    array("partoprenantoj", "partoprenoj"));
    certiguKonekton($uzatajtabeloj, "landoj", "renkontigxo",
                    array("partoprenantoj", "partoprenoj"));
    certiguKonekton($uzatajtabeloj, "landoj", "litonoktoj",
                    array("partoprenantoj", "partoprenoj"));
    certiguKonekton($uzatajtabeloj, "landoj", "cxambroj",
                    array("partoprenantoj", "partoprenoj", "litonoktoj"));
    certiguKonekton($uzatajtabeloj, "partoprenantoj", "rabatoj",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "partoprenantoj", "pagoj",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "partoprenantoj", "renkontigxo",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "partoprenantoj", "litonoktoj",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "partoprenantoj", "cxambroj",
                    array("litonoktoj", "partoprenoj"));

    certiguKonekton($uzatajtabeloj, "pagoj", "renkontigxo",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "pagoj", "rabatoj",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "pagoj", "litonoktoj",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "pagoj", "cxambroj",
                    array("litonoktoj", "partoprenoj"));

    certiguKonekton($uzatajtabeloj, "rabatoj", "renkontigxo",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "rabatoj", "litonoktoj",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "rabatoj", "cxambroj",
                    array("litonoktoj", "partoprenoj"));
  
    certiguKonekton($uzatajtabeloj, "partoprenoj", "cxambroj",
                    "litonoktoj");

    // ĉiuj konektoj al invitpetoj:
    certiguKonekton($uzatajtabeloj, "invitpetoj", "partoprenantoj",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "invitpetoj", "renkontigxo",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "invitpetoj", "litonoktoj",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "invitpetoj", "rabatoj",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "invitpetoj", "pagoj",
                    "partoprenoj");
    certiguKonekton($uzatajtabeloj, "invitpetoj", "landoj",
                    array("partoprenoj", "partoprenantoj"));
    certiguKonekton($uzatajtabeloj, "invitpetoj", "notoj",
                    array("partoprenoj", "partoprenantoj"));
    certiguKonekton($uzatajtabeloj, "invitpetoj", "cxambroj",
                    array("litonoktoj", "partoprenoj"));

    // konektu "litonoktoj" kaj "renkontigxoj" per "cxambroj",
    // sed nur, se ili ankoraŭ ne estas konektitaj per "renkontigxo".
    if(in_array("litonoktoj", $uzatajtabeloj) and 
       in_array("renkontigxo", $uzatajtabeloj) and
       ! in_array("partoprenoj", $uzatajtabeloj) and
       ! in_array("cxambroj", $uzatajtabeloj))
        {
            $uzatajtabeloj []= "cxambroj";
        }
}

/**
 * Aldonas la elementojn de $per al $uzatajtabeloj,
 * se $de kaj $al jam enestas.
 *
 * @param array $uzatajtabeloj la tabeloj menditaj, kaj uzotaj por
 *      la datumbazdemando. Ni tie eble aldonos novajn.
 * @param string $de la unua el du konektendaj tabeloj.
 * @param string $gxis la dua el du konektendaj tabeloj.
 * @param string|array la tabeloj, kiuj estas aldonendaj al
 *                      $uzatajtabeloj.
 */
function certiguKonekton(&$uzatajtabeloj, $de, $al, $per)
{
    debug_echo( "<!-- certiguKonekton(..., $de, $al, $per); -->\n");
    if(in_array($de, $uzatajtabeloj) and in_array($al, $uzatajtabeloj))
        {
      
            if(!is_array($per))
                {
                    $per = array($per);
                }
            foreach($per AS $nomo)
                {
                    //        echo "\n   aldonante: $nomo";
                    if (!in_array($nomo, $uzatajtabeloj))
                        {
                            $uzatajtabeloj[]= $nomo;
                        }
                }
        }
}






?>