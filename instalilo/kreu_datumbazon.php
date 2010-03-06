<?php

  /**
   * Instalilo por la programo - parto por krei la datumbazojn.
   *
   * Depende de INSTALA_MODUSO ni nur printas la SQL-ordonojn por krei la
   * datumbazstrukturon, aŭ jam sendas ilin al la datumbazo.
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage instalilo
   * @copyright 2008-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   * kreas novan datumbaztabelon.
   *
   * @param string $tabelnomo
   * @param array $kamporeguloj  array() el array(), pri kies
   *        formato vidu ĉe {@link donu_kampo_sql()}.
   * @param array $sxlosiloj  listo de ŝlosiloj/indeksoj. De la formo
   *           nomo => detaloj,
   *          kie 'nomo =>' povas esti forlasita (por lasi la sistemon
   *                                           mem krei la nomon).
   *          La nomo 'primary' indikas la ĉefan ŝlosilon.
   *          Se tiu ne estas donita, ni kreas la ĉefan ŝlosilon el "(`ID`)".
   *
   *          detaloj povas esti ĉeno (nomo de kolumno)
   *          aŭ array de tiaj nomoj. En la lasta kazo, se
   *               detaloj[0] == 'index', ĝi estos forprenita
   *          kaj indikas, ke ni havas ne-unikan indekson.
   * @param string $komento
   * @param string $tipo se donita, alia ol la defaŭlta tabeltipo 
   *       (ekzemple MEMORY por nur-memoraj tabeloj).
   */
function kreu_tabelon($tabelnomo, $kampoj,
                      $sxlosiloj=null, $komento="",
                      $tipo=null) {
    $sql = "CREATE TABLE IF NOT EXISTS `" . traduku_tabelnomon($tabelnomo) . "` (\n  ";
    $sqlkampoj = array();
    foreach ($kampoj AS $kampopriskribo) {
        $sqlkampoj[]= donu_kampo_sql($kampopriskribo, $tabelnomo);
    }


    $primary = "ID";


    if(!$sxlosiloj) {
        $sxlosiloj = array();
    }
    
    foreach($sxlosiloj AS $nomo => $valoro) {
        debug_echo( "<!-- nomo: " . $nomo . ", valoro: " .
                    var_export( $valoro, true) . " -->");
        if ('primary' === $nomo) {
            debug_echo( "<!-- primary! -->");
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
            debug_echo( "<!-- valoro: " .
                        var_export( $valoro, true) . " -->");
            $sxlosilfrazo =
                ($unique ? "UNIQUE KEY " : "KEY ") .
                (is_int($nomo) ?'' : "`$nomo` ") .
                "(`" . $valoro ."`)";
            $sqlkampoj[]= $sxlosilfrazo;
        }
    }
    debug_echo( "<!-- sqlkampoj: " . var_export($sqlkampoj, true) . "-->");

    $sqlkampoj[] = "PRIMARY KEY (`$primary`)";

    $sql .= implode(",\n  ", $sqlkampoj);
    $sql .= "\n) ";
    if (CHARSET_DB_SUPPORT) {
        $sql .= "DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci ";
    }
    if($tipo) {
        $sql .= "\n   TYPE='" . $tipo . "'";
    }
    if ($komento) {
        $sql .= "\n   COMMENT='" . addslashes($komento) . "'";
    }
    $sql .= ";\n";

    // TODO


    faru_SQL( $sql);
}


/**
 * kreas SQL-klaŭzon por unuopa kampo.
 * @param array $priskribo la kolumno-specifikaĵo,
 *  en la formo <code>
 *    array( kamponomo, tipo [=> grandeco ], ceteraĵoj ... )
 * </code>
 *   <em>ceteraĵoj</em> havas la sekvajn eblajn formojn:
 *     - komento => ...  (kolumna komento)
 *     - default => ...  (defaŭlta valoro)
 *     - charset => ...  (difinas alian signokodigon)
 *     - ascii          (same kiel "charset => ascii")
 *     - null           (en tiu kolumno eblas havi null-elementojn.
 *     - auto_increment (tiu kolumno enhavas aŭtomatajn numerojn.)
 *     - tradukebla     (tiu kolumno estos tradukebla - por tiu indiko
 *                       ne estos kreita SQL, sed la informo estos
 *                       savita en aparta dosiero, kun la tabelnomo.)
 *     - tradukebla => array(helpo-kampo, klarigo-dosiero)
 *                       ...
 * @param sqlstring $tabelnomo nomo de la tabelo, en kiu aperos tiu kampo.
 *
 * @return sqlstring la SQL-ekvivalento
 */
function donu_kampo_sql($priskribo, $tabelnomo) {

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
            $eroj[]= "\n     COMMENT '" . addslashes($val) . "'";
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
            if (CHARSET_DB_SUPPORT) {
                $tipo .= " character set $val";
            }
            break;
        case 'tradukebla':
            $linio = 'tradukuKampon: "'. $kamponomo . '" en: "' . $tabelnomo
                   . '"';
            foreach ($val AS $nom => $txt) {
                $linio .= " " . $nom . ': "' . $txt . '"';
            }
            fwrite($GLOBALS['tradukoj'],
                   $linio . ";\n");
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
            case 'ascii':
                if (CHARSET_DB_SUPPORT) {
                    $tipo .= " character set ascii";
                }
                break;
            case 'tradukebla':
                fwrite($GLOBALS['tradukoj'],
                       'tradukuKampon: "' . $kamponomo . '" en: "' .
                       $tabelnomo . '"' . ";\n");
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

/* ************ kelkaj helpaj funkcioj por krei pli facile kolumno-regulojn ******* */


/**
 * kreas kolumno-priskribon por flag-kolumno.
 *
 * Tia kolumno enhavas unu el kelkaj valoroj, kies signifojn
 * konas la programo. Ni kodigas ĝin ene de unu signo el la ASCII-signaro.
 * @param string $nomo nomo de la kolumno
 * @param string $defauxlto se donita, la defaŭlta valoro de la kampo.
 * @param string $komento datumbaza komento.
 * @param array|boolean $tradukebla aldonaj traduk-informoj.
 * @return array la kampo-specifikaĵo, por uzo en {@link donu_kampo_sql()}.
 */
function flag_kol($nomo, $defauxlto=null, $komento="", $tradukebla=null)
{
    $kol = array($nomo, "char" => 1, 'ascii');
    if ($defauxlto) {
        $kol['default'] = $defauxlto;
    }
    if ($komento) {
        $kol['komento'] = $komento;
    }
    if (is_array($tradukebla)) {
        $tradukebla['flag'] = "true";
        $kol['tradukebla'] = $tradukebla;
    } else if ($tradukebla) {
        $kol['tradukebla'] = array('flag' => "true");
    }
    
    return $kol;
}


function id_kolumno() {
    return array("ID", 'int', 'auto_increment');
}

function nomo_kolumno() {
    return array('nomo', 'varchar'=>20);
}

function nomo_trad_kolumno_katsistemo($helptabelprefikso) {
    return array('nomo',
                 'varchar'=>20,
                 'tradukebla' => array('subdividotabelo' =>
                                       $helptabelprefikso."kategorisistemoj", 
                                       'subdividoID' => 'sistemoID',
                                       'subdividonomo' => 'nomo'));
}

function nomo_trad_kol_simpla() {
    return array('nomo', 'varchar'=>20,
                 'tradukebla');
}

function rajto_kol($nomo, $komento="")
{
    return flag_kol($nomo, 'N', $komento);
}



/* ***** kaj nun la tabelkreaj funkcioj.
 */


function kreu_kategorisistemajn_tabelojn()
{
    $id_kol = id_kolumno();
    $nomo_kol = nomo_kolumno();
    $priskribo_kol = array('priskribo', 'text');
    $sistemoID_kol = array('sistemoID', 'int');
    $nomo_lokalingve_kol = array('nomo_lokalingve', 'varchar' => 20);
    $entajpanto_kol = array('entajpanto', 'int');
    
    kreu_tabelon("agxkategorioj",
                 array($id_kol,
                       nomo_trad_kolumno_katsistemo('agx'),
                       $priskribo_kol,
                       $sistemoID_kol,
                       array('limagxo', 'int',
                             'komento' => "maksimuma aĝo komence de " .
                             "la renkontiĝo en jaroj")),
                 array(array('nomo', 'sistemoID')),
                 "aĝkategorioj");

    kreu_tabelon("agxkategorisistemoj",
                 array($id_kol,
                       $nomo_kol,
                       $entajpanto_kol,
                       $priskribo_kol),
                 array("nomo"),
                 "sistemoj de aĝkategorioj");

    kreu_tabelon("aligxkategorioj",
                 array($id_kol,
                       nomo_trad_kolumno_katsistemo('aligx'),
                       $priskribo_kol,
                       $sistemoID_kol,
                       array('limdato', 'int'),
                       $nomo_lokalingve_kol),
                 array(array('nomo', 'sistemoID')),
                 "aliĝkategorioj");
                 
    kreu_tabelon("aligxkategorisistemoj",
                 array($id_kol,
                       $nomo_kol,
                       $entajpanto_kol,
                       $priskribo_kol),
                 array("nomo"),
                 "sistemoj de aliĝkategorioj");

    kreu_tabelon("kategorioj_de_landoj",
                 array(array('sistemoID', 'int', 'komento' => "landokategorisistemo"),
                       array('landoID', 'int'),
                       array('kategorioID', 'int')),
                 array('primary' => array('sistemoID', 'landoID')),
                 "liganta tabelo por landoj kaj iliaj kategorioj laŭ sistemo");
    
    kreu_tabelon("landokategorioj",
                 array($id_kol, nomo_trad_kolumno_katsistemo('lando'),
                       $priskribo_kol, $sistemoID_kol),
                 array(array('nomo', 'sistemoID')),
                 "landokategorioj");

    kreu_tabelon("landokategorisistemoj",
                 array($id_kol,
                       $nomo_kol,
                       $entajpanto_kol,
                       $priskribo_kol),
                 array("nomo"),
                 "sistemoj de landokategorioj");

    kreu_tabelon("logxkategorioj",
                 array($id_kol, nomo_trad_kolumno_katsistemo('logx'),
                       $priskribo_kol, $sistemoID_kol,
                       // TODO: anstataux sxlosillitero eblu
                       //       havi plurajn tiajn.
                       array('kondicxo', 'int', 'komento' => "Kondiĉo por esti en tiu kategorio"),
//                        flag_kol('sxlosillitero', null,
//                                 "litero uzata en partoprenanto->domotipo"),
                       ),
                 array(array('sistemoID', 'nomo'),
//                       array('sistemoID', 'sxlosillitero')
                       ),
                 "loĝkategorioj");

    kreu_tabelon("logxkategorisistemoj",
                 array($id_kol,
                       $nomo_kol,
                       $entajpanto_kol,
                       $priskribo_kol),
                 array("nomo"),
                 "sistemoj de loĝkategorioj");

}




function kreu_kotizosistemajn_tabelojn()
{
    $id_kol = id_kolumno();
    $nomo_kol = nomo_kolumno();
    $nomo_trad_kol = nomo_trad_kol_simpla();
    $priskribo_kol = array('priskribo', 'text');
    $entajpanto_kol = array('entajpanto', 'int');
    $nomo_lokalingve_kol = array('nomo_lokalingve', 'varchar' => 20);
    
    kreu_tabelon('kotizosistemoj',
                 array($id_kol, $nomo_kol,
                       $priskribo_kol, $entajpanto_kol,
                       array('aligxkategorisistemo', 'int'),
                       array('landokategorisistemo', 'int'),
                       array('agxkategorisistemo', 'int'),
                       array('logxkategorisistemo', 'int'),
                       array('parttempdivisoro', 'double'),
                       array('malaligxkondicxsistemo', 'int')),
                 array("nomo"),
                 "diversaj kotizosistemoj");

    kreu_tabelon("parttempkotizosistemoj",
                 array($id_kol,
                       array("baza_kotizosistemo", "int"),
                       array("por_noktoj", "int"),
                       array("kondicxo", 'int',
                             'komento' => "tiu ĉi enskribo validas por partoprenoj el X tranoktoj"),
                       array("faktoro", 'decimal' => '6,2'),
                       array("sub_kotizosistemo", "int")),
                 array(array("baza_kotizosistemo", "por_noktoj", "kondicxo")),
                 "traktado de parttempaj partoprenantoj en iu kotizosistemo");
    
    kreu_tabelon('kotizotabeleroj',
                 array(array('kotizosistemo', 'int'),
                       array('aligxkategorio', 'int'),
                       array('landokategorio', 'int'),
                       array('agxkategorio', 'int'),
                       array('logxkategorio', 'int'),
                       array('kotizo', 'decimal' => '6,2')),
                 array('primary' => array('kotizosistemo','aligxkategorio',
                                          'landokategorio','agxkategorio',
                                          'logxkategorio')),
                 "jen la multaj eroj de la kotizo-tabelo");

    $regulaj_xxx_kampoj = array($id_kol,
                                array('regulo', 'int'),
                                array('kotizosistemo', 'int'),
                                array('kvanto', 'decimal' => '6,2'),
                                //TODO: pripensu uzi defaŭltan valuton.
                                array('valuto', 'char' => 3, 'ascii'));
                 
    kreu_tabelon('regulaj_krompagoj',
                 $regulaj_xxx_kampoj,
                 array(array('regulo', 'kotizosistemo')),
                 "La alteco de la unuopaj regulaj krompagoj");

    kreu_tabelon('regulaj_rabatoj',
                 $regulaj_xxx_kampoj,
                 array(array('regulo', 'kotizosistemo')),
                 "La alteco de la unuopaj krompagoj");
    
    $xxxreguloj_kampoj =                 
        array($id_kol,
              $nomo_trad_kol,
              array('mallongigo', 'varchar' => 10,
                    'komento' => "mallongigo por la finkalkulada tabelo"),
              $entajpanto_kol,
              $priskribo_kol,
              array('kondicxo', 'int'),
              flag_kol('uzebla', 'j'),
              flag_kol('lauxnokte', 'n',
                       "ĉu laŭnokta (j), ĉu unufoja (n)?"));


    kreu_tabelon('krompagoreguloj',
                 $xxxreguloj_kampoj,
                 array('nomo'),
                 "eblaj reguloj por krompagoj");
    kreu_tabelon('rabatoreguloj',
                 $xxxreguloj_kampoj,
                 array('nomo'),
                 "eblaj reguloj por rabatoj");
                 
    
//     kreu_tabelon('krompagotipoj',
//                  array($id_kol, $nomo_trad_kol,// $nomo_lokalingve_kol,
//                        array('mallongigo', 'varchar' => 10,
//                              'komento' => "mallongigo por la finkalkulada tabelo"),
//                        $entajpanto_kol, $priskribo_kol,
//                        array('kondicxo', 'int'),
//                        flag_kol('uzebla', 'j'),
//                        flag_kol('lauxnokte', 'n',
//                                 "ĉu laŭnokta krompago (j), ĉu unufoja (n)?")),
//                  array('nomo'),
//                  "tipoj de eblaj krompagoj");

    kreu_tabelon('kondicxoj',
                 array($id_kol, $nomo_kol,
                       $entajpanto_kol,
                       $priskribo_kol,
                       array('kondicxoteksto', 'text',
                             'komento' => 
                             "kondiĉo-esprimo (=> iloj_kondicxoj.php)"),
                       array('jxavaskripta_formo', 'text',
                             'komento' => "korpo de Ĵavo-skripto-funkcio por eltrovi la validecon de la kondiĉo en la aliĝilo. (=> kotizokalkulo.js.php)"),
                       ),
                 array('nomo'),
                 "Kondiĉoj por uzo en krompagotipoj, rabatoj, ktp.");
    
    kreu_tabelon('malaligxkondicxoj',
                 array(array('sistemo', 'int',
                             'komento' => "Malaliĝkondiĉosistemo"),
                       array('aligxkategorio', 'int'),
                       array('kondicxtipo', 'int')),
                 array('primary' => array('sistemo', 'aligxkategorio')),
                 "en kiu kategorio uzu kiun kondiĉon?");
    
    kreu_tabelon('malaligxkondicxotipoj',
                 array($id_kol, $nomo_trad_kol,
                       array('mallongigo', 'varchar' => 10,
                             'komento' => "mallongigo por la finkalkulada tabelo"),
                       $priskribo_kol,
                       array('funkcio', 'varchar' => 50, 'ascii'),
                       array('parametro', 'decimal' => '6,2',
                             'default' => null),
                       flag_kol('uzebla')),
                 array('nomo'),
                 "Trakteblecoj por malaliĝintoj");
    
    kreu_tabelon('malaligxkondicxsistemoj',
                 array($id_kol, $nomo_kol, $priskribo_kol,
                       array('aligxkategorisistemo', 'int')),
                 array('nomo'),
                 "sistemo de malaliĝkondiĉoj");
    
    // TODO; pripensu, kiel tio kongruas kun diversaj
    //       valutoj
    kreu_tabelon('minimumaj_antauxpagoj',
                 array(array('kotizosistemo', 'int'),
                       array('landokategorio', 'int'),
                       array('oficiala_antauxpago', 'decimal' => '6,2',
                             'komento' => "Kion ni montras al la publiko"),
                       array('interna_antauxpago', 'decimal' => '6,2',
                             'komento' => "Kion ni uzas por la kalkuloj")),
                 array('primary' => array('kotizosistemo', 'landokategorio')),
                 "La minimumaj antaŭpagoj por ĉiu landokategorio en iu kotizosistemo");
}

function kreu_cxambrajn_tabelojn()
{
    $id_kol = id_kolumno();

    kreu_tabelon("cxambroj",
                 array($id_kol,
                       array('renkontigxo', 'int'),
                       nomo_kolumno(),
                       array('etagxo', 'varchar' => 50),
                       array('litonombro', 'int'),
                       flag_kol('tipo', null, "i = ina, g = gea, v = vira"),
                       flag_kol('dulita', 'N',
                                "J = dulita, U = unulita, N = vera kvanto de litoj uzebla"),
                       array('rimarkoj', 'varchar' => 100)),
                 array(array('renkontigxo','nomo')),
                 "La ĉambroj haveblaj");
    
    kreu_tabelon('litonoktoj',
                 array($id_kol,
                       array('cxambro', 'int'),
                       array('litonumero', 'int'),
                       array('nokto_de', 'int'),
                       array('nokto_gxis', 'int'),
                       array('partopreno', 'int'),
                       flag_kol('rezervtipo')),
                 array(array('index', 'cxambro'), array('index', 'partopreno')),
                 "kiu loĝas kiam kie?");
    

    /* // ankoraŭ ne uzata
     kreu_tabelon("kunlogxdeziroj",
     array($id_kol,
     array('partoprenoID', 'int'),
     array('kunKiuID', 'int'),
     flag_kol('stato')),
     array(array('partoprenoID', 'kunKiuID')),
     "deziroj de kunlogxado kaj ties statoj");
    */
}


function kreu_kostosistemajn_tabelojn()
{
    $id_kol = id_kolumno();
    $nomo_kol = nomo_kolumno();
    $priskribo_kol = array('priskribo', 'text');
    $entajpanto_kol = array('entajpanto', 'int');
    
    kreu_tabelon('fikskostoj',
                 array($id_kol, $nomo_kol,
                       array('kostosistemo', 'int'),
                       array('kosto', 'decimal' => '7,2')),
                 array(array('kostosistemo', 'nomo')),
                 "fikskostoj de iu renkontiĝo");
                 
    kreu_tabelon('kostosistemoj',
                 array($id_kol, $nomo_kol,
                       $priskribo_kol, $entajpanto_kol),
                 array('nomo'),
                 "diversaj kostosistemoj");
    
    kreu_tabelon('personkostoj',
                 array(array('tipo', 'int'),
                       array('kostosistemo', 'int'),
                       array('maks_haveblaj', 'int'),
                       array('min_uzendaj', 'int'),
                       array('kosto_uzata', 'decimal' => '6,2'),
                       array('kosto_neuzata', 'decimal' => '6,2')),
                 array('primary' => array('tipo', 'kostosistemo')),
                 "Kostoj, kiuj okazos por ĉiu partoprenanto");

    kreu_tabelon('personkostotipoj',
                 array($id_kol, $nomo_kol,
                       $entajpanto_kol, $priskribo_kol,
                       array('kondicxo', 'varchar' => 50, 'ascii',
                             'komento' => "nomo de kondiĉo-funkcio vokenda"),
                       flag_kol('uzebla', 'j'),
                       flag_kol('lauxnokte', 'n',
                                "ĉu laŭnokta kosto (j), ĉu unufoja (n)?")),
                 array('nomo'),
                 "tipoj de eblaj kostoj laŭ persono");

}


function kreu_administrajn_tabelojn()
{
    $id_kol = id_kolumno();
    $nomo_kol = nomo_kolumno();
    $priskribo_kol = array('priskribo', 'text');
    $entajpanto_kol = array('entajpanto', 'int');
    
    /* TODO: ĉu sendanto_nomo estas uzata? -jes, en sendumesagxon. */
    /* TODO: eble ni splitu la individuajn rajtojn al aparta tabelo. */

    kreu_tabelon('entajpantoj',
                 array($id_kol,
                       array('nomo', 'varchar'=>50),
                       array('kodvorto', 'varchar'=>50),
                       array('sendanto_nomo', 'varchar'=>30),
                       array('retposxtadreso', 'varchar'=>50),
                       array('partoprenanto_id', 'int', 'default' => null),
                       rajto_kol("aligi"),
                       rajto_kol("vidi"),
                       rajto_kol("sxangxi"),
                       rajto_kol("cxambrumi"),
                       rajto_kol("ekzporti"),
                       rajto_kol("statistikumi"),
                       rajto_kol("mono"),
                       rajto_kol("estingi"),
                       rajto_kol("retumi"),
                       rajto_kol("rabati"),
                       rajto_kol("inviti"),
                       rajto_kol("administri"),
                       rajto_kol("akcepti"),
                       rajto_kol("teknikumi")),
                 array("nomo"),
                 'Uzantoj de la datumbazo, kun pasvortoj kaj rajtoj.');
    
    kreu_tabelon('protokolo',
                 array($id_kol,
                       array('deveno', 'varchar' => 200),
                       array('ilo', 'varchar' => 200),
                       array('entajpanto', 'varchar' => 20,
                             'komento' => "salutnomo de la entajpanto"),
                       array('tempo', 'datetime'),
                       array('ago', 'varchar' => 20)),
                 null,
                 "protokolo de ĉiuj gravaj agadoj de la uzantoj.");
    
    
    // TODO: nun ne estas uzata, tion ni eble ŝanĝu
    kreu_tabelon('monujo',
                 array($id_kol,
                       array('renkontigxo', 'int'),
                       array('kvanto', 'int' /* ĉu vere int? */),
                       array('kauzo', 'varchar' => 200),
                       array('tempo', 'datetime', 'default' => '0000-00-00 00:00:00'),
                       array('kvitanconumero', 'int'),
                       array('alKiu', 'varchar' => 20),
                       array('kiaMonujo' /* ĉu 'kiu'?*/, 'varchar' => 10)));
    
    kreu_tabelon('nomsxildoj',
                 array($id_kol,
                       array('titolo_lokalingve', 'varchar' => 15),
                       array('titolo_esperante', 'varchar' => 15),
                       array('nomo', 'varchar' => 30),
                       array('funkcio_lokalingve', 'varchar' => 40),
                       array('funkcio_esperante', 'varchar' => 40),
                       array('renkontigxoID', 'int'),
                       flag_kol('havasNomsxildon', 'N')),
                 array(array('index', 'renkontigxoID')),
                 "por specialaj nomŝildoj (por nepartopenantoj)");
    
    kreu_tabelon('renkontigxo',
                 array($id_kol,
                       array('nomo', 'varchar' => 100),
                       array('mallongigo', 'varchar' => 10),
                       array('temo', 'varchar' => 100),
                       array('loko', 'varchar' => 100),
                       array('de', 'date'),
                       array('gxis', 'date'),
                       array('kotizosistemo', 'int'),
                       /* jen venos informoj por malnova kotizokalkulilo ...
                        TODO: forigendaj (ankaŭ el la redaktilo). */
                       array('plej_frue', 'date'),
                       array('meze', 'date'),
                       array('malfrue', 'date'),
                       array('parttemppartoprendivido', 'int'),
                       array('juna', 'int'),
                       array('maljuna', 'int'),
                       /* jen venos nomoj kaj adresoj de diversaj respondeculoj.
                        TODO: metenda en aparta tabelo, eble rilate al entajpantoj. */
                       array('adminrespondeculo', 'varchar' => 50),
                       array('adminretadreso', 'varchar' => 100, 'ascii'),
                       array('sekurkopiojretadreso', 'varchar' => 100, 'ascii'),
                       array('invitleterorespondeculo', 'varchar' => 50),
                       array('invitleteroretadreso', 'varchar' => 100, 'ascii'),
                       array('temarespondulo', 'varchar' => 50),
                       array('temaretadreso', 'varchar' => 100, 'ascii'),
                       array('distrarespondulo', 'varchar' => 50),
                       array('distraretadreso', 'varchar' => 100, 'ascii'),
                       array('vesperarespondulo', 'varchar' => 50),
                       array('vesperaretadreso', 'varchar' => 100, 'ascii'),
                       array('muzikarespondulo', 'varchar' => 50),
                       array('muzikaretadreso', 'varchar' => 100, 'ascii'),
                       array('noktarespondulo', 'varchar' => 50),
                       array('noktaretadreso', 'varchar' => 100, 'ascii'),
                       array('novularespondulo', 'varchar' => 50),
                       array('novularetadreso', 'varchar' => 100, 'ascii')),
                 array('mallongigo',
                       'nomo'),
                 "La bazaj datoj de ĉiu renkontiĝo.");

    kreu_tabelon('retposxto',
                 array($id_kol, $nomo_kol,
                       array('subjekto' /* temlinio */, 'varchar' => 100),
                       array('korpo' /* teksto */, 'text')),
                 array('nomo'),
                 "ŝablonoj por retpoŝtoj al partoprenantoj");

    kreu_tabelon('sercxoj',
                 array($id_kol,
                       array('nomo', 'varchar'=>50),
                       $priskribo_kol, $entajpanto_kol,
                       array('sercxo', 'blob')),
                 array('nomo', array('index', 'entajpanto')),
                 "La daŭrigitaj serĉoj");

    kreu_tabelon("renkkonfiguroj",
                 array($id_kol,
                       array('renkontigxoID', 'int'),
                       array('opcioID', 'varchar' => '30', 'ascii'),
                       array('valoro', 'text')),
                 array(array('renkontigxoID', 'opcioID')),
                 "konfiguroj renkontiĝospecifaj (estos anstataŭigota per 'renkontigxaj_konfiguroj')");

    kreu_tabelon("renkontigxaj_konfiguroj",
                 array($id_kol,
                       array('renkontigxoID', 'int'),
                       array('tipo', 'varchar' => 20, 'ascii',
                             'komento' => 'ekzemple `pagotipo`, `valuto`, `rabatkialo`, `krompagokialo`, `logxtipo` ktp.'),
                       array('interna', 'varchar' => 20, 'ascii',
                             'komento' => "interna identigilo"),
                       array('grupo', 'int', 'default' => '0'),
                       array('teksto', 'varchar' => 100,
                             'tradukebla' =>
                             array('subdividotabelo' => 'renkontigxo',
                                   'subdividoID' => 'renkontigxoID',
                                   'subdividonomo' => 'mallongigo',
                                   'helpoteksto' =>
                                   "CONCAT(tipo,'/',interna)"),
                             'komento' => "esperanta priskriba teksto"),
                       array('aldona_komento', 'varchar' => 100,
                             'komento' => "aperas nur en la administrilo, ne por la klientoj")
                       ),
                 array(array('renkontigxoID', 'tipo', 'interna')),
                 "renkontiĝospecifaj konfiguroj, kiel pagotipoj, valutoj, rabatkialoj, krompagokialoj ktp.");




    kreu_tabelon('kurzoj',
                 array($id_kol,
                       array('valuto', 'char' => 3, 'ascii'),
                       array('dato', 'date'),
                       array('kurzo', 'decimal' => '10,5')),
                 array(array('valuto', 'dato')),
                 "kurzotabelo por traktado de pluraj valutoj");


    kreu_tabelon('tekstoj',
                 array($id_kol,
                       array('renkontigxoID', 'int'),
                       array('mesagxoID', 'varchar' => 30, 'ascii'),
                       array('teksto', 'text',
                             'tradukebla'
                             => array('helpeDe' => 'mesagxoID',
                                   'helpoteksto'
                                      => "CONCAT(mesagxoID,'/', renkontigxoID)",
                                   'klarigoj' 
                                      => '/doku/tekstoj.txt'))),
                 array(array('renkontigxoID', 'mesagxoID')),
                 "tabelo por lokaligo de tekstoj (-> tekstoj.php)");

}

function kreu_pagajn_tabelojn()
{
    $id_kol = id_kolumno();
    $ppenoID_kol = array('partoprenoID', 'int');
    $entantoID_kol = array('entajpantoID', 'int');

    $kampolisto =
        array($id_kol, $ppenoID_kol,
              array('valuto', 'char' => 3, 'ascii'),
              array('kvanto', 'decimal' => '6,2'),
              array('dato', 'date'),
              array('tipo', 'varchar' => 100),
              $entantoID_kol);

    kreu_tabelon('pagoj',
                 $kampolisto,
                 "",
                 "Antaŭ- kaj surlokaj pagoj");

    kreu_tabelon('rabatoj',
                 $kampolisto,
                 "",
                 "individuaj Rabatoj (por kontribuoj)");

    kreu_tabelon('individuaj_krompagoj',
                 $kampolisto,
                 "",
                 "Individuaj krompagoj por specialaj servoj");



}


function kreu_partoprenantajn_tabelojn()
{
    $id_kol = id_kolumno();
    $nomo_trad_kol = nomo_trad_kol_simpla();
    $ppenoID_kol = array('partoprenoID', 'int');
    $ppantoID = array('partoprenantoID', 'int');
    $entantoID_kol = array('entajpantoID', 'int');
    
    kreu_tabelon("invitpetoj",
                 array(array('ID', 'int',
                             'komento' => "samtempe la identigilo de la partopreno"
                             /* pro tio ne havas auto_increment */),
                       array('pasportnumero', 'varchar' => 50,
                             'komento' => "la numero de la pasporto"),
                       array('pasporto_valida_de', 'date'),
                       array('pasporto_valida_gxis', 'date'),
                       array('pasporta_persona_nomo', 'varchar' => 50),
                       array('pasporta_familia_nomo', 'varchar' => 50),
                       array('pasporta_adreso', 'text'),
                       array('senda_adreso', 'text'),
                       array('senda_faksnumero', 'varchar' => 30, 'default' => null),
                       flag_kol('invitletero_sendenda', '?'),
                       array('invitletero_sendodato', 'date', 'default' => '0000-00-00')),
                 null,
                 "Petoj pri invitleteroj");
    
    // TODO: trovu eblecon traduki la 'lokan nomon' al pluraj lingvoj.
    kreu_tabelon('landoj',
                 array($id_kol, $nomo_trad_kol,
                       // array('lokanomo', 'varchar'=>50),
                       array('kodo', 'char' => 2, 'ascii',
                             'komento' => "kodo laŭ ISO-3166-1")),
                 "",
                 "La landoj, el kiuj povus veni la partoprenantoj");
    
    kreu_tabelon('notoj',
                 array($id_kol,
                       $ppantoID,
                       array('kiu', 'varchar' => 100),
                       array('kunKiu', 'varchar' => 100),
                       array('tipo', 'varchar' => 10),
                       array('dato', 'datetime'),
                       array('subjekto' /* temo */, 'varchar' => 100),
                       array('enhavo', 'text'),
                       flag_kol('prilaborata' /* estu -ita */),
                       array('revidu', 'datetime')),
                 array(),
                 "notoj pri partoprenantoj");

    kreu_tabelon('notoj_por_entajpantoj',
                 array(array('notoID', 'int'),
                       $entantoID_kol),
                 array('primary' => array('notoID', 'entajpantoID'),
                       array('index', 'notoID'),
                       array('index', 'entajpantoID')),
                 "kiu noto estas por kiu entajpanto?");

    

    kreu_tabelon('partoprenantoj',
                 array($id_kol,
                       array('nomo', 'varchar' => 50,
                             'komento' => "familia nomo"),
                       array('personanomo', 'varchar' => 50),
                       array('sxildnomo', 'varchar' => 50),
                       flag_kol('sekso', null,
                                "'i' = ina, 'v' = vira", true),
                       array('naskigxdato', 'date'),
                       array('adreso', 'varchar' => 200,
                             'komento' => "Kombino de adresaldonaĵo kaj strato, eble ankaŭ provinco, kie tio necesas por la adreso"),
                       /*
                        array('adresaldonajxo', 'varchar' => 50),
                       array('strato', 'varchar' => 50),
                       array('provinco', 'varchar' => 50),
                       */
                       array('posxtkodo', 'varchar' => 50),
                       array('urbo', 'varchar' => 50),
                       array('lando', 'int'),
                       array('sxildlando', 'varchar' => 50),
                       /*
                       array('okupigxo', 'int'),
                       array('okupigxteksto', 'varchar' => 100),
                       */
                       array('telefono', 'varchar' => 50, 'ascii'),
                       array('tujmesagxiloj', 'varchar' => 200),
                       /*
                       array('telefakso', 'varchar' => 50, 'ascii'),
                       */
                       array('retposxto', 'varchar' => 50, 'ascii'),
                       /*
						 // TODO: kreu apartan kampon pri la prefero x/uni dise
						 // de varbado/nevarbado.
                       flag_kol('retposxta_varbado', 'j'),
                       */
                       array('ueakodo', 'varchar' => 6, 'ascii'),
                       //                       array('rimarkoj', 'varchar' => 100),
                       //                       array('kodvorto', 'varchar' => 10, 'ascii')
                       ),
                 array(array('index', 'nomo'),
                       array('index', 'personanomo'),
                       array('index', 'naskigxdato'),
                       array('index', 'retposxto')),
                 "la partoprenantoj");

    kreu_tabelon('partoprenoj',
                 array($id_kol,
                       array('renkontigxoID', 'int'),
                       $ppantoID,
                       array('ordigoID', 'decimal' => '9,3'),
                       array('agxo', 'int',
                             'komento' => "estas kalkulita el naskiĝdato kaj renkontiĝodato, adaptenda, kiam tiuj ŝanĝiĝas."),
                       /* (anstatauxita per nivelo)
                       flag_kol('komencanto', 'N'),
                       */
                       flag_kol('nivelo', '?',
                                "lingva nivelo: f = flua, p = parolas, k - komencanto",
                                true),
                       flag_kol('studento', '?',
                                "j = estas studento, n = ne estas studento, ? = ni ne scias"),
                       array('rimarkoj', 'text'
                             /* TODO: pripensu, ĉu ni ne tuj je la aliĝado
                              kreu noton, kaj tiam povos forĵeti la
                              rimarko-kampon */),
                       /*
                       flag_kol('invitletero', 'N'),
                       */
                       //                       array('invitilosendata' /* estu -ita */, 'date',
                       //                             'komento' => "ne plu uzenda" /* TODO: tamen ankoraŭ multfoje uzita! */),
                       //                       array('pasportnumero', 'varchar' => 100, 'default' => null, 
                       //                             'komento' => "ne plu uzenda" ),
                 
                       flag_kol('retakonfirmilo', null, "J/N",
                                array('elekto' => 'jesne')),
                       //                       flag_kol('germanakonfirmilo', 'N') /* TODO: plurlingvaj konfirmiloj */,
                       array('konfirmilolingvo', 'char' => 3, 'ascii',
                             'komento' => "'eo', se nur en Esperanto, alikaze la lingvokodo de tiu lingvo, en kiu oni volas aldone havi ĝin.",
                             'tradukebla' => array('flag' => 'true')),
                       array('1akonfirmilosendata' /* estu -ita */, 'date'),
                       array('2akonfirmilosendata' /* estu -ita */, 'date'),
                       flag_kol('partoprentipo', 't',
                                "'t' = tuttempa, 'p' = parttempa", true),
                       array('de', 'date'),
                       array('gxis', 'date'),
                       flag_kol('vegetare', 'N',
                                "'J' = vegetare, 'A' = vegane, 'N' = viande",
                                true),
                       /* la sekvaj tri kampoj nur, kiam loka asocio volas membriĝon.
                        TODO: prenu el konfiguro, kaj depende de tio aldonu la
                        kampojn. */
                       /*
                       flag_kol('GEJmembro', 'N'),
                       flag_kol('surloka_membrokotizo', '?'),
                       array('membrokotizo', 'decimal' => '6,2'),
                       */
                       flag_kol('tejo_membro_laudire', 'n', "", true),
                       flag_kol('tejo_membro_kontrolita', '?', "", true),
                       array('tejo_membro_kotizo', 'decimal' => '6,2'),
                       flag_kol('KKRen', 'N',
                                "Ĉu membro de la organiza teamo?", true),
                       flag_kol('domotipo', null, "", true),
                       // ne nun
                       //                       flag_kol('litolajxo', 'N') /* TODO: verŝajne forĵetenda. */,
                       //                       flag_kol('kunmangxas', 'N'),
                       flag_kol('listo', 'N',
                                "Ĉu aperi en la (interreta) listo de aliĝintoj?", array('elekto' => 'jesne')),
                       flag_kol('intolisto', 'N',
                                "Ĉu aperi en la post-renkontiĝa partopreninto-listo (adresaro)? (J/N)",
                                array('elekto' => 'jesne')),
                       array('pagmaniero', 'varchar' => 30,
                             'komento' => "en la aliĝilo anoncita maniero de antaŭpago",
                             /* todo: traduko? */ ),
                       array('antauxpago_gxis', 'date'),
                       array('kunKiu', 'varchar' => 50),
                       array('kunKiuID', 'int'),
                       flag_kol('cxambrotipo', 'g',
                                "g = ne gravas, u = unuseksa",
                                true),
                       flag_kol('dulita', 'N',
                                "J = mendis dulitan, u = unulitan, N = pli grandan", true),
                       //                       flag_kol('ekskursbileto', 'N'),
                       /* jen venas diversaj programproponoj - eble simpligu (nur unu tia kampo?),
                        aŭ aŭtomate faru noton el ĝi. Sed tiam notoj estu pli
                        facile trovebla ... */
                       array('tema', 'text'),
                       array('distra', 'text'),
                       array('vespera', 'text'),
                       //                       array('muzika', 'text'),
                       array('nokta', 'text'),
                       array('lingva_festivalo', 'text'),
                       array('helpo', 'text'),
                        
                       array('aligxdato', 'date'),
                       array('malaligxdato', 'date'),
                       array('aligxkategoridato', 'date'),
                       flag_kol('alvenstato', 'v',
                                "'v' = venos, 'm' = malaliĝis, 'a' = alvenis, 'n' = verŝajne ne venos/ne venis, 'i' = vidita",
                                true),
                       //                       flag_kol('traktstato', 'N') /* TODO: kontrolu, ĉu bezonata! */,
//                        flag_kol('asekuri', 'N'),
//                        flag_kol('havas_asekuron', 'J'),
                       flag_kol('kontrolata', 'N',
                                "ĉu la administranto kontrolis? (J/N)",
                                array('elekto' => 'jesne')
                                /* (devus esti kontrolita) */),
                       flag_kol('havasMangxkuponon', 'N', ""),
                       flag_kol('havasNomsxildon', 'N', "")),
                 array(array('index', 'partoprenantoID')),
                 "Individuaj partoprenoj de partoprenantoj");
}

/**
 * tabeloj por la manĝo-mendada sistemo.
 */
function kreu_mangxsistemajn_tabelojn()
{
    $id_kol = id_kolumno();
    kreu_tabelon('mangxtempoj',
                 array($id_kol,
                       array('renkontigxoID', 'int'),
                       array('dato', 'DATE', 'komento' => "tago de la manĝo"),
                       flag_kol('mangxotipo', "",
                                "M = matenmanĝo, T = tagmanĝo, "
                                . "V = vespermanĝo, P = manĝpakaĵo"),
                       array('komento', 'text')),
                 array(array('renkontigxoID', 'dato', 'mangxotipo')),
                 "Manĝoj mendeblaj de la unuopaj partoprenantoj.");

    kreu_tabelon("mangxtipoj",
                 array($id_kol,
                       array('renkontigxoID', 'int'),
                       array('prezo', 'decimal' => '6,2'),
                       array('valuto', 'char' => 3, 'ascii',
                             'komento' => "La valuto de la prezo"),
                       flag_kol('mangxotipo', "",
                                "M = matenmanĝo, T = tagmanĝo, "
                                . "V = vespermanĝo, P = manĝpakaĵo"),
                       array('priskribo', 'char' => 50, 'tradukebla')),
                 array(array('renkontigxoID', 'mangxotipo')),
                 "Manĝotipoj haveblaj, ekzemple matenmanĝo, tagmanĝo etc.");
                       

    

    kreu_tabelon("mangxmendoj",
                 array(array('partoprenoID', 'int'),
                       array('mangxtempoID', 'int')),
                 array('primary' => array('partoprenoID', 'mangxtempoID')),
                 "Kiu manĝas kiam?");
}

/**
 * kreas la tabelon por la traduko-sistemo.
 */
function kreu_tradukan_tabelon() {
    /*
CREATE TABLE $tabelo (
  dosiero VARCHAR(100) NOT NULL,
  cheno VARCHAR(255) NOT NULL,
  iso2 CHAR(5) NOT NULL,
  traduko TEXT NOT NULL,
  tradukinto VARCHAR(255),
  komento TEXT NOT NULL,
  stato INT NOT NULL,
  kontrolita INT NOT NULL,
  kontrolinto VARCHAR(255),
  dato TIMESTAMP NOT NULL,
  PRIMARY KEY(dosiero, cheno, iso2),
  KEY di (dosiero,iso2),
  KEY iso2 (iso2),
  KEY `is` (iso2,stato)
) 
     */

    // kontrolita + kontrolinto ne estis uzitaj, do mi forĵetis.

    $kol_dos = array('dosiero', 'varchar' => 100, 'ascii');
    $kol_chen = array('cheno', 'varchar' => 255, 'ascii');

    kreu_tabelon("tradukoj",
                 array($kol_dos,
                       $kol_chen,
                       array('iso2', 'char' => 5, 'ascii'),
                       array('traduko', 'text'),
                       // todo: eble prenu entajpantoid?
                       array('tradukinto', 'varchar' => '255', 'null'),
                       array('komento', 'text'),
                       array('stato', 'int',
                             'komento' => "0 = aktuala, 1 = retradukenda"),
                       array('dato', 'timestamp'),
                       ),
                 array('primary' => array('dosiero', 'cheno', 'iso2'),
                       'di' => array('index', 'dosiero', 'iso2'),
                       'is' => array('index', 'iso2', 'stato')),
                 "Tabelo kun ĉiuj tradukitaĵoj de la traduksistemo (iloj/traduko/*.php)");
    
    kreu_tabelon("temp_tradukoj",
                 array($kol_dos, $kol_chen),
                 array('primary' => array('dosiero', 'cheno')),
                 "portempa tabelo por kalkuli tradukendajn ĉenojn",
                 'MEMORY');

}


/**
 * kreas ĉiujn tabelojn por la Renkontiĝo-administrilo.
 */
function kreu_necesajn_tabelojn()
{
    kreu_kategorisistemajn_tabelojn();
    kreu_cxambrajn_tabelojn();
    kreu_administrajn_tabelojn();
    kreu_kostosistemajn_tabelojn();
    kreu_partoprenantajn_tabelojn();
    kreu_pagajn_tabelojn();
    kreu_kotizosistemajn_tabelojn();
    kreu_tradukan_tabelon();
    if (mangxotraktado == "libera") {
        kreu_mangxsistemajn_tabelojn();
    }
}



$prafix = "..";
require_once($prafix . "/iloj/iloj.php");




/**
 * montras la SQL-esprimon, kaj se ni estas en instala
 * moduso, ankaŭ faras ĝin.
 */
function faru_SQL($sql)
{
    if (INSTALA_MODUSO) {
        echo $sql;
        eoecho ("\n faranta ...");
        flush();
        sql_faru($sql);
        eoecho("farita!\n");
    }
    else {
        echo $sql . "\n";
    }
}



malfermu_datumaro();

$tradukonomo = $prafix . "/dosieroj_generitaj/db_tradukoj.txt";
$tradukoj = fopen($tradukonomo, 'w');
fwrite($tradukoj, <<<END
# Liste de tradukeblaj tabelkampoj
# (Tiu ĉi dosiero en 'dosieroj_generitaj/db_tradukoj.txt'
#   estas kreita de la instalilo kaj esto anstataŭita per nova
#   versio je sekva rulado de la instalilo, do ne indas ŝanĝi
#   ĝin. Se vi volas ion aldoni, kreu novan dosieron (en alia
#   dosierujo) kaj menciu ĝin en la agordoj por la tradukilo.)
#
# Ĉiu linio estu en unu el la sekvaj formoj
#       tradukuKampon: kamponomo en: tabelnomo;
#       tradukuKampon: kamponomo en: tabelnomo helpeDe: kamponomo klarigoj: klarigo;
# 
#  - helpkamponomo estos montrata dum la redaktado, kaj uzata
#    por serĉi klarigojn.
#  - klarigo estas dosiero, en kiu la tradukilo serĉas klarigojn
#    pri la signifo (kaj eble sintaksaj specialaĵoj) de la
#    tradukendaĵo, montrotaj en la tradukilo.
#
# (ĉiuj nomoj estu en simplaj citiloj (').)
# Linioj komenciĝantaj per # estas komentoj.
# Aliaj formatoj estas rezervitaj.
#

END
       );

HtmlKapo("speciala");
echo("
<h1>Instalilo por la aligilo</h1>

<h2>Kreado de tabeloj</h2>
");

echo "<pre>";
kreu_necesajn_tabelojn();
echo "</pre>\n";

fclose($tradukoj);

echo ("<h3>Traduk-difinoj</h3>\n");

echo "<pre>";
readfile($tradukonomo);
echo "</pre>\n";

echo "<p>";
ligu("./#instalilo", "Reen al la instalilo-superrigardo");
echo "</p>\n";

HtmlFino();

