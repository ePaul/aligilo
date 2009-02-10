<?php

  /**
   * Detaloj de pagoj (monfluoj), (individuaj) krompagoj (pagendajxoj)
   * kaj (individuaj) rabatoj.
   *
   * Tiuj parametroj estas uzenda por voko de ekstero:
   * - $_GET['id'] - identigilo de la objekto.
   * - $_GET['klaso'] - unu el
   *     - pago
   *     - krom  (krompago)
   *     - rabato.
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki
   *            2004-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   * debug-moduso.
   */
  // define("DEBUG", true);


  /**
   * la kutimaj iloj.
   */
require_once ('iloj/iloj.php');
malfermu_datumaro();

session_start();




$GLOBALS['pd_klasonomoj'] =
    $GLOBALS['pp_klasonomoj'];

$GLOBALS['pd_titoloj'] =
    array('pago' => "pago",
          'rabato' => "rabato",
          'krom' => "krompago");

$GLOBALS['pd_tiponomo'] =
    array('pago' => "pagotipo",
          'rabato' => "rabatkialo",
          'krom' => "krompago-kialo");

$GLOBALS['pd_rajtoj'] =
    array('pago' => 'mono',
          'rabato' => 'rabati',
          'krom' => 'rabati');

$GLOBALS['pd_valuto_elektebla'] =
    array('pago' => true,
          'rabato' => false,
          'krom' => false);


echo ("<!-- klaso: " . $_REQUEST['klaso'] . "-->");

kontrolu_rajton($GLOBALS['pd_rajtoj'][$_REQUEST['klaso']]);



HtmlKapo();


function donu_objekton($tipo, $id) {
    $klaso = $GLOBALS['pd_klasonomoj'][$tipo];
    $objekto = new $klaso($id);
    if (!($objekto->datoj['ID']))
        {
            // nova pago/rabato/...
            $objekto->datoj['partoprenoID'] =
                $_SESSION['partopreno']->datoj['ID'];
            $objekto->datoj['dato'] = date('Y-m-d');
            $objekto->datoj['valuto'] = CXEFA_VALUTO;
        }
    return $objekto;
}



/**
 * savas objekton kaj redonas gxin.
 */
function savu_pagon($tipo) {
    $obj = donu_objekton($tipo, $_REQUEST['ID']);
    $obj->kopiu();
    $obj->datoj['entajpantoID'] =
        $_SESSION['kkren']['entajpanto'];
    echo "<!-- " . var_export($obj, true) . "-->";
    if (kontrolu_daton($obj->datoj['dato']) //and
        //        ! isempty($objekto->datoj['valuto'])
        ) {
        if ($_REQUEST['ID']) {
            $obj->skribu();
            eoecho("<p>S^ang^is " .
                   $GLOBALS['pd_titoloj'][$obj->klaso] . "n #" .
                   $obj->datoj['ID'] .".</p>\n");
        }
        else {
            $obj->skribu_kreante();
            eoecho("<p>Aldonis " .
                   $GLOBALS['pd_titoloj'][$obj->klaso] . "n #" .
                   $obj->datoj['ID'] .".</p>\n");
        }
    }
    else {
        erareldono("La dato estu en formato <em>jaro-monato-tago</em> (ISO-8601). Bonvolu korekti!");
    }
    
    return $obj;
}


/**
 * montras redaktilon por pseuxropago-objekto.
 *
 * @param Pseuxdopago $objekto
 */
function montru_pagoredaktilon($objekto)
{
    echo( "<!-- montru_pagoredaktilon(" . var_export($objekto, true) . ")-->");

    $tipo = $objekto->klaso;

    $partopreno = new Partopreno($objekto->datoj['partoprenoID']);
    $partoprenanto = new Partoprenanto($partoprenanto->datoj['partoprenantoID']);
    $ppRenk = new Renkontigxo($partopreno->datoj['renkontigxoID']);


    echo "<form action='pago-detaloj.php?klaso=" .$tipo . "' method='POST'>\n";
                                       
    if ($objekto->datoj['ID']) {
        eoecho( "<h2>Redakto de " . $GLOBALS['pd_titoloj'][$tipo] . " #" .
                $objekto->datoj['ID'] . "</h2>\n");
    }
    else {
        eoecho("<h2>Kreo de nova " . $GLOBALS['pd_titoloj'][$tipo]. "</h2>\n");
    }

    echo "<table>\n";
    tabela_kasxilo("ID", 'ID', $objekto->datoj['ID']);
    tabela_kasxilo("partopreno-ID", 'partoprenoID',
                   $objekto->datoj['partoprenoID']);
    
    tabelentajpejo ("alvenodato",'dato',$objekto->datoj['dato'],
                    11," (jaro-monato-tago)", "",date("Y-m-d"));
    
    
    if ($GLOBALS['pd_valuto_elektebla'][$tipo]) {
        tabela_elektolisto_el_konfiguroj("valuto", 'valuto',
                                         'valuto', $objekto->datoj['valuto'],
                                         $ppRenk);
        $postkvanto = "";
    }
    else {
        // TODO: uzu la tekston
        tabela_kasxilo("valuto", 'valuto',
                       $objekto->datoj['valuto']);
        $postkvanto =  $objekto->datoj['valuto'];
    }
    tabelentajpejo ("kvanto",'kvanto',$objekto->datoj['kvanto'], 7,
                    $postkvanto);


    $panto = new Entajpanto($objekto->datoj['entajpantoID']);
    
    tabela_montrilo('entajpanto', $panto->datoj['nomo']);
    
    tabela_elektolisto_el_konfiguroj($GLOBALS['td_tiponomo'][$tipo], "tipo",
                                     $tipo ."tipo", $objekto->datoj['tipo'],
                                     $ppRenk);
                                       
    echo "</table>\n";

    echo "<p>";
    if ($objekto->datoj['ID']) {
        butono("sxangxu", "S^ang^u!");
        ligu("partrezultoj.php","Reen");
    }
    else {
        butono("kreu", "Enmetu!");
        ligu("partrezultoj.php", "Reen");
    }
    echo "</p>\n";
    echo "</form>\n";
} // montru_pagoredaktilon

unset($objekto);

if ($_POST['sendu']) {
    $objekto = savu_pagon($_REQUEST['klaso']);
 }
 else {
    $objekto = donu_objekton($_REQUEST['klaso'],
                             $_REQUEST['id']);
 }

montru_pagoredaktilon($objekto);

HtmlFino();
exit();

