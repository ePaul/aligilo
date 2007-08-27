<?php 
  require_once ('iloj/iloj.php');
  session_start();
  malfermu_datumaro();
 
// TODO: Traduki komentojn (kaj pli grave: tekston) el la germana
// TODO: uzu la funkciojn el iloj_html anstataux pura HTML-input-elementojn.

/*if (!rajtas("noti"))
{
  ne_rajtas();
}*/

  HtmlKapo();
?>
 
<body bgcolor="#DDEEFF"> 
 <?php
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
if ( isset($wahlNotiz) ) 
{ 
  $_SESSION["notiz"] = new Noto($wahlNotiz); 
  $ausgewaehlteFirma = $_SESSION["notiz"]->datoj[FirmenID];
} 
else if (isset($elekto))
{ 
  $_SESSION["notiz"] = new Noto(0); 
  // "select personanomo,nomo from partoprenantoj where ID='$partoprenantoidento' "
  $row2 = mysql_fetch_array (sql_faru(datumbazdemando(array("personanomo", "nomo"),
													  "partoprenantoj",
													  "id = '$partoprenantoidento'")),
							 MYSQL_ASSOC);
 
  $_SESSION["notiz"]->datoj[kiu] = $_SESSION["kkren"]["entajpantonomo"];
  $_SESSION["notiz"]->datoj[kunKiu] = ($row2['personanomo']." ".$row2['nomo']);
  $_SESSION["notiz"]->datoj[partoprenantoID] = $partoprenantoidento;
} 
 
?> 
 
<center><h3>Noto</h3></center> 
<form name="notizen" method="post" action="notoj.php"> 
<table border="0" align="center"> 
   <tr> 
      <td width="40%" valign="middle" class="text"> 
        <div align="right">tipo:&nbsp;</div> 
      </td> 
      <td width="60%" valign="middle" class="text"> 
      <?php
        entajpbutono("",tipo,$_SESSION["notiz"]->datoj[tipo],
                                 "telefon",telefon," telefono<BR>");
        entajpbutono("",tipo,$_SESSION["notiz"]->datoj[tipo],"persone",persone," persone<BR>");
        entajpbutono("",tipo,$_SESSION["notiz"]->datoj[tipo],"letere",letere," letere<BR>");
        entajpbutono("",tipo,$_SESSION["notiz"]->datoj[tipo],"rete",rete," rete<BR>","kutima");
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
        <input type="text" name="kunKiu" value="<?php print htmlspecialchars(($_SESSION["notiz"]->datoj[kunKiu], ENT_QUOTES)?>" size="45"> 
      </td> 
    </tr> 
 
    <tr> 
      <td width="40%" valign="middle" class="text"> 
        <div align="right">subjekto:</div> 
      </td> 
      <td width="60%" valign="middle" class="text"> 
        <input type="text" name="subjekto" value="<?php echo htmlspecialchars(($_SESSION["notiz"]->datoj[subjekto], ENT_QUOTES)?>" size="45"> 
      </td> 
    </tr> 
 
    <tr> 
      <td width="40%" valign="middle" class="text"> 
        <div align="right">enhavo:</div> 
      </td> 
      <td width="60%" valign="middle" class="text"> 
        <textarea name="enhavo" cols="57" rows="20" wrap="soft"><?php echo $_SESSION["notiz"]->datoj[enhavo]?></textarea> 
      </td> 
    </tr> 
 
    <tr> 
      <td align=right valign="bottom" >prilaborata: 
      <td width="40%" valign="bottom" class="text"> 
 
        <input type="checkbox" name="prilaborata" value="j"  <?php if ($_SESSION["notiz"]->datoj[prilaborata]=="j") print checked?> > 
        <?php $_SESSION["notiz"]->datoj[prilaborata] = ""; /*unschön, aber nötig*/  
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
