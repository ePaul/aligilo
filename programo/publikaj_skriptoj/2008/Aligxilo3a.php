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

simpla_aliĝilo_komenco('3a', CH('aligxilo#titolo'));

?><tr><td colspan='4'>
<?php

echo CH('pasporto-detaloj-bla');

if (!$_POST['pasporta_adreso'] or !$_POST['senda_adreso'])
    {
        if (!$_POST['landonomo'])
            {
                echo "<!-- prafix: $prafix -->";

                require_once($prafix . '/iloj/iloj.php');

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

aliĝilo_tabelentajpilo('pasportnumero',
               CH('pasportnumero'),
               25);

if(!$_POST['pasporta_persona_nomo'])
    {
        $_REQUEST['pasporta_persona_nomo'] = $_POST['personanomo'];
    }

if(!$_POST['pasporta_famila_nomo'])
    {
        $_REQUEST['pasporta_familia_nomo'] = $_POST['nomo'];
    }



aliĝilo_tabelentajpilo('pasporta_persona_nomo',
               CH('persona_nomo'),
               25);



?></tr>
<tr><td/><td/>
<?php

echo "<!-- fam: '" . $_POST['pasporta_familia_nomo']. "' / '" .$_POST['nomo']. "' / '".$_REQUEST['pasporta_familia_nomo']."'-->";


aliĝilo_tabelentajpilo('pasporta_familia_nomo',
               CH('familia_nomo'),
               25);



?></tr>
<?php



if(!$_POST['pasporta_adreso'])
    {
        $_REQUEST['pasporta_adreso'] =
            ($_POST['adresaldono']?
             "\n" . $_POST['adresaldono'] : "") .
            "\n{$_POST['strato']}" .
            "\n{$_POST['posxtkodo']} {$_POST['urbo']}" .
            "\n{$landonomo}" ;

    }

aliĝilo_granda_tabelentajpilo('pasporta_adreso',
                              CH('pp-adreso'),
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
            ($_POST['adresaldono']?
             "\n" . $_POST['adresaldono'] : "") .
            "\n{$_POST['strato']}" .
            "\n{$_POST['posxtkodo']} {$_POST['urbo']}" .
            "\n{$landonomo}" ;
    }


aliĝilo_granda_tabelentajpilo('senda_adreso',
                      CH('sendo-adreso'),
                      5);

?><tr><?php

if (!$_POST['senda_faksnumero'])
    {
        $_REQUEST['senda_faksnumero'] = $_POST['telefakso'];
    }

aliĝilo_tabelentajpilo('senda_faksnumero',
               CH('faksnumero'),
               20);


simpla_aliĝilo_fino('3a');

