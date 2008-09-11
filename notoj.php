<?php 

  /**
   * Kreado kaj redaktado de notoj pri Partoprenantoj.
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
 
// TODO: Traduki komentojn (kaj pli grave: tekston) el la germana
// TODO: uzu la funkciojn el iloj_html anstataŭ pura HTML-input-elementojn.

/*if (!rajtas("noti"))
{
  ne_rajtas();
}*/


/**
 * metas noton en la datumbazon, aŭ kreante aŭ ŝanĝante.
 * Poste ni redonas la noto-objekton.
 *
 * @return Noto la noto-objekto kreita aŭ ŝanĝita.
 */
function savu_Noton()
{
    $noto = new Noto($_REQUEST['ID']);
    echo( "<!-- POST: " . var_export($_POST, true) . "-->");
    $noto->kopiu();
    $noto->skribu_kreante_se_necesas();
    $noto->sxangxu_entajpantojn_por_noto($_POST['noto_por']);
    eoecho( "<p>Savis noton #" . $noto->datoj['ID'] . ".</p>\n");

    return $noto;
}


/**
 * kreas novan noto-objekton preparitan por noto pri partoprenanto.
 *
 * @param Partoprenanto $partoprenanto
 *
 * @return Noto la kreita noto.
 * @todo ĉu rete estas ĉiam taŭga defaŭlto por la tipo?
 * eble tiu dependu de la moduso (ie surloke estu "persone").
 */
function novaNoto($partoprenanto) {
    $noto = new Noto();
    $noto->datoj['kiu'] = $_SESSION['kkren']['entajpantonomo'];
    $noto->datoj['kunKiu'] = $partoprenanto->tuta_nomo();
    $noto->datoj['partoprenantoID'] = $partoprenanto->datoj['ID'];
    $noto->datoj['tipo'] = 'rete';
    $nun = date("Y-m-d H:i:s");
    //    echo "<!-- nun: " . $nun . " -->";
    $noto->datoj['revidu'] = $nun;
    $noto->datoj['dato'] = $nun;

    return $noto;
}


/**
 * Montras formularon por krei/ŝanĝi noton.
 *
 * @param Partoprenanto $partoprenanto la Partoprenanto, al kiu rilatas
 *                                     la noto.
 * @param Noto          $noto          la noto-objekto ŝanĝenda.
 */
function montru_notoformularon($partoprenanto, $noto)
{
    if ($noto->datoj['ID']) {
        eoecho( "<h2>S^ang^o de noto</h2>\n");
    }
    else {
        eoecho ("<h2>Nova noto</h2>\n");
    }
    echo "<form method='post' action='notoj.php'>\n";
    echo "<table>\n";
    tabela_kasxilo("Noto-ID", 'ID', $noto->datoj['ID']);
    tabela_kasxilo("Ppanto-ID", 'partoprenantoID',
                   $partoprenanto->datoj['ID'],
                   $partoprenanto->tuta_nomo() . " (" .
                   donu_ligon("partrezultoj.php?partoprenantoidento=" .
                              $partoprenanto->datoj['ID'],
                              "#" . $partoprenanto->datoj['ID']) . ")");
    tabel_entajpbutono("Tipo", 'tipo', $noto->datoj['tipo'], 'telefon',
                       "telefona kontakto", '', true);
    tabel_entajpbutono("",     'tipo', $noto->datoj['tipo'], 'persone',
                       "persona kontakto", '', true);
    tabel_entajpbutono("",     'tipo', $noto->datoj['tipo'], 'letere',
                       "letera kontakto", '', true);
    tabel_entajpbutono("",     'tipo', $noto->datoj['tipo'], 'rete',
                       "ret(pos^t)a kontakto", '', true);
    tabel_entajpbutono("",     'tipo', $noto->datoj['tipo'], 'rimarko',
                       "alia rimarko", '', true);

    tabelentajpejo("dato/tempo", 'dato', $noto->datoj['dato'], 20);
    tabelentajpejo("noto de ...", 'kiu', $noto->datoj['kiu'], 45);
    tabelentajpejo("pri komunikado kun ...", 'kunKiu',
                   $noto->datoj['kunKiu'], 45);

    eoecho("<tr><th>noto por:</th><td>");
    $entajpantoj = $noto->listu_entajpantojn();
    foreach ($entajpantoj AS $id => $inf) {
        echo("<span style='display: inline-block;'>");
        jes_ne_bokso('noto_por[' . $id . ']', $inf[1]);
        eoecho($inf[0] . "</span>\n  ");
    }
    eoecho("</td></tr>");

    tabelentajpejo("temo", 'subjekto', $noto->datoj['subjekto'], 45);

    granda_tabelentajpejo("teksto", 'enhavo',
                          $noto->datoj['enhavo'],
                          57, 20);
    tabela_elektilo("prilaborita", 'prilaborata',
                    array("j" => 'jes',
                          '' => 'ne'),
                    $noto->datoj['prilaborata'], " (se ne, remontru je ..." );
    tabelentajpejo("", 'revidu', $noto->datoj['revidu'], 20, ")");

    echo "</table>\n<p>";

    if ($noto->datoj['ID']) {
        butono("notu", "S^ang^u la noton!");
    }
    else {
        butono("notu", "Nova noto!");
    }

    ligu("sercxrezultoj.php?elekto=notojn&partoprenantoidento=" .
         $partoprenanto->datoj['ID'],
         "C^iuj notoj de " . $partoprenanto->tuta_nomo() );

    ligu("partrezultoj.php?partoprenantoidento=" . $partoprenanto->datoj['ID'],
         "Partoprenanto-detaloj");

    echo "</p>\n</form>\n";
}


// ------------------------------------------------------------

/* jen la agado */


HtmlKapo();

sesio_aktualigu_laux_get();



if ($_POST['sendu'] == 'notu') {
    $noto = savu_noton();
 }
 else if ($_REQUEST['notoID']) {
     $noto = new Noto($_REQUEST['notoID']);
     sesio_aktualigu_ppanton($noto->datoj['partoprenantoID']);
 }
 else if ($_REQUEST['wahlNotiz']) {
     $noto = new Noto($_REQUEST['wahlNotiz']);
     sesio_aktualigu_ppanton($noto->datoj['partoprenantoID']);
 }
 else {
     $noto = novaNoto($_SESSION['partoprenanto']);
     sesio_aktualigu_ppanton($noto->datoj['partoprenantoID']);
 }

montru_notoFormularon($_SESSION['partoprenanto'],
                      $noto);



exit();

// ------------------------------------------------------------
// la resto estas malnova kodo, forigenda post kontrolo, ke ĉio funkcias.

if (isset($NotizAbschicken)) 
{ 
  //sql_faru("LOCK TABLES notoj WRITE;"); 
    $erfolg = $_SESSION["notiz"]->kopiu(); 
    if ( $_SESSION["notiz"]->datoj[ID] == "" ) 
    { 
      $_SESSION["notiz"]->kreu(); 
    } 
     
    //if ($erfolg) //ist noch die Transaktionssicherheit aus dem Inova System
    { 
      //$notiz->datoj[enhavo] = eotransformado($notiz->datoj[enhavo],'unikodo');
     
      $_SESSION["notiz"]->skribu(); 
      echo "<font color=red> noton sekurata</font>"; 
      //$notiz->montru();
      $_SESSION["notiz"] = new Noto( $_SESSION["notiz"]->datoj[ID] );
    } 
  //sql_faru("UNLOCK TABLES;"); 
} 

// TODO: versxajne ne necesa (por alia sistemo) 
// --> estas uzata en diversaj ligoj. Sed la firmon
//     ni ne bezonas, mi kredas (PE).
if ( isset($wahlNotiz) ) 
{ 
  $_SESSION["notiz"] = new Noto($wahlNotiz); 
  $ausgewaehlteFirma = $_SESSION["notiz"]->datoj[FirmenID];
} 
else if (isset($elekto))
{ 
    // nova noto

    // TODO: cxu ni vere bezonas la objekton en la sesio?

  $_SESSION["notiz"] = new Noto(0); 
  // "select personanomo,nomo from partoprenantoj where ID='$partoprenantoidento' "


//     $row2 = mysql_fetch_array (sql_faru(datumbazdemando(array("personanomo", "nomo"),
// 													  "partoprenantoj",
// 													  "id = '$partoprenantoidento'")),
// 							 MYSQL_ASSOC);

  
 
  $_SESSION["notiz"]->datoj[kiu] = $_SESSION["kkren"]["entajpantonomo"];
  $_SESSION["notiz"]->datoj[kunKiu] = $partoprenanto->tuta_nomo();
  $_SESSION["notiz"]->datoj[partoprenantoID] = $partoprenanto->datoj['ID'];
} 


 
eoecho ("<h3>Noto pri ");

ligu("partrezultoj.php?partoprenantoidento=". $partoprenanto->datoj['ID'],
     $partoprenanto->tuta_nomo() ."(#" . $partoprenanto->datoj['ID']. ")");
echo ("</h3>\n");


?> 
 
<form name="notizen" method="post" action="notoj.php"> 
<table border="0" align="center"> 
   <tr> 
      <td width="40%" valign="middle" class="text"> 
        <div align="right">tipo:&nbsp;</div> 
      </td> 
      <td width="60%" valign="middle" class="text"> 
      <?php
        entajpbutono("",tipo,$_SESSION["notiz"]->datoj[tipo],
                     "telefon",telefon," telefone<BR>");
        entajpbutono("",tipo,$_SESSION["notiz"]->datoj[tipo],
                     "persone",persone," persone<BR>");
        entajpbutono("",tipo,$_SESSION["notiz"]->datoj[tipo],
                     "letere",letere," letere<BR>");
        entajpbutono("",tipo,$_SESSION["notiz"]->datoj[tipo],
                     "rete",rete," rete<BR>","kutima");
        entajpbutono("",tipo,$_SESSION["notiz"]->datoj[tipo],
                     "rimarko",rimarko," alia rimarko<BR>");
        ?>        
      </td> 
    </tr> 
 
    <tr> 
      <td width="40%" valign="middle" class="text"> 
        <div align="right">dato kaj tempo:</div> 
      </td> 
      <td width="60%" valign="middle" class="text"> 
        <input type="text" name="dato" value="<?php
 if ($_SESSION["notiz"]->datoj[dato]!="") { print $_SESSION["notiz"]->datoj[dato];}else echo date("Y-m-d H:i:s");?>" size="20"> 
      </td> 
    </tr> 
    <tr> 
      <td width="40%" valign="middle" class="text"> 
        <div align="right">noto de:</div> 
      </td> 
      <td width="60%" valign="middle" class="text"> 
        <input type="text" name="kiu" value="<?php print htmlspecialchars($_SESSION["notiz"]->datoj[kiu], ENT_QUOTES); ?>" size="45"> 
      </td> 
    </tr> 
 
    <tr> 
      <td width="40%" valign="middle" class="text"> 
        <div align="right">komunikpartnero:</div> 
      </td> 
      <td width="60%" valign="middle" class="text"> 
        <input type="text" name="kunKiu" value="<?php print htmlspecialchars(($_SESSION["notiz"]->datoj[kunKiu]), ENT_QUOTES); ?>" size="45">
      </td> 
    </tr> 
 
    <tr> 
      <td width="40%" valign="middle" class="text"> 
        <div align="right">subjekto:</div> 
      </td> 
      <td width="60%" valign="middle" class="text"> 
        <input type="text" name="subjekto" value="<?php echo htmlspecialchars($_SESSION["notiz"]->datoj[subjekto], ENT_QUOTES); ?>" size="45"> 
      </td> 
    </tr> 
 
    <tr> 
      <td width="40%" valign="middle" class="text"> 
        <div align="right">enhavo:</div> 
      </td> 
      <td width="60%" valign="middle" class="text"> 
            <textarea name="enhavo" cols="57" rows="20" wrap="soft"><?php echo $_SESSION["notiz"]->datoj[enhavo]; ?></textarea> 
      </td> 
    </tr> 
 
    <tr> 
      <td align=right valign="bottom" >prilaborita: 
      <td width="40%" valign="bottom" class="text"> 
 
            <input type="checkbox" name="prilaborata" value="j" <?php
      if ($_SESSION["notiz"]->datoj[prilaborata]=="j")
          print 'checked';
            ?> > 
      <?php $_SESSION["notiz"]->datoj[prilaborata] = "";
/*unschön, aber nötig  - TODO: Pli bona maniero! */
        eoecho ("au^ revidu g^in je la:"); ?>
        <input type="text" name="revidu" value="<?php if ($_SESSION["notiz"]->datoj[revidu]!=""){print $_SESSION["notiz"]->datoj[revidu];}else echo date("Y-m-d H:i:s");?>" size="20"> 
<?php //       <img src="images/info.gif" onClick="alert('Nicht als erledigt markierte Notiz wird erst ab Datum für Wiedervorlage\nwieder in der Suchabfrage für unerledigte Notizen angezeigt.')"> 
    ?>  </td> 
    </tr> 
 
 
    <tr> 
      <td width="40%" valign="middle" class="text"> 
        &nbsp; 
      </td> 
      <td width="60%" valign="middle" class="text"> 
        <input type="submit" value="Savu!" name="NotizAbschicken"> 
      </td> 
    </tr> 
  </table> 
</form> 
</body> 
</html> 
