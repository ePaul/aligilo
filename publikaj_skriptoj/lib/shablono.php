<?php

  /**
   * HTML-kreaj funkcioj por la aligxilo.
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id: iloj_kotizo_formatado.php 211 2008-09-09 00:27:49Z epaul $
   * @copyright 2006-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */


  /**
   */
function metu_simplan_lingvoliston($lingvoj)
{
    if ($lingvoj) {
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


/**
 * montras entajpilon en du apudaj tabelcxeloj.
 *
 * <pre>
 *  |        |                                |
 * -+--------+--------------------------------+-
 *  | titolo | .-----------------.            |
 *  |        | |                 | aldonajxoj |
 *  |        | '-----------------'            |
 * -+--------+--------------------------------+-
 *  |        |                                |
 * </pre>
 *
 * @param string         $nomo     la interna nomo de la entajpilo.
 * @param u8string|array $titoloj  la titolo, eble en diverslingvaj versioj.
 * @param int            $grandeco la largxeco de la entajpilo.
 * @param int|string     $indekso  uzata kiel tabindex-atributo.
 * @param string|array   $aldonajxoj  aldona teksto montrenda apud la
 *                                    entajpilo.
 */
function aliĝilo_tabelentajpilo($nomo, $titoloj, $grandeco,
                                $indekso="", $aldonajxoj ="")
{
	echo "<th><label for='$nomo'>" . lauxlingve($titoloj) . "</label></th>\n";
	echo "<td><input name='$nomo' type='text' id='$nomo' size='$grandeco'";
   if (is_array($GLOBALS['mankas']) and in_array($nomo, $GLOBALS['mankas']))
        {
            echo " class='mankas'";
        }
	if ($_REQUEST[$nomo])
		echo " value='" . htmlspecialchars(stripslashes($_REQUEST[$nomo]),
                                           ENT_QUOTES) . "'";
	if ($indekso)
		echo " tabindex='$indekso'";
	echo " />";
	if ($aldonajxoj && lauxlingve($aldonajxoj))
		echo " " . lauxlingve($aldonajxoj);
	echo "</td>\n";
}

/**
 * montras grandan entajpilon en tabellinio.
 *
 *<pre>
 * |        |        |         |              |
 * |--------+--------'---------'--------------|
 * | titolo | .------------------.            |
 * |        | |                  |            |
 * |        | |                  |            |
 * |        | |                  |            |
 * |        | '------------------' aldonajxoj |
 * |--------+--------.---------.--------------|
 * |        |        |         |              |
 *</pre>
 *
 * La entajpilo okupas tri tabel-kolumnojn, la nomo unu.
 *
 * @param string         $nomo     la interna nomo de la entajpilo.
 * @param u8string|array $titoloj  la titolo, eble en diverslingvaj versioj.
 * @param int            $linioj   la nombro de linioj en la entajpilo.
 * @param int            $kolumnoj la nombro de kolumnoj en la entajpilo.
 * @param int|string     $indekso  uzata kiel tabindex-atributo.
 * @param string|array   $aldonajxoj  aldona teksto montrenda apud la
 *                                        entajpilo.
 */
function aliĝilo_granda_tabelentajpilo($nomo, $titoloj, $linioj=3, $kolumnoj=50, $indekso="", $aldonajxoj ="")
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
 *
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
 * @todo anstatauxu la lastajn uzojn per uzoj de
 *    {@link aliĝilo_tabelelektilo()} kaj poste forigu tiun funkcion.
 */
function tabelelektilo($nomo, $titoloj, $elektebloj,
                       $tekstoj, $defauxlto="", $index="", $aldonajxoj="")
{
	echo "<th><label for='$nomo'>" . lauxlingve($titoloj) . "</label></th>\n";
	echo "<td>\n";
	simpla_elektilo($nomo, $elektebloj, $tekstoj, $defauxlto, $index, $aldonajxoj);
	echo "</td>\n";
}

/**
 * Elektilo kun titolo, en du apudaj tabelcxeloj.
 *
 * anstatauxajxo por {@link tabelelektilo}.
 * @param string $nomo
 * @param u8string|array $titoloj la titolo de la elektilo.
 * @param array $elektoj  en formo
 *                          array(interna => teksto)
 * @param string $defauxlto
 * @param string|int $indekso por tabindex=...
 * @param string $aldonajxoj aldona teksto dekstre apud la montrilo.
 */
function aliĝilo_tabelelektilo($nomo, $titoloj, $elektoj,
                                $defauxlto="", $indekso="", $aldonajxoj="")
{
	echo "<th><label for='$nomo'>" . lauxlingve($titoloj) . "</label></th>\n";
	echo "<td>\n";
    $kromhtml = "";
    if ($indekso) {
        $kromhtml .= " tabindex='$indekso'";
    }
    if (is_array($GLOBALS['mankas']) and in_array($nomo, $GLOBALS['mankas'])) {
        $kromhtml .= " class='mankas'";
    }

	elektilo_simpla($nomo, $elektoj, $defauxlto,
                    $aldonajxoj, 1, true, $kromhtml);
	echo "</td>\n";
}

/**
 * tenas informojn kasxite, kaj samtempe metas tabelcxelojn por tio.
 */
function aliĝilo_tabelkaŝilo($nomo, $titoloj, $valoro, $aldonajxoj="")
{
	echo "<th><label for='$nomo'>" . lauxlingve($titoloj) . "</label></th>\n";
    tenukasxe($nomo, $valoro);
	if ($aldonajxoj && lauxlingve($aldonajxoj))
		echo lauxlingve($aldonajxoj);
    else
        echo $valoro;
	echo "</td>\n";
}


function aliĝilo_listu_donitaĵojn($listo, $prefikso="", $postfikso="") {
    foreach($listo AS $nomo => $valoro)
	{
        $tutanomo = $prefikso . $nomo . $postfikso;
        if (is_array($valoro)) {
            aliĝilo_listu_donitaĵojn($valoro, $tutanomo . "[", "]");
        }
        else {
            tenukasxe($tutanomo, $valoro);
        }
	}

}


/**
 * kreas la kapon de la aligxilo-pagxoj.
 * $pasxo - la numero de la aktuala pasxo
 *
 * Varianto por 2007 ff.
 *
 * @param string|int $pasxo
 * @param string $titolo
 * @param array $lingvoj elekteblaj alternativaj lingvoj
 * @param string $aldona_kapo aldonaj linioj por la html-<head>-elemento.
 * @param string $metodo la metodo uzenda por la form-elemento, aux
 *                    'post' aux 'get'.
 */
function simpla_aligxilo_komenco($pasxo, $titolo, $lingvoj="",
                                 $aldona_kapo="", $metodo='post')
{
	echo "<!-- Method: " . $_SERVER["REQUEST_METHOD"] . "-->";
	if ($_SERVER["REQUEST_METHOD"] != 'GET')
	{
		// nur la aktuala lingvo -> neniu lingvosxangxilo estos montrata
		$lingvoj = null;
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

   aliĝilo_listu_donitaĵojn($_POST);

// 	foreach($_POST AS $nomo => $valoro)
// 	{
// 		echo "<input type='hidden' name='$nomo' value='" . htmlspecialchars(stripslashes($valoro), ENT_QUOTES) . "' />\n";
// 	}
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
		?><button type='submit' name='sendu' value='reen'>&lt;== <?php
// <!--<img src="/is/bildoj/Reen.gif"
//        alt='Reen' />-->
  echo CH("~#Reen") ?>!</button><?php
	}
?>
			  </td>
          <td colspan='2' class ='dekstrabutono'>
<button type='submit' name='sendu' value='sekven'><?php
//<!--<img src="/is/bildoj/Sekven.gif"
//					alt="Sekven" />-->
echo CH("~#Sekven"); ?> ==></button></td>
        </tr>
      </table>
	</form>
<?php
  if (marku_traduko_eo_anstatauxojn and $GLOBALS['bezonis-eo-tekston']) {
      echo "<p>" . CH("~#informo-pri-1") . "</p>";
  }
?>
</body>
</html>
<?php
}



?>
