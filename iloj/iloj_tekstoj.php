<?php

  /**
   * Kelkaj funkcioj rilataj al la tekstoj-tabelo en la datumbazo.
   *
   * Tiu enhavas renkontigxo-specifajn tekstojn, ekzemple sxablonojn
   * por auxtomataj mesagxoj.
   *
   *<code>
   * CREATE TABLE `tekstoj` (
   *   `ID` int(10) NOT NULL auto_increment,
   *   `renkontigxoID` int(10) NOT NULL default '0',
   *   `mesagxoID` varchar(30) character set ascii NOT NULL,
   *   `teksto` text collate utf8_esperanto_ci,
   *   PRIMARY KEY  (`ID`),
   *   UNIQUE KEY `renkontigxoID` (`renkontigxoID`,`mesagxoID`)
   * ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci
   *   COMMENT='tabelo por lokaligo de tekstoj (-> tekstoj.php)';
   *</code>
   *
   * La signifoj de la mesagxoID-valoroj (kaj la korespondaj tekstoj)
   * estas troveblaj en doku/tekstoj.txt - tiu estas ankaux uzata de
   * la enprograma teksto-redaktilo {@link tekstoj.php}.
   *
   * @todo Pripensu pli bonan traduk-sistemon.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   * legas la priskribojn de teksto kaj metas en globalan variablon
   * $GLOBALS['tekstpriskriboj'].
   */
function legu_tekstpriskribojn()
{
    // legu la dosieron.
    
    $dosiero = file($GLOBALS['prafix'].'/doku/tekstoj.txt');

    // por kapti komencajn komentojn - ne
    // estu tiaj, sed eble io misfunkciis ...
    $aktuala_nomo = "#";
    $aktuala_kategorio = '#';
    // cxi tien ni metos la ajxojn.
    $priskrib = array();

    foreach($dosiero AS $linio)
        {
            switch($linio{0})
                {
                case '\n':
                    // malplenaj linioj estas komentoj.
                    break;
                case '#':
                    // linioj komencantaj per # estas komentoj.
                    break;
                case '=':
                    // linioj komencantaj per = donas nomon de
                    // kategorio.
                    $aktuala_kategorio = trim($linio, '= ');

                    // kaze ke venos kelkaj priskriboj sen nova nomo,
                    // ni ne volas ilin je la lasta nomo antauxe.
                    $aktuala_nomo = "= " . $aktuala_kategorio . " =";
                    
                    break;
                case '|':
                    // priskribo-linio. aldonu al eble jam ekzistanta linio.
                    $priskrib[$aktuala_nomo]['priskribo'] .=
                        substr($linio, 1);
                    break;
                default:
                    list($aktuala_nomo, $opcioj) =
                        preg_split('/\s+/', $linio);
                    $priskrib[$aktuala_nomo]['mesagxoID'] = $aktuala_nomo;
                    $priskrib[$aktuala_nomo]['kategorio'] = $aktuala_kategorio;
                    $priskrib[$aktuala_nomo]['opcioj'] =
                        preg_split('/,\s*/', trim($opcioj, '[]\n'));
                } // switch
        }  // foreach

    $GLOBALS['tekstpriskriboj'] = $priskrib;

    if (DEBUG)
        {
            echo "<!-- tekstpriskriboj: " . 
                var_export($GLOBALS['tekstpriskriboj'], true) . "-->";
        }
}



  /**
   * redonas priskribon pri iu teksto.
   *
   * @return array <code>
   * array(
   *   'mesagxoID' =>  ($identifikilo, aux $identifikilo sen lingva postfikso)
   *   'priskribo' =>  la priskribo-teksto
   *   'opcioj'    =>  array(), kiu enhavas la opciojn.
   *   'kategorio' =>  nomo de kategorio
   *   ) 
   * </code>
   */
function donu_tekstpriskribon($identifikilo)
{
    if (DEBUG)
        {
            echo "<!-- donu_tekstpriskribon(" . $identifikilo . ") -->";
        }
    if (!$GLOBALS['tekstpriskriboj']) {
        legu_tekstpriskribojn();
    }
    if ($GLOBALS['tekstpriskriboj'][$identifikilo]) {
        return $GLOBALS['tekstpriskriboj'][$identifikilo];
    }
    $id = substr($identifikilo, 0, -3);
    return $GLOBALS['tekstpriskriboj'][$id];
}



/**
 * Donas tekston el la datumbazo.
 *
 * $identifikilo  - la mesagxidentifikilo (litercxeno).
 *                  pri la signifoj rigardu pli supre en
 *                  la dokumentado de la dosiero.
 *
 * $renkontigxo   - objekto de la klaso Renkontigxo (-> objektoj).
 *                  Ni sercxas la tekston por tiu renkontigxo.
 *
 *                  Vi povas ankaux forlasi gxin aux uzi "",
 *                  tiam la metodo uzas la sesio-variablon
 *                  $renkontigxo (se ekzistas) aux la globalan
 *                  variablon $renkontigxo
 *
 * Se la teksto ne ekzistas, la metodo anstatauxe 
 * redonas erarmesagxon ("la teksto ... ne trovigxis.")
 */
function donu_tekston($identifikilo, $renkontigxo="")
{
    debug_echo("<!-- renkontigxo: " . $renkontigxo->datoj['ID'] . "-->");
  if ($renkontigxo == "")
	{
	  if ($_SESSION["renkontigxo"])
		$renkontigxo = $_SESSION["renkontigxo"];
	  else
		$renkontigxo = $GLOBALS["renkontigxo"];
	}

  $sql = datumbazdemando("teksto",
						 "tekstoj",
						 array("mesagxoID = '$identifikilo'",
							   "renkontigxoID = '" . $renkontigxo->datoj["ID"] . "'")
						 );
  $rez = mysql_fetch_array(sql_faru($sql));
  if (empty($rez))
	return "[Text '$identifikilo' fehlt leider für Treffen " .
	  $renkontigxo->datoj["mallongigo"] . ". Bitte bei ".teknika_administranto." beschweren!]";
  else
      return trim($rez["teksto"]);
}

function donu_tekston_lauxlingve($identifikilo, $lingvo, $renkontigxo="")
{
  if ($lingvo != "eo")
	{
	  return donu_tekston($identifikilo. "_" .$lingvo, $renkontigxo);
	}
  else
	{
	  return donu_tekston($identifikilo, $renkontigxo);
	}
}


/**
 * kreas elekto-liston (per radiaj butonoj) el datumbaza teksto, ekzemple
 * por rabatkauxzoj aux (antaux)pagotipoj.
 *
 * Jen gramatiko:
 * <pre>
 *   listo          -> linio                             (1)
 *                  -> linio '\n' listo                  (2)
 *                                                        
 *   linio          -> komento                           (4)
 *                  -> elektero                          (5)
 *                  -> dividilo                          (6)
 *   komento        -> '#' komento-enhavo                (7)
 *   elektero       -> kodo                              (8)
 *                  -> kodo '|' enhavo                   (9)
 *   dividilo       -> '-' komento-enhavo               (10)
 *   komento-enhavo -> <em>teksto, sen '\n'</em>                 (11)
 *                      (ne estos uzata)
 *   kodo           -> <em>teksto, sen '\n' kaj '|'.</em>        (12)
 *                       Estos uzata kiel valoro por
 *                       sendi, se tiu butono estas
+                        elektita.</em>
 *   enhavo         -> <em>teksto, sen '\n' kaj '|'.</em>        (13)
 *                      Tiu estos montrata kiel teksto
 *                      de la radia butono.
 *                     Eblas uzo de c^-kodigo.
 * </pre>
 * @param string $teksto_id kodo por trovi la ĝustan tekston
 *                          el la datumbazo.
 * @param string $valoro kiu el la kodoj estu antauxelektita. Se estas ne-nula
 *               kaj mankas en la listo, ni kreas apartan elekteblecon por
 *               tiu.
 * @param string $butono_nomo je kiu nomo sendi la rezulto al la servilo.
 * @param string $kutima_teksto prefikso por krei la radiobutono-tekston
 *                              en kazo (8).
 * @param string $renkontigxo renkontigxo-objekto. Se ne donita, uzas
 *               $_SESSION['renkontigxo'] aux $GLOBALS['renkontigxo'].
 */
function montru_elekto_liston($teksto_id, $valoro, $butono_nomo,
                              $kutima_teksto='', $renkontigxo='')
{
    $teksto = donu_tekston($teksto_id, $renkontigxo);
    $listo = explode("\n",$teksto);

    echo "<p>\n";
  
    $uloj = array();
  
  foreach($listo as $linio)
    {
        $linio = trim($linio);
      // echo "hallo:".$ulo."||";
        if ($linio[0]=='#') {
            // komento
            continue;
        }
      
        if ($linio[0]=='-') {
            // nova grupo
            echo "</p>\n<p>";
            continue;
        }
      
        list($ulo, $teksto) = explode("|",$linio);
        $uloj[] = $ulo;
        if (!isset($teksto)) {
            $teksto = $kutima_teksto . $ulo;
        }
        
      entajpbutono("", $butono_nomo, $valoro,
                   $ulo, $ulo, $teksto."<br />");
    }
  // $valoro ne estas en la listo
  if ($valoro and !in_array($valoro, $uloj))
	{
        echo "</p><p>";
        entajpbutono("", $butono_nomo, $valoro,
                     $valoro, $valoro, "<b>malnova:</b> ".$valoro."\n");
	}

    echo "</p>";
}




/**
 * Eta sxablona sistemo ... ekzemple por krei unuan konfirmilon.
 *
 * Jen la gramatiko:
 *<pre>
 *---------
 * teksto        -> tekstero                                 (1)
 *                | tekstero kondicxo teksto                 (2)
 *
 * tekstero      -> simpla_teksto                            (3)
 *                | simpla_teksto variablo tekstero          (4)
 *
 * kondicxo      -> '[[?{{' variablonomo '}}' tekstero ']]'  (5)
 *
 * variablo      -> '{{' variablonomo '}}'                   (6)
 *
 * simpla_teksto -> &lt; sinsekvo de literoj, kiu ne enhavas
 *                    '{{', '[[', ']]' aux '}}'. Povas esti
 *                    malplena. >
 *
 * variablonomo  -> simpla_nomo
 *                | simpla_nomo '.' variablonomo
 *
 * simpla_nomo   -> &lt; sinsekvo de litero, kiu formas
 *                          legalan PHP-variablonomon.>
 *-----------
 * </pre>
 * La tekstero de kondicxo-parto estas nur montrata,
 *   se la valoro de la variablo estas nek null/false/ktp.
 *   nek 'n'/'N'.
 * variablo estas anstatauxigita per sia valoro
 *   en $datumoj, kie oni uzas la '.' por disigi
 *   array()-nivelojn.
 * simpla_teksto restas, kiel gxi estas.
 *
 * La funkcio(j) ne tute implementas la gramatikon, nome ene
 * de simpla teksto foje estas akceptataj iuj el '{{', '[[',
 *  ']]', '}}' (sen erarmesagxo). Sed cxiuj tekstoj, kiuj
 * konformas al la gramatiko, estas traktataj gxuste.
 */
function transformu_tekston($sxablono, $datumoj)
{
    $teksto = "";
    $sxablona_pozicio = 0;
    while (false !== ($komenco = strpos($sxablono, '[[?{{', $sxablona_pozicio)))
        {
            // la tekstero el (2):
            $teksto .= simpla_teksttransformo(substr($sxablono,
                                                     $sxablona_pozicio,
                                                     $komenco- $sxablona_pozicio),
                                              $datumoj);

            $kondicxofino = strpos($sxablono, '}}', $komenco+5);
            if ($kondicxofino === false)
                {
                    darf_nicht_sein();
                }
            $fino = strpos($sxablono, ']]', $kondicxofino);
            if ($fino === false)
                {
                    darf_nicht_sein();
                }
            $kondicxo =substr($sxablono,
                              $komenco+5,
                              $kondicxofino - ($komenco+5));
            // la variablonomo el (5):
            $datumo = teksttransformo_donu_datumon($kondicxo, $datumoj);
            if ($datumo and
                $datumo != 'n' and
                $datumo != 'N') {
                // la tekstero el (5):
                $teksto .=
                    simpla_teksttransformo(ltrim(substr($sxablono,
                                                        $kondicxofino+2,
                                                        $fino-($kondicxofino+2)),
                                                 "\r\n"),
                                           $datumoj);
                }
            // la sekva iteracio (aux la post-iteracia parto de la funkcio)
            // traktas la <teksto>n el (2).
            $sxablona_pozicio = $fino + 2;
        }
    // La tekstero el (1)
    $teksto .= simpla_teksttransformo(substr($sxablono, $sxablona_pozicio),
                                      $datumoj);
    return $teksto;
}


/**
 * traktas <tekstero>n el la gramatiko cxe
 * transformu_tekston().
 */
function simpla_teksttransformo($sxablonero, $datumoj)
{
    $teksto = "";
    $sxablona_pozicio = 0;
    while (false !== ($komenco = strpos($sxablonero, '{{', $sxablona_pozicio)))
        {
            // la <simpla_teksto> el (4).
            $teksto .= substr($sxablonero,
                              $sxablona_pozicio,
                              $komenco-$sxablona_pozicio);

            $fino = strpos($sxablonero, '}}', $komenco+2);
            if ($fino === false)
                {
                    darf_nicht_sein();
                }

            // la <variablo> el (4).
            $teksto .= teksttransformo_donu_datumon(substr($sxablonero,
                                                           $komenco+2,
                                                           $fino-($komenco+2)),
                                                    $datumoj);
            // la sekva iteracio (aux la post-iteracia parto de la funkcio)
            // traktas la <tekstero>n el (4).
            $sxablona_pozicio = $fino + 2;
        }
    // la simpla_teksto el (3).
    $teksto .= substr($sxablonero,
                      $sxablona_pozicio);
    return $teksto;
}

/**
 * Traktas <variablonomo> el la gramatiko cxe
 * transformu_tekston().
 */
function teksttransformo_donu_datumon($variablonomo, $datumoj)
{
    list($komenco, $resto) = explode('.', $variablonomo, 2);
    if ($resto and is_array($datumoj[$komenco]))
        {
            return teksttransformo_donu_datumon($resto, $datumoj[$komenco]);
        }
    else
        {
            return $datumoj[$komenco];
        }
}

/**
 * serĉas informojn uzatajn en ŝablona teksto el la tekstoj-tabelo
 * de la datumbazo.
 *
 * @param string $sxablono    ĉi tie ni serĉas.
 * @param string $sxablona_prefikso 
 * @param string $teksto_prefikso
 *
 * @return array de la formo $id => valoro, kun ĉiuj aperantaj
 *            tekstoj.
 */
function trovu_necesajn_tekstojn($sxablono, $sxablona_prefikso,
                                 $teksto_prefikso) {
    $sxablono = "_" . $sxablono;
    $listo = array();
    $pos = 0;
    $sercxprefikso =  "{{" . $sxablona_prefikso;
    
    while($pos = strpos($sxablono, $sercxprefikso, $pos)) {
        $finpos = strpos($sxablono, "}}", $pos);
        $id = substr($sxablono, $pos, $finpos - $pos);
        
        if (!isset($listo[$id])) {
            $listo[$id] = donu_tekston($teksto_prefikso . $id);
        }
        $pos = $finpos + 2;
    }
    return $listo;
}


?>