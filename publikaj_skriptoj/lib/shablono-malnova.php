<?php


  /**
   * sercxas aktivulo-identigilojn en [[]] kaj metas ligojn
   * al la lauxaj aktivulo-pagxoj.
   */
function transformu_tekston_kreu_aktivuloligojn($teksto)
{
	$rezulto = "";
	$indekso = 0;
	while(true)
        {
            if (DEBUG)
                {
                    $rezulto .= "<!-- indekso = $indekso, pos = $pos, fino = $fino -->";
                }
            $pos = strpos($teksto, "[[", $indekso);
            if (DEBUG)
                {
                    $rezulto .= "<!-- indekso = $indekso, pos = $pos, fino = $fino -->";
                }
            if ($pos === FALSE)
                {
                    if (DEBUG)
                        {
                            $rezulto .= "<!-- Ende, pos === false -->";
                        }
                    // ne plu aperas "[[";
                    $rezulto .= substr($teksto, $indekso);
                    break;
                }
            $rezulto .= substr($teksto, $indekso, $pos-$indekso);
            if (DEBUG)
                {
                    $rezulto .= "<!-- indekso = $indekso, pos = $pos, fino = $fino -->";
                }
            $fino = strpos($teksto, "]]", $indekso);
            if (DEBUG)
                {
                    $rezulto .= "<!-- indekso = $indekso, pos = $pos, fino = $fino -->";
                }
            if ($fino === false)
                {
                    if (DEBUG)
                        {
                            $rezulto .= "<!-- Ende, fino === false -->";
                        }
                    // ne okazu!
                    $rezulto .= "<strong>ERARO</strong>"
                        . substr($teksto, $pos+2);
                    break;
                }
            $adreso = substr($teksto, $pos+2, $fino - ($pos+2));
        
            $sql =
                "SELECT ad.nomo_pers, ad.nomo_fam, ak.montru_entute" .
                " FROM MA_Adresoj AS ad, MA_aktivuloj AS ak" .
                " WHERE ((ad.id = ak.membro_id) " .
                "   AND (ak.id = '$adreso')) ";
            $rez = mysql_query($sql) or die("Eraro: " . mysql_error());
            $nomo = mysql_fetch_assoc($rez);
        
            if ((int)($nomo['montru_entute']))
                {
                    $rezulto .=
                        "<a href='http://www.esperanto.de/dej/aktivuloj/homoj"
                        . ($GLOBALS['lingvo'] == 'de' ? '' : '_eo') . "/" .
                        $adreso . "'>" . $nomo['nomo_pers'] . " " .
                        $nomo['nomo_fam'] . "</a>";
                }
            else
                {
                    $rezulto .=  $nomo['nomo_pers'] . " " . $nomo['nomo_fam'];
                }
            if (DEBUG)
                {
                    $rezulto .= "<!-- indekso = $indekso, pos = $pos, fino = $fino -->";
                }
            $indekso = $fino + 2;
        }
	return $rezulto;
}


/**
 * sercxas sxlosilon en la artistolisto kaj
 * donas la du sxlosilojn apude (najbarante
 * unuan kaj lastan).
 */
function sekva_kaj_antauxa_artisto($tiu)
{
	$listo = $GLOBALS['artistolisto'];
	if (! $listo[$tiu])
		return null;

	foreach($listo AS $pagxo => $priskribo)
	{
		if ($lasta == $tiu)
		{
			$sekva = $pagxo;
			break;
		}
		$antauxa = $lasta;
		$lasta = $pagxo;
	}
	if (!$sekva)
	{
		// estis la lasta elemento -> ni trovu la unuan.
		reset($listo);
		return array($antauxa, key($listo));
	}
	if (!$antauxa)
	{
		// tuj la unua estis la gxusta -> ni trovu la lastan
		end($listo);
		return array(key($listo), $sekva);
	}
	// jen la kutima kazo:
	return array($antauxa, $sekva);
}


function kontrolu_lingvojn($lingvoj)
{
   $lingvo = $GLOBALS['lingvo'];
	if (!in_array($lingvo, $lingvoj))
	{
		header("HTTP/1.0 404 Not Found");
		metu_kapon("Seite fehlt - lingvo mankas", $lingvoj);

		echo "<p>" . $GLOBALS['pagxomankas_mesagxo'][$lingvo] . "</p>\n";
		echo "<ul>\n";
		foreach ($lingvoj AS $li)
		{
			echo "<li><a href='" . $GLOBALS['pagxo_prefikso'] . $li . "/" . $GLOBALS['pagxo'] .
			     "'>" . $GLOBALS['lingvonomoj'][$li] . "</a></li>\n";
		}
		echo "</ul>\n";
		metu_piedon();
	   exit();
	}
}

/** por nur dulingvaj pagxoj */
function t($germana, $esperanta)
{
	if ($GLOBALS['lingvo'] == 'de')
		echo $germana;
	else if ($GLOBALS['lingvo'] == 'eo')
		echo $esperanta;
}


function metu_kapon($titolo, $lingvoj, $kapaldonajxoj="")
{
   if(is_array($titolo))
	{
		$titolo = lauxlingve($titolo);
	}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php echo $kapaldonajxoj; ?>
<link rel='stylesheet' type='text/css' href='rolfo.css' />
<title><?php
 echo $titolo;
?></title>
</head>

<body>
<table width="919" height="627" border="0" cellpadding="0" cellspacing="0">
  <tr>
	 <td id='lingvosxangxiloj'>
		<ul><?php
	foreach($lingvoj AS $li)
	{
		if ($li == $GLOBALS['lingvo'])
		{
//			echo "<li> " . $GLOBALS['lingvonomoj'][$li] . " </li>\n";
		}
		else
		{
			echo "<li> <a href='" . $GLOBALS['pagxo_prefikso'] . $li . "/" . $GLOBALS['pagxo'] .
			     "'>" . $GLOBALS['lingvonomoj'][$li] . "</a></li>\n";
		}
	}
	 ?></ul></td>
    <td height="110" colspan="2"><div align="right"><img src="/is/bildoj/IS-Banner-Black2-small.gif" width="595" height="110" />
<img src="/is/bildoj/IS-Banner-Black3-Small.gif" width="156" height="110" /></div></td>
  </tr>
  <tr>
    <td width="108" rowspan="2" valign="top"><img src="/is/bildoj/image004.jpg" width="108" height="481" /></td>
    <td width="703" align="center" valign="top"><div id="Layer5"></div>
      <p><img src="/is/bildoj/Home-(Esp).gif" alt="Menuo" width="703" height="71" border="0" usemap="#navigilo" /></p></td>
    <td width="108" rowspan="2" align="left" valign="top" bgcolor="#000000"><table width="108" height="491" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="108" align="center" valign="top"><a href="http://www.uea.org/"></a><a href="http://www.tejo.org/"></a><a href="http://eo.lernu.net/"></a><a href="http://www.eventoj.hu/kalendar.htm"></a><a href="http://www.vinilkosmo.com/"></a><img src="/is/bildoj/image004Backwards.jpg" width="108" height="481" /></td>
      </tr>
      
    </table></td>
  </tr>
  <tr>
    <td id="enhavtabelero" align="center" valign="top"><?php
}


/**
 * 
 */
function komencu_kotizo_tabelon($titoloj, $aktuala, $lingvoj)
{
	$katNomo =
		lauxlingve(array(
			'eo' => "kategorio",
			'de' => "Kategorie",
			'en' => "category",
				));

	metu_kapon($titoloj, $lingvoj);
	?>
	<h1><?php echo lauxlingve($GLOBALS['kotizolisto'][$aktuala]); ?></h1>
	<p>
	<?php
		$komenco = true;
		foreach($GLOBALS['kotizolisto'] AS $nomo => $val)
		{
			if (! $komenco)
			{
				echo " &ndash; ";
			}
			if ($nomo == $aktuala)
			{
				echo lauxlingve($val);
			}
			else
			{
				echo "<a href='kotizoj$nomo'>" . lauxlingve($val) . "</a>";
			}
			$komenco = false;
		}
	?>
	</p>
	<table class='kotizotabelo'>
	  <thead>
		<tr>
		 <th></th>
       <th><?php echo $katNomo; ?> A</th>
       <th><?php echo $katNomo; ?> B</th>
       <th><?php echo $katNomo; ?> C</th>
      </tr>
	  </thead>
	  <tbody>
	<?php
}


function finu_kotizo_tabelon()
{
?>
            </tbody>
          </table>
<p><?php
	echo lauxlingve($GLOBALS['kotizoklarigoj']);
?></p>
<?php
	metu_piedon();
}

/**
 * komencas programeran pagxon.
 * $propra pagxo - la nomo de la propra pagxo,
 *           por eltrovi la gxustajn ligojn al
 *           la sekva kaj antauxa pagxo.
 * $lingvoj - en kiuj lingvoj ekzistas tiu pagxo?
 */
function komencu_artiston($propra_pagxo, $lingvoj)
{

	list($maldekstra_ligo, $dekstra_ligo) =
		sekva_kaj_antauxa_artisto($propra_pagxo);

	$kapparto = "<link rel='prev' href='$maldekstra_ligo' />
<link rel='next' href='$dekstra_ligo' />\n";
	if ($propra_pagxo != "programo")
	{
		$kapparto .= "<link rel='up' href='programo' />\n";
	}

	metu_kapon("IS 2006/2007 &ndash; " . lauxlingve($GLOBALS['artistolisto'][$propra_pagxo]),
				  $lingvoj, $kapparto);
			  
?>
	      <table width="100%" border="0">
        <tr style="text-align: center">
          <td>
					<a href="<?php echo $maldekstra_ligo; ?>">
					<img src="/is/bildoj/PliajLeft.gif" alt="Pliaj Maldekstren" width="99" height="28" />
					</a>
			 </td>
          <td>
				<img src="/is/bildoj/Programeroj.gif" alt="Programeroj" width="136" height="28" />
			</td>
          <td>
				<a href="<?php echo $dekstra_ligo;?>">
				<img src="/is/bildoj/PliajRight.gif" alt="Pliaj Dekstren" width="99" height="28" border="0" />
				</a></td>
        </tr>
        <tr>
          <td colspan='3'><?php
}


function finu_artiston()
{
	?></td>
        </tr>
      </table>
<?php
	metu_piedon();
}


function komencu_alvenon($propra, $titoloj, $lingvoj)
{

metu_kapon(lauxlingve($titoloj), $lingvoj);
?>
	      <table width="699" border="0" cellspacing="15" cellpadding="0">
        <tr>
          <td align="center"><a href="Aviadile"><img src="/is/bildoj/Aviadile.jpg" alt="Aviadile" width="85" height="48" border="0" /></a></td>
          <td align="center"><a href="Trajne"><img src="/is/bildoj/Trajne.jpg" alt="Trajne" width="85" height="48" border="0" /></a></td>
          <td align="center"><a href="Auxte"><img src="/is/bildoj/Auxte.jpg" alt="A&#365;te" width="85" height="48" border="0" /></a></td>
          <td width="229" rowspan="2" align="center" valign="top"><img src="Anfahrt%20Wewelsburg.jpg" alt="" width="250" height="334" /></td>
        </tr>
        <tr>
          <td colspan="3" valign="top"><?php
}

function finu_alvenon()
{
?>
</td>
        </tr>
      </table>
<?php
metu_piedon();
}


function lauxlingve($array)
{
	if (! is_array($array))
		return $array;
	if ($array[$GLOBALS['lingvo']])
		return $array[$GLOBALS['lingvo']];
	else
		return $array['eo'];
}

function tabelentajpilo($nomo, $titoloj, $grandeco, $indekso="", $aldonajxoj ="")
{
	echo "<th><label for='$nomo'>" . lauxlingve($titoloj) . "</label></th>\n";
	echo "<td><input name='$nomo' type='text' id='$nomo' size='$grandeco'";
    if (in_array($nomo, $_GLOBALS['mankas']))
        {
            echo " class='mankas'";
        }
	if ($_REQUEST[$nomo])
		echo " value='" . $_REQUEST[$nomo] . "'";
	if ($indekso)
		echo " tabindex='$indekso'";
	echo " />";
	if ($aldonajxoj && lauxlingve($aldonajxoj))
		echo lauxlingve($aldonajxoj);
	echo "</td>\n";
}

function granda_tabelentajpilo($nomo, $titoloj, $linioj=3, $kolumnoj=50, $indekso="", $aldonajxoj ="")
{
	echo "<tr>\n";
	echo "<th><label for='$nomo'>" . lauxlingve($titoloj) . "</label></th>\n";
	echo "<td colspan='3'><textarea name='$nomo' id='$nomo' rows='$linioj' cols='$kolumnoj'";
    if (in_array($nomo, $_GLOBALS['mankas']))
        echo " class='mankas'";
	if ($indekso)
		echo " tabindex='$indekso'";
	echo ">";
	if ($_REQUEST[$nomo])
		echo $_REQUEST[$nomo];
	echo "</textarea>\n";
	if ($aldonajxoj && lauxlingve($aldonajxoj))
		echo lauxlingve($aldonajxoj);
	echo "</td>\n";
}

/**
 * kreas elektilon sen tabelkampo
 * $nomo - la interna nomo.
 * $elektebloj - array kun la diversaj ebloj.
 * $tekstoj - por la priskriboj de la elektebloj
 *            en diversaj lingvoj.
 *            array("eblo1" => array('eo' => "unua eblo"),
 * 					  "eblo2" => array('eo' => 'dua eblo'));
 * $defauxlto - kiu eblo estos antauxelektita, se
 *              ne estas jam elektita alia (per $_REQUEST).
 * $aldonajxo - teksto aperonta apud la elektilo (lauxlingve).
 */

function simpla_elektilo($nomo, $elektebloj,
                       $tekstoj, $defauxlto="", $aldonajxoj="")
{
	// se iu estas donita jam lastfoje,
	// prenu tiun kiel defauxlto.

	if ($_POST[$nomo])
	{
		$defauxlto = $_POST[$nomo];
	}
	echo "  <select name='$nomo' id='$nomo'";
    if (in_array($nomo, $_GLOBALS['mankas']))
        echo " class='mankas'";
    echo ">\n";
	foreach($elektebloj AS $eblo)
	{
		echo "     <option value='$eblo'";
		if ($eblo == $defauxlto)
		{
			echo " selected='selected'";
		}
		echo " >" . lauxlingve($tekstoj[$eblo]) . "</option>\n";
	}
	echo "  </select>\n";
	if ($aldonajxoj && lauxlingve($aldonajxoj))
		echo lauxlingve($aldonajxoj);
}

/**
 * kreas elektilon kun titolo en du apudaj tabelkampoj.
 * $nomo - la interna nomo.
 * $titoloj - la titoloj, en diversaj lingvoj.
 * $elektebloj - array kun la diversaj ebloj.
 * $tekstoj - por la priskriboj de la elektebloj
 *            en diversaj lingvoj.
 *            array("eblo1" => array('eo' => "unua eblo"),
 * 					  "eblo2" => array('eo' => 'dua eblo'));
 * $defauxlto - kiu eblo estos antauxelektita, se
 *              ne estas jam elektita alia (per $_REQUEST).
 * $aldonajxo - teksto aperonta apud la elektilo (lauxlingve).
 */
function tabelelektilo($nomo, $titoloj, $elektebloj,
                       $tekstoj, $defauxlto="", $aldonajxoj="")
{
	echo "<th><label for='$nomo'>" . lauxlingve($titoloj) . "</label></th>\n";
	echo "<td>\n";
	simpla_elektilo($nomo, $elektebloj, $tekstoj, $defauxlto, $aldonajxoj);
	echo "</td>\n";
}

function tabelkasxilo($nomo, $titoloj, $valoro, $aldonajxoj)
{
	echo "<th><label for='$nomo'>" . lauxlingve($titoloj) . "</label></th>\n";
	echo "<td><input name='$nomo' type='hidden' id='$nomo'";
	echo " value='" . $valoro . "'";
	echo " />";
	if ($aldonajxoj && lauxlingve($aldonajxoj))
		echo lauxlingve($aldonajxoj);
	echo "</td>\n";
}


function kreu_elektilon($nomo, $array, $defauxltnomo="", $defaulxtteksto="")
{
	echo "<select name='$nomo' id='$nomo'>\n";
	if ($defauxltnomo)
	{
		echo "<option selected='selected' value='$defauxltnomo'>$defaulxtteksto</option>\n";
	}
	foreach($array AS $sxlosilo => $valoro)
	{
			echo "<option value='$sxlosilo'>$valoro</option>\n";
	}
	echo "</select>\n";
}


/**
 * kreas la kapon de la aligxilo-pagxoj.
 * $pasxo - la numero de la aktuala pasxo
 *
 */
function aligxilo_komenco($pasxo, $titolo, $lingvoj, $aldona_kapo="", $metodo='POST')
{
	echo "<!-- Method: " . $_SERVER["REQUEST_METHOD"] . "-->";
	if ($_SERVER["REQUEST_METHOD"] != 'GET')
	{
		// nur la aktuala lingvo -> neniu lingvosxangxilo estos montrata
		$lingvoj = array($GLOBALS['lingvo']);
	}

	metu_kapon($titolo, $lingvoj, $aldona_kapo);
	?>

      <form action='kontrolu?pasxo=<?php echo $pasxo; ?>'
            method='<?php echo $metodo; ?>'>
<?php
	// antauxaj entajpajxoj:

	foreach($_POST AS $nomo => $valoro)
	{
		echo "<input type='hidden' name='$nomo' id='$nomo' value='$valoro' />\n";
	}
?>
      <table id='aligxilo_tabelo'>
		<colgroup>
			<col class='aligxilo-titoloj' />
			<col />
			<col class='aligxilo-titoloj' />
			<col />
		</colgroup>
        <tr>
          <td colspan="4" align="center">
				<h1>Ali&#285;ilo</h1></td>
        </tr>
        <tr>
          <td colspan="4" align="center">
<?php
	for($i = 1; $i <= $GLOBALS['aligxilopasxoj']; $i++)
	{
		if ($i < $pasxo)
		{
			echo "<img class='pasxo_preta' src='/is/bildoj/pasxo$i-verda.gif'
					 style=' width: 118px; height: 58px;' alt=' pasxo $i ' />";
		}
		else
		{
			echo "<img class='pasxo_nepreta' src='/is/bildoj/pasxo$i-blua.gif'
					 style=' width: 118px; height: 58px;' alt=' pasxo $i ' />";
		}
	}
?>
</td>
        </tr>
<?php
}

function aligxilo_fino($pasxo)
{
?>
	        <tr>
			  <td colspan='2' class='maldekstrabutono'>
<?php
	if($pasxo > 1)
	{
		?><button type='submit' name='sendu' value='reen'><img src="/is/bildoj/Reen.gif"
				 alt='Reen' /></button><?php
	}
?>
			  </td>
          <td colspan='2' class ='dekstrabutono'>
				<button type='submit' name='sendu' value='sekven'><img src="/is/bildoj/Sekven.gif"
					alt="Sekven" /></button></td>
        </tr>
      </table>
	</form>
<?php
  metu_piedon();
}


function metu_piedon()
{
?></td>
  </tr>
</table>

<map name="navigilo" id="navigilo">
<area shape="rect" coords="14,4,72,68" title="Hejmpa&#285;o" href="Hejmpagxo" />
<area shape="rect" coords="93,2,146,71" title="Voja&#285;o" href="Auxte" />
<area shape="rect" coords="155,4,209,71" title="Loko" href="loko" />
<area shape="rect" coords="217,3,273,70" title="Ali&#285;u" href="aligxilo" />
<area shape="rect" coords="280,2,343,70" title="Kotizoj" href="kotizojPlenFrua" />
<area shape="rect" coords="353,3,419,73" title="Programo" href="./programo" />
<area shape="rect" coords="428,3,499,67" title="Kondi&#265;oj" href="./kondicxoj" />
<area shape="rect" coords="512,4,567,82" title="Ali&#285;intoj" href="listo" />
<area shape="rect" coords="585,6,636,72" title="Dosieroj" href="./dosieroj" />
<area shape="rect" coords="642,4,697,72" title="Teamo" href="./teamo" />
</map></body>
</html>
<?php
}

?>