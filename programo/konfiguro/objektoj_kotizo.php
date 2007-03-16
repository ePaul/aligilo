<?php

//
// Proceduroj por kalkuli prezojn ktp.
//

//
// TODO: La tuta kotizokalkulado estas gxisdatigenda.
//



class Kotizo
{

  var $bazakotizo,$bazahodiaux,$limdato,                                  // troagxa ist für über 40 Jährige.
    $krominvitilo=0.0,$kromdulita=0.0,$krompago=0.0,$kromekskurso=0.0,
	$kromtroagxa=0.0,$troagxasedrabato=0.0,
	$krom_surloka=0.0,
	$krom_membro = 0.0, $krom_nemembro = 0.0,
    $landarabato, // ne plu uzata
    $kotizo,$pagenda,
    $antauxpago,$antauxpagdato,
    $surlokapago,
    $rabato,
      $rabato_tejo = 0.0,
    $partoprentagoj,
    $aligxkategorio,$landakategorio,$agxkategorio,
    $relevantadato,
    $litoj,
    $mesagxo;
   // nur por trovi erarojn
  var $komento;


  function Kotizo($partopreno,$partoprenanto,$renkontigxo)
  {
	if ($partopreno == null)
		return;

	//agxo je la komenco de la arangxo
	$agxo = kalkulu_agxon($partoprenanto->datoj[naskigxdato],$renkontigxo->datoj[de]);

	//            echo "AGXO: $agxo";
	$this->agxkategorio = $this->kalkulu_agx_kategorio($agxo,$renkontigxo);

	//40 Euro mehr für über 40 jährige (nur 2003)
	if ($agxo >= 40.0 && $renkontigxo->datoj["ID"]==3)
	  $this->kromtroagxa=40; // TODO: auch aus der DB ziehen.

	$this->landakategorio = eltrovu_landokategorion($partoprenanto->datoj[lando]);
	if (!($this->landakategorio))
	  {
		// TODO: cxu eble pli bone A?
		$this->landakategorio = "C";
	  }
  

	// TODO: Cxu GROUP BY necesas?
	// "select sum(kvanto) from pagoj where dato>='".$renkontigxo->datoj[de]."' and partoprenoID='".$partopreno->datoj[ID]."' group by partoprenoID order by dato asc"


	// sumo de surlokaj pagoj (= pagoj, kiuj ne okazis antauxe)
	$row = 
	  mysql_fetch_row(sql_faru(datumbazdemando("SUM(kvanto)",
											   "pagoj",
											   array("dato >= '" . $renkontigxo->datoj[de]."'",
													 "partoprenoID = '" .
													 $partopreno->datoj[ID] ."'" ),
											   "",
											   array("group" => "partoprenoID",
													 "order" => "dato ASC")
											   )));
	if ($row)
	  $this->surlokapago = $row[0];
  
	//eltrovi la antauxpagon egalas: eltrovi la unuan pagon  
	// TODO: Cxu vere? Cxu ne eblas plurfoje antauxpagi?
	// TODO: Cxu GROUP BY necesas?

	// "select sum(kvanto) from pagoj where dato<'".$renkontigxo->datoj[de]."' and partoprenoID='".$partopreno->datoj[ID]."' group by partoprenoID order by dato asc"
	$row = 
	  mysql_fetch_row(sql_faru(datumbazdemando("SUM(kvanto)",
											   "pagoj",
											   array("dato < '" . $renkontigxo->datoj["de"] ."'",
													 "partoprenoID = '"
													 . $partopreno->datoj["ID"] . "'"),
											   "",
											   array("group" => "partoprenoID",
													 "order" => "dato ASC")
											   )));
	if ($row)
	  $this->antauxpago = $row[0];

	// "select dato from pagoj where partoprenoID='".$partopreno->datoj[ID]."' order by dato asc limit 0,1"
	$row = 
	  mysql_fetch_row(sql_faru(datumbazdemando("dato",
											   "pagoj",
											   "partoprenoID = '".$partopreno->datoj['ID']."'",
											   "",
											   array("order" => "dato ASC",
													 "limit" => "0,1")
											   )));
	if ($row)
	  $this->antauxpagdato = $row[0];


	//eltrovi la rabatsumon

	// "select sum(kvanto) from rabatoj where partoprenoID='".$partopreno->datoj[ID]."' group by partoprenoID"
	// TODO: Cxu GROUP BY necesas?
	$row = mysql_fetch_row(sql_faru(datumbazdemando("SUM(kvanto)",
													"rabatoj",
													"partoprenoID='".$partopreno->datoj[ID]."'",
													"",
													array("group" => "partoprenoID")
													)));
	if ($row)
	  $this->rabato = $row[0];

	if ($partopreno->datoj["KKRen"]=='J')
	  $relevantadato=$renkontigxo->datoj["plejfrue"];
	else if ((!$partopreno->datoj["aligxkategoridato"]) or ($partopreno->datoj["aligxkategoridato"])=="0000-00-00")
	  {
		if ( $this->landakategorio[0] == "C" )
		  $this->relevantadato=$partopreno->datoj["aligxdato"];
		else if (( $this->landakategorio[0] == "B" ) and ($this->antauxpago>=10))
		  // TODO: minimuma antauxpago el datumbazo
		  $this->relevantadato=$this->antauxpagdato;
		else if (( $this->landakategorio[0] == "A" ) and ($this->antauxpago>=30))
		  // TODO: minimuma antauxpago el datumbazo
		  $this->relevantadato=$this->antauxpagdato;
		else
		  $this->relevantadato=$renkontigxo->datoj["de"];
	  }
	else
	  $this->relevantadato = $partopreno->datoj["aligxkategoridato"];

	$this->aligxkategorio = $this->kalkulu_aligx_kategorio($this->relevantadato,$renkontigxo);

	$this->partoprentagoj = kalkulu_tagojn($partopreno->datoj[de],$partopreno->datoj[gxis]);
	$renkontigxotempo = kalkulu_tagojn($renkontigxo->datoj[de],$renkontigxo->datoj[gxis]);  

	//$landarabato = kalkulu_landa_rabato($partoprenanto->datoj[lando]);

	if ($renkontigxo->datoj["ID"] < 4)  // nur antaux 2004
	{
	  $rabato=0.0;
	  if (($partopreno->datoj[domotipo][0]!="M")/*and($this->partoprentagoj == $renkontigxotempo)*/)
		{
		  if ($this->landakategorio[0]=="B")
			{
			  $rabato=30.0;
			}
		  else if ($this->landakategorio[0]=="C")
			{
			  $rabato=40.0;
			}
		}
	  else if ($partopreno->datoj[domotipo][0]=="M")
		{
		  if ($this->landakategorio[0]=="B")
			{
			  $rabato=40.0;
			}
		  else if ($this->landakategorio[0]=="C")
			{
			  $rabato=50.0;
			}
		}
	  $this->landarabato = $rabato;
	}
  
	{  //auch aus der DB!!
	  $baza = $this->kutimaprezo($renkontigxo->datoj[ID],
								 $this->agxkategorio,
								 $this->aligxkategorio,
								 $partopreno->datoj["domotipo"],
								 $this->landakategorio);
	  $this->bazahodiaux = $this->kutimaprezo($renkontigxo->datoj[ID],
											  $this->agxkategorio,
											  $this->kalkulu_aligx_kategorio(date( "Y-m-d", time() ),
																			 $renkontigxo/*,&$this->limdato*/),
											  $partopreno->datoj[domotipo],
											  $this->landakategorio);

	  /* mi rekalkulas, por ke la kromefiko nuligxu. */
	  $this->kalkulu_aligx_kategorio($this->relevantadato, $renkontigxo);

	  if ($renkontigxo->datoj["ID"] < 4)  // nur antaux 2004
		{
		  $plej_alta = $this->plejaltaprezo($renkontigxo->datoj[ID],
											$this->agxkategorio,$partopreno->datoj[domotipo]);
		  $parttempa = (($plej_alta)/$renkontigxo->datoj[parttemppartoprendivido]*$this->partoprentagoj);
		}
	  else
		{
		  $parttempa = $baza*($this->partoprentagoj)/$renkontigxo->datoj["parttemppartoprendivido"];
		}
		  

	  if ($this->partoprentagoj < $renkontigxotempo)
		{
		  if ($renkontigxo->datoj["ID"] < 4)  // nur antaux 2004
			{
			  //echo "Baza $baza parttempa $parttempa rabato:".$this->landarabato;
			  if ($parttempa < ($baza-$this->landarabato)) //vorher: plej_alta
				{
				  $baza = $parttempa;
				  $this->landarabato=0;
				  // $kromsenantauxpago = 0; //parttemppartoprenantoj ne devas antauxpagi
				}
			  
			  //else $baza=$plej_alta;  quasi $baza=$baza;
			  //echo "Baza $baza parttempa $parttempa rabato:".$this->landarabato;
			}
		  else
			{
			  $baza = $parttempa;
			}

		} // se la parta_prezo estos pli pl la baza, k.e. junaj homoj kun 6 partoprentagoj ni prenos la bazprezon.

	  $this->bazakotizo = $baza;//kalkulu_bazo($agxo,$kato,$domo,$landakategorio,$partoprentagoj,$this->antauxpago);
	}

	// TODO: jaja, später aus der DB
	//  if ($partopreno->datoj[dulita][0]=="J") $this->kromdulita=(double)20.00;
	//TODO: certigi, ke vere estas dulita cxambro

	$row = mysql_fetch_array(eltrovu_cxambrojn($partopreno->datoj[ID]),MYSQL_NUM);
	if ($row)
	{

		// "select litonombro,dulita from cxambroj where ID='".$row[0]."'"
		$row2 = mysql_fetch_array(sql_faru(datumbazdemando(array("litonombro", "dulita"),
														   "cxambroj",
														   "ID = '" . $row[0] . "'")),
								  MYSQL_BOTH);
		$this->litoj = $row2[litonombro];
  
		if ($row2[dulita]=='J')
		  $this->litoj=2;  //TODO: traduku: Simulierter Zweierzimmer bei Raumüberschuss.
  
		if ($this->litoj=='2')
		  $this->kromdulita = (double)20.00;
		if ($row2[litonombro]=='1')
		  $this->kromdulita=(double)30.00;  //TODO: eventuell andere Zuzahlung für Einzelzimmer
	}
	else
	{
		 // TODO: traduku: noch kein Zimmer zugewiesen - trotzdem dulita-krompago berechnen

		if ($partopreno->datoj['dulita'] == 'J')
		{
			$this->kromdulita=(double)20.00;
		}
	}
  
	if (($partopreno->datoj[invitletero][0]=='J') and
			($partopreno->datoj[invitilosendata]!="0000-00-00"))  //se ni vere elsendis invitilon.
	{
		if (($renkontigxo->datoj[ID] < 4) || ($agxo < 30))
		  $this->krominvitilo=(double)5; 
		else
		  $this->krominvitilo=(double)10;
	  }

	if (($partopreno->datoj[ekskursbileto]=='J'))
	  $this->kromekskurso=7; //auch aus der DB ziehen

	// Se oni tiel entajpis, ni enkasigas ankaux
	// la membrokotizon (aux la alternativan krompagon):
	switch($partopreno->datoj['surloka_membrokotizo'])
	  {
	  case 'j':
		$this->krom_membro = $partopreno->datoj['membrokotizo'];
		break;
	  case 'k':
		$this->krom_nemembro = $partopreno->datoj['membrokotizo'];
		break;
	  }

    switch($partopreno->datoj['tejo_membro_kontrolita'])
        {
        case 'j':
            $this->rabato_tejo = TEJO_RABATO;
            break;
        case '?':
        case 'n':
            $this->rabato_tejo = 0.0;
            break;
        }

	$this->krompago = $this->kromdulita + $this->krominvitilo +
	  $this->kromekskurso + $this->kromtroagxa +
	  $this->krom_surloka + $this->krom_membro + $this->krom_nemembro;

	$this->kotizo = $this->bazakotizo + $this->krompago - 
	  $this->landarabato - $this->rabato - $this->troagxasedrabato - $this->rabato_tejo;

	$this->pagenda = $this->kotizo - $this->antauxpago - $this->surlokapago;

	// + Beachtung der Landeskategorien
	// Später auch mal aus der DB zu ziehen
	//return $baza;
  }


  function plilongigi($io,$longeco)
  {
	$io = eotransformado($io,"x-metodo");
	if (strlen($io)<$longeco)
	  for ($i;$i < ($longeco-strlen($io));$i++)
		$rezulto .= " ";
	return $rezulto.$io;
  }


  function kkampo($tipo,$largeco,$titolo,$io,$sumo="",$duasumo="")
  {
	if ($tipo==0)
	  {
		eoecho ("<tr>\n<td class='kolumno1'> $titolo </td>\n<td class='kolumno2'> $io</td>\n<td class='kolumno3'>$sumo</td>");
		if ($largeco==4)
		  {
			if($duasumo)
			  eoecho ("<td align=right class='kolumno4'>$duasumo</td>");
			else
			  eoecho ("<td align=right class='kolumno4'>$sumo</td>");
		  }
		echo "</tr>\n";
	  }
	else
	  { //Eldono por reta mesagxo
		$this->mesagxo .= $this->plilongigi($titolo,10)."\t|".$this->plilongigi($io,18)."\t|".$this->plilongigi($sumo,10);
		if ($largeco==4)
		  {
			if($duasumo) $this->mesagxo .= "\t|".$this->plilongigi($duasumo,10);
			else $this->mesagxo .= "\t|".$this->plilongigi($sumo,10);
		  }
		$this->mesagxo.="\n";
	  }
  }


  function montru_kotizon($tipo,$partopreno,$partoprenanto,$renkontigxo)
  {
	//  echo "BazaH".$this->bazahodiaux." bK: ".$this->bazakotizo;
	$this->mesagxo="";
	if ($this->bazahodiaux < $this->bazakotizo and $partopreno->datoj[partoprentipo]=='t')
	  $kampolar=4;
    else
	  $kampolar=3;

	if ($this->komento && $tipo == 0)
	  {
		eoecho("<!-- [" . $this->komento . "] -->");
	  }


	// hübscher machen
	if ($kampolar==4)
	  $this->kkampo($tipo,$kampolar,"","se vi antau^pagos:",
					"tro malfrue","g^is: ".$this->limdato);
	
	$this->kkampo($tipo,$kampolar,"ag^kategorio:", $this->formatu_agxkategorion($renkontigxo));

	if ($this->aligxkategorio==0)
	  {
		$this->kkampo($tipo,$kampolar,"alig^kategorio:","> ".$renkontigxo->datoj[meze]);
	  }
	elseif ($this->aligxkategorio==1)
	  {
		$this->kkampo($tipo,$kampolar,"alig^kategorio:",">= ".$renkontigxo->datoj[plej_frue],"");
	  }
	elseif ($this->aligxkategorio==2)
	  {
		$this->kkampo($tipo,$kampolar,"alig^kategorio:","< ".$renkontigxo->datoj[plej_frue]);
	  }
	else
	  {
		$this->kkampo($tipo,$kampolar,"alig^kategorio:","eraro");
	  }
	$this->kkampo($tipo,$kampolar,"domotipo:",$partopreno->datoj[domotipo][0]);
	//  $landakategorio = eltrovu_landokategorion($partoprenanto->datoj[lando]);
	$this->kkampo($tipo,$kampolar,"partoprentagoj:",$this->partoprentagoj);
	$this->kkampo($tipo,$kampolar,"","baza kotizo:",number_format($this->bazakotizo, 2, '.', '')." E^",number_format($this->bazahodiaux, 2, '.', '')." E^");
	//  kampo("----","----","----");
	if ($renkontigxo->datoj["ID"] < 4)
	  {
		$this->kkampo($tipo,$kampolar,"landkategorio:",$this->landakategorio," - ".number_format($this->landarabato, 2, '.', '')." E^");
	  }

	if ($this->rabato!=0)
	  {
		$this->kkampo($tipo,$kampolar,"","rabato:","- ".number_format($this->rabato, 2, '.', '')." E^");
	  }

	if ($this->troagxasedrabato!=0)
	  {
		$this->kkampo($tipo,$kampolar,"",">26, sed ne laboras:","- ".number_format($this->troagxasedrabato, 2, '.', '')." E^");
	  }

    if ($this->rabato_tejo > 0)
        {
            $this->kkampo($tipo, $kampolar, "", "estas TEJO-membro por la sekva jaro",
                          "- " . number_format($this->rabato_tejo, 2, '.', '') . " E^");
        }

	if ($this->kromtroagxa!=0)
	  {
		$this->kkampo($tipo,$kampolar,"","pli ol 40 jarojn:","+ ".number_format($this->kromtroagxa, 2, '.', '')." E^");
	  }


	if ($this->krompago!=0)
	  {
		$this->kkampo($tipo,$kampolar,"kompago(j):","----","----");
		if ($this->krom_nemembro != 0)
		  {
			$this->kkampo($tipo,$kampolar,"", "Ne-membro de GEJ au^ GEA:",
						  "+ ".number_format($this->krom_nemembro, 2, '.', '')." E^");
		  }
		if($this->krom_membro != 0)
		  {
			$this->kkampo($tipo, $kampolar, "", "Membro&shy;ko&shy;tizo de GEJ au^ GEA por la sekva jaro:",
						  "+ ".number_format($this->krom_membro, 2, '.', '')." E^");
		  }

		if ($this->krominvitilo!=0)
		  {
			$this->kkampo($tipo,$kampolar,"", "invitletero:",
						  "+ ".number_format($this->krominvitilo, 2, '.', '')." E^");
		  }
		if ($this->kromdulita!=0)
		  {
			$this->kkampo($tipo,$kampolar,"", "dulita c^ambro:",
						  "+ ".number_format($this->kromdulita, 2, '.', '')." E^");
		  }
		if ($this->kromekskurso!=0)
		  {
			$this->kkampo($tipo,$kampolar,"", "ekskursbileto:",
						  "+ ".number_format($this->kromekskurso, 2, '.', '')." E^");
		  }

		if ($this->krom_surloka != 0)
		  {
			$this->kkampo($tipo, $kampolar, "", "alig^o post 20-a de decembro",
						  "+ " . number_format($this->krom_surloka, 2, '.', '')." E^");
		  }


		$this->kkampo($tipo,$kampolar,"","sumo krompagoj:",
					  "+ ".number_format($this->krompago, 2, '.', '')." E^");
	  }

	//  echo "$mdk restas:$dk ".($baza+$krompago-$rabato)." &#8364;";

	//  kampo("sumo:",$baza+$krompago-$this->datoj[rabato]."E^");
	$this->kkampo($tipo,$kampolar,"====","====","====");
	$this->kkampo($tipo,$kampolar,"","sumo:",number_format($this->kotizo, 2, '.', '')." E^",number_format(($this->kotizo-$this->bazakotizo+$this->bazahodiaux), 2, '.', '')." E^");
	//  kampo("----","----","----");
	$this->kkampo($tipo,$kampolar,"","antau^pago:","- ".number_format($this->antauxpago, 2, '.', '')." E^");
	if ($this->surlokapago!=0)
	  {
		$this->kkampo($tipo,$kampolar,"","surloka pago:",
					  "- ".number_format($this->surlokapago, 2, '.', '')." E^");
	  }
	//kampo("restas pagenda:","<i>".($baza+$krompago-$this->datoj[rabato]-$this->antauxpago /*-$this->datoj[surlokpago]*/)."</i> E^");
	$this->kkampo($tipo,$kampolar,"====","====","====");
	$this->kkampo($tipo,$kampolar,"","restas pagenda:","<i><b>".number_format($this->pagenda, 2, '.', '')."</b></i> E^","<i>".number_format(($this->pagenda-$this->bazakotizo+$this->bazahodiaux), 2, '.', '')."</i> E^");
	if ($kampolar==4) $this->kkampo($tipo,$kampolar,"","se vi antau^pagos:","tro malfrue","g^is: ".$this->limdato);
  }

  function restas_pagenda()
  {
	return $this->pagenda;
  }

  function kalkulu_aligx_kategorio($relevantadato,$renkontigxo)
  {
	/* if ( $landakategorio[0] == "C" ) $io=$partopreno->datoj[aligxdato];
       else if (( $landakategorio[0] == "B" ) and ($partopreno->datoj[antauxpago]>=9))  $io=$partopreno->datoj[antauxpagdato];
       else if (( $landakategorio[0] == "A" ) and ($partopreno->datoj[antauxpago]>=28))  $io=$partopreno->datoj[antauxpagdato];
       else return 0;

  return $this->aligx_kato($io,$renkontigxo);
	*/


				  $this->komento .= "relevantadato: $relevantadato, this->relevantadato: {$this->relevantadato}, datoj[meze] = {$renkontigxo->datoj['meze']}. ";

		  if ($relevantadato >= $renkontigxo->datoj[meze])
			{
			  $this->limdato = $renkontigxo->datoj[de];
			  if ($renkontigxo->datoj["ID"] >= 4)  // ekde 2004
				{
				  $this->krom_surloka = 10.0;
				  return 1;
				}
			  return 0;  // poste/surloke
			}
		  else if ($relevantadato >= $renkontigxo->datoj[plej_frue])
			{
			  $this->limdato = $renkontigxo->datoj[meze];
			  $this->krom_surloka = 0.0;
			  return 1; //meztempen
			}
		  else /* if ($relevantadato < $renkontigxo->datoj[plej_frue]) */
			{
			  $this->limdato = $renkontigxo->datoj[plej_frue];
			  $this->krom_surloka = 0.0;
			  return 2; // ege frue
			}


  }


  //entfällt
  /*function aligx_kato($io,$renkontigxo,&$limdato)
{
  if ($io>=$renkontigxo->datoj[meze])
  {
    $limdato = $renkontigxo->datoj[de];
    return 0;  // poste/surloke
  }
  else if ($io>=$renkontigxo->datoj[plej_frue])
  {
    $limdato = $renkontigxo->datoj[meze];
    return 1; //meztempen
  }
  else if ($io<$renkontigxo->datoj[plej_frue])
  {
    $limdato = $renkontigxo->datoj[plej_frue];
    return 2; // ege frue
  }
}
  */


  function formatu_agxkategorion($renkontigxo, $agxkategorio = -1)
  {
	if ($agxkategorio == -1)
	  {
		 $agxkategorio = $this->agxkategorio;
	  }
	if ($renkontigxo->datoj["ID"] < 4)
	  {
		if ($agxkategorio==0)
		  {
			return "> ".$renkontigxo->datoj[maljuna];
		  }
		elseif  ($agxkategorio==1)
		  {
			return "> ".$renkontigxo->datoj[juna];
		  }
		elseif ($agxkategorio==2)
		  {
			return "<= ".$renkontigxo->datoj[juna];
		  }
	  }
	else
	  {
		switch ($agxkategorio)
		  {
		  case 0: return " 0 - 17";
		  case 1: return "18 - 21";
		  case 2: return "22 - 26";
		  case 3: return "27 - 35";
		  case 4: return "36 - ..";
		  }
	  }
  }

  function kalkulu_agx_kategorio($agxo,$renkontigxo)
  {
	if (is_object($renkontigxo))
	{
		$renkID = $renkontigxo->datoj["ID"];
	}
	else
	{
		$renkID = $renkontigxo;
		$renkontigxo = new Renkontigxo($renkID);
	}
	if ($renkID < 4)
	  {
		if ($agxo>$renkontigxo->datoj[maljuna])
		  {
			return 0;  // plej alta agxo
		  }
		else if ($agxo>$renkontigxo->datoj[juna])
		  {
			return 1; //plenkreskanta
		  }
		else if ($agxo<=$renkontigxo->datoj[juna])
		  {
			return 2; //juna
		  }
	  }
	else
	  {
		// TODO: In Datenbank passend einbauen
		// Achtung: umgekehrte Numerierung!

		if ($agxo <= 17)
		  return 0;
		else if ($agxo <= 21)
		  return 1;
		else if ($agxo <= 26)
		  return 2;
		else if ($agxo <= 35)
		  return 3;
		else
		  return 4;
	  }
  }


  /**
   * Kalkulas la bazan prezon por la renkontigxo.
   *
   * $renkontigxo - la identifikilo de la renkontigxo
   * $agxo        - la agxkategorio (0-2 por $renkontigxo < 4,
   *                                 0-4 por $renkontigxo >= 4)
   * $kategorio   - la aligxkategorio (0-2 por $renkontigxo < 4,
   *                                   1-2 por $renkontigxo >= 4)
   * $domo - tipo de la domo. Gravas nur la unua litero, estu aux
   *         "J" (junulargastejo) aux "M" (memzorgantejo)
   * $landakategorio - necesas nur por $renkontigxo == 4. Estu
   *                   "A", "B" aux "C".
   */
  function kutimaprezo($renkontigxo,$agxo,$kategorio,$domo,$landakategorio="A")
  {
	//TODO: Im Moment noch jedesmal aufs neue zu schreiben
	//TODO: Das wird später aus der DB gezogen!!!
  
	if ($renkontigxo == '2' || $renkontigxo == '1') //Trier und Rotenburg
	  {
		if ($domo[0]!="M")
		  {
			if (($agxo==2) and ($kategorio==0)) $baza = 172.0;
			else if (($agxo==2) and ($kategorio==1)) $baza = 142.0;
			else if (($agxo==2) and ($kategorio==2)) $baza = 128.0;

			else if (($agxo==1) and ($kategorio==2)) $baza = 152.0;
			else if (($agxo==1) and ($kategorio==1)) $baza = 172.0;
			else if (($agxo==1) and ($kategorio==0)) $baza = 212.0;

			else if (($agxo == 0) and ($kategorio==0)) $baza = 232.0;
			else if (($agxo == 0) and ($kategorio==1)) $baza = 202.0;
			else if (($agxo == 0) and ($kategorio==2)) $baza = 182.0;
			else return -1;
		  }
		else
		  {
			if (($agxo>0) and ($kategorio==0)) $baza = 85.0;
			else if (($agxo>0) and ($kategorio==1)) $baza = 75.0;
			else if (($agxo>0) and ($kategorio==2)) $baza = 65.0;

			else if (($agxo == 0) and ($kategorio==0)) $baza = 130.00;
			else if (($agxo == 0) and ($kategorio==1)) $baza = 120.0;
			else if (($agxo == 0) and ($kategorio==2)) $baza = 110.0;
			else return -1;
		  } //kampo ("baza", $baza);
	  }
	else if ($renkontigxo=='3') //3==Naumburg
	  {
		if ($domo[0]!="M")
		  {
			if (($agxo==2) and ($kategorio==0)) $baza = 175.0;
			else if (($agxo==2) and ($kategorio==1)) $baza = 150.0;
			else if (($agxo==2) and ($kategorio==2)) $baza = 130.0;

			else if (($agxo==1) and ($kategorio==0)) $baza = 210.0;
			else if (($agxo==1) and ($kategorio==1)) $baza = 180.0;
			else if (($agxo==1) and ($kategorio==2)) $baza = 160.0;
    
			else if (($agxo == 0) and ($kategorio==0)) $baza = 250.0;
			else if (($agxo == 0) and ($kategorio==1)) $baza = 220.0;
			else if (($agxo == 0) and ($kategorio==2)) $baza = 190.0;
			else return -1;
		  }
		else
		  {
			if (($agxo>0) and ($kategorio==0)) $baza = 90.0;
			else if (($agxo>0) and ($kategorio==1)) $baza = 80.0;
			else if (($agxo>0) and ($kategorio==2)) $baza = 70.0;

			else if (($agxo == 0) and ($kategorio==0)) $baza = 135.00;
			else if (($agxo == 0) and ($kategorio==1)) $baza = 125.0;
			else if (($agxo == 0) and ($kategorio==2)) $baza = 115.0;
			else return -1;
		  } //kampo ("baza", $baza);
	  }
	else if ($renkontigxo == '4' or $renkontigxo == '5' // 4 == Wetzlar 04/05, 5 == Xanten 05/06,
             or $renkontigxo == '6')                    // 6 == Wewelsburg 06/07
        {
		if ($domo{0} == "J") // junulargastejo
		  {
			if ($kategorio == 2) // frua aligxo
			  {
				if ($landakategorio == "A")
				  {
					switch($agxo)
					  {
					  case 0: return 100.0;
					  case 1: return 140.0;
					  case 2: return 170.0;
					  case 3: return 220.0;
					  case 4: return 240.0;
					  default:
						darf_nicht_sein();
					  }
				  }
				else if ($landakategorio == "B")
				  {
					switch($agxo)
					  {
					  case 0: return 85.0;
					  case 1: return 119.0;
					  case 2: return 144.5;
					  case 3: return 187.0;
					  case 4: return 204.0;
					  default:
						darf_nicht_sein();
					  }
				  }
				else if ($landakategorio == "C")
				  {
					switch($agxo)
					  {
					  case 0: return 75.0;
					  case 1: return 105.0;
					  case 2: return 127.5;
					  case 3: return 165.0;
					  case 4: return 180.0;
					  default:
						darf_nicht_sein();
					  }
				  }
				else
				  {
					darf_nicht_sein();
				  }
			  }
			else if ($kategorio == 1) // malfrua aligxo (inkluzive surloka)
			  {
				if ($landakategorio == "A")
				  {
					switch($agxo)
					  {
					  case 0: return 120.0;
					  case 1: return 170.0;
					  case 2: return 200.0;
					  case 3: return 250.0;
					  case 4: return 270.0;
					  default:
						darf_nicht_sein();
					  }
				  }
				else if ($landakategorio == "B")
				  {
					switch($agxo)
					  {
					  case 0: return 102.0;
					  case 1: return 144.5;
					  case 2: return 170.0;
					  case 3: return 212.5;
					  case 4: return 229.5;
					  default:
						darf_nicht_sein();
					  }
				  }
				else if ($landakategorio == "C")
				  {
					switch($agxo)
					  {
					  case 0: return 90.0;
					  case 1: return 127.5;
					  case 2: return 150.0;
					  case 3: return 187.5;
					  case 4: return 202.5;
					  default:
						darf_nicht_sein();
					  }
				  }
				else
				  {
					darf_nicht_sein();
				  }
			  }
			else 
			  {
				darf_nicht_sein();
			  }
		  }
		else if ($domo{0} == "M") // memzorgantejo
		  {
			if ($kategorio == 2) // frua aligxo
			  {
				if ($landakategorio == "A")
				  {
					switch($agxo)
					  {
					  case 0: return 30.0;
					  case 1: return 60.0;
					  case 2: return 80.0;
					  case 3: return 100.0;
					  case 4: return 120.0;
					  default:
						darf_nicht_sein();
					  }
				  }
				else if ($landakategorio == "B")
				  {
					switch($agxo)
					  {
					  case 0: return 21.0;
					  case 1: return 42.0;
					  case 2: return 56.0;
					  case 3: return 70.0;
					  case 4: return 84.0;
					  default:
						darf_nicht_sein();
					  }
				  }
				else if ($landakategorio == "C")
				  {
					switch($agxo)
					  {
					  case 0: return 12.0;
					  case 1: return 24.0;
					  case 2: return 32.0;
					  case 3: return 40.0;
					  case 4: return 48.0;
					  default:
						darf_nicht_sein();
					  }
				  }
				else
				  {
					darf_nicht_sein();
				  }
			  }
			else if ($kategorio == 1) // malfrua aligxo (inkluzive surloka)
			  {
				if ($landakategorio == "A")
				  {
					switch($agxo)
					  {
					  case 0: return 40.0;
					  case 1: return 70.0;
					  case 2: return 95.0;
					  case 3: return 120.0;
					  case 4: return 140.0;
					  default:
						darf_nicht_sein();
					  }
				  }
				else if ($landakategorio == "B")
				  {
					switch($agxo)
					  {
					  case 0: return 28.0;
					  case 1: return 49.0;
					  case 2: return 66.5;
					  case 3: return 84.0;
					  case 4: return 98.0;
					  default:
						darf_nicht_sein();
					  }
				  }
				else if ($landakategorio == "C")
				  {
					switch($agxo)
					  {
					  case 0: return 16.0;
					  case 1: return 28.0;
					  case 2: return 38.0;
					  case 3: return 48.0;
					  case 4: return 56.0;
					  default:
						darf_nicht_sein();
					  }
				  }
				else
				  {
					darf_nicht_sein();
				  }
			  }
			else 
			  {
				darf_nicht_sein();
			  }
		  }
		else
		  {
			darf_nicht_sein();
		  }

		// ni espereble jam faris "return" dum la lasta if_else-kaskado
		darf_nicht_sein();

	  }  // Wetzlar
	else
	  {
		$baza = 88.88;
	  }
	return $baza; 
  }

  // kalkulas la baza prezo, iam kun datumaro kiu enhavas ilin.
  /*function kalkulubazo($agxo,$kategorio,$domo,$landakategorio,$partoprentagoj,$antauxpago)
{
//entfällt bald
} */

//wird von kutimaprezo geschluckt
function plejaltaprezo($renkontigxo,$agxo, $domotipo)
{
  $baza="eraro"; 
  // muss auch noch in die DB
  
  $baza = $this-> kutimaprezo($renkontigxo,$agxo,0,$domotipo,"A"); //Ermitteln des Höchsten Preises der Kategorie.
  
  /*if ($domotipo[0]!="M")
  {        //Domlogxantoj
         if ($agxo==2) $baza = 172.0;
    else if ($agxo==1) $baza = 212.0;
    else if ($agxo==0)  $baza = 232.0;
  }
  else
  {      //Memzorgantoj
         if ($agxo>0) $baza = 85.0;
    else if ($agxo==0)  $baza = 130.0;
  }*/
  return $baza;
}

/**
 * Eltrovas la minimuman antauxpagon laux la
 * landa kategorio.
 */
function minimuma_antauxpago()
{
    // TODO: eble iam metu en datumbazon
  switch($this->landakategorio)
	{
	case 'A':
	  return 30.0;
	  break;
	case 'B':
	  return 10.0;
	  break;
	case 'C':
	  return 0.0;
	  break;
	}
}


}


?>
