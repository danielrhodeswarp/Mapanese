-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 09, 2011 at 08:39 PM
-- Server version: 5.1.53
-- PHP Version: 5.3.8-ZS5.5.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mapanese`
--

-- --------------------------------------------------------

--
-- Table structure for table `ekidata`
--

CREATE TABLE IF NOT EXISTS `ekidata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prefecture_iso_code` char(2) NOT NULL,
  `name_ja` char(100) NOT NULL,
  `name_en` char(150) DEFAULT NULL,
  `latitude` varchar(16) NOT NULL,
  `longitude` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `enamdict`
--

CREATE TABLE IF NOT EXISTS `enamdict` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` char(100) NOT NULL,
  `kana` char(100) NOT NULL,
  `definition` char(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `geo_label`
--

CREATE TABLE IF NOT EXISTS `geo_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prefecture_iso_code` int(11) NOT NULL,
  `shi` char(100) NOT NULL,
  `cho` char(100) NOT NULL,
  `block` char(10) NOT NULL,
  `number` char(10) NOT NULL,
  `latitude` char(16) NOT NULL,
  `longitude` char(16) NOT NULL,
  `ku` char(100) NOT NULL,
  `gun` char(100) NOT NULL,
  `gun_cho` char(100) NOT NULL,
  `son` char(100) NOT NULL,
  `basho` char(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Holder table for geo data from CSV file';

-- --------------------------------------------------------

--
-- Table structure for table `geo_label_basho`
--

CREATE TABLE IF NOT EXISTS `geo_label_basho` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `basho` char(200) NOT NULL,
  `latitude` char(16) NOT NULL,
  `longitude` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `latitude_index` (`latitude`),
  KEY `longitude_index` (`longitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `geo_label_cho`
--

CREATE TABLE IF NOT EXISTS `geo_label_cho` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cho` char(200) NOT NULL,
  `latitude` char(16) NOT NULL,
  `longitude` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `latitude_index` (`latitude`),
  KEY `longitude_index` (`longitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `geo_label_gun`
--

CREATE TABLE IF NOT EXISTS `geo_label_gun` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gun` char(200) NOT NULL,
  `latitude` char(16) NOT NULL,
  `longitude` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `latitude_index` (`latitude`),
  KEY `longitude_index` (`longitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `geo_label_gun_cho`
--

CREATE TABLE IF NOT EXISTS `geo_label_gun_cho` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gun_cho` char(200) NOT NULL,
  `latitude` char(16) NOT NULL,
  `longitude` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `latitude_index` (`latitude`),
  KEY `longitude_index` (`longitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `geo_label_ken`
--

CREATE TABLE IF NOT EXISTS `geo_label_ken` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ken` char(50) NOT NULL,
  `latitude` char(16) NOT NULL,
  `longitude` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `latitude_index` (`latitude`),
  KEY `longitude_index` (`longitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `geo_label_ku`
--

CREATE TABLE IF NOT EXISTS `geo_label_ku` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ku` char(200) NOT NULL,
  `latitude` char(16) NOT NULL,
  `longitude` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `latitude_index` (`latitude`),
  KEY `longitude_index` (`longitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `geo_label_shi`
--

CREATE TABLE IF NOT EXISTS `geo_label_shi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shi` char(120) NOT NULL,
  `latitude` char(16) NOT NULL,
  `longitude` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `latitude_index` (`latitude`),
  KEY `longitude_index` (`longitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `geo_label_son`
--

CREATE TABLE IF NOT EXISTS `geo_label_son` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `son` char(200) NOT NULL,
  `latitude` char(16) NOT NULL,
  `longitude` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `latitude_index` (`latitude`),
  KEY `longitude_index` (`longitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `postcode`
--

CREATE TABLE IF NOT EXISTS `postcode` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary key',
  `postcode` char(7) NOT NULL COMMENT 'Postcode',
  `city_katakana` varchar(128) NOT NULL COMMENT 'City name (katakana)',
  `city_kanji` varchar(128) NOT NULL COMMENT 'City name (kanji)',
  `town_katakana` varchar(128) NOT NULL COMMENT 'Town name (katakana)',
  `town_kanji` varchar(128) NOT NULL COMMENT 'Town name (kanji)',
  `city_en` varchar(128) NOT NULL COMMENT 'City name (English)',
  `town_en` varchar(128) NOT NULL COMMENT 'Town name (English)',
  `prefecture_iso_code` char(2) NOT NULL COMMENT 'Prefecture (prefecture.iso_code)',
  `shi_ja` varchar(128) NOT NULL,
  `shi_en` varchar(128) NOT NULL,
  `ku_ja` varchar(128) NOT NULL,
  `ku_en` varchar(128) NOT NULL,
  `gun_ja` varchar(128) NOT NULL,
  `gun_en` varchar(128) NOT NULL,
  `gun_cho_en` varchar(128) NOT NULL,
  `gun_cho_ja` varchar(128) NOT NULL,
  `cho_en` varchar(128) NOT NULL,
  `cho_ja` varchar(128) NOT NULL,
  `son_en` varchar(128) NOT NULL,
  `son_ja` varchar(128) NOT NULL,
  `basho_en` varchar(128) NOT NULL,
  `basho_ja` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `postcode_index` (`postcode`),
  KEY `shi_en_index` (`shi_en`),
  KEY `gun_en_index` (`gun_en`),
  KEY `cho_en_index` (`cho_en`),
  KEY `gun_cho_en_index` (`gun_cho_en`),
  KEY `son_en_index` (`son_en`),
  KEY `basho_en_index` (`basho_en`),
  KEY `ku_en_index` (`ku_en`),
  KEY `shi_ja_index` (`shi_ja`),
  KEY `gun_ja_index` (`gun_ja`),
  KEY `cho_ja_index` (`cho_ja`),
  KEY `gun_cho_ja_index` (`gun_cho_ja`),
  KEY `son_ja_index` (`son_ja`),
  KEY `basho_ja_index` (`basho_ja`),
  KEY `ku_ja_index` (`ku_ja`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Japanese postcodes with matching addresses';

-- --------------------------------------------------------

--
-- Table structure for table `prefecture`
--

CREATE TABLE IF NOT EXISTS `prefecture` (
  `name_en` char(9) NOT NULL COMMENT 'English name',
  `name_ja` char(4) NOT NULL COMMENT 'Japanese name',
  `iso_code` char(2) NOT NULL COMMENT 'ISO code (North to South)',
  `short_name_ja` char(3) NOT NULL COMMENT 'Japanese name without ''ken'' (or whatever) - for searching',
  PRIMARY KEY (`iso_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Prefecture names';
