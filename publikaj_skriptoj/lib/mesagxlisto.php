<?php
/**
 * biblioteko kun kelkaj funkcioj pri aktualajxoj.
 */



require_once($_SERVER['DOCUMENT_ROOT'] . "/phplibraro/retadreso.php");


/*
 * Ekzemplo de la formato: 

$novajhlisto[]= array('dato'=>'2006-10-06',
					  'id' => 'nova-retpagxaro-2006',
					  'titolo' => array('eo' => "nova retpagxaro por IS"),

					  'teksto' => array('eo' => '<p>
La Internacia Seminario (IS) nun havas novan retpagxaron, trovebla sub
http://www.internacia-seminario.de.
</p>',
	'de'=> "<p>
Die Internationale Woche (IS) hat jetzt neu gestaltete
Webseiten (auch mit neuem Inhalt), zu finden unter http://www.internationale-woche.de/.
</p>"),
					);


*/



/**
 * Redonas liston da novajxoj en HTML-formato, en $GLOBALS['lingvo'].
 *
 * $kiom_longe - nombro da tagoj, el kiuj vi volas la mesagxojn.
 *               Se 0 (la defauxlto), cxiuj mesagxoj estos montritaj.
 */
function listu_novajhojn($kiom_longe=0)
{
	$kasxilo = new Kasxilo();

  $nun = time();
  if ($kiom_longe != 0)
	{
	  $ekde = time() - ($kiom_longe * 24 * 60 * 60);
	}
  else
	{
	  // antaux la eko de la novajxlisto, do cxiuj mesagxoj.
	  $ekde = strtotime("2006-06-01");
	}

	$rezulto = "";
  foreach($GLOBALS['novajhlisto'] AS $linio)
	{
		$tempo = strtotime($linio['dato']);
	  if ($ekde <= $tempo and $tempo <= $nun and
			$linio['titolo'][$GLOBALS['lingvo']])
			// nur montru, se ekzistas titolo en la aktuala lingvo
	  {
		 $rezulto .= "\n<h3><a class='ligilcelo' id='". $linio['id'] . "'></a>" .
				lauxlingve($linio['titolo']) . "</h3>\n";
		 $rezulto .= "<p class='novajxdato'>".$linio['dato'].": </p>".
				$kasxilo->transformu_tekston(lauxlingve($linio['teksto'])) . "\n";
	  }
	}
  if ($rezulto == "")
	{
        $rezulto =  "<p>" . CH("ne-estas-novajxoj") . "</p>";
                       /*
          lauxlingve(array('eo' => "<p>Nun estas neniuj nova&#309;oj.</p>",
					'de' => "<p>Derzeit sind keine Neuigkeiten bekannt.</p>"));
                       */
	}

  return $rezulto;

}

return;

?>


