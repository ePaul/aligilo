<?
    require_once("iloj.php");
    kontrolu_uzanton();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?= $tradukoj["tradukejo-titolo"] ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?
	$db = konektu();
	$tabelo = $agordoj["db_tabelo"];
	$chefa = $agordoj["chefa_lingvo"];
	$nombro_da_aldonoj = 0;
	$nombro_da_redaktoj = 0;
	$nombro_da_forigoj = 0;
	$nombro_da_eraroj = 0;
	
	while(list($nomo, $valoro) = each($HTTP_POST_VARS)) {
		if (substr($nomo, 0, 7) == "aldonu-") {
			$numero = substr($nomo, 7);
			$loka_dosiero = $HTTP_POST_VARS["dosiero-$numero"];
			$loka_cheno = $HTTP_POST_VARS["cheno-$numero"];
			$loka_iso2 = $HTTP_POST_VARS["iso2-$numero"];
			$loka_traduko = $HTTP_POST_VARS["traduko-$numero"];
			$loka_komento = $HTTP_POST_VARS["komento-$numero"];
			$query = "INSERT INTO $tabelo SET dosiero='$loka_dosiero', "
				. "cheno='$loka_cheno', iso2='$loka_iso2', traduko='$loka_traduko', tradukinto='{$_SERVER['PHP_AUTH_USER']}', "
				. "komento='$loka_komento'";
			$result = mysql_query($query);
			if ($result) $nombro_da_aldonoj++;
			else $nombro_da_eraroj++;
		}
		elseif (substr($nomo, 0, 8) == "redaktu-") {
            $numero = substr($nomo, 8);
			$loka_dosiero = $HTTP_POST_VARS["dosiero-$numero"];
			$loka_cheno = $HTTP_POST_VARS["cheno-$numero"];
			$loka_iso2 = $HTTP_POST_VARS["iso2-$numero"];
			$loka_traduko = $HTTP_POST_VARS["traduko-$numero"];
			$loka_komento = $HTTP_POST_VARS["komento-$numero"];
			$query = "UPDATE $tabelo SET traduko='$loka_traduko', tradukinto='{$_SERVER['PHP_AUTH_USER']}', komento='$loka_komento', stato=0 WHERE "
				. "dosiero='$loka_dosiero' AND cheno='$loka_cheno' AND iso2='$loka_iso2'";
			$result = mysql_query($query);
			if ($result) {
				$nombro_da_redaktoj++;
				if ($loka_iso2 == $chefa) {
					$query = "UPDATE $tabelo SET stato=1 WHERE dosiero='$loka_dosiero' AND cheno='$loka_cheno' AND iso2<>'$chefa'";
					$result = mysql_query($query);
					if (!$result) $nombro_da_eraroj++;
				}
			}
			else $nombro_da_eraroj++;
		}
		elseif (substr($nomo, 0, 7) == "forigu-") {
			$numero = substr($nomo, 7);
            $loka_dosiero = $HTTP_POST_VARS["dosiero-$numero"];
			$loka_cheno = $HTTP_POST_VARS["cheno-$numero"];
			$query = "DELETE FROM $tabelo WHERE dosiero='$loka_dosiero' "
				. "AND cheno='$loka_cheno'";
			$result = mysql_query($query);
			if ($result) $nombro_da_forigoj++;
			else $nombro_da_eraroj++;
		}
	}
?>
<h1><?= $tradukoj["sukceson"] ?></h1>
<p><?= $tradukoj["sukcese-konservighis"] ?> <?= $nombro_da_aldonoj ?> <?= $tradukoj["aldonoj"] ?>, <?= $nombro_da_redaktoj ?> <?= $tradukoj["redaktoj"] ?>, <?= $tradukoj["kaj"] ?> <?= $nombro_da_forigoj ?> <?= $tradukoj["forigoj"] ?>.</p>
<p><?= $tradukoj["okazis"] ?> <?= $nombro_da_eraroj ?> <?= $tradukoj["eraroj"] ?>.</p>
<?
	if (!$dosiero) $dosiero = $loka_dosiero;
?>
<p><a href="<?= substr($agordoj["dosierujo"], 0, -1) . $dosiero ?>?antaumontro=jes<?= $de_kie_venis == "redaktilo.php" ? "&lingvo=$lingvo" : "" ?>"><?= $tradukoj["vidu-tradukitan"] ?></a><br />
(Rim.: Ne funkcias por ĉiuj paĝoj.  Mi riparos tion baldaŭ. /Argilo)</p>
<p><a href="<?= $de_kie_venis ?>?dosiero=<?= $dosiero ?><?= $de_kie_venis == "redaktilo.php" ? "&lingvo=$lingvo&montru=$montru" : "" ?>"><?= $tradukoj["reredaktu"] ?></p>
<script type="text/javascript">
        parent.chenlisto.location = "chenlisto.php?lingvo=<?= $lingvo ?><?= $dosiero ? "&dosiero=$dosiero" : "" ?>&montru=<?= $montru ?>&random=" + Math.random();
</script>
</body>
</html>