<?php


  /**
   * Modus-konfiguroj.
   *
   * Kelkaj difinoj, por ŝanĝi la programon el unu moduso al alia.
   * 
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id: moduso.php 210 2008-09-08 22:06:52Z epaul $
   * @package aligilo
   * @subpackage konfiguro
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   * Eblaj modusoj estas 'hejme', 'monde' aŭ 'teste'.
   * temas ĉefe pri elekto de ĝusta datumbazo, krome kelkaj
   * funkcioj de la programo dependas de tie, kaj ankaŭ la
   * aspekto.
   */

//define ("MODUSO", "hejme"); 
define ("MODUSO", "monde"); 
//define ("MODUSO", "teste"); 


/**
 * EBLAS_SKRIBI difinas, ĉu nun eblas ŝanĝoj al la datumbazo.
 * Se FALSE, ĉiuj funkcioj por ŝanĝoj (per la programo) estas
 * malŝaltitaj.
 */

//define ("EBLAS_SKRIBI", true);
define ("EBLAS_SKRIBI", false);


/**
 * Se INSTALA_MODUSO == true, tiam ne eblas normalaj laboroj
 * sed nur instalado de la programo.
 *
 * Post fino de la instalado metu tion al false.
 */


// define("INSTALA_MODUSO", true);
define("INSTALA_MODUSO", false);


?>
