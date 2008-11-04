<?php

  /**
   * Kelkaj funkcioj rilataj al HTML-eldono, enhavante
   * tradukeblajn tekstojn.
   *
   * @package aligilo
   * @subpackage tradukendaj_iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */

  /**
   * redonas HTML-atribut-kodon uzendan, se $nomo[$subnomo] aperas
   * en $GLOBALS['mankas'].
   *
   * @param string $nomo
   * @param string $subnomo
   */
function donu_mankoAttr($nomo, $subnomo) {
    if (is_array($GLOBALS['mankas']) and
        in_array($nomo.'['. $subnomo . ']',
                 $GLOBALS['mankas']))
        {
            return " class='mankas'";
        }
    else
        return "";
}

/**
 * kreas elektilon por dato, konsistanta el tri elektiloj por tago,
 * monato, jaro.
 *
 * @param string $nomo la ĉef-nomo de la elektilo. Ni uzas
 *                  $nomo['tago'], $nomo['monato'], $nomo['jaro'] kiel
 *                  internaj nomoj por la sub-elektiloj.
 * @param int $jaro_min la minimuma jaro por la jaro-elektilo.
 * @param int $jaro_max la maksimuma jaro por la jaro-elektilo.
 */
function simpla_datoelektilo($nomo, $jaro_max=2008, $jaro_min=1930)
{
	$tagolisto = array_merge(array("-#-#-"), range(1,31));

	$tagotraduklisto = array("-#-#-" => '(' . CH('~#tago') . ')');
	for ($i = 1; $i <= 31; $i++) {
		$tagotraduklisto[$i] = CH("~#x-a de", $i);
	}

    $defauxlta_tago = $_POST[$nomo]['tago'] or
        $defaulxta_tago = "-#-#-";

        
	elektilo_simpla($nomo.'[tago]', $tagotraduklisto, $defauxlta_tago,
                    "", 1, false, donu_mankoAttr($nomo, 'tago'));
    
    $monatotraduklisto = array("-#-#-" => '(' . CH('~#monato') . ')',
                               1  => CH('~#januaro'),
                               2  => CH('~#februaro'),
                               3  => CH('~#marto'),
                               4  => CH('~#aprilo'),
                               5  => CH('~#majo'),
                               6  => CH('~#junio'),
                               7  => CH('~#julio'),
                               8  => CH('~#auxgusto'),
                               9  => CH('~#septembro'),
                               10 => CH('~#oktobro'),
                               11 => CH('~#novembro'),
                               12 => CH('~#decembro'),
                               );

    $defauxlta_monato = $_POST[$nomo]['monato'] or
        $defaulxta_monato = "-#-#-";
 
    elektilo_simpla($nomo.'[monato]',  $monatotraduklisto,  $defauxlta_monato,
                    "", 1, false, donu_mankoAttr($nomo, '~#monato'));
    

	$jarotraduklisto = array('-#-#-' => '(' . CH('~#jaro') . ')');
	for ($i = $jaro_max; $i >= $jaro_min; $i--) {
		$jarotraduklisto[$i]="$i";
	}
    $defauxlta_jaro = $_POST[$nomo]['jaro'] or
        $defaulxta_jaro = "-#-#-";

	elektilo_simpla($nomo.'[jaro]', $jarotraduklisto, $defauxlta_jaro,
                    "", 1, false, donu_mankoAttr($nomo, 'jaro'));

}



