-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-6
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 22. April 2008 um 21:07
-- Server Version: 5.0.32
-- PHP-Version: 4.4.4-8+etch4
-- 
-- strukturo, nun kun malaligxtraktosistemoj
-- 
-- 
-- Datenbank: `pagxaro`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_agxkategorioj`
-- 

CREATE TABLE `is_agxkategorioj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(20) collate utf8_esperanto_ci NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `sistemoID` int(11) NOT NULL,
  `limagxo` int(11) NOT NULL COMMENT 'maksimuma aĝo komence de la renkontiĝo en jaroj',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`,`sistemoID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='aĝkategorioj' AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_agxkategorisistemoj`
-- 

CREATE TABLE `is_agxkategorisistemoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(20) collate utf8_esperanto_ci NOT NULL,
  `entajpanto` int(11) NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='sistemoj de aĝkategorioj' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_aligxkategorioj`
-- 

CREATE TABLE `is_aligxkategorioj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(20) collate utf8_esperanto_ci NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `sistemoID` int(11) NOT NULL,
  `limdato` int(11) NOT NULL,
  `nomo_lokalingve` varchar(20) character set utf8 NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`,`sistemoID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='aliĝkategorioj' AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_aligxkategorisistemoj`
-- 

CREATE TABLE `is_aligxkategorisistemoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(20) collate utf8_esperanto_ci NOT NULL,
  `entajpanto` int(11) NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='sistemoj de alĝikategorioj' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_cxambroj`
-- 

CREATE TABLE `is_cxambroj` (
  `ID` int(10) NOT NULL auto_increment,
  `renkontigxo` int(5) NOT NULL default '4',
  `nomo` varchar(10) character set utf8 collate utf8_esperanto_ci NOT NULL,
  `etagxo` varchar(50) character set utf8 collate utf8_esperanto_ci NOT NULL,
  `litonombro` int(2) NOT NULL default '0',
  `tipo` char(1) character set ascii NOT NULL COMMENT 'I = ina, g = gea, v = vira',
  `dulita` char(1) character set ascii NOT NULL default 'N' COMMENT 'J = dulita, U = unulita, N = vera kvanto de litoj uzebla',
  `rimarkoj` varchar(100) character set utf8 collate utf8_esperanto_ci NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `renkontigxo` (`renkontigxo`,`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=321 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_entajpantoj`
-- 

CREATE TABLE `is_entajpantoj` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `nomo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `kodvorto` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `sendanto_nomo` varchar(30) character set utf8 NOT NULL,
  `retposxtadreso` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `partoprenanto_id` int(11) default NULL,
  `aligi` char(1) character set ascii NOT NULL default 'N',
  `vidi` char(1) character set ascii NOT NULL default 'N',
  `sxangxi` char(1) character set ascii NOT NULL default 'N',
  `cxambrumi` char(1) character set ascii NOT NULL default 'N',
  `ekzporti` char(1) character set ascii NOT NULL default 'N',
  `statistikumi` char(1) character set ascii NOT NULL default 'N',
  `mono` char(1) character set ascii NOT NULL default 'N',
  `estingi` char(1) character set ascii NOT NULL default 'N',
  `retumi` char(1) character set ascii NOT NULL default 'N',
  `rabati` char(1) character set ascii NOT NULL default 'N',
  `inviti` char(1) character set ascii NOT NULL default 'N',
  `administri` char(1) character set ascii NOT NULL default 'N',
  `akcepti` char(1) character set ascii NOT NULL default 'N',
  `teknikumi` char(1) character set ascii NOT NULL default 'N',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='Uzantoj de la datumbazo, kun pasvortoj kaj rajtoj.' AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_fikskostoj`
-- 

CREATE TABLE `is_fikskostoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `kostosistemo` int(11) NOT NULL,
  `kosto` decimal(7,2) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`,`kostosistemo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='fikskostoj de iu renkontigxo' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_invitpetoj`
-- 

CREATE TABLE `is_invitpetoj` (
  `ID` int(11) NOT NULL COMMENT 'samtempe la identifikilo de la partopreno',
  `pasportnumero` varchar(50) character set utf8 NOT NULL COMMENT 'la numero de la pasporto',
  `pasporta_persona_nomo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `pasporta_familia_nomo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `pasporta_adreso` text collate utf8_esperanto_ci NOT NULL,
  `senda_adreso` text collate utf8_esperanto_ci NOT NULL,
  `senda_faksnumero` varchar(30) collate utf8_esperanto_ci default NULL,
  `invitletero_sendenda` char(1) character set ascii collate ascii_bin NOT NULL default '?',
  `invitletero_sendodato` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='Petoj pri invitleteroj';

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_kategorioj_de_landoj`
-- 

CREATE TABLE `is_kategorioj_de_landoj` (
  `kategorioID` int(11) NOT NULL,
  `sistemoID` int(11) NOT NULL COMMENT 'landokategorisistemo',
  `landoID` int(11) NOT NULL,
  PRIMARY KEY  (`sistemoID`,`landoID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='liganta tabelo por landoj kaj iliaj kategorioj en la unuopaj';

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_kostosistemoj`
-- 

CREATE TABLE `is_kostosistemoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(20) collate utf8_esperanto_ci NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `entajpanto` int(11) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='diversaj kotizosistemoj' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_kotizosistemoj`
-- 

CREATE TABLE `is_kotizosistemoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(20) collate utf8_esperanto_ci NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `entajpanto` int(11) NOT NULL,
  `aligxkategorisistemo` int(11) NOT NULL,
  `landokategorisistemo` int(11) NOT NULL,
  `agxkategorisistemo` int(11) NOT NULL,
  `logxkategorisistemo` int(11) NOT NULL,
  `parttempdivisoro` double NOT NULL,
  `malaligxkondicxsistemo` int(11) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='diversaj kotizosistemoj' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_kotizotabeleroj`
-- 

CREATE TABLE `is_kotizotabeleroj` (
  `kotizosistemo` int(11) NOT NULL,
  `aligxkategorio` int(11) NOT NULL,
  `landokategorio` int(11) NOT NULL,
  `agxkategorio` int(11) NOT NULL,
  `logxkategorio` int(11) NOT NULL,
  `kotizo` decimal(6,2) NOT NULL,
  PRIMARY KEY  (`kotizosistemo`,`aligxkategorio`,`landokategorio`,`agxkategorio`,`logxkategorio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='jen la multaj eroj de la kotizo-tabelo';

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_krompagoj`
-- 

CREATE TABLE `is_krompagoj` (
  `tipo` int(11) NOT NULL,
  `kotizosistemo` int(11) NOT NULL,
  `krompago` decimal(6,2) NOT NULL,
  PRIMARY KEY  (`tipo`,`kotizosistemo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_krompagotipoj`
-- 

CREATE TABLE `is_krompagotipoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(30) collate utf8_esperanto_ci NOT NULL,
  `nomo_lokalingve` varchar(30) character set utf8 NOT NULL,
  `mallongigo` varchar(10) collate utf8_esperanto_ci NOT NULL COMMENT 'mallongigo por la finkalkulada tabelo',
  `entajpanto` int(11) NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `kondicxo` varchar(100) character set ascii NOT NULL COMMENT 'nomo de kondicxo-funkcio vokenda',
  `uzebla` char(1) collate utf8_esperanto_ci NOT NULL default 'j',
  `lauxnokte` char(1) character set ascii NOT NULL default 'n' COMMENT 'c^u lau^nokta krompago, c^u lau^taga?',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='tipoj de eblaj krompagoj' AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_kunlogxdeziroj`
-- 

CREATE TABLE `is_kunlogxdeziroj` (
  `ID` int(11) NOT NULL auto_increment,
  `partoprenoID` int(11) NOT NULL default '0',
  `kunKiuID` int(11) NOT NULL default '0',
  `stato` char(1) NOT NULL default '',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `partoprenoID` (`partoprenoID`,`kunKiuID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='deziroj de kunlogxado kaj ties statoj' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_landoj`
-- 

CREATE TABLE `is_landoj` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `nomo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `lokanomo` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL,
  `kodo` char(2) character set ascii NOT NULL COMMENT 'kodo laŭ ISO-3166-1',
  `kategorio` char(1) character set ascii collate ascii_bin default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=84 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_landoj_malnova`
-- 

CREATE TABLE `is_landoj_malnova` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `nomo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `lokanomo` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL,
  `kodo` char(2) character set ascii NOT NULL COMMENT 'kodo laŭ ISO-3166-1',
  `kategorio` char(1) character set ascii collate ascii_bin default NULL COMMENT 'kategorio lau^ la malnova kotizosistemo. Ne plu estos uzata en la nova sistemo.',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=84 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_landokategorioj`
-- 

CREATE TABLE `is_landokategorioj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(20) collate utf8_esperanto_ci NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `sistemoID` int(11) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`,`sistemoID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='landokategorioj' AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_landokategorisistemoj`
-- 

CREATE TABLE `is_landokategorisistemoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(20) collate utf8_esperanto_ci NOT NULL,
  `entajpanto` int(11) NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='sistemoj de landokategorioj' AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_litonoktoj`
-- 

CREATE TABLE `is_litonoktoj` (
  `ID` int(11) NOT NULL auto_increment,
  `cxambro` int(10) NOT NULL default '0',
  `litonumero` int(2) NOT NULL default '0',
  `nokto_de` int(2) NOT NULL default '0',
  `nokto_gxis` int(2) NOT NULL default '0',
  `partopreno` int(10) NOT NULL default '0',
  `rezervtipo` char(1) NOT NULL default '',
  PRIMARY KEY  (`ID`),
  KEY `cxambro` (`cxambro`),
  KEY `partopreno` (`partopreno`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1840 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_logxkategorioj`
-- 

CREATE TABLE `is_logxkategorioj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(20) collate utf8_esperanto_ci NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `sistemoID` int(11) NOT NULL,
  `sxlosillitero` char(1) character set ascii NOT NULL COMMENT 'litero uzata en partoprenanto->domotipo',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`,`sistemoID`),
  UNIQUE KEY `sxlosillitero` (`sistemoID`,`sxlosillitero`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='loĝkategorioj' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_logxkategorisistemoj`
-- 

CREATE TABLE `is_logxkategorisistemoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(20) collate utf8_esperanto_ci NOT NULL,
  `entajpanto` int(11) NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='sistemoj de loĝkategorioj' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_malaligxkondicxoj`
-- 

CREATE TABLE `is_malaligxkondicxoj` (
  `sistemo` int(11) NOT NULL,
  `aligxkategorio` int(11) NOT NULL,
  `kondicxtipo` int(11) NOT NULL,
  PRIMARY KEY  (`sistemo`,`aligxkategorio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_malaligxkondicxotipoj`
-- 

CREATE TABLE `is_malaligxkondicxotipoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `mallongigo` varchar(10) collate utf8_esperanto_ci NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `funkcio` varchar(50) character set ascii NOT NULL,
  `parametro` decimal(6,2) default NULL,
  `uzebla` char(1) character set ascii NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='Trakteblecoj por malaliĝintoj' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_malaligxkondicxsistemoj`
-- 

CREATE TABLE `is_malaligxkondicxsistemoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `aligxkategorisistemo` int(11) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='sistemo de malaliĝkondiĉoj' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_minimumaj_antauxpagoj`
-- 

CREATE TABLE `is_minimumaj_antauxpagoj` (
  `kotizosistemo` int(11) NOT NULL,
  `landokategorio` int(11) NOT NULL,
  `oficiala_antauxpago` decimal(6,2) NOT NULL COMMENT 'Kion ni montras al la publiko',
  `interna_antauxpago` decimal(6,2) NOT NULL COMMENT 'kion ni uzas por la kalkuloj',
  PRIMARY KEY  (`kotizosistemo`,`landokategorio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='La minimumaj antau^pagoj por c^iu landokategorio en iu kotiz';

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_monujo`
-- 

CREATE TABLE `is_monujo` (
  `ID` int(10) NOT NULL auto_increment,
  `renkontigxo` int(5) NOT NULL default '0',
  `kvanto` int(10) NOT NULL default '0',
  `kauzo` varchar(200) character set utf8 collate utf8_esperanto_ci NOT NULL,
  `tempo` datetime NOT NULL default '0000-00-00 00:00:00',
  `kvitanconumero` int(10) NOT NULL default '0',
  `alKiu` varchar(20) character set utf8 collate utf8_esperanto_ci NOT NULL,
  `kiaMonujo` varchar(10) character set utf8 collate utf8_esperanto_ci NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1286 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_nomsxildoj`
-- 

CREATE TABLE `is_nomsxildoj` (
  `ID` int(11) NOT NULL auto_increment,
  `titolo_lokalingve` varchar(15) character set utf8 collate utf8_esperanto_ci NOT NULL,
  `titolo_esperante` varchar(15) character set utf8 collate utf8_esperanto_ci NOT NULL,
  `nomo` varchar(30) character set utf8 collate utf8_esperanto_ci NOT NULL,
  `funkcio_lokalingve` varchar(40) character set utf8 collate utf8_esperanto_ci NOT NULL,
  `funkcio_esperante` varchar(40) character set utf8 collate utf8_esperanto_ci NOT NULL,
  `renkontigxoID` int(11) NOT NULL default '0',
  `havasNomsxildon` char(1) character set ascii NOT NULL default 'N',
  PRIMARY KEY  (`ID`),
  KEY `renkontigxoID` (`renkontigxoID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='por specialaj nomsxildoj (por nepartopenantoj)' AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_notoj`
-- 

CREATE TABLE `is_notoj` (
  `ID` int(11) NOT NULL auto_increment,
  `partoprenantoID` int(11) NOT NULL default '0',
  `kiu` varchar(100) collate utf8_esperanto_ci NOT NULL,
  `kunKiu` varchar(100) collate utf8_esperanto_ci NOT NULL,
  `tipo` varchar(100) collate utf8_esperanto_ci NOT NULL,
  `dato` datetime NOT NULL default '0000-00-00 00:00:00',
  `subjekto` varchar(200) collate utf8_esperanto_ci NOT NULL,
  `enhavo` text collate utf8_esperanto_ci NOT NULL,
  `prilaborata` char(1) character set ascii NOT NULL,
  `revidu` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci AUTO_INCREMENT=1150 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_pagoj`
-- 

CREATE TABLE `is_pagoj` (
  `ID` int(10) NOT NULL auto_increment,
  `partoprenoID` int(10) NOT NULL default '0',
  `kvanto` decimal(6,2) NOT NULL default '0.00',
  `dato` date NOT NULL default '0000-00-00',
  `tipo` varchar(100) character set utf8 collate utf8_esperanto_ci NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4273 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_partoprenantoj`
-- 

CREATE TABLE `is_partoprenantoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(50) character set utf8 default NULL,
  `personanomo` varchar(50) character set utf8 default NULL,
  `sxildnomo` varchar(50) character set utf8 default NULL,
  `sekso` char(1) character set ascii NOT NULL,
  `naskigxdato` date NOT NULL default '0000-00-00',
  `adresaldonajxo` varchar(50) character set utf8 default NULL,
  `strato` varchar(50) character set utf8 default NULL,
  `posxtkodo` varchar(50) character set utf8 default NULL,
  `urbo` varchar(50) character set utf8 default NULL,
  `provinco` varchar(50) character set utf8 default NULL,
  `lando` int(11) NOT NULL default '1',
  `sxildlando` varchar(50) character set utf8 default NULL,
  `okupigxo` int(2) NOT NULL default '0',
  `okupigxteksto` varchar(100) character set ascii NOT NULL,
  `telefono` varchar(50) character set ascii NOT NULL,
  `telefakso` varchar(50) character set ascii NOT NULL,
  `retposxto` varchar(80) character set ascii NOT NULL COMMENT 'la retadreso (aux pliaj, disigitaj per komo)',
  `retposxta_varbado` char(1) character set ascii NOT NULL default 'j',
  `ueakodo` varchar(6) character set ascii NOT NULL,
  `rimarkoj` varchar(100) character set utf8 default NULL,
  `kodvorto` varchar(10) character set ascii NOT NULL,
  `malnova` char(1) character set ascii NOT NULL default 'N',
  PRIMARY KEY  (`ID`),
  KEY `nomo` (`nomo`),
  KEY `personanomo` (`personanomo`),
  KEY `naskigxdato` (`naskigxdato`),
  KEY `retposxto` (`retposxto`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci PACK_KEYS=0 COMMENT='la partoprenantoj' AUTO_INCREMENT=2947 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_partoprenoj`
-- 

CREATE TABLE `is_partoprenoj` (
  `ID` int(11) NOT NULL auto_increment,
  `renkontigxoID` int(11) NOT NULL default '0',
  `partoprenantoID` int(11) NOT NULL default '0',
  `agxo` int(11) NOT NULL default '0',
  `komencanto` char(1) character set ascii NOT NULL default 'N',
  `nivelo` char(1) character set ascii NOT NULL default '?',
  `rimarkoj` text collate utf8_esperanto_ci,
  `invitletero` char(1) character set ascii NOT NULL default 'N',
  `invitilosendata` date NOT NULL default '0000-00-00',
  `pasportnumero` varchar(100) collate utf8_esperanto_ci default NULL,
  `retakonfirmilo` char(1) character set ascii NOT NULL,
  `germanakonfirmilo` char(1) character set ascii NOT NULL default 'N',
  `1akonfirmilosendata` date NOT NULL default '0000-00-00',
  `2akonfirmilosendata` date NOT NULL default '0000-00-00',
  `partoprentipo` char(1) character set ascii NOT NULL default 't',
  `de` date NOT NULL default '0000-00-00',
  `gxis` date NOT NULL default '0000-00-00',
  `vegetare` char(1) character set ascii NOT NULL default 'N',
  `GEJmembro` char(1) character set ascii NOT NULL default 'N',
  `tejo_membro_laudire` char(1) character set ascii NOT NULL default 'n',
  `tejo_membro_kontrolita` char(1) character set ascii NOT NULL default '?',
  `tejo_membro_kotizo` decimal(6,2) NOT NULL default '0.00',
  `surloka_membrokotizo` char(1) character set ascii NOT NULL default '?',
  `membrokotizo` decimal(6,2) NOT NULL default '0.00',
  `KKRen` char(1) character set ascii NOT NULL default 'N',
  `domotipo` char(1) character set ascii NOT NULL default 'M',
  `litolajxo` char(1) character set ascii NOT NULL,
  `kunmangxas` char(1) character set ascii NOT NULL default 'N',
  `listo` char(1) character set ascii NOT NULL default 'N',
  `intolisto` char(1) character set ascii NOT NULL default 'N' COMMENT 'Ĉu aperi en la post-renkontiĝa partopreninto-listo? (J/N)',
  `pagmaniero` varchar(10) collate utf8_esperanto_ci default NULL,
  `kunkiu` varchar(50) collate utf8_esperanto_ci default NULL,
  `kunkiuID` int(10) NOT NULL default '0',
  `cxambrotipo` char(1) character set ascii NOT NULL default 'g',
  `dulita` char(1) character set ascii NOT NULL default 'N',
  `ekskursbileto` char(1) character set ascii NOT NULL default 'N',
  `tema` text collate utf8_esperanto_ci NOT NULL,
  `distra` text collate utf8_esperanto_ci NOT NULL,
  `vespera` text collate utf8_esperanto_ci NOT NULL,
  `muzika` text collate utf8_esperanto_ci NOT NULL,
  `nokta` text collate utf8_esperanto_ci NOT NULL,
  `donaco` double(10,2) NOT NULL default '0.00',
  `aligxdato` date NOT NULL default '0000-00-00',
  `malaligxdato` date NOT NULL default '0000-00-00',
  `alvenstato` char(1) character set ascii NOT NULL default 'v',
  `traktstato` char(1) character set ascii NOT NULL default 'N',
  `asekuri` char(1) character set ascii NOT NULL default 'N',
  `havas_asekuron` char(1) character set ascii NOT NULL default 'J',
  `rabato` double(11,2) NOT NULL default '0.00',
  `kialo` varchar(50) character set ascii NOT NULL,
  `surlokpago` double(10,2) NOT NULL default '0.00',
  `aligxkategoridato` date NOT NULL default '0000-00-00',
  `forgesu` char(1) character set ascii NOT NULL default 'N',
  `kontrolata` char(1) character set ascii NOT NULL default 'N',
  `havasMangxkuponon` char(1) character set ascii NOT NULL default 'N',
  `havasNomsxildon` char(1) character set armscii8 NOT NULL default 'N',
  PRIMARY KEY  (`ID`),
  KEY `partoprenantoID` (`partoprenantoID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci PACK_KEYS=0 COMMENT='Individuaj partoprenoj de partoprenantoj' AUTO_INCREMENT=3248 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_personkostoj`
-- 

CREATE TABLE `is_personkostoj` (
  `tipo` int(11) NOT NULL,
  `kostosistemo` int(11) NOT NULL,
  `maks_haveblaj` int(11) NOT NULL,
  `min_uzendaj` int(11) NOT NULL,
  `kosto_uzata` decimal(6,2) NOT NULL,
  `kosto_neuzata` decimal(6,2) NOT NULL,
  PRIMARY KEY  (`tipo`,`kostosistemo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_personkostotipoj`
-- 

CREATE TABLE `is_personkostotipoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(30) collate utf8_esperanto_ci NOT NULL,
  `entajpanto` int(11) NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `kondicxo` varchar(50) character set ascii NOT NULL,
  `uzebla` char(1) collate utf8_esperanto_ci NOT NULL default 'j',
  `lauxnokte` char(1) character set ascii NOT NULL default 'n' COMMENT 'c^u lau^nokta kosto, c^u unufoja?',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='tipoj de eblaj kostoj laux persono' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_protokolo`
-- 

CREATE TABLE `is_protokolo` (
  `ID` int(11) NOT NULL auto_increment,
  `deveno` varchar(200) NOT NULL default '',
  `ilo` varchar(200) NOT NULL default '',
  `entajpanto` varchar(20) NOT NULL default '0',
  `tempo` datetime NOT NULL default '0000-00-00 00:00:00',
  `ago` varchar(20) character set utf8 collate utf8_esperanto_ci NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8109 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_rabatoj`
-- 

CREATE TABLE `is_rabatoj` (
  `ID` int(10) NOT NULL auto_increment,
  `partoprenoID` int(10) NOT NULL default '0',
  `kvanto` decimal(6,2) NOT NULL default '0.00',
  `kauzo` varchar(30) character set utf8 collate utf8_esperanto_ci NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=574 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_renkontigxo`
-- 

CREATE TABLE `is_renkontigxo` (
  `ID` int(4) NOT NULL auto_increment,
  `nomo` varchar(100) collate utf8_esperanto_ci NOT NULL,
  `mallongigo` varchar(10) collate utf8_esperanto_ci NOT NULL,
  `temo` varchar(100) collate utf8_esperanto_ci NOT NULL,
  `loko` varchar(100) collate utf8_esperanto_ci NOT NULL,
  `de` date NOT NULL default '0000-00-00',
  `gxis` date NOT NULL default '0000-00-00',
  `kotizosistemo` int(1) NOT NULL default '0',
  `plej_frue` date NOT NULL default '0000-00-00',
  `meze` date NOT NULL default '0000-00-00',
  `malfrue` date NOT NULL default '0000-00-00' COMMENT 'limdato de la lasta (ne-surloka) aligxkategorio.',
  `parttemppartoprendivido` int(4) NOT NULL default '6',
  `juna` int(3) NOT NULL default '20',
  `maljuna` int(3) NOT NULL default '26',
  `adminrespondeculo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `adminretadreso` varchar(100) character set ascii NOT NULL,
  `sekurkopiojretadreso` varchar(100) character set ascii NOT NULL,
  `invitleterorespondeculo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `invitleteroretadreso` varchar(100) character set ascii NOT NULL,
  `temarespondulo` varchar(50) character set ascii NOT NULL,
  `temaretadreso` varchar(100) character set ascii NOT NULL,
  `distrarespondulo` varchar(50) character set ascii NOT NULL,
  `distraretadreso` varchar(100) character set ascii NOT NULL,
  `vesperarespondulo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `vesperaretadreso` varchar(100) character set ascii NOT NULL,
  `muzikarespondulo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `muzikaretadreso` varchar(100) character set ascii NOT NULL,
  `noktarespondulo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `noktaretadreso` varchar(100) character set ascii NOT NULL,
  `novularespondulo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `novularetadreso` varchar(100) character set ascii NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `mallongigo` (`mallongigo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_retposxto`
-- 

CREATE TABLE `is_retposxto` (
  `ID` int(10) NOT NULL auto_increment,
  `nomo` varchar(50) collate utf8_esperanto_ci NOT NULL,
  `subjekto` varchar(100) collate utf8_esperanto_ci NOT NULL,
  `korpo` text collate utf8_esperanto_ci NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='sxablonoj por retposxtoj al partoprenantoj' AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_sercxoj`
-- 

CREATE TABLE `is_sercxoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(30) collate utf8_esperanto_ci NOT NULL,
  `priskribo` varchar(200) collate utf8_esperanto_ci NOT NULL,
  `entajpanto` int(11) NOT NULL default '0',
  `sercxo` blob NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`),
  KEY `entajpanto` (`entajpanto`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='La dauxrigitaj sercxoj' AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_tekstoj`
-- 

CREATE TABLE `is_tekstoj` (
  `ID` int(10) NOT NULL auto_increment,
  `renkontigxoID` int(10) NOT NULL default '0',
  `mesagxoID` varchar(30) character set ascii NOT NULL,
  `teksto` text collate utf8_esperanto_ci,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `renkontigxoID` (`renkontigxoID`,`mesagxoID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='tabelo por lokaligo de tekstoj (-> tekstoj.php)' AUTO_INCREMENT=185 ;
