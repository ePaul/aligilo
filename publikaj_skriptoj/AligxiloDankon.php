<?php

/*
 * varianto de /is/admin/publikaj_skriptoj/publikkontrolo.php,
 * kreita 2006-10-08 por la rolfo-a IS-pagxaro.
 */


$lingvoj = array('eo', 'de');

kontrolu_lingvojn($lingvoj);

simpla_aligxilo_komenco(6 /*pasxo de la aligxilo*/,
                        CH('/2007/aligxilo#titolo') ,
                        $lingvoj);

require_once ('datumbazkonekto.php');

$renkontigxo = new Renkontigxo(DEFAUXLTA_RENKONTIGXO);



// kontrolado okazis en kontrolu.php

protokolu('aligxo');

      //Enmeti la datumojn en la datumaro

$partoprenanto = new Partoprenanto();
$partoprenanto->kreu();
$partoprenanto->kopiu();

$partoprenanto->skribu();


	  //	  echo "<!-- partoprenanto: \n";
	  //	  var_export($partoprenanto->datoj);
	  //	  echo "-->\n";


$partopreno = new Partopreno();
$partopreno->kreu();
$partopreno->kopiu();

	  //	  echo "<!-- partopreno: \n";
	  //	  var_export($partopreno->datoj);
	  //	  echo "-->\n";

        //$partopreno->montru();

if ($partopreno->datoj['de'] == $renkontigxo->datoj[de] and
	 $partopreno->datoj[gxis] == $renkontigxo->datoj[gxis])
    {
        $partopreno->datoj['partoprentipo']="t";
    }
else
    {
        $partopreno->datoj['partoprentipo']="p";
    }

if ( $domotipo[0] == "J" )
	{
	  $partopreno->datoj[kunmangxas] = "J";
	}
  else
	{
	  $partopreno->datoj['kunmangxas'] = "N";
	}
      
    $partopreno->datoj['aligxdato'] = date("Y-m-d");

	if($_POST['cxambrotipo'] == 'd') // dulita
	{
		// gea cxambro
		$partopreno->datoj['cxambrotipo'] = 'g';
		$partopreno->datoj['dulita'] = 'J';
	}

      
$partopreno->datoj['renkontigxoID']=$renkontigxo->datoj["ID"];
$partopreno->datoj['partoprenantoID']=$partoprenanto->datoj['ID'];

      $partopreno->datoj['1akonfirmilosendata']=date("Y-m-d");
      $partopreno->datoj['alvenstato']='v';
      $partopreno->datoj['traktstato']='N';
      $partopreno->datoj['havasNomsxildon']='N';
      $partopreno->datoj['havasMangxkuponon']='N';
	  $partopreno->datoj['KKRen'] = 'n';
	  $partopreno->datoj['surloka_membrokotizo'] = 'n';

      $partopreno->datoj['tejo_membro_kontrolita'] = '?';

      if ($partopreno->datoj['tejo_membro_laudire']{0} != 'j')
          {
              $partopreno->datoj['tejo_membro_laudire'] = 'n';
          }

	 if($partopreno->datoj['nivelo'] == 'k')
	 {
		// komencanto
		$partopreno->datoj['komencanto'] = 'J';
	 }
	 else
	 {
		$partopreno->datoj['komencanto'] = 'N';
	 }


 
    $partopreno->skribu();

	  rekalkulu_agxojn($partopreno->datoj['ID']);

if ($partopreno->datoj['invitletero']=='J')
    {
        $invitpeto = new Invitpeto();
        $invitpeto->kopiu();
        $invitpeto->datoj['ID'] = $partopreno->datoj['ID'];
        $invitpeto->datoj['invitletero_sendenda'] = '?';
        $invitpeto->datoj['invitletero_sendodato'] = '0000-00-00';
        $invitpeto->skribu_kreante_kun_ID();
    }

	  $partopreno = new Partopreno($partopreno->datoj['ID']);

       // TODO: Etwa hier sollten wir auch n�tige Mails verschicken
       // TODO: k�nnen wir mit partoprenkontrolo zusammenlegen.

	  sendu_auxtomatajn_mesagxojn($partopreno, $partoprenanto, $renkontigxo);


      //$vosto = $sekvontapagxo."?&enkodo=$enkodo&kodnomo=$kodnomo&kodvorto=$kodvorto&partoprenantoidento=$partoprenantoidento&partoprenidento=$partoprenidento";
          

      //automatisches Backup

sendu_ekzport($partoprenanto,$partopreno, $renkontigxo);
require_once($prafix . '/iloj/retmesagxiloj.php');
require_once($prafix . '/iloj/iloj_konfirmilo.php');
require_once($prafix . '/iloj/diversaj_retmesagxoj.php');
sendu_sekurkopion_de_aligxinto($partoprenanto, $partopreno, $renkontigxo,
                               "Alig^ilo");

?>
        <tr>
          <td colspan='4'>
				<h1>
<?php
        echo CH('gratulojn');
/*
	echo lauxlingve(array('eo' => "Gratulojn pro via ali&#285;o!"));
*/
?></h1>
<?php

	if ($_POST['retposxto'])
	{
        echo "<p>" . CH('konfirmilo-sendita', "<em>" . $_POST['retposxto'] . "</em>") . "</p>\n";
	}
	else
	{
        echo "<p>" . CH('konfirmilo-por-konservado') . "</p>\n";
    }

// sendu (kopion) ecx, se li ne donis retadreson.
$konfirmilo_teksto = kreu_kaj_sendu_unuan_konfirmilon($partoprenanto,
                                                      $partopreno,
                                                      $renkontigxo);


// 	  sendu_konfirmilon($partoprenanto,$partopreno,$renkontigxo, $konfirmilo_teksto);
// 	  $konfirmilo_teksto = faru_1akonfirmilon($partoprenanto,$partopreno,
//                                        $renkontigxo);

echo "<pre>" . eotransformado($konfirmilo_teksto, 'utf-8') . "</pre>\n";

?>
</td>
        </tr>
	</table>
   </form>
</body>
</html>
