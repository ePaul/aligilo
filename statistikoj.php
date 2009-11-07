<?php

/* ############################# 
 * Montras kelkajn statistikojn 
 * ############################# 
 *
 *
 * Gxi montras, kiom da homoj venos/venis/malaligxis/...
 * el kiuj landoj (ordigita unufoje laux nomo, unufoje
 * laux nombro).
 *
 * TODO: ebligu facile elekti kombinojn de alvenstatoj,
 *       ekzemple a+i aux n+m.
 */

require_once ("iloj/iloj.php");
session_start();

malfermu_datumaro();


kontrolu_rajton('statistikumi');

HtmlKapo();
{


  echo "<p>";
  ligu("cxambrostatistiko.php",
       "Mang^- kaj c^ambrostatistiko");

  echo "</p>";

  eoecho("<h1>Landostatistiko</h1>");


  if (!empty($_REQUEST['alvenstato']))
	{
	  $alvenstato = $_REQUEST['alvenstato'];
	}
  else
	{
	  if (kalkulu_tagojn(date('y-m-d'), $_SESSION['renkontigxo']->datoj['de']) > 0)
		$alvenstato = 'v'; //  v = venos
	  else
		$alvenstato = 'a'; // a = akceptigxis
	}
  
  
  echo "<table style='border-collapse:separate; border-spacing: 1em 1ex;'> <tr valign='top' ><td align='center'>\n";
  
  $klarigo = "La nombroj de homoj el diversaj landoj kun alvenstato <strong>" .
      $GLOBALS['alvenstatonomoj'][$alvenstato] . "</strong>:";



  eoecho ($klarigo. "<br />(ordigitaj lau^ nombro)\n");

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

  
  echo "</TD><TD align=center>\n";

  eoecho ($klarigo. "<br />(ordigita lau^ nomo)\n");

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

  sql_farukajmontru($sql);

  echo "</TD><TD align=center>\n";

  echo "</TD></TR><tr><td colspan='2' align='center'>";

  // TODO: uzu tabelon sen "nombro:" - aux enmetu la sumo-kalkuladon tuj
  // en la supran tabelon.

  sql_farukajmontru(datumbazdemando(array("'Sumo:'", "count(*)" => "c"),
									array("partoprenoj" => "p",
										  "partoprenantoj" => "e"),
									array("p.partoprenantoID = e.id",
										  "alvenstato = '$alvenstato'"),
									"renkontigxoID"
									));

  echo "</TD></TR><tr><td colspan='2' align='center'>";

  foreach($GLOBALS['alvenstatonomoj'] AS $id => $nomo)
      {
          if ($id == $alvenstato)
              {
                  eoecho(" <strong>" . $nomo . "</strong>");
              }
          else
              {
                  ligu("statistikoj.php?alvenstato=" . $id, $nomo);
              }
      }
  echo "</td></tr></TABLE>\n";


}



HtmlFino();

?>
