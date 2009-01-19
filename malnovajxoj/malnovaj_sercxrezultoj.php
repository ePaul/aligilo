<?php

  /**
   * ne plu aktualaj eroj el {@link sercxrezultoj.php}.
   *
   * Per $_REQUEST['elekto'] oni elektas, kiun serĉon oni volas.
   * - <samp>partoprenintoj_por_enketo</samp>:
   *    CSV-listo de ĉiuj partoprenintoj de la aktuala renkontiĝo, por
   *     uzo en enketo.
   * - <samp>aligxintoj_laux_kotizokategorioj</samp>:
   *     statistiko pri aliĝintoj en la unuopaj aliĝ/lando/...-kategorioj.
   *     Nun ne plu bezonata, la kotizosistemredaktilo enhavas similan
   *     funkcion.
   * - <samp>aligxintoj_laux_kategorioj</samp>:
   *     Hmm, io simila al la lasta.
   * - <samp>andiListe</samp>:
   *     listo de partoprenantoj kun adresoj kaj aĝoj, kiun Andi bezonis
   *     post la 2005-a IS por la ministerio.
   */


/**
 *
 * la kutimaj iloj.
 */
require_once ("iloj/iloj.php");
session_start();
malfermu_datumaro();
 
 
// (TODO: traduku:) Auswahl der gewuenschten Aktion 

$elekto = $_REQUEST['elekto'];



if ('partoprenintoj_por_enketo' == $elekto)
    {
        /*
         Por prepari la enketilon, jen listigo de iom pli teknikaj aferoj kiujn mi bezonas el la IS-datumbazo.

         Esence mi bezonas nur iun txt-file (kun komoj por distingi kampojn kaj nova linio por sekva partoprenanto), aŭ excell-file.

         Jen listo de kampo kiujn mi bezonus minimume:
         -> Persona kodo por ligi (ID-key)
         -> Nomo familia
         -> Nomo persona
         -> Retadreso

         El posta analiza vidpunkto utilus aldonaj donitaĵoj:
         -> Lando
         -> Landokategorio
         -> Landokategorio
         -> Naskiĝdato          
        */

        /**
         * @todo
         * tiu ne plu funkcias en la nuna sistemo, cxar l.kategorio
         * ne plu ekzistas, anstatauxe tio nun dependas de la
         * kotizosistemo (pli precize: landokategorisistemo).
         */
        $sql = datumbazdemando(array('pa.ID', 'pa.personanomo', 'pa.nomo',
                                     'pa.retposxto', 'pa.naskigxdato',
                                     'l.nomo' => 'landonomo',
                                     'l.kategorio' => 'landkat',
                                     ),
                               array('partoprenantoj' => 'pa',
                                     'partoprenoj' => 'p',
                                     'landoj' => 'l', 
                                     ),
                               array('pa.ID = p.partoprenantoID',
                                     'pa.lando = l.ID',
                                     "p.alvenstato = 'a'"),
                               'p.renkontigxoID'
                               );

        sercxu($sql,
               array("ID", "ASC"),
               array(
                     array("ID", "ID", "XXXXX", "", "", ""),
                     array("personanomo", "persona nomo", "XXXXX", "", "", ""),
                     array("nomo", "familia nomo", "XXXXX", "", "", ""),
                     array("retposxto", "retposxta adreso", "XXXXX", "", "", ""),
                     array("naskigxdato", "naskig^dato", "XXXXX", "", "", ""),
                     array("landonomo", "Lando", "XXXXX", "", "", ""),
                     array("landkat", "Landokategorio", "XXXXX", "", "", ""),
                     ),
               array(array(0,array('&sum; XX','A','z'))),
               "ilja_liste",
               "", 2 /* 2 = CSV por elŝuti */, "", "", "");

    }

 else if('aligxintoj_laux_kotizokategorioj' == $elekto)
     {
         // nun anstatauxita per la kalkulado en
         // enspezokalkulado.php.
         // tiu cxi estis provo fari ion similan por
         // la malnova kotizosistemo.
         

         $sql =
             datumbazdemando(array('COUNT(p.ID) AS nombro',
                                   'l.kategorio' => 'landokategorio',
                                   'p.alvenstato',
                                   'p.domotipo',
                                   'p.agxo',
                                   'INTERVAL(p.aligxdato,
                                             DATE_ADD(x.plej_frue, INTERVAL (r.ID - x.ID) YEAR),
                                             DATE_ADD(x.meze, INTERVAL (r.ID - x.ID) YEAR),
                                             r.de,
                                             r.gxis)' => 'aligxkategorio',
                                   'r.mallongigo' => "renkontigxo",
                                   'r.ID' => "renkID"),
                             array('landoj' => 'l',
                                   'partoprenoj' => 'p',
                                   'partoprenantoj' => 'pa',
                                   'renkontigxo AS r',
                                   'renkontigxo AS x'),
                             array('pa.ID = p.partoprenantoID',
                                   'r.ID = p.renkontigxoID',
                                   'r.ID > 0',
                                   'x.ID = 6',
                                   "p.alvenstato = 'a' OR "
                                   . "p.alvenstato = 'i'",
                                   'l.ID = pa.lando'),
                             "",
                             array ('group' => 'renkontigxo, domotipo, '
                                    .          'landokategorio, alvenstato, '
                                    .          'aligxkategorio, agxo')
                                        
                             );


         /// ------- jen laŭ aĝkategorioj -----------

            

             //         var_export($sql);
             $result = sql_faru($sql);
             $resumo = array();
             $aligxkategorioj = array(0,1,2,3,4);
             $landokategorioj = array('A', 'B', 'C');
             $domotipoj = array('J', 'M');
             $renkontigxoj = array(1,2,3,4,5,6);
             $agxkategorioj = array(0, 1,2,3,4);

             foreach($renkontigxoj AS $ren)
                 {
                     foreach($domotipoj AS $domo)
                         {
                             foreach($landokategorioj AS $landKat)
                                 {
                                     foreach($aligxkategorioj AS $aligxKat)
                                         {
                                             foreach($agxkategorioj AS $agxKat)
                                                 {
                                                     $rezultoj[$ren . '##' . $landKat . "##a##" . 
                                                               $aligxKat . '##' .$domo . '##' . $agxKat] =
                                                         array('renkID' => $ren,
                                                               'landokategorio' => $landKat,
                                                               'aligxkategorio' => $aligxKat,
                                                               'agxkategorio' => $agxKat,
                                                               'domotipo' => $domo,
                                                               );
                                                 }
                                         }
                                 }
                         }
                 }

             $kotizo = new Kotizo(null,null,null);
             while ($linio = mysql_fetch_array($result, MYSQL_ASSOC))
                 {
                     // la aĝokategorioj laŭ la 6a IS
                     $agxkategorio =
                         $kotizo->kalkulu_agx_kategorio($linio["agxo"], 6 );
                     $linionomo =
                         $linio['renkID'] . '##' .
                         $linio['landokategorio'] . "##" .
                         $linio['alvenstato'] . '##' . 
                         $linio['aligxkategorio'] . '##' .
                         $linio['domotipo'] . '##' .
                         $agxkategorio;
                
                     $jama_linio = $resumo[$linionomo];
                     if (! $jama_linio)
                         {
                             $jama_linio = $linio;
                             $jama_linio['agxkategorio'] = $agxkategorio;
                         }
                     else
                         {
                             $jama_linio['nombro'] += $linio['nombro'];
                         }
                     $resumo[$linionomo] = $jama_linio;
                 }
             //         var_export($resumo);
             echo "<table>";
             reset($resumo);
             $linio = $resumo[key($resumo)];
             echo "<tr>";
             //eoecho("<th>AK#</th><th>Ag^kategorio</th><th>Landokategorio</th><th>Alvenstato</th><th>Alig^kategorio</th><th>renkontig^o</th><th>domotipo</th><th>nombro</th>");
             eoecho("<th>Ag^kategorio</th><th>Landokategorio</th><th>Alig^kategorio</th><th>renkontig^o</th>" .
                    "<th>domotipo</th><th>nombro</th>");
             echo "</tr>";
             $renkontigxo = new Renkontigxo(6);
             foreach($resumo AS $linio)
                 {
                     echo "<tr><td>" /*. $linio['aĝkategorio'] . "</td><td>"*/;
                     $kotizo->agxkategorio = $linio['agxkategorio'];
                     echo $kotizo->formatu_agxkategorion($renkontigxo);
                     echo "</td><td>" . $linio['landokategorio'] . "</td><td>" .
                         /* $linio['alvenstato'] . "</td><td>" .*/ ($linio['aligxkategorio']+1) .
                         "</td><td>" . $linio['renkontigxo'] . "</td><td>".$linio['domotipo'] ."</td><td>" . $linio['nombro'] . "</td>";
                     echo "</tr>";
                 }
             echo "</table><hr/>";

             // ------------- la bazaj datoj ------

             $vortext = "Sekvas la bazaj datoj, lau^ kiu estis farita la supra statistiko.
 </p>
 <p>
  Jen la alig^kategorioj:</p>
 <ul>
  <li>0: antau^ la unua limdato (unua kategorio)</li>
  <li>1: inter unua kaj dua limdato (dua kategorio)</li>
  <li>2: inter dua limdato kaj komenco de renkontig^o
     (g^is 2004: tria kategorio, poste: krompago pro surloka alig^o)</li>
  <li>3: inter komenco kaj fino de renkontig^o</li>
  <li>4: post la fino de la renkontig^o</li>
 </ul>
  <p>Jen la alvenstatoj:</p>
 <ul>
   <li>v: venos</li>
   <li>a: alvenis</li>
   <li>m: malalig^is</li>
 </ul>
<p>La ciferoj nur estas sencohavaj ekde 2002 (tiuj de 2002 estas importoj de antau^a sistemo). Kaj atentu la limdat-s^ovon inter 2004 kaj 2005.";


             sercxu($sql,
                    array("renkontigxo","asc"), 
                    array(array('renkontigxo', 'Renkontig^o', 'XXXXX', 'l', '', -1),
                          array('landokategorio','Lando&shy;kategorio','XXXXX','c','','-1'), 
                          array('alvenstato','Alvenstato','XXXXX','c','','-1'),
                          array('aligxkategorio','Alig^kategorio','XXXXX','l','','-1'), 
                          array('agxo','Ag^o','XXXXX','l','','-1'), 
                          array('domotipo','Domotipo','XXXXX','l','','-1'), 
                          array('nombro','nombro','XXXXX','l','','-1'), 
                          ),
                    '',
                    'aligxintoj_laux_kategorioj',
                    0,$csv,$vortext, '');



     }
 else if ($elekto == 'aligxintoj_laux_kategorioj')
     {

         $sql = datumbazdemando(array('COUNT(p.ID) AS nombro',
                                      'l.kategorio' => 'landokategorio',
                                      'p.alvenstato',
                                      'INTERVAL(p.aligxdato, r.plej_frue, r.meze, r.de, r.gxis)' => 'aligxkategorio',
                                      'r.mallongigo' => "renkontigxo"),
                                array('landoj' => 'l',
                                      'partoprenoj' => 'p',
                                      'partoprenantoj' => 'pa',
                                      'renkontigxo' => "r"),
                                array('pa.ID = p.partoprenantoID',
                                      'r.ID = p.renkontigxoID',
                                      'r.ID > 0',
                                      'l.ID = pa.lando'),
                                "",
                                array ('group' => 'landokategorio, alvenstato, aligxkategorio, renkontigxo')
                                );
                                     
        
         $vortext = "Statistiko pri alig^ciferoj lau^ diversaj kategorioj.
 </p>
 <p>
  Jen la alig^kategorioj:</p>
 <ul>
  <li>0: antau^ la unua limdato (unua kategorio)</li>
  <li>1: inter unua kaj dua limdato (dua kategorio)</li>
  <li>2: inter dua limdato kaj komenco de renkontig^o
     (g^is 2004: tria kategorio, poste: krompago pro surloka alig^o)</li>
  <li>3: inter komenco kaj fino de renkontig^o</li>
  <li>4: post la fino de la renkontig^o</li>
 </ul>
  <p>Jen la alvenstatoj:</p>
 <ul>
   <li>v: venos</li>
   <li>a: alvenis</li>
   <li>m: malalig^is</li>
 </ul>
<p>La ciferoj nur estas sencohavaj ekde 2002 (tiuj de 2002 estas importoj de antau^a sistemo). Kaj atentu la limdat-s^ovon inter 2004 kaj 2005.";

         sercxu($sql,
                array("renkontigxo","asc"), 
                array(array('renkontigxo', 'Renkontig^o', 'XXXXX', 'l', '', -1),
                      array('landokategorio','Lando&shy;kategorio','XXXXX','c','','-1'), 
                      array('alvenstato','Alvenstato','XXXXX','c','','-1'),
                      array('aligxkategorio','Alig^kategorio','XXXXX','l','','-1'), 
                      array('nombro','nombro','XXXXX','l','','-1'), 
                      ),
                '',
                'aligxintoj_laux_kategorioj',
                0,$csv,$vortext, '');

     }
 else
 if ("andiListe" == $elekto)
     {
         // sonderanfertigung für AnDi. (Version für IS 2005)

         $sql = datumbazdemando(array('personanomo', 'pa.nomo' => 'nomo', 'sekso', 'naskigxdato',
                                      'adresaldonajxo', 'strato', 'posxtkodo', 'urbo',
                                      'l.lokanomo' => 'landonomo',
                                      "FLOOR(( UNIX_TIMESTAMP('2005-12-27') - UNIX_TIMESTAMP(naskigxdato))/(365.25*24*60*60))" => 'agxo',
                                      ), 
                                array('partoprenoj' => 'po', 'partoprenantoj' => 'pa', 'landoj' => 'l',),
                                array('po.partoprenantoID = pa.ID', 'pa.lando = l.id',
                                      "po.alvenstato = 'a'"),
                                'renkontigxoID'
                                );

         $rez = sql_faru($sql);
  
         sercxu($sql,
                array("personanomo", "ASC"),
                array(array("personanomo", "Vorname", "XXXXX", "", "", ""),
                      array("nomo", "Nachname", "XXXXX", "", "", ""),
                      array("agxo", "Alter", "XXXXX", "", "", ""),
                      array("naskigxdato", "Geburtsdatum", "XXXXX", "", "", ""),
                      array("adresaldonajxo", "Adresszusatz", "XXXXX", "", "", ""),
                      array("strato", "Str. und HNr.", "XXXXX", "", "", ""),
                      array("posxtkodo", "PLZ", "XXXXX", "", "", ""),
                      array("urbo", "Ort", "XXXXX", "", "", ""),
                      array("landonomo", "Land", "XXXXX", "", "", ""),
                      ),
                array(array(0,array('&sum; XX','A','z'))),
                "andiListe",
                "", 2 /* CSV por elŝuti */, "", "", "");


     }
 else