-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 29 sep. 2023 à 08:25
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
-- Structure de la table `archive__witchcase-article`
--

CREATE TABLE `archive__witchcase-article` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `headline@string#value` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `headline-left@string#value` varchar(511) DEFAULT NULL,
  `body-left@text#value` text,
  `headline-center@string#value` varchar(511) DEFAULT NULL, 
  `body-center@text#value` text,
  `headline-right@string#value` varchar(511) DEFAULT NULL,
  `body-right@text#value` text,
  `link@link#href` varchar(511) DEFAULT NULL,
  `link@link#text` varchar(511) DEFAULT NULL,
  `link@link#external` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `archive__witchcase-folder`
--

CREATE TABLE `archive__witchcase-folder` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `headline@string#value` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `background@image#file` varchar(511) DEFAULT NULL,
  `background@image#title` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `archive__witchcase-home`
--

CREATE TABLE `archive__witchcase-home` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `meta-title@string#value` varchar(511) DEFAULT NULL,
  `meta-description@string#value` varchar(511) DEFAULT NULL,
  `meta-keywords@string#value` varchar(511) DEFAULT NULL,
  `logo@image#file` varchar(511) DEFAULT NULL,
  `logo@image#title` varchar(511) DEFAULT NULL,
  `contact-email@link#href` varchar(511) DEFAULT NULL,
  `contact-email@link#text` varchar(511) DEFAULT NULL,
  `contact-email@link#external` tinyint(1) DEFAULT '1',
  `call-to-action@link#href` varchar(511) DEFAULT NULL,
  `call-to-action@link#text` varchar(511) DEFAULT NULL,
  `call-to-action@link#external` tinyint(1) DEFAULT '1',
  `headline@string#value` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `background@image#file` varchar(511) DEFAULT NULL,
  `background@image#title` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Structure de la table `content__witchcase-article`
--

CREATE TABLE `content__witchcase-article` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `headline@string#value` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `headline-left@string#value` varchar(511) DEFAULT NULL,
  `body-left@text#value` text,
  `headline-center@string#value` varchar(511) DEFAULT NULL,
  `body-center@text#value` text,
  `headline-right@string#value` varchar(511) DEFAULT NULL,
  `body-right@text#value` text,
  `link@link#href` varchar(511) DEFAULT NULL,
  `link@link#text` varchar(511) DEFAULT NULL,
  `link@link#external` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `content__witchcase-article`
--

INSERT INTO `content__witchcase-article` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `headline@string#value`, `body@text#value`, `image@image#file`, `image@image#title`, `headline-left@string#value`, `body-left@text#value`, `headline-center@string#value`, `body-center@text#value`, `headline-right@string#value`, `body-right@text#value`, `link@link#href`, `link@link#text`, `link@link#external`) VALUES
(1, 'REPRENEZ LE CONTRÔLE', 1, '2023-09-26 16:37:27', 1, '2023-09-26 16:49:01', 'REPRENEZ LE CONTRÔLE', '', 'logo_woody.png', 'logo woody', 'Pour les développeurs', 'Woody CMS est développé sur des technologies purement <strong>PHP/MySQL, en licence GPL</strong>. Le langage PHP est la seule connaissance requise pour y développer un site. Le concept de Woody CMS est une simplification fondamentale du stockage de données : <strong>plus de cache</strong> sur la visualisation des contenus, et aucune perte de temps due au renouvellement de ses fichiers pour valider ses développements. Les éléments contextuels (le menu par exemple) sont cachés par un système très simple et très accessible laissé aux soins du développeur. <br>De plus Woody CMS est une <strong>plateforme multisites</strong> avec un système d\'héritage et laisse une grande liberté dans le développement des modules. <br>En somme Woody CMS fonctionne comme une véritable <strong>\"boite à outils\"</strong> pour les développeurs.  ', 'Pour les utilisateurs', 'A l’heure actuelle, la contribution d’un site web implique un temps de visualisation de son travail en ligne avant de considérer ou non cette tâche achevée. Ce temps de validation doit prendre en compte la gestion du cache qui, suivant son niveau de complexité, peut prendre jusqu\'à 20 minutes pour une seule contribution. Un site implémenté avec Woody CMS permet la <strong>mise en ligne immédiate</strong> d’une information grâce à l’absence de cache de visualisation. Les contributeurs peuvent ainsi valider immédiatement leur travail. De plus Woody CMS est multisite et gère efficacement le <strong>multi-positionnement de contenus</strong>. Les contributeurs peuvent gérer différents sites via <strong>une seule et même interface</strong>, où la modification d’un contenu multipositionné est effective automatiquement à l’ensemble du site. Les visiteurs consultent alors une information instantanément à jour, sans s’impatienter devant un site ralenti par la régénération des fichiers de cache.', 'Pour les administrateurs', 'Woody CMS est un gestionnaire de contenu <strong>malléable</strong> : la forme et les emplacements sont déterminés par les administrateurs de la plateforme. L’administrateur peut créer et modifier à loisir autant de structures de contenus qu\'il souhaite, suivant la forme qu’il recherche. Par exemple la structure <em>article</em> peut contenir une introduction, une image, un lien ou encore un diaporama afin de correspondre exactement à ses besoins. Afin que les nécessités métier ne s’adaptent pas à une structure rigide, Woody CMS range les différents éléments qui constituent le site, suivant une <strong>arborescence choisie et construite en fonction des besoins requis</strong>. Pour les webmaster, Woody CMS propose nativement une gestion multisites avec héritage et une multiposition des éléments, afin de faciliter l\'administration. Pour les administrateurs réseau, une <strong>prise en compte immédiate et sans cache de la configuration</strong> permet l\'ajout et la suppression de host et de siteaccess \"à chaud\".', NULL, NULL, 1),
(2, 'Woody CMS en quelques mots', 1, '2023-09-26 16:47:28', 1, '2023-09-26 18:41:25', 'Woody CMS en quelques mots', 'Voici une copie d\'écran de l\'interface Woody CMS. Ici nous visualisons l\'élément de la home du site de Witch case. Nous distinguons à gauche les informations sur l\'élément <em>home</em> : le module est <em>view</em> (module de visualisation de contenus), le contenu associé n\'est plus un brouillon et n\'est pas une archive, il est indiqué <em>Content</em>. Le type de structure du contenu est <em>home-demo</em>, c\'est-à-dire la page d\'accueil. En dessous, nous avons la partie dédiée à l’emplacement, et plus en dessous encore le tableau des sous-éléments. A droite nous avons une visualisation des attributs du contenu.', 'WoodyEcran.png', ' Capture d\'écran de l\'interface d\'administration ', 'Malléabilité', 'Woody CMS est un gestionnaire dont l’arborescence et la <strong>structure des contenus sont gérés à 100% par ses utilisateurs</strong>. Cela permet d\'adapter ce CMS aux besoins métiers et non l\'inverse. Woody CMS permet la gestion multisites avec héritage. Dans le cas de plusieurs sites à structure identique, le nouveau site héritera des codes des précédents sites. Il ne restera plus qu’à développer la spécificité du nouveau site. Il permet également la multiposition d\'éléments, afin de <strong>modifier en une seule action</strong> un élément présent sur plusieurs emplacements. Chacune des positions est associée à un module (un fichier PHP), et peut être associé, ou non, à un contenu. Cela permet d\'ajouter facilement un nouveau traitement par le biais d\'un nouveau module. Woody CMS est une véritable \"boite à outils\" qui <strong>offre une grande liberté</strong> au développement et à l\'administration.', 'Spécificité', 'Woody CMS est fondamentalement orienté vers les professionnels du web, tout en étant fondamentalement flexible aux besoins métiers, tant dans sa structure de données que dans sa gestion de l\'arborescence. Il est tout d’abord dirigé vers le développement : en supprimant la majorité des fichiers de cache, il <strong>diminue significativement les temps</strong> de validation des codes développés. L\'accès au développement de modules spécifique est aussi simplifié : toute la structure des codes est en MVC mais reste à <strong>100% PHP5</strong>. L\'absence de cache de visualisation permet également de réduire les temps de contribution et d\'administration en ayant <strong>instantanément accès à l\'information</strong> par une récupération systématique des données en base.', 'Le projet ', ' Woody CMS est actuellement un prototype. De nombreuses améliorations sont en cours de développement. Nous ne saurons que trop vous remercier si vous souhaitez <strong>contribuer à son développement</strong>. <br>Il est sous <strong>licence GPL</strong> et développé à <strong>100% en PHP5</strong>, sans utilisation de templating (nous utilisons du code PHP dans les pages de visualisation). <br>Le développement de modules d\'utilisation courante est en cours, l\'ajout d\'attributs possible est également en cours, ainsi que des améliorations au niveau du moteur. <br>Le projet est là et bien vivant, <strong>nous n\'attendons plus que vous !</strong>', 'https://github.com/Jean2Grom/witchcase-local', 'Contribuez sur GitHub', 1),
(3, 'FONCTIONNEMENT GLOBAL', 1, '2023-09-28 18:44:00', 1, '2023-09-28 18:44:00', 'FONCTIONNEMENT GLOBAL', 'Schéma séquence MVC (simplifié)', 'MVC-schema-full.jpg', 'schema MVC', 'Partie <em>View</em>', '<strong>1.</strong> Pour qu\'une page s\'affiche, nous envoyons une requête HTTP (typiquement avec son navigateur web). <br/>\r\n<strong>8.</strong> Nous avons alors toutes les informations nécessaires pour les traitements du module. Nous récupérons alors le fichier \"design\" du module, qui produira l\'apparence du contenu de la page web (le code HTML de la partie évolutive du site, souvent centrale).  <br/>\r\n<strong>10.</strong> Nous récupérons alors le fichier \"design\" (voir plus haut) lié au contexte, on y inclut le résultat du module et nous affichons la page ainsi générée. \r\n', 'Partie <em>Controller</em>', '<strong>2.</strong> Le serveur qui reçoit la requête identifie en premier lieu l\'utilisateur (logé ou non). <br/>\r\n<strong>3.</strong> Le système détermine via le fichier de configuration à quel site la requête fait appel, Woody CMS étant un système multi-sites. <br/>\r\n<strong>5.</strong> Cette dernière requête a récupéré l’information du \"module\" souhaité, le système exécute alors le fichier correspondant à ce module.  <br/>\r\n<strong>9.</strong> Une fois ce code récupéré, il est stocké et nous déterminons quel sera le contexte (c\'est-à-dire la partie de la page HTML qui est partagée par plusieurs pages et qui revient donc régulièrement), les traitements de ce contexte sont effectués. \r\n', 'Partie <em>Model</em>', '<strong>4.</strong> Une fois le site et l\'URL identifiés, nous déterminons l\'emplacement dans l\'arborescence du contenu désiré en envoyant une requête sur la table des emplacements. <br/>\r\n<strong>6.</strong> La requête d\'emplacement a également fourni le contenu cible appelé ici \"target\". Nous envoyons alors une requête simple sur la structure de la table en base de données qui contient ce contenu. <br/>\r\n<strong>7.</strong> Nous obtenons la structure du contenu. Nous regardons alors si d\'autres informations sont souhaitées et nous ajustons si besoin la requête qui doit récupérer ce contenu.\r\n', '', '', 1),
(4, 'EMPLACEMENT MATRICIEL', 1, '2023-09-28 18:47:32', 1, '2023-09-28 19:01:12', 'EMPLACEMENT MATRICIEL', 'Le premier schéma nous montre un enregistrement de la table d\'emplacement, \"n\" étant la profondeur maximum de l\'arborescence. C\'est la traduction à l\'échelle unitaire du stockage des coordonnées matricielles. Le schéma suivant démontre la possibilité de situer tout emplacement dans l\'arborescence, en utilisant un champ par niveau de profondeur. Si l’on veut ajouter un élément dont la profondeur dans l\'arborescence est plus grande d\'un niveau que celle prévue par le nombre de champs, le problème est que nous ne pouvons plus situer cet élément dans ce nouveau niveau d\'arborescence. La solution est d\'ajouter un champ \"à chaud\", avec une valeur nulle par défaut. L\'ajout de ce champ n\'altère donc en rien l\'intégrité des données déjà présentes et résout le problème.', 'donnees_arbo_schema-full.jpg', 'Schémas emplacement matriciel', '', '', '', '', '', '', '', '', 1),
(5, 'CONTENU AJUSTABLE', 1, '2023-09-28 18:50:20', 1, '2023-09-28 18:50:20', 'CONTENU AJUSTABLE', 'Pour un CMS malléable, la solution actuelle et courante de stockage des contenus, comme le montre le premier schéma, est comme le montre le premier schéma : chaque rectangle représente un enregistrement dans une table séparée des autres carrés (ceci est le fonctionnement global, des exceptions peuvent exister notamment si on est en présence de deux attributs de même type). La solution développée pour Woody CMS : une seule table est nécessaire pour stocker l\'ensemble des contenus. Nous noterons que cette innovation est fortement liée à la suivante, qui est de placer la structure dans le titre des champs de la table.', 'solution_courante_schema-full.jpg', 'Schéma contenu ajustable', '', '', '', '', '', '', '', '', 1),
(6, 'NOMMAGE STRUCTUREL DES CHAMPS', 1, '2023-09-28 19:03:31', 1, '2023-09-28 19:03:31', 'NOMMAGE STRUCTUREL DES CHAMPS', 'Les noms de champs sont nommés de façon à ce que leur simple lecture permette la structuration complète de la donnée, et ainsi de pouvoir ajuster la requête qui doit récupérer le contenu. Nous avons besoin que d\'une seule requête pour récupérer tous les contenus utiles en base de données.', 'nomage_schema_recadre-full.jpg', 'schema nommage structurel', '', '', '', '', '', '', '', '', 1),
(7, 'WITCH CASE EN BREF', 1, '2023-09-28 19:08:17', 1, '2023-09-28 19:08:17', 'WITCH CASE EN BREF', '', '', '', 'Pourquoi ?', 'C’est en constatant que le développement informatique relève d’une <strong>nouvelle forme d’artisanat</strong>, contrairement à l’évolution actuelle du métier de développeur, que Witch case est née. L\'industrialisation et la normalisation des besoins actuellement en cours dans la profession nuit à l’<strong>adaptabilité</strong>, alors qu’elle représente une question <strong>primordiale</strong> dans le domaine de l’informatique, et plus spécifiquement dans celui du web. C’est par la volonté d’affirmer notre vision du métier, en proposant des produits <strong>innovants et de qualité</strong>, que Witch case a été fondé. ', 'Les valeurs ', 'L\'<strong>intégrité</strong> est sans doute la première des valeurs de Witch case, le respect humain et l\'honnêteté sont des points sur lesquels nous ne pouvons transiger. La <strong>créativité</strong> et la <strong>qualité</strong> sont les valeurs fondamentales de Witch case, elles représentent le cœur de métier et nous sont indispensables pour proposer des solutions fiables, convaincantes et innovantes. Enfin c’est l’<strong>humanisme</strong> qui nous pousse à développer des produits <strong>utiles</strong> et <strong>libres</strong>, Il nous est essentiel que l’ensemble des utilisateurs puissent se réconcilier avec les outils informatiques. ', 'Et après...', 'Si Woody CMS est la première réalisation de Witch case, il existe déjà d\'autres projets engagés vers une future réalisation. Les technologies développées à travers Woody CMS peuvent se décliner vers d\'autres domaines d\'applications. La vision d’avenir de Witch case est une <strong>coopérative</strong>, avec une répartition <strong>collective</strong> des tâches et des décisions, afin de préserver la <strong>vision artisanale</strong> et non industrielle du métier d\'édition, et donc de garantir un haut niveau de qualité. Si vous êtes intéressé par cette aventure, nous ne saurions que trop vous conseiller de participer aux <strong>développements sur gitHub</strong> et n\'<strong>hésitez pas à nous contacter</strong> si vous le souhaitez, nous vous répondrons avec grand plaisir !', 'https://github.com/Jean2Grom/witchcase-local', 'Contribuez sur gitHub', 1);

-- --------------------------------------------------------

--
-- Structure de la table `content__witchcase-folder`
--

CREATE TABLE `content__witchcase-folder` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `headline@string#value` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `background@image#file` varchar(511) DEFAULT NULL,
  `background@image#title` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `content__witchcase-folder`
--

INSERT INTO `content__witchcase-folder` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `headline@string#value`, `body@text#value`, `background@image#file`, `background@image#title`) VALUES
(1, 'Le CMS', 1, '2023-09-19 15:55:40', 1, '2023-09-19 15:55:40', 'Le CMS', ' L\'outil qui s\'adapte aux métiers', 'img_fond_home.jpg', 'écorce'),
(2, 'Technologies', 1, '2023-09-19 16:35:13', 1, '2023-09-19 16:35:13', 'Technologies', 'Les technologies développées par le projet Witchcase', 'img_fond_technologies.jpg', 'moteur'),
(3, 'À Propos', 1, '2023-09-19 16:51:15', 1, '2023-09-25 19:07:32', 'À propos de Witch case', 'Réconcilier l\'informatique avec ses utilisateurs ', 'img_fond_apropos.jpg', 'organique');

-- --------------------------------------------------------

--
-- Structure de la table `content__witchcase-home`
--

CREATE TABLE `content__witchcase-home` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `meta-title@string#value` varchar(511) DEFAULT NULL,
  `meta-description@string#value` varchar(511) DEFAULT NULL,
  `meta-keywords@string#value` varchar(511) DEFAULT NULL,
  `logo@image#file` varchar(511) DEFAULT NULL,
  `logo@image#title` varchar(511) DEFAULT NULL,
  `contact-email@link#href` varchar(511) DEFAULT NULL,
  `contact-email@link#text` varchar(511) DEFAULT NULL,
  `contact-email@link#external` tinyint(1) DEFAULT '1',
  `call-to-action@link#href` varchar(511) DEFAULT NULL,
  `call-to-action@link#text` varchar(511) DEFAULT NULL,
  `call-to-action@link#external` tinyint(1) DEFAULT '1',
  `headline@string#value` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `background@image#file` varchar(511) DEFAULT NULL,
  `background@image#title` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `content__witchcase-home`
--

INSERT INTO `content__witchcase-home` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `meta-title@string#value`, `meta-description@string#value`, `meta-keywords@string#value`, `logo@image#file`, `logo@image#title`, `contact-email@link#href`, `contact-email@link#text`, `contact-email@link#external`, `call-to-action@link#href`, `call-to-action@link#text`, `call-to-action@link#external`, `headline@string#value`, `body@text#value`, `background@image#file`, `background@image#title`) VALUES
(1, 'Home', 1, '2023-09-14 17:03:25', 1, '2023-09-18 16:29:03', 'WitchCase', 'description du site WC', 'witchcase, cms, ecologie, arborescence', 'logo.jpg', 'logo', 'mailto:info@witchcase.com', 'info@witchcase.com', 1, 'https://github.com/Jean2Grom/witchcase-local', 'Répository GitHub', 1, 'WitchCase', 'le gestionnaire de contenus web qui s\'adapte aux métiers', 'img_fond_contact.jpg', 'witch riding broom');

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

INSERT INTO `content__wc-user` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `last-name@string#value`, `first-name@string#value`, `connection@connexion#id`) VALUES
(1, 'Administrateur', NULL, '2023-09-11 15:03:08', NULL, '2023-09-11 15:03:08', 'Witchcase', 'Administrateur', 1);

-- --------------------------------------------------------

--
-- Structure de la table `draft__witchcase-article`
--

CREATE TABLE `draft__witchcase-article` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `headline@string#value` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `headline-left@string#value` varchar(511) DEFAULT NULL,
  `body-left@text#value` text,
  `headline-center@string#value` varchar(511) DEFAULT NULL,
  `body-center@text#value` text,
  `headline-right@string#value` varchar(511) DEFAULT NULL,
  `body-right@text#value` text,
  `link@link#href` varchar(511) DEFAULT NULL,
  `link@link#text` varchar(511) DEFAULT NULL,
  `link@link#external` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `draft__witchcase-article`
--

INSERT INTO `draft__witchcase-article` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `content_key`, `headline@string#value`, `body@text#value`, `image@image#file`, `image@image#title`, `headline-left@string#value`, `body-left@text#value`, `headline-center@string#value`, `body-center@text#value`, `headline-right@string#value`, `body-right@text#value`, `link@link#href`, `link@link#text`, `link@link#external`) VALUES
(4, 'REPRENEZ LE CONTRÔLE', 1, '2023-09-26 18:31:16', 1, '2023-09-26 18:31:16', 1, 'REPRENEZ LE CONTRÔLE', '', 'logo_woody.png', 'logo woody', 'Pour les développeurs', 'Woody CMS est développé sur des technologies purement <strong>PHP/MySQL, en licence GPL</strong>. Le langage PHP est la seule connaissance requise pour y développer un site. Le concept de Woody CMS est une simplification fondamentale du stockage de données : <strong>plus de cache</strong> sur la visualisation des contenus, et aucune perte de temps due au renouvellement de ses fichiers pour valider ses développements. Les éléments contextuels (le menu par exemple) sont cachés par un système très simple et très accessible laissé aux soins du développeur. <br>De plus Woody CMS est une <strong>plateforme multisites</strong> avec un système d\'héritage et laisse une grande liberté dans le développement des modules. <br>En somme Woody CMS fonctionne comme une véritable <strong>\"boite à outils\"</strong> pour les développeurs.  ', 'Pour les utilisateurs', 'A l’heure actuelle, la contribution d’un site web implique un temps de visualisation de son travail en ligne avant de considérer ou non cette tâche achevée. Ce temps de validation doit prendre en compte la gestion du cache qui, suivant son niveau de complexité, peut prendre jusqu\'à 20 minutes pour une seule contribution. Un site implémenté avec Woody CMS permet la <strong>mise en ligne immédiate</strong> d’une information grâce à l’absence de cache de visualisation. Les contributeurs peuvent ainsi valider immédiatement leur travail. De plus Woody CMS est multisite et gère efficacement le <strong>multi-positionnement de contenus</strong>. Les contributeurs peuvent gérer différents sites via <strong>une seule et même interface</strong>, où la modification d’un contenu multipositionné est effective automatiquement à l’ensemble du site. Les visiteurs consultent alors une information instantanément à jour, sans s’impatienter devant un site ralenti par la régénération des fichiers de cache.', 'Pour les administrateurs', 'Woody CMS est un gestionnaire de contenu <strong>malléable</strong> : la forme et les emplacements sont déterminés par les administrateurs de la plateforme. L’administrateur peut créer et modifier à loisir autant de structures de contenus qu\'il souhaite, suivant la forme qu’il recherche. Par exemple la structure <em>article</em> peut contenir une introduction, une image, un lien ou encore un diaporama afin de correspondre exactement à ses besoins. Afin que les nécessités métier ne s’adaptent pas à une structure rigide, Woody CMS range les différents éléments qui constituent le site, suivant une <strong>arborescence choisie et construite en fonction des besoins requis</strong>. Pour les webmaster, Woody CMS propose nativement une gestion multisites avec héritage et une multiposition des éléments, afin de faciliter l\'administration. Pour les administrateurs réseau, une <strong>prise en compte immédiate et sans cache de la configuration</strong> permet l\'ajout et la suppression de host et de siteaccess \"à chaud\".', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `draft__witchcase-folder`
--

CREATE TABLE `draft__witchcase-folder` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `headline@string#value` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `background@image#file` varchar(511) DEFAULT NULL,
  `background@image#title` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `draft__witchcase-folder`
--

INSERT INTO `draft__witchcase-folder` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `content_key`, `headline@string#value`, `body@text#value`, `background@image#file`, `background@image#title`) VALUES
(10, 'À Propos', 1, '2023-09-26 11:33:00', 1, '2023-09-26 11:33:00', 3, 'À propos de Witch case', 'Réconcilier l\'informatique avec ses utilisateurs ', 'img_fond_apropos.jpg', 'organique');

-- --------------------------------------------------------

--
-- Structure de la table `draft__witchcase-home`
--

CREATE TABLE `draft__witchcase-home` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `meta-title@string#value` varchar(511) DEFAULT NULL,
  `meta-description@string#value` varchar(511) DEFAULT NULL,
  `meta-keywords@string#value` varchar(511) DEFAULT NULL,
  `logo@image#file` varchar(511) DEFAULT NULL,
  `logo@image#title` varchar(511) DEFAULT NULL,
  `contact-email@link#href` varchar(511) DEFAULT NULL,
  `contact-email@link#text` varchar(511) DEFAULT NULL,
  `contact-email@link#external` tinyint(1) DEFAULT '1',
  `call-to-action@link#href` varchar(511) DEFAULT NULL,
  `call-to-action@link#text` varchar(511) DEFAULT NULL,
  `call-to-action@link#external` tinyint(1) DEFAULT '1',
  `headline@string#value` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `background@image#file` varchar(511) DEFAULT NULL,
  `background@image#title` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `draft__witchcase-home`
--

INSERT INTO `draft__witchcase-home` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `content_key`, `meta-title@string#value`, `meta-description@string#value`, `meta-keywords@string#value`, `logo@image#file`, `logo@image#title`, `contact-email@link#href`, `contact-email@link#text`, `contact-email@link#external`, `call-to-action@link#href`, `call-to-action@link#text`, `call-to-action@link#external`, `headline@string#value`, `body@text#value`, `background@image#file`, `background@image#title`) VALUES
(4, 'Home', 1, '2023-09-18 17:23:51', 1, '2023-09-18 17:23:51', 1, 'WitchCase', 'description du site WC', 'witchcase, cms, ecologie, arborescence', 'logo.jpg', 'logo', 'mailto:info@witchcase.com', 'info@witchcase.com', 1, 'https://github.com/Jean2Grom/witchcase-local', 'Répository GitHub', 1, 'WitchCase', 'le gestionnaire de contenus web qui s\'adapte aux métiers', 'img_fond_contact.jpg', 'witch riding broom');

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

INSERT INTO `user__connexion` (`id`, `name`, `email`, `login`, `pass_hash`, `craft_table`, `craft_attribute`, `craft_attribute_var`, `attribute_name`, `modifier`, `modified`, `creator`, `created`) VALUES
(1, 'Administrator', 'adminstrator@witchcase', 'admin', '$2y$11$11FgVhXijP654xVeVG/VjeKIQnyRjVx0AsQ2QGQXiEx0VJeWeaGJ.', 'content__wc-user', 'connexion', 'id', 'connection', NULL, '2023-09-11 15:03:08', NULL, '2023-09-11 15:03:08');

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
(4, 2, 'login', NULL, NULL, 0, 0, 0, ''),
(5, 3, '*', 0, 14, 0, 1, 1, '');

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

INSERT INTO `user__profile` (`id`, `name`, `site`, `created`) VALUES
(1, 'administrator', '*', '2023-09-11 15:03:08'),
(2, 'public', '*', '2023-09-11 15:03:08'),
(3, 'public', 'witchcase', '2023-09-25 17:19:53');

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
(1, 1, '2023-09-11 15:03:08');

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

INSERT INTO `witch` (`id`, `name`, `data`, `site`, `url`, `status`, `invoke`, `craft_table`, `craft_fk`, `alias`, `is_main`, `context`, `datetime`, `priority`, `level_1`, `level_2`, `level_3`, `level_4`) VALUES
(1, 'Root', 'Ici se trouve la racine de la plateforme. C\'est à partir d\'ici que sont créées les homes de chaque site de la plateforme.', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, NULL, NULL, NULL, NULL),
(2, 'Admin WitchCase', 'Site d\'administration', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, 1, NULL, NULL, NULL),
(3, 'Utilisateurs', '', 'admin', 'utilisateurs', 0, '', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 10, 1, 1, NULL, NULL),
(4, 'Administrateur', '', 'admin', 'utilisateurs/administrateur', 0, '', 'content__wc-user', 1, NULL, 1, '', '2023-09-11 15:03:08', 0, 1, 1, 1, NULL),
(5, 'Home', '', 'admin', '', 0, 'root', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, 1, 2, NULL, NULL),
(6, 'Login', 'Module de déconnexion/connexion', 'admin', 'login', 0, 'login', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 40, 1, 2, 1, NULL),
(7, 'Witch', 'Visualisation des Witches, c\'est a dire de chaque point de l\'arborescence -appelé ici Matriarcat. Chacun de ces points peut être associé à un contenu et/ou à un module exécutable. \r\nOn peut également définir une URL permettant de cibler cette witch.', 'admin', 'view', 0, 'view', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 30, 1, 2, 2, NULL),
(8, 'Edit Witch', '', 'admin', 'edit', 0, 'edit', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 20, 1, 2, 3, NULL),
(9, 'Edit Craft', 'This is the draft of craft, you can publish it, save it for later, or remove draft to cancel modification.', 'admin', 'edit-content', 0, 'contents/edit', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 10, 1, 2, 4, NULL),
(10, 'Menu', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, 1, 2, 5, NULL),
(11, 'Profiles', 'Permissions handeling is based on user profiles.', 'admin', 'profiles', 0, 'profiles', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, 1, 2, 5, 1),
(12, 'Structures', '', 'admin', 'structures', 0, 'structures', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, 1, 2, 5, 2),
(13, 'Site Witchcase', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-09-12 11:04:11', 0, 2, NULL, NULL, NULL),
(14, 'Home', '', 'witchcase', '', 0, 'view', 'content__witchcase-home', 1, NULL, 1, NULL, '2023-09-14 16:54:36', 0, 2, 1, NULL, NULL),
(15, 'Le CMS', '', 'witchcase', 'le-cms', 0, 'view', 'content__witchcase-folder', 1, NULL, 1, NULL, '2023-09-19 15:53:49', 0, 2, 1, 1, NULL),
(16, 'Technologies', '', 'witchcase', 'technologies', 0, 'view', 'content__witchcase-folder', 2, NULL, 1, NULL, '2023-09-19 16:33:02', 0, 2, 1, 2, NULL),
(17, 'À Propos', '', 'witchcase', 'a-propos', 0, 'view', 'content__witchcase-folder', 3, NULL, 1, NULL, '2023-09-19 16:35:59', 0, 2, 1, 3, NULL),
(18, 'REPRENEZ LE CONTRÔLE', 'Woody CMS est le premier CMS dont la technologie est orientée vers les acteurs du web. Que vous soyez développeur, contributeur, webmaster ou même administrateur réseau, ce gestionnaire de contenu (CMS) a pour objectif de vous simplifier la vie en supprimant les attentes interminables dues au fonctionnement d\'un site web, tout en bénéficiant de la malléabilité exigée d\'un site qui vous représente.', NULL, NULL, 0, NULL, 'content__witchcase-article', 1, NULL, 1, NULL, '2023-09-26 16:33:33', 0, 2, 1, 1, 1),
(19, 'Woody CMS en quelques mots', 'Woody CMS est un prototype de gestion de contenu, à partir duquel on développe, contribue et administre un site web. Woody CMS propose un modèle de stockage des données en base, gère la sécurité, assure une partition MVC des codes, et propose une interface web pour administrer son ou ses site(s).', NULL, NULL, 0, NULL, 'content__witchcase-article', 2, NULL, 1, NULL, '2023-09-26 16:39:44', 0, 2, 1, 1, 2),
(20, 'Contacter', '', 'witchcase', 'contacter', 0, 'contact', NULL, NULL, NULL, 1, NULL, '2023-09-27 11:48:37', -9, 2, 1, 4, NULL),
(21, 'FONCTIONNEMENT GLOBAL', 'Ici nous représentons les flux avec un diagramme de séquence, depuis la requête du navigateur jusqu\'à la visualisation de la page. Nous avons mis ici en valeur la structure MVC (Model View Controller) de Woody CMS.', NULL, NULL, 0, NULL, 'content__witchcase-article', 3, NULL, 1, NULL, '2023-09-28 18:40:14', 40, 2, 1, 2, 1),
(22, 'EMPLACEMENT MATRICIEL', 'Une des innovations utilisées par Woody CMS est la gestion de l\'arborescence avec des coordonnées matricielles. Cette technique permet d’avoir qu\'une unique requête pour déterminer l\'arborescence de l\'emplacement auquel nous accèdons.', NULL, NULL, 0, NULL, 'content__witchcase-article', 4, NULL, 1, NULL, '2023-09-28 18:45:14', 30, 2, 1, 2, 2),
(23, 'CONTENU AJUSTABLE', 'Afin de récupérer toutes les informations du contenu à afficher, Witch case a développé une solution qui consiste à ajuster en direct la table correspondant au contenu à afficher. Ainsi, on peut récupérer l\'ensemble des informations en une seule requête.', NULL, NULL, 0, NULL, 'content__witchcase-article', 5, NULL, 1, NULL, '2023-09-28 18:48:54', 20, 2, 1, 2, 3),
(24, 'NOMMAGE STRUCTUREL DES CHAMPS', 'Afin de permettre une plus grande complexité des attributs qui composent un contenu, il faut connaitre sa structure et son comportement avant d\'envoyer la requête visant à récupérer les contenus. Pour cela nous identifions les champs en BDD par un nommage structurel.', NULL, NULL, 0, NULL, 'content__witchcase-article', 6, NULL, 1, NULL, '2023-09-28 19:02:27', 10, 2, 1, 2, 4),
(25, 'WITCH CASE EN BREF', 'Witch case est une société d\'édition web crée en 2016 par Jean de Gromard. Ingénieur de formation, il a passé 10 ans dans les technologies du web dont 5 à se spécialiser dans l\'intégration de site via des CMS, avec une expertise sur eZPublish. Woody CMS est le premier projet de Witch case. Le prototype a été développé entre 2015 et 2016, sur une durée d\'environ un an.', NULL, NULL, 0, NULL, 'content__witchcase-article', 7, NULL, 1, NULL, '2023-09-28 19:06:12', 0, 2, 1, 3, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `archive__witchcase-article`
--
ALTER TABLE `archive__witchcase-article`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__witchcase-folder`
--
ALTER TABLE `archive__witchcase-folder`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__witchcase-home`
--
ALTER TABLE `archive__witchcase-home`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__wc-user`
--
ALTER TABLE `archive__wc-user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__witchcase-article`
--
ALTER TABLE `content__witchcase-article`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__witchcase-folder`
--
ALTER TABLE `content__witchcase-folder`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__witchcase-home`
--
ALTER TABLE `content__witchcase-home`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__wc-user`
--
ALTER TABLE `content__wc-user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__witchcase-article`
--
ALTER TABLE `draft__witchcase-article`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__witchcase-folder`
--
ALTER TABLE `draft__witchcase-folder`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__witchcase-home`
--
ALTER TABLE `draft__witchcase-home`
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
-- AUTO_INCREMENT pour la table `archive__witchcase-article`
--
ALTER TABLE `archive__witchcase-article`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `archive__witchcase-folder`
--
ALTER TABLE `archive__witchcase-folder`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `archive__witchcase-home`
--
ALTER TABLE `archive__witchcase-home`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `content__witchcase-article`
--
ALTER TABLE `content__witchcase-article`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `content__witchcase-folder`
--
ALTER TABLE `content__witchcase-folder`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `content__witchcase-home`
--
ALTER TABLE `content__witchcase-home`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `content__wc-user`
--
ALTER TABLE `content__wc-user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
--
-- AUTO_INCREMENT pour la table `draft__wc-user`
--
ALTER TABLE `draft__wc-user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
--
-- AUTO_INCREMENT pour la table `archive__wc-user`
--
ALTER TABLE `archive__wc-user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT pour la table `draft__witchcase-article`
--
ALTER TABLE `draft__witchcase-article`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `draft__witchcase-folder`
--
ALTER TABLE `draft__witchcase-folder`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `draft__witchcase-home`
--
ALTER TABLE `draft__witchcase-home`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `user__connexion`
--
ALTER TABLE `user__connexion`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `user__policy`
--
ALTER TABLE `user__policy`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `user__profile`
--
ALTER TABLE `user__profile`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `witch`
--
ALTER TABLE `witch`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
