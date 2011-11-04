<?php


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

eoecho("<h2>Diversaj serc^oj</h2>\n");


if ($_POST['sendu'] == 'dauxrigu')
{
  $valoroj = kopiuSercxon();
  $kodita = base64_encode(kodiguSercxon($valoroj));
  eoecho("<h3>Konservu serc^on</h3>");

  echo "<form action='sercxoj.php' method='post'>\n<p>";
  eoecho ("Bonvolu entajpi nomon kaj priskribon por via serc^o." .
		  " Eblas uzi la &#99;^-kodigon por la esperantaj supersignoj" .
		  " (&#69;^ por E^).</p>\n<p>\n");
  tenukasxe('sercxo', $kodita);
  entajpejo("Nomo:", 'nomo', $valoroj['sercxo_titolo']);
  granda_entajpejo("Priskribo:", 'priskribo', "", '50', '5');
  butono("konservu", "Konservu");
  echo "</p>\n</form>";
  HtmlFino();
  return;
}

if ($_REQUEST['sendu'] == 'forigu')
{
  foriguSercxon($id);
}

// echo "<!-- POST: \n";
// var_export($_POST);
// // echo "\n valoroj: \n";
// // var_export($valoroj);
// echo "-->\n";

if($_POST['sendu'] == 'konservu')
{
  konservuSercxon($_POST['nomo'], $_POST['priskribo'], base64_decode($_POST['sercxo']), $_POST['ID']);
}

if ($_POST['sendu'] == 'sxangxu') {
    sxangxu_datumbazon("sercxoj",
                       array("nomo" => $_REQUEST['nomo'],
                             "priskribo" => $_REQUEST['priskribo']),
                       array("ID" => $_REQUEST['ID']));
 }

if ($_REQUEST['sendu'] == 'redaktu')
    {
        eoecho("<h3>Redaktu serc^on #" . $_REQUEST['id']. "</h3>");
    
        echo "<form action='sercxoj.php' method='post'>\n<p>";
   
        $sql = datumbazdemando(array("s.ID" => "ID",
                                     "s.nomo" => "nomo",
                                     "s.priskribo" => "priskribo"),
                               array("sercxoj" => "s"),
                               array("s.ID = '".$_REQUEST['id']. "'"));
        $linio = mysql_fetch_assoc(sql_faru($sql));

        eoecho ("Bonvolu entajpi nomon kaj priskribon por via serc^o." .
		  " Eblas uzi la &#99;^-kodigon por la esperantaj supersignoj" .
		  " (&#69;^ por E^).</p>\n");
        echo ("<table>");
        tabela_kasxilo('ID', 'ID', $linio['ID']);
        tabelentajpejo("Nomo:", 'nomo', $linio['nomo']);
        granda_tabelentajpejo("Priskribo:", 'priskribo', $linio['priskribo'],
                              '60', '6');
        echo "</table><p>";
        butono("sxangxu", "S^ang^u");
        ligu ("gxenerala_sercxo.php?antauxa_sercxo=" . $linio['ID'],
              "Ne s^ang^u, montru serc^on");
        ligu("gxenerala_sercxo.php?antauxa_sercxo=" . $linio['ID'] .
             "&sendu=sercxu",
             "Ne s^ang^u, tuj serc^u");
        ligu ("partsercxo.php", "Reen al <em>serc^i partoprenantojn</em>");
        ligu("sercxoj.php", "Reen al la listo");
        echo "</p>\n</form>";
        HtmlFino();
        return;
  
    }


sercxoElektilo();

ligu("gxenerala_sercxo.php", "Nova Serc^o");


HtmlFino();




?>