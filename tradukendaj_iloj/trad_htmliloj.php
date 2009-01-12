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
                    "", 1, false, donu_mankoAttr($nomo, 'monato'));
    

	$jarotraduklisto = array('-#-#-' => '(' . CH('~#jaro') . ')');
	for ($i = $jaro_max; $i >= $jaro_min; $i--) {
		$jarotraduklisto[$i]="$i";
	}
    $defauxlta_jaro = $_POST[$nomo]['jaro'] or
        $defaulxta_jaro = "-#-#-";

	elektilo_simpla($nomo.'[jaro]', $jarotraduklisto, $defauxlta_jaro,
                    "", 1, false, donu_mankoAttr($nomo, 'jaro'));

}




$GLOBALS['mangxotipoj'] = array('M' => CH("~#matenmangxo"),
                                'T' => CH("~#tagmangxo"),
                                'V' => CH("~#vespermangxo"));


/**
 * kreas Javascript-kodon por la menduCxiujn-funkcio
 * uzata de {@link montru_mangxmendilon()}.
 */
function kreu_mangxmendilan_JS()
{
    $malmendu_teksto = CH("~#malmendu-cxiujn");
    $mendu_teksto = CH("~#mendu-cxiujn");
    
?>
/**
 * mendas aux malmendas cxiujn mangxojn de certa tipo,
 * depende de tio, cxu la referenca nodo estas aktiva.
 */
function menduCxiujn(referenco, tipo) {
  var elektita;
  //  alert("referenco: " + referenco);
  var ligo = document.getElementById("cxiomendoligo-" + tipo);
  //  alert("ligo: " + ligo);


  if (ligo.className == 'malmendu-cxiujn') {
      elektita = false;
      // por la sekva fojo
      ligo.className = 'mendu-cxiujn';
      ligo.firstChild.data = "<?php eoecho($mendu_teksto); ?>";
  }
  else {
      elektita = true;
      // por la sekva fojo
      ligo.className = 'malmendu-cxiujn';
      ligo.firstChild.data = "<?php eoecho($malmendu_teksto); ?>";
  }



  var tabellinio = document.getElementById("mendillinio-" + tipo);
  var inputListo = tabellinio.getElementsByTagName("input");
  for (var i = 0 ; i < inputListo.length; i++) {
      if (inputListo[i].type == 'checkbox') {
          inputListo[i].checked = elektita;
      }
  }
  
}
<?php
}

/**
 * montras mendilon por mangxoj.
 *
 * @param Partopreno|null $partopreno
 *                               la partopreno-objekto, por kiu ni
 *                               montru la mendilon. Se mankas, tiam
 *                               montru mendilon por nova partopreno.
 */
function montru_mangxomendilon($partopreno=null)
{

    echo "<!-- montru_mangxomendilon(" . var_export($partopreno, true).
        ")\n-->";
    
    $malmendu_teksto = CH("~#malmendu-cxiujn");
    $mendu_teksto = CH("~#mendu-cxiujn");

    $mangxolisto = listu_eblajn_mangxojn($partopreno);

    $tabelo = array();
    $tagolisto = array();
    foreach($mangxolisto AS $mangxoID) {
        $mtempo = new Mangxtempo($mangxoID);
        if ($partopreno) {
            $mendita = cxuMangxas($partopreno->datoj['ID'], $mangxoID);
        }
        else {
            if (isset($_REQUEST['mangxmendo'])) {
                $mendita =
                    jesne_al_boolean($_REQUEST['mangxmendo'][$mangxoID]);
            }
            else {
                $mendita = true;
            }
        }
        $tabelo[$mtempo->datoj['mangxotipo']][$mtempo->datoj['dato']] =
            array('mtempo' => $mtempo,
                  'mendita' => $mendita);
        $tagolisto[] = $mtempo->datoj['dato'];
    }
    $tagolisto = array_values(array_unique($tagolisto));

    ksort($tabelo, SORT_STRING);
    
    echo "<table class='mangxmendilo'>\n";
    echo "  <tr class='mangxmendilo-datoj'>\n    <td/>\n";
    foreach($tagolisto AS $dato) {
        echo "    <th>" . $dato . "</th>\n";
    }
    echo "  </tr>\n";

    foreach($tabelo AS $tipo => $tabellinio) {
        eoecho("  <tr id='mendillinio-". $tipo. "'>\n    <th>" . $GLOBALS['mangxotipoj'][$tipo] .
               "</th>\n");
        $cxiu_elektita = true;
        foreach($tagolisto AS $dato) {
            $ero = $tabellinio[$dato];
            if ($ero) {
                echo "    <td>";
                jes_ne_bokso("mangxmendo[".$ero['mtempo']->datoj['ID']."]",
                             $ero['mendita']);
                $cxiu_elektita = $cxiu_elektita && (boolean) $ero['mendita'];
                //                eoecho($dato);
                echo "</td>\n";
            } else {
                echo "    <td/>\n";
            }
        }
        echo("    <td class='cxiuj-ligo'>");
        ligu("javascript:menduCxiujn(this, '".$tipo."')",
             ($cxiu_elektita? $malmendu_teksto : $mendu_teksto), '',
             array('class' => $cxiu_elektita ?
                   'malmendu-cxiujn' : 'mendu-cxiujn',
                   'id' => 'cxiomendoligo-' . $tipo));
        echo "</td>\n";
//         jes_ne_bokso("cxiuj-mangxoj-" . $tipo,
//                      false, "menduCxiujn(this, '".$tipo."')");
//         eoecho ("c^iuj </td>\n");
        echo "  </tr>\n";
    }
    echo "</table>\n";

} // montru_mangxomendilon()


class DummyPiednotilo extends Piednotilo {

    function kreu_piednoton() {}
}


function formatu_aligxintoliston($lingvo, $ordigo, $renkontigxoID)
{
    require_once($GLOBALS['prafix'].'/iloj/iloj_listo.php');


    list($listo, $nombro, $landoj) =
        kreu_aligxintoliston($renkontigxoID, $ordigo, $lingvo);

    eniru_lingvon($lingvo);
    eniru_dosieron();
    metu_piednotsistemon(new DummyPiednotilo());


    echo "<p>" . CH("estas-homoj-el-landoj",
                    $nombro, $landoj, count($listo)) . "</p>";
    

    echo "<table>\n" .
        "  <tr><th>" . CH("persona") .
        "</th><th>". CH("sxildnomo") .
        "</th><th>" . CH("familia") .
        "</th><th>" . CH("lando") .
        "</th><th>" . CH("urbo") .
        "</th></tr>\n";
    foreach($listo AS $linio) {
        echo "<tr>";
        eoecho( "<td>" . $linio['personanomo'] . "</td>");
        eoecho( "<td>" . $linio['sxildnomo'] . "</td>");
        eoecho( "<td>" . $linio['fam'] . "</td>");
        eoecho( "<td>" . $linio['landonomo'] . "</td>");
        eoecho( "<td>" . $linio['urbo'] . "</td>");
        echo "</tr>";
    }
    echo "</table>";

    eliru_dosieron();
    eliru_lingvon($lingvo);

}
