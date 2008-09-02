<?php

  /**
   * Sercxado de partoprenantoj.
   *
   * Kaj sercx-formularo (fakte du: simpla/gxenerala)
   * kaj montrilo por rezultolisto de la simpla sercxo.
   * Krome enestas ligoj al spezialigitaj sercxoj (kaj
   * la detala sercxo).
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   * @todo plibonigi la HTML-kodon, strukturigi al funkcioj/objektoj.
   */


  /**
   */
  require_once ('iloj/iloj.php');
  session_start();
  malfermu_datumaro();

  unset($_SESSION["partoprenanto"]);
  unset($_SESSION["partopreno"]);
  

  if (!rajtas("vidi"))
  {
    ne_rajtas();
  }

  HtmlKapo();

  if($sql.$sercxfrazo!="")
  {
    if (empty($sql)and (!empty($sercxfrazo)))
    {
      $sql=$sercxfrazo;
    }
    else if ($sql=="sercxu")
    {
      // $sql = "Select id, personanomo, nomo, urbo, lando, posxtkodo, naskigxdato from partoprenantoj where 1 ";
	  $sql = datumbazdemando(array("id", "personanomo", "nomo", "urbo",
								   "lando", "posxtkodo", "naskigxdato"),
							 "partoprenantoj");

      sql_kaju($sql,nomo,$nomo);
      sql_kaju($sql,urbo,$urbo);
      sql_kaju($sql,personanomo,$personanomo);
      sql_kaju($sql,posxtkodo,$posxtkodo);
      sql_kaju($sql,ID,$ID);
      sql_kaju($sql,naskigxdato,$naskigxdato);

      //$sql.="limit 0,10"; //ne plu bezonata
    }
    $result = sql_faru($sql);

    //session_register("sercxfrazo");
    //$sercxfrazo = $sql;

    if (mysql_num_rows($result)!=0)
    {
      echo "<TABLE border=1>\n";
      echo    "<tr> <th> vidu </th>\n";
      //eoecho ("     <th> s^ang^u </th>\n");
      //eoecho ("     <th> alig^u </th>\n");
      echo "        <th> ID </th>\n";
      echo "        <th> personanomo </th>\n";
      echo "        <th> nomo </th>\n";
      echo "        <th> urbo </th>\n";
      echo "        <th> lando </th>\n";
      eoecho ("     <th> pos^tkodo </th>\n");
      eoecho ("     <th> naskig^dato </th> </tr>\n");

      while ($row = mysql_fetch_array($result, MYSQL_NUM))
      {
          $row[4]= eltrovu_landon($row[4]); // TODO: Join
        echo "<TR> <TD>\n";
        ligu ("partrezultoj.php?partoprenantoidento=$row[0]","-->");
        echo "</TD><TD> ";//$vosto\n";
       // ligu ("partoprenanto.php?ago=sxangxi&partoprenantoidento=$row[0]&sp=partrezultoj.php","-->");
       // echo "</TD><TD>\n";
       // ligu ("partopreno.php","-->");
       // echo "</TD><TD>\n";
        eoecho (implode(" </TD><TD> ",$row));
        echo "</TR>\n";
      }
      echo "</TABLE>\n";
      }
    else echo "Mi ne trovas tiajn datumojn\n";
  }

eoecho ("<h3>Simpla serc^ilo</h3>");
eoecho ("<p>(lokoteniloj: '_' estas iu signo, '%' estas iuj signoj.)</p>\n");

  echo "<FORM ACTION='partsercxo.php?sql=sercxu' METHOD='POST'>\n";
  echo "<TABLE><TR><TD><p style='text-align:right;margin-left:1cm;'>";
  entajpejo("persona nomo:",personanomo,$personanomo,15);
  entajpejo("familia nomo:",nomo,$nomo,15);
  entajpejo("urbo:",urbo,$urbo,15);
  entajpejo("pos^tkodo:",posxtkodo,$posxtkodo,15);
  entajpejo("naskig^dato:",naskigxdato,$naskigxdato,15);
  entajpejo("ID:",ID,$ID,15);
  send_butono("Serc^u!");
  echo "</p></TABLE>";
  echo "</FORM>\n";

echo "<hr/>\n";

echo "<p>";
// ligu("sercxrezultoj.php?elekto=venantoj", "c^iu partoprenanto de la ".$_SESSION["renkontigxo"]->datoj[nomo] . " (lau^ persona nomo)");
ligu("sercxrezultoj.php?elekto=venantoj&ordo=aligxdato", "c^iu partoprenanto de la ".$_SESSION["renkontigxo"]->datoj[nomo] . " (lau^ alventempo de la alig^ilo)");
echo "</p><p>";
ligu("gxenerala_sercxo.php", "G^enerala serc^o pri c^io ajn");
echo "</p>";
//ligu("sercxoj.php", "Jam preparitaj serc^oj");

require_once('iloj/iloj_sercxo_konservo.php');
kasxeblaSercxoElektilo();



echo "<hr/>\n";

eoecho("<h3 id='detala'>Detala serc^o</h3>\n");
  echo "<form action='sercxrezultoj.php?elekto=pliaj' method='post'>\n";
   
  eoecho ("<b>ag^o inter: </b>");
  echo "<select name='agxode' size=1>\n";
      echo "<option selected value=''>-</option>\n";
      for ($i=0;$i<60;$i++)
      {
         echo "<option value='$i'>$i</option>\n";
      }
      echo "</select> \n";

      echo "kaj <select name='agxogxis' size='1'>\n";      
      for ($i=1;$i<60;$i++)
      {
         echo "<option value='$i'>$i</option>\n";
      }
      echo "<option selected value='200'>&infin;</option>";
  echo "</select>  <BR>\n";
  
  echo "<br><b>hejmlando:</b>";
  entajpbokso (" (",malellando,$malellando,J,J,"ne) el");
  echo "<select name='lando' size='1'>\n<option value=''>-</option>\n";
// "Select nomo,kategorio,ID from landoj order by nomo ASC"
$result = sql_faru(datumbazdemando(array("nomo", "kategorio", "ID"),
								   "landoj",
								   "",
								   "",
								   array("order" => "nomo ASC")));
    while ($row = mysql_fetch_array($result, MYSQL_BOTH))
    {
      echo "<option";
      $temp = "$row[nomo] ($row[kategorio])";
	   // TODO: $_SESSION["partoprenanto"] igxis "" je la komenco,
       //      ni do ne povas uzi gxin nun.
      if ($row[ID] == $_SESSION["partoprenanto"]->datoj[lando])
          echo " selected='selected'";
      echo " value = '{$row[2]}'>";
      eoecho ($temp)."</option>\n";
    }
    echo " </select> ";
 
  echo "<table><tr><td><b>landokategorio:</b>";


$kotsistemo = $_SESSION['renkontigxo']->donu_kotizosistemon();
$lkatsisID = $kotsistemo->datoj["landokategorisistemo"];

$rez = sql_faru(datumbazdemando(array("ID", "nomo"),
                                "landokategorioj",
                                "sistemoID = '" .$lkatsisID."'"
                                ));
$i = 0;
while($linio = mysql_fetch_assoc($rez)) {
    if ($i % 3 == 0 and $i > 0) {
        echo "</tr>\n<tr><td/>";
    }
    entajpbutono("<td>", 'landoKat', $landoKat, $linio['ID'], $linio['ID'],
                 $linio['nomo'] . "</td>");
    $i++;
 }
while($i % 3 != 0) {
    echo "<td/>";
    $i++;
 }
entajpbutono ("<td>",'landoKat',$landoKat,'?','?',"egalas</td></tr>","kutima");

echo "<tr><td><b>alvenstato:</b>";
$kutima = (date('Y-m-d') < $_SESSION['renkontigxo']->datoj['de']) ?
    'v' : 'a';
$i = 0;
foreach($GLOBALS['alvenstatonomoj'] AS $id => $nomo) {
    entajpbutono("<td>",'alvenstato',$alvenstato,$id, $id, $nomo,
                 $id == $kutima ? "kutima" : "", "</td>");
    $i++;
    if ($i % 3 == 0) {
        echo "</tr>\n<tr><td/>";
    }
}
entajpbutono ("<td/><td>",'alvenstato',$alvenstato,'?','?',"egalas");
  
  echo "<tr><td><b>traktstato:</b>";    
  entajpbutono ("<td>",traktstato,$traktstato,N,N,normala);
  entajpbutono ("<td>",traktstato,$traktstato,S,S,"speciala");
  entajpbutono ("<td>",traktstato,$traktstato,P,P,problema);
  entajpbutono ("<td>",traktstato,$traktstato,a,ambaux,"egalas","kutima");
  
echo "<tr><td><b>havas Asekuron:</b>";
entajpbutono("<td>", 'havasAsekuron', $havasasekuron, 'J', 'J', '"Jes"');
entajpbutono("<td>", 'havasAsekuron', $havasasekuron, 'N', 'N', '"Ne"');
entajpbutono("<td>", 'havasAsekuron', $havasasekuron, 'D', '', '(forgesis diri)');
entajpbutono("<td>", 'havasAsekuron', $havasasekuron, 'a', 'ambaux', 'egalas', 'kutima');
// eoecho("<br/>(ankorau^ ne funkcias - PE)");

  echo "<tr><td><b>asekuri:</b>";    
  entajpbutono ("<td>",asekuri,$asekuri,N,N,ne);
  entajpbutono ("<td>",asekuri,$asekuri,J,J,jes);
  entajpbutono ("<td>",asekuri,$asekuri,E,E,eble);
  echo "</tr><tr><td/>";
  entajpbutono ("<td>",asekuri,$asekuri,D,'',decidu); 
  entajpbutono ("<td>",asekuri,$asekuri,a,ambaux,"egalas","kutima");
  
  eoecho ("<tr><td><b>havas Mang^kuponon:</b>");
  entajpbutono ("<td>",mangxkupo,$mangxkupo,'J','J',jes);
  entajpbutono ("<td>",mangxkupo,$mangxkupo,'N','N',ne);
  entajpbutono ("<td>",mangxkupo,$mangxkupo,'P','P','printita');
  entajpbutono ("<td>",mangxkupo,$mangxkupo,a,ambaux,"egalas","kutima");
  
  eoecho ("<tr><td><b>havas Noms^ildon:</b>");
  entajpbutono ("<td>",nomsxildo,$nomsxildo,'J','J',jes);
  entajpbutono ("<td>",nomsxildo,$nomsxildo,'N','N',ne);
  entajpbutono ("<td>",nomsxildo,$nomsxildo,'P','P','printita');
  entajpbutono ("<td>",nomsxildo,$nomsxildo,a,ambaux,"egalas","kutima");
  
  
  echo "<BR><tr><td><b>sekso:</b>";
  entajpbutono ("<TD>",sekso,$sekso,i,ino,ino);
  entajpbutono ("<TD>",sekso,$sekso,v,viro,viro);
  entajpbutono ("<TD>",sekso,$sekso,a,ambaux,"ambau^","kutima");
  
  echo "<tr><td><b>novuloj:</b>";
  entajpbutono ("<td> ",komencanto,$komencanto,'=','=',jes);
  entajpbutono ("<Td>",komencanto,$komencanto,'<>','<>',ne);
  entajpbutono ("<TD>",komencanto,$komencanto,a,ambaux,"egalas","kutima");

  eoecho( "<tr><td><b>GEJ/GEA membro:</b> (lau^ alig^ilo)");
  entajpbutono ("<TD>",gejmembro,$gejmembro,J,J,jes);
  entajpbutono ("<TD>",gejmembro,$gejmembro,n,n,ne);
  entajpbutono ("<TD>",gejmembro,$gejmembro,a,ambaux,"egalas","kutima");

  eoecho( "<tr><td><b>GEJ/GEA membro:</b> (lau^ kontrolado)");
  entajpbutono ("<TD>",'surlkotizo',$surlkotizo,'j','j',"jam estas, kaj pagas");
  entajpbutono ("<TD>",'surlkotizo',$surlkotizo,'i','i',"ig^as, kaj pagas");
  entajpbutono ("<TD>",'surlkotizo',$surlkotizo,'h','h',"ig^as, kaj ne pagas");
  entajpbutono ("<TD>",'surlkotizo',$surlkotizo,'n','n',"ne membras, ne pagas ");
  echo "</tr><tr><td/>";
  entajpbutono ("<TD>",'surlkotizo',$surlkotizo,'a','a',"jam membro, ne pagas");
  entajpbutono ("<TD>",'surlkotizo',$surlkotizo,'k','k',"ne membro, krompagas");
  entajpbutono ("<TD>",'surlkotizo',$surlkotizo,'?','?',"ankorau^ decidenda");
  entajpbutono ("<TD>",'surlkotizo',$surlkotizo,'-','-',"egalas", "kutima");

  eoecho( "<tr><td><b>TEJO-membro:</b> (lau^ alig^ilo)");
  entajpbutono ("<TD>",tejomembrolaux,$tejomembrolaux,'j','j',"jes");
  entajpbutono ("<TD>",tejomembrolaux,$tejomembrolaux,'n','n',"ne");
  entajpbutono ("<TD>",tejomembrolaux,$tejomembrolaux,'','',"(mankas)");
  entajpbutono ("<TD>",tejomembrolaux,$tejomembrolaux,'a','a',"egalas","kutima");

  echo "<tr><td><b>TEJO-membro</b> (post kontrolo)";
  entajpbutono ("<TD>",tejomembropost,$tejomembropost,'j','j',"jam estas");
  entajpbutono ("<TD>",tejomembropost,$tejomembropost,'?','?',"ne kontrolita");
  entajpbutono ("<TD>",tejomembropost,$tejomembropost,'i','i',"ig^as/pagas surloke");
  echo "</tr><tr><td/>";
  entajpbutono ("<TD>",tejomembropost,$tejomembropost,'p','p',"pagas ion al UEA/TEJO sen membr(ig^)i");
  entajpbutono ("<TD>",'tejomembropost',$tejomembropost,'-','-',"egalas", "kutima");



  echo "<tr><td><b>KKRen:</b>";
  entajpbutono ("<TD>",KKRen,$KKRen,J,J,jes);
  entajpbutono ("<TD>",KKRen,$KKRen,n,n,ne);
  entajpbutono ("<TD>",KKRen,$KKRen,a,ambaux,"egalas","kutima");
  
  eoecho( "<tr><th>Mang^maniero:</th>");
  entajpbutono ("<td>",vegetare,$vegetare,'J','J', "Vegetarano");
  entajpbutono ("<td>", 'vegetare', $vegegate, 'A', 'A', "Vegano");
  entajpbutono ("<td>",vegetare,$vegetare,'N','N',"Viandmang^anto");
  entajpbutono ("<td>",vegetare,$vegetare,'?','?',"egalas","kutima");
    
  echo "<tr><td><b>partopreno:</b>";
  entajpbutono ("<td>",partoprentipo,$partoprentipo,t,t,"t-tempe");
  entajpbutono ("<td>",partoprentipo,$partoprentipo,p,p,"p-tempe");
  entajpbutono ("<td>",partoprentipo,$partoprentipo,a,ambaux,"egalas","kutima");
  
  entajpbokso  ("<tr><td><td>",kuncxambroj,$kuncxamrboj,J,J,"kun c^ambroj");
  if (rajtas("cxambrumi"))
  {
    entajpbokso  ("<td>",tutacxambro,$tutacxambro,T,T,"kun la tutaj c^ambroj");
  }
 entajpbokso  ("<tr><td><td>",kunadreso,$kunadreso,J,J,"kun adreso"); 
  echo "<tr><td><b>domtipo:</b>";
  entajpbutono ("<td>",domotipo,$domotipo,J,J,"J-ejo");
  entajpbutono ("<td>",domotipo,$domotipo,M,M,"M-ejo");
  entajpbutono ("<td>",domotipo,$domotipo,a,ambaux,"egalas","kutima");
  
  echo "<tr><td><b>cxambrotipo:</b>";
  entajpbutono ("<td>",cxambrotipo,$cxambrotipo,u,u,"unuseksa");
  entajpbutono ("<td>",cxambrotipo,$cxambrotipo,g,g,"gea");
  entajpbutono ("<td>",cxambrotipo,$cxambrotipo,a,ambaux,"ambau^","kutima");
  
  eoecho ("<tr><td><b>dulita c^ambro:</b>");
  entajpbutono ("<td> ",dulita,$dulita,'J','J',jes);
  entajpbutono ("<Td>",dulita,$dulita,'N','N',ne);
entajpbutono("<td>", 'dulita',$dulita,'U','U',"unulita");
  entajpbutono ("<TD>",dulita,$dulita,a,ambaux,"egalas","kutima");

  eoecho ("<tr><td><b>kunmang^as:</b>");
  entajpbutono ("<td>",kunmangxas,$kunmangxas,J,J,"jes (sen krompago)");
entajpbutono("<td>", 'kunmangxas',$kunmangxas,K,K,"krompagas");
  entajpbutono ("<td>",kunmangxas,$kunmangxas,N,N,"ne");
  entajpbutono ("<td>",kunmangxas,$kunmangxas,a,ambaux,"egalas","kutima");
  
  eoecho ("<tr><td><b>mendis ekskursbileton:</b>");
  entajpbutono ("<td>",ekskursbileto,$ekskursbileto,J,J,"jes");
  entajpbutono ("<td>",ekskursbileto,$ekskursbileto,n,n,"ne");
  entajpbutono ("<td>",ekskursbileto,$ekskursbileto,a,ambaux,"egalas","kutima");
  
  echo "<tr><td><b>invitletero:</b>";
  entajpbutono ("<td>",invitletero,$invitletero,'=','=',jes);
  entajpbutono ("<td>",invitletero,$invitletero,'<>','<>',ne);
  entajpbutono ("<td>",invitletero,$invitletero,a,ambaux,"egalas","kutima");
  
  echo "<tr><td><b>retkonfirmilo:</b>";
  entajpbutono ("<td>",retakonfirmilo,$retakonfirmilo,'=','=',jes);
  entajpbutono ("<td>",retakonfirmilo,$retakonfirmilo,'<>','<>',ne);
  entajpbutono ("<td>",retakonfirmilo,$retakonfirmilo,a,ambaux,"egalas","kutima");

  echo "<tr><td><b>germanakonfirmilo:</b>";
  entajpbutono ("<td>",germanakonfirmilo,$retakonfirmilo,'=','=',jes);
  entajpbutono ("<td>",germanakonfirmilo,$retakonfirmilo,'<>','<>',ne);
  entajpbutono ("<td>",germanakonfirmilo,$retakonfirmilo,a,ambaux,"egalas","kutima");

  
  echo "<tr><td><b>kontrolata:</b>";
  entajpbutono ("<td>",kontrolata,$kontrolata,'=','=',jes);
  entajpbutono ("<td>",kontrolata,$kontrolata,'<>','<>',ne);
  entajpbutono ("<td>",kontrolata,$kontrolata,a,ambaux,"egalas","kutima");
  
  echo "<tr><td><b>1akonfirmilo sendata:</b>";
  entajpbutono ("<td>",konf1a,$konf1a,'<>','<>',jes);
  entajpbutono ("<td>",konf1a,$konf1a,'=','=',ne);
  entajpbutono ("<td>",konf1a,$konf1a,a,ambaux,"egalas","kutima");

  echo "<tr><td><b>2akonfirmilo sendata:</b>";
  entajpbutono ("<td>",konf2a,$konf2a,'<>','<>',jes);
  entajpbutono ("<td>",konf2a,$konf2a,'=','=',ne);
  entajpbutono ("<td>",konf2a,$konf2a,a,ambaux,"egalas","kutima");
   
  echo "<tr><td><b>kontribuoj:</b>";
  entajpbokso  ("<td>",tema,$tema,J,J,"teme");
  entajpbokso  ("<td>",distra,$distra,J,J,"distre");
  entajpbokso  ("<td>",vespera,$vespera,J,J,"vespere");
  entajpbokso  ("<td>",muzika,$muzika,J,J,"muzike");
  
  echo "</table>";
  
  eoecho ("<BR><b>Montri kiel:</b><BR>");
  ?>
  <input type="radio" name="csv" value="0" checked> tabulo<br>
  <input type="radio" name="csv" value="1"> csv<br>
  <input type="radio" name="csv" value="2"> csv por preni
<?php

  eoecho ("<BR>");
  send_butono("Serc^u!");
  echo "</FORM>\n<br><hr/>";

eoecho ("<h3 id='specialaj'>Specialaj serc^oj</h3>\n");


  eoecho ("<h4>Antau^pagoj kaj rabatoj:</h4>");
  ligu("sercxrezultoj.php?elekto=antauxpagoj","-> c^iu antau^pago");
  ligu("sercxrezultoj.php?elekto=rabatoj","-> c^iu rabato");

  eoecho ("<BR><b>Notojn:</b><BR>");
  ligu("sercxrezultoj.php?elekto=laborontajnotoj&montro=aktuala",
       "-> remontrendajn notojn (remontro-dato estinte)");
  ligu("sercxrezultoj.php?elekto=laborontajnotoj&montro=nur",
       "-> nur malfruajn notojn (remontro-dato estonte)");
  ligu("sercxrezultoj.php?elekto=laborontajnotoj&montro=inkl",
       "-> c^iujn neprilaboritajn notojn");
  echo"<BR>";
  ligu("sercxrezultoj.php?elekto=rimarkoj",
       "-> vidi la rimarkojn de la partoprenantoj");

  eoecho ("<h4>Diversaj^ojn:</h4>\n");

ligu("sercxrezultoj.php?elekto=kotizokomparo",
     "Komparo de nova kaj malnova kotizokalkulado");
echo "<br/>";

  rajtligu("sercxrezultoj.php?elekto=kunlogxantoj","-> c^iu kiu deziras kunlog^adon","",'cxambrumi','ne');
  eoecho ("<BR>c^iu junulargasto por la c^ambrodisdonado: ");
  ligu("sercxrezultoj.php?elekto=cxambrodisdonado&AB=nur","AB kun antau^pago");
  //ligu("sercxrezultoj.php?elekto=cxambrodisdonado&AB=C","nur C");
  //ligu("sercxrezultoj.php?elekto=cxambrodisdonado","c^iu");
  echo "<BR>";
  ligu("sercxrezultoj.php?elekto=skribuagxon","kalkulu kaj skribu la ag^ojn de la partoprenantoj");
  echo "<BR>";
  ligu("sercxrezultoj.php?elekto=profesioj","montru la profesiojn de la partoprenantoj");
  ligu("sercxrezultoj.php?elekto=francoj", "eksportu la francajn partoprenantojn");
  ligu("sercxrezultoj.php?elekto=junulargastejolisto", "eksportu liston por la junulargastejo en Wetzlar");
  ligu("sercxrezultoj.php?elekto=andiListe", "eksportu liston por ministerio");
  ligu("sercxrezultoj.php?elekto=cxambrolisto", "montru liston de la cxambroj kaj enlogxantoj");
  ligu("sercxrezultoj.php?elekto=germanoj_laux_lando&dosiertipo=0", "Germanoj lau^ lando");
  ligu("sercxrezultoj.php?elekto=germanoj_laux_lando&dosiertipo=1", "1");
  ligu("sercxrezultoj.php?elekto=germanoj_laux_lando&dosiertipo=2", "2");
  ligu("sercxrezultoj.php?elekto=germanoj_laux_lando&dosiertipo=3", "3");
  echo "<BR>";
  ligu("sercxrezultoj.php?elekto=nenula_saldo", "Partoprenantoj kun ne-nula pago-saldo");
  echo "<BR>";
  rajtligu("sercxrezultoj.php?elekto=restaspagenda","kiom pagendas por c^iu?","","mono",'ne');
  ligu("sercxrezultoj.php?elekto=kunmangxo",
	   "Kontrolu, c^u c^ie kunmang^ado = junulargastejumado");

ligu('sercxrezultoj.php?elekto=partoprenintoj_por_enketo',
     "Listo de bazaj datoj de partoprenintoj por enketaj celoj (CSV)");
echo "<br />";
ligu('sercxrezultoj.php?elekto=aligxintoj_laux_kategorioj',
     "G^enerala alig^into-statistiko de la lastaj jaroj (sen ag^o)");
ligu('sercxrezultoj.php?elekto=aligxintoj_laux_kategorioj&csv=1',
     "(CSV por kopiado)");
ligu('sercxrezultoj.php?elekto=aligxintoj_laux_kategorioj&csv=2',
     "(CSV por els^uti)");
echo "<br />";
ligu('sercxrezultoj.php?elekto=aligxintoj_laux_kotizokategorioj',
     "G^enerala alig^into-statistiko de la lastaj jaroj (kun ag^oj");
ligu('sercxrezultoj.php?elekto=aligxintoj_laux_kotizokategorioj&csv=1',
     "(CSV por kopiado)");
ligu('sercxrezultoj.php?elekto=aligxintoj_laux_kotizokategorioj&csv=2',
     "(CSV por els^uti)");
  HtmlFino();
?>
