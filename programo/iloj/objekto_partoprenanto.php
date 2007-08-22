<?php

  /*
   * La tabelnomoj cxi tie cxiam estas
   * la abstraktaj tabelnomoj. La traduko
   * al la konkretaj nomoj okazas en
   * iloj_sql.
   */



/**********************************
 * la datumoj de iu partoprenanto. Tabelo "partoprenanto".
 *
 * ID
 * nomo
 * personanomo
 * sxildnomo
 * sekso
 * naskigxdato
 * // TODO:agxo
 * agxo        ----  agxo je la komenco de renkontigxo.
 *                - tio estas sxovita nun al partopreno, do ne plu uzigxas
 *                  (kaj estos forigota baldaux)
 * adresaldonajxo
 * strato
 * posxtkodo
 * urbo
 * provinco
 * lando
 * sxildlando
 * okupigxo
 * okupigxteksto
 * telefono
 * telefakso
 * retposxto
 * retposxta_varbado - j (sendu ikse), n (ne sendu), u (sendu unikode)
 * ueakodo
 * rimarkoj   - ne plu uzata TODO: forigu
 * kodvorto   ???
 * malnova   (ankoraux el 2001, kiam ne ekzistis "partopreno" - ne plu uzata).
 */

class Partoprenanto extends Objekto
{

    /** persona pronomo: "s^i"/"li"/"ri" */
    var $personapronomo;
    /* sekso: "ina"/"vira"/"ielsekse" */
    var $sekso;

    /**
     * Konstruilo.
     *
     * Kreas/elprenas partoprenanton kaj
     * eltrovas sekson/personan pronomon.
     */
    function Partoprenanto($id=0)
    {
        // super-konstruilo
        $this->Objekto($id,"partoprenantoj");

        switch ($this->datoj[sekso])
            {
            case "i": $this->personapronomo = "s^i";$this->sekso = "ina";break;
            case "v": $this->personapronomo = "li";$this->sekso = "vira";break;
            default:  $this->personapronomo = "ri";$this->sekso = "ielsekse";
            }
    }


    /**
     * kreas (kaj redonas) tekstan tabelon de la plej gravaj detaloj,
     * ekzemple por konfirmilo aux informaj mesagxoj al organizantoj.
     */
    function gravaj_detaloj_tekste()
    {
        $teksto =
            "\nNomo (sekso):  " . $this->tuta_nomo() . " (" . 
            $this->datoj['sekso'] . ")"
            ;
        if ($this->datoj['adresaldonajxo'])
            {
                $teksto .=
                    "\n               " . $this->datoj['adresaldonajxo'];
            }
        $teksto .=
            "\nStrato         " . $this->datoj['strato'] .
            "\nLoko:          " . $this->datoj['posxtkodo'] . ", " .
            $this->datoj['urbo'];
        if ($this->datoj['provinco'])
            {
                $teksto .=
                    "\nProvinco:      " . $this->datoj['provinco'];
            }
        $teksto .=
            "\nLando:         " . $this->landonomo() . "(" .
            $this->landokategorio() . ")";
        if ($this->datoj['sxildlando'])
            {
                $teksto .= "\nS^ildlando: ". $this->datoj['sxildlando'];
            }
        $teksto .= 
            "\nNaskig^dato:    " . $this->datoj['naskigxdato'] .
            "\n" .
            "\nTelefono:      " . $this->datoj['telefono'] .
            "\nTelefakso:     " . $this->datoj['telefakso'] .
            "\nRetpos^to:      " . $this->datoj['retposxto'];
        if ($this->datoj['ueakodo'])
            {
                $teksto .=
                    "\nUEA-kodo:      " . $this->datoj['ueakodo'];
            }
        return $teksto;
    }

    /**
     * Montras la partoprenanton kiel HTML-tabelo.
     */
    function montru_aligxinto($sen_bla = FALSE)
    {
        if(! $sen_bla)
            {
                eoecho ("Informado pri partoprenantoj....");
	  
                // TODO: senhxaosigi ...
	  	  
                rajtligu("partrezultoj.php?dis_ago=estingi","estingi","anzeige","estingi",'n');
            }
        echo  "<table>\n";
        kampo("ID:",$this->datoj[ID]);
        kampo("nomo:", $this->tuta_nomo() . " (".$this->datoj[sekso].")");
        // 	if ($this->datoj[sxildnomo]!='')
        // 	  {
        // 		kampo("nomo:",$this->datoj[personanomo]." (".$this->datoj[sxildnomo].") ".$this->datoj[nomo]." (".$this->datoj[sekso].")");
        // 	  }
        // 	else
        // 	  kampo("nomo:",$this->datoj[personanomo]." ".$this->datoj[nomo]." (".$this->datoj[sekso].")");
        if ($this->datoj[adresaldonajxo])
            {
                kampo("",$this->datoj[adresaldonajxo]);
            }
        kampo("strato:",$this->datoj[strato]);
        kampo("loko:",$this->datoj[posxtkodo].", ".$this->datoj[urbo]);
        if ($this->datoj[provinco]) {kampo("provinco:", $this->datoj[provinco]);}
        kampo("lando:",
              $this->landonomo().
              ' ('.$this->landokategorio().')');
        if ($this->datoj[sxildlando]!='') {kampo("s^ildlando:", $this->datoj[sxildlando]);}
  
        if (okupigxo_eblas == 'jes')
            {
                kampo($this->personapronomo." ".okupigxtipo($this->datoj[okupigxo]),
                      $this->datoj[okupigxteksto]);
            }
        kampo("naskita:",$this->datoj[naskigxdato]);
        if ($this->datoj[telefono])
            {
                kampo("telefono:",$this->datoj[telefono]);
            }
        if ($this->datoj[telefakso])
            {
                kampo("telefakso:",$this->datoj[telefakso]);
            }
        if ($this->datoj['retposxto'])
            {
                kampo("retpos^to:",$this->datoj['retposxto']);

                switch($this->datoj['retposxta_varbado'])
                    {
                    case 'n':
                        kampo("-", "ne volas retpos^tajn informojn");
                        break;
                    case 'j':
                        kampo('x', "volas retpos^tajn informojn x-kode");
                        break;
                    case 'u':
                        kampo ('u', "volas retpos^tajn informojn unikode");
                        break;
                    }
            }
        if ($this->datoj['ueakodo'])
            {
                kampo("UEA-kodo:", $this->datoj['ueakodo']);
            }
        echo "</table>\n";
    }

    /**
     * redonas la tutan nomon de la partoprenanto ("personanomo nomo").
     */
    function tuta_nomo()
    {
        if ($this->datoj['sxildnomo'])
            {
                return $this->datoj['personanomo'] . " (" .$this->datoj['sxildnomo']. ") "
                    . $this->datoj['nomo'];
            }
        return $this->datoj['personanomo'] . " " . $this->datoj['nomo'];
    }


    function landonomo()
    {
        return eltrovu_landon($this->datoj['lando']);
    }

    function loka_landonomo()
    {
        return eltrovu_landon_lokalingve($this->datoj['lando']);
    }

    function landokategorio()
    {
        return eltrovu_landokategorion($this->datoj['lando']);
    }


} // partoprenanto



?>
