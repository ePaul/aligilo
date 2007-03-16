<?
define('FPDF_FONTPATH','./font/');
require_once($prafix . '/iloj/fpdf/fpdf.php');

function dulingva($esperanta, $germana, $lingvo)
{
  if($lingvo == "eo")
	{
	  return eo($esperanta);
	}
  else
	return $germana;
}

 
class Konfirmilo
{
  var $font='TEMPO';
  var $pdf;
  var $germane;


  /**
   * konstruilo.
   * Gxi kreas FPDF-objekton.
   */
  function Konfirmilo()
  {
	$this->pdf=new FPDF();
	$this->pdf->AddFont($this->font,'',$this->font.'.php');
	$this->pdf->AddFont($this->font.'D','',$this->font.'D.php');
	$this->pdf->SetFont($this->font,'',15);
	$this->pdf->Open();  
	$this->pdf->SetTopMargin(0);
  }

  // versxajne ne plu bezonata?
  function esso($s)
  {
	//if (strpos($s,utf8_encode('ß'))>0) $this->pdf->SetFont('Arial','',12);   
  }

  /**
   * kreas konfirmilon en unu el du lingvoj.
   * $partopreno    - Partopreno-objekto
   * $partoprenanto - la Partoprenanto-objekto
   * $renkontigxo   - Renkontigxo-objekto
   * $kotizo        - Kotizo-objekto (estu kreita el la tri antauxe
   *                   menciitaj objektoj)
   * $lingvo - aux "de" aux "eo".
   */
  function kreu_konfirmilon_unulingve($partopreno, $partoprenanto,
									  $renkontigxo, $kotizo, $lingvo)
  {
	$this->pdf->AddPage(); 
	$this->pdf->SetLeftMargin(20);
	$this->pdf->SetRightMargin(20);
	if ($lingvo == "eo")
	  {
		$jesne = array('J'=>'jes','N'=>'ne','n'=>'ne',''=>'ne');
	  }
	else
	  {
		$jesne = array('J'=>'ja','N'=>'nein','n'=>'nein',''=>'nein');
	  }

	$this->pdf->Image('bildoj/eo-logo.png', 162, 10, 28);
	
	$this->pdf->SetFont($this->font,'',30);
	$this->pdf->text(39,17, "germana esperanto-junularo");
	$this->pdf->text(43+2,25, "deutsche esperanto-jugend");
	$this->pdf->SetFont('Arial','I',12);
	$this->pdf->text(105,34, ".... wir machen Völkerverständigung");
 
	$this->pdf->SetFont($this->font,'',8); 
	// $this->pdf->SetFont('Arial','',8); 
	// TODO: an 2005 anpassen
	//TODO: aus der DB nehmen
	$this->pdf->text(20,51, "Julia Noe, August-Bebel-Str. 42/42, 15234 Frankfurt/Oder, Germanio");
	// $this->pdf->text(20,51, "Martin Sawitzki, Max-Planck-Ring 8d, 98693 Ilmenau, Germanio");
	$this->pdf->line(20,53,97,53);

	// falc- kaj truil-markiloj
	$this->pdf->line(4,100,9,100);
	$this->pdf->line(4,147,7,147);
	$this->pdf->line(4,198,9,198);
     

	// adreso de la partoprenanto
	$this->pdf->SetFont($this->font.'D','',12);
	$this->esso($partoprenanto->datoj[personanomo].
				$partoprenanto->datoj[adresaldonajxo].
				$partoprenanto->datoj[strato].
				$partoprenanto->datoj[posxtkodo]);
	$this->pdf->setY(59);
	$this->pdf->write(5, eo($partoprenanto->datoj[personanomo]." ".$partoprenanto->datoj[nomo]));
	$this->pdf->ln();
	if ($partoprenanto->datoj[adresaldonajxo]!='')
	  {
		$this->pdf->write(5,eo($partoprenanto->datoj[adresaldonajxo]));
		$this->pdf->ln();
	  }
	$this->pdf->write(5,eo($partoprenanto->datoj[strato]));
	$this->pdf->ln();
	$this->pdf->write(5,eo($partoprenanto->datoj[posxtkodo]." ".$partoprenanto->datoj[urbo]));
	$this->pdf->ln();
	$this->pdf->write(5,eo(eltrovu_landon_lokalingve($partoprenanto->datoj[lando])));
 
	$this->pdf->SetFont($this->font,'',10);
	$this->pdf->setY(90);
	$this->pdf->write(5, "Saluton!");
	$this->pdf->ln();

	$this->pdf->write(5, eo(donu_tekston_lauxlingve("konf2-enkonduko",
													$lingvo, $renkontigxo)));

	//    $this->pdf->write(5,
	// 					 dulingva("La organiza teamo tre g^ojas ke vi intencas veni al la".
	// 							  " c^ijara IS en " . $renkontigxo->datoj['loko'].
	// 							  ". Jen viaj datumoj:",
	// 							  "Das Organisationsteam freut sich sehr, dass du zum diesjährigen IS in Wetzlar kommen willst. Hier deine Daten:", $lingvo));
	$this->pdf->ln();
 
	$this->pdf->SetFontSize(10);
	$this->pdf->setXY(30,102);
	if ($kotizo->landakategorio=='C')
	  $this->pdf->cell(40,4,dulingva("Alveno de via alig^ilo:",
									 "Ankunft der Anmeldung:", $lingvo),0,2,'R');
	else
	  $this->pdf->cell(40,4,eo("Alveno de via antau^pago:",
							   "Ankunft der Anzahlung:", $lingvo),0,2,'R');
 
	$this->pdf->cell(40,4,eo("Via log^landa kategorio:",
							 "Deine Landeskategorie", $lingvo), 0, 2, 'R');
	$this->pdf->cell(40,4,eo("Via ag^kategorio:",
							 "Deine Alterskategorie", $lingvo), 0, 2, 'R');
	$this->pdf->cell(40,4,eo("Partoprentagoj:",
							 "Teilnahmetage", $lingvo), 0, 2, 'R');
	// TODO: Se ni dekuplas memzorgo kaj amaslogxejo (aux junulargasto kaj mangxado),
	// kreu diversajn kampojn
	$this->pdf->cell(40,4,eo("Memzorganto:",
							 "Selbstversorger:"),0,2,'R');
	$this->pdf->cell(40,4,eo("Dulita c^ambro:",
							 "Zweibettzimmer:", $lingvo),0,2,'R');

	$this->pdf->SetFont($this->font.'D','',10);

	$kategoritekstoj = array("de" => array("antaux" => "vor dem",
										   "post" => "nach dem",
										   "ne" => "Ã¼berhaupt nicht"),
							 "eo" => array("antaux" => "antau^ la",
										   "post" => "post la",
										   "ne" => "ankorau^ ne"));

	if ($kotizo->krom_surloka > 0.05)
	  {
		$alk = $kategoritekstoj[$lingvo]['ne'];
	  }
	else if ($kotizo->aligxkategorio == 2)
	  {
		$alk = $kategoriteksto[$lingvo]['antaux'] . ' ' . $renkontigxo->datoj['meza'];
	  }
	else
	  {
		$alk = $kategoriteksto[$lingvo]['post'] . " " . $renkontigxo->datoj['meza'];
	  }

	//    switch ($kotizo->aligxkategorio)
	// 	 {
	// 	 case 2: $alk="antau^ la 01.11.2004";break;   //TODO: aus der DB holen
	// 	 case 1: $alk="post la 01.11.2004";break;
	// 	   // case 0: $alk="post la 01.12.2003";break;
	// 	 }

	//    // TODO: Kam überhaupt schon Anzahlung?
	//    if ($kotizo->krom_surloka > 5)
	// 	 {
	// 	   $alk = "ankorau^ ne";
	// 	 }

	$this->pdf->setXY(70,102);
	$this->pdf->cell(40,4,eo($alk),0,2,'L');
	$this->pdf->cell(40,4,eo($kotizo->landakategorio),0,2,'L');
	//  switch ($kotizo->agxkategorio)
	//  {
	//     case 2: $aka="g^is 20 jaroj";break;   //TODO: aus der DB holen
	//     case 1: $aka="21 g^is 26 jaroj";break;
	//     case 0: $aka="pli ol 26 jaroj";break;
	//  }
	$aka = $kotizo->formatu_agxkategorion($renkontigxo);
	$this->pdf->cell(40,4,eo($aka),0,2,'L');
	$this->pdf->cell(40,4,eo($kotizo->partoprentagoj),0,2,'L');
	if ($partopreno->datoj[domotipo]=='M')
	  {
		$memzorganto=dulingva("jes", "ja", $lingvo);
		$domotipo='memzorgantejo';
		$en_domo = dulingva("en la memzorgantejo",
							"im Memzorgantejo", $lingvo);
	  }
	else
	  {
		$memzorganto=dulingva("ne", "nein", $lingvo);
		$domotipo='junulargastejo';
		$en_domo = dulingva("en la junulargastejo", "in der Jugendherberge", $lingvo);
	  }
	$this->pdf->cell(40,4,$memzorganto,0,2,'L');
 
	//certigi, ke vere estas dulita cxambro

	if ($kotizo->litoj=='2')
	  $this->pdf->cell(40,4,$jesne['J'],0,2,'L');
	else
	  $this->pdf->cell(40,4,$jesne['N'],0,2,'L');
 
	$this->pdf->SetFont($this->font,'',10);
	$this->pdf->setXY(120,102);
	$this->pdf->cell(40,4,dulingva("Via kotizo estas:",
								   "Dein Beitrag ist:", $lingvo),
					 0,2,'R');
	if ($kotizo->kromekskurso > 0)
	  {
		$this->pdf->cell(40,4,dulingva("ekskursa bileto:",
									   "Teilnahme am Ausflug:", $lingvo),0,2,'R');
	  }
	$this->pdf->cell(40,4,dulingva("Vi antau^pagis:",
								   "Anzahlung:", $lingvo),0,2,'R');
	$this->pdf->cell(40,4,dulingva("Krompagoj:", "Zuzahlungen",
								   $lingvo),0,2,'R');
	$this->pdf->cell(40,4,dulingva("Rabato:", "Rabatt:", $lingvo),0,2,'R');
	$this->pdf->cell(40,4,dulingva("Restas pagenda:", "Bleibt zu zahlen:", $lingvo),0,2,'R');

	$this->pdf->SetFont($this->font.'D','',8);
	// TODO: bessere Formulierung: Bitte den Rest zum IS bar mitbringen
	$this->pdf->cell(65,4,dulingva("Dum la IS ni akceptos nur eu^ropajn eu^rojn!",
								   "Während des IS nehmen wir nur europäische Euro an!",
								   $lingvo),0,2,'R');
	$this->pdf->SetFont($this->font.'D','',10);
	$this->pdf->setXY(165,102);
	$this->pdf->cell(20,4,number_format($kotizo->bazakotizo,2)." EUR",0,2,'R');
	if ($kotizo->kromekskurso > 0)
	  {
		$this->pdf->cell(20,4,number_format($kotizo->kromekskurso,2)." EUR",0,2,'R');
	  }

	$this->pdf->cell(20,4,number_format($kotizo->antauxpago,2)." EUR",0,2,'R');
	$this->pdf->cell(20,4,number_format($kotizo->krompago-$kotizo->kromekskurso,2)." EUR",0,2,'R');
	$this->pdf->cell(20,4,number_format($kotizo->rabato,2)." EUR",0,2,'R');
	$this->pdf->cell(20,4,number_format($kotizo->pagenda,2)." EUR",0,2,'R');

	$this->pdf->SetFont($this->font,'',10);
	$this->pdf->setY(130);
	$litoj = eltrovu_litojn($partopreno->datoj[ID]);
	//echo "Litoj: ".$litoj["sumo"] ;
	//echo "K:".$kotizo->antauxpago." and ".$kotizo->landakategorio;

	if ($partopreno->datoj[partoprentipo]!='t' and $domotipo=='junulargastejo')
	  {
		$teksto = donu_tekston_lauxlingve("konf2-parttempa", $lingvo, $renkontigxo);
	  }
	else // TODO: (Cxu ankaux en Wetzlar?) In Trier haben wir genügend Betten
	  if ($kotizo->krom_surloka > 5)
		{
		  $teksto = anstatauxu(donu_tekston_lauxlingve("konf2-mankas-antauxpago",
													   $lingvo, $renkontigxo),
							   array("{{sumo}}" =>
									 $kotizo->minimuma_antauxpago() - $kotizo->antauxpago));
		}
	  else if ($litoj["sumo"] < $kotizo->partoprentagoj and $domotipo=='junulargastejo') 
		{
		  if ($litoj["sumo"]!='0') // oni erare donis malgxustan nombron da litoj
			{
			  erareldono("Malg^usta litonombro. Mankus noktoj: (noktonombro:".$litoj["sumo"].")");
			  halt();
			}
		  else
			{
			  $teksto = donu_tekston_lauxlingve("mankas-cxambro", $lingvo, $renkontigxo);
			}

		}
	  else 
		{ //se cxio enordas
		  $teksto = anstatauxu(donu_tekston_lauxlingve("konf2-cxio-enordas",
													   $lingvo, $renkontigxo),
							   array("{{en_domo}}" => $en_domo));
		  $cioenordo = 'jes';
		}

	echo "<!-- teksto: $teksto -->\n";

	$this->pdf->write(5, eo($teksto));
	$this->pdf->ln(10);
	//$this->pdf->setY(155);
	$this->pdf->SetFont($this->font.'D','',12);
	$this->pdf->cell(20,5, dulingva("Gravaj informoj:",
									"Wichtige Informationen", $lingvo),0,2);
	$this->pdf->SetFont($this->font,'',10);
	$this->pdf->setX(25);

	$teksto = donu_tekston_lauxlingve("konf2-gravaj-informoj", $lingvo, $renkontigxo);

	if ($partopreno->datoj['agxo']< 18 )
	  $teksto .= donu_tekston_lauxlingve("konf2-junulo", $lingvo, $renkontigxo);
	if ($domotipo=='junulargastejo' and $cioenordo == 'jes')
	  {
		$teksto .= donu_tekston_lauxlingve("konf2-21a-horo", $lingvo, $renkontigxo);
		//aus der DB zaubern
		// 	   $teksto.="Ni povas garantii, ke vi ricevos liton, se vi alvenas g^is la 21a horo. Se vi alvenos pli poste, bonvolu sciigi nin pri via alventempo, por ke ni povu rezervi liton por vi. Alikaze vi riskas, ke vi ne ricevos liton.\n";
	  }
	else if ($domotipo=='memzorgantejo')
	  {
		$teksto .= donu_tekston_lauxlingve("konf2-memzorganto", $lingvo, $renkontigxo);
		// 	   $teksto.="Kiel memzorganto ni povas garantii al vi, ke c^iam estas loko en la amaslog^ejo. Kunportu vian dormsakon, aermatracon, mang^ilaron kaj c^ion ajn, kion vi bezonas.\n";
	  }
	if ($partoprenanto->datoj[lando]==HEJMLANDO) //germanio
	  {
		$teksto .= donu_tekston_lauxlingve("konf2-membreco-averto", $lingvo, $renkontigxo);
	  }
 
	$teksto.=' ';
	$this->pdf->multicell(170,5, eo($teksto));

	// $this->pdf->ln(5);
	// $this->pdf->setY(200);
 
	// TODO: cxu sencas absoluta pozicio?
	$this->pdf->setY(232);
   
	$this->pdf->write(5, eo(donu_tekston_lauxlingve("konf2-elkonduko",
													$lingvo, $renkontigxo)));
 
	$this->pdf->Image('bildoj/julia-subskribo-transparent.png', 100, 251, 80); // TODO: allgemein

	$this->pdf->Ln(10.0);

	$this->pdf->SetFont($this->font.'D','',12);
	$this->pdf->cell(20,5, dulingva("Enhavo:", "Inhalt", $lingvo),0,2);
	$this->pdf->SetFont($this->font,'',10);
 
	$enhavo = dulingva("- tiu c^i konfirmilo\n".
					   "- la 2a informilo\n",
					   "- Diese BestÃ¤tigung\n" .
					   "- Die Esperanto-Version dieser BestÃ¤tigung\n" .
					   "- Das zweite Informilo\n", $lingvo);
	if ($this->germane and $lingvo == "eo")
	  {
		$enhavo .= "- la germanlingva versio de tiu c^i konfirmilo\n";
	  }
	if ($partopreno->datoj['agxo']<'18') 
	  $enhavo .= dulingva("- gepatra permeso de via IS-partopreno",
						  "- Elterliche Erlaubnis deiner IS-Teilnahme", $lingvo);
	// $this->pdf->setXY(25,205);
	$this->pdf->multicell(170,5, eo($enhavo));

  }


  function kreu_konfirmilon($partoprenoID,$partoprenantoID,$savu='NE')
  {
 
	$partopreno = new Partopreno($partoprenoID);


	$partoprenanto = new Partoprenanto($partoprenantoID);
	$renkontigxo = new Renkontigxo($partopreno->datoj['renkontigxoID']);

	if($partopreno->datoj['germanakonfirmilo']{0} == 'J')
	  {
		$this->germane = true;
	  }
	else
	  {
		$this->germane = false;
	  }

 

	$kotizo = new Kotizo($partopreno,$partoprenanto,$renkontigxo);


	$this->kreu_konfirmilon_unulingve($partopreno, $partoprenanto,
									  $renkontigxo, $kotizo, "eo");

	if ($this->germane)
	  {
		$this->kreu_konfirmilon_unulingve($partopreno, $partoprenanto,
										  $renkontigxo, $kotizo, "de");
	  }

	if ($partopreno->datoj['agxo']<'18') //(Gepatra klarigo mit ranhängen)
	  {
		$this->kreu_permesilon($partoprenanto, $renkontigxo);
	  }

 
	if ($savu=='J')
	  {
		$partopreno->datoj['2akonfirmilosendata']=date("Y-m-d");
		$partopreno->skribu();
	  }

  }

  /*
   *
   */ 
  function kreu_permesilon($partoprenanto, $renkontigxo, $defVira = "")
  {
	if ($partoprenanto)
	  {
		$vira = ($partoprenanto->datoj['sekso']{0} == 'v');
	  }
	else
	  {
		$vira = $defVira;
	  }
	$this->pdf->AddPage(); 
	$this->pdf->SetY(30);
	$this->pdf->SetFont($this->font,'',30);
	$this->pdf->cell(160,10,"Gepatra permeso por via IS partopreno",0,1,C);
	$this->pdf->SetFont($this->font,'',14);
	$this->pdf->cell(160,10,eo("(Nur por partoprenantoj, kiuj ankorau^ ne havas 18 jarojn je " . $renkontigxo->datoj['de'] . ")"),0,1,C); 
	 
	$this->pdf->SetY(55);
	$this->pdf->write(5,"Nomo de la partoprenanto:  ");
	$this->pdf->cell(100, 5, eo($partoprenanto->datoj['personanomo'] . " " . $partoprenanto->datoj['nomo']), "B", 1, 'C');
	//   $this->pdf->line(76,60,180,60);
	$this->pdf->write(5,eo("\nSe vi je la komencig^o de la IS ankorau^ ne havas 18 jarojn, bonvolu nepre kunporti la suban permesilon de viaj gepatroj:\n\n")); 
	$this->pdf->SetFont($this->font."D");
	$this->pdf->write(5,eo("Mi permesas al mia " . ($vira ? "filo" : "filino" ) ." vojag^i al la Internacia Seminario kaj partopreni g^in. Krome " . ($vira? "li" : "s^i") . " rajtas sen gardpersono partopreni la ekskursojn (inklusive la nag^vesperon)."));
	$this->pdf->SetFont($this->font);
	$this->pdf->line(20,109,140,109);
	$this->pdf->SetY(110);
	$this->pdf->cell(80,5,"(dato kaj subskribo de la gepatroj)",0,1,C);
	$this->pdf->SetY(130);
	$this->pdf->SetFont($this->font,'',8);
	$this->pdf->cell(160,5,eo("Bonvolu uzi au^ la esperantlingvan, au^ la germanlingvan version / Benutze bitte entweder die deutsch-, oder die esperantosprachige Version."),0,1,C);
	$this->pdf->SetY(160);
	$this->pdf->SetFont($this->font,'',30);
	$this->pdf->cell(160,10,"Einverständnisserklärung der Eltern",0,1,C); 
	$this->pdf->SetFont($this->font,'',14);
	$this->pdf->cell(160,10,("(Nur für Teilnehmer, die am " .
							 $renkontigxo->datoj['de'] .
							 " noch nicht 18 Jahre alt sind.)"),0,1,C);

	$this->pdf->SetY(140+55);
	$this->pdf->write(5,"Name des Teilnehmers:  ");
	$this->pdf->cell(100, 5, eo($partoprenanto->datoj['personanomo'] . " " . $partoprenanto->datoj['nomo']), "B", 1, 'C');
	$this->pdf->write(5,("\nWenn du zu Beginn des IS noch keine 18 Jahre alt bist, bring bitte auf jeden Fall die untenstehende Erlaubnis von deinen Eltern mit:\n\n"));
	$this->pdf->SetFont($this->font."D");
	if ($vira)
	  {
		$this->pdf->write(5,"Ich erlaube meinem Sohn zur Internationalen Woche zu reisen und daran teilzunehmen. Weiterhin darf er ohne Aufsichtsperson an den Ausflügen (inklusive des Schwimmabends) teilnehmen."); 
	  }
	else
	  {
		$this->pdf->write(5,"Ich erlaube meiner Tochter zur Internationalen Woche zu reisen und daran teilzunehmen. Weiterhin darf sie ohne Aufsichtsperson an den Ausflügen (inklusive des Schwimmabends) teilnehmen."); 
	  }
	$this->pdf->SetFont($this->font);
	//   $this->pdf->write(5,"Ich erlaube meinem Sohn / meiner Tochter zum Internationalen Seminar zu reisen und daran teilzunehmen. Weiterhin darf er / sie ohne Aufsichtsperson an den Ausfluegen (inklusive des Schwimmabends) teilnehmen.")); 
	$this->pdf->line(20,140+109,140,109+140);
	$this->pdf->SetY(110+140);
	$this->pdf->cell(80,5,"(Datum und Unterschrift der Eltern)",0,1,C); 
	
  }
  


  function sendu($dosiernomo = 'dosieroj_generitaj/konfirmilo.pdf')
  {
	  $this->pdf->Output($dosiernomo);
  }

//echo "<A HREF=getpdf.php>finished</A>";
}
?>
