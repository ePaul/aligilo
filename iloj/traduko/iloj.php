<?
	// Pretigu $agordoj kaj $trad_lingvoj.
	require_once($DOCUMENT_ROOT . ($DOCUMENT_ROOT ? "/" : "") . "is/tradukado/agordoj.php");
if (!function_exists("konektu")) {
	eval('
    function konektu()
    {
        global $agordoj;
    	static $result;
    	if ($result) return $result;

        $result = mysql_pconnect($agordoj["db_host"], $agordoj["db_uzanto"], $agordoj["db_pasvorto"]);
        if (!$result)
            return false;
        if (!mysql_select_db($agordoj["db_nomo"]))
            return false;
        mysql_query("set names utf8"); 
        return $result;
    }
	');
}
	    
if (!function_exists("al_utf8")) {
	eval('
    function al_utf8($cxeno)
	{
		$cxeno = str_replace("CX", "Ĉ", $cxeno);
        $cxeno = str_replace("GX", "Ĝ", $cxeno);
        $cxeno = str_replace("HX", "Ĥ", $cxeno);
        $cxeno = str_replace("JX", "Ĵ", $cxeno);
        $cxeno = str_replace("SX", "Ŝ", $cxeno);
        $cxeno = str_replace("UX", "Ŭ", $cxeno);

        $cxeno = str_replace("Cx", "Ĉ", $cxeno);
        $cxeno = str_replace("Gx", "Ĝ", $cxeno);
        $cxeno = str_replace("Hx", "Ĥ", $cxeno);
        $cxeno = str_replace("Jx", "Ĵ", $cxeno);
        $cxeno = str_replace("Sx", "Ŝ", $cxeno);
        $cxeno = str_replace("Ux", "Ŭ", $cxeno);

        $cxeno = str_replace("cx", "ĉ", $cxeno);
        $cxeno = str_replace("gx", "ĝ", $cxeno);
        $cxeno = str_replace("hx", "ĥ", $cxeno);
        $cxeno = str_replace("jx", "ĵ", $cxeno);
        $cxeno = str_replace("sx", "ŝ", $cxeno);
        $cxeno = str_replace("ux", "ŭ", $cxeno);

        $cxeno = str_replace("C\'x", "Cx", $cxeno);
        $cxeno = str_replace("G\'x", "Gx", $cxeno);
        $cxeno = str_replace("H\'x", "Hx", $cxeno);
        $cxeno = str_replace("J\'x", "Jx", $cxeno);
        $cxeno = str_replace("S\'x", "Sx", $cxeno);
        $cxeno = str_replace("U\'x", "Ux", $cxeno);

        $cxeno = str_replace("c\'x", "cx", $cxeno);
        $cxeno = str_replace("g\'x", "gx", $cxeno);
        $cxeno = str_replace("h\'x", "hx", $cxeno);
        $cxeno = str_replace("j\'x", "jx", $cxeno);
        $cxeno = str_replace("s\'x", "sx", $cxeno);
        $cxeno = str_replace("u\'x", "ux", $cxeno);

		return $cxeno;
	}
	');
}

	function alghustigu_dosiernomon($dosiero) {
		global $agordoj;
		if(preg_match( ':/[^/.]+$:', $dosiero)) $dosiero .= ".php";
	    if (substr($dosiero, 0, strlen($agordoj["dosierujo"])) == $agordoj["dosierujo"]) {
		return substr($dosiero, strlen($agordoj["dosierujo"]) - 1);
		} else {
			return $dosiero;
		}
	}
	
	function listigu_chiujn_lingvojn() {
		global $agordoj;
	    $db = konektu();
		$tabelo = $agordoj["db_tabelo"];
		$query = "SELECT DISTINCT iso2 FROM $tabelo";
		$result = mysql_query($query);
		
		$arejo = array();
		while ($row = mysql_fetch_array($result)) {
			array_push($arejo, $row["iso2"]);
		}
		
		return $arejo;
	}
	
	function petu_ensaluton($mesagxo, $petu_denove = 1) {
		global $tradukoj;
		if ($petu_denove) {
			header('WWW-Authenticate: Basic realm="lernu!"');
			header('HTTP/1.0 401 Unauthorized');
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?= $tradukoj["tradukejo-titolo"] ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<h1><?= $tradukoj["tradukejo-titolo"] ?></h1>
<p><?= $mesagxo ?></p>
</body>
</html>
<?
		exit;
	}
	
	function kontrolu_uzanton() {
		global $agordoj;
		if ($agordoj["salutado"]) {
			if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SESSION['informoj'])) {
				petu_ensaluton("Por ensaluti, bonvolu uzi la samajn salutnomon kaj pasvorton, kiujn vi uzas por ensaluti ĉe <i>lernu!</i>.");
			} else {
				$db = konektu();
				$query = "SELECT * FROM lernu_uzanto WHERE salutnomo = '{$_SERVER['PHP_AUTH_USER']}'";
				$result = mysql_query($query);
				if (mysql_num_rows($result) > 0) {
					$row = mysql_fetch_array($result);
					if ($_SERVER['PHP_AUTH_PW'] != $row["pasvorto"]) {
						petu_ensaluton("Vi entajpis nevalidan salutnomon aŭ pasvorton.  Por ensaluti, bonvolu uzi la samajn salutnomon kaj pasvorton, kiujn vi uzas por ensaluti ĉe <i>lernu!</i>.");
					} else {
						if ($row["tradukanto"] != "jes") {
							petu_ensaluton("Vi ne havas tradukrajton.  Por fariĝi tradukanto, bonvolu skribi al tradukado@lernu.net.", 0);
						} 
						 session_start();
                         $_SESSION["informoj"] = $row;

					}
				} else {
					petu_ensaluton("Vi entajpis nevalidan salutnomon aŭ pasvorton.  Por ensaluti, bonvolu uzi la samajn salutnomon kaj pasvorton, kiujn vi uzas por ensaluti ĉe <i>lernu!</i>.");
				}
			}
		}
	}

	function skatolo_por_cheno($ordono, $stato, $class, $dosiero, $montru_dosieron, $cheno, $lingvo, $originalo = "", $traduko = "", $komento = "", $tradukinto = "") {
		global $tradukoj, $agordoj;
		static $nombrilo = 0;
		$nombrilo++;
		if ($ordono == "aldonu") $ordono_teksto = $tradukoj["aldonu-ordono"];
		elseif ($ordono == "redaktu") $ordono_teksto = $tradukoj["redaktu-ordono"];
		elseif ($ordono == "forigu") $ordono_teksto = $tradukoj["forigu-ordono"];
?>

<table class="<?= $class ?>">
<tr>
<td><input type="hidden" name="dosiero-<?= $nombrilo ?>" value="<?= $dosiero ?>" />
<input type="hidden" name="cheno-<?= $nombrilo ?>" value="<?= $cheno ?>" />
<input type="hidden" name="iso2-<?= $nombrilo ?>" value="<?= $lingvo ?>" />
<?= $tradukoj["cheno"] ?> <?= $montru_dosieron ? $dosiero . "#" : "" ?><?= $cheno ?></td>
<td align="right" valign="top"><?= $tradukoj["stato"] ?>&nbsp;<span class="<?= $class ?>"><?= $stato ?></span></td>
</tr>
<tr>
<td><?= $tradukoj["chefa-lingvo"] ?> <b><?= al_utf8(htmlspecialchars($originalo)) ?></b></td>
<td align="right" valign="bottom"><?= $ordono_teksto ?>:&nbsp;<input type="checkbox" name="<?= $ordono ?>-<?= $nombrilo ?>" value="jes" onclick="a = document.getElementById('traduko-<?= $nombrilo ?>'); if (a) a.disabled = !this.checked; a = document.getElementById('komento-<?= $nombrilo ?>'); if (a) a.disabled = !this.checked;" /><br /></td>
</tr>
<?
		if ($ordono != "forigu") {
			if (strlen($traduko) == 0) {
				$vicoj = 4;
			} else {
				$vicoj = 2 + ((int) strlen($traduko) / 50);
			}
?>
<tr><td colspan="2"><textarea id="traduko-<?= $nombrilo ?>" name="traduko-<?= $nombrilo ?>" cols="60" rows="<?= $vicoj ?>" disabled="disabled"><?= htmlspecialchars($traduko) ?></textarea></td></tr>
<?
			if ($lingvo == $agordoj["chefa_lingvo"]) {
?>
<tr><td colspan="2"><?= $tradukoj["komento"] ?></td></tr>
<tr><td colspan="2"><textarea id="komento-<?= $nombrilo ?>" name="komento-<?= $nombrilo ?>" cols="60" rows="2" disabled="disabled"><?= htmlspecialchars($komento) ?></textarea></td></tr>
<?
			} else {
				if (strlen($komento) > 0) {
?>
<tr><td colspan="2"><?= $tradukoj["komento"] ?> <b><?= al_utf8($komento) ?></b></td></tr>
<?
				}
			}
			
			if ($tradukinto) {
?>
<tr><td colspan="2"><?= $tradukoj["tradukinto"] ?> <b><?= $tradukinto ?></b></td></tr>
<?
			}
		}
?>
</table>

<?
	}
?>