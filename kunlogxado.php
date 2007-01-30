<?php

define("DEBUG", true);

require_once ("iloj/iloj.php");
require_once ("iloj/iloj_kunlogxadoj.php");
session_start();
malfermu_datumaro();


HtmlKapo();



// echo "<!-- _REQUEST: ";
// var_export($_REQUEST);
// echo "\n _SESSION: ";
// var_export($_SESSION);
// echo "\n-->";

if ($_REQUEST['kunlogxID'])
{
  switch($_REQUEST['ago'])
	{
	case 'forvisxu':
	  // TODO
	  break;
	case 'ne_eblas':
	  // TODO
	  break;
	case 'eblas':
	  // TODO
	  break;
	default:
	  break;
	}
  $deziro = new Kunlogxdeziro($_REQUEST['kunlogxID']);
  montru_kunlogxdezirdetalojn($deziro);
  
  HtmlFino();
  return;
}
if ($_REQUEST['partoprenoID'])
{
  $_SESSION['partopreno'] = new Partopreno($_REQUEST['partoprenoID']);
  $_SESSION['partoprenanto'] = new Partoprenanto($_SESSION['partopreno']->datoj['partoprenantoID']);
}


if ($_SESSION['partopreno'])
{
  switch($_REQUEST['ago'])
	{
	case 'kunigu':
	  {
		aldonu_al_datumbazo("kunlogxdeziroj",
							array("partoprenoID" => $_REQUEST['partoprenoID'],
								  "kunKiuID" => $_REQUEST['kunkiuID'],
								  "stato" => '?'));
		eoecho("<p>Aldonis la kunlog^deziron de #" . $_REQUEST['partoprenoID'] . " kun #" .
			 $_REQUEST['kunkiuID'] . " al la datumbazo.");
		break;
	  }
	case 'sercxu':
	  {
		$ri = $_SESSION['partoprenanto']->personapronomo;
		$Ri = ucfirst($ri);
		eoecho ("<h2>Kunlog^deziroj de " . $_SESSION['partoprenanto']->tuta_nomo() . " (#" .
				$_SESSION['partopreno']->datoj['ID'] . ")</h2>");
		eoecho ("<p>$Ri volas log^i kun:</p>");
		$nemontru = montru_kunlogxdezirojn($_SESSION['partopreno']);
		eoecho ("<p>La jenaj aliaj personoj volas log^i kun $ri:</p>");
		montru_kunlogxdezirojn_inversajn($_SESSION['partopreno']);
		if ($_SESSION['partopreno']->datoj['kunkiu'])
		  {
			eoecho("<p>$Ri indikis en la alig^ilo, ke $ri volas log^i kun <em>" .
				   $_SESSION['partopreno']->datoj['kunkiu'] . "</em>. Tio povus".
				   " esti unu el la sekvaj personoj:</p>");
			
			sercxu_eblajn_kunlogxantojn($_SESSION['partopreno'],
										$_SESSION['partoprenanto']->tuta_nomo(),
										$nemontru);
		  }
		else
		  {
			eoecho("<p>$Ri ne indikis en {$ri}a alig^ilo iujn kunlog^dezirojn.</p>");
		  }

// 		montru_kunlogxdezirojn_ambauxdirekte($_SESSION['partopreno']);

	  }
	  break;
	case 'montru':
	  {
		eoecho ("<p>Kunlog^deziroj de " . $_SESSION['partoprenanto']->tuta_nomo() . " (#" .
				$_SESSION['partopreno']->datoj['ID'] . "):</p>");
		montru_kunlogxdezirojn($_SESSION['partopreno']);
		break;
	  }
	}

}

  HtmlFino();


?>