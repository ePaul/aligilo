<?php

  /**
   * Redaktilo por la individuaj tekstoj en
   * la teksto-tabelo (kaj por krei novajn tiajn).
   */


require_once ('iloj/iloj.php');

session_start();
malfermu_datumaro();

Htmlkapo();

kontrolu_rajton("teknikumi");

if ($_POST['sendu'] == "aldonu")
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
	  ligu("nova_teksto.php?id=" . $linio['id'],
           "pluredaktu la originalan mesag^on");
      eoecho(".)");
      //	  require('nova_teksto.php');
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
 else if ($_POST['sendu'] == 'sxangxu')
     {
         // TODO
     }
 else if ($_GET['id'])
     {

         echo "<!-- " . var_export($_GET, true) . "-->";

         $sql = datumbazdemando(array('renkontigxoID', 'mesagxoID', 'teksto'),
                                'tekstoj',
                                "ID = '{$_GET['id']}'");
         $rez = sql_faru($sql);
         switch (mysql_num_rows($rez))
             {
             case 0:
                 // eraro, mankas datumbazero
                 darf_nicht_sein();
                 break;
             case 1:
                 // en ordo
                 break;
             default:
                 darf_nicht_sein();
             }
         
         $linio = mysql_fetch_assoc($rez);
         if ($linio['renkontigxoID'] != $_SESSION['renkontigxo']->datoj['ID'])
             {
                 $_SESSION['renkontigxo'] =
                     new Renkontigxo($linio['renkontigxoID']);
             }

         // redakto de ekzistanta noto
         eoecho("<h2>Redakto de ekzistanta teksto</h2>");
         eoecho("<p>Vi nun s^ang^os tekston de la renkontig^o " . $_SESSION['renkontigxo']->datoj['mallongigo'] . " (#" . $_SESSION['renkontigxo']->datoj['ID'] . ").</p>");

        $id_postt =
        "(Kutime ne necesas s^ang^i tiun - faru tion nur," .
        " se vi scias, ke kaj kial necesas.)";

        $_REQUEST['mesagxoID'] = $linio['mesagxoID'];
        $_REQUEST['teksto'] = $linio['teksto'];


     }
else
    {
eoecho("<h2>Aldono de nova teksto</h2>");
eoecho("<p>Vi nun aldonos tekston al la renkontig^o " . $_SESSION['renkontigxo']->datoj['mallongigo'] . " (#" . $_SESSION['renkontigxo']->datoj['ID'] . ").</p>");


        $id_postt = "";
        $linio = "";

    }
?>
<form action='nova_teksto.php' method='POST'>
<table class='tekstoj-redaktilo'>
<?php


tabelentajpejo("Identifikilo", 'mesagxoID', $_REQUEST['mesagxoID'], 30, $id_postt);
granda_tabelentajpejo("Teksto", 'teksto', $_REQUEST['teksto'], '60', '10');

echo "</table>";

if ($_GET['id'])
    {
        butono('sxangxu', 'S^ang^u');
    }
else
    {
        butono("aldonu", "Aldonu");
    }

ligu("tekstoj.php", "Reen al la teksto-listo");
ligu("renkontigxo.php", "Reen al la renkontigxo");
ligu("administrado.php", "Reen al la grava administrado");

echo "</form>";



HtmlFino();

?>