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


$sql_rez = eltrovu_cxambrojn($partopreno->datoj['ID']);
$nombro = mysql_num_rows($sql_rez);
if ($partopreno->datoj['domotipo']=='J')
	{
        switch ($nombro)
            {
            case 0:
                akceptada_instrukcio("Elektu c^ambron por $ri.");
                // ankoraux ne havas cxambron
                
                break;
            case 1:
                $en_ordo = true;
                break;
            default:
                // pli ol unu cxambro
                akceptada_instrukcio("$Ri havas pli ol unu liton. Se eblas, ".
                                     "metu {$ri}n en nur unu liton dum la ".
                                     "tuta tempo. " .
                                     "(Se vi ne certas pri tio, demandu la" .
                                     " c^efadministranton.)");
            } // switch
	}
  else
	{
        if ($num > 0)
            {
                akceptada_instrukcio("$Ri ne havu c^ambron. Elj^etu {$ri}n".
                                     " el tiu c^ambro, au^ ".
                                     "demandu respondeculon pri tio.");
            }
        else
            {
                $en_ordo = true;
            }
	}


if ($en_ordo)
    {
        akceptada_instrukcio("Lau^ mi, c^ambroj en ordas.");
        ligu_sekvan("Bone.");
    }
else
    {
        ligu_sekvan("Ne, mi ne volas. ");
    }

akceptado_kesto_fino();



if ($partopreno->datoj['domotipo']=='J')
    {
        switch($nombro) {
        case 0:
            eoecho ("<p>$Ri bezonas c^ambron, sed tiu ankorau^ ne rezervig^is".
                    " por $ri.");
            ligu('cxambroj.php', "Elektu c^ambron");
            echo "</p>";
            break;
        case 1:
            $linio = mysql_fetch_assoc($sql_rez);
            if ($linio['rezervtipo'] == 'r')
                {
                    eoecho ("<p>${Ri} jam havas rezervitan c^ambron:<br />");
                }
            else
                {
                    eoecho("<li>{$Ri} jam havas disdonitan c^ambron:<br />");
                }
            montru_cxambron($linio['cxambro'], $_SESSION['renkontigxo'],
                            $partoprenanto,$partopreno);
            break;
        default:
            eoecho("<p>$Ri s^ajne havas pli ol unu liton:</p><div>" );
                $montritaj_cxambroj = array();
                while($linio = mysql_fetch_assoc($sql_rez)) {
                    if (!in_array($linio['cxambro'], $montritaj_cxambroj)) {
                        echo "<div style='display: inline-block;'>";
                        montru_cxambron($linio['cxambro'],
                                        $_SESSION['renkontigxo'],
                                        $partoprenanto,
                                        $partopreno);
                        echo "</div>\n";
                        $montritaj_cxambroj []= $linio['cxambro'];
                    }
                }
                echo "</div>";
        } // switch
    }
else
    { // memzorganto
        eoecho("<p>$Ri log^os en la memzorgantejo, do ne necesas prizorgi".
               " c^ambron por $ri.</p>");
        
        if ($nombro != 0)
            {
                while($linio = mysql_fetch_assoc($sql_res)) {
                    montru_cxambron($linio['cxambro']);
                }
                
            }
    }




  /******** Disdono de diversajxoj (???) *************/
HtmlFino();

