<?php

/*
 * Por dissendado de la adresaro.
 */


/**
 */
function sendu_adresaron($row,$savu,$to_name,$to_address,$bcc='')
{

  $nomo = eotransformado($row[personanomo], "utf-8");

  $teksto = <<<DATOFINO

Saluton kara {$nomo},

dankon pro via cxeesto dum la 51a Internacia Seminario
(aux "IS 2007/2008") en Würzburg.
Ni (la organiza teamo) ege gxuis la etoson kaj ni esperas,
ke vi sekvan jaron denove venos al IS, tiam en Biedenkopf
(vidu sube).

Kiel promesite, ni nun sendas la adresaron de cxiuj
partoprenantoj de la IS, kaj krome kelkajn pliajn informojn.

* Adresaro
* Enketo
* Trovitajxoj
* Fotoj + Filmo
* Sekva IS


 Adresaro
----------

La adresaron vi trovos en PDF-formato kiel aldonajxo
(ordigita laux persona nomo).
Se vi havas problemojn rilate al gxia uzo, bonvolu
sendi mesagxon al is.admin@esperanto.de.

Bonvolu ne pludoni la adresaron al homoj kiuj
ne cxeestis la IS-on en Würzburg (tiuj tamen versxajne
jam ricevis gxin, se ili aperas kun retadreso en la
adresaro). Cetere, vi povas ankaux peti gxin denove
de is.admin@esperanto.de.
Ankaux estas malpermesita uzi la adresaron por sendi
amasajn leterojn (cxu retposxte, cxu papere).
Tio inkluzivas varbadon por Esperanto-renkontigxoj
(kiel lastjare okazis)!


 Enketo
--------

Por plibonigi la Internacian Seminarion, ni nuntempe
preparas enketon pri gxi. Ni petas vin partopreni -
detaloj pri tio sekvos post kelkaj tagoj.

Vi taman rajtas jam nun sendi kritikon kaj lauxdon
al la IS-teamo, ekzemple al gej.kkren@esperanto.de.

[...]


 Trovitajxoj
-------------

Ni post la fino de la IS trairis la cxambrojn kaj
la memzorgantejon kaj kolektis multajn ajxojn, kiujn
iuj forgesis.

Bedauxrinde mankis spaco en la auxto, per kiu ni
reveturigis la aferojn el la oficejo kaj de la
libroservo, do ni kelkajn vestajxojn (kiuj sxajnis
malaltvaloraj) simple forjxetis.

Iuj aliaj ajxoj tamen alvenis en la oficejo (ekzemple
elektra kablo de klapkomputilo) - se vi ion perdis,
sendu retmesagxon al BerO@esperanto.de.



 Fotoj de IS
-------------

Elekton de 199 el la multegaj fotoj, kiujn vi lasis en la
IS-foto-komputilo, vi trovas nun cxe

    http://picasaweb.google.com/InternaciaSeminario/IS20078

La fotoj ankaux havas amuzajn komentojn elpensitajn de nia
IS-estro.

Ni baldaux trovos iun manieron enretigi cxiujn fotojn - vi
trovos la ligon en nia IS-retpagxo, same kiel anoncon en nia
partoprenanta dissendolisto:

    http://groups.yahoo.com/group/is-en-germanio/

Rolf ankaux kunmetis plurajn el la video-klipoj, kiujn vi lasis
en la Fotokomputilo, al unu filmo, nun trovebla je Youtube:

    http://www.youtube.com/watch?v=3gEj8V4dDOo


 Sekva IS
----------

La sekva, 52a, IS okazos de la 27a de decembro 2008
gxis la 3a de januaro 2009, en la Germana urbeto
Biedenkopf, en okcidenta Hesio (en meza Germanio,
norde de Frankfurt).

Ni dankas al tiuj, kiuj jam surloke aligxis. La
retpagxo por reta aligxo laux aktuala plano pretos
post kelkaj semajnoj.


En la nomo de la Organiza teamo (KKRen)
Pauxlo Ebermann (teknika respondeculo pri IS-administrado
                 kaj auxtoro de tiu teksto)



DATOFINO;

	  
      
      $dosierojn = array($GLOBALS['prafix'].'dosieroj_generitaj/adresaro.pdf'); // jen la necesaj dosieroj
      echo "Al: $to_address";
      sendu_dosier_mesagxon("Adresaro kaj pliaj informoj pri pasinta IS",
							$teksto,$to_name,$to_address,$dosierojn,$bcc);
      erareldono (" Messag^o sendita! ");

}


$prafix = "../";
require_once ("../iloj/iloj.php");
session_start();

malfermu_datumaro();

kontrolu_rajton('retumi');

HtmlKapo();

// die("Vi ne rajtas uzi tiun dosieron. Se vi ne scias, kial, demandu Pauxlon.");

$komenco = 0;
$nombro = 1;



$demando = datumbazdemando(array("p.ID", "nomo", "personanomo", "retposxto", "sekso",
								 "pn.agxo" ),
						   array("partoprenantoj" => "p", "partoprenoj" => "pn"),
						   array("pn.partoprenantoID = p.ID",
								 "retposxto <> ''",
								 "alvenstato = 'a'",
								 "renkontigxoID = '7'", // IS 2007/2008
								 ),
						   "",
						   array("limit" => "$komenco, $nombro",
								 "order" => "pn.ID ASC")
						   );
						   
  echo "Demando: [" . $demando . "]<br/>\n";

  echo "dato:" . date("Y-m-d H:i:s") . "<br/>\n";
						   
						   $rezulto = sql_faru($demando);

$i = $komenco;

while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
{
  eoecho($i . " " . $row[personanomo]." ".$row[nomo]."<br/>\n");  
  $i++;

  //  $to_name = funkciulo("admin");
  //  $to_address = funkciuladreso("admin");
  $to_name = $row[personanomo]." ".$row[nomo];
    
//  echo "\nDas w〓 geworden!!\n";
$to_address = "Paul-Ebermann@gmx.de";
//$to_address = $row['retposxto'];
  $bcc = "";
    sendu_adresaron($row,$savu,$to_name,$to_address,$bcc);

flush();
usleep(500);
}

echo "Fino.<br/>\n";
echo "dato:" . date("Y-m-d H:i:s") . "<br/>\n";

HtmlFino();

?>