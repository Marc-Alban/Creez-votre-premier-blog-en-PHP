-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 01 déc. 2020 à 08:25
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`idComment`, `content`, `disabled`, `UserId`, `PostId`, `dateCreation`) VALUES
(24, 'Whoua trop cool le xss &lt;p&gt;tes ^^ &lt;h2&gt;gg&lt;/h2&gt;t&lt;/p&gt;', 1, 12, 24, '2020-12-01 09:08:08'),
(26, '--/-*/-*&#039;&quot;(&quot;&#039;', 1, 12, 24, '2020-12-01 09:08:38');

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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`idPost`, `title`, `description`, `chapo`, `imagePost`, `datePost`, `statuPost`, `UserId`) VALUES
(24, 'je suis un article', 'premiere description \r\nAvec des &lt;h1&gt;pour tester le xss&lt;/h1&gt;', 'premier article', '1.png', '2020-12-01', 1, 12);

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`idUser`, `userName`, `email`, `passwordUser`, `activated`, `userType`, `dateCreation`) VALUES
(10, 'Élodie', 'tat@live.fr', '$2y$10$D9OHhPa.V5WU1E8hFgtyVe2z0RWH5sDJWYxWPFG6BpLA2KU4CT9Eu', 0, 'Utilisateur', '2020-11-30 11:10:37'),
(11, 'jean-marc', 'marc76@live.fr', '$2y$10$Mk3sePZJCD9DM.xyf45YAOdok7zDWSbIEfeSWsDX2JwnXWLhVU0ni', 1, 'Admin', '2020-11-30 14:49:34'),
(12, 'Toto76', 'test76@live.fr', '$2y$10$P9gCi8q56a6v4qc1Lv1p1OxnAFIgUs4ppIxDUXjcCkeGnOObPXMiW', 1, 'Admin', '2020-12-01 08:09:04');

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
