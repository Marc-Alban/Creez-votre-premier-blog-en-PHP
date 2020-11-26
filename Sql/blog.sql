-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 26 nov. 2020 à 10:56
-- Version du serveur :  8.0.21
-- Version de PHP : 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `blog`
--

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `idComment` int NOT NULL AUTO_INCREMENT,
  `content` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `disabled` tinyint NOT NULL DEFAULT '1',
  `UserId` int DEFAULT NULL,
  `PostId` int DEFAULT NULL,
  `dateCreation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idComment`),
  KEY `fk_Comment_User1_idx` (`UserId`),
  KEY `fk_Comment_Post1_idx` (`PostId`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `idPost` int NOT NULL AUTO_INCREMENT,
  `title` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `chapo` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `imagePost` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `datePost` date NOT NULL,
  `statuPost` int NOT NULL DEFAULT '0',
  `UserId` int DEFAULT NULL,
  PRIMARY KEY (`idPost`),
  KEY `fk_Post_User_idx` (`UserId`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`idPost`, `title`, `description`, `chapo`, `imagePost`, `datePost`, `statuPost`, `UserId`) VALUES
(21, '1er article de la saison', 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of &quot;de Finibus Bonorum et Malorum&quot; (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, &quot;Lorem ipsum dolor sit amet..&quot;, comes from a line in section 1.10.32.\r\n\r\nThe standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from &quot;de Finibus Bonorum et Malorum&quot; by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.', 'Une sortie &agrave; Epinal tr&egrave;s sympas', '1.jpg', '2020-11-16', 1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `idUser` int NOT NULL AUTO_INCREMENT,
  `userName` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `passwordUser` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `userType` enum('Admin','Utilisateur') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Utilisateur',
  `dateCreation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`idUser`, `userName`, `email`, `passwordUser`, `activated`, `userType`, `dateCreation`) VALUES
(5, 'tata76', 'tat@live.fr', '$2y$10$5oRQ/LWO.p7YVLK8YfaFOOkkdSahd4lwhKHZ6ARjNvTp3BbAXWIhm', 1, 'Admin', '2020-11-11 14:21:39'),
(9, 'Jean-paul', 'marc@712live.fr', '$2y$10$EXAcTxmEKfMrzIOj/Ftj5uYlrwNvnZE2NQSBAm6niTIzf1UAojgEK', 1, 'Admin', '2020-11-26 10:21:02');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `fk_Comment_Post1` FOREIGN KEY (`PostId`) REFERENCES `post` (`idPost`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_Comment_User1` FOREIGN KEY (`UserId`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fk_Post_User` FOREIGN KEY (`UserId`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
