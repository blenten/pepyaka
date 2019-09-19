-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               10.4.6-MariaDB - mariadb.org binary distribution
-- Операционная система:         Win64
-- HeidiSQL Версия:              10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Дамп структуры базы данных pepyaka
CREATE DATABASE IF NOT EXISTS `pepyaka` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `pepyaka`;

-- Дамп структуры для таблица pepyaka.comments
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `postTime` datetime NOT NULL DEFAULT current_timestamp(),
  `author` varchar(250) NOT NULL,
  `target` varchar(250) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `author` (`author`),
  KEY `target` (`target`),
  CONSTRAINT `author` FOREIGN KEY (`author`) REFERENCES `users` (`login`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `target` FOREIGN KEY (`target`) REFERENCES `users` (`login`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы pepyaka.comments: ~12 rows (приблизительно)
DELETE FROM `comments`;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` (`id`, `content`, `postTime`, `author`, `target`) VALUES
	(23, '^__^', '2019-09-19 18:18:39', 'sono', 'Vineferric');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;

-- Дамп структуры для таблица pepyaka.users
CREATE TABLE IF NOT EXISTS `users` (
  `login` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT 0,
  `gender` enum('M','F') NOT NULL DEFAULT 'M',
  `avatar` varchar(1024) DEFAULT NULL,
  `registrationDate` date DEFAULT curdate(),
  PRIMARY KEY (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы pepyaka.users: ~4 rows (приблизительно)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`login`, `password`, `votes`, `gender`, `avatar`, `registrationDate`) VALUES
	('mininaoksana89', '$2y$10$MKeYdYtu3ktu5Xwm.1arE.9.W0FZ4lfKlIsF3rpVsNqG40YhfFHJa', -1, 'F', NULL, '2019-09-19'),
	('sono', '$2y$10$KXp83mnY8GJvoJpbR4FFQ.2yyzKgb1Zvm6mAlhA/vjPdArG1YE/j2', 1, 'M', '5d838e19b6983', '2019-09-19'),
	('Vineferric', '$2y$10$ES5VIOSRCHkjgJcucywpDO5F7TI5Y52yFlfyvrpdQjQRIYVe0oOuu', 1, 'F', '5d837c33ed22b', '2019-09-19');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

-- Дамп структуры для таблица pepyaka.votes
CREATE TABLE IF NOT EXISTS `votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `effect` enum('+','-','0') NOT NULL,
  `voteTime` datetime NOT NULL DEFAULT current_timestamp(),
  `voter` varchar(250) DEFAULT NULL,
  `voteFor` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `voter` (`voter`),
  KEY `votefor` (`voteFor`),
  CONSTRAINT `votefor` FOREIGN KEY (`voteFor`) REFERENCES `users` (`login`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `voter` FOREIGN KEY (`voter`) REFERENCES `users` (`login`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы pepyaka.votes: ~120 rows (приблизительно)
DELETE FROM `votes`;
/*!40000 ALTER TABLE `votes` DISABLE KEYS */;
INSERT INTO `votes` (`id`, `effect`, `voteTime`, `voter`, `voteFor`) VALUES
	(135, '+', '2019-09-19 17:01:45', 'Vineferric', 'sono'),
	(136, '+', '2019-09-19 18:18:19', 'sono', 'Vineferric'),
	(137, '-', '2019-09-19 18:18:46', 'sono', 'mininaoksana89');
/*!40000 ALTER TABLE `votes` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
