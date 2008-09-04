<?php

  /**
   * Modus-konfiguroj.
   *
   * Kelkaj difinoj, por sxangxi la programon el unu moduso al alia.
   * 
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage konfiguro
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   * Eblaj modusoj estas 'hejme', 'monde' aux 'teste'.
   * temas cxefe pri elekto de gxusta datumbazo, krome kelkaj
   * funkcioj de la programo dependas de tie, kaj ankaux la
   * aspekto.
   */

define ("MODUSO", "hejme"); 
//define ("MODUSO", "monde"); 
//define ("MODUSO", "teste"); 


/**
 * EBLAS_SKRIBI difinas, cxu nun eblas sxangxoj al la datumbazo.
 * Se FALSE, cxiuj funkcioj por sxangxoj (per la programo) estas
 * malsxaltitaj.
 */

define ("EBLAS_SKRIBI", true);
//define ("EBLAS_SKRIBI", false);


/**
 * Se INSTALA_MODUSO == true, tiam ne eblas normalaj laboroj
 * sed nur instalado de la programo.
 *
 * Post fino de la instalado metu tion al false.
 */


define("INSTALA_MODUSO", true);
// define("INSTALA_MODUSO", false);

?>