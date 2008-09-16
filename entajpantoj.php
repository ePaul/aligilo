<?php


  /**
   * Kreado kaj redaktado de entajpantoj (= uzantoj de la administrilo).
   *
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage pagxoj
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   * @todo plibonigi la HTML-kodon, strukturigi al funkcioj/objektoj.
   */


  /**
   */

  //define("DEBUG", TRUE);
require_once ("iloj/iloj.php");
session_start();

malfermu_datumaro();

kontrolu_rajton("teknikumi");

HtmlKapo();

{

    /**
     * interna nomo, longa nomo, mallongigo por tabeltitolo
     */
    $tmplisto = array(array($x = "aligi",        $x,           "al&shy;igi"),
                      array($x = "vidi",         $x,           "vi&shy;di"),
                      array(     "sxangxi",      "s^ang^i",    "s^an&shy;g^i"),
                      array(     "cxambrumi",    "c^ambrumi",  "c^ambr."),
                      array(     "ekzporti",     "eksporti",   "eksp."),
                      array($x = "statistikumi", $x,           "stat."),
                      array(     "mono",      "entajpi monon", "mo&shy;no"),
                      array($x = "estingi",      $x,           "est."),
                      array($x = "retumi",       $x,           "ret."),
                      array($x = "rabati",       $x,           "rab."),
                      array($x = "inviti",       $x,           "inv."),
                      array($x = "administri",   $x,           "ad&shy;min."),
                      array($x = "akcepti",      $x,           "akc."),
                      array($x = "teknikumi",    $x,           "tekn."));
    //    echo "<!--";
    //    var_export($tmplisto);
    //    echo "-->";
  foreach($tmplisto AS $ero)
	{
        $rajtolisto[]= array("rajto" => $ero[0],
                             "alias" => $ero[1],
                             "mallongigo" => $ero[2]);
	}
  unset($tmplisto);
}



// echo "<!--\n";
// var_export($rajtolisto);
// echo "-->\n";

function forigu_vere() {
    $forigu = $_REQUEST['forigu'];
    forigu_el_datumbazo("entajpantoj", $forigu);
    // ni forgesas pri cxiu ajn noto, ke gxi estis por tiu cxi entajpanto.
    forigu_el_datumbazo("notoj_por_entajpantoj",
                        array("entajpantoID" => $forigu));
    eoecho("<p>Vi j^us forigis la entajpanton #".$forigu.".</p>");
}

function forigu_demando() {
    eoecho("<h2>Forigo de entajpanto</h2>\n");

    $entajpanto = new Entajpanto($_REQUEST['forigu']);
    $linio = $entajpanto->datoj;
	  
    echo "<table>\n";
    eoecho("<tr><th>ID</th><td>{$linio['ID']}</td></tr>\n");
    eoecho("<tr><th>Salutnomo</th><td>{$linio['nomo']}</td></tr>\n");
    eoecho("<tr><th>Retadreso</th><td>{$linio['retposxtadreso']}</td>\n");
    eoecho("<tr><th>Partoprenanto-ID</th><td>{$linio['partoprenanto_id']}</td>\n");
    eoecho("<tr><th>Sendantonomo</th><td>{$linio['sendanto_nomo']}</td>\n");
    foreach($GLOBALS['rajtolisto'] AS  $ero)
		{
            eoecho("<tr><th>" . $ero['alias']. "</th><td>" .
                   ($linio[$ero['rajto']] == 'J' ? "[X]" : "[_]")
                   ."</td>\n");
		}
    eoecho("</table>\n");
    eoecho("<p>C^u vi vere volas forigi tiun c^i entajpanton?");
    ligu_butone("entajpantoj.php?forigu=" . $linio['ID'], "Jes",
                array("vere" =>"jes"));
    ligu("entajpantoj.php?redaktu=" . $linio['ID'],
         "Ne, reen al redaktado");
    ligu("entajpantoj.php", "Ne, reen al la listo");
    HtmlFino();
}

function savu_entajpanton() {
    debug_echo("<!-- POST: " . var_export($_POST, true) . "-->");
    $entajpanto = new Entajpanto($_REQUEST['ID']);
    $entajpanto->kopiu();
    if ($_POST['pasvortsxangxo'] == 'JES') {
        if ($_POST['pasvorto']) {
            $entajpanto->datoj['kodvorto'] = $_POST['pasvorto'];
            eoecho ("<p>S^ang^o de pasvorto!</p>");
        }
        else {
            erareldono("Vi petis pri s^ang^o de pasvorto, ".
                       "sed ne donis novan!");
        }
    }
    if ($entajpanto->datoj['ID'] == 'nova') {
        $entajpanto->skribu_kreante();
    }
    else {
        $entajpanto->skribu();
    }
    eoecho("<p> Savis Entajpanton #" . $entajpanto->datoj['ID'] . ".</p>\n");

    if ($_REQUEST['redaktu'] == 'nova') {
        $_REQUEST['redaktu'] = $entajpanto->datoj['ID'];
    }
    
}

function entajpanto_redaktilo($entajpanto)
{

  echo "<form method='POST' action='entajpantoj.php'>\n";
  echo "<table>\n";

  $linio = $entajpanto->datoj;

  tabela_kasxilo("ID", 'ID', $linio['ID']);

  
  tabelentajpejo("Salutnomo", "nomo", $linio['nomo'], 20);
  tabelentajpejo("Retpos^ta adreso", "retposxtadreso",
                 $linio['retposxtadreso'], 20);
  tabelentajpejo("Retpos^tsenda nomo", "sendanto_nomo",
                 $linio['sendanto_nomo'], 30,
                 "Uzata por sendado de ne-au^tomataj mesag^oj");
  entajpbokso("<tr><th>", "pasvortsxangxo", "", "JES", "JES");
  entajpejo("Nova pasvorto </th><td>", "pasvorto", "", 20, "","",
            "nur entajpu, se estas s^ang^o (kaj tiam metu hokon antau^e)</td>",
            "j");

	//  entajpboksokajejo("pasvortsxangxo", "", "jes", "jes",
	//					  "Nova pasvorto: ", '', 'kodvorto', '', 20, 'Mankas pasvorto.');

  //  echo("<br/>\n");
  tabelentajpejo("Partoprenanto-ID ", "partoprenanto_id",
                 $linio['partoprenanto_id'], 6,
                 "(0 = ne havas partoprenanton)");
  echo "<table>\n";
  
  eoecho ("</p>\n<p>Li/s^i havu la rajton ...</p>");
  echo "<table style='margin-left: 2em; '>\n";
  foreach($GLOBALS['rajtolisto'] AS $ero)
	{
        entajpbokso("<tr><td>", $ero['rajto'], $linio[$ero['rajto']],
                    'J', 'J',  "</td><td>" . $ero['alias'] . "</td></tr>");
	}
  echo "</table>\n";
  eoecho ("<p> ... en la datumbazo</p>");

  entajpbokso("<p>", "redaktu", "", "jes", $linio['ID'],
			  "Pluredaktu tiun c^i entajpanton.", "", "sen kasxa");
  echo "<br/>\n";
  send_butono("S^ang^u");
  ligu("entajpantoj.php", "Reen al la listo");
  if($linio['ID'] != "nova") {
      ligu("entajpantoj.php?forigu=" . $linio['ID'],
           "Forigu tiun c^i entajpanton!");
  }
  echo "</p>";
  echo "</form>\n";
    
}


// montru tabelon de cxiuj entajpantoj


function listu_cxiujn_entajpantojn()
{

$sql = datumbazdemando(array_merge(array("ID", "nomo", "retposxtadreso",
                                         "partoprenanto_id", 'sendanto_nomo'),
								   array_map("reset", $GLOBALS['rajtolisto'])),
					   "entajpantoj");

$kruco = array('J' => "<strong>X</strong>",
				'N' => " _ ");

$anstatauxoj = array_fill(4,
                          count($GLOBALS['rajtolisto'])+1,
                          &$kruco);

$kolumnoj = array(/* kolumnoj */
			 array('ID', '', 'red.','z', 'entajpantoj.php?redaktu=XXXXX',
				   'partoprenanto_id'),
			 array('nomo', 'nomo', 'XXXXX', 'l','',''),
			 array('retposxtadreso', 'ret&shy;pos^to','@','z','mailto:XXXXX', -1),
             array('sendanto_nomo','Plena nomo', 'XXXXX', 'l', '', ''),
			 array('partoprenanto_id', 'p-anto', 'XXXXX', 'r',
				   'partrezultoj.php?partoprenantoidento=XXXXX',
                   'partoprenanto_id'));

 foreach($GLOBALS['rajtolisto'] AS $ero) {
    $kolumnoj[]= array($ero['rajto'], $ero['mallongigo'],
                       "XXXXX", 'z', '', '');
}



sercxu($sql,
	   array("nomo", "asc"),
       $kolumnoj,
	   array(/*sumoj*/),
	   "entajpantoj",
	   array(/* pliaj parametroj */
			 "Zeichenersetzung" => $anstatauxoj),
	   0 /* formato de la tabelo */,
	   "Jen listo de c^iuj entajpantoj.", 0, "ne");

ligu("entajpantoj.php?redaktu=nova", "Kreu novan entajpanton");


HtmlFino();

}


/**
 * nun la agado
 */


if($_REQUEST['forigu'])
    {
        if($_POST['vere'] == 'jes')
            {
                forigu_vere();
            }
        else
            {
                forigu_demando();
                return;
            }
    }

if ($_REQUEST['sendu'])
    {
        savu_entajpanton();
    }


if($_REQUEST['redaktu'])
    {
        if (is_numeric($_REQUEST['redaktu'])) {
        
            $entajpanto = new Entajpanto($redaktu);
        
            eoecho("<h2>Redakto de entajpanto</h2>\n");
        }
        else {
            $_REQUEST['redaktu'] = "nova";
            $entajpanto = new Entajpanto();
            $entajpanto->datoj['ID'] = "nova";
            eoecho("<h2>Kreado de nova entajpanto</h2>\n");
        }

        entajpanto_redaktilo($entajpanto);

        HtmlFino();
        return;
    }


listu_cxiujn_entajpantojn();


?>