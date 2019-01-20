-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Dim 20 Janvier 2019 à 15:02
-- Version du serveur :  5.7.11
-- Version de PHP :  7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `kontest`
--
CREATE DATABASE IF NOT EXISTS `kontest` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `kontest`;

-- --------------------------------------------------------

--
-- Structure de la table `contests`
--

CREATE TABLE `contests` (
  `contest_id` int(11) NOT NULL,
  `contest_name` varchar(255) NOT NULL,
  `contest_state` int(11) NOT NULL DEFAULT '1' COMMENT '0=closed / 1=open',
  `contest_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL,
  `news_date` date NOT NULL,
  `news_title` varchar(255) COLLATE utf8_bin NOT NULL,
  `news_content` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `scores`
--

CREATE TABLE `scores` (
  `score_userid` int(11) NOT NULL,
  `score_contestid` int(11) NOT NULL,
  `score_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_role` int(5) NOT NULL DEFAULT '25',
  `user_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_pic` varchar(200) NOT NULL DEFAULT 'default.jpg',
  `user_email` varchar(60) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`user_id`, `user_role`, `user_name`, `user_pic`, `user_email`, `user_pass`) VALUES
(1, 25, 'admin', 'default.jpg', 'admin@domain.tld', '$2y$10$CSVmCpLG1nDLGUA2nnxCLePJnxCAcdcOvhLjh7vtQYdUAG4DSNM.O');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `contests`
--
ALTER TABLE `contests`
  ADD PRIMARY KEY (`contest_id`);

--
-- Index pour la table `news`
--
ALTER TABLE `news`
  ADD KEY `news_id` (`news_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `contests`
--
ALTER TABLE `contests`
  MODIFY `contest_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT pour la table `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
