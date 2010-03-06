<?php

/**
 * Instalilo por la programo - Superrigarda paĝo.
 *
 * @author Paul Ebermann
 * @version $Id$
 * @package aligilo
 * @subpackage instalilo
 * @copyright 2010 Paul Ebermann.
 *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
 */


$prafix = "..";
require_once($prafix . "/iloj/iloj.php");


HtmlKapo("speciala");
?>
<h1>Instalilo por la aligilo</h1>

<?php if(INSTALA_MODUSO) {
?>
<p>
Se vi instalas laŭ la
<a href="http://aligilo.berlios.de/instalado.html">Instalado-gvidilo</a>, la
  preparaj paŝoj jam estas faritaj, kiam vi venas ĉi tien. Ili estas ĉi tie
  en malpli longa formo, por kontroli ĉu nenio estas forgesita.
</p>
  <p><a href="#preparo">preparaj paŝoj</a>, <a href="#instalilo">instalilo</a>, <a href="#poste">post-instalaj paŝoj</a></p>

<h2 id="preparo">Preparaj paŝoj</h2>
  <p>La preparaj paŝoj estas farendaj per eksternaj programoj, ekzemple
  datumbaz-administrilo aŭ redaktilo.</p>

<ol>
  <li>Datumbazo:
  <p>Kreu datumbazon (aŭ elektu jam ekzistantan).</p>
  <li>Datumbazuzanto:
  <p>Kreu uzanton por la datumbaza sistemo (se vi ne volas uzi ekzistantan).</p>
  <p>Donu al la uzanto la necesajn rajtojn por la elektita datumbazo:
  <code>CREATE TABLE</code> (nur bezonata dum la instalado),
  <code>SELECT</code>, <code>INSERT</code>,  <code>UPDATE</code>,
  <code>CREATE TEMPORARY TABLE</code>.</p>
</li>
  <li>Programo:
  <p>Kopiu la programon al la retservilo.
  (Kiam vi legas tiun ĉi dokumenton, tio supozeble jam okazis.)
  </p></li>
  <li>Konfiguro:
  <p>Kopiu <code>htaccess-sxablono</code> al <code>.htaccess</code> kaj
  ŝanĝu la necesajn partojn en ĝi (estas komentoj tie).
  </p>
  <p>
  Kopiu la dosierujon <code>konfiguro-sxablono</code> al <code>konfiguro</code>, kaj
  enmetu en <code>datumaro.php</code> la datumbazajn informojn: uzantonomo, pasvorto,
   datumbazo-nomo. Eble ankaŭ indas ŝanĝi la datumbazan prefikson
   (necesas, se pluraj programoj uzas la saman datumbazon).
  </p>
</li>
</ol>
   <?php
   }
else { ?>
  <p>La programo nun ne estas en instala moduso. Tio signifas,
    ke vera instalado nun ne eblas.</p>
<p> Vi tamen povas voki la sekvajn instalilo-paŝojn,
    sed ili tiel ne kreas kaj plenigas tabelojn, sed nur montras la SQL-ordonojn, kiuj
    estus sendataj en instala moduso.
</p>
    <p> Se vi volas instali, necesas en <code>konfiguro/moduso.php</code> enŝalti
    la instalan moduson. Supozeble ankaŭ indas forigi ĉiujn datumbaztabelojn.
    (La enhavo tiel malaperas, certigu ke vi havas sekurkopion, se vi ankoraŭ bezonas
     ĝin.)
    </p>
<?php
 }
?>

<h2 id="instalilo">Instalilo</h2>
  <p>Voku la sekvajn tri paĝojn en tiu sinsekvo:</p>
<ol>
<li><a href="kreu_datumbazon.php">Kreu datumbazon</a>
  (kreas ĉiujn datumbaztabelojn kaj informojn por la tradukilo)</li>
  <li><a href="komencaj_datumoj.php">Komencaj datumoj</a>
  (landoj, kondiĉoj, simpla kotizosistemo)</li>
  <li><a href="uzanto_renkontigxo.php">Unua uzanto kaj renkontiĝo</a></li>
</ol>

<h2 id="poste">Post-instalaj paŝoj</h2>

<ol>
  <li>Konfiguro:
  <p>
  En <code>konfiguro/moduso.php</code>, ŝanĝu <code>INSTALA_MODUSO</code>
  al <code>false</code>. (Nun estas <code><?php echo INSTALA_MODUSO ? "true" : "false";
			  ?></code>.)
  </p>
</li>
  <li><a href="../" target="_top">Ensalutu</a>, iru al grava administrado,
  sxangxu la renkontigxon kaj enmetu pliajn uzantojn.
</ol>

<?php
  HtmlFino();
