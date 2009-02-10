<?php

  /**
   * La tradukendaj partoj de la kotizokalkulilo.
   *
   * @see iloj_kotizo.php
   *
   * @package aligilo
   * @subpackage iloj
   * @author Paul Ebermann
   * @version $Id$
   * @since Revizo 141 (antauxe parto de iloj_kotizo.php)
   * @copyright 2007-2009 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */





  /**
   */

function kotizo_kategorio_titolo($tipo) {
    switch($tipo) {
    case 'lando':
        return CH_mult("~#landokategorio");
    case 'aligx':
        return CH_mult("~#aligxkategorio");
    case 'agx':
        return CH_mult("~#agxkategorio");
    case 'logx':
        return CH_mult("~#logxkategorio");
    }
}

function kotizo_kategorioj_titolo() {
    return CH_mult("~#kategorioj");
}


function kotizo_partoprentempo_titolo()
{
    return CH_mult("~#partoprentempo");
}

function kotizo_partoprentempo_teksto($tipo, $noktoj)
{
    if ($tipo == 't')
        return CH_mult("~#tuttempa");
    else
        switch($noktoj) {
        case 0:
            return CH_mult("~#parttempa-0-1");
        case 1:
            return CH_mult("~#parttempa-1-2");
        default:
            return CH_mult("~#parttempa", $noktoj, $noktoj + 1);
        }
}


function kotizo_minimumo_titolo() {
    return CH_mult("~#min");
}

function kotizo_sumo_titolo() {
    return CH_mult("~#sum");
}

function kotizo_restas_pagenda_titolo() {
    return CH_mult("~#restas-pagenda");
}


function kotizo_programo_titolo() {
    return CH_mult("~#programo");
}

function kotizo_baza_titolo() {
    return CH_mult("~#baza");
}


function kotizo_parttempa_titolo() {
    return CH_mult("~#parttempaKot");
}

function kotizo_pagoj_titolo() {
    return CH_mult('~#pagoj');
}

function kotizo_mangxoj_titolo() {
    return CH_mult('~#mangxoj');
}

function kotizo_rabatoj_titolo() {
    return CH_mult("~#rabatoj");
}

function kotizo_krompagoj_titolo() {
    return CH_mult("~#krompagoj");
}

