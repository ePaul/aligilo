<?
    require_once("iloj.php");
    kontrolu_uzanton();
//    set_time_limit(0);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?= $tradukoj["tradukejo-titolo"] ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="tradukado.css" />
</head>
<body>
<h1><?= $tradukoj["ghisdatigo-titolo"] ?></h1>
<h2><?= $tradukoj["necesas-aldoni"] ?></h2>
<form method="post" action="konservu.php">
<?
	$db = konektu();
	$tabelo = $agordoj["db_tabelo"];
	$chefa = $agordoj["chefa_lingvo"];
	$trovitaj = array();
$cxef_dosierujo = substr($GLOBALS['DOCUMENT_ROOT'] . $agordoj["dosierujo"], 0, -1);

traktu_dosierujon($cxef_dosierujo); 

	function traktu_dosierujon($dosierujo) {
        //        echo "traktas " . $dosierujo . " ... <br />\n";
		global $agordoj;
		$dir = @opendir($dosierujo);
		while ($file = @readdir($dir))
		{
			if (($file == ".") or ($file == "..")) {
			} elseif (@is_dir($dosierujo . "/" . $file)) {
				traktu_dosierujon($dosierujo . "/" . $file);
			} else {
				$i = strrpos($file, ".");
				if ($i > 0 and in_array(substr($file, $i+1), $agordoj["sufiksoj"])) {
					traktu_dosieron($dosierujo . "/" . $file);
				}
			}
		}
	}

	function traktu_dosieron($dosiero) {
        //        echo "(traktas " . $dosiero . " ...) <br />\n";
		static $nombro_trovitaj;
		global $trovitaj, $tabelo, $chefa, $tradukoj;
		
		if (isset($_GET["parta"]) && (filemtime($dosiero) < time() - (60*60*24*7))) {
			return;
		}
		
		$tuto = join("", file($dosiero));
		preg_match_all("/CH(_lig|_lau|JS|_repl|_mult|)\s*\(\s*[\"']([^\"']*)[\"']\s*(,|\))/",
                       $tuto, $chenoj);
		$chenoj = $chenoj[2];
		for ($i = 0; $i < count($chenoj); $i++) {
			$cheno = $chenoj[$i];
	        if (substr($cheno, 0, 1) == "/") {
				$loka_dosiero = strtok($cheno, "#");
				$loka_cheno = strtok("#");
			} else {
                $baza_dos = substr($dosiero, strlen($GLOBALS['cxef_dosierujo']));
                $listo = explode('#', $cheno);
                if (count($listo) > 1)
                {
                    $loka_dosiero = substr($baza_dos, 0, strrpos($baza_dos, '/')+1)
                        . $listo[0];
                    $loka_cheno = $listo[1];
                }
            else
                {
                    $loka_dosiero = $baza_dos;
                    $loka_cheno = $cheno;
                }

			}
			if (!in_array($loka_dosiero . "#" . $loka_cheno, $trovitaj)) {
				$trovitaj[$nombro_trovitaj++] = $loka_dosiero . "#" . $loka_cheno;
				$query = "SELECT traduko FROM $tabelo WHERE "
					. "dosiero = '$loka_dosiero' AND cheno = '$loka_cheno' "
					. "AND iso2 = '$chefa'";
				$result = mysql_query($query);
				$row = mysql_fetch_array($result);
				if (!$row) {
					skatolo_por_cheno("aldonu", $tradukoj["stato-aldonenda"], "aldonenda", $loka_dosiero, 1, $loka_cheno, $chefa);
				}
			}
		}
	}
?>
<?
	if (!isset($_GET["parta"])) {
?>
<h2><?= $tradukoj["necesas-forigi"] ?></h2>
<?	
	$query = "SELECT dosiero, cheno, traduko FROM $tabelo WHERE iso2 = '$chefa'";
	$result = mysql_query($query);
	while ($row = mysql_fetch_array($result)) {
		if (!in_array($row["dosiero"] . "#" . $row["cheno"], $trovitaj)) {
			skatolo_por_cheno("forigu", $tradukoj["stato-forigenda"], "forigenda", $row["dosiero"], 1, $row["cheno"], $chefa, $row["traduko"]);
		}
	}
?>
<?
	}
?>
<p>
<input type="submit" name="Konservu" value="<?= $tradukoj["konservu-butono"] ?>" />
<!--input type="hidden" name="dosiero" value="<?= $dosiero ?>" /-->
<input type="hidden" name="de_kie_venis" value="ghisdatigu.php" />
<input type="hidden" name="lingvo" value="<?= $chefa ?>" />
<input type="hidden" name="montru" value="chion" />
</p>
</form>
</body>
</html>