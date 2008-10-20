<?php 
  /*
Plugin Name: Aligilo: Nombro de aliĝintoj
Description: Parto de la renkontiĝo-aligilo, tiu programeto eltrovu la aktualan nombron de aliĝintoj.
Author: Paul Ebermann
   */



function aligilo_aligxintoj_nombro() {

    $prafix = get_option("aligilo-prafix");
    $GLOBALS['prafix'] = $prafix;
    $renkID = get_option("aligilo-renkontigxo");

    require_once($prafix . "/iloj/iloj.php");
    malfermu_datumaro();
    
    $sql = datumbazdemando(array("COUNT(*)" => "num"),
                           array("partoprenoj" => "p"),
                           array("p.alvenstato = 'v'",
                                 "p.renkontigxoID = '$renkID'")
                           );
    $rez = sql_faru($sql);
    $linio = mysql_fetch_assoc($rez);



    return $linio['num'];
}



function aligilo_aligxintoj_nombro_filtro($enhavo) {
    echo "<!-- aligilo_aligxintoj_nombro_filtro -->";
    echo "<!-- listo-filtro ekzistas? " . (string)function_exists("aligilo_aligxintoj_listo_filtro") . "-->";
    echo "<!-- kotizotabelo-filtro ekzistas? " . (string)function_exists("aligilo_kotizotabelo_filtro") . "-->";
    $key = "<num_aligxintoj/>";
    $i = strpos($enhavo, $key);
    if ($i !== false) {
        $num = aligilo_aligxintoj_nombro();
        return substr($enhavo, 0, $i) .
            "<!-- aligilo-nombro-testo (trovita) -->" .
            $num . substr($enhavo, $i + strlen($key));
    }
    return "<!-- aligilo-nombro-testo (nenio trovita) -->" . $enhavo;
}


add_filter('the_content', 'aligilo_aligxintoj_nombro_filtro');


  
?>