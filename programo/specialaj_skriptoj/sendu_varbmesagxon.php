<?php

/*
 * Por dissendado de varbmesagxo al partoprenintoj
 */


/*
         1         2         3         4         5         6         7         8
12345678901234567890123456789012345678901234567890123456789012345678901234567890
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
Saluton kara {$nomo},

Denove apud granda fortikaj^o, kaj denove kun viaj plej s^atataj
bandoj, ni invitas vin al 51a Internacia-Seminario! G^i okazos
en Würzburg inter la 27.12.2007 kaj la 03.01.2008.
Espereble kun vi.

Vi certe jam scias ke post kelkaj tagoj venos la fino de la
frua alig^kategorio.

Do alig^u g^is la 31a oktobro por spari monon. G^is la 20a decembro
eblas alig^i rete.

Sed nun alig^is jam 159 homoj, kaj restas nur 50 litoj liberaj en
la junulargastejo. Do alig^u rapide se vi volas dormi en lito:
    http://www.esperanto.de/is/eo/2007/

La temo de la 51a Internacia Seminario estas: \"Popola Identeco\".
C^u vi sentas kataluno au^ hispano, anglo au^ brito, bosniano
au^ serbo, preskau^ c^ie en la mondo ekzistas konflikto inter
diversaj popolaj identecoj. Ni ankorau^ serc^as volontulojn kiuj
s^atas kontribui al la tema programo. Do se vi emas rakonti pri
popolo au^ identeco, tiam bonvolu sendu vian proponon. Ne gravas
c^u vi montros fotojn au^ prelegos serioze. Bonos ke vi sciigos al
ni viajn spertojn.

C^i jare blovas fres^a vento el Britio. Rolf Fantom estas la nuna
estro de la IS, kiu nun regas super la germanoj. Li venis per
novaj ideoj por plibonigi vian Internacian Seminarion. Ekzemple
oni nun devas promeni je la maldekstra flanko de la koridoroj
ene de la junulargastejo, kaj pardonpeti ec^ se io ne estas via
kulpo. Se vi nun estas scivolema c^u li ankau^ forstrekis la
Gufujojn (c^ar en Britio oni nur bezonas drinkejon) tiam alig^u ;-)
     http://www.esperanto.de/is/eo/2007/

Unu afero, kiun Rolf ja forstrekis, estis la tuttaga ekskurso!
Jes, vere. Sed por kompensi tiun domag^on ni nun havas multajn
kaj diversajn ekskursojn duontagajn.

Por memzorgantoj ni havas ankorau^ iom pli da spaco. Por la
memzorgantoj sekvas gravaj novaj^oj:

Unue: la memzorgantejo estas malpli ol cent metroj for de la
      junulargastejo!
Due:  eblas dormi en memzorgantejo kaj tamen mang^i en la
      junulargastejo kontrau iom da mono (detaloj sekvos)!
Trie: Ni metos akvo-varmigilojn en la memzorgantejo. (Jes,
      \"Ni amas vin en IS!\")

Do se vi ankorau^ ne alig^is tiam faru nun, por plej bona
prezo - poste g^i estas konsindere pli alta:
     http://www.esperanto.de/is/eo/2007/

Se vi jam alig^is invitu viajn geamikojn por
\"kunigi kaj kunligi\" c^e IS.

G^is baldau^!

La organizantoj
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
      sendu_xxxxx_mesagxon("51a IS - limdato por dua kategorio: 31a de oktobro", $teksto,$to_name,$to_address, $kodigo);
      erareldono ("Messag^o sendita!");
}


$prafix = "../";
require_once ($prafix . "iloj/iloj.php");
session_start();

malfermu_datumaro();




$komenco = 0;
$nombro = 100;
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
            sendu_specialan_mesagxon($row,$to_name,$to_address);
        }
    else
        {
            eoecho(", ne necesas)");
        }
}

echo "<br/><a name='fino'>Fino</a>.<br/>\n";


?>
