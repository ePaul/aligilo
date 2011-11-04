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
Kara $nomo,
 
ni esperas, ke vi bone alvenis hejmen post la IS. Per tiu mesagxo venos
kelkaj informoj por vi:

(1) Trovitajxoj
(2) IS-Enketo
(3) Adresaro

(1) Trovitajxoj
   -------------

Kiel cxiujare ni dum la ordigado post la IS trovis plurajn aferojn -
plejparte vestaxjojn, sed ankaux aliajn aferojn.
Se vi ion perdis, bonvolu sendi mesagxon al la Berlina Oficejo (BerO)
de GEJ (bero @ esperanto.de) - ni rigardos, cxu gxi estas inter la
trovitajxoj, kaj se jes, povas sendi al vi (vi pagos la sendokostojn).

Inverse: Se vi trovis ion, kiu ne apartenas al vi, sendu mesagxon al la
BerO - eble iu alia jam sercxas gxin. (Se vi jam scias la posedanton,
bonvolu rekte kontaktu lin - lia adreso ja troveblas en la listo.)

Ni planas kunporti kaj disauxkcii la restajn trovitajxojn dum iu estonta
IS, kiam ni havos suficxe da spaco en la auxto.


(2) IS-Enketo
   -----------
La organiza teamo rimarkis, ke cxi-jare ne cxiu funkciis glate. Ni nun
volas ekscii, kiom la partoprenantoj rimarkis ;-) Pli serioze: Ni volas
plibonigi la organizadon. Tial ni petas vin partopreni la sekvan
enketon. Ni lotumos inter tiuj partoprenantoj de la enketo, kiuj
respondis gxis la 15a de februaro, antauxpagon por la sekva IS (en
valoro de 30 Euro).
Ni ne uzos viajn datojn krom por statistiko, do sen persona rilato. La
nomojn unu fidebla persono forigos de la datoj. (Se vi preferas, vi
rajtas ankaux tute anonime respondi (uzu nekonatan retadreson) - sed
tiel ne eblas partopreni la lotumadon.)

Bonvolu plenigi la sekvan formularon (per krucoj "X", kie tauxgas, aux
per teksto anstataux la _______ - vi rajtas ankaux skribi pli ol la
linio estas longa) kaj resendu gxin al ...
(Bonvolu forigi la ceteron de la mesagxo, se vi simple respondos.)

-8X ------------------------------------------------------------------

(X) Jes, mi volas partopreni la lotumadon.
  (Forprenu la X, se vi ne volas.)


DATOFINO;

if ($row['sekso'] == 'i')
	 $teksto .= "Mi estas: (X) ina ( ) malina \n";
	 else
	 $teksto .= "Mi estas: ( ) ina (X) malina \n";
$teksto .= "Mi apartenas al la agxgrupo (agxo je komenco de IS):\n";

$agxo1 = $agxo2 = $agxo3 = $agxo4 = $agxo5 = ' ';
if ($row['agxo'] <= 17)
	 $agxo1 = 'X';
 else if ($row['agxo'] <= 21)
	 $agxo2 = 'X';
 else if ($row['agxo'] <= 26)
	 $agxo3 = 'X';
 else if ($row['agxo'] <= 35)
	 $agxo4 = 'X';
 else
	 $agxo5 = 'X';

$teksto .= "  ($agxo1) sub 17, ($agxo2) 18-21, ($agxo3) 22-26,\n" .
"  ($agxo4) 27-35, ($agxo5) super 36\n";

$teksto .= <<<DATOFINO

 Cxu vi partoprenis unuafoje? ( ) jes ( ) ne 
  se ne: 
  Kio estis gxis nun via plej sxatata IS? _________
  Kial? ________________
 Kion programeron vi plej sxatas en la IS? _________
  Kial?________________ 
 Kion programeron/okazajxon vi plej malsxatas en la IS? ________
  Kial?________________
 
 Donu notojn por la sekvaj programpunktoj de la IS en Wetzlar,
 kie 1 estas la plej bona, 5 la plej malbona. (Lasu malplena,
 se vi ne scias/ne partoprenis).
 
 Taga programo
  entute:                           [  ] (1-5)
  Tema programo                     [  ] (1-5)
    speciale bone estis:    __________
    speciale malbone estis: __________
  Distra programo                   [  ] (1-5)
    speciale bone estis:    __________
    speciale malbone estis: __________
  Movada programo                   [  ] (1-5)
    speciale bone estis:    __________
    speciale malbone estis: __________
  Cxu vi rimarkis la diferencon
  inter tiuj tri partoj?  ( ) jes ( ) ne
 Urborigardado                      [  ] (1-5)
 Nagxpostagmezo                     [  ] (1-5)
 Vespera programo
  entute:                           [  ] (1-5)
  interkona vespero:                [  ] (1-5)
  koncerto de Kadakar:              [  ] (1-5)
  koncerto de Esperanto Desperado:  [  ] (1-5)
  teatrajxo:                        [  ] (1-5)
  koncerto de La Rolls:             [  ] (1-5)
  novjara koncerto:                 [  ] (1-5)
  internacia vespero:               [  ] (1-5)
 Tuttaga ekskurso (al Marburg)      [  ] (1-5)
 Silvestra bufedo                   [  ] (1-5)
 Silvestra balo                     [  ] (1-5)
 Novjara promenado                  [  ] (1-5)
 IS-lumdisko                        [  ] (1-5)
 Nokta programo
  entute:                           [  ] (1-5)
  Diskejo:                          [  ] (1-5)
  Gufujo:                           [  ] (1-5)
  Trinkejo:                         [  ] (1-5)
  Nokta universitato                [  ] (1-5)
    speciale bone estis:    __________
    speciale malbone estis: __________
  Nokta filmo                       [  ] (1-5)
    speciale bone estis:    __________
    speciale malbone estis: __________
 Libroservo                         [  ] (1-5)
 Adresaro                           [  ] (1-5)

 Bonvolu nun NE rigardu en la brosxuron aux en la retpagxon!

 Cxu vi scias la temon de la IS? ( ) jes ( ) ne
  Se jes: Kio estis?  _____________

 Cxu vi nun intencas veni denove al IS? ( ) jes ( ) ne
 Cxu vi rekomendos al amikaj esperantistoj
  veni al IS?  ( ) jes ( ) ne

  Se ne, kio estis la cxefa kauxzo? ________________

 Kion vi proponas por plibonigi la ISon?
  ____________________
 Kio estis nenecesa? ____________________
 Kion vi proponas al ni, por venigi pli da homoj (el
  via lando)?  __________________
 Pliaj aldonoj: __________________________

-8X ------------------------------------------------------------------

(3) Adresaro
   ----------

La IS-adresaro trovigxas en PDF-formato en la aldono. Bonvolu atenti la
regulojn pri la uzo, kiuj trovigxas komence de gxi. Se vi perdis la
adresaron, vi povas mendi novan cxe 

Mi cxi-foje uzis diversajn kolorojn por plifaciligi la distingon
inter la unuopaj personoj - tio estas fakte eksperimento. Se vi
ne sxatas, marku gxin tiel en la supra enketo.


Kore salutas
nome de KKRen (Konstanta Komisiono pri Renkontigxoj de GEJ)
Pauxlo Ebermann

DATOFINO;

	  
      
      $dosierojn = array('adresaro.pdf'); // jen la necesaj dosieroj
      echo "Al: $to_address";
      sendu_dosier_mesagxon("Adresaro kaj pliaj informoj pri pasinta IS",$teksto,$to_name,$to_address,$dosierojn,$bcc);
      erareldono ("Messag^o sendata!");

}


require_once ("iloj.php");
session_start();

malfermu_datumaro();

die("Vi ne rajtas uzi tiun dosieron. Se vi ne scias, kial, demandu Pauxlon.");

$komenco = 219;


$numero = 20;

$demando = datumbazdemando(array("p.ID", "nomo", "personanomo", "retposxto", "sekso",
  "pn.agxo" ),
  array("partoprenantoj" => "p", "partoprenoj" => "pn"),
			array("pn.partoprenantoID = p.ID",
  "retposxto <> ''",
			  "alvenstato = 'a'",
			  "renkontigxoID = '4'",
			  ),
  "",
  array("limit" => "$komenco, $numero",
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

  //  $to_name = funkciulo("admin");
  //  $to_address = funkciuladreso("admin");
  $to_name = $row[personanomo]." ".$row[nomo];
    
  echo "\nDas wärs geworden!!\n";
// $to_name=$row[personanomo]." ".$row[nomo];  // TODO: einkommentieren, wenn es losgehen soll && die Texte abgesegnet sind
$to_address = $row[retposxto];
        $bcc = "";
  sendu_adresaron($row,$savu,$to_name,$to_address,$bcc);

}

echo "Fino.<br/>\n";


?>