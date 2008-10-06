<?php


  /**
   * La cxambro-superrigardo.
   *
   * 
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */

require_once ('iloj/iloj.php');
require_once ('iloj/iloj_cxambroj.php');

session_start();
malfermu_datumaro();

kontrolu_rajton("cxambrumi");

sesio_aktualigu_laux_get();


$renkontigxodauxro = $_SESSION['renkontigxo']->renkontigxonoktoj();



if ($_REQUEST['sp'])
    {
        $_SESSION['sekvontapagxo'] = $_REQUEST['sp'];
    }



HtmlKapo();

echo "<!-- POST: " . var_export($_POST, true) . "-->";


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

eoecho ("Listo de la c^ambroj lau^ la etag^oj:<BR><BR>");
  
  if ($etagxo=='')
	{
	  // montru cxiujn etagxojn
	  montru_laux_etagxoj();
      echo "<p>";

	}
  else
	{
        eoecho ("<h3> Etag^o <em>" . ucfirst($etagxo) . "</em></h3>");

	  // montru la etagxon $etagxo

	  //    $cxam_sql = "select ID from cxambroj where renkontigxo=".$_SESSION["renkontigxo"]->datoj[ID]." and etagxo='".$etagxo."' order by nomo";
	  $cxam_sql = datumbazdemando("ID",
								  "cxambroj",
								  array("etagxo = '".$etagxo ."'"),
								  "renkontigxo",
								  array("order" => "nomo"));
	  
	  $cxam_rezulto = sql_faru($cxam_sql);
	  
	  echo "<table valign='top'>";
	  while ($row = mysql_fetch_array($cxam_rezulto, MYSQL_BOTH))
		{
		  if ($kalk%3==0)   //TODO:? auch einstellbar machen (kion? cxu la 3?)
			// [respondo de Martin:] Ich hatte vor eine Art Konfiguration für jeden Benutzer und / oder jedes Treffen zu ermöglichen, die solche Sachen einstellbar macht.
              echo "<tr>";
		  $kalk++;
		  echo "<td class='cxambro'>";
		  montru_cxambron($row['ID'],
                          $_SESSION["renkontigxo"],
                          $_SESSION["partoprenanto"],
                          $_SESSION["partopreno"],
                          null,
                          "cxambroj.php?etagxo=" . $etagxo);
		  echo "</td>";
		  if($kalk%3 == 0)
			echo "</tr>";
		}
	  if ($kalk%3 != 0)
		echo "</tr>";
	  echo "</table>";

      montru_cxambrointersxangxilon();

      echo "<p>";
      ligu("cxambroj.php", "C^iuj etag^oj");

	}


if ($_SESSION['sekvontapagxo']) {
    ligu($_SESSION['sekvontapagxo'], "Reen");
 }
 else if ($_SESSION['partoprenanto']) {
    ligu('partrezultoj.php', "Reen");
 }

echo "</p>\n";

HtmlFino();

?>
