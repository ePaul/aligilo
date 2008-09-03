<?php


  /**
   * Formularo por intersxangxi la cxambrojn
   * de du partoprenantoj.
   *
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


require_once ('iloj/iloj.php');
require_once ('iloj/iloj_cxambroj.php');

session_start();
malfermu_datumaro();

kontrolu_rajton('cxambrumi');

sesio_aktualigu_laux_get();


  HtmlKapo();


if ($_REQUEST['sendu'] == "sxangxu")
{
  $tempID = rand(-10000, -1000);
  $al = substr($_REQUEST['al'], 1);
  $ppID = $_REQUEST['ppID'];

  

  // intersxangxu la cxambrojn ...
  sxangxu_datumbazon("litonoktoj",
					 array("partopreno" => $tempID),
					 array("partopreno" => $ppID));
  sxangxu_datumbazon("litonoktoj",
					 array("partopreno" => $ppID),
					 array("partopreno" => $al));
  sxangxu_datumbazon("litonoktoj",
					 array("partopreno" => $al),
					 array("partopreno" => $tempID));

  eoecho ("<p>S^ang^o de");
  ligu ("partrezultoj.php?partoprenidento=". $ppID,"#".$ppID);
  eoecho(" al ");
  ligu("partrezultoj.php?partoprenidento=". $al, "#" .$al);
  eoecho(" farita.</p>");

}

{
  
  
  function formatu_litonokton($linio) {
      return "c^. " . $linio['cxambronomo'] . "/" . $linio['nokto_de'] .
          "&ndash;" . $linio['nokto_gxis']
          . "/" . $linio['rezervtipo'];
  }

  eoecho("<h2>Inters^ang^o de la c^ambroj de du partoprenantoj</h2>");
  
  echo "<form action='cxambro_sxangxo.php' method='post'>";
  echo "<table>";

  $sql = datumbazdemando(array('nokto_de', 'nokto_gxis', 'rezervtipo',
                               'cx.nomo' => 'cxambronomo'),
                         array('litonoktoj', 'cxambroj' => 'cx'),
                         array("cxambro = cx.id"),
                         array('partopreno'),
                         array('order' => 'nokto_de, nokto_gxis'));
  $rez = sql_faru($sql);
  $mialisto = array();
  while ($linio = mysql_fetch_assoc($rez)) {
      $mialisto[]= formatu_litonokton($linio);
  }


  tabela_kasxilo("Unua partopreno:", 'ppID',
                 $_SESSION['partopreno']->datoj['ID'],
                 donu_ligon("partrezultoj.php?partoprenoidento=" .
                            $_SESSION['partopreno']->datoj['ID'],
                            "#" . $_SESSION['partopreno']->datoj['ID']) .
                 " (" . $_SESSION['partoprenanto']->tuta_nomo() . ", " .
                 implode(", ",  $mialisto) .  ") ");
                            
  


  $cxam_sql = datumbazdemando(array("pn.ID" => 'partoprenoID',
                                    "p.nomo", "personanomo",
									"nokto_de", "nokto_gxis",
                                    "rezervtipo",
                                    'cx.nomo' => 'cxambronomo'),
							  array("litonoktoj" => "l",
									"partoprenoj" => "pn",
									"partoprenantoj" => "p",
                                    'cxambroj' => 'cx'),
							  array("l.partopreno = pn.ID",
									"pn.partoprenantoID = p.ID",
                                    "l.cxambro = cx.ID"),
							  "renkontigxoID",
							  array("order" => "personanomo, nomo")
							  );

  $rez = sql_faru($cxam_sql);
  $templisto = array();

  while ($linio = mysql_fetch_assoc($rez))
      {
          // ni devas uzi iun prefikson por la sxlosiloj
          // por ke tabela_elektilo ne pensu, ke ili estas
          // nur simplaj array-indeksoj (= forjxetendaj).
          $sx = '#'.$linio['partoprenoID'];

          if (isset($templisto[$sx])) {
              $templisto[$sx]['cxambroj'] []=
                  formatu_litonokton($linio);
          }
          else {
              $templisto[$sx] =
                  array('bazaj'
                        => $sx . " &ndash; " .  $linio['personanomo']
                        .  " " . $linio['nomo'] ,
                        'cxambroj'
                        => array(formatu_litonokton($linio))
                        );
          }
      }

  $listo = array();
  foreach($templisto AS $sx => $val) {
      $listo[$sx] = $val['bazaj'] .
          " (" . implode(", ", $val['cxambroj']) . ")";
  }


  tabela_elektilo("Dua partopreno:", 'al', $listo, "", "",
                  min(10, count($listo)));


  echo "</table>\n<p>";

  butono("sxangxu", "Inters^ang^u!");

  echo "</p>\n</form>";

}


HtmlFino();

?>