-- phpMyAdmin SQL Dump
-- version 2.6.2-Debian-3sarge3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 08. März 2007 um 12:55
-- Server Version: 5.0.32
-- PHP-Version: 4.4.4-8
-- 
-- Datenbank: `pagxaro`
-- 

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_cxambroj`
-- 

CREATE TABLE `is_cxambroj` (
  `ID` int(10) NOT NULL auto_increment,
  `renkontigxo` int(5) NOT NULL default '4',
  `nomo` varchar(10) character set utf8 collate utf8_esperanto_ci default NULL,
  `etagxo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `litonombro` int(2) NOT NULL default '0',
  `tipo` char(1) NOT NULL default '',
  `dulita` char(1) NOT NULL default 'N',
  `rimarkoj` varchar(100) character set utf8 collate utf8_esperanto_ci default NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `renkontigxo` (`renkontigxo`,`nomo`)
) ENGINE=MyISAM AUTO_INCREMENT=274 DEFAULT CHARSET=latin1 AUTO_INCREMENT=274 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_entajpantoj`
-- 

CREATE TABLE `is_entajpantoj` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `nomo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `kodvorto` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `retposxtadreso` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `partoprenanto_id` int(11) default NULL,
  `aligi` char(1) NOT NULL default 'N',
  `vidi` char(1) NOT NULL default 'N',
  `sxangxi` char(1) NOT NULL default 'N',
  `cxambrumi` char(1) NOT NULL default 'N',
  `ekzporti` char(1) NOT NULL default 'N',
  `statistikumi` char(1) NOT NULL default 'N',
  `mono` char(1) NOT NULL default 'N',
  `estingi` char(1) NOT NULL default 'N',
  `retumi` char(1) NOT NULL default 'N',
  `rabati` char(1) NOT NULL default 'N',
  `administri` char(1) NOT NULL default 'N',
  `akcepti` char(1) NOT NULL default 'N',
  `teknikumi` char(1) NOT NULL default 'N',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

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
  `lokanomo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `kategorio` char(1) character set ascii collate ascii_bin default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=latin1 AUTO_INCREMENT=79 ;

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
) ENGINE=MyISAM AUTO_INCREMENT=1602 DEFAULT CHARSET=latin1 AUTO_INCREMENT=1602 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_monujo`
-- 

CREATE TABLE `is_monujo` (
  `ID` int(10) NOT NULL auto_increment,
  `renkontigxo` int(5) NOT NULL default '0',
  `kvanto` int(10) NOT NULL default '0',
  `kauzo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `tempo` datetime NOT NULL default '0000-00-00 00:00:00',
  `kvitanconumero` int(10) NOT NULL default '0',
  `alKiu` varchar(20) character set utf8 collate utf8_esperanto_ci default NULL,
  `kiaMonujo` varchar(10) character set utf8 collate utf8_esperanto_ci default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=1286 DEFAULT CHARSET=latin1 AUTO_INCREMENT=1286 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_nomsxildoj`
-- 

CREATE TABLE `is_nomsxildoj` (
  `ID` int(11) NOT NULL auto_increment,
  `titolo_lokalingve` varchar(15) character set utf8 collate utf8_esperanto_ci default NULL,
  `titolo_esperante` varchar(15) character set utf8 collate utf8_esperanto_ci default NULL,
  `nomo` varchar(30) character set utf8 collate utf8_esperanto_ci default NULL,
  `funkcio_lokalingve` varchar(30) character set utf8 collate utf8_esperanto_ci default NULL,
  `funkcio_esperante` varchar(30) character set utf8 collate utf8_esperanto_ci default NULL,
  `renkontigxoID` int(11) NOT NULL default '0',
  `havasNomsxildon` char(1) character set utf8 collate utf8_esperanto_ci default NULL,
  PRIMARY KEY  (`ID`),
  KEY `renkontigxoID` (`renkontigxoID`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COMMENT='por specialaj nomsxildoj (por nepartopenantoj)' AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_notoj`
-- 

CREATE TABLE `is_notoj` (
  `ID` int(11) NOT NULL auto_increment,
  `partoprenantoID` int(11) NOT NULL default '0',
  `kiu` varchar(100) character set utf8 collate utf8_esperanto_ci default NULL,
  `kunKiu` varchar(100) character set utf8 collate utf8_esperanto_ci default NULL,
  `tipo` varchar(100) character set utf8 collate utf8_esperanto_ci default NULL,
  `dato` datetime NOT NULL default '0000-00-00 00:00:00',
  `subjekto` varchar(200) character set utf8 collate utf8_esperanto_ci default NULL,
  `enhavo` text character set utf8 collate utf8_esperanto_ci,
  `prilaborata` char(1) NOT NULL default '',
  `revidu` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=805 DEFAULT CHARSET=latin1 AUTO_INCREMENT=805 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_pagoj`
-- 

CREATE TABLE `is_pagoj` (
  `ID` int(10) NOT NULL auto_increment,
  `partoprenoID` int(10) NOT NULL default '0',
  `kvanto` decimal(6,2) NOT NULL default '0.00',
  `dato` date NOT NULL default '0000-00-00',
  `tipo` varchar(100) character set utf8 collate utf8_esperanto_ci default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=3900 DEFAULT CHARSET=latin1 AUTO_INCREMENT=3900 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_partoprenantoj`
-- 

CREATE TABLE `is_partoprenantoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(50) character set utf8 default NULL,
  `personanomo` varchar(50) character set utf8 default NULL,
  `sxildnomo` varchar(50) character set utf8 default NULL,
  `sekso` char(1) NOT NULL default '',
  `naskigxdato` date NOT NULL default '0000-00-00',
  `adresaldonajxo` varchar(50) character set utf8 default NULL,
  `strato` varchar(50) character set utf8 default NULL,
  `posxtkodo` varchar(50) character set utf8 default NULL,
  `urbo` varchar(50) character set utf8 default NULL,
  `provinco` varchar(50) character set utf8 default NULL,
  `lando` int(11) NOT NULL default '1',
  `sxildlando` varchar(50) character set utf8 default NULL,
  `okupigxo` int(2) NOT NULL default '0',
  `okupigxteksto` varchar(100) NOT NULL default '',
  `telefono` varchar(50) NOT NULL default '',
  `telefakso` varchar(50) NOT NULL default '',
  `retposxto` varchar(50) NOT NULL default '',
  `retposxta_varbado` char(1) NOT NULL default 'j',
  `ueakodo` varchar(6) NOT NULL default '',
  `rimarkoj` text character set utf8 default NULL,
  `kodvorto` varchar(10) NOT NULL default '',
  `malnova` char(1) NOT NULL default 'N',
  PRIMARY KEY  (`ID`),
  KEY `nomo` (`nomo`),
  KEY `personanomo` (`personanomo`),
  KEY `naskigxdato` (`naskigxdato`),
  KEY `retposxto` (`retposxto`)
) ENGINE=MyISAM AUTO_INCREMENT=2699 DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=2699 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_partoprenantoj_kopio`
-- 

CREATE TABLE `is_partoprenantoj_kopio` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(50) NOT NULL default '',
  `personanomo` varchar(50) NOT NULL default '',
  `sxildnomo` varchar(50) NOT NULL default '',
  `sekso` char(1) NOT NULL default '',
  `naskigxdato` date NOT NULL default '0000-00-00',
  `adresaldonajxo` varchar(50) NOT NULL default '',
  `strato` varchar(50) NOT NULL default '',
  `posxtkodo` varchar(50) NOT NULL default '',
  `urbo` varchar(50) NOT NULL default '',
  `provinco` varchar(50) NOT NULL default '',
  `lando` int(11) NOT NULL default '1',
  `sxildlando` varchar(50) NOT NULL default '',
  `okupigxo` int(2) NOT NULL default '0',
  `okupigxteksto` varchar(100) NOT NULL default '',
  `telefono` varchar(50) NOT NULL default '',
  `telefakso` varchar(50) NOT NULL default '',
  `retposxto` varchar(50) NOT NULL default '',
  `retposxta_varbado` char(1) NOT NULL default 'j',
  `ueakodo` varchar(6) NOT NULL default '',
  `rimarkoj` varchar(100) NOT NULL default '',
  `kodvorto` varchar(10) NOT NULL default '',
  `malnova` char(1) NOT NULL default 'N',
  PRIMARY KEY  (`ID`),
  KEY `nomo` (`nomo`),
  KEY `personanomo` (`personanomo`),
  KEY `naskigxdato` (`naskigxdato`),
  KEY `retposxto` (`retposxto`)
) ENGINE=MyISAM AUTO_INCREMENT=2694 DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=2694 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_partoprenoj`
-- 

CREATE TABLE `is_partoprenoj` (
  `ID` int(11) NOT NULL auto_increment,
  `renkontigxoID` int(11) NOT NULL default '0',
  `partoprenantoID` int(11) NOT NULL default '0',
  `agxo` int(11) NOT NULL default '0',
  `komencanto` char(1) NOT NULL default 'N',
  `nivelo` char(1) NOT NULL default '?',
  `rimarkoj` text NOT NULL,
  `invitletero` char(1) NOT NULL default 'N',
  `invitilosendata` date NOT NULL default '0000-00-00',
  `pasportnumero` varchar(100) character set utf8 collate utf8_esperanto_ci default NULL,
  `retakonfirmilo` char(1) NOT NULL default '',
  `germanakonfirmilo` char(1) NOT NULL default 'N',
  `1akonfirmilosendata` date NOT NULL default '0000-00-00',
  `2akonfirmilosendata` date NOT NULL default '0000-00-00',
  `partoprentipo` char(1) NOT NULL default 't',
  `de` date NOT NULL default '0000-00-00',
  `gxis` date NOT NULL default '0000-00-00',
  `vegetare` char(1) NOT NULL default 'N',
  `GEJmembro` char(1) NOT NULL default 'N',
  `tejo_membro_laudire` char(1) NOT NULL default 'n',
  `tejo_membro_kontrolita` char(1) NOT NULL default '?',
  `tejo_membro_kotizo` DECIMAL( 6, 2 ) DEFAULT '0.00' NOT NULL,
  `surloka_membrokotizo` char(1) NOT NULL default 'n',
  `membrokotizo` decimal(6,2) NOT NULL default '0.00',
  `KKRen` char(1) NOT NULL default 'N',
  `domotipo` char(1) NOT NULL default 'M',
  `litolajxo` char(1) NOT NULL default '',
  `kunmangxas` char(1) NOT NULL default 'N',
  `listo` char(1) NOT NULL default 'N',
  `pagmaniero` varchar(10) character set utf8 collate utf8_esperanto_ci default NULL,
  `kunkiu` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `kunkiuID` int(10) NOT NULL default '0',
  `cxambrotipo` char(1) NOT NULL default 'g',
  `cxambro` varchar(6) NOT NULL default '',
  `dulita` char(1) NOT NULL default 'N',
  `ekskursbileto` char(1) NOT NULL default 'N',
  `tema` text character set utf8 collate utf8_esperanto_ci default NULL,
  `distra` text character set utf8 collate utf8_esperanto_ci default NULL,
  `vespera` text character set utf8 collate utf8_esperanto_ci default NULL,
  `muzika` text character set utf8 collate utf8_esperanto_ci default NULL,
  `nokta` text character set utf8 collate utf8_esperanto_ci default NULL,
  `donaco` double(10,2) NOT NULL default '0.00',
  `aligxdato` date NOT NULL default '0000-00-00',
  `malaligxdato` date NOT NULL default '0000-00-00',
  `alvenstato` char(1) NOT NULL default 'v',
  `traktstato` char(1) NOT NULL default 'N',
  `asekuri` char(1) NOT NULL default 'N',
  `havas_asekuron` char(1) NOT NULL default 'J',
  `rabato` double(11,2) NOT NULL default '0.00',
  `kialo` varchar(50) NOT NULL default '',
  `surlokpago` double(10,2) NOT NULL default '0.00',
  `aligxkategoridato` date NOT NULL default '0000-00-00',
  `forgesu` char(1) NOT NULL default 'N',
  `venos` char(1) NOT NULL default 'j',
  `alvenis` char(1) NOT NULL default 'N',
  `kontrolata` char(1) NOT NULL default 'N',
  `havasMangxkuponon` char(1) NOT NULL default 'N',
  `havasNomsxildon` char(1) NOT NULL default 'N',
  PRIMARY KEY  (`ID`),
  KEY `partoprenantoID` (`partoprenantoID`)
) ENGINE=MyISAM AUTO_INCREMENT=2965 DEFAULT CHARSET=latin1 PACK_KEYS=0 AUTO_INCREMENT=2965 ;

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
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=6274 DEFAULT CHARSET=latin1 AUTO_INCREMENT=6274 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_rabatoj`
-- 

CREATE TABLE `is_rabatoj` (
  `ID` int(10) NOT NULL auto_increment,
  `partoprenoID` int(10) NOT NULL default '0',
  `kvanto` decimal(6,2) NOT NULL default '0.00',
  `kauzo` varchar(30) character set utf8 collate utf8_esperanto_ci default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=461 DEFAULT CHARSET=latin1 AUTO_INCREMENT=461 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_renkontigxo`
-- 

CREATE TABLE `is_renkontigxo` (
  `ID` int(4) NOT NULL auto_increment,
  `nomo` varchar(100) character set utf8 collate utf8_esperanto_ci default NULL,
  `mallongigo` varchar(10) character set utf8 collate utf8_esperanto_ci default NULL,
  `temo` varchar(100) character set utf8 collate utf8_esperanto_ci default NULL,
  `loko` varchar(100) character set utf8 collate utf8_esperanto_ci default NULL,
  `de` date NOT NULL default '0000-00-00',
  `gxis` date NOT NULL default '0000-00-00',
  `plej_frue` date NOT NULL default '0000-00-00',
  `meze` date NOT NULL default '0000-00-00',
  `parttemppartoprendivido` int(4) NOT NULL default '6',
  `juna` int(3) NOT NULL default '20',
  `maljuna` int(3) NOT NULL default '26',
  `adminrespondeculo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `adminretadreso` varchar(100) character set ascii default NULL,
  `sekurkopiojretadreso` varchar(100) character set ascii default NULL,
  `invitleterorespondeculo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `invitleteroretadreso` varchar(100) character set ascii default NULL,
  `temarespondulo` varchar(50) character set ascii default NULL,
  `temaretadreso` varchar(100) character set ascii default NULL,
  `distrarespondulo` varchar(50) character set ascii default NULL,
  `distraretadreso` varchar(100) character set ascii default NULL,
  `vesperarespondulo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `vesperaretadreso` varchar(100) character set ascii default NULL,
  `muzikarespondulo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `muzikaretadreso` varchar(100) character set ascii default NULL,
  `noktarespondulo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `noktaretadreso` varchar(100) character set ascii default NULL,
  `novularespondulo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `novularetadreso` varchar(100) character set ascii default NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `mallongigo` (`mallongigo`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_retposxto`
-- 

CREATE TABLE `is_retposxto` (
  `ID` int(10) NOT NULL auto_increment,
  `nomo` varchar(50) character set utf8 collate utf8_esperanto_ci default NULL,
  `subjekto` varchar(100) character set utf8 collate utf8_esperanto_ci default NULL,
  `korpo` text character set utf8 collate utf8_esperanto_ci,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COMMENT='sxablonoj por retposxtoj al partoprenantoj' AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_sercxoj`
-- 

CREATE TABLE `is_sercxoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(30) character set utf8 collate utf8_esperanto_ci default NULL,
  `priskribo` varchar(200) character set utf8 collate utf8_esperanto_ci default NULL,
  `entajpanto` int(11) NOT NULL default '0',
  `sercxo` blob NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`),
  KEY `entajpanto` (`entajpanto`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 COMMENT='La dauxrigitaj sercxoj' AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_tekstoj`
-- 

CREATE TABLE `is_tekstoj` (
  `ID` int(10) NOT NULL auto_increment,
  `renkontigxoID` int(10) NOT NULL default '0',
  `mesagxoID` varchar(30) character set ascii default NULL,
  `teksto` text collate utf8_esperanto_ci,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `renkontigxoID` (`renkontigxoID`,`mesagxoID`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='tabelo por lokaligo de tekstoj (-> tekstoj.php)' AUTO_INCREMENT=82 ;

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
