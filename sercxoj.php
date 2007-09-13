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
  granda_entajpejo("Priskribo:", 'nomo', "", '50', '5');
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

if ($_REQUEST['sendu'] == 'redaktu')
    {
        // TODO!: ebligu redaktadon de serc^oj (nomo / priskribo)
        erareldono("Bedau^rinde la redaktado de konservitaj serc^oj ankorau^ ne eblas");
    }


sercxoElektilo();

ligu("gxenerala_sercxo.php", "Nova Serc^o");


HtmlFino();




?>