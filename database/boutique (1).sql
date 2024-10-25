-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 25, 2024 at 02:00 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boutique`
--

-- --------------------------------------------------------

--
-- Table structure for table `avis`
--

CREATE TABLE `avis` (
  `id_avis` int NOT NULL,
  `id_produit` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  `note` int NOT NULL,
  `commentaire` text NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `avis`
--

INSERT INTO `avis` (`id_avis`, `id_produit`, `id_utilisateur`, `note`, `commentaire`, `date_creation`) VALUES
(1, 31, 11, 3, 'dd', '2024-10-17 09:07:35'),
(2, 32, 11, 4, 'confortable', '2024-10-17 11:46:24'),
(3, 33, 11, 4, 'vsvdjdjl', '2024-10-17 11:48:00'),
(4, 33, 11, 2, 'ljefoubgf,lzjd', '2024-10-17 11:48:23'),
(5, 33, 11, 1, 'khegfyibejkmuoef', '2024-10-17 11:48:54'),
(6, 33, 11, 5, 'bon', '2024-10-17 11:49:59'),
(7, 31, 3, 3, 'yjycf', '2024-10-22 08:56:27');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id_categorie` int NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id_categorie`, `nom`, `parent_id`, `description`) VALUES
(1, 'Vêtements', NULL, 'Catégorie pour tous les vêtements de sport'),
(2, 'Chaussures', NULL, 'Catégorie pour toutes les chaussures de sport'),
(3, 'Accessoires', NULL, 'Catégorie pour tous les accessoires de sport'),
(4, 'Equipements', NULL, 'Catégorie pour tous les équipements de sport'),
(5, 'T-shirts', 1, 'Catégorie pour tous les T-shirts de sport'),
(6, 'Pantalons', 1, 'Catégorie pour tous les pantalons de sport'),
(7, 'Shorts', 1, 'Catégorie pour tous les shorts de sport'),
(8, 'Sweats', 1, 'Catégorie pour tous les sweats de sport'),
(9, 'Casquettes', NULL, 'Catégorie pour toutes les casquettes de sport'),
(10, 'Sacs', 3, 'Catégorie pour tous les sacs de sport'),
(11, 'Chaussettes', 3, 'Catégorie pour toutes les chaussettes de sport'),
(14, 'Combinaisons', NULL, 'Catégorie pour toutes les combinaisons de sport'),
(15, 'Vêtements de running', 1, 'Catégorie pour tous les vêtements de running'),
(16, 'Leggings', 1, 'Catégorie pour tous les leggings de sport');

-- --------------------------------------------------------

--
-- Table structure for table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` int NOT NULL,
  `date_commande` datetime DEFAULT NULL,
  `montant_total` decimal(10,2) DEFAULT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `statut` enum('panier','validé','expédié','annulé') DEFAULT 'panier'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `date_commande`, `montant_total`, `id_utilisateur`, `statut`) VALUES
(2, '2024-10-25 11:27:35', '95.00', 11, 'validé'),
(3, '2024-10-25 15:52:50', '210.00', 1, 'validé');

-- --------------------------------------------------------

--
-- Table structure for table `commande_produit`
--

CREATE TABLE `commande_produit` (
  `id_commande` int NOT NULL,
  `id_produit` int NOT NULL,
  `quantite` int DEFAULT NULL,
  `prix_unitaire` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `commande_produit`
--

INSERT INTO `commande_produit` (`id_commande`, `id_produit`, `quantite`, `prix_unitaire`) VALUES
(2, 31, 1, '30.00'),
(2, 32, 1, '40.00'),
(2, 33, 1, '25.00'),
(3, 31, 7, '30.00');

-- --------------------------------------------------------

--
-- Table structure for table `paiements`
--

CREATE TABLE `paiements` (
  `id_paiement` int NOT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `date_paiement` datetime DEFAULT NULL,
  `methode_paiement` varchar(50) DEFAULT NULL,
  `statut_paiement` enum('réussi','échoué','en attente') DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `id_commande` int DEFAULT NULL,
  `id_utilisateur` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `paiements`
--

INSERT INTO `paiements` (`id_paiement`, `montant`, `date_paiement`, `methode_paiement`, `statut_paiement`, `transaction_id`, `id_commande`, `id_utilisateur`) VALUES
(2, '95.00', '2024-10-25 11:27:35', 'carte', 'réussi', 'pi_test_5ec742dc-92b3-11ef-9e4d-f4390985b0f1', 2, 11),
(3, '210.00', '2024-10-25 15:52:50', 'carte', 'réussi', 'pi_3QDnwcP5XJmDt2UG0Q2hrdAT', 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `produits`
--

CREATE TABLE `produits` (
  `id_produit` int NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) NOT NULL,
  `description` text,
  `prix` decimal(10,2) DEFAULT NULL,
  `stock` int DEFAULT NULL,
  `taille` varchar(50) DEFAULT NULL,
  `tailles_disponibles` varchar(255) DEFAULT NULL,
  `marque` varchar(100) DEFAULT NULL,
  `date_ajout` date DEFAULT NULL,
  `collection` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produits`
--

INSERT INTO `produits` (`id_produit`, `nom`, `image_url`, `description`, `prix`, `stock`, `taille`, `tailles_disponibles`, `marque`, `date_ajout`, `collection`) VALUES
(31, ' T-shirt Performance', 'perfNike.jpg', 'T-shirt respirant et confortable, idéal pour les entraînements intensifs.', '30.00', 42, '0', 'XS,S,M,L,XL', 'Nike', NULL, 'Homme'),
(32, 'Pantalon de Yoga', 'yogaAdidas.jfif', 'Pantalon extensible et confortable, parfait pour le yoga et la méditation.', '40.00', 39, '0', 'XS,S,M,L,XL', 'Adidas', NULL, 'Femme'),
(33, 'Short de Course', 'shortPuma.jpg', 'Short léger et respirant, idéal pour la course à pied.', '25.00', 59, '0', 'S,M,L,XL', 'Puma', NULL, 'Homme'),
(34, 'Legging de Sport', 'leggingUnderArmour.webp', 'Legging ajusté et confortable, parfait pour toutes les activités sportives.', '35.00', 35, '0', 'XS,S,M,L', 'UnderArmour', NULL, 'Femme'),
(35, 'Hoodie de Sport', 'hoodieReebok.webp', 'Sweat à capuche chaud et confortable, idéal pour les jours plus frais.', '50.00', 45, '0', 'S,M,L,XL,XXL', 'Reebok', NULL, 'Homme'),
(36, 'Veste de Running', 'vesteNorthface.jpg', 'Veste légère et imperméable, parfaite pour les sorties de running.', '60.00', 30, '0', 'XS,S,M,L,XL', 'NorthFace', NULL, 'Femme'),
(37, 'T-shirt de Fitness', 'tshirtNewBalance.webp', 'T-shirt respirant et ajusté, parfait pour les séances de fitness.', '28.00', 40, '0', 'XS,S,M,L', 'New Balance', NULL, 'Femme'),
(38, 'Gilet de Sport', 'giletAsics.jpg', 'Gilet léger et chaud, idéal pour les activités en extérieur.', '45.00', 35, '0', 'S,M,L,XL', 'Asics', NULL, 'Homme'),
(39, 'Ensemble de Jogging', 'ensembleChamion.jpg', 'Ensemble de jogging confortable et chaud, parfait pour le sport et le loisir.', '70.00', 40, '0', 'S,M,L,XL', 'Champion', NULL, 'Homme');

-- --------------------------------------------------------

--
-- Table structure for table `produit_categorie`
--

CREATE TABLE `produit_categorie` (
  `id_produit` int NOT NULL,
  `id_categorie` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produit_categorie`
--

INSERT INTO `produit_categorie` (`id_produit`, `id_categorie`) VALUES
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(37, 5),
(35, 8);

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `motdepasse` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `email`, `adresse`, `motdepasse`, `role`) VALUES
(1, 'Baileche', 'Hamza', 'hamza1301@outlook.fr', NULL, '$2y$10$pPfTCUGDooaXAzpmVAZFc.v1HxQTxoxQNsRbm7t0tQO0BrwYCc.mu', 'user'),
(3, 'Baileche', 'Hamza', 'hamza.baileche@laplateforme.io', NULL, '$2y$10$soDDvpKka.ECa.ZY7oxwAOzTKr8Q0FYuf0HY5yPzITOQl3..kMMMa', 'admin'),
(4, 'as', 'as', 'jhzdjhed@gmail.fr', NULL, '$2y$10$hiUtprh65P3qAj29c.JmU.jgRmmZpU7.e0uikjf4rHbxd15jynzTW', 'user'),
(5, 'as', 'as', 'jhkkkdjhed@gmail.fr', NULL, '$2y$10$u/H8LNpU7lUih.sVPvD37uyjIf1jsxqb5OCi9OVzQBcpcUOXtHJKC', 'user'),
(6, 'zegy', 'jhéevdgjh', 'yefgedtfet@gmail.fr', NULL, '$2y$10$/BruA2Z6a0g62VAellMIZ.xR9KE/tY5FU43hqS57GSXALlWmsZiXC', 'user'),
(7, 'zegy', 'jhéevdgjh', 'yefgeedtfet@gmail.fr', NULL, '$2y$10$69p7aiPk5a1RhztOnVR5nuyZfEBV3bhwOw5fLPb397ghhi9cGUEHe', 'user'),
(8, 'Soilihi', 'Hamza', 'hamza@hamza.fr', NULL, '$2y$10$znnAnemAhpreCwiMYVIdB.XULNMbLhXBmdlyWbFXzRMyz2c9xSIaS', 'user'),
(9, 'fsfds', 'fsd', 'derroce@gmail.com', NULL, '$2y$10$s5XZBqP3bRAI2buklEAWauABgwK7.PNA57guszhWBgLS/kuCMVP/a', 'user'),
(10, 'arthur', 'arthur', 'arthur@gmail.com', NULL, '$2y$10$G5Zy3GoNC1Cog8YAB1UxyefStxQ9nr/npRduorRQ15r40hRWvgwEC', 'admin'),
(11, 'Diomande', 'Adama', 'adama.diomande@laplateforme.io', NULL, '$2y$10$g7YKOoGuXuEIzqoX/n/9seNhgIih5y0vREtRCtyg/7YxPlePdheb2', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id_avis`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Indexes for table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Indexes for table `commande_produit`
--
ALTER TABLE `commande_produit`
  ADD PRIMARY KEY (`id_commande`,`id_produit`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Indexes for table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id_paiement`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Indexes for table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id_produit`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `avis`
--
ALTER TABLE `avis`
  MODIFY `id_avis` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id_paiement` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `produits`
--
ALTER TABLE `produits`
  MODIFY `id_produit` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Constraints for table `commande_produit`
--
ALTER TABLE `commande_produit`
  ADD CONSTRAINT `commande_produit_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`),
  ADD CONSTRAINT `commande_produit_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`);

--
-- Constraints for table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`),
  ADD CONSTRAINT `paiements_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
