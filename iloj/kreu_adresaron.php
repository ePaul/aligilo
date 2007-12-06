<?php

 define('FPDF_FONTPATH', $prafix.'/iloj/fpdf/font/');
  require_once ('iloj/fpdf/ufpdf.php');
  echo "<!-- FPDF_FONTPATH: " . FPDF_FONTPATH . "-->";

  $fp = fopen($GLOBALS['prafix'] . "/dosieroj_generitaj/adresaro.csv","w"); //por la .csv versio
  $font='FreeSans';

  $pdf=new UFPDF();
  $pdf->AddFont($font,'',$font.'.php');
  $pdf->AddFont($font,'B',$font.'Bold.php');
  $pdf->SetFont($font,'',15);
  $pdf->Open();  

  $pdf->AddPage();


  $pdf->write(7, uni("Listo de Partoprenantoj\n" . $_SESSION["renkontigxo"]->datoj["nomo"] .
				   " en " . $_SESSION["renkontigxo"]->datoj["loko"] .
				   " (" . $_SESSION["renkontigxo"]->datoj["de"] . " g^is " .
				   $_SESSION["renkontigxo"]->datoj["gxis"] . ")\n"));
	if ('JES' == $granda)
	{
	  $pdf -> SetFont($font,'',12);
	  $pdf->write(8, uni("Bonvolu kontroli (kaj eble korekti) vian adreson en la adresaro, por ke en" .
	                     " la fina versio estu g^ustaj datoj."));
	  $pdf->ln(12);
	  $linlargxo = 7;
	  $interlinspaco = 13;
	}
	else
	{
	  $pdf->SetFont($font,'',9);
	  $pdf->write(3.7, uni("Vi rajtas uzi tiun adresaron nur por personaj celoj. Vi ne rajtas" .
				   " uzi g^in por amasaj leteroj au^ retmesag^oj (ankau^ ne por informi pri via" .
					" Esperanto-renkontig^o), kaj ne rajtas pludoni" .
				   " g^in (ec^ ne parte). " .
				   " Se amiko de vi (kiu partoprenis la " .
					 $_SESSION['renkontigxo']->datoj['mallongigo'].
					 ") ne ricevis la adresaron," .
				   " li povas mendi propran c^e is.admin@esperanto.de. La sama validas," .
				   " se vi perdis g^in.\n" .
					"Atentu, la g^usta sinsekvo de la adres-partoj sur leteroj - depende de la lando -" .
					" ofte ne estas la sama kiel tiu en tiu c^i listo. Informig^u antau^ eksendado" .
					" de letero (ekzemple per retpos^to al la ricevonto)."));
	  $pdf->ln(7);
	  $linlargxo = 3.0;
	  $interlinspaco = 4.05;
	}
  $pdf->write(($linlargxo*1.7),
	           uni("persona nomo; nomo; adresaldonaj^o; strato; pos^tkodo; urbo; lando; telefono; telefakso; retpos^to"));
  $pdf -> ln($interlinspaco);
  $pdf -> ln($interlinspaco);
  //  $demando = "select p.ID,pn.ID,p.nomo, personanomo,l.nomo,retposxto,adresaldonajxo,strato,posxtkodo,urbo,lando,telefono,telefakso,retposxto from partoprenantoj as p, partoprenoj as pn, landoj as l where pn.partoprenantoID=p.ID and l.ID=lando and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and alvenstato='a' order by personanomo,p.nomo";       
  $demando = datumbazdemando(array("p.ID", "pn.ID", "p.nomo", "personanomo",
								   "l.nomo", "retposxto", "adresaldonajxo",
								   "strato", "posxtkodo", "urbo", "lando", "telefono",
								   "telefakso"),
							 array("partoprenantoj" => "p", "partoprenoj" => "pn",
								   "landoj" => "l"),
							 array("pn.partoprenantoID = p.ID",
								   "l.ID = lando",
									// "pn.listo = 'N'", // nur la ne-interretlistuloj?
								   "alvenstato = 'a'"
								   ),
							 "renkontigxoID",
							 array("order" => "personanomo, p.nomo")
							 );

   echo "<BR><BR>";
    $rezulto = sql_faru($demando);
	$koloro = 0;
    while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
    {     
      eoecho($row[personanomo]." ".$row[2]."<BR>");
	   if ($bunta == "JES")
		{
		  switch($koloro % 4)
			{
			case 0:
			  $pdf->SetTextColor(200,0,0);
			  break;
			case 1:
			  $pdf->SetTextColor(0,0,255);
			  break;
			case 2:
			  $pdf->SetTextColor(0,150,0);
			  break;
			default:
			  $pdf->SetTextColor(0,0,0);
			  break;
			}
		  $koloro ++;
		}
      $pdf->write($linlargxo,uni(($row[personanomo] . " " . $row[2] . "; " . $row[adresaldonajxo] . " " .
		                         $row[strato] . "; " . $row[posxtkodo] . "; " . $row[urbo] . "; " . $row[4] .
		                         "; " . $row[telefono] . "; " . $row[telefakso] . "; " . $row[retposxto])));
      $pdf->ln($interlinspaco);
      
      fputs($fp,
		      utf8_decode("'".$row[personanomo]."';'".$row[2]."';'".$row[adresaldonajxo]."';'" .
			               $row[strato]."';'".$row[posxtkodo]."';'".$row[urbo]."';'".$row[4]."';'".$row[telefono].
			               "';'".$row[telefakso]."';'".$row[retposxto])."'\n");
    }
   $pdf->Output($GLOBALS['prafix'] . "/dosieroj_generitaj/adresaro.pdf");
   fclose($fp);
   echo "<BR><BR>";
   hazard_ligu("dosieroj_generitaj/adresaro.pdf","els^uti la adresaron (PDF).","_top","jes");
	hazard_ligu("dosieroj_generitaj/adresaro.csv","els^uti la adresaron (CSV).","_top","jes");


?>