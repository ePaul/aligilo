<?php


  /**
   * Dua paĝo de la aliĝilo.
   *
   * Ĝi demandas pri personaj datoj kaj partopreno-detaloj.
   *
   * @package aligilo
   * @subpackage aligxilo
   * @author Paul Ebermann
   * @version $Id$
   * @since Revision 35.
   * @copyright 2001-2004 Martin Sawitzki (paĝo 'publikkontrolo.php')
   *            2004-2006 Paul Ebermann   (paĝo 'publikkontrolo.php')
   *            2006-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   */

simpla_aliĝilo_komenco(2, CH('aligxilo#titolo'));

//echo "<!-- POST:";
//var_export($_POST);
//echo "-->";

function en_adresaro() {
    return aliĝilo_aldonu_piednoton(CH("aperos-en-adresaro"), "<sup>≡</sup>");
}

function en_alilisto() {
    return aliĝilo_aldonu_piednoton(CH("aperos-en-ali-listo"));
}



echo "<tr>\n<td colspan='4'>";

echo CH("tiuj-informoj");
if ($_POST['invitletero'] == 'J')
    {
        echo CH("invitdatumoj-poste");
    }


?>
    </td>
        </tr>
        <tr>
<?php


aliĝilo_tabelentajpilo('personanomo',
                       CH('persona-nomo')
                       .deviga().en_adresaro().en_alilisto(),
                       40);
aliĝilo_tabelentajpilo('nomo',
                       CH('familia-nomo')
                       .deviga().en_adresaro().en_alilisto(),
                       '40');
?>
        </tr>
        <tr>
<?php
        ;

// aliĝilo_tabelentajpilo('telefakso',
//                        CH('telefakso'),
//                        '30', '',
//                        CH('internacia-formato'));



aliĝilo_tabelentajpilo('sxildnomo',
                       CH('sxildnomo').en_adresaro().en_alilisto(),
                       30);


// TODO: ligo al la klarigo

if ($_POST['tejo_membro_laudire'] == 'j')
    {
        aliĝilo_tabelentajpilo('ueakodo',
                               CH('uea-kodo') .
                               aliĝilo_aldonu_piednoton(CH("uea-kodo-cxar-tejo-rabato")),
                               6);
    }

?>
        </tr>
        <tr>
<?php
        ;

aliĝilo_tabel_jesne_ilo("listo",
                         CH('listo'),
                         CH('listo-jes'),
                         'J');


aliĝilo_tabel_jesne_ilo("intolisto",
                         CH('into-listo'),
                         CH('intolisto-jes'),
                         'J');

?>
        </tr>
        <tr>
<?php


aliĝilo_tabelelektilo_radie('sekso',
                            CH('sekso') .deviga(),
                            array('i' => "♀ " . CH('ina'),
                                  'v' => "♂ " . CH('vira')));


aliĝilo_tabelelektilo_radie('nivelo',
                            CH('lingva-nivelo') .deviga(),
                            array('f' => CH('lingvo-flua'),
                                  'p' => CH('lingvo-parol'),
                                  'k' => CH('lingvo-komencanto')));

?>
        </tr>
<?php

	aliĝilo_granda_tabelentajpilo('adreso',
                                  CH('adreso')
                                  .deviga()
                                  .aliĝilo_aldonu_piednoton(CH("adreso-piednoto"))
                                  .en_adresaro());

   
?>
        <tr>
<?php

        aliĝilo_tabelentajpilo('urbo',
                               CH('urbo').deviga().en_adresaro().en_alilisto(),
                               30);

        aliĝilo_tabelentajpilo('posxtkodo',
                               CH('posxtkodo').en_adresaro(),
                               10);

        
?>
        </tr>
        <tr>
<?php


aliĝilo_tabelentajpilo('retposxto',
                       CH('retposxto').en_adresaro(),
                       30);

aliĝilo_tabelentajpilo('telefono',
                       CH('telefono')
                       .aliĝilo_aldonu_piednoton(CH('internacia-formato'))
                       .en_adresaro(),
                       "30");


?>
        </tr>
        <tr>
<?php

        ;
aliĝilo_granda_tabelentajpilo("tujmesagxiloj",
                               CH("tujmesagxiloj")
                              .aliĝilo_aldonu_piednoton(CH("tujmesagxiloj-piednoto"))
                              .en_adresaro());

?>
        </tr>
        <tr>
<?php

?>
        </tr>
<?php


	simpla_aliĝilo_fino(2);

?>