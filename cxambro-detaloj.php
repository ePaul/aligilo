<?php


  /**
   * Detala rigardo de unu cxambro.
   *
   * 
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   * @todo rajto nur estu bezonata por sxangxi ion, ne por simple rigardi.
   */

  /**
   */
require_once ('iloj/iloj.php');
require_once ('iloj/iloj_cxambroj.php');

session_start();
malfermu_datumaro();


kontrolu_rajton("cxambrumi");



if ($_REQUEST['sp'])
    {
        $_SESSION['sekvontapagxo'] = $_REQUEST['sp'];
    }

sesio_aktualigu_laux_get();


if ( $_REQUEST['sendu'] == "cxambrotipsxangxo" )
{

    kontrolu_rajton("cxambrumi");

    // sxangxo de cxambrotipo (gea/unuseksa), duliteco kaj/aux rimarkoj.

    sxangxu_datumbazon("cxambroj",
                       array("tipo"     => $_REQUEST['tipo'],
                             "rimarkoj" => $_REQUEST['rimarkoj'],
                             "dulita"   => $_REQUEST['dulita']),
                       array("ID" => $_REQUEST['cxambronumero']));
}




HtmlKapo();

debug_echo( "<!-- POST: " . var_export($_POST, true) . "-->");


if ($_SESSION["partoprenanto"])
{
  eoecho ("Ni serc^as c^ambron por: <b>" . $_SESSION["partoprenanto"]->datoj[personanomo] .
          " " . $_SESSION["partoprenanto"]->datoj[nomo] .
	      " [" . $_SESSION["partoprenanto"]->datoj[sekso] .
          "/" . $_SESSION["partopreno"]->datoj[cxambrotipo] .
	      "/" . $_SESSION["partopreno"]->datoj['agxo'] .
	      "] </b> de: " . $_SESSION["partopreno"]->datoj[de] .
	      " g^is: ".$_SESSION["partopreno"]->datoj[gxis]."<BR>\n");
  if ($_SESSION["partopreno"]->datoj['renkontigxoID']!=$_SESSION["renkontigxo"]->datoj['ID']) 
  {
    erareldono("malg^usta renkontig^o!");
    exit();
  }
  
}

{
  // montru, kiu sxatas kunlogxi kun kiu
  montru_kunlogxantojn($cxambronumeo);
  // montru nun la cxambron mem.
  montru_cxambron($cxambronumero,$_SESSION["renkontigxo"],
                  $_SESSION["partoprenanto"],$_SESSION["partopreno"],
                  "granda");

  montru_cxambrointersxangxilon($cxambronumero);

}


 

if ($_SESSION['sekvontapagxo']) {
    ligu($_SESSION['sekvontapagxo'], "Reen");
 }
 else if ($_SESSION['partoprenanto']) {
    ligu('partrezultoj.php', "Reen");
 }

HtmlFino();

?>
