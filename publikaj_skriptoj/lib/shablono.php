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
   * metas liston kun ligoj al alilingvaj versioj de la pagxo.
   * @param array $lingvoj listo de lingvoj.
   */
function aliĝilo_metu_simplan_lingvoliston($lingvoj)
{
    if ($lingvoj) {
        echo "  <ul id='lingvolisto-simpla'>\n";
        if (count($lingvoj) < 2)
            {
                echo "      <li><!-- dummy--></li>\n";
            }
        foreach($lingvoj AS $li)
            {
                if ($li == $GLOBALS['lingvo'])
                    {
                        //			echo "<li> " . $GLOBALS['lingvonomoj'][$li] . " </li>\n";
                    }
                else
                    {
                        echo "      <li>" .
                            donu_ligon($GLOBALS['pagxo_prefikso'] . $li .
                                       "/" . $GLOBALS['pagxo'],
                                       $GLOBALS['lingvonomoj'][$li]).
                            "</li>\n";
                    }
            }
        echo "</ul>\n";
    }
} //  aliĝilo_metu_simplan_lingvoliston



/**
 * elektas version de teksto en aktuala lingvo.
 *
 * @param string|array $array se cxeno, redonas gxin simple.
 *       Se estas array, elektas $array[$GLOBALS['lingvo'], se tiu ekzistas,
 *       alikaze $array['eo'].
 * @return string
 */
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
 * Elektilo kun titolo, en du apudaj tabelcxeloj.
 *
 * @param string $nomo
 * @param u8string|array $titoloj la titolo de la elektilo.
 * @param array $elektoj  en formo
 *                          array(interna => teksto)
 * @param string $defauxlto
 * @param string|int $indekso por tabindex=...
 * @param string $aldonajxoj aldona teksto dekstre apud la montrilo.
 */
function aliĝilo_tabelelektilo_radie($nomo, $titoloj, $elektoj,
                                     $defauxlto="", $kolumnoj=1,
                                     $bezonata=false)
{
	echo "<th><label for='$nomo'>" . $titoloj . "</label></th>\n";
    $kromhtml = "";
    $klasoj = array();

    if ($bezonata) {
        $klasoj[]= "nepra";
    }

    if (is_array($GLOBALS['mankas']) and in_array($nomo, $GLOBALS['mankas'])) {
        $klasoj[]= "mankas";
    }
    if (count($klasoj)) {
        $kromhtml .= " class='" . implode(" ", $klasoj) . "'";
    }

    if ($kolumnoj > 1) {
        $kromhtml .= " colspan='" . $kolumnoj . "'";
    }

	echo "<td" . $kromhtml .">\n";

    foreach($elektoj AS $interna => $teksto) {
        echo " <span class='elekteblo'>";
        simpla_entajpbutono($nomo, $_REQUEST[$nomo], $interna,
                            ($defauxlto == $interna? "kutima" : ""));
        eoecho ($teksto);
        echo "</span> \n";
    }
	echo "</td>\n";
}

/**
 * Elektilo kun titolo, en du apudaj tabelcxeloj.
 *
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
	echo "<th><label for='$nomo'>" . $titoloj . "</label></th>\n";
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
 * markbutono kun titolo en du apudaj tabelcxeloj.
 *
 * @param string $nomo
 * @param u8string $titolo
 * @param u8string $jes_teksto
 * @param string|boolean $defauxlto la defauxlta
 *         stato de la elektilo.
 */
function aliĝilo_tabel_jesne_ilo($nomo, $titolo, $jes_teksto, $defaŭlto)
{
	echo "<th><label for='$nomo'>" . $titolo . "</label></th>\n";
	echo "<td>\n";
    if (isset($_POST[$nomo])) {
        $val = $_POST[$nomo];
    }
    else {
        $val = $defaŭlto;
    }
    jes_ne_bokso($nomo, $val);
    echo($jes_teksto);
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

/**
 * kreas serion de kasxitaj input-elementoj por konservi la donitajn
 * valorojn.
 * @param array $listo array en la formo  nomo => valoro.
 *              valoro mem povas esti tia array, tiam ni rekurzive subeniras,
 *              kaj kreas tauxgan input-elementojn por rekrei la array-on.
 * @param string $prefikso komence aldonita al la nomoj en la listo, por krei
 *                          la nomojn uzendan por la intput-elementoj.
 * @param string $postfikso fine aldonita al la nomoj en la listo, por krei
 *                          la nomojn uzendan por la intput-elementoj.
 */
function aliĝilo_listu_donitaĵojn($listo, $prefikso="", $postfikso="")
{
    foreach($listo AS $nomo => $valoro)
	{
        if (substr($nomo, 0, 4) == 'iru_')
            continue;
        $tutanomo = $prefikso . $nomo . $postfikso;
        if (is_array($valoro)) {
            aliĝilo_listu_donitaĵojn($valoro, $tutanomo . "[", "]");
        }
        else {
            tenukasxe($tutanomo, $valoro);
        }
	}
}


function listu_paŝojn_kun_bildoj($aktuala_pasxo) {
	for($i = 1; $i <= $GLOBALS['aligxilopasxoj']; $i++) {
		if ($i < $aktuala_pasxo)
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
}

/**
 * 
 */
function listu_paŝojn_tekste($aktuala_paŝo) {
    for ($i = 1; $i < $aktuala_paŝo; $i++) {
        // inta
        echo "   <input class='inta_pasxo' type='submit' name='iru_al_pasxo_"
            .$i . "' value='" . CH('~#pasxo') . " " . $i . " – ".
            $GLOBALS['aligxilo_pasxonomoj'][$i] . "' />\n";
    }
    // anta
    echo "   <span class='aktuala_pasxo'>" .  CH('~#pasxo') . " " .
        $aktuala_paŝo . " – ".
        $GLOBALS['aligxilo_pasxonomoj'][$aktuala_paŝo] . "</span>\n";
    // aktuala
    for ($i = $aktuala_paŝo +1;
         $i <= count($GLOBALS['aligxilo_pasxonomoj']);
         $i++) {
        // onta
        echo "   <span class='onta_pasxo'>" . CH('~#pasxo') . " " . $i . " – ".
            $GLOBALS['aligxilo_pasxonomoj'][$i] . "</span>\n";
    }
}


/**
 * kreas la kapon de la aligxilo-pagxoj.
 * $pasxo - la numero de la aktuala pasxo
 *
 * Varianto por 2007 ff.
 *
 * @uses aliĝilo_listu_donitaĵojn()
 * @param string|int   $pasxo la aktuala paŝo-numero. Uzata kiel parametro
 *                          por {@link kontrolu.php}.
 * @param u8string     $titolo
 * @param array        $lingvoj elekteblaj alternativaj lingvoj
 * @param htmlstring   $aldona_kapo aldonaj linioj por la html-<head>-elemento.
 * @param asciistring  $metodo la metodo uzenda por la form-elemento, aŭ
 *                         'post' aŭ 'get'.
 */
function simpla_aliĝilo_komenco($pasxo, $titolo, $lingvoj="",
                                 $aldona_kapo="", $metodo='post')
{
	debug_echo( "<!-- Method: " . $_SERVER["REQUEST_METHOD"] . "-->");
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
           aliĝilo_metu_simplan_lingvoliston($lingvoj);
    ?><h1><?php echo $titolo; ?></h1></td>
        </tr>
        <tr>
          <td colspan="4" align="center">
<?php
     // bla
                                      ;
    if(isset($GLOBALS['aligxilo_pasxonomoj'])) {
        listu_paŝojn_tekste($pasxo);
    }
    else {
        listu_paŝojn_kun_bildoj($pasxo);
    }
?>
</td>
        </tr>
<?php

}


function deviga() {
    return aliĝilo_aldonu_piednoton(CH("~#deviga"), '*') . " ";
}


$GLOBALS['aligxilo_piednotoj'] = array();

$GLOBALS['piednoto_signoj'] = array("²", "³", "<sup>4</sup>",
                                    "<sup>5</sup>", "<sup>6</sup>",
                                    "<sup>7</sup>", "<sup>8</sup>",
                                    "<sup>9</sup>", "<sup>10</sup>",
                                    "<sup>11</sup>", "<sup>12</sup>");


/**
 * @param eostring $teksto por la piednoto
 * @return $teksto indiksigno por la piednoto
 */
function aliĝilo_aldonu_piednoton($teksto, $signo=null) {
    debug_echo("<!-- aldonu_piednoton(".$teksto . ", " .$signo . ")-->");
    $val =& $GLOBALS['aligxilo_piednotoj'][$teksto];
    if (!isset($val)) {
        if (isset($signo)) {
            $val = $signo;
            // TODO: forigi signo el piednoto_signoj
            $indekso = array_search($signo, $GLOBALS['piednoto_signoj']);
            if ($indekso !== false) {
                unset($GLOBALS['piednoto_signoj'][$indekso]);
            }
        }
        else {
            $val = array_shift($GLOBALS['piednoto_signoj']);
        }
    }
    return $val;
}


/**
 * Fino de aliĝilo
 *
 * Versio por 2007 ktp.
 */
function simpla_aliĝilo_fino($pasxo)
{
?>
	<tr>
      <td colspan='2' class='maldekstrabutono'>
<?php
	if($pasxo > 1)
	{
        echo ("   <input class='inta_pasxo' type='submit' ".
              "name='iru_al_pasxo_" . ($pasxo - 1) ."' value='" .
              CH("~#Reen") . "' />\n");
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
                                 ;
  if (marku_traduko_eo_anstatauxojn and $GLOBALS['bezonis-eo-tekston']) {
      aligxilo_aldonu_piednoton(CH("~#informo-pri-1"), "¹");
  }

  if (count($GLOBALS['aligxilo_piednotoj'])) {
      echo ("<table>\n   ");
      $listo = array();
      foreach($GLOBALS['aligxilo_piednotoj'] AS $nomo => $val) {
          eoecho("    <tr><td>" . $val. "</td><td>" . $nomo . "</td></tr>\n");
      }
//      eoecho(implode("  ", $listo));
      echo ("  </table>");
  }
    

?>
</body>
</html>
<?php
}


