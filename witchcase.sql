-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : mer. 12 mars 2025 à 10:30
-- Version du serveur : 8.0.36
-- Version de PHP : 8.2.8

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
-- Structure de la table `archive__test`
--

CREATE TABLE `archive__test` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `nouvel-attribut-boolean__archive__archive@boolean#value` int DEFAULT NULL,
  `nouvel-attribut-integer__archive__archive@integer#value` int DEFAULT NULL,
  `nouvel-attribut-datetime__archive__archive@datetime#value` datetime DEFAULT NULL,
  `nouvel-attribut-string__archive__archive@string#value` varchar(511) DEFAULT NULL,
  `nouvel-attribut-text__archive__archive@text#value` text,
  `nouvel-attribut-decimal__archive__archive@decimal#value` decimal(10,2) DEFAULT NULL,
  `nouvel-attribut-link@link#href` varchar(511) DEFAULT NULL,
  `nouvel-attribut-link@link#text` varchar(511) DEFAULT NULL,
  `nouvel-attribut-link@link#external` tinyint(1) DEFAULT '1',
  `nouvel-attribut-file@file#file` varchar(511) DEFAULT NULL,
  `nouvel-attribut-file@file#title` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `archive__wc-user`
--

CREATE TABLE `archive__wc-user` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `last-name@string#value` varchar(511) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first-name@string#value` varchar(511) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `connection@connexion#id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cauldron`
--

CREATE TABLE `cauldron` (
  `id` int UNSIGNED NOT NULL,
  `target` int UNSIGNED DEFAULT NULL,
  `status` bit(1) DEFAULT NULL COMMENT 'Null for content, 0 for draft, 1 for archive',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `recipe` varchar(128) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'folder',
  `data` json DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '0',
  `creator` int UNSIGNED DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modificator` int UNSIGNED DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `level_1` int DEFAULT NULL,
  `level_2` int UNSIGNED DEFAULT NULL,
  `level_3` int UNSIGNED DEFAULT NULL,
  `level_4` int UNSIGNED DEFAULT NULL,
  `level_5` int UNSIGNED DEFAULT NULL,
  `level_6` int UNSIGNED DEFAULT NULL,
  `level_7` int UNSIGNED DEFAULT NULL,
  `level_8` int UNSIGNED DEFAULT NULL,
  `level_9` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cauldron`
--

INSERT INTO `cauldron` (`id`, `target`, `status`, `name`, `recipe`, `data`, `priority`, `creator`, `created`, `modificator`, `modified`, `level_1`, `level_2`, `level_3`, `level_4`, `level_5`, `level_6`, `level_7`, `level_8`, `level_9`) VALUES
(1, NULL, NULL, 'ROOT', 'root', NULL, 0, NULL, '2024-06-11 15:42:06', NULL, '2024-11-27 15:08:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, NULL, NULL, 'admin', 'wc-site-folder', NULL, 0, NULL, '2024-06-11 15:42:06', NULL, '2024-11-27 16:13:42', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, NULL, NULL, 'wc-user', 'wc-recipe-folder', NULL, 0, NULL, '2024-06-11 15:42:06', NULL, '2024-11-27 16:15:12', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, NULL, NULL, 'Administrateur', 'wc-user', NULL, 0, NULL, '2024-06-11 15:42:06', NULL, '2025-01-08 15:53:14', 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(5, NULL, NULL, 'profiles', 'folder', NULL, 0, NULL, '2024-06-11 15:42:06', NULL, '2025-01-08 15:53:30', 1, 1, 1, 1, NULL, NULL, NULL, NULL, NULL),
(7, NULL, NULL, 'Jean', 'wc-user', NULL, 0, NULL, '2024-06-11 15:42:06', NULL, '2025-01-08 15:53:44', 1, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(8, NULL, NULL, 'profiles', 'folder', NULL, 0, NULL, '2024-06-11 15:42:06', NULL, '2025-01-28 10:03:52', 1, 1, 2, 2, NULL, NULL, NULL, NULL, NULL),
(149, NULL, NULL, 'folder', 'wc-recipe-folder', NULL, 0, NULL, '2024-10-08 15:37:41', NULL, '2024-11-27 16:15:09', 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(408, NULL, NULL, 'identifier', 'wc-recipe-folder', NULL, 0, NULL, '2025-01-10 16:21:37', NULL, '2025-01-10 16:21:37', 1, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(438, NULL, NULL, 'test 01', 'folder', NULL, 0, NULL, '2025-01-22 19:21:25', NULL, '2025-02-25 15:41:57', 1, 2, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(770, NULL, NULL, 'link', 'wc-recipe-folder', NULL, 0, NULL, '2025-03-06 17:17:29', NULL, '2025-03-06 17:17:29', 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(773, NULL, NULL, 'wc-file', 'wc-recipe-folder', NULL, 0, NULL, '2025-03-10 09:19:15', NULL, '2025-03-10 09:19:15', 1, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(790, NULL, NULL, 'DRAFTS', 'wc-drafts-folder', NULL, 0, NULL, '2025-03-11 14:18:10', NULL, '2025-03-11 14:18:10', 1, 2, 1, 1, NULL, NULL, NULL, NULL, NULL),
(792, NULL, b'0', 'aaaa 2', 'folder', NULL, 0, NULL, '2025-03-11 17:42:23', NULL, '2025-03-11 17:42:23', 1, 2, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(793, NULL, NULL, 'test', 'image', NULL, 100, NULL, '2025-03-11 17:43:06', NULL, '2025-03-11 17:43:06', 1, 2, 2, 1, NULL, NULL, NULL, NULL, NULL),
(794, NULL, NULL, 'file', 'wc-file', NULL, 200, NULL, '2025-03-11 17:43:07', NULL, '2025-03-11 17:43:07', 1, 2, 2, 1, 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `content__test`
--

CREATE TABLE `content__test` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nouvel-attribut-link@link#href` varchar(511) DEFAULT NULL,
  `nouvel-attribut-link@link#text` varchar(511) DEFAULT NULL,
  `nouvel-attribut-link@link#external` tinyint(1) DEFAULT '1',
  `nouvel-attribut-file@file#file` varchar(511) DEFAULT NULL,
  `nouvel-attribut-file@file#title` varchar(511) DEFAULT NULL,
  `nouvel-attribut-image@image#file` varchar(511) DEFAULT NULL,
  `nouvel-attribut-image@image#title` varchar(511) DEFAULT NULL,
  `nouvel-attribut-connexion@connexion#id` int DEFAULT NULL,
  `nouvel-attribut-datetime@datetime#value` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `content__wc-user`
--

CREATE TABLE `content__wc-user` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
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

INSERT INTO `content__wc-user` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `last-name@string#value`, `first-name@string#value`, `connection@connexion#id`) VALUES
(1, 'Administrateur', NULL, '2024-03-01 15:46:01', NULL, '2024-03-01 15:46:01', 'Witchcase', 'Administrateur', 1);

-- --------------------------------------------------------

--
-- Structure de la table `draft__test`
--

CREATE TABLE `draft__test` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `nouvel-attribut-link@link#href` varchar(511) DEFAULT NULL,
  `nouvel-attribut-link@link#text` varchar(511) DEFAULT NULL,
  `nouvel-attribut-link@link#external` tinyint(1) DEFAULT '1',
  `nouvel-attribut-file@file#file` varchar(511) DEFAULT NULL,
  `nouvel-attribut-file@file#title` varchar(511) DEFAULT NULL,
  `nouvel-attribut-image@image#file` varchar(511) DEFAULT NULL,
  `nouvel-attribut-image@image#title` varchar(511) DEFAULT NULL,
  `nouvel-attribut-connexion@connexion#id` int DEFAULT NULL,
  `nouvel-attribut-datetime@datetime#value` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `draft__test`
--

INSERT INTO `draft__test` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `content_key`, `nouvel-attribut-link@link#href`, `nouvel-attribut-link@link#text`, `nouvel-attribut-link@link#external`, `nouvel-attribut-file@file#file`, `nouvel-attribut-file@file#title`, `nouvel-attribut-image@image#file`, `nouvel-attribut-image@image#title`, `nouvel-attribut-connexion@connexion#id`, `nouvel-attribut-datetime@datetime#value`) VALUES
(2, 'test', 1, '2024-05-14 11:57:56', 1, '2024-05-14 11:57:56', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `draft__wc-user`
--

CREATE TABLE `draft__wc-user` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `last-name@string#value` varchar(511) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first-name@string#value` varchar(511) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `connection@connexion#id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `draft__wc-user`
--

INSERT INTO `draft__wc-user` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `content_key`, `last-name@string#value`, `first-name@string#value`, `connection@connexion#id`) VALUES
(1, 'Administrateur', 1, '2024-04-04 14:58:34', 1, '2024-04-04 14:58:34', 1, 'Witchcase', 'Administrateur', 3);

-- --------------------------------------------------------

--
-- Structure de la table `ingredient__boolean`
--

CREATE TABLE `ingredient__boolean` (
  `id` int UNSIGNED NOT NULL,
  `cauldron_fk` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `value` bit(1) DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ingredient__boolean`
--

INSERT INTO `ingredient__boolean` (`id`, `cauldron_fk`, `name`, `value`, `priority`) VALUES
(1, 8, 'test_boolean', b'1', 0),
(58, 168, 'test bool', b'1', 100),
(65, 438, 'check', b'0', 600);

-- --------------------------------------------------------

--
-- Structure de la table `ingredient__datetime`
--

CREATE TABLE `ingredient__datetime` (
  `id` int UNSIGNED NOT NULL,
  `cauldron_fk` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `value` datetime DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ingredient__datetime`
--

INSERT INTO `ingredient__datetime` (`id`, `cauldron_fk`, `name`, `value`, `priority`) VALUES
(51, 7, 'testxx', '2024-06-06 07:09:00', 1900);

-- --------------------------------------------------------

--
-- Structure de la table `ingredient__float`
--

CREATE TABLE `ingredient__float` (
  `id` int UNSIGNED NOT NULL,
  `cauldron_fk` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `value` float DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ingredient__float`
--

INSERT INTO `ingredient__float` (`id`, `cauldron_fk`, `name`, `value`, `priority`) VALUES
(1, 68, 'test float', 12.4344, 0),
(2, 68, 'test float', 13.333, 0),
(63, 75, 'test float', 12.2222, 800),
(64, 75, 'test float', 13.333, 700),
(69, 97, 'test float', 12.2222, 900),
(70, 97, 'test float', 13.333, 800),
(101, 7, 'test float', 12.2222, 2100),
(102, 7, 'test float', 13.333, 2000);

-- --------------------------------------------------------

--
-- Structure de la table `ingredient__integer`
--

CREATE TABLE `ingredient__integer` (
  `id` int UNSIGNED NOT NULL,
  `cauldron_fk` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `value` int DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ingredient__integer`
--

INSERT INTO `ingredient__integer` (`id`, `cauldron_fk`, `name`, `value`, `priority`) VALUES
(2, 68, 'user__connexion', 2, 0),
(3, 4, 'user__connexion', 1, 0),
(4, 5, 'user__profile', 1, 0),
(5, 8, 'user__profile', 1, 0),
(6, 8, 'user__profile', 2, 0),
(53, 75, 'user__connexion', 2, 500),
(56, 97, 'user__connexion', 2, 500),
(72, 7, 'user__connexion', 2, 1800),
(89, 168, 'id test 2', 7, 200),
(108, 228, 'id to remove', 7, 300),
(116, 223, 'id to remove', 7, 400),
(117, 371, 'id to remove', 7, 400),
(178, 374, 'integer', 7, 300),
(180, 378, 'integer', 7, 300),
(181, 385, 'integer', 7, 300),
(182, 390, 'integer', 7, 300),
(183, 214, 'integer', 7, 300),
(185, 395, 'integer', 7, 600),
(186, 401, 'identifier', 0, 0),
(187, 402, 'identifier', 0, 0),
(188, 403, 'identifier', 0, 0),
(189, 404, 'identifier', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `ingredient__price`
--

CREATE TABLE `ingredient__price` (
  `id` int UNSIGNED NOT NULL,
  `cauldron_fk` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `value` decimal(10,2) DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ingredient__string`
--

CREATE TABLE `ingredient__string` (
  `id` int UNSIGNED NOT NULL,
  `cauldron_fk` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `value` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `priority` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ingredient__string`
--

INSERT INTO `ingredient__string` (`id`, `cauldron_fk`, `name`, `value`, `priority`) VALUES
(1, 4, 'last-name', 'Witchcase', 0),
(2, 4, 'fist-name', 'Administrateur', 0),
(113, 7, 'fist-name', 'Jean', 2300),
(114, 7, 'last-name', 'Gromard', 2200),
(1182, 438, 'title', 'titre2', 500),
(1221, 793, 'name', '180-1809447_doom-guy-png-download-doom-low-health-face', 300),
(1222, 793, 'caption', '', 100),
(1223, 794, 'storage-path', 'image/png/6e49173ee5248765104a1bca4d49556babba39fa', 200),
(1224, 794, 'filename', '180-1809447_doom-guy-png-download-doom-low-health-face.png', 100);

-- --------------------------------------------------------

--
-- Structure de la table `ingredient__text`
--

CREATE TABLE `ingredient__text` (
  `id` int UNSIGNED NOT NULL,
  `cauldron_fk` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `priority` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ingredient__text`
--

INSERT INTO `ingredient__text` (`id`, `cauldron_fk`, `name`, `value`, `priority`) VALUES
(1, 8, 'text', 'htdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfxhtdyj yjt tr hgfs fdsu tjysfx', 0),
(44, 228, 'content to remove', '<p><strong>content</strong><br></p>', 100),
(52, 223, 'content to remove', '<p><strong>content</strong><br></p>', 200),
(53, 371, 'content to remove', '<p><strong>content</strong><br></p>', 200),
(115, 374, 'text', '<p><strong>contentwcwxcwx</strong><br></p>', 400),
(116, 378, 'text', '<p><strong>contentwcwxcwx</strong><br></p>', 400),
(117, 385, 'text', '<p><strong>contentwcwxcwx</strong><br></p>', 400),
(118, 390, 'text', '<p><strong>contentwcwxcwx</strong><br></p>', 400),
(119, 214, 'text', '<p><strong>contentwcwxcwx</strong><br></p>', 400),
(121, 395, 'text', '<p><strong>contentwcwxcwx</strong><br></p>', 400);

-- --------------------------------------------------------

--
-- Structure de la table `user__connexion`
--

CREATE TABLE `user__connexion` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'user signature',
  `email` varchar(511) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `login` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pass_hash` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `craft_table` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `craft_attribute` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'connexion',
  `craft_attribute_var` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT 'fk_user__connexion',
  `attribute_name` varchar(511) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `modifier` int DEFAULT NULL,
  `modified` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `creator` int DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user__connexion`
--

INSERT INTO `user__connexion` (`id`, `name`, `email`, `login`, `pass_hash`, `craft_table`, `craft_attribute`, `craft_attribute_var`, `attribute_name`, `modifier`, `modified`, `creator`, `created`) VALUES
(1, 'Administrator', 'adminstrator@witchcase', 'admin', '$2y$11$11FgVhXijP654xVeVG/VjeKIQnyRjVx0AsQ2QGQXiEx0VJeWeaGJ.', 'content__wc-user', 'connexion', 'id', 'connection', NULL, '2024-03-01 15:46:01', NULL, '2024-03-01 15:46:01'),
(2, 'Jean', 'jean.de.gromard@gmail.com', 'jean', '$2y$11$11FgVhXijP654xVeVG/VjeKIQnyRjVx0AsQ2QGQXiEx0VJeWeaGJ.', 'content__wc-user', 'connexion', 'id', 'connection', NULL, '2024-03-01 15:46:01', NULL, '2024-03-01 15:46:01'),
(3, 'Administrator', 'adminstrator@witchcase', 'admin', '$2y$11$11FgVhXijP654xVeVG/VjeKIQnyRjVx0AsQ2QGQXiEx0VJeWeaGJ.', 'draft__wc-user', 'connexion', 'id', 'connection', 1, '2024-04-04 14:58:34', 1, '2024-04-04 14:58:34'),
(4, 'admin', 'admin@nimp.fr', 'bbb', '$2y$10$s3sYPL.8Fukd5gPT49aQGOmddgohaqQC6wGKbRamnhUGN8pwtWU9K', 'content__test', 'connexion', 'id', 'nouvel-attribut-connexion', 1, '2024-10-09 16:22:38', 1, '2024-10-09 16:22:38');

-- --------------------------------------------------------

--
-- Structure de la table `user__policy`
--

CREATE TABLE `user__policy` (
  `id` int UNSIGNED NOT NULL,
  `fk_profile` int UNSIGNED DEFAULT NULL,
  `module` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'view',
  `status` int UNSIGNED DEFAULT NULL,
  `fk_witch` int UNSIGNED DEFAULT NULL,
  `position_ancestors` tinyint(1) NOT NULL DEFAULT '0',
  `position_included` tinyint(1) NOT NULL DEFAULT '1',
  `position_descendants` tinyint(1) NOT NULL DEFAULT '1',
  `custom_limitation` varchar(31) COLLATE utf8mb4_general_ci DEFAULT NULL
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
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `site` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '*',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user__profile`
--

INSERT INTO `user__profile` (`id`, `name`, `site`, `created`) VALUES
(1, 'administrator', '*', '2024-03-01 15:46:01'),
(2, 'public', '*', '2024-03-01 15:46:01');

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
(1, 1, '2024-03-01 15:46:01'),
(2, 1, '2024-03-01 15:46:01'),
(3, 1, '2024-04-04 14:58:34'),
(4, 2, '2024-10-09 16:22:38');

-- --------------------------------------------------------

--
-- Structure de la table `witch`
--

CREATE TABLE `witch` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data` text COLLATE utf8mb4_general_ci,
  `site` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `url` varchar(1023) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `status` int UNSIGNED NOT NULL DEFAULT '0',
  `invoke` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cauldron` int UNSIGNED DEFAULT NULL,
  `cauldron_priority` int NOT NULL DEFAULT '0',
  `craft_table` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `craft_fk` int UNSIGNED DEFAULT NULL,
  `alias` int DEFAULT NULL,
  `is_main` int UNSIGNED NOT NULL DEFAULT '1',
  `context` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `priority` int NOT NULL DEFAULT '0',
  `level_1` int UNSIGNED DEFAULT NULL,
  `level_2` int UNSIGNED DEFAULT NULL,
  `level_3` int UNSIGNED DEFAULT NULL,
  `level_4` int UNSIGNED DEFAULT NULL,
  `level_5` int UNSIGNED DEFAULT NULL,
  `level_6` int UNSIGNED DEFAULT NULL,
  `level_7` int UNSIGNED DEFAULT NULL,
  `level_8` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `witch`
--

INSERT INTO `witch` (`id`, `name`, `data`, `site`, `url`, `status`, `invoke`, `cauldron`, `cauldron_priority`, `craft_table`, `craft_fk`, `alias`, `is_main`, `context`, `datetime`, `priority`, `level_1`, `level_2`, `level_3`, `level_4`, `level_5`, `level_6`, `level_7`, `level_8`) VALUES
(1, 'Root', 'Ici se trouve la racine de la plateforme. C\'est à partir d\'ici que sont créées les homes de chaque site de la plateforme.', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:01', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Admin WitchCase', 'Site d\'administration', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:01', 400, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Utilisateurs', '', 'admin', 'utilisateurs', 0, '', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:01', -300, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Administrateur', '', 'admin', 'utilisateurs/administrateur', 0, '', 4, 200, 'content__wc-user', 1, NULL, 1, '', '2024-03-01 15:46:01', 0, 1, 1, 1, NULL, NULL, NULL, NULL, NULL),
(5, 'Home', '', 'admin', '', 0, 'root', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:01', -400, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Login', 'Module de déconnexion/connexion', 'admin', 'login', 0, 'login', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:01', 800, 1, 2, 1, NULL, NULL, NULL, NULL, NULL),
(7, 'Witch', 'Visualisation des Witches, c\'est a dire de chaque point de l\'arborescence -appelé ici Matriarcat. Chacun de ces points peut être associé à un contenu et/ou à un module exécutable. \r\nOn peut également définir une URL permettant de cibler cette witch.', 'admin', 'view', 0, 'witch/view', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:01', 700, 1, 2, 2, NULL, NULL, NULL, NULL, NULL),
(8, 'Edit Witch', '', 'admin', 'edit', 0, 'witch/edit', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:01', 500, 1, 2, 3, NULL, NULL, NULL, NULL, NULL),
(9, 'Edit Craft', 'This is the draft of craft, you can publish it, save it for later, or remove draft to cancel modification.', 'admin', 'edit-content', 0, 'contents/edit', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:01', 400, 1, 2, 4, NULL, NULL, NULL, NULL, NULL),
(10, 'Menu', '', NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:01', 200, 1, 2, 5, NULL, NULL, NULL, NULL, NULL),
(11, 'Profiles', 'Permissions handeling is based on user profiles.', 'admin', 'profiles', 0, 'profiles', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:01', 600, 1, 2, 5, 1, NULL, NULL, NULL, NULL),
(12, 'Craft Structures', '', 'admin', 'structures-old', 0, 'structures', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:01', 400, 1, 2, 5, 2, NULL, NULL, NULL, NULL),
(13, 'Apply', '', 'admin', 'apply', 0, 'emptyCache', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-01 15:46:28', 300, 1, 2, 5, 3, NULL, NULL, NULL, NULL),
(14, 'aaaa', 'test pour le chaudron !', 'admin', 'aaaa', 1, 'cauldrons', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-08 17:35:00', 300, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Recipes', 'Les données sont stockées sous la forme de structures qui sont éditables ici.', 'admin', 'recipe', 0, 'recipe/list', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-03-09 15:48:02', 500, 1, 2, 5, 4, NULL, NULL, NULL, NULL),
(16, 'Cauldron', '', 'admin', 'cauldron', 0, 'cauldron', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-04-08 15:04:18', 600, 1, 2, 9, NULL, NULL, NULL, NULL, NULL),
(25, 'View Structure', 'Cauldron\'s inside element\'s structure visualization', 'admin', 'recipe/view', 0, 'recipe/view', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-06-11 13:57:05', 0, 1, 2, 5, 4, 1, NULL, NULL, NULL),
(26, 'Edit Structure', '', 'admin', 'recipe/edit', 0, 'recipe/edit', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-06-13 13:54:51', 0, 1, 2, 5, 4, 2, NULL, NULL, NULL),
(27, 'Create Structure', '', 'admin', 'recipe/create', 0, 'recipe/create', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-08-07 15:05:40', 0, 1, 2, 5, 4, 3, NULL, NULL, NULL),
(30, 'Create Witch', '', 'admin', 'create-witch', 0, 'witch/create', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-09-24 21:02:27', 300, 1, 2, 7, NULL, NULL, NULL, NULL, NULL),
(37, 'Witch', 'Witch Folder', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-10-01 13:59:43', 100, 1, 2, 8, NULL, NULL, NULL, NULL, NULL),
(38, 'clipboard', NULL, 'admin', 'clipboard', 0, 'witch/clipboard', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-10-01 15:24:28', 0, 1, 2, 8, 1, NULL, NULL, NULL, NULL),
(45, 'bbb 3', 'murf desc', 'admin', 'testmurf', 1, 'witch/view', 7, 0, NULL, NULL, NULL, 0, NULL, '2024-10-03 20:03:45', 0, 2, 6, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'ccc', '', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, '', '2024-10-03 20:03:45', -500, 2, 6, 2, NULL, NULL, NULL, NULL, NULL),
(49, 'ddd', 'dytytc', 'admin', 'ddd', 2, 'default', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-10-03 20:03:45', -600, 2, 6, 2, 1, NULL, NULL, NULL, NULL),
(58, 'aaaa 2', 'test pour le chaudron', 'admin', 'aaaa-2', 1, 'default', 792, 0, 'draft__test', 2, NULL, 1, NULL, '2024-10-08 09:58:02', 200, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 'test 280', '', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-08 09:58:02', 400, 3, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 'test 3', '', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-08 09:58:02', 200, 3, 1, 1, NULL, NULL, NULL, NULL, NULL),
(61, 'test 4', NULL, 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-08 09:58:02', 0, 3, 1, 1, 1, NULL, NULL, NULL, NULL),
(62, 'test 4', NULL, 'admin', 'testtest-4', 0, 'witch/view', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-08 09:58:02', -500, 3, 1, 1, 2, NULL, NULL, NULL, NULL),
(63, 'murf', 'murf desc', 'admin', 'testtestmurf-2', 1, 'witch/view', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-08 09:58:02', 0, 3, 1, 2, NULL, NULL, NULL, NULL, NULL),
(64, 'Administrateur', '', 'admin', NULL, 0, NULL, NULL, 0, 'content__wc-user', 1, NULL, 0, '', '2024-10-08 09:58:02', -500, 3, 1, 2, 1, NULL, NULL, NULL, NULL),
(65, 'Create Witch', 'dytytc', 'admin', 'testtestmurf-2testmurftest/create-witch-2', 2, 'witch/view', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-08 09:58:02', -600, 3, 1, 2, 1, 1, NULL, NULL, NULL),
(66, 'Test Marie', NULL, 'admin', 'testtest/test-marie', 0, 'default', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-08 09:58:02', 0, 3, 1, 3, NULL, NULL, NULL, NULL, NULL),
(67, 'murf', 'murf desc', 'admin', 'testmurf', 1, 'witch/view', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-08 09:58:02', 200, 3, 2, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 'Administrateur', '', 'admin', NULL, 0, NULL, NULL, 0, 'content__wc-user', 1, NULL, 0, '', '2024-10-08 09:58:02', -500, 3, 2, 1, NULL, NULL, NULL, NULL, NULL),
(69, 'Create Witch', 'dytytc', 'admin', 'testmurftest/create-witch-2', 2, 'witch/view', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-08 09:58:02', -600, 3, 2, 1, 1, NULL, NULL, NULL, NULL),
(70, 'bbb 2', '', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-10-10 13:20:31', 0, 2, 8, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 'Jeannot', NULL, 'admin', '-2', 0, 'default', 7, 200, NULL, NULL, NULL, 1, NULL, '2024-10-10 13:38:23', 300, 3, 4, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 'Admin - test', NULL, 'admin', 'test/admin-test', 0, 'default', 4, 100, NULL, NULL, NULL, 1, NULL, '2024-10-10 13:39:52', 0, 3, 4, 1, NULL, NULL, NULL, NULL, NULL),
(74, 'jeannot 2', NULL, 'admin', 'test/jeannot-2', 0, 'default', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-10-10 13:52:28', 0, 3, 4, 2, NULL, NULL, NULL, NULL, NULL),
(102, 'Admin WitchCase', 'Site d\'administration', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 300, 2, 8, 1, NULL, NULL, NULL, NULL, NULL),
(103, 'Utilisateurs', '', 'admin', NULL, 0, '', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', -300, 2, 8, 1, 1, NULL, NULL, NULL, NULL),
(104, 'Administrateur', '', 'admin', NULL, 0, NULL, 4, 100, 'content__wc-user', 1, NULL, 0, '', '2024-10-24 13:39:33', 0, 2, 8, 1, 1, 1, NULL, NULL, NULL),
(105, 'Home', '', 'admin', 'home', 0, 'root', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', -400, 2, 8, 1, 2, NULL, NULL, NULL, NULL),
(106, 'Cauldron', '', 'admin', 'homecauldron', 0, 'cauldron', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 50, 2, 8, 1, 2, 1, NULL, NULL, NULL),
(107, 'Login', 'Module de déconnexion/connexion', 'admin', 'homelogin', 0, 'login', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 40, 2, 8, 1, 2, 2, NULL, NULL, NULL),
(108, 'Witch', 'Visualisation des Witches, c\'est a dire de chaque point de l\'arborescence -appelé ici Matriarcat. Chacun de ces points peut être associé à un contenu et/ou à un module exécutable. \r\nOn peut également définir une URL permettant de cibler cette witch.', 'admin', 'homeview', 0, 'witch/view', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 30, 2, 8, 1, 2, 3, NULL, NULL, NULL),
(109, 'Edit Witch', '', 'admin', 'homeedit', 0, 'witch/edit', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 20, 2, 8, 1, 2, 4, NULL, NULL, NULL),
(110, 'Edit Craft', 'This is the draft of craft, you can publish it, save it for later, or remove draft to cancel modification.', 'admin', 'homeedit-content', 0, 'contents/edit', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 10, 2, 8, 1, 2, 5, NULL, NULL, NULL),
(111, 'Create Witch', '', 'admin', 'homecreate-witch', 0, 'witch/create', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 0, 2, 8, 1, 2, 6, NULL, NULL, NULL),
(112, 'Menu', '', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 0, 2, 8, 1, 2, 7, NULL, NULL, NULL),
(113, 'Profiles', 'Permissions handeling is based on user profiles.', 'admin', 'profiles-2', 0, 'profiles', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 0, 2, 8, 1, 2, 7, 1, NULL, NULL),
(114, 'Recipes', 'Les données sont stockées sous la forme de structures qui sont éditables ici.', 'admin', 'recipe-2', 0, 'recipe/list', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 0, 2, 8, 1, 2, 7, 2, NULL, NULL),
(115, 'Create Structure', '', 'admin', 'recipe/create-2', 0, 'recipe/create', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 0, 2, 8, 1, 2, 7, 2, 1, NULL),
(116, 'Edit Structure', '', 'admin', 'recipe/edit-2', 0, 'recipe/edit', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 0, 2, 8, 1, 2, 7, 2, 2, NULL),
(117, 'View Structure', 'Cauldron\'s inside element\'s structure visualization', 'admin', 'recipe/view-2', 0, 'recipe/view', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 0, 2, 8, 1, 2, 7, 2, 3, NULL),
(118, 'Structures old school', '', 'admin', 'structures-old-2', 0, 'structures', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 0, 2, 8, 1, 2, 7, 3, NULL, NULL),
(119, 'Apply', '', 'admin', 'apply-2', 0, 'emptyCache', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', -1, 2, 8, 1, 2, 7, 4, NULL, NULL),
(120, 'Witch', 'Witch Folder', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 0, 2, 8, 1, 2, 8, NULL, NULL, NULL),
(121, 'clipboard', NULL, 'admin', 'clipboard-2', 0, 'witch/clipboard', NULL, 0, NULL, NULL, NULL, 0, NULL, '2024-10-24 13:39:33', 0, 2, 8, 1, 2, 8, 1, NULL, NULL),
(127, 'Cauldrons', NULL, 'admin', 'cauldrons', 0, 'cauldrons', NULL, 0, NULL, NULL, NULL, 1, NULL, '2024-11-26 15:11:01', 200, 1, 2, 5, 5, NULL, NULL, NULL, NULL),
(129, 'Jean', '', 'admin', NULL, 0, NULL, 7, 0, NULL, NULL, NULL, 1, NULL, '2025-01-10 16:01:18', 0, 1, 1, 2, NULL, NULL, NULL, NULL, NULL),
(130, 'test 01', '', 'admin', NULL, 0, NULL, 438, 0, NULL, NULL, NULL, 1, NULL, '2025-01-16 15:57:52', 0, 2, 6, 2, 2, NULL, NULL, NULL, NULL),
(131, 'Admin WitchCase', 'Site d\'administration', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 100, 3, 5, NULL, NULL, NULL, NULL, NULL, NULL),
(132, 'Utilisateurs', '', 'admin', NULL, 0, '', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', -300, 3, 5, 1, NULL, NULL, NULL, NULL, NULL),
(133, 'Administrateur', '', 'admin', NULL, 0, '', 4, 0, 'content__wc-user', 1, NULL, 0, '', '2025-01-22 16:29:59', 0, 3, 5, 1, 1, NULL, NULL, NULL, NULL),
(134, 'Jean', '', 'admin', NULL, 0, NULL, 7, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 0, 3, 5, 1, 2, NULL, NULL, NULL, NULL),
(135, 'Home', '', 'admin', 'home-2', 0, 'root', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', -400, 3, 5, 2, NULL, NULL, NULL, NULL, NULL),
(136, 'Login', 'Module de déconnexion/connexion', 'admin', 'home-2login', 0, 'login', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 800, 3, 5, 2, 1, NULL, NULL, NULL, NULL),
(137, 'Witch', 'Visualisation des Witches, c\'est a dire de chaque point de l\'arborescence -appelé ici Matriarcat. Chacun de ces points peut être associé à un contenu et/ou à un module exécutable. \r\nOn peut également définir une URL permettant de cibler cette witch.', 'admin', 'home-2view', 0, 'witch/view', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 700, 3, 5, 2, 2, NULL, NULL, NULL, NULL),
(138, 'Cauldron', '', 'admin', 'home-2cauldron', 0, 'cauldron', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 600, 3, 5, 2, 3, NULL, NULL, NULL, NULL),
(139, 'Edit Witch', '', 'admin', 'home-2edit', 0, 'witch/edit', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 500, 3, 5, 2, 4, NULL, NULL, NULL, NULL),
(140, 'Edit Craft', 'This is the draft of craft, you can publish it, save it for later, or remove draft to cancel modification.', 'admin', 'home-2edit-content', 0, 'contents/edit', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 400, 3, 5, 2, 5, NULL, NULL, NULL, NULL),
(141, 'Create Witch', '', 'admin', 'home-2create-witch', 0, 'witch/create', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 300, 3, 5, 2, 6, NULL, NULL, NULL, NULL),
(142, 'Menu', '', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 200, 3, 5, 2, 7, NULL, NULL, NULL, NULL),
(143, 'Profiles', 'Permissions handeling is based on user profiles.', 'admin', 'profiles-3', 0, 'profiles', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 400, 3, 5, 2, 7, 1, NULL, NULL, NULL),
(144, 'Recipes', 'Les données sont stockées sous la forme de structures qui sont éditables ici.', 'admin', 'recipe-3', 0, 'recipe/list', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 300, 3, 5, 2, 7, 2, NULL, NULL, NULL),
(145, 'Create Structure', '', 'admin', 'recipe/create-3', 0, 'recipe/create', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 0, 3, 5, 2, 7, 2, 1, NULL, NULL),
(146, 'Edit Structure', '', 'admin', 'recipe/edit-3', 0, 'recipe/edit', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 0, 3, 5, 2, 7, 2, 2, NULL, NULL),
(147, 'View Structure', 'Cauldron\'s inside element\'s structure visualization', 'admin', 'recipe/view-3', 0, 'recipe/view', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 0, 3, 5, 2, 7, 2, 3, NULL, NULL),
(148, 'Craft Structures', '', 'admin', 'structures-old-3', 0, 'structures', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 100, 3, 5, 2, 7, 3, NULL, NULL, NULL),
(149, 'Apply', '', 'admin', 'apply-3', 0, 'emptyCache', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 0, 3, 5, 2, 7, 4, NULL, NULL, NULL),
(150, 'Cauldrons', NULL, 'admin', 'cauldrons-2', 0, 'cauldrons', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 0, 3, 5, 2, 7, 5, NULL, NULL, NULL),
(151, 'Witch', 'Witch Folder', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 100, 3, 5, 2, 8, NULL, NULL, NULL, NULL),
(152, 'clipboard', NULL, 'admin', 'clipboard-3', 0, 'witch/clipboard', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-01-22 16:29:59', 0, 3, 5, 2, 8, 1, NULL, NULL, NULL),
(153, 'Admin WitchCase', 'Site d\'administration', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 100, 3, 4, 2, 1, NULL, NULL, NULL, NULL),
(154, 'Utilisateurs', '', 'admin', NULL, 0, '', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', -300, 3, 4, 2, 1, 1, NULL, NULL, NULL),
(155, 'Administrateur', '', 'admin', NULL, 0, '', 4, 0, 'content__wc-user', 1, NULL, 0, '', '2025-03-06 17:12:59', 0, 3, 4, 2, 1, 1, 1, NULL, NULL),
(156, 'Jean', '', 'admin', NULL, 0, NULL, 7, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 0, 3, 4, 2, 1, 1, 2, NULL, NULL),
(157, 'Home', '', 'admin', 'home-2-2', 0, 'root', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', -400, 3, 4, 2, 1, 2, NULL, NULL, NULL),
(158, 'Login', 'Module de déconnexion/connexion', 'admin', 'home-2login-2', 0, 'login', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 800, 3, 4, 2, 1, 2, 1, NULL, NULL),
(159, 'Witch', 'Visualisation des Witches, c\'est a dire de chaque point de l\'arborescence -appelé ici Matriarcat. Chacun de ces points peut être associé à un contenu et/ou à un module exécutable. \r\nOn peut également définir une URL permettant de cibler cette witch.', 'admin', 'home-2view-2', 0, 'witch/view', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 700, 3, 4, 2, 1, 2, 2, NULL, NULL),
(160, 'Cauldron', '', 'admin', 'home-2cauldron-2', 0, 'cauldron', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 600, 3, 4, 2, 1, 2, 3, NULL, NULL),
(161, 'Edit Witch', '', 'admin', 'home-2edit-2', 0, 'witch/edit', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 500, 3, 4, 2, 1, 2, 4, NULL, NULL),
(162, 'Edit Craft', 'This is the draft of craft, you can publish it, save it for later, or remove draft to cancel modification.', 'admin', 'home-2edit-content-2', 0, 'contents/edit', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 400, 3, 4, 2, 1, 2, 5, NULL, NULL),
(163, 'Create Witch', '', 'admin', 'home-2create-witch-2', 0, 'witch/create', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 300, 3, 4, 2, 1, 2, 6, NULL, NULL),
(164, 'Menu', '', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 200, 3, 4, 2, 1, 2, 7, NULL, NULL),
(165, 'Profiles', 'Permissions handeling is based on user profiles.', 'admin', 'home-2profiles-3', 0, 'profiles', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 400, 3, 4, 2, 1, 2, 7, 1, NULL),
(166, 'Recipes', 'Les données sont stockées sous la forme de structures qui sont éditables ici.', 'admin', 'home-2recipe-3', 0, 'recipe/list', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 300, 3, 4, 2, 1, 2, 7, 2, NULL),
(167, 'Create Structure', '', 'admin', 'recipe/create-3-2', 0, 'recipe/create', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 0, 3, 4, 2, 1, 2, 7, 2, 1),
(168, 'Edit Structure', '', 'admin', 'recipe/edit-3-2', 0, 'recipe/edit', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 0, 3, 4, 2, 1, 2, 7, 2, 2),
(169, 'View Structure', 'Cauldron\'s inside element\'s structure visualization', 'admin', 'recipe/view-3-2', 0, 'recipe/view', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 0, 3, 4, 2, 1, 2, 7, 2, 3),
(170, 'Craft Structures', '', 'admin', 'home-2structures-old-3', 0, 'structures', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 100, 3, 4, 2, 1, 2, 7, 3, NULL),
(171, 'Apply', '', 'admin', 'home-2apply-3', 0, 'emptyCache', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 0, 3, 4, 2, 1, 2, 7, 4, NULL),
(172, 'Cauldrons', NULL, 'admin', 'home-2cauldrons-2', 0, 'cauldrons', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:12:59', 0, 3, 4, 2, 1, 2, 7, 5, NULL),
(173, 'Witch', 'Witch Folder', 'admin', NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:13:00', 100, 3, 4, 2, 1, 2, 8, NULL, NULL),
(174, 'clipboard', NULL, 'admin', 'home-2clipboard-3', 0, 'witch/clipboard', NULL, 0, NULL, NULL, NULL, 0, NULL, '2025-03-06 17:13:00', 0, 3, 4, 2, 1, 2, 8, 1, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `archive__test`
--
ALTER TABLE `archive__test`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__wc-user`
--
ALTER TABLE `archive__wc-user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `cauldron`
--
ALTER TABLE `cauldron`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_level_1` (`level_1`),
  ADD KEY `target` (`target`),
  ADD KEY `IDX_level_2` (`level_2`),
  ADD KEY `IDX_level_3` (`level_3`),
  ADD KEY `IDX_level_4` (`level_4`),
  ADD KEY `IDX_level_5` (`level_5`),
  ADD KEY `IDX_level_6` (`level_6`),
  ADD KEY `IDX_level_7` (`level_7`),
  ADD KEY `IDX_level_8` (`level_8`),
  ADD KEY `IDX_level_9` (`level_9`);

--
-- Index pour la table `content__test`
--
ALTER TABLE `content__test`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__wc-user`
--
ALTER TABLE `content__wc-user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__test`
--
ALTER TABLE `draft__test`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__wc-user`
--
ALTER TABLE `draft__wc-user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ingredient__boolean`
--
ALTER TABLE `ingredient__boolean`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CAULDRON` (`cauldron_fk`);

--
-- Index pour la table `ingredient__datetime`
--
ALTER TABLE `ingredient__datetime`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CAULDRON` (`cauldron_fk`);

--
-- Index pour la table `ingredient__float`
--
ALTER TABLE `ingredient__float`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CAULDRON` (`cauldron_fk`);

--
-- Index pour la table `ingredient__integer`
--
ALTER TABLE `ingredient__integer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CAULDRON` (`cauldron_fk`);

--
-- Index pour la table `ingredient__price`
--
ALTER TABLE `ingredient__price`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CAULDRON` (`cauldron_fk`);

--
-- Index pour la table `ingredient__string`
--
ALTER TABLE `ingredient__string`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CAULDRON` (`cauldron_fk`);

--
-- Index pour la table `ingredient__text`
--
ALTER TABLE `ingredient__text`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CAULDRON` (`cauldron_fk`);

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
  ADD KEY `IDX_level_8` (`level_8`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `archive__test`
--
ALTER TABLE `archive__test`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `archive__wc-user`
--
ALTER TABLE `archive__wc-user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `cauldron`
--
ALTER TABLE `cauldron`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=795;

--
-- AUTO_INCREMENT pour la table `content__test`
--
ALTER TABLE `content__test`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `content__wc-user`
--
ALTER TABLE `content__wc-user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `draft__test`
--
ALTER TABLE `draft__test`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `draft__wc-user`
--
ALTER TABLE `draft__wc-user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `ingredient__boolean`
--
ALTER TABLE `ingredient__boolean`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT pour la table `ingredient__datetime`
--
ALTER TABLE `ingredient__datetime`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT pour la table `ingredient__float`
--
ALTER TABLE `ingredient__float`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT pour la table `ingredient__integer`
--
ALTER TABLE `ingredient__integer`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT pour la table `ingredient__price`
--
ALTER TABLE `ingredient__price`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `ingredient__string`
--
ALTER TABLE `ingredient__string`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1225;

--
-- AUTO_INCREMENT pour la table `ingredient__text`
--
ALTER TABLE `ingredient__text`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT pour la table `user__connexion`
--
ALTER TABLE `user__connexion`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
