-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 26 avr. 2024 à 00:47
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `sharerecipe`
--

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

DROP TABLE IF EXISTS `favoris`;
CREATE TABLE IF NOT EXISTS `favoris` (
  `id_favori` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int DEFAULT NULL,
  `id_recette` int DEFAULT NULL,
  PRIMARY KEY (`id_favori`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_recette` (`id_recette`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `favoris`
--

INSERT INTO `favoris` (`id_favori`, `id_utilisateur`, `id_recette`) VALUES
(29, 15, 64),
(67, 19, 58),
(75, 15, 63),
(76, 8, 65),
(77, 8, 61);

-- --------------------------------------------------------

--
-- Structure de la table `recettes`
--

DROP TABLE IF EXISTS `recettes`;
CREATE TABLE IF NOT EXISTS `recettes` (
  `id_recette` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `ingredients` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `etapes_preparation` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `temps_preparation` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Non renseigné',
  `temps_cuisson` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Non renseigné',
  `image` varchar(256) NOT NULL DEFAULT 'img_defaut.jpg',
  `id_auteur` int NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_recette`),
  UNIQUE KEY `unique_titre_par_auteur` (`titre`,`id_auteur`),
  KEY `id_auteur` (`id_auteur`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `recettes`
--

INSERT INTO `recettes` (`id_recette`, `titre`, `description`, `ingredients`, `etapes_preparation`, `temps_preparation`, `temps_cuisson`, `image`, `id_auteur`, `date_creation`) VALUES
(57, 'Poulet rôti au four', 'Un délicieux poulet rôti croustillant à l\'extérieur et tendre à l\'intérieur, assaisonné avec des herbes aromatiques.', 'Poulet entier, herbes de Provence, sel, poivre, beurre.', 'Préchauffez le four, assaisonnez le poulet, enfournez et laissez rôtir jusqu\'à ce qu\'il soit bien doré.', '15 minutes', '1 heure et 30 minutes', 'rcp8bdb1de47064a9b30d4985c8e3b5d039_pouletroti.jpg', 8, '2024-04-12 11:57:17'),
(58, 'Pâtes à la carbonara', ' Des pâtes onctueuses avec une sauce à la crème, du lard croustillant et du parmesan râpé.', 'Spaghetti, lardons, crème fraîche, œufs, parmesan râpé, poivre.', 'Faites cuire les pâtes, faites revenir les lardons, mélangez la crème et les œufs, incorporez le tout aux pâtes.', '30 minutes', 'moins de 15 minutes', 'rcp7a97dc594e45b0dcb1474cfb50a0c798_patecarbo.jpg', 8, '2024-04-12 11:58:18'),
(59, 'Salade César', ' Une salade fraîche et croquante avec du poulet grillé, des croûtons dorés, du parmesan et une sauce crémeuse.', 'Laitue romaine, blanc de poulet, croûtons, parmesan, sauce César.', 'Préparez la laitue, faites griller le poulet, ajoutez les croûtons et le parmesan, assaisonnez avec la sauce César.', '30 minutes', 'moins de 15 minutes', 'rcpc3740d21519781a9f3d03541e8afe6fa_saladecesar.jpg', 8, '2024-04-12 11:58:57'),
(60, 'Pizza Margherita', ' Une pizza classique avec une croûte croustillante, de la sauce tomate, de la mozzarella fraîche et des feuilles de basilic.', 'Pâte à pizza, sauce tomate, mozzarella, basilic frais, sel, huile d\'olive.\r\n', 'Étalez la pâte, étalez la sauce tomate, ajoutez la mozzarella, cuire au four et garnissez de basilic frais.', '30 minutes', '15 minutes', 'rcp05788d7eb9a1dff407f18bcd2b851edb_pizza.jpg', 19, '2024-04-12 12:00:44'),
(61, 'Tarte aux pommes', 'Une tarte croustillante avec des pommes fondantes, saupoudrée de cannelle et de sucre.', 'Pâte brisée, pommes, sucre, cannelle, beurre.', 'Étalez la pâte, disposez les pommes en tranches, saupoudrez de sucre et de cannelle, cuire au four.', '30 minutes', '1 heure', 'rcp3a8980fee502b1c5a9f9fd78c6d8ad4d_tartepomme.jpg', 19, '2024-04-12 12:01:37'),
(62, 'Poulet rôti au four', 'Un délicieux poulet rôti croustillant', 'Poulet entier, herbes de Provence', 'Préchauffez le four, assaisonnez le poulet, enfournez et laissez rôtir jusqu\'à ce qu\'il soit bien doré.', 'plus de 2 heures', 'plus de 2 heures', 'img_defaut.jpg', 19, '2024-04-12 12:02:40'),
(63, 'Soupe à l\'oignon', 'Une soupe réconfortante avec des oignons caramélisés, du bouillon de bœuf, du pain grillé et du fromage fondu.', 'Oignons, bouillon de bœuf, pain, fromage gruyère, beurre.', ' Faites revenir les oignons jusqu\'à ce qu\'ils soient caramélisés, ajoutez le bouillon, servez avec des tranches de pain et du fromage fondu.', '15 minutes', '1 heure', 'rcpd0c0cada2c2b5674b56a5074672b112f_soupeoignon.jpg', 8, '2024-04-12 12:03:40'),
(64, 'Crêpes sucrées', 'Des crêpes légères et moelleuses, garnies de Chocolat, de bananes et de crème fouettée.', 'Farine, œufs, lait, bananes, crème fraîche.', ' Préparez la pâte à crêpes, faites cuire les crêpes,', '15 minutes', '30 minutes', 'rcp39486d10b32378c65a8a11dc523a54f1_crepesucre.jpg', 8, '2024-04-12 12:04:33'),
(65, 'Tiramisu', 'Un dessert italien classique avec des couches de biscuits imbibés de café, de mascarpone crémeux et de cacao en poudre.', 'Biscuits à la cuillère, café fort, mascarpone, sucre, œufs, cacao en poudre.', 'Trempez les biscuits dans le café, alternez les couches de biscuits et de mascarpone, saupoudrez de cacao.', '30 minutes', 'non renseigné', 'rcpf29372f92dc26dfe64ab2d7da64fc91b_Tiramisu-Recipe-Cover.jpg', 15, '2024-04-12 12:05:33');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `prenom` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `mot_de_passe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `Role` enum('utilisateur','admin') NOT NULL DEFAULT 'utilisateur',
  PRIMARY KEY (`id_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `Role`) VALUES
(8, 'Share', 'Recipe', 'Share@Rcp.com', '$2y$10$bz0qfU.27PJge27tH9XCQerODnT5uRZi08CiElxoe8Zpnct/0lJE6', 'admin'),
(15, 'tata', 'gâteau', 'totogateau@mail.com', '$2y$10$pRePl/ak2XpmAvHqaXEVGOaWWlrRdZNiEEwok75L908LXoMVfw6kq', 'utilisateur'),
(17, 'ffff', 'RAE', 'test@gmail.com', '$2y$10$P2fLGSrEA8SDZjB0J87Gf.fyM4rMwkMgW5GTzJPKJzRMVdq.oJkS6', 'utilisateur'),
(19, 'hello', 'world', 'hello@world.com', '$2y$10$v6BD78MkfWC1juf1juOYH.4MYC.giIlcTn4i0ZXpHeYf6uLT10XgG', 'utilisateur');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `favoris_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`),
  ADD CONSTRAINT `favoris_ibfk_2` FOREIGN KEY (`id_recette`) REFERENCES `recettes` (`id_recette`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
