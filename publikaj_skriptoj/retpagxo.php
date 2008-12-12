<?php


$vok_nomo = $_SERVER["REQUEST_URI"];

if (substr($vok_nomo, -4) == '.css' or
    substr($vok_nomo, -3) == '.js') {
    define('DEBUG', false);
 }
 else{
     define('DEBUG', true);
     header("Content-Type: text/html; charset=utf-8");
 }


$prafix = "..";

// require_once($_SERVER['DOCUMENT_ROOT'] . '/is/tradukado/traduko.php');
require_once($prafix . "/iloj/traduko/traduko_objektoj.php");
eniru_dosieron('pubdos:/nevalida');
require_once("lib/konfiguro.php");

$rezultoj = array();

// $pagxo_prefikso estas regula esprimo por la prefikso,
// difinita en lib/konfiguro.php.

$esprimo = "#^(" . $pagxo_prefikso . ")(..)/([^?]*)(\?.*)?$#";
preg_match($esprimo,
			  $vok_nomo,
			  $rezultoj);
list(,$pagxo_prefikso,$lingvo, $pagxo) = $rezultoj;

// echo "<pre>";
// echo "vok_nomo: " . $vok_nomo . "\n";
// echo "esprimo: " . $esprimo . "\n";
// echo "rezultoj: " . var_export($rezultoj, true) . "\n";
// echo "</pre>";

// ekde cxi tie $pagxo_prefikso estas tio, kio finfine estis uzata
// en la adreso.

// diru lingvon kaj dosiernomon al la traduko-skripto
eniru_lingvon($lingvo);
// $GLOBALS['traduko_dosieroj'] = array('/' . $pagxo . ".php");

$dosierujo = substr($pagxo, 0, strpos($pagxo, '/'));
//echo "<!-- dosierujo: $dosierujo -->";


require_once("lib/shablono.php");

metu_piednotsistemon($GLOBALS['aliĝilo_piednotilo']);


if ($dosierujo and file_exists($dosierujo .  '/konfiguro.php'))
    {
        //        echo "<!-- legas ". $dosierujo .  '/konfiguro.php' . "-->";
        require_once($dosierujo .  '/konfiguro.php');
    }


if($pagxo == "")
{
  $pagxo = "aligxilo";
  //  $GLOBALS['traduko_dosieroj'] = array('/index.php');
}

if($pagxo{strlen($pagxo)-1} == "/")
{
  $pagxo .= "aligxilo";
  //  $GLOBALS['traduko_dosieroj'] = array('/' . $pagxo . ".php");
}
else if (is_dir("./" . $pagxo ))
{
  $uri = $_SERVER["REDIRECT_REDIRECT_SCRIPT_URI"] . "/aligxilo";
  header("HTTP/1.0 301 Andere Adresse");
  header("Location: " . $uri);
?>
<html>
   <header>
   <title>Seite nicht hier / pagxo aliloke.</title>
   </header>
   <body>
   <p>
   La pa&#285;o, kiun vi mendis, trovigxas aliloke.
   </p>
   <p>
   Die Seite, die Sie bestellt haben, befindet sich woanders.
   </p>
   <p><?php echo "<a href='$uri'>$uri</a>"; ?></p>
   </body>
</html>
<?php
   exit();
}



$dosiero = "./" . $pagxo . ".php";

if((strpos($dosiero, "lib/")===false) && file_exists($dosiero))
{
//  echo "<!-- GLOBALS: \n";
//	print_r($GLOBALS);
//  echo "-->";

// if (substr($dosierujo, -5) == '-test')
//     {
//         // specialajxo por testi kun la gxustaj tradukoj:
//         $GLOBALS['traduko_dosieroj'] = array('pubdos:/'.
//                                              substr($dosierujo,0,-5) .
//                                              substr($pagxo,
//                                                     strpos($pagxo, '/')) .
//                                              '.php');
        
//     }
//  else
//      {
//          $GLOBALS['traduko_dosieroj'] = array('pubdos:/' . $pagxo . ".php");
//      }

    eniru_dosieron($dosiero);


  require($dosiero);
}
else
{
  header("HTTP/1.0 404 Not Found");
?>
<html>
   <header>
   <title>Seite nicht gefunden - pagxo ne trovita.</title>
   </header>
   <body>
   <p>
   Beda&#365;rinde ni ne trovis la dosieron <em><?php echo $dosiero; ?></em>,
    kiun vi mendis (per <em><?php echo $vok_nomo; ?></em>).
   </p>
   <p>
   Leider wurde die von Ihnen angeforderte Seite nicht gefunden.
   </p>
   </body>
</html>
<?php
}


?>
