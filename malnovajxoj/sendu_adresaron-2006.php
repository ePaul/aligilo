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

dankon pro via cxeesto dum la 49a Internacia
Seminario (aux "IS 2005/2006").
Ni (la organiza teamo) ege gxuis la etoson,
kaj ni esperas, ke vi sekvan jaron denove
venos al IS en Germanio.

Kiel promesite, ni nun sendas la adresaron
de cxiuj partoprenantoj de la IS, kaj krome
kelkajn pliajn informojn.

* Adresaro
* Trovitajxoj
* Fotoj
* Filmoj
* Kritikoj/Lauxdoj
* Sekva IS

 Adresaro
----------

La adresaron vi trovos en PDF-formato kiel
aldonajxo (ordigita laux persona nomo).
Se vi havas problemojn rilate al gxia uzo,
bonvolu sendi mesagxon al ...

Bonvolu ne pludoni la adresaron al homoj kiuj
ne cxeestis la IS-on en Xanten (tiuj tamen versxajne
jam ricevis gxin, se ili aperas kun retadreso en la
adresaro). Cetere, vi povas ankaux peti gxin denove
de ...

 Trovitajxoj
-------------

Ni post la fino de la IS trairis la cxambrojn
kaj kolektis multajn ajxojn, kiujn iu forgesis.
Tiujn ni (fakte Martin kaj Pauxlo en la malgranda
auxto de Martin) transportis al la Berlina Oficejo
(BerO) de GEJ, kie ili nun stokigxas en la kelo.

La Berlinaj Studentoj-Esperantistoj (BSE) estis tiom
afablaj krei liston de la trovitajxoj dum ilia unua
post-IS-a kunveno:

* nigra trikita pulovro
* trikita jako, malhelblua, blankaj strioj, grandeco L, kun zipo.
* esperanto-T-cxemizo
* nigra trikita pulovro, rugxaj, verdaj kaj grizaj strioj
* grizblua pantalono, grandeco L
* nigra cxapo
* paro de vinrugxaj gantoj
* paro de rozkoloraj "Thinsulate"-gantoj
* blanka XL-T-cxemizo, initialoj SWR.
* nigra T-cxemizo, "liberte pour Leonard Peltier ..."
* blanka mantuko (rozoj)
* blanka sxnurego (plastika)
* paro da rugxvinkoloraj, virinaj sxuoj,  grandeco 40.
* nigra trikita jako, kun zipo, grandeco L (malodoras).
* blua fliza pulovero, grandeco M.
* nigra "Timberland"-sako
* dormsako ("Eurohiker", nigra)
* trinkbotelo en izola sako de "Adventuridge"
* kablo USB/FireWire (helgriza)
* kuverto "Stephanie", kun fotoj de infanoj kaj letero
* izolmatraco (blua, ege largxa)
* kravato kun strioj violetaj/bluaj
* trigamba segxo (el fero kaj ligno, tre peza)

Se io el tio estas via (kaj vi volas rehavi gxin),
bonvolu sendi mesagxon al ...
Kontraux pago de la sendokostoj ni povas sendi gxin
al vi.

Restis krome kelkaj foto-lumdiskoj - tiujn vi
povas acxeti kontraux 2 Euxroj + sendokostoj.


 Fotoj de IS
-------------

Pluraj fotoj de la IS (tiuj, kiuj estas ankaux sur
la lumdisko, sed en iomete pli alta kvalito - pro
spacmanko sur la lumdisko) haveblas je
    http://bildoj.esperanto.de/49-a_IS/

Fotoj de Lukazs estas troveblaj cxi tie:
    http://kalmar.one.pl/esperanto/fotoj/IS-2005-06

Ivo Miesen prilaboris siajn fotojn (kaj faris elekton)
kaj la rezulto troveblas (same kiel fotoj de aliaj
arangxoj) cxi tie:
    http://fotoalbum.dds.nl/ivo_m

Fotoj de Sebastian Kirf trovigxas cxe
    http://fotoj.kirf.de/thumbnails.php?album=9


 Filmoj
--------

Dum kelkaj tagoj okazis seminarieto kun profesia
filmfaristo por krei varbfilmojn pri Esperanto.
La rezultojn (du filmoj de po ~ 45 sekundoj, en
Esperanto, kun germanaj subtekstoj) ni metis al
nia servilo kaj alligis cxe
http://www.esperanto.de/is/eo/2005/index.


Kelkaj filmoj de la partoprenantoj troveblas cxe
    http://bildoj.esperanto.de/49-a_IS-filmoj/


 Kritikoj/Lauxdoj
------------------

Ni kunportis la liston de la lauxdoj kaj kritikoj,
kiuj pendis dum IS en la enirhalo, kaj analizos gxin.

Se vi havas kritikon aux lauxdon, kiun vi forgesis
skribi sur tiun folion, vi povas sendi gxin al
... - ni certe pridiskutos gxin
ene de KKRen. (Bonvolu menciu "kritiko" aux "lauxdo"
en la kaplinio, por ke la spamfiltrilo nur kaptu la
kritikojn ;-) [1])


 Sekva IS
----------

Ni bedauxrinde ankoraux ne scias, kie ekzakte okazos
la sekva IS, sed certe gxi okazos en Germanio kaj de
la 27a de decembro 2006 gxis la 3a de januaro 2007.
Gxi estos la 50a, kaj gxi estu aparte bona!

KKRen (la IS-teamo) nun esploras eblajn ejojn
(kun malpli fora memzorgantejo!), diskutas la
temon kaj la plano por la vespera programo jam
nun estas preskaux preta.

Se vi volas cxiam esti informita pri la plej novaj
aktualajxoj, vi aligxu (se vi ankoraux ne faris) al
la dissendolisto is-en-germanio,
http://groups.yahoo.com/group/is-en-germanio.


Ankorauxfoje dankon pro via cxeesto
kaj gxis sekva IS

En la nomo de la Organiza teamo (KKRen)
Pauxlo Ebermann (teknika respondeculo pri IS-administrado
                 kaj auxtoro de tiu teksto)

[1] Ne, nia spamoflitrilo tute ne distingas inter
   kritiko kaj lauxdo, tio estis sxerco.

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

die("Vi ne rajtas uzi tiun dosieron. Se vi ne scias, kial, demandu Pauxlon.");

$komenco = 160;


$numero = 20;

$demando = datumbazdemando(array("p.ID", "nomo", "personanomo", "retposxto", "sekso",
								 "pn.agxo" ),
						   array("partoprenantoj" => "p", "partoprenoj" => "pn"),
						   array("pn.partoprenantoID = p.ID",
								 "retposxto <> ''",
								 "alvenstato = 'a'",
								 "renkontigxoID = '5'", // IS 2005/2006
								 ),
						   "",
						   array("limit" => "$komenco, $numero",
								 "order" => "pn.ID ASC")
						   );
						   
  echo "Demando: [" . $demando . "]<br/>\n";
						   
						   $rezulto = sql_faru($demando);

$i = $komenco;

while ($row = mysql_fetch_array($rezulto,MYSQL_BOTH))
{
  eoecho($i . " " . $row[personanomo]." ".$row[nomo]."<br/>\n");  
  $i++;

  //  $to_name = funkciulo("admin");
  //  $to_address = funkciuladreso("admin");
  $to_name = $row[personanomo]." ".$row[nomo];
    
//  echo "\nDas wärs geworden!!\n";
$to_address = $row[retposxto];
  $bcc = "";
  sendu_adresaron($row,$savu,$to_name,$to_address,$bcc);

flush();
usleep(500);
}

echo "Fino.<br/>\n";


?>