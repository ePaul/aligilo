<?php

  /*
   * Tiu dosiero estas intencita kiel tuta redesegno de la
   * bazaj funkcioj el iloj_mesagxoj.php. La specifaj funkcioj
   * estos transprenitaj de diversaj_retmesagxoj.php kaj aliaj
   * individuaj dosieroj.
   *
   * Ankoraux prilaborata.
   */



require_once ($prafix.'/iloj/email_message.php');



class Retmesagxo {

    var $baza_objekto;

    function Retmesagxo()
    {
        $this->baza_objekto = new email_message_class();
        $this->baza_objekto->default_charset="UTF-8";
    }

    function testu_eraron($eraro)
    {
        if ($eraro)
            {
                erareldono($eraro);
                exit();
            }
    }

    function sendanto_estu($adreso, $nomo)
    {
        $eraro = $this->baza_objekto->SetEncodedEmailHeader("From",
                                                            $adreso,
                                                            $nomo);
        $this->testu_eraron($eraro);
    }

    function kopio_al($adreso)
    {
        $eraro = $this->baza_objekto->SetHeader("Bcc", $adreso);
        $this->testu_eraron($eraro);
    }


    function eksendu()
    {
        $eraro = $this->baza_objekto->Send();
        $this->testu_eraron($eraro);
    }

    function ricevanto_estu($adreso, $nomo)
    {
        $eraro = $this->baza_objekto->SetEncodedEmailHeader("To",
                                                            $adreso,
                                                            $nomo);
        $this->testu_eraron($eraro);
    }

    function temo_estu($teksto)
    {
        $eraro = $this->baza_objekto->SetEncodedHeader("Subject", $teksto);
        $this->testu_eraron($eraro);
    }


    /**
     * aldonas tekstan parton al la retposxto.
     * (Mi ne elprovis, kio okazas, se estas
     *  pluraj tiaj.)
     * La kodigo estu UTF-8 (aux io kompatibla). 
     */
    function teksto_estu($teksto)
    {
        // TODO: Cxu WrapText() ?
        $difino = array(
                        "Content-Type"=>"text/plain; charset=UTF-8",
                        "DATA"=>$teksto
                        );
        $eraro = $this->baza_objekto->CreateAndAddPart($difino,$part);
        

        //        $eraro = $this->baza_objekto->AddPlainTextPart($teksto);
        $this->testu_eraron($eraro);
    }


    /**
     * metas tekston, kun komenca kaj finaj linioj pri la
     * auxtomateco de la teksto kaj kie plendi.
     *
     * La kodigo estu UTF-8 (aux io kompatibla). 
     */
    function auxtomata_teksto_estu($teksto, $renkontigxo="")
    {
        if (!$renkontigxo)
            {
                $renkontigxo = $_SESSION['renkontigxo'];
            }
        $this->teksto_estu("### au^tomata mesag^o de la " . programo_nomo . " ###\n\n" .
                    $teksto .
                    "\n\n### En kazo de teknika problemo bonvolu informi " .
                    teknika_administranto_retadreso . ". ###" .
                    "\n### (En kazo de enhava problemo, informu " .
                    $renkontigxo->datoj['adminretadreso'] . 
                    "  ###" 
                    );
}


    /**
     * aldonas dosieron el la dosiersistemo de la servilo.
     * 
     *
     * $dosiernomo - la nomo de la dosiero. (Gxi ekzistu gxis post
     *                    la voko de eksendu().)
     * $tipo       - la enhavtipo. Se ne donita, ni provos diveni gxin
     *                laux la nomo de la dosiero.
     */
    function aldonu_dosieron_el_disko($dosiernomo, $tipo="")
    {
        $datoj = array('FileName' => $dosiernomo,
                       'Content-Type' => ($tipo? $tipo : 'automatic/name'));
        $eraro = $this->baza_objekto->AddFilePart($datoj);
        $this->testu_eraron($eraro);
    }

    /**
     * aldonas dosieron kreita enmemore.
     * 
     * $enhavo     - la enhavo de la dosiero (kiel bitoka cxeno).
     * $nomo       - la dosiernomo por nomi la aldonajxon. 
     * $tipo       - la enhavtipo. Se ne donita, ni provos diveni gxin
     *                laux la nomo de la dosiero.
     */
    function aldonu_dosieron_el_memoro($enhavo, $nomo, $tipo="")
    {
        $datoj = array('Data' => $enhavo,
                       'Name' => $nomo,
                       'Content-Type' => ($tipo? $tipo : 'automatic/name'));
        $eraro = $this->baza_objekto->AddFilePart($datoj);
        $this->testu_eraron($eraro);
    }

    

} // retmesagxo


/**
 * Kreas kaj redonas novan retmesagxan objekton
 * tauxga por auxtomata mesagxo.
 *
 * Sendanto kaj kopio-ricevanto estas prenataj
 *  el konfiguraj opcioj.
 */
function kreu_auxtomatan_mesagxon()
{
    $mesagxo = new Retmesagxo();
    $mesagxo->sendanto_estu(auxtomataj_mesagxoj_retadreso,
                            auxtomataj_mesagxoj_sendanto);

    if (constant('retmesagxo_kopio_al'))
        {
            if (strpos(retmesagxo_kopio_al, '@') > 0)
                {
                    $mesagxo->kopio_al(retmesagxo_kopio_al);
                }
            else
                {
                    // ne sendu kopion
                }
        }
    else
        {
            // sendu kopion al la teknika administranto
            $mesagxo->kopio_al(teknika_administranto_retadreso);
        }
    return $mesagxo;
}





?>