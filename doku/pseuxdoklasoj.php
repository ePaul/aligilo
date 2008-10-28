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



/**
 * UTF-8-kodita teksto (Pseŭdoklaso por dokumentado.)
 *
 * Oni donu normalan PHP-ĉenon. Ties enhavo estas celita
 * por rekta eldonado, ne plu estos ŝanĝota. Do ĉiuj signoj
 * estu koditaj jam ĝuste.
 *
 * @package aligilo
 * @subpackage doku
 * @author Paul Ebermann
 */
class u8string {}


/**
 * X-kodita teksto uzata en la tradukilo. (Pseŭdoklaso por dokumentado.)
 *
 * Oni donu normalan PHP-ĉenon. Ties enhavo estas estos konvertota antaŭ
 * la eldonado per {@link al_utf8()}. En ĝi eblas uzi la x-metodon por
 * la eo-signoj, kaj c'x ktp. por veraj iksoj post la suspektataj literoj.
 *
 * @package aligilo
 * @subpackage doku
 * @author Paul Ebermann
 */
class tradstring {}


/**
 * Traduk-ĉeno-identigilo. (Pseŭdoklaso por dokumentado.)
 *
 * Oni donu normalan PHP-ĉenon. Ties enhavo estas uzata
 * kiel ĉeno-identigilo por traduko. Ĝi povas enhavi
 * informojn pri la dosiero.
 *
 * Uzu {@link analizu_cxenon()} por disigi ĝin al ĉeno-
 * kaj dosiero-parto en absoluta formo.
 *
 * ĉeno-identigilo povas havas unu el la sekvaj formatoj:
 *
 * - ĉeno    (sen #, - ni uzas la lastan per
 *               {@link eniru_dosieron} anoncitan dosieron)
 * - #ĉeno   (identa)
 * - dosiero#ĉeno  (uzas la dosieron en la sama dosierujo)
 * - dosierujo/dosiero#ĉeno  (iras al alia dosierujo, relative)
 * - /dosierujo/dosiero#ĉeno  (duon-absoluta, uzas la saman
 *                               "protokolon")
 * - proto:/dosierujo/dosiero#ĉeno  (indikas absolutan lokon)
 *
 * - ~#ĉeno  malatentas la dosieron de {@link eniru_dosieron},
 *             sed provas mem eltrovi, kiu dosiero vokas nin.
 *
 * Kutime sufiĉas la baza formo (sen #). En funkcioj vokataj de aliaj
 * dosieroj uzu la ~#ĉeno-formon. La resto estas por specialaj uzoj. 
 *
 * @package aligilo
 * @subpackage doku
 * @author Paul Ebermann
 */
class tradcheno {}


/**
 * pura ASCII-ĉeno (sen speciala formato). (Pseŭdoklaso por dokumentado.)
 *
 * Ankaŭ ĉi tie temas pri normala PHP-ĉeno, kiu tamen enhavu
 * nur ASCII-signojn.
 *
 * @package aligilo
 * @subpackage doku
 * @author Paul Ebermann
 */
class asciistring {}


/**
 * ISO-kodo de lingvo (Pseŭdoklaso por dokumentado.)
 *
 * Ankaŭ ĉi tie temas pri normala PHP-ĉeno, kiu enhavu
 * kodon de lingvo - ekzemple por elekti la gxustan tradukon.
 *
 * @package aligilo
 * @subpackage doku
 * @author Paul Ebermann
 */
class lingvokodo {}


/**
 * nomo de simboltipo. (Pseŭdoklaso por dokumentado.)
 *
 * Normala PHP-ĉeno, kiu enhavas nomon de simbolo-tipo uzata
 * de la {@link leksika_analizilo leksika analizilo}.
 * Kiuj ebloj ekzistas, dependas de la uzataj leksikaj difinioj.
 *
 * La du nomoj 'komento' kaj 'spaco' havas rezervitan signifon, kaj
 * ne aperos inter la simboloj kreitaj de la leksika analizilo.
 *
 * @package aligilo
 * @subpackage doku
 * @author Paul Ebermann
 */
class simboltipo {}


/**
 * Regula esprimo en Perl-kompatibla formo. (Pseŭdoklaso por dokumentado.)
 *
 * Normala UTF-8-PHP-ĉeno, kiu enhavas regulan esprimon en la formo uzata
 * de la {@link PHPDOC#book.pcre PCRE-biblioteko}, inkluzive enkonduk- kaj
 * finsigno (samaj) kaj eble modifikilaj literoj fine. (Ni mem aldonos 'u',
 * se necesas.)
 *
 * @package aligilo
 * @subpackage doku
 * @author Paul Ebermann
 */
class uregexp {}

?>