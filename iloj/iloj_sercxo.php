<?php

/*
 * Iloj por la gxenerala sercxo (gxenerala_sercxo.php kaj sercxoj.php).
 *
 * Gxenerale (preskaux) cxiuj funkcioj uzas $valoroj-array-on,
 * kie enestas la sercx-opcioj.
 *
 * Distribuitaj al la sekvaj dosieroj estas pliaj
 * funkcioj:
 *
 * iloj_sercxo_html.php: 
 *
 * iloj_sercxo_analizo.php:
 *
 * iloj_sercxo_konservo.php
 *   ebligas konservadon kaj malkonservadon de
 *   sercx-konfiguroj en/el aparta tabelo de la
 *   datumbazo.
 *
 */

//define('DEBUG', true);

require_once('iloj_sercxo_html.php');
require_once('iloj_sercxo_analizo.php');
require_once('iloj_sercxo_konservo.php');

/**
 * Kopias cxiujn array-elementojn, kies
 * sxlosiloj komencas per "sercxo_".
 *
 * $arr  - la kopiinda array. Se malplena,
 *         ni prenas $_POST anstatauxe.
 *
 * redonas: array() kun la kopiitaj sxlosilo-valoroj-paroj.
 */
function kopiuSercxon($arr = "")
{
  if(! $arr)
	{
	  $arr = $_POST;
	}
  $valoroj = array();
  foreach ($arr AS $nomo => $valoro)
	{
	  if(substr($nomo,0,7) == "sercxo_")
		{
		  $valoroj[$nomo] = $valoro;
		}
	}
  return $valoroj;
}



/**
 * Uzas la sercx-opciojn por sercxi kaj
 * montras la rezulton.
 *
 * TODO: Plibeligu la aspekton.
 */
function montruRezulton($valoroj)
{
  eoecho("<h3>Serc^rezulto</h3>\n");

  list($kampoj, $inversa, $sql) = kreuSercxSQL($valoroj);
  echo "<p><code>$sql</code></p>";

  if (empty($kampoj))
	{
	  eoecho("<p>Vi elektu <em>almenau^ unu serc^indaj^o</em> &ndash; alikaze mi".
			 " nenion povas serc^i.</p>");
	  return;
	}


   $kolumnoj = array();
   foreach($kampoj AS $alias)
 	{
 	  $kolumnoj []= array($alias, $inversa[$alias]['titolo'], 'XXXXX', 'l',
						  $inversa[$alias]['ligo'], '');
 	}

   // TODO: cxu vere?
   // Ne funkcias, cxar la menu-elektilo volas ordigi laux p.nomo, kaj
   // nia demando ne enhavas tabelon 'p', sed nur partoprenanto.

    if (/*in_array('renkNumero', $kampoj)
         and*/ in_array('nomo', $kampoj)
 	   and in_array('personanomo', $kampoj)
 	   and in_array('ID', $kampoj))
 	 {
         if ($valoroj['sercxo_titolo'])
             {
                 $almenuo = "g^en. serc^o: " . $valoroj['sercxo_titolo'];
             }
         else
             {
                 $almenuo = 'rezulto de sennoma g^enerala serc^o';
             }
 	 }
    else
   {
	 $almenuo = '';
   }

   reset($kampoj);


   sercxu($sql,
		  /*order - ne eblas forlasi, do ni ordigas (provizore) laux la unua elemento */
		  array(current($kampoj), 'asc'),
		  $kolumnoj,
		  // TODO: eble pliaj sum-eblecoj
		  array(array(array('entute:', '', 'r'),
					  array('XX', 'A', 'l')
					  )
				),
		  'gxenerala_sercxo_rezulto',
		  /* extra */ 0,
		  /* csv */ 0,
		  /* vortext */ "",
		  $almenuo,
		  /* proprakapo */ 'ne');
}

?>