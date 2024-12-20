-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 01 nov. 2024 à 14:07
-- Version du serveur : 8.0.30
-- Version de PHP : 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `boutique`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id_avis` int NOT NULL,
  `id_produit` int NOT NULL,
  `id_utilisateur` int NOT NULL,
  `note` int NOT NULL,
  `commentaire` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_categorie` int NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categories`
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
(16, 'Leggings', 1, 'Catégorie pour tous les leggings de sport'),
(18, 'Sports', NULL, NULL),
(19, 'Football', 18, NULL),
(20, 'Rugby', 18, NULL),
(21, 'Running', 18, NULL),
(22, 'Handball', 18, NULL),
(23, 'Basketball', 18, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id_commande` int NOT NULL,
  `date_commande` datetime DEFAULT NULL,
  `montant_total` decimal(10,2) DEFAULT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `statut` enum('panier','validé','expédié','annulé') DEFAULT 'panier'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id_commande`, `date_commande`, `montant_total`, `id_utilisateur`, `statut`) VALUES
(1, '2024-10-28 09:28:01', 40.00, 10, 'validé'),
(2, '2024-10-29 09:33:12', 210.00, 1, 'validé'),
(3, '2024-10-29 10:34:34', 100.00, 1, 'validé');

-- --------------------------------------------------------

--
-- Structure de la table `commande_produit`
--

CREATE TABLE `commande_produit` (
  `id_commande` int NOT NULL,
  `id_produit` int NOT NULL,
  `quantite` int DEFAULT NULL,
  `prix_unitaire` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `commande_produit`
--

INSERT INTO `commande_produit` (`id_commande`, `id_produit`, `quantite`, `prix_unitaire`) VALUES
(1, 32, 1, 40.00),
(2, 31, 7, 30.00),
(3, 33, 4, 25.00);

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
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
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id_paiement`, `montant`, `date_paiement`, `methode_paiement`, `statut_paiement`, `transaction_id`, `id_commande`, `id_utilisateur`) VALUES
(1, 40.00, '2024-10-28 09:28:01', 'carte', 'réussi', 'pi_3QEoIwP5XJmDt2UG1shjWVPD', 1, 10),
(2, 210.00, '2024-10-29 09:33:12', 'carte', 'réussi', 'pi_3QFArTP5XJmDt2UG0Su8I6w4', 2, 1),
(3, 100.00, '2024-10-29 10:34:34', 'carte', 'réussi', 'pi_3QFBosP5XJmDt2UG1Bp3reun', 3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `produits`
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
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id_produit`, `nom`, `image_url`, `description`, `prix`, `stock`, `taille`, `tailles_disponibles`, `marque`, `date_ajout`, `collection`) VALUES
(31, ' T-shirt Performance', 'perfNike.jpg', 'T-shirt respirant et confortable, idéal pour les entraînements intensifs.', 30.00, 43, '0', 'XS,S,M,L,XL', 'Nike', NULL, 'Homme'),
(32, 'Pantalon de Yoga', 'yogaAdidas.jfif', 'Pantalon extensible et confortable, parfait pour le yoga et la méditation.', 40.00, 39, '0', 'XS,S,M,L,XL', 'Adidas', NULL, 'Femme'),
(33, 'Short de Course', 'shortPuma.jpg', 'Short léger et respirant, idéal pour la course à pied.', 25.00, 56, '0', 'S,M,L,XL', 'Puma', NULL, 'Homme'),
(34, 'Legging de Sport', 'leggingUnderArmour.webp', 'Legging ajusté et confortable, parfait pour toutes les activités sportives.', 35.00, 35, '0', 'XS,S,M,L', 'UnderArmour', NULL, 'Femme'),
(35, 'Hoodie de Sport', 'hoodieReebok.webp', 'Sweat à capuche chaud et confortable, idéal pour les jours plus frais.', 50.00, 45, '0', 'S,M,L,XL,XXL', 'Reebok', NULL, 'Homme'),
(36, 'Veste de Running', 'vesteNorthface.jpg', 'Veste légère et imperméable, parfaite pour les sorties de running.', 60.00, 30, '0', 'XS,S,M,L,XL', 'NorthFace', NULL, 'Femme'),
(37, 'T-shirt de Fitness', 'tshirtNewBalance.webp', 'T-shirt respirant et ajusté, parfait pour les séances de fitness.', 28.00, 40, '0', 'XS,S,M,L', 'New Balance', NULL, 'Femme'),
(38, 'Gilet de Sport', 'giletAsics.jpg', 'Gilet léger et chaud, idéal pour les activités en extérieur.', 45.00, 35, '0', 'S,M,L,XL', 'Asics', NULL, 'Homme'),
(39, 'Ensemble de Jogging', 'ensembleChamion.jpg', 'Ensemble de jogging confortable et chaud, parfait pour le sport et le loisir.', 70.00, 40, '0', 'S,M,L,XL', 'Champion', NULL, 'Homme');

-- --------------------------------------------------------

--
-- Structure de la table `produit_categorie`
--

CREATE TABLE `produit_categorie` (
  `id_produit` int NOT NULL,
  `id_categorie` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `produit_categorie`
--

INSERT INTO `produit_categorie` (`id_produit`, `id_categorie`) VALUES
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(37, 5),
(35, 8),
(34, 1),
(34, 16),
(39, 2),
(39, 1),
(31, 5);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id_utilisateur` int NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `motdepasse` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `email`, `adresse`, `motdepasse`, `role`, `telephone`) VALUES
(1, 'Baileche', 'Hamza', 'hamza1301@outlook.fr', '22 rue jouv13003', '$2y$10$pPfTCUGDooaXAzpmVAZFc.v1HxQTxoxQNsRbm7t0tQO0BrwYCc.mu', 'user', '0739265281'),
(3, 'Baileche', 'Hamza', 'hamza.baileche@laplateforme.io', NULL, '$2y$10$soDDvpKka.ECa.ZY7oxwAOzTKr8Q0FYuf0HY5yPzITOQl3..kMMMa', 'admin', NULL),
(4, 'as', 'as', 'jhzdjhed@gmail.fr', NULL, '$2y$10$hiUtprh65P3qAj29c.JmU.jgRmmZpU7.e0uikjf4rHbxd15jynzTW', 'user', NULL),
(5, 'as', 'as', 'jhkkkdjhed@gmail.fr', NULL, '$2y$10$u/H8LNpU7lUih.sVPvD37uyjIf1jsxqb5OCi9OVzQBcpcUOXtHJKC', 'user', NULL),
(6, 'zegy', 'jhéevdgjh', 'yefgedtfet@gmail.fr', NULL, '$2y$10$/BruA2Z6a0g62VAellMIZ.xR9KE/tY5FU43hqS57GSXALlWmsZiXC', 'user', NULL),
(7, 'zegy', 'jhéevdgjh', 'yefgeedtfet@gmail.fr', NULL, '$2y$10$69p7aiPk5a1RhztOnVR5nuyZfEBV3bhwOw5fLPb397ghhi9cGUEHe', 'user', NULL),
(8, 'Soilihi', 'Hamza', 'hamza@hamza.fr', NULL, '$2y$10$znnAnemAhpreCwiMYVIdB.XULNMbLhXBmdlyWbFXzRMyz2c9xSIaS', 'user', NULL),
(9, 'fsfds', 'fsd', 'derroce@gmail.com', NULL, '$2y$10$s5XZBqP3bRAI2buklEAWauABgwK7.PNA57guszhWBgLS/kuCMVP/a', 'admin', NULL),
(10, 'arthur', 'arthur', 'arthur@gmail.com', NULL, '$2y$10$G5Zy3GoNC1Cog8YAB1UxyefStxQ9nr/npRduorRQ15r40hRWvgwEC', 'admin', NULL),
(11, 'Diomande', 'Adama', 'adama.diomande@laplateforme.io', NULL, '$2y$10$g7YKOoGuXuEIzqoX/n/9seNhgIih5y0vREtRCtyg/7YxPlePdheb2', 'user', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `wishlist`
--

CREATE TABLE `wishlist` (
  `id_utilisateur` int NOT NULL,
  `id_produit` int NOT NULL,
  `date_ajout` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `wishlist`
--

INSERT INTO `wishlist` (`id_utilisateur`, `id_produit`, `date_ajout`) VALUES
(9, 31, '2024-11-01 14:00:10'),
(9, 32, '2024-11-01 14:01:59'),
(9, 34, '2024-11-01 14:02:00');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id_avis`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_date_creation` (`date_creation`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `commande_produit`
--
ALTER TABLE `commande_produit`
  ADD PRIMARY KEY (`id_commande`,`id_produit`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id_paiement`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id_produit`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`);

--
-- Index pour la table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id_utilisateur`,`id_produit`),
  ADD KEY `id_produit` (`id_produit`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id_avis` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id_paiement` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id_produit` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE,
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `commande_produit`
--
ALTER TABLE `commande_produit`
  ADD CONSTRAINT `commande_produit_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `commande_produit_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`);

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`),
  ADD CONSTRAINT `paiements_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
