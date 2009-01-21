<?php

  /**
   * Aliĝilo - paĝo 3a (informoj por invitletero).
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2007-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */

simpla_aliĝilo_komenco('4', CH('aligxilo#titolo'));

?><tr><td colspan='4'>
<?php

if ($_POST['invitletero'] != 'J') {
    
    echo "<p>" . CH("ne-bezonas") . "</p>\n";
}
 else {
     echo "<p>" . CH('pasporto-detaloj-bla') . "</p>\n";


if (!$_POST['pasporta_adreso'] or !$_POST['senda_adreso'])
    {
        if (!$_POST['landonomo'])
            {
                $landonomo = eltrovu_landon($_POST['lando']);
                tenukasxe('landonomo', $landonomo);
            }
        else
            {
                $landonomo = $_POST['landonomo'];
            }
}

  
?></td></tr>
<tr>
<?php

        ;
 aliĝilo_tabelentajpilo('pasportnumero',
                        CH('pasportnumero') . deviga(),
                        25);

?></td></tr>
<tr>
<?php
 
        ;
 require_once($prafix . "/tradukendaj_iloj/trad_htmliloj.php");
 
 echo "<th>" . CH("valida-de").deviga() . "</th><td>";
 simpla_datoelektilo('pp_validas_de', 2009, 1980);


 echo "</td><th>" . CH("valida-gxis"). deviga() . "</th><td>";
 simpla_datoelektilo('pp_validas_gxis', 2030, 2009);

?></tr>
<tr>
<?php

if(!$_POST['pasporta_persona_nomo'])
    {
        $_REQUEST['pasporta_persona_nomo'] = $_POST['personanomo'];
    }

if(!$_POST['pasporta_famila_nomo'])
    {
        $_REQUEST['pasporta_familia_nomo'] = $_POST['nomo'];
    }


aliĝilo_tabelentajpilo('pasporta_persona_nomo',
                       CH('persona_nomo').deviga(),
                       25);




echo "<!-- fam: '" . $_POST['pasporta_familia_nomo']. "' / '" .$_POST['nomo']. "' / '".$_REQUEST['pasporta_familia_nomo']."'-->";


aliĝilo_tabelentajpilo('pasporta_familia_nomo',
                       CH('familia_nomo').deviga(),
               25);



?></tr>
<?php



if(!$_POST['pasporta_adreso'])
    {
        $_REQUEST['pasporta_adreso'] =
            $_POST['adreso'] .
            "\n{$_POST['posxtkodo']} {$_POST['urbo']}" .
            "\n{$landonomo}" ;

    }

aliĝilo_granda_tabelentajpilo('pasporta_adreso',
                              CH('pp-adreso').deviga(),
                              5);


?><tr><td colspan='4'>
<?php

echo CH('invitletero-sendado-bla');

?></td></tr>
<?php
if(!$_POST['senda_adreso'])
    {
        $_REQUEST['senda_adreso'] =
            "{$_POST['personanomo']} {$_POST['nomo']}" .
            "\n{$_POST['adreso']}" .
            "\n{$_POST['posxtkodo']} {$_POST['urbo']}" .
            "\n{$landonomo}" ;
    }


aliĝilo_granda_tabelentajpilo('senda_adreso',
                              CH('sendo-adreso').deviga(),
                      5);

?><tr><?php

if (!$_POST['senda_faksnumero'])
    {
        $_REQUEST['senda_faksnumero'] = $_POST['telefakso'];
    }

aliĝilo_tabelentajpilo('senda_faksnumero',
                       CH('faksnumero') .
                       aliĝilo_aldonu_piednoton(CH("faksnumero-formato")),
                       20);

 }


simpla_aliĝilo_fino('4');

