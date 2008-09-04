<?php

  /**
   * Instalilo por la programo.
   *
   * (ankoraux en la plana fazo.)
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage specialaj_skriptoj
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   * kreas novan datumbaztabelon.
   *
   * @param string $tabelnomo
   * @param array $kamporeguloj  array() el array(), pri kies
   *        formato vidu cxe {@link donu_kampo_sql()}.
   * @param array $sxlosiloj  listo de sxlosiloj. De la formo
   *           nomo => detaloj,
   *          kie 'nomo =>' povas esti forlasita (por lasi la sistemon
   *                                           mem krei la nomon).
   *          La nomo 'primary' indikas la cxefan sxlosilon.
   *          Se tiu ne estas donita, ni kreas sxlosilon el "(`ID`)".
   *
   *          detaloj povas esti cxeno (nomo de kolumno)
   *          aux array de tiaj nomoj. En la lasta kazo, se
   *               detaloj[0] == 'index', gxi estos forprenita
   *          kaj indikas, ke ni havas ne-unikan indekson.
   * @param string $komento
   */
function kreu_tabelon($tabelnomo, $kampoj, $sxlosiloj, $komento="") {
    $sql = "CREATE TABLE `" . traduku_tabelnomon($tabelnomo) . "` (\n  ";
    $sqlkampoj = array();
    foreach ($kampoj AS $kampopriskribo) {
        $sqlkampoj[]= donu_kampo_sql($kampopriskribo);
    }


    $primary = "ID";



    foreach($sxlosiloj AS $nomo => $valoro) {
        if ($nomo == 'primary') {
            if (is_array($valoro)) {
                $primary = implode('`, `', $valoro);
            }
            else {
                $primary = $valoro;
            }
        } else {
            $unique = true;
            if (is_array($valoro)) {
                if ($valoro[0] == 'index') {
                    $unique = false;
                    array_shift($valoro);
                }
                $valoro = implode('`, `', $valoro);
            }
            $sqlkampoj[]=
                ($unique ? "UNIQUE KEY " : "KEY ") .
                (is_int($nomo) ?'' : "`$nomo` ") .
                "(`" . $valoro ."`)";
        }
    }

    $sqlkampoj[] = "PRIMARY KEY (`$primary`)";

    $sql .= implode(",\n  ", $sqlkampoj);
    $sql .= "\n) ";
    $sql .= "DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci ";
    if ($komento) {
        $sql .= "COMMENT='$komento'";
    }
    $sql .= ";\n";

    // TODO


    echo  $sql;
}


/**
 * kreas SQL-klauxzon por unuopa kampo.
 * @param array $priskribo la kolumno-specifikajxo,
 *  en la formo <code>
 *    array( kamponomo, tipo [=> grandeco ], ceterajxoj ... )
 * </code>
 *   <em>ceterajxoj</em> havas la sekvajn eblajn formojn:
 *     - komento => ...  (kolumna komento)
 *     - default => ...  (defauxlta valoro)
 *     - charset => ...  (difinas alian signokodigon)
 *     - null           (en tiu kolumno eblas havi null-elementojn.
 *     - auto_increment (tiu kolumno enhavas auxtomatajn numerojn.)
 *
 * @return sqlstring la SQL-ekvivalento
 */
function donu_kampo_sql($priskribo) {

    $eroj = array();

    // kamponomo
    $kamponomo = reset($priskribo);
    next($priskribo);

    // tipo
    list($sx, $val) = each($priskribo);
    if (is_string($sx)) {
        $tipo = $sx.'(' . $val . ')';
    }
    else {
        $tipo = $val;
    }

    $null = false;
    
    // la resto
    while(list($sx, $val) = each($priskribo)) {
        switch($sx) {
        case 'komento':
            $eroj[]= "COMMENT '$val'";
            break;
        case 'default':
            if (is_null($val)) {
                $eroj[]= "DEFAULT NULL";
                $null = true;
            }
            else {
                $eroj[]= "DEFAULT '$val'";
            }
            break;
        case 'charset':
            $eroj[]= "character set $val";
            break;
        default:
            if (!is_int($sx)) {
                darf_nicht_sein('$sx: ' . $sx . ', $val: ' . $val);
            }
            switch($val) {
            case 'null':
                $null = true;
                break;
            case 'auto_increment':
                $eroj[]= "auto_increment";
                break;
            default:
                darf_nicht_sein('$sx: ' . $sx . ', $val: ' . $val);
            }
        }  // switch($sx)
    }  // while(each)


    $sql =  "`" . $kamponomo. "` " . $tipo . "";
    
    if (!$null) {
        $sql .= " NOT NULL";
    }

    if (count($eroj)) {
        $sql .= ' ' . implode (' ', $eroj);
    }
    return $sql;
}


function kreu_necesajn_tabelojn() {

    kreu_tabelon("agxkategorioj",
                 array(array("ID", 'int', 'auto_increment'),
                       array('nomo', 'varchar'=>20),
                       array('priskribo', 'text'),
                       array('sistemoID', 'int'),
                       array('limagxo', 'int',
                             'komento' => "maksimuma aĝo komence de " .
                             "la renkontiĝo en jaroj")),
                 array('nomo' => array('nomo', 'sistemoID')),
                 "aĝkategorioj");
                 


}


$prafix = "..";
require_once($prafix . "/iloj/iloj.php");


echo "<pre>";
kreu_necesajn_tabelojn();
echo "</pre>";

?>