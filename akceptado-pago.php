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
     // ni nun unuan fojon alvenis ...
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

akceptada_instrukcio("Komparu la kalkulon kun tiu sur la akceptofolio. ".
                     "Se necesas, s^ang^u la akceptofolion. Se io estas".
                     " neklara, voku la c^efadministranton.");
if($restas > 0) {
    // necesas pagi!

    akceptada_instrukcio("Kolektu pagon de $ri. Se estas malpli ol <strong>".
                         $restas. ". E^</strong>, prenu garantiaj^on de $ri" .
                         " kaj metu g^in kun noto-slipeto en la kason. Au^ ".
                         "simple sendu {$ri}n al la banko por reveni poste.");
    akceptada_instrukcio("Enmetu la pagon sube, kaj ankau^ notu g^in en la ".
                         " akceptofolio.");
    akceptada_instrukcio("Premu la butonon <em>Enmetu pagon</em>.");

    ligu_sekvan("Mi prenis garantiaj^on kaj akceptos ${ri}n sen ".
                "kompleta pago.");

 }
 else if ($restas < 0) {
     // cxu tuj repagi monon?
     akceptada_instrukcio("$Ri jam <strong>pagis pli</strong> ol sian tutan".
                          " kotizon. Demandu {$ri}n, c^u $ri volas".
                          " donaci la kromaj^on de ". (-$restas) .
                          " E^, au^ rehavi g^in (au^ poste decidi).</li>");
     akceptada_instrukcio("Entajpu la donacon au^ repagon sube, notu g^in en".
                          " la akceptofolio kaj uzu la respektivan butonon.".
                          " (Se $ri volas parte repagigi kaj parte donaci, ".
                          " entajpu unu post la alia.)");
     akceptada_instrukcio("(En kazo de <em>repago</em>, kompreneble donu al".
                          "  $ri la monon.)");
     ligu_sekvan("Ne, $ri volas poste decidi, kion fari per la mono, kaj" .
                 " venos tiam al la oficejo.");
     
 }
 else {
     // $restas == 0
    akceptada_instrukcio("$Ri <strong>jam pagis</strong> g^uste sian" .
                         " tutan kotizon, do ne necesas io plia nun.");
    ligu_sekvan();
 }



akceptado_kesto_fino();


// #########################################################################


echo "<form action='akceptado-pago.php' method='POST'><ul>\n";


eoecho("<h3>Kotizokalkulado:</h3>\n");
echo("<table id='rezulto'>\n");
$kot->montru_kotizon(0, $partopreno, $partoprenanto,
                     $_SESSION['renkontigxo']);
echo("</table>\n");


if($restas > 0) {
    eoecho("<h3>Pago</h3>\n");
    simpla_entajpejo("<p>", 'pago', $restas, 4, "", " E^. ");
    
    butono("kolektu", "Enmetu pagon");
    echo "</p>";
    
 } else if ($restas < 0) {
    eoecho("<h3>Repago au^ donaco?</h3>");
    simpla_entajpejo("<p>", "malpago", -$restas, 5, "", " ");
    butono("repagu", "Repagu");
    butono("donacu", "Donacu");
    eoecho("Notu ankau^ tion en la akceptofolio.");
    echo "</li>\n</ul>\n";
    ligu_sekvan("$Ri volas poste decidi, kion fari per la superflua mono," .
                " kaj venos por tio al la oficejo.");
 } else {
    // $restas == 0
    // -> nenio por fari
 }

echo "</form>\n";

HtmlFino();

?>
