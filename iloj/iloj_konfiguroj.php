<?php

  /**
   * Funkcioj rilate al konfigurajxoj.
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage iloj
   * @copyright 2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   */

;

/**
 * redonas la tekston (eble kun tradukoj)
 * por iu konfigura opcio.
 *
 * @param asciistring $tipo
 * @param asciistring $interna
 * @param int         $renkID
 * @return eostring 
 */
function donu_konfiguran_tekston($tipo, $interna, $renkID)
{
    $id = eltrovu_gxenerale('id',
                            'renkontigxaj_konfiguroj',
                            array('tipo' => $tipo,
                                  'interna' => $interna,
                                  'renkontigxoID' => $renkID));
    $konfiguro = new Renkontigxa_Konfiguro($id);
    return $konfiguro->tradukita('teksto');


} // donu_konfiguran_tekston