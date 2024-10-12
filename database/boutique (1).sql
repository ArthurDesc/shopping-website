-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : sam. 12 oct. 2024 à 10:25
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
  `note` int DEFAULT NULL,
  `commentaire` text,
  `date_avis` datetime DEFAULT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `id_produit` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_categorie` int NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id_categorie`, `nom`, `description`) VALUES
(1, 'Vêtements', 'Catégorie pour tous les vêtements de sport'),
(2, 'Chaussures', 'Catégorie pour toutes les chaussures de sport'),
(3, 'Accessoires', 'Catégorie pour tous les accessoires de sport'),
(4, 'Equipements', 'Catégorie pour tous les équipements de sport'),
(5, 'T-shirts', 'Catégorie pour tous les t-shirts de sport'),
(6, 'Pantalons', 'Catégorie pour tous les pantalons de sport'),
(7, 'Shorts', 'Catégorie pour tous les shorts de sport'),
(8, 'Sweats', 'Catégorie pour tous les sweats de sport'),
(9, 'Casquettes', 'Catégorie pour toutes les casquettes de sport'),
(10, 'Sacs', 'Catégorie pour tous les sacs de sport'),
(11, 'Chaussettes', 'Catégorie pour toutes les chaussettes de sport'),
(12, 'Gants', 'Catégorie pour tous les gants de sport'),
(13, 'Maillots de bain', 'Catégorie pour tous les maillots de bain de sport'),
(14, 'Combinaisons', 'Catégorie pour toutes les combinaisons de sport'),
(15, 'Vêtements de running', 'Catégorie pour tous les vêtements de running'),
(16, 'Leggings', 'leggings');

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
  `marque` varchar(100) DEFAULT NULL,
  `date_ajout` date DEFAULT NULL,
  `collection` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id_produit`, `nom`, `image_url`, `description`, `prix`, `stock`, `taille`, `marque`, `date_ajout`, `collection`) VALUES
(31, ' T-shirt Performance', 'perfNike.jpg', 'T-shirt respirant et confortable, idéal pour les entraînements intensifs.', 30.00, 50, '0', 'Nike', NULL, 'Homme'),
(32, 'Pantalon de Yoga', 'yogaAdidas.jfif', 'Pantalon extensible et confortable, parfait pour le yoga et la méditation.', 40.00, 40, '0', 'Adidas', NULL, 'Femme'),
(33, 'Short de Course', 'shortPuma.jpg', 'Short léger et respirant, idéal pour la course à pied.', 25.00, 60, '0', 'Puma', NULL, 'Homme'),
(34, 'Legging de Sport', 'leggingUnderArmour.webp', 'Legging ajusté et confortable, parfait pour toutes les activités sportives.', 35.00, 35, '0', 'UnderArmour', NULL, 'Femme'),
(35, 'Hoodie de Sport', 'hoodieReebok.webp', 'Sweat à capuche chaud et confortable, idéal pour les jours plus frais.', 50.00, 45, '0', 'Reebok', NULL, 'Homme'),
(36, 'Veste de Running', 'vesteNorthface.jpg', 'Veste légère et imperméable, parfaite pour les sorties de running.', 60.00, 30, '0', 'NorthFace', NULL, 'Femme'),
(37, 'T-shirt de Fitness', 'tshirtNewBalance.webp', 'T-shirt respirant et ajusté, parfait pour les séances de fitness.', 28.00, 40, '0', 'New Balance', NULL, 'Femme'),
(38, 'Gilet de Sport', 'giletAsics.jpg', 'Gilet léger et chaud, idéal pour les activités en extérieur.', 45.00, 35, '0', 'Asics', NULL, 'Homme'),
(39, 'Ensemble de Jogging', 'ensembleChamion.jpg', 'Ensemble de jogging confortable et chaud, parfait pour le sport et le loisir.', 70.00, 40, '0', 'Champion', NULL, 'Homme');

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
-- Structure de la table `utilisateurs`
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
-- Déchargement des données de la table `utilisateurs`
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
(10, 'arthur', 'arthur', 'arthur@gmail.com', NULL, '$2y$10$G5Zy3GoNC1Cog8YAB1UxyefStxQ9nr/npRduorRQ15r40hRWvgwEC', 'admin');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id_avis`),
  ADD KEY `id_utilisateur` (`id_utilisateur`),
  ADD KEY `idx_avis_produit` (`id_produit`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_categorie`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id_commande`),
  ADD KEY `idx_commande_utilisateur` (`id_utilisateur`);

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
  ADD KEY `idx_paiement_commande` (`id_commande`),
  ADD KEY `idx_paiement_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id_produit`);

--
-- Index pour la table `produit_categorie`
--
ALTER TABLE `produit_categorie`
  ADD PRIMARY KEY (`id_produit`,`id_categorie`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id_avis` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_categorie` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id_commande` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id_paiement` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id_produit` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id_utilisateur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commande_produit`
--
ALTER TABLE `commande_produit`
  ADD CONSTRAINT `commande_produit_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `commande_produit_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id_commande`) ON DELETE CASCADE,
  ADD CONSTRAINT `paiements_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `produit_categorie`
--
ALTER TABLE `produit_categorie`
  ADD CONSTRAINT `produit_categorie_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id_produit`) ON DELETE CASCADE,
  ADD CONSTRAINT `produit_categorie_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id_categorie`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
