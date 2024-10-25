-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 25, 2024 at 09:32 AM
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
(31, ' T-shirt Performance', 'perfNike.jpg', 'T-shirt respirant et confortable, idéal pour les entraînements intensifs.', '30.00', 49, '0', 'XS,S,M,L,XL', 'Nike', NULL, 'Homme'),
(32, 'Pantalon de Yoga', 'yogaAdidas.jfif', 'Pantalon extensible et confortable, parfait pour le yoga et la méditation.', '40.00', 39, '0', 'XS,S,M,L,XL', 'Adidas', NULL, 'Femme'),
(33, 'Short de Course', 'shortPuma.jpg', 'Short léger et respirant, idéal pour la course à pied.', '25.00', 59, '0', 'S,M,L,XL', 'Puma', NULL, 'Homme'),
(34, 'Legging de Sport', 'leggingUnderArmour.webp', 'Legging ajusté et confortable, parfait pour toutes les activités sportives.', '35.00', 35, '0', 'XS,S,M,L', 'UnderArmour', NULL, 'Femme'),
(35, 'Hoodie de Sport', 'hoodieReebok.webp', 'Sweat à capuche chaud et confortable, idéal pour les jours plus frais.', '50.00', 45, '0', 'S,M,L,XL,XXL', 'Reebok', NULL, 'Homme'),
(36, 'Veste de Running', 'vesteNorthface.jpg', 'Veste légère et imperméable, parfaite pour les sorties de running.', '60.00', 30, '0', 'XS,S,M,L,XL', 'NorthFace', NULL, 'Femme'),
(37, 'T-shirt de Fitness', 'tshirtNewBalance.webp', 'T-shirt respirant et ajusté, parfait pour les séances de fitness.', '28.00', 40, '0', 'XS,S,M,L', 'New Balance', NULL, 'Femme'),
(38, 'Gilet de Sport', 'giletAsics.jpg', 'Gilet léger et chaud, idéal pour les activités en extérieur.', '45.00', 35, '0', 'S,M,L,XL', 'Asics', NULL, 'Homme'),
(39, 'Ensemble de Jogging', 'ensembleChamion.jpg', 'Ensemble de jogging confortable et chaud, parfait pour le sport et le loisir.', '70.00', 40, '0', 'S,M,L,XL', 'Champion', NULL, 'Homme');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id_produit`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `produits`
--
ALTER TABLE `produits`
  MODIFY `id_produit` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
