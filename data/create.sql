-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : lun. 01 fév. 2021 à 22:10
-- Version du serveur :  8.0.23-0ubuntu0.20.04.1
-- Version de PHP : 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `fp`
--

-- --------------------------------------------------------

--
-- Structure de la table `championship`
--

CREATE TABLE `championship` (
  `id_championship` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `criterion`
--

CREATE TABLE `criterion` (
  `id_criterion` int NOT NULL,
  `id_matchgame` int NOT NULL,
  `motivation1` int NOT NULL DEFAULT '0',
  `currentForm1` int NOT NULL DEFAULT '0',
  `physicalForm1` int NOT NULL DEFAULT '0',
  `weather1` int NOT NULL DEFAULT '0',
  `bestPlayers1` int NOT NULL DEFAULT '0',
  `marketValue1` int NOT NULL DEFAULT '0',
  `home_away1` int NOT NULL DEFAULT '0',
  `motivation2` int NOT NULL DEFAULT '0',
  `currentForm2` int NOT NULL DEFAULT '0',
  `physicalForm2` int NOT NULL DEFAULT '0',
  `weather2` int NOT NULL DEFAULT '0',
  `bestPlayers2` int NOT NULL DEFAULT '0',
  `marketValue2` int NOT NULL DEFAULT '0',
  `home_away2` int NOT NULL DEFAULT '0',
  `trend1` int NOT NULL DEFAULT '0',
  `trend2` int NOT NULL DEFAULT '0',
  `histo1` int NOT NULL DEFAULT '0',
  `histoD` int NOT NULL DEFAULT '0',
  `histo2` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `fp_preferences`
--

CREATE TABLE `fp_preferences` (
  `id_fp_preferences` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `fp_preferences`
--

INSERT INTO `fp_preferences` (`id_fp_preferences`, `name`) VALUES
(1, 'FootballPronos');

-- --------------------------------------------------------

--
-- Structure de la table `fp_role`
--

CREATE TABLE `fp_role` (
  `id_fp_role` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `fp_role`
--

INSERT INTO `fp_role` (`id_fp_role`, `name`) VALUES
(1, 'contributor'),
(2, 'administrator');

-- --------------------------------------------------------

--
-- Structure de la table `fp_theme`
--

CREATE TABLE `fp_theme` (
  `id_fp_theme` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `directory_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `fp_theme`
--

INSERT INTO `fp_theme` (`id_fp_theme`, `name`, `directory_name`) VALUES
(1, 'Football predictions', 'default');

-- --------------------------------------------------------

--
-- Structure de la table `fp_user`
--

CREATE TABLE `fp_user` (
  `id_fp_user` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `registration` date NOT NULL,
  `language` varchar(5) NOT NULL DEFAULT 'fr_FR',
  `theme` int NOT NULL DEFAULT '1',
  `last_season` int DEFAULT NULL,
  `last_championship` int DEFAULT NULL,
  `role` int NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `marketValue`
--

CREATE TABLE `marketValue` (
  `id_marketValue` int NOT NULL,
  `id_season` int NOT NULL,
  `id_team` int NOT NULL,
  `marketValue` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `matchday`
--

CREATE TABLE `matchday` (
  `id_matchday` int NOT NULL,
  `id_season` int NOT NULL,
  `id_championship` int NOT NULL,
  `number` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `matchgame`
--

CREATE TABLE `matchgame` (
  `id_matchgame` int NOT NULL,
  `id_matchday` int NOT NULL,
  `team_1` int NOT NULL,
  `team_2` int NOT NULL,
  `result` enum('1','D','2') DEFAULT NULL,
  `odds1` float DEFAULT NULL,
  `oddsD` float DEFAULT NULL,
  `odds2` float DEFAULT NULL,
  `date` date DEFAULT NULL,
  `red1` int NOT NULL,
  `red2` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `player`
--

CREATE TABLE `player` (
  `id_player` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `position` enum('Goalkeeper','Defender','Midfielder','Forward') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `plugin`
--

CREATE TABLE `plugin` (
  `id_plugin` int NOT NULL,
  `plugin_name` varchar(50) NOT NULL,
  `plugin_desc` tinytext NOT NULL,
  `activate` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `plugin_meteo_concept_preferences`
--

INSERT INTO `plugin` (`id_plugin`, `plugin_name`, `plugin_desc`, `activate`) VALUES
(1, 'meteo-concept', 'Weather plugin for Meteo-Concept API', 1);

-- --------------------------------------------------------

--
-- Structure de la table `plugin_meteo_concept_preferences`
--

CREATE TABLE `plugin_meteo_concept_preferences` (
  `id_plugin_meteo_concept_preferences` int NOT NULL,
  `url` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `token` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `plugin_meteo_concept_preferences`
--

INSERT INTO `plugin_meteo_concept_preferences` (`id_plugin_meteo_concept_preferences`, `url`, `token`) VALUES
(1, 'https://api.meteo-concept.com/api/forecast/daily/', '');

-- --------------------------------------------------------

--
-- Structure de la table `plugin_meteo_concept_team`
--

CREATE TABLE `plugin_meteo_concept_team` (
  `id_plugin_meteo_concept_team` int NOT NULL,
  `id_team` int NOT NULL,
  `weather_code` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `season`
--

CREATE TABLE `season` (
  `id_season` int NOT NULL,
  `name` varchar(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `season_championship_team`
--

CREATE TABLE `season_championship_team` (
  `id_season_championship_team` int NOT NULL,
  `id_season` int NOT NULL,
  `id_championship` int NOT NULL,
  `id_team` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `season_team_player`
--

CREATE TABLE `season_team_player` (
  `id_season_team_player` int NOT NULL,
  `id_season` int NOT NULL,
  `id_team` int NOT NULL,
  `id_player` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `team`
--

CREATE TABLE `team` (
  `id_team` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `weather_code` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `teamOfTheWeek`
--

CREATE TABLE `teamOfTheWeek` (
  `id_teamOfTheWeek` int NOT NULL,
  `id_matchday` int NOT NULL,
  `id_player` int NOT NULL,
  `rating` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `championship`
--
ALTER TABLE `championship`
  ADD PRIMARY KEY (`id_championship`);

--
-- Index pour la table `criterion`
--
ALTER TABLE `criterion`
  ADD PRIMARY KEY (`id_criterion`),
  ADD KEY `C_Match` (`id_matchgame`);

--
-- Index pour la table `fp_preferences`
--
ALTER TABLE `fp_preferences`
  ADD PRIMARY KEY (`id_fp_preferences`);

--
-- Index pour la table `fp_theme`
--
ALTER TABLE `fp_theme`
  ADD PRIMARY KEY (`id_fp_theme`);

--
-- Index pour la table `fp_user`
--
ALTER TABLE `fp_user`
  ADD PRIMARY KEY (`id_fp_user`),
  ADD KEY `FPU_theme` (`theme`);

--
-- Index pour la table `marketValue`
--
ALTER TABLE `marketValue`
  ADD PRIMARY KEY (`id_marketValue`),
  ADD KEY `MV_Season` (`id_season`),
  ADD KEY `MV_Team` (`id_team`);

--
-- Index pour la table `matchday`
--
ALTER TABLE `matchday`
  ADD PRIMARY KEY (`id_matchday`),
  ADD KEY `MD_Season` (`id_season`),
  ADD KEY `MD_Championship` (`id_championship`);

--
-- Index pour la table `matchgame`
--
ALTER TABLE `matchgame`
  ADD PRIMARY KEY (`id_matchgame`),
  ADD KEY `MG_Team1` (`team_1`),
  ADD KEY `MG_Team2` (`team_2`),
  ADD KEY `MG_Matchday` (`id_matchday`);

--
-- Index pour la table `player`
--
ALTER TABLE `player`
  ADD PRIMARY KEY (`id_player`);

--
-- Index pour la table `plugin`
--
ALTER TABLE `plugin`
  ADD PRIMARY KEY (`id_plugin`);

--
-- Index pour la table `plugin_meteo_concept_preferences`
--
ALTER TABLE `plugin_meteo_concept_preferences`
  ADD PRIMARY KEY (`id_plugin_meteo_concept_preferences`);

--
-- Index pour la table `plugin_meteo_concept_team`
--
ALTER TABLE `plugin_meteo_concept_team`
  ADD PRIMARY KEY (`id_plugin_meteo_concept_team`);

--
-- Index pour la table `season`
--
ALTER TABLE `season`
  ADD PRIMARY KEY (`id_season`);

--
-- Index pour la table `season_championship_team`
--
ALTER TABLE `season_championship_team`
  ADD PRIMARY KEY (`id_season_championship_team`),
  ADD KEY `SCT_Season` (`id_season`),
  ADD KEY `SCT_Championship` (`id_championship`),
  ADD KEY `SCT_Team` (`id_team`);

--
-- Index pour la table `season_team_player`
--
ALTER TABLE `season_team_player`
  ADD PRIMARY KEY (`id_season_team_player`),
  ADD KEY `STP_Season` (`id_season`),
  ADD KEY `STP_Team` (`id_team`),
  ADD KEY `STP_Player` (`id_player`);

--
-- Index pour la table `team`
--
ALTER TABLE `team`
  ADD PRIMARY KEY (`id_team`);

--
-- Index pour la table `teamOfTheWeek`
--
ALTER TABLE `teamOfTheWeek`
  ADD PRIMARY KEY (`id_teamOfTheWeek`),
  ADD KEY `TOTW_Player` (`id_player`),
  ADD KEY `TOTW_Matchday` (`id_matchday`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `championship`
--
ALTER TABLE `championship`
  MODIFY `id_championship` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `criterion`
--
ALTER TABLE `criterion`
  MODIFY `id_criterion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `fp_preferences`
--
ALTER TABLE `fp_preferences`
  MODIFY `id_fp_preferences` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `fp_theme`
--
ALTER TABLE `fp_theme`
  MODIFY `id_fp_theme` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `fp_user`
--
ALTER TABLE `fp_user`
  MODIFY `id_fp_user` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `marketValue`
--
ALTER TABLE `marketValue`
  MODIFY `id_marketValue` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `matchday`
--
ALTER TABLE `matchday`
  MODIFY `id_matchday` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `matchgame`
--
ALTER TABLE `matchgame`
  MODIFY `id_matchgame` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `player`
--
ALTER TABLE `player`
  MODIFY `id_player` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `plugin`
--
ALTER TABLE `plugin`
  MODIFY `id_plugin` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `plugin_meteo_concept_preferences`
--
ALTER TABLE `plugin_meteo_concept_preferences`
  MODIFY `id_plugin_meteo_concept_preferences` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `plugin_meteo_concept_team`
--
ALTER TABLE `plugin_meteo_concept_team`
  MODIFY `id_plugin_meteo_concept_team` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `season`
--
ALTER TABLE `season`
  MODIFY `id_season` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `season_championship_team`
--
ALTER TABLE `season_championship_team`
  MODIFY `id_season_championship_team` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `season_team_player`
--
ALTER TABLE `season_team_player`
  MODIFY `id_season_team_player` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `team`
--
ALTER TABLE `team`
  MODIFY `id_team` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `teamOfTheWeek`
--
ALTER TABLE `teamOfTheWeek`
  MODIFY `id_teamOfTheWeek` int NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `criterion`
--
ALTER TABLE `criterion`
  ADD CONSTRAINT `C_Match` FOREIGN KEY (`id_matchgame`) REFERENCES `matchgame` (`id_matchgame`);

--
-- Contraintes pour la table `fp_user`
--
ALTER TABLE `fp_user`
  ADD CONSTRAINT `FPU_theme` FOREIGN KEY (`theme`) REFERENCES `fp_theme` (`id_fp_theme`);

--
-- Contraintes pour la table `marketValue`
--
ALTER TABLE `marketValue`
  ADD CONSTRAINT `MV_Season` FOREIGN KEY (`id_season`) REFERENCES `season` (`id_season`),
  ADD CONSTRAINT `MV_Team` FOREIGN KEY (`id_team`) REFERENCES `team` (`id_team`);

--
-- Contraintes pour la table `matchday`
--
ALTER TABLE `matchday`
  ADD CONSTRAINT `MD_Championship` FOREIGN KEY (`id_championship`) REFERENCES `championship` (`id_championship`),
  ADD CONSTRAINT `MD_Season` FOREIGN KEY (`id_season`) REFERENCES `season` (`id_season`);

--
-- Contraintes pour la table `matchgame`
--
ALTER TABLE `matchgame`
  ADD CONSTRAINT `MG_Matchday` FOREIGN KEY (`id_matchday`) REFERENCES `matchday` (`id_matchday`),
  ADD CONSTRAINT `MG_Team1` FOREIGN KEY (`team_1`) REFERENCES `team` (`id_team`),
  ADD CONSTRAINT `MG_Team2` FOREIGN KEY (`team_2`) REFERENCES `team` (`id_team`);

--
-- Contraintes pour la table `season_championship_team`
--
ALTER TABLE `season_championship_team`
  ADD CONSTRAINT `SCT_Championship` FOREIGN KEY (`id_championship`) REFERENCES `championship` (`id_championship`),
  ADD CONSTRAINT `SCT_Season` FOREIGN KEY (`id_season`) REFERENCES `season` (`id_season`),
  ADD CONSTRAINT `SCT_Team` FOREIGN KEY (`id_team`) REFERENCES `team` (`id_team`);

--
-- Contraintes pour la table `season_team_player`
--
ALTER TABLE `season_team_player`
  ADD CONSTRAINT `STP_Player` FOREIGN KEY (`id_player`) REFERENCES `player` (`id_player`),
  ADD CONSTRAINT `STP_Season` FOREIGN KEY (`id_season`) REFERENCES `season` (`id_season`),
  ADD CONSTRAINT `STP_Team` FOREIGN KEY (`id_team`) REFERENCES `team` (`id_team`);

--
-- Contraintes pour la table `teamOfTheWeek`
--
ALTER TABLE `teamOfTheWeek`
  ADD CONSTRAINT `TOTW_Matchday` FOREIGN KEY (`id_matchday`) REFERENCES `matchday` (`id_matchday`),
  ADD CONSTRAINT `TOTW_Player` FOREIGN KEY (`id_player`) REFERENCES `player` (`id_player`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
