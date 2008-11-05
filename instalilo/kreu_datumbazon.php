<?php

  /**
   * Instalilo por la programo - parto por krei la datumbazojn.
   *
   * Ĝis nun ni nur printas la SQL-ordonojn por krei la datumbazstrukturon,
   * anstataŭ fari ion.
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage instalilo
   * @copyright 2008 Paul Ebermann.
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
   */
function kreu_tabelon($tabelnomo, $kampoj, $sxlosiloj=null, $komento="") {
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
    if ($komento) {
        $sql .= "\n   COMMENT='$komento'";
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
            $eroj[]= "\n     COMMENT '$val'";
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


function flag_kol($nomo, $defauxlto=null, $komento="")
{
    $kol = array($nomo, "char" => 1, 'ascii');
    if ($defauxlto) {
        $kol['default'] = $defauxlto;
    }
    if ($komento) {
        $kol['komento'] = $komento;
    }
    return $kol;
}


function id_kolumno() {
    return array("ID", 'int', 'auto_increment');
}

function nomo_kolumno() {
    return array('nomo', 'varchar'=>20);
}

function nomo_trad_kolumno() {
    return array('nomo', 'varchar'=>20, 'tradukebla');
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
    $nomo_trad_kol = nomo_trad_kolumno();
    $priskribo_kol = array('priskribo', 'text');
    $sistemoID_kol = array('sistemoID', 'int');
    $nomo_lokalingve_kol = array('nomo_lokalingve', 'varchar' => 20);
    $entajpanto_kol = array('entajpanto', 'int');
    
    kreu_tabelon("agxkategorioj",
                 array($id_kol,
                       $nomo_trad_kol,
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
                       $nomo_trad_kol,
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
                 array($id_kol, $nomo_trad_kol,
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
                 array($id_kol, $nomo_trad_kol,
                       $priskribo_kol, $sistemoID_kol,
                       // TODO: anstataux sxlosillitero eblu
                       //       havi plurajn tiajn.
                       array('kondicxo', 'int', 'komento' => "Kondicxo por esti en tiu kategorio"),
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
    $nomo_trad_kol = nomo_trad_kolumno();
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
                 
    kreu_tabelon('krompagoj',
                 array(array('tipo', 'int'),
                       array('kotizosistemo', 'int'),
                       array('krompago', 'decimal' => '6,2')),
                 array('primary' => array('tipo', 'kotizosistemo')),
                 "La alteco de la unuopaj krompagoj");
    
    kreu_tabelon('krompagotipoj',
                 array($id_kol, $nomo_trad_kol,// $nomo_lokalingve_kol,
                       array('mallongigo', 'varchar' => 10,
                             'komento' => "mallongigo por la finkalkulada tabelo"),
                       $entajpanto_kol, $priskribo_kol,
                       array('kondicxo', 'int'),
                       flag_kol('uzebla', 'j'),
                       flag_kol('lauxnokte', 'n',
                                "ĉu laŭnokta krompago (j), ĉu unufoja (n)?")),
                 array('nomo'),
                 "tipoj de eblaj krompagoj");

    kreu_tabelon('kondicxoj',
                 array($id_kol, $nomo_kol,
                       $entajpanto_kol,
                       $priskribo_kol,
                       array('kondicxoteksto', 'text',
                             'komento' => 
                             "kondiĉo-esprimo (=> iloj_kondicxoj.php)")),
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
                 array($id_kol, $nomo_kol,
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
                 "konfiguroj renkontiĝospecifaj");


    kreu_tabelon('tekstoj',
                 array($id_kol,
                       array('renkontigxoID', 'int'),
                       array('mesagxoID', 'varchar' => 30, 'ascii'),
                       // TODO: kelkaj tekstoj estu tradukeblaj,
                       //       sed ne cxiuj.
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

function kreu_partoprenantajn_tabelojn()
{
    $id_kol = id_kolumno();
    $nomo_trad_kol = nomo_trad_kolumno();
    $ppenoID_kol = array('partoprenoID', 'int');
    $ppantoID = array('partoprenantoID', 'int');
    
    kreu_tabelon("invitpetoj",
                 array(array('ID', 'int',
                             'komento' => "samtempe la identigilo de la partopreno"
                             /* pro tio ne havas auto_increment */),
                       array('pasportnumero', 'varchar' => 50,
                             'komento' => "la numero de la pasporto"),
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
                       array('entajpantoID', 'int')),
                 array('primary' => array('notoID', 'entajpantoID'),
                       array('index', 'notoID'),
                       array('index', 'entajpantoID')),
                 "kiu noto estas por kiu entajpanto?");

    
    kreu_tabelon('pagoj',
                 array($id_kol, $ppenoID_kol,
                       array('kvanto', 'decimal' => '6,2'),
                       array('dato', 'date'),
                       array('tipo', 'varchar' => 100)));

    // TODO: kial pago-tipo bezonas 100 kaj rabatkialo nur 30?
    kreu_tabelon('rabatoj',
                 array($id_kol, $ppenoID_kol,
                       array('kvanto', 'decimal' => '6,2'),
                       array('kauzo', 'varchar' => 30)));


    kreu_tabelon('partoprenantoj',
                 array($id_kol,
                       array('nomo', 'varchar' => 50, 'komento' => "familia nomo"),
                       array('personanomo', 'varchar' => 50),
                       array('sxildnomo', 'varchar' => 50),
                       flag_kol('sekso'),
                       array('naskigxdato', 'date'),
                       array('adresaldonajxo', 'varchar' => 50),
                       array('strato', 'varchar' => 50),
                       array('posxtkodo', 'varchar' => 50),
                       array('urbo', 'varchar' => 50),
                       array('provinco', 'varchar' => 50),
                       array('lando', 'int'),
                       array('sxildlando', 'varchar' => 50),
                       array('okupigxo', 'int'),
                       array('okupigxteksto', 'varchar' => 100),
                       array('telefono', 'varchar' => 50, 'ascii'),
                       array('telefakso', 'varchar' => 50, 'ascii'),
                       array('retposxto', 'varchar' => 50, 'ascii'),
                       flag_kol('retposxta_varbado', 'j'),
                       array('ueakodo', 'varchar' => 6, 'ascii'),
                       array('rimarkoj', 'varchar' => 100),
                       array('kodvorto', 'varchar' => 10, 'ascii')),
                 array(array('index', 'nomo'),
                       array('index', 'personanomo'),
                       array('index', 'naskigxdato'),
                       array('index', 'retposxto')),
                 "la partoprenantoj");

    kreu_tabelon('partoprenoj',
                 array($id_kol,
                       array('renkontigxoID', 'int'),
                       $ppantoID,
                       array('agxo', 'int',
                             'komento' => "estas kalkulita el naskiĝdato kaj renkontiĝodato, adaptenda, kiam tiuj ŝanĝiĝas."),
                       flag_kol('komencanto', 'N'),
                       flag_kol('nivelo', '?',
                                "lingva nivelo: f = flua, p = parolas, k - komencanto"),
                       array('rimarkoj', 'text' /* TODO: pripensu, ĉu ni ne tuj je la aliĝado kreu noton, 
                                                 kaj tiam povos forĵeti la rimarko-kampon */),
                       flag_kol('invitletero', 'N'),
                       array('invitilosendata' /* estu -ita */, 'date',
                             'komento' => "ne plu uzenda" /* TODO: tamen ankoraŭ multfoje uzita! */),
                       array('pasportnumero', 'varchar' => 100, 'default' => null, 
                             'komento' => "ne plu uzenda" /* TODO: tamen ankoraŭ multfoje uzita! */),
                       flag_kol('retakonfirmilo'),
                       flag_kol('germanakonfirmilo', 'N') /* TODO: plurlingvaj konfirmiloj */,
                       array('1akonfirmilosendata' /* estu -ita */, 'date'),
                       array('2akonfirmilosendata' /* estu -ita */, 'date'),
                       flag_kol('partoprentipo', 't'),
                       array('de', 'date'),
                       array('gxis', 'date'),
                       flag_kol('vegetare', 'N'),
                       /* la sekvaj tri kampoj nur, kiam loka asocio volas membriĝon.
                        TODO: prenu el konfiguro, kaj depende de tio aldonu la
                        kampojn. */
                       flag_kol('GEJmembro', 'N'),
                       flag_kol('surloka_membrokotizo', '?'),
                       array('membrokotizo', 'decimal' => '6,2'),
                       flag_kol('tejo_membro_laudire', 'n'),
                       flag_kol('tejo_membro_kontrolita', '?'),
                       array('tejo_membro_kotizo', 'decimal' => '6,2'),
                       flag_kol('KKRen', 'N', "Ĉu membro de la organiza teamo?"),
                       flag_kol('domotipo'),
                       flag_kol('litolajxo', 'N') /* TODO: verŝajne forĵetenda. */,
                       flag_kol('kunmangxas', 'N'),
                       flag_kol('listo', 'N', "Ĉu aperi en la (interreta) listo de aliĝintoj?"),
                       flag_kol('intolisto', 'N', "Ĉu aperi en la post-renkontiĝa partopreninto-listo? (J/N)"),
                       array('pagmaniero', 'varchar' => 10),
                       array('kunKiu', 'varchar' => 50),
                       array('kunKiuID', 'int'),
                       flag_kol('cxambrotipo', 'g' , "g = gea, u = unuseksa"),
                       flag_kol('dulita', 'N',
                                "J = mendis dulitan, u = unulitan, N = pli grandan"),
                       flag_kol('ekskursbileto', 'N'),
                       /* jen venas diversaj programproponoj - eble simpligu (nur unu tia kampo?),
                        aŭ aŭtomate faru noton el ĝi. Sed tiam notoj estu pli
                        facile trovebla ... */
                       array('tema', 'text'),
                       array('distra', 'text'),
                       array('vespera', 'text'),
                       array('muzika', 'text'),
                       array('nokta', 'text'),
                        
                       array('aligxdato', 'date'),
                       array('malaligxdato', 'date'),
                       array('aligxkategoridato', 'date'),
                       flag_kol('alvenstato', 'v'),
                       flag_kol('traktstato', 'N') /* TODO: kontrolu, ĉu bezonata! */,
                       flag_kol('asekuri', 'N'),
                       flag_kol('havas_asekuron', 'J'),
                       flag_kol('kontrolata', 'N'),
                       flag_kol('havasMangxkuponon', 'N'),
                       flag_kol('havasNomsxildon', 'N')),
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
                       array('prezo', 'decimal' => '6,2'),
                       array('komento', 'text')),
                 array(array('dato', 'mangxotipo')),
                 "Manĝoj mendeblaj de la unuopaj partoprenantoj.");
    

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

    // kontrolita + kontrolinto ne estis uzitaj, do mi forjxetis.
    kreu_tabelon("tradukoj",
                 array(array('dosiero', 'varchar' => 100, 'ascii'),
                       array('cheno', 'varchar' => 255, 'ascii'),
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
 * moduso, ankaux faras gxin.
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
#    por sercxi klarigojn.
#  - klarigo estas dosiero, en kiu la tradukilo sercxas klarigojn
#    pri la signifo (kaj eble sintaksaj specialajxoj) de la
#    tradukendajxo, montrotaj en la tradukilo.
#
# (cxiuj nomoj estu en simplaj citiloj (').)
# Linioj komenciĝantaj per # estas komentoj.
# Aliaj formatoj estas rezervitaj.
#

END
       );

HtmlKapo();

echo "<pre>";
kreu_necesajn_tabelojn();
echo "</pre>\n";

fclose($tradukoj);

echo "<pre>";
readfile($tradukonomo);
echo "</pre>\n";

HtmlFino();

?>