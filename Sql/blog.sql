-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 03 sep. 2020 à 10:20
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
  `content` text,
  `dateCreation` datetime DEFAULT NULL,
  `disabled` tinyint DEFAULT NULL,
  `UserId` int DEFAULT NULL,
  `PostId` int DEFAULT NULL,
  PRIMARY KEY (`idComment`),
  KEY `fk_Comment_User1_idx` (`UserId`),
  KEY `fk_Comment_Post1_idx` (`PostId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `idPost` int NOT NULL AUTO_INCREMENT,
  `title` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `label` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `imagePost` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `categorie` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `dateCreation` date NOT NULL,
  `dateUpdate` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `statuPost` enum('non publie','publie') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `UserId` int DEFAULT NULL,
  PRIMARY KEY (`idPost`),
  KEY `fk_Post_User_idx` (`UserId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`idPost`, `title`, `description`, `label`, `imagePost`, `categorie`, `dateCreation`, `dateUpdate`, `statuPost`, `UserId`) VALUES
(1, '1er post test ', 'description post', 'test, exemple', 'titre.png', 'exemple', '2020-09-02', '', 'publie', 1);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `idUser` int NOT NULL AUTO_INCREMENT,
  `userName` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `activated` tinyint(1) DEFAULT NULL,
  `validationKey` tinyint(1) DEFAULT NULL,
  `userType` enum('Admin','Abonnee') DEFAULT NULL,
  `dateCreation` datetime DEFAULT NULL,
  PRIMARY KEY (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`idUser`, `userName`, `email`, `password`, `activated`, `validationKey`, `userType`, `dateCreation`) VALUES
(1, 'Fatellim', 'millet.marcalban@gmail.com', '$2y$10$xPiAWebsv7tIZ.VjN8owYeezde4Wh2/kAY6smIziMjCXh4oBjlGZu', 1, 1, 'Admin', '2020-07-13 09:13:07');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `fk_Comment_Post1` FOREIGN KEY (`PostId`) REFERENCES `post` (`idPost`),
  ADD CONSTRAINT `fk_Comment_User1` FOREIGN KEY (`UserId`) REFERENCES `user` (`idUser`);

--
-- Contraintes pour la table `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `fk_Post_User` FOREIGN KEY (`UserId`) REFERENCES `user` (`idUser`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
