<?php

  /**
   * Kreado kaj redaktado de entajpantoj (= uzantoj de la administrilo).
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2004-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   */

//define("DEBUG", TRUE);
require_once ("iloj/iloj.php");
session_start();

malfermu_datumaro();


function sxangxuEntajpanton(&$entajpanto)
{
    if ($_POST['ID'] != $entajpanto->datoj['ID']) {
        // oni nur sxangxu siajn proprajn datojn
        darf_nicht_sein("ID = '" .$_POST['ID']. "' != '" .
                        $entajpanto->datoj['ID']. "' = entajpanto-ID!");
        return;
    }
    $sxangxita = false;

    if($_POST['sendanto_nomo'] and
       $_POST['sendanto_nomo'] != $entajpanto->datoj['sendanto_nomo'])
        {
            $entajpanto->datoj['sendanto_nomo'] = $_POST['sendanto_nomo'];
            $sxangxita = true;
        }
    
    if($_POST['retposxtadreso'] and
       $_POST['retposxtadreso'] != $entajpanto->datoj['retposxtadreso'])
        {
            $entajpanto->datoj['retposxtadreso'] = $_POST['retposxtadreso'];
            $sxangxita = true;
        }

    if($_POST['partoprenanto_id'] != $entajpanto->datoj['partoprenanto_id'])
        {
            $num_id = intval($_POST['partoprenanto_id']);
            if ($num_id) {

                $sql = datumbazdemando("ID", "partoprenantoj",
                                       "ID = '$num_id'");
                $rez = sql_faru($sql);
                if (mysql_num_rows($rez) < 1) {
                    erareldono("Ne ekzistas partoprenanto kun ID = #" . $num_id
                                . " - mi ne s^ang^as vian " .
                               "partoprenanto-Identigilon!");
                }
                else {
                    $entajpanto->datoj['partoprenanto_id'] = $num_id;
                    $sxangxita = true;
                }
            }
            else {
                $entajpanto->datoj['partoprenanto_id'] = null;
                $sxangxita = true;
            }
        }

    if ($_POST['pasvorto']) {
        if ($_POST['pasvorto'] == $_POST['dua_pasvorto']) {
            $entajpanto->datoj['kodvorto'] = $_POST['pasvorto'];
            $entajpanto->skribu();
            eoecho ("<p>Mi s^ang^is la pasvorton. Bonvolu denove ");
            ligu("komenci.php", "ensaluti");
            echo "!</p>";
            protokolu("pasvorts^ang^o");
            $_SESSION["kodvorto"] = "";
            $_SESSION["kodnomo"] = "";
            unset($_SESSION['kkren']);
            session_destroy();
            HtmlFino();
            exit();
        }
        else {
            erareldono("Bonvolu entajpi dufoje la saman pasvorton!");
            // erarmesagxo
        }
    }
    if ($sxangxita) {
        $entajpanto->skribu();
        eoecho ("<p>Mi s^ang^is viajn entajpanto-detalojn.</p>");
    }
    else {
        echo "<!-- nenio sxangxenda -->";
    }
}


function montru_uzantoformularon($entajpanto) {

    eoecho("<h2>Via Uzanto-konto</h2>\n");

    echo ("<form action='uzanto.php' method='post'>\n<table>");

    tabela_kasxilo("ID", 'ID', $entajpanto->datoj['ID']);
    tabela_montrilo("Salutnomo", $entajpanto->datoj['nomo'] . " &mdash; " .
                  "Vi uzas tiun nomon por ensaluti en la programon.");
    tabelentajpejo("Sendanto-nomo", 'sendanto_nomo',
                   $entajpanto->datoj['sendanto_nomo'], 20,
                   "Via vera nomo - uzebla kiel sendanto-nomo, se vi havas" .
                   "la rajton 'retumi'.");
    tabelentajpejo("Retpos^tadreso", 'retposxtadreso',
                   $entajpanto->datoj['retposxtadreso'], 20,
                   "Via retpos^tadreso - uzata de la c^ef-administranto, ".
                   "por atingi vin, kaj krome uzebla kiel sendanto-adreso, " .
                   "se vi havas la rajton 'retumi'.");
    tabelentajpejo("Partoprenanto-ID", 'partoprenanto_id',
                   $entajpanto->datoj['partoprenanto_id'], 7,
                   "Se vi ankau^ mem partoprenas la arang^on, metu c^i tie vian".
                   " Partoprenanto-identigilon. Tio ebligas iujn specialajn " .
                   "funkciojn, kiel vidi notojn de tiu partoprenanto c^e " .
                   "<em>viaj notoj</em>.");
    tabelentajpejo("Nova pasvorto", 'pasvorto', "", 20,
                   "Via pasvorto, uzata por ensaluti la programon. " .
                   "G^i ne montrig^as c^i tie, sed vi povas entajpi novan, " .
                   "se vi volas s^ang^i g^in.", '', '', 'j');
    tabelentajpejo("Nova pasvorto (ripeto)", 'dua_pasvorto', "", 20,
                   "Ripetu c^i tie vian novan pasvorton (se vi volas s^ang^i ".
                   " g^in.", '', '', 'j');
    
    echo "</table>\n<p>";
    send_butono("S^ang^u");
    echo "</p>\n</form>\n";
}


function montru_rajtojn($entajpanto) {
	eoecho ("<h2>Viaj rajtoj</h2>\n");
	echo "<table>\n";
 	foreach($GLOBALS['rajtolisto'] AS $ero)
	{
        eoecho("<tr><th>" . $ero['alias'] . "</th><td>" .
        		( $entajpanto->datoj[$ero['rajto']] == 'J' ? "[X]" : "[_]") . "</td></tr>\n");
	}
	echo "</table>\n";
}


/***** Jen la teksto *******/



HtmlKapo();

$entajpanto = new Entajpanto($_SESSION['kkren']['entajpanto']);

if (!empty($_POST['sendu'])) {
    sxangxuEntajpanton($entajpanto);
 }

montru_uzantoformularon($entajpanto);

montru_rajtojn($entajpanto);

echo "<p>";
ligu('sercxrezultoj.php?elekto=notoj_de_entajpanto&entajpantoid=' . $entajpanto->datoj['ID'],
     "Viaj notoj");
echo "</p>\n";


// TODO: pliaj informoj, ekzemple rajtoj, ligoj al notoj, ktp.

HtmlFino();


?>
