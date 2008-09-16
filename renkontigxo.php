<?php

/**
 * Montrilo kaj redaktilo por la bazaj informoj de
 * la aktuala renkontiĝo, ankaŭ por krei novan renkontiĝon
 * (la lasta ankoraŭ ne tute funkcias).
 *
 * @author Martin Sawitzki, Paul Ebermann
 * @version $Id$
 * @package aligilo
 * @subpackage pagxoj
 * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 * @todo ebligu krei novan renkontiĝon respektive tutan kopion de malnova,
 *       inkluzive de tekstoj kaj aliaj renkontiĝo-rilataj aferoj
 *       (bug #13765).
 */


  /**
   */
require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();

Htmlkapo();

kontrolu_rajton("teknikumi");


if($sendu == "kreu")
{

    // TODO!: ankaŭ kopiu la tekstojn.

  echo "<pre>";
  var_export($_POST);
  echo "</pre>";
  
  $ren = new Renkontigxo(0);
  $ren->kopiu();
  $ren->skribu_kreante();

  eoecho ("<p>Nun kreig^is nova renkontig^o kun ID '" . $ren->datoj['ID'] . "'</p>");
  $ren->montru();
  HtmlFino();
  return;
}
else if($sendu == 'sxangxu')
{
  eoecho( "Nun s^ang^ig^is renkontig^o.");

  $_SESSION['renkontigxo']->kopiu();
  $_SESSION['renkontigxo']->skribu();
  $_SESSION['renkontigxo'] = new Renkontigxo($_SESSION['renkontigxo']->datoj['ID']);

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
  <p>La informoj krom la elekto de la kotizosistemo ne plu estas uzataj. 
     Rigardu la pag^on pri ");
  ligu("kotizoj.php",
       "Kotizosistemoj");

  eoecho(" por fari s^ang^ojn, kiuj efikas ion.</p>
  <table>
");

  tabela_elektilo_db("Kotizosistemo", 'kotizosistemo',
                     "kotizosistemoj", "nomo", "ID",
                     $renk['kotizosistemo'], "",
                     "Kotizosistemo por uzi por tiu c^i renkontig^o.");

  tabelentajpejo("De", "de", $renk['de'], 10,
				 "Unua tago de la renkontig^o, en internacia formato".
                 " (ISO 8601). Ekzemplo: <em>2005-12-27</em>");
  tabelentajpejo("G^is", "gxis", $renk['gxis'], 10,
				 "Lasta tago de la renkontig^o, en internacia formato ".
                 "(ISO 8601). Ekzemplo: <em>2006-01-03</em>");
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
				 "Maksimuma ag^o por la unua ag^kategorio. Tiu valoro nun por IS ne estas " .
				 "uzata, c^ar ni nun havas pli ol nur tri kategorioj. Ekzemplo: <em>20</em>.");
  tabelentajpejo("Maljuna", 'maljuna', $renk['maljuna'], 10,
				 "Maksimuma ag^o por la dua ag^kategorio. Tiu valoro nun por IS ne estas " .
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
  tabelentajpejo("Admin-retadreso", "adminretadreso",
                 $renk['adminretadreso'], 30);
  tabelentajpejo("Sekurkopioj-retadreso", "sekurkopiojretadreso",
                 $renk['sekurkopiojretadreso'], 30);


  tabelentajpejo("Invitletero-respondulo", "invitleterorespondeculo",
				 $renk['invitleterorespondeculo'],  20,
				 "");
  tabelentajpejo("Invitletero-retadreso", "invitleteroretadreso",
				 $renk['invitleteroretadreso'], 30);

  tabelentajpejo("Tema respondulo", 'temarespondulo',
				 $renk['temarespondulo'],  20,
				 "");
  tabelentajpejo("Tema retadreso", "temaretadreso",
				 $renk['temaretadreso'], 30);

  tabelentajpejo("Distra respondulo", "distrarespondulo",
				 $renk['distrarespondulo'],  20,
				 "");
  tabelentajpejo("Distra retadreso", "distraretadreso",
				 $renk['distraretadreso'], 30);

  tabelentajpejo("Vespera respondulo", "vesperarespondulo",
				 $renk['vesperarespondulo'],  20,
				 "");
  tabelentajpejo("Vespera retadreso", "vesperaretadreso",
				 $renk['vesperaretadreso'], 30);

  tabelentajpejo("Muzika respondulo", "muzikarespondulo",
				 $renk['muzikarespondulo'],  20,
				 "");
  tabelentajpejo("Muzika retadreso", "muzikaretadreso",
				 $renk['muzikaretadreso'], 30);

  tabelentajpejo("Nokta respondulo", "noktarespondulo",
				 $renk['noktarespondulo'],  20,
				 "");
  tabelentajpejo("Nokta retadreso", "noktaretadreso",
				 $renk['noktaretadreso'], 30);

  tabelentajpejo("Novula/Junula respondulo", "novularespondulo",
				 $renk['novularespondulo'],  20,
				 "");
  tabelentajpejo("Novula retadreso", "novularetadreso",
				 $renk['novularetadreso'], 30);

  eoecho("
  </table>
");

  butono("sxangxu", "S^ang^u tiun renkontig^on");
  butono("kreu", "Kreu novan renkontig^on");


  eoecho ("
</form>

  <hr/>

  <h3 id='tekstoj'>Tekstoj</h3>
  <p>
    La <em>tekstoj</em> estas uzataj ekzemple por
    havi retmesag^tekst(er)ojn kaj similajn aferojn, kiuj varias
    lau^ renkontig^o,
    ne en la programo sed en la datumbazo.
  </p>");

  $sql = datumbazdemando(array('count(*)' => 'nombro'),
						 'tekstoj',
						 "renkontigxoID = '{$renk['ID']}'");
  $rez = sql_faru($sql);
  $linio = mysql_fetch_assoc($rez);

  eoecho ("
<p>
   Nuntempe ekzistas " . $linio['nombro'] . " tekstoj por la aktuala
   renkontig^o.
</p> <p>");

  ligu("tekstoj.php", "Vidu la liston (kaj eble redaktu kelkajn)");
  
  echo "<br/>";
  ligu("nova_teksto.php", "Aldonu novan tekston");

  echo "</p><p>";

  ligu("administrado.php", "Reen al la administrado-pag^o.");
  echo "</p>";
}

HtmlFino();

  echo "<!--";
  var_export($_SESSION);
  echo "-->";


?>