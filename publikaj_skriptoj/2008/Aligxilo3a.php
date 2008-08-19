<?php

  //$lingvoj = array('eo', 'de');

  //kontrolu_lingvojn($lingvoj);

simpla_aligxilo_komenco('3a',
                        CH('aligxilo#titolo'),
                        /*					  array('eo' => "50a IS &ndash; ali&#285;ilo",
                         'de' => "50. IS &ndash; Anmeldeformular"),*/
					 $lingvoj);

?><tr><td colspan='4'>
<?php

echo CH('pasporto-detaloj-bla');

if (!$_POST['pasporta_adreso'] or !$_POST['senda_adreso'])
    {
        if (!$_POST['landonomo'])
            {
                echo "<!-- prafix: $prafix -->";

                require_once($prafix . '/iloj/iloj.php');
                //                require_once('datumbazkonekto.php');
                $landonomo = eltrovu_landon($_POST['lando']);
                echo
                    "<input type='hidden' name='landonomo' value='$landonomo' />\n";
            }
        else
            {
                $landonomo = $_POST['landonomo'];
            }
}

  
?></td></tr>
<tr>
<?php

tabelentajpilo('pasportnumero',
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



tabelentajpilo('pasporta_persona_nomo',
               CH('persona_nomo'),
               25);



?></tr>
<tr><td/><td/>
<?php

echo "<!-- fam: '" . $_POST['pasporta_familia_nomo']. "' / '" .$_POST['nomo']. "' / '".$_REQUEST['pasporta_familia_nomo']."'-->";


tabelentajpilo('pasporta_familia_nomo',
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

granda_tabelentajpilo('pasporta_adreso',
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


granda_tabelentajpilo('senda_adreso',
                      CH('sendo-adreso'),
                      5);

?><tr><?php

if (!$_POST['senda_faksnumero'])
    {
        $_REQUEST['senda_faksnumero'] = $_POST['telefakso'];
    }

tabelentajpilo('senda_faksnumero',
               CH('faksnumero'),
               20);


simpla_aligxilo_fino('3a');

?>
