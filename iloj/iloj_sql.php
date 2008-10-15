<?php

  /**
   * Interago kun la datumbazo.
   *
   * Ĉiuj tabelnomoj, kiujn prenas tiuj funkcioj, estas abstraktaj
   * tabelnomoj, la transformon faras {@link traduku_tabelnomon()}.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



/**
 * Finas la programon kun kelkaj debug-informoj.
 *
 * Ĉi tiu funkcio ekzistas nur por
 * trovi cimojn en la programo.
 * Tiun ĉi funkcion oni nur voku je lokoj
 * en la programo, kiujn oni normale ne atingu.
 *
 * Ĝi printas la vok-ĉenon, kaj finas
 * la programon.
 *
 * @todo trovu taŭgan esperantan nomon.
 * @param mixed $klarigo se donita (= ne false), ĝi
 *                  ankaŭ estos aldonita al la eldonoj.
 */
function darf_nicht_sein($klarigo = "")
{
    eoecho("<p>Tiu kazo ne rajtus okazi, vers^ajne estas eraro en la programo." .
           " Bonvolu informi ");
    ligu('mailto:'.teknika_administranto_retadreso, teknika_administranto);
    eoecho (" pri tio, kun kopio de la subaj datoj.</p>");
    
    echo '<div align="left" style="border-top: solid thin; border-bottom: solid thin;"><pre>';
  if ($klarigo)
      {
          eoecho("Aldona informo:");
          var_export($klarigo);
          echo "<hr />";
      }
  var_export(debug_backtrace());
  print "</pre></div>";
  exit();
}

/**
 * Kreas AS-esprimon por tabelreferenco.
 *
 * Tradukas la abstraktan tabelnomon kun aliasnomo
 * al SQL-peco kun la konkreta tabelnomo
 * kaj la aliaso kiel aliasnomo.
 *
 * @uses traduku_tabelnomon() por trovi la konkretan tabelnomon el
 *                            la abstrakta.
 * @param string $alias la nomo, sub kiu estos uzata la tabelo poste.
 * @param string $tabelnomo la abstrakta tabelnomo de la tabelo.
 * @return sqlstring sql-pecon de la formo <val>konk($nomo) AS $alias</val>.
 */
function kreu_tabelan_as_esprimon($alias, $tabelnomo)
{
  return traduku_tabelnomon($tabelnomo) . " AS " . $alias;
}




/**
 * Tradukas abstraktan tabelnomon al konkreta tabelnomo.
 *
 * Ekzistas pluraj eblecoj, kiel traduki la nomon.
 * Dependas de la enhavo de {@link konfiguro/datumaro.php}.
 *
 * - (1) Se tie troviĝas funkcio
 *      tabelnomtradukilo(...),
 *     ĝi estas vokita per la abstrakta tabelnomo.
 *     Se la rezulto ne estas malplena (0, null, nedefinita),
 *     ĝi estos la konkreta tabelnomo.
 * - (2) Se ekzistas globala variablo $tabelnomtradukilo (kiu estu
 *     array), tiam ni serĉas la valoron de la tabelnomo
 *     (do, $tabelnomtradukilo[$tabelnomo]). Se la rezulto
 *     ne estas malplena, ĝi estos la konkreta tabelnomo.
 * - (3) Se ekzistas globalaj variabloj $tabelnomprefikso kaj/aŭ
 *     $tabelnompostfikso, ili estas kunmetitaj kun la
 *     abstrakta tabelnomo por ricevi la konkretan
 *     tabelnomon.
 * - (4) Se neniu el tiuj eblecoj taŭgas, la konkreta
 *     tabelnomo estos la abstrakta tabelnomo.
 *
 * @global array $tabelnomtradukilo
 * @global string $tabelnomprefikso
 * @global string $tabelnompostfikso
 * @uses tabelnomtradukilo (se ekzistas)
 *
 * @param string $tabelnomo la abstrakta tabelonomo
 * @return string la konkreta tabelnomo
 */
function traduku_tabelnomon($tabelnomo)
{
  // (1)
  if (function_exists("tabelnomtradukilo"))
	{
	  $traduko = tabelnomtradukilo($tabelnomo);
	  if (!empty($traduko))
		{
		  return $traduko;
		}
	}
  // (2)
  if (is_array($GLOBALS["tabelnomtradukilo"]))
	{
	  $traduko = $GLOBALS["tabelnomtradukilo"][$tabelnomo];
	  if (!empty($traduko))
		{
		  return $traduko;
		}
	}
  // (3) + (4)
  return $GLOBALS["tabelnomprefikso"] . $tabelnomo . $GLOBALS["tabelnompostfikso"];
}


/********************************************************************************
 * Ĝenerala funkcio por krei SELECT-SQL-ordonojn.
 *
 * Nur la unuaj du argumentoj estas necesaj, la aliajn
 * oni povas forlasi.
 *
 *
 * Voku ekzemple
 *
 * <code>
 *   datumbazdemando(array("p.ID", "pn.ID"),
 *                   array("partoprenanto" => "p", "partopreno" => "pn"),
 *                   array("p.ID = pn.partoprenantoID",
 *                         "alvenstato ='a'",
 *                         "partoprentipo ='t'"),
 *                   "renkontigxoID");
 * </code>
 * por ricevi ion simile al
 * <code>
 *    SELECT p.ID, pn.ID
 *    FROM partoprenantoj AS p, partoprenoj AS pn
 *    WHERE p.ID=pn.partoprenantoID
 *      AND alvenstato='a'
 *      AND partoprentipo='t'
 *      AND renkontigxoID='{$_SESSION["renkontigxo"]->datoj['ID']}'
 * </code>
 *
 *
 * @param string|array $kampoj
 *                   la kampoj en la rezulta tabelo. Tiu parametro estu
 *                   array(), kaj eblas du formoj de eroj:
 *                    -    "valoro"
 *                    -    "valoro" => "kaŝnomo"
 *                   Se vi donis kaŝnomon por la tabeloj,
 *                   uzu nur tiun en la valoro (ne la originalan
 *                   tabelnomon).
 *                   Kiam estas nur unu elemento (sen kaŝnomo), oni povas
 *                   ankaŭ uzi la nuran valoron mem (kiel ĉeno).
 *
 * @param string|array $tabelonomoj la tabelnomoj, el kiu ni prenu la datojn.
 *                    estu array() kun enhavo de la formo
 *                        "tabelonomo"
 *                        "tabelonomo" => "kaŝnomo"
 *                   Kiam estas nur unu elemento (sen kaŝnomo), oni povas
 *                   ankaŭ uzi la nuran tabelnomon mem (kiel string-o).
 *
 * @param sqlstring|array $restriktoj la kondiĉoj por la serĉo. Se estas array,
 *                    la elementoj estu ĉenoj kaj ili estos
 *                    kunigitaj per <b>and</b>.
 *                     Alikaze estu ĉeno kun SQL-kondiĉo.
 *                    Se vi donis kaŝnomon por la tabeloj,
 *                    uzu nur tiun en la valoro (ne la originalan
 *                    tabelnomon).
 *                  - Se vi donis nenion (aŭ malplenan string-on),
 *                    la funkcio uzas "1".
 *
 * @param string|array $id_laux_sesio aldona restrikto laŭ ID de
 *                                    objekto en sesio-variablo.
 *                 - se vi donas array(), ĝi konsistu
 *                    el elementoj de la formo
 *                     -  "variablo" => "sql_esprimo"
 *                             signifas, ke aldoniĝas
 *                             "and sql_esprimo = '".$_SESSION["variablo"]->datoj["ID"]."'"
 *                              al la restrikto.
 *                     - "variablo"       (mallongigo por
 *                                           "variablo" => "variablo".)
 *                  - se estas (nemalplena) ĉeno , ĝi funkcias kiel
 *                      array("renkontigxo" => $id_laux_sesio)
 *                  
 * @param array $aliaj_ordonoj 
 *               oni povas doni (en array) pliajn ordonojn, ekzemple
 *                - ordigo  ("order")
 *                - grupado ("group")
 *                - limigo ("limit")
 * @todo Mankas plia dokumentado pri $aliaj_ordonoj.
 * @todo Ni trovu manieron ebligi LEFT|RIGHT OUTER JOIN.
 * @return sqlstring SQL-SELECT-ordonon por demandi la datumbazon.
 */
function datumbazdemando($kampoj, $tabelnomoj, $restriktoj = "",
						 $id_laux_sesio = "", $aliaj_ordonoj = "")
{
  if (is_array($kampoj))
	{
	  foreach($kampoj as $nomo => $alias)
		{
		  if (is_string($nomo))
			{
			  $kampotekstoj[] = $nomo . " AS " . $alias;
			}
		  else
			{
			  $kampotekstoj[] = $alias;
			}
		}
	  $kampokodo = implode(", ", $kampotekstoj);
	}
  else if (is_string($kampoj))
	{
	  $kampokodo = $kampoj;
	}
  else
	{
        darf_nicht_sein("kampoj: " . var_export($kampoj, true));
	}

  if(is_array($tabelnomoj))
	{
	  foreach($tabelnomoj as $nomo => $alias)
		{
		  if (is_string($nomo))
			{
			  $tabeltekstoj[] = kreu_tabelan_as_esprimon($alias, $nomo);
			}
		  else
			{
                $tabeltekstoj[] = kreu_tabelan_as_esprimon($alias, $alias);
			}
		}
	  $tabelkodo = implode(", ", $tabeltekstoj);
	}
  else if (is_string($tabelnomoj))
	{
        // nur unu tabelo
        $tabelkodo = kreu_tabelan_as_esprimon($tabelnomoj, $tabelnomoj);
	}
  else
	{
        darf_nicht_sein('tabelnomoj: ' . var_export($tabelnomoj, true));
	}

  if (empty($restriktoj)) {
      $restriktoj = array("1");
  }

  $restriktokodo = donu_where_kondicxon($restriktoj, $id_laux_sesio);

//   if (empty($restriktoj))
// 	{
// 	  $restriktokodo = "1";
// 	}
//   else if (is_array($restriktoj))
// 	{
// 	  $restriktokodo = implode(" )\n   AND ( ", $restriktoj);
// 	}
//   else if (is_string($restriktoj))
// 	{
// 	  $restriktokodo = $restriktoj;
// 	}
//   else
// 	{
//         darf_nicht_sein('restriktoj: ' . var_export($restriktoj, true));
// 	}

  $rezulto =
	"SELECT " . $kampokodo . " " .
	"\n FROM " . $tabelkodo . " " .
	"\n WHERE " . $restriktokodo . " "
	;

//   if ($id_laux_sesio)
// 	{
// 	  if (is_string($id_laux_sesio))
// 		{
// 		  $rezulto .=
// 			"\n   AND ($id_laux_sesio = '" . $_SESSION["renkontigxo"]->datoj["ID"] . "') " ;
// 		}
// 	  else if (is_array($id_laux_sesio))
// 		{
// 		  foreach ($id_laux_sesio as $variablo => $sql_esprimo)
// 			{
// 			  if (is_int($variablo))
// 				{
// 				  $variablo = $sql_esprimo;
// 				}
// 			  $rezulto .= 
// 				"\n   AND ( " . $sql_esprimo . " = '" .
//                   $_SESSION[$variablo]->datoj["ID"] . "' )" ;
// 			}
// 		}
// 	  else
// 		{
//             darf_nicht_sein("id_laux_sesio: " .
//                             var_export($id_laux_sesio, true));
// 		}
// 	}
  if (is_array($aliaj_ordonoj))
	{
	  if(isset($aliaj_ordonoj["group"]))
		{
		  $rezulto .= "\n GROUP BY " . $aliaj_ordonoj["group"] ;
		}
	  if(isset($aliaj_ordonoj["order"]))
		{
		  $rezulto .= "\n ORDER BY " . $aliaj_ordonoj["order"] ;
		}
	  if(isset($aliaj_ordonoj["limit"]))
		{
		  $rezulto .= "\n LIMIT " . $aliaj_ordonoj["limit"] ;
		}
	}	
  if(DEBUG)
	{
	  echo "<!-- datumbazdemando: $rezulto \n-->";
	}
  return $rezulto;
}   // datumbazdemando(...)


/**
 * Aldonas ion en la datumbazon.
 *
 * @param string $tabelnomo la abstrakta tabelnomo.
 * @param array  $kion  estu array de la formo
 *                   kampo => valoro
 *  kie kampo estas la nomo de la kampo, valoro aŭ ĉeno
 *  (aŭ ĉen-konvertebla valoro), kio iĝos SQL-ĉeno, aŭ null
 *   (kiu iĝos SQL-NULL)
 * @uses datumbazaldono()
 *
 */
function aldonu_al_datumbazo($tabelnomo, $kion)
{
  if (! EBLAS_SKRIBI)
      erareldono(" 'Datenbank darf nicht ge&auml;ndert werden' ");

  $sql = datumbazaldono($tabelnomo, $kion);

  return sql_faru($sql);
}


/**
 * Kreas SQL-ordonon por aldoni ion al la datumbazo.
 *
 * @param string $tabelnomo la abstrakta tabelnomo.
 * @param array  $kion  estu array de la formo
 *                   kampo => valoro
 *  kie kampo estas la nomo de la kampo, valoro aŭ ĉeno
 *  (aŭ ĉen-konvertebla valoro), kio iĝos SQL-ĉeno, aŭ null
 *   (kiu iĝos SQL-NULL)
 * @return sqlstring la SQL-ordono kreita.
 */

function datumbazaldono($tabelnomo, $kion) {
  if (! EBLAS_SKRIBI)
	return " SELECT 'Datenbank darf nicht ge&auml;ndert werden' ";

  $sql =
      "INSERT INTO `" . traduku_tabelnomon($tabelnomo) . "`" .
      "\n   (`" . implode( "`, `", array_keys($kion)) . "`)".
      "\n   VALUES  (" .
      implode( ", ", array_map('sql_quote', array_values($kion))) . ") ;\n";

  if(DEBUG)
	{
	  echo "<!-- datumbazaldono: $sql -->";
	}
  
  return $sql;
}


/**
 * konvertas PHP-objekton al SQL-esprimo.
 * @param mixed $objekto iu ajn PHP-valoro, aŭ NULL.
 * @return sqlstring la objekto kiel SQL-NULL, aŭ SQL-ĉeno.
 * @todo ĉu necesas trakti speciale signojn kiel "'"?
 */
function sql_quote($objekto) {
    if ($objekto === null) {
        return "NULL";
    }
    else {
        return "'$objekto'";
    }
}


/**
 * Rekalkulas aĝojn de partoprenantoj.
 * 
 * Rekalkulas ĉiujn aĝojn (aŭ nur la aĝojn de la
 * partopreno kun $id) de la partoprenantoj je la
 * komencoj de la renkontiĝoj.
 * Tiu funkcio demandas kaj eble ŝanĝas la datumbazon,
 * sed ne redonas ion.
 *
 * @param string|int $id
 *      - "renkontigxo" por rekalkuli ĉiujn partoprenojn
 *          en $_SESSION['renkontigxo']
 *      - "partoprenanto" por rekalkuli ĉiujn partoprenojn
 *          de $_SESSION['partoprenanto']
 *      - int-numero, por rekalkuli nur partoprenon kun tiu ID.
 *      - "", por rekalkuli ĉiujn partoprenojn (defaŭlto)
 */
function rekalkulu_agxojn($id = "")
{
  $where = array("p.partoprenantoid = pn.ID",
				 "p.renkontigxoid = r.ID");

  if(intval($id) != 0)
	{
	  $where[] = "p.ID = '$id'";
	}
  else if ($id != "")
	{
	  $where[] = "p.{$id}id = '{$_SESSION[$id]->datoj['ID']}'";
	}

  $sql = datumbazdemando(array("p.ID" => "ID",
							   "FLOOR((TO_DAYS(r.de)-TO_DAYS(pn.naskigxdato))/365.25)" =>
							   "nova_agxo",
							   "p.agxo"),
						 array("partoprenoj" => "p",
							   "partoprenantoj" => "pn",
							   "renkontigxo" => "r"),
						 $where
						 );
  $rez = sql_faru($sql);
  while($linio = mysql_fetch_assoc($rez))
	{
	  if ($linio['nova_agxo'] != $linio['agxo'])
		sxangxu_datumbazon("partoprenoj",
						   array("agxo" => $linio['nova_agxo']),
						   array("ID" => $linio['ID'])
						   );
	  if (DEBUG)
		{
		  echo "<!-- sxangxis #{$linio['ID']} de {$linio['agxo']} al {$linio['nova_agxo']} -->";
		}
	}
}


/**
 * ŝanĝas linio(j)n en la datumbazo.
 *
 * @param string $tabelnomo la (abstrakta) nomo de la tabelo
 * @param array $valoroj   - array de la formo
 *                  array( kampo => valoro, kampo => valoro, ...)
 *                <em>kampo</em> estu valida kamponomo de la tabelo,
 *                <em>valoro<em> estu PHP-ĉeno (aŭ io konvertebla al tio) 
 *                aŭ PHP-null (vidu {@link sql_quote()}.
 *               La funkcio ŝanĝas la donitajn kampojn al
 *               la donitaj valoroj, respektive.
 * @param array|string $restriktoj_normalaj Restrikto, kiujn kampojn ŝanĝi.
 *             - array en la formo
 *                     kampo => valoro
 *                <em>kampo</em> estu valida kamponomo de la tabelo,
 *                "valoro" estu iu ajn php-valoro, kies
 *                   string-versio (+ '...') estu taŭga kiel SQL-valoro.
 *				 La funkcio ŝanĝas nur tiujn liniojn, kiuj
 *               enhavas en la donita kampo la donitan valoron.
 *
 *             -  Kiam oni donas ne array(), sed nur unu valoron,
 *               tio estas ekvivalenta al: <code>array('ID' => valoro)</code>
 * @param $restriktoj_sesio - array en la formo
 *                  array( kampo => variablo, kampo => variablo, ...)
 *                "kampo" estu valida kamponomo de la tabelo,
 *                "variablo" estu nomo de sesio-variablo, kies
 *                  identifikilon (->datoj["ID"]) ni uzas.
 *				 La funkcio ŝanĝas nur tiujn liniojn, kiuj
 *               enhavas en la donita kampo la identifikilon.
 *                Kiam oni skribas nur "kampo", tio estas identa
 *                al "kampo" => "kampo".
 * @todo La parametro $restriktoj_sesio nun (revizo 171) tute ne estas
 *             uzata ... estas pripensinda ŝanĝi ĝin laŭ la modelo
 *             de la respektiva parametro de  {@link datumbazdemando()}.
 *  
 */
function sxangxu_datumbazon($tabelnomo, $valoroj,
							$restriktoj_normalaj="",
							$restriktoj_sesio = "")
{
  if (EBLAS_SKRIBI)
	{
	  $sql = datumbazsxangxo($tabelnomo, $valoroj,
                             $restriktoj_normalaj, $restriktoj_sesio);
	  return sql_faru($sql);
	}
  erareldono ("La datumbazo estas nun en nes^ang^ebla stato." .
          " Bonvolu reprovi poste.");
  return false;
}

/*****************************************************************
 * Tiu funkcio kreas la SQL por ŝanĝi la datumbazo.
 *
 * Pri la argumentoj vidu ĉe  {@link sxangxu_datumbazon()}.
 *
 */
function datumbazsxangxo($tabelnomo, $valoroj,
						$restriktoj_normalaj="",
						$restriktoj_sesio = "")
{
  if (! EBLAS_SKRIBI)
	return " SELECT 'Datenbank darf nicht ge&auml;ndert werden' ";

  $sqlval = array();
  foreach ($valoroj as $kampo => $valoro)
	{
        $sqlval[] = "$kampo  = " . sql_quote($valoro);
	}

  $sql =
      "UPDATE " . traduku_tabelnomon($tabelnomo) .
      "\n   SET " . implode(", ", $sqlval) .
      "\n WHERE " . donu_where_kondicxon($restriktoj_normalaj,
                                         $restriktoj_sesio);

  if(DEBUG)
	{
	  echo "<!-- datumbazsxangxo: $sql -->";
	}
  return $sql;
} // datumbazsxangxo(...)


/**
 * konvertas kondicxon el array-formo al SQL.
 *
 * @param array|string $restriktoj_normalaj Restrikto, kiujn liniojn
 *                          sxangxi/redoni/forigi.
 *             - Estu array en la formoj:
 *                     kampo => valoro
 *                     sql_esprimo
 *                <em>kampo</em> estu valida kamponomo de la tabelo,
 *                "valoro" estu iu ajn php-valoro, kies
 *                   string-versio (+ '...') estu taŭga kiel SQL-valoro.
 *				 La sql-demando tiam traktas nur tiujn liniojn, kiuj
 *               enhavas en la donita kampo la donitan valoron.
 *               Altenative eblas doni tutan esprimon (t.e. sen "=>"),
 *               tiam tiu estos uzata kiel restrikto.
 *             -  Kiam oni donas ne array(), sed nur unu int-valoron,
 *               tio estas ekvivalenta al array('ID' => valoro).
 *             - se la unu valoro ne estas numera, ni traktas gxin kiel
 *               sql-esprimon.
 * @param string|array $restriktoj_sesio aldona restrikto laŭ ID de
 *                                    objekto en sesio-variablo.
 *              - se vi donas array(), ĝi konsistu el elementoj de la formo
 *                 -  "variablo" => "sql_esprimo"
 *                         signifas, ke
 *                                $_SESSION["variablo"]->datoj['ID']
 *                            estu identa kun la valoro de la SQL-esprimo.
 *                 - "variablo"  (mallongigo por "variablo" => "variablo".)
 *              - se estas (nemalplena) ĉeno , ĝi funkcias kiel
 *                      array("renkontigxo" => $id_laux_sesio)
 * @return sqlstring
 */
function donu_where_kondicxon($restriktoj_normalaj, $restriktoj_sesio)
 {
  $sqlres = array();
  if (is_array($restriktoj_normalaj))
	{
	  foreach($restriktoj_normalaj as $kampo => $valoro)
		{
            if (is_int($kampo)) {
                // $kampo mem estas iu SQL-kondicxo
                $sqlres[] = '( ' . $valoro . ' )';
            }
            else
                $sqlres[] = "$kampo = '$valoro'";
		}
	}
  else if (is_numeric($restriktoj_normalaj))  {
      $sqlres []= "ID = '" . (int) $restriktoj_normalaj . "'";
  }
  else {
      $sqlres []= '(' . $restriktoj_normalaj . ')';
  }

  if (is_string($restriktoj_sesio) and $restriktoj_sesio != "")
	{
	  $restriktoj_sesio =
          array('renkontigxo' => $restriktoj_sesio);
	}
  if (is_array($restriktoj_sesio))
	{
	  foreach($restriktoj_sesio as $variablo => $kampo)
          {
		  if (is_int($variablo))
			{
			  $variablo = $kampo;
			}
		  $sqlres[] =
              "( " . $kampo . " = '" . $_SESSION[$variablo]->datoj["ID"] . "' )";
		}
	}
  if (count($sqlres) == 0)
	{
	  darf_nicht_sein();
	}
  return implode("\n   AND ", $sqlres);
}


/**
 * Forigas linion el datumbaztabelo.
 *
 * @param string $tabelnomo    la (abstrakta) nomo de la tabelo
 * @param string $session_nomo la nomo de la session-variablo,
 *                 kies identigilo estas la identigilo
 *                 de la forigenda linio.
 */
function forigu_laux_sesio($tabelnomo, $session_nomo)
{
  forigu_el_datumbazo($tabelnomo, $_SESSION[$session_nomo]->datoj["ID"]);
}

/**
 * Forigas linion el datumbaztabelo.
 *
 * @param string $tabelnomo la (abstrakta) nomo de la tabelo
 * @param string|int|array $id    la identigilo de la forigenda linio
 *                               se array, tiam kondicxolisto de la formo
 *                                    kamponomo => valoro
 *                              (uzebla por tabeloj, kiuj ne havas
 *                               ID-atributon.)
 * @param array $restriktoj_sesiaj ...
 */
function forigu_el_datumbazo($tabelnomo, $id, $restriktoj_sesiaj="")
{
    if (! EBLAS_SKRIBI) {
        erareldono("Datenbank darf nicht geändert werden");
        exit();
    }
    $sql = datumbazforigo($tabelnomo, $id, $restriktoj_sesiaj);
    
    return sql_faru($sql);
}

/**
 * donas SQL-esprimon por forigo el datumbazo.
 *
 * 
 * @param array|string $restriktoj_normalaj Restrikto, kiujn kampojn ŝanĝi.
 *             - array en la formo
 *                     kampo => valoro
 *                <em>kampo</em> estu valida kamponomo de la tabelo,
 *                "valoro" estu iu ajn php-valoro, kies
 *                   string-versio (+ '...') estu taŭga kiel SQL-valoro.
 *				 La funkcio ŝanĝas nur tiujn liniojn, kiuj
 *               enhavas en la donita kampo la donitan valoron.
 *
 *             -  Kiam oni donas ne array(), sed nur unu valoron,
 *               tio estas ekvivalenta al array('ID' => valoro).
 * @param array $restriktoj_sesio - ...
 * @return sqlstring
 */
function datumbazforigo($tabelnomo, $restriktoj_normalaj,
                        $restriktoj_sesiaj ="") {
    if (! EBLAS_SKRIBI) {
        return "SELECT 'ne eblas sxangxi la datumbazon'";
    }

    $sql = "DELETE FROM " . traduku_tabelnomon($tabelnomo) .
        " WHERE " . donu_where_kondicxon($restiktoj_normalaj,
                                         $restriktoj_sesiaj);
  if(DEBUG)
	{
	  echo "<!-- forigo-ordono: $sql -->";
	}
    
    return $sql;
    
}


/**
 * Misuzas la datumbazon kiel kalkulilo.
 *
 * @param sqlstring $kion la kalkulendaĵo kiel SQL-esprimo.
 *     Ĝi ne povas uzi enhavon de iu tabelo,
 *     nur kalkuli konstantojn.
 */
function kalkulu_per_datumbazo($kion)
{
  $rez = mysql_fetch_assoc(sql_faru("SELECT (" . $kion . ") AS rezulto" ));
  return $rez["rezulto"];
}


/**
 * Eltrovas ion el datumbaztabelo laŭ identifikilo.
 * @param string|array $kion la kamponomo (string-o) aŭ pluraj kamponomoj
 *           en array.
 * @param string $kie  la tabelnomo
 * @param int $id la identigilo
 * @return string|array se $kion estis array el pluraj elementoj,
 *                         ankaŭ redonas array-on, alikaze
 *                         nur la valoron de tiu unu kampo.
 */
function eltrovu_laux_id($kion, $kie, $id)
{
  $sql = datumbazdemando( $kion, $kie, "ID = '$id'");
  $result = sql_faru($sql);
  $row = mysql_fetch_assoc($result);

  if (is_array($kion)) {
      if (count($kion) > 0) {
          return $row;
      }
      else {
          return $row[reset($kion)];
      }
  } else {
      return $row[$kion];
  }
}

/**
 * Eltrovas unu valoron el iu datumbaztabelo.
 *
 * Tiu funkcio estas kombino de {@link datumbazdemando()}
 * (por speciala kazo),
 * {@link sql_faru()}, {@link mysql_fetch_assoc() mysql_fetch_assoc()} kaj
 *  simpla array-aliro.
 *
 * @param string $kampo nomo de iu kampo, aŭ pli ĝenerale SQL-esprimo
 *               por precize unu valoro.
 * @param string|array $tabelnomoj nomo de unu tabelo, aŭ nomoj de pluraj
 *                     tabeloj en array (vidu {@link datumbazdemando()})
 * @param string|array $restriktoj
 *                     SQL-restriktoj (vidu {@link datumbazdemando()})
 * @param string|array $id_laux_sesio nomo(j) de sesio-variablo(j)
 *                      (vidu {@link datumbazdemando()})
 * @param array $aliaj_ordonoj aldonaj konfiguraj opcioj
 *                   (vidu {@link datumbazdemando()})
 * @return mixed la valoro de tiu unu SQL-esprimo.
 */
function eltrovu_gxenerale($kampo, $tabelnomoj, $restriktoj="",
                           $id_laux_sesio="", $aliaj_ordonoj="") {
    $sql = datumbazdemando(array($kampo => "valoro"),
                           $tabelnomoj, $restriktoj, $id_laux_sesio);
    $rezulto = sql_faru($sql);
    $row = mysql_fetch_assoc($rezulto);
    return $row['valoro'];
}



 /**
  * kontrolas, ĉu estis eraro en la lasta SQL-agado.
  * Se jes, montras tiun eraron kaj finas per {@link darf_nicht_sein()}.
  */
function sql_eraro($sql='')
{
  $eraro = mysql_error();
  if ($eraro)
  {
      eoecho( "<p> Iu eraro okazis c^e la SQL-esprimo");
      echo "<code>$sql</code>\n";
      echo "<br/> Estis: <code>".mysql_error() . "</code></p>\n";
      darf_nicht_sein();
  }
}


 /**
  * Ekzekutas SQL-esprimon.
  *
  * Poste ni kontrolas, ĉu {@link sql_eraro() okazis eraro}, kaj redonas
  * la SQL-rezult-objekton.
  *
  * @param sqlstring $sql la SQL-esprimo, kreita de {@link datumbazdemando()}
  *                       aŭ simile.
  */
function sql_faru($sql)
{
  $result = mysql_query($sql);
  sql_eraro($sql);
  return $result;
}


/**
 * Aldonas "AND"-"LIKE"-frazon al SQL-esprimo.
 *
 * @param sqlstring $sql la origina SQL-esprimo, kaj ankaŭ la nova (estos
 *                    ŝanĝita)
 * @param sqlstring $io  iu SQL-esprimo, por kompari.
 * @param string $ajn serĉesprimo kun _ (unu litero) kaj %
 *               (iom ajn da literoj), por {@link http://dev.mysql.com/doc/refman/5.0/en/string-comparison-functions.html#operator_like LIKE}.
 */
function sql_kaju(&$sql,$io,$ajn)
{
  if ($ajn)
     $sql .= " AND $io LIKE '$ajn' ";
}


/**
 * redonas la nomon de entajpanto laŭ ĝia identigilo.
 */
function eltrovu_entajpanton($id)
{
  return eltrovu_laux_id("nomo", "entajpantoj", $id);
}


/**
 * redonas la nomon de iu lando, laŭ ID.
 */
function eltrovu_landon($id)
{
  return eltrovu_laux_id("nomo", "landoj", $id);
}

/**
 * redonas la lokalingvan nomon de iu lando, laŭ ID.
 */
function eltrovu_landon_lokalingve($id)
{
  return eltrovu_laux_id("lokanomo", "landoj", $id);
}


/*
 * Eltrovas la nomon de iu renkontiĝo
 * laŭ la identifikilo
 */
function eltrovu_renkontigxon($id)
{
  return eltrovu_laux_id("nomo", "renkontigxo", $id);
}



/*
 * Kalkulado de datoj 
 */


 /**
  * kalkulas la aĝon je la limdato
  *
  * @param string $nask  naskiĝdato de iu persono, en ISO-formato
  *                    (jaro-monato-tago).
  * @param string $kompardato la limdato (ekzemple komenco de la
  *                                renkontiĝo).
  *                Se forlasita, uzas la hodiaŭan daton.
  * @return string la aĝo en jaroj (decimala)
  */

function kalkulu_agxon($nask,$kompardato="")
{
  // misuzo de la datumbazo kiel kalkulilo :-)
  
  if ($kompardato)
  {
	return kalkulu_per_datumbazo("FLOOR((TO_DAYS('$kompardato') - TO_DAYS('$nask'))/365.25)");
  }
  else
  {
	return kalkulu_per_datumbazo("FLOOR((TO_DAYS(CURRENT_DATE()) - TO_DAYS('$nask'))/365.25)");
  }
}

/**
 * kalkulas la tagodiferencon inter du datoj.
 */

function kalkulu_tagojn($de,$gxis)
{
  // TODO: eble kalkulu rekte en PHP, ne per la datumbazo
  return kalkulu_per_datumbazo("TO_DAYS('$gxis')-TO_DAYS('$de')");
}

/**
 * disigas daton en siajn komponentojn.
 *
 * @param string $io la dato en formato jaro-monato-tago.
 * @return array de la formo 'jaro' =>  ..., 'monato' => ..., 'tago' => ...).
 */
function JMTdisigo($io)
{
    return array_combine(array("jaro","monato","tago"),
                         explode("-",$io));
}

 /**
  * kalkulas daton unu aŭ kelkajn tagojn post alia dato.
  *
  * @param string $io la baza dato en 'Y-m-d'-formato.
  * @param int $n kiom da tagoj poste.
  *
  * @return string la nova dato en 'Y-m-d'-formato.
  */
function sekvandaton ($io,$n=1)
{
  list($jaro,$monato,$tago) = explode("-",$io);
  return date("Y-m-d", mktime(0, 0, 0, $monato, $tago+$n, $jaro));
}


 /**
  * kontrolas, ĉu enmetita dato estas valida.
  *
  * @param string $io dato en formato jaro-monato-tago
  * @return boolean true, se ĝi estas valida.
  * @uses checkdate()
  */
function kontrolu_daton($io)
{
  $ar = JMTdisigo($io);
  //checkdate uzas iom strangan sinsekvon de la dato-komponentoj.
  return checkdate($ar['monato'],
                   $ar['tago'],
                   $ar['jaro']) ;
}

/**
 * Protokolas la uzanton en la protokolo-tabelo, kun
 * nomo, komputilo, retumilo, tempo.
 *
 * @param string $ago se donita, ni ankaŭ protokolas, kion la uzanto
 *                    nun faris. Ekzemploj estas "aliĝo", "elsaluto",
 *                    "ensaluto malsukcesa", "ensaluto sukcesa".
 */
function protokolu($ago = "")
{

  $de = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
  $tempo =date("Y-m-d H:i:s");

  $entajpanto = $_SESSION['kodnomo']
      or $entajpanto = "(aligxilo)";

  aldonu_al_datumbazo("protokolo",
					  array("deveno" => $de,
                            "ilo" => $_SERVER["HTTP_USER_AGENT"],
							"entajpanto" => $entajpanto,
							"tempo" => $tempo,
                            'ago' => $ago));
}

/**
 * ekzekutas la donitan (SELECT-)SQL-esprimon, kaj montras
 * la rezulton en simpla HTML-tabelo.
 *
 * @param sqlstring $sql la SQL-serĉ-ordono.
 * @todo koloroj farendaj per CSS - aŭ entute anstataŭu la implementadon
 *       per voko de {@link sercxu()}
 */
function sql_farukajmontru($sql)
{
  $result = sql_faru($sql);
  echo "<table border=1>\n";

    // TODO: Dafür gibt's aber CSS Files.
    // Außerdem zeilenglobale Einstellungen zum <tr>.
  $k[0] = "<td align='right' bgcolor='#CCFFFF'>\n";
  $k[1] = "<td align='right' bgcolor='#CCFFCC'>\n";

  $kampoj = mysql_num_fields($result);
  while ($row = mysql_fetch_array($result, MYSQL_NUM))
  {
    echo "<tr> ";
    $e = $j % 2;
    $i = 0;
    while ($i < $kampoj)
    {
      eoecho($k[$e]." &nbsp;".$row[$i]."</td>");
      $i++;
    }
    $j++;
    echo "</tr>\n";
  }
  echo "<tr><th>nombro:</th><td> $j</td></tr>\n";
  echo "</table>\n";
}

?>