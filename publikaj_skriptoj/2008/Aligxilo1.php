<?php

echo "<!-- defren: " . DEFAUXLTA_RENKONTIGXO . "-->";

  //$renkontigxonumero = 7;

$lingvoj = array();
$fintrad = CH_mult('/2008/aligxilo#fintradukita');
foreach($fintrad AS $lin => $jesne)
{
    if ($jesne == 'jes')
        $lingvoj[]= $lin;
}

// echo "<!-- " . var_export($lingvoj, true) . "-->";

// $lingvoj = array('de', 'eo');


// kontrolu_lingvojn($lingvoj);


simpla_aligxilo_komenco(1,
                 CH('aligxilo#titolo'),
                 $lingvoj,
                 "<script src='kotizokalkulo.js' type='text/javascript'></script>");

/*
aligxilo_komenco(1,
 					  array('eo' => "50a IS &ndash; ali&#285;ilo",
							  'de' => "50. IS &ndash; Anmeldeformular"),
					  $lingvoj,
					  "<script src='kotizokalkulo.js' type='text/javascript'></script>");
*/

echo "<!-- prafix: $prafix -->";

require_once($prafix . '/iloj/iloj.php');

$renkontigxo = new Renkontigxo(DEFAUXLTA_RENKONTIGXO);


?>
        <tr>
          <th><?php

        echo CH('logxlando'); /*
             lauxlingve(array(
		'eo' => "Lo&#285;lando",
		'de' => "Wohn-Land",
		)); */
?></th>
          <td>
<?php
$germanlingve = ($lingvo == 'de');
$mankasklaso = (is_array($GLOBALS['mankas']) and in_array('lando', $GLOBALS['mankas'])) ?
			 " class='mankas'" :
			 "";

montru_landoelektilon(5 /* linioj en la elektiloj */,
                      $_POST['lando'] ? $_POST['lando'] : "-#-"
                      /* la elektita lando */,
                      $germanlingve /* cxu uzi la germanlingvan varianton*/,
                      $mankasklaso /* cxu mankis la enhavo */,
                      $renkontigxo);
?>
</td>
          <td rowspan="4" colspan='2' class='nevidebla' id='kotizokalkulo'><p>
<?php
echo CH('jen-baza-kotizo') /*lauxlingve(array(
		'eo' => "Jen via baza kotizo:\n",
		'de' => "Hier dein Grund-Beitrag:\n",
		))*/ . "\n";
?></p><span id='kotizocifero' class='kotizocifero'>&nbsp;</span></td>
        </tr>
        <tr>
          <th><?php
echo CH('naskigxdato') /* lauxlingve(array(
		'eo' => "Naski&#285;dato",
		'de' => "Geburtsdatum",
        ))*/;
?></th>
          <td>
<?php
	
	$tagolisto = array_merge(array("-#-#-"), range(1,31));

	$tagotraduklisto = array();
	for ($i = 1; $i <= 31; $i++) {
		$tagotraduklisto[$i] = CH("x-a de", $i) /*array('eo' => "{$i}a de", 'de' => "{$i}.")*/;
	}
	$tagotraduklisto["-#-#-"] =
        '(' . CH('tago') . ')' /*
                                array('eo' => "(tago)", 'de' => "(Tag)")*/
        ;
	simpla_elektilo('tago', $tagolisto, $tagotraduklisto, "-#-#-");


	$monatolisto = array_merge(array("-#-#-"), range(1,12));


/*
	$monatolisto_eo = array(1 => "Januaro", "Februaro", "Marto", "Aprilo", "Majo",
							   "Junio", "Julio", "A&#365;gusto", "Septembro", "Oktobro",
							  "Novembro", "Decembro");
$monatolisto_de = array(1 => "Januar", "Februar", "M&auml;rz", "April", "Mai",
                        "Juni", "Juli", "August", "September", "Oktober",
                        "November", "Dezember");
 
	for ($i = 1; $i <= 12; $i++)
	{
		$monatotraduklisto[$i] =
			array('eo' => $monatolisto_eo[$i],
					'de' => $monatolisto_de[$i]);
	}
	$monatotraduklisto['-#-#-'] = array('eo' => "(monato)", 'de' => "(Monat)");
*/
$monatotraduklisto = array("-#-#-" => '(' . CH('monato') . ')',
                           1 => CH('januaro'),  CH('februaro'),
                           CH('marto'), CH('aprilo'), CH('majo'),
                           CH('junio'), CH('julio'), CH('auxgusto'),
                           CH('septembro'), CH('oktobro'), CH('novembro'),
                           CH('decembro'));


simpla_elektilo('monato', $monatolisto, $monatotraduklisto, '-#-#-');


   

	$jarolisto = array('-#-#-');
	for ($i = 2008; $i >= 1930; $i--) {
		$jarolisto[]= "$i";
		$jarotraduklisto[$i]="$i";
	}
$jarotraduklisto['-#-#-'] = '(' . CH('jaro') . ')'; //array('eo' => '(jaro)', 'de' => "(Jahr)");

	simpla_elektilo('jaro', $jarolisto, $jarotraduklisto, '-#-#-');
?>
</td>
        </tr>
        <tr>
<?php

	tabelelektilo('domotipo',
                  CH('logxado'),
                  /*array('eo' => 'Lo&#285;ado',
                   'de' => 'Wohnung'),*/
						array('J', 'M'),
                  array('J' => CH('junulargastejo') /* array('eo' => 'Junulargastejo',
                                                     'de' => "Jugendherberge")*/,
                        'M' => CH('memzorgantejo') /* array('eo' => 'Memzorgantejo',
                                                    'de' => "Massenunterkunft")*/
                        ));
?>
        </tr>
        <tr>
			<th><?php

        echo CH('partoprentempo');
          /*lauxlingve(array('eo' => "Partoprentempo",
      'de' => "Teilnahmezeit"));*/

?></th>
			<td>
<?php
	 
    $dateloop = $renkontigxo->datoj['de'];
    do
    {
	   $de_ebloj[] = $dateloop;
		$de_tradukoj[$dateloop] = $dateloop;

      $dateloop=sekvandaton ($dateloop);

		$gxis_ebloj[] = $dateloop;
		$gxis_tradukoj[$dateloop] = $dateloop;
    }
    while ($dateloop != $renkontigxo->datoj['gxis']);

	 simpla_elektilo('de', $de_ebloj, $de_tradukoj, $renkontigxo->datoj['de']);

echo CH('gxis');
//	 echo lauxlingve(array('eo' => ' &#285;is ', 'de' => " bis "));

	 simpla_elektilo('gxis', $gxis_ebloj, $gxis_tradukoj, $renkontigxo->datoj['gxis']);

?>
 </td>
</tr>
<?php

simpla_aligxilo_fino(1);

?>
