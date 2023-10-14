-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 08 sep. 2023 à 15:21
-- Version du serveur : 8.0.32
-- Version de PHP : 8.2.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `witchcase`
--

-- --------------------------------------------------------

--
-- Structure de la table `archive__wc-user`
--

CREATE TABLE `archive__wc-user` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `last-name@string#value` varchar(511) DEFAULT NULL,
  `first-name@string#value` varchar(511) DEFAULT NULL,
  `connection@connexion#id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `content__wc-user`
--

CREATE TABLE `content__wc-user` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last-name@string#value` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first-name@string#value` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `connection@connexion#id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `content__wc-user`
--

INSERT INTO `content__wc-user` (`id`, `name`, `last-name@string#value`, `first-name@string#value`, `connection@connexion#id`) VALUES
(1, 'Administrateur', 'Witchcase', 'Administrateur', 1);

-- --------------------------------------------------------

--
-- Structure de la table `draft__wc-user`
--

CREATE TABLE `draft__wc-user` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `last-name@string#value` varchar(511) DEFAULT NULL,
  `first-name@string#value` varchar(511) DEFAULT NULL,
  `connection@connexion#id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user__connexion`
--

CREATE TABLE `user__connexion` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL COMMENT 'user signature',
  `email` varchar(511) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `pass_hash` varchar(255) DEFAULT NULL,
  `craft_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `craft_attribute` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'connexion',
  `craft_attribute_var` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'fk_user__connexion',
  `attribute_name` varchar(511) DEFAULT NULL,
  `modifier` int DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `creator` int DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user__connexion`
--

INSERT INTO `user__connexion` (`id`, `name`, `email`, `login`, `pass_hash`, `craft_table`, `craft_attribute`, `craft_attribute_var`, `attribute_name`) VALUES
(1, 'Administrator', 'adminstrator@witchcase', 'admin', '$2y$11$11FgVhXijP654xVeVG/VjeKIQnyRjVx0AsQ2QGQXiEx0VJeWeaGJ.', 'content__wc-user', 'connexion', 'id', 'connection');

-- --------------------------------------------------------

--
-- Structure de la table `user__policy`
--

CREATE TABLE `user__policy` (
  `id` int UNSIGNED NOT NULL,
  `fk_profile` int UNSIGNED DEFAULT NULL,
  `module` varchar(255) NOT NULL DEFAULT 'view',
  `status` int UNSIGNED DEFAULT NULL,
  `fk_witch` int UNSIGNED DEFAULT NULL,
  `position_ancestors` tinyint(1) NOT NULL DEFAULT '0',
  `position_included` tinyint(1) NOT NULL DEFAULT '1',
  `position_descendants` tinyint(1) NOT NULL DEFAULT '1',
  `custom_limitation` varchar(31) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user__policy`
--

INSERT INTO `user__policy` (`id`, `fk_profile`, `module`, `status`, `fk_witch`, `position_ancestors`, `position_included`, `position_descendants`, `custom_limitation`) VALUES
(1, 1, '*', NULL, NULL, 0, 0, 0, ''),
(2, 2, '404', NULL, NULL, 0, 0, 0, ''),
(3, 2, '403', NULL, NULL, 0, 0, 0, ''),
(4, 2, 'login', NULL, NULL, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Structure de la table `user__profile`
--

CREATE TABLE `user__profile` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `site` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '*',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user__profile`
--

INSERT INTO `user__profile` (`id`, `name`, `site`) VALUES
(1, 'administrator', '*'),
(2, 'public', '*');

-- --------------------------------------------------------

--
-- Structure de la table `user__rel__connexion__profile`
--

CREATE TABLE `user__rel__connexion__profile` (
  `fk_connexion` int NOT NULL,
  `fk_profile` int NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `user__rel__connexion__profile`
--

INSERT INTO `user__rel__connexion__profile` (`fk_connexion`, `fk_profile`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `witch`
--

CREATE TABLE `witch` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `data` text,
  `site` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `url` varchar(1023) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `status` int UNSIGNED NOT NULL DEFAULT '0',
  `invoke` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `craft_table` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `craft_fk` int UNSIGNED DEFAULT NULL,
  `alias` int DEFAULT NULL,
  `is_main` int UNSIGNED NOT NULL DEFAULT '1',
  `context` varchar(255) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `priority` int NOT NULL DEFAULT '0',
  `level_1` int UNSIGNED DEFAULT NULL,
  `level_2` int UNSIGNED DEFAULT NULL,
  `level_3` int UNSIGNED DEFAULT NULL,
  `level_4` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
-- Déchargement des données de la table `witch`
--

INSERT INTO `witch` (`id`, `name`, `data`, `site`, `url`, `status`, `invoke`, `craft_table`, `craft_fk`, `alias`, `is_main`, `context`, `priority`, `level_1`, `level_2`, `level_3`, `level_4`) VALUES
(1, 'Root', 'Ici se trouve la racine de la plateforme. C\'est à partir d\'ici que sont créées les homes de chaque site de la plateforme.', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL,  0, NULL, NULL, NULL, NULL),
(2, 'Admin WitchCase', 'Site d\'administration', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL,  0, 1, NULL, NULL, NULL),
(3, 'Utilisateurs', '', 'admin', 'utilisateurs', 0, '', NULL, NULL, NULL, 1, NULL, 10, 1, 1, NULL, NULL),
(4, 'Administrateur', '', 'admin', 'utilisateurs/administrateur', 0, '', 'content__wc-user', 1, NULL, 1, '',  0, 1, 1, 1, NULL),
(5, 'Home', '', 'admin', '', 0, 'root', NULL, NULL, NULL, 1, NULL,  0, 1, 2, NULL, NULL),
(6, 'Login', 'Module de déconnexion/connexion', 'admin', 'login', 0, 'login', NULL, NULL, NULL, 1, NULL, 40, 1, 2, 1, NULL),
(7, 'Witch', 'Visualisation des Witches, c\'est a dire de chaque point de l\'arborescence -appelé ici Matriarcat. Chacun de ces points peut être associé à un contenu et/ou à un module exécutable. \r\nOn peut également définir une URL permettant de cibler cette witch.', 'admin', 'view', 0, 'view', NULL, NULL, NULL, 1, NULL, 30, 1, 2, 2, NULL),
(8, 'Edit Witch', '', 'admin', 'edit', 0, 'edit', NULL, NULL, NULL, 1, NULL,  20, 1, 2, 3, NULL),
(9, 'Edit Craft', 'This is the draft of craft, you can publish it, save it for later, or remove draft to cancel modification.', 'admin', 'edit-content', 0, 'contents/edit', NULL, NULL, NULL, 1, NULL, 10, 1, 2, 4, NULL),
(10, 'Menu', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL,  0, 1, 2, 5, NULL),
(11, 'Profiles', 'Permissions handeling is based on user profiles.', 'admin', 'profiles', 0, 'profiles', NULL, NULL, NULL, 1, NULL,  0, 1, 2, 5, 1),
(12, 'Structures', '', 'admin', 'structures', 0, 'structures', NULL, NULL, NULL, 1, NULL, 0, 1, 2, 5, 2);


--
-- Index pour les tables déchargées
--

--
-- Index pour la table `archive__wc-user`
--
ALTER TABLE `archive__wc-user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__wc-user`
--
ALTER TABLE `content__wc-user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__wc-user`
--
ALTER TABLE `draft__wc-user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user__connexion`
--
ALTER TABLE `user__connexion`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user__policy`
--
ALTER TABLE `user__policy`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user__profile`
--
ALTER TABLE `user__profile`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user__rel__connexion__profile`
--
ALTER TABLE `user__rel__connexion__profile`
  ADD PRIMARY KEY (`fk_connexion`,`fk_profile`);

--
-- Index pour la table `witch`
--
ALTER TABLE `witch`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_level_1` (`level_1`),
  ADD KEY `IDX_level_2` (`level_2`),
  ADD KEY `IDX_level_3` (`level_3`),
  ADD KEY `IDX_level_4` (`level_4`);

--
-- AUTO_INCREMENT pour les tables déchargées
--
--
-- AUTO_INCREMENT pour la table `content__wc-user`
--
ALTER TABLE `content__wc-user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `user__connexion`
--
ALTER TABLE `user__connexion`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `user__policy`
--
ALTER TABLE `user__policy`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user__profile`
--
ALTER TABLE `user__profile`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `witch`
--
ALTER TABLE `witch`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
