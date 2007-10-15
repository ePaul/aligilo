<?php



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
    if ($partopreno->datoj['domotipo'] == 'j')
        {
            // cxambro-disdonado
            sxangxu_datumbazon("litonoktoj",
                               array("rezervtipo" => "d"),
                               "",
                               "partopreno");
            if ($partopreno->datoj['havasMangxkuponon'] == 'N') {
                $mankasKupono = true;
            } else {
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


    HtmlKapo();

    eoecho( "<p>Ni sukcese akceptis la partoprenanton ");
    ligu('partrezultoj.php?partoprenoidento='.$partopreno->datoj['ID'],
         $partoprenanto->tuta_nomo());
    echo (".</p>\n");

    if ($mankasSxildo and $mankasKupono) {
        eoecho("<p>Bedau^rinde mankas kaj noms^ildo kaj mang^kupono por $ri." .
               " $Ri revenu poste, kiam ili estos printitaj, al la oficejo ".
               " au^ akceptejo.</p>\n");
        
    } else if ($mankasSxildo) {
        eoecho("<p>Bedau^rinde {$ri}a noms^ildo ankorau^ ne estas produktita.".
               " Sed $ri jam povos preni vian mang^kuponon, por tio $ri iru".
               " al aparta tablo.</p>\n");
    } else if ($mankasKupono) {
        eoecho("<p>Bedau^rinde {$ri}a mang^kupono ankorau^ ne estas ".
               "produktita. Sed $ri jam povos preni {$ri}an noms^ildon,".
               " por tio $ri iru al aparta tablo.</p>\n");
    } else {
        eoecho("<p>$Ri iru al aparta tablo por preni sian mang^kuponon kaj ".
               "noms^ildon.</p>\n");
    }
    
    HtmlFino();
    exit();
 }

akceptado_kapo("fino");

echo "<ul>\n";

eoecho ("<li>Ni venis al la lasta s^tupo ...</li>\n");
eoecho("<li>Prenu bros^uron de la stoko, notu la nomon <strong>" . $partoprenanto->tuta_nomo() . "</strong> kaj ");
if ($partopreno->datoj['domotipo'] == 'j')
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
        eoecho($linio['nomo'] . " kiel c^ambronumero sur la dorsa flanko." .
               " Donu al $ri la bros^uron.</li>\n");
        eoecho("<li>Donu al $ri pakaj^on da littolaj^o.</li>");
    }
else
    {
        eoecho(" 'M' kiel c^ambronumero sur la dorsa flanko." .
               " Donu al $ri la bros^uron.</li>\n");
    }

echo "</ul>\n";

ligu_butone("akceptado-fino.php", "Akceptu {$ri}n", "akceptu");

HtmlFino();

?>
