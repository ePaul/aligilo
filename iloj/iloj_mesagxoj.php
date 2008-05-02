<?php

require_once ($prafix.'/iloj/email_message.php');

/*
 * Funkcioj por sendi retposxtmesagxojn.
 *
 * La mesagxoj estas sendotaj per sendmail.
 *
 * (Mi jxus sxangxis de la uzo de ISO-8859-1 kun utf8_decode
 *  al rekta uzo de UTF-8. -- Pauxlo, 2004-07-12)
 *
 * 2005-08-12: La sendado okazos per PHP-mail()
 *      (iloj/email_message.php), ne plu per sendmail
 *      (iloj/sendmail_message.php).
 *
 * TODO!: anstatauxu cxion per la funkcioj en retmesagxiloj.php
 *        kaj diversaj_retmesagxoj.php
 *
 */


/**
 * eltrovas la unuan nomon el du- aux plurparta nomo.
 *
 * Por "Saluton ...," en internaj mesagxoj.
 * ### uzata en sendu_mesagxon_oficiala(),
 * ###          sendu_mesagxon_se_juna_aux_nova(),
 * ###          sendu_mesagxon_se_troagxa().    (sube) ###
 */
function antauxnomo($nomo)
{
  $arr = explode(" ", $nomo, 2);
  return $arr[0];
}



/**
 * Sendas simplan mesagxon al iu ajn.
 *
 * $kaj        - la teksto de la mesagxo (en UTF-8, kun esperanta c^-kodigo,
 *               se ne estas $nekodigo = true).
 * $to_name    - la nomo de la ricevonto (en UTF-8, kun esperanta c^-kodigo).
 * $to_address - la retposxradreso de la ricevonto.
 * $subject    - la temlinio. Se vi forlasas (aux uzas ""), la temlinio
 *                estas "is-aligilo raporto".
 * $nekodigu   - se vi uzas la parametron (kun TRUE), ni ne
 *               eotransformas la tekston, sed uzas gxin kiel
 *               gxi estas.
 *
 * La sendinto estas "IS - Aligilo", kun adreso "is.admin@esperanto.de",
 *
 * ### estas uzata nuntempe rekte nur en iloj_mesagxoj:
 * ###       sendu_mesagxon_oficiala(),
 * ###       sendu_mesagxon_se_troagxa(),
 * ###       sendu_mesagxon_se_juna_aux_malnova(),
 * ###       sendu_ekzport()  ###
 */
function sendu_mesagxon($kaj,$to_name,$to_address, $subject = "", $nekodigu = FALSE)
{
  if ($subject == "")
	$subject = renkontigxo_nomo."-aligilo-raporto";

  $mesagxo  = eotransformado("### au^tomata mesag^o de la DEJ-aligilo ###\n\n", "utf-8");
  if ($nekodigu)
	{
	  $mesagxo .= $kaj;
	}
  else
	{
	  $mesagxo .= eotransformado($kaj, "utf-8");
	}
  $mesagxo .= "\n\n### Se estas teknika problemo bonvolu informi " .
      teknika_administranto_retadreso . " ###";
  $mesagxo .= "\n### (Se estas enhava problemo, informu is.admin@esperanto.de)  ###"; // TODO: forigi retadreson

  $to_name = eotransformado($to_name, "utf-8");

  $from_name = "IS - Aligilo";
  $from_address = "is.admin@esperanto.de"; // TODO: eble prenu el la datumbazo.

  $email_message = new email_message_class;
  $email_message->default_charset="UTF-8";
  if (!strcmp($error=$email_message->SetEncodedEmailHeader("To",$to_address,
														   $to_name),"")
	  && !strcmp($error=$email_message->SetEncodedEmailHeader("From",$from_address,
															  $from_name),"")
	  && !strcmp($error=$email_message->SetEncodedEmailHeader("Bcc",
                                                              teknika_administranto_retadreso,
                                                              teknika_administranto),"")  // TODO: forigu, se suficxas la kopioj
	  && !strcmp($error=$email_message->SetEncodedEmailHeader("Reply-To",$from_address,
															  $from_name),"")
	  && !strcmp($error=$email_message->SetEncodedHeader("Errors-To",$from_address,
														 $from_name),"")
	  && !strcmp($error=$email_message->SetEncodedHeader("Subject",$subject),"")
	  && !strcmp($error=$email_message->AddQuotedPrintableTextPart($email_message->WrapText($mesagxo)),"")
	  )
	{
	  $error = $email_message -> Send();
	  if ($error)
		{
		  erareldono($error);
		  exit();
		}
	}
  // TODO:? Kial ni metas cxion al $error, se ni neniam uzas gxin?
  // [respondo de Martin:] .... weil die Klasse abgeschrieben ist. Vielleicht braucht die Funktion auch eine Variable um ihre Werte zurückzugeben.

}


/**
 * Sendas retmesagxon al iu homo.
 *
 * $subjekto  - temlinio de la mesagxo (en UTF-8, EO-signoj per c^-kodigo)
 * $korpo     - la teksto de la mesagxo (dito)
 * $to_name   - la nomo de la ricevonto (dito)
 * $to_adress - la retposxtadreso de la ricevonto
 *
 * ### uzata en sendumesagxon.php ###
 */
function sendu_liberan_mesagxon($subjekto,$korpo,$to_name,$to_address)
{
  $subject = eotransformado($subjekto, "utf-8");

  $mesagxo .= eotransformado($korpo, "utf-8");

  $from_name = "Julia Noe";   // TODO: (eble prenu nomon aux el la datumbazo/konfiguro, aux la entajpanton ?)
  $from_address = "is.admin@esperanto.de";  // TODO: Eble prenu el la datumbazo?

  $email_message = new email_message_class;
  $email_message->default_charset="UTF-8";
  
  if (!strcmp($error=$email_message->SetEncodedEmailHeader("To",$to_address, eotransformado($to_name, "utf-8")),"")
  && !strcmp($error=$email_message->SetEncodedEmailHeader("From",$from_address, $from_name),"")
  && !strcmp($error=$email_message->SetEncodedEmailHeader("Reply-To",$from_address, $from_name),"")
  && !strcmp($error=$email_message->SetEncodedHeader("Errors-To",$from_address, $from_name),"")
	 //  && !strcmp($error=$email_message->SetEncodedHeader("Return-Path",$from_address, $from_name),"") 
	  && !strcmp($error=$email_message->SetEncodedEmailHeader("Bcc",
                                                              teknika_administranto_retadreso,
                                                              teknika_administranto),"")  // TODO: forigu, se suficxas la kopioj
  && !strcmp($error=$email_message->SetEncodedHeader("Subject",$subject),"")
  && !strcmp($error=$email_message->AddQuotedPrintableTextPart($email_message->WrapText($mesagxo)),""))
  $error = $email_message -> Send();
	  if ($error)
		{
		  erareldono($error);
		  exit();
		}
}


/**
 * Sendas mesagxon kun aldonitaj dosieroj.
 *
 * $subjekto    - temlinio
 * $korpo		- mesagxteksto
 * $to_name		- nomo de la ricevonto
 * $to_adress   - adreso de la ricevonto
 * $dosierojn   - array() kun la nomoj de
 *                la dosieroj, kiuj aldonendas.
 * $bcc_address - adreso, al kiu sendigxu sekreta kopio.
 *
 * ### uzata de specialaj_skriptoj/... kaj
 * ###          sendu_2ankonfirmilon() (sube). ###
 */
function sendu_dosier_mesagxon($subjekto, $korpo,
							   $to_name, $to_address,
							   $dosierojn=array(), $bcc_address='')
{
  // TODO: purigu la implementon de sendu_dosier_mesagxon
  // TODO: gxeneraligu la sendinto-nomon kaj -adreson!
  ;

  $subject = utf8_decode($subjekto);

//  $mesagxo  = "### auxtomata mesagxo de la DEJ-aligilo ###\n\n";
//  $mesagxo .= utf8_decode($korpo);
  $mesagxo .= $korpo;
  $mesagxo .= "\n\n### Se estas iu teknika problemo, bonvolu informi Paul.Ebermann@esperanto.de ###";

//   $from_name = "KKRen (Pauxlo Ebermann)";
//   $from_address = "ebermann+is-enketo@math.hu-berlin.de";
//   $from_address = "is.enketo@esperanto.de";

//   $respondo_nomo = "IS-Enketo";
//   $respondo_adreso = "is.enketo@esperanto.de";

   $from_name = "IS-Administranto";
   $from_address = "is.admin@esperanto.de";

   $respondo_nomo = "IS-Administranto";
   $respondo_adreso = "is.admin@esperanto.de";
  


  $email_message = new email_message_class;
  $email_message->default_charset = "UTF-8";

 
  if ($bcc_address)
	{
	  $bcc_address .= ", Paul.Ebermann@esperanto.de";
	}
  else
	{
	  $bcc_address = "Paul.Ebermann@esperanto.de";
	}

  echo "BCC: ".$bcc_address . "\n";
  if (!strcmp($error=$email_message->SetEncodedEmailHeader("To",$to_address, utf8_decode($to_name)),"")
  && !strcmp($error=$email_message->SetEncodedEmailHeader("From",$from_address, $from_name),"")
  && !strcmp($error=$email_message->SetEncodedEmailHeader("BCC",$bcc_address, $from_name),"")
  && !strcmp($error=$email_message->SetEncodedEmailHeader("Reply-To",$respondo_adreso, $respondo_nomo),"")
  && !strcmp($error=$email_message->SetEncodedHeader("Errors-To",$from_address, $from_name),"")
	  //  && !strcmp($error=$email_message->SetEncodedHeader("Return-Path",$from_address, $from_name),"") 
  && !strcmp($error=$email_message->SetEncodedHeader("Subject",$subject),"")
  && !strcmp($error=$email_message->AddQuotedPrintableTextPart($email_message->WrapText($mesagxo)),""))
  {
    for($i=(sizeof($dosierojn)-1);$i>=0;$i--)
    {
        $attachment=array(
                "FileName"=>$dosierojn[$i],
                "Content-Type"=>"automatic/name"
        );
        $email_message->AddFilePart($attachment);
       
     }
   }
  $error = $email_message -> Send();
	  if ($error)
		{
		  erareldono($error);
		  exit();
		}
}


/**
 * TODO: dokumentado por sendu_mesagxon_invitilo
 *
 * ### vokita de sendu_auxtomatajn_mesagxojn()  (sube) ###
 */
function sendu_mesagxon_invitilo($partoprenidento,$partoprenantoidento,$pasportnro,$to_name,$to_address,$rimarkoj)
{
//  global $nomo,$personanomo,$sekso,$personapronomo,$naskigxdato,$adresaldonajxo, $strato,$posxtkodo,$urbo,$lando,$telefono,
  //       $telefakso,$retposxtadreso,$rimarkoj,$ulentajpanto,$entajpdato,$sxangxanto,$sxangxdato;

  //eltrovu_partoprenanton($partoprenantoidento);
  $parto = new Partoprenanto($partoprenantoidento);
  $preno = new Partopreno($partoprenidento);
  $mesagxo  = "invitleteron por pasportnumero: ".$preno->datoj[pasportnumero]."\n\n";

  $mesagxo2 = "\n\ndatumoj: \npersonanomo: ".$parto->datoj[personanomo]. " \nfamilianomo: ".$parto->datoj[nomo]. " (".$parto->datoj[sekso]." / ".$parto->datoj[naskigxdato].") \n";

  if ($parto->datoj[adresaldonajxo])
  {
    $mesagxo2 .=$parto->datoj[adresaldonajxo]."\n";
  }
  $mesagxo2 .=$parto->datoj[strato]."\n".$parto->datoj[posxtkodo]." - ".$parto->datoj[urbo]."\n";
  if ($parto->datoj[provinco]) {$mesagxo2 .=$parto->datoj[provinco]."\n";}
  $mesagxo2 .= eltrovu_landon($parto->datoj[lando]);
 
  sendu_mesagxon_oficiala($partoprenidento,$partoprenantoidento,$mesagxo,$mesagxo2,$to_name,$to_address,$rimarkoj);
}

/**
 * TODO: dokumentado por sendu_mesagxon_programan
 *
 * ### uzata en sendu_auxtomatajn_mesagxojn()  (sube) ###
 */
function sendu_mesagxon_programan($partoprenidento,$partoprenantoidento,$tipo,$kontribuo,$to_name,$to_address,$rimarkoj)
{
  $mesagxo = "kontribui al la ".$tipo." programo per: ".$kontribuo."\n\n";
  sendu_mesagxon_oficiala($partoprenidento,$partoprenantoidento,$mesagxo,"",$to_name,$to_address,$rimarkoj);
}

/**
 * ### uzata en sendu_auxtomatajn_mesagxojn() ###
 */
function sendu_mesagxon_se_juna_aux_nova($partopreno, $partoprenanto, $renkontigxo)
{
//   echo "<!--\n";
//   var_export($partopreno);
//   var_export($partoprenanto);
//   var_export($renkontigxo);
//   echo "\n-->";


  // TODO! verallgemeinern
  if ($renkontigxo->datoj["mallongigo"] != "IS 2005")
	{
	  echo '<!-- renkontigxo != "IS 2005" -->';
	  return;
	}
  $juna = $nova = FALSE;

  if (strcmp($partoprenanto->datoj["naskigxdato"], "1987-12-27") > 0) // unter 18
	{
	  $juna = TRUE;
	}
  if ($partopreno->datoj["komencanto"]{0} == "J")
	{
	  $nova = TRUE;
	}
  if (!$juna && !$nova) // nek juna nek nova
	{
	  echo '<!-- nek juna nek nova. -->';
	  return;
	}

  $ri = $partoprenanto->personapronomo;
  $Ri = ucfirst($ri);
  
  
  $mesagxo = "Saluton " . antauxnomo($renkontigxo->datoj['novularespondulo']) . ",

j^us alig^is partoprenanto kiu estas";
  if ($juna && $nova)
	{
	  $mesagxo .= " kaj komencanto kaj junulo (< 18).";
	  $temo = "Nova komencanto + junulo aligxis";
	}
  else if ($junua)
	{
	  $mesagxo .= " junulo (< 18).";
	  $temo = "Nova junulo aligxis";
	}
  else
	{
	  $mesagxo .= " komencanto.";
	  $temo = "Nova komencanto aligxis";
	}
  $mesagxo .=  "\n"
	. "\nNomo: ".$partoprenanto->datoj["personanomo"]." ".$partoprenanto->datoj["nomo"]
	. "\nRetadreso: ".$partoprenanto->datoj["retposxto"]
	. "\nNaskig^dato: " . $partoprenanto->datoj["naskigxdato"]
	. "\n"
	. "\n$Ri rimarkis: [" . $partopreno->datoj["rimarkoj"] . "]"
	. "\n ";
  sendu_mesagxon($mesagxo, $renkontigxo->datoj['novularespondulo'],
				 $renkontigxo->datoj['novularetadreso'], $temo);
  echo "<!-- sendis la mesagxon al novulisto -->\n";

}

/**
 * Se la $partoprenanto je la komenco de IS 2004 jam agxas pli
 * ol 27 jarojn (t.e. naskigxis antaux 1977-12-27), la $renkontigxo
 * estas IS 2004, kaj li logxas en Germanio, ni sendas mesagxon
 * al la respondeculo pri la taga programo.
 *
 * Alikaze nenio okazos.
 *
 * ### Uzata en   sendu_auxtomatajn_mesagxojn()  (sube). ###
 */
function sendu_mesagxon_se_troagxa($partopreno, $partoprenanto, $renkontigxo)
{
  $ri = $partoprenanto->personapronomo;
  $Ri = ucfirst($ri);

  if ($renkontigxo->datoj["mallongigo"] == "IS 2004"
	  and $partoprenanto->datoj["lando"] == HEJMLANDO
	  and strcmp($partoprenanto->datoj["naskigxdato"],"1977-12-27") < 0  // (felicxe datoj same ordigxas kiel la tekstoj)
	  )
	{
	  echo "<!-- sendis la mesagxon al Ilka -->\n";
	  sendu_mesagxon("Saluton Ilka," 
					 . "\n" 
					 . "\nJ^us alig^is partoprenanto, kiu log^as en Germanio kaj"
					 . "\nkiu agxas pli ol 27 jaroj."
					 . "\n"
					 . "\nNomo: ".$partoprenanto->datoj["personanomo"]." ".$partoprenanto->datoj["nomo"]
					 . "\nRetadreso: ".$partoprenanto->datoj["retposxto"]
					 . "\nNaskig^dato: " . $partoprenanto->datoj["naskigxdato"]
					 . "\n"
					 . "\n$Ri rimarkis: [" . $partopreno->datoj["rimarkoj"] . "]"
					 . "\n"
					 . "\n ",
					 "Ilka Piechotta", "is.distra@esperanto.de",
					 "Troagxa germanio-enlogxanto"
					 );
	}
  else if (($renkontigxo->datoj["mallongigo"] == "IS 2005")
	  and $partoprenanto->datoj["lando"] == HEJMLANDO
	  and strcmp($partoprenanto->datoj["naskigxdato"],"1978-12-27") < 0  // (felicxe datoj same ordigxas kiel la tekstoj)
	  )
	{
	  $mesagxo = "Saluton " . antauxnomo($renkontigxo->datoj['temarespondulo']) .  ".," 
		. "\n" 
		. "\nJ^us alig^is partoprenanto, kiu log^as en Germanio kaj"
		. "\nkiu agxas pli ol 27 jaroj."
		. "\n"
		. "\nNomo: ".$partoprenanto->datoj["personanomo"]." ".$partoprenanto->datoj["nomo"]
		. "\nRetadreso: ".$partoprenanto->datoj["retposxto"]
		. "\nNaskig^dato: " . $partoprenanto->datoj["naskigxdato"]
		. "\n"
		. "\n$Ri rimarkis: [" . $partopreno->datoj["rimarkoj"] . "]"
		. "\n"
		. "\n ";
	  sendu_mesagxon($mesagxo, $renkontigxo->datoj['temarespondulo'],
					 $renkontigxo->datoj['temaretadreso'],
					 "Troagxa germanio-enlogxanto");
	}
  else
	{
  	  echo "<!-- ne necesis sendi mesagxon al Programrespondeculo. -->\n";
	  // alikaze ni faras nenion.
	}
}

/**
 * TODO: dokumentado por sendu_mesagxon_oficiala
 *
 *  ### uzata en sendu_mesagxon_invitilo() kaj ###
 *  ###          sendu_mesagxon_programan().   ###
 */
function sendu_mesagxon_oficiala($partoprenidento,$partoprenantoidento,$kaj,$kaj2,$to_name,$to_address,$rimarkoj)
{
  //TODO:? muß noch überarbeitet werden
  // [respondo de Martin:] a) da sind globale Variablen drin //  b) Die Funktion ist noch nicht allgemein genug. Da gibt es noch Codestücken die dasselbe machen.

  //  global $nomo, $personanomo;
 // eltrovu_partoprenanton($partoprenantoidento);

  $parto = new Partoprenanto($partoprenantoidento);
  $ri = $parto->personapronomo;
  $Ri = ucfirst($ri);

  $mesagxo = "Saluton kara ". antauxnomo($to_name) .
	", \nj^us alig^is partoprenanto kiu deziras\n\n";
  $mesagxo .= $kaj;
  $mesagxo .= "Nomo: ".$parto->datoj[personanomo]. " ". $parto->datoj[nomo]." \n";//ID: ".$partoprenantoidento partoprenidento: $partoprenidento";
  $mesagxo .= "retadreso: ".$parto->datoj[retposxto]."\n";
  $mesagxo .= $kaj2;

  if ($rimarkoj!=''){ $mesagxo .= "\n\n$Ri rimarkis:\n".$rimarkoj;}
  sendu_mesagxon($mesagxo,$to_name,$to_address);
}

/**
 * Sendas retposxte sekurkopion de la datoj de unu partoprenanto
 * (kaj ties partopreno).
 * Krome la mesagxo enhavas la $_POST-enhavon.
 * 
 * $partoprenanto - la datoj de la partoprenantoj (fakte ni uzas nur la ID)
 * $partopreno    - la datoj de la partopreno     (-----------"-----------)
 * $renkontigxo   - la datoj de la aktuala renkontigxo
 *                    (estas uzata por eltrovi, al kiu ni sendu la mesagxon).
 *
 *  ### Uzado: partrezultoj.php, AligxiloDankon.php ###
 */
function sendu_ekzport($partoprenanto,$partopreno, $renkontigxo)
{
  // TODO:? Kial ni ne rekte uzas la
  // objektojn - ili ja jam enhavas la datumojn.
  // [respondo de Martin:] Die ID ist im Objekt noch nicht unbedingt enthalten - wenn es gerade neu angelegt wurde.
  // mia komento: ni ja uzas fakte nur la ID rekte el la objekto.

  // "Select * from partoprenantoj where id=".$partoprenanto->datoj[ID]
  $result = mysql_fetch_array(sql_faru(datumbazdemando("*", "partoprenantoj",
													   "id = " . $partoprenanto->datoj[ID])),
							  MYSQL_NUM);
  $mesagxo = "# ekzport de entajpataj datumoj\n";
  $mesagxo .= "# Partoprenanto\n";
  $mesagxo .= $partoprenanto->sql_eksport()."\n#";
  $mesagxo .= implode(";",$result);
  // "Select * from partoprenoj where id=".$partopreno->datoj[ID]
  $result = mysql_fetch_array(sql_faru(datumbazdemando("*", "partoprenoj",
													   "id = " . $partopreno->datoj[ID])),
							  MYSQL_NUM);
  $mesagxo .= "\n# Partopreno\n";
  $mesagxo .= $partopreno->sql_eksport()."\n#";
  $mesagxo .= implode(";",$result);

  $mesagxo .= "\n\n\n";
  $mesagxo .= faru_aligxtekston($partoprenanto->datoj[ID],$partopreno->datoj[ID]);

  $mesagxo .= "\n\n --------- POST-datoj: --------- \n";
  $mesagxo .= var_export($_POST, true);
  $mesagxo .= "\n";

  sendu_mesagxon($mesagxo,
	             "IS-Sekurkopioj",
	             $renkontigxo->datoj['sekurkopiojretadreso'],
				 "IS-Backup: #" . $partoprenanto->datoj[ID] . " + #". $partopreno->datoj[ID],
				 "ne kodigu");

}

/**
 * TODO: dokumentado por sendu_konfirmilon
 *
 * $teksto - en tiu variablo ni metos la tekston de la mesagxo,
 *           por ebligi montri gxin ankoraux en la retpagxo (krom
 *           la dissendado).
 *
 * ### uzado:  partrezultoj.php, AligxiloDankon.php ###
 */
function sendu_konfirmilon($partoprenanto,$partopreno,$renkontigxo, &$teksto)
{
  $subject = "unua konfirmilo por la ".$renkontigxo->datoj[nomo];

  $mesagxo  = "### auxtomata mesagxo ###\n\n";

  $from_name = "IS-Aligilo";
  $from_address = "is.admin@esperanto.de"; // TODO: forigi retadreson
  $to_name = utf8_decode($partoprenanto->datoj[personanomo]." ".$partoprenanto->datoj[nomo]);
  $to_address = $partoprenanto->datoj[retposxto];

  $teksto = faru_1akonfirmilon($partoprenanto,$partopreno,$renkontigxo);

  $mesagxo .= $teksto;

  $email_message = new email_message_class;
  $email_message->default_charset = "UTF-8";
  if (!strcmp($error=$email_message->SetEncodedEmailHeader("To",$to_address, $to_name),"")
  && !strcmp($error=$email_message->SetEncodedEmailHeader("From",$from_address, $from_name),"")
  && !strcmp($error=$email_message->SetEncodedEmailHeader("Reply-To",$from_address, $from_name),"")
	  && !strcmp($error=$email_message->SetEncodedEmailHeader("Bcc","Paul.Ebermann@esperanto.de","Paul Ebermann"),"")  // TODO: forigu, se suficxas la kopioj
  && !strcmp($error=$email_message->SetEncodedHeader("Errors-To",$from_address, $from_name),"")
	  //  && !strcmp($error=$email_message->SetEncodedHeader("Return-Path",$from_address, $from_name),"") 
  && !strcmp($error=$email_message->SetEncodedHeader("Subject",$subject),"")
  && !strcmp($error=$email_message->AddQuotedPrintableTextPart($email_message->WrapText($mesagxo)),""))
  $error = $email_message -> Send();
	  if ($error)
		{
		  erareldono($error);
		  exit();
		}

}

/**
 * TODO: dokumentado por sendu_2ankonfirmilon
 * TODO: 2a konfirmilo adaptu al Wetzlar (aux
 *  prenu el datumbazo)
 * TODO: Übergabeparameter verschönern
 *
 * ### Uzata en administrado.php, partrezultoj.php . ###
 */
function sendu_2ankonfirmilon($row,$savu,$to_name,$to_address,$bcc='')
{
  $prafix = $GLOBALS['prafix'];
  require_once($prafix.'/iloj/kreu_konfirmilon.php');

      
      $korpo = "Saluton ".$to_name . ",";

	  if ($row['germane'] == 'J')
		{
		  $korpo .="\n\n [Deutsche Ãœbersetzung unten / Germana traduko sube.]";
		}

// TODO: Xanten -> verallgemeinern (datumbazo) (en kelkaj lokoj)
// TODO: Eble metu la tekston en la datumbazon aux en konfigurdosieron.


      $korpo .= "\n\nla organiza teamo tre gxojas, ke vi intencas veni al la cxijara Internacia Seminario en Wewelsburg.\n";
      $korpo .= "\nPer tiu cxi retmesagxo vi ricevas la oficialan konfirmilon por la IS kiel pdf-komputildosiero. Bonvolu traglegi gxin kaj kunporti elpresitan version de gxi al la IS.\n";
      //$korpo .= "\nSe vi ne povas legi la .pdf bonvolu kontaktu min.\n";
// TODO: Auf 2006 umstellen/verallgemeinern
      $korpo .= "Por pli detalaj informoj bonvolu ankaux uzi nian retpagxon sub http://www.esperanto.de/is/eo/2006/\n";
      $korpo .= "\nEnhavo:\n - konfirmilo.pdf (Kotizoj kaj gravaj informoj.)";
      if ($row[agxo]<'18')
		$korpo.= "\n(enhavante ankaux la gepatran permeson por la IS - nepre kunportu gxin plenumota)";
      $korpo .= "\n - 2ainformilo.pdf (Pliaj informoj pri la IS-ejo, kaj la vojo al Wewelsburg)";

// TODO: subskribo auxtomata
      $korpo .= "\n\namike,\nJulia";

	  if ($row['germane'] == 'J')
		{
		  $korpo .= "\n\n-----[ Deutsche Ãœbersetzung / germana traduko ]-----------";
		  $korpo .= "\n\nSaluton ".$to_name . ",";
      $korpo .= "\n\nDas Organisations-Team freut sich sehr, dass du zur Internationalen Woche nach Wewelsburg kommen willst.\n";
      $korpo .= "\nMit dieser E-Mail erhÃ¤ltst du die offizielle BestÃ¤tigung fÃ¼r das IS als PDF-Datei. Bitte lies es dir durch und bring eine ausgedruckte Version zum IS mit.\n";
      //$korpo .= "\nSe vi ne povas legi la .pdf bonvolu kontaktu min.\n";
// TODO: Auf 2004 umstellen/verallgemeinern
      $korpo .= "FÃ¼r weitere Informationen nutze bitte auch unsere Webseite unter http://www.esperanto.de/is/de/2006/\n";
      $korpo .= "\nInhalt:\n - konfirmilo.pdf (Beitrag und wichtige Informationen - zweisprachig.)";
      if ($row[agxo]<'18')
		$korpo.= "\n(enthÃ¤lt auch die Eltern-Erlaubnis für das IS - unbedingt ausgefÃ¼llt mitbringen!)";
      $korpo .= "\n - 2ainformilo.pdf (Weitere Informationen Ã¼ber das IS, den IS-Ort, und den Weg nach Wewelsburg.)";
		}

	  $ppanto = new Partoprenanto($row[0]);
	  $kon = new Konfirmilo(bezonas_unikodon($ppanto));
      
      $kon ->kreu_konfirmilon($row[1],$row[0],$savu);
      $kon->sendu(); // dauxrigas kiel konfirmilo.pdf
      
      $dosierojn = array('dosieroj_generitaj/konfirmilo.pdf',
						 'dosieroj/2aInformilo.pdf'); // jen la necesaj dosieroj
      echo "Al: $to_address\n";
      sendu_dosier_mesagxon("Konfirmilo por via IS partopreno",$korpo,$to_name,$to_address,$dosierojn,$bcc);
      erareldono ("Messag^o sendata!");

}

function faru_1an_konfirmilon_germane($partoprenanto, $partopreno, $renkontigxo)
{
  $ek = "Hallo " .
	eotransformado($partoprenanto->datoj[personanomo]." ".$partoprenanto->datoj[nomo], "x-metodo");
  $ek .= utf8_encode("\nWir haben gerade deine Anmeldung für das Treffen ") .
	eotransformado($renkontigxo->datoj[nomo], "x-metodo") .
	" in " . eotransformado($renkontigxo->datoj[loko], "x-metodo").
	" erhalten.\n";
  if ($partopreno->datoj[retakonfirmilo]=="J")
  {
    $ek .= utf8_encode("\nDu hast angegeben, dass du Bestätigungen per E-Mail erhalten willst, daher\n".
	  "diese Nachricht als erste Bestätigung. Die zweite Bestätigung kommt im November.\n");
  }
  $ek .= "\n";
  //$ek .= "De ".$partoprenanto->datoj[retposxto]." venis la sekva aligxilo\n";
  $ek .= "Anmeldedatum: ".($partopreno->datoj[aligxdato])."\n";
  $ek .= "\n";
  $ek .= utf8_encode("---- Persönliche Daten ----\n");
  $ek .= "Vorname: ".$partoprenanto->datoj[personanomo]."\n";
  $ek .= "Nachname: ".$partoprenanto->datoj[nomo]."\n";
  $ek .= "Geschlecht: ".$partoprenanto->datoj[sekso] . utf8_encode(" (i = ina, weiblich, v = vira, männlich)\n");
  $ek .= "Geburtsdatum: ".($partoprenanto->datoj[naskigxdato])."\n";
  //  $ek .= utf8_encode("Beschäftigung: ").okupigxtipo($partoprenanto->datoj[okupigxo])." ".eotransformado($partoprenanto->datoj[okupigxteksto], "x-metodo")."\n";
  if ($partoprenanto->datoj[lando]=='16') $ek .= "Mitglied der DEJ: ".$partopreno->datoj[GEJmembro]."\n";
  $ek .= "\n";
  $ek .= "---- Adresse ----\n";
  if ($partoprenanto->datoj[adresaldonajxo]) $ek .= "Adresszusatz:".$partoprenanto->datoj[adresaldonajxo]."\n";
  $ek .= utf8_encode("Straße: ").$partoprenanto->datoj[strato]."\n";
  $ek .= "Postleitzahl: ".$partoprenanto->datoj[posxtkodo]."\n";
  $ek .= "Stadt: ".$partoprenanto->datoj[urbo]."\n";
  $ek .= "Land: ".eltrovu_landon($partoprenanto->datoj[lando])."\n";
  $ek .= "\n---- Kommunikation ----\n";
  $ek .= "Telefon: ".$partoprenanto->datoj[telefono]."\n";
  $ek .= "Telefax: ".$partoprenanto->datoj[telefakso]."\n";
  $ek .= "E-Mail: ".$partoprenanto->datoj[retposxto]."\n";

  $ek .= "\n---- Teilnahmedaten ----\n";

  if ($partoprenanto->datoj['ueakodo'])
      {
          $ek .= "Via UEA-kodo estas " . $partoprenanto->datoj['ueakodo'] . ".";
      }

  if ($partopreno->datoj[komencanto][0]=="J")
  {
    $ek .= utf8_encode("Du bist Neuling/Anfänger.\n");
  }
  // TODO: eble ankaux traduku
//   if ($partopreno->datoj[invitletero][0]=="J")
//   {
//     $ek .= "Vi deziras invitlereron por pasportnumero: ".$partopreno->datoj[pasportnumero].".\n";
//   }
//   if ($partopreno->datoj["havas_asekuron"]{0} == "J")
// 	{
// 	  $ek .= "Vi havas asekuron pri malsano kaj kunportos la necesajn paperojn.\n";
// 	}
//   else
// 	{
// 	  $ek .= "Vi ne havas tauxgan asekuron pri malsano.\n";
// 	}
  if ($partopreno->datoj[partoprentipo][0]=="t")
  {
	$ek .= "Du nimmst die gesamte Zeit teil (von: " .$partopreno->datoj[de]. " bis: " . $partopreno->datoj[gxis].")\n";
  }
  elseif ($partopreno->datoj[partoprentipo][0]=="p")
  {
	$ek .= "Du nimmst nur zeitweise teil (von: " .$partopreno->datoj[de]. " bis: " . $partopreno->datoj[gxis].")\n";
  }
  else
  {
	$ek .= "Es fehlt der Teilnahmezeitraum. Da muss wohl ein Fehler aufgetreten sein - bitte sag uns Bescheid.\n";
  }
  if ($partopreno->datoj[vegetare][0]=="J")
  {
	$ek .= "Du hast dich als Vegetarier angemeldet und ";
  }
  else if($partopreno->datoj['vegetare']{0}=="A")
	{
	  $ek .= "Du hast dich als Veganer angemeldet und ";
	}
  else if ($partopreno->datoj['vegetare']{0}=="N")
	{
	  $ek .="Du hast dich als Fleischesser angemeldet und ";
	}
  else
	{
        $ek .= utf8_encode("Du hast dich (bezüglich Essen) unbekannt angemeldet\n (") .
		$partopreno->datoj['vegetare'] . ") und ";
	}

  if ($partopreno->datoj[domotipo][0]=="M")
  {
    $vosto .= "bist Selbstversorger";
    if ($partopreno->datoj[kunmangxas][0]!="N")
    {
      $vosto .= ", aber isst mit";
    }
  }
  else if ($partopreno->datoj[domotipo][0]=="J")
  {
    $vosto .= "wohnst in der Jugendherberge in (vielleicht) ";
    if ($partopreno->datoj[dulita][0]=="J")
    {
      $vosto .= "zweibettigem ";
    }
    if ($partopreno->datoj[cxambrotipo][0]=="u")
    {
      $vosto .= "eingeschlechtlichem ";
    }
    if ($partopreno->datoj[cxambrotipo][0]=="g")
    {
      $vosto .= "beidgeschlechtlichem "."";
    }
    if ($partopreno->datoj[cxambrotipo][0]=="n")
    {
      $vosto .= "unwichtigem "."";
    }
    $vosto .= "Zimmer";

    if ($partopreno->datoj[kunkiu]!="")
    {
      $vosto .= ", (vielleicht) mit ".$partopreno->datoj[kunkiu]."\n";
    }
  }
  $ek .= $vosto;
  $ek .= ".\n";

  if ($partopreno->datoj[tema])
  {
    $ek .= "\n[X] Du willst zum thematischem Programm beitragen mit: ".$partopreno->datoj[tema]."";
  }
  if ($partopreno->datoj[distra])
  {
    $ek .= "\n[X] Du willst zum Zerstreuungs-Programm beitragen mit: ".$partopreno->datoj[distra]."";
  }
  if ($partopreno->datoj[vespera])
  {
    $ek .= "\n[X] Du willst zum Abend-Programm beitragen mit: ".$partopreno->datoj[vespera]."";
  }
  if ($partopreno->datoj[muzika])
  {
    $ek .= "\n[X] Du willst zum musikalischem Abend beitragen mit: ".$partopreno->datoj[muzika]."";
  }

  if ($partopreno->datoj[rimarkoj])
  {
    $ek .= "\nDu hast die folgenden Bemerkungen:\n\n[".$partopreno->datoj[rimarkoj]."]\n";
  }
  // Sonderregelung Deutsche Ü 27
  if ($renkontigxo->datoj["mallongigo"] == "IS 2004"
	  and $partoprenanto->datoj["lando"] == HEJMLANDO
	  and strcmp($partoprenanto->datoj["naskigxdato"],"1977-12-27") < 0  // (felicxe datoj same ordigxas kiel la tekstoj)
	  )
	{
	  $ek .= utf8_encode("\n Du hast (zu Beginn des IS) ein Alter von 27 Jahren oder mehr."
		. "\n Daher kannst du nur teilnehmen, wenn wenn du zum Programm"
		. "\n beiträgst. Bitte schicke deine Vorschläge an die"
		. "\n Programmverantwortlichen. Siehe"
		. "\n http://www.esperanto.de/is/de/2004/adresoj#programo"
		. "\n");
	}


  {
	// TODO: gxeneraligi antauxpagon
  // TODO: (se ankoraux uzata) eltrovu_landokategorion nun funkcias alimaniere.
	$landkat = eltrovu_landokategorion($partoprenanto->datoj[lando]);
	switch ($landkat)
	  {
	  case "A":
		$antauxpago = "30";
		break;
	  case "B":
		$antauxpago = "10";
		break;
	  }
	if ($landkat != "C")
	  {
		$ek .= utf8_encode("\n\nVergiss nicht, dass deine Anmeldung nur nach Eingang einer Anzahlung von mindestens {$antauxpago} Euro bei uns gültig wird.\n");
		$ek .= utf8_encode("Für Zahlungsmöglichkeiten siehe http://www.internacia-seminario.de/2004/kontoj\n\n");
	  }
  }

  $ek .= utf8_encode(
	"\nUm das IS schon vor dem IS zu besprechen, gemeinsame Anreise".
	"\nzu planen, usw., existiert jetzt eine eigene Yahoogroup:".
	"\nis-en-germanio (Die Kommunikation dort wird vor allem auf".
	"\n Esperanto stattfinden, dadurch kannst du dich daran schon".
	"\n gewöhnen ...)".
	"\n".
	"\nUm dich anzumelden, nutze ".
	"\n       http://groups.yahoo.com/group/is-en-germanio/" .
	"\noder schicke eine E-Mail an" .
	"\n       is-en-germanio-subscribe@yahoogroups.com" .
	"\n");


  if ($partopreno->datoj['invitletero']{0}=='J')
  {
	$ek .= "\n Du willst ein Einladungsschreiben. Siehe die Hinweise auf Esperanto oben.\n";
  }
  $ek .= "\nWir erwarten euch beim IS.\n\nJulia Noe, im Namen des Organisations-Teams des IS.";
  return $ek;
}


/**
 * TODO: dokumentado por faru_1ankonfirmilon
 *
 * ### uzata de partrezultoj.php, AligxiloDankon.php,
 *     kaj sendu_konfirmilon(). ###
 */
function faru_1akonfirmilon($partoprenanto,$partopreno,$renkontigxo)
{
  //$partoprenanto = new Partoprenanto($antoID);
  //$partopreno = new Partopreno($enoID);
  //$kotizo = new Kotizo($partopreno,$partoprenanto,$renkontigxo);

  $ek = "";

  if ($partopreno->datoj["germanakonfirmilo"] == "J")
	{
	  $ek .= utf8_encode("[ Deutsche Übersetzung am Ende. ]\n\n");
	}

  $ek .= "Saluton kara " .
	eotransformado($partoprenanto->datoj[personanomo]." ".$partoprenanto->datoj[nomo], "x-metodo");
  $ek .= "\nni jxus ricevis vian aligxilon por la\n" .
	eotransformado($renkontigxo->datoj[nomo], "x-metodo") .
	" en " . eotransformado($renkontigxo->datoj[loko], "x-metodo").".\n";
  if ($partopreno->datoj[retakonfirmilo]=="J")
  {
    $ek .= "\nVi indikis, ke vi deziras retan konfirmilon, do vi\n ricevas la jenan mesagxon kiel 1a konfirmilo. \nLa 2a konfirmilo sekvos en novembro.\n";
  }
  $ek .= "\n";
  //$ek .= "De ".$partoprenanto->datoj[retposxto]." venis la sekva aligxilo\n";
  $ek .= "Aligxdato: ".($partopreno->datoj[aligxdato])."\n";
  $ek .= "\n";
  $ek .= "---- Personaj datoj ----\n";
  $ek .= "Personanomo: ".$partoprenanto->datoj[personanomo]."\n";
  $ek .= "Familianomo: ".$partoprenanto->datoj[nomo]."\n";
  $ek .= "Sekso: ".$partoprenanto->datoj[sekso]."\n";
  $ek .= "Naskigxdato: ".($partoprenanto->datoj[naskigxdato])."\n";
  //  $ek .= "okupigxo: ".okupigxtipo($partoprenanto->datoj[okupigxo])." ".$partoprenanto->datoj[okupigxteksto]."\n";
  if ($partoprenanto->datoj[lando]=='16') $ek .= "Gejmembro: ".$partopreno->datoj[GEJmembro]."\n";
  $ek .= "\n";
  $ek .= "---- Adreso ----\n";
  if ($partoprenanto->datoj[adresaldonajxo]) $ek .= "adresaldonajxo:".$partoprenanto->datoj[adresaldonajxo]."\n";
  $ek .= "strato: ".$partoprenanto->datoj[strato]."\n";
  $ek .= "posxtkodo: ".$partoprenanto->datoj[posxtkodo]."\n";
  $ek .= "urbo: ".$partoprenanto->datoj[urbo]."\n";
  $ek .= "lando: ".eltrovu_landon($partoprenanto->datoj[lando])."\n";
  $ek .= "\n---- Komunikado ----\n";
  $ek .= "telefono: ".$partoprenanto->datoj[telefono]."\n";
  $ek .= "telefakso: ".$partoprenanto->datoj[telefakso]."\n";
  $ek .= "retposxtadreso: ".$partoprenanto->datoj[retposxto]."\n";

  $ek .= "\n---- Partoprendatumoj ----\n";

  if ($partoprenanto->datoj['ueakodo'])
      {
          $ek .= "Via UEA-kodo estas " . $partoprenanto->datoj['ueakodo'] . ".\n";
      }
  if ($partopreno->datoj['tejo_membro_laudire'] == 'j')
      {
          $ek .= "Vi indikis, ke vi en " . substr($renkontigxo->datoj['gxis'], 0, 4) . " estos individua membro de TEJO.\n" .
              "(Ni kontrolos tion - prefere sendu vian TEJO/UEA-kotizon\n".
              " jam antaux la renkontigxo al UEA.)\n";
      }

  if ($partopreno->datoj[komencanto][0]=="J")
  {
    $ek .="Vi estas novulo / komencanto.\n";
  }
  if ($partopreno->datoj[invitletero][0]=="J")
  {
    $ek .= "Vi deziras invitlereron por pasportnumero: ".$partopreno->datoj[pasportnumero].".\n";
  }
  if ($partopreno->datoj["havas_asekuron"]{0} == "J")
	{
	  $ek .= "Vi havas asekuron pri malsano kaj kunportos la necesajn paperojn.\n";
	}
  else
	{
	  $ek .= "Vi ne havas tauxgan asekuron pri malsano.\n";
	}
  if ($partopreno->datoj[partoprentipo][0]=="t")
  {
    $ek .= "Vi partoprenos tuttempe (de: ".$partopreno->datoj[de]." gxis: ".$partopreno->datoj[gxis].")"."\n";
  }
  elseif ($partopreno->datoj[partoprentipo][0]=="p")
  {
    $ek .= "Vi partoprenos partatempe (de: ".$partopreno->datoj[de]." gxis: ".$partopreno->datoj[gxis].")"."\n";
  }
  else
  {
    $ek .= "mankas partoprentipo?? io eraro okazis - bonvolu kontaktu nin"."\n";
    //TODO: MAcht das skript dann automatisch :))
  }
  if ($partopreno->datoj[vegetare][0]=="J")
  {
    $ek .="Vi aligxis kiel vegetarano kaj ";
  }
  else if ($partopreno->datoj['vegetare']{0}=="N")
  {
    $ek .="Vi aligxis kiel viandmangxanto kaj ";
  }
  else if ($partopreno->datoj['vegetare']{0}=="A")
	{
	  $ek .= "Vi aligxis kiel vegano kaj ";
	}
  else
	{
	  $ek .= "Vi aligxis (pri mangxado) en nekonata formo\n" .
		"(" . $partopreno->datoj['vegetare'] . ")";
	}
  if ($partopreno->datoj[domotipo][0]=="M")
  {
    $vosto .= "memzorgas ";
    if ($partopreno->datoj[kunmangxas][0]!="N")
    {
      $vosto .= "sed kunmangxas ";
    }
  }
  else if ($partopreno->datoj[domotipo][0]=="J")
  {
    $vosto .= "junulargastejumas \n en (eble) ";
    if ($partopreno->datoj[dulita][0]=="J")
    {
      $vosto .= "dulita ";
    }
    if ($partopreno->datoj[cxambrotipo][0]=="u")
    {
      $vosto .= "unuseksa ";
    }
    if ($partopreno->datoj[cxambrotipo][0]=="g")
    {
      $vosto .= "gea "."";
    }
    if ($partopreno->datoj[cxambrotipo][0]=="n")
    {
      $vosto .= "negrava "."";
    }
    $vosto .= "cxambro ";

    if ($partopreno->datoj[kunkiu]!="")
    {
      $vosto .= "(eble) kun ".$partopreno->datoj[kunkiu]."\n";
    }
  }
  $ek .= $vosto;

  if ($partopreno->datoj[tema])
  {
    $ek .= "\n[X] kontribuos al la tema programo per: ".$partopreno->datoj[tema]."";
  }
  if ($partopreno->datoj[distra])
  {
    $ek .= "\n[X] kontribuos al la distra programo per: ".$partopreno->datoj[distra]."";
  }
  if ($partopreno->datoj[vespera])
  {
    $ek .= "\n[X] kontribuos al la vespera programo per: ".$partopreno->datoj[vespera]."";
  }
  if ($partopreno->datoj[muzika])
  {
    $ek .= "\n[X] kontribuas al la muzika vespero: ".$partopreno->datoj[muzika]."";
  }

 /* if ($partopreno->datoj[rabato]!=0.00)
  {
    $ek .= "\n[X] deziras rabato de: ".$partopreno->datoj[rabato]." Euro, cxar \"".$partopreno->datoj[kialo]." \"";
  }*/
  if ($partopreno->datoj[rimarkoj])
  {
    $ek .= "\nkaj havas la jenajn rimarkojn:\n\n[".$partopreno->datoj[rimarkoj]."]\n";
  }
  // Sonderregelung Deutsche Ü 27
  if ($renkontigxo->datoj["mallongigo"] == "IS 2005"
	  and $partoprenanto->datoj["lando"] == HEJMLANDO 
	  and strcmp($partoprenanto->datoj["naskigxdato"],"1978-12-27") < 0  // (felicxe datoj same ordigxas kiel la tekstoj)
	  )
	{
	  $ek .= "\nVi (je la komenco de IS) agxos 27 jarojn aux pli. Tial vi"
		. "\n nur povos partopreni, se vi kontribuos al la programo."
		. "\n Bonvolu sendi proponojn al la programrespondeculoj."
		. "\n Vidu  http://www.esperanto.de/is/de/2005/adresoj#programo."
		. "\n";
	}

  { // TODO: generaligu antauxpagon (prenu el datumbazo?)
  // TODO: (se ankoraux uzata) eltrovu_landokategorion nun funkcias alimaniere.
	$landkat = eltrovu_landokategorion($partoprenanto->datoj[lando]);
	switch ($landkat)
	  {
	  case "A":
		$antauxpago = "30";
		break;
	  case "B":
		$antauxpago = "10";
		break;
	  }
	if ($landkat != "C")
	  {
		$ek .= "\n\nNe forgesu, ke via aligxo nur ekvalidas post alveno de\n minimuma antauxpago de {$antauxpago} euxroj cxe ni.\n";
		$ek .= "Pri pageblecoj rigardu http://www.esperanto.de/is/eo/2006/kontoj\n\n";
	  }
  }

  if ($partopreno->datoj[invitletero]{0}=='J')
  {
	$ek .= "\n" . donu_tekston("konf1-invitilo", $renkontigxo) . "\n";
  }
  $ek .=
	"\nPor priparoli la ISon jam antaux la IS, plani kunveturadon, ktp." .
	"\nnun ekzistas aparta jahugrupo:  is-en-germanio." .
	"\n" .
	"\nPor aligxi uzu" .
	"\n       http://groups.yahoo.com/group/is-en-germanio/" .
	"\naux sendu retmesagxon al" .
	"\n       is-en-germanio-subscribe@yahoogroups.com" .
	"\n";

  $ek .= "\nNi atendos vin en la IS.\n\nJulia Noe en la nomo de la organiza teamo de la IS.";
  //$kotizo->montru_kotizon(1,$partopreno,$partoprenanto,$renkontigxo);
  //$ek .= $kotizo->mesagxo;

  if ($partopreno->datoj["germanakonfirmilo"] == "J")
	{
	  $ek .= utf8_encode("\n\n ------- Deutsche Übersetzung ---------\n\n") .
						 faru_1an_konfirmilon_germane($partoprenanto, $partopreno,
													  $renkontigxo);
	}
  return $ek;
}

/**
 * TODO: dokumentado por faru_aligxtekston
 *
 * ### uzata nuntempe nur en sendu_ekzport()  (supre)
 * ###  kaj (provizore) en diversaj_retmesagxoj.php    ###
 */
function faru_aligxtekston($antoID,$enoID)
{
  $partoprenanto = new Partoprenanto($antoID);
  $partopreno = new Partopreno($enoID);

  $ek = "----- aufgeschluesselter Teil ----\n";
  $ek .= "De ".$partoprenanto->datoj[retposxto]." venis la sekva aligxilo\n";
  $ek .= "Aligx-Dato = ".($partopreno->datoj[aligxdato])."\n";
  $ek .= "\n";

  $ek .= "Nomo = ".$partoprenanto->datoj[nomo]."\n";
  $ek .= "Antauxnomo = ".$partoprenanto->datoj[personanomo]."\n";
  $ek .= "Sekso = ".$partoprenanto->datoj[sekso]."\n";
  $ek .= "Naskigxdato = ".($partoprenanto->datoj[naskigxdato])."\n";
  $ek .= "Gejmembro = ".$partopreno->datoj[GEJmembro]."\n";
  $ek .= "\n";
  $ek .= "---- Adreso ----\n";
  if ($partoprenanto->datoj[adresaldonajxo]) $ek .= "adresaldonajxo:".$partoprenanto->datoj[adresaldonajxo]."\n";
  $ek .= "strato = ".$partoprenanto->datoj[strato]."\n";
  $ek .= "posxtkodo = ".$partoprenanto->datoj[posxtkodo]."\n";
  $ek .= "urbo = ".$partoprenanto->datoj[urbo]."\n";
  $ek .= "lando = ".eltrovu_landon($partoprenanto->datoj[lando])."\n";
  $ek .= "\n---- Komunikado ----\n";
  $ek .= "telefono = ".$partoprenanto->datoj[telefono]."\n";
  $ek .= "telefakso = ".$partoprenanto->datoj[telefakso]."\n";
  $ek .= "retposxtadreso = ".$partoprenanto->datoj[retposxto]."\n";

  $ek .= "\n---- Partoprendatumoj ----\n";

  if ($partopreno->datoj[komencanto][0]=="J")
  {
    $ek .="[X] estas novulo / komencanto\n";
  }
  if ($partopreno->datoj[invitletero][0]=="J")
  {
    $ek .= "[X] bezonas invitlereron por pasportnumero: ".$partopreno->datoj[pasportnumero]."\n";
  }
  if ($partopreno->datoj[partoprentipo][0]=="t")
  {
    $ek .= "partoprenos >>>tuttempe<<< \n(de: ".$partopreno->datoj[de]." gxis: ".$partopreno->datoj[gxis].")"."\n";
  }
  elseif ($partopreno->datoj[partoprentipo][0]=="p")
  {
    $ek .= "partoprenos >>>partatempe<<< \n(de: ".$partopreno->datoj[de]." gxis: ".$partopreno->datoj[gxis].")"."\n";
  }
  else
  {
    $ek .= "mankas partopreno?? io eraro okazis - bonvolu kontaktu nin"."\n";
    // MAcht das skript dann automatisch :))
  }
  if ($partopreno->datoj[vegetare][0]=="J")
  {
    $ek .="estas >>>vegetarano<<<"."\n";
  }
  else if($partopreno->datoj['vegetare']{0} == 'A')
	{
	  $ek .= "estas >>>vegano<<<\n";
	}
  else if ($partopreno->datoj['vegetare']{0} == 'N')
	{
	  $ek .="estas >>>viandmangxanto<<<"."\n";
	}
  else
	{
	  $ek .= "mangxas en nekonata formo (" . $partopreno->datoj['vegetare'] . ")\n";
	}

  if ($partopreno->datoj[domotipo][0]=="M")
  {
    $vosto .= ">>>memzorgas<<<"."\n";
    if ($partopreno->datoj[kunmangxas][0]!="N")
    {
      $vosto .= "sed kunmang^as ";
    }
  }
  else if ($partopreno->datoj[domotipo][0]=="J")
  {
    $vosto .= ">>>junulargastejumas<<< \n en ";
    if ($partopreno->datoj[dulita][0]=="J")
    {
      $vosto .= ">>>dulita<<< \n";
    }
    if ($partopreno->datoj[cxambrotipo][0]=="u")
    {
      $vosto .= ">>>unuseksa<<< (sekso estas: ".$partoprenanto->datoj[sekso].")\n";
    }
    if ($partopreno->datoj[cxambrotipo][0]=="g")
    {
      $vosto .= ">>>gea<<<"."\n";
    }
    if ($partopreno->datoj[cxambrotipo][0]=="n")
    {
      $vosto .= ">>>negrava<<< "."\n";
    }
    $vosto .= "cxambro \n\n";

    if ($partopreno->datoj[kunkiu]!="")
    {
      $vosto .= "volas logxi kun >>>".$partopreno->datoj[kunkiu]."<<<\n";
    }
  }
  $ek .= $vosto;

  if ($partopreno->datoj[tema])
  {
    $ek .= "[X] kontribuos al la tema programo per: ".$partopreno->datoj[tema]."\n";
  }
  if ($partopreno->datoj[distra])
  {
    $ek .= "[X] kontribuos al la distra programo per: ".$partopreno->datoj[distra]."\n";
  }
  if ($partopreno->datoj[vespera])
  {
    $ek .= "[X] kontribuos al la vespera programo per: ".$partopreno->datoj[vespera]."\n";
  }
  if ($partopreno->datoj[muzika])
  {
    $ek .= "[X] kontribuas al la muzika vespero: ".$partopreno->datoj[muzika]."\n";
  }
  // TODO: Prüfen, ob es wegfallen kann.
  if ($partopreno->datoj[rabato]!=0.00)
  {
    $ek .= "[X] deziras rabato de: ".$partopreno->datoj[rabato]." E^, c^ar \"".$partopreno->datoj[kialo]." \"";
  }

  if ($partopreno->datoj["havas_asekuron"]{0} == "N")
	{
	  $ek .= "[X] bezonas asekuron pri malsano.";
	}

  return $ek;
}

/**
 * Sendas plurajn mesagxojn al programresponduloj, invitilo-repondulo,
 * ktp, se necesas.
 *
 * ### uzata en partrezultoj.php, AligxiloDankon.php ###
 */
function sendu_auxtomatajn_mesagxojn($partopreno, $partoprenanto, $renkontigxo)
{
         // TODO: Etwa hier sollten wir auch nötige Mails verschicken
       // TODO: können wir mit partoprenkontrolo zusammenlegen.
      if ($partopreno->datoj['invitletero']{0} == 'J')
      {
        sendu_mesagxon_invitilo($partopreno->datoj[ID],
								$partoprenanto->datoj[ID],
								$partopreno->datoj['pasportnumero'],
								$renkontigxo->datoj['invitleterorespondeculo'],
								$renkontigxo->datoj['invitleteroretadreso'],
								$partopreno->datoj['rimarkoj']);
      }

	  // Programkontribuoj:

	  foreach(array('tema', 'distra', 'vespera', 'muzika', 'nokta') as $tipo)
		{
		  if ($partopreno->datoj[$tipo])
			{
			  sendu_mesagxon_programan($partopreno->datoj['ID'],
									  $partoprenanto->datoj['ID'],
									  $tipo,
									  $partopreno->datoj[$tipo],
									  $renkontigxo->datoj[$tipo . 'respondulo'],
									  $renkontigxo->datoj[$tipo . 'retadreso'],
									  $partopreno->datoj['rimarkoj']);
			}
		}


	  // Sonderregelung Deutsche Ü 27
	  sendu_mesagxon_se_troagxa($partopreno, $partoprenanto, $renkontigxo);

	  sendu_mesagxon_se_juna_aux_nova($partopreno, $partoprenanto, $renkontigxo);


}


/**
 * TODO: dokumentado por kreunoton
 * kreas noton; gxis nun nur uzata en la sendumesagxon.php por
 *  krei la saman tekston kiel noton kiun oni jxus sendis.
 *
 * ### uzata en sendumesagxon.php.      ###
 */
function kreunoton($partoprenantoID,$kiu,$kunKiu="",$tipo="alia",$subjekto,$enhavo,$prilaborata='j')
{
  $noto = new Noto(0);
  $noto->kreu();
  $noto->datoj[partoprenantoID] = $partoprenantoID;
  $noto->datoj[kiu] = $kiu;
  $noto->datoj[kunKiu] = $kunKiu;
  $noto->datoj[tipo] = $tipo;
  $noto->datoj[subjekto] = $subjekto;
  $noto->datoj[enhavo] = $enhavo;
  $noto->datoj[prilaborata] = $prilaborata;
  $noto->datoj[dato] = date("Y-m-d H:i:s");
  $noto->skribu();
}



?>
