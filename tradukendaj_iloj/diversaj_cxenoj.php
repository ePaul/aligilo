<?php


  /**
   * Diversaj tradukendaj cxenoj uzataj dise en la programo.
   *
   * @package aligilo
   * @subpackage tradukendaj_iloj
   * @author Martin Sawitzki, Paul Ebermann
   * @version $Id$
   * @copyright 2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */



function ne_tradukita_piednoto($nova_lingvo, $origina_lingvo)
{
    // Hmm, iom stulta sistemo ... ni elpensu ion, kiu prenas
    // la lingvokodojn bezonatajn rekte el la datumbazo, depende
    // de tio, kiuj lingvoj ekzistas entute.

    switch($nova_lingvo) {
    case 'eo':
        return CH_lau("~#traduko-mankas-nun-eo", $origina_lingvo);
    case 'de':
        return CH_lau("~#traduko-mankas-nun-de", $origina_lingvo);
    case 'pl':
        return CH_lau("~#traduko-mankas-nun-pl", $origina_lingvo);
    case 'cz':
        return CH_lau("~#traduko-mankas-nun-cz", $origina_lingvo);
    case 'en':
        return CH_lau("~#traduko-mankas-nun-en", $origina_lingvo);
    case 'fr':
        return CH_lau("~#traduko-mankas-nun-fr", $origina_lingvo);
    case 'es':
        return CH_lau("~#traduko-mankas-nun-es", $origina_lingvo);
    case 'ca':
        return CH_lau("~#traduko-mankas-nun-ca", $origina_lingvo);
        // TODO: aldonu pliajn lingvojn
    default:
        return CH_lau_repl("~#traduko-mankas-nun-xxx", $origina_lingvo,
                           array('lingvo' => $nova_lingvo));
    }

}