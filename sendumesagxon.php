<?php

  /**
   * Verkado kaj sendado de individuaj retmesaĝoj al partoprenantoj.
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @uses sendu_malauxtomatan_mesagxon_el_POST()
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



require_once ('iloj/iloj.php');
require_once("iloj/retmesagxiloj.php");
require_once("iloj/diversaj_retmesagxoj.php");

  session_start();
  malfermu_datumaro();
  
  HtmlKapo();

if (!rajtas("retumi"))
{
  ne_rajtas();
}


function sendu_gxin() {
    sendu_malauxtomatan_mesagxon_el_POST();
    eoecho("<p style='color:red'>Mesag^o sendita.</p>");
}

function faru_noton_el_gxi() {
    kreunoton($_POST['ID'], $_POST['de_nomo'],
               $_POST['alkiu'], "rete",
               $_POST['temo'], $_POST['teksto'], $_POST['prilaborata']);
     eoecho("<p style='color:red'>Noto savita.</p>");
}

function montru_gxin() {
    echo "<table>";
    tabela_kasxilo("Sendanto-nomo",'',$_POST['de_nomo']);
    tabela_kasxilo("Sendanto-adreso",'', $_POST["de_adreso"]);
    tabela_kasxilo("ppanto-ID",   '', $_POST['ID']);
    tabela_kasxilo("Nomo",        '', $_POST['alkiu']);
    tabela_kasxilo("Retadreso",   '', $_POST['retadreso']);
    tabela_kasxilo("Temo",        '', $_POST['temo']);
    tabela_kasxilo("Enhavo",      '', $_POST['teksto']);
    tabela_kasxilo("Prilaborita", '', $_POST['prilaborata']);
    echo "</table>";
}

 

switch($_REQUEST['sendu'])
{
 case 'elektu':

  // "select ID,nomo,subjekto,korpo from retposxto where ID=$elektata");
     $result = sql_faru(datumbazdemando(array("subjekto", "korpo"),
                                       "retposxto",
                                       "ID = '{$_POST['sxablonoID']}'"));
    $row = mysql_fetch_array($result, MYSQL_ASSOC);

    $teksto =
        transformu_tekston($row['korpo'],
                           array('anto' => $_SESSION['partoprenanto']->datoj,
                                 'eno' => $_SESSION['partopreno']->datoj,
                                 'igxo' => $_SESSION['renkontigxo']->datoj,
                                 'ktp' => array('entajpantonomo' =>
                                                $_SESSION["kkren"]["entajpantonomo"])));

    echo('<form name="notoj" method="post" action="sendumesagxon.php">');

    $alkiu = $_SESSION["partoprenanto"]->tuta_nomo();

    eoecho ("<p>Kiun mesag^on vi volas sendi al $alkiu?</p>");


    // TODO: eble aldonu pliajn retadresojn cxi tie.
    $sendantolisto = array($_SESSION['renkontigxo']->datoj['mallongigo'] . " - Administranto");
    $adresolisto = array($_SESSION['renkontigxo']->datoj['adminretadreso']);

    if ($miaPPID = $_SESSION['kkren']['partoprenanto_id']
        and $miaPP = new Partoprenanto($miaPPID)) {
        if (trim($miaPP->tuta_nomo()))
            $sendantolisto[]=$miaPP->tuta_nomo();
        if ($miaPP->datoj['retposxto']) {
            $adresolisto[]= $miaPP->datoj['retposxto'];
        }
    }

    $linio = eltrovu_laux_id(array("retposxtadreso", "sendanto_nomo"),
                                 "entajpantoj",
                             $_SESSION['kkren']['entajpanto']);
    if ($linio['retposxtadreso']) {
        $adresolisto[]= $linio['retposxtadreso'];
    }
    if ($linio['sendanto_nomo']) {
        $sendantolisto[]= $linio['sendanto_nomo'];
    }

    $sendantolisto = array_combine($sendantolisto, $sendantolisto);
    $adresolisto = array_combine($adresolisto, $adresolisto);


  echo "<table>";
  tabela_kasxilo("ppanto-ID", 'ID',
                 $_SESSION['partoprenanto']->datoj['ID']);
  tabela_elektilo("Sendanto-nomo",'de_nomo', $sendantolisto);
  tabela_elektilo("Sendanto-adreso", "de_adreso", $adresolisto);
  tabela_kasxilo("Al-Nomo", 'alkiu', $alkiu);
  tabela_kasxilo("Al-Retadreso", 'retadreso',
                 $_SESSION['partoprenanto']->datoj['retposxto']);
  tabelentajpejo("Temo",'temo',$row['subjekto'], 57);
  granda_tabelentajpejo("Enhavo", 'teksto',
                        $teksto, 57, 20);
  echo "</table>";

  /*
  echo "<b>retadreso:</b> ".$_SESSION["partoprenanto"]->datoj[retposxto]."<BR>";  
  echo nl2br("<b>subjekto:</b> ".$row[subjekto]."<BR><BR>");
  echo "<textarea name=\"korpo\" cols=\"57\" rows=\"20\" wrap=\"soft\">".stripslashes($row[korpo])."</textarea>";
  echo "<BR>";
  */

  echo "<p>";
  entajpbokso("","prilaborata","","j","j","prilaborata<BR>",'','ne');
  
  //  tenukasxe("elektata", $elektata);

  butono("not+send", "Notu kaj sendu!");
  butono("sendu", "Nur sendu!");
  butono("notu", "Nur notu!");
  ligu("partrezultoj.php", "Reen");

  echo "</p>";

  echo "</form>";

  break;
 case 'sendu':
     sendu_gxin();
     montru_gxin();
     break;
 case 'notu':
     faru_noton_el_gxi();
     montru_gxin();
     break;
 case 'not+send':
     sendu_gxin();
     faru_noton_el_gxi();
     montru_gxin();
     break;
 default:

     echo("<form method='post' action='sendumesagxon.php'>\n");
     
     $alkiu = $_SESSION["partoprenanto"]->tuta_nomo();
     
     eoecho ("<p>Kiun mesag^on vi volas sendi al $alkiu?</p>");
    
     echo "<p>";
     elektilo_simpla_db("sxablonoID",
                        "retposxto");
     butono("elektu", "Elektu!");
     echo "</p>";
     
     echo("</form>");

     break;

 } // switch
?></body>
</html>

