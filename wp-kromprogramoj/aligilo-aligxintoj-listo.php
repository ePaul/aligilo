<?php 
  /*
Plugin Name: Aligilo: Listo de aliĝintoj
Description: Parto de la renkontiĝo-aligilo, tiu programeto montras la liston de aliĝintoj (tiuj kun alvenstato 'v').
Author: Paul Ebermann
   */


  // TODO!: La nombroj estas kalkulitaj kun "alvenstato = 'v'", la
  //        listo kun "alvenstato != 'm'". Tio ne estas la sama,
  //        cxefe post la renkontigxo. Decidu por unu kaj uzu tion cxie.


function aligilo_aligxintoj_listo_filtro($enhavo) {
    echo "<!-- aligilo_aligxintoj_listo_filtro() -->";
    $regex = '%\A(.*?)<aligilo:aligxintoj-listo(?:\s+renkontigxo="(\d+)")?(?:\s+lingvo="([a-z]+)")?(?:\s+ordigo="([a-z]+)")\s*/>(.*)\z%s';

    $rezulto = "";
    while (true) {
        $rrez = array();
        $i = preg_match($regex, $enhavo, $rrez);
        if ($i) {
            $rezulto .= $rrez[1];

            $renkNum = $rrez[2];
            $lingvo = $rrez[3];
            $ordigo = $rrez[4];
            $resto = $rrez[5];
            
            $rezulto .= "<tfoot><tr><td colspan='4'> renkontiĝo: " . $renkNum .
                ", lingvo: " . $lingvo . ", ordigo: " . $ordigo .
                "</td></tr></tfoot>\n";

            $datumoj = aligilo_aligxinto_listo((int)$renkNum,
                                               $lingvo,
                                               $ordigo);

            $rezulto .= $datumoj['tabelo'];

            $enhavo = $resto;
        }
        else
            return $rezulto . "<!-- ne plu trovita -->" . $enhavo;
    }


}


add_filter('the_content', 'aligilo_aligxintoj_listo_filtro');



function aligilo_aligxinto_listo($renkontigxoID, $ordigo, $lingvo) { 

    $GLOBALS['prafix'] = get_option("aligilo-prafix");

    require_once($GLOBALS['prafix'] . "/iloj/iloj.php");
    malfermu_datumaro();
    
$sql_listo = datumbazdemando(array("COUNT(pn.ID)" => "nombro"),
					   array("partoprenantoj" => "p", "partoprenoj" => "pn"),
					   array("p.ID = pn.partoprenantoID",
							 "pn.alvenstato = 'v'",
							 "pn.listo = 'J'",
                             "pn.renkontigxoID ='$renkontigxoID'",
                             )
					   );
$rez_listo = sql_faru($sql_listo);
$linio = mysql_fetch_assoc($rez_listo);
$nombro_listo = $linio['nombro'];

$sql_listo = datumbazdemando(array("COUNT(pn.ID)" => "nombro"),
                             array("partoprenantoj" => "p",
                                   "partoprenoj" => "pn"),
                             array("p.ID = pn.partoprenantoID",
                                   "pn.alvenstato = 'v'",
                                   "pn.renkontigxoID ='$renkontigxoID'"
                                   )
                             );
$rez_listo = sql_faru($sql_listo);
$linio = mysql_fetch_assoc($rez_listo);
$nombro_chiuj = $linio['nombro'];

$sql_landoj = datumbazdemando(array("COUNT(distinct p.lando)" => "nombro"),
                             array("partoprenantoj" => "p",
                                   "partoprenoj" => "pn"),
                             array("p.ID = pn.partoprenantoID",
                                   "pn.alvenstato = 'v'",
                                   "pn.renkontigxoID ='$renkontigxoID'"
                                   )
                             );
echo "<!-- $sql_landoj -->";
$rez_listo = sql_faru($sql_landoj);
$linio = mysql_fetch_assoc($rez_listo);
$nombro_landoj = $linio['nombro'];




$sql = datumbazdemando(array("IF(p.sxildnomo<> '', p.sxildnomo, p.personanomo)" => 'persona',
                             "p.nomo" => 'fam',
							 "p.urbo" => 'urbo', "p.sxildlando" => 'sxildo',
							 "l.nomo" => 'lando_eo', "l.lokanomo" => 'lando_de'),
					   array("partoprenantoj" => "p", "partoprenoj" => "pn",
							 "landoj" => "l"),
					   array("p.ID = pn.partoprenantoID",
							 "alvenstato <> 'm'",
							 "p.lando = l.ID",
							 "pn.listo = 'J'",
                             "pn.renkontigxoID ='$renkontigxoID'"
                             ),
					   "",
					   array("order" => "p.personanomo ASC, p.nomo ASC")
					   );

 $tabelo = "<tbody>";
 $rez = sql_faru($sql);
 while($linio = mysql_fetch_array($rez))
     {
         $tabelo .= "<tr>\n";
         $tabelo .= "  <td  style='text-align: right; padding-right:0.3em;'>";
         $tabelo .= uni( $linio['persona'] );
         $tabelo .= "</td>\n<td>";
         if ($linio['fam']{1} == '^')
             {
                 $fam = substr($linio['fam'], 0,2);
             }
         else
             {
                 $fam = mb_substr($linio['fam'], 0, 1, "utf-8");
             }
         $tabelo .=  uni( $fam) . ".";
         $tabelo .= "</td>\n  <td>";
         if ($linio['sxildo'])
             {
                 $tabelo .=  uni($linio['sxildo']);
             }
         else
             {
                 if ($lingvo == 'de') {
                     $tabelo .= uni($linio['lando_de']);
                 }
                 else {
                     $tabelo .= uni($linio['lando_eo']);
                 }
             }
         $tabelo .= "</td>\n  <td>";
         $tabelo .= uni($linio['urbo']);
         $tabelo .= "</td>\n</tr>\n";
     }
 $tabelo .= "</tbody>\n";

 return compact("tabelo", "nombro_landoj", "nombro_listo", "nombro_chiuj");

}


  
?>