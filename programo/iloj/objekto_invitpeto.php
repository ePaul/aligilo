<?php

  /*
   * La tabelnomoj cxi tie cxiam estas
   * la abstraktaj tabelnomoj. La traduko
   * al la konkretaj nomoj okazas en
   * iloj_sql.
   */



/**
 * Datumoj rilataj al petado de invitletero/vizo
 * (en aparta tabelo, cxar ne cxiu bezonas gxin)
 *
 *  ID            (= partoprenoID)
 *  pasportnumero
 *  pasporta_familia_nomo
 *  pasporta_persona_nomo
 *  pasporta_adreso
 *  senda_adreso
 *  senda_faksnumero
 *
 *  invitletero_sendenda    ?/j/n
 *  invitletero_sendodato
 */
class Invitpeto extends Objekto
{
    
    /* Konstruilo */
    function Invitpeto($id=0)
    {
        $this->Objekto($id,"invitpetoj");
    }

    /**
     * detaloj en teksta formo por la konfirmilo.
     * Ne enhavas la internajn informojn
     * (sendodato, sendenda).
     */
    function konfirmilaj_detaloj()
    {
        $teksto =
            "\nPP-numero:        " . $this->datoj['pasportnumero'] .
            "\nPPa familia nomo: " . $this->datoj['pasporta_familia_nomo'] .
            "\nPPa persona nomo: " . $this->datoj['pasporta_persona_nomo'] .
            "\nPPa adreso:" .
            str_replace("\n", "\n    ",
                        "\n".$this->datoj['pasporta_adreso']) .
            "\nSenda faksnumero: " . $this->datoj['senda_faksnumero'] .
            "\nSenda adreso:" .
            str_replace("\n", "\n    ",
                        "\n" . $this->datoj['senda_adreso']);
        return $teksto;
    }
    
    function montru_detalojn()
    {
        echo "<table>\n";
        kampo("ID:", $this->datoj['ID']);
        kampo("PP-numero:", $this->datoj['pasportnumero']);
        kampo("PPa familia nomo:", $this->datoj['pasporta_familia_nomo']);
        kampo("PPa persona nomo:", $this->datoj['pasporta_persona_nomo']);
        kampo("PPa adreso:", nl2br($this->datoj['pasporta_adreso']));
        kampo("Senda adreso:", nl2br($this->datoj['senda_adreso']));
        kampo("Senda faksnumero:", $this->datoj['senda_faksnumero']);

        switch($this->datoj['invitletero_sendenda'])
            {
            case '?':
                kampo("[?]", "ankorau^ decidenda, c^u sendi invitleteron");
                break;
            case 'j':
                kampo("[X]", "Sendu invitleteron");
                break;
            case 'n':
                kampo("[-]", "Ne sendu invitleteron");
                break;
            default:
                kampo("Invitletero sendenda?",
                      "eraro: '" . $this->datoj['invitletero_sendenda'] . "'");
            }
        kampo("Sendodato:", $this->datoj['invitletero_sendodato']);
        echo "</table>";
    }



} // invitpeto


?>
