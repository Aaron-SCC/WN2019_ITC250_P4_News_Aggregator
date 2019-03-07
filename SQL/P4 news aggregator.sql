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
(1,	'Science'),
(2,	'Politics'),
(3,	'Sports');

DROP TABLE IF EXISTS `rssFeeds`;
CREATE TABLE `rssFeeds` (
  `feedName` varchar(255) NOT NULL,
  `feedID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `categoryID` int(11) DEFAULT '0',
  `feedUrl` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`feedID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `rssFeeds` (`feedName`, `feedID`, `categoryID`, `feedUrl`) VALUES
('Space Exploration',	1,	1,	'https://news.google.com/topics/CAAqIQgKIhtDQkFTRGdvSUwyMHZNRGN4TjNjU0FtVnVLQUFQAQ?oc=3&ceid=US:en'),
('World Politics',	3,	2,	'https://news.google.com/publications/CAAqNQgKIi9DQklTSFFnTWFoa0tGM2R2Y214a2NHOXNhWFJwWTNOeVpYWnBaWGN1WTI5dEtBQVAB?oc=3&ceid=US:en'),
('Physics',	4,	1,	'https://news.google.com/topics/CAAqBwgKMJHQ9Qowhc7cAg?oc=3&ceid=US:en'),
('Genetics',	5,	1,	'https://news.google.com/topics/CAAqBwgKMPHT9Qow0bHaAg?oc=3&ceid=US:en'),
('US Politics',	6,	2,	'https://news.google.com/topics/CAAqIggKIhxDQkFTRHdvSkwyMHZNRGxqTjNjd0VnSmxiaWdBUAE?oc=3&ceid=US:en'),
('The White House',	7,	2,	'https://news.google.com/topics/CAAqBwgKMIzp9Qow_fX1Ag?oc=3&ceid=US:en'),
('NFL',	8,	3,	'https://news.google.com/topics/CAAqBwgKMLS-zgEwir4o?oc=3&ceid=US:en'),
('NBA',	9,	3,	'https://news.google.com/topics/CAAqBwgKMMTz0gEwu401?oc=3&ceid=US:en'),
('MBA',	10,	3,	'https://news.google.com/topics/CAAqIQgKIhtDQkFTRGdvSUwyMHZNREU0YW5vU0FtVnVLQUFQAQ?oc=3&ceid=US:en');