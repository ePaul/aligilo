<?php

  /**
   * Kelkaj funkcioj rilataj al HTML-eldono.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
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
    echo '    <link rel="stylesheet" href="' . $dosiernomo .
        '" type="text/css" charset="iso-8859-1">';
    // TODO: titolo konfigurebla!
    eoecho ("    <title>" . renkontigxo_nomo . "-Aligilo [".  MODUSO .
            "]</title>");
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
   * @link HtmlFino()
   */ 
function HtmlKapo($klaso = "")
{

    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/transitional.dtd">
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="content-language" content="eo">
        <?php
        metu_stilfolion_kaj_titolon();

?>    <base target="anzeige">
    <script type="text/javascript" src="iloj/cxiujpagxoj.js" charset="iso-8859-1"></script>
 </head>
  <body <?php
 if ($klaso!="") {echo "class='$klaso'";}
   ?> >
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
 * @link HtmlKapo()
 */
function HtmlFino()
{
    ?>
  </body>
</html><?php
}


     /**
      * transformas de la post-^-methodo (c^)
      * al (HTML-)unikoda esperanto, aŭ al la x-metodo.
      *
      * @param string $texto Teksto en UTF-8 kun c^-koditaj
      *               supersignoj.
      * @param string $enkodo la transform-maniero por la teksto, unu el la
      *                sekvaj valoroj:
      *  - "x-metodo": transformas la Eo-signojn al iksa-kodigo,
      *             "E^" al "Euro".
      *  - "unikodo": HTMLa unikoda transformo, ekzemple &#265; por c^.
      *  - "utf-8": rekta UTF-8-kodigo.
      *  - "pdf-speciala": Kodigo al la speciala PDF-kodigo uzata de niaj
      *         tiparoj por FPDF (la ne-unikoda versio). "E^" iĝas
      *         "EUR", kaj ĉiuj ne-latin-1-aj signoj (kaj kelkaj aliaj)
      *         ne estas montreblaj tiel (Vidu {@link eo()},
      *         {@link estas_ekster_latin1()}). Kontraŭe al la aliaj
      *         kodigoj, tiu ĉi ne nur ŝanĝas la ^-koditajn signojn,
      *         sed transkodigas la tutan tekston.
      *   - "identa": identa transformo - ŝanĝas nenion.
      *
      *  Ĉiuj aliaj valoroj nun ankaŭ funkcias kiel la identa
      *   transformo, sed eble estonte aldoniĝos pliaj transformoj.
      *  
      * @return string la transformita teksto.
      */
    function eotransformado($texto,$enkodo)
{
    if ($enkodo == "x-metodo")
        {  
            $texto = str_replace("C^","Cx",$texto);
            $texto = str_replace("c^","cx",$texto);

            $texto = str_replace("G^","Gx",$texto);
            $texto = str_replace("g^","gx",$texto);

            $texto = str_replace("H^","Hx",$texto);
            $texto = str_replace("h^","hx",$texto);

            $texto = str_replace("J^","Jx",$texto);
            $texto = str_replace("j^","jx",$texto);

            $texto = str_replace("S^","Sx",$texto);
            $texto = str_replace("s^","sx",$texto);

            $texto = str_replace("U^","Ux",$texto);
            $texto = str_replace("u^","ux",$texto);

            $texto = str_replace("E^","Euro",$texto);
        }
    else if ($enkodo == "unikodo")
        {
            $trans = array ("C^" => "&#264;", "c^" => "&#265;",
                            "G^" => "&#284;", "g^" => "&#285;",
                            "H^" => "&#292;", "h^" => "&#293;",
                            "J^" => "&#308;", "j^" => "&#309;",
                            "S^" => "&#348;", "s^" => "&#349;",
                            "U^" => "&#364;", "u^" => "&#365;",
                            "E^" => "&#8364;"); // TODO: eble ni uzu &euro; ?
            $texto = strtr($texto, $trans);

        }
    else if ($enkodo == "utf-8")
        {
            $trans = array("C^" => "Ĉ", "c^" => "ĉ",
                           "G^" => "Ĝ", "g^" => "ĝ",
                           "H^" => "Ĥ", "h^" => "ĥ",
                           "J^" => "Ĵ", "j^" => "ĵ",
                           "S^" => "Ŝ", "s^" => "ŝ",
                           "U^" => "Ŭ", "u^" => "ŭ",
                           "E^" => "€");
            $texto = strtr($texto, $trans);
        }
    else if ($enkodo == "pdf-speciala") {
        $teksto = eo($teksto);
    }
    return $texto;
}

/* ####################################### */
 /* echo kun Eo signo laŭ unikodo aŭ 'xe' */
 /* ####################################### */


  /**
   * Eldonas eo-transformitan tekston.
   *
   * @param string $io eldonenda teksto, en c^-kodigo.
   * @uses eotransform()
   */
 function eoecho($io)
{
    echo eotransform($io);
}


/**
 * Transformas tekston el nia esperanta c^-kodigo al
 * la defaŭlta kodigo.
 *
 * @param string $io transforminda teksto
 * @global string _SESSION['enkodo'] kodigo uzenda
 * @global string GLOBALS['enkodo']   kodigo uzenda, se $_SESSION["enkodo"] ne ekzistas. (Se ankaŭ tiu ne ekzistas, uzu "unikodo".
 * @return string la transformita teksto.
 * @uses eotransformado
 */
function eotransform($io)
{
    $enkodo = $_SESSION['enkodo'] or
        $enkodo = $GLOBALS['enkodo'] or
        $enkodo = "unikodo";
    return eotransformado($io, $enkodo);
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
 *
 * @param int $alteco  la nombro da linioj en la elektilo.
 *           se 1, tiam estas elektilo kun klapmenuo,
 *           alikaze estos plurlinia elektilo.
 * @param int $lando  la identigilo de la antaŭelektita lando.
 *           (se vi nenion donis, uzos la konstanton HEJMLANDO.)
 * @param boolean $loka uzu la loka-lingvan varianton de la landonomo
 *           (ekzemple germana), se <var>$loka</var> estas donita kaj io, kio
 *           iĝas 'true'.
 * @param string $klaso   iu html-atribut-fragmento, ekzemple
 *            class='mankas' por aldoni al la <select>-elemento.
 * @param Renkontigxo $renkontigxo renkontiĝo-objekto - rilate al ties
 *                     kotizosistemo ni montras la landokategoriojn.
 */
function montru_landoelektilon($alteco, $lando=HEJMLANDO, $loka=false,
                               $klaso="", $renkontigxo=null)
{
    if (DEBUG) echo "<!-- lando: $lando -->";
  
    echo "<select name='lando' size='{$alteco}' {$klaso}>\n";
  
    if ($loka)
        {
            $nomonomo = 'lokanomo';
        }
    else
        {
            $nomonomo = 'nomo';
        }

    $result = sql_faru(datumbazdemando(array($nomonomo => "landonomo",
                                             "ID"),
                                       "landoj",
                                       "",
                                       "",
                                       array("order" => "landonomo ASC")));
    while ($row = mysql_fetch_assoc($result))
        {
            echo "<option";
            if ($row['ID'] == $lando)
                {
                    echo " selected='selected'";
                }
            echo " value='". $row['ID']."'>";

            $kategorio = eltrovu_landokategorion($row['ID'], $renkontigxo);
            //      echo "<!-- " . var_export($kategorio, true) . "-->";
      
            eoecho ($row['landonomo']. " (". $kategorio->datoj['nomo']. ')');
            echo "</option>\n";
        }
    echo "</select>  <br/>\n";
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
 * @global string _REQUEST[$nomo] tion ni uzas, se $io == "".
 *
 */
function simpla_entajpejo($teksto, $nomo, $io = "",  $grandeco="",
                          $kutima="", $postteksto="", $kasxe="n")
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
    echo "<input type='radio' id='$nomo=$komparo' name='$nomo' value='$komparo' ";
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
 * @see simpla_entajpbutono()
 * @see tabel_entajpbutono()
 */
function entajpbutono($teksto,$nomo,$io,$komparo,$valoro,$postteksto="",$kutima="")
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
 * @param string $io se $io[0] == 'J', la bokso estas markita.
 * @param string $skripto ĵavoskripto, estos vokata por ĉiu
 *                       ŝanĝo de la stato, t.e. je krucigo
 *                       kaj senkrucigo.
 */
function skripto_jes_ne_bokso($nomo,$io,$skripto="")
{
    echo " <input name='$nomo' type='hidden' value='NE'>\n";
    echo " <input name='$nomo' type='checkbox' ";
    if ($io[0] == 'J')
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
 * TODO: auf CSS umstellen
 * eldonas la ruĝan tekston ekz. se mankas necesaj datumoj en iu entajpformularo
 */
function erareldono ($err)
{
    echo "<font color='red'>";
    eoecho ($err);
    echo "!</font><br/>";
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
 * Kreas la HTML-kodon por valoro en formularo, kiu
 * ne montriĝas, sed tamen sendiĝos kun la datoj
 * (<input type="hidden" .../>)
 *
 *  $nomo - la nomo de la variablo
 *  $valoro - la valoro de la variablo.
 *
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
 * Metos HTML-ligilon.
 *
 * @param   string $kien la URI de la paĝo (povas esti relativa).
 * @param eostring $nomo la teksto de la ligilo (en eo-kodigo)
 * @param   string $celo (nenecesa) se en alia ol la defaŭlta
 *          kadro, donu ties nomon.
 * @param string|array  $aldona_skripto aldonaj atributoj por
 *                    la '&lt;a>'-Elemento.
 */
function ligu($kien,$nomo,$celo="", $aldona_skripto="")
{
    echo ' &nbsp;' . donu_ligon($kien, $nomo, $celo, $aldona_skripto);
}

/**
 * Kreas kaj redonas HTML-ligilon.
 *
 * @param   string $kien la URI de la paĝo (povas esti relativa).
 * @param eostring $nomo la teksto de la ligilo (en eo-kodigo)
 * @param   string $celo (nenecesa) se en alia ol la defaŭlta
 *          kadro, donu ties nomon.
 * @param string|array  $aldona_skripto aldonaj atributoj por
 *                    la '&lt;a>'-Elemento.
 *                     aŭ en unu ĉeno (inkluzive atributnomoj,
 *                   '=' kaj citiloj), aŭ kiel nomo-valoro-array.
 *
 * @return string la HTML-kodo, preta por eldoni ĝin.
 */
function donu_ligon($kien,$nomo,$celo="", $aldona_skripto="")
{
    $rez = '<a href="'.str_replace('&', '&amp;', $kien).'" ';
    if ($celo)
        {
            $rez .= "target='$celo'";
        }
    if($aldona_skripto) {
        if (is_string($aldona_skripto)) {
            $rez .= " " . htmlspecialchars($aldona_skripto, ENT_NOQUOTES);
        }
        else {
            foreach($aldona_skripto AS $nomo => $valoro) {
                $rez .= " " . $nomo . '="' .
                    htmlspecialchars($valoro,  ENT_COMPAT) . '"';
            }
        }
    }
    $rez .= ">";
    $rez .= eotransform($nomo);
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
 * @param   string $kien la URL por alligi. Ni aldonas <samp>'?rand=</samp>
 *                     kaj hazardan numeron.
 * @param eostring $nomo la teksto de la ligo.
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
 * Ne uzu ene de aliaj formularoj!
 *
 * $kien - kiun paĝon voki
 * $titolo - teksto sur la butono
 *
 * $valoroj - kion sendi (teksto) (defaŭlto: 'ne_gravas')
 * $nomo    - nomo de la butono   (defaŭlto: 'sendu')
 *
 * alternative:
 *
 *  $valoroj - array(), kiu enhavas nomojn kaj valorojn sendendajn per
 *            la formularo (inkluzive la butono).
 *  $nomo  - nomo de la butono (defaulxto: sendu).
 *           $nomo kaj $valoroj[$nomo] estas uzataj por la butono, se
 *           $valoroj[nomo] ekzistas, alikaze la unua paro en $valoroj
 *
 * @see butono()
 * @see send_butono()
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
    echo "' size='18' type='submit'> \n";
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
 * $sql - la SQL-demando. La rezulto enhavu almenaŭ "ID", "nomo", "personanomo"
 *         kaj "renkNumero" kiel kampoj.
 *         Ekzempla SQL-demando por ĉiuj partoprenantoj:
 *
 *          $sql = datumbazdemando(array("pp.ID", "pp.nomo", "personanomo",
 *                                       "max(renkontigxoID) as renkNumero" ),
 *                                 array("partoprenantoj" => "pp",
 *                                       "partoprenoj" => "pn" ),
 *                                 "pn.partoprenantoID = pp.ID",
 *                                 "",
 *                                 array("group" => "pp.ID",
 *                                       "order" => "personanomo, nomo")
 *                                 );
 * $nomo - la valoro de la "name"-atributo de la <select>-elemento.
 *         La defaŭlta valoro estas "partoprenantoidento".
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
 * helpfunkcio por konverti nomon de funkcio al legebla
 *  teksto por la listo.
 *
 * TODO: pli bona loko, eble ĉe aliaj konverto-funkcioj.
 */
function konvertu_funkcinomon($funknomo) {
    return strtr($funknomo, "x_", "^ ");
}


function tabela_kondicxoelektilo($postteksto="", $defauxlto=null) {
    $kondicxoj =
        array_combine($GLOBALS['kondicxolisto'],
                      array_map("konvertu_funkcinomon",
                                $GLOBALS['kondicxolisto']));
    
    if (!$defauxlto) {
        $kondicxoj = array_merge(array("---" => "(bonvolu elekti)"),
                                 $kondicxoj);
        $defaulxto = "---";
    }
    tabela_elektilo("kondic^o", "kondicxo",
                    $kondicxoj,
                    $defauxlto,
                    $postteksto);
}


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
 * $titolo       - priskribo de la enhavo de la elektilo.
 * $ago          - adreso de retpaĝo, kiu akceptas la sendaĵon
 *                  (por la 'action'-atributo.)
 * $nomo         - nomo de la sendenda informo
 * $elekteblecoj - array() el elekteblecoj, en formo
 *                   id => teksto
 *                  La tekstoj estos montrataj, la ID estos
 *                  sendota al $ago.
 * $defauxlto    - ID de la elemento, kiu estos antaŭelektita
 * $rajto        - se != "", rajto kiun la uzanto devos havi por
 *                 vidi/uzi la elektilon. Alikaze nur estos
 *                 montrata la titolo kun la valoro
 *                  (= $elekteblecoj[$defauxlto]).
 * $butonteksto  - teksto por la butono - defaŭlto estas iu hoko.
 * @uses elektilo_simpla()
 * @uses send_butono()
 */
function elektilo_kun_butono($titolo, $ago, $nomo,
                             $elekteblecoj, $defauxlto,
                             $rajto="", $butonteksto="")
{
    //    echo "<!-- defaŭlto: " . $defauxlto . "-->";
    if ( "" == $rajto or rajtas($rajto)) {

        
        echo "<form class='formulareto' action='" . $ago . "' method='POST'>";
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
 *   __________
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
 *                               ($montrata povas enhavi c^-kodigon).
 * @param string   $defauxlto  kiu eblo estos antaŭelektita, se
 *                             ne estas jam elektita alia (per $_POST[nomo]).
 * @param eostring $aldonajxo  teksto aperonta apud la elektilo.
 * @param int      $alteco
 */

function elektilo_simpla($nomo, $elektebloj, $defauxlto="",
                         $aldonajxoj="", $alteco = 1)
{
    // se iu estas donita jam lastfoje,
    // prenu tiun kiel defaŭlto.

    //    echo "<!-- defaŭlto: " . $defauxlto . "-->";
    if ($_POST[$nomo])
        {
            $defauxlto = $_POST[$nomo];
        }
    //    echo "<!-- defaŭlto: " . $defauxlto . "-->";
    echo "  <select name='$nomo' size='$alteco' id='$nomo'>\n";
    foreach($elektebloj AS $eblo => $teksto)
        {
            if (is_integer($eblo)) {
                $eblo = $teksto;
            }
            echo "     <option value='$eblo'";
            if ($eblo == $defauxlto)
                {
                    echo " selected='selected'";
                }
            eoecho( " >" . $teksto . "</option>\n");
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
 * funkcias kiel elektilo_simpla, sed prenas la tekstojn
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
        eoecho( " >" . $linio['teksto'] . "</option>\n");
    }
    echo "  </select>\n";
    if ($aldonajxoj)
        eoecho( $aldonajxoj);
}


/**
 * kreas tabelon de ĉiuj notoj de la partoprenanto kun menciita ID.
 *
 * @param int  $ppID  identigilo de  la partoprenanto.
 * @param string $kapteksto - se ne "", kreas tutan HTML-dokumenton kaj uzas
 *                      tiun tekston kiel enkondukan tekston pri la tabelo.
 *                      Alikaze nur eldonas la tabelon (por uzo ene de alia
 *                      dokumento).
 */
function listu_notojn($ppID, $kapteksto="") {

    $sercxilo = new Sercxilo();
    $sercxilo->datumbazdemando(array("ID", "prilaborata", "dato",
                                     "subjekto","kiu", "kunKiu","tipo"),
                               "notoj",
                               "partoprenantoID = '$ppID'");
    $sercxilo->metu_kolumnojn(array(array('kampo' => 'ID',
                                          'tekstosxablono' => '->',
                                          'arangxo' => 'z',
                                          'ligilsxablono'
                                          => 'notoj.php?notoID=XXXXX'), 
                                    array('kampo' => 'prilaborata',
                                          'titolo' => 'prilaborita?',
                                          'arangxo' => 'z',
                                          'anstatauxilo'
                                          => array('j'=>'<strong class="malaverto">prilaborita</strong>',
                                                   '' =>'<strong class="averto">neprilaborita</strong>',
                                                   'n'=>'<strong class="averto">neprilaborita</strong>')),
                                    array('kampo' => 'dato',
                                          'titolo' => 'dato',
                                          'arangxo' => 'l'), 
                                    array('kampo' => 'subjekto',
                                          'titolo' => 'temo',
                                          'arangxo' => 'l'),
                                    array('kampo' => "kiu",
                                          'titolo' => "kiu",
                                          'arangxo' => 'l'), 
                                    array('kampo' => "kunKiu",
                                          'titolo' => "kunKiu?",
                                          'arangxo' => 'l'), 
                                    array('kampo' => "tipo",
                                          'titolo' => "tipo",
                                          'arangxo' => 'l')
                                    ));
    $sercxilo->metu_sumregulojn(array(array('', array('# XX','A','z'))));
    $sercxilo->metu_ordigon("dato", "desc");

    if ($kapteksto) {
        $sercxilo->metu_antauxtekston($kapteksto);
        $sercxilo->montru_rezulton_en_HTMLdokumento();
    }
    else {
        $sercxilo->montru_rezulton_en_HTMLtabelo();
    }

    
}   // listu_notojn


?>