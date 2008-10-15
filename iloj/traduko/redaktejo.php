<?

/**
 * Kadraro por la redaktado de tradukoj.
 *
 *
 * @author Paul Ebermann (lastaj ŝanĝoj) + teamo E@I (ikso.net)
 * @version $Id$
 * @package aligilo
 * @subpackage tradukilo
 * @copyright 2005-2008 Paul Ebermann, ?-2005 E@I-teamo
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */

/**
 */

	require_once("iloj.php");
    kontrolu_uzanton();
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
		<frame src="chenlisto.php?lingvo=<?= $lingvo ?>&amp;montru=chion&amp;random=<?= rand()/getrandmax() ?>" name="chenlisto" />
	</frameset>
	<frame src="redaktilo.php?lingvo=<?= $lingvo ?>&amp;montru=chion" name="basefrm" />
</frameset>

</html>


