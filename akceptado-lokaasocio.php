<?php

/*
 * Akceptado de partoprenantoj
 *
 * Pasxo 4 - membreco cxe loka asocio
 *
 */


require_once ('iloj/iloj.php');

session_start();

malfermu_datumaro();

kontrolu_rajton("akcepti");
require_once('iloj/iloj_akceptado.php');


  $partoprenanto = $_SESSION["partoprenanto"];
  $partopreno = $_SESSION['partopreno'];
  if($_REQUEST['partoprenidento'] and
	  $_REQUEST['partoprenidento'] != $partopreno->datoj['ID'])
	{
		// iu malgxusta okazis - ni rekreu la $partoprenon.
		$partopreno = new Partopreno($_REQUEST['partoprenidento']);
		$_SESSION['partopreno'] = $partopreno;
		$partoprenanto = new Partoprenanto($partopreno->datoj['partoprenantoID']);
		$_SESSION['partoprenanto'] = $partoprenanto;
	}

  // la persona pronomo (li aux sxi)
  $ri = $partoprenanto->personapronomo;
  $Ri = ucfirst($ri);

akceptado_kapo("lokaasocio");


if ($_POST['sendu'])
    {
        switch($_POST['ago'])
            {
            case 'n':
            case 'a':
            case 'h':
                $partopreno->datoj['surloka_membrokotizo'] = $_POST['ago'];
                $partopreno->datoj['membrokotizo'] = 0;
                break;
            case '?':
                $partopreno->datoj['surloka_membrokotizo'] = '?';
                // TODO: ebligu pluiron
                break;
            case 'j':
            case 'i':
            case 'k':
                $partopreno->datoj['surloka_membrokotizo'] = $_POST['ago'];
                $partopreno->datoj['membrokotizo'] =
                    $_POST['kotizo-'.$_POST['ago']];
                break;
            default:
                darf_nicht_sein("Nekonata ago-tipo: " . $_POST['ago']);
            }
        $partopreno->skribu();
        $partopreno = new Partopreno($partopreno->datoj['ID']);
    }


	if (!necesas_lokaasocio_traktado())
	{
		eoecho ("<p>Lau^ la opinio de la programo ne necesas " .
		        deviga_membreco_nomo . "-traktado por tiu ulo. Vi" .
                " (kiel homo) kompreneble tamen rajtas fari tion.</p>");
	}

eoecho ("<form action='akceptado-lokaasocio.php' method='POST'>\n");

eoecho ("<ul><li>" .
        "Foje kelkaj homoj nepre ig^u membroj de iuj organizoj. Je tiu" .
        " renkontig^o ");


if ($partoprenanto->datoj['GEJmembro'] == 'J')
    {
        $defauxlto = 'j'; // jam estas membro kaj rekotizas
    }
else
    {
        $defauxlto = 'i'; // igxos nova membro kaj kotizas
    }

if (deviga_membreco_tipo == 'landa')
    {
        eoecho ("homoj log^anta en " . eltrovu_landon(HEJMLANDO) .
                " devas esti membro de " . deviga_membreco_nomo .
                " por la jaro " . deviga_membreco_jaro. ". Nemembroj ".
                "devos pagi krompagon.");
        if ($partoprenanto->datoj['lando'] != HEJMLANDO)
            {
                $estas_eksterlandano=true;
                $defauxlto = 'n'; // ne devas igxi membro
            }
    }
 else if (deviga_membreco_tipo=='monda')
     {
         eoecho("c^iuj partoprenantoj devas esti membro de " .
                 deviga_membreco_nomo . " por la jaro " .
                deviga_membreco_jaro . ". Nemembroj devos pagi " .
                "krompagon.");
     }
else
    {
        eoecho("ne estas deviga membreco.");
        $defauxlto = 'n'; // ne devas igxi membro
    }
eoecho ("</li>\n");


if (deviga_membreco_nomo == "GEA/GEJ")
    {
        /* speciale por GEJ/GEA */
        if (strcmp($partoprenanto->datoj['naskigxdato'],
                   (intval(deviga_membreco_jaro) - 27) . '-01-01') > 1)
            {
                // tro agxa por GEJ
                $ma_ligo = '/datumbazoj/gea_ma/index.php';
                $ma_nomo = "GEA-membrodatumbazo";
                $ma_kadro = 'gea_ma';
                $asocio_nomo = "GEA";
            }
        else
            {
                $ma_ligo = '/dej/ima-neu/index.php';
                $ma_nomo = "GEJ-membrodatumbazo";
                $ma_kadro = "gej_ma";
                $asocio_nomo = "GEJ";
            }
        eoecho("<li>Kontrolu en la ");
        ligu($ma_ligo, $ma_nomo, $ma_kadro);
        eoecho (", c^u {$ri} jam estas membro de " .$asocio_nomo.".</li>\n");
        eoecho("<li>Se jes, kontrolu, c^u {$ri} jam pagis por " .
               deviga_membreco_nomo . ".)</li>\n");
    }
//else
//    echo deviga_membreco_nomo;

if ($partopreno->datoj['surloka_membrokotizo'] =='?' )
    {
        $ago = $defauxlto;
    }
 else
     {
         $ago = $partopreno->datoj['surloka_membrokotizo'];
         
     }

eoecho ("<li>Lau^ la aktuala enhavo de la datumbazo, {$ri} <em>" .
        $partopreno->membrokotizo() . "</em>.</li>");

eoecho("<li><h3>Kion ni faru?</h3>");

/* n */ entajpbutono("<p>", 'ago', 'n', $ago, 'n',
                     "{$Ri} ne estas membro kaj ne devas esti. ".
                     ($estas_eksterlandano ?
                      "<strong>(Elektu tiun por eksterlandano!)</strong>":"").
                     "</p>");

/* a */ entajpbutono("<p>", 'ago', 'a', $ago, 'a',
                     "{$Ri} estas membro, kaj jam pagis kotizon por " .
                     deviga_membreco_jaro . " (au^ ne devas pagi, au^ donis" .
                     " enkasigrajtigon).</p>");

/* j */ entajpbutono("<p>", 'ago', 'j', $ago, 'j',
                     "{$Ri} jam estas membro kaj nun pagas la kotizon de ");
simpla_entajpejo("", 'kotizo-j', $partopreno->datoj['membrokotizo'],
          "10", "", "", " E^ kun la renkontig^a kotizo.</p>");

/* i */ entajpbutono("<p>", 'ago', 'i', $ago, 'i',
                      "{$Ri} ig^as nova membro kaj pagas la kotizon de ");
simpla_entajpejo("", 'kotizo-i',
          $partopreno->datoj['membrokotizo'],
          "10", "", "", " E^ kun la renkontig^a kotizo.</p>");

/* h */ entajpbutono("<p>", 'ago', 'h', $ago, 'h',
                     "{$Ri} ig^is nova membro, sed ne devas pagi nun " .
                     "(ekzemple pro enkasigrajtigo).");

/* k */ entajpbutono("<p>", 'ago', 'k', $ago, 'k',
                         "{$Ri} devus ig^i (au^ resti) membro, sed" .
                         " ne ne volas kaj preferas pagi");
simpla_entajpejo(" la krompagon de ", 'kotizo-k', 
           $partopreno->datoj['membrokotizo'],
           "10", "", "", " E^ kun la renkontig^a kotizo.</p>");
/* ? */  entajpbutono("<p>", 'ago', '@@@', $ago, '?',
                      "Res^altu la datumbazeron al <em>ne jam traktita</em>" .
                      " &ndash; ni pripensos poste kaj tiam dau^rigos la" .
                      " akceptadon.");

echo "</li></ul>\n<p>";



tenukasxe('partoprenidento', $partopreno->datoj['ID']);

send_butono("S^ang^u");



if (necesas_lokaasocio_traktado())
	{
        eoecho ("Necesas s^ang^i la aktualan staton antau^" .
                " pluiri al la sekva pas^o. ");
    }
else
    {
        ligu_sekvan();
    }


echo "</p>\n</form>\n";



HtmlFino();

?>
