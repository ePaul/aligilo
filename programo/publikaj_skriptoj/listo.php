<?php

/**************************************
 *
 * Listo de partoprenantoj por la retpagxo
 *
 * Tiu cxi dosiero estos inkluzivigota de
 * la gxusta dosiero en /is/dulingva/.../.
 * Tiu metas la variablon $renkontigxoid al
 * la numero de la renkontigxo, kies
 * aligxinto-listo estu montrata.
 *
 ***************************************** */

$prafix='./../admin/';
require_once ($prafix . "iloj/iloj.php");
require_once ($prafix . "iloj/formulareroj.php");

// session_start();
$_SESSION["enkodo"]="unikodo";


malfermu_datumaro();

$_SESSION['renkontigxo'] = new Renkontigxo($renkontigxoid);



$sql_listo = datumbazdemando(array("COUNT(pn.ID)" => "nombro"),
					   array("partoprenantoj" => "p", "partoprenoj" => "pn"),
					   array("p.ID = pn.partoprenantoID",
							 "pn.alvenstato <> 'm'",
							 "pn.listo = 'J'"),
					   "pn.renkontigxoID"
					   );
echo "<!-- $sql_listo -->";
$rez_listo = sql_faru($sql_listo);
$linio = mysql_fetch_assoc($rez_listo);
$nombro_listo = $linio['nombro'];

$sql_listo = datumbazdemando(array("COUNT(pn.ID)" => "nombro"),
					   array("partoprenantoj" => "p", "partoprenoj" => "pn"),
					   array("p.ID = pn.partoprenantoID",
							 "pn.alvenstato <> 'm'"),
					   "pn.renkontigxoID"
					   );
echo "<!-- $sql_listo -->";
$rez_listo = sql_faru($sql_listo);
$linio = mysql_fetch_assoc($rez_listo);
$nombro_cxiuj = $linio['nombro'];


t(<<<ENDE
  <h1>{$_SESSION['renkontigxo']->datoj['nomo']} in {$_SESSION['renkontigxo']->datoj['loko']}
  &mdash; Liste der Angemeldeten</h1>

  <p>
  Es haben sich bis jetzt {$nombro_cxiuj} Teilnehmer angemeldet,
  von denen {$nombro_listo} in der Liste erscheinen wollen.
  </p>
ENDE
, <<<FINO
  <h1>{$_SESSION['renkontigxo']->datoj['nomo']} in {$_SESSION['renkontigxo']->datoj['loko']}
  &mdash; Listo de ali&#285;intoj</h1>

  <p>
  &#284;is nun ali&#285;is {$nombro_cxiuj} partoprenantoj, el kiuj
  {$nombro_listo} volas aperi en la Listo.
  </p>
FINO
  );

t(<<<ENDE
  <p>Es gibt au&szlig;erdem noch eine <a href="http://groups.yahoo.com/group/is-en-germanio/">Yahoo&shy;group is-en-germanio</a>, um mit anderen Teilnehmern Kontakt aufzunehmen. Wir laden dich hiermit ein, dort teilzunehmen.
  </p>
ENDE
  , <<<FINO
  <p>Ekzistas anka&#365;
    <a href="http://groups.yahoo.com/group/is-en-germanio/">jahu&shy;grupo is-en-germanio</a>
  por kontakti aliajn IS-partoprenontojn. Mi invitas vin partopreni tie.
  </p>
FINO
  );

granda_kesto_komenco();

echo '
<table id="partoprenantolisto">
<tr>
';
geoecho('  <th colspan="2">', 'Name / ', " nomo</th>\n");
// geoecho("  <th>", "Familienname / ", "familia nomo</th>\n");
geoecho("  <th>", "Land / ", "lando</th>\n");
geoecho("  <th>", "Stadt / ", "urbo</th>\n");
echo "</tr>\n";

$sql = datumbazdemando(array("p.personanomo" => 'persona', "p.nomo" => 'fam',
							 "p.urbo" => 'urbo', "p.sxildlando" => 'sxildo',
							 "l.nomo" => 'lando_eo', "l.lokanomo" => 'lando_de'),
					   array("partoprenantoj" => "p", "partoprenoj" => "pn",
							 "landoj" => "l"),
					   array("p.ID = pn.partoprenantoID",
							 "alvenstato <> 'm'",
							 "p.lando = l.ID",
							 "pn.listo = 'J'"),
					   "renkontigxoID",
					   array("order" => "p.personanomo ASC, p.nomo ASC")
					   )
	 ;
{}
$rez = sql_faru($sql);
while($linio = mysql_fetch_array($rez))
{
  echo "<tr>\n";
  eoecho( "  <td style='text-align:right;' class='green'>" . $linio['persona'] . "</td>\n");
  if ($linio['fam']{1} == '^')
	{
	  $fam = substr($linio['fam'], 0,2);
	}
  else
	{
	  $fam = $linio['fam']{0};
	}
  eoecho( "  <td class='green'>" . $fam . ".</td>\n");
  if ($linio['sxildo'])
	{
	  eoecho( "  <td class='green'>" . $linio['sxildo'] . "</td>\n");
	}
  else
	{
	  geoecho ("  <td class='green'>",
			   $linio['lando_de'] . " / " , 
			   $linio['lando_eo'] . "</td>\n");
	}
  eoecho( "  <td class='green'>" . $linio['urbo'] . "</td>\n");
  echo "</tr>\n";
}
echo "</table>\n";


granda_kesto_fino();


?>