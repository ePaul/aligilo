<?php
  /**
   * kontrolado de aligxilo-partoj,
   * kun voko de la poa sekva parto.
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

@header("Content-Type: text/html; charset=UTF-8");


if ($_SERVER["REQUEST_METHOD"] != "POST")
{
  header("HTTP/1.1 405 Method Not Allowed");
  header("Allow: POST");
?>
<html>
<head>
  <title>Ungültige HTTP-Methode</title>
</head>
<body>
   <p>
  <?php echo CH('voku-nur-per-formularo'); /*
   Diese Seite sollte nur von einem der Formulare dieser Webseite aufgerufen werden.</p>
	<p>Tiu pa&#285;o estu nur vokata de unu el la formularoj de tiu retejo.</p>
                                           */
?></p>
</body>
</html>
<?php
	exit();
}


/**
 * informas la tradukilon pri dosiersxangxo
 * kaj redonas la nomon de la dosiero por uzo
 * de require.
 *
 * uzekzemplo:
 *   require aligxilon(4);
 *
 * @param int|string $pasxo la numero de la sekva pasxo.
 */
function aligxilon($pasxo)
{
    $dosiero = "Aligxilo" . $pasxo . ".php";
    eniru_dosieron($dosiero);
    return $GLOBALS['dosierujo'] . '/' . $dosiero;

    /*
    $dosierujo = $GLOBALS['dosierujo'];
    $dosiernomo =  $dosierujo . '/Aligxilo' . $pasxo . ".php";
    if (substr($dosierujo, -5) == '-test')
        {
            $dosiernomo_trad =  '/'.substr($dosierujo, 0, -5) .
                '/Aligxilo' . $pasxo . ".php";
        }
    else
        {
            $dosiernomo_trad = $dosiernomo;
        }
    eniru_dosieron("/" . $dosiernomo_trad);
    return $dosiernomo;
    */
}

if (!$_GET['pasxo'])
{
	die("Ungültiger Aufruf");
}

// iom pli komplika komparo ... cxar
// Internet-Explorer sendas la tekston de la butono
// anstataux la valoron de value=...
//
// Ni en la dua komparo eble pli bone komparu kun
// la aktuala teksto metita (el la datumbazo).
// 
// CH("/lib/shablono.php#Reen")
//
// Hmm, eble komparu kun <== helpas, se ni tion metas
// apud la vorton. TODO: elprovu kun silnovaj IE (6.*).


$iru_len = strlen('iru_al_pasxo');
foreach($_POST AS $var_nomo => $val) {
    if (substr($var_nomo, 0, $iru_len) == 'iru_al_pasxo')
        {
            $nova_pasxo = (int)substr($var_nomo, -1);
            if (0 < $nova_pasxo and
                $nova_pasxo < $_GET['pasxo'])
                {
                    require aligxilon($nova_pasxo);
                    exit;
                }
        }
}


echo "<!-- ne trovita! -->";
// se ni venis tien, la uzanto petis "sekven".

$mankas = array();

/**
 * kontrolas, cxu estis forgesita necesa informo.
 *
 * Kiel parametroj oni donu cxenojn - cxiu estu nomo
 * de parametro. Se gxi ne estas donita aux "-#-#-"
 * (markilo de ne-elekto en kelkaj elektiloj), la
 * nomo estas aldonita al la globala $mankas-listo.
 *
 * @param string $... nomoj de variabloj kontrolendaj.
 */
function kontrolu_informojn()
{
	$array = func_get_args();
   foreach($array AS $dato)
     {
         if (preg_match('/^(\w+)\[(\w+)\]$/', $dato, $trovoj)) {
             $val =&  $_POST[$trovoj[1]][$trovoj[2]];
         }
         else {
             $val =& $_POST[$dato];
         }
         if ($val == '-#-#-' or $val == '') {
             $GLOBALS['mankas'][]= $dato;
         }
     }
}

/**
 * kontrolas, cxu $_POST[$arraynomo] estas array de la formo
 *   'jaro' => ...
 *   'monato' => ...
 *   'tago' => ...,
 *  kaj tiuj enhavas tauxgan daton.
 *
 * @param string $arraynomo la nomo de la POST-variablo, kiu enhavu
 *       la dato-kampojn.
 * @param string $varnomo la nomo de POST-variablo, en kiu estas enmetenda
 *       la formatita dato en kazo de sukceso.
 */
function kontrolu_daterojn($arraynomo, $varnomo) {

    /*
    kontrolu_informojn($arraynomo.'[tago]',
                       $arraynomo.'[monato]',
                       $arraynomo.'[jaro]');
    */
    $jaro = (int)($_POST[$arraynomo]['jaro']);
    $monato = (int)($_POST[$arraynomo]['monato']);
    $tago = (int)($_POST[$arraynomo]['tago']);
    if (checkdate($monato,$tago,$jaro)) {
        $_POST[$varnomo] = $jaro . '-' . $monato . '-' . $tago;
    }
    else {
        unset($_POST[$varnomo]);
        $GLOBALS['mankas'][]= $arraynomo.'[tago]';
        $GLOBALS['mankas'][]= $arraynomo.'[monato]';
        $GLOBALS['mankas'][]= $arraynomo.'[jaro]';
    }

}

/**
 * kontrolas, cxu estis elektita unu el kelkaj
 * permeseblaj valoroj.
 *
 * Se $_POST[$dato] ne estas en $ebloj, $dato
 * estos aldonita al $mankas.
 * @param string $dato la nomo de la parametro
 * @param array  $eblo array kun la permesitaj ebleoj.
 */
function kontrolu_elekton($dato, $ebloj)
{
	if (!in_array($_POST[$dato], $ebloj))
	{
		$GLOBALS['mankas'][] = $dato;
		echo "<!-- malgxusta: $dato = {$_POST[$dato]} -->";
	}
}


switch($_GET['pasxo'])
{
	case '1':
	{
        echo "<!-- POST: " . var_export($_POST, true) . "-->";
        kontrolu_daterojn('naskigxo', 'naskigxdato');
        kontrolu_informojn('lando', 'pagmaniero_1');
        kontrolu_elekton('vegetare', array('N', 'J', 'A'));
        kontrolu_elekton('invitletero', array('N', 'J'));
        kontrolu_elekton('cxambrotipo', array('u', 'g'));
        kontrolu_elekton('domotipo', array('J', 'A', 'T', 'M'));
        kontrolu_elekton('tejo_membro_laudire', array('j', 'n', 's'));

        if (strcmp($_POST['de'], $_POST['gxis']) > 0)
		  {
			  $mankas[] = 'de';
			  $mankas[] = 'gxis';
		  }
        
        switch($_POST['pagmaniero_1']) {
        case 'ueakonto':
        case 'paypal':
        case 'ne-scias':
            $_POST['pagmaniero'] = $_POST['pagmaniero_1'];
            // ne bezonas plian informon
            break;
        case 'peranto':
            kontrolu_informojn('pagmaniero_2');
            $_POST['pagmaniero'] = 'peranto:' . $_POST['pagmaniero_2'];
            break;
        case 'organizajxo':
            kontrolu_elekton('pagmaniero_3',
                             // TODO: pligrandigu liston
                             array('gej', 'pej', 'cxej',
                                   'hej', 'iej', 'jefo'));
            $_POST['pagmaniero'] = 'organizo:' . $_POST['pagmaniero_3'];
            break;
        default:
            $mankas[]= 'pagmaniero_1';
        }


        if ($mankas)
            {
                debug_echo("<!-- mankas: " . var_export($mankas, true) . "-->");
                require aligxilon(1);
            }
        else
            {
                $_POST['naskigxdato'] =
                    $_POST['naskigxo']['jaro'] . '-' .
                    $_POST['naskigxo']['monato'] . '-' .
                    $_POST['naskigxo']['tago'];
                require aligxilon(2);
            }
		exit();
	}
	case '2':
	{
		kontrolu_informojn('personanomo', 'nomo', 'adreso', 'urbo', 'retposxto');
        kontrolu_elekton('sekso', array('i', 'v'));
        kontrolu_elekton('nivelo', array('f', 'p', 'k'));

        require aligxilon($mankas ? '2' : '3');
		exit();
	}
 case '3':
	{
		if ($_POST['invitletero'] == 'J')
            {
                require aligxilon(4);
            }
        else
            {
                require aligxilon(5);
            }
        exit();
	}
	case '4':
	{
		// kontroloj ne necesas

        if ($_POST['invitletero']) {
            kontrolu_daterojn('pp_validas_de', 'pasporto_valida_de');
            kontrolu_daterojn('pp_validas_gxis', 'pasporto_valida_gxis');

            kontrolu_informojn('pasportnumero');
            kontrolu_informojn('pasporta_persona_nomo');
            kontrolu_informojn('pasporta_familia_nomo');         
            kontrolu_informojn('pasporta_adreso');
            
            kontrolu_informojn('senda_adreso');
        }

		if ($mankas)
            require aligxilon(4);
        else
            require aligxilon(5);

		exit();
	}
	case '5':
	{
        //        kontrolu_elekton('retakonfirmilo', array('J', 'N'));
		kontrolu_elekton('konsento', array('JES'));

		if ($mankas)
			require aligxilon(5);
		else
			require aligxilon('Dankon');
		exit();
	}
	default:
	{
		die ("malgxusta pasxo!");
	}
}

?>