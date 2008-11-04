<?php

  /**
   * Aligxilo - pagxo 3 (Kontribuoj, eble TEJO-kodo).
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2006-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */
  // define('DEBUG', true);

$lingvoj = array('eo', 'de');

kontrolu_lingvojn($lingvoj);

$skripto = "<script type='text/javascript' src='kotizokalkulo2.js'></script>";

simpla_aligxilo_komenco(4,
                        CH('aligxilo#titolo'),
					 $lingvoj, $skripto);

require_once($prafix . '/iloj/iloj.php');

$renkontigxo = new Renkontigxo(DEFAUXLTA_RENKONTIGXO);
$kotizosistemo = $renkontigxo->donu_kotizosistemon();

$partoprenanto = new Partoprenanto();
$partopreno = new Partopreno();

// legu la formular-datojn:
$partoprenanto->kopiu();
$partopreno->kopiu();





function parse_JMTdato_al_tagoj($teksto) {
    $timestamp = strtotime($teksto);
    // al tagoj
    return $timestamp / (60* 60 * 24);
}

$tagoj_ren =parse_JMTdato_al_tagoj($renkontigxo->datoj['de']); 
$tagoj_nask = parse_JMTdato_al_tagoj($partoprenanto->datoj['naskigxdato']);
$partopreno->datoj['agxo'] =
    floor( ($tagoj_ren - $tagoj_nask) / 365.25);
$partopreno->datoj['alvenstato'] = 'v';


$kotizobj_surloke = new Kotizokalkulilo($partoprenanto, $partopreno,
                                        $renkontigxo, $kotizosistemo);

debug_echo ("<!-- surloke: " . var_export($kotizobj_surloke, true) . "-->");

// antauxpago nun
$partopreno->datoj['aligxkategoridato'] = date('Y-m-d');
$kotizobj_nun = new Kotizokalkulilo($partoprenanto, $partopreno,
                                    $renkontigxo, $kotizosistemo);

debug_echo ("<!-- nun: " . var_export($kotizobj_nun, true) . "-->");

$antauxpago = $kotizobj_surloke->minimuma_antauxpago();


// TODO: rekalkulu kotizon (inkluzive
// invitletero kaj ekskursbileto)

$kotizo_nun = $kotizobj_nun->restas_pagenda();
$kotizo_surloke = $kotizobj_surloke->restas_pagenda();
$restas_surloke = $kotizo_nun - $antauxpago;


?>
        <tr>
<?php

$pagmanieroj = array('uea', 'gej', 'paypal', 'persone');

/* por kelkaj landoj ni ofertas aldonajn eblojn:
 * TODO: metu en datumbazon!
 */

$pagodefauxlto = 'uea';

switch($_POST['lando'])
{
	case 18: // hungario
		$pagmanieroj[]= 'hej';
		$pagodefauxlto = 'hej';
		break;
	case 12: // Britio
		$pagmanieroj[]= 'jeb';
		$pagodefauxlto = 'jeb';
		break;
	case 15: // Francio
		$pagmanieroj[]= 'jefo';
		$pagodefauxlto = 'jefo';
		break;
	case 21: // Italio
		$pagmanieroj[]= 'iej';
		$pagodefauxlto = 'iej';
		break;
	case 16: // Germanio
		$pagodefauxlto = 'gej';
		break;
}


// Atentu: tiun array() ne eblas krei per simpla ripeto,
// cxar la tradukilo bezonas, ke la CH(...)-ordonoj estas en
// la dosiero.
$pagmaniertradukoj = array('uea' => CH('uea-konto'),
                           'gej' => CH('gej-konto'),
                           'paypal' => CH('paypal'),
                           'persone' => CH('persone'),
                           'hej' => CH('hej'),
                           'jefo' => CH('jefo'),
                           'iej' => CH('iej'),
                           'jeb' => CH('jeb'),
                           );



/**
 * TODO!: ligo al la retpagxo
 */
$pagmanierojligo = CH('pagmanierojligo');


// TODO: pripensu, cxu/kiel eblas uzi aliĝilo_tabelelektilo
tabelelektilo('pagmaniero',
              CH('pagmaniero', "<a href='$pagmanierojligo'>", "</a>"),
              $pagmanieroj, $pagmaniertradukoj,
              $pagodefauxlto);

$limdato = $kotizobj_nun->limdato();

//echo("<!-- limdato: " . var_export($limdato, true) . "-->");

?><!-- ################################ Kotizo-montrado ################ -->
	          <td  rowspan='2' colspan='2' class='triona' id='kotizokalkulo'>
<?php
              tenukasxe('antauxpago_limdato', $limdato);
tenukasxe('minimuma_antauxpago', $antauxpago);
?><div id='kotizonun'><p>
<?php

echo CH('kotizo-nun', $limdato);
?></p>
					<span class='kotizocifero'><?php
echo $kotizo_nun . " &euro;";
     ?></span></div>
<div id='restassurloke'><p><?php
     echo
     CH('restas-surloke', $antauxpago);
?></p>
					<span class='kotizocifero'><?php
	echo $restas_surloke . " &euro;";
?></span></div>
<div id='kotizosurloke'><p><?php echo CH('kotizo-surloke');  ?></p>
<span class='kotizocifero'><?php
 echo $kotizo_surloke . " &euro;"; ?></span><?php
if ($kotizobj_nun->krominvitilo > 0)
{
	echo "<p>";
	echo
        CH('invitilo-antauxpago', $kotizobj_nun->krominvitilo);
	echo "</p>";
}
?></div>	
</td><!-- ################################ fino Kotizo-montrado ################ -->
        </tr>
        <tr>
<?php

    /**
     * TODO: kiel uzi aliĝilo_tabelelektilo()?
     */

	if ($antauxpago > 0)
	{
		$kvantoj = array('cxio', 'antaux', 'ne');
	}
	else
	{
		$kvantoj = array('cxio', 'ne');
	}

	tabelelektilo('pagokvanto',
                  CH('mi-pagos-nun'),
                  $kvantoj,
                  array('cxio' => CH('cxion'),
                        'antaux' => CH('antauxpagon', $antauxpago),
                        'ne' => CH('nenion')
                        ),
                  'antaux');

?>
        </tr>
<?php

simpla_aligxilo_fino(4);

?>