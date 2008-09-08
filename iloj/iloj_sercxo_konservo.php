<?php

/*
 * Iloj por konservi kaj malkonservi sercxojn en
 * la datumbazo.
 */

/**
 * konservas la sercxon en la serxo-tabelo.
 */
function konservuSercxon($nomo, $priskribo, $koditaSercxo, $id='')
{
  if ($id)
	{
	  sxangxu_datumbazon("sercxoj",
						 array("nomo" => $nomo,
							   "priskribo" => $priskribo,
							   "sercxo" => $koditaSercxo,
							   "entajpanto" => $_SESSION['kkren']['entajpanto']),
						 array('ID' => $id));
      eoecho ("<p>Serc^o #" . $id . "  s^ang^ita.</p>");
	}
  else
      {
          aldonu_al_datumbazo("sercxoj",
                              array("nomo" => $nomo,
                                    "priskribo" => $priskribo,
                                    "sercxo" => mysql_real_escape_string($koditaSercxo),
                                    "entajpanto" => $_SESSION['kkren']['entajpanto']));
          $id = mysql_insert_id();
          eoecho ("<p>Serc^o #" . $id . "  aldonita.</p>");
      }
}

/**
 * Kodigas la $valoroj-array kiel (bitoka) cxeno,
 * por konservi gxin poste en la datumbazo.
 *
 * Por kodigo estas uzata serialize() kaj krome
 * bzip2-kompresado (bz2compress()).
 */
function kodiguSercxon($valoroj)
{
  return bzcompress(serialize($valoroj));
}

/**
 * Malkodigas la sercx-indikojn al array().
 */
function malkodiguSercxon($kodita)
{
  return unserialize(bzdecompress($kodita));
}


/**
 * prenas la sercxon kun identifikilo $id el la
 * datumbazo, montras nomon, priskribon ktp. kaj
 * metas la sercxopciojn al $valoroj.
 */
function trovuSercxon($id, &$valoroj)
{
  $sql = datumbazdemando(array("s.ID" => "ID",
							   "s.nomo" => "sercxnomo",
							   "e.nomo" => "entajpanto",
							   "s.priskribo" => "priskribo",
							   "s.sercxo" => "sercxo"),
						 array("sercxoj" => "s", "entajpantoj" => "e"),
						 array("s.entajpanto = e.ID",
							   "s.ID = '$id'"));
  $rez = sql_faru($sql);
  if ($linio = mysql_fetch_assoc($rez))
	{
		if ($_REQUEST['sendu']!='sercxu' OR
			 substr($_REQUEST['tipo'], 0, 4) == 'Html')
		{
	  		eoecho( "<h3>Dau^rigita serc^o</h3>\n");
			echo ("<table>\n");
	  		eoecho("<tr><th>ID</th><td>{$linio['ID']}</td></tr>\n");
	  		eoecho("<tr><th>nomo</th><td>{$linio['sercxnomo']}</td></tr>\n");
	  		eoecho("<tr><th>kreinto</th><td>{$linio['entajpanto']}</td></tr>\n");
	  		eoecho("<tr><th>priskribo</th><td>{$linio['priskribo']}</td></tr>\n");
	  		echo("<tr><td colspan='2'>");
	  		ligu("sercxoj.php?sendu=redaktu&id=" . $linio['ID'],
	  		     "Redaktu informojn");
	  		echo "</td></tr>\n";
	  		echo ("</table>");
	  }
	  $valoroj = malkodiguSercxon($linio['sercxo']);
      if (!$valoroj['sercxo_titolo'])
          {
              $valoroj['sercxo_titolo'] = $linio['sercxnomo'];
          }

      $_SESSION['sekvontapagxo'] = "gxenerala_sercxo.php?antauxa_sercxo=" .
          $id . "&sendu=sercxu";
      //      $valoroj
	}
  else
	{
	  darf_nicht_sein();
	}
}


function kasxeblaSercxoElektilo($montru = 'NE')
{
  echo ("<p>");
  skripto_jes_ne_bokso("montru_antauxajn", $montru,
					   "malkasxu('montru_antauxajn', 'listo-de-sercxoj')");
  eoecho (" Montru antau^ajn serc^ojn</p>\n");
  if ($montru == 'JES')
	{
	  echo "<div id='listo-de-sercxoj'>\n";
	}
  else
	{
	  echo "<div style='display:none;' id='listo-de-sercxoj'>\n";
	}
  sercxoElektilo();
  echo "</div>\n";
}

/** 
 * Montras elektilon de cxiuj konservitaj sercxoj
 * kun du butonoj "Resxargxu" kaj "Tuj sercxu".
 */
function sercxoElektilo()
{
  $sql = datumbazdemando(array("s.ID" => "ID", "s.nomo" => "sercxnomo",
							   "e.nomo" => "entajpanto"),
						 array("sercxoj" => "s", "entajpantoj" => "e"),
						 "s.entajpanto = e.ID",
						 "",
						 array("order" => "sercxnomo ASC"));
  $rez = sql_faru($sql);
  
  if ($num = mysql_num_rows($rez))
	{
	  eoecho("  <h3>Antau^aj serc^oj</h3>\n");
	  echo "  <table>\n";
//	  echo "  <ul>\n";
      eoecho("<tr><th>serc^nomo</th><th>kreinto</th><th>s^arg^u</th><th>tuj serc^u</th>".
      			 "<th>administri</th></tr>\n");
      $sercxtipoj =
      	  array('HtmlTabelo' => "tabelo",
       			 'HtmlCSV' => "CSV (k)",
       			 'Utf8CSV' => "CSV (s^)");
	  while($linio = mysql_fetch_assoc($rez))
		{
		  eoecho("    <tr>\n");
		  eoecho("      <td>" . $linio['sercxnomo']  . "</td>\n");
		  eoecho("      <td>" . $linio['entajpanto'] . "</td>\n");
		  echo("      <td>");
		  ligu("gxenerala_sercxo.php?antauxa_sercxo=" . $linio['ID'],
		  	   "s^arg^u");
		  echo "</td>\n";
		  echo("      <td>");
		  foreach($sercxtipoj AS $tipo => $teksto) {
		  		ligu("gxenerala_sercxo.php?antauxa_sercxo=" . $linio['ID'] .
		  		     "&sendu=sercxu&tipo=" . $tipo,
		  		     $teksto);
		  }
		  echo "</td>\n";
		  
		  if($linio['entajpanto'] == $_SESSION['kkren']['entajpantonomo'] or
			 rajtas('teknikumi'))
			{
		  	  echo("      <td>");
			  ligu ("sercxoj.php?sendu=redaktu&id=". $linio['ID'],
			  		"redaktu informojn");
			  ligu_butone("sercxoj.php?id=". $linio['ID'], "forigu", 'forigu');
			  echo "</td>\n";
			}
		  echo "    </tr>\n";
		}
	  echo "  </table>\n";
	}
  else
	{
	  eoecho ("<p>Ne ekzistas antau^aj serc^oj.</p>");
	}
}


function foriguSercxon($id)
{
  //  if (!rajtas(
  $sql = datumbazdemando("entajpanto",
						 "sercxoj",
						 "ID = '$id'");
  $rez = sql_faru($sql);
  if(!($linio = mysql_fetch_assoc($rez)))
	{
	  eoecho( "<p>ne ekzistas serc^o kun ID = '$id'</p>");
	  return;
	}
  if($linio['entajpanto'] != $_SESSION['kkren']['entajpanto'])
	{
	  eoecho ("<p>Vi rajtas forigi nur viajn proprajn serc^ojn, ".
			  "ne tiujn de alia entajpanto.</p>");
	  return;
	}
  forigu_el_datumbazo("sercxoj", $id);
}



?>