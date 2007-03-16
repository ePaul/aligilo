<?php

/**
 *
 * Ebleco preni tekstojn laux renkontigxo
 * el la datumbazo.
 *
 **********************************************

 CREATE TABLE tekstoj (
  ID			int(10)	 NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'eindeutige Nummer',
  renkontigxoID int(10)	 NOT NULL COMMENT 'zu welchen Renkontigxo gehört der Text?',
  mesagxoID		char(20) NOT NULL COMMENT 'zum Finden des Textes im Programm',
  teksto		text	 NOT NULL COMMENT 'der Text selbst.',
  UNIQUE (renkontigxoID, mesagxoID)
 ) COMMENT = 'tabelo por lokaligo de tekstoj (-> tekstoj.php)'

 ********************
 *
 *  Signifoj de la mesagxoID:
 * --------------------------
 *
 * vidu http://www.esperanto.de/dej/vikio.pl?IS-Datenbazo/Tekstoj
 *
 */



/**
 * Donas tekston el la datumbazo.
 *
 * $identifikilo  - la mesagxidentifikilo (litercxeno).
 *                  pri la signifoj rigardu pli supre en
 *                  la dokumentado de la dosiero.
 *
 * $renkontigxo   - objekto de la klaso Renkontigxo (-> objektoj).
 *                  Ni sercxas la tekston por tiu renkontigxo.
 *
 *                  Vi povas ankaux forlasi gxin aux uzi "",
 *                  tiam la metodo uzas la sesio-variablo
 *                  $renkontigxo (se ekzistas) aux la globala
 *                  variablo $renkontigxo
 *
 * Se la teksto ne ekzistas, la metodo anstatauxe 
 * redonas erarmesagxon ("la teksto ... ne trovigxis.")
 */
function donu_tekston($identifikilo, $renkontigxo="")
{
  if ($renkontigxo == "")
	{
	  if ($_SESSION["renkontigxo"])
		$renkontigxo = $_SESSION["renkontigxo"];
	  else
		$renkontigxo = $GLOBALS["renkontigxo"];
	}

  $sql = datumbazdemando("teksto",
						 "tekstoj",
						 array("mesagxoID = '$identifikilo'",
							   "renkontigxoID = '" . $renkontigxo->datoj["ID"] . "'")
						 );
  $rez = mysql_fetch_array(sql_faru($sql));
  if (empty($rez))
	return "[Text '$identifikilo' fehlt leider fÃ¼r Treffen " .
	  $renkontigxo->datoj["mallongigo"] . ". Bitte bei ".teknika_administranto." beschweren!]";
  else
	return $rez["teksto"];
}

function donu_tekston_lauxlingve($identifikilo, $lingvo, $renkontigxo="")
{
  if ($lingvo != "eo")
	{
	  return donu_tekston($identifikilo. "_" .$lingvo, $renkontigxo);
	}
  else
	{
	  return donu_tekston($identifikilo, $renkontigxo);
	}
}


//holt alle Einzahler aus den Texten und zeigt sie an.
function montru_elekto_liston($teksto_id,$pago_tipo,$butono_nomo,$kutima_teksto='')
{
  $antauxpaguloj = donu_tekston($teksto_id);
  
  $antauxpaguloj = explode("\r\n",$antauxpaguloj);

  echo "<BR><BLOCKQUOTE><p>";
  
  $uloj = array();
  
  foreach($antauxpaguloj as $linio)
    {
      // echo "hallo:".$ulo."||";
      if ($linio[0]=='#') continue;
      
      if ($linio[0]=='-') {echo "</p>\n<p>";continue;}
      
      list($ulo, $teksto) = explode("|",$linio);
	  $uloj[] = $ulo;
	  if (!isset($teksto))
		$teksto = $kutima_teksto. $ulo;
	  
      entajpbutono("",$butono_nomo,$pago_tipo,$ulo,$ulo,$teksto."<br />\n");
    }
  if ($pago_tipo and !in_array($pago_tipo, $uloj))
	{
	  entajpbutono("<br/>", tipo,$pago_tipo, $pago_tipo, $pago_tipo,
				   "<b>malnova:</b> ".$pago_tipo."<br />\n");
	}

    echo "</p></BLOCKQUOTE>";
}


function anstatauxu($teksto, $sxangxoj)
{
  foreach($sxangxoj AS $sercxu => $per)
	{
	  $teksto = str_replace($sercxu, $per, $teksto);
	}
  return $teksto;
}


?>