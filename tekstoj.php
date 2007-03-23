<?php

/**
 * Montrilo kaj redaktilo por la tekstoj de iu renkontigxo.
 */

require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();

Htmlkapo();

kontrolu_rajton("teknikumi");

  $renk = ($_SESSION['renkontigxo']->datoj);


if($_POST['sendu'] == 'sxangxu')
{
  eoecho( "Nun s^ang^ig^is la jenaj renkontig^aj datoj:");

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
            // donu mesagxon, kio sxangxigxis:
            eoecho("<p>Mi s^ang^is la tekston '" . $mID .
                   "' (#" . $linio['ID'] . ") de:</p><pre>"
                   . $linio['teksto'] . "</pre><p>al:</p><pre>"
                   . $valoro . "</pre>");
		  }
	  }
  }
  ligu("administrado.php", "Reen al la Administrado.");
  ligu("renkontigxo.php", "Reen al la renkontig^o");
  ligu("tekstoj.php", "Reen al la teksto-listo");
  HtmlFino();
  return;
}

if ($_POST['sendu'] == 'redaktu' or $_POST['sendu'] == 'redaktu_cxiujn')
{

    echo "<!-- POST: " . var_export($_POST, true) . "-->";

    // ebleco de redakto de pluraj samtempe.

  eoecho("

<form action='tekstoj.php' method='post'>


  <h2>Pluraj teksts^ang^oj</h2>
  <p>
    La <em>tekstoj</em> estas uzataj ekzemple por
    havi retmesag^tekstojn kaj similajn aferojn, kiuj varias lau^ renkontig^o,
    ne en la programo, sed en la datumbazo. Pri la signifoj legu en 
    ");
  // TODO: movu la dokumentadon al pli tauxga loko
  ligu("http://www.esperanto.de/dej/vikio/IS-Datenbazo/Tekstoj", "la vikipag^o", "_top");
  eoecho("
    pri tiu temo. La tekstoj estu en esperanta &#99;^-kodigo.
  </p>
  <table class='tekstoj-redaktilo'>");


  $sql = datumbazdemando(array('mesagxoID', 'id', 'teksto'),
						 'tekstoj',
						 "renkontigxoID = '{$renk['ID']}'");
  $rez = sql_faru($sql);
  while($linio = mysql_fetch_assoc($rez))
	{
        echo "<!-- id = " . $linio['id'] . ", redaktu[...] = '"
            . $_POST['redaktu'][$linio['id']] . "' \n -->";
        // ni montru nur tiujn, kiujn la teknikisto
        // elektis por redakti.
        if ($_POST['sendu'] == 'redaktu_cxiujn' or
            $_POST['redaktu'][$linio['id']])
            {
                echo "
    <tr><th>". $linio['mesagxoID'] . "</th>
      <td><textarea rows='20' cols='50' name='mesagxo_{$linio['mesagxoID']}'>" .
                    $linio['teksto'] . "</textarea>
      </td>
    </tr>";
            }
	}

  echo "
  </table>
";
  butono("sxangxu", "S^ang^u c^iujn tekstojn");
  ligu("tekstoj.php", "Reen al la tekstoj-pag^o.");
  ligu("renkontigxo.php", "Reen al la renkontig^o");
  ligu("administrado.php", "Reen al la administrado-pag^o.");
  echo "
</form>
";
}
else
    {
        // ############################################################

        // nur montru cxiujn tekstojn (kun redakto-ligo).

        eoecho("
<form action='tekstoj.php' method='post'>


  <h2>Tekstoj</h2>
  <p>
    La <em>tekstoj</em> estas uzataj ekzemple por
    havi retmesag^tekstojn kaj similajn aferojn, kiuj varias lau^ renkontig^o,
    ne en la programo, sed en la datumbazo. Pri la signifoj legu en 
    ");
  // TODO: movu la dokumentadon al pli tauxga loko
  ligu("http://www.esperanto.de/dej/vikio/IS-Datenbazo/Tekstoj", "la vikipag^o", "_top");
  eoecho("
    pri tiu temo. La tekstoj estu en esperanta &#99;^-kodigo.
  </p>
  <ul><li>Vi povos redakti unuopan tekston (inkluzive la identifikilon) per la <code>red</code>-ligo.</li>
   <li>Alternative vi povos elekti kelkajn tekstojn (meti hokon) kaj uzi la butonon <em>Redaktu la merkitajn tekstojn</em> sube.</li>
   <li>Vi ec^ povos <em>redakti c^iujn tekstojn</em> per samnoma butono.</li>
  </ul>
<table>");
  echo "<tr><td colspan='4'>";
  ligu("nova_teksto.php", "kreu novan tekston");
  echo "</td></tr>\n";

  $sql = datumbazdemando(array('id', 'mesagxoID', 'teksto'),
						 'tekstoj',
						 "renkontigxoID = '{$renk['ID']}'");
  $rez = sql_faru($sql);
  while($linio = mysql_fetch_assoc($rez))
	{
        eoecho ("
    <tr><th>". $linio['mesagxoID'] . "</th>
      <td><input type='checkbox' name='redaktu[" . $linio['id'] .
                "]' value='true' /><br/>");
        ligu('nova_teksto.php?id=' . $linio['id'], "red.");
        eoecho("</td><td><p style='white-space: pre; white-space: pre-wrap'>" .
		$linio['teksto'] . "</pre>
      </td>
    </tr>");
	}



  echo "</table>";

  butono('redaktu', "Redaktu la markitajn tekstojn");
  butono('redaktu_cxiujn', "Redaktu c^iujn tekstojn");
  ligu("renkontigxo.php", "Reen al la renkontig^o");
  ligu("administrado.php", "Reen al la grava administrado");

echo "</form>";

    }




HtmlFino();

if (DEBUG)
    {
  echo "<!--SESSION: ";
  var_export($_SESSION);
  echo "-->";
    }

?>