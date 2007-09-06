<?php

require_once ('iloj/iloj.php');
require_once ('iloj/iloj_cxambroj.php');

session_start();
malfermu_datumaro();

kontrolu_rajton("teknikumi");

HtmlKapo();


echo "<!-- POST: ";
var_export($_POST);
echo "-->";

$renkontigxoID = $_SESSION['renkontigxo']->datoj['ID'];


if ("sxangxu" == $sendu)
{
  $cxambro = new Cxambro($id);
  if ($cxambro->datoj['renkontigxo'] != $renkontigxoID)
	{
	  eoecho ("<p>Ne eblas s^ang^i cxambrojn de alia renkontig^o.</p>");
	  exit();
	}
  $cxambro->kopiu();
  $cxambro->skribu();
  eoecho("<p>Mi s^ang^is c^ambron ");
  ligu ("cxambroj.php?cxambronumero=" . $cxambro->datoj['ID'], $cxambro->datoj['nomo']);
  eoecho (".</p>");
  unset($id);
}
else if ("kreu" == $sendu)
{

  $sql = datumbazdemando(array("count(*)" => "nombro"),
						 "cxambroj",
						 "nomo = '$nomo'",
						 "renkontigxo"
						 );
  echo "<!-- sql: $sql \n-->";
  $rez = sql_faru($sql);
  if ($linio = mysql_fetch_assoc($rez))
	{
	  if ($linio['nombro'] > 0)
		{
		  eoecho("Jam ekzistas c^ambro kun tiu nomo en tiu renkontig^o!");
		  exit();
		}
	}
  else
	{
	  echo ("Fehler!");
	  exit();
	}

  $cxambro = new Cxambro();
  $cxambro->kopiu();
  $cxambro->kreu();
  $cxambro->datoj['renkontigxo'] = $renkontigxoID;
  //  $cxambro->montru();
  $cxambro->skribu();

  eoecho ("<p>Mi kreis c^ambron ");
  ligu ("cxambroj.php?cxambronumero=" . $cxambro->datoj['ID'], $cxambro->datoj['nomo']);
  eoecho (".</p>");
  unset($id);
}

if ($id)
{
  $cxambro = new Cxambro($id);
}

if ($cxambro)
{
  if ($cxambro->datoj['renkontigxo'] == $renkontigxoID)
	{
	  eoecho("<h2>Redakto de c^ambro ". $cxambro->datoj['nomo']. "</h2>");
	  $redakto = true;
	}
  else
	{
	  eoecho("<h2>Kreo de nova c^ambro surbaze de " . $cxambro->datoj['nomo'] .
			 " el alia renkontig^o</h2>");
	}
}
else
{
  eoecho("<h2>Kreo de nova c^ambro</h2>");
}

echo "<form action='kreu_cxambron.php' method='post'>\n";

echo "<table>\n";
eoecho("<tr><th>ID</th><td>");
if ($redakto)
{
  eoecho ($cxambro->datoj['ID']);
  tenukasxe("id", $cxambro->datoj['ID']);
}
else
{
  eoecho ("<em>au^tomate disdonota</em>");
}
eoecho ("</td></tr>\n");
eoecho("<tr><th>renkontig^o</th><td>" . $_SESSION['renkontigxo']->datoj['nomo'] .
		"</td></tr>\n");

eoecho("<tr><th>nomo</th><td>");
entajpejo("", "nomo", $cxambro->datoj['nomo'], 10);
eoecho("</td></tr>");

eoecho("<tr><th>etag^o</th><td>");
entajpejo("", "etagxo", $cxambro->datoj['etagxo'], 50);
eoecho("</td></tr>");

eoecho("<tr><th>litonombro</th><td>");
entajpejo("", "litonombro", $cxambro->datoj['litonombro'], 5);
eoecho("</td></tr>");

eoecho("<tr><th>rimarkoj</th><td>");
entajpejo("", "rimarkoj", $cxambro->datoj['rimarkoj'], 30);
eoecho("</td></tr>");

echo "</table>\n";

if ($redakto)
{
  butono("kreu", "Kreu novan (kun alia ID)");
  butono("sxangxu", "S^ang^u ekzistantan");
}
else
{
  butono("kreu", "Kreu");
}

echo "</form>\n";



?>