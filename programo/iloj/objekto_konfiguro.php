<?php

  /**
   * Datumbazobjektoj rilate al konfiguroj.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */




/**
 * Renkontigxo-specifaj konfiguroj, kiel eblaj
 *  pagotipoj, valutoj, ktp.
 *
 * - ID
 * - renkontigxoID
 * - tipo   (ekzemple 'pagotipo', 'valuto', 'rabatkialo', ...)
 * - interna (interna identigilo de la opcio)
 * - grupo  (eble identigilo de grupo, por montri la opciojn en iuj listoj
 *           kun spacoj inter la grupoj. grupo-ID nur gravas ene de sama tipo.)
 * - teksto (esperantlingva teksto por tiu opcio - tradukebla)
 * - aldona_komento (komento, kiu nur aperas en la elektiloj ene de
 *                   la administrilo, ne en io ajn publika.)
 */
class Renkontigxa_konfiguro extends Objekto
{
    function Renkontigxa_konfiguro($id = 0)
    {
        $this->Objekto($id, "renkontigxaj_konfiguroj");
    }
}

/**
 * Listas la renkontiĝo-konfigurojn de donita tipo por iu renkontiĝo.
 * @param asciistring $tipo unu el "pagotipo", "valuto", "rabatotipo",
 *        "kromtipo" kaj "logxtipo".
 * @param Renkontigxo|int $renkontigxo (aux ID de tiu)
 * @return array ({@link Renkontigxa_konfiguro})
 */
function listu_konfigurojn($tipo, $renkontigxo = "")
{
  $renkontigxo = kreuRenkontigxon($renkontigxo);
  
  $sql = datumbazdemando(array("ID", "grupo", "interna"),
			 "renkontigxaj_konfiguroj",
			 array('tipo' => $tipo,
			       'renkontigxoID' =>
			       $renkontigxo->datoj['ID']),
			 "",
			 array('order' => "grupo ASC, interna ASC")
			 );
  $rez = sql_faru($sql);
  $listo = array();
  while ($linio = mysql_fetch_assoc($rez)) {
    $konf = new Renkontigxa_konfiguro($linio["ID"]);
    $listo []= $konf;
  }
  return $listo;
}