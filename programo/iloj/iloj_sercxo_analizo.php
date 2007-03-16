<?php

/*
 *
 * Iloj, kiuj analizas la $valoroj-strukturo kaj kreas
 * pli tauxgajn strukturojn.
 */





/**
 * Kreas SQL-ordonon el la $valoroj-listo.
 */
function kreuSercxSQL($valoroj)
{
  $cxiujtabeloj = array("renkontigxo",
						"cxambroj",
						"litonoktoj",
						"partoprenoj",
						"partoprenantoj",
						"rabatoj",
						"pagoj",
						"notoj",
						"landoj");
  $uzatajtabeloj = array();
  foreach($cxiujtabeloj AS $tabelnomo)
	{
	  if($valoroj['sercxo_tabelo_'.$tabelnomo.'_uzu'] == 'JES')
		{
		  $uzatajtabeloj[]= $tabelnomo;
		}
	}
  $kondicxoj = kreuKondicxojn($uzatajtabeloj, $valoroj);
  list($kampoj, $inversa) = kreuKampoliston($uzatajtabeloj, $valoroj);

  if (empty($kampoj))
	return array("", "", "");
  certiguCxiujnKonektojn($uzatajtabeloj);
  $kondicxoj = array_merge($kondicxoj, kreuKonektKondicxojn($uzatajtabeloj));

  if (DEBUG)
	{
	  echo "<!--";
	  echo "\n kampoj: ";
	  var_export($kampoj);
	  echo "\n kondicxoj: ";
	  var_export($kondicxoj);
	  echo "\n uzatajtabeloj: ";
	  var_export($uzatajtabeloj);
	  echo "-->";
	}
  

  return array( $kampoj,
				$inversa,
				datumbazdemando( $kampoj,
								 $uzatajtabeloj,
								 $kondicxoj)
				);
}

function kreuKampoliston($uzatajtabeloj, $valoroj)
{
  $listo = array();
  $inversa = array();
  foreach($valoroj AS $varnomo => $montru)
	{
	  $rezultoj = array();
	  if($montru == 'JES' and
		 preg_match('/^sercxo_([^_]+)_(.+)_montru$/', $varnomo, $rezultoj))
		{
		  $tabelnomo = $rezultoj[1];
		  $kamponomo = $rezultoj[2];
		  if (DEBUG)
			{
			  echo "<!--  montras: " . $varnomo . "-->\n";
			}
		  if (in_array($tabelnomo, $uzatajtabeloj))
			{
			   // intence nur =.
			  ($alias = $valoroj["sercxo_{$tabelnomo}_{$kamponomo}_alias"])
				or ($alias = $kamponomo); // TODO: eventuell besser $tabelnomo.$kamponomo ?

			  $listo = array_merge($listo,
								   array($tabelnomo .'.'.$kamponomo => $alias));
			  $inversa[$alias] = array('kampo' => $tabelnomo .'.'.$kamponomo);
			  if($valoroj["sercxo_{$tabelnomo}_{$kamponomo}_ligo"])
				{
				  $inversa[$alias]['ligo'] =
					$valoroj["sercxo_{$tabelnomo}_{$kamponomo}_ligo"];  
				}
			  if($valoroj["sercxo_{$tabelnomo}_{$kamponomo}_titolo"])
				{
				  $inversa[$alias]['titolo'] =
					$valoroj["sercxo_{$tabelnomo}_{$kamponomo}_titolo"];  
				}
			  else
				{
				  $inversa[$alias]['titolo'] = $alias;
				}

			}
		  else
			{
			  if (DEBUG)
				{
				  echo "<!-- nicht im Array: ";
				  var_export($tabelnomo); echo "\n";
				  var_export($uzatajtabeloj);
				  echo "-->";
				}
			}
		} // if match
	} // foreach
  return array($listo, $inversa); // TODO: Client anpassen
}  // kreuKampoliston

/**
 * kreas liston de SQL-kondicxoj el la $valoroj
 * (nur por kampoj de la uzataj tabeloj)
 */
function kreuKondicxojn($uzatajtabeloj, $valoroj)
{
  $kondicxoj = array();
  foreach($valoroj AS $varnomo => $jesNe)
	{
	  $rezultoj = array();
	  if('JES' == $jesNe and
		 preg_match('/^sercxo_([^_]+)_(.+)_estasKriterio$/', $varnomo, $rezultoj))
		{
		  $tabelnomo = $rezultoj[1];
		  $kamponomo = $rezultoj[2];
		  if (!in_array($tabelnomo, $uzatajtabeloj))
			{
			  // ni ne atentas kondicxojn en neuzataj tabeloj
			  continue;
			}
		  $tipo = $valoroj["sercxo_{$tabelnomo}_{$kamponomo}_tipo"];
		  $valoro = $valoroj["sercxo_{$tabelnomo}_{$kamponomo}_valoro"];
		  $nomo = $tabelnomo .".".$kamponomo;
		  switch($tipo)
			{
			case 'sama':
			  $kondicxoj []= ($nomo . " = '" . $valoro . "'");
			  break;
			case 'malpli':
			  $kondicxoj []= ($nomo . " < '" . $valoro . "'");
			  break;
			case 'pli':
			  $kondicxoj []= ($nomo . " > '" . $valoro . "'");
			  break;
			case 'inter':
			  list($unua, $dua) = split('/', $valoro, 2);
			  $kondicxoj []= ("'" . $unua . "' < " . $nomo);
			  $kondicxoj []= ($nomo . " < '" . $dua . "'");
			  break;
			case 'LIKE':
			  $kondicxoj []= ($nomo . " LIKE '" . $valoro . "'");
			  break;
			case 'REGEXP':
			  $kondicxoj []= ($nomo . " RLIKE '" . $valoro . "'");
			  break;
			case 'parto':
			  $kondicxoj []= ($nomo . " LIKE '%" . $valoro . "%'");
			  break;
			case 'plena':
			  $kondicxoj []= ($nomo . " != ''");
			case 'unu_el':
			  {
				$elektolisto = $valoroj["sercxo_{$tabelnomo}_{$kamponomo}_elekto"];
				if(is_null($elektolisto))
				  {
					// nenio elektita -> nenio trovebla ...
					$kondicxo[] .= "1 = 0 /* $nomo: unu el neniuj */";
					break;
				  }
				if(!is_array($elektolisto))
				  {
					$elektolisto = array($elektolisto);
				  }
				$variantoj = array();
				foreach($elektolisto AS $elekto)
				  {
					$variantoj[] .= "{$nomo} = '{$elekto}'";
				  }
				$kondicxoj[] = "/* unu el pluraj */ (" . join(") OR (", $variantoj) . ")";
			  }
			  break;
			default:
			  // ne okazu!
			  darf_nicht_sein();
			}  // switch
		}   // if(match)
	}  // foreach
  return $kondicxoj;
}   // kreuKondicxojn

/**
 * Kreas cxiujn necesajn JOIN-kondicxojn kaj
 * redonas array() da ili.
 */
function kreuKonektKondicxojn($uzatajtabeloj)
{
  $kondicxoj = array();
  kreuKonekton($kondicxoj, $uzatajtabeloj,
			   "notoj", "partoprenantoID", "partoprenantoj", "ID");
  kreuKonekton($kondicxoj, $uzatajtabeloj,
			   "partoprenoj", "partoprenantoID", "partoprenantoj", "ID");
  kreuKonekton($kondicxoj, $uzatajtabeloj,
			   "partoprenantoj", "lando", "landoj", "ID");
  kreuKonekton($kondicxoj, $uzatajtabeloj,
			   "pagoj", "partoprenoID", "partoprenoj", "ID");
  kreuKonekton($kondicxoj, $uzatajtabeloj,
			   "rabatoj", "partoprenoID", "partoprenoj", "ID");
  kreuKonekton($kondicxoj, $uzatajtabeloj,
			   "litonoktoj", "partopreno", "partoprenoj", "ID");
  kreuKonekton($kondicxoj, $uzatajtabeloj,
			   "litonoktoj", "cxambro", "cxambroj", "ID");
  kreuKonekton($kondicxoj, $uzatajtabeloj,
			   "partoprenoj", "renkontigxoID", "renkontigxo", "ID");

  // nur konektu cxambro al renkontigxo, se ne jam estas
  // konekto per la partopreno
  if(!in_array("partoprenoj", $uzatajtabeloj))
  {
	kreuKonekton($kondicxoj, $uzatajtabeloj,
				 "cxambroj", "renkontigxo", "renkontigxo", "ID");
  }
  echo "<!-- kondicxoj: \n";
  var_export($kondicxoj);
  echo "-->\n";
  return $kondicxoj;
}

/**
 * kreas la JOIN-kondicxon por konekti du tabelojn,
 *  se necesas (t.e. se ili ambaux estas uzataj).
 */
function kreuKonekton(&$kondicxoj, $uzatajtabeloj, $tabelo1, $kampo1, $tabelo2, $kampo2)
{
  echo "<!-- kreuKonekton($tabelo1, $kampo1, $tabelo2, $kampo2) -->\n";
  if(in_array($tabelo1, $uzatajtabeloj) and in_array($tabelo2, $uzatajtabeloj))
	{
	  $aldono = ($tabelo1 . "." . $kampo1 . " = " . $tabelo2 . "." . $kampo2);
	  echo "<!--   :: $aldono  -->\n";
	  $kondicxoj []= $aldono;
	}
  
}


/*
 * Certigas, ke por JOIN-itaj tabeloj ankaux tiuj tabeloj
 * cxeestos, kiuj estas inter tiuj tabeloj.
 */
function certiguCxiujnKonektojn(&$uzatajtabeloj)
{
  certiguKonekton($uzatajtabeloj, "notoj", "landoj",
				  "partoprenantoj");
  certiguKonekton($uzatajtabeloj, "notoj", "partoprenoj",
				  "partoprenantoj");
  certiguKonekton($uzatajtabeloj, "notoj", "pagoj",
				  array("partoprenantoj", "partoprenoj"));
  certiguKonekton($uzatajtabeloj, "notoj", "rabatoj",
				  array("partoprenantoj", "partoprenoj"));
  certiguKonekton($uzatajtabeloj, "notoj", "renkontigxo",
				  array("partoprenantoj", "partoprenoj"));
  certiguKonekton($uzatajtabeloj, "notoj", "litonoktoj",
				  array("partoprenantoj", "partoprenoj"));
  certiguKonekton($uzatajtabeloj, "notoj", "cxambroj",
				  array("partoprenantoj", "partoprenoj", "litonoktoj"));
  certiguKonekton($uzatajtabeloj, "landoj", "partoprenoj",
				  "partoprenantoj");
  certiguKonekton($uzatajtabeloj, "landoj", "pagoj",
				  array("partoprenantoj", "partoprenoj"));
  certiguKonekton($uzatajtabeloj, "landoj", "rabatoj",
				  array("partoprenantoj", "partoprenoj"));
  certiguKonekton($uzatajtabeloj, "landoj", "renkontigxo",
				  array("partoprenantoj", "partoprenoj"));
  certiguKonekton($uzatajtabeloj, "landoj", "litonoktoj",
				  array("partoprenantoj", "partoprenoj"));
  certiguKonekton($uzatajtabeloj, "landoj", "cxambroj",
				  array("partoprenantoj", "partoprenoj", "litonoktoj"));
  certiguKonekton($uzatajtabeloj, "partoprenantoj", "rabatoj",
				  "partoprenoj");
  certiguKonekton($uzatajtabeloj, "partoprenantoj", "pagoj",
				  "partoprenoj");
  certiguKonekton($uzatajtabeloj, "partoprenantoj", "renkontigxo",
				  "partoprenoj");
  certiguKonekton($uzatajtabeloj, "partoprenantoj", "litonoktoj",
				  "partoprenoj");
  certiguKonekton($uzatajtabeloj, "partoprenantoj", "cxambroj",
				  array("litonoktoj", "partoprenoj"));

  certiguKonekton($uzatajtabeloj, "pagoj", "renkontigxo",
				  "partoprenoj");
  certiguKonekton($uzatajtabeloj, "pagoj", "rabatoj",
				  "partoprenoj");
  certiguKonekton($uzatajtabeloj, "pagoj", "litonoktoj",
				  "partoprenoj");
  certiguKonekton($uzatajtabeloj, "pagoj", "cxambroj",
				  array("litonoktoj", "partoprenoj"));

  certiguKonekton($uzatajtabeloj, "rabatoj", "renkontigxo",
				  "partoprenoj");
  certiguKonekton($uzatajtabeloj, "rabatoj", "litonoktoj",
				  "partoprenoj");
  certiguKonekton($uzatajtabeloj, "rabatoj", "cxambroj",
				  array("litonoktoj", "partoprenoj"));
  
  certiguKonekton($uzatajtabeloj, "partoprenoj", "cxambroj",
				  "litonoktoj");

  // konektu "litonoktoj" kaj "renkontigxoj" per "cxambroj",
  // sed nur, se ili ne jam estas konektataj per "renkontigxo".
  if(in_array("litonoktoj", $uzatajtabeloj) and 
	 in_array("renkontigxo", $uzatajtabeloj) and
	 ! in_array("partoprenoj", $uzatajtabeloj) and
	 ! in_array("cxambroj", $uzatajtabeloj))
	{
	  $uzatajtabeloj []= "cxambroj";
	}
}

/**
 * Aldonas la elementojn de $per al $uzatajtabeloj,
 * se $de kaj $al jam enestas.
 */
function certiguKonekton(&$uzatajtabeloj, $de, $al, $per)
{
//   echo "<!-- certiguKonekton(..., $de, $al, $per); -->\n";
  if(in_array($de, $uzatajtabeloj) and in_array($al, $uzatajtabeloj))
	{
// 	  echo "<!--   Konektu: $de <- -> $al per $per -->\n";
// 	  echo "<!-- uzatajtabeloj: \n";
// 	  var_export($uzatajtabeloj);
	  
	  if(!is_array($per))
		{
		  $per = array($per);
		}
	  foreach($per AS $nomo)
		{
		  //		  echo "\n   aldonante: $nomo";
		  if (!in_array($nomo, $uzatajtabeloj))
			{
			  $uzatajtabeloj[]= $nomo;
			}
		}
// 	  echo "\nuzatajtabeloj: \n";
// 	  var_export($uzatajtabeloj);
// 	  echo "-->\n";
	}
}






?>