<?php

/* ############################# 
 * Montras kelkajn statistikojn 
 * ############################# 
 *
 *
 * Gxi montras, kiom da homoj venas el kiuj
 * landoj (ordigita unufoje laux nomo, unufoje
 * laux nombro).
 */

require_once ("iloj/iloj.php");
session_start();

malfermu_datumaro();

if (DEBUG == TRUE)
{
  echo "<!--\n \$_SESSION: ";
  var_export($_SESSION);
  echo "\n \$_SESSION['kkren']: ";
  var_export($_SESSION["kkren"]);
  echo "\n  \$_SESSION['beispiel']: ";
  $_SESSION["beispiel"] = "test";
  var_export($_SESSION);
  echo "\n-->";
}

kontrolu_rajton('statistikumi');

HtmlKapo();
{

  if ($_REQUEST['alvenstato'])
	{
	  $alvenstato = $_REQUEST['alvenstato'];
	}
  else
	{
	  if (kalkulu_tagojn(date('y-m-d'), $_SESSION['renkontigxo']->datoj['de']) > 0)
		$alvenstato = 'v'; //  v = venos
	  else
		$alvenstato = 'a'; // a = alvenis
	}
  
  
  echo "<table> <tr valign='top' ><td align='center'>\n";
  
  if ($alvenstato == "a")
	{
	  $klarigo = "akceptig^is";
	}
  else if ($alvenstato == "v")
	{
	  $klarigo = "alig^is, kaj ankorau^ ne akceptig^is";
	}
  else if ($alvenstato == 'm')
	{
	  $klarigo = "alig^is, sed jam malalig^is";
	}

  eoecho ("La nombroj de homoj el diversaj landoj, kiuj g^is nun {$klarigo}: <br />(ordigitaj lau^ nombro)\n");

  //    $sql  = "Select l.nomo, count(*) as c ";
  //    $sql .= "from landoj as l, partoprenoj as p, partoprenantoj as e ";
  //    $sql .= "where p.partoprenantoID = e.id and e.lando=l.id and alvenstato='v' and renkontigxoID=".$_SESSION["renkontigxo"]->datoj[ID];
  //    $sql .= " group by lando order by c DESC";


  $sql = datumbazdemando(array("l.nomo", "count(*)" => "c"),
						 array("landoj" => "l", "partoprenoj" => "p",
							   "partoprenantoj" => "e"),
						 array("p.partoprenantoID = e.ID",
							   "e.lando = l.id",
							   "p.alvenstato = '$alvenstato'"),
						 "p.renkontigxoID",
						 array("group" => "lando",
							   "order" => "c DESC"));

  sql_farukajmontru($sql);
  
  // "select count(*) as c from partoprenoj as p, partoprenantoj as e where p.partoprenantoID=e.id and alvenstato='v' and renkontigxoID=".$_SESSION["renkontigxo"]->datoj[ID]
  sql_farukajmontru(datumbazdemando(array("'Sumo:'", "count(*)" => "c"),
									array("partoprenoj" => "p",
										  "partoprenantoj" => "e"),
									array("p.partoprenantoID = e.id",
										  "alvenstato = '$alvenstato'"),
									"renkontigxoID"
									));
  
  echo "</TD><TD align=center>\n";

  eoecho ("La nombroj de homoj el diversaj landoj, kiuj g^is nun {$klarigo}: <br />(ordigita lau^ nomo)\n");

  //    $sql  = "Select l.nomo, count(*) as c ";
  //    $sql .= "from landoj as l, partoprenoj as p, partoprenantoj as e ";
  //    $sql .= "where p.partoprenantoID = e.id and e.lando=l.id and alvenstato='v' and renkontigxoID=".$_SESSION["renkontigxo"]->datoj[ID];
  //    $sql .= " group by e.lando order by l.nomo ASC";

  $sql = datumbazdemando(array("l.nomo", "count(*)" => "c"),
						 array("landoj" => "l", "partoprenoj" => "p",
							   "partoprenantoj" => "e"),
						 array("p.partoprenantoID = e.ID",
							   "e.lando = l.id",
							   "alvenstato = '$alvenstato'"),
						 "renkontigxoID",
						 array("group" => "e.lando",
							   "order" => "l.nomo ASC"));

  // TODO: uzu tabelon sen "sumo".
  sql_farukajmontru($sql);

  echo "</TD><TD align=center>\n";

  echo "</TD></TR><tr><td colspan='2'>";
  ligu("statistikoj.php?alvenstato=a", "alvenintoj");
  ligu("statistikoj.php?alvenstato=v", "venontoj");
  ligu("statistikoj.php?alvenstato=m", "malalig^is");
  echo "</td></tr></TABLE>\n";

}

HtmlFino();

?>
