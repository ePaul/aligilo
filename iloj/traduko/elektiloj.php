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
<style>
body {
	margin: 10px;
	padding: 0;
}
</style>
<script type="text/javascript">
	function updateDisplay(select) {
		for (i = 0; i < select.options.length; i++) {
			if (select.options[i].selected) {
				var temp = parent.chenlisto.location.href;
                // elektu tekston gxis la antauxlasta '='
				var temp = temp.substr(0, temp.lastIndexOf("=", temp.lastIndexOf('=') - 1) + 1);
				parent.chenlisto.location =  temp + select.options[i].value
                + "&random=" + Math.random();

				var temp = parent.basefrm.location.href;
				var temp = temp.substr(0, temp.lastIndexOf("=") + 1);
				parent.basefrm.location = temp + select.options[i].value;
			}
		}
	}
</script>
</head>
<body>
<h1><?= $tradukoj["redaktejo-titolo"] ?></h1>
<p><?= $tradukoj["lingvo"] ?> <b><?= $trad_lingvoj[$lingvo] ?> (<?= $lingvo ?>)</b><br />
<a href="index.php" onclick="window.top.location=this.href; return false;"><?= $tradukoj["elektu-alian-lingvon"] ?></a></p>
<?
    if ($lingvo == $agordoj["chefa_lingvo"]) {
?>
<p><a href="ghisdatigu.php" onclick="parent.basefrm.location=this.href; return false;"><?= $tradukoj["ghisdatigu-1"] ?></a> <?= $tradukoj["ghisdatigu-2"] ?></p>
<p><a href="ghisdatigu.php?parta" onclick="parent.basefrm.location=this.href; return false;"><?= $tradukoj["ghisdatigu-3"] ?></a> <?= $tradukoj["ghisdatigu-4"] ?></p>
<p><a href="chenlisto.php?lingvo=<?= $lingvo ?><?= $dosiero ? "&dosiero=$dosiero" : "" ?>&montru=chion" target="chenlisto"><?= $tradukoj["revoku-chenliston"] ?></p>
<?
	} else {
?>
<form>
<p><?= $tradukoj["montru"] ?> 
<select name="montru" onchange="updateDisplay(this);">
<option value="tradukendajn"><?= $tradukoj["nur-tradukendajn"] ?></option>
<option value="retradukendajn"><?= $tradukoj["nur-retradukendajn"] ?></option>
<option value="ambau"><?= $tradukoj["tradukendajn-kaj-retraukendajn"] ?></option>
<option value="chion" selected="selected"><?= $tradukoj["chion"] ?></option>
</select>
</p>
</form>
<?
	}
?>
</body>
</html>
