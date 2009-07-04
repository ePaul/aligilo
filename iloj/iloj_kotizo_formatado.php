<?php


  /**
   * diversaj klasoj, kiuj povas formati kotizo-tabelon
   * (laux persono, aux gxenerale.)
   *
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @since Revizo 141 (antauxe parto de iloj_kotizo.php)
   * @copyright 2007-2008 Paul Ebermann.
   *       Uzebla laÅ­ kondiÄ‰oj de GNU Äœenerala Publika Permesilo (GNU GPL)
   */




  /**
   * superklaso por la (entutaj) Formatiloj por Kotizosistemoj.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   */
class KotizoSistemFormatilo {


    /**
     * Konstruilo.
     *
     * $lingvo - la lingvo uzenda (dulitera kodo).
     */
    function KotizoSistemFormatilo($lingvo) {
        $this->lingvo = $lingvo;
    }


    /**
     * formatas tabelon de la formo kreita de
     * kotizosistmo->kreu_kotizotabelon().
     */
    function formatu_tabelon($tabelo) {
        echo "Funktion formatu_tabelon nicht Ã¼berschrieben! (in "
            . var_export($this, true) . ")";
    }


}


/**
 * Formatas la datumojn laux JSON-formato, t.e. en simpla
 * JavaScript-formo.
 *
 * @link http://www.json.org/
 * @package aligilo
 * @subpackage iloj
 * @author Paul Ebermann
 */
class JSONKotizoSistemFormatilo extends KotizoSistemFormatilo {


    function JSONKotizoSistemFormatilo() {
        $this->KotizoSistemFormatilo("");
    }

    /**
     * kreas jxavaskript-ordonon el la tabelo.
     */
    function kreu_ordonon($prefix, $tabelo, $postfix) {
        return
            $prefix . $this->formatu_liston($tabelo,
                                            str_repeat(' ', strlen($prefix))) .
            $postfix . "\n";
    }

    function formatu_liston($tabelo, $indent) {
        //        echo "<!--" . var_export($tabelo, true) . "-->";
        $rezulto = "{";
        $indent .= " ";
        if (!is_array($tabelo)) {
            return '"' . $tabelo . '"';
        }
        foreach($tabelo AS $id => $valoro) {
            if ($id == 'kotizo') {
                return '"' . (int)$valoro . '"';
            }
            else if ('titolo' == $id) {
            }
            else {
                $prefikso = ' "' . $id . '" : ';
                $nova_indent = $indent. str_repeat(' ', strlen($prefikso));
                $rezulto .= $prefikso;
                $rezulto .= $this->formatu_liston($valoro, $nova_indent);
                $rezulto .= ",\n" . $indent;
            }
        }
        if (strlen($rezulto) > 2) {
            $lastKomma = strrpos($rezulto, ",");
            $rezulto = substr($rezulto, 0, $lastKomma);
        }
        $rezulto .= "}";
        return $rezulto;
    }


    function formatu_tabelon($tabelo) {
        return $this->formatu_liston($tabelo, "");
    }

}




  /**
   * superklaso por cxiuj (popersona) kotizoformatiloj.
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   */
class KotizoFormatilo {

    var $lingvo;

    /**
     * Konstruilo.
     *
     * $lingvo - la lingvo uzenda (dulitera kodo).
     */
    function KotizoFormatilo($lingvo) {
        $this->lingvo = $lingvo;
    }


    /**
     * formatas tabelon kreitan en
     *   Kotizokalkulilo::kreu_kotizotabelon().
     *  $tabelo
     *    array() el lini-grupoj, kiuj po havas la formon
     *       array('titolo' => titolo de linigrupo,
     *             'enhavo' => array() el unu gxis pluraj
     *                         du- aux tri-elementaj array()-oj,
     *                         kiuj po enhavas la enhavon de unu
     *                         linio laux kampoj.
     *                         Tiuj enhavo-elementoj povas mem esti
     *                          aux cxeno, numero, aux
     *                          array('eo' => ..., 'de' => ..., ...)
     */
    function formatu_tabelon($tabelo) {
        // implementenda
        echo "<!-- formatu_tabelon() de la superklaso! -->";
    }


    /* **********************************************************
     * Helpaj funkcioj
     */



    /**
     * Elektas laux lingvo unu el pluraj tekstoj.
     *
     * $tekstoj
     *    aux cxeno, aux array() de la formo:
     *      'eo' => ...,
     *      'de' => ...
     *       ...
     *
     * 'eo' estas la defauxlta lingvo uzata, kiam la elektita
     * lingvo mankas. Se $teksto ne estas array(), gxi mem estas
     * redonita.
     */
    function lauxlingve($tekstoj) {
        if (is_array($tekstoj)) {
            $teksto = $tekstoj[$this->lingvo] or
                $teksto = $tekstoj['eo'];
            return $teksto;
        }
        else {
            return $tekstoj;
        }
    }


}


/**
 * formatado kiel HTML-tabelo (eldonita per echo).
 * @package aligilo
 * @subpackage iloj
 * @author Paul Ebermann
 */
class HTMLKotizoFormatilo extends KotizoFormatilo {

    var $html_class;


    /** konstruilo
     * @param asciistring $htmlclass 
     *            class-atributo por la kreitaj tabeloj.
     *               defauxlto estas 'rezulto'.
     */
    function HTMLKotizoFormatilo($htmlclass='rezulto') {
        $this->KotizoFormatilo('eo');
        $this->html_class = $htmlclass;
    }


    /**
     * formatas la tabelon.
     */
    function formatu_tabelon($tabelo) {
        $html = "<table class='" . $this->html_class. "'>\n";
        foreach($tabelo AS $linio) {
            $titolo = $linio['titolo'];
            if (is_array($titolo)) {
                        $titolo = $titolo['eo'];
            }
            $enhavo = $linio['enhavo'];
            $html .= "<tr><th rowspan='" . count($enhavo) . "'>" . $titolo .
                "</th>";
            $unua_linio = array_shift($enhavo);
            $html .= $this->html_formatu_linireston($unua_linio) . "</tr>\n";
            foreach($enhavo AS $sublinio) {
                $html .= "<tr>" . $this->html_formatu_linireston($sublinio) . "</tr>\n";
            }
        }
        $html .= "</table>\n";
        eoecho($html);
    }



    /**
     * $linio - array():
     *           [0] => iu teksto
     *           [1] => teksto aux nombro - se nombro, gxi estos formatita
     *                   kiel mono
     *           [3] => (eble) plia nombro - estos formatita kiel mono
     *                    kun + aux -, se gxi ne estas teksto.
     *           [grava] => true  - se donita, la linio estas montrita
     *                               per <strong>.
     */
    function html_formatu_linireston($linio) {
        $rez = "";
        if (DEBUG) {
            $rez .= "<!-- html_formatu_linireston(" . var_export($linio, true) . "-->";
        }

        $prefikso = $postfikso = "";
        if ($linio['grava']) {
            $prefikso = "<strong>";
            $postfikso = "</strong>";
        }
        foreach($linio AS $index => $cxelo) {
            if (! is_int($index))
                continue;
            if (DEBUG) {
                $rez .= "<!-- [" . var_export($cxelo, true) . "] -->";
            }
            if (is_array($cxelo)) {
                $cxelo = $cxelo['eo'];
            }
            $allineado = formatu_cxelon($cxelo, $index);
            $rez .= "<td class='allin" . $allineado . "'>" .
                $prefikso .$cxelo . $postfikso. "</td>";
        }
        return $rez;

        $rez .= "<td>" . $linio[0]['eo'] . "</td><td>";
        if (is_numeric($linio[1])) {
            $rez .= number_format($linio[1], 2, ".", "") . " E^</td>";
        }
        else {
            $rez .= $linio[1] . "</td>";
        }
        if (isset($linio[2])) {
            $nombro = number_format($linio[2], 2, ".", "");
            if ($nombro[0]!= '-') {
                $nombro = '+ ' . $nombro;
            }
            $rez .= "<td>" . $nombro . " E^</td>";
        }
        return $rez;
    }

} // HTMLKotizoFormatilo

/**
 * eldono al PDF-objekto, ekzemple por dua informilo aux
 * la akceptofolio.
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
 */
class PDFKotizoFormatilo extends KotizoFormatilo {

    var $pdf;
    var $unikode;


    /**
     * $pdf - la TCPDF-objekto, al kiu sendi la rezultojn.
     */
    function PDFKotizoFormatilo(&$pdf, $lingvo='eo', $unikode=true) {
        $this->KotizoFormatilo($lingvo);
        $this->pdf = &$pdf;
        $this->unikode = $unikode;
    }

    /**
     * transformas la tekston laux lingvo kaj kodigo
     * al la tauxga rezulto.
     *
     * $tekstoj
     *    aux cxeno, aux array() de la formo:
     *      'eo' => ...,
     *      'de' => ...
     *       ...
     *
     * 'eo' estas la defauxlta lingvo uzata, kiam la elektita
     * lingvo mankas.
     *
     * La tekstoj estu kodita en UTF-8, eble kun esperantaj
     * signoj koditaj per c^-maniero.
     *
     * La funkcio redonas la tekston en gxusta kodigo por PDF-eldono.
     */
    function kodigu($teksto) {
        // TODO!: elprovu, cxu tiel funkcias en cxiuj kazoj.
        // gxi laboras iom alie ol la varianto en kreu_konfirmilon.php.
        if ($this->unikode) {
            return uni($this->lauxlingve($teksto));
        }
        else {
            return eo($this->lauxlingve($teksto));
        }
    }

    


    function formatu_tabelon($tabelo) {

        // kelkaj fiksitaj valoroj ...
        //  TODO: eble kalkuli, por esti pli flekseba.

	  $largxecoj = array('titolo'=>25, 50, 27, 23, 23);
        $alteco = 4;
        $grandaj_linioj = 0;
        $maks_grandaj_linioj = count($tabelo) - 1;
        $kadro = 0;
        $this->pdf->setFontSize(9);
                
                
                
        foreach($tabelo AS $granda_linio) {
            if ($grandaj_linioj > 0) {
                // supra linio
                $kadro = "T";
            }
            $this->pdf->setFont('', 'B');
            $this->pdf->cell($largxecoj['titolo'],
                             $alteco,
                             $this->kodigu($granda_linio['titolo']),
                             $kadro);
            $this->pdf->setFont('', '');
            $lasta = count($granda_linio['enhavo'])-1;
            foreach($granda_linio['enhavo'] AS $linIndex => $linio) {

                if ($linio['grava']) {
                    // dika tiparo
                    $this->pdf->setFont('', 'B');
                }

                for($index = 0; $index < 4; $index++) { // TODO: 3/4 aŭtomate kalkuli
                    $cxelo = $linio[$index];
                    debug_echo ("<!-- (" . $index . ": " .
                                var_export($cxelo, true) . ") -->");

                    $allineado = formatu_cxelon($cxelo, $index);

                    $this->pdf->cell($largxecoj[$index], $alteco,
                                     $this->kodigu($cxelo),
                                     $kadro, 0, $allineado);
                }
                // normala tiparo
                $this->pdf->setFont("", "");
                $this->pdf->ln();
                $this->pdf->cell($largxecoj['titolo'], 0, "");
                $kadro = 0;
            }
            $grandaj_linioj ++;
            $this->pdf->ln();
        }
    }

} // PDFKotizoFormatilo


/**
 * formatas la valoron de iu cxelo en la kazo,
 * ke gxi estas numero.
 *
 * $cxelo - la valoro. La nova valoro estos reen metita tien.
 * $kolumno - la indekso de la tabela kolumno (post la titolo
 *            kalkulita de 0). Nur kolumno 2 estas speciale
 *            traktita.
 *@return
 * la funkcio redonas la deziratan allinean direkton:
 *    - 'L' (maldekstre)
 *    - 'R' (dekstre)
 */
function formatu_cxelon(&$cxelo, $kolumno)
{
    $malnova = $cxelo;

    if (is_numeric($cxelo)) {
        $cxelo = number_format($cxelo, 2, ".", "") . " E^";
        // TODO: forigu la magian numeron 3
        // (= indekso de la lasta kolumno)
        if (3 == $kolumno) {
            // lasta kolumno, kie estas la sumoj
            if ($cxelo[0] == '-') {
                // aldonu spaceton
                $cxelo = '- ' . substr($cxelo, 1);
            }
            else {
                // aldonu +
                $cxelo = '+ ' . $cxelo;
            }
        }
        debug_echo ("<!-- formatu_cxelon(" . $malnova . ") => " . $cxelo .
                    ",R \n-->");
        return 'R';
    }
    else if (is_string($cxelo) and
             preg_match('#^(-|\+)? ?(\d+(?:\.\d+)?) ([A-Z]{3})$#',
                        $cxelo, $listo)) {
        $signo = $listo[1];
        $num = (double)$listo[2];
        $valuto = $listo[3];

        $nova_num = number_format($num, 2, '.', "");
        
        if ($signo == ''
            // TODO: forigu la magian numeron 3
            // (= indekso de la lasta kolumno)
            and $kolumno == 3)
            {
                $cxelo = '+ ' . $nova_num . ' ' . $valuto;
            }
        else
            {
                $cxelo = $signo . " " . $nova_num . " " . $valuto;
            }
        debug_echo ("<!-- formatu_cxelon(" . $malnova . ") => " . $cxelo .
                    ",R \n-->");


        return 'R';
    }
    debug_echo ("<!-- formatu_cxelon(" . $malnova . ") => " . $cxelo .
                ",L \n-->");
    return 'L';
}


/**
 * transformilo de PDF-cxelaj alineoj al tiuj de str_pad()
 *  (kaj nia plilongigu()).
 */
define('STR_PAD_L', STR_PAD_RIGHT);
define('STR_PAD_R', STR_PAD_LEFT);
define('STR_PAD_C', STR_PAD_BOTH);


/**
 * Formatilo por teksta kotizotabelo.
 *
 * @package aligilo
 * @subpackage iloj
 * @author Paul Ebermann
 */
class TekstaKotizoFormatilo extends KotizoFormatilo
{

    var $kodigo;
    var $preta_tabelo;


    /**
     * $kodigo - ekzemple 'x-metodo' aux 'utf-8'.
     */    
    function TekstaKotizoFormatilo($lingvo, $kodigo)
    {
        $this->Kotizoformatilo($lingvo);
        $this->kodigo = $kodigo;
    }


    function formatu_tabelon($tabelo) {

        $rezulto = "";

        $largxecoj = array();
        foreach($tabelo as $grupo) {
            //            echo "<!-- grupo: " . var_export($grupo, true) . "-->";
            $largxecoj['titolo'] = max($this->longeco($grupo['titolo']),
                                       $largxecoj['titolo']);
            foreach($grupo['enhavo'] AS $linio) {
                foreach($linio AS $i => $cxelo) {
                    //                for ($i = 0; $i < 3; $i++) {
                    if (is_int($i)) {
                        $cxelo = $linio[$i];
                        
                        formatu_cxelon($cxelo, $i);
                        $largxecoj[$i] = max($this->longeco($cxelo),
                                             $largxecoj[$i]);
                    }
                }
            }
        }
        debug_echo( "<!-- largxecoj: " . var_export($largxecoj, true) . "-->");

        $ekde_dua_grupo = false;
        foreach ($tabelo AS $grupo) {
            if ($ekde_dua_grupo) {
                $ekde_dua_kolumno = false;
                $rezulto .= "-";
                foreach($largxecoj AS $len) {
                    if ($ekde_dua_kolumno) {
                        $rezulto .= "-+-";
                    }
                    else {
                        $ekde_dua_kolumno = true;
                    }
                    $rezulto .= str_repeat('-', $len);
                }
                $rezulto .= "-\n";
            }
            $ekde_dua_grupo = true;
            $rezulto .= ' ' . plilongigu($this->kodigu($grupo['titolo']),
                                         $largxecoj['titolo']);
            $unua_linio = true;
            foreach ($grupo['enhavo'] AS $linio) {
                // TODO: se ne unua linio, antauxe metu spacon
                if ($unua_linio) {
                    $unua_linio = false;
                }
                else {
                    $rezulto .= str_repeat(' ', $largxecoj['titolo']+1);
                }
                foreach($largxecoj AS $i => $largxo) {
                    //                for ($i = 0; $i < 3; $i++) {
                    if (is_int($i)) {
                        $cxelo = $linio[$i];
                        $rezulto .= " | ";
                        $direkto = constant('STR_PAD_' . formatu_cxelon($cxelo, $i));
                        $rezulto .= plilongigu($this->kodigu($cxelo),
                                               $largxo,
                                               $direkto);
                    }
                }
                
                $rezulto .= "\n";
            }
        }
        
        $this->preta_tabelo = $rezulto;
    }


    /* *********** */



    function kodigu($teksto) {
        return eotransformado($this->lauxlingve($teksto),
                              $this->kodigo);
    }



    /**
     * kalkulas la longecon de tabelcxelo en signoj,
     * depende de lingvo kaj kodigo.
     */
    function longeco($teksto) {
        $len = mb_strlen($this->kodigu($teksto),
                         'utf-8');
        debug_echo( "<!-- longeco(" . var_export($teksto, true) . ") = " . $len . "-->");
        return $len;
    }
    

}


