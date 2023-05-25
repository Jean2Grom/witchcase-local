-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 25 mai 2023 à 16:13
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
-- Base de données : `witchcase_local`
--
CREATE DATABASE IF NOT EXISTS `witchcase_local` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `witchcase_local`;

-- --------------------------------------------------------

--
-- Structure de la table `archive__folder-demo`
--

CREATE TABLE `archive__folder-demo` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `headline@string#value` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `nouvel-attribut-datetime@datetime#value` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `archive__home-demo`
--

CREATE TABLE `archive__home-demo` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `meta-title@string#value` varchar(511) DEFAULT NULL,
  `meta-description@string#value` varchar(511) DEFAULT NULL,
  `meta-keywords@text#value` text,
  `headline@string#value` varchar(511) DEFAULT NULL,
  `logo@image#file` varchar(511) DEFAULT NULL,
  `logo@image#title` varchar(511) DEFAULT NULL,
  `contact-email@link#href` varchar(511) DEFAULT NULL,
  `contact-email@link#text` varchar(511) DEFAULT NULL,
  `contact-email@link#external` tinyint(1) DEFAULT '1',
  `footer-left@string#value` varchar(511) DEFAULT NULL,
  `footer-right@string#value` varchar(511) DEFAULT NULL,
  `background-image@image#file` varchar(511) DEFAULT NULL,
  `background-image@image#title` varchar(511) DEFAULT NULL,
  `body@text#value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `archive__user`
--

CREATE TABLE `archive__user` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `content__folder-demo`
--

CREATE TABLE `content__folder-demo` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `headline@string#value` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `body@text#value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `nouvel-attribut-datetime@datetime#value` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `content__folder-demo`
--

INSERT INTO `content__folder-demo` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `headline@string#value`, `body@text#value`, `nouvel-attribut-datetime@datetime#value`) VALUES
(1, 'Rubrique 1', 1, '2016-05-03 13:00:09', 1, '2021-12-30 20:10:46', 'Rubrique 1', 'hohoho', NULL),
(2, 'Rubrique 2', 1, '2016-05-03 13:02:05', 1, '2016-05-03 13:02:05', 'Rubrique 2', '', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `content__home-demo`
--

CREATE TABLE `content__home-demo` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `meta-title@string#value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `meta-description@string#value` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `meta-keywords@text#value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `headline@string#value` varchar(511) DEFAULT NULL,
  `logo@image#file` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `logo@image#title` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `logo@image#link` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `contact-email@link#href` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `contact-email@link#text` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `contact-email@link#external` tinyint(1) DEFAULT '1',
  `footer-left@string#value` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `footer-right@string#value` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `background-image@image#file` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `background-image@image#title` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `background-image@image#link` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `body@text#value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `content__home-demo`
--

INSERT INTO `content__home-demo` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `meta-title@string#value`, `meta-description@string#value`, `meta-keywords@text#value`, `headline@string#value`, `logo@image#file`, `logo@image#title`, `logo@image#link`, `contact-email@link#href`, `contact-email@link#text`, `contact-email@link#external`, `footer-left@string#value`, `footer-right@string#value`, `background-image@image#file`, `background-image@image#title`, `background-image@image#link`, `body@text#value`) VALUES
(1, 'Site de démonstration', 1, '2016-05-03 12:46:25', 1, '2021-12-11 15:52:53', 'Woody CMS | Site de démonstration', 'Witch Case société d\'édition web open source productrice de Woody CMS', '', 'Site de démonstration', 'Bibliotheque.jpg', 'Logo Woody CMS', '', 'mailto:admin@witchcase.com', 'admin@witch-case.com', 1, 'Site réalisé avec Woody CMS', '@Witch case 2016. All Right Reserved', 'print-button.png', 'Woody CMS', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `content__user`
--

CREATE TABLE `content__user` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last-name@string#value` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `first-name@string#value` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `connection@connexion#id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `content__user`
--

INSERT INTO `content__user` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `last-name@string#value`, `first-name@string#value`, `connection@connexion#id`) VALUES
(1, 'Administrateur', 1, '2016-02-04 19:26:12', 9, '2021-12-14 19:39:05', 'Witchcase', 'Administrateur', 1);

-- --------------------------------------------------------

--
-- Structure de la table `draft__folder-demo`
--

CREATE TABLE `draft__folder-demo` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `headline@string#value` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `nouvel-attribut-datetime@datetime#value` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `draft__home-demo`
--

CREATE TABLE `draft__home-demo` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `meta-title@string#value` varchar(511) DEFAULT NULL,
  `meta-description@string#value` varchar(511) DEFAULT NULL,
  `meta-keywords@text#value` text,
  `headline@string#value` varchar(511) DEFAULT NULL,
  `logo@image#file` varchar(511) DEFAULT NULL,
  `logo@image#title` varchar(511) DEFAULT NULL,
  `contact-email@link#href` varchar(511) DEFAULT NULL,
  `contact-email@link#text` varchar(511) DEFAULT NULL,
  `contact-email@link#external` tinyint(1) DEFAULT '1',
  `footer-left@string#value` varchar(511) DEFAULT NULL,
  `footer-right@string#value` varchar(511) DEFAULT NULL,
  `background-image@image#file` varchar(511) DEFAULT NULL,
  `background-image@image#title` varchar(511) DEFAULT NULL,
  `body@text#value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `draft__user`
--

CREATE TABLE `draft__user` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  `craft_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `craft_attribute` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'connexion',
  `craft_attribute_var` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'fk_user__connexion',
  `attribute_name` varchar(511) DEFAULT NULL,
  `modifier` int DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `creator` int DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user__connexion`
--

INSERT INTO `user__connexion` (`id`, `name`, `email`, `login`, `pass_hash`, `craft_table`, `craft_attribute`, `craft_attribute_var`, `attribute_name`, `modifier`, `modified`, `creator`, `created`) VALUES
(1, 'Administrator', 'admin@witch-case.com', 'admin', '$2y$11$11FgVhXijP654xVeVG/VjeKIQnyRjVx0AsQ2QGQXiEx0VJeWeaGJ.', 'content__user', 'connexion', 'id', 'connection', 1, '2023-05-25 17:58:56', NULL, '2021-07-15 13:19:41');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user__policy`
--

INSERT INTO `user__policy` (`id`, `fk_profile`, `module`, `status`, `fk_witch`, `position_ancestors`, `position_included`, `position_descendants`, `custom_limitation`) VALUES
(1, 1, '*', NULL, NULL, 0, 1, 1, NULL),
(92, 2, 'login', NULL, NULL, 0, 0, 0, NULL),
(93, 2, '404', NULL, NULL, 0, 0, 0, NULL),
(94, 2, '403', NULL, NULL, 0, 0, 0, NULL),
(95, 2, '*', 0, 78, 0, 0, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user__profile`
--

CREATE TABLE `user__profile` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `site` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '*',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user__profile`
--

INSERT INTO `user__profile` (`id`, `name`, `site`, `created`) VALUES
(1, 'administrator', '*', '2015-09-21 19:30:00'),
(2, 'public', '*', '2016-04-12 09:10:11');

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

INSERT INTO `user__rel__connexion__profile` (`fk_connexion`, `fk_profile`, `created`) VALUES
(1, 1, '2023-05-25 17:54:41'),
(1, 2, '2023-05-25 17:54:41');

-- --------------------------------------------------------

--
-- Structure de la table `witch`
--

CREATE TABLE `witch` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `data` text,
  `site` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `url` varchar(1023) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT '/',
  `status` int UNSIGNED NOT NULL DEFAULT '0',
  `invoke` varchar(511) NOT NULL DEFAULT 'view',
  `craft_table` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `craft_fk` int UNSIGNED DEFAULT NULL,
  `ext_id` varchar(32) DEFAULT NULL,
  `alias` int DEFAULT NULL,
  `is_main` int UNSIGNED NOT NULL DEFAULT '1',
  `context` varchar(255) DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `priority` int NOT NULL DEFAULT '0',
  `level_1` int UNSIGNED DEFAULT NULL,
  `level_2` int UNSIGNED DEFAULT NULL,
  `level_3` int UNSIGNED DEFAULT NULL,
  `level_4` int UNSIGNED DEFAULT NULL,
  `level_5` int UNSIGNED DEFAULT NULL,
  `level_6` int UNSIGNED DEFAULT NULL,
  `level_7` int UNSIGNED DEFAULT NULL,
  `level_8` int UNSIGNED DEFAULT NULL,
  `level_9` int UNSIGNED DEFAULT NULL,
  `level_10` int UNSIGNED DEFAULT NULL,
  `level_11` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `witch`
--

INSERT INTO `witch` (`id`, `name`, `data`, `site`, `url`, `status`, `invoke`, `craft_table`, `craft_fk`, `ext_id`, `alias`, `is_main`, `context`, `datetime`, `priority`, `level_1`, `level_2`, `level_3`, `level_4`, `level_5`, `level_6`, `level_7`, `level_8`, `level_9`, `level_10`, `level_11`) VALUES
(1, 'Root', 'Ici se trouve la racine de la plateforme. C\'est à partir d\'ici que sont créées les homes de chaque site de la plateforme', 'admin', '/', 0, 'root', NULL, NULL, '5b02d0443587a47f625aa3ce4970f50f', NULL, 1, '', '2015-07-01 16:36:41', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Utilisateurs', '', 'admin', '/utilisateurs', 0, '', NULL, NULL, '9dac789225beb25f38f3abba1312f83d', NULL, 1, NULL, '2016-02-02 16:40:49', 0, 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Administrateur', '', 'admin', '/utilisateurs/administrateur', 0, '', 'content__user', 1, '68a2092655ae4a277d1f1b4c74a972df', NULL, 1, '', '2016-02-04 19:26:12', 0, 11, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Modules admin', '', NULL, NULL, 0, '', NULL, NULL, NULL, NULL, 1, '', '2021-10-09 20:16:10', 0, 20, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'Site de démonstration', 'Ce site a pour but de vous montrer un exemple du fonctionnement de WitchCase.', 'site-demo', '/', 0, '', 'content__home-demo', 1, '23292293583afc89324de42dc49d975a', NULL, 1, '', '2016-05-03 12:46:25', 0, 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'Rubrique 1', 'c\'est la ruuubrique', 'site-demo', '/rubrique-1', 0, 'view', 'content__folder-demo', 1, '4a57224800d771f400986e26436e27f2', NULL, 1, NULL, '2016-05-03 13:00:09', 1, 19, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'Rubrique 2', '', 'site-demo', '/rubrique-2', 0, 'view', 'content__folder-demo', 2, '4e78cfccfbce2bc6f9e4d6927e8814b9', NULL, 1, NULL, '2016-05-03 13:02:05', 0, 19, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'Rubrique 01', 'c\'est la rubrique 1 sous la rubrique 2', 'site-demo', '/rubrique-2/rubrique-01', 0, '', 'content__folder-demo', 1, 'd4094b3853440fac1f772f131d89e168', NULL, 1, NULL, '2021-03-24 22:41:48', 0, 19, 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'login', '', 'admin-site-demo', '/login', 0, 'login', '', 0, '4bbea08a23d07e6c149e146153b77ffc', NULL, 1, NULL, '2016-02-05 11:18:05', 21, 19, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'Login', 'Module de déconnexion/connexion', 'admin', '/login', 0, 'login', NULL, NULL, NULL, NULL, 1, '', '2021-10-10 12:19:50', 10, 20, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'WITCH', 'Visualisation des Witches, c\'est a dire de chaque point de l\'arborescence -appelé ici Matriarcat. Chacun de ces points peut être associé à un contenu et/ou à un module exécutable. \r\nOn peut également définir une URL permettant de cibler cette witch.', 'admin', '/view', 0, 'view', NULL, NULL, NULL, NULL, 1, '', '2021-10-10 14:20:12', 50, 20, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'EDIT', 'Pour éditer un élément', 'admin', '/edit', 0, 'edit', NULL, NULL, NULL, NULL, 1, '', '2021-10-10 14:23:34', 30, 20, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'CREATE', 'Pour créer des éléments', 'admin', '/create', 0, 'create', NULL, NULL, NULL, NULL, 1, '', '2021-10-10 14:25:25', 40, 20, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 'Profiles', '', 'admin', '/profiles', 0, 'profiles/list', NULL, NULL, NULL, NULL, 1, '', '2021-11-01 23:37:42', 20, 20, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 'Create', '', 'admin', '/profiles/create', 0, 'profiles/create', NULL, NULL, NULL, NULL, 1, '', '2021-11-01 23:40:42', 0, 20, 6, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 'Edit', '', 'admin', '/profiles/edit', 0, 'profiles/edit', NULL, NULL, NULL, NULL, 1, '', '2021-11-02 07:46:34', 0, 20, 6, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 'CONTENT EDIT', 'Édition du contenu conformément à sa structure.', 'admin', '/edit-content', 0, 'contents/edit', NULL, NULL, NULL, NULL, 1, '', '2021-11-26 16:50:32', 0, 20, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 'Structures', '', 'admin', '/structures', 0, 'structures', NULL, NULL, NULL, NULL, 1, '', '2021-12-03 13:39:59', 0, 20, 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `archive__folder-demo`
--
ALTER TABLE `archive__folder-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__home-demo`
--
ALTER TABLE `archive__home-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__user`
--
ALTER TABLE `archive__user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__folder-demo`
--
ALTER TABLE `content__folder-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__home-demo`
--
ALTER TABLE `content__home-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__user`
--
ALTER TABLE `content__user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__folder-demo`
--
ALTER TABLE `draft__folder-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__home-demo`
--
ALTER TABLE `draft__home-demo`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__user`
--
ALTER TABLE `draft__user`
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
  ADD KEY `IDX_level_4` (`level_4`),
  ADD KEY `IDX_level_5` (`level_5`),
  ADD KEY `IDX_level_6` (`level_6`),
  ADD KEY `IDX_level_7` (`level_7`),
  ADD KEY `IDX_level_8` (`level_8`),
  ADD KEY `IDX_level_9` (`level_9`),
  ADD KEY `IDX_level_10` (`level_10`),
  ADD KEY `IDX_level_11` (`level_11`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `archive__folder-demo`
--
ALTER TABLE `archive__folder-demo`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `archive__home-demo`
--
ALTER TABLE `archive__home-demo`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `archive__user`
--
ALTER TABLE `archive__user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `content__folder-demo`
--
ALTER TABLE `content__folder-demo`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `content__home-demo`
--
ALTER TABLE `content__home-demo`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `content__user`
--
ALTER TABLE `content__user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `draft__folder-demo`
--
ALTER TABLE `draft__folder-demo`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `draft__home-demo`
--
ALTER TABLE `draft__home-demo`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `draft__user`
--
ALTER TABLE `draft__user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user__connexion`
--
ALTER TABLE `user__connexion`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT pour la table `user__policy`
--
ALTER TABLE `user__policy`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT pour la table `user__profile`
--
ALTER TABLE `user__profile`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `witch`
--
ALTER TABLE `witch`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=238;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
