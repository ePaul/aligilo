<?php

  /**
   * Klaso kun kelkaj apudaj funkcioj, utilaj por sendi retmesagxojn.
   * 
   * Ili anstatauxu la bazajn funkciojn el {@link iloj_mesagxoj.php},
   * kaj estos uzataj de specifaj sendo-funkcioj en
   * {@link diversaj_retmesagxoj.php}
   *
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @since Revision 35.
   * @copyright 2007-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   * ni sxargxas la klason {@link email_message_class}.
   */
require_once ($prafix.'/iloj/email_message.php');


/**
 * Klaso por krei kaj sendi retmesagxon.
 *
 * Gxi cxirkauxas objekton de 
 * {@link email_message_class}, uzas ties metodojn, kaj aldonas
 * plurajn pli utilajn por nia uzo.
 *
 * @uses email_message_class
 * @package aligilo
 * @subpackage iloj
 * @author Paul Ebermann
 */
class Retmesagxo {

    // la objekto de email_message_class, kiun
    // ni uzas por delegi la laboron.

    /**
     * la uzata objekto por krei kaj sendi la mesagxon.
     * @var email_message_class
     */
    var $baza_objekto;

    /**
     * Listo de la kopio-ricevantoj.
     * @var array 
     */
    var $kopioj_listo = array();


    /**
     * la konstruilo.
     */
    function Retmesagxo()
    {
        $this->baza_objekto = new email_message_class();
        $this->baza_objekto->default_charset="UTF-8";
    }

    /**
     * Kontrolas, cxu eraro okazis, kaj eble finas la programon.
     *
     * Tiu funkcio estis vokita de cxiuj metodoj, kiuj uzas
     * funkcion (el email_message_class) redonantan tian eraro-valoron.
     *
     * @param mixed $eraro  Se tio estas io kun boolean-valoro true,
     *              ni eldonas gxin kiel eraro kaj finas la programon.
     */
    function testu_eraron($eraro)
    {
        if ($eraro)
            {
                erareldono("Problemo: " . $eraro);
                exit();
            }
    }


    /**
     * Eksendas la mesagxon, kaj eldonas iujn informojn pri tio.
     *
     * @uses email_message_class::Send
     */
    function eksendu()
    {
        $eraro = $this->baza_objekto->Send();
        $this->testu_eraron($eraro);
        eoecho("<p>Sendis mesag^on al: " .
               $this->baza_objekto->headers['To'] . ", " .
               $this->baza_objekto->headers['Cc'] . ", " .
               $this->baza_objekto->headers['Bcc'] . "</p>\n");
    }


    /**
     * Difinas la sendanto-nomon de la mesagxo.
     *
     * @param string $adreso - la retposxtadreso de la sendanto
     * @param string $nomo   - la sendantonomo
     */
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
     *
     * @param string|array $adreso aux String kun unu adreso, aux el
     *        pluraj adresoj, disigitaj per komo, aux array de unuopaj adresoj.
     *        Tiuj estos aldonitaj al la gxisnuna listo de
     *        kasxitaj kopio-ricevantoj.
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


    /**
     * difinas, kiu estu la "oficiala" ricevanto de la mesagxo.
     * (Nuntempe eblas havi nur unu tian.)
     *
     * @param string $adreso
     * @param string $nomo
     */
    function ricevanto_estu($adreso, $nomo)
    {
        $eraro = $this->baza_objekto->SetEncodedEmailHeader("To",
                                                            $adreso,
                                                            $nomo);
        $this->testu_eraron($eraro);
    }


    /**
     * Difinas la enhavon de la temo-linio.
     *
     * @param string $teksto la nova enhavo de la temo-linio.
     */
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
     * @param string $teksto - la enhavo de la mesagxo.
     */
    function teksto_estu($teksto)
    {
        // TODO: Cxu WrapText() ?
        $difino = array(
                        "Content-Type"=>"text/plain; charset=UTF-8",
                        "DATA"=>$teksto
                        );
        $eraro = $this->baza_objekto->CreateAndAddPart($difino,$part);
        
        $this->testu_eraron($eraro);
    }


    /**
     * aldonas tekstan parton al la retposxto.
     * (Mi ne elprovis, kio okazas, se estas
     *  pluraj tiaj.)
     * La kodigo estu ISO-8859-1 (aux io kompatibla). 
     *
     * @param string $teksto - la enhavo de la mesagxo.
     * @uses email_message_class::CreateAndAddPart()
     */
    function latin1a_teksto_estu($teksto) {
        // TODO: Cxu WrapText() ?
        $difino = array(
                        "Content-Type"=>"text/plain; charset=ISO-8859-1",
                        "DATA"=>$teksto
                        );
        $eraro = $this->baza_objekto->CreateAndAddPart($difino,$part);

        $this->testu_eraron($eraro);
    }


    /**
     * metas tekston, kun komenca kaj finaj linioj pri la
     * auxtomateco de la teksto kaj kie plendi.
     *
     * La kodigo de la teksto estu UTF-8 (aux io kompatibla).
     * 
     * @param string $teksto la enhavo de la mesagxo.
     * @param string $eokodigo
     *                  Metodo por transformi nian c^-surogatojn, kiel
     *                  en {@link eotransformado()}:
     *                    "" (la defauxlto): -la enhavo ne estos sxangxita
     *                    "x-metodo"
     *                    "utf-8"
     *                    ( "unikodo" - uzu HTML-kodigon - ne sencas.)
     * @param string $sendanto kiu/kio kauxzis la sendadon de la
     *                         mesagxo, ekzemple "aligxilo" aux
     *                         iu salutnomo de uzanto.
     * @param Renkontigxo $renkontigxo uzata por la mencio de administranta
     *                                 adreso - se mankas, uzas
     *                                 {@link $_SESSION['renkontigxo']}
     *                                 anstatauxe.
     * @uses teksto_estu()
     * @uses latin1a_teksto_estu()
     * @uses eotransformado()
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
                                               /* al */ "ISO-8859-1",
                                               /* de */ "UTF-8");
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
     * @param string $dosiernomo la nomo de la dosiero. (Gxi estos uzata de
     *                           eksendu(), do ne forigu aux reuzu ĝin
     *                           antauxe.)
     * @param string $tipo       la MIME-tipo de la dosiero.
     *                           Se ne donita, ni provos diveni gxin
     *                           laux la nomo de la dosiero.
     * @uses email_message_class::AddFilePart.
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
     * @param string $enhavo la enhavo de la dosiero (kiel bitoka cxeno).
     * @param string $nomo   la dosiernomo por nomi la aldonajxon. 
     * @param string $tipo   la enhavtipo. Se ne donita, ni provos diveni gxin
     *                        laux la nomo de la dosiero.
     */
    function aldonu_dosieron_el_memoro($enhavo, $nomo, $tipo="")
    {
        $datoj = array('Data' => $enhavo,
                       'Name' => $nomo,
                       'Content-Type' => ($tipo? $tipo : 'automatic/name'));
        $eraro = $this->baza_objekto->AddFilePart($datoj);
        $this->testu_eraron($eraro);
    }

    

} // class retmesagxo


/**
 * Kreas kaj redonas novan retmesagxan objekton
 * tauxga por auxtomata mesagxo.
 *
 * Sendanto kaj kopio-ricevanto estas prenataj
 *  el konfiguraj opcioj.
 * @see Retmesagxo
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


/**
 * eltrovas la unuan nomon el du- aux plurparta nomo, t.e.
 * la parton gxis la unua spaceto.
 *
 * Por "Saluton ...," en internaj mesagxoj.
 *
 * @param string $nomo la nomo, eble el pluraj partoj.
 */
function antauxnomo($nomo)
{
  $arr = explode(" ", $nomo, 2);
  return $arr[0];
}



?>