<?php

$lingvoj = array('eo', 'de');

kontrolu_lingvojn($lingvoj);

$skripto = <<<DATOFINO
<script type='text/javascript'>
  window.onload = function() {
		var elementoj = document.getElementsByName('pagokvanto');
		for(var i = 0 ; i < elementoj.length; i++)
		{
			elementoj[i].onchange = sxangxu_kotizon;
		}
		sxangxu_kotizon();
	}

	function sxangxu_kotizon()
	{
//		alert("sxangxu_kotizon(), this=" + this);
		var kvanto;
		if (this.tagName && this.tagName.toLowerCase() == 'select')
		{
//			alert("this.tagName: " + this.tagName);
			kvanto = this.value;
		}
		else
		{
//			alert("this.tagName: " + this.tagName);
			var elementoj = document.getElementsByName('pagokvanto');
			for(var i = 0 ; i < elementoj.length; i++)
			{
//				alert("e[i].tagName: " + elementoj[i].tagName);
				if (elementoj[i].tagName.toLowerCase() == 'select')
				{
					kvanto = elementoj[i].value;
				}
			}
		}
		switch(kvanto)
		{
		case 'ne':
			document.getElementById('kotizonun').style.display = 'none';
			document.getElementById('kotizosurloke').style.display = 'block';
			document.getElementById('restassurloke').style.display = 'none';
			document.getElementById('kotizokalkulo').className = 'videbla';
		break;
		case 'cxio':
			document.getElementById('kotizonun').style.display = 'block';
			document.getElementById('kotizosurloke').style.display = 'none';
			document.getElementById('restassurloke').style.display = 'none';
			document.getElementById('kotizokalkulo').className = 'videbla';
			
		break;
		case 'antaux':
			document.getElementById('kotizonun').style.display = 'block';
			document.getElementById('kotizosurloke').style.display = 'none';
			document.getElementById('restassurloke').style.display = 'block';
			document.getElementById('kotizokalkulo').className = 'duona';
		break;
		default:
//			alert("ne funkcias:" + kvanto);
		}
	}
</script>
DATOFINO;

simpla_aligxilo_komenco(4,
                        CH('/2007/aligxilo#titolo')
                        /*					  array('eo' => "50a IS &ndash; ali&#285;ilo",
                         'de' => "50. IS &ndash; Anmeldeformular")*/,
					 $lingvoj, $skripto);

require_once('datumbazkonekto.php');

$renkontigxo = new Renkontigxo(DEFAUXLTA_RENKONTIGXO);

$partoprenanto = new Partoprenanto();
$partopreno = new Partopreno();

// legu la formular-datojn:
$partoprenanto->kopiu();
$partopreno->kopiu();

$kotizobj_surloke = new Kotizo($partopreno, $partoprenanto, $renkontigxo);

// echo "<!-- surloke: " . var_export($kotizobj_surloke, true) . "-->";


$partopreno->datoj['aligxkategoridato'] = date('Y-m-d');
$kotizobj_nun = new Kotizo($partopreno, $partoprenanto, $renkontigxo);

// echo "<!-- nun: " . var_export($kotizobj_nun, true) . "-->";


$kategorio = eltrovu_landokategorion($_POST['lando']);
switch($kategorio)
	{
		case 'A':
			$antauxpago = 30;
			break;
		case 'B':
			$antauxpago = 10;
			break;
		case 'C':
			$antauxpago = $kotizobj_nun->krominvitilo;
			break;
	}



// TODO: rekalkulu kotizon (inkluzive
// invitletero kaj ekskursbileto)

$kotizo_nun = $kotizobj_nun->kotizo;
$kotizo_surloke = $kotizobj_surloke->kotizo;
$restas_surloke = $kotizo_nun - $antauxpago;


?>
        <tr>
<?php

$pagmanieroj = array('uea', 'gej', 'paypal', 'persone');

/* por kelkaj landoj ni ofertas aldonajn eblojn: */

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

$pagmaniertradukoj = array(
                           'uea' => CH('uea-konto')
                           /*array('eo' => "al la UEA-konto de GEJ",
                            'de' => "auf das UEA-konto von GEJ")*/,
                           'gej' => CH('gej-konto')/*array('eo' => "al la bankkonto de GEJ",
              'de' => "an das Bankkonto von DEJ")*/,
                           'paypal' => CH('paypal') /*array('eo' => "per la interreta sistemo PayPal",
                                                     'de' => "mit dem Internet-System PayPal")*/,
                           'persone' => CH('persone') /*array('eo' => "persone al KKRen-membro",
                                                       'de' => "pers&ouml;nlich an ein KKRen-Mitglied")*/,
                           'hej' => CH('hej') /* array('eo' => "per Hungara Esperanto-Junularo",
                                               'de' => "&uuml;ber die Ungarische Esperanto-Jugend (HEJ)")*/,
                           'jefo' => CH('jefo') /*array('eo' => "per Esperanto-Jeunes (JEFO)",
                                           'de' => "&uuml;ber die Franz&oouml;sische Esperanto-Jugend (JEFO)")*/,
                           'iej' => CH('iej') /* array('eo' => "per Itala Esperanto-Junularo",
						'de' => "&uuml;ber die Italienische Esperanto-Jugend (IEJ)")*/,
                           'jeb' => CH('jeb')/*array('eo' => "al la Junularo Esperantista Brita (JEB)",
              'de' => "&uuml;ber die Britische Esperanto-Jugend (JEB)")*/,
	);



tabelelektilo('pagmaniero',
              CH('pagmaniero', "<a href='kontoj'>", "</a>")
              /*array('eo' => "Pagmaniero (<a href='kontoj'>?</a>)",
               'de' => "Zahlungsart (<a href='kontoj'>?</a>)")*/,
              $pagmanieroj, $pagmaniertradukoj,
              $pagodefauxlto);

?><!-- ################################ Kotizo-montrado ################ -->
	          <td rowspan="2" colspan='2' class='triona' id='kotizokalkulo'><input
       type='hidden' name='antauxpago_limdato' value='<?php echo $kotizobj_nun->limdato; ?>'
		 /><input type='hidden' name='minumuma_antauxpago' value='<?php echo $antauxpago;?>' /><div
				 id='kotizonun'><p>
<?php
	$limdato = $kotizobj_nun->limdato;

echo CH('kotizo-nun', $limdato);
    /* lauxlingve(array(
			'eo' => "Via (entuta) kotizo, se vi anta&#365;pagas nun (&#285;is {$limdato}):",
			'de' => "Dein Gesamt-Beitrag, wenn du jetzt (bis {$limdato}) anzahlst:",
				));
    */
?></p>
					<span class='kotizocifero'><?php
echo $kotizo_nun . " &euro;";
     ?></span></div>
<div id='restassurloke'><p><?php
     echo
     CH('restas-surloke', $antauxpago);
/*
     lauxlingve(array(
			'eo' => "Restas por pagi surloke (se vi anta&#365;pagas nun {$antauxpago} &euro;):",
			'de' => "Es bleibt vor Ort zu zahlen (wenn du jetzt {$antauxpago} &euro; anzahlst):",
            ));*/
?></p>
					<span class='kotizocifero'><?php
	echo $restas_surloke . " &euro;";
?></span></div>
<div id='kotizosurloke'><p>
<?php
echo CH('kotizo-surloke'); 
    /* lauxlingve(array(
		'eo' => "Via (entuta) kotizo, se vi ne anta&#365;pagas:",
		'de' => "Dein Gesamt-Beitrag, wenn du nicht anzahlst:",
        ));*/
?></p>
					<span class='kotizocifero'><?php
echo $kotizo_surloke . " &euro;";
     ?></span><?php
if ($kotizobj_nun->krominvitilo > 0)
{
	echo "<p>";
	echo
        CH('invitilo-antauxpago', $kotizobj_nun->krominvitilo);
        /* lauxlingve(array(
		'eo' => "Ni ne sendos invitilon al vi, &#285;is vi anta&#365;pagis almena&#365; {$kotizobj_nun->krominvitilo} &euro; pro tio.",
		'de' => "Wir k&ouml;nnen keine Einladung verschicken, bis du nicht mindestens die Geb&uuml;hr von {$kotizobj_nun->krominvitilo} &euro; gezahlt hast.",
		)); */
	echo "</p>";
}
?></div>	
</td><!-- ################################ fino Kotizo-montrado ################ -->
        </tr>
        <tr>
<?php


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
                  /*
					  array('eo' => "Mi pagos nun",
						     'de' => "Ich zahle jetzt"),
                  */
					  $kvantoj,
                  array('cxio' => CH('cxion') /*array('eo' => "... &#265;ion",
                                               'de' => "... alles")*/,
                        'antaux' => CH('antauxpagon', $antauxpago) /* array('eo' => "... la anta&#365;pagon de {$antauxpago} &euro;",
													  'de' => "... die Anzahlung von {$antauxpago} &euro;") */,
                        'ne' => CH('nenion') /* array('eo' => "... nenion kaj pagos surloke.",
                                       'de' => "... nichts (und zahle vor Ort)")*/
                        ),
					  'antaux');

?>
        </tr>
<?php

simpla_aligxilo_fino(4);

?>