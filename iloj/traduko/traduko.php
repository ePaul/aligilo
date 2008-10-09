<?

/**
 * Tradukilo-interfaco al aliaj programpartoj.
 *
 * Tiu dosiero estas vokita de aliaj partoj de la programo, kaj
 * proponas diversajn funkciojn por traduki tekstojn.
 *
 * @author Paul Ebermann (lastaj ŝanĝoj) + teamo E@I (ikso.net)
 * @version $Id$
 * @package aligilo
 * @subpackage tradukilo
 * @copyright 2005-2008 Paul Ebermann, ?-2005 E@I-teamo
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */

/**
 */


require_once(dirname(__FILE__) . "/iloj.php");
if ($agordoj["parametro_nomo"]) {
    lingvo($_GET[$agordoj["parametro_nomo"]]);
 }

$antaumontro_tradukendaj = 0;

$traduko_dosieroj = array(alghustigu_dosiernomon($_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']));

    
function include_trad($dosiero) {
    global $traduko_dosieroj;
    $orig_dosiero = $dosiero;
    if (substr($dosiero, 0, 1) == "/") {
        $nova_dosiero = $dosiero;
    } else {
        $nuna_dosiero = $traduko_dosieroj[count($traduko_dosieroj) - 1];
        $loko = strrpos($nuna_dosiero, "/");
        $nuna_dosierujo = substr($nuna_dosiero, 0, $loko);
            
        while (substr($dosiero, 0, 3) == "../") {
            $dosiero = substr($dosiero, 3);
            $loko = strrpos($nuna_dosierujo, "/");
            $nuna_dosierujo = substr($nuna_dosierujo, 0, $loko);
        }
            
        $nova_dosiero = $nuna_dosierujo . "/" . $dosiero;
    }
        
    array_push($traduko_dosieroj, $nova_dosiero);
    include($orig_dosiero);
    array_pop($traduko_dosieroj);
}
    
function eniru_dosieron($dosiero) {
    global $traduko_dosieroj;
    array_push($traduko_dosieroj, $dosiero);
}
    
function eliru_dosieron() {
    global $traduko_dosieroj;
    array_pop($traduko_dosieroj);
}
    
function lingvo($iso2 = "nenio") {
    global $trad_lingvo, $agordoj;
    if ($iso2 == "nenio") {
        // Nenion ŝanĝu.
    } else {
        if (strlen($iso2) >= 2) {
            $trad_lingvo = $iso2;
        } else {
            $trad_lingvo = $agordoj["chefa_lingvo"];
        }
    }
    return ($trad_lingvo ? $trad_lingvo : $agordoj["chefa_lingvo"]);
}
    
function lig($ligilo) {
    global $agordoj;
    if ($agordoj["parametro_nomo"]) {
        if (strpos($ligilo, ":") === false) { // certigu ke ne temas pri ligilo al ekstera retejo
            $loko = strrpos($ligilo, "#");
            if ($loko === false) { // prenu "#parto" de la fino de la ligilo, se ekzistas
                // faru nenion
            } else {
                $interna_ligilo = substr($ligilo, $loko);
                $ligilo = substr($ligilo, 0, $loko);
            }
            if (strpos($ligilo, "?") === false) {
                $dividilo = "?";
            } else {
                $dividilo = "&";
            }
            $ligilo = $ligilo . $dividilo . $agordoj["parametro_nomo"] . "=" . lingvo() . $interna_ligilo;
        }
    }
    return $ligilo;
}
    
function CH_lig($origina_cheno) {
    $args = func_get_args();
    $novaj_parametroj = Array($origina_cheno);
        
    for ($i = 1; $i < count($args); $i++) {
        $ligilo = lig($args[$i]);
        if (substr($args[$i], 0, 7) == "http://") {
            array_push($novaj_parametroj, "<a href=\"$ligilo\" target=\"_blank\">");
        } else {
            array_push($novaj_parametroj, "<a href=\"$ligilo\">");
        }
        array_push($novaj_parametroj, "</a>");
    }
    return call_user_func_array("CH", $novaj_parametroj);
}
    
// chuck: Ĉi tiu funkcio ebligas ke tekstoj estu uzeblaj en JS.
function CHJS($origina_cheno) {
    return str_replace("\r\n", "\\n", addslashes(CH($origina_cheno)));
}
    
function CH_lau($origina_cheno) {
    global $trad_lingvo;
    $args = func_get_args();
    $tl = $trad_lingvo;
    $trad_lingvo = array_pop($args);
    $ret = call_user_func_array("CH", $args);
    $trad_lingvo = $tl;
    return $ret;
}


/**
 * anstataŭas en la rezulto nomitajn variablojn.
 */
function CH_repl($origina_cheno, $listo)
{
    $params = func_get_args();
    array_shift($params);
    $params[0] = $origina_cheno;
    $teksto = call_user_func_array('CH', $params);
    foreach($listo AS $sxlosilo => $valoro)
        {
            if (is_string($sxlosilo))
                {
                    $teksto = str_replace('{$'. $sxlosilo . '}',
                                          $valoro,
                                          $teksto);
                }
            else
                {
                    $teksto = str_replace('{$'. $valoro . '}',
                                          $GLOBALS[$valoro],
                                          $teksto);
                }
        }
    return $teksto;
}

function ekzistas($origina_cheno) {
    global $traduko_dosieroj, $trad_lingvo, $db, $agordoj;
        
    if ($_GET["antaumontro"]) $trad_lingvo = $_GET["lingvo"];
    if (!$trad_lingvo) $trad_lingvo = $agordoj["chefa_lingvo"];
    if (substr($origina_cheno, 0, 1) == "/") {
        $dosiero = strtok($origina_cheno, "#");
        $cheno = strtok("#");
    } else {
        $dosiero = $traduko_dosieroj[count($traduko_dosieroj) - 1];
        $cheno = $origina_cheno;
    }
    $db = konektu();
    $tabelo = $agordoj["db_tabelo"];
    $query = "SELECT traduko FROM $tabelo WHERE dosiero"
        . " = '$dosiero' AND cheno = '$cheno' AND iso2 = '$trad_lingvo'";
    $result = mysql_query($query);
    return mysql_num_rows($result);
}
    

/**
 */
function CH($origina_cheno) {
    global $traduko_dosieroj, $trad_lingvo, $db, $antaumontro_tradukendaj, $agordoj;
    global $nuna_dosiero, $nuna_trad_lingvo, $nunaj_chenoj;
        
    if ($_GET["antaumontro"])
        $trad_lingvo = $_GET["lingvo"];
    if (!$trad_lingvo)
        $trad_lingvo = $agordoj["chefa_lingvo"];
    if (substr($origina_cheno, 0, 1) == "/") {
        $dosiero = strtok($origina_cheno, "#");
        $cheno = strtok("#");
    } else {
        //            echo "<!-- CH('" . $origina_cheno . "') -->";
        $baza_dos = $traduko_dosieroj[count($traduko_dosieroj) - 1];
        $listo = explode('#', $origina_cheno);
        if (count($listo) > 1)
            {
                $dosiero = substr($baza_dos, 0, strrpos($baza_dos, '/')+1)
                    . $listo[0];
                $cheno = $listo[1];
            }
        else
            {
                $dosiero = $baza_dos;
                $cheno = $origina_cheno;
            }
    }
        
    if (($dosiero == $nuna_dosiero) and ($trad_lingvo == $nuna_trad_lingvo)) {
        // Jam ni havas la necesajn chenojn en $nunaj_chenoj.
    } else {
        $nunaj_chenoj = array();
        $nuna_dosiero = $dosiero;
        $nuna_trad_lingvo = $trad_lingvo;
            
        $db = konektu();
        $tabelo = $agordoj["db_tabelo"];
        $query = "SELECT cheno, traduko FROM $tabelo WHERE dosiero"
            . " = '$dosiero' AND iso2 = '$trad_lingvo'";
        $result = mysql_query($query);
        while ($row = mysql_fetch_array($result)) {
            $nunaj_chenoj[$row["cheno"]] = $row["traduko"];
        }
    }
    // Nun $nunaj_chenoj estas plena je tradukoj por tiu chi dosiero kaj lingvo.
    // Faru $row kiel antaue.
        
    unset($row);
    if ($nunaj_chenoj[$cheno]) {
        $row = array();
        $row["traduko"] = $nunaj_chenoj[$cheno];
    }

    if (!$row and !($_GET["antaumontro"] == "jes")) {
        $prenis_eo = 1;
        $db = konektu();
        $tabelo = $agordoj["db_tabelo"];
        $query = "SELECT traduko FROM $tabelo WHERE dosiero"
            . " = '$dosiero' AND cheno = '$cheno' AND iso2 = '" . $agordoj["chefa_lingvo"] . "'";
        $result = mysql_query($query);
        $row = mysql_fetch_array($result);
    }
        
    if (!$row) {
        $antaumontro_tradukendaj++;
        return "&lt;$nuna_dosiero#$origina_cheno&gt;";
    } else {
        if ($trad_lingvo == "eo" or $prenis_eo) {
            $row["traduko"] = al_utf8($row["traduko"]);
        }
        $args = func_get_args();
        if (substr($row["traduko"], 0, 2) == "<?" and substr($row["traduko"], -2) == "?>") {
            // evaluado de entajpitaĵoj ne estas permesita
            //              eval(substr($row["traduko"], 2, -2));
        } else {
            $rezulto = preg_replace("/\{(\d*)\}/e", "\$args[\\1]", $row["traduko"]);
        }
        //            echo "<!-- dosiero: '$dosiero', cheno: '$cheno', rezulto: '$rezulto' -->";
        return $rezulto;
    }
}
    
/**
 * redonas array() kun ĉiuj tradukoj.
 */
function CH_mult($origina_cheno) {
    global $traduko_dosieroj, $db, $agordoj;
        
    if (substr($origina_cheno, 0, 1) == "/") {
        $dosiero = strtok($origina_cheno, "#");
        $cheno = strtok("#");
    } else {
        $baza_dos = $traduko_dosieroj[count($traduko_dosieroj) - 1];
        $listo = explode('#', $origina_cheno);
        if (count($listo) > 1)
            {
                $dosiero = substr($baza_dos, 0, strrpos($baza_dos, '/')+1)
                    . $listo[0];
                $cheno = $listo[1];
                //                    echo "<!-- c>1, dosiero: '$dosiero', cheno: '$cheno' -->";
            }
        else
            {
                $dosiero = $baza_dos;
                $cheno = $origina_cheno;
                //                    echo "<!-- c<=1, dosiero: '$dosiero', cheno: '$cheno' -->";
            }
    }
        
    $db = konektu();
    $tabelo = $agordoj["db_tabelo"];
    $query = "SELECT iso2, traduko FROM $tabelo WHERE dosiero"
        . " = '$dosiero' AND cheno = '$cheno'";
    $result = mysql_query($query);
    $tradukoj = array();
    while ($row = mysql_fetch_array($result)) {
        $tradukoj[$row["iso2"]] = $row["traduko"];
    }
    return $tradukoj;
}
    
// por la pagho http://nova.ikso.net/filmo_eo_estas/index.php
function CH_chiuj($origina_cheno) {
    global $traduko_dosieroj, $db, $agordoj;
    $nur = $_GET["nur"]; // por montri nur unu el la tradukoj
        
    if (substr($origina_cheno, 0, 1) == "/") {
        $dosiero = strtok($origina_cheno, "#");
        $cheno = strtok("#");
    } else {
        $dosiero = $traduko_dosieroj[count($traduko_dosieroj) - 1];
        $cheno = $origina_cheno;
    }
        
    $db = konektu();
    $tabelo = $agordoj["db_tabelo"];
        
    $query = "SELECT traduko FROM $tabelo WHERE dosiero"
        . " = '$dosiero' AND cheno = '$cheno' AND iso2 = 'eo'";
    $result = mysql_query($query);
    $row = mysql_fetch_array($result);
    $tradukoj = "<b>eo</b>: <i>" . al_utf8($row["traduko"]) . "</i>";
        
    $query = "SELECT iso2, traduko FROM $tabelo WHERE dosiero"
        . " = '$dosiero' AND cheno = '$cheno'";
    if ($nur != "") {
        $query .= " AND iso2 = '$nur'";
    }
    $result = mysql_query($query);
    while ($row = mysql_fetch_array($result)) {
        if ($row["iso2"] == "eo") {
            continue;
        }
        $tradukoj .=  "<br /><b>" .  $row["iso2"] . "</b>: " . $row["traduko"];
    }
    return $tradukoj;
}


function traduku_datumbazeron($tabelo, $kampo, $id, $lingvo) {
    return CH_lau($GLOBALS['agordoj']["db-trad_prefikso"].$tabelo."/".$kampo."#".$id,
                  $lingvo);
}

?>