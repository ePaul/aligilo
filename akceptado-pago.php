<?php




/*
 * Akceptado de partoprenantoj
 *
 *  Pasxo 6: Pago
 *
 * TODO!: pretigi, elprovi
 */

require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

kontrolu_rajton("akcepti");
require_once('iloj/iloj_akceptado.php');


  $partoprenanto = $_SESSION["partoprenanto"];
  $partopreno = $_SESSION['partopreno'];

  // la persona pronomo (li aux sxi)
  $ri = $partoprenanto->personapronomo;
  $Ri = ucfirst($ri);



if ($_POST['sendu'] == 'kolektu') {
    $pago = new Pago();
    $pago->kreu();
    $pago->datoj['partoprenoID'] = $partopreno->datoj['ID'];
    $pago->datoj['kvanto'] = $_POST['pago'];
    $pago->datoj['tipo'] = 'surlokpago';
    $pago->skribu();

    //// TODO: pripensu novan uzon de Monujo.
    //         $mono = new Monujo();
    //         $mono->kreu();
    //         // TODO
    //         $mono->skribu();

        
 }
 else if ($_POST['sendu'] == 'donacu' or $_POST['sendu'] == 'repagu') {

    $pago = new Pago();
    $pago->kreu();
    $pago->datoj['partoprenoID'] = $partopreno->datoj['ID'];
    $pago->datoj['kvanto'] = - $_POST['malpago'];
    $pago->datoj['tipo'] =
        ($_POST['sendu'] == 'donacu' ? 'donaco' : 'repago');
    $pago->skribu();

    // TODO: monujo (nur cxe repago)
     
 }
 else {
     $ne_pluiru = true;
 }

$kot = new Kotizo($partopreno, $partoprenanto,
                  $_SESSION['renkontigxo']);
$restas = $kot->restas_pagenda();

if ($restas == 0.0 and !$ne_pluiru) {
    kalkulu_necesajn_kaj_eblajn_pasxojn('pago');
    $pasxo = sekva_pasxo();
    http_redirect('akceptado-'.$pasxo['id'].'.php', null,
                  false, 303);
    exit();
 }


akceptado_kapo("pago");


// #########################################################################


echo "<form action='akceptado-pago.php' method='POST'><ul>\n";
eoecho("<li>Jen {$ri}a kotizokalkulado: <table id='rezulto'>\n");
$kot->montru_kotizon(0, $partopreno, $partoprenanto,
                     $_SESSION['renkontigxo']);
echo("</table></li>\n");
eoecho("<li>Komparu la kalkulon kun tiu sur la akceptofolio. Se necesas," .
       " s^ang^u la akceptofolion. Se io estas neklara, voku la" .
       " c^efadministranton.</li>\n");
if($restas > 0) {
    simpla_entajpejo("<li>Kolektu pagon de ", 'pago', "", 4, "",
                     " E^. (Se estas malpli ol " . $restas .
                     " E^, prenu garantiaj^on kaj metu g^in kun noto-slipo ".
                     "en la kason.</li>\n");
    eoecho("<li>Notu la pagon en la akceptofolio.</li>\n");
    echo "</ul>\n";
    
    butono("kolektu", "Enmetu pagon");
    ligu_sekvan("Mi prenis garantiaj^on kaj akceptos ${ri}n sen ".
                "kompleta pago.");
    
 } else if ($restas < 0) {
    eoecho ("<li>$Ri jam <strong>pagis pli</strong> ol sian tutan kotizon.".
            " Bonvolu demandi {$ri}n, c^u $ri volas donaci la kromaj^on de ".
            (-$restas) . " E^, au^ rehavi g^in (au^ poste decidi).</li>");
    simpla_entajpejo("<li>", "malpago", -$restas, 5, "", " ");
    butono("repagu", "Repagu");
    butono("donacu", "Donacu");
    eoecho("Notu ankau^ tion en la akceptofolio.");
    echo "</li>\n</ul>\n";
    ligu_sekvan("$Ri volas poste decidi, kion fari per la superflua mono," .
                " kaj venos por tio al la oficejo.");
 } else { // $restas == 0
    eoecho("<li>$Ri jam pagis g^uste sian tutan kotizon, do ne necesas".
           " io plia.</li>\n</ul>\n");
    ligu_sekvan();
 }

echo "</form>\n";

HtmlFino();

?>
