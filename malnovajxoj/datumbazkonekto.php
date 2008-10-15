<?php

/**
 * datumbazkonekto
 */

$prafix = $GLOBALS['prafix'];

	if (!$prafix)
	{
	    die("Fehlerhafte Einbindung des Programmes.");
	}
	else
	{
	    echo "<!--\n";
	    echo "  prafix: $prafix \n";
	    echo "  aligxilonomo: $aligxilonomo \n-->";
	}
	require_once ($prafix . "/iloj/iloj.php");
	// require_once ($prafix . "iloj/formulareroj.php");

	$_SESSION["enkodo"]="unikodo";

	malfermu_datumaro();

?>