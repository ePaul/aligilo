<?php 
  /*
Plugin Name: Aligilo: Kotizotabelo
Description: Parto de la renkontiĝo-aligilo, tiu programero montru la tabelon de la kotizoj por la unuopaj kategorioj.
Author: Paul Ebermann
   */


function aligilo_kotizotabelo($linioID) {

    $GLOBALS['prafix'] = $prafix = get_option("aligilo-prafix");
    $renkID = get_option("aligilo-renkontigxo");

    require_once($prafix . "/iloj/iloj.php");
    malfermu_datumaro();

    $renkontigxo = new Renkontigxo($renkID);
    $kotizosistemo = new Kotizosistemo($renkontigxo->datoj['kotizosistemo']);
    $tipoj = $GLOBALS['kategoriotipoj'];
    $cxeftipo = array_pop($tipoj);
    $faritaj = array($cxeftipo => $linioID);
    echo "<table class='granda_kotizotabelo'>\n";
    $kotizosistemo->metu_grandan_kotizolinion("simpla_kotizocxelo",
                                              $tipoj, $faritaj, "");
    echo "</table>";
}

/*
function aligilo_publika_kotizocxelo() {
   // fortrancxas la post-komajn ciferojn!
    echo number_format($kotizosistemo->eltrovu_bazan_kotizon($kategorioj));
}}
*/



function aligilo_kotizotabelo_filtro($enhavo) {
    echo "<!-- aligilo_aligxintoj_kotizotabelo-filtro -->";
    $key = "<aligilo:kotizotabelo/>";
    $i = strpos($enhavo, $key);
    if ($i !== false) {
        ob_start();
        aligilo_kotizotabelo(6);
        $tabelo = ob_get_contents();
        ob_end_clean();
        return substr($enhavo, 0, $i) .
            "<!-- kotizotabelo-komenco -->" .
             $tabelo . substr($enhavo, $i + strlen($key));
    }
    return $enhavo;
}


add_filter('the_content', 'aligilo_kotizotabelo_filtro');




?>