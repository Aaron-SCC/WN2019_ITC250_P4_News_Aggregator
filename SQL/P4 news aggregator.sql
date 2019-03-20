-- Adminer 4.6.3 MySQL dump
SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
DROP TABLE IF EXISTS `rssCategories`;
CREATE TABLE `rssCategories` (
  `categoryID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`categoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `rssCategories` (`categoryID`, `categoryName`) VALUES
(1, 'Science'),
(2, 'Politics'),
(3, 'Sports');
DROP TABLE IF EXISTS `rssFeeds`;
CREATE TABLE `rssFeeds` (
  `feedName` varchar(255) NOT NULL,
  `feedID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `categoryID` int(11) DEFAULT '0',
  `feedUrl` varchar(255) DEFAULT NULL,
  `feedTimeStamp` int(10) unsigned DEFAULT NULL,
  `feedRawFeed` longtext,
  PRIMARY KEY (`feedID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `rssFeeds` (`feedName`, `feedID`, `categoryID`, `feedUrl`, `feedTimeStamp`, `feedRawFeed`) VALUES
('Space Exploration',   1,  1,  'space+exploration',    NULL,   NULL),
('World Politics',  3,  2,  'world+politics',   NULL,   NULL),
('Physics', 4,  1,  'physics',  NULL,   NULL),
('Genetics',    5,  1,  'genetics', NULL,   NULL),
('US Politics', 6,  2,  'us+politics',  NULL,   NULL),
('The White House', 7,  2,  'the+white+house',  NULL,   NULL),
('NFL', 8,  3,  'nfl',  NULL,   NULL),
('NBA', 9,  3,  'nba',  NULL,   NULL),
('MBA', 10, 3,  'mba',  NULL,   NULL);
-- 2019-03-20 03:09:03
