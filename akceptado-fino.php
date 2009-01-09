<?php

// define("DEBUG", true);

/*
 * Akceptado de partoprenantoj
 *
 *  Pasxo 7: Fino
 *
 * TODO!: pretigi, elprovi
 */

require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

kontrolu_rajton("akcepti");
require_once('iloj/iloj_akceptado.php');


  $partoprenanto = $_SESSION["partoprenanto"];

// por repreni aktualajn datumojn, kaze ke iu alia intertempe printis
// nomsxildojn aux simile
$partopreno = new Partopreno($_SESSION['partopreno']->datoj['ID']);

  // la persona pronomo (li aux sxi)
  $ri = $partoprenanto->personapronomo;
  $Ri = ucfirst($ri);

if ($_POST['sendu'] == 'akceptu') {
    

    // datumbazsxangxoj
    
    $partopreno->datoj['alvenstato'] = 'a';
    if ($partopreno->datoj['domotipo'] == 'J')
        {
            // cxambro-disdonado
            sxangxu_datumbazon("litonoktoj",
                               array("rezervtipo" => "d"),
                               array(),
                               array("partopreno" => 'partopreno'));
            if ($partopreno->datoj['havasMangxkuponon'] == 'N') {
                $mankasKupono = true;
            } else { // havasMangxkuponon estas aux 'P' aux 'J', aux en iu nedefinita stato
                $partopreno->datoj['havasMangxkuponon'] = 'J';
            }
        }
    if ($partopreno->havasNomsxildon == 'N') {
        $mankasSxildo = true;
    } else {
        $partopreno->havasNomsxildon = 'J';
    }
    $partopreno->skribu();
    $partopreno = $_SESSION['partopreno'] =
        new Partopreno($partopreno->datoj['ID']);


    akceptado_kapo("post-fino");

    // TODO: (?) mangxkuponoj por memzorgantoj?

    akceptada_instrukcio("<strong>Bonvenon en la IS!</strong>.");
    if ($mankasSxildo and $mankasKupono) {
        akceptada_instrukcio("Bedau^rinde mankas kaj noms^ildo kaj ".
                             "mang^kupono por $ri." .
               " $Ri revenu poste, kiam ili estos printitaj, al la oficejo ".
               " au^ akceptejo.");
        
    } else if ($mankasSxildo) {
        akceptada_instrukcio("Bedau^rinde {$ri}a noms^ildo ankorau^ ".
                             "ne estas produktita. Sed $ri jam povos preni" .
                             " vian mang^kuponon, por tio $ri iru".
                             " al aparta tablo.");
    } else if ($mankasKupono) {
        akceptada_instrukcio("Bedau^rinde {$ri}a mang^kupono ankorau^ ne".
                             " estas produktita. Sed $ri jam povos preni".
                             " {$ri}an noms^ildon, por tio $ri iru al ".
                             " aparta tablo.");
    } else {
        akceptada_instrukcio("<p>$Ri iru al aparta tablo por preni sian mang^kuponon kaj ".
               "noms^ildon.</p>\n");
    }

    akceptado_kesto_fino();

    eoecho( "<p>Ni sukcese akceptis la partoprenanton ");
    ligu('partrezultoj.php?partoprenidento='.$partopreno->datoj['ID'],
         $partoprenanto->tuta_nomo());
    echo (".</p>\n");

    
    HtmlFino();
    exit();
 }

akceptado_kapo("fino");



akceptada_instrukcio("Prenu bros^uron de la stoko");


akceptada_instrukcio("Notu la nomon kaj c^ambronumeron sur la dorsa flanko.");
akceptada_instrukcio("Donu al $ri la bros^uron.");

// TODO: adaptu, kiam estos pluraj domotipoj
// TODO: cxu ni disdonas gxin en tiu tablo, aux cxu la homoj prenos mem?
if ($partopreno->datoj['domotipo'] == 'J') {
    akceptada_instrukcio("Donu al $ri pakaj^on da littolaj^o.");
 }
akceptada_instrukcio("Premu la suban butonon.");

akceptado_kesto_fino();

eoecho ("<h3>Por la bros^uro</h3>\n");

// TODO: adaptu, kiam estos pluraj domotipoj
if ($partopreno->datoj['domotipo'] == 'J')
    {
        $sql =
            datumbazdemando(array('l.nokto_de', 'cx.nomo'), 
                            array('litonoktoj' => 'l',
                                  'cxambroj' => 'cx'),
                            array('cx.ID = l.cxambro',
                                  "l.partopreno = '".
                                  $partopreno->datoj['ID']."'"),
                            "",
                            array('order' => 'l.nokto_de ASC')
                            );
        $rez = sql_faru($sql);
        $linio = mysql_fetch_assoc($rez);
        $numero = $linio['nomo'];
    }
else
    {
        $numero = 'M';
    }

eoecho ("<table><tr>\n<th>Nomo</th><td>" . $partoprenanto->tuta_nomo() .
        "</td></tr>\n<tr><th>C^ambronumero</th><td>" . $numero .
        "</td></tr>\n</table>\n");

eoecho ("<h3>Oficiala akceptado</h3>\n");
echo ("<p>");
ligu_butone("akceptado-fino.php", "Akceptu {$ri}n", "akceptu");
echo ("</p>");

HtmlFino();

?>
