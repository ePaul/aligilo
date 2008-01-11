<?php 
  //resendas la elekton cxe la butono en la maldekstra
  // menuo al la gxusta dosiero

  //echo "<!-- POST: " . var_export($_POST, true) . "-->";

  if ($_REQUEST['elekto'] == "Montru!") {
      require_once("iloj/iloj.php");
      session_start();
      malfermu_datumaro();

      if ($_POST['partoprenantoidento'])
          {
              $_SESSION['partoprenanto'] = new Partoprenanto($_POST['partoprenantoidento']);
              // sercxu partoprenon de la aktuala renkontigxo por la partoprenanto,
              // kaj elektu tiun kiel $_SESSION['partopreno'].
              
              $sql = datumbazdemando("id",
                                     "partoprenoj",
                                     "",
                                     array("renkontigxo" => "renkontigxoID",
                                           "partoprenanto" => "partoprenantoID"),
                                     array("limit" => "0, 10"));
              $result = sql_faru($sql);
              
              if (mysql_num_rows($result)==1) {
                  $row = mysql_fetch_assoc($result);
                  $_SESSION["partopreno"] = new Partopreno($row['id']);
              }
              else {
                  unset($_SESSION['partopreno']);
              }
              
          }
      else if ($_POST['partoprenidento'])
          {
              $_SESSION['partopreno'] = new Partopreno($_POST['partoprenidento']);
				  $_SESSION['partoprenanto'] = new Partoprenanto($_SESSION['partopreno']->datoj['partoprenantoID']);
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

      if ((MODUSO != 'monde') and // nur en testa kaj surloka varianto
          // testu, cxu ri ankoraux ne alceptigxis
          $_SESSION['partopreno'] and
          ($_SESSION['partopreno']->datoj['renkontigxoID'] ==
           $_SESSION['renkontigxo']->datoj['ID']) and 
          ($_SESSION["partopreno"]->datoj['alvenstato'] == 'v')
          )
          {
              http_redirect('akceptado-datoj.php', null, false, 303);
          }
	  else
          {
              http_redirect('partrezultoj.php', null, false, 303);
          }
      
  }

  // TODO: uzu veran plusendon anstataux require.

  if ($elekto == "novan noton") {require 'notoj.php';}
  if ($elekto == "notojn") {require 'sercxrezultoj.php';}  
  
?>