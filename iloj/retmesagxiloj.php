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

    // la objekto de email_message_class, kiun
    // ni uzas por delegi la laboron.
    var $baza_objekto;

    // listo de Bcc-kopi-ricevintoj.
    var $kopioj_listo = array();

    function Retmesagxo()
    {
        $this->baza_objekto = new email_message_class();
        $this->baza_objekto->default_charset="UTF-8";
    }

    /**
     * testas, cxu $eraro estas io alia ol "" (aux false/0/null ktp.).
     * se jes, eldonas gxin kaj finas la programon.
     *
     * Tiu funkcio estis vokita de cxiuj metodoj, kiuj uzas
     * funkcion redonantan tian eraro-valoron.
     */
    function testu_eraron($eraro)
    {
        if ($eraro)
            {
                erareldono("Problemo: " . $eraro);
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


    /**
     * aldonas plian kopio-ricevanton
     * (aux plurajn tiajn).
     */
    function kopion_al($adreso)
    {
        if ($adreso and !is_array($adreso))
            {
                $adreso = preg_split('/, */', $adreso);
            }


        if ($adreso)
            {
                $this->kopioj_listo = array_merge($this->kopioj_listo,
                                                  $adreso);
            }
        else
            {
                // nenio sxangxigxas, ne necesas
                // rekalkuli la Bcc-kaplinion.
                return;
            }

        $eraro =
            $this->baza_objekto->SetHeader("Bcc",
                                           implode(",",
                                                   $this->kopioj_listo));
        $this->testu_eraron($eraro);
    }


    function eksendu()
    {
        $eraro = $this->baza_objekto->Send();
        $this->testu_eraron($eraro);
        eoecho("<p>Sendis mesag^on al: " .
               $this->baza_objekto->headers['To'] . ", " .
               $this->baza_objekto->headers['Cc'] . ", " .
               $this->baza_objekto->headers['Bcc'] . "</p>\n");
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
     *
     * $teksto - la enhavo de la mesagxo.
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


    function latin1a_teksto_estu($teksto) {
        // TODO: Cxu WrapText() ?
        $difino = array(
                        "Content-Type"=>"text/plain; charset=ISO-8859-1",
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
     * La kodigo de la teksto estu UTF-8 (aux io kompatibla).
     * 
     * $teksto - la enhavo de la mesagxo.
     * $eokodigo - Metodo por transformi nian c^-surogatojn.
     *                "" (la defauxlto) -la enhavo ne estos sxangxita
     *                "x-metodo"
     *                "utf-8"
     *                ( "unikodo" - uzu HTML-kodigon - ne sencas.)
     */
    function auxtomata_teksto_estu($teksto,
                                   $eokodigo = "",
                                   $sendanto = "nekonato",
                                   $renkontigxo="")
    {
        if (!$renkontigxo)
            {
                $renkontigxo = $_SESSION['renkontigxo'];
            }

        $fina_teksto =
            "### au^tomata mesag^o de la " . programo_nomo . " ###\n" .
            "### Sendita fare de " .$sendanto . " ###\n" .
            "\n" .
            $teksto .
            "\n\n### En kazo de teknika problemo bonvolu informi " .
            teknika_administranto_retadreso . ". ###" .
            "\n### (En kazo de enhava problemo, informu " .
            $renkontigxo->datoj['adminretadreso'] . 
            ".) ###" ;

        if ($eokodigo != "utf-8" and
            ! estas_ekster_latin1($fina_teksto)) {
            $fina_teksto = mb_convert_encoding(eotransformado($fina_teksto,
                                                              $eokodigo),
                                               "ISO-8859-1","UTF-8");
                $this->latin1a_teksto_estu($fina_teksto);
        }
        else {
            $this->teksto_estu(eotransformado($fina_teksto,
                                              $eokodigo));
        }
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

    $kopiadreso = constant('retmesagxo_kopio_al') or
        $kopiadreso = teknika_administranto_retadreso;
    
    if (strpos($kopiadreso, '@') >= 0)
        {
            $mesagxo->kopion_al($kopiadreso);
        }
    return $mesagxo;
}





?>