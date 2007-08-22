<?php

// tio cxi dosiero importas la tutan datumbazon kion vi povas krei per la eksport funkciado
// vi antauxe devas forvisxi cxion tabelon, sed la datumbazo mem jam devas ekzisti

// vi bezonas la vojon al gzip kaj la nomon de la dosiero sen la '.gz' finajxo.

// post la uzado remetu la exit();

$dateien=array(
"gxustaj_plzkoord.sql",
"gxustaj_plzkoordinatoj.sql",
"ie.sql",
"is_cxambroj.sql",
"is_entajpantoj.sql",
"is_kunlogxdeziroj.sql",
"is_landoj.sql",
"is_litonoktoj.sql",
"is_monujo.sql",
"is_nomsxildoj.sql",
"is_notoj.sql",
"is_pagoj.sql",
"is_partoprenantoj.sql",
"is_partoprenoj.sql",
"is_protokolo.sql",
"is_rabatoj.sql",
"is_renkontigxo.sql",
"is_retposxto.sql",
"is_sercxoj.sql",
"is_tekstoj.sql",
"kkren_funkcioj.sql",
"kola_landaj_ligoj.sql",
"MA_Adresoj.sql",
"MA_aktivuloj.sql",
"MA_funkcioj.sql",
"MA_instruistoj.sql",
"ma_notoj.sql",
"plzkoord.sql");



  $vojo_al_gzip='C:\IS-DB-in-Xanten\webserver\xampp\mysql\bin';
  
  $dosiero = 'C:\IS-DB-in-Xanten\import-daten-vor-is\MA_Adresoj.sql';
  
  $vojo = 'C:\IS-DB-in-Xanten\import-daten-vor-is\\';
  
  $datumbazo = 'pagxaro';
  
//exit();

function system_eraroj($output,$cxe)
{
 print $output;
  
  switch ($output)
  {
    case 2:
        print '<BR>okazis iu menciinda problemo '.$cxe;break;
    case 1:
        print '<BR>okazis iu eraro '.$cxe;break;
    case 0:
        /*print 'cxio en ordo';*/break;
  }         
  echo "<br>";
}

  //$vojo_al_gzip='D:\webserver\mysql\bin';
  
///  $dosiero = 'D:\ijk-db\IS-DB1005paul\probieren\projekto-is_.sql';
  
 // $datumbazo = 'paul1';
  
  foreach ($dateien as $dosiero)
  {
  $dosiero = $vojo.$dosiero;
  $variable = $vojo_al_gzip.'\gzip -df '.$dosiero.'.gz';
  print $variable."<br>";
  
  system($variable,$output);
  //print $vojo_al_gzip.'";

  system_eraroj($output,' cxe la malpakado');
  
  $variable = 'c:\IS-DB-in-Xanten\webserver\xampp\mysql\bin\mysql -D '.$datumbazo.' -u root -e "source '.$dosiero.'"';
  print $variable."<br>";
  system($variable,$output);

  system_eraroj($output,' cxe la importado en mysql');
  
  print '<BR><BR>import finita';
  
}
php?>