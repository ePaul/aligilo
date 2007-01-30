<?php

/**
 * Montrilo kaj redaktilo por la bazaj informoj de
 * cxiu renkontigxo, ankaux por krei novan renkontigxon.
 */

require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();

Htmlkapo();

kontrolu_rajton("teknikumi");

if ($sendu == "aldonu")
{
  eoecho ("<h2>Aldono de Teksto</h2>");
  // unue ni rigardas, cxu teksto kun sama identifikilo jam estas en la datumbazo
  $sql = datumbazdemando(array('id', 'teksto'),
						 "tekstoj",
						 "mesagxoID = '" . $mesagxoID . "'",
						 "renkontigxoID"
						 );

  $rez = sql_faru($sql);
  if (mysql_num_rows($rez) > 0)
	{
	  // se jes, ni eldonas erarmesagxon kaj ebligas novan provon.
	  $linio = mysql_fetch_assoc($rez);
	  erareldono("Jam ekzistas mesag^o kun tia identifikilo en la aktuala renkontig^o:");
	  echo ("<pre>" . $linio['teksto'] . "</pre>");
	  eoecho("<p>Bonvolu elekti alian identifikilon (au^ ");
	  ligu("renkontigxo.php", "reiru al la renkontig^o");
	  eoecho (" kaj tie pluredaktu la originalan mesag^on.)");
	  require('nova_teksto.php');
	  return;
	}

  // Alikaze ni aldonas la novan tekston al la datumbazo.

  aldonu_al_datumbazo('tekstoj',
					  array('renkontigxoID' => $_SESSION['renkontigxo']->datoj['ID'],
							'mesagxoID' => $mesagxoID,
							'teksto' => $teksto));

  eoecho( "<p>Aldonis la sekvan tekston kun identifikilo '$mesagxoID' al la renkontig^o '" .
		  $_SESSION['renkontigxo']->datoj['mallongigo'] . "' (#" .
		  $_SESSION['renkontigxo']->datoj['ID'] . "):</p>");
  echo ("<pre>" . $teksto . "</pre>");
  
}

if($sendu == "kreu")
{
  eoecho ("Nun kreig^us nova renkontig^o (ne jam implementita).");
  echo "<pre>";
  var_export($_POST);
  echo "</pre>";
  HtmlFino();
  return;
}
else if($sendu == 'sxangxu')
{
  eoecho( "Nun s^ang^ig^is renkontig^o.");

  $_SESSION['renkontigxo']->kopiu();
  $_SESSION['renkontigxo']->skribu();
  $_SESSION['renkontigxo'] = new Renkontigxo($_SESSION['renkontigxo']->datoj['ID']);

  foreach($_POST AS $nomo => $valoro) {
	if(substr($nomo, 0, 8) == 'mesagxo_')
	  {
		$mID = substr($nomo, 8);
		$sql = datumbazdemando(array("ID", "teksto"),
							   "tekstoj",
							   array("mesagxoID = '$mID'"),
							   "renkontigxoID");
		$rez = sql_faru($sql);
		$linio = mysql_fetch_assoc($rez);
		if ($linio and
			$linio['teksto'] != $valoro)
		  {
			sxangxu_datumbazon("tekstoj",
							   array("teksto" => $valoro),
							   array("ID" => $linio['ID']));
		  }
	  }
  }
  ligu("administrado.php", "Reen al la Administrado.");
  HtmlFino();
  return;
}

{
  echo "<!-- \$_SESSION = ";
  var_export($_SESSION);
  echo "-->\n";


  $renk = ($_SESSION['renkontigxo']->datoj);

  echo "<!-- \$_SESSION = ";
  var_export($_SESSION);
  echo "-->\n";
  
  eoecho("<h2>Redaktilo por la aktuala renkontig^o</h2>\n");
  eoecho( "
<form action='renkontigxo.php' method='post'>
  <h3>G^eneralaj Informoj</h2>
  <table>
   <tr><th>ID</th><td>{$renk['ID']}</td></tr>
");
  tabelentajpejo("Oficiala nomo", "nomo", $renk['nomo'], 50,
				 "Ekzemplo: <em>49a Internacia Seminario</em>.");
  tabelentajpejo("Mallongigo", "mallongigo", $renk['mallongigo'], 10,
				 "La mallongigo estas uzata nur interne de la programo, ekzemple" .
				 " en la maldekstra menuo. Ekzemplo estas <em>IS 2005</em>.");
  tabelentajpejo("Temo", "temo", $renk['temo'], 50,
				 "La oficiala temo de la renkontig^o. Ekzemple".
				 " <em>Religioj, kulturoj kaj vivfilosofioj en la 21a jarcento</em>");
  tabelentajpejo("Loko", "loko", $renk['loko'], 50);
  eoecho("
  </table>

  <h3>Kotizo-informoj</h3>
  <table>
");
  tabelentajpejo("De", "de", $renk['de'], 10,
				 "Unua tago de la renkontig^o, en internacia formato (ISO 8601).".
				 " Ekzemplo: <em>2005-12-27</em>");
  tabelentajpejo("G^is", "gxis", $renk['gxis'], 10,
				 "Lasta tago de la renkontig^o, en internacia formato (ISO 8601).".
				 " Ekzemplo: <em>2006-01-03</em>");
  tabelentajpejo("Unua kategorio", 'plej_frue', $renk['plej_frue'], 10,
				 "Unua tago, kiu ne plu estas en la unua alig^kategorio. C^iuj alig^oj".
				 " antau^ tiu tago estas ankorau en la plej malmultkosta alig^kategorio.".
				 " Ekzemplo: <em>2005-11-01</em>");
  tabelentajpejo("Dua kategorio", 'meze', $renk['meze'], 10,
				 "Unua tago, kiu ne plu estas en la dua alig^kategorio. C^iuj alig^oj".
				 " ekde tiu tago estas en la plej kosta alig^kategorio (C^e IS nun 20 E^".
				 " pli ol la antau^aj alig^oj. Ekzemplo: <em>2005-12-20</em>");
  tabelentajpejo("Divido por parttempuloj", 'parttemppartoprendivido',
				 $renk['parttemppartoprendivido'], 3,
				 "Divisoro por kalkuli ka kotizon de parttempaj partoprenantoj." .
				 " La kotizo estas" .
				 " <var>tuttempa kotizo</var>*<var>partoprentagoj</var>/<var>divisoro</var>" .
				 " do normale la divisoro estas iom malpli ol la nombro de tagoj entute." .
				 " Ekzemplo: <em>6</em>");
  tabelentajpejo("Juna", 'juna', $renk['juna'], 10,
				 "Maksimuma ag^o por la unua ag^kategorio. Tiu valoro nun por IS ne estas" .
				 "uzata, c^ar ni nun havas pli ol nur tri kategorioj. Ekzemplo: <em>20</em>.");
  tabelentajpejo("Maljuna", 'maljuna', $renk['maljuna'], 10,
				 "Maksimuma ag^o por la dua ag^kategorio. Tiu valoro nun por IS ne estas" .
				 "uzata, c^ar ni nun havas pli ol nur tri kategorioj. Ekzemplo: <em>26</em>.");
  eoecho("
  </table>

  <h3>Responduloj</h3>
  <p>
    Jen pluraj respondeculoj &mdash; kaj nomo kaj kontakta retadreso.
    Tiuj informoj estas uzataj por sendi diversajn au^tomatajn mesag^ojn
    al tiuj.
  </p>
  <table>
");
  tabelentajpejo("Admin-respondulo", "adminrespondeculo", $renk['adminrespondeculo'],
				 20, "");
  tabelentajpejo("Admin-retadreso", "adminretadreso", $renk['adminretadreso'], 30,
				 "Ekzemplo: <em>is.admin@esperanto.de</em>");
  tabelentajpejo("Sekurkopioj-retadreso", "sekurkopiojretadreso", $renk['sekurkopiojretadreso'], 30,
				 "Ekzemplo: <em>is.sekurkopioj@esperanto.de</em>");


  tabelentajpejo("Invitletero-respondulo", "invitleterorespondeculo",
				 $renk['invitleterorespondeculo'],  20,
				 "");
  tabelentajpejo("Invitletero-retadreso", "invitleteroretadreso",
				 $renk['invitleteroretadreso'], 30,
				 "Ekzemplo: <em>is.invit@esperanto.de</em>");

  tabelentajpejo("Tema respondulo", 'temarespondulo',
				 $renk['temarespondulo'],  20,
				 "");
  tabelentajpejo("Tema retadreso", "temaretadreso",
				 $renk['temaretadreso'], 30,
				 "Ekzemplo: <em>is.tema@esperanto.de</em>");

  tabelentajpejo("Distra respondulo", "distrarespondulo",
				 $renk['distrarespondulo'],  20,
				 "");
  tabelentajpejo("Distra retadreso", "distraretadreso",
				 $renk['distraretadreso'], 30,
				 "Ekzemplo: <em>is.distra@esperanto.de</em>");

  tabelentajpejo("Vespera respondulo", "vesperarespondulo",
				 $renk['vesperarespondulo'],  20,
				 "");
  tabelentajpejo("Vespera retadreso", "vesperaretadreso",
				 $renk['vesperaretadreso'], 30,
				 "Ekzemplo: <em>is.vespera@esperanto.de</em>");

  tabelentajpejo("Muzika respondulo", "muzikarespondulo",
				 $renk['muzikarespondulo'],  20,
				 "");
  tabelentajpejo("Muzika retadreso", "muzikaretadreso",
				 $renk['muzikaretadreso'], 30,
				 "Ekzemplo: <em>is.muzika@esperanto.de</em>");

  tabelentajpejo("Nokta respondulo", "noktarespondulo",
				 $renk['noktarespondulo'],  20,
				 "");
  tabelentajpejo("Nokta retadreso", "noktaretadreso",
				 $renk['noktaretadreso'], 30,
				 "Ekzemplo: <em>is.nokta@esperanto.de</em>");

  tabelentajpejo("Novula/Junula respondulo", "novularespondulo",
				 $renk['novularespondulo'],  20,
				 "");
  tabelentajpejo("Novula retadreso", "novularetadreso",
				 $renk['novularetadreso'], 30,
				 "Ekzemplo: <em>is.novula@esperanto.de</em>");

  eoecho("
  </table>

  <h3>Tekstoj</h3>
  <p>
    La <em>tekstoj</em> estas uzataj ekzemple por
    havi retmesag^tekstojn kaj similajn aferojn, kiuj varias lau^ renkontig^o,
    ne en la programo sed en la datumbazo. Pri la signifoj legu en 
    ");
  ligu("http://www.esperanto.de/dej/vikio/IS-Datenbazo/Tekstoj", "la vikipag^o", "_top");
  eoecho("
    pri tiu temo. La tekstoj estu en esperanta &#99;^-kodigo.
  </p>
  <table class='tekstoj-redaktilo'>");

  echo "<tr><td colspan='2'>";
  ligu("nova_teksto.php", "kreu novan tekston");
  echo "</td></tr>\n";

  $sql = datumbazdemando(array('mesagxoID', 'teksto'),
						 'tekstoj',
						 "renkontigxoID = '{$renk['ID']}'");
  $rez = sql_faru($sql);
  while($linio = mysql_fetch_assoc($rez))
	{
	  echo "
    <tr><th>". $linio['mesagxoID'] . "</th>
      <td><textarea rows='20' cols='50' name='mesagxo_{$linio['mesagxoID']}'>" .
		$linio['teksto'] . "</textarea>
      </td>
    </tr>";
	}

  echo "
  </table>
";
  butono("sxangxu", "S^ang^u tiun renkontig^on");
  butono("kreu", "Kreu novan renkontig^on");
  ligu("administrado.php", "Reen al la administrado-pag^o.");
  echo "
</form>
";
}

HtmlFino();

  echo "<!--";
  var_export($_SESSION);
  echo "-->";


?>