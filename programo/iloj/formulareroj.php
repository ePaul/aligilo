<?php

/*
 * pluraj helpaj funkcioj por krei formularojn.
 * Ili uzas parte la funkciojn el iloj_html.php,
 * parte tiujn el /is/dulingva/lib/dulingva.php.
 *
 */


/**
 * Se $GLOBALS["parto"] == "",
 *   faras nenion.
 * Alikaze, se $io != "",
 *   faras nenion.
 * Alikaze
 *   eldonas la (eble dulingvan) erarmesagxon
 * kaj metas $GLOBALS["parto"] al "korektigi".
 */
function malplentesto_geo ($io,$errDE, $errEO)
{
  if (($GLOBALS["parto"] != "") and ($io==""))
  {
    erareldono_geo ("Bitte gib dein " . $errDE . " an!",
                    "Bonvolu entajpu vian ".$errEO . "!");
    $GLOBALS["parto"]="korektigi";
  }
}



/**
 * Entajpejo kun (eble) dulingva teksto.
 * Gxi estas metita en apartan tabellinion:
 *
 * .----------.-----------------------------.
 * | teksto   | [________]   (postteksto)   |
 * |          | (erarmesagxoj)              |
 * '----------'-----------------------------'
 *
 *
 * $tekstoDE     - germana priskribo
 * $tekstoEO     - esperanta priskribo
 * $nomo         - name-atributo de la entajpejo
 *                 (kiu do estos poste la variabl-nomo
 *                  en la respondo al la servilo)
 * $io           - teksto, kiu estas jam de komence en
 *                 la entajpejo. Se estas "" (la defauxlto),
 *                 $kutima estas uzata.
 * $grandeco     - la grandeco de la entajpejo (size=...).
 * $mankoDE	    
 * $mankoEO	    
 *               - erarmesagxoj kiu montrigxu (kun "bonvolu entajpi vian"),
 *                 kiam $io estas "". (Se $manko="" (kio estas la defauxlto),
 *                 la malplentesto ne okazas, same, kiam la globala variablo
 *                 $parto estas "".)
 * $kutima       - la komenca teksto en la entajpejo, se
 *                  $io == "".
 * $posttekstoDE
 * $posttekstoEO
 *               - dulingva teksto, kiu estos montrita post la entajpejo, se
 *                 ne "". (Gxi estos montrata en krampoj "()".)
 */
function entajpejoB($tekstoDE, $tekstoEO, $nomo,$io="",$grandeco="",$mankoDE="", $mankoEO="",$kutima="",$posttekstoDE="", $posttekstoEO="")
{
  if (nurEsperante())
  {
    eoecho ("<tr><td class=\"green\"><b>" . $tekstoEO . "</b></td>");
  }
  else
  {
    eoecho("<tr><td class=\"green\"><b>" . $tekstoDE . "</b><br/><b>" . $tekstoEO . "</b></td>");
  }
  echo "<td class=\"green\"><input type=\"text\" name=\"$nomo\" size=\"$grandeco\" maxlength=\"40\" ";
  if ($io)
  {
    echo "value=\"$io\"";
  }
  else
  {
    echo "value=\"$kutima\"";
  }
  echo "/>";
  if ($posttekstoDE)
	geoecho (" (", $posttekstoDE . " / ", $posttekstoEO . ")");
  echo "<br />";
  if ($mankoEO)
	malplentesto_geo($io,$mankoDE, $mankoEO);
  echo "</td></tr>\n";
}



/**
 * eldonas komencan tekston, eble germanan tekston kaj finan tekston.
 * Cxie eblas skribi ^c por atingi la gxustan rezulton (&#265;).
 */
function geoecho($komenca, $germana, $fina)
{
   if(nurEsperante())
   {
     eoecho($komenca . $fina);
   }
   else
   {
     eoecho($komenca . $germana . $fina );
   }
}

// mi forigis la $teksto-parametron.


/**
 * ( ) bla
 */
function entajpbutonoB($nomo,$io,$komparo,$valoro,$posttekstoDE="",$posttekstoEO="",$kutima="")
{

  echo "<input name=\"$nomo\" type=\"radio\" ";
  if (($io==$komparo) or (!$io and $kutima=="kutima"))
  {
    echo "checked=\"checked\" ";
  }
  echo "value=\"$valoro\" />";
  geoecho ("", $posttekstoDE . " / ", $posttekstoEO);
}




function erareldono_geo($germana, $esperanta)
{
  if(nurEsperante())
  {
    erareldono($esperanta);
  }
  else
  {
    erareldono($germana . " / " . $esperanta);
  }
}

/**
 * .------+---------------.
 * | bla  |  [ ] blabla   |
 * '------+---------------'
 */
function entajpboksoB($titoloDE, $titoloEO,$nomo,$io,$komparo,$valoro,$posttekstoDE="", $posttekstoEO)
{
  geoecho ("<tr><td class=\"green\"><b>", $titoloDE."<br/>", $titoloEO. "</b></td>");
  echo "<td class=\"green\"> <input name=\"$nomo\" type=\"checkbox\" ";
  if ($io==$komparo) echo "checked=\"checked\" ";
  echo "value=\"$valoro\"/>";
  geoecho ("", $posttekstoDE . " / ", $posttekstoEO);
  echo "</td></tr>\n";
}

/**
 * [ ]  blabla
 */
function entajpbokseroB($nomo,$io,$komparo,$valoro,$posttekstoDE="",$posttekstoEO="",$kutima="")
{

  echo "<input name=\"$nomo\" type=\"checkbox\" ";
  if (($io==$komparo) or (!$io and $kutima=="kutima"))
  {
    echo "checked=\"checked\" ";
  }
  echo "value=\"$valoro\" />";
  geoecho ("", $posttekstoDE . " / ", $posttekstoEO);
}



/**
 * [ ] bla: [_____________]
 *
 * kasxe: se $kasxe != "" aperos kasxita dato-transdonilo, kiu
 *        funkcias se la bokso ne laboras. la valoro donita estas
 *        $kasxe.
 */
function entajpboksokajejoB($boxnomo,$boxio,$boxkomparo,$boxvaloro,$tekstoDE,$tekstoEO,$ejnomo,$ejio,$grandeco,$mankoDE,$mankoEO,$kasxe="")
{
  if ($ejio)
  {
    $boxio = "JES";
  }
  eoecho ("<tr>");
  echo "<td> ";
  if ($kasxe)
	echo " <input name='$boxnomo' type='hidden' value='{$kasxe}'>\n";//necesas

echo "<input name='$boxnomo' type='checkbox' ";
  if ($boxio==$boxkomparo) echo "checked='checked' ";
  echo "value='$boxvaloro'/>";

  geoecho ("", $tekstoDE, $tekstoEO . "</td>");
  echo "<td><input name='$ejnomo' size='$grandeco' maxlength='40' ";
  if ($ejio)
  {
    echo "value='$ejio'";
  }
  echo "/>";

  if ($boxio==$boxkomparo and $ejio=="")
  erareldono_geo ($mankoDE, $mankoEO);
  echo "</td></tr>\n";
}




?>