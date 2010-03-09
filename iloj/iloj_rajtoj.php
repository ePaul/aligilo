<?php


  /**
   * Iloj por trakti rajtojn de entajpantoj.
   *
   * @author Paul Ebermann
   * @version $Id$
   * @package aligilo
   * @subpackage iloj
   * @copyright 2008-2010 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



  /**
   */


{

    /**
     * interna nomo, longa nomo, mallongigo por tabeltitolo
     */
    $tmplisto = array(array($x = "aligi",        $x,           "al&shy;igi"),
                      array($x = "vidi",         $x,           "vi&shy;di"),
                      array(     "sxangxi",      "s^ang^i",    "s^an&shy;g^i"),
                      array(     "cxambrumi",    "c^ambrumi",  "c^ambr."),
                      array(     "ekzporti",     "eksporti",   "eksp."),
                      array($x = "statistikumi", $x,           "stat."),
                      array(     "mono",      "entajpi monon", "mo&shy;no"),
                      array($x = "estingi",      $x,           "est."),
                      array($x = "retumi",       $x,           "ret."),
                      array($x = "rabati",       $x,           "rab."),
                      array($x = "inviti",       $x,           "inv."),
                      array($x = "administri",   $x,           "ad&shy;min."),
                      array($x = "akcepti",      $x,           "akc."),
                      array($x = "teknikumi",    $x,           "tekn."));
    //    echo "<!--";
    //    var_export($tmplisto);
    //    echo "-->";
  foreach($tmplisto AS $ero)
	{
        $GLOBALS['rajtolisto'][]= array("rajto" => $ero[0],
                             		    "alias" => $ero[1],
                                        "mallongigo" => $ero[2]);
	}
  unset($tmplisto);
}


/**
 * Kontrolas iun rajton de la aktuala uzanto.
 *
 * Faras demandon al la datumbazo tiucele.
 * @param $ago nomo de kolumno en la rajto-tabelo.
 * @return boolean true, se la uzanto havas tiun rajton,
 *                 false alikaze (ankaŭ se la uzanto ne
 *                  ekzistas aŭ pasvorto malĝustas).
 * @global string _SESSION["kodvorto"]  la pasvorto de la
 *                 uzanto, uzata por kontroli.
 * @global string _SESSION["kodnomo"] la uzantonomo por
 *                 kontroli la rajton.
 */
function rajtas($ago)
{
  $kodnomo =& $_SESSION["kodnomo"];
  if(!isset($kodnomo))
    return false;

    $sql = datumbazdemando(array($ago, "kodvorto"),
                           "entajpantoj",
                           array('nomo' => $kodnomo),
                           "",
                           array("order" => "id"));
    $row = mysql_fetch_assoc(sql_faru($sql));

    return 
        $row
        and ($row['kodvorto'] == $_SESSION['kodvorto']) 
        and ('J' == $row[$ago] );
}


/**
 * donas erarmesaĝon, ke la uzanto ne rajtas fari ion,
 * kaj finas la skripton.
 *
 * @param string $ago kiun rajton oni bezonus.
 * @todo prenu la nomon, kie plendi el la konfiguro.
 * @todo ĉu iel taŭge fini la HTML-strukturon?
 */
function ne_rajtas($ago="?")
{
  eoecho ("Malg^usta kodvorto au^ nomo ne ekzistas, au^ eble vi ne rajtas uzi tiu c^i pag^on ($ago)<BR>");
  eoecho ("Se vi pensas, ke vi devus rajti, kaj ke vi donis la g^ustan kodvorton, plendu c^e Pau^lo."); // TODO: Pauxlo -> el konfiguro
  ligu("index.php","<-- reen al la komenca pag^o","_top");

  // TODO: exit() finas la tutan skripton, sen zorgi, ke la HTML estas ie en la mezo ...
  // Eble iom helpus voki htmlFino().
  exit();
}

/**
 * Certigas, ke la nuna uzanto rajtas fari ion.
 * 
 * Se la uzanto rajtas, nenio okazos.
 * Se la nuna uzanto ne havas la rajton, ni eldonas
 * erarmesaĝon kaj finos la skripton.
 * @param string $ago
 */
function kontrolu_rajton($ago)
{
  if (! rajtas($ago) )
	{
	  ne_rajtas($ago);
	}
}

