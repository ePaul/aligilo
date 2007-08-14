<?php

  /*
   * Tiu dosiero estas intencita kiel tuta redesegno de iloj_mesagxoj.php.
   *
   * Ankoraux sub laboro.
   */



require_once ($prafix.'/iloj/email_message.php');



class Retmesagxo {

    var $baza_objekto;

    function Retmesagxo()
    {
        $baza_objekto = new email_message_class();
    }

    function metu_sendanton($adreso, $nomo)
    {
        // TODO
    }


    function eksendu()
    {
        $eraro = $baza_objekto->Send();
        if ($eraro)
            {
                erareldono($eraro);
                exit();
            }
    }

} // retmesagxo

function kreu_auxtomatan_mesagxon()
{
    $mesagxo = new Retmesagxo();
    $mesagxo->metu_sendanton(auxtomataj_mesagxoj_sendanto,
                             auxtomataj_mesagxoj_retadreso);
    return $mesagxo;
}


?>