<?php

/*
 * Por dissendado de varbmesagxo al partoprenintoj
 */


/**
 * Sendas retmesagxon al iu homo.
 *
 * $subjekto  - temlinio de la mesagxo (en UTF-8, EO-signoj per c^-kodigo)
 * $korpo     - la teksto de la mesagxo (dito)
 * $to_name   - la nomo de la ricevonto (dito)
 * $to_adress - la retposxtadreso de la ricevonto
 */
function sendu_xxxxx_mesagxon($subjekto,$korpo,$to_name,
                              $to_address, $kodigo='utf-8')
{
  $subject = eotransformado($subjekto, $kodigo);

//  $mesagxo  = "### auxtomata mesagxo de la DEJ-aligilo ###\n\n";
  $mesagxo .= eotransformado($korpo, $kodigo);
//  $mesagxo .= "\n\n### Se estas iu problemo bonvolu informi Paul.Ebermann@esperanto.de ###";


  $from_name = "IS-Teamo";   // TODO: (eble prenu nomon aux el la datumbazo/konfiguro, aux la entajpanton ?)
  $from_address = "is.admin@esperanto.de";  // TODO: Eble prenu el la datumbazo?

  $email_message = new email_message_class;
  $email_message->default_charset="UTF-8";
  
  if (!strcmp($error=$email_message->SetEncodedEmailHeader("To",$to_address, eotransformado($to_name, $kodigo)),"")
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
 * kreas la mesagxon kaj vokas sendu_xxxxx_mesagxon().
 */
function sendu_specialan_mesagxon($row,$to_name,$to_address)
{

    $jaroj = '200' . $row['renkID'] .'/200' . ($row['renkID']+1);
    $nomo = $row['personanomo'];

    if ($row['retposxta_varbado'] == 'u')
        {
            $kodigoteksto =
                "
Vi ricevas la mesag^ojn en unikoda formato UTF-8 - se vi preferas
la ikso-kodigon (do cx, gx, hx, jx, sx, ux), bonvolu same mencii tie.";
            $kodigo = 'utf-8';
        }
    else
        {
            $kodigoteksto = "
Vi ricevas la mesag^ojn en ikso-kodigo - se vi preferas la unikodan
formaton UTF-8 (do ĉ, ĝ, ĥ, ĵ, ŝ, ŭ), bonvolu same mencii tie.";
            $kodigo = "x-metodo";
        }

    $renkontigxo = new Renkontigxo($row['renkID']);
    $renkNomo = $renkontigxo->datoj['nomo'];
    $renkLoko = $renkontigxo->datoj['loko'];

  $teksto = "
Saluton kara IS-partopreninto {$nomo}!

Jes, ja, denove alvenas la tempo pripensadi vian
silvestran vojag^adon!

Se vi planas veni al la 51a Internacia Seminario, ni
volas atentigi ke baldau^ finig^os la ebleco veni plej
favorpreze - la limdato alvenas!

Se vi alig^os g^is la 27a de Au^gusto, kaj la antau^pago
tuj alvenos al ni, vi ricevos nian 'ege frua alig^o'-prezon ;-)
(La sekva limdato estas la 31a de oktobro.)

http://ttt.esperanto.de/is/aligxo

Tie vi povos vidi c^u viaj amikoj estas inter la 73 kiuj jam
au^guste (au^ pli frue) alig^is, kalkuli vian kotizon, kaj
mem alig^i! Ankau^ estas detaloj tie pri kiel antau^pagi.

Ne forgesu inklusivi en via alig^ilo detalojn pri eventuala
kontribuo al la 51a Internacia Seminario, c^u prelego au^
laborgrupo, helpado en la drinkejo au^ muzikumado dum la
Internacia Vespero, ni volas au^di de vi :-)

Ni tre anticipas revidi vin dum la 51a Internacia Seminario
en Würzburg!

Nome de la tuta teamo,
Rolf Fantom,
C^eforganizanto

-----
Se vi ricevis tiun mesag^on kvankam vi jam alig^is por tiu IS,
au^ se vi ricevis tiun mesag^on plurfoje, bonvolu informi nin,
estas eraro au^ de la programo au^ en niaj datumoj.
Se vi pensas, ke vi neniam alig^is al iu IS, bonvolu ankau^
informi nin.

Ni sendos aktualajn informojn pri IS (lau^ tiuj kriterioj) eble
trifoje jare - se vi ne plu volas ricevi ilin, sendu mesag^on al
is.admin@esperanto.de (au^ alig^u kaj elektu en la alig^ilo
\"mi ne volas ricevi retpos^tajn informojn\").{$kodigoteksto}

";

      
      echo "Al: $to_address";
      sendu_xxxxx_mesagxon("51a IS - limdato por unua kategorio: 27a de au^gusto", $teksto,$to_name,$to_address, $kodigo);
      erareldono ("Messag^o sendita!");
}


$prafix = "../";
require_once ($prafix . "iloj/iloj.php");
session_start();

malfermu_datumaro();




$komenco = 960;
$nombro = 20;
// por elprovi:
 $nombro = 1;

$demando = datumbazdemando(array("p.ID" => "ID", "p.nomo" => "nomo",
                                 "personanomo",
                                 "retposxto",
                                 'retposxta_varbado',
                                 'MAX(pn.renkontigxoID)' => 'renkID',
//                                 'r.nomo' => "renkNomo",
//                                 'r.loko', 'r.de', 'r.gxis',
								  ),
						   array("partoprenantoj" => "p",
                                 "partoprenoj" => "pn",
                                 'renkontigxo' => "r"),
						   array("pn.partoprenantoID = p.ID",
								 "retposxto <> ''",
                                 "retposxta_varbado <> 'n'",
//                                 "MAX(pn.renkontigxoID) = r.ID",
								 ),
						   "",
						   array("limit" => "$komenco, $nombro",
                                 "group" => "p.ID",
								 "order" => "p.ID ASC",
								 )
						   );


echo "Demando: [<code>" . $demando . "</code>]<br/>\n";


$rezulto = sql_faru($demando);

 die("Vi ne rajtas uzi tiun dosieron. Se vi ne scias, kial, demandu Pauxlon.");

$i = $komenco;


while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
{
    eoecho("<br />" . $i . ": " . $row['personanomo']." ".$row['nomo'].": ". $row['ID'] .' (' . $row['renkID']);  
    $i++;


    if ($row['renkID'] < 7)
        {
            // ne jam aligxis al la 2006a IS

            eoecho(', sendota)');
				flush();

            $to_name = $row['personanomo']." ".$row['nomo'];
            $to_address = $row['retposxto'];
            $to_address = "Paul.Ebermann@esperanto.de";
//            sendu_specialan_mesagxon($row,$to_name,$to_address);
        }
    else
        {
            eoecho(", ne necesas)");
        }
}

echo "<br/><a name='fino'>Fino</a>.<br/>\n";


?>
