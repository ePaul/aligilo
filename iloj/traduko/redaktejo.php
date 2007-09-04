<?
	require_once("iloj.php");
    kontrolu_uzanton();
	$dosiero = alghustigu_dosiernomon($dosiero);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?= $tradukoj["tradukejo-titolo"] ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<frameset cols="250, *">
	<frameset rows="200, *">
		<frame src="elektiloj.php?lingvo=<?= $lingvo ?>" name="elektiloj" />
		<frame src="chenlisto.php?lingvo=<?= $lingvo ?><?= $dosiero ? "&dosiero=$dosiero" : "" ?>&montru=chion&random=<?= rand()/getrandmax() ?>" name="chenlisto" />
	</frameset>
	<frame src="redaktilo.php?lingvo=<?= $lingvo ?><?= $dosiero ? "&dosiero=$dosiero" : "" ?>&montru=chion" name="basefrm" />
</frameset>

</html>


