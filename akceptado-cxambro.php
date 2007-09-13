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

// por povi reiri cxi tien post la elekto de cxambro.
$_SESSION['sekvontapagxo'] = 'akceptado-cxambro.php';

  // la persona pronomo (li aux sxi)
  $ri = $partoprenanto->personapronomo;
  $Ri = ucfirst($ri);

$en_ordo = false;

akceptado_kapo("cxambro");

echo "<ul>";

if ($partopreno->datoj['domotipo']=='J')
	{
        $sql_rez = eltrovu_cxambrojn($partopreno->datoj['ID']);
        switch (mysql_num_rows($sql_rez))
            {
            case 0:
                // ankoraux ne havas cxambron
                eoecho ("<li>$Ri bezonas c^ambron, sed tiu ankorau^ ne rezervig^is por li.");
                // TODO: elekti cxambron
                ligu('cxambroj.php', "Elektu c^ambron");
                
                echo "</li>";
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
                // TODO: butono por disdoni - au^ c^u ni tion faru au^tomate
                // je "akcepti!"?
                $en_ordo = true;
                break;
            default:
                // pli ol unu cxambro
                eoecho("<li>$Ri s^ajne havas pli ol unu liton. " .
                       "C^u tio vere necesas? (Se vi ne certas, " .
                       "demandu la respondeculon pri c^ambrodisdonado.)<div>");
                $montritaj_cxambroj = array();
                while($linio = mysql_fetch_assoc($sql_rez)) {
                    if (!in_array($linio['cxambro'], $montritaj_cxambroj)) {
                        echo "<div style='display: inline-block;'>";
                        montru_cxambron($linio['cxambro'],
                                        $_SESSION['renkontigxo'],
                                        $partoprenanto,
                                        $partopreno,
                                        "malgranda");
                        echo "</div>\n";
                        $montritaj_cxambroj []= $linio['cxambro'];
                    }
                }
                echo "</div>";
                eoecho("Se eblas, metu lin en nur unu liton.");
                echo "</li>\n";
            } // switch
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
        else
            {
                $en_ordo = true;
            }
        echo "</li>\n";
	}

echo "</ul>\n";

if ($en_ordo)
    {
        eoecho("<p>Lau^ mi, c^ambroj en ordas.</p>");
        ligu_sekvan("Bone.");
    }
else
    {
        eoecho("<p>S^ajne estas ankorau^ farendaj^oj pri la c^ambro.</p>");
        ligu_sekvan("Tamen, ");
    }

  /******** Disdono de diversajxoj *************/
HtmlFino();

?>
