<?php
 define('FPDF_FONTPATH',$prafix.'/font/');
 require_once($prafix . '/iloj/fpdf/fpdf.php');
  
 class Mangxkupono
 {
 var $font='TEMPO';
 var $x=10;
 var $y=10;
 var $pdf;

   var $renkontigxo;
 
 function Mangxkupono($renkontigxo)
 {
   $this->renkontigxo = $renkontigxo;
   
   $this->pdf=new FPDF();
   $this->pdf->AddFont($this->font,'',$this->font.'.php');
   $this->pdf->SetFont($this->font,'',15);
   $this->pdf->Open();
   $this->pdf->AddPage(); 
 }

 function esso($s)
 {
   //if (strpos($s,utf8_encode('ß'))>0) $this->pdf->SetFont('Arial','',18);   
 }
  
 function kreu_mangxkuponon($x,$y,$partoprenantoID,$partoprenoID,$savu,$vego)
 {
 
 $partopreno = new Partopreno($partoprenoID);
 $partoprenanto = new Partoprenanto($partoprenantoID);
 
 $this->pdf->setFontSize(15);
 $this->pdf->SetLineWidth(0.6);
 for ($i=0;$i<=6;$i++)
 {
   $this->pdf->rect($x+$i*10,$y,10,24);
   $this->pdf->rect($x+$i*10,$y+94,10,24);
   $this->pdf->rect($x+70,$y+24+$i*10,24,10);
 } 

  $this->pdf->rect($x,$y,94,118);
  $this->pdf->SetLineWidth(0.2);
  $this->pdf->rect($x,$y+40,53,38);

  $this->pdf->text($x+18,$y+31,eo("Matenmang^o"));
	  // TODO: eble prenu la germanajn nomojn el
	  // TODO:  datumbazo aux konfigurdosiero
  $this->pdf->text($x+22,$y+38,"Frühstück");

  $this->pdf->text($x+18,$y+84,eo("Vespermang^o"));
  $this->pdf->text($x+22,$y+91,eo("Abendessen"));
  
  $this->pdf->image("bildoj/tagmangxo.png",$x+56,$y+48,6);
  $this->pdf->image("bildoj/mittagessen.png",$x+62,$y+45,6);
  
  // TODO: ne uzu bildojn, sed rekte generu la tekston
  //  (kaj ne de 27.12. gxis 3.1., sed laux la renkontigxo-datoj)
  $this->pdf->image("bildoj/27.png",$x+2,$y+98,5);
  for ($i=28;$i<=31;$i++)
  {
    $this->pdf->image("bildoj/$i.png",$x+12+($i-28)*10,$y+98,5);
    $this->pdf->image("bildoj/$i.png",$x+2+($i-28)*10,$y+4,5);
    $this->pdf->text($x+74,$y+32+($i-28)*10,"$i.12");
  }
  for ($i=01;$i<=02;$i++)
  {
    $this->pdf->image("bildoj/$i.png",$x+12+($i+3)*10,$y+98,5);
    $this->pdf->image("bildoj/$i.png",$x+2+($i+3)*10,$y+4,5);
    $this->pdf->text($x+74,$y+32+($i+3)*10,"0$i.01.");
  }
  $this->pdf->image("bildoj/3.png",$x+62,$y+4,5);
  $this->pdf->text($x+74,$y+32+(3+3)*10,"03.01.");

  $this->pdf->setFontSize(20);
  $i=20;
  //$this->esso($partoprenanto->datoj[personanomo].$partoprenanto->datoj[nomo]);
  $nomo = eo($partoprenanto->datoj[personanomo]);
  if ($partoprenanto->datoj[sxildnomo]!='')  {$nomo = eo($partoprenanto->datoj[sxildnomo]);}
  
  while ($this->pdf->GetStringWidth($nomo)>47)
  {
     $i--;
     $this->pdf->setFontSize($i);
  }
  $this->pdf->text($x+5,$y+47,$nomo);
  $this->pdf->setFontSize(14);
  $this->pdf->text($x+5,$y+56,eo($partoprenanto->datoj[nomo]));
  
  $this->pdf->SetFont($this->font,'',15);
  
  $this->pdf->line($x+5,$y+48,$x+50,$y+48);
  $this->pdf->line($x+5,$y+57,$x+50,$y+57);
  $this->pdf->setFontSize(16);
  if ($partopreno->datoj['vegetare']=='J' or $vego=='J')
	{
	  $this->pdf->text($x+10,$y+66,eo("Vegetarano"));
	  $this->pdf->text($x+14,$y+73,eo("Vegetarier"));
	}
  else if ($partopreno->datoj['vegetare']=='A' or $vego=='A')
	{
	  $this->pdf->text($x+10,$y+66,eo("Vegano"));
	  $this->pdf->text($x+14,$y+73,eo("Veganer"));
	}
  else
	{
	  $this->pdf->text($x+10,$y+66,eo("Viandmang^anto"));
	  $this->pdf->text($x+14,$y+73,eo("Fleischesser"));
	}
  
  $this->pdf->image("bildoj/eo-echt.png",$x+72,$y+8,20,12);
  $this->pdf->image("bildoj/eo-echt.png",$x+72,$y+100,20,12);
  
  if ($partopreno->datoj[partoprentipo]!='t' and $partoprenoID!='0')
  {
    $dauro = kalkulu_tagojn($_SESSION["renkontigxo"]->datoj[de],$_SESSION["renkontigxo"]->datoj[gxis]);
    $tagoj = kalkulu_tagojn($partopreno->datoj[de],$partopreno->datoj[gxis]);
    $ekas = kalkulu_tagojn($_SESSION["renkontigxo"]->datoj[de],$partopreno->datoj[de]);
    
    for ($i=0;$i<$ekas;$i++)
    {
      $this->pdf->SetLineWidth(0.4);
      //matenmangxo
      $this->pdf->line($x+$i*10,$y,$x+10+$i*10, $y+24);
      $this->pdf->line($x+10+$i*10,$y,$x+1+$i*10, $y+24);
      //vespermangxo      
      $this->pdf->line($x+$i*10,$y+94,$x+10+$i*10, $y+24+94);
      $this->pdf->line($x+10+$i*10,$y+94,$x+1+$i*10, $y+24+94);
      //tagmangxo      
      $this->pdf->line($x+70,$y+24+$i*10,$x+94, $y+34+$i*10);
      $this->pdf->line($x+94,$y+24+$i*10,$x+70, $y+34+$i*10);
    }
    
    //la dekstra parto
    for ($i=$tagoj+$ekas;$i<$dauro;$i++)
    {
      $this->pdf->SetLineWidth(0.4);
      //matenmangxo
      $this->pdf->line($x+$i*10,$y,$x+10+$i*10, $y+24);
      $this->pdf->line($x+10+$i*10,$y,$x+1+$i*10, $y+24);
      //vespermangxo      
      $this->pdf->line($x+$i*10,$y+94,$x+10+$i*10, $y+24+94);
      $this->pdf->line($x+10+$i*10,$y+94,$x+1+$i*10, $y+24+94);
      //tagmangxo      
      $this->pdf->line($x+70,$y+24+$i*10,$x+94, $y+34+$i*10);
      $this->pdf->line($x+94,$y+24+$i*10,$x+70, $y+34+$i*10);
    }

  }
  
  $this->pdf->setFontSize(13);
  $this->pdf->setXY($x+72,$y+2);

  $loko = $this->renkontigxo->datoj['loko'];
  $lokolen = strlen($loko);
  if ($lokolen > 7 and substr($loko, $lokolen - 4) == 'burg')
  {
    $loko = substr($loko, 0, $lokolen - 4) . "- " .
	  substr($loko, $lokolen - 4);
  }
  $this->pdf->multicell(22,4, eo($this->renkontigxo->datoj['mallongigo'] .
                       ' en ' . $loko),
						'','C') ;
  $this->pdf->setFontSize(9);
  $this->pdf->setXY($x+71,$y+15);
  $this->pdf->multicell(22,4,eo($this->renkontigxo->datoj['de'] . " g^is\n" .
								$this->renkontigxo->datoj['gxis']),
						'','C');
  $this->pdf->setXY($x+72,$y+96);

	  // TODO: prenu el konfiguro
  $this->pdf->multicell(20,4,eo("Germana\n\nEsperanto-\n\nJunularo"),'','C');

  // TODO: text wirklich hochkant drucken (anstatt Bild)

  /*$pdf->text(10,20,eo("Tagmang^o"));
  $pdf->text(10,200,eo("Mittagessen"));
  $pdf->text(10,40,"27.12.");
    $pdf->text(10,60,"28.12.");
      $pdf->text(10,80,"29.12.");
        $pdf->text(10,100,"30.12.");
          $pdf->text(10,120,"31.12.");
            $pdf->text(10,140,"01.01.");
              $pdf->text(10,160,"02.01.");
                $pdf->text(10,180,"03.01.");*/


  if ($savu=="J")
	{
	  $partopreno->datoj['havasMangxkuponon']='P';
	  $partopreno->skribu();
	}
} 
 
   /**
	* aldonas mangxkuponon por la partoprenanto al la mangxkuponfolio.
	*/
function kaju($pID,$pnID,$savu='ne',$vego)
{
  if ($this->y>200)
  {
    $this->pdf->AddPage();
    $this->x=10;
    $this->y=10;
  }
  $this->kreu_mangxkuponon($this->x,$this->y,$pID,$pnID,$savu,$vego);  
  $this->x+=95;
  if ($this->x>150) {$this->y+=119;$this->x=10;}
}  
 
   function sendu($vego)
   { 
	 while ($this->y<200)
	   $this->kaju(0,0,'ne',$vego);
	 $this->pdf->Output('dosieroj_generitaj/mangxkuponoj.pdf');
   }
}
?>
