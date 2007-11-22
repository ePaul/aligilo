<?php

/**
 *
 * Ebleco preni tekstojn laux renkontigxo
 * el la datumbazo.
 *
 **********************************************

 CREATE TABLE tekstoj (
  ID			int(10)	 NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'eindeutige Nummer',
  renkontigxoID int(10)	 NOT NULL COMMENT 'zu welchen Renkontigxo gehört der Text?',
  mesagxoID		char(20) NOT NULL COMMENT 'zum Finden des Textes im Programm',
  teksto		text	 NOT NULL COMMENT 'der Text selbst.',
  UNIQUE (renkontigxoID, mesagxoID)
 ) COMMENT = 'tabelo por lokaligo de tekstoj (-> tekstoj.php)'

 ********************
 *
 *  Signifoj de la mesagxoID:
 * --------------------------
 *
 * vidu http://www.esperanto.de/dej/vikio.pl?IS-Datenbazo/Tekstoj
 * aux ../doku/tekstoj.txt
 *
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
   * rezulto:
   * array(
   *   'mesagxoID' =>  ($identifikilo, aux $identifikilo sen lingva postfikso)
   *   'priskribo' =>  la priskribo-teksto
   *   'opcioj'    =>  array(), kiu enhavas la opciojn.
   *   'kategorio' =>  nomo de kategorio
   *   ) 
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


//holt alle Einzahler aus den Texten und zeigt sie an.
function montru_elekto_liston($teksto_id,$pago_tipo,$butono_nomo,$kutima_teksto='')
{
  $antauxpaguloj = donu_tekston($teksto_id);
  
  $antauxpaguloj = explode("\r\n",$antauxpaguloj);

  echo "<BR><BLOCKQUOTE><p>";
  
  $uloj = array();
  
  foreach($antauxpaguloj as $linio)
    {
      // echo "hallo:".$ulo."||";
      if ($linio[0]=='#') continue;
      
      if ($linio[0]=='-') {echo "</p>\n<p>";continue;}
      
      list($ulo, $teksto) = explode("|",$linio);
	  $uloj[] = $ulo;
	  if (!isset($teksto))
		$teksto = $kutima_teksto. $ulo;
	  
      entajpbutono("",$butono_nomo,$pago_tipo,$ulo,$ulo,$teksto."<br />\n");
    }
  if ($pago_tipo and !in_array($pago_tipo, $uloj))
	{
	  entajpbutono("<br/>", tipo,$pago_tipo, $pago_tipo, $pago_tipo,
				   "<b>malnova:</b> ".$pago_tipo."<br />\n");
	}

    echo "</p></BLOCKQUOTE>";
}


function anstatauxu($teksto, $sxangxoj)
{
  foreach($sxangxoj AS $sercxu => $per)
	{
	  $teksto = str_replace($sercxu, $per, $teksto);
	}
  return $teksto;
}





/**
 * Eta sxablona sistemo ... por ekzemple krei unuan konfirmilon.
 *
 * Jen la gramatiko:
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
 * simpla_teksto -> <sinsekvo de literoj, kiu ne enhavas
 *                    '{{', '[[', ']]', '}}'. Povas esti
 *                    malplena. >
 *
 * variablonomo  -> simpla_nomo
 *                | simpla_nomo '.' variablonomo
 *
 * simpla_nomo   -> <sinsekvo de litero, kiu formas
 *                        legalan PHP-variablonomon.>
 *-----------
 * La tekstero de kondicxo-parto estas nur montrata,
 *   se la valoro de la variablo estas nek null/false/ktp.
 *   nek 'n'/'N'.
 * variablo estas anstatauxigita per sia valoro
 *   en $datumoj, kie oni uzas la '.' por disigi
 *   array()-nivelojn.
 * simpla_teksto restas, kiel gxi estas.
 *
 * La funkcio(j) ne tute implementas la gramatikon, nome ene
 * de simpla teksto foje estas akceptataj iuj el {{', '[[',
 *  ']]', '}}' (sen erarmesagxo). Sed cxiuj tekstoj, kiuj
 * konformas al la gramatiko estas traktataj gxuste.
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


?>