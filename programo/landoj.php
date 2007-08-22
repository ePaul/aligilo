<?php

/*
 * Administrado de la landoj.
 *
 */

//define("DEBUG", TRUE);
require_once ("iloj/iloj.php");
session_start();

malfermu_datumaro();

kontrolu_rajton("administri");

HtmlKapo();


// echo "<!--\n";
// var_export($rajtolisto);
// echo "-->\n";

if($forigu)
{
  if($vere == 'jes')
	{
	  forigu_el_datumbazo("landoj", $forigu);
	  eoecho("<p>Vi j^us forigis la landon #".$forigu.".</p>");
	}
  else
	{
	  eoecho("<h1>Forigo de lando</h1>\n");
	  $sql = datumbazdemando('*',
							 'landoj',
							 "ID = '$forigu'");
	  $rez = sql_faru($sql);
	  $linio = mysql_fetch_assoc($rez);
	  
	  echo "<table>\n";
	  eoecho("<tr><th>ID</th><td>{$linio['ID']}</td></tr>\n");
	  eoecho("<tr><th>Nomo</th><td>{$linio['nomo']}</td></tr>\n");
	  eoecho("<tr><th>Loka nomo</th><td>{$linio['lokanomo']}</td></tr>\n");
	  eoecho("<tr><th>Kategorio</th><td>{$linio['kategorio']}</td></tr>\n");
	  eoecho("</table>\n");

      $sql = datumbazdemando(array('ID', 'nomo', 'personanomo'),
                             'partoprenantoj',
                             "lando = '$forigu'");
      $rez = sql_faru($sql);
      $num = mysql_num_rows($rez);

      if ($num > 0)
          {
              eoecho("<p>Estas ankorau^ " . $num . " partoprenantoj el tiu lando. Bonvolu unue forigu ilin au^ s^ang^u ilian landanecon, se vi vere volas forigi la supre menciitan landon.</p>");

              eoecho("<table>");
              while ($linio = mysql_fetch_assoc($rez))
                  {
                      echo "<tr><td><a href='partrezultoj.php?partoprenantoidento=" . $linio['ID'] . "'>--></a></td><td>";
                      eoecho( $linio['personanomo'] . "</td><td>" . $linio['nomo'] . "</td></tr>\n");
                  }
              eoecho("</table>");

              echo "<p>";
              ligu("landoj.php?redaktu=$forigu", "Reen");
              echo "</p>";

          }
      else
          {
              eoecho("<p>C^u vi vere volas forigi tiun c^i landon?");
              ligu("landoj.php?forigu=$forigu&vere=jes", "Jes");
              ligu("landoj.php?redaktu=$forigu", "Ne");
          }
	  HtmlFino();
	  return;
	}
}

if ($sendu)
{

//   echo "<pre>";
//   var_export($_POST);
//   echo "</pre>";

  $sxangxlisto = array();
  foreach(array("nomo", "lokanomo", "kategorio") AS $tipo)
	{
	  if ($_POST[$tipo])
		{
		  $sxangxlisto[$tipo] = $_POST[$tipo];
		}
	}

  if($_POST['ID'] == 'nova')
	{
	  aldonu_al_datumbazo("landoj", $sxangxlisto);
	  $num = mysql_insert_id();
	  eoecho ("<p>Mi aldonis linion #" . $num . " al la tabelo.</p>");
      $redaktu = $num;
	}
  else
	{
	  sxangxu_datumbazon("landoj",
						 $sxangxlisto,
						 array("ID" => $_POST['ID']));
	  eoecho ("<p>Mi s^ang^is linion #" . $_POST['ID'] . " en la tabelo.</p>");
	}
}


if($_REQUEST['redaktu'])
{

  eoecho("<h1>Redakto de lando</h1>");
  echo "<form method='POST' action='landoj.php'>\n";

  if ($redaktu == 'nova')
	{
	  $linio = array("ID" => 'nova');
	  eoecho("<p> Ni aldonas novan landon\n");
	}
  else
	{
        $sql = datumbazdemando('*',
                               'landoj',
                               "ID = '$redaktu'");
	  
	  $rez = sql_faru($sql);
	  $linio = mysql_fetch_assoc($rez);
	  
	  eoecho("<p>ID: {$linio['ID']}\n" );
	}
  tenukasxe("ID", $linio['ID']);
  echo("<br/>\n<table>");
  
  tabelentajpejo("esperantlingva nomo:", "nomo", $linio['nomo'], 20);
  tabelentajpejo("lokalingva nomo:", "lokanomo", $linio['lokanomo'], 20);
  entajpbutono("<tr><th>Landokategorio:</th><td>", 'kategorio', $linio['kategorio'],
               'A', 'A', "A ");
  entajpbutono(" | ", 'kategorio', $linio['kategorio'],
               'B', 'B', "B ");
  entajpbutono("| ", 'kategorio', $linio['kategorio'],
               'C', 'C', "C", "kutima");
  echo "</td></tr>\n</table><br/>\n";
  eoecho (" ... en la datumbazon.</p>");

  //  echo "<br/>\n";
  if ($id == 'nova')
      {
          send_butono("S^ang^u");
      }
  else
      {
          send_butono("Aldonu");
      }
  entajpbokso("<p>(", "redaktu", "", "jes", $linio['ID'],
			  "Pluredaktu tiun c^i landon.)", "", "sen kasxa");
  ligu("landoj.php", "Reen al la listo");
  if($redaktu != "nova")
	ligu("landoj.php?forigu=$redaktu", "Forigu tiun c^i landon!");
  echo "</p>";
  echo "</form>\n";

  HtmlFino();
  return;
}



// montru tabelon de cxiuj landoj

$sql = datumbazdemando(array("ID", "nomo", "lokanomo", "kategorio"),
					   "landoj");

sercxu($sql,
	   array("nomo", "asc"),
	   array(/* kolumnoj */
			 array('ID', '', 'red.','z', 'landoj.php?redaktu=XXXXX', -1),
			 array('ID', 'ID', 'XXXXX','z', '', ''),
			 array('nomo', 'nomo', 'XXXXX', 'l','',''),
			 array('lokanomo', 'loka nomo', 'XXXXX', 'l','',''),
             array('kategorio', 'kategorio', 'XXXXX', 'c', '', ''),
			 ),
	   array(/*sumoj*/),
	   "landoj",
	   array(/* pliaj parametroj */
			 ),
	   0 /* formato de la tabelo */,
	   "Jen listo de c^iuj landoj.", 0, 0);

ligu("landoj.php?redaktu=nova", "Aldonu novan landon");

HtmlFino();


?>