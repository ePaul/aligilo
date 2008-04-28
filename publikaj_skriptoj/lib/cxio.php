<?php

define(DEBUG, false);

$vok_nomo = $_SERVER["REQUEST_URI"];

/*
if(strpos($vok_nomo, "x/rolfo/lib/")!==false) // gefunden
{
  header("HTTP/1.1 403 Nicht erlaubt");
?>
<html>
   <header>
   <title>Seite nicht erlaubt.</title>
   </header>
   <body>
   <p>
   Beda&#365;rinde oni ne rajtas vidi la pa&#285;on, kiun vi mendis.
   </p>
   <p>
   Leider ist die von ihnen erw&uuml;nschte Seite gesperrt.
   </p>
   </body>
</html>
<?php
  exit();
}
*/

require_once('../../tradukado/traduko.php');

require_once("konfiguro.php");

$rezultoj = array();
preg_match("#^" . $pagxo_prefikso . "(..)/([^?]*)(\?.*)?$#",
			  $vok_nomo,
			  $rezultoj);
list(,$lingvo, $pagxo) = $rezultoj;

// diru lingvon kaj dosiernomon al la traduko-skripto
lingvo($lingvo);
// $GLOBALS['traduko_dosieroj'] = array('/' . $pagxo . ".php");

$dosierujo = substr($pagxo, 0, strpos($pagxo, '/'));
//echo "<!-- dosierujo: $dosierujo -->";
if ($dosierujo and file_exists('../' . $dosierujo .  '/konfiguro.php'))
    {
        //        echo "<!-- legas ". $dosierujo .  '/konfiguro.php' . "-->";
        require_once('../' . $dosierujo .  '/konfiguro.php');
    }


if($pagxo == "")
{
  $pagxo = "index";
  //  $GLOBALS['traduko_dosieroj'] = array('/index.php');
}

if($pagxo{strlen($pagxo)-1} == "/")
{
  $pagxo .= "index";
  //  $GLOBALS['traduko_dosieroj'] = array('/' . $pagxo . ".php");
}
else if (is_dir("../" . $pagxo ))
{
  $uri = $_SERVER["REDIRECT_REDIRECT_SCRIPT_URI"] . "/index";
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



$dosiero = "../" . $pagxo . ".php";

if((strpos($dosiero, "lib/")===false) && file_exists($dosiero))
{
//  echo "<!-- GLOBALS: \n";
//	print_r($GLOBALS);
//  echo "-->";
  require_once("shablono.php");
  if (substr($dosierujo, -5) == '-test')
      {
          // specialajxo por testi kun la gxustaj tradukoj:
          $GLOBALS['traduko_dosieroj'] = array('/'.
                                               substr($dosierujo,0,-5) .
                                               substr($pagxo,
                                                      strpos($pagxo, '/')) .
                                               '.php');
                                               
      }
  else
      {
          $GLOBALS['traduko_dosieroj'] = array('/' . $pagxo . ".php");
      }
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
