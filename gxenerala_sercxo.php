<?php

if($_POST['sendu'] == 'dauxrigu')
{
  require_once("sercxoj.php");
  return;
}

require_once ('iloj/iloj.php');
require_once ('iloj/iloj_sercxo.php');

session_start();
malfermu_datumaro();

unset($_SESSION["partoprenanto"]);
unset($_SESSION["partopreno"]);


if (!rajtas("vidi"))
{
  ne_rajtas();
}

HtmlKapo();

eoecho("<h2>G^enerala Serc^o</h2>\n");

//ligu("sercxoj.php", "konservitaj serc^oj");
kasxeblaSercxoElektilo();

ligu("partsercxo.php", "Reen al la partoprenantoserc^o");
ligu("gxenerala_sercxo.php", "nova serc^o");

$valoroj = kopiuSercxon();
if ($_REQUEST['antauxa_sercxo'])
{
  trovuSercxon($_REQUEST['antauxa_sercxo'], $valoroj);
  //  $_POST['sendu'] = "sercxu
}

if ($_REQUEST['sendu'] == 'sercxu')
{
  montruRezulton($valoroj);
}

if (empty($valoroj))
{
  // defauxlta sercxo
  $valoroj = array("sercxo_tabelo_renkontigxo_uzu" => 'JES',
				   "sercxo_renkontigxo_ID_estasKriterio" => "JES",
				   "sercxo_renkontigxo_ID_elekto"
				   => array($_SESSION['renkontigxo']->datoj['ID']),
				   "sercxo_tabelo_partoprenantoj_uzu" => "JES",
				   "sercxo_partoprenantoj_ID_montru" => "JES",
				   "sercxo_partoprenantoj_nomo_montru" => "JES",
				   "sercxo_partoprenantoj_personanomo_montru" => "JES");
}

// echo "<!-- POST: \n";
// var_export($_POST);
// echo "\n valoroj: \n";
// var_export($valoroj);
// echo "-->\n";



metuKasxKontrolilon();



//echo "<input type='button' onClick='test()' value='Debug' />\n";

eoecho ("<h3>Serc^formularo</h3>\n");
ligu("/dej/vikio.pl?IS-Datenbazo/Gxenerala_sercxo", "Helpo pri la serc^o", "_blank");

echo "<form action='gxenerala_sercxo.php' method='POST' />\n";

eoecho("<table class='sercxilo'>\n");
eoecho("<colgroup>
  <col title='Kamponomo'/>
  <col title='C^u en la rezulto?'/>
  <col title='C^u serc^kriterio?'/>
  <col title='detala kriterio'/>
</colgroup>\n");

eoecho("<tr class='legendo'><th class='maldekstra' colspan='2'>C^u en la rezulto?&nbsp;</th><th colspan='2'>&nbsp;C^u serc^kriterio?</th>".
	   "</tr>\n");
eoecho("<tr class='legendo'><th class='maldekstra'>Kamponomo&nbsp;</th><th class='meza maldekstra'></th><th class='meza'></th><th>&nbsp;Detala kriterio</th></tr>\n");

sercxtabelkapo("Lando", "landoj", $valoroj);

// TODO:elektilo
sercxtabellinio("ID",             'landoj', 'ID', $valoroj, "landoid");
sercxtabellinio("Nomo",           'landoj', 'nomo', $valoroj, "landonomo");
sercxtabellinio("Loka nomo",   'landoj', 'lokanomo',    $valoroj);
sercxelektolinio("Landokategorio", 'landoj', 'kategorio', $valoroj,
				 array("A" => "A", "B" => "B", "C" => "C"));

sercxtabelkapo("Partoprenanto", "partoprenantoj", $valoroj);

sercxtabellinio("ID",             'partoprenantoj', 'ID',         $valoroj,
				"ID", 'partrezultoj.php?partoprenantoidento=XXXXX',
				'ppn&shy;toID');
sercxtabellinio("Nomo",           'partoprenantoj', 'nomo',       $valoroj);
sercxtabellinio("Persona nomo",   'partoprenantoj', 'personanomo',    $valoroj);
sercxtabellinio("S^ildnomo",      'partoprenantoj', 'sxildnomo',      $valoroj);
sercxelektolinio("Sekso",         'partoprenantoj', 'sekso',          $valoroj,
				 array('i' => 'ina', 'v' => 'vira'));
sercxtabellinio("Naskig^dato",    'partoprenantoj', 'naskigxdato',    $valoroj);
sercxtabellinio("Adresaldonaj^o", 'partoprenantoj', 'adresaldonajxo', $valoroj);
sercxtabellinio("Strato",         'partoprenantoj', 'strato',         $valoroj);
sercxtabellinio("Pos^tkodo",      'partoprenantoj', 'posxtkodo',      $valoroj);
sercxtabellinio("Urbo",           'partoprenantoj', 'urbo',           $valoroj);
sercxtabellinio("Provinco",       'partoprenantoj', 'provinco',       $valoroj);
sercxtabellinio("S^ildlando",     'partoprenantoj', 'sxildlando',     $valoroj);
sercxtabellinio("UEA-kodo",       'partoprenantoj', 'ueakodo',        $valoroj);
sercxtabellinio("Telefono",       'partoprenantoj', 'telefono',       $valoroj);
sercxtabellinio("Telefakso",      'partoprenantoj', 'telefakso',      $valoroj);
sercxtabellinio("retpos^to",      'partoprenantoj', 'retposxto',      $valoroj);

// eoecho("</table>\n");

sercxtabelkapo("Partopreno", "partoprenoj", $valoroj);

echo "</th></tr>\n";

sercxtabellinio("ID",          'partoprenoj', 'ID',             $valoroj,
				"ppnoID", 'partrezultoj.php?partoprenoID=XXXXX',
				'ppn&shy;oID');
sercxtabellinio("Ag^o",        'partoprenoj', 'agxo',           $valoroj, "agxo");
sercxelektolinio("Komencanto", 'partoprenoj', 'komencanto',     $valoroj,
				 array('' => "ne elektis", "J" => "Jes", "N" => "Ne"));
sercxtabellinio("Rimarkoj",    'partoprenoj', 'rimarkoj',        $valoroj);
sercxelektolinio("Invitletero mendita", 'partoprenoj', 'invitletero', $valoroj,
				 array('' => 'ne elektis', 'J' => "jes", "N" => "ne"));
sercxtabellinio("Invitilo sendita", 'partoprenoj', 'invitilosendata', $valoroj);
sercxtabellinio("Pasportnumero",    'partoprenoj',    'pasportnumero',   $valoroj);

sercxtabellinio("Sendo de 1-a konfirmilo", 'partoprenoj', '1akonfirmilosendata', $valoroj);
sercxtabellinio("Sendo de 2a konfirmilo",  'partoprenoj', '2akonfirmilosendata', $valoroj);
sercxelektolinio("Partoprentipo",          'partoprenoj', 'partoprentipo', $valoroj,
				 array('t' => 'tuttempe', 'p' => 'parttempe'));
sercxtabellinio("De",    'partoprenoj', 'de',    $valoroj);
sercxtabellinio("G^is",  'partoprenoj', 'gxis', $valoroj);

sercxelektolinio("Mang^maniero", "partoprenoj", "vegetare", $valoroj,
				 array('' => "ne elektis", "J" => 'vegetarano',
					   'N' => 'viandmang^anto', 'A' => 'vegano'));
sercxelektolinio("GEJ-membro",           'partoprenoj', 'GEJmembro', $valoroj,
				 array('' => 'ne elektis', 'J' => 'Jes', 'N' => 'Ne'));
sercxelektolinio("TEJO-membro lau^dire",   'partoprenoj', 'tejo_membro_laudire', $valoroj,
				 array('' => 'ne elektis', 'j' => 'Jes', 'n' => 'Ne'));
sercxelektolinio("TEJO-membro kontrolita", 'partoprenoj',
                 'tejo_membro_kontrolita', $valoroj,
				 array('?' => 'ne jam kontrolita', 'j' => 'estas membro',
			          'n' => 'ne estas membro', 'i' => "ig^as membro surloke"));
sercxtabellinio("TEJO-membrokotizo", 'partoprenoj', 'tejo_membro_kotizo', $valoroj);

sercxelektolinio("C^u surloka membrokotizo?", 'partoprenoj', 'surloka_membrokotizo', $valoroj,
				 array('j' => 'Jes, membrokotizo',
					   'n' => 'ne', 'k' => 'anstatau^e krompago'));
sercxtabellinio("membrokotizo (au^ krompago)", 'partoprenoj', 'membrokotizo',   $valoroj);
sercxelektolinio("KKRen-membro",  'partoprenoj', 'KKRen',  $valoroj,
				array('' => 'ne elektis', 'J' => 'Jes', 'n' => 'Ne'));
sercxelektolinio("Domotipo",      'partoprenoj', 'domotipo', $valoroj,
				array('J' => 'junulargastejo', 'M' => 'memzorgantejo'));
sercxelektolinio("kunmang^as",    'partoprenoj', 'kunmangxas', $valoroj,
            array('J' => 'jes', 'N' => 'ne', '' => 'ne elektis'));
sercxtabellinio("Kun kiu",        'partoprenoj', 'kunkiu',      $valoroj);
sercxtabellinio("Kun kiu (ID)",   'partoprenoj', 'kunkiuID',  $valoroj,
				"kkID", 'partrezultoj.php?partoprenantoidento=XXXXX');
sercxelektolinio("C^ambrotipo",   'partoprenoj', 'cxambrotipo', $valoroj,
				 array('g' => 'gea c^ambro', 'u' => 'unuseksa c^ambro'),
				 "cxambrotipdeziro");
sercxelektolinio("Mendis dulitan c^ambron", 'partoprenoj', 'dulita', $valoroj,
				 array('J' => 'jes', 'N' => 'ne', '' => 'ne elektis'),
				 "dulitadeziro");
sercxelektolinio("Mendis ekskursbileton",   'partoprenoj', 'ekskursbileto', $valoroj,
				 array('J' => 'jes', 'N' => 'ne'));
sercxtabellinio("Tema programo:",           'partoprenoj', 'tema',    $valoroj);
sercxtabellinio("Distra programo:",         'partoprenoj', 'distra',  $valoroj);
sercxtabellinio("Vespera programo:",        'partoprenoj', 'vespera', $valoroj);
sercxtabellinio("Muzika programo:",         'partoprenoj', 'muzika',  $valoroj);
sercxtabellinio("Nokta programo:",          'partoprenoj', 'nokta',   $valoroj);

sercxtabellinio("Alveno (de la alig^o)",    'partoprenoj', 'aligxdato',     $valoroj);
sercxtabellinio("Alveno (de la malalig^o)", 'partoprenoj', 'malaligxdato',     $valoroj);
sercxelektolinio("Alvenstato",              'partoprenoj', 'alvenstato', $valoroj,
				 array('v' => 'venos', 'a' => 'alvenis', 'm' => 'malalig^is'));
sercxelektolinio("Traktstato",              'partoprenoj', 'traktstato', $valoroj,
				 array('N' => 'normale', 'P' => 'P (prefere?)', 'S (speciale?)'));
sercxelektolinio("Necesas asekuri?",        'partoprenoj', 'asekuri', $valoroj,
				 array('' => 'ne elektis', 'E' => 'log^as en Eu^ropunio (au^ EWR)',
					   'J' => 'Jes', 'N' => 'ne'));
sercxelektolinio("Havas asekuron",          'partoprenoj', 'havas_asekuron', $valoroj,
				 array('' => 'ne elektis', 'J' => 'Jes', 'N' => 'Ne'));
sercxtabellinio("Alig^kategoridato",        'partoprenoj', 'aligxkategorio', $valoroj);
sercxelektolinio("Kontrolita",              'partoprenoj', 'kontrolata',     $valoroj,
				 array(''=> 'ne elektis',   'J' => 'Jes', 'N' => 'Ne'));
sercxelektolinio("Mang^kupono",             'partoprenoj', 'havasMangxkuponon', $valoroj,
				 array('N' => 'Ne jam printita', 'P' => 'printita', 'J' => 'ricevis'));
sercxelektolinio("Noms^ildo",               'partoprenoj', 'havasNomsxildon',   $valoroj,
				 array('N' => 'Ne jam printita', 'P' => 'printita', 'J' => 'ricevis'));


sercxtabelkapo("Renkontig^o", "renkontigxo", $valoroj);

{
  $sql = datumbazdemando(array("ID", "mallongigo"),
						 "renkontigxo", "", "", array("order"=>"ID ASC"));
  $rez = sql_faru($sql);
  $elektolisto = array();
  while($linio = mysql_fetch_assoc($rez))
	{
	  $elektolisto[$linio['ID']] = ($linio['ID'] . ' (' .$linio['mallongigo'] . ')'); 
	}
  sercxelektolinio("ID",  'renkontigxo',  'ID', $valoroj,
				   $elektolisto, "renkNumero");
}
sercxtabellinio("Mallongigo", 'renkontigxo', 'mallongigo', $valoroj);
sercxtabellinio("Nomo",       'renkontigxo', 'nomo',       $valoroj, 'renkontigxonomo');
sercxtabellinio("Temo",       'renkontigxo', 'temo',       $valoroj, 'renkontigxotemo');
sercxtabellinio("Loko",       'renkontigxo', 'loko',       $valoroj, 'renkontigxoloko');
		// elektilo / tiu cxi / egalas


sercxtabelkapo("Notoj", "notoj", $valoroj);

sercxtabellinio("ID",       'notoj', 'ID',     $valoroj, "notoid");
sercxtabellinio("Kiu skribis", 'notoj', 'kiu', $valoroj);
sercxtabellinio("Komunikante kun kiu", 'notoj', 'kunkiu',  $valoroj);
sercxelektolinio("Tipo",   'notoj', 'tipo',    $valoroj,
				 array('letere' => 'per Letero',
					   'persone' => 'persone',
					   'rete'    => 'rete',
					   'telefon' => 'telefone',
					   'rimarko' => 'alia rimarko'),
				 "nototipo");
sercxtabellinio("kiam", 'notoj', 'dato', $valoroj, "notodato");
sercxtabellinio("Temo", 'notoj', 'subjekto', $valoroj);
sercxtabellinio("Prilaborita", 'notoj', 'prilaborita', $valoroj,
				array('' => 'ne', 'j' => 'Jes'));
sercxtabellinio("revidodato", 'notoj', 'revido', $valoroj);

sercxtabelkapo("Pago (unuopa)", "pagoj", $valoroj);

sercxtabellinio("ID",     'pagoj', 'ID',             $valoroj, "pagoid");
sercxtabellinio("Kvanto", 'pagoj', 'kvanto', $valoroj, "pagokvanto");
sercxtabellinio("Dato",   'pagoj', 'dato', $valoroj, "pagodato");
sercxtabellinio("Tipo",   'pagoj', 'tipo', $valoroj, "pagotipo");

sercxtabelkapo("Rabato (unuopa)", "rabatoj", $valoroj);

sercxtabellinio("ID",     'rabatoj', 'ID',     $valoroj, "rabatoid");
sercxtabellinio("Kvanto", 'rabatoj', 'kvanto', $valoroj, "rabatokvanto");
sercxtabellinio("Kau^zo", 'rabatoj', 'kauzo',  $valotoj);

sercxtabelkapo("Litonoktoj", "litonoktoj", $valoroj);

sercxtabellinio("ID",          'litonoktoj', 'ID',         $valoroj, "litonoktoid");
sercxtabellinio("Lito-numero", 'litonoktoj', 'litonumero', $valoroj);
sercxtabellinio("Nokto de",    'litonoktoj', 'nokto_de',   $valoroj);
sercxtabellinio("Nokto g^is",  'litonoktoj', 'nokto_gxis', $valoroj);
sercxtabellinio("Rezervtipo",  'litonoktoj', 'rezervtipo', $valoroj,
				array('d' => 'disdonitaj', 'r' => 'rezervitaj'));

sercxtabelkapo("C^ambroj", "cxambroj", $valoroj);

sercxtabellinio("ID",          'cxambroj', 'ID',         $valoroj, "cxambroid");
sercxtabellinio("Nomo",        'cxambroj', 'nomo',       $valoroj, "cxambronomo");
sercxtabellinio("Etag^o",      'cxambroj', 'etagxo',     $valoroj);
sercxtabellinio("Lito-nombro", 'cxambroj', 'litonombro', $valoroj);

sercxelektolinio("Tipo",       'cxambroj', 'tipo',       $valoroj,
				 array('' => 'ne elektis', 'g' => 'gea', 'i' => 'ina', 'v' => 'vira'),
				 "cxambrotipo");
sercxelektolinio("Dulita",     'cxambroj', 'dulita',     $valoroj,
				 array('J' => 'Jes', 'N' => 'Ne'));
sercxtabellinio("Rimarkoj",    'cxambroj', 'rimarkoj', $valoroj, "cxambrorimarkoj");


eoecho("</table>\n");

echo ("<p>");
butono('sercxu', "Serc^u");
butono('dauxrigu', "Konservu");
echo ("</p>");
echo "</form>\n";
// TODO


HtmlFino();




?>