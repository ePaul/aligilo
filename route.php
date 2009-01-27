<?php 

  /**
   * Plusendilo post elekto de partoprenanto aŭ entajpo de partopreno-ID
   * el la maldekstra menuo.
   * 
   * Tiu paĝo unue serĉas la ĝustan personon/partoprenon, metas
   *  ĝin en sesio-variablon, kaj poste plusendas al {@link partrezultoj.php},
   * {@link akceptado-datoj.php}, {@link notoj.php} (kreu novan noton) aŭ
   *  {@link sercxrezultoj.php} (listo de notoj).
   *
   * @link menuo.php
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki, 2004-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


switch($_REQUEST['elekto']) {
    case 'Montru!':
    {

        /**
         * la kutimaj iloj.
         */
        require_once("iloj/iloj.php");
        session_start();
        malfermu_datumaro();

        if ($_POST['partoprenantoidento'])
            {

                sesio_aktualigu_ppanton($_POST['partoprenantoidento']);

//                 // TODO: ĉu uzi taŭgan funkcion el iloj_sesio?
//                 $_SESSION['partoprenanto'] = new Partoprenanto($_POST['partoprenantoidento']);
//                 // serĉu partoprenon de la aktuala renkontiĝo por la partoprenanto,
//                 // kaj elektu tiun kiel $_SESSION['partopreno'].
              
//                 $sql = datumbazdemando("id",
//                                        "partoprenoj",
//                                        "",
//                                        array("renkontigxo" => "renkontigxoID",
//                                              "partoprenanto" => "partoprenantoID"),
//                                        array("limit" => "0, 10"));
//                 $result = sql_faru($sql);
              
//                 if (mysql_num_rows($result)==1) {
//                     $row = mysql_fetch_assoc($result);
//                     $_SESSION["partopreno"] = new Partopreno($row['id']);
//                 }
//                 else {
//                     unset($_SESSION['partopreno']);
//                 }
              
            }
        else if ($_POST['partoprenidento'])
            {
                sesio_aktualigu_ppenon($_POST['partoprenidento']);
                
//                 $_SESSION['partopreno'] = new Partopreno($_POST['partoprenidento']);
//                 $_SESSION['partoprenanto'] = new Partoprenanto($_SESSION['partopreno']->datoj['partoprenantoID']);
            }
        else
            {
                HtmlKapo();
                eoecho("<h2>Eraro!</h2>");
                eoecho("<p>Necesas elekti linion el la listo au^ entajpi ".
                       "partopreno-identigilon en la keston.</p>");
                HtmlFino();
                exit();
            }

        if ((MODUSO == 'hejme') and // nur en surloka varianto
            // testu, ĉu ri ankoraŭ ne akceptiĝis
            $_SESSION['partopreno'] and
            ($_SESSION['partopreno']->datoj['renkontigxoID'] ==
             $_SESSION['renkontigxo']->datoj['ID']) and 
            (estas_unu_el($_SESSION["partopreno"]->datoj['alvenstato'], 'v', 'i'))
           )
            {
                http_redirect('akceptado-datoj.php', null, false, 303);
            }
        else
            {
                http_redirect('partrezultoj.php', null, false, 303);
            }
        break;
    }
 case 'novan noton':
     {
        require_once("iloj/iloj.php");
         http_redirect("notoj.php?partoprenantoidento=" . $_REQUEST['partoprenantoidento'], null, false, 303);
         break;
     }
 case 'notojn':
     {
        require_once("iloj/iloj.php");
         http_redirect("sercxrezultoj.php?elekto=notojn&partoprenantoidento=" . $_REQUEST['partoprenantoidento'], null, false, 303);
         break;
 }
 } // switch

  
?>
