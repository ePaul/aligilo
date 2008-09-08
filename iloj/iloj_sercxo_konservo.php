<?php

  /**
   * Iloj por konservi kaj malkonservi serĉojn en
   * la datumbazo.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2005-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   * konservas la serĉon en la serxo-tabelo.
   * La funkcias eldonas iun HTML-tekstan informon pri la rezulto.
   *
   * @param eostring $nomo la nomo, sub kiu ni savu la serĉon.
   * @param eostring $priskribo priskriba teksto pri la serĉo.
   * @param string   $koditaSercxo serĉo-detaloj en kodita formo
   * @param int      $id identigilo - se donita, ni ŝanĝos ekzistantan
   *                    serĉon, alikaze kreas novan.
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
 * Kodigas la $valoroj-array kiel (bitoka) ĉeno,
 * por konservi ĝin poste en la datumbazo.
 *
 * Por kodigo estas uzata serialize() kaj krome
 * bzip2-kompresado (bz2compress()).
 *
 * @param array $valoroj la senditaĵoj de la formularo de
 *    {@link gxenerala_sercxo.php}.
 * @return string kodita formo de la senditaĵoj, taŭga por
 *         meti en la datumbazon.
 */
function kodiguSercxon($valoroj)
{
    return bzcompress(serialize($valoroj));
}

/**
 * Malkodigas la serĉ-indikojn al array().
 *
 * @param string $kodita kodita formo de la senditaĵoj, prenita el
 *         la datumbazo.
 * @return array la senditaĵoj de la formularo de
 *    {@link gxenerala_sercxo.php}.
 */
function malkodiguSercxon($kodita)
{
    return unserialize(bzdecompress($kodita));
}


/**
 * prenas la serĉon kun identigilo $id el la
 * datumbazo, montras nomon, priskribon ktp. kaj
 * metas la serĉopciojn al $valoroj.
 *
 * @param int $id identigilo de la serĉo
 * @paran array $valoroj tien ni metos la informojn.
 * @param boolean $montru se <val>true</val>, ni montras iun tabelan
 *           superrigardon. Se <val>false</val>, ni faras nenion.
 */
function trovuSercxon($id, &$valoroj, $montru)
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
            if ($montru)
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

            $_SESSION['sekvontapagxo'] = "gxenerala_sercxo.php?antauxa_sercxo="
                . $id . "&sendu=sercxu";
        }
    else
        {
            darf_nicht_sein();
        }
}


/**
 * montras tabelon de ĉiuj konservitaj serĉoj,
 * kiu estas (mal)kaŝebla per elekto-butono.
 *
 * (Tio nur funkcias kun Ĵavaskripto.)
 *
 * @param boolean $montru - ĉu ni montru ĝin antaŭ la unua uzanta ago?
 * @uses sercxoElektilo()
 */
function kasxeblaSercxoElektilo($montru = false)
{
    echo ("<p>");
    skripto_jes_ne_bokso("montru_antauxajn", $montru ? 'JES' : 'NE',
                         "malkasxu('montru_antauxajn', 'listo-de-sercxoj')");
    eoecho (" Montru antau^ajn serc^ojn</p>\n");
    if ($montru)
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
 * Montras tabelon de ĉiuj konservitaj serĉoj, kun po
 * diversaj ago-eblecoj.
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
            eoecho("<tr><th>serc^nomo</th><th>kreinto</th><th>s^arg^u</th>".
                   "<th>tuj serc^u</th><th>administri</th></tr>\n");
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
                    echo("      <td>");
		  
                    if($linio['entajpanto'] == $_SESSION['kkren']['entajpantonomo'] or
                       rajtas('teknikumi'))
                        {
                            ligu ("sercxoj.php?sendu=redaktu&id=". $linio['ID'],
                                  "redaktu informojn");
                            ligu_butone("sercxoj.php?id=". $linio['ID'], "forigu", 'forigu');
                        }
                    echo "</td>\n";
                    echo "    </tr>\n";
                }
            echo "  </table>\n";
        }
    else
        {
            eoecho ("<p>Ne ekzistas antau^aj serc^oj.</p>");
        }
}


/**
 * forigas konservitan serĉon.
 *
 * Antaŭe la funkcio kontrolas la rajton: la aktuala uzanto devas esti
 * la kreinto de la serĉo aŭ havi la rajton teknikumi.
 *
 * @param $id identigilo de la konservita serĉo.
 */
function foriguSercxon($id)
{
    $sql = datumbazdemando("entajpanto",
                           "sercxoj",
                           "ID = '$id'");
    $rez = sql_faru($sql);
    if(!($linio = mysql_fetch_assoc($rez)))
        {
            eoecho( "<p>ne ekzistas serc^o kun ID = '$id'</p>");
            return;
        }
    if($linio['entajpanto'] != $_SESSION['kkren']['entajpanto']
       and !rajtas('teknikumi'))
        {
            eoecho ("<p>Vi rajtas forigi nur viajn proprajn serc^ojn, ".
                    "ne tiujn de alia entajpanto.</p>");
            return;
        }
    forigu_el_datumbazo("sercxoj", $id);
}



?>