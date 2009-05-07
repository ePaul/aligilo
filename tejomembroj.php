<?php 

  /**
   * Amasa sxangxo de la TEJO-membreco-konfirmoj.
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */
  require_once ('iloj/iloj.php');
  session_start();
  malfermu_datumaro();
 
// TODO: kiu rajto?
kontrolu_rajton("vidi");

if (rajtas("sxangxi") or
    $_SESSION['kkren']['entajpantonomo'] == 'tejo-volontulo') {
    $rajtas_sxangxi = true;
 }
 else {
     $rajtas_sxangxi = false;
 }


/**
 * metas ligon al la listo kun nova ordigo de la linioj.
 * (Se la nova ordigo estus la sama kiel la malnova, montras
 *  nur la titolon, sen ligo.)
 * @param eostring $titolo la montrota teksto por la ligo.
 * @param sqlstring $novakampo la nomo de la kampo, kiu
 *      estu la nova cxefa ordigo.
 * @param sqlstring $ordigo la gxisnuna ordigo
 */
function metu_ordigoligon($titolo, $novakampo, $ordigo) {
    $malnovaj_ordigoj = explode(",", $ordigo);
    if ($novakampo != $malnovaj_ordigoj[0]) {
        array_unshift($malnovaj_ordigoj, $novakampo);
        $novaj_ordigoj =
            array_values(array_unique($malnovaj_ordigoj));
        
        ligu("tejomembroj.php?ordigo=" .
             implode(",", $novaj_ordigoj),
             $titolo);
    }
    else {
        eoecho($titolo);
    }
}

/**
 * @param asciistring $ordigo la nomo de la kampo, laux kiu estu
 *                   ordigita la listo.
 */
function kreu_la_longan_liston($ordigo)
{
    eoecho("<h2>Listo de partoprenantoj kaj eblaj membroj</h2>\n");
    echo "<form action='tejomembroj.php' method='POST'>\n";
    tenukasxe("ordigo", $ordigo);
    echo "<table id='tejomembreco'>\n";
    echo ("<tr><th>");
    metu_ordigoligon("p-enoID", 'ID', $ordigo);
    echo "</th><th>";
    metu_ordigoligon("UEA-kodo", 'ueakodo', $ordigo);
    echo "</th><th>";
    metu_ordigoligon("persona nomo", 'personanomo', $ordigo);
    echo "</th><th>";
    metu_ordigoligon("familia nomo", 'nomo', $ordigo);
    echo "</th><th>";
    metu_ordigoligon("urbo", 'urbo', $ordigo);
    echo "</th><th>";
    metu_ordigoligon("lando", 'landonomo', $ordigo);
    echo "</th><th>";
    metu_ordigoligon("membro lau^dire", 'tejo_membro_laudire',
                     $ordigo);
    if ($GLOBALS['rajtas_sxangxi']) {
        echo "</th><th colspan='4'>";
    }
    else {
        echo "</th><th>";
    }
    metu_ordigoligon("membro lau^ kontrolo",
                     'tejo_membro_kontrolita', $ordigo);
    echo "</th></tr>";
    
    $sql = datumbazdemando(array('pa.nomo',
                                 'pa.personanomo',
                                 'pa.sekso',
                                 'pa.ueakodo',
                                 'pa.urbo',
                                 'pn.ID',
                                 'pn.tejo_membro_laudire',
                                 'pn.tejo_membro_kontrolita',
                                 'pn.partoprenantoID',
                                 'l.nomo' => "landonomo"),
                           array('landoj' => 'l',
                                 'partoprenoj' => 'pn',
                                 'partoprenantoj' => 'pa'),
                           array('l.ID = pa.lando',
                                 'pa.ID = pn.partoprenantoID',
                                 "pn.alvenstato != 'm'"),
                           "",
                           array("order" => $ordigo));
    $rez = sql_faru($sql);
    while($linio = mysql_fetch_assoc($rez)) {
        echo "<!-- linio: {$linio['ID']} -->";
        metu_tejomembro_tabellinion($linio);
    }
    echo "</table>\n<p>";
    if ($GLOBALS['rajtas_sxangxi']) {
        butono("sxangxu", "S^ang^u");
    }
    echo "</p>\n</form>\n";
}

/**
 * Kreas unu tabellinion.
 * @param array $datoj unu datumbaz-respondo-linio.
 */
function metu_tejomembro_tabellinion($datoj)
{
    echo "  <tr>\n";
    $id = $datoj['ID'];
    $antoID = $datoj['partoprenantoID'];
    echo "<td>" .
        donu_ligon("partrezultoj.php?partoprenidento=" .
                   $id, $id) .
        "</td><td>";
    if ($GLOBALS['rajtas_sxangxi']) {
    tenukasxe('malnovaUEAkodo['.$antoID.']',
              $datoj['ueakodo']);
    simpla_entajpejo("",
                     'novaUEAkodo['.$antoID.']',
                     $datoj['ueakodo'],
                     7);
    }
    else {
        echo $datoj['ueakodo'];
    }
    eoecho("</td><td>" .
            $datoj['personanomo'] . "</td><td>" .
            $datoj['nomo'] .        "</td><td>" .
            $datoj['urbo'] .        "</td><td>" .
            $datoj['landonomo'] .   "</td><td>");

    // TODO: formatado diversa, alikaze nur jes_ne(...).
    if (jesne_al_boolean($datoj['tejo_membro_laudire'])) {
        echo "jes";
    }
    else {
        echo "ne";
    }
    echo "</td><td>";
    if ($GLOBALS['rajtas_sxangxi']) {
        tenukasxe("malnovaKontrolita[".$id."]",
                  $datoj['tejo_membro_kontrolita']);
        simpla_entajpbutono('novaKontrolita['.$id.']',
                            $datoj['tejo_membro_kontrolita'],
                            'j');
        eoecho("<abbr title='estas membro'>jes</abbr>");
        echo "</td><td>";
        simpla_entajpbutono('novaKontrolita['.$id.']',
                            $datoj['tejo_membro_kontrolita'],
                            '?');
        eoecho( "<abbr title='ne kontrolita'>?</abbr>");
        echo "</td><td>";
        simpla_entajpbutono('novaKontrolita['.$id.']',
                            $datoj['tejo_membro_kontrolita'],
                            'n');
        eoecho("<abbr title='ne estas membro'>ne</abbr>");
        echo "</td><td>";
        simpla_entajpbutono('novaKontrolita['.$id.']',
                            $datoj['tejo_membro_kontrolita'],
                            'i');
        eoecho("<abbr title='ig^as surloke (nur metu tion dum akceptado!'>ig^as</abbr>");
    }
    else {
        switch($datoj['tejo_membro_kontrolita']) {
        case 'j':
            echo "jam antau^e";
            break;
        case 'n':
            echo "ne membras";
            break;
        case 'i':
            echo "ig^as surloke";
            break;
        case '?':
            echo "ne kontrolita";
            break;
        }
    }
    echo "</td>\n";
    echo "  </tr>\n";
}

function mangxu_kodoSxangxojn($malnova, $nova) {
    foreach($nova AS $id => $kodo) {
        if ($kodo != $malnova[$id]) {
            eoecho("<p>s^ang^as anto[#{$id}].ueakodo de ".
                   "'{$malnova[$id]}' al '{$kodo}'.</p>\n");
            sxangxu_datumbazon('partoprenantoj',
                               array('ueakodo'
                                     => $kodo),
                               $id);
        }
    }
}

function mangxu_kontrolitaSxangxojn($malnova, $nova) {
    foreach($nova AS $id => $val) {
        if ($val != $malnova[$id]) {
            eoecho("<p>s^ang^as eno[#{$id}].kontrolita de ".
                   "'{$malnova[$id]}' al '{$val}'.</p>\n");
            sxangxu_datumbazon('partoprenoj',
                               array('tejo_membro_kontrolita'
                                     => $val),
                               $id);
        }
    }
}


HtmlKapo();

if ($rajtas_sxangxi)  {

    eoecho("<h1>Kontrolado de UEA-membrecoj</h1>\n");
        
    if ($_POST['sendu'] == 'sxangxu') {
        eoecho("<h2>S^ang^oj</h2>\n");
        mangxu_kodoSxangxojn($_POST['malnovaUEAkodo'],
                             $_POST['novaUEAkodo']);
        mangxu_kontrolitaSxangxojn($_POST['malnovaKontrolita'],
                                   $_POST['novaKontrolita']);
        echo "<hr/>\n";
    }
 }

$ordigo = $_REQUEST['ordigo'] or
    $ordigo = "ueakodo,nomo,personanomo";

kreu_la_longan_liston($ordigo);



HtmlFino();