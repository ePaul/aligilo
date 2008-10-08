<?php



function metu_simplan_lingvoliston($lingvoj)
{
?><ul id='lingvolisto-simpla'><?php
	if (count($lingvoj) < 2)
	{
		echo "<li><!-- dummy--></li>\n";
	}
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
?></ul><?php
}

/**
 * @todo ordigu, por ke ne bezonatu metu_kapon kaj metu_piedon.
 */
function kontrolu_lingvojn($lingvoj)
{
   $lingvo = $GLOBALS['lingvo'];
	if (false and !in_array($lingvo, $lingvoj))
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
   if (is_array($GLOBALS['mankas']) and in_array($nomo, $GLOBALS['mankas']))
        {
            echo " class='mankas'";
        }
	if ($_REQUEST[$nomo])
		echo " value='" . htmlspecialchars(stripslashes($_REQUEST[$nomo]),ENT_QUOTES) . "'";
	if ($indekso)
		echo " tabindex='$indekso'";
	echo " />";
	if ($aldonajxoj && lauxlingve($aldonajxoj))
		echo " " . lauxlingve($aldonajxoj);
	echo "</td>\n";
}

function granda_tabelentajpilo($nomo, $titoloj, $linioj=3, $kolumnoj=50, $indekso="", $aldonajxoj ="")
{
	echo "<tr>\n";
	echo "<th><label for='$nomo'>" . lauxlingve($titoloj) . "</label></th>\n";
	echo "<td colspan='3'><textarea name='$nomo' id='$nomo' rows='$linioj' cols='$kolumnoj'";
   if (is_array($GLOBALS['mankas']) and in_array($nomo, $GLOBALS['mankas']))
        echo " class='mankas'";
	if ($indekso)
		echo " tabindex='$indekso'";
	echo ">";
	if ($_REQUEST[$nomo])
		echo stripslashes($_REQUEST[$nomo]);
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

function simpla_elektilo($nomo, $elektebloj, $tekstoj, $defauxlto="",
							    $indekso="", $aldonajxoj="")
{
	// se iu estas donita jam lastfoje,
	// prenu tiun kiel defauxlto.

	if ($_POST[$nomo])
	{
		$defauxlto = $_POST[$nomo];
	}
	echo "  <select name='$nomo' id='$nomo'";
   if (is_array($GLOBALS['mankas']) and in_array($nomo, $GLOBALS['mankas']))
        echo " class='mankas'";
	if ($indekso)
		echo " tabindex='$indekso'";
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
		echo " " . lauxlingve($aldonajxoj);
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
 * $index     - eble valoro de la tabindex-atributo.
 * $aldonajxo - teksto aperonta apud la elektilo (lauxlingve).
 */
function tabelelektilo($nomo, $titoloj, $elektebloj,
                       $tekstoj, $defauxlto="", $index="", $aldonajxoj="")
{
	echo "<th><label for='$nomo'>" . lauxlingve($titoloj) . "</label></th>\n";
	echo "<td>\n";
	simpla_elektilo($nomo, $elektebloj, $tekstoj, $defauxlto, $index, $aldonajxoj);
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
 * Varianto por 2007 ff.
 */
function simpla_aligxilo_komenco($pasxo, $titolo, $lingvoj, $aldona_kapo="", $metodo='post')
{
	echo "<!-- Method: " . $_SERVER["REQUEST_METHOD"] . "-->";
	if ($_SERVER["REQUEST_METHOD"] != 'GET')
	{
		// nur la aktuala lingvo -> neniu lingvosxangxilo estos montrata
		$lingvoj = array($GLOBALS['lingvo']);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php echo $aldona_kapo; ?>
<link rel='stylesheet' type='text/css' href='stilo.css' />
<title><?php
 echo $titolo;
?></title>
</head>

<body>
      <form action='kontrolu?pasxo=<?php echo $pasxo; ?>'
            method='<?php echo $metodo; ?>'>
<?php
	// antauxaj entajpajxoj:

	foreach($_POST AS $nomo => $valoro)
	{
		echo "<input type='hidden' name='$nomo' value='" . htmlspecialchars(stripslashes($valoro), ENT_QUOTES) . "' />\n";
	}
	flush();
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
<?php
           metu_simplan_lingvoliston($lingvoj);
    ?><h1><?php echo $titolo; ?></h1></td>
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

/**
 * Fino de aligxilo
 *
 * Versio por 2007 ktp.
 */
function simpla_aligxilo_fino($pasxo)
{
?>
	        <tr>
			  <td colspan='2' class='maldekstrabutono'>
<?php
	if($pasxo > 1)
	{
		?><button type='submit' name='sendu' value='reen'><?php
// <!--<img src="/is/bildoj/Reen.gif"
//        alt='Reen' />-->
  echo CH("/lib/shablono.php#Reen") ?>!</button><?php
	}
?>
			  </td>
          <td colspan='2' class ='dekstrabutono'>
<button type='submit' name='sendu' value='sekven'><?php
//<!--<img src="/is/bildoj/Sekven.gif"
//					alt="Sekven" />-->
echo CH("/lib/shablono.php#Sekven"); ?>!</button></td>
        </tr>
      </table>
	</form>
</body>
</html>
<?php
}



/*
 * nuntempe nur uzataj por la eraro-pagxo el kontrolu_lingvojn().
 * Tute forigenda post ties forigo.
 */


function metu_kapon($titolo, $lingvoj, $kapaldonajxoj="")
{
		$titolo = lauxlingve($titolo);

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php echo $kapaldonajxoj; ?>
<link rel='stylesheet' type='text/css' href='stilo.css' />
<title><?php
 echo $titolo;
?></title>
</head>

<body>
<table width="919" border="0" cellpadding="0" cellspacing="0">
  <tr>
	 <td id='lingvosxangxiloj'>
		<ul><?php
	if (count($lingvoj) < 2)
	{
		echo "<li><!-- dummy--></li>\n";
	}
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
    <td height="110" colspan="2"><div align="right"><img src="/is/bildoj/IS-Banner-Black2-small.gif" width="595" height="110" alt="" />
<img src="/is/bildoj/IS-Banner-Black3-Small.gif" width="156" height="110" alt=""/></div></td>
  </tr>
  <tr>
    <td width="108" rowspan="2" valign="top">
			<img src="/is/bildoj/image004.jpg" width="108" height="481" alt="" />
			<p><a href='kontakto'><?php echo lauxlingve($GLOBALS['kontaktonomo']); ?></a></p></td>
    <td width="703" align="center" valign="top"><div id="Layer5"></div>
      <p><img src="/is/bildoj/Home-(Esp).gif" alt="Menuo" width="703" height="71" border="0" usemap="#navigilo" /></p></td>
    <td width="108" rowspan="2" align="left" valign="top"><img src="/is/bildoj/image004Backwards.jpg" alt="" width="108" height="481" />
		<p><a href='pagxarlisto'><?php echo lauxlingve($GLOBALS['pagxarlistonomo']); ?></a></p></td>
<!-- bw	</td> -->

<!--
    <td width="108" rowspan="2" align="left" valign="top" bgcolor="#000000"><table width="108" height="491" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="108" align="center" valign="top"><a href="http://www.uea.org/"></a><a href="http://www.tejo.org/"></a><a href="http://eo.lernu.net/"></a><a href="http://www.eventoj.hu/kalendar.htm"></a><a href="http://www.vinilkosmo.com/"></a><img src="/is/bildoj/image004Backwards.jpg" width="108" height="481" /></td>
      </tr>
      
    </table></td>
-->
  </tr>
  <tr>
    <td id="enhavtabelero" align="center" valign="top"><?php
}





function metu_piedon()
{
?></td>
  </tr>
</table>

<map name="navigilo" id="navigilo">
<area shape="rect" coords="14,4,72,68"  alt="Hejmpa&#285;o" title="Hejmpa&#285;o" href="Hejmpagxo" />
<area shape="rect" coords="93,2,146,71" alt="Voja&#285;o"  title="Voja&#285;o" href="Auxte" />
<area shape="rect" coords="155,4,209,71" alt="Loko" title="Loko" href="loko" />
<area shape="rect" coords="217,3,273,70" alt="Ali&#285;u" title="Ali&#285;u" href="aligxilo" />
<area shape="rect" coords="280,2,343,70" alt="Kotizoj"  title="Kotizoj" href="kotizojPlenFrua" />
<area shape="rect" coords="353,3,419,73" alt="Programo"  title="Programo" href="./programo" />
<area shape="rect" coords="428,3,499,67" alt="Kondi&#265;oj" title="Kondi&#265;oj" href="./kondicxoj" />
<area shape="rect" coords="512,4,567,82" alt="Ali&#285;intoj" title="Ali&#285;intoj" href="listo" />
<area shape="rect" coords="585,6,636,72" alt="Dosieroj" title="Dosieroj" href="./dosieroj" />
<area shape="rect" coords="642,4,697,72" alt="Teamo" title="Teamo" href="./teamo" />
</map></body>
</html>
<?php
}

?>
