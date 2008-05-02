<?php

/* kontrolado de aligxilo-partoj */

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
 */
function aligxilon($pasxo)
{
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
}

if (!$_GET['pasxo'])
{
	die("Ungültiger Aufruf");
}


if ($_POST['sendu'] == 'reen')
{
    if ($_GET['pasxo'] == '3a')
        {
            require aligxilon(3);
        }
    else if ($_GET['pasxo'] == 4 and $_POST['invitletero'] == 'J')
        {
            require aligxilon('3a');
        }
        else 
        {
            require aligxilon($_GET['pasxo'] - 1);
        }
	exit;
}

// se ni venis tien, la uzanto petis "sekven".

$mankas = array();

/**
 * kontrolas, cxu estis forgesita necesa informo.
 *
 * Kiel parametroj oni donu cxenojn - cxiu estu nomo
 * de parametro. Se gxi ne estas donita aux "-#-#-"
 * (markilo de ne-elekto en kelkaj elektiloj), la
 * nomo estas aldonita al la globala $mankas-listo.
 */
function kontrolu_informojn()
{
	$array = func_get_args();
   foreach($array AS $dato)
     {
         if ($_POST[$dato] == '-#-#-' or $_POST[$dato] == '')
         {
            $GLOBALS['mankas'][]= $dato;
         }
     }
}

/**
 * kontrolas, cxu estis elektita unu el kelkaj
 * permeseblaj valoroj.
 *
 * $dato - la nomo de la parametro
 * $eblo - array kun la permesitaj ebleoj.
 *
 * Se $_POST[$dato] ne estas en $ebloj, $dato
 * estos aldonita al $mankas.
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
        kontrolu_informojn('jaro', 'tago', 'monato', 'lando');
		  if (strcmp($_POST['de'], $_POST['gxis']) > 0)
		  {
			  $mankas[] = 'de';
			  $mankas[] = 'gxis';
		  }

        if ($mankas)
            {
                require aligxilon(1);
            }
        else
            {
                $_POST['naskigxdato'] = $_POST['jaro'] . '-' . $_POST['monato'] .
				                            '-' . $_POST['tago'];
                require aligxilon(2);
            }
		exit();
	}
	case '2':
	{
		kontrolu_informojn('personanomo', 'nomo', 'strato', 'urbo');
        kontrolu_elekton('sekso', array('i', 'v'));
        kontrolu_elekton('vegetare', array('N', 'J', 'A'));
        kontrolu_elekton('nivelo', array('f', 'p', 'k'));

        require aligxilon($mankas ?'2' : '3');
		exit();
	}
 case '3':
	{
		if ($_POST['invitletero'] == 'J')
            {
                require aligxilon('3a');
            }
        else
            require aligxilon(4);
        exit();
	}
 case '3a':
     {
         kontrolu_informojn('pasportnumero');
         kontrolu_informojn('pasporta_persona_nomo');
         kontrolu_informojn('pasporta_familia_nomo');         
         kontrolu_informojn('pasporta_adreso');

         kontrolu_informojn('senda_adreso');

		if ($mankas)
            require aligxilon('3a');
        else
            require aligxilon(4);
		exit();
     }     
	case '4':
	{
		// kontroloj ne necesas
        
		require aligxilon(5);
		exit();
	}
	case '5':
	{
		kontrolu_elekton('konsento', array('J'));

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