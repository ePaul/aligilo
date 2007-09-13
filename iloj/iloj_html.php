<?php

/***
 * eldonas la HTML kapon por certigi UTF-8,
 * kaj jxustan HTML.
 *
 *  $klaso - se donita, uzas class=... kiel
 *           atributo por la <body>-Elemento.
 */ 
function HtmlKapo($klaso = "")
{

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="content-language" content="eo">
<?php
   $dosiernomo =  $GLOBALS['prafix']."/stilo_".MODUSO.".css";
   if (DEBUG)
	 {
	   echo "<!-- MODUSO:      " . MODUSO .
		 "\n     dosiernomo:  " . $dosiernomo .
		 "\n     laborejo:    " . getcwd() . 
		 "\n     def(MODUSO): " . defined("MODUSO") .
		 "\n-->\n"; 
	 }
   if (defined("MODUSO") and file_exists($dosiernomo))
	{
	  echo '    <link rel="stylesheet" href="stilo_' .MODUSO. '.css" type="text/css" charset="iso-8859-1">';
	}
		  else
	{
 ?>
    <link rel="stylesheet" href="stilo_defauxlta.css" type="text/css" charset="iso-8859-1">
<?php } ?>    <title>
       IS - Aligilo - <?php echo MODUSO; ?>
    </title>
    <base target="anzeige">
    <script type="text/javascript" src="iloj/cxiujpagxoj.js" charset="iso-8859-1"></script> 
  </head>
  <body <?php if ($klaso!="") {echo "class='$klaso'";} ?> >
  <a name="top"></a>
  <?php if (! EBLAS_SKRIBI)
        { ?>
  <p class='averto'>
     La programo nun estas en nurlega stato.
     &#264;iuj &#349;ajnaj &#349;an&#285;oj ne efikas.
  </p>
 <?php } 
}

/**
 * La fino de la HTML-pagxo ...
 */
function HtmlFino()
{
?>
</body>
</html>
<?php
}


/* ################################# */
/* SPECIALAJ PROCEDUROJ POR ELDONADO */
/* ################################# */

/* ##################################################################### */
/* 
/* ##################################################################### */

/**
 * transformas de la post-^-methodo (c^)
 * al (HTML-)unikoda esperanto, aux al la x-metodo.
 *
 * $enkodo - aux "x-metodo" aux "unikodo",
 *           aux "utf-8" (por retposxto)
 * $teksto - la teksto, kun "c^"-kodigo de
 *           la esperantaj literoj.
 * 
 * redonas la tekston, kie oni anstatauxis
 *  al la x-kodigo (E^ -> Euro) aux
 * HTML-Unikodo-kodigo.
 *
 */
function eotransformado($texto,$enkodo)
{
  if ($enkodo == "x-metodo")
  {  
    $texto = str_replace("C^","Cx",$texto);
    $texto = str_replace("c^","cx",$texto);

    $texto = str_replace("G^","Gx",$texto);
    $texto = str_replace("g^","gx",$texto);

    $texto = str_replace("H^","Hx",$texto);
    $texto = str_replace("h^","hx",$texto);

    $texto = str_replace("J^","Jx",$texto);
    $texto = str_replace("j^","jx",$texto);

    $texto = str_replace("S^","Sx",$texto);
    $texto = str_replace("s^","sx",$texto);

    $texto = str_replace("U^","Ux",$texto);
    $texto = str_replace("u^","ux",$texto);

    $texto = str_replace("E^","Euro",$texto);
  }
  else if ($enkodo == "unikodo")
  {
    $trans = array ("C^" => "&#264;", "c^" => "&#265;",
                    "G^" => "&#284;", "g^" => "&#285;",
                    "H^" => "&#292;", "h^" => "&#293;",
                    "J^" => "&#308;", "j^" => "&#309;",
                    "S^" => "&#348;", "s^" => "&#349;",
                    "U^" => "&#364;", "u^" => "&#365;",
                    "E^" => "&#8364;"); // TODO: eble ni uzu &euro; ?
    $texto = strtr($texto, $trans);

  }
  else if ($enkodo == "utf-8")
	{
	  $trans = array("C^" => "Äˆ", "c^" => "Ä‰",
					 "G^" => "Äœ;", "g^" => "Ä",
					 "H^" => "Ä¤;", "h^" => "Ä¥",
					 "J^" => "Ä´", "j^" => "Äµ",
					 "S^" => "Åœ", "s^" => "Å",
					 "U^" => "Å¬", "u^" => "Å­",
					 "E^" => "â‚¬");
	  $texto = strtr($texto, $trans);
	}
  return $texto;
}

/* ####################################### */
/* echo kun Eo signo laux unikodo aux 'xe' */
/* ####################################### */

function eoecho($io)
{
  echo eotransform($io);
}

function eotransform($io)
{
  if ($_SESSION["enkodo"] == "")
	{
	  $enkodo = $GLOBALS["enkodo"];
	  if ($enkodo == "")
		{
		  $enkodo = "unikodo";
		}
	}
  else
	{
	  $enkodo = $_SESSION["enkodo"];
	}
  return eotransformado($io, $enkodo);
}


/**
 * Montras renkontigxoelektilon.
 * La HTML-nomo estas "formrenkontigxo",
 * la elektota valoro estas la identifikilo
 * de la renkontigxo.
 *
 *  $antauxelekto  - la identifikilo de la renkontigxo,
 *                   kiu estu jam elektita.
 *                   se vi forlasas, elektigxas la plej
 *                   malfrue komenc(o|a|i)nta renkontigxo
 *                   ( = la unua en la listo).
 * $grandeco kiom granda estu la listo, kutima estas 5
 *
 */
function montru_renkontigxoelektilon($antauxelekto = "plej_nova",$grandeco='5')
{
  // Elektilo por la renkontigxo:

  echo "<select name='formrenkontigxo' size='$grandeco'>\n";
  if ($antauxelekto == "plej_nova")
	{
	  $unua = true;
	}
  // "Select ID,nomo,loko,de,gxis from renkontigxo order by de DESC"
  $result = sql_faru(datumbazdemando(array("ID", "nomo", "loko", "de", "gxis"),
									 "renkontigxo",
									 "",
									 "",
									 array("order" => "de DESC")
									 ));
  while ($row = mysql_fetch_array($result, MYSQL_BOTH))
    {
      echo "<option";
      // elektu auxtomate la unuan renkontigxon
      if ($unua or ($row["ID"] == $antauxelekto))
      {
         echo ' selected="selected"';
         $unua = false;
      }
      $temp = "$row[nomo]  en $row[loko] ($row[de] - $row[gxis])";
      echo " value='$row[ID]'>";
      eoecho ($temp)."\n";
    }
    echo " </select>  <BR>\n";
    unset($unua);

}


/**
 * Elektilo por lando.
 *
 * $alteco - la nombro da linioj en la elektilo.
 *           se 1, tiam estas elektilo kun klapmenuo,
 *           alikaze estos plurlinia elektilo.
 * $lando  - la identifikilo de la antauxelektita lando.
 *           (se vi nenion donis, uzos la valoron de
 *            HEJMLANDO.)
 * $loka   - uzu la loka-lingvan varianton de la landonomo
 *           (ekzemple germana), se estas donita kaj io, kio
 *           igxas 'true'..
 * $klaso  - iu html-atribut-fragmento, ekzemple
 *            class='mankas' por aldoni al la <select>-elemento.
 */
function montru_landoelektilon($alteco, $lando=HEJMLANDO, $loka=false, $klaso="")
{
  if (DEBUG) echo "<!-- lando: $lando -->";
  
  echo "<select name='lando' size='{$alteco}'{$klaso}>\n";
  
  if ($loka)
      {
          $nomonomo = 'lokanomo';
      }
  else
      {
          $nomonomo = 'nomo';
      }


  $result = sql_faru(datumbazdemando(array($nomonomo => "landonomo",
                                           "kategorio", "ID"),
									 "landoj",
									 "",
									 "",
									 array("order" => "landonomo ASC")));
  while ($row = mysql_fetch_assoc($result))
    {
      echo "<option";
      if ($row[ID] == $lando)
      {
        echo " selected='selected'";
      }
      echo " value = \"$row[ID]\">";

      eoecho ($row['landonomo'].      " (". $row['kategorio']. ')');
	  echo "</option>\n";
    }
  echo "</select>  <br/>\n";
}


/**
 * Montras entajpejon ene de tabellinio (<tr/>).
 *
 * .--------.----------------------.
 * | teksto | [_______] postteksto |
 * '--------'----------------------'
 *
 * $teksto   - la titolo (en <th/>).
 * $nomo     - la nomo de la tekstkampo (por sendi al la servilo)
 * $io       - la komenca teksto de la tekstkampo
 * $grandeco - la largxeco de la tekstkampoj (proksiume en literoj)
 * $postteksto - teksto montrita post la entajpejo.
 * $manko, $kutima, $kasxe - kiel cxe entajpejo()
 * ...
 */
function tabelentajpejo ($teksto, $nomo, $io="", $grandeco="",$postteksto="",
                         $manko="", $kutima="", $kasxe="n")
{
  eoecho("    <tr><th>$teksto</th><td>");
  entajpejo("", $nomo, $io, $grandeco, $manko, $kutima, $postteksto, $kasxe);
  echo "</td></tr>\n";
}


/**
 * Montras grandan entajpejon ene de tabellinio (<tr/>).
 *
 * .--------.---------------------------.
 * | teksto | [¯¯¯¯¯¯¯¯¯¯¯¯] postteksto |
 * |        | [            ]            |
 * |        | [____________]            |
 * '--------'---------------------------'
 *
 * $teksto   - la titolo (en <th/>).
 * $nomo     - la nomo de la tekstkampo (por sendi al la servilo)
 * $io       - la komenca teksto de la tekstkampo
 * $kolumnoj - la largxeco de la tekstkampo (proksiume en literoj)
 * $linioj   - la alteco de la tekstkampo (nombro da tekstlinioj)
 * $postteksto - teksto montrita post la entajpejo.
 * $manko, $kutima, $kasxe - kiel cxe entajpejo()
 * ...
 */
function granda_tabelentajpejo($teksto, $nomo, $io="",  $kolumnoj="", $linioj="",
							   $postteksto="", $manko="", $kutima="")
{
  eoecho("    <tr><th>$teksto</th><td>");
  granda_entajpejo("", $nomo, $io, $kolumnoj, $linioj, $manko, $kutima, $postteksto);
  echo "</td></tr>\n";
}


/**
 * Entajpejo por tekstoj:
 *
 *  teksto  [_____]  postteksto
 *
 * $teksto     - priskribo antaux la bokso.
 * $nomo       - nomo de la input-elemento por sendi gxin al la servilo
 * $io         - valoro por enmeti
 * $grandeco   - grandeco de la entajpejo
 * $manko      - ebla erarmesagxo (por testi, cxu $io estas malplena  -->malplentesto())
 * $kutima     - valoro por enmeti, se $io == "".
 * $postteksto - teksto por montri post la entajpejo
 * $kasxe      - se 'j', tiam estu entajpejo por
 *               pasvortoj (= montras nur *).
 */
function entajpejo($teksto, $nomo, $io="", $grandeco="", $manko="",
				   $kutima="", $postteksto="", $kasxe="n")
{
    eoecho ($teksto);
    echo " <input name='$nomo' size='$grandeco' ";
    if ($kasxe == "j")
        {
            echo "type='password' ";
        }
    echo "value = '" . htmlspecialchars($io ? $io : $kutima,
                                        ENT_QUOTES) ."'";
    echo "/>";
    if ($postteksto)
        {
            eoecho (" " .$postteksto."\n");
        }
    echo "<br/>";
    if ($manko)
        {
            malplentesto($io,$manko);
        }
}

/**
 * Entajpejo por tekstoj:
 *
 *  teksto  [_____]  postteksto
 *
 * $teksto     - priskribo antaux la bokso.
 * $nomo       - nomo de la input-elemento por sendi gxin al la servilo
 * $io         - komenca valoro de la kampo. Se malplena, uzas
 *                $_REQUEST['nomo'].
 * $grandeco   - grandeco de la entajpejo
 * $kutima     - valoro por enmeti, se $io == "".
 * $postteksto - teksto por montri post la entajpejo
 * $kasxe      - se 'j', tiam estu entajpejo por
 *               pasvortoj (= montras nur *).
 *
 * La cxefa diferenco (krom malapero de $manko)
 * al entajpejo estas, ke fine de gxi ne aperas <br/>.
 */
function simpla_entajpejo($teksto, $nomo, $io = "",  $grandeco="",
				   $kutima="", $postteksto="", $kasxe="n")
{
    if (! $io)
        $io = $_REQUEST['nomo'];
    eoecho ($teksto);
    echo " <input name='$nomo' size='$grandeco' ";
    if ($kasxe == "j")
        {
            echo "type='password' ";
        }
    else
        {
            echo "type='text' ";
        }

    if ($io)
        {
            echo "value='".htmlspecialchars($io, ENT_QUOTES)."' ";
        }
    else
        {
            echo "value='".htmlspecialchars($kutima, ENT_QUOTES)."'";
        }
    echo "/>";
    if ($postteksto)
        {
            eoecho (" " .$postteksto."\n");
        }
}



/**
 * Montras grandan entajpejon.
 *
 * teksto  [¯¯¯¯¯¯¯¯¯¯¯¯]  postteksto
 *         [            ]
 *         [____________]
 *
 * $teksto   - la titolo (en <th/>).
 * $nomo     - la nomo de la tekstkampo (por sendi al la servilo)
 * $io       - la komenca teksto de la tekstkampo
 * $kolumnoj - la largxeco de la tekstkampo (proksiume en literoj)
 * $linioj   - la alteco de la tekstkampo (nombro da tekstlinioj)
 * $postteksto - teksto montrita post la entajpejo.
 * $manko, $kutima, $kasxe - kiel cxe entajpejo()
 * ...
 */
function granda_entajpejo($teksto, $nomo, $io="", $kolumnoj="", $linioj="", $manko="",
						  $kutima="", $postteksto="")
{
  eoecho ($teksto);
  echo " <textarea name='$nomo' ";
  if ($linioj)
	{
	  echo "rows='$linioj' ";
	}
  if ($kolumnoj)
	{
	  echo "cols='$kolumnoj' ";
	}
  echo ">";
  if ($io)
  {
    echo $io;
  }
  else
  {
    echo $kutima;
  }
  echo "</textarea>";
  if ($postteksto)
  {
    eoecho ("<br/> " .$postteksto."\n");
  }
  echo "<br/>";
  if ($manko)
  {
    malplentesto($io,$manko);
  }
}


/**
 * (_)   aux   (X)
 *
 * $nomo    - la nomo (por sendi)
 * $elekto  - valoro por decidi, cxu elekti tiun cxi kampon.
 * $komparo - se $elekto == $komparo, cxi entajpbutono estas
 *            elektita [   (*)   ]. Gxi estas ankaux uzata
 *            kiel valoro por sendi.
 * $kutima  - se $elekto == "" kaj $kutima == "kutima", la
 *            entajpbutono estas elektita. (defauxlto: "")
 * $skripto - se donita, la skripto estas vokita dum sxangxo
 *            de la stato. 
 */
function simpla_entajpbutono($nomo, $elekto, $komparo, $kutima="", $skripto="")
{
  echo "<input type='radio' name='$nomo' value='$komparo' ";
  if($elekto == $komparo or ($elekto == "" and $kutima == "kutima"))
	{
	  echo "checked='checked' ";
	}
  if($skripto)
	{
	  $skripto = htmlspecialchars($skripto, ENT_QUOTES);
	  echo /* "onchange='$skripto'" */" onfocus='$skripto' onblur='$skripto'".
		" onclick='$skripto'";
	}
  echo "/>";
}



/**
 * Entajpbutono.
 *
 *    teksto  (_)  postteksto
 *
 * $teksto     - teksto antaux la entajpbutono.
 * $nomo       - nomo de la variablo (por sendi al la servilo)
 * $io         - valoro por kompari al $komparo
 * $komparo    - se $komparo == $io, la entajpbutono estas komence elektata [  (*)  ]
 * $valoro     - kio estos sendita al la servilo, se la butono estas
 *               elektita dum la sendado.
 * $postteksto - estos montrata post la entajpbutono (defauxlto: "").
 * $kutima     - se kutima == "kutima" kaj $io == "", tiam la butono estas
 *               ankaux komence elektata.
 */
function entajpbutono($teksto,$nomo,$io,$komparo,$valoro,$postteksto="",$kutima="")
{
  eoecho ($teksto."\n");
  echo " <input name='$nomo' type='radio' ";
  if ( ($io == $komparo)
       or ( (!$io)
              and ($kutima == "kutima")
             )
      )
  {
    echo "checked='checked' ";
  }
  echo "VALUE='$valoro'>&nbsp;";
  eoecho ($postteksto."\n");
}


/**
 * Entajpbokso:
 *
 *   teksto [X] postteksto
 *
 * $teksto     - teksto antaux la bokso.
 * $nomo       - nomo de la inputelemento (uzata por sendi la valoron al la servilo)
 * $io         - valoro de la bokso - aux $komparo ([X]) aux ne ([ ]).
 * $komparo    - valoro por kompari al $io (se sama, metu krucon).
 * $valoro     - kio estos resendota al la servilo, kiam estos kruco.
 * $postteksto - teksto por montri post la bokso.
 * $kutima     - se != "" kaj $io == "", tiam estas kruco.
 * $kasxe      - se "jes" (defauxlto), aldonu kasxitan <input>-Elementon,
 *               kiu metas la valoron de $nomo al NE, se ne estos kruco.
 *               (Alikaze tute ne estos valoro sendota al la servilo.)
 */
function entajpbokso($teksto,$nomo,$io,$komparo,$valoro,$posttexto="",$kutima="",$kasxe="jes")
{
  eoecho ($teksto."\n");
  if ($kasxe=="jes")
	echo " <input name='$nomo' type='hidden' value='NE'>\n";//necesas
  echo " <input name='$nomo' type='checkbox' ";
  if ( ($io == $komparo)
        or ( (!$io)
              and ($kutima)
             )
       )
  {
    echo "checked='checked' ";
  }
  echo "value='$valoro'>&nbsp;";
  eoecho ($posttexto."\n");
}


function skripto_jes_ne_bokso($nomo,$io,$skripto="")
{
  //  eoecho ($teksto."\n");
  echo " <input name='$nomo' type='hidden' value='NE'>\n";
  echo " <input name='$nomo' type='checkbox' ";
  if ($io{0} == 'J')
	{
	  echo "checked='checked' ";
	}
  if($skripto)
	{
	  echo "onchange='" .htmlspecialchars($skripto, ENT_QUOTES) . "' ";
	}
  echo "value='JES'>\n";
}


/**
 * TODO: Dokumentado por entajpboksokajejo
 */
function entajpboksokajejo($boxnomo,$boxio,$boxkomparo,$boxvaloro,
                           $teksto,$posxteksto,$ejnomo,$ejio,
                           $grandeco,$manko)
{
  if ($ejio)
  {
    $boxio = "JES";
  }
  entajpbokso("",$boxnomo,$boxio,$boxkomparo,$boxvaloro);
  eoecho ($teksto);
  entajpejo("",$ejnomo,$ejio,$grandeco,"","",$posxteksto);
  if ( ($boxio == $boxkomparo)
       and ($ejio == ""))
  {
    erareldono ($manko);
  }
}


/* ################################################# */
/* testas je malpleneco kaj enkodas laux HTML legxoj */
/* ################################################# */

function malplentesto (&$io,$err="")
{
  global $parto;
  // TODO:? Cxu vi povas diri, kion fakte faras (faru) la funkcio malplentesto()?

  // tranformas cxion HTML specialan signon, por ke mi jxuste enskibas gxin en la datumaro

  //$io = HTMLsekurigi(&$io); geht leider nicht, wegen übergabeproblemen.
  // HTML sicherung muß noch bei JEDEM String - einmalig erfolgen.

  //$io = /*htmlentities*/(str_replace("'","`",$io));

  if ($parto and /*(($parto == "korektigi") or ($parto=="kontroli"))and */($io==""))
  {
    if ($err)  // malgucken, obs später mal auch ohne geht trotzdem geht.
    {
      erareldono ("Bonvolu entajpu vian ".$err);
    }
    $parto="korektigi";
  }
}

/**
 * TODO: auf CSS umstellen
 * eldonas la rugxan tekston ekz. se mankas necesaj datumoj en iu entajpformularo
 */
function erareldono ($err)
{
  echo "<font color='red'>";
  eoecho ($err);
  echo "!</font><br/>";
}

/**
 * Kreas la HTML-kodon por valoro en formularo, kiu
 * ne montrigxas, sed tamen sendigxos kun la datoj
 * (<input type="hidden" .../>)
 *
 *  $nomo - la nomo de la variablo
 *  $valoro - la valoro de la variablo.
 *
 */
function tenukasxe($nomo,$valoro)
{
    echo "<input type='hidden' name='$nomo' value='" .
        htmlspecialchars($valoro, ENT_QUOTES) . "' />\n";
}


/**
 * TODO: Dokumentado por HTMLsekurigi
 * probeweise auskomentiert.  beim nächsten mal löschen
 */
/*function HTMLsekurigi(&$io)
{
  $io = /*htmlentities* /(str_replace("'","`",$io));
}
*/


/**
 * Metas HTML-ligilon, se la nuna entajpanto rajtas
 * iun agon. Alikaze montras strekitan tekston (sen ligilo).
 *
 * $kien   - la ligota pagxo
 * $nomo   - nomo de la ligilo
 * $celo   - la kadron, en kiu la pagxo montrigxu
 *           (nur necesa, se ne la defauxlta)
 * $ago    - la ago, por kiu oni bezonas la rajton.
 * $montru - se ne komencas per "j", kaj oni ne rajtas,
 *           la teksto tute ne montrigxu.
 */
function rajtligu($kien,$nomo,$celo="",$ago="",$montru="j")
{
  // Ni testas, cxu oni rajtas iri al la ligota pagxo
  if ( rajtas($ago) )
  {
    ligu($kien,$nomo,$celo);
  }
  else if ($montru[0]=='j')
  {
    eoecho ("<a class='nerajtas'>\n $nomo \n</a>");
  }
}

/**
 * Metos HTML-ligilon.
 *
 *  $kien - la URI de la pagxo.
 *  $nomo - la teksto de la ligilo (en eo-kodigo)
 *  $celo - (nenecesa) se en alia ol la defauxlta
 *          kadro, donu ties nomon.
 */
function ligu($kien,$nomo,$celo="")
{
    echo ' &nbsp;' . donu_ligon($kien, $nomo, $celo);
}

/**
 * Redonas HTML-ligilon.
 *
 *  $kien - la URI de la pagxo.
 *  $nomo - la teksto de la ligilo (en eo-kodigo)
 *  $celo - (nenecesa) se en alia ol la defauxlta
 *          kadro, donu ties nomon.
 */
function donu_ligon($kien,$nomo,$celo="")
{
  $rez = '<a href="'.str_replace('&', '&amp;', $kien).'" ';
  if ($celo)
  {
    $rez .= "target='$celo'";
  }
  $rez .= ">";
  $rez .= eotransform($nomo);
  $rez .= "</a>";
  return $rez;
}

/**
 * alligas iun pagxon/dosieron kun aldona hazarda numero, por
 * eviti uzon de retumilan stokejo.
 */
function hazard_ligu($kien, $nomo, $celo="")
{
  ligu($kien . "?rand=" . rand(1000,9999), $nomo, $celo);
}

/**
 * butono kun sia propra POST-formulareto, por uzo anstataux
 * simpla ligo por fari iun agon.
 *
 * Ne uzu ene de aliaj formularoj!
 *
 * $kien - kiun pagxon voki
 * $titolo - teksto sur la butono
 * $valoro - kion sendi        (defauxlto: 'ne_gravas')
 * $nomo   - nomo de la butono (defauxlto: 'sendu')
 */

function ligu_butone($kien, $titolo, $valoro='ne_gravas', $nomo='sendu')
{
    echo "<form action='" . htmlspecialchars($kien, ENT_QUOTES) .
        "' method='POST' class='formulareto'>";
    butono($valoro, $titolo, $nomo);
    echo "</form>";
}


/**
 * .-----------.
 * |  titolo   |
 * '-----------'
 *
 * Butono por sendi formularon (submit).
 *
 * $titolo - la teksto de la butono. Povas uzi c^-kodigon.
 * $nomo   - la nomo de la butono, defauxlto "sendu".
 */
function send_butono($titolo)
{
  echo "<input name='sendu' value='";
  eoecho ($titolo);
  echo "' size='18' type='submit'> \n";
}

/**
 * .-----------.
 * |  titolo   |
 * '-----------'
 *
 * Butono por sendi formularon (submit).
 *
 * $valoro - la valoro de la butono (estos sendota).
 * $titolo - la teksto de la butono. povas enhavi HTML-on kaj uzi c^-kodigon.
 * $nomo   - la nomo de la butono, defauxlto "sendu".
 */
function butono($valoro, $titolo, $nomo="sendu")
{
  echo "<button type='submit' name='$nomo' value='$valoro'>";
  eoecho($titolo);
  echo "</button>\n";
}

/**
 * ducela eldono por tabellinioj
 * TODO: auf CSS umstellen
 */
function kampo($titolo,$io)
{
  eoecho ("<TR><TD align=right bgcolor=#CCFFFF> $titolo </TD>\n<TD align=left bgcolor=#CCFFCC> $io</TD>");
  echo "</TR>\n";
}

function kampoj($titolo, $kampoj)
{
  eoecho("<tr><th>".$titolo."</th>\n");
  
  foreach ($kampoj AS $de => $al)
	{
	  if (is_int($de))
		{
		  eoecho( "    <td>" . $al . "</td>\n");
		}
	  else
		{
		  eoecho( "    <td class='". $al . "'>". $de . "</td>");
		}
	}
  echo "</tr>\n";
}

/**
 * TODO: Dokumentado por depend_malsxargxi_kaj_korekti
 * prüft ob bei den Programmfeldern die Checkbox mit den Feldern korreliert
 */
function depend_malsxargxi_kaj_korekti(&$bokso,&$ejo)
{
  global $parto;
  //echo "B: $bokso, $ejo";
  if ( ($bokso[0] != "J") and ($ejo))
  {
    $ejo = "";
  }
  if ( $bokso[0] == "J" and (!$ejo) )
  {
    $parto = "korektigi";
  }
}


/**
 * Montras HTML-elektilon (<select>-elementon) de partoprenantoj
 * Gxi montras personan nomon, familian nomon kaj la mallongigo
 * de renkontigxo, kaj kiam oni elektis ion, gxi sendas la
 * identifikilon ("ID").
 *
 * $sql - la SQL-demando. La rezulto enhavu almenaux "ID", "nomo", "personanomo"
 *         kaj "renkNumero" kiel kampoj.
 *         Ekzempla SQL-demando por cxiuj partoprenantoj:
 *
 *  		$sql = datumbazdemando(array("pp.ID", "pp.nomo", "personanomo",
 *  									 "max(renkontigxoID) as renkNumero" ),
 *  							   array("partoprenantoj" => "pp",
 *  									 "partoprenoj" => "pn" ),
 *  							   "pn.partoprenantoID = pp.ID",
 *  							   "",
 *  							   array("group" => "pp.ID",
 *  									 "order" => "personanomo, nomo")
 *  							   );
 * $nomo - la valoro de la "name"-atributo de la <select>-elemento.
 *         La defauxlta valoro estas "partoprenantoidento".
 *
 */
function partoprenanto_elektilo($sql,$grandeco='10', $nomo ="partoprenantoidento", $kun_identifikilo = FALSE)
{
  if(substr($sql, 0, 6) != "SELECT")
	{
	  darf_nicht_sein();
	  return false;
	}
  $rezulto = sql_faru($sql);
  $mallongigoj = array();
  echo "<select size='$grandeco' name='" . $nomo . "'>\n";
  
  while ($row = mysql_fetch_assoc($rezulto)) 
			 {
                 if ($row['renkNumero'])
                     {
			   $mallongigo = $mallongigoj[$row["renkNumero"]];

			   // Ni sercxas por cxiu renkontigxo maksimume unu foje la
			   // mallongigon
			   if (empty($mallongigo))
			   {
				 $rez = mysql_fetch_assoc(sql_faru(datumbazdemando("mallongigo",
																   "renkontigxo",
																   "ID = '".$row["renkNumero"]."'",
																   "",
																   array("limit" => "1")
																   )));
				 $mallongigo = $rez["mallongigo"];
				 $mallongigoj[$row["renkNumero"]] = $mallongigo;
			   }
                     }
                 else
                     {
                         $mallongigo = "";
                     }
			   echo "<option"; 
			   eoecho (" value='".$row["ID"]."'>".$row['personanomo'].' '.$row['nomo']);
			   if ($mallongigo)
				 eoecho (" (" . $mallongigo . ")");
			   if ($kun_identifikilo)
				 {
				   echo " (#" . $row["ID"] . ")";
				 }
			   echo "</option>\n"; 
			 }
	echo "</select>\n";
}




/**
 * Gxenerala sercx-funkcio.
 *
 * Sercxas en la datumbazo kaj montras la rezulton en HTML-tabelo.
 *
 * $sql - la SQL-demando (sen ordigo).
 *
 * $ordigo - array(),
 *   $ordigo[0]:  laux kiu kolumno la rezultoj ordigxu
 *   $ordigo[1]:  cxu la rezultoj ordigxu pligrandigxanta ("ASC") aux
 *                malpligrangigxanta ("DESC")
 *
 * $kolumnoj - array() de array-oj, por la unuopaj kolumnoj. Por cxiu kolumno,
 *      la array enhavu la sekvajn ses komponentojn (cxiuj cxeestu, ecx se malplenaj):
 *    [0] - aux nomo aux numero de kampo de la SQL-rezulto.
 *          Prefere uzu nomon, cxar per numero la ordigo ne funkcias.
 *    [1] - la titolo de la kolumno
 *    [2] - La teksto, kiu aperu en la tabelo. Se vi uzas XXXXX (jes, 5 iksoj),
 *          tie aperas la valoro el la SQL-rezulto.
 *    [3] - arangxo: cxu la valoroj aperu dekstre ("r"), meze ("z") aux
 *             maldekstre ("l") en la tabelkampo?
 *    [4] - se ne "", la celo de ligilo. (Alikaze ne estos ligilo.)
 *    [5] - Se estas ligilo, kaj cxi tie ne estas -1, dum klako al
 *          la ligilo en la menuo elektigxas la persono, kies identifikilo
 *          estas en la kampo, kies nomo/numero estas cxi tie.
 *
 * $sumoj - jen indikoj pri linioj kun sumoj de la teksto.
 *      por cxiu sum-linio ekzistas array (en $sumoj). En cxiu linio-array
 *      estas po 3 elementoj por kolono. [TODO: trovu pli bonan sistemon.]
 *   [3*n+0] - La teksto de la kampo. Se vi uzas XX, tie aperos la rezulto
 *             de la sumado.
 *   [3*n+1] - La speco de la sumado. eblecoj:
 *              A - simple nur kalkulu, kiom da linioj estas.
 *              J - kalkulu, kiom ofte aperas 'J' en la koncerna kampo
 *              N - adiciu la numerojn en la koncerna kampo.
 *              S - speciala sumado, rigardu cxe $extra[Spaltenrechnung].
 *   [3*n+3] - arangxo ('l', 'r', 'z' - vidu cxe $kolumoj - [3].)
 *
 * $identifikilo - (TODO: ankoraux ne estas uzata)
 *
 * $extra - aldonaj parametroj. Se tiaj ne ekzistas, eblas uzi 0.
 *      Alikaze estu array, kies sxlosiloj estu iuj el la sekve
 *      menciitaj. La valoroj havas cxiam apartajn signifojn.
 *    [Zeichenersetzung]  - ebligas la anstatauxigon
 *                          de la valoroj per iu ajn teksto (aux HTML-kodo).
 *                la valoro estu array, kiu enhavu por cxiu kolumno, kie
 *                okazu tia anstatauxigo (sxlosilo=numero de la kolumno,
 *                komencante per 0), plian array, kiu enhavu cxiun
 *                anstatauxotan valoron kiel sxlosilo, la anstatauxontan
 *                valoron kiel valoro. Ekzemplo:
 *       array('1' => array('j'=>'<b><font color=green>prilaborata',
 *                          ''=>'<b><font color=red>neprilaborata',
 *                          'n'=>'<b><font color=red>neprilaborata'))
 *          En kolumno 1 (en la teksto enmetota por XXXXX) cxiu 'j' estas
 *          anstatauxita per "prilaborata", cxiu '' kaj 'n' per "neprilaborata".
 *          En aliaj kolumnoj ne okazos tia anstatauxo.
 *    [anstatauxo_funkcio] - funkcias simile kiel "Zeichenersetzung",
 *               sed anstataux anstatauxa array() estu nomo de funkcio,
 *				 kio estos vokata por eltrovi la valoron.
 *               Gxi nur estos vokota unufoje por la tuta kampo, ne por
 *               cxiu litero de gxi.
 *    [okupigxtipo] - anstatauxigu en iu kolumno la okupigxtipvaloron per
 *                    la nomon de tiu tipo.
 *               La valoro estu kolumnonumero. La valoro de la koncerna
 *               datumbazkampo estos donita al la funkcio okupigxtipo()
 *               (en iloj_sql), kaj ties rezulto estas la teksto en tiu
 *               kolumno.
 *
 *    [SpaltenRechnung] - sumigu valorojn de iu kampo, kiam alia kampo enhavas 'j'.
 *               La valoro estu array, kies nula elemento estu kamponomo aux -numero.
 *               Se en iu sumig-ordono aperas la sumadospeco 'S', tiam
 *               tie estos sumita la valoroj de tiu cxi kampo, en tiuj linioj,
 *               kies sum-kampo enhavas 'j'. (Jes, malgranda 'j'.)
 *               [TODO:  Nun, 2004-09-30, neniu pagxo uzas tiun cxi funkcion.
 *                  Eble mi forigos gxin (aux sxangxos la sintakson).]
 *    [litomanko] - montru aparte, en kiuj noktoj ankoraux mankas litoj.
 *               La valoro estu kamponomo aux -numero.
 *               La valoro de tiu kampo estu partoprenidento.
 *               Je la fino de la linio (post la aliaj kolumnoj) estos
 *               montrita, en kiuj noktoj tiu partoprenanto jam havas
 *               liton, kaj en kiuj noktojn ankoraux mankas.
 *               Poste aperos ligilo "sercxu" al la cxambrodisdono.
 *    [tutacxambro]
 *               La valoro estu kamponomo aux -numero de kampo kun partopreno-ID.
 *               En aparta linio post cxiu rezultlinio estos montrataj la
 *               datoj de la unua cxambro, en kiu tiu partoprenanto logxas.
 * $csv - tipo de la rezulto. Eblaj valoroj:
 *   0 - HTML kun bunta tabelo
 *   1 - CSV (en HTML-ujo)
 *   2 - CSV por elsxuti
 *   3 - CSV por elsxuti, en UTF-8
 * $antauxteksto - teksto, kiu estu montrata antaux la tabelo.
 *                 (Gxi estas uzata nur kun $proprakapo == 'jes').
 * $almenuo      - se gxi ne estas "", post la tabelo aperas ligo
 *                 "Enmeti en la maldekstra menuo", kies alklako
 *                 aldonas la rezulton en la maldekstra menuo.
 *                 Por ke tio funkciu, la sql-sercxfrazu redonu
 *                 kampojn nomitaj 'nomo', 'personanomo', 'renkNumero'
 *                 kaj 'ID' (kiu estu partoprenanto-ID).
 * $proprakapo   - montras la tabelon ene de <html><body>-kadro, kun
 *                 ebla antauxteksto. (Estas uzata nur, se $csv < 2.)
 */
function sercxu($sql, $ordigo, $kolumnoj, $sumoj, $identifikilo, $extra, $csv, $antauxteksto, $almenuo, $proprakapo="jes")
{
  sercxu_nova($sql, $ordigo, $kolumnoj, $sumoj, $identifikilo, $extra, $csv, $antauxteksto, $almenuo, $proprakapo);

}


?>