<?php

  /**
   * Kelkaj funkcioj rilataj al HTML-eldono, enhavante
   * tradukeblajn tekstojn.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


function donu_mankoCSS($nomo, $subnomo) {
    if (is_array($GLOBALS['mankas']) and
        in_array($nomo.'['. $subnomo . ']',
                 $GLOBALS['mankas']))
        {
            return " class='mankas'";
        }
    else
        return "";
}


function simpla_datoelektilo($nomo, $jaro_min=1930, $jaro_max=2008)
{
	$tagolisto = array_merge(array("-#-#-"), range(1,31));

	$tagotraduklisto = array("-#-#-" => '(' . CH('~#tago') . ')');
	for ($i = 1; $i <= 31; $i++) {
		$tagotraduklisto[$i] = CH("~#x-a de", $i);
	}

    $defauxlta_tago = $_POST[$nomo]['tago'] or
        $defaulxta_tago = "-#-#-";

        
	elektilo_simpla($nomo.'[tago]', $tagotraduklisto, $defauxlta_tago,
                    "", 1, false, donu_mankoCSS($nomo, 'tago'));
    
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
                    "", 1, false, donu_mankoCSS($nomo, '~#monato'));
    

	$jarotraduklisto = array('-#-#-' => '(' . CH('~#jaro') . ')');
	for ($i = $jaro_max; $i >= $jaro_min; $i--) {
		$jarotraduklisto[$i]="$i";
	}
    $defauxlta_jaro = $_POST[$nomo]['jaro'] or
        $defaulxta_jaro = "-#-#-";

	elektilo_simpla($nomo.'[jaro]', $jarotraduklisto, $defauxlta_jaro,
                    "", 1, false, donu_mankoCSS($nomo, 'jaro'));

}



