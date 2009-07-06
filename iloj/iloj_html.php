<?php

  /**
   * Kelkaj funkcioj rilataj al HTML-eldono.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki,
   *            2004-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /* öäüÖÜÄ€ßĉĝĵĥŝŭ«žčĈĜĴĤŜŬ»ŽČ */



  /**
   * metas HTML-elementojn por stilfolio kaj titolo,
   * depende de la aktuala MODUSO.
   */
function metu_stilfolion_kaj_titolon() {
        $dosiernomo =  $GLOBALS['prafix']."/stilo_".MODUSO.".css";
    if (DEBUG)
        {
            echo "<!-- MODUSO:      " . MODUSO .
                "\n     dosiernomo:  " . $dosiernomo .
                "\n     laborejo:    " . getcwd() . 
                "\n     def(MODUSO): " . defined("MODUSO") .
                "\n-->\n"; 
        }
    if (!(defined("MODUSO") and file_exists($dosiernomo)))
        {
            $dosiernomo = $GLOBALS['prafix'] . "/stilo_defauxlta.css";
        }
    echo '     <link rel="stylesheet" href="' . $dosiernomo .
        '" type="text/css" charset="iso-8859-1">' . "\n";
    // TODO: titolo konfigurebla!
    eoecho ("     <title>" . renkontigxo_nomo . "-Aligilo [".  MODUSO .
            "]</title>\n");
 /*
  (estas intence "aligilo" kaj ne "aliĝilo",
  ĉar ni per ĝi _igas_ la homojn _al_ la
  renkontiĝo, ne mem aliĝas per ĝi ...)
  
  La Aliĝilo estas aparta parto de ĝi (per kiu la
  ppantoj iĝas al la renkontiĝo = aliĝas).
 */
}

  /**
   * eldonas la HTML kapon por la kutimaj paĝoj.
   *
   * Tiu mencias la HTML-dokumenttipon, kodigon (UTF-8), lingvon (eo),
   *  stilfolion (depende de {@link MODUSO} kaj iun ĉiam uzatan
   *   {@link cxiujpagxoj.js Ĵavoskripton}.
   *
   * Kutime ĉiu paĝo aspektu tiel:
   * <code>
   * HtmlKapo();
   *  // enhavo
   * HtmlFino();
   * </code>
   *
   * @param string $klaso se donita, uzas class=$klaso kiel atributo
   *                por la <body>-Elemento.
   * @see HtmlFino()
   */ 
function HtmlKapo($klaso = "")
{
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<!-- <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> -->
<html>
  <head>
     <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
     <meta http-equiv="content-language" content="eo">
<?php
        debug_echo("<!-- DEBUG-moduso. -->");
        metu_stilfolion_kaj_titolon();

?>
     <base target="anzeige">
     <script type="text/javascript" src="iloj/cxiujpagxoj.js" charset="iso-8859-1"></script>
  </head>
  <body <?php
 if ($klaso!="") {echo "class='$klaso'";}
   ?>>
 <a name="top"></a>
 <?php if (! EBLAS_SKRIBI)
      {
          eoecho("<p class='averto'>
        La programo nun estas en nurlega stato.
        C^iuj &#349;ajnaj s^ang^oj ne efikas.
        </p>");
      }
}

/**
 * La fino de la HTML-paĝo.
 *
 * @see HtmlKapo()
 */
function HtmlFino()
{
    ?>
  </body>
</html><?php
}



/* ####################################### */
 /* echo kun Eo signo laŭ unikodo aŭ 'xe' */
 /* ####################################### */


  /**
   * Eldonas eo-transformitan tekston.
   *
   * @param eostring $io eldonenda teksto, en c^-kodigo.
   * @uses eotransform()
   */
 function eoecho($io)
{
    echo eotransform($io);
}


/**
 * Montras renkontiĝoelektilon.
 *
 * La HTML-nomo estas "formrenkontigxo",
 * la elektota valoro estas la identigilo
 * de la renkontiĝo.
 *
 * @param string $antauxelekto la identigilo de tiu renkontiĝo,
 *                   kiu estu jam elektita.
 *                   se vi forlasas, elektiĝas la plej
 *                   malfrue komenc(o|a|i)ta renkontiĝo
 *                   ( = la unua en la listo).
 * @param int $grandeco kiom granda estu la listo, defaŭlta valoro estas 5.
 *
 */
function montru_renkontigxoelektilon($antauxelekto = "plej_nova",$grandeco='5')
{
    // Elektilo por la renkontiĝo:

    echo "<select name='formrenkontigxo' size='$grandeco'>\n";
    if ($antauxelekto == "plej_nova")
        {
            $unua = true;
        }
    $result = sql_faru(datumbazdemando(array("ID", "nomo", "loko",
                                             "de", "gxis"),
                                       "renkontigxo",
                                       "",
                                       "",
                                       array("order" => "de DESC")
                                       ));
    while ($row = mysql_fetch_array($result, MYSQL_BOTH))
        {
            echo "<option";
            // elektu aŭtomate la unuan renkontiĝon
            if ($unua or ($row["ID"] == $antauxelekto))
                {
                    echo ' selected="selected"';
                    $unua = false;
                }
            $temp = "$row[nomo] en $row[loko] ($row[de] - $row[gxis])";
            echo " value='$row[ID]'>";
            eoecho ($temp)."\n";
        }
    echo " </select>  <BR>\n";
}


/**
 * Montras elektilon por lando.
 *  La elektilo-nomo estas "lando".
 *
 * @param int $alteco  la nombro da linioj en la elektilo.
 *           se 1, tiam estas elektilo kun klapmenuo,
 *           alikaze estos plurlinia elektilo.
 * @param int $lando  la identigilo de la antaŭelektita lando.
 *           (se vi nenion donis, uzos la konstanton HEJMLANDO.)
 * @param lingvokodo $lingvo identigilo por la lingvo uzenda por
 *                   la teksto (kaj la nomo de la landokategorio).
  @param boolean $loka uzu la loka-lingvan varianton de la landonomo
            (ekzemple germana), se <var>$loka</var> estas donita kaj io, kio
            iĝas 'true'.
 * @param string $klaso   iu html-atribut-fragmento, ekzemple
 *            class='mankas' por aldoni al la <select>-elemento.
 * @param Renkontigxo $renkontigxo renkontiĝo-objekto - rilate al ties
 *                     kotizosistemo ni montras la landokategoriojn.
 */
function montru_landoelektilon($alteco, $lando=HEJMLANDO, $lingvo=null,
                               $klaso="", $renkontigxo=null)
{
    // provizore nur cxi tie
    require_once($GLOBALS['prafix'] . "/iloj/sqlobjektoj.php");


    debug_echo( "<!-- lando: $lando -->");
  
    $renkontigxo = kreuRenkontigxon($renkontigxo);
    $kotSis = $renkontigxo->donu_kotizosistemon();

    $landoKatSisID = $kotSis->datoj['landokategorisistemo'];

    $sqltrad =
        datumbazdemando(array("l.ID", 'l.nomo',
                              't.traduko' => 'trad'),
                        array("landoj" => "l", "tradukoj" => "t",
                              ),
                        array("l.id      = (t.cheno + 0)",
                              "t.iso2    = '$lingvo'",
                              "t.dosiero = 'datumbazo:/landoj/nomo'"),
                        "",
                        array("order" => "nomo ASC"));
    

    $sql = datumbazdemando(array('l.nomo' => "nomo",
                                 'k.kategorioID',
                                 "l.ID"),
                           array("landoj" => "l",
                                 "kategorioj_de_landoj" => "k"),
                           array("k.sistemoID" => $landoKatSisID,
                                 "k.landoID = l.ID"),
                           "",
                           array("order" => "nomo ASC"));
    $listilo = new SQL_outer_left_join("nomo");
    $listilo->maldekstra_sql($sql);
    $listilo->dekstra_sql($sqltrad);

    $landolisto = array();

    while($linio = $listilo->sekva()) {

        debug_echo("<!-- \nlinio: " . var_export($linio, true) . " -->");
        
        $landonomo = kalkulu_landonomon($linio['nomo'], $linio['trad'],
                                        $lingvo);
        $katNomo = kalkulu_kategorinomon($linio['kategorioID'], $lingvo);
        
        $landolisto[(string)$linio['ID']] =  $landonomo . " (" . $katNomo . ")";


    }

    debug_echo ("<!--" . var_export($landolisto, true) . "-->");

    ordigu_laux_lingvo($landolisto, $lingvo);

    elektilo_simpla("lando", $landolisto, $lando, "",
                    $alteco, false, $klaso);

//     echo "<select name='lando' size='{$alteco}' {$klaso}>\n";
//         echo "  <option";
//         if ($linio['ID'] == $lando) {
//             echo " selected='selected'";
//         }
//         echo " value='" . $linio['ID'] . "'>";
//         eoecho( $landonomo . " (" . $katNomo . ")");
//         echo "</option>\n";

//     echo "</select>\n";

}   // montru_landoelektilon

/**
 * ordigas array laux lingvo.
 *
 * @todo faru pli bone konfigurebla - nun estas speciale
 *    por la haveblaj LOCALE-valoroj en la eo.de-servilo.
 */
function ordigu_laux_lingvo(&$array, $lingvo) {
    if ($lingvo and defined("STR_KOMPARO_" . $lingvo)) {
        $komp = constant("STR_KOMPARO_" . $lingvo);
        uasort($array, $komp);
    }
    else if ($lingvo) {
        metu_ordigolokalajxon($lingvo);
        asort($array, SORT_LOCALE_STRING);
    }
}

function metu_ordigolokalajxon($lingvo) {
        switch ($lingvo) {
        case 'de':
            setlocale(LC_COLLATE, "de_DE.utf8@euro");
            break;
        case 'eo':
            setlocale(LC_COLLATE, "eo.utf8");
            break;
        case 'cs':
            setlocale(LC_COLLATE, 'cs_CZ.utf8');
            break;
        case 'pl':
            setlocale(LC_COLLATE, 'pl_PL.utf8');
            break;
        default:
            setlocale(LC_COLLATE, "C");
        }
    
}


/**
 * elektas la landonomon inter interna kaj tradukita nomo,
 * ankaux depende de elekto de lingvo.
 *
 * @param eostring   $interna_nomo  la interna nomo de la lando.
 * @param tradstring $tradukita  la traduknomo en $lingvo el la traduktabelo,
 *                               aux NULL.
 * @param lingvokodo $lingvo  kodo de lingvo.
 */
function kalkulu_landonomon($interna_nomo, $tradukita, $lingvo)
{
    if ($tradukita and $lingvo == 'eo') {
        return transformu_x_al_eo($tradukita);
    } else if ($tradukita) {
        return $tradukita;
    } else if ($lingvo) {
        $landonomo =  $interna_nomo;
        $GLOBALS['bezonis-eo-tekston'] = true;
        if (marku_traduko_eo_anstatauxojn) {
            $landonomo .= "¹";
        }
        return $landonomo;
    } else {
        return $interna_nomo;
    }

    
}   // kalkulu_landonomon

/**
 * Eltrovas la landokategori-nomon el kategorio-ID kaj lingvokodo.
 * @todo trovu cimon
 */
function kalkulu_kategorinomon($katID, $lingvo) {
    
    static $landokategorioj = array();
    
    if (!$landokategorioj[$katID]) {
        $landokategorioj[$katID] =
            new Landokategorio($katID);
    }
    if ($lingvo) {
        $katNomo =
            $landokategorioj[$katID]->tradukita("nomo",
                                                $lingvo);
    } else {
            $katNomo = $landokategorioj[$katID]->datoj['nomo'];
    }
    return $katNomo;
}


/**
 * Montras entajpejon ene de tabellinio (<samp><tr/></samp>).
 *
 *<pre>
 * .--------.----------------------.
 * | teksto | [_______] postteksto |
 * '--------'----------------------'
 *</pre>
 *
 * @param string $teksto    la titolo (aperos en <th/>).
 * @param string $nomo      la nomo de la tekstkampo (por sendi al la servilo)
 * @param string $io        la komenca teksto de la tekstkampo
 * @param int    $grandeco  la larĝeco de la tekstkampoj (proksiume en
 *                             literoj)
 * @param string $postteksto teksto montrita post la entajpejo.
 * @param string $manko      erarmesaĝo, kiam $io = "" (nur uzita, se ne "").
 * @param string $kutima     defaŭlta valoro - uzata, se $io = "".
 * @param string $kasxe      se 'j', kaŝas la entajpitaĵon (uzenda
 *                           por pasvortoj).
 * @uses entajpejo()
 */
function tabelentajpejo ($teksto, $nomo, $io="", $grandeco="",$postteksto="",
                         $manko="", $kutima="", $kasxe="n")
{
    eoecho("    <tr><th><label for='$nomo'>$teksto</label></th><td>");
    entajpejo("", $nomo, $io, $grandeco, $manko, $kutima, $postteksto, $kasxe);
    echo "</td></tr>\n";
}


/**
 * Montras grandan entajpejon ene de tabellinio (<samp><tr>...</tr></samp>).
 *
 *<pre>
 * .--------.---------------------------.
 * | teksto | [¯¯¯¯¯¯¯¯¯¯¯¯] postteksto |
 * |        | [            ]            |
 * |        | [____________]            |
 * '--------'---------------------------'
 *</pre>
 *
 * @param string $teksto    la titolo (en <th/>).
 * @param string $nomo      la nomo de la tekstkampo (por sendi al la servilo)
 * @param string $io        la komenca teksto de la tekstkampo
 * @param int $kolumnoj  la larĝeco de la tekstkampo (proksiume en literoj)
 * @param int $linioj    la alteco de la tekstkampo (nombro da tekstlinioj)
 * @param string $postteksto  teksto montrita post la entajpejo.
 * @param string $manko erarmesaĝo, kiam $io = "" (nur uzita, se ne "").
 * @param string $kutima  defaŭlta valoro  uzata, se $io = "".
 * @uses granda_entajpejo()
 */
function granda_tabelentajpejo($teksto, $nomo, $io="",  $kolumnoj="", $linioj="",
                               $postteksto="", $manko="", $kutima="")
{
    eoecho("    <tr><th>$teksto</th><td>");
    granda_entajpejo("", $nomo, $io, $kolumnoj, $linioj, $manko, $kutima, $postteksto);
    echo "</td></tr>\n";
}



/**
 * Entajpejo por tekstoj.
 *
 *<pre>
 *  teksto  [_____]  postteksto
 *</pre>
 *
 * @param string $teksto     priskribo antaŭ la bokso.
 * @param string $nomo       nomo de la input-elemento por sendi ĝin al la servilo
 * @param string $io         valoro por enmeti
 * @param int $grandeco      grandeco de la entajpejo
 * @param string $manko      ebla erarmesaĝo (por testi, ĉu $io estas malplena  -->malplentesto())
 * @param string $kutima     valoro por enmeti, se $io == "".
 * @param string $postteksto teksto por montri post la entajpejo
 * @param string $kasxe      se 'j', tiam estu entajpejo por
 *               pasvortoj (= montras nur *).
 */
function entajpejo($teksto, $nomo, $io="", $grandeco="", $manko="",
                   $kutima="", $postteksto="", $kasxe="n")
{
    eoecho ($teksto);
    echo " <input name='$nomo' size='$grandeco' ";
    if ($kasxe == "j")
        {
            echo "type='password' ";
        }
    else
        {
            echo "type='text' ";
        }
    echo "value = '" . htmlspecialchars($io ? $io : $kutima,
                                        ENT_QUOTES) ."'";
    echo "/>";
    if ($postteksto)
        {
            eoecho (" " .$postteksto."\n");
        }
    echo "<br/>";
    if ($manko)
        {
            malplentesto($io,$manko);
        }
}

/**
 * Entajpejo por tekstoj
 *
 *<pre>
 *  teksto  [_____]  postteksto
 *</pre>
 *
 * La ĉefa diferenco (krom malapero de $manko)
 * al {@link entajpejo()} estas, ke fine de ĝi ne aperas <br/>.
 * Krome ĝi, se $io estas malplena, uzas la enhavon de
 *  $_REQUEST[$nomo] por havi komencan valoron (kutima nur
 *   estas uzata, se ankaŭ tio malplenas).
 *
 * @param string $teksto      priskribo antaŭ la bokso.
 * @param string $nomo        nomo de la input-elemento por sendi ĝin al la servilo
 * @param string $io          komenca valoro de la kampo. Se malplena, uzas
 *                $_REQUEST['nomo'].
 * @param int $grandeco      grandeco de la entajpejo
 * @param string $kutima     valoro por enmeti, se kaj $io == "" kaj
 *  $_REQUEST[$nomo] == ""
 * @param string $postteksto teksto por montri post la entajpejo
 * @param string $kasxe      se 'j', tiam estu entajpejo por
 *               pasvortoj (= montras nur *).
 * @param string $htmlaldonajxo aldona HTML-atributoj
 * @global string _REQUEST[$nomo] tion ni uzas, se $io == "".
 *
 */
function simpla_entajpejo($teksto, $nomo, $io = "",  $grandeco="",
                          $kutima="", $postteksto="", $kasxe="n",
                          $htmlaldonajxo="")
{
    if (! $io)
        $io = $_REQUEST[$nomo];
    eoecho ($teksto);
    echo " <input name='$nomo' size='$grandeco' ";
    if ($kasxe == "j")
        {
            echo "type='password' ";
        }
    else
        {
            echo "type='text' ";
        }

    if ($io)
        {
            echo "value='".htmlspecialchars($io, ENT_QUOTES)."' ";
        }
    else
        {
            echo "value='".htmlspecialchars($kutima, ENT_QUOTES)."'";
        }
    if ($htmlaldonajxo) {
        echo " " . $htmlaldonajxo;
    }
    echo "/>";
    if ($postteksto)
        {
            eoecho (" " .$postteksto."\n");
        }
}



/**
 * Montras grandan entajpejon.
 *
 *<pre>
 * teksto  [¯¯¯¯¯¯¯¯¯¯¯¯]  postteksto
 *         [            ]
 *         [____________]
 *</pre>
 *
 * @param string $teksto    la titolo (en <th/>).
 * @param string $nomo      la nomo de la tekstkampo (por sendi al la servilo)
 * @param string $io        la komenca teksto de la tekstkampo
 * @param int $kolumnoj     la larĝeco de la tekstkampo (proksiume en literoj)
 * @param int $linioj       la alteco de la tekstkampo (nombro da tekstlinioj)
 * @param string $postteksto teksto montrita post la entajpejo.
 * @param string $manko      ebla erarmesaĝo (por testi, ĉu $io estas
 *                           malplena, vidu {@link malplentesto()})
 * @param string $kutima     valoro por enmeti, se $io == "".
 */
function granda_entajpejo($teksto, $nomo, $io="", $kolumnoj="", $linioj="", $manko="",
                          $kutima="", $postteksto="")
{
    eoecho ($teksto);
    echo " <textarea name='$nomo' ";
    if ($linioj)
        {
            echo "rows='$linioj' ";
        }
    if ($kolumnoj)
        {
            echo "cols='$kolumnoj' ";
        }
    echo ">";
    if ($io)
        {
            echo $io;
        }
    else
        {
            echo $kutima;
        }
    echo "</textarea>";
    if ($postteksto)
        {
            eoecho ("<br/> " .$postteksto."\n");
        }
    echo "<br/>";
    if ($manko)
        {
            malplentesto($io,$manko);
        }
}


/**
 * Simpla radiobutono.
 *
 * <pre>
 * (_)   aŭ   (X)
 * </pre>
 *
 * Tiu simpla butono aperos sen teksto.
 *
 * @param string $nomo    la nomo (por sendi)
 * @param string $elekto  valoro por decidi, ĉu elekti tiun ĉi kampon.
 * @param string $komparo se $elekto == $komparo, ĉi tiu entajpbutono estas
 *                      jam elektita (<samp> (*) </samp>).
 *                      $komparo estas ankaŭ uzata kiel valoro por sendi.
 * @param string $kutima  se $elekto == "" kaj $kutima == "kutima", la
 *                      entajpbutono ankaŭ estas elektita. (defaŭlto: "")
 * @param string $skripto se donita, la skripto (javaskripto uzebla
 *                     en evento-atributoj de HTML) estas vokita dum ŝanĝo
 *                     de la stato (onfocus, onblur, onclick). Ĝi aperos
 *                     trifoje, do prefere estu mallonga (ekzemple voko de
 *                     funkcio difinita aliloke).
 * @see entajpbutono()
 */
function simpla_entajpbutono($nomo, $elekto, $komparo, $kutima="", $skripto="")
{
    echo "<input type='radio'";
    $id = "$nomo=$komparo";
    if ((strpos($id, '[') === false) and
        (strpos($id, ']') === false)) {
        echo " id='$id'";
    }
    echo " name='$nomo' value='$komparo' ";
    if($elekto == $komparo or ($elekto == "" and $kutima == "kutima"))
        {
            echo "checked='checked' ";
        }
    if($skripto)
        {
            $skripto = htmlspecialchars($skripto, ENT_QUOTES);
            echo /* "onchange='$skripto'" */" onfocus='$skripto' onblur='$skripto'".
                " onclick='$skripto'";
        }
    echo "/>";
}


/**
 * Radiobutono en tabellinio.
 *
 *<pre>
 *  .-----------------------------.
 *  |  teksto | (_) | postteksto  |
 *  '-----------------------------'
 *</pre>
 *  aŭ
 *<pre>
 *  .---------------------------.
 *  |  teksto | (_) postteksto  |
 *  '---------------------------'
 *</pre>
 *
 * @param eostring $teksto  teksto antaŭ la entajpbutono
 * @param   string $nomo    nomo de la variablo (por sendi al la servilo)
 * @param   string $elekto  valoro por decidi, ĉu elekti tiun ĉi kampon
 *                        (= la aktuala valoro de la elektitaĵo)
 * @param   string $valoro  se $elekto == $komparo, ĉi tiu entajpbutono estas
 *                      elektita (<samp> (*) </samp>).
 *                      $komparo estas ankaŭ uzata kiel valoro por sendi.
 * @param eostring $postteksto  estos montrata post la entajpbutono.
 * @param   string $kutima      se kutima == "kutima" kaj $io == "", tiam
 *                            la butono estas ankaŭ komence elektata.
 * @param boolean $du_cxeloj  se <val>true</val>, metas nur du ĉelojn
 *                 (t.e. postteksto kaj butonon en la saman ĉelon).
 *
 * @uses simpla_entajpbutono()
 * @see entajpbutono()
 */
function tabel_entajpbutono($teksto,$nomo,$elekto,$valoro,$postteksto="",$kutima="", $du_cxeloj=false)
{
    eoecho ("<tr><th><label for='$nomo=$valoro'>" . $teksto .
            "</label></th><td>");
    simpla_entajpbutono($nomo, $elekto, $valoro, $kutima);
    if (! $du_cxeloj) {
        echo "</td><td>";
    }
    else {
        echo " ";
    }
    eoecho($postteksto . "</td></tr>\n");
}

/**
 * Radiobutono silsimpla.
 *
 *<pre>
 *    teksto  (_)  postteksto
 *</pre>
 *
 * @param string $teksto     teksto antaŭ la entajpbutono.
 * @param string $nomo       nomo de la variablo (por sendi al la servilo)
 * @param string $io         valoro por kompari al $komparo. Kutime la aktuala
 *                           valoro de la decidenda variablo.
 * @param string $komparo    se $komparo == $io, la entajpbutono estas komence elektata [  (*)  ]
 * @param string $valoro     kio estos sendita al la servilo, se la butono
 *                           estas elektita dum la sendado. Kutime la sama kiel
 *                           $komparo.
 * @param string $postteksto estos montrata post la entajpbutono.
 * @param string $kutima     se kutima == "kutima" kaj $io == "", tiam
 *                           la butono estas ankaŭ komence elektata.
 * @param string $htmlaldonajxo aldona HTML-atributoj
 * @see simpla_entajpbutono()
 * @see tabel_entajpbutono()
 */
function entajpbutono($teksto,$nomo,$io,$komparo,$valoro,$postteksto="",$kutima="", $htmlaldonajxo="")
{
    eoecho ($teksto."\n");
    echo " <input name='$nomo' type='radio' ";
    if ( ($io == $komparo)
         or ( (!$io)
              and ($kutima == "kutima")
              )
         )
        {
            echo "checked='checked' ";
        }
    if ($htmlaldonajxo) {
        echo $htmlaldonajxo . " ";
    }
    echo "VALUE='$valoro'>&nbsp;";
    eoecho ($postteksto."\n");
}


/**
 * markobutono silsimpla.
 *
 * <pre>
 *   teksto [X] postteksto
 * </pre>
 *
 * @param string $teksto     teksto antaŭ la bokso.
 * @param string $nomo       nomo de la inputelemento (uzata por sendi la valoron al la servilo)
 * @param string $io         valoro de la bokso - aŭ $komparo ([X]) aŭ ne ([ ]).
 * @param string $komparo    valoro por kompari al $io (se sama, metu krucon).
 * @param string $valoro     kio estos resendota al la servilo, kiam estos
 *                           kruco. (kutime la sama kiel $komparo.)
 * @param string $postteksto teksto por montri post la bokso.
 * @param string $kutima     se != "" kaj $io == "", tiam estas kruco.
 * @param string $kasxe      se "jes" (defaŭlto), aldonu kaŝitan
 *                           <input>-Elementon, kiu metas (dum la sendado)
 *                           la valoron de $nomo al NE, se ne estos kruco.
 *                       (Alikaze tute ne estos valoro sendota al la servilo.)
 * @see jes_ne_bokso()
 */
function entajpbokso($teksto, $nomo, $io, $komparo, $valoro,
                     $posttexto="", $kutima="", $kasxe="jes")
{
    eoecho ($teksto."\n");
    if ($kasxe=="jes")
        echo " <input name='$nomo' type='hidden' value='NE'>\n";//necesas
    echo " <input name='$nomo' type='checkbox' ";
    if ( ($io == $komparo)
         or ( (!$io)
              and ($kutima)
              )
         )
        {
            echo "checked='checked' ";
        }
    echo "value='$valoro'>&nbsp;";
    eoecho ($posttexto."\n");
}


/**
 * markobutono, eble kun skripto vokata je ŝanĝoj.
 * 
 * <pre>
 *    [X]  aŭ  [_]
 * </pre>
 * Se markita, dum sendado sendas "JES", alikaze "NE".
 *
 * @param string $nomo la nomo de la markobutono.
 * @param string|boolean $io
 *            se $io === true aŭ $io[0] == 'J', la bokso estas markita.
 * @param string $skripto ĵavoskripto, estos vokata por ĉiu
 *                       ŝanĝo de la stato, t.e. je krucigo
 *                       kaj senkrucigo.
 * @see entajpbokso()
 */
function jes_ne_bokso($nomo,$io,$skripto="")
{
    echo " <input name='$nomo' type='hidden' value='NE'>\n";
    echo " <input name='$nomo' type='checkbox' ";
    if ($io === true or $io[0] == 'J')
        {
            echo "checked='checked' ";
        }
    if($skripto)
        {
            echo "onchange='" .htmlspecialchars($skripto, ENT_QUOTES) . "' ";
        }
    echo "value='JES'>\n";
}


/**
 * Kombino de {@link entajpbokso()} kaj {@link entajpejo()}.
 *
 *<pre>
 *   [_] teksto [________] postteksto
 *</pre>
 *
 * @param string $boxnomo
 * @param string $boxio
 * @param string $boxvaloro
 * @param string $teksto
 * @param string $postteksto
 * @param string $ejnomo
 * @param string $ejio
 * @param int    $grandeco longeco de la kampo.
 * @param string $manko    erareldono, uzata se $boxio == $boxkomparo (= hoko)
 *                         sed $ejio = "" (= nenio entajpita).
 * @todo daŭrigi dokumentadon.
 * @uses entajpbokso()
 * @uses entajpejo()
 */
function entajpboksokajejo($boxnomo, $boxio, $boxkomparo, $boxvaloro,
                           $teksto,$postteksto,
                           $ejnomo, $ejio, $grandeco, $manko)
{
    if ($ejio)
        {
            $boxio = "JES";
        }
    entajpbokso("",$boxnomo,$boxio,$boxkomparo,$boxvaloro);
    eoecho ($teksto);
    entajpejo("",$ejnomo,$ejio,$grandeco,"","",$postteksto);
    if ( ($boxio == $boxkomparo)
         and ($ejio == ""))
        {
            erareldono ($manko);
        }
}


/* ################################################# */
 /* testas je malpleneco kaj enkodas laŭ HTML leĝoj */
 /* ################################################# */

 function malplentesto (&$io,$err="")
{
    global $parto;
    // TODO:? Ĉu vi povas diri, kion fakte faras (faru) la funkcio malplentesto()?

    // tranformas ĉion HTML specialan signon, por ke mi ĵuste enskibas ĝin en la datumaro

    //$io = HTMLsekurigi(&$io); geht leider nicht, wegen uebergabeproblemen.
    // HTML sicherung muss noch bei JEDEM String - einmalig erfolgen.

    //$io = /*htmlentities*/(str_replace("'","`",$io));

    if ($parto and /*(($parto == "korektigi") or ($parto=="kontroli"))and */($io==""))
        {
            if ($err)  // malgucken, obs spaeter mal auch ohne geht trotzdem geht.
                {
                    erareldono ("Bonvolu entajpu vian ".$err);
                }
            $parto="korektigi";
        }
}

/**
 * eldonas atentigan tekston, ekzemple se mankas necesaj
 * datumoj en iu entajpformularo.
 * 
 * @param eostring $err la erarmesagxo.
 */
function erareldono ($err)
{
    echo "<strong class='averto'>";
    eoecho ($err);
    echo "!</strong><br/>";
}


/**
 * tabellinio kun kaŝitaj formular-informo.
 *
 *<pre>
 * .---------.------------.
 * | teksto  | postteksto |
 * '---------'------------'
 *</pre>
 *
 * Tenas datumojn kaŝe, sed krome montras tabellinion kun
 * titolo kaj aldona teksto (aŭ valoro).
 *
 * @param string $teksto      la titolo
 * @param string $nomo        la nomo de la variablo
 * @param string $valoro      la sendenda valoro
 * @param eostring $postteksto  teksto montrenda post la kaŝilo. Se malplena,
 *                montras $valoron.
 * @uses tenukasxe()
 * @see tabela_montrilo()
 */
function tabela_kasxilo($teksto, $nomo, $valoro, $postteksto="")
{
    eoecho ("<tr>\n<th><label for='$nomo'>" . $teksto . "</label></th>\n");
    echo "<td>";
    tenukasxe($nomo, $valoro);
    if ($postteksto)
        eoecho($postteksto);
    else
        eoecho($valoro);
    echo "</td>\n</tr>\n";
}


/**
 * tabellinio sen io ajn formularero.
 *
 * Krome gxi same funkcias kiel {@link tabela_kasxilo()}.
 *
 * @param eostring $teksto
 * @param eostring $postteksto
 */
function tabela_montrilo($teksto, $postteksto="") {
    eoecho ("<tr>\n<th>" . $teksto);
    echo "</th>\n<td>";
    eoecho($postteksto);
    echo "</td>\n</tr>\n";
}


/**
 * Kreas la HTML-kodon por valoro en formularo, kiu
 * ne montriĝas, sed tamen sendiĝos kun la datoj
 * (<input type="hidden" .../>)
 *
 * @param string $nomo la nomo de la variablo
 * @param string|int $valoro la valoro de la variablo.
 */
function tenukasxe($nomo,$valoro)
{
    echo "<input type='hidden' name='$nomo' value='" .
        htmlspecialchars($valoro, ENT_QUOTES) . "' />\n";
}


/**
 * Metas HTML-ligilon, se la nuna entajpanto rajtas
 * iun agon. Alikaze montras strekitan tekston (sen ligilo) aŭ nenion.
 *
 * @param   string $kien   la ligota paĝo
 * @param eostring $nomo   nomo de la ligilo
 * @param   string $celo   la kadron, en kiu la paĝo montriĝu
 *                           (nur necesa, se ne la defaŭlta)
 * @param   string $ago    la ago, por kiu oni bezonas la rajton.
 * @param string  $montru  se ne komenciĝas per "j" (defaŭlto),
 *                           kaj oni ne rajtas,
 *                           la teksto tute ne montriĝu.
 */
function rajtligu($kien,$nomo,$celo="",$ago="",$montru="j")
{
    // Ni testas, ĉu oni rajtas iri al la ligota paĝo
    if ( rajtas($ago) )
        {
            ligu($kien,$nomo,$celo);
        }
    else if ($montru[0]=='j')
        {
            eoecho ("<a class='nerajtas'>\n $nomo \n</a>");
        }
}

/**
 * metas ligon ene de HTML-listo-elemento.
 *<pre>
 *  * nomo
 *</pre>
 * @param urlstring $kien la URI de la paĝo (povas esti relativa).
 * @param eostring $nomo la teksto de la ligilo (en eo-kodigo)
 * @uses ligu()
 */
function liligu($kien, $nomo) {
    echo "  <li>";
    ligu($kien, $nomo);
    echo "</li>\n";
}

/**
 * Metos HTML-ligilon.
 *
 * @param urlstring $kien la URI de la paĝo (povas esti relativa).
 * @param eostring $nomo la teksto de la ligilo (en eo-kodigo)
 * @param   string $celo (nenecesa) se en alia ol la defaŭlta
 *          kadro, donu ties nomon.
 * @param string|array  $aldona_skripto aldonaj atributoj por
 *                    la '&lt;a>'-Elemento.
 * @uses donu_ligon()
 */
function ligu($kien,$nomo,$celo="", $aldona_skripto="")
{
    echo ' &nbsp;' . donu_ligon($kien, $nomo, $celo, $aldona_skripto);
}

/**
 * Kreas kaj redonas HTML-ligilon.
 *
 * @param urlstring $kien la URI de la paĝo (povas esti relativa).
 * @param eostring $teksto la teksto de la ligilo (en eo-kodigo)
 * @param   string $celo (nenecesa) se en alia ol la defaŭlta
 *          kadro, donu ties nomon.
 * @param string|array  $aldona_skripto aldonaj atributoj por
 *                    la '&lt;a>'-Elemento.
 *                     aŭ en unu ĉeno (inkluzive atributnomoj,
 *                   '=' kaj citiloj), aŭ kiel nomo-valoro-array.
 *
 * @return u8string la HTML-kodo, preta por eldoni ĝin.
 */
function donu_ligon($kien,$teksto,$celo="", $aldona_skripto="")
{
    $rez = '<a href="'.str_replace('&', '&amp;', $kien).'" ';
    if ($celo)
        {
            $rez .= "target='$celo'";
        }
    if (is_string($aldona_skripto)) {
        $rez .= " " . htmlspecialchars($aldona_skripto, ENT_NOQUOTES);
    }
    else if (is_array($aldona_skripto)) {
        foreach($aldona_skripto AS $nomo => $valoro) {
            $rez .= " " . $nomo . '="' .
                htmlspecialchars($valoro,  ENT_COMPAT) . '"';
        }
    }
    
    $rez .= ">";
    $rez .= eotransform($teksto);
    $rez .= "</a>";
    return $rez;
}

/**
 * alligas iun paĝon/dosieron kun aldona hazarda numero, por
 * eviti uzon de retumila stokejo.
 * La dosiero ĉiam malfermiĝu en nova fenestro/kadro
 *       (<samp>target='_blank'</samp>), ĉar PDF-dosiero en
 *   retumila subkadro estas iom malfacile uzebla.
 *
 * @todo esploru la Opera-problemon pri tio
 *
 * @param urlstring $kien la URL por alligi. Ni aldonas <samp>'?rand=</samp>
 *                     kaj hazardan numeron.
 * @param  eostring $nomo la teksto de la ligo.
 * 
 */
function hazard_ligu($kien, $nomo)
{
    ligu($kien . "?rand=" . rand(1000,9999), $nomo, "_blank");
}

/**
 * butono kun sia propra POST-formulareto, por uzo anstataŭ
 * simpla ligo por fari iun agon.
 *
 * <strong>Ne uzu ene de aliaj formularoj!</strong>
 *
 * @param urlstring $kien - kiun paĝon voki
 * @param eostring $titolo - teksto sur la butono
 *
 * @param array|string $valoroj  Se estas string, kion sendi (teksto)
 *                                  (defaŭlto: 'ne_gravas')
 *            Se estas array(), gxi enhavu nomojn kaj valorojn sendendajn per
 *            la formularo (inkluzive la butono).
 * @param string $nomo  nomo de la butono   (defaŭlto: 'sendu')
 *            En la array-kazo, $nomo kaj $valoroj[$nomo] estas uzataj
 *            por la butono, se $valoroj[nomo] ekzistas, alikaze la
 *            unua paro en $valoroj.
 *
 * @see butono()
 * @see send_butono()
 * @see ligu()
 * @uses tenukasxe()
 * @uses butono()
 */

function ligu_butone($kien, $titolo, $valoroj='ne_gravas', $nomo='sendu')
{
    echo "<form action='" . htmlspecialchars($kien, ENT_QUOTES) .
        "' method='POST' class='formulareto'>";
    if (is_array($valoroj)) {
        if (!isset($valoroj[$nomo])) {
            reset($valoroj);
            $nomo = key($valoroj);
        }
        $butono_valoro = $valoroj[$nomo];
        unset($valoroj[$nomo]);
        // la restantaj ni metas kaŝite
        foreach($valoroj AS $ilo => $val) {
            tenukasxe($ilo, $val);
        }
        butono($butono_valoro, $titolo, $nomo);
    }
    else {
        butono($valoroj, $titolo, $nomo);
    }
    echo "</form>";
}


/**
 * Butono por sendi formularon (input/submit).
 *
 * <pre>
 * .-----------.
 * |  titolo   |
 * '-----------' 
 * </pre>
 *
 *
 * @param eostring $titolo la teksto de la butono. Povas uzi c^-kodigon.
 * @see ligu_butone()
 * @see butono()
 */
function send_butono($titolo)
{
    echo "<input name='sendu' value='";
    eoecho ($titolo);
    echo "' type='submit'> \n";
}

/**
 * Butono por sendi formularon (button/submit).
 *
 * <pre>
 * .-----------.
 * |  titolo   |
 * '-----------' 
 * </pre>
 *
 *
 * @param string $valoro la valoro de la butono (estos sendota).
 * @param eostring $titolo la teksto de la butono.
 *                          povas enhavi HTML-on kaj uzi c^-kodigon.
 * @param string $nomo   la nomo de la butono, defaŭlto "sendu".
 * @see ligu_butone()
 * @see send_butono()
 */
function butono($valoro, $titolo, $nomo="sendu")
{
    echo "<button type='submit' name='$nomo' value='$valoro'>";
    eoecho($titolo);
    echo "</button>\n";
}

/**
 * duĉela eldono por tabellinioj
 * TODO: auf CSS umstellen
 * TODO: Cxu uzata?
 */
function kampo($titolo,$io)
{
    eoecho ("<TR><TD align=right bgcolor=#CCFFFF> $titolo </TD>\n<TD align=left bgcolor=#CCFFCC> $io</TD>");
    echo "</TR>\n";
}

function kampoj($titolo, $kampoj)
{
    eoecho("<tr><th>".$titolo."</th>\n");
  
    foreach ($kampoj AS $de => $al)
        {
            if (is_int($de))
                {
                    eoecho( "    <td>" . $al . "</td>\n");
                }
            else
                {
                    eoecho( "    <td class='". $al . "'>". $de . "</td>");
                }
        }
    echo "</tr>\n";
}

/**
 * TODO: Dokumentado por depend_malsxargxi_kaj_korekti
 * prueft ob bei den Programmfeldern die Checkbox mit den Feldern korreliert
 */
function depend_malsxargxi_kaj_korekti(&$bokso,&$ejo)
{
    global $parto;
    //echo "B: $bokso, $ejo";
    if ( ($bokso[0] != "J") and ($ejo))
        {
            $ejo = "";
        }
    if ( $bokso[0] == "J" and (!$ejo) )
        {
            $parto = "korektigi";
        }
}


/**
 * Montras HTML-elektilon (<select>-elementon) de partoprenantoj
 * Ĝi montras personan nomon, familian nomon kaj la mallongigo
 * de renkontiĝo, kaj kiam oni elektis ion, ĝi sendas la
 * identifikilon ("ID").
 *
 * @param sqlstring $sql - la SQL-demando. La rezulto enhavu
 *          almenaŭ "ID", "nomo", "personanomo", eble ankaux
 *         "renkNumero" kiel kampoj. (Pliaj kampoj eblas, sed ne
 *         estas uzataj.)
 *         Ekzempla SQL-demando por ĉiuj partoprenantoj:
 *  <code>
 *          $sql = datumbazdemando(array("pp.ID", "pp.nomo", "personanomo",
 *                                       "max(renkontigxoID) as renkNumero" ),
 *                                 array("partoprenantoj" => "pp",
 *                                       "partoprenoj" => "pn" ),
 *                                 "pn.partoprenantoID = pp.ID",
 *                                 "",
 *                                 array("group" => "pp.ID",
 *                                       "order" => "personanomo, nomo")
 *                                 );
 * </code>
 * @param string $nomo - la valoro de la "name"-atributo de la
 *              <select>-elemento. La defaŭlta valoro estas
 *              "partoprenantoidento".
 *
 */
function partoprenanto_elektilo($sql,$grandeco='10', $nomo ="partoprenantoidento", $kun_identifikilo = FALSE)
{
    if(substr($sql, 0, 6) != "SELECT")
        {
            darf_nicht_sein();
            return false;
        }
    $rezulto = sql_faru($sql);
    $mallongigoj = array();
    echo "<select size='$grandeco' name='" . $nomo . "'>\n";
  
    while ($row = mysql_fetch_assoc($rezulto)) 
        {
            if ($row['renkNumero'])
                {
                    $mallongigo = $mallongigoj[$row["renkNumero"]];

                    // Ni serĉas por ĉiu renkontiĝo maksimume unu foje la
                    // mallongigon
                    if (empty($mallongigo))
                        {
                            $rez = mysql_fetch_assoc(sql_faru(datumbazdemando("mallongigo",
                                                                              "renkontigxo",
                                                                              "ID = '".$row["renkNumero"]."'",
                                                                              "",
                                                                              array("limit" => "1")
                                                                              )));
                            $mallongigo = $rez["mallongigo"];
                            $mallongigoj[$row["renkNumero"]] = $mallongigo;
                        }
                }
            else
                {
                    $mallongigo = "";
                }
            echo "<option"; 
            eoecho (" value='".$row["ID"]."'>".$row['personanomo'].' '.$row['nomo']);
            if ($mallongigo)
                eoecho (" (" . $mallongigo . ")");
            if ($kun_identifikilo)
                {
                    echo " (#" . $row["ID"] . ")";
                }
            echo "</option>\n"; 
        }
    echo "</select>\n";
}

/**
 * Elektilo en tabellinio.
 *<pre>
 * .--------.------------------------.
 * | teksto |  [_______]  postteksto |
 * '--------'--|       |-------------'
 *             |       |
 *             |       |
 *             '-------'
 *</pre>
 * @param eostring $teksto  titolo
 * @param string $nomo     la interna nomo.
 * @param array $elektebloj  array kun la diversaj ebloj, en la formo
 *                interna => montrata
 * @param string|int $defauxlto  kiu eblo estos antaŭelektita, se
 *              ne estas jam elektita alia (per $_POST[$nomo]).
 * @param eostring $postteksto - teksto aperonta apud la elektilo.
 * @param int      $alteco la nombro de linioj videblaj.
 * @uses elektilo_simpla
 */
function tabela_elektilo($teksto, $nomo, $elektebloj,
                         $defauxlto="", $postteksto = "", $alteco=1) {
    eoecho("<tr><th><label for='" . $nomo . "'>" . $teksto .
           "</label></th><td>");
    elektilo_simpla($nomo, $elektebloj, $defauxlto, $postteksto, $alteco);
    echo("</td></tr>\n");
}


/**
 * Elektilo en tabellinio, kun datoj el datumbazo.
 *
 *<pre>
 * .--------.------------------------.
 * | teksto |  [_______]  postteksto |
 * '--------'--|       |-------------'
 *             |       |
 *             |       |
 *             '-------'
 * </pre>
 * @param eostring $teksto  titolo
 * @param string $nomo    - la interna nomo.
 * @param string $tabelo - la abstrakta nomo de la datumbaztabelo.
 * @param string $kampo_teksto - la kampo por la tekstoj
 * @param string $kampo_interna - la kampo por la valoroj sendotaj
 * @param string|int $defauxlto     - kio estos antaŭelektita, se $_POST['nomo'] ne ekzistas.
 * @param string $restriktoj    - pliaj restriktoj por la elekto
 * @param eostring $postteksto - teksto aperonta apud la elektilo.
 * @uses elektilo_simpla_db()
 */
function tabela_elektilo_db($teksto, $nomo, $tabelo,
                            $kampo_teksto="nomo",
                            $kampo_interna = "ID",
                            $defauxlto="",
                            $restriktoj="",
                            $postteksto="",
                            $alteco=1) {
    eoecho("<tr><th><label for='" . $nomo . "'>" . $teksto .
           "</label></th><td>");
    elektilo_simpla_db($nomo, $tabelo, $kampo_teksto, $kampo_interna,
                       $defauxlto, $restriktoj, $postteksto, $alteco);
    echo("</td></tr>\n");
}


/**
 * Elektilo en tabellinio, kun datoj el datumbazo.
 *
 *<pre>
 * .--------.------------------------.
 * | teksto |  [_______]  postteksto |
 * '--------'--|       |-------------'
 *             |       |
 *             |       |
 *             '-------'
 * </pre>
 * @param eostring $teksto  titolo
 * @param string $nomo    - la interna nomo.
 * @param string $tabelo - la abstrakta nomo de la datumbaztabelo.
 * @param string $kampo_teksto - la kampo por la tekstoj
 * @param string $kampo_interna - la kampo por la valoroj sendotaj
 * @param string|int $defauxlto     - kio estos antaŭelektita, se $_POST['nomo'] ne ekzistas.
 * @param string $restriktoj    - pliaj restriktoj por la elekto
 * @param eostring $postteksto - teksto aperonta apud la elektilo.
 * @uses elektilo_simpla_db()
 */
function tabela_elektilo_radie_db($teksto, $nomo, $tabelo,
                                  $kampo_teksto="nomo",
                                  $kampo_interna = "ID",
                                  $defauxlto="",
                                  $restriktoj="",
                                  $postteksto="",
                                  $alteco=1) {
    eoecho("<tr><th><label for='" . $nomo . "'>" . $teksto .
           "</label></th><td>");
    elektilo_simpla_radie_db($nomo, $tabelo, $kampo_teksto, $kampo_interna,
                             $defauxlto, $restriktoj, $postteksto, $alteco);
    echo("</td></tr>\n");
}



/**
 * Elektilo en tabellinio por krompago-/kromkosto/rabato-kondicxoj.
 *
 *<pre>
 * .---------.------------------------.
 * | Kondiĉo |  [_______]  postteksto |
 * '---------'--|       |-------------'
 *              |       |
 *              |       |
 *              '-------'
 *</pre>
 *
 * En la listo aperas la kondiĉo-funkcioj uzeblaj por krompagoj, rabatoj,
 * parttemp-kotizo-sistemoj, aŭ kromkostoj.
 * La sendo-nomo estas "kondicxo".
 *
 * @param eostring $postteksto kio estas skribita apude.
 * @param string   $defauxlto  kio estas antaŭelektita - unu
 *                              el la identigiloj de la kondicxo-tabelo.
 * @uses tabela_elektilo()
 * @see tabela_ma_kondicxoelektilo()
 */
function tabela_kondicxoelektilo($postteksto="", $defauxlto=null)
{
    tabela_elektilo_db("Kondic^o", 'kondicxo',
                       'kondicxoj', 'nomo', 'ID',
                       $defauxlto, '', $postteksto);
}


/**
 * Simpla elektilo por kondicxoj.
 *
 * <pre>
 *   _________
 *  [_________]   postteksto
 *  |         |
 *  |         |
 *  |         |
 *  '---------'
 * </pre>
 * @param string $nomo la nomo por sendi poste.
 */
function simpla_kondicxoelektilo($nomo, $defauxlto=null,  $postteksto = "")
{
    elektilo_simpla_db($nomo,
                    'kondicxoj', 'nomo', 'ID',
                    $defauxlto, '', $postteksto);
}


/**
 * Elektilo en tabellinio por malaligxkondicxoj.
 *
 *<pre>
 * .---------.------------------------.
 * | funkcio |  [_______]  postteksto |
 * '---------'--|       |-------------'
 *              |       |
 *              |       |
 *              '-------'
 *</pre>
 *
 * En la listo aperas la malaligxkondicxo-funkcioj uzeblaj
 * por la kotizosistemo.
 *
 * @param eostring $postteksto kio estas skribita apude.
 * @param string   $defauxlto  kio estas antauxelektita. Estu unu el
 *                             la valoroj en $GLOBALS['ma_kondicxolisto'].
 * @uses tabela_elektilo()
 * @uses $GLOBALS['ma_kondicxolisto']
 * @uses konvertu_funkcinomon
 * @see tabela_kondicxoelektilo
 */

function tabela_ma_kondicxoelektilo($postteksto="", $defauxlto=null) {
    $kondicxoj =
        array_combine($GLOBALS['ma_kondicxolisto'],
                      array_map("konvertu_funkcinomon",
                                $GLOBALS['ma_kondicxolisto']));
    
    if (!$defauxlto) {
        $kondicxoj = array_merge(array("---" => "(bonvolu elekti)"),
                                 $kondicxoj);
        $defaulxto = "---";
    }
    tabela_elektilo("funkcio", "funkcio",
                    $kondicxoj,
                    $defauxlto,
                    $postteksto);
}

/**
 * elektilo kun OK-butono en propra formulareto.
 *<pre>
 *           __________    ____
 *   Titolo [_________]   | v |
 *          |         |   '---'
 *          |         |
 *          |         |
 *          '---------'
 *</pre>
 * aŭ:
 *<pre>
 *   Titolo valoro 
 *</pre>
 * 
 * @param eostring  $titolo  priskribo de la enhavo de la elektilo.
 * @param urlstring $ago     adreso de retpaĝo, kiu akceptas la sendaĵon
 *                             (por la 'action'-atributo.)
 * @param string $nomo       nomo de la sendenda informo
 * @param array $elekteblecoj array() el elekteblecoj, en formo
 *                   id => teksto
 *             La tekstoj estos montrataj, la ID estos
 *             sendota al $ago.
 * @param string|int $defauxlto    - ID de la elemento, kiu estos antaŭelektita
 * @param string  $rajto   se != "", rajto kiun la uzanto devos havi por
 *                 vidi/uzi la elektilon. Alikaze nur estos
 *                 montrata la titolo kun la valoro
 *                  (= $elekteblecoj[$defauxlto]).
 * @param eostring $butonteksto teksto por la butono - defaŭlto estas iu hoko.
 *
 * @uses elektilo_simpla()
 * @uses send_butono()
 */
function elektilo_kun_butono($titolo, $ago, $nomo,
                             $elekteblecoj, $defauxlto,
                             $rajto="", $butonteksto="")
{
    //    echo "<!-- defaŭlto: " . $defauxlto . "-->";
    if ( "" == $rajto or rajtas($rajto)) {

        
        echo "<form class='formulareto' action='" .
            htmlspecialchars($ago, ENT_QUOTES) . "' method='POST'>";
        eoecho("<label>" . $titolo);
        elektilo_simpla($nomo, $elekteblecoj, $defauxlto);
        echo "</label>";

        if (!$butonteksto) {
            $butonteksto = "&radic;"; // TODO: pli bona hoko.
        }

        send_butono($butonteksto);
        echo "</form>";
    }
    else {
        eoecho($titolo);
        eoecho($elekteblecoj[$defauxlto]);
    }
}



/**
 * Kreas simplan elektilon.
 *
 *<pre>
 *   _________
 *  [_________]   aldonaĵo
 *  |         |
 *  |         |
 *  |         |
 *  '---------'
 *</pre>
 *
 * @param string   $nomo        la interna nomo.
 * @param array    $elektebloj  array kun la diversaj ebloj, en la formo
 *                            $interna => $montrata
 *                               ($montrata estas {@link eostring}.)
 * @param string   $defauxlto  kiu eblo estos antaŭelektita, se
 *                             ne estas jam elektita alia (per $_POST[nomo]).
 * @param eostring $aldonajxoj  teksto aperonta apud la elektilo.
 * @param int      $alteco      la nombro de linioj montrata. Se estas
 *                              1 (la defauxlta), estas klap-listo, alikaze
 *                              plurlinia elektilo.
 * @param boolean $anstatauxu_int_sxlosilojn se true (la defauxlto),
 *               en $elektebloj ni en kazo de int-sxlosiloj/indeksoj
 *               uzas la valoron kiel sxlosilo.
 */
function elektilo_simpla($nomo, $elektebloj, $defauxlto="",
                         $aldonajxoj="", $alteco = 1,
                         $anstatauxu_int_sxlosilojn = true,
                         $htmlaldonajxo="")
{
    // se iu estas donita jam lastfoje,
    // prenu tiun kiel defaŭlto.

    //    echo "<!-- defaŭlto: " . $defauxlto . "-->";
    if ($_POST[$nomo])
        {
            $defauxlto = $_POST[$nomo];
        }
    //    echo "<!-- defaŭlto: " . $defauxlto . "-->";
    echo "  <select name='$nomo' size='$alteco' id='$nomo'" .$htmlaldonajxo . ">\n";
    foreach($elektebloj AS $eblo => $teksto)
        {
            if ($anstatauxu_int_sxlosilojn and is_integer($eblo)) {
                $eblo = $teksto;
            }
            echo "     <option value='$eblo'";
            if ($eblo == $defauxlto)
                {
                    echo " selected='selected'";
                }
            eoecho( ">" . $teksto . "</option>\n");
        }
    echo "  </select>\n";
    if ($aldonajxoj)
        eoecho( $aldonajxoj);
}

/**
 * Kreas simplan elektilon.
 *
 *<pre>
 *   __________
 *  [_________]   aldonaĵo
 *  |         |
 *  |         |
 *  |         |
 *  '---------'
 *</pre>
 * funkcias kiel {@link elektilo_simpla()}, sed prenas la tekstojn
 * el iu datumbaztabelo.
 *
 * @param string       $nomo           la nomo de la elektilo (= sub kiu
 *                                      nomo sendi al la servilo)
 * @param string       $tabelo         la abstrakta nomo de la datumbaztabelo.
 * @param string       $kampo_teksto   la kampo por la tekstoj
 * @param string       $kampo_interna  la kampo por la valoroj sendotaj
 * @param string       $defauxlto      kio estos antaŭelektata, se
 *                                     $_POST['nomo'] ne ekzistas.
 * @param array|string $restriktoj     pliaj restriktoj por la elekto
 *                                     (vidu {@link datumbazdemando}
 * @param eostring     $aldonajxoj     teksto aperanta post la elektilo.
 * @param int          $alteco         la nombro de linioj videblaj.
 */
function elektilo_simpla_db($nomo, $tabelo, $kampo_teksto="nomo",
                            $kampo_interna = "ID",
                            $defauxlto="", $restriktoj="", $aldonajxoj="", $alteco = 1)
{
    if ($_POST[$nomo])
        {
            $defauxlto = $_POST[$nomo];
        }
    $rez = sql_faru(datumbazdemando(array($kampo_teksto => 'teksto',
                                          $kampo_interna => 'ID'),
                                    $tabelo, $restriktoj));
    echo "  <select name='$nomo'  size='$alteco' id='$nomo'>\n";
    while($linio = mysql_fetch_assoc($rez)) {
        echo "    <option value='" . $linio['ID'] . "' ";
        if ($linio['ID'] == $defauxlto) {
            echo " selected='selected'";
        }
        eoecho( ">" . $linio['teksto'] . "</option>\n");
    }
    echo "  </select>\n";
    if ($aldonajxoj)
        eoecho( $aldonajxoj);
}

/**
 * Kreas simplan elektilon.
 *
 *<pre>
 *   __________
 *  [_________]   aldonaĵo
 *  |         |
 *  |         |
 *  |         |
 *  '---------'
 *</pre>
 * funkcias kiel {@link elektilo_simpla()}, sed prenas la tekstojn
 * el iu datumbaztabelo.
 *
 * @param string       $nomo           la nomo de la elektilo (= sub kiu
 *                                      nomo sendi al la servilo)
 * @param string       $tabelo         la abstrakta nomo de la datumbaztabelo.
 * @param string       $kampo_teksto   la kampo por la tekstoj
 * @param string       $kampo_interna  la kampo por la valoroj sendotaj
 * @param string       $defauxlto      kio estos antaŭelektata, se
 *                                     $_POST['nomo'] ne ekzistas.
 * @param array|string $restriktoj     pliaj restriktoj por la elekto
 *                                     (vidu {@link datumbazdemando}
 * @param eostring     $aldonajxoj     teksto aperanta post la elektilo.
 * @param int          $alteco         la nombro de linioj videblaj.
 */
function elektilo_simpla_radie_db($nomo, $tabelo, $kampo_teksto="nomo",
                                  $kampo_interna = "ID",
                                  $defauxlto="", $restriktoj="",
                                  $aldonajxoj="", $alteco = 1)
{
    if ($_POST[$nomo])
        {
            $defauxlto = $_POST[$nomo];
        }
    $rez = sql_faru(datumbazdemando(array($kampo_teksto => 'teksto',
                                          $kampo_interna => 'ID'),
                                    $tabelo, $restriktoj));
    while($linio = mysql_fetch_assoc($rez)) {
        echo "    <input type='radio' name='" . $nomo .
            "' value='" . $linio['ID'] . "' ";
        if ($linio['ID'] == $defauxlto) {
            echo " checked='checked'";
        }
        eoecho( ">" . $linio['teksto'] . "\n");
    }
    if ($aldonajxoj)
        eoecho( $aldonajxoj);
}


/**
 * kreas elektoliston (per radiaj butonoj) el la renkontigxo-konfiguroj,
 * en tabellinio.
 * <pre>
 *  ( ) elekto 1 | (*) elekto 2  | *  ( ) elekto 3  
 * </pre>
 * @param string      $nomo (la interna nomo)
 * @param asciistring $tipo la konfiguro-tipo, t.e.
 *                          sekcio de la konfiguro-tabelo.
 * @param asciistring $valoro la antauxelektota valoro.
 * @param Renkontigxo|int $renkontigxo
 *
 * @uses simpla_entajpbutono()
 * @uses datumbazdemando()
 */
function simpla_elektolisto_el_konfiguroj($nomo,  $tipo,
                                          $valoro, $renkontigxo=0)
{
    if (is_object($renkontigxo)) {
        $renkontigxo = $renkontigxo->datoj['ID'];
    }
    if (!$renkontigxo or !is_int($renkontigxo)) {
        $renkontigxo = $_SESSION['renkontigxo']->datoj['ID'];
    }

    elektilo_simpla_radie_db($nomo, "renkontigxaj_konfiguroj",
                             'teksto', 'interna', $valoro,
                             array('tipo' => $tipo,
                                   'renkontigxoID' => $renkontigxo,
                                   ));

}


/**
 * kreas elektoliston (per radiaj butonoj) el la renkontigxo-konfiguroj,
 * en tabellinio.
 * <pre>
 * |--------+---------------|
 * | titolo | ( ) elekto 1  |
 * |        | (*) elekto 2  |
 * |        | ( ) elekto 3  |
 * |--------+---------------|
 * </pre>
 * @param eostring    $titolo
 * @param string      $nomo (la interna nomo)
 * @param asciistring $tipo la konfiguro-tipo, t.e.
 *                          sekcio de la konfiguro-tabelo.
 * @param asciistring $valoro la antauxelektota valoro.
 * @param Renkontigxo|int $renkontigxo
 *
 * @uses simpla_entajpbutono()
 * @uses datumbazdemando()
 */
function tabela_elektolisto_el_konfiguroj($titolo, $nomo,  $tipo,
                                          $valoro, $renkontigxo=0)
{
    debug_echo("<!-- tabela_elektolisto_el_konfiguroj(" . $titolo . ", " .
               $nomo . ", " . $tipo . ", " . $valoro . ", " .
               var_export($renkontigxo, true) . ")\n -->");

    if (is_object($renkontigxo)) {
        $renkontigxo = $renkontigxo->datoj['ID'];
    }
    if (!$renkontigxo or !is_int($renkontigxo)) {
        $renkontigxo = $_SESSION['renkontigxo']->datoj['ID'];
    }
    eoecho("<tr>\n   <th>" . $titolo . "</th>\n   <td>\n");


    $sql = datumbazdemando(array('interna', 'grupo', 'teksto',
                                 'aldona_komento'),
                           'renkontigxaj_konfiguroj',
                           array('renkontigxoID' => $renkontigxo,
                                 'tipo' => $tipo),
                           "",
                           array('order' => "grupo ASC"));
    $rez = sql_faru($sql);
    $antauxa_grupo = '#';
    while($linio = mysql_fetch_assoc($rez)) {
        debug_echo("<!-- " . var_export($linio, true) . "-->");
        if($linio['grupo'] != $antauxa_grupo) {
            if ($antauxa_grupo != '#') {
                echo "<br/>\n<br/>";
            }
            $antauxa_grupo = $linio['grupo'];
        }
        else {
            echo "<br />\n";
        }
        //        echo $linio['interna'] . " ";
        simpla_entajpbutono($nomo, $valoro, $linio['interna']);
        eoecho(" " .$linio['teksto'] . "\n");
        if ($linio['aldona_komento']) {
            eoecho("<br/>\n<span class='aldona_komento'>".
                   $linio['aldona_komento'] . "</span>\n");
        }
    }
    echo "      </p>\n";
    echo "  </td>\n</tr>\n";
}
