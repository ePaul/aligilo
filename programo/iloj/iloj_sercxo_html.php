<?php

/*
 * HTML-eroj por krei belan sercx-tabelon.
 *
 */



/**
 * Montras sercxo-kampon por la gxenerala sercxo en tabellinio.
 *
 * TODO: dokumentado
 */
function sercxtabellinio($priskribo, $tabelo,$nomo,  $valoroj, $alias="", $ligo='')
{
  $tipnomo = "sercxo_{$tabelo}_{$nomo}_tipo";
  $valoronomo = "sercxo_{$tabelo}_{$nomo}_valoro";
  $montrunomo = "sercxo_{$tabelo}_{$nomo}_montru";
  $uzunomo = "sercxo_{$tabelo}_{$nomo}_estasKriterio";

  $enhavo = $valoroj[$valoronomo];
  $tipo = $valoroj[$tipnomo];
  $montru = $valoroj[$montrunomo];
  $uzu = $valoroj[$uzunomo];
  //  $skripto = "kolorSxangxoDekstre('$tabelo', '$nomo')";

  eoecho ("<tr id='{$tabelo}-{$nomo}-tabellinio'><th>{$priskribo}</th><td>");
  if($alias)
	{
	  tenukasxe("sercxo_{$tabelo}_{$nomo}_alias", $alias);
	}
  if($ligo)
	{
	  tenukasxe("sercxo_{$tabelo}_{$nomo}_ligo", $ligo);
	}
  skripto_jes_ne_bokso($montrunomo, $montru,
					   "kolorSxangxoMaldekstre('$tabelo', '$nomo')");
  //  entajpbokso( $priskribo , $montrunomo, $montru, "JES", "JES");
  echo( "</td><td>");
  skripto_jes_ne_bokso($uzunomo, $uzu,
					   "kolorSxangxoDekstre('$tabelo', '$nomo')");
  echo ("</td><td><span id='{$tabelo}-{$nomo}-kriterioj'>");
  echo "<input type='text' name='{$valoronomo}' value='$enhavo' size='20'/>";
//   simpla_entajpbutono($tipnomo, $tipo, "malatentu", "kutima", $skripto);
//   eoecho (" <em>ne uzu</em> |\n");
  simpla_entajpbutono($tipnomo, $tipo, "sama", "kutima");
  eoecho (" = |\n");
  simpla_entajpbutono($tipnomo, $tipo, "malpli");
  eoecho (" < |\n");
  simpla_entajpbutono($tipnomo, $tipo, "pli");
  eoecho (" > |\n");
  simpla_entajpbutono($tipnomo, $tipo, "inter");
  eoecho (" >/< |\n");
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
 * ...
 * $elekteblecoj:  array() kun eroj de la formo x => y, kie x = sercxenda teksto,
 *                 y = priskribo.
 */
function sercxelektolinio($priskribo, $tabelo, $nomo, $valoroj, $elekteblecoj, $alias="")
{
  $tipnomo = "sercxo_{$tabelo}_{$nomo}_tipo";
  $montrunomo = "sercxo_{$tabelo}_{$nomo}_montru";
  $uzunomo = "sercxo_{$tabelo}_{$nomo}_estasKriterio";
  //  $skripto = "kolorSxangxoDekstre('$tabelo', '$nomo')";

  eoecho ("<tr id='{$tabelo}-{$nomo}-tabellinio'><th >$priskribo</th><td>");
  if($alias)
	{
	  tenukasxe("sercxo_{$tabelo}_{$nomo}_alias", $alias);
	}
  skripto_jes_ne_bokso( $montrunomo, $valoroj[$montrunomo],
					   "kolorSxangxoMaldekstre('$tabelo', '$nomo')");
  echo( "</td><td>");
  skripto_jes_ne_bokso( $uzunomo, $valoroj[$uzunomo],
					   "kolorSxangxoDekstre('$tabelo', '$nomo')");
  
  echo( "</td><td><span id='{$tabelo}-{$nomo}-kriterioj'>");
//   simpla_entajpbutono($tipnomo, $valoroj[$tipnomo], "malatentu", "kutima", $skripto);
//   eoecho (" <em>ne uzu</em> |\n");
  tenukasxe($tipnomo, "unu_el");
  eoecho (" nur unu el la sekve krucitaj: <br/>\n");

  $valornomo = "sercxo_{$tabelo}_{$nomo}_elekto";
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


function sercxtabelkapo($priskribo, $tabelo, $valoroj, $kutima = "")
{
  $uzunomo = "sercxo_tabelo_{$tabelo}_uzu";

  echo "<tr><td colspan='4' class='nevidebla'/></tr>\n";
  echo "<tr><th class='tabelo' colspan='4'>\n";

  skripto_jes_ne_bokso( $uzunomo, $valoroj[$uzunomo],
					   "kasxKontrolo('$tabelo')");
  eoecho(" <strong>{$priskribo}</strong>\n");
  echo "<script type='text/javascript'> kontrollisto.push('$tabelo'); </script>\n";
  echo "</th></tr>\n";
}

function metuKasxKontrolilon()
{
  ?>
 <script type="text/javascript">

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
	   document.getElementsByName("sercxo_" + tabelnomo + "_" + kamponomo +"_estasKriterio");
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
	   document.getElementsByName("sercxo_" + tabelnomo + "_" + kamponomo +"_montru");
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
 </script>
	 <?php

	 }



?>