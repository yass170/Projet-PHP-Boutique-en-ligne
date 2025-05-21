-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour ecommerce_simulation
CREATE DATABASE IF NOT EXISTS `ecommerce_simulation` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `ecommerce_simulation`;

-- Listage de la structure de table ecommerce_simulation. categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categorie_parente_id` (`parent_id`) USING BTREE,
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table ecommerce_simulation.categories : ~3 rows (environ)
DELETE FROM `categories`;
INSERT INTO `categories` (`id`, `nom`, `parent_id`) VALUES
	(1, 'Femme', NULL),
	(2, 'Homme', NULL),
	(4, 'Unisexe', NULL);

-- Listage de la structure de table ecommerce_simulation. commandes
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `date_commande` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table ecommerce_simulation.commandes : ~12 rows (environ)
DELETE FROM `commandes`;
INSERT INTO `commandes` (`id`, `utilisateur_id`, `date_commande`, `total`) VALUES
	(1, 3, '2025-03-28 17:43:47', 511.00),
	(2, 5, '2025-03-28 19:42:54', 273.99),
	(3, 3, '2025-03-30 15:24:15', 1259.79),
	(4, 3, '2025-03-30 16:27:31', 1199.80),
	(5, 3, '2025-03-31 09:23:22', 84.90),
	(6, 3, '2025-03-31 09:29:23', 239.70),
	(7, 3, '2025-03-31 09:30:16', 2999.50),
	(8, 3, '2025-03-31 09:40:58', 599.90),
	(9, 3, '2025-03-31 09:55:14', 599.90),
	(10, 3, '2025-03-31 12:24:36', 279.88),
	(11, 3, '2025-03-31 12:44:03', 181.00),
	(12, 3, '2025-03-31 12:45:41', 899.85);

-- Listage de la structure de table ecommerce_simulation. commandes_produits
CREATE TABLE IF NOT EXISTS `commandes_produits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `commande_id` int NOT NULL,
  `produit_id` int NOT NULL,
  `taille` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `couleur` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantite` int NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `commande_id` (`commande_id`),
  KEY `produit_id` (`produit_id`),
  CONSTRAINT `commandes_produits_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `commandes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `commandes_produits_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table ecommerce_simulation.commandes_produits : ~23 rows (environ)
DELETE FROM `commandes_produits`;
INSERT INTO `commandes_produits` (`id`, `commande_id`, `produit_id`, `taille`, `couleur`, `quantite`, `prix_unitaire`) VALUES
	(1, 1, 16, 'XS', 'Rouge', 3, 22.00),
	(2, 1, 18, 'XS', 'Rouge', 3, 125.00),
	(3, 1, 19, 'XS', 'Rouge', 2, 35.00),
	(4, 2, 3, 'S', 'Bleu', 1, 49.00),
	(5, 2, 1, 'XS', 'Rouge', 1, 59.99),
	(6, 2, 4, 'XS', 'Noir', 1, 120.00),
	(7, 2, 6, 'XS', 'Noir', 1, 45.00),
	(8, 3, 1, 'XS', 'Rouge', 21, 59.99),
	(9, 4, 1, 'XS', 'Rouge', 20, 59.99),
	(10, 5, 2, 'XS', 'Rouge', 1, 39.90),
	(11, 5, 6, 'XS', 'Rouge', 1, 45.00),
	(12, 6, 2, 'XS', 'Rouge', 3, 39.90),
	(13, 6, 4, 'XS', 'Rouge', 1, 120.00),
	(14, 7, 1, 'XS', 'Noir', 50, 59.99),
	(15, 8, 1, 'XS', 'Noir', 10, 59.99),
	(16, 9, 1, 'XS', 'Noir', 10, 59.99),
	(17, 10, 1, 'XS', 'Noir', 2, 59.99),
	(18, 10, 2, 'XS', 'Rouge', 1, 39.90),
	(19, 10, 4, 'S', 'Bleu', 1, 120.00),
	(20, 11, 25, 'XS', 'Rouge', 1, 99.00),
	(21, 11, 16, 'XS', 'Rouge', 1, 22.00),
	(22, 11, 21, 'XS', 'Rouge', 1, 60.00),
	(23, 12, 1, 'XS', 'Noir', 15, 59.99);

-- Listage de la structure de table ecommerce_simulation. logs_admin
CREATE TABLE IF NOT EXISTS `logs_admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `admin_id` int NOT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_action` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `logs_admin_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table ecommerce_simulation.logs_admin : ~0 rows (environ)
DELETE FROM `logs_admin`;

-- Listage de la structure de table ecommerce_simulation. paniers
CREATE TABLE IF NOT EXISTS `paniers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `produit_id` int NOT NULL,
  `taille` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `couleur` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantite` int NOT NULL,
  `date_ajout` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `produit_id` (`produit_id`),
  CONSTRAINT `paniers_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `paniers_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table ecommerce_simulation.paniers : ~0 rows (environ)
DELETE FROM `paniers`;

-- Listage de la structure de table ecommerce_simulation. parametres_site
CREATE TABLE IF NOT EXISTS `parametres_site` (
  `cle` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table ecommerce_simulation.parametres_site : ~9 rows (environ)
DELETE FROM `parametres_site`;
INSERT INTO `parametres_site` (`cle`, `valeur`) VALUES
	('email_admin', 'yass.toumi20@gmail.com'),
	('site_name', 'SimuDress'),
	('site_url', 'http://localhost/ecommerce-simulation/'),
	('smtp_app_name', 'PHPMailerYasser'),
	('smtp_host', 'smtp.gmail.com'),
	('smtp_pass', 'dpockpktrgunwtzd'),
	('smtp_port', '587'),
	('smtp_secure', 'tls'),
	('smtp_user', 'yass.toumi20@gmail.com');

-- Listage de la structure de table ecommerce_simulation. produits
CREATE TABLE IF NOT EXISTS `produits` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `stock` int NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categorie_id` int NOT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `vues` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `categorie_id` (`categorie_id`),
  CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table ecommerce_simulation.produits : ~32 rows (environ)
DELETE FROM `produits`;
INSERT INTO `produits` (`id`, `nom`, `description`, `prix`, `stock`, `image`, `categorie_id`, `actif`, `vues`) VALUES
	(1, 'Robe de soiree', 'Robe élégante pour soirées.', 59.99, 10, 'robedesoiree.png', 1, 1, 29),
	(2, 'Jeans Slim Femme', 'Jean slim tendance pour femme.', 39.90, 45, 'jeansslimfemme.png', 1, 1, 8),
	(3, 'Blouse en soie', 'Blouse douce et élégante.', 49.00, 30, 'blouseensoie.png', 1, 1, 3),
	(4, 'Veste en cuir Femme', 'Veste en cuir véritable.', 120.00, 13, 'vesteencuirfemme.png', 1, 1, 9),
	(5, 'T-shirt coton Femme', 'T-shirt en coton léger.', 19.99, 100, 'tshirtcotonfemme.png', 1, 1, 0),
	(6, 'Pull en laine Femme', 'Pull chaud en laine naturelle.', 45.00, 24, 'pullenlainefemme.png', 1, 1, 9),
	(7, 'Jupe plissee', 'Jupe fluide et plissée.', 35.00, 40, 'jupeplissee.png', 1, 1, 0),
	(8, 'Chemisier a manches longues', 'Chemisier habillé et fluide.', 38.50, 45, 'chemisieramancheslongues.png', 1, 1, 0),
	(9, 'Short en jean Femme', 'Short décontracté en jean.', 27.00, 60, 'shortenjeanfemme.png', 1, 1, 0),
	(10, 'Pantalon taille haute', 'Pantalon élégant taille haute.', 42.00, 35, 'pantalontaillehaute.png', 1, 1, 0),
	(11, 'Cardigan en cachemire', 'Cardigan doux et chaud.', 90.00, 10, 'cardiganencachemire.png', 1, 1, 0),
	(12, 'Manteau dhiver Femme', 'Manteau long pour l’hiver.', 130.00, 12, 'manteaudhiverfemme.png', 1, 1, 0),
	(13, 'Robe d\'ete Femme', 'Robe légère pour l’été.', 33.00, 40, 'robedetefemme.png', 1, 1, 0),
	(14, 'Debardeur en coton Femme', 'Débardeur simple et confortable.', 14.99, 70, 'debardeurencotonfemme.png', 1, 1, 0),
	(15, 'Salopette en jean Femme', 'Salopette stylée en jean.', 50.00, 30, 'salopetteenjeanfemme.png', 1, 1, 0),
	(16, 'T-shirt imprime Homme', 'T-shirt imprimé stylé.', 22.00, 79, 'tshirtimprimehomme.png', 2, 1, 19),
	(17, 'Jeans decontracte Homme', 'Jean coupe droite.', 41.50, 60, 'jeansdecontractehomme.png', 2, 1, 13),
	(18, 'Veste en cuir Homme', 'Veste masculine en cuir.', 125.00, 15, 'vesteencuirhomme.png', 2, 1, 5),
	(19, 'Sweat zippe Homme', 'Sweat confortable zippé.', 35.00, 50, 'sweatzippehomme.png', 2, 1, 3),
	(20, 'Polo classique Homme', 'Polo sobre et classique.', 27.99, 45, 'poloclassiquehomme.png', 2, 1, 0),
	(21, 'Blouson bomber Homme', 'Blouson style urbain.', 60.00, 19, 'blousonbomberhomme.png', 2, 1, 3),
	(22, 'Short en lin Homme', 'Short léger en lin.', 29.00, 55, 'shortenlinhomme.png', 2, 1, 0),
	(23, 'Costume complet Homme', 'Costume élégant 2 pièces.', 150.00, 10, 'costumecomplethomme.png', 2, 1, 0),
	(24, 'Chemise oxford Homme', 'Chemise élégante en coton.', 37.90, 40, 'chemiseoxfordhomme.png', 2, 1, 0),
	(25, 'Veste en laine Homme', 'Veste chaude en laine.', 99.00, 17, 'vesteenlainehomme.png', 2, 1, 1),
	(26, 'Chino slim Homme', 'Pantalon chino slim.', 44.00, 50, 'chinoslimhomme.png', 2, 1, 0),
	(27, 'Gilet matelasse Homme', 'Gilet sans manches matelassé.', 48.00, 22, 'giletmatelassehomme.png', 2, 1, 0),
	(28, 'T-shirt col V Homme', 'T-shirt à col V confortable.', 20.00, 70, 'tshirtcolvhomme.png', 2, 1, 1),
	(29, 'Jean skinny Homme', 'Jean slim moulant.', 39.00, 40, 'jeanskinnyhomme.png', 2, 1, 0),
	(30, 'Blouson en jean Homme', 'Blouson mode en denim.', 55.00, 25, 'blousonenjeanhomme.png', 2, 1, 0),
	(31, 'Pull Homme', 'Pull tendance pour homme.', 50.00, 120, 'pullhomme.png', 2, 1, 0),
	(33, 'Legging Femme', 'Legging super cool', 10.00, 50, 'leggingfemme.png', 1, 1, 1);

-- Listage de la structure de table ecommerce_simulation. produits_couleurs
CREATE TABLE IF NOT EXISTS `produits_couleurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int NOT NULL,
  `couleur` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `produit_id` (`produit_id`),
  CONSTRAINT `produits_couleurs_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=163 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table ecommerce_simulation.produits_couleurs : ~125 rows (environ)
DELETE FROM `produits_couleurs`;
INSERT INTO `produits_couleurs` (`id`, `produit_id`, `couleur`) VALUES
	(5, 2, 'Rouge'),
	(6, 2, 'Bleu'),
	(7, 2, 'Blanc'),
	(8, 2, 'Noir'),
	(9, 3, 'Rouge'),
	(10, 3, 'Bleu'),
	(11, 3, 'Blanc'),
	(12, 3, 'Noir'),
	(13, 4, 'Rouge'),
	(14, 4, 'Bleu'),
	(15, 4, 'Blanc'),
	(16, 4, 'Noir'),
	(17, 5, 'Rouge'),
	(18, 5, 'Bleu'),
	(19, 5, 'Blanc'),
	(20, 5, 'Noir'),
	(21, 6, 'Rouge'),
	(22, 6, 'Bleu'),
	(23, 6, 'Blanc'),
	(24, 6, 'Noir'),
	(25, 7, 'Rouge'),
	(26, 7, 'Bleu'),
	(27, 7, 'Blanc'),
	(28, 7, 'Noir'),
	(29, 8, 'Rouge'),
	(30, 8, 'Bleu'),
	(31, 8, 'Blanc'),
	(32, 8, 'Noir'),
	(33, 9, 'Rouge'),
	(34, 9, 'Bleu'),
	(35, 9, 'Blanc'),
	(36, 9, 'Noir'),
	(37, 10, 'Rouge'),
	(38, 10, 'Bleu'),
	(39, 10, 'Blanc'),
	(40, 10, 'Noir'),
	(41, 11, 'Rouge'),
	(42, 11, 'Bleu'),
	(43, 11, 'Blanc'),
	(44, 11, 'Noir'),
	(45, 12, 'Rouge'),
	(46, 12, 'Bleu'),
	(47, 12, 'Blanc'),
	(48, 12, 'Noir'),
	(49, 13, 'Rouge'),
	(50, 13, 'Bleu'),
	(51, 13, 'Blanc'),
	(52, 13, 'Noir'),
	(53, 14, 'Rouge'),
	(54, 14, 'Bleu'),
	(55, 14, 'Blanc'),
	(56, 14, 'Noir'),
	(57, 15, 'Rouge'),
	(58, 15, 'Bleu'),
	(59, 15, 'Blanc'),
	(60, 15, 'Noir'),
	(61, 16, 'Rouge'),
	(62, 16, 'Bleu'),
	(63, 16, 'Blanc'),
	(64, 16, 'Noir'),
	(65, 17, 'Rouge'),
	(66, 17, 'Bleu'),
	(67, 17, 'Blanc'),
	(68, 17, 'Noir'),
	(69, 18, 'Rouge'),
	(70, 18, 'Bleu'),
	(71, 18, 'Blanc'),
	(72, 18, 'Noir'),
	(73, 19, 'Rouge'),
	(74, 19, 'Bleu'),
	(75, 19, 'Blanc'),
	(76, 19, 'Noir'),
	(77, 20, 'Rouge'),
	(78, 20, 'Bleu'),
	(79, 20, 'Blanc'),
	(80, 20, 'Noir'),
	(81, 21, 'Rouge'),
	(82, 21, 'Bleu'),
	(83, 21, 'Blanc'),
	(84, 21, 'Noir'),
	(85, 22, 'Rouge'),
	(86, 22, 'Bleu'),
	(87, 22, 'Blanc'),
	(88, 22, 'Noir'),
	(89, 23, 'Rouge'),
	(90, 23, 'Bleu'),
	(91, 23, 'Blanc'),
	(92, 23, 'Noir'),
	(93, 24, 'Rouge'),
	(94, 24, 'Bleu'),
	(95, 24, 'Blanc'),
	(96, 24, 'Noir'),
	(97, 25, 'Rouge'),
	(98, 25, 'Bleu'),
	(99, 25, 'Blanc'),
	(100, 25, 'Noir'),
	(101, 26, 'Rouge'),
	(102, 26, 'Bleu'),
	(103, 26, 'Blanc'),
	(104, 26, 'Noir'),
	(105, 27, 'Rouge'),
	(106, 27, 'Bleu'),
	(107, 27, 'Blanc'),
	(108, 27, 'Noir'),
	(109, 28, 'Rouge'),
	(110, 28, 'Bleu'),
	(111, 28, 'Blanc'),
	(112, 28, 'Noir'),
	(113, 29, 'Rouge'),
	(114, 29, 'Bleu'),
	(115, 29, 'Blanc'),
	(116, 29, 'Noir'),
	(117, 30, 'Rouge'),
	(118, 30, 'Bleu'),
	(119, 30, 'Blanc'),
	(120, 30, 'Noir'),
	(121, 31, 'Rouge'),
	(131, 33, 'Noir'),
	(132, 33, 'Blanc'),
	(133, 33, 'Bleu'),
	(134, 33, 'Rouge'),
	(159, 1, 'Noir'),
	(160, 1, 'Blanc'),
	(161, 1, 'Bleu'),
	(162, 1, 'Rouge');

-- Listage de la structure de table ecommerce_simulation. produits_tailles
CREATE TABLE IF NOT EXISTS `produits_tailles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `produit_id` int NOT NULL,
  `taille` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `produit_id` (`produit_id`),
  CONSTRAINT `produits_tailles_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table ecommerce_simulation.produits_tailles : ~156 rows (environ)
DELETE FROM `produits_tailles`;
INSERT INTO `produits_tailles` (`id`, `produit_id`, `taille`) VALUES
	(1, 30, 'XS'),
	(2, 29, 'XS'),
	(3, 28, 'XS'),
	(4, 27, 'XS'),
	(5, 26, 'XS'),
	(6, 25, 'XS'),
	(7, 24, 'XS'),
	(8, 23, 'XS'),
	(9, 22, 'XS'),
	(10, 21, 'XS'),
	(11, 20, 'XS'),
	(12, 19, 'XS'),
	(13, 18, 'XS'),
	(14, 17, 'XS'),
	(15, 16, 'XS'),
	(16, 15, 'XS'),
	(17, 14, 'XS'),
	(18, 13, 'XS'),
	(19, 12, 'XS'),
	(20, 11, 'XS'),
	(21, 10, 'XS'),
	(22, 9, 'XS'),
	(23, 8, 'XS'),
	(24, 7, 'XS'),
	(25, 6, 'XS'),
	(26, 5, 'XS'),
	(27, 4, 'XS'),
	(28, 3, 'XS'),
	(29, 2, 'XS'),
	(31, 30, 'S'),
	(32, 29, 'S'),
	(33, 28, 'S'),
	(34, 27, 'S'),
	(35, 26, 'S'),
	(36, 25, 'S'),
	(37, 24, 'S'),
	(38, 23, 'S'),
	(39, 22, 'S'),
	(40, 21, 'S'),
	(41, 20, 'S'),
	(42, 19, 'S'),
	(43, 18, 'S'),
	(44, 17, 'S'),
	(45, 16, 'S'),
	(46, 15, 'S'),
	(47, 14, 'S'),
	(48, 13, 'S'),
	(49, 12, 'S'),
	(50, 11, 'S'),
	(51, 10, 'S'),
	(52, 9, 'S'),
	(53, 8, 'S'),
	(54, 7, 'S'),
	(55, 6, 'S'),
	(56, 5, 'S'),
	(57, 4, 'S'),
	(58, 3, 'S'),
	(59, 2, 'S'),
	(61, 30, 'M'),
	(62, 29, 'M'),
	(63, 28, 'M'),
	(64, 27, 'M'),
	(65, 26, 'M'),
	(66, 25, 'M'),
	(67, 24, 'M'),
	(68, 23, 'M'),
	(69, 22, 'M'),
	(70, 21, 'M'),
	(71, 20, 'M'),
	(72, 19, 'M'),
	(73, 18, 'M'),
	(74, 17, 'M'),
	(75, 16, 'M'),
	(76, 15, 'M'),
	(77, 14, 'M'),
	(78, 13, 'M'),
	(79, 12, 'M'),
	(80, 11, 'M'),
	(81, 10, 'M'),
	(82, 9, 'M'),
	(83, 8, 'M'),
	(84, 7, 'M'),
	(85, 6, 'M'),
	(86, 5, 'M'),
	(87, 4, 'M'),
	(88, 3, 'M'),
	(89, 2, 'M'),
	(91, 30, 'L'),
	(92, 29, 'L'),
	(93, 28, 'L'),
	(94, 27, 'L'),
	(95, 26, 'L'),
	(96, 25, 'L'),
	(97, 24, 'L'),
	(98, 23, 'L'),
	(99, 22, 'L'),
	(100, 21, 'L'),
	(101, 20, 'L'),
	(102, 19, 'L'),
	(103, 18, 'L'),
	(104, 17, 'L'),
	(105, 16, 'L'),
	(106, 15, 'L'),
	(107, 14, 'L'),
	(108, 13, 'L'),
	(109, 12, 'L'),
	(110, 11, 'L'),
	(111, 10, 'L'),
	(112, 9, 'L'),
	(113, 8, 'L'),
	(114, 7, 'L'),
	(115, 6, 'L'),
	(116, 5, 'L'),
	(117, 4, 'L'),
	(118, 3, 'L'),
	(119, 2, 'L'),
	(121, 30, 'XL'),
	(122, 29, 'XL'),
	(123, 28, 'XL'),
	(124, 27, 'XL'),
	(125, 26, 'XL'),
	(126, 25, 'XL'),
	(127, 24, 'XL'),
	(128, 23, 'XL'),
	(129, 22, 'XL'),
	(130, 21, 'XL'),
	(131, 20, 'XL'),
	(132, 19, 'XL'),
	(133, 18, 'XL'),
	(134, 17, 'XL'),
	(135, 16, 'XL'),
	(136, 15, 'XL'),
	(137, 14, 'XL'),
	(138, 13, 'XL'),
	(139, 12, 'XL'),
	(140, 11, 'XL'),
	(141, 10, 'XL'),
	(142, 9, 'XL'),
	(143, 8, 'XL'),
	(144, 7, 'XL'),
	(145, 6, 'XL'),
	(146, 5, 'XL'),
	(147, 4, 'XL'),
	(148, 3, 'XL'),
	(149, 2, 'XL'),
	(151, 31, 'XL'),
	(163, 33, 'XS'),
	(164, 33, 'S'),
	(165, 33, 'M'),
	(166, 33, 'L'),
	(167, 33, 'XL'),
	(198, 1, 'XS'),
	(199, 1, 'S'),
	(200, 1, 'M'),
	(201, 1, 'L'),
	(202, 1, 'XL');

-- Listage de la structure de table ecommerce_simulation. utilisateurs
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('utilisateur','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'utilisateur',
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table ecommerce_simulation.utilisateurs : ~5 rows (environ)
DELETE FROM `utilisateurs`;
INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `date_naissance`, `email`, `telephone`, `mot_de_passe`, `role`, `date_inscription`) VALUES
	(1, 'Test', 'User', '2000-01-01', 'y@g.com', '0600000000', '$2y$10$Zld63KRUhMfy2qs4YFZ3LuEiYcX1Etk.HhCUo6GR2ENQptD3nQvK.', 'utilisateur', '2025-03-28 13:12:44'),
	(2, 'Admin', 'User', '1990-01-01', 'a@g.com', '0600000001', 'Ggart77130&', 'admin', '2025-03-28 13:12:44'),
	(3, 'Toumi', 'Yasser', '2005-02-02', 'yass.toumi20@gmail.com', '0641077701', '$2y$10$ZJICVJAZuKheglTyCGqnB.VRgFGW2TbIPgByy1PJ5WdV4T6RfvH8y', 'admin', '2025-03-28 16:42:51'),
	(4, 'Toumi', 'Yasser', '2005-02-02', 'yass.toumi21@gmail.com', '0641077701', '$2y$10$FWqN0ZuTf1KS2WOEk13Em.28THJwkWjbSIemywzjKKRuiX2GfaD3.', 'admin', '2025-03-28 16:54:20'),
	(5, 'CANTITEAU', 'Vaiana', '2004-12-21', 'vacantiteau@icloud.com', '0781800347', '$2y$10$Y15gl9pjEGBguOJ.oHIFWeq0VqncmEA1.Q09wdatFPe1uBOpfhdKy', 'utilisateur', '2025-03-28 19:40:17');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
