<?php


require_once ('iloj/iloj.php');


  session_start();
  malfermu_datumaro();


kontrolu_rajton("sxangxi");

sesio_aktualigo_laux_get();


if ($_POST['alvenstato']) {
    // TODO: kontrolo, cxu estas tauxga valoro
    $_SESSION['partopreno']->datoj['alvenstato'] = $_POST['alvenstato'];
    $_SESSION['partopreno']->skribu();
 }



$sekvapagxo = $_SESSION["sekvontapagxo"] or
    $sekvapagxo = 'partrezultoj.php';

http_redirect($sekvapagxo, null, false, 303);




?>