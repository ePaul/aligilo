<?php


/**
 * kalkulas, kiom da tagoj estas gxis komenco/fino
 * de IS kaj kreas tauxgan tekston (en kelkaj lingvoj).
 */
function tagoj_gxis($komenco_teksto, $fino_teksto)
{

$is_komenco = strtotime($komenco_teksto);
$is_fino = strtotime($fino_teksto);
$nun_tempo = time();
$tagoj_gxis =  floor(($is_komenco - $nun_tempo)/(60*60*24));
$tagoj_ekde = floor(($nun_tempo - $is_fino)/(60*60*24));


if (1 < $tagoj_gxis)
{
    $datoj = CH("ankoraux-x-tagoj", $tagoj_gxis);
    /*
  $datoj = array('de' => "Bis zur Er&ouml;ffnung sind es noch {$tagoj_gxis} Tage.",
				 'eo' => "&#284;is la malfermo estas ankora&#365; {$tagoj_gxis} tagoj.");
    */
}
else if(1 == $tagoj_gxis)
{
    $datoj = CH("komencigxos-morgaux");
    /*
  $datoj = array('de' => "Es beginnt morgen.",
				 'eo' => "&#284;i komenci&#285;os morga&#365;.");
    */
}
else if (0 == $tagoj_gxis)
{
    $datoj = CH('komencigxas_hodiaux');
    /*
  $datoj = array('de' => "Es beginnt heute.",
				 'eo' => "&#284; komenci&#285;as hodia&#365;.");
    */
}
else if ($tagoj_ekde < 0)
{
    // TODO: kontrolu kalkuladon
    $datoj = CH('hodiaux-estas-xa-tago', 1-$tagoj_gxis, 1-$tagoj_ekde);
    /*
  $datoj = array('de' => "Heute ist der " . (1-$tagoj_gxis) . "-te Tag des IS.",
				 'eo' => "Hodia&#365; estas la " . (1-$tagoj_gxis) ."a tago de la IS.");
    */
}
else if ($tagoj_ekde == 0)
{
    $datoj = CH('hodiaux-finas');
    /*
  $datoj = array('de' => "Heute endet es leider schon.",
				 'eo' => "&#284;i fini&#285;as beda&#365;rinde jam hodia&#365;.");
    */
}
else if ($tagoj_ekde == 1)
{
    $datoj = CH('hieraux-finis');
    /*
  $datoj = array('de' => "Es ist gestern zu Ende gegangen.",
			   'eo' => "&#284;i hiera&#365; fini&#285;is.");
    */
}
else
{
    $datoj = CH('finis-antaux-x-tagoj', $tagoj_ekde);
    /*
  $datoj = array('de' => "Es endete vor {$tagoj_ekde} Tagen.
        Diese Webseiten dazu sind daher nun etwas inaktuell.",
				 'eo' => "La {$is_nomo_eo} fini&#285;is anta&#365; {$tagoj_ekde} tagoj.
        La pa&#285;aro tial nun estas iom neaktuala.");
    */
}

/*
$datoj['tagoj_gxis'] = $tagoj_gxis;
$datoj['tagoj_ekde'] = $tagoj_ekde;
*/

return $datoj;

}
?>
