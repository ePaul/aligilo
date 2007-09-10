<?php

/* ######################## */
/* INTERAGO KUN LA DATUMARO */
/* ######################## */


/**
 * Cxi tiu funkcio ekzistas nur por
 * trovi cimojn en la programo.
 * Tiun cxi funkcion oni nur voku je lokoj
 * en la programo, kiujn oni normale ne atingu.
 *
 * Gxi printas la vok-cxenon, kaj finas
 * la programon.
 */
function darf_nicht_sein($klarigo = "")
{
    eoecho("Tiu kazo ne rajtus okazi, vers^ajne estas eraro en la programo." .
           " Bonvolu informi ");
    ligu('mailto:'.teknika_administranto_retadreso, teknika_administranto);
    eoecho (" pri tio, kun kopio de la subaj datoj.");
    
    //  eoecho('Dieser Fall sollte nicht auftreten. Bitte sag <A href="mailto:'.teknika_administranto_retadreso.'">'.teknika_administranto.' </A>Bescheid');
    //  eoecho( ' (mit einer Kopie der untenstehenden Daten).');
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
 * Tradukas la abstraktan tabelnomon (eble kun aliasnomo)
 * al SQL-peco kun la konkreta tabelnomo kaj la aliaso aux
 * abstrakta tabelnomo kiel aliasnomo.
 *
 * kreu_as_esprimon($nomo)
 *           kreas sql-pecon de la formo
 *             <$nomo> AS $nomo,
 *           kie <$nomo> estas la konkreta 
 *           tabelnomo por $nomo.
 * kreu_as_esprimon($alias, $nomo)
 *           kreas sql-pecon de la formo
 *             <$nomo> AS $alias,
 *           kie <$nomo> estas la konkreta
 *           tabelnomo por $nomo.
 *
 * Por la konkreta tabelnomo oni uzas traduku_tabelnomon
 */
function kreu_as_esprimon($alias, $tabelnomo="")
{
  if ($tabelnomo == "")
	{
	  $tabelnomo = $alias;
	}
	if (strpos($alias, ' AS '))
	{
		return traduku_tabelnomon($alias);
	}

  return traduku_tabelnomon($tabelnomo) . " AS " . $alias;
}

/**
 * Tradukas la abstraktan tabelnomon al la konkreta tabelnomo.
 *
 * Ekzistas pluraj eblecoj, kiel traduki la nomon.
 * Dependas de la enhavo de konfiguro/datumaro.php.
 *
 * (1) Se tie trovigxas funkcio
 *      tabelnomtradukilo(...),
 *     gxi estas vokita per la abstrakta tabelnomo.
 *     Se la rezulto ne estas malplena (0, null, nedefinita),
 *     gxi estos la konkreta tabelnomo.
 * (2) Se ekzistas globala variablo $tabelnomtradukilo (kiu estu
 *     array), tiam ni sercxas la valoron de la tabelnomo
 *     (do, $tabelnomtradukilo[$tabelnomo]). Se la rezulto
 *     ne estas malplena, gxi estos la konkreta tabelnomo.
 * (3) Se ekzistas globalaj variabloj $tabelnomprefikso kaj/aux
 *     $tabelnompostfikso, ili estas kunmetitaj kun la
 *     abstrakta tabelnomo por ricevi la konkretan
 *     tabelnomon.
 * (4) Se neniu el tiuj eblecoj tauxgas, la konkreta
 *     tabelnomo estos la abstrakta tabelnomo.
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
 * Gxenerala funkcio por krei SELECT-SQL-ordonojn.
 * Nur la unuaj du argumentoj estas necesaj, la aliajn
 * oni povas forlasi.
 *
 *  $kampoj         - la kampoj en la rezulta tabelo. Ankaux
 *                     tiu estu array(), kaj eblas du formoj:
 *                        "valoro"
 *                        "valoro" => "kasxnomo"
 *                    Se vi donis kasxnomon por la tabeloj,
 *                    uzu nur tiun en la valoro (ne la originalan
 *                    tabelnomon).
 *                   Kiam estas nur unu elemento (sen kasxnomo), oni povas
 *                   ankaux uzi la nuran valoron mem (kiel string-o).
 *
 *  $tabelonomoj    - la tabelnomoj, el kiu ni prenu la datojn.
 *                    estu array() kun enhavo de la formo
 *                        "tabelonomo"
 *                        "tabelonomo" => "kasxnomo"
 *                   Kiam estas nur unu elemento (sen kasxnomo), oni povas
 *                   ankaux uzi la nuran tabelnomon mem (kiel string-o).
 *
 *  $restriktoj     - la kondicxoj por la sercxo. Se estas array,
 *                    la elementoj estu string-oj kaj ili estos
 *                    kunigitaj per "and". Alikaze estu string-o.
 *                    Se vi donis kasxnomon por la tabeloj,
 *                    uzu nur tiun en la valoro (ne la originalan
 *                    tabelnomon).
 *                  - Se vi donis nenion (aux malplenan string-on),
 *                    la funkcio uzas "1".
 *
 *  $id_laux_sesio  - se vi donas array(), gxi konsistu el elementoj de la formo
 *                    (a)
 *                       "variablo" => "sql_esprimo"
 *                    (b)
 *                       "variablo"
 *                       (mallongigo por "variablo" => "variablo".)
 *
 *                    (a) signifas, ke aldonigxas
 *                        "and sql_esprimo = '".$_SESSION["variablo"]->datoj["ID"]."'"
 *                    al la restrikto (.
 *                  - se estas (nemalplena) string-o , gxi funkcias kiel
 *                      array("renkontigxo" => $id_laux_sesio)
 *                  
 *  $aliaj_ordonoj  - oni povas doni (en array) pliajn ordonojn,
 *                    ekzemple
 *                     * ordigo  ("order")
 *                     * grupado ("group")
 *                     * limigo ("limit")
 *
 *
 * Voku ekzemple
 *
 *   datumbazdemando(array("p.ID", "pn.ID"),
 *                   array("partoprenanto" => "p", "partopreno" => "pn"),
 *                   array("p.ID = pn.partoprenantoID",
 *                         "alvenstato ='a'",
 *                         "partoprentipo ='t'"),
 *                   "renkontigxoID");
 * por ricevi ion simile al
 * "select p.ID, pn.ID
 *   from partoprenantoj as p, partoprenoj as pn
 *   where p.ID=pn.partoprenantoID  and alvenstato='a' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and partoprentipo='t'"
 *
 * -------------
 *  Kiam mi aldonos prefikson por la datumbaz-tabeloj,
 *  tiu cxi funkcio auxtomate uzos gxin.
 * 
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
	  // TODO: Fehlermeldung/Standardwert?
	  darf_nicht_sein();
	}

  if(is_array($tabelnomoj))
	{
	  foreach($tabelnomoj as $nomo => $alias)
		{
		  if (is_string($nomo))
			{
			  // TODO: prefikso
			  $tabeltekstoj[] = kreu_as_esprimon($alias, $nomo);
			}
		  else
			{
			  // TODO: prefikso
			  $tabeltekstoj[] = kreu_as_esprimon($alias);
			}
		}
	  $tabelkodo = implode(", ", $tabeltekstoj);
	}
  else if (is_string($tabelnomoj))
	{
	  $tabelkodo = kreu_as_esprimon($tabelnomoj);
	}
  else
	{
	  // TODO: Fehlermeldung/Standardwert?
	  darf_nicht_sein();
	}

  if (empty($restriktoj))
	{
	  $restriktokodo = "1";
	}
  else if (is_array($restriktoj))
	{
	  $restriktokodo = implode(" ) and ( ", $restriktoj);
	}
  else if (is_string($restriktoj))
	{
	  $restriktokodo = $restriktoj;
	}
  else
	{
	  // TODO: Fehlerausgabe oder Standardwert nehmen?
	  darf_nicht_sein();
	}

  $rezulto =
	"SELECT " . $kampokodo . " " .
	" FROM " . $tabelkodo . " " .
	" WHERE (" . $restriktokodo . ") "
	;

  if ($id_laux_sesio)
	{
	  if (is_string($id_laux_sesio))
		{
		  $rezulto .=
			" and ($id_laux_sesio = '" . $_SESSION["renkontigxo"]->datoj["ID"] . "') " ;
		}
	  else if (is_array($id_laux_sesio))
		{
		  foreach ($id_laux_sesio as $variablo => $sql_esprimo)
			{
			  if (is_int($variablo))
				{
				  $variablo = $sql_esprimo;
				}
			  $rezulto .= 
				" and ( " . $sql_esprimo . " = '" . $_SESSION[$variablo]->datoj["ID"] . "' )" ;
			}
		}
	  else
		{
		  // TODO: Fehlerausgabe oder Standardwert nehmen?
		  darf_nicht_sein();
		}
	}
  if (is_array($aliaj_ordonoj))
	{
	  if(isset($aliaj_ordonoj["group"]))
		{
		  $rezulto .= " GROUP BY " . $aliaj_ordonoj["group"] ;
		}
	  if(isset($aliaj_ordonoj["order"]))
		{
		  $rezulto .= " ORDER BY " . $aliaj_ordonoj["order"] ;
		}
	  if(isset($aliaj_ordonoj["limit"]))
		{
		  $rezulto .= " LIMIT " . $aliaj_ordonoj["limit"] ;
		}
	}	
  if(DEBUG)
	{
	  echo "<!-- datumbazdemando: $rezulto -->";
	}
  return $rezulto;
}   // datumbazdemando(...)


/***********************************************************************
 * Aldonas ion en la datumbazon.
 *
 *  $tabelnomo - la (abstrakta) tabelnomo
 *
 *  $kion - estu array de la formo
 *
 *         array(kampo => valoro, kampo => valoro, ...)
 *
 *  kie kampo estas la nomo de la kampo.
 *
 */
function aldonu_al_datumbazo($tabelnomo, $kion)
{
  if (! EBLAS_SKRIBI)
	return " SELECT 'Datenbank darf nicht ge&auml;ndert werden' ";

  $sql = "INSERT INTO " . traduku_tabelnomon($tabelnomo) .
	" (" . implode( ",", array_keys($kion)) . ") VALUES" .
	" ('" . implode( "', '", array_values($kion)) . "')";
  if(DEBUG)
	{
	  echo "<!-- datumbazaldono: $sql -->";
	}
  return sql_faru($sql);
}

// /**
//  * rekalkulas la agxon de iu partoprenanto (aux de cxiuj partoprenantoj,
//  * se vi ne donis la identifikilon). Gxi uzas kiel referencdaton
//  * la komencan daton de la aktuala renkontigxo.
//  *
//  */
// function sxangxu_agxon($id="")
// {
//   $sql = "UPDATE " . traduku_tabelnomon("partoprenantoj") . " SET agxo = FLOOR((TO_DAYS('".$_SESSION["renkontigxo"]->datoj["de"]."')-TO_DAYS(naskigxdato))/365.25)";
//   if ($id != "")
// 	{
// 	  $sql .= " WHERE ID = '$id'";
// 	}
//   sql_faru($sql);
// }

/**
 * Rekalkulas cxiujn agxojn (aux nur la agxojn de la
 * partopreno kun $id) de la partoprenantoj je la
 * komencoj de la renkontigxoj.
 *
 *  $id - "renkontigxo" por rekalkuli cxiujn partoprenojn
 *          en $_SESSION['renkontigxo']
 *      - "partoprenanto" por rekalkuli cxiujn partoprenojn
 *          de $_SESSION['partoprenanto']
 *      - int-numero, por rekalkuli nur partoprenon kun tiu ID.
 *      - "", por rekalkuli cxiujn partoprenojn (defauxlto)
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
 * sxangxas linio(j)n en la datumbazo.
 *
 *  $tabelnomo - la (abstrakta) nomo de la tabelo
 *  $valoroj   - array de la formo
 *                  array( kampo => valoro, kampo => valoro, ...)
 *                "kampo" estu valida kamponomo de la tabelo,
 *                "valoro" estu iu ajn sql-esprimo.
 *               La funkcio sxangxas la donitajn kampojn al
 *               la donitaj valoroj.
 *  $restriktoj_normalaj - array en la formo
 *                  array( kampo => valoro, kampo => valoro, ...)
 *                "kampo" estu valida kamponomo de la tabelo,
 *                "valoro" estu iu ajn php-valoro, kies
 *                   string-versio estu tauxga kiel SQL-valoro (sen '').
 *				 La funkcio sxangxas nur tiujn liniojn, kiuj
 *               enhavas en la donita kampo la donitan valoron.
 *  $restriktoj_sesio - array en la formo
 *                  array( kampo => variablo, kampo => variablo, ...)
 *                "kampo" estu valida kamponomo de la tabelo,
 *                "variablo" estu nomo de sesio-variablo, kies
 *                  identifikilon (->datoj["ID"]) ni uzas.
 *				 La funkcio sxangxas nur tiujn liniojn, kiuj
 *               enhavas en la donita kampo la identifikilon.
 *                Kiam oni skribas nur "kampo", tio estas identa
 *                al "kampo" => "kampo".
 *  
 */
function sxangxu_datumbazon($tabelnomo, $valoroj,
							$restriktoj_normalaj="",
							$restriktoj_sesio = "")
{
  if (EBLAS_SKRIBI)
	{
	  $sql = datumbazsxangxo($tabelnomo, $valoroj, $restriktoj_normalaj, $restriktoj_sesio);
	  return sql_faru($sql);
	}
  erareldono ("La datumbazo estas nun en nes^ang^ebla stato." .
          " Bonvolu reprovi poste.");
  return false;
}

/*****************************************************************
 * Tiu funkcio kreas la SQL por sxangxi la datumbazo.
 *
 * Pri la argumento vidu cxe  sxangxu_datumbazon.
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
	  $sqlval[] = "$kampo  = '$valoro'";
	}
  

  $sqlres = array();
  if (is_array($restriktoj_normalaj))
	{
	  foreach($restriktoj_normalaj as $kampo => $valoro)
		{
		  $sqlres[] = "$kampo = '$valoro'";
		}
	}

  if (is_string($restriktoj_sesio) && $restriktoj_sesio != "")
	{
	  $restriktoj_sesio = array($restriktoj_sesio);
	}
  if (is_array($restriktoj_sesio))
	{
	  foreach($restriktoj_sesio as $kampo => $variablo)
		{
		  if (is_int($kampo))
			{
			  $kampo = $variablo;
			}
		  $sqlres[] = "$kampo = '" . $_SESSION[$variablo]->datoj["ID"] . "'";
		}
	}
  if (count($sqlres) == 0)
	{
	  darf_nicht_sein();
	}

  $sql = "UPDATE " . traduku_tabelnomon($tabelnomo) . " SET " . implode(", ", $sqlval) .
	" WHERE " . implode(" and ", $sqlres);

  if(DEBUG)
	{
	  echo "<!-- datumbazsxangxo: $sql -->";
	}
  return $sql;
} // datumbazsxangxo(...)

/***********************************************
 * Forigas linion el datumbaztabelo.
 *
 * $tabelnomo    - la (abstrakta) nomo de la tabelo
 * $session_nomo - la nomo de la session-variablo,
 *                 kies identifikilo estas la identifikilo
 *                 de la forigenda linio.
 */
function forigu_laux_sesio($tabelnomo, $session_nomo)
{
  forigu_el_datumbazo($tabelnomo, $_SESSION[$session_nomo]->datoj["ID"]);
}

/***********************************************
 * Forigas linion el datumbaztabelo.
 *
 * $tabelnomo  - la (abstrakta) nomo de la tabelo
 * $id         - la identifikilo de la forigenda linio
 */
function forigu_el_datumbazo($tabelnomo, $id)
{
  if (! EBLAS_SKRIBI)
	return " SELECT 'Datenbank darf nicht ge&auml;ndert werden' ";

  $sql = "DELETE FROM " . traduku_tabelnomon($tabelnomo) .
	" WHERE ID = '" . $id . "'";
  if(DEBUG)
	{
	  echo "<!-- forigo-ordono: $sql -->";
	}
  return sql_faru($sql);
}


/**
 * Jen misuzo de la datumbazo kiel kalkulilo :-)
 *
 * $kion - la kalkulendajxo kiel SQL-esprimo.
 *         Gxi ne povas uzi enhavon de iu tabelo,
 *         nur kalkuli konstantojn.
 */
function kalkulu_per_datumbazo($kion)
{
  $rez = mysql_fetch_assoc(sql_faru("SELECT (" . $kion . ") AS rezulto" ));
  return $rez["rezulto"];
}


/**
 * Eltrovas ion el datumbaztabelo laux identifikilo.
 *   $kion - la kamponomo (string-o) aux pluraj kamponomoj
 *           en array.
 *   $kie  - la tabelnomo
 *   $id   - la identifikilo.
 */
function eltrovu_laux_id($kion, $kie, $id)
{
  $sql = datumbazdemando( $kion, $kie, "id = '$id'");
  $result = sql_faru($sql);
  $row = mysql_fetch_assoc($result);
  return ($row[$kion]);
}




/**************************************************/


/* ################################# */
/* redonas la nomon de la entajpanto */
/* ################################# */

  // TODO: eble kunigu kun eltrovu_*;
  // TODO: wird später entfernt. ==> alle in ein Objekt.

function eltrovu_entajpanto($id)
{
  return eltrovu_laux_id("nomo", "entajpantoj", $id);
}

/* ################## */
/* montras la erarojn */
/* ################## */

function sql_eraro($sql='')
{
  $eraro = mysql_error();
  if ($eraro)
  {
    echo "<BR> Io eraro okazis cxe la sql esprimo <code>$sql</code>";
    echo "<BR> Estis: <code>".mysql_error() . "</code>";
    // TODO: Das machen wir bald via Automatik.!!!
    //eoecho ("<BR><BR>Bonvolu reporti g^in al nia <a href='mailto:".teknika_administranto_retadreso."'>administrado</a><BR><BR>");
	darf_nicht_sein();
    ligu("index.php","--> komenca pag^o","_top");
    exit;
  }
}
       // TODO: ==> eble kunigu gxin kun sql_faru;


/* ################################################### */
/* ekzekutas kaj montas la rezulton de iu sql esprimo  */
/* ################################################### */

function sql_faru($sql)
{
//	echo $sql;
  $result = mysql_query($sql);
  sql_eraro($sql);
//  var_export($result);
  return $result;
}


/**
 * Aldonas "AND"-"LIKE"-frazon al SQL-esprimo.
 */
function sql_kaju(&$sql,$io,$ajn)
{
  if ($ajn)
     $sql .= " AND $io LIKE '$ajn' ";
}

/* ############################ */
/* redonas la nomon de iu lando */
/* ############################ */

function eltrovu_landon($id)
{
  return eltrovu_laux_id("nomo", "landoj", $id);
}

function eltrovu_landon_lokalingve($id)
{
  return eltrovu_laux_id("lokanomo", "landoj", $id);
}


/* ################################# *
 * redonas la kategorion de iu lando
 * per la identifikilo
 * ################################# */

function eltrovu_landokategorion($id)
{
  return eltrovu_laux_id("kategorio", "landoj", $id);
}


/*
 * Eltrovas la nomon de iu renkontigxo
 * per la identifikilo
 */
function eltrovu_renkontigxo($id)
{
  return eltrovu_laux_id("nomo", "renkontigxo", $id);
}






/*
 * eltrovas, cxu la donita partporenanto
 * jam partoprenis en iu _alia_ renkontigxo ol
 * la donita.
 */
function jampartoprenis($partoprenanto,$renkontigxo)
{
    
//   "select renkontigxoID from partoprenoj where renkontigxoID != '"
//                       . $renkontigxo->datoj[ID]
//                       . "' and partoprenantoID = '"
//                       . $partoprenanto->datoj[ID]
//                       . "'"
// 

  // TODO: Eble pli bone estas SELECT COUNT(*) ?

  $sql = datumbazdemando("renkontigxoID",
						 "partoprenoj",
						 array("renkontigxoID != '" . $renkontigxo->datoj[ID] . "'",
							   "partoprenantoID = '" . $partoprenanto->datoj[ID] . "'" )
						 );

  $result = sql_faru($sql);
  $row = mysql_fetch_array($result, MYSQL_BOTH);

  //echo "row: ".$row[0];

  return ($row[0] != '');
}

/* #################### */
/* KALKULADO DE DATUMOJ */
/* #################### */

/* ###################################
 * kalkulas la agxon je la limdato
 *
 * $io - naskigxdato de iu persono
 * $kompardato - la limdato (ekzemple
 *   komenco de la renkontigxo).
 *   Se forlasita, uzas la hodiauxan
 *   daton.
 * ambaux datoj estu en SQL-dat-formato.
 * ##################################### */

function kalkulu_agxon($io,$kompardato="")
{
  // misuzo de la datumbazo kiel kalkulilo :-)
  
  if ($kompardato)
  {
	return kalkulu_per_datumbazo("FLOOR((TO_DAYS('$kompardato') - TO_DAYS('$io'))/365.25)");
  }
  else
  {
	return kalkulu_per_datumbazo("FLOOR((TO_DAYS(CURRENT_DATE()) - TO_DAYS('$io'))/365.25)");
  }
}

/* ############################################### */
/* kalkulas la tagodiferencon, datoj en SQL kutimo */
/* ############################################### */

function kalkulu_tagojn($de,$gxis)
{
  // TODO: eble kalkulu rekte en PHP, ne per la datumbazo
  return kalkulu_per_datumbazo("TO_DAYS('$gxis')-TO_DAYS('$de')");
}

/* #################################################### */
/* disigas la datumon el "tago-monato-jaro" al 3 partoj */
/* #################################################### */

function JMTdisigo($io)
{
  list($jaro,$monato,$tago)=split("\-",$io);
  return array("jaro"=>$jaro,"monato"=>$monato,"tago"=>$tago);
}

/* ############################################################# */
/* kalkulas kaj redonas la SEKVONTAN daton en "tago-monato-jaro" */
/* kaj respektas jarsxangxojn                                    */
/* ############################################################# */

function sekvandaton ($io,$n=1)
{
  list($jaro,$monato,$tago) = split("\-",$io);
  return date("Y-m-d", mktime(0, 0, 0, $monato, $tago+$n, $jaro));
}

/* #################################### */
/* kontrolas, cxu la dato estas korekta */
/* #################################### */

function kontrolu_daton($io)
{
/*  list($jaro,$monato,$tago) = */
  $ar = JMTdisigo($io);
  if ( ($ar[tago] == "")
        or ($ar[monato] == "")
        or ($ar[jaro] == "")
        )
  {
    return "";
  }
  return ( checkdate($ar[monato],$ar[tago],$ar[jaro]) ); //checkdate bezonas (M-T-Y), sed mi uzas Y-M-T
}

/**
 * Protokolas la uzanton en la protokolo-tabelo, kun
 * nomo, komputilo, retumilo, tempo.
 */
function protokolu($ago = "")
{
  //  global $HTTP_USER_AGENT;

  $de = gethostbyaddr($_SERVER["REMOTE_ADDR"]);
  $tempo =date("Y-m-d H:i:s");

  //    $sql = "insert into protokolo (deveno, ilo, entajpanto,kodvorto,tempo) VALUES ('$de','$HTTP_USER_AGENT','" . $_SESSION["kodnomo"] . "','" . $_SESSION["kodvorto"] . "','$tempo')";
  //    sql_faru($sql);

  aldonu_al_datumbazo("protokolo",
					  array("deveno" => $de,
                            "ilo" => $_SERVER["HTTP_USER_AGENT"],
							"entajpanto" => $_SESSION["kodnomo"],
							"tempo" => $tempo,
                            'ago' => $ago));
}

/**
 * ekzekutas la donitan SQL-esprimon, kaj montras
 * la rezulton en HTML-tabelo.
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
    $i = 0;
    while ($i < $kampoj)
    {
      $e = $j % 2;
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