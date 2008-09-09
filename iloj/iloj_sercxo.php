<?php

  /**
   * Iloj por la gxenerala sercxo (gxenerala_sercxo.php kaj sercxoj.php).
   *
   * Ĝenerale (preskaŭ) ĉiuj funkcioj uzas $valoroj-array-on,
   * kie enestas la serĉ-opcioj.
   *
   * Distribuitaj al la sekvaj dosieroj estas pliaj
   * funkcioj.
   *
   * @uses iloj_sercxo_html.php (kreado de formularo)
   *
   * @uses iloj_sercxo_analizo.php (analizo de la sercx-opcioj
   *          donitaj de la formularo).
   *
   * @uses iloj_sercxo_konservo.php
   *   ebligas konservadon kaj malkonservadon de
   *   serĉ-konfiguroj en/el aparta tabelo de la
   *   datumbazo.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2005-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   * @todo ie klarigu la formaton de la serĉ-opcio-array.
   */


  /**
   */

  //define('DEBUG', true);

require_once('iloj_sercxo_html.php');
require_once('iloj_sercxo_analizo.php');
require_once('iloj_sercxo_konservo.php');

/**
 * Kopias ĉiujn array-elementojn, kies
 * ŝlosiloj komencas per "sercxo_".
 *
 * $arr  - la kopiinda array. Se malplena,
 *         ni prenas $_POST anstataŭe.
 *
 * redonas: array() kun la kopiitaj ŝlosilo-valoroj-paroj.
 */
function kopiuSercxon($arr = "")
{
    if(! $arr)
        {
            $arr = $_POST;
        }
    $valoroj = array();
    foreach ($arr AS $nomo => $valoro)
        {
            if(substr($nomo,0,7) == "sercxo_")
                {
                    $valoroj[$nomo] = $valoro;
                }
        }
    return $valoroj;
}



/**
 * Uzas la serĉ-opciojn por serĉi kaj
 * montras la rezulton.
 *
 * TODO: Plibeligu la aspekton.
 * @uses Sercxilo
 */
function montruRezulton($valoroj)
{

	$sercxilo = new Sercxilo();
	$teksto = eotransform("<h3>Serc^rezulto</h3>\n");

  	list($kampoj, $inversa, $sql) = kreuSercxSQL($valoroj);

    if (empty($kampoj))
        {
            eoecho("<p>Vi elektu <em>almenau^ unu serc^indaj^o</em>" .
                   " &ndash; alikaze mi nenion povas serc^i.</p>");
            return;
        }
	
    $sercxilo->metu_sql($sql);
	$teksto .= "<p><code>$sql</code></p>";


    $kolumnoj = array();
    foreach($kampoj AS $alias)
        {
            $kol = array('kampo' => $alias,
                         'titolo' => $inversa[$alias]['titolo']);
            if($inversa[$alias]['ligo']) {
                $kol['ligilsxablono'] = $inversa[$alias]['ligo'];
            }
            $kolumnoj []= $kol;
            // 	  $kolumnoj []= array($alias, $inversa[$alias]['titolo'], 'XXXXX', 'l',
            //						  $inversa[$alias]['ligo'], '');
        }
 	$sercxilo->metu_kolumnojn($kolumnoj);


    if (/*in_array('renkNumero', $kampoj)
         and*/ in_array('nomo', $kampoj)
               and in_array('personanomo', $kampoj)
               and in_array('ID', $kampoj))
        {
            if ($valoroj['sercxo_titolo'])
                {
                    $sercxilo->metu_menutitolon("g^en. serc^o: " .
                                                $valoroj['sercxo_titolo']);
                }
            else
                {
                    $sercxilo->metu_menutitolon("rezulto de sennoma ".
                                                "g^enerala serc^o");
                }
        }
    else
        {
        }

    reset($kampoj);
    $sercxilo->metu_ordigon(current($kampoj), 'asc');
    $sercxilo->metu_sumregulojn(array(array(array('entute:', '', 'r'),
                                            array('XX', 'A', 'l'))));
    $sercxilo->metu_identigilon('gxenerala_sercxo_rezulto');

    if(strtoupper(substr($_REQUEST['tipo'], 0, 4)) == 'HTML') {
        echo $teksto;
    }
    $sercxilo->montru_rezulton_en_tipo($_REQUEST['tipo']);

    /*
    switch($_REQUEST['tipo'])
        {
        case 'HtmlTabelo':
            echo $teksto;
            $sercxilo->montru_rezulton_en_HTMLtabelo();
            return;
        case 'HtmlCSV':
            echo $teksto;
            echo "<hr />\n";
            $sercxilo->montru_rezulton_en_HTMLcsv();
            echo "<hr />\n";
            return;
        case 'Latin1CSV':
            $sercxilo->montru_rezulton_en_Latin1csv();
            exit();
        case 'Utf8CSV':
            $sercxilo->montru_rezulton_en_UTF8csv();
            exit();
        default:
            echo $teksto;
            eoecho("<p class='averto'>Mankas la parametro 'tipo'!</p>\n");
            return;
        }
    */

}

?>