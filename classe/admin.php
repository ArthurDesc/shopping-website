<?php

class Admin {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Gestion des produits
    public function ajouterProduit($nom, $description, $prix, $stock, $categorie_id) {
        $sql = "INSERT INTO produits (nom, description, prix, stock, categorie_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssdii", $nom, $description, $prix, $stock, $categorie_id);
        return $stmt->execute();
    }

    public function modifierProduit($id, $nom, $description, $prix, $stock, $categorie_id) {
        $sql = "UPDATE produits SET nom = ?, description = ?, prix = ?, stock = ?, categorie_id = ? WHERE id_produit = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssdiii", $nom, $description, $prix, $stock, $categorie_id, $id);
        return $stmt->execute();
    }

    public function supprimerProduit($id) {
        $sql = "DELETE FROM produits WHERE id_produit = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listerProduits() {
        $sql = "SELECT * FROM produits";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Gestion des commandes
    public function listerCommandes() {
        $sql = "SELECT c.*, u.email FROM commandes c JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur ORDER BY c.date_commande DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function modifierStatutCommande($id_commande, $nouveau_statut) {
        $sql = "UPDATE commandes SET statut = ? WHERE id_commande = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $nouveau_statut, $id_commande);
        return $stmt->execute();
    }

    // Gestion des utilisateurs
    public function listerUtilisateurs() {
        $sql = "SELECT * FROM utilisateurs";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function modifierUtilisateur($id, $nom, $prenom, $email, $role) {
        $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, role = ? WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $nom, $prenom, $email, $role, $id);
        return $stmt->execute();
    }

    public function supprimerUtilisateur($id) {
        $sql = "DELETE FROM utilisateurs WHERE id_utilisateur = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Gestion des catégories
    public function ajouterCategorie($nom) {
        $sql = "INSERT INTO categories (nom) VALUES (?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $nom);
        return $stmt->execute();
    }

    public function modifierCategorie($id, $nom) {
        $sql = "UPDATE categories SET nom = ? WHERE id_categorie = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $nom, $id);
        return $stmt->execute();
    }

    public function supprimerCategorie($id) {
        $sql = "DELETE FROM categories WHERE id_categorie = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function listerCategories() {
        $sql = "SELECT * FROM categories";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Statistiques
    public function obtenirStatistiquesVentes() {
        $sql = "SELECT COUNT(*) as total_commandes, SUM(montant_total) as chiffre_affaires FROM commandes";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc();
    }

    public function obtenirProduitsPopulaires($limite = 5) {
        $sql = "SELECT p.nom, SUM(cp.quantite) as total_vendu 
                FROM commande_produit cp 
                JOIN produits p ON cp.id_produit = p.id_produit 
                GROUP BY cp.id_produit 
                ORDER BY total_vendu DESC 
                LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}