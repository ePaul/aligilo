<?php


  /**
   * HTML-eroj por krei belan serĉ-tabelon.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2005-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



/**
 * Montras ĝeneralan serĉo-kampon en tabellinio.
 *
 * En la kondiĉo-parto de la tabellinio aperas entajpejo, kaj apude
 * elekteblecoj por diversaj serĉmodusoj.
 *
 * @param eostring $priskribo la nomo de kampo (por serĉantoj),
 *           t.e. titolo en la serĉ-tabelo.
 * @param string   $tabelo   nomo de datumbaztabelo.
 * @param string   $nomo    la (datumbaza) nomo de la kampo.
 * @param array    $valoroj la ĝenerala serĉ-detaloj-strukturo.
 * @param string   $alias   kiel ni renomu la kampon dum la serĉo?
 *                            (necesa se samnoma kampo aperas en
 *                             diversaj tabeloj.)
 * @param string   $ligo    ŝablono por krei ligon. Se donita, la enhavo
 *                          de la kampo estas samtempe ligo, kaj la
 *                          ligo-celo estos kreita per enmetado de la valoro
 *                          en $ligo je la loko de XXXXX.
 * @param eostring $titolo se donita, ni uzas tion kiel nomo de la 
 *                         rezulto-tabelo. Se ne, ni uzas $priskribo.
 */
function sercxtabellinio($priskribo, $tabelo,$nomo,
                         $valoroj,
                         $alias="", $ligo='', $titolo="")
{
  $tipnomo = "sercxo_{$tabelo}__{$nomo}_tipo";
  $valoronomo = "sercxo_{$tabelo}__{$nomo}_valoro";
  $montrunomo = "sercxo_{$tabelo}__{$nomo}_montru";
  $uzunomo = "sercxo_{$tabelo}__{$nomo}_estasKriterio";

  $enhavo = $valoroj[$valoronomo];
  $tipo = $valoroj[$tipnomo];
  $montru = $valoroj[$montrunomo];
  $uzu = $valoroj[$uzunomo];
  //  $skripto = "kolorSxangxoDekstre('$tabelo', '$nomo')";

  eoecho ("<tr id='{$tabelo}-{$nomo}-tabellinio'><th>{$priskribo}</th><td>");
  if($alias)
	{
	  tenukasxe("sercxo_{$tabelo}__{$nomo}_alias", $alias);
	}
  if($ligo)
	{
	  tenukasxe("sercxo_{$tabelo}__{$nomo}_ligo", $ligo);
	}
  if ($titolo)
      {
          tenukasxe("sercxo_{$tabelo}__{$nomo}_titolo", $titolo);
      }
  else
      {
          tenukasxe("sercxo_{$tabelo}__{$nomo}_titolo", $priskribo);
      }
  jes_ne_bokso($montrunomo, $montru,
               "kolorSxangxoMaldekstre('$tabelo', '$nomo')");
  //  entajpbokso( $priskribo , $montrunomo, $montru, "JES", "JES");
  echo( "</td><td>");
  jes_ne_bokso($uzunomo, $uzu,
					   "kolorSxangxoDekstre('$tabelo', '$nomo')");
  echo ("</td><td><span id='{$tabelo}-{$nomo}-kriterioj'>");
  echo "<input type='text' name='{$valoronomo}' value='$enhavo' size='20'/>";
//   simpla_entajpbutono($tipnomo, $tipo, "malatentu", "kutima", $skripto);
//   eoecho (" <em>ne uzu</em> |\n");
  simpla_entajpbutono($tipnomo, $tipo, "sama", "kutima");
  eoecho (" = |\n");
  simpla_entajpbutono($tipnomo, $tipo, "malpli");
  eoecho (" &lt; |\n");
  simpla_entajpbutono($tipnomo, $tipo, "pli");
  eoecho (" > |\n");
  simpla_entajpbutono($tipnomo, $tipo, "inter");
  eoecho (" >/&lt; |\n");
  simpla_entajpbutono($tipnomo, $tipo, "LIKE");
  eoecho (" % _ |\n");
  simpla_entajpbutono($tipnomo, $tipo, "REGEXP");
  eoecho (" RE |\n");
  simpla_entajpbutono($tipnomo, $tipo, "plena");
  eoecho (" plena |\n");
  simpla_entajpbutono($tipnomo, $tipo, "parto");
  eoecho (" parto\n");
  echo "</span></td></tr>\n";
}

/**
 * montras sercxokampon kun elekteblaj kondicxoj en tabellinio.
 *
 * La ebloj venas el la renkontigxo-konfiguroj, krome funkcias tute
 * same kiel {@link sercxelektolinio}.
 * @param asciistring $tipo la tipo de konfiguroj uzenda por tiu kampo.
 * @todo disigo de la listo laŭ la konfiguro-grupoj. 
 */
function el_konfigura_sercxelektolinio($priskribo, $tabelo, $nomo, $valoroj,
				       $tipo, $alias="", $titolo="")
{
  $listo = listu_konfigurojn($tipo);
  $elektebloj = array();
  foreach($listo AS $konf) {
    $elektebloj[$konf->datoj['interna']] =
      $konf->datoj['teksto'];
  }
  sercxelektolinio($priskribo, $tabelo, $nomo, $valoroj,
		   $elektebloj, $alias, $titolo);
}


/**
 * Montras serĉo-kampon kun elekteblaj kondiĉoj en tabellinio.
 *
 * La kondiĉo-parto enhavas la eblecon elekti unu aŭ plurajn
 * el listo de valoroj - iu datumbazero estos trovita, se la valoro
 *  de la kampo estas unu el la markitaj.
 * ...
 *
 * @param eostring $priskribo la nomo de kampo (por serĉantoj),
 *           t.e. titolo en la serĉ-tabelo.
 * @param string   $tabelo   nomo de datumbaztabelo.
 * @param string   $nomo    la (datumbaza) nomo de la kampo.
 * @param array    $valoroj la ĝenerala serĉ-detaloj-strukturo.
 *
 * @param array    $elekteblecoj:  array() kun eroj de la formo
 *     interna_valoro => priskribo (eostring).
 *
 * @param string   $alias   kiel ni renomu la kampon dum la serĉo?
 *                            (necesa se samnoma kampo aperas en
 *                             diversaj tabeloj.)
 * @param eostring $titolo se donita, ni uzas tion kiel nomo de la 
 *                         rezulto-tabelo. Se ne, ni uzas $priskribo.
 */
function sercxelektolinio($priskribo, $tabelo, $nomo, $valoroj,
                          $elekteblecoj, $alias="", $titolo="")
{
  $tipnomo = "sercxo_{$tabelo}__{$nomo}_tipo";
  $montrunomo = "sercxo_{$tabelo}__{$nomo}_montru";
  $uzunomo = "sercxo_{$tabelo}__{$nomo}_estasKriterio";
  //  $skripto = "kolorSxangxoDekstre('$tabelo', '$nomo')";

  eoecho ("<tr id='{$tabelo}-{$nomo}-tabellinio'><th >$priskribo</th><td>");
  if($alias)
	{
	  tenukasxe("sercxo_{$tabelo}__{$nomo}_alias", $alias);
	}
  if($titolo)
	{
	  tenukasxe("sercxo_{$tabelo}__{$nomo}_titolo", $titolo);
	}
  else
      {
          tenukasxe("sercxo_{$tabelo}__{$nomo}_titolo", $priskribo);
      }
  jes_ne_bokso( $montrunomo, $valoroj[$montrunomo],
                "kolorSxangxoMaldekstre('$tabelo', '$nomo')");
  echo( "</td><td>");
  jes_ne_bokso( $uzunomo, $valoroj[$uzunomo],
                "kolorSxangxoDekstre('$tabelo', '$nomo')");
  
  echo( "</td><td><span id='{$tabelo}-{$nomo}-kriterioj'>");
//   simpla_entajpbutono($tipnomo, $valoroj[$tipnomo], "malatentu", "kutima", $skripto);
//   eoecho (" <em>ne uzu</em> |\n");
  tenukasxe($tipnomo, "unu_el");
  eoecho (" nur unu el la sekve krucitaj: <br/>\n");

  $valornomo = "sercxo_{$tabelo}__{$nomo}_elekto";
  $elektoj = $valoroj[$valornomo];
  if (! is_array($elektoj))
	{
	  $elektoj = array($elektoj);
	}

  $elekttekstoj = array();

  foreach($elekteblecoj AS $frazo => $alias)
	{
	  $teksto = "<input type='checkbox' name='{$valornomo}[]' value='$frazo' ";
	  if (in_array($frazo, $elektoj))
		{
		  $teksto .= "checked='checked' ";
		}
	  $teksto .= "/> ";
	  $teksto .= eotransform($alias);
	  $elekttekstoj []= $teksto;
	}
  echo join(" |\n", $elekttekstoj);
  
  echo "\n</span></td></tr>\n";

}

/**
 * metas tabellinion, kiu estas kaplinio por tabelo.
 * Ĝi enhavas elektobutonon, per kiu oni povas videbligi (aŭ malvidebligi)
 * tiun parton de la serĉtabelo. Nur en aktiva formo la kampoj en ĝi estos
 * uzata (por montri aŭ kiel kondiĉoj) en la serĉado. (Bezonas
 *  Ĵavoskripton por funkcii.)
 *
 * @param eostring $priskribo  la familiara nomo de la tabelo. Nur uzata por
 *      la teksto en tiu kaplinio.
 * @param string   $tabelo la (datumbaza) nomo de la tabelo.
 * @param array    $valoroj la ĝenerala serĉ-detaloj-strukturo.
 */
function sercxtabelkapo($priskribo, $tabelo, $valoroj)
{
  $uzunomo = "sercxo_tabelo_{$tabelo}_uzu";

  echo "<tr><td colspan='4' class='nevidebla'/></tr>\n";
  echo "<tr><th class='tabelo' colspan='4'>\n";

  jes_ne_bokso( $uzunomo, $valoroj[$uzunomo],
                "kasxKontrolo('$tabelo')");
  eoecho(" <strong>{$priskribo}</strong>\n");
  echo "<script type='text/javascript'> kontrollisto.push('$tabelo'); </script>\n";
  echo "</th></tr>\n";
}


/**
 * kreas la necesan Javaskript-kodon por kontroli la montradon kaj
 * malmontradon de la tabelpartoj, kaj ankaŭ la kolorŝanĝojn 
 * de la tabellinioj (kondiĉo-kampoj respektive montru-kampoj).
 */
function metuKasxKontrolilon()
{
  ?>
 <script type="text/javascript">
    //<![CDATA[

	var kontrollisto = new Array();

 window.onload = function () {
   for(var i = 0; i < kontrollisto.length; i++)
   {
	 kasxKontrolo(kontrollisto[i]);
   }
 }



 function kolorSxangxoDekstre(tabelnomo, kamponomo)
   {
	 var elektiloj = 
	   document.getElementsByName("sercxo_" + tabelnomo + "__" + kamponomo +"_estasKriterio");
	 var linio = document.getElementById(tabelnomo + "-" + kamponomo + "-tabellinio");
	 var kriterioj = document.getElementById(tabelnomo + "-" + kamponomo + "-kriterioj");
	 if(elektiloj[1].checked)
	   {
		 linio.cells[2].style.backgroundColor = 'rgb(200,220,255)';
		 linio.cells[3].style.backgroundColor = 'rgb(200,220,255)';
		 kriterioj.style.display = 'inline';
	   }
	 else
	   {
		 linio.cells[2].style.backgroundColor = 'rgb(255,255,255)';
		 linio.cells[3].style.backgroundColor = 'rgb(255,255,255)';
		 kriterioj.style.display = 'none';
	   }
   }

 function kolorSxangxoMaldekstre(tabelnomo, kamponomo)
   {
	 var elektiloj = 
	   document.getElementsByName("sercxo_" + tabelnomo + "__" + kamponomo +"_montru");
	 var linio = document.getElementById(tabelnomo + "-" + kamponomo + "-tabellinio");
	 if(elektiloj[1].checked)
	   {
		 linio.cells[0].style.backgroundColor = 'yellow';
		 linio.cells[1].style.backgroundColor = 'yellow';
	   }
	 else
	   {
		 linio.cells[0].style.backgroundColor = 'rgb(255,255,200)';
		 linio.cells[1].style.backgroundColor = 'rgb(255,255,200)';
	   }
   }


   function kasxKontrolo(tabelnomo)
   {

     var checkbox = document.getElementsByName("sercxo_tabelo_" + tabelnomo + "_uzu");

	 var regexp = new RegExp( tabelnomo +"-([^-]+)-tabellinio");

     listo = document.getElementsByTagName("tr");
     for (var i = 0; i < listo.length; i++)
     {
	   var result;
       if (listo[i].id && (result = listo[i].id.match(regexp)))
		 {
		   if(checkbox[1].checked)
			 {
			   listo[i].style.display = "table-row";
			   kolorSxangxoDekstre(tabelnomo, result[1]);
			   kolorSxangxoMaldekstre(tabelnomo, result[1]);
			 }
		   else
			 {
			   listo[i].style.display = "none";
			 }
		 }
     }
	 //	 debug("End: kasxKontrolo('" + tabelnomo + "')");
   }
	// ]]>
</script>
	 <?php

	 }



?>