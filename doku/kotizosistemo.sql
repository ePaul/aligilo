-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-6
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 05. Dezember 2007 um 20:14
-- Server Version: 5.0.32
-- PHP-Version: 4.4.4-8+etch4
-- 
-- Datenbank: `test`
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='aĝkategorioj' AUTO_INCREMENT=15 ;

-- 
-- Daten für Tabelle `is_agxkategorioj`
-- 

INSERT INTO `is_agxkategorioj` (`ID`, `nomo`, `priskribo`, `sistemoID`, `limagxo`) VALUES 
(1, 'bebo', 'El la kondiĉoj:\r\n\r\nBeboj ĝis 2 jaroj, kiuj ne bezonas propran liton (de ni), partoprenas senkoste. (Bonvolu tamen alligi lin/ŝin, por ke ni povas krei nomŝildon. Kaj se via bebo sub 2 jaroj tamen bezonas liton, notu tion en la komento-kampo.)', 1, 2),
(2, '0-17', 'la veraj junuloj - kategorio "Sub18".', 1, 17),
(3, '18-21', '', 1, 21),
(4, '22-26', '', 1, 26),
(5, '27-35', 'nur iom "tro aĝa".', 1, 35),
(6, '36+', 'La "maljunuloj".', 1, 200),
(7, 'bebo', 'El la kondiĉoj:\r\n\r\nBeboj ĝis 2 jaroj, kiuj ne bezonas propran liton (de ni), partoprenas senkoste. (Bonvolu tamen alligi lin/ŝin, por ke ni povas krei nomŝildon. Kaj se via bebo sub 2 jaroj tamen bezonas liton, notu tion en la komento-kampo.)', 2, 2),
(8, '0-17', 'la veraj junuloj - kategorio "Sub18".', 2, 17),
(9, '18-21', '', 2, 21),
(10, '22-26', 'La plej multaj estas c^i tie.', 2, 26),
(11, '27-35', 'nur iom "tro aĝa".', 2, 35),
(12, '36+', 'La "maljunuloj".', 2, 200),
(13, 'junuloj', 'c^iuj sub 18 jaroj', 3, 17),
(14, 'maljunuloj', 'La resto.', 3, 200);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='sistemoj de aĝkategorioj' AUTO_INCREMENT=4 ;

-- 
-- Daten für Tabelle `is_agxkategorisistemoj`
-- 

INSERT INTO `is_agxkategorisistemoj` (`ID`, `nomo`, `entajpanto`, `priskribo`) VALUES 
(1, 'IS 2007', 11, 'La aĝokategorioj de IS 2007 (kaj simile jam kelkajn jarojn antaŭe).'),
(2, 'Ekzempla sistemo', 11, 'Varianto de la aĝokategorioj de IS 2007.'),
(3, 'Tute nova sistemo', 11, 'Tre simpla sistemo el nur du ag^kategorioj.');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='aliĝkategorioj' AUTO_INCREMENT=5 ;

-- 
-- Daten für Tabelle `is_aligxkategorioj`
-- 

INSERT INTO `is_aligxkategorioj` (`ID`, `nomo`, `priskribo`, `sistemoID`, `limdato`, `nomo_lokalingve`) VALUES 
(1, 'tre frua', 'Aliĝo ĝis la 27a de aŭgusto.', 1, 122, 'sehr früh'),
(2, 'frua', 'Aliĝo ĝis la 31a de oktobro.', 1, 57, 'früh'),
(3, 'malfrua', 'Aliĝo ĝis la 20a de decembro.', 1, 7, 'spät'),
(4, 'surloka', 'surloka aliĝo (krompago 10 €).\r\n(Atentu, la nomo de tiu kategorio devas esti "surloka", por kalkulo de la "surloka krompago".)', 1, -1000, 'vor Ort');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='sistemoj de alĝikategorioj' AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `is_aligxkategorisistemoj`
-- 

INSERT INTO `is_aligxkategorisistemoj` (`ID`, `nomo`, `entajpanto`, `priskribo`) VALUES 
(1, 'IS 2007', 11, 'La aliĝkategorioj de IS 2007: "tre frua", "frua", "malfrua" kaj "surloka".');

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

-- 
-- Daten für Tabelle `is_fikskostoj`
-- 

INSERT INTO `is_fikskostoj` (`ID`, `nomo`, `kostosistemo`, `kosto`) VALUES 
(1, 'lupago memzorgantejo', 1, 1000.00);

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

-- 
-- Daten für Tabelle `is_kategorioj_de_landoj`
-- 

INSERT INTO `is_kategorioj_de_landoj` (`kategorioID`, `sistemoID`, `landoID`) VALUES 
(7, 3, 1),
(8, 1, 1),
(3, 1, 7),
(1, 1, 8),
(3, 1, 9),
(1, 1, 10),
(3, 1, 11),
(1, 1, 12),
(1, 1, 13),
(1, 1, 14),
(1, 1, 15),
(1, 1, 16),
(2, 1, 17),
(2, 1, 18),
(1, 1, 19),
(3, 1, 20),
(1, 1, 21),
(3, 1, 22),
(2, 1, 23),
(3, 1, 24),
(1, 1, 25),
(3, 1, 26),
(3, 1, 27),
(1, 1, 28),
(1, 1, 29),
(1, 1, 30),
(2, 1, 31),
(2, 1, 32),
(3, 1, 33),
(1, 1, 34),
(2, 1, 35),
(1, 1, 36),
(1, 1, 37),
(3, 1, 38),
(2, 1, 39),
(3, 1, 40),
(3, 1, 41),
(2, 1, 42),
(2, 1, 43),
(2, 1, 44),
(2, 1, 45),
(2, 1, 46),
(3, 1, 47),
(3, 1, 48),
(3, 1, 49),
(3, 1, 50),
(3, 1, 51),
(3, 1, 52),
(3, 1, 53),
(3, 1, 54),
(3, 1, 55),
(3, 1, 56),
(3, 1, 57),
(3, 1, 58),
(3, 1, 59),
(3, 1, 60),
(3, 1, 61),
(3, 1, 62),
(3, 1, 63),
(3, 1, 66),
(3, 1, 67),
(3, 1, 65),
(3, 1, 69),
(3, 1, 70),
(3, 1, 73),
(3, 1, 74),
(3, 1, 75),
(3, 1, 76),
(0, 0, 8),
(0, 0, 10),
(0, 0, 12),
(0, 0, 13),
(0, 0, 14),
(0, 0, 15),
(0, 0, 16),
(0, 0, 19),
(0, 0, 21),
(0, 0, 25),
(0, 0, 28),
(0, 0, 29),
(0, 0, 30),
(0, 0, 34),
(0, 0, 36),
(0, 0, 37),
(0, 0, 17),
(0, 0, 18),
(0, 0, 23),
(0, 0, 31),
(0, 0, 32),
(0, 0, 35),
(0, 0, 39),
(0, 0, 42),
(0, 0, 43),
(0, 0, 44),
(0, 0, 45),
(0, 0, 46),
(0, 0, 7),
(0, 0, 9),
(0, 0, 11),
(0, 0, 20),
(0, 0, 22),
(0, 0, 24),
(0, 0, 26),
(0, 0, 27),
(0, 0, 33),
(0, 0, 38),
(0, 0, 40),
(0, 0, 41),
(0, 0, 47),
(0, 0, 48),
(0, 0, 49),
(0, 0, 50),
(0, 0, 51),
(0, 0, 52),
(0, 0, 53),
(0, 0, 54),
(0, 0, 55),
(0, 0, 56),
(0, 0, 57),
(0, 0, 58),
(0, 0, 59),
(0, 0, 60),
(0, 0, 61),
(0, 0, 62),
(0, 0, 63),
(0, 0, 65),
(0, 0, 66),
(0, 0, 67),
(0, 0, 69),
(0, 0, 70),
(0, 0, 73),
(0, 0, 74),
(0, 0, 75),
(0, 0, 76),
(7, 5, 8),
(7, 5, 10),
(7, 5, 12),
(7, 5, 13),
(7, 5, 14),
(7, 5, 15),
(7, 5, 16),
(7, 5, 19),
(7, 5, 21),
(7, 5, 25),
(7, 5, 28),
(7, 5, 29),
(7, 5, 30),
(7, 5, 34),
(7, 5, 36),
(7, 5, 37),
(8, 5, 17),
(8, 5, 18),
(8, 5, 23),
(8, 5, 31),
(8, 5, 32),
(8, 5, 35),
(8, 5, 39),
(8, 5, 42),
(8, 5, 43),
(8, 5, 44),
(8, 5, 45),
(8, 5, 46),
(9, 5, 7),
(9, 5, 9),
(9, 5, 11),
(9, 5, 20),
(9, 5, 22),
(9, 5, 24),
(9, 5, 26),
(9, 5, 27),
(9, 5, 33),
(9, 5, 38),
(9, 5, 40),
(9, 5, 41),
(9, 5, 47),
(9, 5, 48),
(9, 5, 49),
(9, 5, 50),
(9, 5, 51),
(9, 5, 52),
(9, 5, 53),
(9, 5, 54),
(9, 5, 55),
(9, 5, 56),
(9, 5, 57),
(9, 5, 58),
(9, 5, 59),
(9, 5, 60),
(9, 5, 61),
(9, 5, 62),
(9, 5, 63),
(9, 5, 65),
(9, 5, 66),
(9, 5, 67),
(9, 5, 69),
(9, 5, 70),
(9, 5, 73),
(9, 5, 74),
(9, 5, 75),
(9, 5, 76),
(8, 5, 77),
(9, 5, 82),
(9, 5, 78),
(9, 5, 64),
(9, 5, 72),
(9, 5, 80),
(9, 5, 79),
(2, 1, 77),
(3, 1, 82),
(3, 1, 78),
(3, 1, 64),
(3, 1, 72),
(3, 1, 80),
(3, 1, 79);

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

-- 
-- Daten für Tabelle `is_kostosistemoj`
-- 

INSERT INTO `is_kostosistemoj` (`ID`, `nomo`, `priskribo`, `entajpanto`) VALUES 
(1, 'IS 2007', 'La kostoj de IS 2007', 11);

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
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='diversaj kotizosistemoj' AUTO_INCREMENT=4 ;

-- 
-- Daten für Tabelle `is_kotizosistemoj`
-- 

INSERT INTO `is_kotizosistemoj` (`ID`, `nomo`, `priskribo`, `entajpanto`, `aligxkategorisistemo`, `landokategorisistemo`, `agxkategorisistemo`, `logxkategorisistemo`, `parttempdivisoro`) VALUES 
(1, 'IS 2007', 'La kotizosistemo de IS 2007.\r\nBonvolu ne s^ang^u g^in rekte, sed anstatau^e faru kopion kaj s^ang^u tiun.', 11, 1, 1, 1, 1, 6),
(2, 'Elprova sistemo', 'Variaj^o de la kotizosistemo de IS 2007.', 11, 1, 5, 3, 1, 3.1415),
(3, 'alia varianto', 'Variaj^o de la kotizosistemo de IS 2007.', 0, 1, 1, 1, 1, 6);

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

-- 
-- Daten für Tabelle `is_kotizotabeleroj`
-- 

INSERT INTO `is_kotizotabeleroj` (`kotizosistemo`, `aligxkategorio`, `landokategorio`, `agxkategorio`, `logxkategorio`, `kotizo`) VALUES 
(1, 1, 1, 2, 1, 100.00),
(1, 1, 2, 3, 1, 120.00),
(1, 1, 1, 1, 2, 0.00),
(1, 2, 1, 1, 3, 120.00),
(1, 1, 1, 2, 2, 15.00),
(1, 1, 2, 1, 2, 0.00),
(1, 2, 1, 6, 1, 260.00),
(1, 1, 1, 1, 1, 0.00),
(1, 1, 2, 1, 1, 0.00),
(1, 1, 3, 1, 1, 0.00),
(1, 1, 2, 2, 1, 80.00),
(1, 1, 3, 2, 1, 70.00),
(1, 1, 1, 3, 1, 140.00),
(1, 1, 3, 3, 1, 100.00),
(1, 1, 1, 4, 1, 175.00),
(1, 1, 2, 4, 1, 145.00),
(1, 1, 3, 4, 1, 125.00),
(1, 1, 1, 5, 1, 230.00),
(1, 1, 2, 5, 1, 190.00),
(1, 1, 3, 5, 1, 170.00),
(1, 1, 1, 6, 1, 250.00),
(1, 1, 2, 6, 1, 210.00),
(1, 1, 3, 6, 1, 185.00),
(1, 1, 3, 1, 2, 0.00),
(1, 1, 2, 2, 2, 8.00),
(1, 1, 3, 2, 2, 5.00),
(1, 1, 1, 3, 2, 35.00),
(1, 1, 2, 3, 2, 20.00),
(1, 1, 3, 3, 2, 10.00),
(1, 1, 1, 4, 2, 50.00),
(1, 1, 2, 4, 2, 30.00),
(1, 1, 3, 4, 2, 15.00),
(1, 1, 1, 5, 2, 60.00),
(1, 1, 2, 5, 2, 40.00),
(1, 1, 3, 5, 2, 20.00),
(1, 1, 1, 6, 2, 75.00),
(1, 1, 2, 6, 2, 50.00),
(1, 1, 3, 6, 2, 25.00),
(1, 2, 1, 1, 1, 0.00),
(1, 2, 2, 1, 1, 0.00),
(1, 2, 3, 1, 1, 0.00),
(1, 2, 1, 2, 1, 110.00),
(1, 2, 2, 2, 1, 95.00),
(1, 2, 3, 2, 1, 80.00),
(1, 2, 1, 3, 1, 150.00),
(1, 2, 2, 3, 1, 130.00),
(1, 2, 3, 3, 1, 115.00),
(1, 2, 1, 4, 1, 185.00),
(1, 2, 2, 4, 1, 155.00),
(1, 2, 3, 4, 1, 140.00),
(1, 2, 1, 5, 1, 240.00),
(1, 2, 2, 5, 1, 200.00),
(1, 2, 3, 5, 1, 180.00),
(1, 2, 2, 6, 1, 220.00),
(1, 2, 3, 6, 1, 195.00),
(1, 2, 1, 1, 2, 0.00),
(1, 2, 2, 1, 2, 0.00),
(1, 2, 3, 1, 2, 0.00),
(1, 2, 1, 2, 2, 20.00),
(1, 2, 2, 2, 2, 15.00),
(1, 2, 3, 2, 2, 10.00),
(1, 2, 1, 3, 2, 40.00),
(1, 2, 2, 3, 2, 25.00),
(1, 2, 3, 3, 2, 17.00),
(1, 2, 1, 4, 2, 55.00),
(1, 2, 2, 4, 2, 40.00),
(1, 2, 3, 4, 2, 22.00),
(1, 2, 1, 5, 2, 70.00),
(1, 2, 2, 5, 2, 45.00),
(1, 2, 3, 5, 2, 28.00),
(1, 2, 1, 6, 2, 80.00),
(1, 2, 2, 6, 2, 50.00),
(1, 2, 3, 6, 2, 33.00),
(1, 3, 1, 1, 1, 0.00),
(1, 3, 2, 1, 1, 0.00),
(1, 3, 3, 1, 1, 0.00),
(1, 3, 1, 2, 1, 130.00),
(1, 3, 2, 2, 1, 110.00),
(1, 3, 3, 2, 1, 95.00),
(1, 3, 1, 3, 1, 185.00),
(1, 3, 2, 3, 1, 155.00),
(1, 3, 3, 3, 1, 140.00),
(1, 3, 1, 4, 1, 215.00),
(1, 3, 2, 4, 1, 185.00),
(1, 3, 3, 4, 1, 160.00),
(1, 3, 1, 5, 1, 270.00),
(1, 3, 2, 5, 1, 230.00),
(1, 3, 3, 5, 1, 200.00),
(1, 3, 1, 6, 1, 290.00),
(1, 3, 2, 6, 1, 250.00),
(1, 3, 3, 6, 1, 220.00),
(1, 3, 1, 1, 2, 0.00),
(1, 3, 2, 1, 2, 0.00),
(1, 3, 3, 1, 2, 0.00),
(1, 3, 1, 2, 2, 25.00),
(1, 3, 2, 2, 2, 19.00),
(1, 3, 3, 2, 2, 12.00),
(1, 3, 1, 3, 2, 45.00),
(1, 3, 2, 3, 2, 33.00),
(1, 3, 3, 3, 2, 20.00),
(1, 3, 1, 4, 2, 65.00),
(1, 3, 2, 4, 2, 45.00),
(1, 3, 3, 4, 2, 25.00),
(1, 3, 1, 5, 2, 80.00),
(1, 3, 2, 5, 2, 60.00),
(1, 3, 3, 5, 2, 33.00),
(1, 3, 1, 6, 2, 95.00),
(1, 3, 2, 6, 2, 65.00),
(1, 3, 3, 6, 2, 38.00),
(1, 4, 1, 1, 1, 0.00),
(1, 4, 2, 1, 1, 0.00),
(1, 4, 3, 1, 1, 0.00),
(1, 4, 1, 2, 1, 130.00),
(1, 4, 2, 2, 1, 110.00),
(1, 4, 3, 2, 1, 95.00),
(1, 4, 1, 3, 1, 185.00),
(1, 4, 2, 3, 1, 155.00),
(1, 4, 3, 3, 1, 140.00),
(1, 4, 1, 4, 1, 215.00),
(1, 4, 2, 4, 1, 185.00),
(1, 4, 3, 4, 1, 160.00),
(1, 4, 1, 5, 1, 270.00),
(1, 4, 2, 5, 1, 230.00),
(1, 4, 3, 5, 1, 200.00),
(1, 4, 1, 6, 1, 290.00),
(1, 4, 2, 6, 1, 250.00),
(1, 4, 3, 6, 1, 220.00),
(1, 4, 1, 1, 2, 0.00),
(1, 4, 2, 1, 2, 0.00),
(1, 4, 3, 1, 2, 0.00),
(1, 4, 1, 2, 2, 25.00),
(1, 4, 2, 2, 2, 19.00),
(1, 4, 3, 2, 2, 12.00),
(1, 4, 1, 3, 2, 45.00),
(1, 4, 2, 3, 2, 33.00),
(1, 4, 3, 3, 2, 20.00),
(1, 4, 1, 4, 2, 65.00),
(1, 4, 2, 4, 2, 45.00),
(1, 4, 3, 4, 2, 25.00),
(1, 4, 1, 5, 2, 80.00),
(1, 4, 2, 5, 2, 60.00),
(1, 4, 3, 5, 2, 33.00),
(1, 4, 1, 6, 2, 95.00),
(1, 4, 2, 6, 2, 65.00),
(1, 4, 3, 6, 2, 38.00),
(2, 1, 1, 1, 1, 17.00);

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

-- 
-- Daten für Tabelle `is_krompagoj`
-- 

INSERT INTO `is_krompagoj` (`tipo`, `kotizosistemo`, `krompago`) VALUES 
(1, 1, 20.00),
(2, 1, 5.00),
(3, 1, 10.00),
(5, 1, 10.00),
(6, 1, 30.00),
(4, 1, 10.00);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_krompagotipoj`
-- 

CREATE TABLE `is_krompagotipoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(30) collate utf8_esperanto_ci NOT NULL,
  `nomo_lokalingve` varchar(30) character set utf8 NOT NULL,
  `entajpanto` int(11) NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `kondicxo` varchar(100) character set ascii NOT NULL COMMENT 'nomo de kondicxo-funkcio vokenda',
  `uzebla` char(1) collate utf8_esperanto_ci NOT NULL default 'j',
  `lauxnokte` char(1) character set ascii NOT NULL default 'n' COMMENT 'c^u lau^nokta krompago, c^u lau^taga?',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='tipoj de eblaj krompagoj' AUTO_INCREMENT=8 ;

-- 
-- Daten für Tabelle `is_krompagotipoj`
-- 

INSERT INTO `is_krompagotipoj` (`ID`, `nomo`, `nomo_lokalingve`, `entajpanto`, `priskribo`, `kondicxo`, `uzebla`, `lauxnokte`) VALUES 
(1, 'dulita c^ambro', 'Zweibett-Zimmer', 11, 'Krompago pro mendo kaj ricevo de dulita c^ambro.', 'havas_dulitan_cxambron', 'j', 'n'),
(2, 'invitletero (sub 30)', 'Einladungsbrief (unter 30)', 11, 'Krompago por sendo de invitletero.', 'invitletero_sub30', 'j', 'n'),
(3, 'invitletero (ekde 30)', 'Einladungsbrief (ab 30)', 11, 'krompago por invitletero.', 'invitletero_ekde30', 'j', 'n'),
(4, 'surloka alig^o', 'Vorort-Anmeldung', 11, 'krompago, se iu alig^as nur surloke (t.e. post 20a de decembro en IS), au^ forgesis antau^pagi.', 'surloka_aligxo', 'j', 'n'),
(5, 'mang^kupono', 'Essens-Coupon', 11, 'krompago por mang^kupono (lau^ nokto)', 'mangxkupona_krompago', 'j', 'j'),
(7, 'c^iuj (1x)', 'Alle (1x)', 0, 'Krompago por c^iuj (unufoje)', 'cxiam', 'j', 'n'),
(6, 'unulita c^ambro', 'Einbett-Zimmer', 11, 'unulita c^ambro', 'havas_unulitan_cxambron', 'j', 'n');

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='landokategorioj' AUTO_INCREMENT=10 ;

-- 
-- Daten für Tabelle `is_landokategorioj`
-- 

INSERT INTO `is_landokategorioj` (`ID`, `nomo`, `priskribo`, `sistemoID`) VALUES 
(1, 'A', 'A-landoj: ĝenerale riĉaj eŭropaj landoj.', 1),
(2, 'B', 'B-landoj: riĉaj ekstereŭropaj landoj, kaj mezriĉaj eŭropaj landoj.', 1),
(3, 'C', 'C-landoj: ĉiuj aliaj landoj (malriĉaj landoj en la tuta mondo, kaj kelkaj etaj eŭropaj landoj, el kiuj neniam iu venis)', 1),
(4, 'A', 'A-landoj: ĝenerale riĉaj eŭropaj landoj.', 4),
(5, 'B', 'B-landoj: riĉaj ekstereŭropaj landoj, kaj mezriĉaj eŭropaj landoj.', 4),
(6, 'C', 'C-landoj: ĉiuj aliaj landoj (malriĉaj landoj en la tuta mondo, kaj kelkaj etaj eŭropaj landoj, el kiuj neniam iu venis)', 4),
(7, 'A', 'A-landoj: ĝenerale riĉaj eŭropaj landoj.', 5),
(8, 'B', 'B-landoj: riĉaj ekstereŭropaj landoj, kaj mezriĉaj eŭropaj landoj.', 5),
(9, 'C', 'C-landoj: ĉiuj aliaj landoj (malriĉaj landoj en la tuta mondo, kaj kelkaj etaj eŭropaj landoj, el kiuj neniam iu venis)', 5);

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='sistemoj de landokategorioj' AUTO_INCREMENT=6 ;

-- 
-- Daten für Tabelle `is_landokategorisistemoj`
-- 

INSERT INTO `is_landokategorisistemoj` (`ID`, `nomo`, `entajpanto`, `priskribo`) VALUES 
(1, 'IS 2007', 11, 'La landokategorisistemo de IS 2007 (kaj simile dum kelkaj antaŭaj). Tri kategorioj A, B, C, laŭ distanco kaj riĉeco de la landoj.'),
(4, 'Nova landosistemo', 11, 'Landosistemo kun la kutimaj tri kategorioj, sed ankorau^ sen landoj.'),
(5, 'plia varianto', 11, 'La landokategorisistemo de IS 2007 (kaj simile dum kelkaj antaŭaj). Tri kategorioj A, B, C, laŭ distanco kaj riĉeco de la landoj.');

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

-- 
-- Daten für Tabelle `is_logxkategorioj`
-- 

INSERT INTO `is_logxkategorioj` (`ID`, `nomo`, `priskribo`, `sistemoID`, `sxlosillitero`) VALUES 
(1, 'junulargastejo', 'Loĝado en Junulargastejo, kun plena manĝado.', 1, 'J'),
(2, 'memzorgantejo', 'Spaco por matraco en la amasloĝejo, sen manĝado (krom la silvestra bufedo).', 1, 'M');

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

-- 
-- Daten für Tabelle `is_logxkategorisistemoj`
-- 

INSERT INTO `is_logxkategorisistemoj` (`ID`, `nomo`, `entajpanto`, `priskribo`) VALUES 
(1, 'IS 2007', 11, 'La loĝkategorioj por IS 2007/2008 (kaj pluraj antaŭaj ISoj): <ul>\r\n<li> junulargastejo (kun manĝado)</li>\r\n<li>memzorgantejo (amasloĝejo, senmanĝado). </li>\r\n</ul>\r\nNe indas krei novan log^kategorisistemon por analizaj celoj, c^ar la partoprenoj en c^iuj g^isnunaj renkontig^oj havas nur la domotipojn ''J'' kaj ''M''.');

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

-- 
-- Daten für Tabelle `is_minimumaj_antauxpagoj`
-- 

INSERT INTO `is_minimumaj_antauxpagoj` (`kotizosistemo`, `landokategorio`, `oficiala_antauxpago`, `interna_antauxpago`) VALUES 
(1, 1, 30.00, 28.00),
(1, 2, 10.00, 9.00),
(1, 3, 0.00, 0.00);

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

-- 
-- Daten für Tabelle `is_personkostoj`
-- 

INSERT INTO `is_personkostoj` (`tipo`, `kostosistemo`, `maks_haveblaj`, `min_uzendaj`, `kosto_uzata`, `kosto_neuzata`) VALUES 
(3, 1, 165, 150, 26.00, 2.00);

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `is_personkostotipoj`
-- 

CREATE TABLE `is_personkostotipoj` (
  `ID` int(11) NOT NULL auto_increment,
  `nomo` varchar(30) collate utf8_esperanto_ci NOT NULL,
  `entajpanto` int(11) NOT NULL,
  `priskribo` text collate utf8_esperanto_ci NOT NULL,
  `kondicxo` text collate utf8_esperanto_ci NOT NULL,
  `uzebla` char(1) collate utf8_esperanto_ci NOT NULL default 'j',
  `lauxnokte` char(1) character set ascii NOT NULL default 'n' COMMENT 'c^u lau^nokta krompago, c^u lau^taga?',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `nomo` (`nomo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_esperanto_ci COMMENT='tipoj de eblaj kostoj laux persono' AUTO_INCREMENT=4 ;

-- 
-- Daten für Tabelle `is_personkostotipoj`
-- 

INSERT INTO `is_personkostotipoj` (`ID`, `nomo`, `entajpanto`, `priskribo`, `kondicxo`, `uzebla`, `lauxnokte`) VALUES 
(1, '≥ 27 (lau^nokte)', 0, 'c^iuj homoj super 27 jaroj, kun lau^nokta kalkulado.', 'return $partopreno->datoj["agxo"] > 26;', 'j', 'j'),
(2, '≥ 27 (unufoje)', 0, 'c^iuj ekde 27 jaroj.', 'return $partopreno->datoj["agxo"] > 26;', 'j', 'n'),
(3, 'lito junulargastejo', 0, 'bazaj kostoj por log^ado en la junulargastejo', 'return $partopreno->datoj["domotipo"] == "J";', 'j', 'j');
