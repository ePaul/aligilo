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
Saluton kara {$nomo},

ni sendas al vi tiun mesag^on, c^ar vi iam partoprenis la Internacian
Seminarion (IS) (via lasta IS estis la {$renkNomo}
en {$renkLoko} dum la jars^ang^o {$jaroj}), sed vi g^is nun
ankorau^ ne alig^is por la c^ijara IS.
Detaloj por malmendi au^ s^ang^i la abonon estas je la fino.

La 50a IS okazos c^ijare en la junulargastejo de Wewelsburg, proksime
al Paderborn en mezokcidenta Germanujo.

La jubileo je la temo \"50 jaroj IS - c^u ankorau^ juna\" estos okazo
por reen rigardi al la porjunulara laboro de la Germana Esperanto-Junularo.
Iamaj estraranoj raportas pri la problemoj kaj defioj en la diversaj
etapoj de la asocio. Kiel kaj kial estis fondita GEJ post la dua
mondmilito kiel memstara asocio? Kiujn efikojn havis la postmilita
tempo, la influo de la studenta movado de la 60aj jaroj, la malvarma
milito, la reunuigo de Germanujo kaj la antau^enig^anta tutmondig^o al
la laboro de GEJ, ties politiko kaj liste laste al la membroj? Kio ilin
motivigas kaj akcelas?
Pri tiuj kaj aliaj demandoj ni volas ekscii pli kaj diskuti kun vi
dum unu semajno c^irkau^ silvestro.

Samtempe, la ejo mem ofertas la eblecon priesplori lau^ konkretaj signoj, 
spuroj kaj ekzemploj la fas^ismon en Germanujo, c^ar g^i estis uzita kiel 
nacisocialisma kultejo je la tempo de la Hitlera diktaturo. Tiu temo ne
nur en Germanio estas pli kaj pli grava rilate al la porjunulara laboro.
Ni esperas ricevi de la partoprenantoj valorajn kontribuojn, pensigajn
opiniojn kaj interesajn raportojn. Por pli bone ekscii pri la historio de
Wewelsburg, Mirjam tradukis la vikipedian artikolon pri g^i al Esperanto:
 http://eo.wikipedia.org/wiki/Wewelsburg

Krom la tema programo ankau^ estos distra kaj ric^ega vespera programo
kun la steluloj de la Esperantomuziko, kiel ekzemple Dolchamar, JoMo,
Martin Wiese kaj Esperanto Desperado.

C^ion pri la programo, la alveno, la kotizoj (kiuj ne s^ang^ig^is ekde la
lasta jaro, nur aldonig^is rabato por TEJO-membroj) vi povas trovi en
nov-aspektigita IS pag^aro http://www.internacia-seminario.de/.
Ankau^ trovig^as listo de alig^intoj kaj de la teamo.

Kompreneble tie ankau^ eblas alig^i (jam la unua pag^o de la alig^ilo
kalkulas la prezon) - kaj decidu tuj, c^ar la limdato por la unua
kategorio estas la fino de oktobro (g^is tiam alvenu ankau^ via
antau^pago).
Nova ebleco por antau^pagi estas nia nova PayPal-konto - tiel la mono
alvenas sen tempoprokrasto.

G^is nun (26a de oktobro, 0:41 lau^ mezeu^ropa tempo)
alig^is 109 partoprenantoj el 25 landoj - c^u baldau^
ankau^ vi?


Nome de KKRen (la IS-organiza teamo) salutas
Pau^lo Ebermann (vicadministranto)

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
      sendu_xxxxx_mesagxon("50a IS - limdato por unua kategorio: 31a de oktobro", $teksto,$to_name,$to_address, $kodigo);
      erareldono ("Messag^o sendita!");
}


$prafix = "../";
require_once ($prafix . "iloj/iloj.php");
session_start();

malfermu_datumaro();


$komenco = 900;
$nombro = 20;

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


    if ($row['renkID'] < 6)
        {
            // ne jam aligxis al la 2006a IS

            eoecho(', sendota)');
				flush();

            $to_name = $row['personanomo']." ".$row['nomo'];
            $to_address = $row['retposxto'];
            $to_address = "Paul.Ebermann@esperanto.de";
            // sendu_specialan_mesagxon($row,$to_name,$to_address);
        }
    else
        {
            eoecho(", ne necesas)");
        }
}

echo "<br/><a name='fino'>Fino</a>.<br/>\n";


?>