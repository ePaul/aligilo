<?php

  /**
   * Diversaj serĉfunkcioj vokataj el aliaj paĝoj.
   *
   * Per $_REQUEST['elekto'] oni elektas, kiun serĉon oni volas.
   * - <samp>nenula_saldo</samp>:
   *     listo de tiuj alvenintoj, kies kotizo-pago-saldo ne estas
   *      nulo (= inter -1 kaj +1), do kiuj estas ankoraŭ prilaborendaj.
   * - <samp>germanoj_laux_lando</samp>:
   *     listo de ĉiuj homoj el Germanio, ordigita laŭ provinco
   *     (federacieroj). Uzebla por organizi kunvenojn de landaj organizoj.
   * - <samp>cxambrolisto</samp>
   *     listas ĉiujn partoprenantojn, kiuj estas jam en ĉambro, kun
   *      ĉambronomo kaj noktoj, en kiuj oni estas tie.
   * - <samp>junulargastejolisto</samp>
   *     listo de partoprenantoj en CSV, en formato por uzo, kiun petis
   *     la junulargastejo en Wetzlar.
   * - <samp>francoj</samp>
   *     listo de la francaj partoprenantoj en CSV, por organizado de karavano.
   * - <samp>laborontajnotoj</samp>:
   *     montras notojn, neprilaboritajn notojn, aŭ
   *     nur tiujn, kies remontro-dato jam pasis.
   *     Kion precize, tion decidas
   *     $_REQUEST['montro'].
   * - <samp>notoj_de_entajpanto</samp>
   *      listoj de notoj por, pri kaj de iu
   *      entajpanto. Per $_REQUEST['entajpantoid'] ni
   *      informiĝas, kies notojn montri, per
   *      $_REQUEST['montro'] decidiĝas, kiun parton
   *      de la notoj montri. (Estu unu el la valoroj
   *      akceptitaj de {@link kreu_NotoTabelilon()}
   *      ($aktualTipo).)
   * - <samp>notojn</samp>
   *     listo de la notoj de unuopa partoprenanto, elektata
   *      per <samp>$_REQUEST['partoprenantoidento']</samp>
   * - <samp>kunmangxo</samp>
   *     listas ĉiujn partoprenantojn de la aktuala renkontiĝo, kie domotipo
   *     ne kongruas kun kunmanĝo.
   * - <samp>venantoj</samp>
   *     montras ĉiujn partoprenantojn de aktuala renkontiĝo, ordigita laŭ
   *     persona nomo aŭ $ordo (se donita).
   * - <samp>profesioj</samp>
   *     montras liston de tiuj partoprenantoj, kiuj donis iun ne-nulan
   *     "profesio"-informon. (Ni ne plu demandas tiun informon dum la lastaj
   *     jaroj, do ne tro utilas nun.)
   * - <samp>cxambrodisdonado</samp>
   *     montras ĉiujn partoprenantojn, kiuj mendis Junulargastejon,
   *     kun iliaj ĉambro-deziroj. Se $AB == "nur", montras nur A/B-landanojn
   *     kun antaŭpago (por trakti tiujn unue, ekzemple).
   * - <samp>skribuagxon</samp>
   *     rekalkulas la aĝojn de ĉiuj partoprenantoj.
   *     Uzenda, kiam la komenco-dato de renkontiĝo ŝanĝiĝis.
   * - <samp>kunlogxantoj</samp>
   *     listo de ĉiuj partoprenantoj, kiuj havis kunloĝo-deziron.
   *     Enkonstruita estas formularo por ligi la homojn al la korespondaj
   *     personoj.
   * - <samp>restaspagenda</samp>
   *     (ne plu funkcias)
   * - <samp>pliaj</samp>
   *    serĉo pri partoprendetaloj, koresponda al la serĉformularo
   *    en partsercxo.php.
   * - <samp>antauxpagoj</samp>
   *    kreas liston de ĉiuj (antaŭ)pagoj, kaj sumojn laŭ antaŭpagotipo.
   * - <samp>rabatoj</samp>
   *    listo de ĉiuj rabatoj, kaj sumoj laŭ rabato-tipo.
   * - <samp>rimarkoj</samp>
   *    listo de tiuj partoprenantoj (de aktuala renkontiĝo), kiuj
   *    donis rimarkon dum la aliĝo.
   * - <samp>kotizokomparo</samp>
   *    komparo de la kotizo-kalkuladoj laŭ nova kaj malnova
   *    kotizosistemo/kalkulilo. Nun ne plu funkcias, pro forigo de
   *    la malnova.
   * - <samp>memligo</samp>
   *    ripeto de antaŭa serĉo (el <samp>$_SESSION['memligo'][$id]</samp>)
   *    kun alia ordigo (<samp>$orderby, $asc</samp>).
   *   
   * @uses sercxu()
   * @uses Sercxilo
   * @package aligilo
   * @subpackage pagxoj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   */

  // define('DEBUG', true);

/**
 *
 * la kutimaj iloj.
 */
require_once ("iloj/iloj.php");
session_start();
malfermu_datumaro();
 
 
// $elekto estas identigilo de la elektata sercx-ago.

$elekto = $_REQUEST['elekto'];


if ($elekto=="laborontajnotoj") 
     { 
         $sercxilo = kreu_NotoTabelilon("listo_notoj_cxiuj",
                                        true, $_REQUEST['montro']);
         if ($_REQUEST['montro'] == 'cxiuj') {
             $teksto = "C^iuj notoj";
         } else {
             $teksto = "C^iuj " . $_REQUEST['montro'] . " notoj";
         }

         $sercxilo->metu_antauxtekston($teksto);

         $sercxilo->montru_rezulton_en_HTMLdokumento();

     }
 else if ('nenula_saldo' == $elekto)
     {
         $sql = datumbazdemando(array('p.ID', 'p.partoprenantoID', 'pn.nomo', 'pn.personanomo'),
                                array('partoprenoj' => 'p', 'partoprenantoj' => 'pn' ),
                                array("p.alvenstato = 'a'",
                                      'pn.ID = p.partoprenantoID'),
                                "renkontigxoID",
                                array("order" => "pn.personanomo, pn.nomo")
                                );
         $rez = sql_faru($sql);
         $renkontigxo = $_SESSION['renkontigxo'];
         $kotsistemo = new Kotizosistemo($renkontigxo->datoj['kotizosistemo']);
         HtmlKapo();
         eoecho("<h1>Ne-nulaj saldoj</h1>
     <p>Jen listo de c^iuj partoprenintoj de aktuala IS, kies pago-kotizo-saldo
     estas ne-nula (t.e. <code>|x| &ge; 1 &euro;</code>).</p>");
         $sumo_pos = 0;
         $sumo_neg = 0;
         while($linio = mysql_fetch_assoc($rez))
             {
                 $prenanto = new Partoprenanto($linio['partoprenantoID']);
                 $preno = new Partopreno($linio['ID']);
                 $kot = new Kotizokalkulilo($prenanto, $preno, $renkontigxo, $kotsistemo);
                 $enda = $kot->restas_pagenda();
                 if (abs($enda) >= 1.0)
                     {
                         ligu ("partrezultoj.php?partoprenantoidento=".$linio['partoprenantoID'] .
                               "&partoprenidento=" . $linio['ID'] . "&montrukotizo=montru",
                               $prenanto->datoj['personanomo'] . " " . $prenanto->datoj['nomo']);
                         eoecho (" pagis: " .($kot->pagoj).", kotizo: "
                                 . $kot->partakotizo . ", ");
                         if ($enda > 0)
                             {
                                 echo "restas pagenda: <span style='color: red;'>" . $enda . "</span><br/>\n";
                                 $sumo_pos += $enda;
                             }
                         else
                             {
                                 echo "repagenda: <span style='color: darkgreen;'>" . (- $enda ) .
                                     "</span><br/>\n";
                                 $sumo_neg += (- $enda);
                             }
                     }
             }
         echo "<p>Entute restas pagenda <span style='color: red;'>$sumo_pos</span>, restas repagenda <span style='color: darkgreen;'>$sumo_neg</span></p>";
         HtmlFino();
         exit();
     }
 else if ('germanoj_laux_lando' == $elekto)
     {
         $sql = datumbazdemando(array('pn.ID','personanomo', 'nomo', 'provinco', 'urbo', 'posxtkodo'),
                                array('partoprenoj' => 'p', 'partoprenantoj' => 'pn'),
                                array('pn.ID = p.partoprenantoID',
                                      'pn.lando = 16', /* Germanio */
                                      'p.agxo < 27'
                                      ),
                                "renkontigxoID"
                                );
         sercxu_objekte($sql,
                        array("provinco", "ASC"),
                        array(array('ID','','->','z','partrezultoj.php?partoprenantoidento=XXXXX','0'),
                              array("personanomo", "persona_nomo", "XXXXX", "", "", ""),
                              array("nomo", "famila_nomo", "XXXXX", "", "", ""),
                              array("provinco", "provinco", "XXXXX", "", "", ""),
                              array("urbo", "loko", "XXXXX", "", "", ""),
                              array("posxtkodo", "pos^tkodo", "XXXXX", "", "", ""),
                              ),
                        array(),
                        "germanoj_laux_lando",
                        "", $_REQUEST['dosiertipo'],
                        "germanoj sub 27, lau^ lando, kun provincoj",
                        "germanoj sub 27");
     }
 else if ('cxambrolisto' == $elekto)
     {

         $sql = datumbazdemando(array('personanomo', 'pn.nomo' => 'nomo', 'c.nomo' => 'cxambro',
                                      'l.nokto_de', 'l.nokto_gxis', ),
                                array('partoprenoj' => 'p', 'cxambroj' => 'c', 'litonoktoj' => 'l',
                                      'partoprenantoj' => 'pn',),
                                array('pn.ID = p.partoprenantoID',
                                      'p.ID = l.partopreno',
                                      'l.cxambro = c.ID',
                                      "rezervtipo = 'd'",
                                      ),
                                "renkontigxoID"
                                );
         sercxu($sql,
                array("personanomo", "asc"),
                array(array("personanomo", "persona_nomo", "XXXXX", "", "", ""),
                      array("nomo", "famila_nomo", "XXXXX", "", "", ""),
                      array("cxambro", "c^ambro", "XXXXX", "", "", ""),
                      array("nokto_de", "de", "XXXXX", "", "", ""),
                      array("nokto_gxis", "gxis", "XXXXX", "", "", ""),
                      ),
                array(),
                "cxambrolisto",
                "", 0 /* CSV por elŝuti */, "Homoj kun c^ambroj", "homoj kun c^ambroj", 'jes');
     }
 else if ("junulargastejolisto" == $elekto)
     {
         // Sonderanfertigung für Jugendherberge Wetzlar


         $sql = datumbazdemando(array('personanomo', 'pa.nomo' => 'nomo', 'po.agxo', 'domotipo',
                                      "KKRen", 'vegetare', 'alvenstato',
                                      "sxildlando", 'l.nomo' => "landonomo"),
                                array("partoprenoj" => "po", "partoprenantoj" => "pa",
                                      "landoj" => "l"),
                                array("po.partoprenantoID = pa.ID", "po.alvenstato <> 'm'",
                                      "pa.lando = l.ID"),
                                "renkontigxoID",
                                array("order" => "personanomo ASC")
                                );
         $rez = sql_faru($sql);
  
         header("Content-Type: application/octet-stream"); //csv als Download anbieten 
         header('Content-Disposition: attachment; filename="teilnehmer.csv"');
  
         echo "Vorname;Nachname;Alter;Wohnort;Vegetarier;Essen;Land;KKRen;\n";

         while($linio = mysql_fetch_assoc($rez))
             {
                 if ($linio['domotipo'] == "M")
                     {
                         $mangxo = "-";
                     }
                 else if ($linio['vegetare'] == 'J')
                     {
                         $mangxo = 'V';
                     }
                 else
                     {
                         $mangxo = 'F';
                     }

                 if ($linio['sxildlando'])
                     {
                         $lando = $linio['sxildlando'];
                     }
                 else
                     {
                         $lando = $linio['landonomo'];
                     }

                 if ($linio['KKRen'] == 'J')
                     {
                         $kkren = 'J';
                     }
                 else
                     {
                         $kkren = 'N';
                     }

                 echo eotransformado($linio['personanomo'] . ";" . $linio['nomo']. ";" . $linio['agxo'] .
                                     ";" . $linio['domotipo']. ";" . $linio['vegetare'] . ";" .
                                     $mangxo . ";".  $lando .";" .$kkren. ';'. $linio['alvenstato'] .";\n" , "utf-8");
             }


     }
 else if ("francoj" == $elekto)
     {
         // SELECT pa.personanomo, pa.nomo, pa.retposxto FROM `is_partoprenoj` as po, is_partoprenantoj as pa  WHERE (po.partoprenantoID = pa.ID) and (pa.lando = 15) and (po.renkontigxoID = 4)

         $sql = datumbazdemando(array("personanomo", "nomo", "retposxto"),
                                array("partoprenoj" => "po", "partoprenantoj" => "pa"),
                                array("po.partoprenantoID = pa.ID", "pa.lando = 15"),
                                "renkontigxoID");
         sercxu($sql,
                array("personanomo", "ASC"),
                array(array("personanomo", "persona_nomo", "XXXXX", "", "", ""),
                      array("nomo", "famila_nomo", "XXXXX", "", "", ""),
                      array("retposxto", "retadreso", "XXXXX", "", "", ""),
                      ),
                array(),
                "francoj_is_2004", "", 2 /* CSV por elŝuti */, "", "", "");
     }
 else if ($elekto=="notojn")  
     {
         //
         // vokata de la listo en la menuo (per route.php), kaj ankaŭ 
         // rekte de iuj lokoj.


         $vortext = "Montras c^iun noton de partoprenanto " .
             donu_ligon("partrezultoj.php?partoprenantoidento=" . $partoprenantoidento,
                        "#" . $partoprenantoidento) . ".</p>\n".
             "<p>" . donu_ligon('notoj.php?elekto=bla&partoprenantoidento=' .$partoprenantoidento,
                                "Kreu novan noton!"); 

         listu_notojn($partoprenantoidento, $vortext);
    
     }
 else if ('notoj_de_entajpanto' == $elekto) {
     HtmlKapo();

     $epanto = $_REQUEST['entajpantoid'] or
         $epanto = $_SESSION['kkren']['entajpanto'];

     if (!$epanto) {
         // ne devus okazi, krom se la sesio difektigxis.
         erareldono("Mankas entajpantoid");
         HtmlFino();
         return;
     }

     $entajpanto = new Entajpanto($epanto);
     
     $montrotipo = $_REQUEST['montro']
         or $montrotipo = 'remontrendaj';

     eoecho ("<p>Kion montri?</p>\n<p>");
     foreach($GLOBALS['notomontrotipoj'] AS $tipo => $informoj)
         {
             if ($tipo == $montrotipo) {
                 eoecho (" <strong>&ndash;&gt; " . $informoj['teksto'] . "</strong>");
             }
             else {
                 ligu('sercxrezultoj.php?elekto=notoj_de_entajpanto&entajpantoid='.$epanto
                      .'&montro=' . $tipo,
                      "&ndash;&gt; " . $informoj['teksto']);
             }
         }
     echo("</p>");


     eoecho("<h2>Notoj por " . $entajpanto->datoj['nomo'] . "</h2>\n");


     $sercxilo = kreu_NotoTabelilon('notoj_por_listo', true,
                                    $montrotipo, $epanto);
     $sercxilo->montru_rezulton_en_HTMLtabelo();

     if ($entajpanto->datoj['partoprenanto_id']) {
         
         eoecho("<h2>Notoj pri ".$entajpanto->datoj['nomo']."</h2>\n");
         $sercxilo = kreu_NotoTabelilon('notoj_pri_listo', false,
                                        $montrotipo, 0,
                                        "n.partoprenantoID = '" .
                                        $entajpanto->datoj['partoprenanto_id']."'");
         $sercxilo->montru_rezulton_en_HTMLtabelo();
     }

     eoecho("<h2>Notoj de " . $entajpanto->datoj['nomo'] . "</h2>\n");

     $sercxilo = kreu_NotoTabelilon('notoj_de_listo', true,
                                    $montrotipo, 0,
                                    "kiu LIKE '%".$entajpanto->datoj['nomo'] ."%'");

     $sercxilo->montru_rezulton_en_HTMLtabelo();

     HtmlFino();
 }
 else if ($elekto == "kunmangxo")
     {
         $sql = datumbazdemando(array("pn.ID", "p.ID" => "partoprenoIdento",
                                      "pn.nomo" => "nomo", "personanomo",
                                      "p.domotipo", "p.kunmangxas",
                                      "'". $_SESSION['renkontigxo']->datoj['ID']."'" => "renkNumero"),
                                array("partoprenoj" => "p",
                                      "partoprenantoj" => "pn"),
                                array("NOT (( p.domotipo = 'J' AND p.kunmangxas = 'J' ) OR ".
                                      "( p.domotipo = 'M' AND p.kunmangxas = 'N' )) ",
                                      "p.partoprenantoid = pn.ID"
                                      ),
                                'p.renkontigxoID');

         sercxu($sql,
                array("personanomo","asc"),
                array(array('ID','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','0'), 
                      array('personanomo','personanomo','XXXXX','l','',''), 
                      array('nomo','nomo','XXXXX','l','','-1'), 
                      array('kunmangxas','kunmang^as','XXXXX','z','','-1'),
                      array('domotipo','domotipo','XXXXX','z','','-1'), 
                      ), 
                array(array('',
                            array('&sum; XX','A','z'))),
                "kumangxantoj-listo",
                '',0,$vortext, "Amaslog^antaj kunmang^antoj"); 

     }
 else if ("interreta_listo" == $elekto) {
     HtmlKapo();
     require_once($GLOBALS['prafix'].'/tradukendaj_iloj/trad_htmliloj.php');

     $renkID = $_REQUEST['renkID'] or
         $renkID = $_SESSION['renkontigxo']->datoj['ID'] or
         $renkID = 1; // IJK 2009


     $ligo = "sercxrezultoj.php?elekto=interreta_listo&lingvo=eo&renkID=" .
         $renkID . "&ordigo=";
     eoecho( "Ordigu lau^: ");
     ligu($ligo . "normala", "alig^tempo (defau^lto)");
     ligu($ligo . "sxildo", "kromnomo");
     ligu($ligo . "pers", "persona nomo");
     ligu($ligo . "fam", "familia nomo");
     ligu($ligo . "lando", "esperanta landonomo");
     ligu($ligo . "landokodo", "ISO-landokodo");
     ligu($ligo . "urbo", "Urbo");


     formatu_aligxintoliston($_REQUEST['lingvo'],
                             $_REQUEST['ordigo'],
                             $renkID);
     
     HtmlFino();
 }
 else if ($elekto=="venantoj")  
     { 
         $vortext = "Montras c^iun partoprenanton de la ".$_SESSION["renkontigxo"]->datoj['nomo']; 

         // "select p.ID,pn.ID,p.nomo,personanomo,retakonfirmilo,aligxdato,lando,l.ID,l.nomo from partoprenantoj as p,partoprenoj as pn, landoj as l where l.ID=p.lando and pn.partoprenantoID=p.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."'"

         if ($ordo == "")
             {
                 $ordo = "personanomo";
             }
    
         $sql = datumbazdemando(array("p.ID", "pn.ID" => "partoprenoIdento",
                                      "p.nomo" => "nomo", "personanomo",
                                      "retakonfirmilo", "aligxdato", /* "lando", */
                                      /* "l.ID" =>'landoid', */
                                      "l.nomo" => "landonomo",
                                      "'" . ($_SESSION['renkontigxo']->
                                             datoj['ID']) .
                                      "'" => "renkNumero"),
                                array("partoprenantoj" => "p",
                                      "partoprenoj" => "pn",
                                      "landoj" => "l"),
                                array("l.ID = p.lando",
                                      "pn.partoprenantoID = p.ID"),
                                "renkontigxoID");

         sercxu($sql,
                array($ordo,"asc"),
                array(array('ID','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','0'),
                      array('personanomo','personanomo','XXXXX','l','','', ),
                      array('nomo','nomo','XXXXX','l','','-1', ),
                      array('landonomo','lando','XXXXX','r','','-1',),
                      array('aligxdato','aligxdato','XXXXX','l','','-1', ),
                      array("retakonfirmilo","retakonfirmilo",'XXXXX','z','','-1', ),
                      ),
                array(array('',
                            array('&sum; XX','A','z'),
                            '',
                            '',
                            '',
                            array('&sum; XX','J','z'))),
                "venontoj",
                '',0,$vortext, "Alig^intoj de ". $_SESSION["renkontigxo"]->datoj['mallongigo']); 
 
     } 
 else if ($elekto=="profesioj")  
     { 
         $vortext = "Montras c^iujn profesiojn"; 
  
  
         // "select p.ID,pn.ID,p.nomo,personanomo,okupigxo,okupigxteksto,lando,l.ID,l.nomo from partoprenantoj as p,partoprenoj as pn, landoj as l where l.ID=p.lando and pn.partoprenantoID=p.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and okupigxo!=''", 
         $sql = datumbazdemando(array("p.ID", "pn.ID", "p.nomo", "personanomo",
                                      "okupigxo", "okupigxteksto", "lando",
                                      "l.ID", "l.nomo" => "landonomo"),
                                array("partoprenantoj" => "p",
                                      "partoprenoj" => "pn",
                                      "landoj" => "l"),
                                array("l.ID = p.lando",
                                      "pn.partoprenantoID = p.ID",
                                      "okupigxo != ''"),
                                "renkontigxoID");
         require_once('iloj/iloj_sercxo_rezulto.php');
         sercxu($sql,
                array("okupigxo,okupigxteksto","asc"),
                array(array('0','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','0'),
                      array('personanomo','personanomo','XXXXX','l','',''),
                      array('nomo','nomo','XXXXX','l','','-1'),
                      array('landonomo','lando','XXXXX','r','','-1',),
                      array('okupigxo','','XXXXX','l','','', ),
                      array('okupigxteksto','','XXXXX','l','',''),
                      ),
                array(array(0,array('&sum; XX','A','z'),0,0,array('&sum; XX','E','z'))),
                "okupigxtipoj",
                /* array('okupigxtipo'=>'4'), */
                array('anstatauxo_funkcio' => array(4 => 'okupigxtipo')),
                0,
                $vortext, "homoj kun okupig^o", 'jes'); 
  
     } 
 else if ($elekto=="cxambrodisdonado")  
     { 

         // TODO: falls mehrere Anzahlungen einer Person, diese zusammenfassen
         // TODO:  ... (Group by, SUM). am besten noch herausfinden, zu welchem
         // TODO:  ...  Zeitpunkt die Mindestanzahlung überschritten wurde.

         if ($nur=='antauxpago')
             {
                 $vortext = "C^iu c^ambrohavemulo kun antau^pago, ordigita lau^ dato de pago.";
                 $kaj = array("dato", "kvanto");
                 $kaj3= array("pagoj");
                 $kaj2= array("partoprenoID = pn.ID");
                 $order="dato";
                 $menutitolo = "c^ambremuloj kun antau^pago";
             }
         else
             {
                 $vortext = "C^iu c^ambrohavemulo lau^ ordo de alig^o";
                 $kaj = array();
                 $kaj2 = array();
                 $kaj3 = array();
                 $order="aligxdato";
                 $menutitolo = "c^iuj c^ambremuloj";
             }
         $demando =
             datumbazdemando(array_merge(array("p.ID", "pn.ID",
                                               "p.nomo" => 'famnomo',
                                               "personanomo",
                                               "aligxdato",
                                               /* "lando", "l.ID",*/
                                               "l.nomo" => 'landonomo',
                                               "l.Kategorio" => 'kat',
                                               "dulita",
                                               "kunkiu", "cxambrotipo"),
                                                $kaj),
                                    array_merge(array("partoprenantoj" => "p",
                                                      "partoprenoj" => "pn",
                                                      "landoj" => "l"),
                                                $kaj3),
                                    array_merge(array("l.ID = p.lando",
                                                      "pn.partoprenantoID = p.ID",
                                                      "domotipo = 'J'"),
                                                $kaj2),
                                    "renkontigxoID");
    
         

         sercxu($demando, 
                array($order,"asc"), 
                array(array('0','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','0'),
                      array('personanomo','personanomo','XXXXX','l','',''),
                      array('famnomo','nomo','XXXXX','l','','-1'),
                      array('landonomo','lando','XXXXX','r','','-1'),
                      array('aligxdato','aligxdato','XXXXX','l','','-1'),
                      array('kat','kat.','XXXXX','r','','-1'),
                      array('kvanto','kvanto','XXXXX','r','','-1'),
                      array('dato','dato','XXXXX','r','','-1'),
                      array('cxambrotipo','c^t','XXXXX','z','','-1'),
                      array('dulita','dulita','XXXXX','z','','-1'),
                      array('kunkiu','kunkiu','XXXXX','z','','-1')
                      ),
                array(array('',array('&sum; XX','A','z'),'','',array('&sum; XX','J','z'))),
                "cxambrohavemuloj",
                array('litomanko'=>'1'),
                0,$vortext, $menutitolo);
     }
 else if ($elekto=="skribuagxon")  
     {  

         rekalkulu_agxojn();
         eoecho( "Ag^oj rekalkulitaj!" );
     }
 else if ($elekto=="kunlogxantoj")  
     {  
         HtmlKapo();
         eoecho('<form action="partrezultoj.php" name="peter" method="POST">'.
                '<input type="hidden" name="kune" value="0"><h2>Kunlog^antoj</h2>');
         eoecho("<p>Se A deziras log^i kun B, tiam elektu B en la listo sub la maldekstra menuo,".
                " kaj alklaku la butonon en la tabellinio de A.</p>");

         // "select p.ID,pn.ID,nomo,personanomo,kunkiu,kunkiuID from partoprenantoj as p,partoprenoj as pn where pn.partoprenantoID=p.ID and kunkiu!='' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."'", 
         $sql = datumbazdemando(array("p.ID", "pn.ID", "nomo", "personanomo", "kunkiu", "kunkiuID"),
                                array("partoprenantoj" => "p",
                                      "partoprenoj" => "pn"),
                                array("pn.partoprenantoID = p.ID",
                                      "kunkiu != ''"),
                                "renkontigxoID");
         sercxu($sql,
                array("personanomo","asc"), 
                array(array('0','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','0'),
                      array('personanomo','personanomo','XXXXX','l','',''),
                      array('nomo','nomo','XXXXX','l','','-1'),
                      array('kunkiu','kunkiu','XXXXX','l','','-1'),
                      array('kunkiuID','kunkiuID','XXXXX','l','','-1'),
                      array('1','Disdonu',
                            '<input name="partoprenidento" value="XXXXX" ' .
                            'type="submit"  onclick="reindamit()"> ',
                            'l','','')
                      ),
                array(array('',array('&sum; XX','A','z'),'','',array('&sum; XX','Z','z'))),
                "kunlogxanto-listo",
                '',0,'', 'Kunlog^dezirantoj', 'ne');
         echo "</form>";
         HtmlFino();
     } 
 else if ($elekto=="restaspagenda")
     {
         HTMLkapo();
         // "select p.ID,pn.ID from partoprenantoj as p, partoprenoj as pn where pn.partoprenantoID=p.ID and pn.renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' and alvenstato!='m'"
         $rezulto = sql_faru(datumbazdemando(array("p.ID", "pn.ID"),
                                             array("partoprenantoj" => "p",
                                                   "partoprenoj" => "pn"),
                                             array("pn.partoprenantoID = p.ID",
                                                   "alvenstato != 'm'",
                                                   "alvenstato != 'n'"),
                                             "renkontigxoID"));
         eoecho ("<table border=1><TR><TD>personanomo<td>nomo<td>pagendas<td>antau^pagis<td>restas");
         while ($row = mysql_fetch_array($rezulto, MYSQL_NUM))
             {
                 $partoprenanto = new Partoprenanto($row[0]);
                 $partopreno = new Partopreno($row[1]);

                 $ko = new Kotizo($partopreno,$partoprenanto,$_SESSION["renkontigxo"]);
                 echo "<TR><TD>".$partoprenanto->datoj[personanomo]."<TD>".$partoprenanto->datoj[nomo]."<td>".$ko->kotizo."<td>".$ko->antauxpago."<td>".$ko->pagenda;

             }
 
     }
 else if ("pliaj" == $elekto)  // la detala serĉado
     { 
         $kaj = array();
         $tabelolisto =
             array("partoprenantoj" => "p",
                   "partoprenoj" => "pn",
                   "landoj" => "l");
         $kolonoj = array(array('ID','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"',
                                '0'), 
                          array('personanomo','personanomo','XXXXX','l','',''),
                          array('nomo','nomo','XXXXX','l','','-1'),
                          array('agxo','ag^o','XXXXX','r','',''),
                          array('retposxto','retpos^to','XXXXX','l','',''),
                          array('landonomo','lando','XXXXX','l','','-1'),
                          array('urbo','urbo','XXXXX','l','','-1'),
                          array('aligxdato','aligxdato','XXXXX','l','','-1'),
                          );

         $kampolisto = array("p.ID", "pn.ID" => "partoprenoIdento",
                             "p.nomo" => "nomo", "personanomo",
                             "retakonfirmilo", "aligxdato", "lando",
                             //"l.ID" => "landoID",
                             "l.nomo" => "landonomo", "pn.agxo",
                             "urbo", 'retposxto',
                             "'" . $_SESSION["renkontigxo"]->datoj["ID"] . "'"
                             => "renkNumero",
                              "kontrolata", 
                             "2akonfirmilosendata", "1akonfirmilosendata",
                             "alvenstato", "traktstato", "asekuri", "domotipo",
                             "komencanto", "partoprentipo", "havasMangxkuponon",
                             "havasNomsxildon", "KKRen");
             


         if ($landoKat!='?')
             {
                 $tabelolisto['kategorioj_de_landoj'] = 'kdl';
                 $kaj[]= " kdl.kategorioID = '$landoKat'";
                 $kaj[]= " kdl.landoID = l.ID ";

             }
         if ($lando!='')
             {
                 $helpa = "l.ID"; //='".$lando."'";
                 if ($malellando=='J')
                     $helpa .= ' <> ';
                 else
                     $helpa .= ' = ';
                 $helpa .= "'" . $lando . "'";
                 $kaj[] = $helpa;
             }
    
    
         if ($_REQUEST['alvenstato'] !='?')
             {
                 $kaj[] = "pn.alvenstato = '".$_REQUEST['alvenstato']."'";
             }
         if ($traktstato[0]!='a')
             {
                 $kaj[] = "pn.traktstato = '".$traktstato."'";
             }
    
         if ($havasAsekuron[0]!='a')
             {
                 $kaj[] = "pn.havas_asekuron = '".$havasAsekuron."'";
             }

         if ($asekuri[0]!='a')
             {
                 $kaj[] = "pn.asekuri = '".$asekuri."'";
             }
    
         if ($sekso[0]!='a')
             {
                 $kaj[] = "p.sekso = '".$sekso[0]."'";
             }
         if ($agxode!='')  // das mit dem Alter und Heimatland kommt später hinein
             {

                 $kaj[] = "pn.agxo >= '".$agxode."'";
                 $kaj[] = "pn.agxo <= '".$agxogxis."'";
                 //$select_kaj .= "FLOOR((TO_DAYS('".$_SESSION["renkontigxo"]->datoj[de]."')-TO_DAYS(naskigxdato))/365.25),";

                 //array_push($kolonoj,"agxo","ag^o",'XXXXX','z','','-1');
             }
         if ($komencanto[0]!='a')
             {
                 $kaj[] = "pn.komencanto ".$komencanto." 'J'";
             }
         if ($vegetare!='?')
             {
                 $kaj[] = "pn.vegetare = '". $vegetare ."'";
             }
         if ($gejmembro[0]!='a')
             {
                 $kaj[] = "pn.GEJmembro = '".$gejmembro[0]."'";
             }
         if ($surlkotizo[0]!='-')
             {
                 $kaj[] = "pn.surloka_membrokotizo = '".$surlkotizo[0]."'";
             }
         if ($tejomembrolaux[0]!='a')
             {
                 $kaj[] = "pn.tejo_membro_laudire = '".$tejomembrolaux[0]."'";
             }
         if ($tejomembropost[0]!='-')
             {
                 $kaj[] = "pn.tejo_membro_kontrolita = '".$tejomembropost[0]."'";
             }
         if ($KKRen[0]!='a')
             {
                 $kaj[] = "pn.KKRen = '".$KKRen[0]."'";
             } 
         if ($partoprentipo[0]!='a')
             {
                 $kaj[] = "pn.partoprentipo = '".$partoprentipo[0]."'";
             } 
         if ($domotipo[0]!='a')
             {
                 $kaj[] = "pn.domotipo = '".$domotipo[0]."'";
             }     
         if ($kunmangxas[0]!='a')
             {
                 $kaj[] = "pn.kunmangxas = '".$kunmangxas[0]."'";
             }  
         if ($ekskursbileto[0]!='a')
             {
                 $kaj[] = "pn.ekskursbileto = '".$ekskursbileto[0]."'";
             }     
         if ($cxambrotipo[0]!='a')
             {
                 $kaj[] = "pn.cxambrotipo = '".$cxambrotipo[0]."'";
             }     
         if ($dulita[0]!='a')
             {
                 if ($dulita == 'N') {
                     $kaj[] = "pn.dulita = '' or pn. dulita = 'N'";
                 }
                 else {
                     $kaj[] = "pn.dulita = '$dulita'";
                 }
             } 
         if ($kuncxambroj=='J')
             {
                 $extra['litomanko']= '1';
             }
         if ($tutacxambro=='T')
             {
                 $extra['tutacxambro']='1';
             }
         if ($kunadreso=='J')
             {

                 if(KAMPOELEKTO_IJK) {
                     array_push($kolonoj, array('adreso', 'adreso', 'XXXXX',
                                                '1', '', '-1'));
                     $kampolisto[]= 'adreso';
                 }
                 else {
                     array_push($kolonoj,
                                array("adresaldonajxo","adresaldonaj^o",
                                      'XXXXX','l','','-1'));
                     array_push($kolonoj,
                                array("strato","strato",'XXXXX','l','','-1'));
                     array_push($kolonoj,
                                array("provinco","provinco",
                                      'XXXXX','r','','-1'));
                     array_push($kampolisto,
                                'adresaldonajxo', 'strato', 'provinco');
                 }
                 array_push($kolonoj,
                            array("posxtkodo","pos^tkodo",
                                  'XXXXX','r','','-1'));
                 array_push($kolonoj,
                            array("naskigxdato","naskd",
                                  'XXXXX','z','','-1'));
                 array_push($kampolisto,  "posxtkodo", "naskigxdato");

             }
//          if ($invitletero[0]!='a')
//              {
//                  $kaj[] = "pn.invitletero ".$invitletero." 'J'";
//                  array_push($kolonoj,array("invitletero","invitletero",'XXXXX','z','','-1'));
//                  array_push($kolonoj,array("invitilosendata","invitosendata",'XXXXX','l','','-1'));
//              }

         if ($retakonfirmilo[0]!='a')
             {
                 $kaj[] = "pn.retakonfirmilo ".$retakonfirmilo." 'J'";
                 $kolonoj[]=
                     array("retakonfirmilo","retakonfirmilo",
                           'XXXXX','z','','-1');
                 $kampolisto[]= 'retakonfirmilo';
             }
         if (KAMPOELEKTO_IJK) {
             // TODO(?): sercxo pri konfirmilolingvo
         }
         else {
             if ($germanakonfirmilo[0]!='a')
                 {
                     $kaj[] = "pn.germanakonfirmilo ".$germanakonfirmilo." 'J'";
                     array_push($kolonoj,array("germanakonfirmilo","germanaakonfirmilo",'XXXXX','z','','-1'));
                     $kampolisto[]= 'germanakonfirmilo';
             }
         }
         if ($kontrolata[0]!='a')
             {
                 $kaj[] = "pn.kontrolata ".$kontrolata." 'J'";
                 array_push($kolonoj,array("kontrolata","kontrolata",'XXXXX','z','','-1'));
             }
    
         if ($konf1a[0]!='a')
             {
                 $kaj[] = "pn.1akonfirmilosendata ".$konf1a." '0000-00-00'";
                 array_push($kolonoj,array("1akonfirmilosendata","1akonfirmilosendata",'XXXXX','z','','-1'));
             }

         if ($konf2a[0]!='a')
             {
                 $kaj[] = "pn.2akonfirmilosendata ".$konf2a." '0000-00-00'";
                 array_push($kolonoj,array("2akonfirmilosendata","2akonfirmilosendata",'XXXXX','z','','-1'));
             }
    
         if ($mangxkupo[0]!='a')
             {
                 $kaj[] = "pn.havasMangxkuponon=' ".$mangxkupo." '";
                 array_push($kolonoj,array("havasMangxkuponon","havasMangxkuponon",'XXXXX','z','','-1'));
             }

         if ($nomsxildo[0]!='a')
             {
                 $kaj[] = "pn.havasNomsxildon = '".$nomsxildo."'";
                 array_push($kolonoj,array("havasNomsxildon","havasNomsxildon",'XXXXX','z','','-1'));
             }


         if ($distra=="J")
             {
                 $kaj[] = "pn.distra != ''";
                 array_push($kolonoj, array('distra','distrakontribuo','XXXXX','l','',''));
                 $kampolisto[]='distra';
             }       
         if ($tema=="J")
             {
                 $kaj[] = "pn.tema != ''";
                 array_push($kolonoj, array('tema','temakontribuo','XXXXX','l','',''));
                 $kampolisto[]='tema';
             }
         if ($vespera=="J")
             {
                 $kaj[] = "pn.vespera != ''";
                 array_push($kolonoj, array('vespera','vesperakontribuo','XXXXX','l','',''));
                 $kampolisto[]= 'vespera';
             }
         if ($muzika=="J")
             {
                 $kaj[] = "pn.muzika != ''";
                 array_push($kolonoj,
                            array('muzika','muzikakontribuo','XXXXX','l','',''));
                 $kampolisto[]= 'muzika';
             }
         if ($helpo=="J")
             {
                 $kaj[] = "pn.helpo <> ''";
                 $kolonoj[]= array('helpo','helpoferto','XXXXX','l','','');
             }

         $vortext = "Montras c^iun partoprenanton lau^vole: [(" . implode(") kaj (", $kaj) . ")]";
    


         $sercxfrazo =
             datumbazdemando($kampolisto,
                             $tabelolisto,
                             array_merge(array("l.ID = p.lando",
                                               "pn.partoprenantoID = p.ID"),
                                         $kaj),
                             "renkontigxoID");


         sercxu($sercxfrazo,
                array("personanomo", "asc"),
                $kolonoj,
                array(array('',array('&sum; XX','A','z'))),
                "detalasercxo",
                $extra,$csv,$vortext,"(el detala serc^o)");
     }
 else if ($elekto=="antauxpagoj")
     {

         $sql = datumbazdemando(array("pp.ID" => "ppID", 
                                      "p.ID" => "pagoID",
                                      "pt.ID" => "ptID", "nomo", "personanomo",
                                      "kvanto", "valuto",
                                      "dato", "tipo"),
                                array("pagoj" => "p",
                                      "partoprenoj" => "pp",
                                      "partoprenantoj" => "pt"),
                                array("p.partoprenoID = pp.ID",
                                      "pp.partoprenantoID = pt.ID"),
                                "renkontigxoID");
         sercxu($sql,
                array("tipo,dato","asc"),
                array(array('ppID','ppID','->','z','"partrezultoj.php?partoprenidento=XXXXX"','ptID'),
                      array('tipo','tipo','XXXXX','l','',''),
                      array('personanomo','personanomo','XXXXX','l','',''), 
                      array('nomo','nomo','XXXXX','l','','-1'), 
                      array('pagoID', 'pagoID', '->', 'z',
                            '"pago-detaloj.php?klaso=pago&id=XXXXX"', "ptID"),
                      array('kvanto','kvanto','XXXXX','l','',''), 
                      array('valuto','valuto','XXXXX','l','',''), 
                      array('dato','dato','XXXXX','l','','-1')),
                array(array(array('#', '*', 'd'),
                            array('XX', 'A', 'm'),
                            '','',
                            array('&sum;', '*', 'd'),
                            array('XX', 'N', 'm'))),
                "antauxpago-listo",
                0,0, "C^iuj antau^pagoj:", "c^iuj antau^pagintoj");

         //Einzelsummen Anzahlungen
         // "select SUM(kvanto),tipo from pagoj as p,partoprenoj as pn where p.partoprenoID=pn.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' group by tipo"


         $sql = datumbazdemando(array("SUM(kvanto)" => "kvantsumo",
                                      "tipo", "valuto"),
                                array("pagoj" => "p",
                                      "partoprenoj" => "pn"),
                                "p.partoprenoID = pn.ID",
                                "renkontigxoID",
                                array("group" => "tipo, valuto"));
         sercxu($sql,
                array("tipo","asc"),
                array(array('tipo','tipo','XXXXX','l','',''),
                      array('kvantsumo','kvanto','XXXXX','l','',''),
                      array('valuto','valuto','XXXXX','l','',''),
                      ),
                array(array(array('# XX', 'A', 'z'),
                            array('&sum; XX', 'N', 'z'))),
                "antauxpagoj-laux-tipo",
                0,0, "Sumoj lau^ la antau^pagmanieroj:", '');
     }
 else if ($elekto=="rabatoj")
     {
         // "select r.ID,r.partoprenoID,pp.ID,pp.partoprenantoID,pt.ID,nomo,personanomo,kvanto,kauzo from rabatoj as r, partoprenoj as pp, partoprenantoj as pt where r.partoprenoID=pp.ID and pp.partoprenantoID=pt.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."'"


         $sql = datumbazdemando(array("r.ID", "r.partoprenoID", "pp.ID" => "ppID", "pp.partoprenantoID",
                                      "pt.ID", "nomo", "personanomo", "kvanto", "kauzo"),
                                array("rabatoj" => "r",
                                      "partoprenoj" => "pp",
                                      "partoprenantoj" => "pt"),
                                array("r.partoprenoID = pp.ID",
                                      "pp.partoprenantoID = pt.ID"),
                                "renkontigxoID");
         sercxu($sql,
                array("kauzo","asc"),
                array(array('ppID','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','3'),
                      array('kauzo','kau^zo','XXXXX','l','',''),
                      array('personanomo','personanomo','XXXXX','l','',''),
                      array('nomo','nomo','XXXXX','l','','-1'),
                      array('kvanto','kvanto','XXXXX','l','',''), 
                      ),
                0,
                "rabato-listo",
                0,0, "C^iuj rabatoj:", 'C^iuj rabatitoj');


         // "select SUM(kvanto),kauzo,renkontigxoID,r.partoprenoID,pn.ID from rabatoj as r,partoprenoj as pn where r.partoprenoID=pn.ID and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."' group by kauzo",
         $sql = datumbazdemando(array("SUM(kvanto)" => "kvantsumo", "kauzo", "renkontigxoID",
                                      "r.partoprenoID", "pn.ID"),
                                array("rabatoj" => "r", "partoprenoj" => "pn"),
                                "r.partoprenoID = pn.ID",
                                "renkontigxoID",
                                array("group" => "kauzo")
                                );
         sercxu($sql,
                array("kauzo", "asc"),
                array(array('kauzo','kau^zo','XXXXX','l','',''),
                      array('kvantsumo','kvanto','XXXXX','l','','')
                      ),
                array(array(array('# XX', 'A', 'z'), array('&sum; XX', 'N', 'z'))),
                "rabatoj-laux-kauxzo",
                0,0, "Sumoj lau^ la unuopaj rabatkau^zoj", '');
   
     }
 else if ($elekto=="rimarkoj")
     {
         // "select pp.ID,pp.partoprenantoID,pt.ID,pp.rimarkoj,nomo,personanomo from partoprenoj as pp, partoprenantoj as pt where pp.partoprenantoID=pt.ID and pp.rimarkoj!='' and renkontigxoID='".$_SESSION["renkontigxo"]->datoj[ID]."'",

         $sql = datumbazdemando(array("pp.ID", "pp.partoprenantoID", "pt.ID",
                                      "pp.rimarkoj", "nomo", "personanomo"),
                                array("partoprenoj" => "pp", "partoprenantoj" => "pt"),
                                array("pp.partoprenantoID = pt.ID",
                                      "pp.rimarkoj != ''"),
                                "renkontigxoID");

         sercxu($sql,
                array("personanomo","asc"),
                array(array('1','','->','z','"partrezultoj.php?partoprenantoidento=XXXXX"','1'),
                      array('personanomo','personanomo','XXXXX','l','',''), 
                      array('nomo','nomo','XXXXX','l','','-1'), 
                      array('3','rimarkoj','XXXXX','l','',''), 
                      ),
                0,
                "rimarko-listo",
                0,
                0, "C^iuj rimarkoj de la partoprenantoj.", 'rimarkintoj');
     }
 else if ("kotizokomparo" == $elekto) {


     $kotizosistemo =
         new Kotizosistemo($_SESSION['renkontigxo']->datoj['kotizosistemo']);


     $sql = datumbazdemando(array("pn.ID" => "eno", "pt.ID" => "anto"),
                            array("partoprenoj" => "pn",
                                  "partoprenantoj" => "pt"),
                            array("pn.partoprenantoID = pt.ID"),
                            "renkontigxoID");
     $rez = sql_faru($sql);

     HtmlKapo();
     eoecho ("<table>\n".
             "<tr><th>p-enoID</th><th>nomo</th><th>nova kotizo</th>".
             "<th>malnova kotizo</th><th>diferenco</th></tr>\n");
     while ($linio = mysql_fetch_assoc($rez)) {
         $pprenanto = new Partoprenanto($linio['anto']);
         $ppreno = new Partopreno($linio['eno']);

         // malnova kotizosistemo
         $kot = new Kotizo($ppreno,
                           $pprenanto,
                           $_SESSION['renkontigxo']);

         // nova kotizosistemo
         $kotkal = new Kotizokalkulilo($pprenanto,
                                       $ppreno,
                                       $_SESSION['renkontigxo'],
                                       $kotizosistemo);

         $malnova = $kot->restas_pagenda();
         $nova = $kotkal->restas_pagenda();

         if (abs($nova - $malnova) < 1) {
             echo
                 "<!-- " . $pprenanto->tuta_nomo() . "(" . $nova . "/" . $malnova . ") -->";
         }
         else {
             eoecho ("<tr><td>".
                     donu_ligon("partrezultoj.php?partoprenidento=" .
                                $ppreno->datoj['ID'],
                                $ppreno->datoj['ID']) . "</td><td>" .
                     $pprenanto->tuta_nomo() . "</td><td>" .
                     $nova . "</td><td>" . $malnova . "</td><td>" .
                     ($nova - $malnova).  "</td></tr>");
                
         }

     }
     echo ("</table>");
     HtmlFino();
 } // kotizokomparo

 else if ("lasta_sercxo" == $elekto)
     {
         // nova varianto de memligo (por la nova sercxilo-objekto)
         
         $sercxilo = $_SESSION['lasta_sercxo'][$_REQUEST['id']];
         if ($_REQUEST['ordigo']) {
             $sercxilo->metu_ordigon($_REQUEST['ordigo'],
                                     $_REQUEST['direkto']);
         }
         $sercxilo->montru_rezulton_en_tipo($_REQUEST['tipo']);

         exit();
     }
 else if ("memligo" == $elekto)
     {

         $datumoj = $_SESSION['memligo'][$_GET['id']];

//          echo "<!-- _GET: " . var_export($_GET, true) . "-->";

//          echo "<!-- elekto: $elekto, id: $id, orderby: $orderby, asc: $asc -->";

//          echo "<!-- " . var_export($_SESSION['memligo'], true) . "-->";

         // por ebligi varian ordigadon en tabeloj.
         // nova varianto de $elekto == "eigenlink",
         // uzata de sercxu() anstataŭ de Suche().

         
         sercxu($datumoj['sql'],
                array($orderby, $asc),
                $datumoj["kolumnoj"],
                $datumoj["sumoj"],
                $id,
                $datumoj["aldone"],
                0 /* csv == 0 -> HTML-tabelo */,
                $datumoj["antauxteksto"],
                $datumoj['almenuo'],
                "jes");
     }
 else 
     { 
         echo "Irgendwas ist schiefgelaufen....\n<pre>POST:"; 
   
         var_export($_POST);
   
         echo ("\n GET:");

         var_export($_GET);

     } 

 
?>