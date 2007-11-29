<?php

require_once('iloj/iloj.php');


session_start();
malfermu_datumaro();

kontrolu_rajton("inviti");

HtmlKapo();

if (($_REQUEST['sendu'] == 'Elektu') && $_REQUEST['invitpetoID'])
    {
        // ni uzas la saman identifikilon por la invitpetoj
        // kiel por la partoprenoj, cxar estas 1-1-rilato.

        $partoprenoID = $_REQUEST['invitpetoID'];
        if ($partoprenoID)
            {
                $_SESSION['partopreno'] = new Partopreno($partoprenoID);
            }
        else
            {
                erareldono("La invitpeto-identifikilo #" .
                           $_REQUEST['invitpetoID'] . " ne ekzistas.");
            }
    }


if (!$_SESSION['partopreno'])
    {
        // ne okazu
        erareldono("Forgesig^is la partopreno. Bonvolu unue elekti partoprenon..");
        echo "<form method='GET' action='invitpeto.php'>\n";
        entajpejo("Por specialaj uzoj, vi povas entajpi c^i tie la partopreno-identifikilon:", 'invitpetoID');
        send_butono("Elektu");
        echo "</form>";
        HtmlFino();
        exit();
    }

if($_SESSION['partoprenanto']->datoj['ID'] != $_SESSION['partopreno']->datoj['partoprenantoID'])
    {
        $_SESSION['partoprenanto'] = new Partoprenanto($_SESSION['partopreno']->datoj['partoprenantoID']);
    }

/*
 * ni difinas $partopreno_renkontigxo por uzi anstataux
 * $_SESSION['renkontigxo'], cxar gxi ja povus esti io alia
 * (se oni rigardas malnovan partoprenon, ekzemple).
 */ 
if ($_SESSION['partopreno']->datoj['renkontigxoID'] != $_SESSION['renkontigxo']->datoj['ID'])
    {
        $partopreno_renkontigxo = new Renkontigxo($_SESSION['partopreno']->datoj['renkontigxoID']);
    }
 else
     {
         $partopreno_renkontigxo = $_SESSION['renkontigxo'];
     }


if ($_REQUEST['sendu'] && ($_REQUEST['sendu'] != 'Elektu'))
    {
        eoecho("<!-- ");
        var_export($_REQUEST);
        eoecho("-->");
    }


switch($_REQUEST['sendu'])
    {
    case 'Kreu':
        $peto = new Invitpeto();
        $peto->kopiu();
        $peto->skribu_kreante_kun_ID();
        $_SESSION['partopreno'] = new Partopreno($peto->datoj['ID']);
        eoecho("<p>Aldono de invitpeto sukcesis.</p>");
        break;

    case 'Sxangxu':
        $peto = new Invitpeto($_REQUEST['ID']);
        $peto->kopiu();
        $peto->skribu();
        $_SESSION['partopreno'] = new Partopreno($peto->datoj['ID']);
        eoecho("<p>S^ang^o de invitpeto sukcesis.</p>");
        break;

    default:
        // faru nenion
        break;
    }


$aktuala_invitpeto = $_SESSION['partopreno']->sercxu_invitpeton();

if ($aktuala_invitpeto)
    {
        eoecho ("<h1>S^ang^u invitpeto-datojn</h1>");
    }
else
    {
        eoecho ("<h1>Nova invitpeto</h1>");
    }

echo "<form method='POST' action='invitpeto.php'>\n";
eoecho("<p>Ni redaktas invitpeton por la alig^o ");
ligu("partrezultoj.php?partoprenoidento=".$_SESSION['partopreno']->datoj['ID'],
     "#" . $_SESSION['partopreno']->datoj['ID']);
eoecho (" de " .
        $_SESSION['partoprenanto']->tuta_nomo() . " (#" .
        $_SESSION['partoprenanto']->datoj['ID'].") al la " .
        $partopreno_renkontigxo->datoj['mallongigo'] . ".</p>");
       
echo "<table>\n";
eoecho("<tr><th colspan='2'><h2>Informoj lau^ pasporto</h2></td></tr>");
tabelentajpejo("Pasportnumero", 'pasportnumero',
               $aktuala_invitpeto->datoj['pasportnumero'], 30);
tabelentajpejo("Familia nomo", 'pasporta_familia_nomo',
               $aktuala_invitpeto->datoj['pasporta_familia_nomo'], 30);
tabelentajpejo("Persona(j) nomo(j)", 'pasporta_persona_nomo',
               $aktuala_invitpeto->datoj['pasporta_persona_nomo'], 30);
granda_tabelentajpejo("Adreso", 'pasporta_adreso',
                      $aktuala_invitpeto->datoj['pasporta_adreso'], 50, 5);

eoecho("<tr><th colspan='2'><h2>Kien sendi la invitilon?</h2></td></tr>");
tabelentajpejo("Faksnumero por sendi la invitilon",
               'senda_faksnumero',
               $aktuala_invitpeto->datoj['senda_faksnumero'], 30);
granda_tabelentajpejo("Adreso", 'senda_adreso',
                      $aktuala_invitpeto->datoj['senda_adreso'], 50, 5);

eoecho("<tr><th colspan='2'><h2>Internaj informoj</h2></td></tr>");

eoecho("<tr><th>Partopreno-ID</th><td>#".
       $_SESSION['partopreno']->datoj['ID'] .
       "</td></tr>");

eoecho("<tr><th>C^u sendi invitleteron?</th><td>");
entajpbutono("Decidu poste", 'invitletero_sendenda',
             $aktuala_invitpeto->datoj['invitletero_sendenda'],
             '?', '?', "|", "kutima");
entajpbutono("Jes", 'invitletero_sendenda',
             $aktuala_invitpeto->datoj['invitletero_sendenda'],
             'j', 'j', "|");
entajpbutono("Ne", 'invitletero_sendenda',
             $aktuala_invitpeto->datoj['invitletero_sendenda'],
             'n', 'n', "");
eoecho("</td></tr>");

tabelentajpejo("Sendodato de Invitletero",
               'invitletero_sendodato',
               $aktuala_invitpeto->datoj['invitletero_sendodato'], 11);

echo "</table>";

echo "<p>";
tenukasxe('ID', $_SESSION['partopreno']->datoj['ID']);
if ($aktuala_invitpeto)
    {
        butono('Sxangxu', "S^ang^u");
    }
else
    {
        butono("Kreu", "Kreu");
    }

if ($_SESSION['sekvontapagxo'])
    {
        ligu($_SESSION['sekvontapagxo'], "Ne s^ang^u kaj reiru");
    }
else
    {
        ligu("partrezultoj.php", "Ne s^ang^u kaj reiru");
    }

echo "</form>";


?>