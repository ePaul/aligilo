<?
    require_once("iloj.php");
    kontrolu_uzanton();

    $chefa = $agordoj["chefa_lingvo"];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title><?= $tradukoj["tradukejo-titolo"] ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <style>
	p.informo {
		color: blue;
	}
	
	p.eraro {
		color: red;
	}
	div.dekstra {
		float: right;
		width: 40%;
		border: solid 1px rgb(0, 0, 255);
		background-color: rgb(230, 230, 255);		
		padding: 5px;
	}
  </style>
</head>
<body>
<div class="dekstra">
<!--
<p>Jen <a href="http://groups.yahoo.com/group/ecxei-membroj/">dissendolisto</a> por membroj, tradukantoj kaj testantoj.  Vi povas aliĝi per la suba formularo.</p>
<form method="get" action="http://groups.yahoo.com/subscribe/ecxei-membroj">
<table>
<tr><td>Via retpoŝto:</td><td><input type="text" name="user" value="" size="20" /></td></tr>
<tr><td></td><td><input type=submit name="abonu" value="Abonu!" /></td></tr>
</table>
</form>
-->
<p>Ni dankas al la teamo de <a href='http://ikso.net/'>ikso.net</a>
pro la provizo de la tradukilo, ĉefe al <em>Argilo</em>, kiu
        programis ĝin,
        kaj <em>Jevgenij</em>, kiu donis ĝin al ni.
        </p>

<h2>Konsiloj por tradukantoj</h2>
<p>
   (Ĉi tie vi trovos kelkajn informojn pri la tradukado. -- Paŭlo)
</p>

<ul>
<li>Aktuale cxefe gravas traduki la tekstojn en 2007.</li>
<li>Se vi elektis vian lingvon, vi povos per "Montru" elekti vidi nur
        tradukendajn tekstojn (kie mankas vialingva traduko),
        retradukendajn tekstojn (kie la esperanta originalo ŝangxiĝis
                                 lastatempe (= post la lasta ŝanĝo de
                                             la vialingva versio))
    aŭ ambaŭ - tio helpas tralabori ĉion.</li>
<li></li>
</ul>


</div>

<h1><?= $tradukoj["tradukejo-titolo"] ?></h1>
<p><?= $tradukoj["bonveniga-mesagho"] ?></p>
<?
	$db = konektu();
	if (!$db) {
?>
<p class="eraro"><?= $tradukoj["ne-konektis"] ?></p>
<?
	} else {
	    $tabelo = $agordoj["db_tabelo"];
		$query = "SELECT iso2, COUNT(cheno) AS nombro FROM $tabelo GROUP BY iso2 ORDER BY nombro DESC";
		$result = mysql_query($query);
		
		if (!$result) {
?>
<?=$agordoj["db_tabelo"]?> 
<?=$agordoj["db_uzanto"]?> 
<?=$agordoj["db_pasvorto"]?>

<p class="informo"><?= $tradukoj["nova-tabelo"] ?></p>
<?
			$query2 = <<<SQL
CREATE TABLE $tabelo (dosiero VARCHAR(100) NOT NULL,
cheno VARCHAR(255) NOT NULL,
iso2 CHAR(5) NOT NULL,
traduko TEXT NOT NULL,
tradukinto VARCHAR(255),
komento TEXT NOT NULL,
stato INT NOT NULL,
kontrolita INT NOT NULL,
kontrolinto VARCHAR(255),
dato TIMESTAMP NOT NULL,
PRIMARY KEY(dosiero, cheno, iso2),
KEY di (dosiero,iso2),
KEY iso2 (iso2),
KEY `is` (iso2,stato))
SQL;
			$rezulto2 = mysql_query($query2);
			if ($rezulto2) {
?>
<p class="informo"><?= $tradukoj["kreis-tabelon"] ?></p>
<?
			} else {
?>
<p class="eraro"><?= $tradukoj["ne-kreis-tabelon"] ?></p>
<?
			}
		} else {
?>
<p><?= $tradukoj["elektu-lingvon"] ?></p>

<ul>
<li><a href="redaktejo.php?lingvo=<?= $chefa ?>"><?= $trad_lingvoj[$chefa] ?> (<?= $chefa ?>)</a></li>
</ul>
<ul>
<?
			$query2 = "SELECT COUNT(cheno) AS nombro FROM $tabelo "
				. "WHERE iso2 = '$chefa'";
			$result2 = mysql_query($query2);
			$row2 = mysql_fetch_array($result2);
			$sumo = $row2["nombro"];
			
			while ($row = mysql_fetch_array($result)) {
				if ($row["iso2"] != $chefa) {
					$lingvo = $row["iso2"];
					$query3 = "SELECT COUNT(a.cheno) AS nombro FROM $tabelo = a, "
						. "$tabelo = b WHERE a.iso2 = '$chefa' AND b.iso2 = '$lingvo' "
						. "AND a.dosiero = b.dosiero AND a.cheno = b.cheno";
					$result3 = mysql_query($query3);
					$row3 = mysql_fetch_array($result3);
					$nombro = $row3["nombro"];
?>

<li><a href="redaktejo.php?lingvo=<?= $row["iso2"] ?>"><?= $trad_lingvoj[$row["iso2"]] ?> (<?= $row["iso2"] ?>)</a> - <?= number_format(round($nombro * 100 / $sumo, 1), 1, ",", "") ?>%</li>

<?
				}
			}
?>
</ul>
<form method="post" action="redaktejo.php">
<p><?= $tradukoj["aldonu-lingvon"] ?> 
<select name="lingvo">
<option value=""><?= $tradukoj["elektu-lingvon-menuero"] ?></option>
<?
			while (list($iso2, $nomo) = each($trad_lingvoj)) {
?>
<option value="<?= $iso2 ?>"><?= $nomo ?> (<?= $iso2 ?>)</option>
<?
			}
?>
</select>
<input type="submit" name="ek" value="<?= $tradukoj["ek-butono"] ?>" /></p>
</form>
<?
		}
	}
?>
</body>
</html>