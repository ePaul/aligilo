<?php

  /**
   * datumbaz-aliro.
   * 
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage konfiguro
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


/**
 * Aliro al la datumbazo.
 *
 * Se la konekto ne funkcias, la funkcio simple finas la programon
 * kun tauxga erarmesagxo.
 *
 * Necesas sxangxi la unuajn liniojn de la funkcio,
 * por difini la gxustajn parametrojn por la datumbazkonekto.
 * Ili estas specifaj laux {@link MODUSO}, do eblas havi diversajn
 * konfigurojn por diversaj modusoj, kaj sxalti inter ili per sxangxo
 * de nur {@link moduso.php}.
 *
 * {@source 3 3}
 * $servilo difinas la nomon de la datumbazservilo. 'localhost' tauxgas, se
 * la datumbaza servilo laboras en la sama komputilo kiel la
 * retservilo kun la programo.
 * Alikaze enmetu DNS-nomon aux IP-adreson.
 *
 * {@source 6 3}
 * $uzanto difinas la uzantonomon. Tiu uzanto havu la tauxgajn rajtojn por
 * aliri la datumbazon menciitan sube. (Dum la instalado, gxi ankaux
 * bezonas la rajton krei tabelojn.)
 *
 * {@source 9 3}
 * $pasvorto difinas la pasvorton por la datumbaz-uzanto. Jes, bedauxrinde vere
 * necesas meti la pasvorton cxi tie en la kodon ... se vi havas pli
 * bonan proponon, diru al ni.
 * (Tamen, ne poste la enmetado rekreu kaj publikigu la
 * dokumentajxon ...)
 *
 * {@source 12 3}
 * $datumbazo difinas la nomon de la datumbazo uzota de la programo.
 *
 */
function malfermu_datumaro()
{
    $servilo =array("monde" => "localhost",
                    "teste" => "localhost",
                    "hejme" => "localhost");
    $uzanto = array("monde" => "",
                    "teste" => "",
                    "hejme" => "");
    $pasvorto = array("monde" => "",
                      "teste" => "",
                      "hejme" => "");
    $datumbazo = array("monde" => "",
                       "teste" => "test",
                       "hejme" => "");

    // kaj jen la vera laboro ...

    if ($servilo[MODUSO] AND
        $uzanto[MODUSO] AND
        $pasvorto[MODUSO] AND
        $datumbazo[MODUSO])
        {
            mysql_pconnect($servilo[MODUSO],
                           $uzanto[MODUSO],
                           $pasvorto[MODUSO])
                or die("Ne eblas konekti al la datumbazo.");
            mysql_select_db($datumbazo[MODUSO])
                or die("Ne eblas konekti al datumbazo '" .
                       $datumbazo[MODUSO] . "'");

            // ni sxaltas la konekton al uzo de UTF-8, kie ajn eblas.
            mysql_query("SET NAMES 'utf8'");
        }
    else
        {
            die ("Malgxusta MODUSO ('" . MODUSO . "') aux nesuficxa " .
                 "konfiguro de datumbazaliro!");
        }

} // malfermu_datumaro()



?>
