<?php


/*
 * Akzeptado de partoprenantoj
 *
 *
 *  Pasxo 5: cxambroj
 *
 */




require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

kontrolu_rajton("akcepti");
require_once('iloj/iloj_akceptado.php');


  $partoprenanto = $_SESSION["partoprenanto"];
  $partopreno = $_SESSION['partopreno'];

  // la persona pronomo (li aux sxi)
  $ri = $partoprenanto->personapronomo;
  $Ri = ucfirst($ri);

akceptado_kapo("cxambro");

echo "<ul>";

  if ($_SESSION["partopreno"]->datoj[domotipo]=='J')
	{
        $sql_rez = eltrovu_cxambrojn($partopreno->datoj['ID']);
        switch (mysql_num_rows($sql_rez))
            {
            case 0:
                // ankoraux ne havas cxambron
                eoecho ("<li>$Ri bezonas c^ambron, sed tiu ankorau^ ne rezervig^is por li.");
                // TODO: elekti cxambron
                break;
            case 1:
                $linio = mysql_fetch_assoc($sql_rez);
                if ($linio['rezervtipo'] == 'r')
                    {
                        eoecho ("<li>${Ri} jam havas rezervitan c^ambron:<br />");
                    }
                else
                    {
                        eoecho("<li>{$Ri} jam havas disdonitan c^ambron:<br />");
                    }
                montru_cxambron($linio['cxambro'], $_SESSION['renkontigxo'],
                                $partoprenanto,$partopreno,
                                "malgranda");
                // TODO: butono por disdoni
                break;
            default:
                // pli ol unu cxambro
                eoecho("<li>$Ri s^ajne havas pli ol unu c^ambron. " .
                       "C^u tio vere necesas? (Se vi ne certas, " .
                       "demandu la respondeculon pri c^ambrodisdonado.)");
                while($linio = mysql_fetch_assoc($sql_res)) {
                    montru_cxambron($linio['cxambro'], $_SESSION['renkontigxo'], $partoprenanto,
                                    $partopreno,"malgranda");
                }
                echo "</li>\n";
            }

        
//         // TODO
// 	  $row = mysql_fetch_array(eltrovu_cxambrojn($_SESSION["partopreno"]->datoj[ID]),
// 							   MYSQL_NUM);
// 	  echo "<li>";
// 	  montru_cxambron($row[0],$_SESSION["renkontigxo"],
// 					 $partoprenanto,$_SESSION["partopreno"],"malgranda");
// 	  eoecho ("<br />Notu la c^ambronumero sur {$ri}a bros^uro</li>");
	}
  else
	{
        eoecho("<li>$Ri log^os en la memzorgantejo, do ne necesas prizorgi".
               " c^ambron por $ri.");

        $sql_rez = eltrovu_cxambrojn($partopreno->datoj['ID']);
        if (mysql_num_rows($sql_rez) > 0)
            {
                erareldono("<p>Hmm, s^ajne $ri tamen havas c^ambron. Tiel ne estu ...");
                while($linio = mysql_fetch_assoc($sql_res)) {
                    montru_cxambron($linio['cxambro']);
                }
                eoecho("Elj^etu {$ri}n el tiu c^ambro, au^ demandu".
                       " respondeculon pri tio.</p>");
            }
        echo "</li>\n";
	}

echo "</ul>\n<p>";
ligu_sekvan("C^ambroj en ordas.");

  /******** Disdono de diversajxoj *************/
HtmlFino();

?>
