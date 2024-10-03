<?php
class ArticleManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addArticle($nom, $description, $prix, $stock, $taille, $marque, $collection) {
        $sql = "INSERT INTO produits (nom, description, prix, stock, taille, marque, date_ajout, collection) 
                VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssdiisss", $nom, $description, $prix, $stock, $taille, $marque, $collection);
        
        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        return false;
    }

    public function updateArticle($id, $nom, $description, $prix, $stock, $taille, $marque, $collection) {
        $sql = "UPDATE produits SET nom = ?, description = ?, prix = ?, stock = ?, 
                taille = ?, marque = ?, collection = ? WHERE id_produit = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssdiissi", $nom, $description, $prix, $stock, $taille, $marque, $collection, $id);
        return $stmt->execute();
    }

    public function deleteArticle($id) {
        $sql = "DELETE FROM produits WHERE id_produit = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getArticle($id) {
        $sql = "SELECT * FROM produits WHERE id_produit = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAllArticles() {
        $sql = "SELECT * FROM produits";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getArticleCategories($article_id) {
        $sql = "SELECT c.* FROM categories c 
                JOIN produit_categorie pc ON c.id_categorie = pc.id_categorie 
                WHERE pc.id_produit = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addCategoryToArticle($article_id, $category_id) {
        $sql = "INSERT INTO produit_categorie (id_produit, id_categorie) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $article_id, $category_id);
        return $stmt->execute();
    }

    public function removeCategoryFromArticle($article_id, $category_id) {
        $sql = "DELETE FROM produit_categorie WHERE id_produit = ? AND id_categorie = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $article_id, $category_id);
        return $stmt->execute();
    }

    public function getAllCategories() {
        $sql = "SELECT * FROM categories";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addCategory($nom, $description) {
        $sql = "INSERT INTO categories (nom, description) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ss", $nom, $description);
        return $stmt->execute();
    }

    public function updateCategory($id, $nom, $description) {
        $sql = "UPDATE categories SET nom = ?, description = ? WHERE id_categorie = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $nom, $description, $id);
        return $stmt->execute();
    }

    public function deleteCategory($id) {
        // Vérifiez d'abord s'il y a des produits dans cette catégorie
        $sql = "SELECT COUNT(*) FROM produit_categorie WHERE id_categorie = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];

        if ($count > 0) {
            return false; // Ne supprimez pas la catégorie si elle contient des produits
        }

        $sql = "DELETE FROM categories WHERE id_categorie = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
