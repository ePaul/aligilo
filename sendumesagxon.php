<?php
  require_once ('iloj/iloj.php');

  session_start();
  malfermu_datumaro();
  
  HtmlKapo();

if (!rajtas("retumi"))
{
  ne_rajtas();
}

 
$alkiu.=$_SESSION["partoprenanto"]->tuta_nomo();

?><form name="notoj" method="post" action="sendumesagxon.php">
<?php
eoecho ("Kian mesag^on vi volas sendi al $alkiu?<BR><BR>");



if (isset($ek))
{
  // "select ID,nomo,subjekto,korpo from retposxto where ID=$elektata");
  $result = sql_faru(datumbazdemando(array("ID", "nomo", "subjekto", "korpo"),
									 "retposxto",
									 "ID = '$elektata'"));
  $row = mysql_fetch_array($result, MYSQL_ASSOC);
  
  if (isset($korpo)) $row[korpo]=$korpo;
  
  $row[korpo]=str_replace('[partoprenantonomo]',$_SESSION["partoprenanto"]->datoj[personanomo],$row[korpo]);
  $row[korpo]=str_replace('[entajpantonomo]',$_SESSION["kkren"]["entajpantonomo"],$row[korpo]);
  $row[korpo]=str_replace('[renkontigxo]',$_SESSION["renkontigxo"]->datoj[nomo],$row[korpo]);
  $row[korpo]=str_replace('[loko]',$_SESSION["renkontigxo"]->datoj[loko],$row[korpo]);

  if (($ek=="Nur sendu!")or($ek=="Notu kaj sendu!"))
  {
      sendu_liberan_mesagxon($row[subjekto],stripslashes($row[korpo]),$alkiu,$_SESSION["partoprenanto"]->datoj[retposxto],array('test.pdf','martin.png'));
    echo "<font color=red>Mesagxo sendita<BR><BR></font>";
  }
  if (($ek=="Notu!")or($ek=="Notu kaj sendu!"))
  {
    kreunoton($_SESSION["partoprenanto"]->datoj[ID],$_SESSION["kkren"]["entajpantonomo"],$alkiu,$tipo="rete",$row[subjekto],$row[korpo],$prilaborata);
    //      $row[koerper],"j");
    echo "<font color=red>Noto skribata<BR><BR></font>";
  }

  echo "<b>retadreso:</b> ".$_SESSION["partoprenanto"]->datoj[retposxto]."<BR>";  
  echo nl2br("<b>subjekto:</b> ".$row[subjekto]."<BR><BR>");
  echo "<textarea name=\"korpo\" cols=\"57\" rows=\"20\" wrap=\"soft\">".stripslashes($row[korpo])."</textarea>";
  echo "<BR>";
  entajpbokso("","prilaborata","","j","j","prilaborata<BR>",'','ne');
  
  tenukasxe("elektata",$elektata);
  echo "<input type=submit name=ek value=\"Notu kaj sendu!\"><BR>";
  echo "<input type=submit name=ek value=\"Nur sendu!\"><BR>";
  echo "<input type=submit name=ek value=\"Notu!\"><BR>";
//  echo "<A href=\"sendumesagxon.php?elektata=".$elektata."&sendu=nun&ek=blup\"> Nur sendu!</A><BR>";
//  echo "<A href=\"sendumesagxon.php?elektata=".$elektata."&notugxin=faru&ek=blup\"> Notu!</A></form><BR><BR>";
}
else
{
  // select ID,nomo from retposxto order by nomo");
  $result = sql_faru(datumbazdemando(array("ID", "nomo"),
									 "retposxto",
									 "",
									 "",
									 array("order"=> "nomo")));

  ?>
   <form name="elektado" method="post" action="sendumesagxon.php">
   <select size="1" name="elektata">

   <?php
  while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
  {
  ?>
    <option value="<?php echo $row[ID]?>"><?php eoecho ($row[nomo])?></option>
  <?php
  }
  ?>
  </select>
   <input type="submit" value="faru" name="ek">
   </form>
<?php
}
?>
</body>
</html>

