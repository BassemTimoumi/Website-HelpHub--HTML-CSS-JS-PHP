-- phpMyAdmin SQL Dump
-- version 2.6.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Apr 27, 2025 at 03:02 PM
-- Server version: 4.1.9
-- PHP Version: 4.3.10
-- 
-- Database: `helphub`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `associations`
-- 
CREATE DATABASE IF NOT EXISTS helphub;
USE helphub;

CREATE TABLE `associations` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(100) default NULL,
  `prenom` varchar(100) default NULL,
  `adresse` varchar(255) default NULL,
  `email` varchar(100) default NULL,
  `nom_association` varchar(100) default NULL,
  `cin` varchar(8) default NULL,
  `identifiant_fiscal` varchar(10) default NULL,
  `logo` varchar(255) default NULL,
  `pseudo` varchar(50) default NULL,
  `mot_de_passe` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `associations`
-- 

INSERT INTO `associations` VALUES (1, 'Yengui', 'Amine', 'Bouchoucha,Tunis', 'amineyengui0407@gmail.com', 'ISG', '87654321', '$ABC12', 'uploads/logos/_komiko_4_11zon.jpg', 'azerty', 'bouchouchaisg$');

-- --------------------------------------------------------

-- 
-- Table structure for table `donateur_projet`
-- 

CREATE TABLE `donateur_projet` (
  `id` int(11) NOT NULL auto_increment,
  `id_projet` int(11) NOT NULL default '0',
  `id_donateur` int(11) NOT NULL default '0',
  `montant_participation` float NOT NULL default '0',
  `date_participation` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  KEY `id_projet` (`id_projet`),
  KEY `id_donateur` (`id_donateur`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `donateur_projet`
-- 

INSERT INTO `donateur_projet` VALUES (1, 2, 1, 400, '2025-04-19 02:37:47');
INSERT INTO `donateur_projet` VALUES (2, 3, 1, 700, '2025-04-19 16:51:08');
INSERT INTO `donateur_projet` VALUES (3, 3, 1, 100, '2025-04-20 13:43:28');

-- --------------------------------------------------------

-- 
-- Table structure for table `donateurs`
-- 

CREATE TABLE `donateurs` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(100) default NULL,
  `prenom` varchar(100) default NULL,
  `email` varchar(100) default NULL,
  `cin` varchar(8) default NULL,
  `pseudo` varchar(50) default NULL,
  `mot_de_passe` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `donateurs`
-- 

INSERT INTO `donateurs` VALUES (1, 'Timoumi', 'Bassem', 'timoumibassem126@gmail.com', '12345678', 'abcde', 'bassemtimoumi#');

-- --------------------------------------------------------

-- 
-- Table structure for table `projet`
-- 

CREATE TABLE `projet` (
  `id_projet` int(11) NOT NULL auto_increment,
  `titre` varchar(30) NOT NULL default '',
  `description` varchar(200) NOT NULL default '',
  `date_limite` date NOT NULL default '0000-00-00',
  `montant_total_a_collecter` double NOT NULL default '0',
  `montant_total_collecte` double default '0',
  `id_responsable_association` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id_projet`),
  KEY `id_responsable_association` (`id_responsable_association`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `projet`
-- 

INSERT INTO `projet` VALUES (2, 'aide', 'yiugbqedfuigguisqfguib', '2025-04-30', 1000, 400, 1);
INSERT INTO `projet` VALUES (3, 'aide 2', 'aide projet2', '2025-04-29', 1000, 800, 1);
