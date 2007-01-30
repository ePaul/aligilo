<?php

// tio cxi dosiero importas la tutan datumbazon kion vi povas krei per la eksport funkciado
// vi antauxe devas forvisxi cxion tabelon, sed la datumbazo mem jam devas ekzisti

// vi bezonas la vojon al gzip kaj la nomon de la dosiero sen la '.gz' finajxo.

// post la uzado remetu la exit();


  $vojo_al_gzip='D:\webserver\mysql\bin';
  
  $dosiero = 'D:\ijk-db\IS-DB1005paul\probieren\projekto_eksport.sql';
  
  $datumbazo = 'paul1';
  
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
}

  $vojo_al_gzip='D:\webserver\mysql\bin';
  
  $dosiero = 'D:\ijk-db\IS-DB1005paul\probieren\projekto-is_.sql';
  
  $datumbazo = 'paul1';
  
  system($vojo_al_gzip.'\gzip -df '.$dosiero.'.gz',$output);

  system_eraroj($output,' cxe la malpakado');
  
  system('d:\Webserver\mysql\bin\mysql -D '.$datumbazo.' -e "source '.$dosiero.'"',$output);

  system_eraroj($output,' cxe la importado en mysql');
  
  print '<BR><BR>import finita';
php?>