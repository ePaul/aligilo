<?php

  //
  // TODO: Nuntempe tute ne uzata dosiero. Eble forigenda.
  //

//
// .---------[ Retposxto de Martin ]---------------
// | Hallo Paul,
// | 
// | PE> $rezulto = sql_faru("select l.ID,l.partopreno,c.nomo,c.ID,
// | PE>                            
// | PE> c.renkontigxo,pp.id,partoprenantoID,p.ID
// | PE>                       from
// | PE> cxambroj,litonoktoj,partoprenoj,partoprenantoj
// | PE>                       where c.ID=l.ID and
// | PE>                         
// | PE> c.renkontigxo='".$_SESSION["renkontigxo"]->datoj[ID]."'
// | PE>                          and l.partopreno
// | PE>                       order by alvenstato,personanomo,nomo");
// | PE>  //TODO: hier auch noch parametrisieren
// | PE> ---
// | 
// | PE> (Formatado de mi, en la originalo estis cxio
// | PE>  (krom la komento) en unu linio. Eble estis
// | PE>  ankaux mi, kiu aldonis "TODO:".)
// | 
// | Aspektas al mi kiel komentaro kion mi aldonis.
// | Mi tiukaze nur ne certas kion mi celis.
// | 
// | La enhavo de tio dosiere similas al tio de finkalkulo.php. Sxajnas ke
// | vi povas forjeti gxin.
// | 
// | PE> Cxu tio iam funkciis?
// | PE> (Mi ne kredas:
// | PE>  * uzas nedefinitajn mallongigojn,
// | 
// | En finkalkulado estas simila demando kio versxjane funkcias.
// | Sxajnas ke a) cxambrookkupanto estas la komenca datumo por tiuj
// | funkcioj aux b) mi volis krei funkcion kiu donas cxiun enlogxanton de
// | la junulargastejo en .pdf, sed tute ne finigxis gxin.
// | 
// | En Trier la junulargastejo demandis precisan liston de la enlogxantoj,
// | eble mi volis aldoni gxin tiel.
// | 
// | PE>  * la "l.partopreno" ne estas tauxga valoro por uzi kun "and".
// | PE>  * la ORDER BY nur rajtas uzi nomojn, kiuj aperas en la rezulto
// | PE> )
// | PE> Aux cxu vi nur neniam uzis la statistikon pri la
// | PE> cxambrookupantoj?
// | PE> (Gxi estas ligita el administrado.php.)
// | 
// | Ne funkcias tie kaj la texto de la ligo estas la sama kiel la
// | cxambrostatistiko. Vi trovis pecon kion mi ne finigxis.
// | 
// | En Naumburg ni ne bezonis ekzaktan partoprenantoliston el la datumaro.
// | Pri Wetzlar mi ne scias. Gxi restu sur la projektlisto kiel malgrava
// | punkto.
// | 
// | PE> Kion mi faru - cxu mi divenu, kiel estu gxuste?
// | 
// | Lasu gxin por nun.
// '----------------------------
//
//


/* ############################# */
/* Montras kelkajn sxtatistikojn */
/* ############################# */


require_once ('iloj/iloj.php');
require_once('iloj/fpdf.php');
session_start();

malfermu_datumaro();

if (!rajtas("administri"))
{
  ne_rajtas();
}

function malgrandigu($io,$grandeco)
{
  global $pdf;
  while ($pdf->GetStringWidth($io)>$grandeco) $io=substr($io,0,strlen($io)-1);
  return $io;
}
function nf($io)
{
  return number_format($io, 2, '.', '');
}
function kaplinio()
{
    global $pdf;
    $pdf->Cell(4, 5 ,"?", 1,0,L);    

    $pdf->Cell(30, 5 ,"personanomo", 1,0,L);    
    $pdf->Cell(30, 5 ,"nomo", 1,0,L);
    $pdf->Cell(4, 5 ,"T", 1,0,R);    
       
    $pdf->Cell(20, 5 ,eo("log^lando"), 1,0,L);     

    $pdf->Cell(4, 5 ,eo("I"), 1,0,L);   
    

    $pdf->Cell(15, 5 ,'IPago', 1,0,R);    //1. Anzahlung gewissermaßen
    $pdf->Cell(18, 5 ,'APago', 1,0,R);    
    $pdf->Cell(18, 5 ,'SPago', 1,0,R);      
    
    $pdf->Cell(18, 5 ,'kotizo', 1,0,R);    
    
    $pdf->Cell(15, 5 ,'restas', 1,1,R);    
}
 define('FPDF_FONTPATH','./font/');
 
 $font='TEMPO';
 
 $pdf=new FPDF();
 $pdf->AddFont($font,'',$font.'.php');
 $pdf->AddFont($font.'D','',$font.'D.php');
 $pdf->Open();
 $pdf->AddPage();
 $pdf->SetFont($font.'D','',12);

 $pdf->text(15,17, eo("IS-Zimmerauflistung: ".$_SESSION["renkontigxo"]->datoj[nomo]." in ".$_SESSION["renkontigxo"]->datoj[loko]));
 $pdf->text(15,23, "Dato:".date('Y-m-d'));
 
 $pdf->setY(40);
 
//"select l.ID,l.partopreno,c.nomo,c.ID,c.renkontigxo,pp.id,partoprenantoID,p.ID from cxambroj,litonoktoj,partoprenoj,partoprenantoj where c.ID=l.ID and c.renkontigxo='".$_SESSION["renkontigxo"]->datoj[ID]."' and l.partopreno order by alvenstato,personanomo,nomo"

//TODO: la SQL estis tute fusxe - cxu oni ne uzis gxin,
// aux cxu gxi tamen funkciis?

// TODO: eltrovu, kiel funkciu la demando kaj
//  korektu lauxe.

$rezulto =
sql_faru(datumbazdemando(array("l.ID", "l.partopreno", "c.nomo", "c.ID", "c.renkontigxo",
							   "pp.id", "partoprenantoID", "p.ID"),
						 array("cxambroj" => "c", "litonoktoj" => "l",
							   "partoprenoj" => "pp", "partoprenantoj" => "p"),
						 array("c.ID = l.cxambro",
							   "l.partopreno = pp.id",
							   "pp.partoprenantoID = p.ID"),
						 "c.renkontigxo"
						 array("order" => "alvenstato, personanomo, nomo"));
		 //TODO: hier auch noch parametrisieren

 kaplinio();
 $pdf->SetFont($font,'',12);
 while ($row = mysql_fetch_array($rezulto, MYSQL_NUM))
  {
    $partoprenanto = new Partoprenanto($row[0]);
    
    $partopreno = new Partopreno($row[1]);
    $ko = new Kotizo($partopreno,$partoprenanto,$_SESSION["renkontigxo"]);
    $kotizo += $ko->kotizo;
    
    $pdf->Cell(4, 5 ,eo($partopreno->datoj[alvenstato]), 1,0,L);   
    
    
    $pdf->Cell(30, 5 ,malgrandigu(eo($partoprenanto->datoj[personanomo]),28), 1,0,L);    
    $pdf->Cell(30, 5 ,malgrandigu(eo($partoprenanto->datoj[nomo]),28), 1,0,L);
    $pdf->Cell(4, 5 ,$ko->partoprentagoj, 1,0,R);    
       
    $pdf->Cell(20, 5 ,eo($partoprenanto->landonomo()), 1,0,L);
    if ($partopreno->datoj[invitilosendata]!='0000-00-00')
        $aus='J';
    else
        $aus='';
    $pdf->Cell(4, 5 ,eo($aus), 1,0,L);   
    
    if ($partoprenanto->landokategorio()=='C' and $aus=='J' and $ko->antauxpago>=5.00)
    {
      $pdf->Cell(15, 5 ,'5.00', 1,0,R);    //1. Anzahlung gewissermaßen
      $pdf->Cell(18, 5 ,$ko->antauxpago-5.00, 1,0,R);    
      $APago+=$ko->antauxpago-5.00;
      $IPago+=5;
    }
    else
    {
      $pdf->Cell(15, 5 ,'', 1,0,R); 
      $pdf->Cell(18, 5 ,$ko->antauxpago, 1,0,R);    
      $APago+=$ko->antauxpago;
     }
    $pdf->Cell(18, 5 ,number_format($ko->surlokapago, 2, '.', ''), 1,0,R);      
    $SPago +=$ko->surlokapago;
    
    $aus3=$ko->kotizo;
    if ($partopreno->datoj[alvenstato]=='m') $aus3='';
    $pdf->Cell(18, 5 ,number_format($aus3, 2, '.', ''), 1,0,R);    
    $Skotizo += $aus3;
    
    $restas = $aus3-$ko->surlokapago-$ko->antauxpago;
    $resto += $restas;
    $pdf->Cell(15, 5 ,number_format($restas, 2, '.', ''), 1,1,R);    
    
    if ($pdf->getY()>260)
    {
     $pdf->SetFont($font.'D','',12);
     $pdf->Cell(4, 5 ,"", 1,0,L);    

     $pdf->Cell(30, 5 ,"", 1,0,L);    
     $pdf->Cell(30, 5 ,"", 1,0,L);
     $pdf->Cell(4, 5 ,"", 1,0,R);    
      
     $pdf->Cell(20, 5 ,'Summe:', 1,0,R);     
     $pdf->Cell(4, 5 ,'', 1,0,L);   
    

     $pdf->Cell(15, 5 ,nf($IPago), 1,0,R);    //1. Anzahlung gewissermaßen
     $TIPago+=$IPago;$IPago=0;
     $pdf->Cell(18, 5 ,nf($APago), 1,0,R);    
     $TAPago+=$APago;$APago=0;
     $pdf->Cell(18, 5 ,nf($SPago), 1,0,R);      
     $TSPago+=$SPago;$SPago=0;
     $pdf->Cell(18, 5 ,nf($Skotizo), 1,0,R);    
     $TSkotizo+=$Skotizo;$Skotizo=0;
     $pdf->Cell(15, 5 ,'', 1,1,R);    
           
     $pdf->AddPage();
     kaplinio();  
     $pdf->SetFont($font,'',12);
    }
  }
   $pdf->SetFont($font.'D','',12);

    $pdf->Cell(4, 5 ,"", 1,0,L);    

     $pdf->Cell(30, 5 ,"", 1,0,L);    
     $pdf->Cell(30, 5 ,"", 1,0,L);
     $pdf->Cell(4, 5 ,"", 1,0,R);    
      
     $pdf->Cell(20, 5 ,'Summe:', 1,0,R);     
     $pdf->Cell(4, 5 ,'', 1,0,L);   
    

     $pdf->Cell(15, 5 ,nf($IPago), 1,0,R);    //1. Anzahlung gewissermaßen
     $TIPago+=$IPago;$IPago=0;
     $pdf->Cell(18, 5 ,nf($APago), 1,0,R);    
     $TAPago+=$APago;$APago=0;
     $pdf->Cell(18, 5 ,nf($SPago), 1,0,R);      
     $TSPago+=$SPago;$SPago=0;
     $pdf->Cell(18, 5 ,nf($Skotizo), 1,0,R);    
     $TSkotizo+=$Skotizo;$Skotizo=0;
     $pdf->Cell(15, 5 ,'', 1,1,R);    
  
  $pdf->Cell(4, 5 ,"", 1,0,L);    

     $pdf->Cell(30, 5 ,"", 1,0,L);    
     $pdf->Cell(30, 5 ,"", 1,0,L);
     $pdf->Cell(4, 5 ,"", 1,0,R);    
      
     $pdf->Cell(20, 5 ,'gesamt:', 1,0,R);     
     $pdf->Cell(4, 5 ,'', 1,0,L);   
    
     $pdf->Cell(15, 5 ,nf($TIPago), 1,0,R);    //1. Anzahlung gewissermaßen
     $pdf->Cell(18, 5 ,nf($TAPago), 1,0,R);    
     $pdf->Cell(18, 5 ,nf($TSPago), 1,0,R);      
     $pdf->Cell(18, 5 ,nf($TSkotizo), 1,0,R);    
     $pdf->Cell(15, 5 ,'', 1,1,R);    
     
   $pdf->Output("finkalkulo.pdf");
   ligu("finkalkulo.pdf","els^uti la kalkuladon.","_top","jes");  

?>