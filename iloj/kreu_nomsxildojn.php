<?


 define('FPDF_FONTPATH', $prafix.'/iloj/fpdf/tiparoj/');
// define('FPDF_FONTPATH','./font/');

 require_once($prafix.'/iloj/fpdf/ufpdf.php');
  
 class Nomsxildo
 {
   var $font='FreeSans';
   var $alternativo = 'Times';
   var $x=5;
   var $y=10;
   var $pdf;
   var $maxY = 260;
   var $maxX;
   
   function Nomsxildo()
   {
	 $this->pdf=new UFPDF();
	 $this->pdf->setAutoPageBreak(false);

	 $this->pdf->AddFont($this->font,'',$this->font.'.php');
	 $this->pdf->AddFont($this->font,'B',$this->font.'Bold.php');

	 // $this->pdf->AddFont($this->alternativo, '', 'times.php');
	 //	 $this->pdf->AddFont($this->alternativo, 'B', 'timesb.php');
	 //	 $this->pdf->AddFont($this->alternativo, 'I', 'timesi.php');
	 //	 $this->pdf->AddFont($this->alternativo, 'BI', 'timesbi.php');

// 	 $this->pdf->AddFont('TEMPO','','TEMPO.php');
	 $this->pdf->SetFont($this->font,'',15);
	 $this->pdf->Open();
	 //$this->pdf->SetLeftMargin(0,8);
	 $this->pdf->AddPage(); 
   }

 function esso($s)
 {
//    if (strpos($s,utf8_encode('ß'))>0) $this->pdf->SetFont('TEMPO','',14); 
//    return $s;
 }

   /**
	* kreas nomsxildon.
	* $x, $y bazaj koordinatoj
	* $partoprenantoID - la identifikilo de la partoprenanto.
	* specialaj nomsxildoj:
	*  partoprenoID == -1: printu specialan nomsxildon,
	*             tiam partoprenantoID nomas la identifikilon
	*             en la nomsxildo-tabelo.
	*  partoprenoID == 0: printu malplenajn nomsxildojn.
	*/
 function kreu_nomsxildon($x,$y,$partoprenantoID,$partoprenoID,$savu)
 {

   if ($partoprenoID == -1)
	 {
	   $dungito= new Speciala_Nomsxildo($partoprenantoID);
	   echo "<!--";
	   var_export($dungito);
	   echo "-->";
	   $this->kreu_nomsxildon_interne($x, $y,
									  $dungito->datoj['titolo_esperante'],
									  $dungito->datoj['nomo'],
									  $dungito->datoj['funkcio_esperante'],
									  20, 0, 0);
	 }
   else if ($partoprenoID == 0)
	 {
	   $this->kreu_nomsxildon_interne($x, $y, "","", "", 20, 0, 0);
	   
	 }
   else
	 {
	   $partopreno = new Partopreno($partoprenoID);
	   $partoprenanto = new Partoprenanto($partoprenantoID);
   
	   if ($savu=="J")
		 {
		   $partopreno->datoj[havasNomsxildon]='P';
		   $partopreno->skribu();
		 }
	   $this->kreu_nomsxildon_interne($x,
									  $y,
									  $partoprenanto->datoj['sxildnomo'] ?
									  $partoprenanto->datoj['sxildnomo'] :
									  $partoprenanto->datoj['personanomo'],
									  $partoprenanto->datoj['nomo'],
								  $partoprenanto->datoj['sxildlando'] ?
									  $partoprenanto->datoj['sxildlando'] :
									  eltrovu_landon($partoprenanto->datoj['lando']),
									  $partopreno->datoj['agxo'],
									  $partopreno->datoj['de'],
									  $partopreno->datoj['gxis']
									  );
	 }
 }


   function kreu_nomsxildon_interne($x,$y,$personanomo, $familianomo, $landonomo, $agxo,
									$de, $gxis)
   {


	 $this->pdf->SetLineWidth(0.1);
	 $this->pdf->rect($x,$y,90,55); // cxirkauxa kadro

	 // $this->pdf->rect($x,$y,35,10); // kadro cxirkaux "Esperanto"
	 $this->pdf->image("bildoj/eo-echt.png",$x+1.4,$y+1.6, 12, 10);
	 $this->pdf->setFontSize(15);
	 $this->pdf->text($x+15, $y+9, "ESPERANTO");




	 if (((int)$agxo) < 18)
	   {
		 $plena = "malplena-is";
	   }
	 else
	   {
		 $plena = "nur-nigra";
	   }
	/* $this->pdf->image("bildoj/x-${plena}.png", $x+67.6, $y+3.25, 8,9);*/



	 $this->pdf->image("bildoj/is-enblemo-skizo-luisa-5-granda-{$plena}.png",
			   $x + 60, $y + 3, 20, 12);

	 //	 $this->pdf->setFont($this->alternativo, 'B', 15);
	 //	 $this->pdf->text($x+40, $y+13, "IS");
	 //	 $this->pdf->text($x+42, $y+13, "S");
	 //	 $this->pdf->text($x+51, $y+13, "06");
	 //	 $this->pdf->text($x+54, $y+13, "6");
//	 $this->pdf->setFont('', 'B', 17);
//	 $this->pdf->text($x+62, $y+10, "IS");
//	 $this->pdf->text($x+76, $y+10, "06");

	 // 	 $this->pdf->setFontSize(30);
	 // 	 $this->pdf->text($x+43, $y+10, "X");
	 
	 $this->pdf->setFont('');
	 $this->pdf->setXY($x+2.8, $y+27.6);

	 $pers_grandeco = $this->malgrandigu(uni($personanomo ." "), 41.5, 39);
	 $fam_grandeco = $this->malgrandigu(uni($familianomo), 37.4, 39);
	 if ($pers_grandeco >= $fam_grandeco)
	 {
	    $this->pdf->setFontSize($pers_grandeco);
	    $this->pdf->cell(41.5, 0, uni($personanomo ." "), 0, 0, 'R');
	    $this->pdf->setFontSize($fam_grandeco);
	    $this->pdf->cell(37.3, 0, uni($familianomo),0, 2, 'L');
	 }
	 else
	 {
	    $this->malgrandigu(uni($personanomo . " " . $familianomo),
			       41.5 + 37.4, 39);
	    $this->pdf->cell(41.5 + 37.4, 0,
			     uni($personanomo ." " . $familianomo),
			     0, 0, 'L');
	 }

	 $this->pdf->setXY($x+6.9, $y+35.7);

	 //	 $this->pdf->setFontSize(13);
	 $this->malgrandigu(uni($landonomo), 48.35, 14);
	 $this->pdf->cell(48, 6, uni($landonomo), 0, 0, 'C');


	 if ($de != 0)
	   {
		 $ek = date("d", strtotime($de));
		 $fin = date("d", strtotime($gxis));
		 $degxis =  $ek . "a â€“ " . $fin . "a";
	   }
	 else
	   {
		 $degxis = "";
	   }
	 $this->pdf->setFontSize(8);
	 $this->pdf->cell(34.6, 7, uni($degxis), 0, 1, 'R');

	 $this->pdf->setX($x+6.9);
//	 $this->pdf->rect($x, $y+45.5, 65, 12);

	 $this->pdf->setFontSize(12);

	  $ren = $_SESSION['renkontigxo'];
	 // TODO: la finteksto povas ankaux veni al la datumbazo
	 $this->pdf->multiCell(76.2, 5.1,
				$ren->datoj['nomo'] . " de GEJ \n" .
				"en " . $ren->datoj['loko'] . ", Germanio",
/*			   "49a Internacia Seminario de GEJ\n"."en Xanten, Germanio", */
						   0, "C");

   }

   function malgrandigu($teksto, $largxeco, $orgGrandeco)
   {
	 $i = $orgGrandeco + 1;
	 do
	   {
		 $i = $i - 0.5;
		 $this->pdf->setFontSize($i);
	   }
	 while ($this->pdf->GetStringWidth($teksto) > $largxeco);
	return $i;

   }

 
   function kaju($pID,$pnID,$savu='ne')
   {
	 if ($this->y > $this->maxY)
	   {
		 $this->pdf->AddPage();
		 $this->x=5;
		 $this->y=10;
	   }
	 
	 $this->kreu_nomsxildon($this->x,$this->y,
							$pID,$pnID,
							$savu);
	 
	 $this->x+=90;
	 if ($this->x>150)
	   {
		 $this->y+=55;
		 $this->x=5;
	   }
   }  
 
   function sendu()
   { 
	 while ($this->y <= $this->maxY )
	   $this->kaju(0,0);
	 $this->pdf->Output('dosieroj_generitaj/nomsxildoj.pdf');
   }
}
?>
