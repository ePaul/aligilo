<?php

/*
 * Por dissendado de iuj informoj.
 */


/**
 * Sendas retmesagxon al iu homo.
 *
 * $subjekto  - temlinio de la mesagxo (en UTF-8, EO-signoj per c^-kodigo)
 * $korpo     - la teksto de la mesagxo (dito)
 * $to_name   - la nomo de la ricevonto (dito)
 * $to_adress - la retposxtadreso de la ricevonto
 */
function sendu_xxxxx_mesagxon($subjekto,$korpo,$to_name,$to_address)
{
  $subject = eotransformado($subjekto, "utf-8");

//  $mesagxo  = "### auxtomata mesagxo de la DEJ-aligilo ###\n\n";
  $mesagxo .= eotransformado($korpo, "utf-8");


  $from_name = "Pauxlo Ebermann";   // TODO: (eble prenu nomon aux el la datumbazo/konfiguro, aux la entajpanton ?)
  $from_address = "Paul.Ebermann@esperanto.de";  // TODO: Eble prenu el la datumbazo?

  $email_message = new email_message_class;
  $email_message->default_charset="UTF-8";
  
  if (!strcmp($error=$email_message->SetEncodedEmailHeader("To",$to_address, eotransformado($to_name, "utf-8")),"")
  && !strcmp($error=$email_message->SetEncodedEmailHeader("From",$from_address, $from_name),"")
  && !strcmp($error=$email_message->SetEncodedEmailHeader("Reply-To",$from_address, $from_name),"")
  && !strcmp($error=$email_message->SetEncodedHeader("Errors-To",$from_address, $from_name),"")
	 //  && !strcmp($error=$email_message->SetEncodedHeader("Return-Path",$from_address, $from_name),"") 
	  && !strcmp($error=$email_message->SetEncodedEmailHeader("Bcc","Paul.Ebermann@esperanto.de","Paul Ebermann"),"")  // TODO: forigu, se suficxas la kopioj
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
 */
function sendu_specialan_mesagxon($row,$to_name,$to_address,$bcc='')
{

  $nomo = eotransformado($row[personanomo], "utf-8");

  $teksto = <<<DATOFINO
Kara $nomo,
 
antaux ioma tempo vi aligxis al la 49a Internacia Seminario.
Ni rimarkis, ke ofte helpas, ke partoprenontoj povas komuniki
inter si (kaj ne nur al KKRen) jam antaux la IS - ekzemple pri
kuna alveturo, kaj similaj aferoj.

Pri tio ni nun kreis dissendolisto (jahugrupon) "is-en-germanio".

Gxi estos uzata ne nur por tiu cxi IS, sed por cxiuj ontaj ISoj
(gxis ni iam trovos alian solvon), do indas aligxi al gxi ekzemple
ankaux por ekscii pri la sekvaj ISoj ...

Vi povas aligxi al gxi per la retpagxo de la grupo

    http://groups.yahoo.com/group/is-en-germanio

aux per sendo de retmesagxo al

    is-en-germanio-subscribe@yahoogroups.com


Kelkaj KKRenanoj jam aligxis, do ankaux gxeneralajn demandojn
vi tie povos meti (kaj espereble ricevi respondon).
Pri konkretaj problemoj turnigxu al la koncerna respondulo
ene de KKRen - listo trovigxas cxe
    http://www.esperanto.de/is/de/2005/adresoj.


Kore salutas
nome de KKRen (Konstanta Komisiono pri Renkontigxoj de GEJ)
    kaj IReK  (InterReta Komisiono de GEJ)

Pauxlo Ebermann (respondeculo pri tekniko en KKRen kaj
                 membro de IReK)

DATOFINO;
      
      echo "Al: $to_address";
      sendu_xxxxx_mesagxon("Dissendolisto pri la IS",$teksto,$to_name,$to_address);
      erareldono ("Messag^o sendata!");
}


require_once ("iloj.php");
session_start();

malfermu_datumaro();

die("Vi ne rajtas uzi tiun dosieron. Se vi ne scias, kial, demandu Pauxlon.");

$komenco = 50;
$nombro = 20;

$demando = datumbazdemando(array("p.ID", "nomo", "personanomo", "retposxto", "sekso",
								 "pn.agxo" ),
						   array("partoprenantoj" => "p", "partoprenoj" => "pn"),
						   array("pn.partoprenantoID = p.ID",
								 "retposxto <> ''",
								 "alvenstato = 'v'",
								 "renkontigxoID = '5'",
								 ),
						   "",
						   array("limit" => "$komenco, $nombro",
								 "order" => "pn.ID ASC"
								 )
						   );

echo "Demando: [" . $demando . "]<br/>\n";

$rezulto = sql_faru($demando);

$i = $komenco;

while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
{
  eoecho($i . " " . $row[personanomo]." ".$row[nomo]."<br/>\n");  
  $i++;
  $to_name = $row[personanomo]." ".$row[nomo];
  $to_address = $row[retposxto];
  sendu_specialan_mesagxon($row,$to_name,$to_address);
}

echo "Fino.<br/>\n";


?>