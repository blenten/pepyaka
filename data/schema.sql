/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE IF NOT EXISTS `pepyaka` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `pepyaka`;

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `post_time` datetime NOT NULL DEFAULT current_timestamp(),
  `author` varchar(250) NOT NULL,
  `target` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `target` (`target`),
  CONSTRAINT `author` FOREIGN KEY (`author`) REFERENCES `users` (`login`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `target` FOREIGN KEY (`target`) REFERENCES `users` (`login`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
  `login` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT 0,
  `sex` enum('M','F') NOT NULL DEFAULT 'M',
  `info` text DEFAULT NULL,
  `avatar` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `effect` enum('+','-') NOT NULL,
  `vote_time` datetime NOT NULL DEFAULT current_timestamp(),
  `voter` varchar(250) DEFAULT NULL,
  `vote_for` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voter` (`voter`),
  KEY `votefor` (`vote_for`),
  CONSTRAINT `votefor` FOREIGN KEY (`vote_for`) REFERENCES `users` (`login`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `voter` FOREIGN KEY (`voter`) REFERENCES `users` (`login`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
