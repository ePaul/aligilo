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
   * eldonas la HTML kapon por la kutimaj paĝoj.
   *
   * Tiu mencias la HTML-dokumenttipon, kodigon (UTF-8), lingvon (eo),
   *  stilfolion (depende de {@link MODUSO} kaj iun ĉiam uzatan
   *   {@link cxiujpagxoj.js Ĵavoskripton}.
   *
   * Kutime ĉiu pagxo aspektu tiel:
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
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="content-language" content="eo">
        <?php
        $dosiernomo =  $GLOBALS['prafix']."/stilo_".MODUSO.".css";
    if (DEBUG)
        {
            echo "<!-- MODUSO:      " . MODUSO .
                "\n     dosiernomo:  " . $dosiernomo .
                "\n     laborejo:    " . getcwd() . 
                "\n     def(MODUSO): " . defined("MODUSO") .
                "\n-->\n"; 
        }
    if (defined("MODUSO") and file_exists($dosiernomo))
        {
            echo '    <link rel="stylesheet" href="stilo_' .MODUSO. '.css" type="text/css" charset="iso-8859-1">';
        }
    else
        {
            ?>
            <link rel="stylesheet" href="stilo_defauxlta.css" type="text/css" charset="iso-8859-1">
                <?php
         }
    // TODO: titolo konfigurebla!
 ?>    <title>IS - Aligilo - <?php echo MODUSO; ?></title>
    <base target="anzeige">
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
      * al (HTML-)unikoda esperanto, aux al la x-metodo.
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
      *         tiparoj por FPDF (la ne-unikoda versio). "E^" igxas
      *         "EUR", kaj cxiuj ne-latin-1-aj signoj (kaj kelkaj aliaj)
      *         ne estas montreblaj tiel (Vidu {@link eo()},
      *         {@link estas_ekster_latin1()}). Kontrauxe al la aliaj
      *         kodigoj, tiu cxi ne nur sxangxas la ^-koditajn signojn,
      *         sed transkodigas la tutan tekston.
      *   - "identa": identa transformo - sxangxas nenion.
      *
      *  Cxiuj aliaj valoroj nun ankaux funkcias kiel la identa
      *   transformo, sed eble estonte aldonigxos pliaj transformoj.
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
 /* echo kun Eo signo laux unikodo aux 'xe' */
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
 * Montras renkontigxoelektilon.
 *
 * La HTML-nomo estas "formrenkontigxo",
 * la elektota valoro estas la identigilo
 * de la renkontigxo.
 *
 * @param string $antauxelekto la identigilo de tiu renkontigxo,
 *                   kiu estu jam elektita.
 *                   se vi forlasas, elektigxas la plej
 *                   malfrue komenc(o|a|i)ta renkontigxo
 *                   ( = la unua en la listo).
 * @param int $grandeco kiom granda estu la listo, defaŭlta valoro estas 5.
 *
 */
function montru_renkontigxoelektilon($antauxelekto = "plej_nova",$grandeco='5')
{
    // Elektilo por la renkontigxo:

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
            // elektu auxtomate la unuan renkontigxon
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
 * @param int $lando  la identigilo de la antauxelektita lando.
 *           (se vi nenion donis, uzos la konstanton HEJMLANDO.)
 * @param boolean $loka uzu la loka-lingvan varianton de la landonomo
 *           (ekzemple germana), se <var>$loka</var> estas donita kaj io, kio
 *           igxas 'true'.
 * @param string $klaso   iu html-atribut-fragmento, ekzemple
 *            class='mankas' por aldoni al la <select>-elemento.
 * @param Renkontigxo $renkontigxo renkontigxo-objekto - rilate al ties
 *                     kotizosistemo ni montras la landokategoriojn.
 */
function montru_landoelektilon($alteco, $lando=HEJMLANDO, $loka=false,
                               $klaso="", $renkontigxo=null)
{
    if (DEBUG) echo "<!-- lando: $lando -->";
  
    echo "<select name='lando' size='{$alteco}'{$klaso}>\n";
  
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
 * @param int    $grandeco  la largxeco de la tekstkampoj (proksiume en
 *                             literoj)
 * @param string $postteksto teksto montrita post la entajpejo.
 * @param string $manko      erarmesagxo, kiam $io = "" (nur uzita, se ne "").
 * @param string $kutima     defauxlta valoro - uzata, se $io = "".
 * @param string $kasxe      se 'j', kasxas la entajpitajxon (uzenda
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
 * @param int $kolumnoj  la largxeco de la tekstkampo (proksiume en literoj)
 * @param int $linioj    la alteco de la tekstkampo (nombro da tekstlinioj)
 * @param string $postteksto  teksto montrita post la entajpejo.
 * @param string $manko erarmesagxo, kiam $io = "" (nur uzita, se ne "").
 * @param string $kutima  defauxlta valoro  uzata, se $io = "".
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
 * @param string $teksto     priskribo antaux la bokso.
 * @param string $nomo       nomo de la input-elemento por sendi gxin al la servilo
 * @param string $io         valoro por enmeti
 * @param int $grandeco      grandeco de la entajpejo
 * @param string $manko      ebla erarmesagxo (por testi, cxu $io estas malplena  -->malplentesto())
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
 *   estas uzata, se ankaux tio malplenas).
 *
 * @param string $teksto      priskribo antaux la bokso.
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
 * @param string $manko      ebla erarmesagxo (por testi, cxu $io estas
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
 * (_)   aux   (X)
 * </pre>
 *
 * Tiu simpla butono aperos sen teksto.
 *
 * @param string $nomo    la nomo (por sendi)
 * @param string $elekto  valoro por decidi, cxu elekti tiun cxi kampon.
 * @param string $komparo se $elekto == $komparo, cxi tiu entajpbutono estas
 *                      jam elektita (<samp> (*) </samp>).
 *                      $komparo estas ankaux uzata kiel valoro por sendi.
 * @param string $kutima  se $elekto == "" kaj $kutima == "kutima", la
 *                      entajpbutono ankaux estas elektita. (defauxlto: "")
 * @param string $skripto se donita, la skripto (javaskripto uzebla
 *                     en evento-atributoj de HTML) estas vokita dum sxanĝo
 *                     de la stato (onfocus, onblur, onclick). Gxi aperos
 *                     trifoje, do prefere estu mallonga (ekzemple voko de
 *                     funkcio difinita aliloke).
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
 *
 * @param string $teksto  teksto antaux la entajpbutono
 * @param string $nomo    nomo de la variablo (por sendi al la servilo)
 * @param string $elekto  valoro por decidi, cxu elekti tiun cxi kampon
 *                        (= la aktuala valoro de la elektitajxo)
 * @param string $komparo  se $elekto == $komparo, cxi tiu entajpbutono estas
 *                      elektita (<samp> (*) </samp>).
 *                      $komparo estas ankaux uzata kiel valoro por sendi.
 * @param string $postteksto  estos montrata post la entajpbutono.
 * @param string $kutima      se kutima == "kutima" kaj $io == "", tiam
 *                            la butono estas ankaux komence elektata.
 * @uses simpla_entajpbutono
 */
function tabel_entajpbutono($teksto,$nomo,$elekto,$valoro,$postteksto="",$kutima="")
{
    eoecho ("<tr><th><label for='$nomo=$valoro'>" . $teksto .
            "</label></th><td>");
    simpla_entajpbutono($nomo, $elekto, $valoro, $kutima);
    eoecho("</td><td>" . $postteksto . "</td></tr>\n");
}

/**
 * Radiobutono silsimpla.
 *
 *<pre>
 *    teksto  (_)  postteksto
 *</pre>
 *
 * @param string $teksto     teksto antaux la entajpbutono.
 * @param string $nomo       nomo de la variablo (por sendi al la servilo)
 * @param string $io         valoro por kompari al $komparo. Kutime la aktuala
 *                           valoro de la decidenda variablo.
 * @param string $komparo    se $komparo == $io, la entajpbutono estas komence elektata [  (*)  ]
 * @param string $valoro     kio estos sendita al la servilo, se la butono
 *                           estas elektita dum la sendado. Kutime la sama kiel
 *                           $komparo.
 * @param string $postteksto estos montrata post la entajpbutono.
 * @param string $kutima     se kutima == "kutima" kaj $io == "", tiam
 *                           la butono estas ankaux komence elektata.
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
 * @param string $teksto     teksto antaux la bokso.
 * @param string $nomo       nomo de la inputelemento (uzata por sendi la valoron al la servilo)
 * @param string $io         valoro de la bokso - aux $komparo ([X]) aux ne ([ ]).
 * @param string $komparo    valoro por kompari al $io (se sama, metu krucon).
 * @param string $valoro     kio estos resendota al la servilo, kiam estos
 *                           kruco. (kutime la sama kiel $komparo.)
 * @param string $postteksto teksto por montri post la bokso.
 * @param string $kutima     se != "" kaj $io == "", tiam estas kruco.
 * @param string $kasxe      se "jes" (defauxlto), aldonu kasxitan
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
 * markobutono, eble kun skripto vokata je sxangxoj.
 * 
 * <pre>
 *    [X]  aŭ  [_]
 * </pre>
 * Se markita, dum sendado sendas "JES", alikaze "NE".
 *
 * @param string $nomo la nomo de la markobutono.
 * @param string $io se $io[0] == 'J', la bokso estas markita.
 * @param string $skripto jxavoskripto, estos vokata por cxiu
 *                       sxangxo de la stato, t.e. je krucigo
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
 * @todo dauxrigi dokumentadon.
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
 /* testas je malpleneco kaj enkodas laux HTML leĝoj */
 /* ################################################# */

 function malplentesto (&$io,$err="")
{
    global $parto;
    // TODO:? Cxu vi povas diri, kion fakte faras (faru) la funkcio malplentesto()?

    // tranformas cxion HTML specialan signon, por ke mi jxuste enskibas ĝin en la datumaro

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
 * .---------.------------.
 * | teksto  | postteksto |
 * '---------'------------'
 *
 * Tenas datumojn kasxe, sed krome montras tabellinion kun
 * titolo kaj aldona teksto.
 *
 * $teksto     - la titolo
 * $nomo       - la nomo de la variablo
 * $valoro     - la sendenda valoro
 * $postteksto - teksto montrenda post la kasxilo. Se malplena,
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
 * iun agon. Alikaze montras strekitan tekston (sen ligilo).
 *
 * $kien   - la ligota paĝo
 * $nomo   - nomo de la ligilo
 * $celo   - la kadron, en kiu la paĝo montriĝu
 *           (nur necesa, se ne la defauxlta)
 * $ago    - la ago, por kiu oni bezonas la rajton.
 * $montru - se ne komencas per "j", kaj oni ne rajtas,
 *           la teksto tute ne montriĝu.
 */
function rajtligu($kien,$nomo,$celo="",$ago="",$montru="j")
{
    // Ni testas, cxu oni rajtas iri al la ligota paĝo
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
 *  $kien - la URI de la paĝo.
 *  $nomo - la teksto de la ligilo (en eo-kodigo)
 *  $celo - (nenecesa) se en alia ol la defauxlta
 *          kadro, donu ties nomon.
 */
function ligu($kien,$nomo,$celo="")
{
    echo ' &nbsp;' . donu_ligon($kien, $nomo, $celo);
}

/**
 * Redonas HTML-ligilon.
 *
 *  $kien - la URI de la paĝo.
 *  $nomo - la teksto de la ligilo (en eo-kodigo)
 *  $celo - (nenecesa) se en alia ol la defauxlta
 *          kadro, donu ties nomon.
 */
function donu_ligon($kien,$nomo,$celo="")
{
    $rez = '<a href="'.str_replace('&', '&amp;', $kien).'" ';
    if ($celo)
        {
            $rez .= "target='$celo'";
        }
    $rez .= ">";
    $rez .= eotransform($nomo);
    $rez .= "</a>";
    return $rez;
}

/**
 * alligas iun paĝon/dosieron kun aldona hazarda numero, por
 * eviti uzon de retumilan stokejo.
 */
function hazard_ligu($kien, $nomo, $celo="")
{
    ligu($kien . "?rand=" . rand(1000,9999), $nomo, $celo);
}

/**
 * butono kun sia propra POST-formulareto, por uzo anstataux
 * simpla ligo por fari iun agon.
 *
 * Ne uzu ene de aliaj formularoj!
 *
 * $kien - kiun paĝon voki
 * $titolo - teksto sur la butono
 *
 * $valoroj - kion sendi (teksto) (defauxlto: 'ne_gravas')
 * $nomo    - nomo de la butono   (defauxlto: 'sendu')
 *
 * alternative:
 *
 *  $valoroj - array(), kiu enhavas nomojn kaj valorojn sendendajn per
 *            la formularo (inkluzive la butono).
 *  $nomo  - nomo de la butono (defaulxto: sendu).
 *           $nomo kaj $valoroj[$nomo] estas uzataj por la butono, se
 *           $valoroj[nomo] ekzistas, alikaze la unua paro en $valoroj
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
        // la restantaj ni metas kasxite
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
 * .-----------.
 * |  titolo   |
 * '-----------'
 *
 * Butono por sendi formularon (submit).
 *
 * $titolo - la teksto de la butono. Povas uzi c^-kodigon.
 */
function send_butono($titolo)
{
    echo "<input name='sendu' value='";
    eoecho ($titolo);
    echo "' size='18' type='submit'> \n";
}

/**
 * .-----------.
 * |  titolo   |
 * '-----------'
 *
 * Butono por sendi formularon (submit).
 *
 * $valoro - la valoro de la butono (estos sendota).
 * $titolo - la teksto de la butono. povas enhavi HTML-on kaj uzi c^-kodigon.
 * $nomo   - la nomo de la butono, defauxlto "sendu".
 */
function butono($valoro, $titolo, $nomo="sendu")
{
    echo "<button type='submit' name='$nomo' value='$valoro'>";
    eoecho($titolo);
    echo "</button>\n";
}

/**
 * ducela eldono por tabellinioj
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
 * $sql - la SQL-demando. La rezulto enhavu almenaux "ID", "nomo", "personanomo"
 *         kaj "renkNumero" kiel kampoj.
 *         Ekzempla SQL-demando por cxiuj partoprenantoj:
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
 *         La defauxlta valoro estas "partoprenantoidento".
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

                    // Ni sercxas por cxiu renkontiĝo maksimume unu foje la
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
 * .--------.------------------------.
 * | teksto |  [_______]  postteksto |
 * '--------'--|       |-------------'
 *             |       |
 *             |       |
 *             '-------'
 * $teksto  - titolo
 * $nomo    - la interna nomo.
 * $elektebloj - array kun la diversaj ebloj, en la formo
 *                interna => montrata
 * $defauxlto - kiu eblo estos antauxelektita, se
 *              ne estas jam elektita alia (per $_POST[$nomo]).
 * $postteksto - teksto aperonta apud la elektilo.
 *
 */
function tabela_elektilo($teksto, $nomo, $elektebloj,
                         $defauxlto="", $postteksto = "") {
    eoecho("<tr><th><label for='" . $nomo . "'>" . $teksto .
           "</label></th><td>");
    elektilo_simpla($nomo, $elektebloj, $defauxlto);
    eoecho($postteksto . "</td></tr>\n");
}


/**
 * .--------.------------------------.
 * | teksto |  [_______]  postteksto |
 * '--------'--|       |-------------'
 *             |       |
 *             |       |
 *             '-------'
 * $teksto  - titolo
 * $nomo    - la interna nomo.
 * $tabelo - la abstrakta nomo de la datumbaztabelo.
 * $kampo_teksto - la kampo por la tekstoj
 * $kampo_interna - la kampo por la valoroj sendotaj
 * $defauxlto     - kio estos antauxelektata, se $_POST['nomo'] ne ekzistas.
 * $restriktoj    - pliaj restriktoj por la elekto
 * $defauxlto - kiu eblo estos antauxelektita, se
 *              ne estas jam elektita alia (per $_POST[$nomo]).
 * $postteksto - teksto aperonta apud la elektilo.
 *
 */
function tabela_elektilo_db($teksto, $nomo, $tabelo,
                            $kampo_teksto="nomo",
                            $kampo_interna = "ID",
                            $defauxlto="",
                            $restriktoj="",
                            $postteksto="") {
    eoecho("<tr><th><label for='" . $nomo . "'>" . $teksto .
           "</label></th><td>");
    elektilo_simpla_db($nomo, $tabelo, $kampo_teksto, $kampo_interna,
                       $defauxlto, $restriktoj);
    eoecho($postteksto . "</td></tr>\n");
}



/** helpfunkcio por konverti nomon de funkcio al legebla
 *  teksto por la listo.
 * TODO: pli bona loko, eble cxe aliaj konverto-funkcioj.
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
 *           __________    ____
 *   Titolo [_________]   | v |
 *          |         |   '---'
 *          |         |
 *          |         |
 *          '---------'
 * aux:
 *
 *   Titolo valoro 
 *
 * elektilo kun OK-butono en propra formulareto.
 * 
 * $titolo       - priskribo de la enhavo de la elektilo.
 * $ago          - adreso de retpaĝo, kiu akceptas la sendajxon
 *                  (por la 'action'-atributo.)
 * $nomo         - nomo de la sendenda informo
 * $elekteblecoj - array() el elekteblecoj, en formo
 *                   id => teksto
 *                  La tekstoj estos montrataj, la ID estos
 *                  sendota al $ago.
 * $defauxlto    - ID de la elemento, kiu estos antauxelektita
 * $rajto        - se != "", rajto kiun la uzanto devos havi por
 *                 vidi/uzi la elektilon. Alikaze nur estos
 *                 montrata la titolo kun la valoro
 *                  (= $elekteblecoj[$defauxlto]).
 * $butonteksto  - teksto por la butono - defauxlto estas iu hoko.
 */
function elektilo_kun_butono($titolo, $ago, $nomo,
                             $elekteblecoj, $defauxlto,
                             $rajto="", $butonteksto="")
{
    //    echo "<!-- defauxlto: " . $defauxlto . "-->";
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
 *   __________
 *  [_________]   aldonajxo
 *  |         |
 *  |         |
 *  |         |
 *  '---------'
 *
 * kreas elektilon sen tabelkampo
 * $nomo - la interna nomo.
 * $elektebloj - array kun la diversaj ebloj, en la formo
 *                interna => montrata
 * $defauxlto - kiu eblo estos antauxelektita, se
 *              ne estas jam elektita alia (per $_POST[nomo]).
 * $aldonajxo - teksto aperonta apud la elektilo.
 */

function elektilo_simpla($nomo, $elektebloj, $defauxlto="",
                         $aldonajxoj="")
{
    // se iu estas donita jam lastfoje,
    // prenu tiun kiel defauxlto.

    //    echo "<!-- defauxlto: " . $defauxlto . "-->";
    if ($_POST[$nomo])
        {
            $defauxlto = $_POST[$nomo];
        }
    //    echo "<!-- defauxlto: " . $defauxlto . "-->";
    echo "  <select name='$nomo' id='$nomo'>\n";
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
 * funkcias kiel elektilo_simpla, sed prenas la tekstojn
 * el iu datumbaztabelo.
 * $nomo    - la nomo de la elektilo.
 * $tabelo - la abstrakta nomo de la datumbaztabelo.
 * $kampo_teksto - la kampo por la tekstoj
 * $kampo_interna - la kampo por la valoroj sendotaj
 * $defauxlto     - kio estos antauxelektata, se $_POST['nomo'] ne ekzistas.
 * $restriktoj    - pliaj restriktoj por la elekto
 * $aldonajxoj    - teksto aperanta post la elektilo.
 */
function elektilo_simpla_db($nomo, $tabelo, $kampo_teksto="nomo",
                            $kampo_interna = "ID",
                            $defauxlto="", $restriktoj="", $aldonajxoj="")
{
    if ($_POST[$nomo])
        {
            $defauxlto = $_POST[$nomo];
        }
    $rez = sql_faru(datumbazdemando(array($kampo_teksto => 'teksto',
                                          $kampo_interna => 'ID'),
                                    $tabelo, $restriktoj));
    echo "  <select name='$nomo' id='$nomo'>\n";
    while($linio = mysql_fetch_assoc($rez)) {
        echo "    <option value='" . $linio['ID'] . "' ";
        if ($linio['ID'] == $defauxlto) {
            echo " selected='selected'";
        }
        eoecho( " >" . $linio['teksto'] . "</option>\n");
    }
    echo "  </select>\n";
    if ($aldonajxoj)
        echo $aldonajxoj;
}


/**
 * kreas tabelon de cxiuj notoj de la partoprenanto kun menciita ID.
 *
 * $ppID - identigilo de  la partoprenanto.
 * $kapteksto (opcia) - se donita, kreas tutan HTML-dokumenton kaj uzas
 *                      tiun tekston kiel enkondukan tekston pri la tabelo.
 *                      Alikaze nur eldonas la tabelon.
 */
function listu_notojn($ppID, $kapteksto="") {
    $sql = datumbazdemando(array("ID", "prilaborata", "dato",
                                 "subjekto","kiu", "kunKiu","tipo"),
                           "notoj",
                           "partoprenantoID = '$ppID'");
  
    sercxu($sql, 
           array("dato","desc"), 
           array(array('0','','->','z','"notoj.php?wahlNotiz=XXXXX"','-1'), 
                 array('prilaborata','prilaborata?','XXXXX','z','','-1'), 
                 array('dato','dato','XXXXX','l','','-1'), 
                 array('subjekto','subjekto','XXXXX','l','','-1'), 
                 array("kiu","kiu",'XXXXX','l','','-1'), 
                 array("kunKiu","kun Kiu?",'XXXXX','l','','-1'), 
                 array("tipo","tipo",'XXXXX','l','','-1')
                 ), 
           array(array('', array('&sum; XX','A','z'))),
           "notoj-transfero",
           array('Zeichenersetzung'=>
                 array('1'=>array('j'=>'<strong class="malaverto">prilaborata</strong>',
                                  '' =>'<strong class="averto">neprilaborata</strong>',
                                  'n'=>'<strong class="averto">neprilaborata</strong>')
                       ),
                 ),
           0,$kapteksto,'', $kapteksto ? "jes" : "ne");
    
}



/**
 * Ĝenerala sercx-funkcio.
 *
 * Sercxas en la datumbazo kaj montras la rezulton en HTML-tabelo.
 *
 * $sql - la SQL-demando (sen ordigo).
 *
 * $ordigo - array(),
 *   $ordigo[0]:  laux kiu kolumno la rezultoj ordiĝu
 *   $ordigo[1]:  cxu la rezultoj ordiĝu pligrandiĝanta ("ASC") aux
 *                malpligrangiĝanta ("DESC")
 *
 * $kolumnoj - array() de array-oj, por la unuopaj kolumnoj. Por cxiu kolumno,
 *      la array enhavu la sekvajn ses komponentojn (cxiuj cxeestu, ecx se malplenaj):
 *    [0] - aux nomo aux numero de kampo de la SQL-rezulto.
 *          Prefere uzu nomon, cxar per numero la ordigo ne funkcias.
 *    [1] - la titolo de la kolumno
 *    [2] - La teksto, kiu aperu en la tabelo. Se vi uzas XXXXX (jes, 5 iksoj),
 *          tie aperas la valoro el la SQL-rezulto.
 *    [3] - aranĝo: cxu la valoroj aperu dekstre ("r"), meze ("z") aux
 *             maldekstre ("l") en la tabelkampo?
 *    [4] - se ne "", la celo de ligilo. (Alikaze ne estos ligilo.)
 *    [5] - Se estas ligilo, kaj cxi tie ne estas -1, dum klako al
 *          la ligilo en la menuo elektiĝas la persono, kies identifikilo
 *          estas en la kampo, kies nomo/numero estas cxi tie.
 *
 * $sumoj - jen indikoj pri linioj kun sumoj de la teksto.
 *      por cxiu sum-linio ekzistas array (en $sumoj). En cxiu linio-array
 *      estas po 3 elementoj por kolono. [TODO: trovu pli bonan sistemon.]
 *   [3*n+0] - La teksto de la kampo. Se vi uzas XX, tie aperos la rezulto
 *             de la sumado.
 *   [3*n+1] - La speco de la sumado. eblecoj:
 *              A - simple nur kalkulu, kiom da linioj estas.
 *              J - kalkulu, kiom ofte aperas 'J' en la koncerna kampo
 *              N - adiciu la numerojn en la koncerna kampo.
 *              S - speciala sumado, rigardu cxe $extra[Spaltenrechnung].
 *   [3*n+3] - aranĝo ('l', 'r', 'z' - vidu cxe $kolumoj - [3].)
 *
 * $identifikilo - (TODO: ankoraux ne estas uzata - cxu vere?)
 *
 * $extra - aldonaj parametroj. Se tiaj ne ekzistas, eblas uzi 0.
 *      Alikaze estu array, kies sxlosiloj estu iuj el la sekve
 *      menciitaj. La valoroj havas cxiam apartajn signifojn.
 *    [Zeichenersetzung]  - ebligas la anstatauxigon
 *                          de la valoroj per iu ajn teksto (aux HTML-kodo).
 *                la valoro estu array, kiu enhavu por cxiu kolumno, kie
 *                okazu tia anstatauxigo (sxlosilo=numero de la kolumno,
 *                komencante per 0), plian array, kiu enhavu cxiun
 *                anstatauxotan valoron kiel sxlosilo, la anstatauxontan
 *                valoron kiel valoro. Ekzemplo:<code>
 *       array('1' => array('j'=>'&lt;b>&lt;font color=green>prilaborata',
 *                          ''=>'&lt;b>&lt;font color=red>neprilaborata',
 *                          'n'=>'&lt;b>&lt;font color=red>neprilaborata'))</code>
 *          En kolumno 1 (en la teksto enmetota por XXXXX) cxiu 'j' estas
 *          anstatauxita per "prilaborata", cxiu '' kaj 'n' per "neprilaborata".
 *          En aliaj kolumnoj ne okazos tia anstatauxo.
 *    [anstatauxo_funkcio] - funkcias simile kiel "Zeichenersetzung",
 *               sed anstataux anstatauxa array() estu nomo de funkcio,
 *               kio estos vokata por eltrovi la valoron.
 *               Ĝi nur estos vokota unufoje por la tuta kampo, ne por
 *               cxiu litero de ĝi.
 *    [okupigxtipo] - anstatauxigu en iu kolumno la okupiĝtipvaloron per
 *                    la nomon de tiu tipo.
 *               La valoro estu kolumnonumero. La valoro de la koncerna
 *               datumbazkampo estos donita al la funkcio okupigxtipo()
 *               (en iloj_sql), kaj ties rezulto estas la teksto en tiu
 *               kolumno.
 *
 *    [SpaltenRechnung] - sumigu valorojn de iu kampo, kiam alia kampo enhavas 'j'.
 *               La valoro estu array, kies nula elemento estu kamponomo aux -numero.
 *               Se en iu sumig-ordono aperas la sumadospeco 'S', tiam
 *               tie estos sumita la valoroj de tiu cxi kampo, en tiuj linioj,
 *               kies sum-kampo enhavas 'j'. (Jes, malgranda 'j'.)
 *               [TODO:  Nun, 2004-09-30, neniu paĝo uzas tiun cxi funkcion.
 *                  Eble mi forigos ĝin (aux sxanĝos la sintakson).]
 *    [litomanko] - montru aparte, en kiuj noktoj ankoraux mankas litoj.
 *               La valoro estu kamponomo aux -numero.
 *               La valoro de tiu kampo estu partoprenidento.
 *               Je la fino de la linio (post la aliaj kolumnoj) estos
 *               montrita, en kiuj noktoj tiu partoprenanto jam havas
 *               liton, kaj en kiuj noktojn ankoraux mankas.
 *               Poste aperos ligilo "sercxu" al la cxambrodisdono.
 *    [tutacxambro]
 *               La valoro estu kamponomo aux -numero de kampo kun partopreno-ID.
 *               En aparta linio post cxiu rezultlinio estos montrataj la
 *               datoj de la unua cxambro, en kiu tiu partoprenanto loĝas.
 * $csv - tipo de la rezulto. Eblaj valoroj:
 *   0 - HTML kun bunta tabelo
 *   1 - CSV (en HTML-ujo)
 *   2 - CSV por elsxuti
 *   3 - CSV por elsxuti, en UTF-8
 * $antauxteksto - teksto, kiu estu montrata antaux la tabelo.
 *                 (Ĝi estas uzata nur kun $proprakapo == 'jes').
 * $almenuo      - se ĝi ne estas "", post la tabelo aperas ligo
 *                 "Enmeti en la maldekstra menuo", kies alklako
 *                 aldonas la rezulton en la maldekstra menuo.
 *                 Por ke tio funkciu, la sql-sercxfrazu redonu
 *                 kampojn nomitaj 'nomo', 'personanomo', 'renkNumero'
 *                 kaj 'ID' (kiu estu partoprenanto-ID).
 * $proprakapo   - montras la tabelon ene de <html><body>-kadro, kun
 *                 ebla antauxteksto. (Estas uzata nur, se $csv < 2.)
 */
function sercxu($sql, $ordigo, $kolumnoj, $sumoj, $identifikilo, $extra, $csv, $antauxteksto, $almenuo, $proprakapo="jes")
{
    sercxu_nova($sql, $ordigo, $kolumnoj, $sumoj, $identifikilo, $extra, $csv, $antauxteksto, $almenuo, $proprakapo);

}


?>