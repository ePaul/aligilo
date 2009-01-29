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
   * @copyright 2007-2008 Paul Ebermann.
   *       Uzebla laŭ kondiĉoj de GNU Ĝenerala Publika Permesilo (GNU GPL)
   */





  /**
   */


function kotizo_baza_tabelgrupo($kalkulilo) {
    return array('titolo' => CH_mult("~#programo"),
                 'signo' => '+',
                 array('titolo' => CH_mult("~#baza"),
                       'detaloj' =>
                       array('kategorioj' => $kalkulilo->kategorioj,
                             'dauxro' => $kalkulilo->partoprentempo),
                       'valoro' => array('kvanto' => $kalkulilo->partakotizo,
                                         'valuto' => CXEFA_VALUTO)));
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
    return CH_mult("~#parttempa");
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