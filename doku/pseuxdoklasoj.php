<?php

  /**
   * Kelkaj pseŭdo-klasoj por la dokumentado.
   *
   * Se unu el tiuj klasoj aperas en la dokumentaĵo kiel rezulta
   * aŭ parametra tipo, tiam temas ne pri objekto de tiu klaso, sed
   * pri iu PHP-primitiva tipo, sed kun speciala interpreto.
   * Por klarigi tiun signifon (kaj ne devi skribi ĝin ĉiam ĉi tien),
   * mi kreis tiujn klasojn, simple atingebla per ligo.
   *
   * Ili ne estos uzata en la programo mem, nur por la dokumentaĵo.
   *
   * @package aligilo
   * @subpackage doku
   * @author Paul Ebermann
   * @version $Id$
   * @copyright 2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   * Esperanta teksto en c^-kodigo. (Pseŭdoklaso por dokumentado.)
   *
   * Se por parametroj/rezultoj de funkcioj estas indikita la tipo
   * <em>eostring</em>, tiam eblas doni tekston (kutiman PHP-ĉeno).
   * Ĝi estu kodita en UTF-8, sed antaŭ la uzado de la teksto la programo
   * faros la jenajn anstataŭojn:
   *  - c^ = ĉ
   *  - g^ = ĝ
   *  - j^ = ĵ
   *  - h^ = ĥ
   *  - s^ = ŝ
   *  - u^ = ŭ
   *  (kaj analoge por la majuskloj).
   *  Aldone ni havas la sekvan anstataŭon:
   *  - E^ = €
   *
   *  La anstataŭado estos farota per la funkcioj {@link eotransformado()},
   *  ofte vokata de {@link eotransform()} aŭ {@link eoecho()}.
   *
   * @package aligilo
   * @subpackage doku
   * @author Paul Ebermann
   */
class eostring { }


/**
 * SQL-esprimo aŭ parto de tio. (Pseŭdoklaso por dokumentado.)
 *
 * Se por parametroj/rezultoj de funkcioj estas indikita la tipo
 * <em>sqlstring</em>, tiam la bezonata afero estas kutima PHP-ĉeno,
 * kiu enhavas partan aŭ tutan SQL-demandon. Detaloj kutime estu
 * en la priskribo de la parametro.
 *
 * @package aligilo
 * @subpackage doku
 * @author Paul Ebermann
 */
class sqlstring { }

/**
 * Absoluta aŭ relativa HTTP-URI, en la formo sendenda al la servilo.
 * (Pseŭdoklaso por dokumentado.)
 * 
 * Se por parametroj/rezultoj de funkcioj estas indikita la tipo
 * <em>urlstring</em>, tiam oni donu simplan PHP-ĉenon, kiu enhavas
 * (kutime relativan) HTTP-adreson. Pri eble necesa kodigo de signoj
 *  specialaj por HTML (ekzemple &amp;, sufiĉe ofta en tiaj adresoj) la
 * ricevanto zorgos.
 * @package aligilo
 * @subpackage doku
 * @author Paul Ebermann
 */
class urlstring { }


?>