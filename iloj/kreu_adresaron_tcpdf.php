<?php

  /**
   * Funkcio por krei la adresaron en PDF-a kaj CSV-a formo. 
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

require_once ($GLOBALS['prafix'] . '/iloj/tcpdf_php4/tcpdf.php');


/**
 * Kreas adresaron en PDFa formo, kaj paralele en CSVa formo.
 * La dosieroj estos metataj en 'dosieroj_generitaj', kaj ni fine
 * montros ligon al tiuj. Dum la kreado montras liston de nomoj
 * prilaborataj.
 * 
 * @param string $granda se <samp>"JES"</samp>, la adresaro estos farita
 *           en pli granda versio por korektlegi.
 * @param string $bunta se <samp>"JES"</samp>, la linioj de la adresaroj
 *                sxangxos inter kvar koloroj - tiel estas pli facile distingi
 *                la unuopajn adresojn, se kelkaj estas plurliniaj.
 */
function kreu_adresaron($granda, $bunta) {
    echo "<p>\n";

    $fp = fopen($GLOBALS['prafix'] . "/dosieroj_generitaj/adresaro.csv",
                "w"); //por la .csv versio
    $font='freesans';

    $pdf=new TCPDF();
    $pdf->AddFont($font,'',$font.'.php');
    $pdf->AddFont($font,'B',$font.'b.php');
    $pdf->SetFont($font,'',15);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    $pdf->Open();  

    $pdf->AddPage();


    $pdf->write(7, uni("Listo de Partoprenantoj\n" .
                       $_SESSION["renkontigxo"]->datoj["nomo"] .
                       " en " . $_SESSION["renkontigxo"]->datoj["loko"] .
                       " (" . $_SESSION["renkontigxo"]->datoj["de"] . " g^is " .
                       $_SESSION["renkontigxo"]->datoj["gxis"] . ")\n"));
	if ('JES' == $granda)
        {
            $pdf -> SetFont($font,'',12);
            $pdf->write(8,
                        uni("Bonvolu kontroli (kaj eble korekti) vian".
                            " adreson en la adresaro, por ke en" .
                            " la fina versio estu g^ustaj datumoj."));
            $pdf->ln(12);
            $linlargxo = 7;
            $interlinspaco = 13;
        }
	else
        {
            $pdf->SetFont($font,'B',9);
            // TODO: metu tiun tekston en la datumbazon.
            $pdf->write(3.7, uni("Vi rajtas uzi tiun adresaron nur por" .
                                 " personaj celoj. Vi ne rajtas" .
                                 " uzi g^in por amasaj leteroj au^".
                                 " retmesag^oj (ankau^ ne por informi".
                                 " pri via Esperanto-renkontig^o), kaj".
                                 " ne rajtas pludoni g^in (ec^ ne parte). "));
            $pdf->SetFont('', '');
            $pdf->write(3.7, uni(
                                 " Se amiko de vi (kiu partoprenis la " .
                                 $_SESSION['renkontigxo']->datoj['mallongigo'].
                                 ") ne ricevis la adresaron," .
                                 " li povas mendi propran c^e " .
                                 $_SESSION['renkontigxo']->datoj['adminretadreso'] .
                                 ". La sama validas por vi, se vi perdos ".
                                 " g^in.\n" .
                                 "Atentu, la g^usta sinsekvo de la" .
                                 " adres-partoj sur leteroj - depende de" .
                                 " la lando -" .
                                 " ofte ne estas la sama kiel tiu en tiu".
                                 " c^i listo. Informig^u antau^ eksendado" .
                                 " de letero (ekzemple per retpos^to al la" .
                                 " ricevonto)."));
            $pdf->ln(7);
            $linlargxo = 3.0;
            $interlinspaco = 4.05;
        }
    $pdf->write(($linlargxo*1.7),
                uni("persona nomo; nomo; adresaldonaj^o; strato; pos^tkodo; ".
                    "urbo; lando; telefono; telefakso; retpos^to"));
    $pdf -> ln($interlinspaco);
    $pdf -> ln($interlinspaco);

    $demando = datumbazdemando(array("p.ID", "pn.ID",
                                     "p.nomo" => "famnomo",
                                     "personanomo",
                                     "sxildnomo", 
                                     "l.nomo" => "landonomo", "retposxto",
                                     "adresaldonajxo",
                                     "strato", "posxtkodo", "urbo", "lando",
                                     "telefono", "telefakso"),
                               array("partoprenantoj" => "p",
                                     "partoprenoj" => "pn",
                                     "landoj" => "l"),
                               array("pn.partoprenantoID = p.ID",
                                     "l.ID = lando",
                                     "pn.intolisto = 'J'", 
                                     "alvenstato = 'a'"
                                     // nur uloj. kiuj estis akceptitaj
                                     ),
                               "renkontigxoID", // aktuala renkontigxo
                               array("order" => "personanomo, famnomo")
                               );

    echo "<BR><BR>";
    $rezulto = sql_faru($demando);
	$koloro = 0;
    while ($row = mysql_fetch_assoc($rezulto))
        {
            if ($row['sxildnomo']) {
                $tutanomo = $row['personanomo'] . ' (' .
                    $row['sxildnomo'] .') ' . $row['famnomo'];
            }
            else {
                $tutanomo = $row['personanomo'] . ' ' . $row['famnomo'];
            }
            eoecho($tutanomo."<br/>");
            if ($bunta == "JES")
                {
                    switch($koloro % 4)
                        {
                        case 0:
                            $pdf->SetTextColor(200,0,0);
                            break;
                        case 1:
                            $pdf->SetTextColor(0,0,255);
                            break;
                        case 2:
                            $pdf->SetTextColor(0,150,0);
                            break;
                        default:
                            $pdf->SetTextColor(0,0,0);
                            break;
                        }
                    $koloro ++;
                }
            $pdf->write($linlargxo,uni($tutanomo . "; " .
                                       $row['adresaldonajxo'] .
                                       "; " . $row['strato'] . "; " .
                                       $row['posxtkodo'] . "; " .
                                       $row['urbo'] .
                                       "; " . $row['landonomo'] . "; " .
                                       $row['telefono'] . "; " .
                                       $row['telefakso']
                                       . "; " . $row['retposxto']));
            $pdf->ln($interlinspaco);
      
            // TODO: pripensu, ĉu ni ne ankaŭ por la CSV-versio restu ĉe UTF-8
            fputs($fp,
                  utf8_decode("'".$tutanomo."';'".$row['adresaldonajxo']."';'" .
                              $row['strato']."';'".$row['posxtkodo']."';'".
                              $row['urbo']."';'".$row['landonomo']."';'".
                              $row['telefono']."';'".$row['telefakso']."';'".
                              $row['retposxto'])."'\n");
        }
    $pdf->Output($GLOBALS['prafix'] . "/dosieroj_generitaj/adresaro.pdf");
    fclose($fp);
    echo "<br/><br/>";
    hazard_ligu("dosieroj_generitaj/adresaro.pdf",
                "els^uti la adresaron (PDF).");
	hazard_ligu("dosieroj_generitaj/adresaro.csv",
                "els^uti la adresaron (CSV).");
    echo "</p>";
}


?>