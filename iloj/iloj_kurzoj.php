<?php


  /**
   * Funkcioj rilate al kurzoj.
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage iloj
   * @copyright 2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */


  /**
   * eltrovas la lastan kurzon de valuto gxis iu dato.
   *
   * @param asciistring $valuto
   * @param datostring $dato
   */
function eltrovu_kurzon($valuto, $dato)
{
    if ($valuto == CXEFA_VALUTO) {
        return array(1.0, null);
    }
    
    $sql = datumbazdemando(array('kurzo', 'dato'),
                           'kurzoj',
                           array('valuto' => $valuto,
                                 "dato <= '$dato'"),
                           "",
                           array('order' => 'dato DESC',
                                 'limit' => '0,1'));
    
    $linio = mysql_fetch_assoc(sql_faru($sql));
    if ($linio)
        return array($linio['kurzo'],
                     $linio['dato']);
    else
        return null;
}