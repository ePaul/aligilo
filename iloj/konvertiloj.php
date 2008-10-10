<?php

  /**
   * Kelkaj funkcioj por konverti cxenojn inter diversaj formatoj.
   *
   * @package aligilo
   * @subpackage iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2001-2004 Martin Sawitzki, 2004-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */

  /**
   */




/**
 * transkodigas tekston por simpla PDF-eldono.
 *
 * @param string $teksto, en UTF-8, kun esperanta c^-kodigo.
 * @return la sama teksto, kun transkodigo de la c^-koditaj
 *         eo-signoj al la respektivaj lokoj en la PDF-kodigo.
 * @see zeichensatz.php
 */
function eo($teksto)
{
    $trans = array ("C^" => chr(195), "c^" => chr(164),
					"G^" => chr(207), "g^" => chr(223),
					"H^" => chr(176), "h^" => chr(167),
					"J^" => chr(165), "j^" => chr(162),
					"S^" => chr(163), "s^" => chr(217),
					"U^" => chr(186), "u^" => chr(170),
					chr(223) => chr(175), // ß estas en iom speciala loko.
					//					"E^" => chr(128));
					"E^" => "EUR");

    return (strtr(utf8_decode($teksto), $trans));
}



/**
 * transkodigas tekston por unikoda PDF-eldono.
 *
 * transkodigas tekston en UTF-8 kun ^ al UTF-8
 * kun veraj supersignoj.
 * Tio estas uzata por la unikoda PDF-eldono.
 * @param string $teksto, en UTF-8, kun esperanta c^-kodigo.
 * @return la sama teksto, kun ankaŭ esperantaj signoj en UTF-8.
 */
function uni($teksto)
{
  return eotransformado($teksto, "utf-8");
}


  /**
   * eltrovas, ĉu unikoda teksto estas kodigebla en nia PDF-kodigo.
   *
   * Kontrolas, ĉu unu el la signoj de $teksto, interpretita kiel
   * UTF-8-teksto, estas ekster nia varianto de la kodigo Latina-1.
   *
   * @param string $teksto UTF-8-kodita teksto, eble kun la esperantaj signoj
   *                      en c^-kodigo.
   * @return boolean true, se $teksto enhavas almenaŭ unu signon, kiu
   *                       ne aperas en nia speciala PDF-kodigo (varianto
   *                       de UTF-8, kun Eo-signoj kaj eĉ la Eo-ovo.),
   *                       alikaze false.
   */
function estas_ekster_latin1($teksto) {
  // TODO: pripensu, ĉu ankaŭ eblas tion
  // legi el la UTF-8 versio. (Tamen ne tiom gravas.)
  $cxiujdatoj_utf16 = mb_convert_encoding($teksto, "UTF-16", "UTF-8");

  // tiuj signoj, kiuj mankas en nia speciala
  // PDF-varianto de Latin-1
  $malpermesitaj = array(162, 163, 164, 165, 167, 170, 175, 176,
                         186, 188, 195, 207, 217);

  for ($i = 0; $i < strlen($cxiujdatoj_utf16); $i += 2)
	{
        if (ord($cxiujdatoj_utf16{$i}) > 0
            // -> litero > 256, t.e. ne en ISO-8859-1
             or in_array(ord($cxiujdatoj_utf16{$i+1}), $malpermesitaj)
            // unu el la malpermesitaj
            )
		return true;
	}
  return false;
}



/**
 * transformas de la post-^-methodo (c^)
 * al (HTML-)unikoda esperanto, aŭ al la x-metodo.
 *
 * @param eostring $texto Teksto en UTF-8 kun c^-koditaj
 *               supersignoj.
 * @param string $enkodo la transform-maniero por la teksto, unu el la
 *                sekvaj valoroj:
 *  - "por-tradukilo": transformas cx-kodigon al c'x ktp., kaj
 *                   samtempe c^ al cx, kaj E^ al €.
 *                     Estas uzata por transformi datumbazenhavon al
 *                    ĉenoj taŭgaj por la tradukilo.
 *  - "x-metodo": transformas la Eo-signojn al iksa-kodigo,
 *             "E^" al "Euro".
 *  - "unikodo": HTMLa unikoda transformo, ekzemple &#265; por c^.
 *  - "utf-8": rekta UTF-8-kodigo.
 *  - "pdf-speciala": Kodigo al la speciala PDF-kodigo uzata de niaj
 *         tiparoj por FPDF (la ne-unikoda versio). "E^" iĝas
 *         "EUR", kaj ĉiuj ne-latin-1-aj signoj (kaj kelkaj aliaj)
 *         ne estas montreblaj tiel (Vidu {@link eo()},
 *         {@link estas_ekster_latin1()}). Kontraŭe al la aliaj
 *         kodigoj, tiu ĉi ne nur ŝanĝas la ^-koditajn signojn,
 *         sed transkodigas la tutan tekston.
 *   - "identa": identa transformo - ŝanĝas nenion.
 *
 *  Ĉiuj aliaj valoroj nun ankaŭ funkcias kiel la identa
 *   transformo, sed eble estonte aldoniĝos pliaj transformoj.
 *  
 * @return string la transformita teksto.
 */
function eotransformado($teksto,$enkodo)
{
    switch($enkodo) {
    case 'pdf-speciala':
        return eo($teksto);
        
    case "por-tradukilo":
        $trans = array("Cx" => "C'x", "cx" => "c'x",
                       "Gx" => "G'x", "gx" => "g'x",
                       "Hx" => "H'x", "hx" => "h'x",
                       "Jx" => "J'x", "jx" => "j'x",
                       "Sx" => "S'x", "sx" => "s'x",
                       "Ux" => "U'x", "ux" => "u'x",
                       
                       "C^" => "Cx", "c^" => "cx",
                       "G^" => "Gx", "g^" => "gx",
                       "H^" => "Hx", "h^" => "hx",
                       "J^" => "Jx", "j^" => "jx",
                       "S^" => "Sx", "s^" => "sx",
                       "U^" => "Ux", "u^" => "ux",
                       "E^" => "€");
        break;
    case "x-metodo":
        $trans = array("C^" => "Cx", "c^" => "cx",
                       "G^" => "Gx", "g^" => "gx",
                       "H^" => "Hx", "h^" => "hx",
                       "J^" => "Jx", "j^" => "jx",
                       "S^" => "Sx", "s^" => "sx",
                       "U^" => "Ux", "u^" => "ux",
                       "E^" => "Euro");
        break;
    case "unikodo":
        $trans = array ("C^" => "&#264;", "c^" => "&#265;",
                        "G^" => "&#284;", "g^" => "&#285;",
                        "H^" => "&#292;", "h^" => "&#293;",
                        "J^" => "&#308;", "j^" => "&#309;",
                        "S^" => "&#348;", "s^" => "&#349;",
                        "U^" => "&#364;", "u^" => "&#365;",
                        "E^" => "&#8364;"); // TODO: eble ni uzu &euro; ?
        break;
    case "utf-8":
        $trans = array("C^" => "Ĉ", "c^" => "ĉ",
                       "G^" => "Ĝ", "g^" => "ĝ",
                       "H^" => "Ĥ", "h^" => "ĥ",
                       "J^" => "Ĵ", "j^" => "ĵ",
                       "S^" => "Ŝ", "s^" => "ŝ",
                       "U^" => "Ŭ", "u^" => "ŭ",
                       "E^" => "€");
        break;
    case 'identa':
    default:
        $trans = array();
    }
    return strtr($teksto, $trans);
}


/**
 * Transformas tekston el nia esperanta c^-kodigo al
 * la defaŭlta kodigo.
 *
 * @param eostring $io transforminda teksto
 * @global string _SESSION['enkodo'] kodigo uzenda
 * @global string GLOBALS['enkodo']   kodigo uzenda, se $_SESSION["enkodo"] ne ekzistas. (Se ankaŭ tiu ne ekzistas, uzu "unikodo".
 * @return string la transformita teksto.
 * @uses eotransformado
 */
function eotransform($io)
{
    $enkodo = $_SESSION['enkodo'] or
        $enkodo = $GLOBALS['enkodo'] or
        $enkodo = "unikodo";
    return eotransformado($io, $enkodo);
}


/**
 * helpfunkcio por konverti nomon de funkcio al legebla
 * teksto por iu listo.
 *
 * @todo trovu pli bonan lokon, eble ĉe aliaj konverto-funkcioj.
 * @param string funknomo la nomo de PHP-funkcio (aux parto de tio),
 *        kun Eo-signoj en X-kodigo, vortdividoj per "_".
 * @return eostring la nomo en legebla formo, en c^-kodigo.
 */
function konvertu_funkcinomon($funknomo) {
    return strtr($funknomo, "x_", "^ ");
}



?>