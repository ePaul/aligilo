<?php


echo "<pre>";


$array1 = array("titolo" => "Hallo",
                'signo' => "+",
                array("unua komponento"),
                array("dua komponento"));

$array2 = null;

$rezulto1 = array_merge($array1, $array2);

$rezulto2 = array_merge($array2, $array1);

var_export($rezulto1);
var_export($rezulto2);


