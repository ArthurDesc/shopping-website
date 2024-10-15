<?php
class CategoryManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addCategory($nom, $description) {
        $sql = "INSERT INTO categories (nom, description) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $nom, $description);
        
        if ($stmt->execute()) {
            return $stmt->insert_id;
        }
        return false;
    }

    public function updateCategory($id, $nom, $description) {
        $sql = "UPDATE categories SET nom = ?, description = ? WHERE id_categorie = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Erreur de préparation de la requête : " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("ssi", $nom, $description, $id);
        if ($stmt->execute()) {
            return true;
        } else {
            error_log("Erreur lors de l'exécution de la requête : " . $stmt->error);
            return false;
        }
    }

    public function deleteCategory($id) {
        // Vérifier d'abord s'il y a des produits associés à cette catégorie
        $sql = "SELECT COUNT(*) FROM produit_categorie WHERE id_categorie = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];

        if ($count > 0) {
            return false; // Ne pas supprimer la catégorie si elle est associée à des produits
        }

        // Si aucun produit n'est associé, supprimer la catégorie
        $sql = "DELETE FROM categories WHERE id_categorie = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getAllCategories() {
        $sql = "SELECT id_categorie, nom, description, parent_id FROM categories ORDER BY parent_id IS NULL DESC, nom ASC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCategory($id) {
        $sql = "SELECT * FROM categories WHERE id_categorie = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getCategoryProducts($category_id) {
        $sql = "SELECT p.* FROM produits p 
                JOIN produit_categorie pc ON p.id_produit = pc.id_produit 
                WHERE pc.id_categorie = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
