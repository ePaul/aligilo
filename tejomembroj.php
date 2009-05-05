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


/**
 * @param asciistring $ordigo la nomo de la kampo, laux kiu estu
 *                   ordigita la listo.
 */
function kreu_la_longan_liston($ordigo)
{
    eoecho("<h2>Listo de TEJO-membroj</h2>\n");
    echo "<form action='tejomembroj.php' method='POST'>\n";
    echo "<table id='tejomembreco'>\n";
    //    eoecho ("<tr><th>");
    
    $sql = datumbazdemando(array('pa.nomo',
                                 'pa.personanomo',
                                 'pa.sekso',
                                 'pa.ueakodo',
                                 'pa.urbo',
                                 'pn.ID',
                                 'pn.tejo_membro_laudire',
                                 'pn.tejo_membro_kontrolita',
                                 'l.nomo' => "landonomo"),
                           array('landoj' => 'l',
                                 'partoprenoj' => 'pn',
                                 'partoprenantoj' => 'pa'),
                           array('l.ID = pa.lando',
                                 'pa.ID = pn.partoprenantoID'),
                           "",
                           array("order" => $ordigo));
    $rez = sql_faru($sql);
    while($linio = mysql_fetch_assoc($rez)) {
        echo "<!-- linio: {$linio['ID']} -->";
        metu_tabellinion($linio);
    }
    echo "</table>\n<p>";
    echo "</p>\n</form>\n";
}

/**
 * @param array $datoj
 */
function metu_tabellinion($datoj)
{
    echo "  <tr>\n";
    $id = $datoj['ID'];
    echo "<td>" .
        donu_ligon("partrezultoj.php?partoprenoidento=" .
                   $id, $id) .
        "</td>";
    eoecho( "<td>" .
            $datoj['ueakodo'] .     "</td><td>" .
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
    tenukasxe("malnovaKontrolita[".$id."]",
              $datoj['tejo_membro_kontrolita']);
    simpla_entajpbutono('novaKontrolita['.$id.']',
                        $datoj['tejo_membro_kontrolita'],
                        'j');
    echo "</td><td>";
    simpla_entajpbutono('novaKontrolita['.$id.']',
                        $datoj['tejo_membro_kontrolita'],
                        '?');
    echo "</td><td>";
    simpla_entajpbutono('novaKontrolita['.$id.']',
                        $datoj['tejo_membro_kontrolita'],
                        'n');
    echo "</td>\n";
    echo "  </tr>\n";
}


HtmlKapo();


kreu_la_longan_liston("ueakodo");

HtmlFino();