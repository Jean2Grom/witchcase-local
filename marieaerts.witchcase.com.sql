-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : ven. 13 oct. 2023 à 16:05
-- Version du serveur : 8.1.0
-- Version de PHP : 8.2.11

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

-- --------------------------------------------------------

--
-- Structure de la table `archive__article-witchcase`
--

CREATE TABLE `archive__article-witchcase` (
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
-- Structure de la table `archive__folder-witchcase`
--

CREATE TABLE `archive__folder-witchcase` (
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
-- Structure de la table `archive__home-witchcase`
--

CREATE TABLE `archive__home-witchcase` (
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
-- Structure de la table `archive__marieaerts-artwork-element`
--

CREATE TABLE `archive__marieaerts-artwork-element` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `embed-player@string#value` varchar(511) DEFAULT NULL,
  `text@text#value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `archive__marieaerts-document`
--

CREATE TABLE `archive__marieaerts-document` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `file@file#file` varchar(511) DEFAULT NULL,
  `file@file#title` varchar(511) DEFAULT NULL,
  `author@string#value` varchar(511) DEFAULT NULL,
  `information@string#value` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `archive__marieaerts-event`
--

CREATE TABLE `archive__marieaerts-event` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `title@string#value` varchar(511) DEFAULT NULL,
  `head@string#value` varchar(511) DEFAULT NULL,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `link@link#href` varchar(511) DEFAULT NULL,
  `link@link#text` varchar(511) DEFAULT NULL,
  `link@link#external` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `archive__marieaerts-home`
--

CREATE TABLE `archive__marieaerts-home` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `title@string#value` varchar(511) DEFAULT NULL,
  `meta-title@string#value` varchar(511) DEFAULT NULL,
  `meta-description@string#value` varchar(511) DEFAULT NULL,
  `meta-keywords@string#value` varchar(511) DEFAULT NULL,
  `contact-email@link#href` varchar(511) DEFAULT NULL,
  `contact-email@link#text` varchar(511) DEFAULT NULL,
  `contact-email@link#external` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `archive__marieaerts-page`
--

CREATE TABLE `archive__marieaerts-page` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `title@string#value` varchar(511) DEFAULT NULL,
  `description@text#value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `body@text#value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `content__article-witchcase`
--

CREATE TABLE `content__article-witchcase` (
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
-- Déchargement des données de la table `content__article-witchcase`
--

INSERT INTO `content__article-witchcase` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `headline@string#value`, `body@text#value`, `image@image#file`, `image@image#title`, `headline-left@string#value`, `body-left@text#value`, `headline-center@string#value`, `body-center@text#value`, `headline-right@string#value`, `body-right@text#value`, `link@link#href`, `link@link#text`, `link@link#external`) VALUES
(1, 'REPRENEZ LE CONTRÔLE', 1, '2023-09-26 16:37:27', 1, '2023-09-26 16:49:01', 'REPRENEZ LE CONTRÔLE', '', 'logo_woody.png', 'logo woody', 'Pour les développeurs', 'Woody CMS est développé sur des technologies purement <strong>PHP/MySQL, en licence GPL</strong>. Le langage PHP est la seule connaissance requise pour y développer un site. Le concept de Woody CMS est une simplification fondamentale du stockage de données : <strong>plus de cache</strong> sur la visualisation des contenus, et aucune perte de temps due au renouvellement de ses fichiers pour valider ses développements. Les éléments contextuels (le menu par exemple) sont cachés par un système très simple et très accessible laissé aux soins du développeur. <br>De plus Woody CMS est une <strong>plateforme multisites</strong> avec un système d\'héritage et laisse une grande liberté dans le développement des modules. <br>En somme Woody CMS fonctionne comme une véritable <strong>\"boite à outils\"</strong> pour les développeurs.  ', 'Pour les utilisateurs', 'A l’heure actuelle, la contribution d’un site web implique un temps de visualisation de son travail en ligne avant de considérer ou non cette tâche achevée. Ce temps de validation doit prendre en compte la gestion du cache qui, suivant son niveau de complexité, peut prendre jusqu\'à 20 minutes pour une seule contribution. Un site implémenté avec Woody CMS permet la <strong>mise en ligne immédiate</strong> d’une information grâce à l’absence de cache de visualisation. Les contributeurs peuvent ainsi valider immédiatement leur travail. De plus Woody CMS est multisite et gère efficacement le <strong>multi-positionnement de contenus</strong>. Les contributeurs peuvent gérer différents sites via <strong>une seule et même interface</strong>, où la modification d’un contenu multipositionné est effective automatiquement à l’ensemble du site. Les visiteurs consultent alors une information instantanément à jour, sans s’impatienter devant un site ralenti par la régénération des fichiers de cache.', 'Pour les administrateurs', 'Woody CMS est un gestionnaire de contenu <strong>malléable</strong> : la forme et les emplacements sont déterminés par les administrateurs de la plateforme. L’administrateur peut créer et modifier à loisir autant de structures de contenus qu\'il souhaite, suivant la forme qu’il recherche. Par exemple la structure <em>article</em> peut contenir une introduction, une image, un lien ou encore un diaporama afin de correspondre exactement à ses besoins. Afin que les nécessités métier ne s’adaptent pas à une structure rigide, Woody CMS range les différents éléments qui constituent le site, suivant une <strong>arborescence choisie et construite en fonction des besoins requis</strong>. Pour les webmaster, Woody CMS propose nativement une gestion multisites avec héritage et une multiposition des éléments, afin de faciliter l\'administration. Pour les administrateurs réseau, une <strong>prise en compte immédiate et sans cache de la configuration</strong> permet l\'ajout et la suppression de host et de siteaccess \"à chaud\".', NULL, NULL, 1),
(2, 'Woody CMS en quelques mots', 1, '2023-09-26 16:47:28', 1, '2023-09-26 18:41:25', 'Woody CMS en quelques mots', 'Voici une copie d\'écran de l\'interface Woody CMS. Ici nous visualisons l\'élément de la home du site de Witch case. Nous distinguons à gauche les informations sur l\'élément <em>home</em> : le module est <em>view</em> (module de visualisation de contenus), le contenu associé n\'est plus un brouillon et n\'est pas une archive, il est indiqué <em>Content</em>. Le type de structure du contenu est <em>home-demo</em>, c\'est-à-dire la page d\'accueil. En dessous, nous avons la partie dédiée à l’emplacement, et plus en dessous encore le tableau des sous-éléments. A droite nous avons une visualisation des attributs du contenu.', 'WoodyEcran.png', ' Capture d\'écran de l\'interface d\'administration ', 'Malléabilité', 'Woody CMS est un gestionnaire dont l’arborescence et la <strong>structure des contenus sont gérés à 100% par ses utilisateurs</strong>. Cela permet d\'adapter ce CMS aux besoins métiers et non l\'inverse. Woody CMS permet la gestion multisites avec héritage. Dans le cas de plusieurs sites à structure identique, le nouveau site héritera des codes des précédents sites. Il ne restera plus qu’à développer la spécificité du nouveau site. Il permet également la multiposition d\'éléments, afin de <strong>modifier en une seule action</strong> un élément présent sur plusieurs emplacements. Chacune des positions est associée à un module (un fichier PHP), et peut être associé, ou non, à un contenu. Cela permet d\'ajouter facilement un nouveau traitement par le biais d\'un nouveau module. Woody CMS est une véritable \"boite à outils\" qui <strong>offre une grande liberté</strong> au développement et à l\'administration.', 'Spécificité', 'Woody CMS est fondamentalement orienté vers les professionnels du web, tout en étant fondamentalement flexible aux besoins métiers, tant dans sa structure de données que dans sa gestion de l\'arborescence. Il est tout d’abord dirigé vers le développement : en supprimant la majorité des fichiers de cache, il <strong>diminue significativement les temps</strong> de validation des codes développés. L\'accès au développement de modules spécifique est aussi simplifié : toute la structure des codes est en MVC mais reste à <strong>100% PHP5</strong>. L\'absence de cache de visualisation permet également de réduire les temps de contribution et d\'administration en ayant <strong>instantanément accès à l\'information</strong> par une récupération systématique des données en base.', 'Le projet ', ' Woody CMS est actuellement un prototype. De nombreuses améliorations sont en cours de développement. Nous ne saurons que trop vous remercier si vous souhaitez <strong>contribuer à son développement</strong>. <br>Il est sous <strong>licence GPL</strong> et développé à <strong>100% en PHP5</strong>, sans utilisation de templating (nous utilisons du code PHP dans les pages de visualisation). <br>Le développement de modules d\'utilisation courante est en cours, l\'ajout d\'attributs possible est également en cours, ainsi que des améliorations au niveau du moteur. <br>Le projet est là et bien vivant, <strong>nous n\'attendons plus que vous !</strong>', 'https://github.com/Jean2Grom/witchcase-local', 'Contribuez sur GitHub', 1),
(3, 'FONCTIONNEMENT GLOBAL', 1, '2023-09-28 18:44:00', 1, '2023-09-28 18:44:00', 'FONCTIONNEMENT GLOBAL', 'Schéma séquence MVC (simplifié)', 'MVC-schema-full.jpg', 'schema MVC', 'Partie <em>View</em>', '<strong>1.</strong> Pour qu\'une page s\'affiche, nous envoyons une requête HTTP (typiquement avec son navigateur web). <br/>\r\n<strong>8.</strong> Nous avons alors toutes les informations nécessaires pour les traitements du module. Nous récupérons alors le fichier \"design\" du module, qui produira l\'apparence du contenu de la page web (le code HTML de la partie évolutive du site, souvent centrale).  <br/>\r\n<strong>10.</strong> Nous récupérons alors le fichier \"design\" (voir plus haut) lié au contexte, on y inclut le résultat du module et nous affichons la page ainsi générée. \r\n', 'Partie <em>Controller</em>', '<strong>2.</strong> Le serveur qui reçoit la requête identifie en premier lieu l\'utilisateur (logé ou non). <br/>\r\n<strong>3.</strong> Le système détermine via le fichier de configuration à quel site la requête fait appel, Woody CMS étant un système multi-sites. <br/>\r\n<strong>5.</strong> Cette dernière requête a récupéré l’information du \"module\" souhaité, le système exécute alors le fichier correspondant à ce module.  <br/>\r\n<strong>9.</strong> Une fois ce code récupéré, il est stocké et nous déterminons quel sera le contexte (c\'est-à-dire la partie de la page HTML qui est partagée par plusieurs pages et qui revient donc régulièrement), les traitements de ce contexte sont effectués. \r\n', 'Partie <em>Model</em>', '<strong>4.</strong> Une fois le site et l\'URL identifiés, nous déterminons l\'emplacement dans l\'arborescence du contenu désiré en envoyant une requête sur la table des emplacements. <br/>\r\n<strong>6.</strong> La requête d\'emplacement a également fourni le contenu cible appelé ici \"target\". Nous envoyons alors une requête simple sur la structure de la table en base de données qui contient ce contenu. <br/>\r\n<strong>7.</strong> Nous obtenons la structure du contenu. Nous regardons alors si d\'autres informations sont souhaitées et nous ajustons si besoin la requête qui doit récupérer ce contenu.\r\n', '', '', 1),
(4, 'EMPLACEMENT MATRICIEL', 1, '2023-09-28 18:47:32', 1, '2023-09-28 19:01:12', 'EMPLACEMENT MATRICIEL', 'Le premier schéma nous montre un enregistrement de la table d\'emplacement, \"n\" étant la profondeur maximum de l\'arborescence. C\'est la traduction à l\'échelle unitaire du stockage des coordonnées matricielles. Le schéma suivant démontre la possibilité de situer tout emplacement dans l\'arborescence, en utilisant un champ par niveau de profondeur. Si l’on veut ajouter un élément dont la profondeur dans l\'arborescence est plus grande d\'un niveau que celle prévue par le nombre de champs, le problème est que nous ne pouvons plus situer cet élément dans ce nouveau niveau d\'arborescence. La solution est d\'ajouter un champ \"à chaud\", avec une valeur nulle par défaut. L\'ajout de ce champ n\'altère donc en rien l\'intégrité des données déjà présentes et résout le problème.', 'donnees_arbo_schema-full.jpg', 'Schémas emplacement matriciel', '', '', '', '', '', '', '', '', 1),
(5, 'CONTENU AJUSTABLE', 1, '2023-09-28 18:50:20', 1, '2023-09-28 18:50:20', 'CONTENU AJUSTABLE', 'Pour un CMS malléable, la solution actuelle et courante de stockage des contenus, comme le montre le premier schéma, est comme le montre le premier schéma : chaque rectangle représente un enregistrement dans une table séparée des autres carrés (ceci est le fonctionnement global, des exceptions peuvent exister notamment si on est en présence de deux attributs de même type). La solution développée pour Woody CMS : une seule table est nécessaire pour stocker l\'ensemble des contenus. Nous noterons que cette innovation est fortement liée à la suivante, qui est de placer la structure dans le titre des champs de la table.', 'solution_courante_schema-full.jpg', 'Schéma contenu ajustable', '', '', '', '', '', '', '', '', 1),
(6, 'NOMMAGE STRUCTUREL DES CHAMPS', 1, '2023-09-28 19:03:31', 1, '2023-09-28 19:03:31', 'NOMMAGE STRUCTUREL DES CHAMPS', 'Les noms de champs sont nommés de façon à ce que leur simple lecture permette la structuration complète de la donnée, et ainsi de pouvoir ajuster la requête qui doit récupérer le contenu. Nous avons besoin que d\'une seule requête pour récupérer tous les contenus utiles en base de données.', 'nomage_schema_recadre-full.jpg', 'schema nommage structurel', '', '', '', '', '', '', '', '', 1),
(7, 'WITCH CASE EN BREF', 1, '2023-09-28 19:08:17', 1, '2023-09-28 19:08:17', 'WITCH CASE EN BREF', '', '', '', 'Pourquoi ?', 'C’est en constatant que le développement informatique relève d’une <strong>nouvelle forme d’artisanat</strong>, contrairement à l’évolution actuelle du métier de développeur, que Witch case est née. L\'industrialisation et la normalisation des besoins actuellement en cours dans la profession nuit à l’<strong>adaptabilité</strong>, alors qu’elle représente une question <strong>primordiale</strong> dans le domaine de l’informatique, et plus spécifiquement dans celui du web. C’est par la volonté d’affirmer notre vision du métier, en proposant des produits <strong>innovants et de qualité</strong>, que Witch case a été fondé. ', 'Les valeurs ', 'L\'<strong>intégrité</strong> est sans doute la première des valeurs de Witch case, le respect humain et l\'honnêteté sont des points sur lesquels nous ne pouvons transiger. La <strong>créativité</strong> et la <strong>qualité</strong> sont les valeurs fondamentales de Witch case, elles représentent le cœur de métier et nous sont indispensables pour proposer des solutions fiables, convaincantes et innovantes. Enfin c’est l’<strong>humanisme</strong> qui nous pousse à développer des produits <strong>utiles</strong> et <strong>libres</strong>, Il nous est essentiel que l’ensemble des utilisateurs puissent se réconcilier avec les outils informatiques. ', 'Et après...', 'Si Woody CMS est la première réalisation de Witch case, il existe déjà d\'autres projets engagés vers une future réalisation. Les technologies développées à travers Woody CMS peuvent se décliner vers d\'autres domaines d\'applications. La vision d’avenir de Witch case est une <strong>coopérative</strong>, avec une répartition <strong>collective</strong> des tâches et des décisions, afin de préserver la <strong>vision artisanale</strong> et non industrielle du métier d\'édition, et donc de garantir un haut niveau de qualité. Si vous êtes intéressé par cette aventure, nous ne saurions que trop vous conseiller de participer aux <strong>développements sur gitHub</strong> et n\'<strong>hésitez pas à nous contacter</strong> si vous le souhaitez, nous vous répondrons avec grand plaisir !', 'https://github.com/Jean2Grom/witchcase-local', 'Contribuez sur gitHub', 1);

-- --------------------------------------------------------

--
-- Structure de la table `content__folder-witchcase`
--

CREATE TABLE `content__folder-witchcase` (
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
-- Déchargement des données de la table `content__folder-witchcase`
--

INSERT INTO `content__folder-witchcase` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `headline@string#value`, `body@text#value`, `background@image#file`, `background@image#title`) VALUES
(1, 'Le CMS', 1, '2023-09-19 15:55:40', 1, '2023-09-19 15:55:40', 'Le CMS', ' L\'outil qui s\'adapte aux métiers', 'img_fond_home.jpg', 'écorce'),
(2, 'Technologies', 1, '2023-09-19 16:35:13', 1, '2023-09-19 16:35:13', 'Technologies', 'Les technologies développées par le projet Witchcase', 'img_fond_technologies.jpg', 'moteur'),
(3, 'À Propos', 1, '2023-09-19 16:51:15', 1, '2023-09-25 19:07:32', 'À propos de Witch case', 'Réconcilier l\'informatique avec ses utilisateurs ', 'img_fond_apropos.jpg', 'organique');

-- --------------------------------------------------------

--
-- Structure de la table `content__home-witchcase`
--

CREATE TABLE `content__home-witchcase` (
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
-- Déchargement des données de la table `content__home-witchcase`
--

INSERT INTO `content__home-witchcase` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `meta-title@string#value`, `meta-description@string#value`, `meta-keywords@string#value`, `logo@image#file`, `logo@image#title`, `contact-email@link#href`, `contact-email@link#text`, `contact-email@link#external`, `call-to-action@link#href`, `call-to-action@link#text`, `call-to-action@link#external`, `headline@string#value`, `body@text#value`, `background@image#file`, `background@image#title`) VALUES
(1, 'Home', 1, '2023-09-14 17:03:25', 1, '2023-09-18 16:29:03', 'WitchCase', 'description du site WC', 'witchcase, cms, ecologie, arborescence', 'logo.jpg', 'logo', 'mailto:info@witchcase.com', 'info@witchcase.com', 1, 'https://github.com/Jean2Grom/witchcase-local', 'Répository GitHub', 1, 'WitchCase', 'le gestionnaire de contenus web qui s\'adapte aux métiers', 'img_fond_contact.jpg', 'witch riding broom');

-- --------------------------------------------------------

--
-- Structure de la table `content__marieaerts-artwork-element`
--

CREATE TABLE `content__marieaerts-artwork-element` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `embed-player@string#value` varchar(511) DEFAULT NULL,
  `text@text#value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `content__marieaerts-artwork-element`
--

INSERT INTO `content__marieaerts-artwork-element` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `image@image#file`, `image@image#title`, `embed-player@string#value`, `text@text#value`) VALUES
(1, 'Python', 1, '2023-10-09 18:35:15', 1, '2023-10-09 18:35:15', 'Python_cal9.jpg', 'Python cal.9, série Armes/Guns series, papier Vinci 320g, 120 x 80 cm, 2010 - collection privée', '', ''),
(2, 'Lady Smith', 1, '2023-10-09 18:37:23', 1, '2023-10-09 18:37:23', 'lady_smith.jpg', 'Lady Smith, série Armes/Guns series, papier Vinci 320g, 120 x 80 cm, 2010', '', ''),
(3, 'Ingram', 1, '2023-10-09 18:38:09', 1, '2023-10-09 18:38:09', 'Ingram_mac10.jpg', 'Ingram Mac.10, série Armes/Guns series, papier Vinci 320g, 120 x 80 cm, 2010', '', ''),
(4, 'Kalachnikov', 1, '2023-10-09 18:38:57', 1, '2023-10-09 18:38:57', 'AK47.jpg', 'Kalachnikov AK 47, série Armes/Guns series, papier Vinci 320g, 120 x 80 cm, 2010', '', ''),
(5, 'Smith&Wesson', 1, '2023-10-09 18:39:57', 1, '2023-10-09 18:39:57', 'Smithwesson_magnum500.jpg', 'Smith&Wesson magnum 500, série Armes/Guns series, papier Vinci 320g, 120 x 80 cm, 2010', '', ''),
(6, 'Olympic', 1, '2023-10-09 18:40:44', 1, '2023-10-09 18:40:44', 'olympic_cal9.jpg', 'Olympic cal.9, série Armes/Guns series, papier Vinci 320g, 120 x 80 cm, 2010 - collection privée', '', ''),
(7, 'Mauser', 1, '2023-10-09 18:41:28', 1, '2023-10-09 18:41:28', 'mauser.jpg', 'Mauser, série Armes/Guns series, papier Vinci 320g, 120 x 80 cm, 2010', '', ''),
(8, 'Aug 77', 1, '2023-10-09 18:42:16', 1, '2023-10-10 00:06:54', 'Aug77.jpg', 'Aug 77, série Armes/Guns series, papier Vinci 320g, 120 x 80 cm, 2010', '', ''),
(9, 'illustration', 1, '2023-10-10 00:16:35', 1, '2023-10-10 00:18:09', 'commemoration_img1.jpg', 'Commémoration/Commemoration, action, video DV, sound, 16:9, 10\'54', '', '<p>Entre tragique et comique, cette performance révèle toute l’impuissance du personnage de «L’homme sans tête».</p>\r\n<p>Éclatant en pleine ascension, les «têtes» de substitution que cet être tente de se satisfaire, le plongent dans son propre désarroi, laissant éclater sa profonde incapacité à vivre. </p>'),
(10, 'img 1', 1, '2023-10-10 00:25:07', 1, '2023-10-10 00:25:07', 'commemoration_img1.jpg', '', '', ''),
(11, 'img 2', 1, '2023-10-10 00:25:47', 1, '2023-10-10 00:25:47', 'commemoration_img2.jpg', '', '', ''),
(12, 'img 3', 1, '2023-10-10 00:26:15', 1, '2023-10-10 00:26:15', 'commemoration_img3.jpg', '', '', ''),
(13, 'img 4', 1, '2023-10-10 00:26:39', 1, '2023-10-10 00:26:39', 'commemoration_img4.jpg', '', '', ''),
(14, 'extrait vidéo', 1, '2023-10-10 00:27:38', 1, '2023-10-10 00:27:38', '', '', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/HtaQP7z5_jY?si=ytqi3VaWJ_C__ZTo\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>', ''),
(15, 'Devoir', 1, '2023-10-12 11:01:15', 1, '2023-10-12 15:15:33', 'devoir.jpg', 'Devoir/Duty, tirage lambda / lambda print, color, 120 x 77 cm, 2012', '', ''),
(16, 'texte Marianne', 1, '2023-10-12 15:14:53', 1, '2023-10-12 15:14:53', '', '', '', 'La photographie Devoir met en scène quatre hommes sans tête. Figure récurrente dans la production de Marie Aerts, l’homme sans tête, un être générique à caractère humain sans passé ni avenir, est vide de pensée et de désir.\r\n<br/>\r\n<br/>\r\nInspirée du chef d’oeuvre pictural La liberté guidant le peuple, la photographie de Marie Aerts réinterprète dans un geste parodique la célèbre allégorie de la Révolution Française. On retrouve, dans la composition de Devoir, le drapeau réduit à un mouchoir de poche et le mime du pistolet brandi. L’artiste interroge notre désir de liberté partagé entre une aspiration à la rébellion et un espoir de paix.\r\n<br/>\r\n<br/>\r\nMarianne Feder '),
(17, 'Fée clochette', 1, '2023-10-13 16:41:09', 1, '2023-10-13 16:41:09', 'feeclochette.jpg', 'Fée clochette/Tinker bell, graphite sur papier Clairefontaine 250g, 42 x 29,7 cm, 2012', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `content__marieaerts-document`
--

CREATE TABLE `content__marieaerts-document` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `file@file#file` varchar(511) DEFAULT NULL,
  `file@file#title` varchar(511) DEFAULT NULL,
  `author@string#value` varchar(511) DEFAULT NULL,
  `information@string#value` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `content__marieaerts-document`
--

INSERT INTO `content__marieaerts-document` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `file@file#file`, `file@file#title`, `author@string#value`, `information@string#value`) VALUES
(2, 'Marie Aerts, le vide démasqué', 1, '2023-10-10 17:43:44', 1, '2023-10-10 17:43:44', 'textAlice_FR.pdf', 'Marie Aerts, le vide démasqué', 'Alice Laguarda, philosophe, critique d\'art et architecte', '2010'),
(3, 'Les fantômes en habits noirs de Marie Aerts', 1, '2023-10-10 18:22:00', 1, '2023-10-10 18:22:00', 'TurbulencesVideos.pdf', 'Les fantômes en habits noirs de Marie Aerts', 'Gilbert Pons', 'in Turbulences vidéos, automne 2012'),
(4, 'CV', 1, '2023-10-13 16:55:51', 1, '2023-10-13 16:55:51', 'CV.pdf', 'CV', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `content__marieaerts-event`
--

CREATE TABLE `content__marieaerts-event` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `title@string#value` varchar(511) DEFAULT NULL,
  `head@string#value` varchar(511) DEFAULT NULL,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `link@link#href` varchar(511) DEFAULT NULL,
  `link@link#text` varchar(511) DEFAULT NULL,
  `link@link#external` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `content__marieaerts-event`
--

INSERT INTO `content__marieaerts-event` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `title@string#value`, `head@string#value`, `image@image#file`, `image@image#title`, `body@text#value`, `link@link#href`, `link@link#text`, `link@link#external`) VALUES
(1, 'Chimères de l\'Ailleurs', 1, '2023-10-12 15:38:20', 1, '2023-10-12 15:38:20', 'Exposition collective \"Chimères de l\'Ailleurs\", Lille 3000, SPARK, BAR (Bureau d\'Art et de Recherche)', 'Exposition du 18 mai au 27 juillet 2019, Qsp Galerie, 112 avenue Jean Lebas, 59100 Roubaix', 'Flyer_Chimeresdelailleurs-2.jpg', '', 'Avec la participation de Marie Aerts, Vir Andres Hera, Frédéric Bruly-Bouabré, Patrick Chapelière, Bertrand Dezoteux, Romuald Jandolo, Augustin Lesage et Rémi Tamburini.', 'https://www.facebook.com/spark.asso', 'En savoir plus', 1);

-- --------------------------------------------------------

--
-- Structure de la table `content__marieaerts-home`
--

CREATE TABLE `content__marieaerts-home` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `title@string#value` varchar(511) DEFAULT NULL,
  `meta-title@string#value` varchar(511) DEFAULT NULL,
  `meta-description@string#value` varchar(511) DEFAULT NULL,
  `meta-keywords@string#value` varchar(511) DEFAULT NULL,
  `contact-email@link#href` varchar(511) DEFAULT NULL,
  `contact-email@link#text` varchar(511) DEFAULT NULL,
  `contact-email@link#external` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `content__marieaerts-home`
--

INSERT INTO `content__marieaerts-home` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `title@string#value`, `meta-title@string#value`, `meta-description@string#value`, `meta-keywords@string#value`, `contact-email@link#href`, `contact-email@link#text`, `contact-email@link#external`) VALUES
(1, 'Marie Aerts', 1, '2023-10-04 19:06:08', 1, '2023-10-04 19:06:08', 'Marie Aerts', 'Marie Aerts | Artiste Plasticienne', 'Le travail de Marie Aerts questionne les notions de pouvoir et d\'organisation sociale qui régissent les sociétés humaines. L\'artiste interroge les méthodes de légitimation  des différentes formes de domination, et les rapports que celles-ci entretiennent avec la croyance.', 'Marie Aerts, aerts, art, artiste, placticienne, dessin, photo, peinture, vidéo, video, installation, performance', 'mailto:aertsmarie@gmail.com', 'aertsmarie@gmail.com', 1);

-- --------------------------------------------------------

--
-- Structure de la table `content__marieaerts-page`
--

CREATE TABLE `content__marieaerts-page` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `title@string#value` varchar(511) DEFAULT NULL,
  `description@text#value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `body@text#value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `content__marieaerts-page`
--

INSERT INTO `content__marieaerts-page` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `title@string#value`, `description@text#value`, `image@image#file`, `image@image#title`, `body@text#value`) VALUES
(1, 'documents', 1, '2023-10-10 11:43:55', 1, '2023-10-10 18:18:51', 'Documents', NULL, '', '', ''),
(2, 'Devoir', 1, '2023-10-12 10:58:18', 1, '2023-10-12 10:58:18', 'Devoir', '', '', '', ''),
(3, 'Armes', 1, '2023-10-12 14:59:11', 1, '2023-10-12 14:59:11', 'Armes', '2010', '', '', 'Graphite sur papier Vinci 320g, série de 8 dessins.'),
(4, 'Commémoration', 1, '2023-10-12 15:01:31', 1, '2023-10-12 15:01:31', 'Commémoration', '2011', '', '', 'Action réalisée à la Galerie DIx9, lors de l\'exposition \"Débarquement\", en septembre 2011. Captation vidéo Anne Lehennaf.'),
(5, 'Fée clochette', 1, '2023-10-13 16:42:47', 1, '2023-10-13 16:42:47', 'Fée clochette', '2012', '', '', 'Graphite sur papier Clairefontaine 250g, 42 x 29,7 cm'),
(6, 'bio', 1, '2023-10-13 16:54:07', 1, '2023-10-13 16:54:07', 'Parcours', 'Diplomée (DNSEP) de l’ESAM (Caen/Cherbourg) avec félicitation du jury\r\n1er prix du festival Bandits-Mages pour la vidéo Débarquement 3', '', '', 'Lors d’un séjour à Londres en 2007, inspiré de l’atmosphère du quartier d’affaires de la City, est né le premier\r\nprototype de l’homme sans tête. Cette figure du Gnome donnera lieu à des manifestations dans divers lieux d’art\r\ncontemporain londoniens (exposition collective et performances avec le groupe Engine Chat Chat).\r\n<br/>\r\n<br/>\r\nDe retour à Paris, résidences d’artistes, et participations à des expositions collectives se sont multipliées. Avec\r\nnotamment «Watch your step» au WHARF, Centre d’Art Contemporain d’Hérouville St Clair (2007) ou «Le grand\r\nPari(s) de l’art contemporain» à l’Abbaye de Maubuisson (2010).\r\n<br/>\r\n<br/>\r\nL’homme sans tête devient ainsi l’objet central de divers projets développés à travers performances, vidéos et\r\nphotographies. Parmi ces projets, j’ai créé un partenariat avec le Studio Harcourt à Paris, afin de réaliser un portrait de l’homme sans tête. Cette collaboration s’est célébrée par une première exposition personnelle de mon\r\ntravail dans les célèbres studios en 2010 (voir chronique d’exposition in Artpress n°369, Anne Cartel, juillet-août\r\n2010). La même année, François Alleaume présentait «Le pouvoir à rebours», une exposition personnelle dans\r\nla galerie Hypertopie à Caen.\r\n<br/>\r\n<br/>\r\nLe pouvoir, la violence et le sacré sont, aujourd’hui, les trois notions essentielles de mes recherches. Elles se\r\nsont tout d’abord traduites dans des dessins et des sculptures tel Auto-défense, véritable réplique de l’arme\r\nDesert Eagle.50, dont le canon est retourné à 180°(collection privée). L’homme sans tête se multiplie et forme\r\nune organisation qui se développe en particulier dans des oeuvres vidéo, telles Invasion ou Débarquement 3.\r\nPrésentée en mai 2011 au Festival de Cannes par l’intermédiaire d’Hélène Lacharmoise de la Galerie Dix9 à\r\nParis, Débarquement 3 fut projetée par la Galerie Dix9 en septembre 2011. Cette vidéo a reçu le prix du festival\r\nBandits-Mages (festival international de vidéos et performances) à Bourges (novembre 2011), et sera présentée\r\nà Cutlog Paris en octobre 2013. Outre le travail vidéo, je réalise de nombreuses performances, dont Conspiration\r\ncréée pour la foire Show Off Paris, et Procession à Fiac (Tarn) en avril 2012.\r\n<br/>\r\n<br/>\r\nJe prépare deux évènements pour l’Artothèque de Caen : « Aqua Vitalis, Positions de l’art contemporain »,\r\nexposition collective, commissariat Claire Tangy et Paul Ardenne du 14 septembre au 31 décembre 2013, et une\r\nexposition personnelle prévue pour 2015 dans l’espace Projet de l’Artothèque. Représentée par la Galerie Dix9\r\ndans les foires d’art contemporain, une seconde présentation de mon travail est prévue début 2014.');

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
  `last-name@string#value` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first-name@string#value` varchar(511) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `connection@connexion#id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `content__user`
--

INSERT INTO `content__user` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `last-name@string#value`, `first-name@string#value`, `connection@connexion#id`) VALUES
(1, 'Administrateur', NULL, '2023-09-11 15:03:08', NULL, '2023-09-11 15:03:08', 'Witchcase', 'Administrateur', 1);

-- --------------------------------------------------------

--
-- Structure de la table `draft__article-witchcase`
--

CREATE TABLE `draft__article-witchcase` (
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
-- Déchargement des données de la table `draft__article-witchcase`
--

INSERT INTO `draft__article-witchcase` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `content_key`, `headline@string#value`, `body@text#value`, `image@image#file`, `image@image#title`, `headline-left@string#value`, `body-left@text#value`, `headline-center@string#value`, `body-center@text#value`, `headline-right@string#value`, `body-right@text#value`, `link@link#href`, `link@link#text`, `link@link#external`) VALUES
(4, 'REPRENEZ LE CONTRÔLE', 1, '2023-09-26 18:31:16', 1, '2023-09-26 18:31:16', 1, 'REPRENEZ LE CONTRÔLE', '', 'logo_woody.png', 'logo woody', 'Pour les développeurs', 'Woody CMS est développé sur des technologies purement <strong>PHP/MySQL, en licence GPL</strong>. Le langage PHP est la seule connaissance requise pour y développer un site. Le concept de Woody CMS est une simplification fondamentale du stockage de données : <strong>plus de cache</strong> sur la visualisation des contenus, et aucune perte de temps due au renouvellement de ses fichiers pour valider ses développements. Les éléments contextuels (le menu par exemple) sont cachés par un système très simple et très accessible laissé aux soins du développeur. <br>De plus Woody CMS est une <strong>plateforme multisites</strong> avec un système d\'héritage et laisse une grande liberté dans le développement des modules. <br>En somme Woody CMS fonctionne comme une véritable <strong>\"boite à outils\"</strong> pour les développeurs.  ', 'Pour les utilisateurs', 'A l’heure actuelle, la contribution d’un site web implique un temps de visualisation de son travail en ligne avant de considérer ou non cette tâche achevée. Ce temps de validation doit prendre en compte la gestion du cache qui, suivant son niveau de complexité, peut prendre jusqu\'à 20 minutes pour une seule contribution. Un site implémenté avec Woody CMS permet la <strong>mise en ligne immédiate</strong> d’une information grâce à l’absence de cache de visualisation. Les contributeurs peuvent ainsi valider immédiatement leur travail. De plus Woody CMS est multisite et gère efficacement le <strong>multi-positionnement de contenus</strong>. Les contributeurs peuvent gérer différents sites via <strong>une seule et même interface</strong>, où la modification d’un contenu multipositionné est effective automatiquement à l’ensemble du site. Les visiteurs consultent alors une information instantanément à jour, sans s’impatienter devant un site ralenti par la régénération des fichiers de cache.', 'Pour les administrateurs', 'Woody CMS est un gestionnaire de contenu <strong>malléable</strong> : la forme et les emplacements sont déterminés par les administrateurs de la plateforme. L’administrateur peut créer et modifier à loisir autant de structures de contenus qu\'il souhaite, suivant la forme qu’il recherche. Par exemple la structure <em>article</em> peut contenir une introduction, une image, un lien ou encore un diaporama afin de correspondre exactement à ses besoins. Afin que les nécessités métier ne s’adaptent pas à une structure rigide, Woody CMS range les différents éléments qui constituent le site, suivant une <strong>arborescence choisie et construite en fonction des besoins requis</strong>. Pour les webmaster, Woody CMS propose nativement une gestion multisites avec héritage et une multiposition des éléments, afin de faciliter l\'administration. Pour les administrateurs réseau, une <strong>prise en compte immédiate et sans cache de la configuration</strong> permet l\'ajout et la suppression de host et de siteaccess \"à chaud\".', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `draft__folder-witchcase`
--

CREATE TABLE `draft__folder-witchcase` (
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
-- Déchargement des données de la table `draft__folder-witchcase`
--

INSERT INTO `draft__folder-witchcase` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `content_key`, `headline@string#value`, `body@text#value`, `background@image#file`, `background@image#title`) VALUES
(10, 'À Propos', 1, '2023-09-26 11:33:00', 1, '2023-09-26 11:33:00', 3, 'À propos de Witch case', 'Réconcilier l\'informatique avec ses utilisateurs ', 'img_fond_apropos.jpg', 'organique');

-- --------------------------------------------------------

--
-- Structure de la table `draft__home-witchcase`
--

CREATE TABLE `draft__home-witchcase` (
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
-- Déchargement des données de la table `draft__home-witchcase`
--

INSERT INTO `draft__home-witchcase` (`id`, `name`, `creator`, `created`, `modificator`, `modified`, `content_key`, `meta-title@string#value`, `meta-description@string#value`, `meta-keywords@string#value`, `logo@image#file`, `logo@image#title`, `contact-email@link#href`, `contact-email@link#text`, `contact-email@link#external`, `call-to-action@link#href`, `call-to-action@link#text`, `call-to-action@link#external`, `headline@string#value`, `body@text#value`, `background@image#file`, `background@image#title`) VALUES
(4, 'Home', 1, '2023-09-18 17:23:51', 1, '2023-09-18 17:23:51', 1, 'WitchCase', 'description du site WC', 'witchcase, cms, ecologie, arborescence', 'logo.jpg', 'logo', 'mailto:info@witchcase.com', 'info@witchcase.com', 1, 'https://github.com/Jean2Grom/witchcase-local', 'Répository GitHub', 1, 'WitchCase', 'le gestionnaire de contenus web qui s\'adapte aux métiers', 'img_fond_contact.jpg', 'witch riding broom');

-- --------------------------------------------------------

--
-- Structure de la table `draft__marieaerts-artwork-element`
--

CREATE TABLE `draft__marieaerts-artwork-element` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `embed-player@string#value` varchar(511) DEFAULT NULL,
  `text@text#value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `draft__marieaerts-document`
--

CREATE TABLE `draft__marieaerts-document` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `file@file#file` varchar(511) DEFAULT NULL,
  `file@file#title` varchar(511) DEFAULT NULL,
  `author@string#value` varchar(511) DEFAULT NULL,
  `information@string#value` varchar(511) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `draft__marieaerts-event`
--

CREATE TABLE `draft__marieaerts-event` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `title@string#value` varchar(511) DEFAULT NULL,
  `head@string#value` varchar(511) DEFAULT NULL,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `body@text#value` text,
  `link@link#href` varchar(511) DEFAULT NULL,
  `link@link#text` varchar(511) DEFAULT NULL,
  `link@link#external` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `draft__marieaerts-home`
--

CREATE TABLE `draft__marieaerts-home` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `title@string#value` varchar(511) DEFAULT NULL,
  `meta-title@string#value` varchar(511) DEFAULT NULL,
  `meta-description@string#value` varchar(511) DEFAULT NULL,
  `meta-keywords@string#value` varchar(511) DEFAULT NULL,
  `contact-email@link#href` varchar(511) DEFAULT NULL,
  `contact-email@link#text` varchar(511) DEFAULT NULL,
  `contact-email@link#external` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `draft__marieaerts-page`
--

CREATE TABLE `draft__marieaerts-page` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `creator` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modificator` int DEFAULT NULL,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `content_key` int DEFAULT NULL,
  `title@string#value` varchar(511) DEFAULT NULL,
  `description@text#value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `image@image#file` varchar(511) DEFAULT NULL,
  `image@image#title` varchar(511) DEFAULT NULL,
  `body@text#value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Administrator', 'adminstrator@witchcase', 'admin', '$2y$11$11FgVhXijP654xVeVG/VjeKIQnyRjVx0AsQ2QGQXiEx0VJeWeaGJ.', 'content__user', 'connexion', 'id', 'connection', NULL, '2023-09-11 15:03:08', NULL, '2023-09-11 15:03:08');

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
(5, 3, '*', 0, 14, 0, 1, 1, ''),
(6, 4, '*', 0, 26, 0, 0, 1, '');

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
(3, 'public', 'witchcase', '2023-09-25 17:19:53'),
(4, 'public', 'marieaerts', '2023-10-09 18:56:20');

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
  `level_4` int UNSIGNED DEFAULT NULL,
  `level_5` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `witch`
--

INSERT INTO `witch` (`id`, `name`, `data`, `site`, `url`, `status`, `invoke`, `craft_table`, `craft_fk`, `alias`, `is_main`, `context`, `datetime`, `priority`, `level_1`, `level_2`, `level_3`, `level_4`, `level_5`) VALUES
(1, 'Root', 'Ici se trouve la racine de la plateforme. C\'est à partir d\'ici que sont créées les homes de chaque site de la plateforme.', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, NULL, NULL, NULL, NULL, NULL),
(2, 'Admin WitchCase', 'Site d\'administration', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, 1, NULL, NULL, NULL, NULL),
(3, 'Utilisateurs', '', 'admin', 'utilisateurs', 0, '', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 10, 1, 1, NULL, NULL, NULL),
(4, 'Administrateur', '', 'admin', 'utilisateurs/administrateur', 0, '', 'content__user', 1, NULL, 1, '', '2023-09-11 15:03:08', 0, 1, 1, 1, NULL, NULL),
(5, 'Home', '', 'admin', '', 0, 'root', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, 1, 2, NULL, NULL, NULL),
(6, 'Login', 'Module de déconnexion/connexion', 'admin', 'login', 0, 'login', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 40, 1, 2, 1, NULL, NULL),
(7, 'Witch', 'Visualisation des Witches, c\'est a dire de chaque point de l\'arborescence -appelé ici Matriarcat. Chacun de ces points peut être associé à un contenu et/ou à un module exécutable. \r\nOn peut également définir une URL permettant de cibler cette witch.', 'admin', 'view', 0, 'view', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 30, 1, 2, 2, NULL, NULL),
(8, 'Edit Witch', '', 'admin', 'edit', 0, 'edit', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 20, 1, 2, 3, NULL, NULL),
(9, 'Edit Craft', 'This is the draft of craft, you can publish it, save it for later, or remove draft to cancel modification.', 'admin', 'edit-content', 0, 'contents/edit', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 10, 1, 2, 4, NULL, NULL),
(10, 'Menu', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, 1, 2, 5, NULL, NULL),
(11, 'Profiles', 'Permissions handeling is based on user profiles.', 'admin', 'profiles', 0, 'profiles', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, 1, 2, 5, 1, NULL),
(12, 'Structures', '', 'admin', 'structures', 0, 'structures', NULL, NULL, NULL, 1, NULL, '2023-09-11 15:03:08', 0, 1, 2, 5, 2, NULL),
(13, 'Site Witchcase', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-09-12 11:04:11', 0, 2, NULL, NULL, NULL, NULL),
(14, 'Home', '', 'witchcase', '', 0, 'view', 'content__home-witchcase', 1, NULL, 1, NULL, '2023-09-14 16:54:36', 0, 2, 1, NULL, NULL, NULL),
(15, 'Le CMS', '', 'witchcase', 'le-cms', 0, 'view', 'content__folder-witchcase', 1, NULL, 1, NULL, '2023-09-19 15:53:49', 0, 2, 1, 1, NULL, NULL),
(16, 'Technologies', '', 'witchcase', 'technologies', 0, 'view', 'content__folder-witchcase', 2, NULL, 1, NULL, '2023-09-19 16:33:02', 0, 2, 1, 2, NULL, NULL),
(17, 'À Propos', '', 'witchcase', 'a-propos', 0, 'view', 'content__folder-witchcase', 3, NULL, 1, NULL, '2023-09-19 16:35:59', 0, 2, 1, 3, NULL, NULL),
(18, 'REPRENEZ LE CONTRÔLE', 'Woody CMS est le premier CMS dont la technologie est orientée vers les acteurs du web. Que vous soyez développeur, contributeur, webmaster ou même administrateur réseau, ce gestionnaire de contenu (CMS) a pour objectif de vous simplifier la vie en supprimant les attentes interminables dues au fonctionnement d\'un site web, tout en bénéficiant de la malléabilité exigée d\'un site qui vous représente.', NULL, NULL, 0, NULL, 'content__article-witchcase', 1, NULL, 1, NULL, '2023-09-26 16:33:33', 0, 2, 1, 1, 1, NULL),
(19, 'Woody CMS en quelques mots', 'Woody CMS est un prototype de gestion de contenu, à partir duquel on développe, contribue et administre un site web. Woody CMS propose un modèle de stockage des données en base, gère la sécurité, assure une partition MVC des codes, et propose une interface web pour administrer son ou ses site(s).', NULL, NULL, 0, NULL, 'content__article-witchcase', 2, NULL, 1, NULL, '2023-09-26 16:39:44', 0, 2, 1, 1, 2, NULL),
(20, 'Contacter', '', 'witchcase', 'contacter', 0, 'contact', NULL, NULL, NULL, 1, NULL, '2023-09-27 11:48:37', -9, 2, 1, 4, NULL, NULL),
(21, 'FONCTIONNEMENT GLOBAL', 'Ici nous représentons les flux avec un diagramme de séquence, depuis la requête du navigateur jusqu\'à la visualisation de la page. Nous avons mis ici en valeur la structure MVC (Model View Controller) de Woody CMS.', NULL, NULL, 0, NULL, 'content__article-witchcase', 3, NULL, 1, NULL, '2023-09-28 18:40:14', 40, 2, 1, 2, 1, NULL),
(22, 'EMPLACEMENT MATRICIEL', 'Une des innovations utilisées par Woody CMS est la gestion de l\'arborescence avec des coordonnées matricielles. Cette technique permet d’avoir qu\'une unique requête pour déterminer l\'arborescence de l\'emplacement auquel nous accèdons.', NULL, NULL, 0, NULL, 'content__article-witchcase', 4, NULL, 1, NULL, '2023-09-28 18:45:14', 30, 2, 1, 2, 2, NULL),
(23, 'CONTENU AJUSTABLE', 'Afin de récupérer toutes les informations du contenu à afficher, Witch case a développé une solution qui consiste à ajuster en direct la table correspondant au contenu à afficher. Ainsi, on peut récupérer l\'ensemble des informations en une seule requête.', NULL, NULL, 0, NULL, 'content__article-witchcase', 5, NULL, 1, NULL, '2023-09-28 18:48:54', 20, 2, 1, 2, 3, NULL),
(24, 'NOMMAGE STRUCTUREL DES CHAMPS', 'Afin de permettre une plus grande complexité des attributs qui composent un contenu, il faut connaitre sa structure et son comportement avant d\'envoyer la requête visant à récupérer les contenus. Pour cela nous identifions les champs en BDD par un nommage structurel.', NULL, NULL, 0, NULL, 'content__article-witchcase', 6, NULL, 1, NULL, '2023-09-28 19:02:27', 10, 2, 1, 2, 4, NULL),
(25, 'WITCH CASE EN BREF', 'Witch case est une société d\'édition web crée en 2016 par Jean de Gromard. Ingénieur de formation, il a passé 10 ans dans les technologies du web dont 5 à se spécialiser dans l\'intégration de site via des CMS, avec une expertise sur eZPublish. Woody CMS est le premier projet de Witch case. Le prototype a été développé entre 2015 et 2016, sur une durée d\'environ un an.', NULL, NULL, 0, NULL, 'content__article-witchcase', 7, NULL, 1, NULL, '2023-09-28 19:06:12', 0, 2, 1, 3, 1, NULL),
(26, 'Site MarieAerts', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-04 17:02:44', -1, 3, NULL, NULL, NULL, NULL),
(27, 'Home', '', 'marieaerts', '', 0, 'home', 'content__marieaerts-home', 1, NULL, 1, NULL, '2023-10-04 17:42:18', 0, 3, 1, NULL, NULL, NULL),
(28, 'news', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-04 17:42:54', 0, 3, 3, NULL, NULL, NULL),
(29, 'vidéos', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-04 17:43:12', 0, 3, 2, 4, NULL, NULL),
(30, 'documents', '', 'marieaerts', 'documents', 0, 'view', 'content__marieaerts-page', 1, NULL, 1, NULL, '2023-10-04 17:43:38', -10, 3, 2, 2, NULL, NULL),
(31, 'bio', '', 'marieaerts', 'bio', 0, 'view', 'content__marieaerts-page', 6, NULL, 1, NULL, '2023-10-04 17:44:13', -20, 3, 2, 1, NULL, NULL),
(32, 'dessins / peintures', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-04 17:44:57', 0, 3, 2, 3, NULL, NULL),
(33, 'Menu', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:06:29', 0, 3, 2, NULL, NULL, NULL),
(34, 'performances', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:13:49', 0, 3, 2, 5, NULL, NULL),
(35, 'sculptures', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:14:40', 0, 3, 2, 6, NULL, NULL),
(36, 'photos', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:16:55', 0, 3, 2, 7, NULL, NULL),
(37, 'Épées', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:18:38', 0, 3, 2, 3, 1, NULL),
(38, 'Vers le chemin du bonheur total ou Du capitalisme', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:22:00', 0, 3, 2, 3, 2, NULL),
(39, 'Les bienheureux', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:23:32', 0, 3, 2, 3, 3, NULL),
(40, 'Chair', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:26:41', 0, 3, 2, 4, 1, NULL),
(41, 'Trône', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:28:24', 0, 3, 2, 3, 4, NULL),
(42, 'Aile du désir', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:29:08', 0, 3, 2, 6, 1, NULL),
(43, 'Le roi', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:30:06', 0, 3, 2, 4, 2, NULL),
(44, 'Portraits', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:30:50', 0, 3, 2, 7, 1, NULL),
(45, 'Révolte', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:31:53', 0, 3, 2, 6, 2, NULL),
(46, 'Grâce', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:32:26', 0, 3, 2, 4, 3, NULL),
(47, 'Débarquement 3', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:32:42', 0, 3, 2, 4, 4, NULL),
(48, 'Trou noir', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:33:32', 0, 3, 2, 4, 5, NULL),
(49, 'Commémoration', '', 'marieaerts', 'commemoration', 0, 'view', 'content__marieaerts-page', 4, NULL, 1, NULL, '2023-10-07 17:34:12', 0, 3, 2, 5, 1, NULL),
(50, 'Invasion', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:35:46', 0, 3, 2, 4, 6, NULL),
(51, 'Conspiration', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:36:16', 0, 3, 2, 5, 2, NULL),
(52, 'Disparition', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:36:46', 0, 3, 2, 4, 7, NULL),
(53, 'Armes', '', 'marieaerts', 'armes', 0, 'view', 'content__marieaerts-page', 3, NULL, 1, NULL, '2023-10-07 17:37:23', 0, 3, 2, 3, 5, NULL),
(54, 'Mains en l\'air', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:38:31', 0, 3, 2, 7, 2, NULL),
(55, 'Corbeau 1 et 2', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:39:07', 0, 3, 2, 6, 3, NULL),
(56, 'Auto défense', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:39:40', 0, 3, 2, 6, 4, NULL),
(57, 'Dénaturé', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:40:00', 0, 3, 2, 7, 3, NULL),
(58, 'Uchronie', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:40:19', 0, 3, 2, 3, 6, NULL),
(59, 'Victoire', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:41:06', 0, 3, 2, 7, 4, NULL),
(60, 'Le conquérant', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:41:31', 0, 3, 2, 3, 7, NULL),
(61, 'Fée clochette', '', 'marieaerts', 'fee-clochette', 0, 'view', 'content__marieaerts-page', 5, NULL, 1, NULL, '2023-10-07 17:41:58', 0, 3, 2, 3, 8, NULL),
(62, 'Être de ceux auxquels les hommes croient', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:42:29', 0, 3, 2, 6, 5, NULL),
(63, 'Équilibre', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-07 17:43:37', 0, 3, 2, 6, 6, NULL),
(64, 'Devoir', '', 'marieaerts', 'devoir', 0, 'view', 'content__marieaerts-page', 2, NULL, 1, NULL, '2023-10-07 17:43:55', 0, 3, 2, 7, 5, NULL),
(65, 'Python', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 1, NULL, 1, NULL, '2023-10-09 18:27:52', 80, 3, 2, 3, 5, 1),
(66, 'Lady Smith', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 2, NULL, 1, NULL, '2023-10-09 18:36:12', 70, 3, 2, 3, 5, 2),
(67, 'Ingram', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 3, NULL, 1, NULL, '2023-10-09 18:37:44', 60, 3, 2, 3, 5, 3),
(68, 'Kalachnikov', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 4, NULL, 1, NULL, '2023-10-09 18:38:34', 50, 3, 2, 3, 5, 4),
(69, 'Smith&Wesson', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 5, NULL, 1, NULL, '2023-10-09 18:39:18', 40, 3, 2, 3, 5, 5),
(70, 'Olympic', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 6, NULL, 1, NULL, '2023-10-09 18:40:19', 30, 3, 2, 3, 5, 6),
(71, 'Mauser', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 7, NULL, 1, NULL, '2023-10-09 18:41:02', 20, 3, 2, 3, 5, 7),
(72, 'Aug 77', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 8, NULL, 1, NULL, '2023-10-09 18:41:49', 10, 3, 2, 3, 5, 8),
(73, 'illustration', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 9, NULL, 1, NULL, '2023-10-10 00:15:51', 0, 3, 2, 5, 1, 1),
(74, 'img 1', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 10, NULL, 1, NULL, '2023-10-10 00:24:33', 0, 3, 2, 5, 1, 2),
(75, 'img 2', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 11, NULL, 1, NULL, '2023-10-10 00:25:19', 0, 3, 2, 5, 1, 3),
(76, 'img 3', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 12, NULL, 1, NULL, '2023-10-10 00:26:01', 0, 3, 2, 5, 1, 4),
(77, 'img 4', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 13, NULL, 1, NULL, '2023-10-10 00:26:26', 0, 3, 2, 5, 1, 5),
(78, 'extrait vidéo', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 14, NULL, 1, NULL, '2023-10-10 00:27:02', -1, 3, 2, 5, 1, 6),
(79, 'Texte critique', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-10 15:32:17', 5, 3, 2, 2, 1, NULL),
(80, 'Dossier de presse', '', NULL, NULL, 0, NULL, NULL, NULL, NULL, 1, NULL, '2023-10-10 15:33:02', 0, 3, 2, 2, 2, NULL),
(81, 'Marie Aerts, le vide démasqué', '', NULL, NULL, 0, NULL, 'content__marieaerts-document', 2, NULL, 1, NULL, '2023-10-10 15:33:54', 0, 3, 2, 2, 1, 1),
(82, 'Les fantômes en habits noirs de Marie Aerts', '', NULL, NULL, 0, NULL, 'content__marieaerts-document', 3, NULL, 1, NULL, '2023-10-10 18:20:11', 0, 3, 2, 2, 2, 1),
(83, 'Python', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 1, NULL, 0, NULL, '2023-10-11 10:38:26', 0, 3, 1, 1, NULL, NULL),
(84, 'img 3', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 12, NULL, 0, NULL, '2023-10-11 10:39:52', 0, 3, 1, 2, NULL, NULL),
(85, 'Photo', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 15, NULL, 1, NULL, '2023-10-12 10:58:31', 0, 3, 2, 7, 5, 1),
(88, 'texte Marianne', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 16, NULL, 1, NULL, '2023-10-12 15:14:21', 0, 3, 2, 7, 5, 2),
(89, 'Chimères de l\'Ailleurs', '', NULL, NULL, 0, NULL, 'content__marieaerts-event', 1, NULL, 1, NULL, '2023-10-12 15:36:33', 0, 3, 3, 1, NULL, NULL),
(90, 'Chimères de l\'Ailleurs', '', NULL, NULL, 0, NULL, 'content__marieaerts-event', 1, NULL, 0, NULL, '2023-10-12 15:38:57', -1, 3, 1, 3, NULL, NULL),
(91, 'extrait vidéo', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 14, NULL, 0, NULL, '2023-10-12 15:56:06', 0, 3, 1, 4, NULL, NULL),
(93, 'Fée clochette', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 17, NULL, 1, NULL, '2023-10-13 16:39:35', 0, 3, 2, 3, 8, 1),
(94, 'Fée clochette', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 17, NULL, 0, NULL, '2023-10-13 16:42:57', 0, 3, 1, 5, NULL, NULL),
(95, 'CV', '', NULL, NULL, 0, NULL, 'content__marieaerts-document', 4, NULL, 1, NULL, '2023-10-13 16:55:25', 0, 3, 2, 1, 1, NULL),
(96, 'Photo', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 15, NULL, 0, NULL, '2023-10-13 17:12:08', 0, 3, 1, 6, NULL, NULL),
(97, 'Lady Smith', '', NULL, NULL, 0, NULL, 'content__marieaerts-artwork-element', 2, NULL, 0, NULL, '2023-10-13 17:31:38', 0, 3, 1, 7, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `archive__article-witchcase`
--
ALTER TABLE `archive__article-witchcase`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__folder-witchcase`
--
ALTER TABLE `archive__folder-witchcase`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__home-witchcase`
--
ALTER TABLE `archive__home-witchcase`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__marieaerts-artwork-element`
--
ALTER TABLE `archive__marieaerts-artwork-element`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__marieaerts-document`
--
ALTER TABLE `archive__marieaerts-document`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__marieaerts-event`
--
ALTER TABLE `archive__marieaerts-event`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__marieaerts-home`
--
ALTER TABLE `archive__marieaerts-home`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__marieaerts-page`
--
ALTER TABLE `archive__marieaerts-page`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `archive__user`
--
ALTER TABLE `archive__user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__article-witchcase`
--
ALTER TABLE `content__article-witchcase`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__folder-witchcase`
--
ALTER TABLE `content__folder-witchcase`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__home-witchcase`
--
ALTER TABLE `content__home-witchcase`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__marieaerts-artwork-element`
--
ALTER TABLE `content__marieaerts-artwork-element`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__marieaerts-document`
--
ALTER TABLE `content__marieaerts-document`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__marieaerts-event`
--
ALTER TABLE `content__marieaerts-event`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__marieaerts-home`
--
ALTER TABLE `content__marieaerts-home`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__marieaerts-page`
--
ALTER TABLE `content__marieaerts-page`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `content__user`
--
ALTER TABLE `content__user`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__article-witchcase`
--
ALTER TABLE `draft__article-witchcase`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__folder-witchcase`
--
ALTER TABLE `draft__folder-witchcase`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__home-witchcase`
--
ALTER TABLE `draft__home-witchcase`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__marieaerts-artwork-element`
--
ALTER TABLE `draft__marieaerts-artwork-element`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__marieaerts-document`
--
ALTER TABLE `draft__marieaerts-document`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__marieaerts-event`
--
ALTER TABLE `draft__marieaerts-event`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__marieaerts-home`
--
ALTER TABLE `draft__marieaerts-home`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `draft__marieaerts-page`
--
ALTER TABLE `draft__marieaerts-page`
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
  ADD KEY `IDX_level_5` (`level_5`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `archive__article-witchcase`
--
ALTER TABLE `archive__article-witchcase`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `archive__folder-witchcase`
--
ALTER TABLE `archive__folder-witchcase`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `archive__home-witchcase`
--
ALTER TABLE `archive__home-witchcase`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `archive__marieaerts-artwork-element`
--
ALTER TABLE `archive__marieaerts-artwork-element`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `archive__marieaerts-document`
--
ALTER TABLE `archive__marieaerts-document`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `archive__marieaerts-event`
--
ALTER TABLE `archive__marieaerts-event`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `archive__marieaerts-home`
--
ALTER TABLE `archive__marieaerts-home`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `archive__marieaerts-page`
--
ALTER TABLE `archive__marieaerts-page`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `content__article-witchcase`
--
ALTER TABLE `content__article-witchcase`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `content__folder-witchcase`
--
ALTER TABLE `content__folder-witchcase`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `content__home-witchcase`
--
ALTER TABLE `content__home-witchcase`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `content__marieaerts-artwork-element`
--
ALTER TABLE `content__marieaerts-artwork-element`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `content__marieaerts-document`
--
ALTER TABLE `content__marieaerts-document`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `content__marieaerts-event`
--
ALTER TABLE `content__marieaerts-event`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `content__marieaerts-home`
--
ALTER TABLE `content__marieaerts-home`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `content__marieaerts-page`
--
ALTER TABLE `content__marieaerts-page`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `content__user`
--
ALTER TABLE `content__user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `draft__article-witchcase`
--
ALTER TABLE `draft__article-witchcase`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `draft__folder-witchcase`
--
ALTER TABLE `draft__folder-witchcase`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `draft__home-witchcase`
--
ALTER TABLE `draft__home-witchcase`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `draft__marieaerts-artwork-element`
--
ALTER TABLE `draft__marieaerts-artwork-element`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `draft__marieaerts-document`
--
ALTER TABLE `draft__marieaerts-document`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `draft__marieaerts-event`
--
ALTER TABLE `draft__marieaerts-event`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `draft__marieaerts-home`
--
ALTER TABLE `draft__marieaerts-home`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `draft__marieaerts-page`
--
ALTER TABLE `draft__marieaerts-page`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `user__connexion`
--
ALTER TABLE `user__connexion`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `user__policy`
--
ALTER TABLE `user__policy`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `user__profile`
--
ALTER TABLE `user__profile`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `witch`
--
ALTER TABLE `witch`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
