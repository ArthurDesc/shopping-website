<?php
class CategoryManager {
    private $conn;
    private $lastError = '';

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addCategory($nom, $parent_id = null) {
        try {
            error_log("CategoryManager: Tentative d'ajout de la catégorie: $nom");
            
            // Obtenir le prochain ID disponible
            $sql = "SELECT MAX(id_categorie) as max_id FROM categories";
            $result = $this->conn->query($sql);
            $row = $result->fetch_assoc();
            $next_id = $row['max_id'] + 1;
            
            $sql = "INSERT INTO categories (id_categorie, nom, parent_id) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("CategoryManager: Erreur de préparation de la requête: " . $this->conn->error);
                throw new Exception("Erreur de préparation de la requête: " . $this->conn->error);
            }
            $stmt->bind_param("isi", $next_id, $nom, $parent_id);
            $result = $stmt->execute();
            if (!$result) {
                error_log("CategoryManager: Erreur lors de l'exécution de la requête: " . $stmt->error);
                throw new Exception($stmt->error);
            }
            error_log("CategoryManager: Catégorie ajoutée avec succès");
            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Erreur dans CategoryManager::addCategory : " . $this->lastError);
            return false;
        }
    }

    public function updateCategory($categoryId, $newName) {
        try {
            $stmt = $this->conn->prepare("UPDATE categories SET nom = ? WHERE id_categorie = ?");
            if (!$stmt) {
                $this->lastError = "Erreur de préparation de la requête: " . $this->conn->error;
                error_log($this->lastError);
                return false;
            }

            $stmt->bind_param("si", $newName, $categoryId);
            $result = $stmt->execute();
            
            if (!$result) {
                $this->lastError = "Erreur d'exécution: " . $stmt->error;
                error_log($this->lastError);
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            $this->lastError = "Exception: " . $e->getMessage();
            error_log($this->lastError);
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
        $sql = "SELECT id_categorie, nom, parent_id FROM categories ORDER BY parent_id IS NULL DESC, nom ASC";
        $result = $this->conn->query($sql);
        $categories = $result->fetch_all(MYSQLI_ASSOC);
        error_log("Catégories récupérées : " . json_encode($categories));
        return $categories;
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

    public function getProductCategories($product_id) {
        $sql = "SELECT c.* FROM categories c 
                JOIN produit_categorie pc ON c.id_categorie = pc.id_categorie 
                WHERE pc.id_produit = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getLastError() {
        return $this->lastError;
    }

    public function getHeaderCategories() {
        return [
            'homme' => ['Vêtements', 'Chaussures', 'Accessoires'],
            'femme' => ['Vêtements', 'Chaussures', 'Accessoires'],
            'enfant' => ['Vêtements', 'Chaussures', 'Accessoires'],
            'sport' => ['Football', 'Basketball', 'Running', 'Tennis']
        ];
    }

    public function getCategoryIdByName($name) {
        // Cette méthode devrait retourner l'ID de la catégorie basé sur son nom
        // Implémentez la logique pour faire correspondre les noms aux IDs de votre base de données
    }

    public function getCollection($product_id) {
        $sql = "SELECT collection FROM produits WHERE id_produit = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            $this->lastError = "Erreur de préparation de la requête: " . $this->conn->error;
            error_log($this->lastError);
            return null;
        }

        $stmt->bind_param("i", $product_id);
        $result = $stmt->execute();
        
        if (!$result) {
            $this->lastError = "Erreur d'exécution: " . $stmt->error;
            error_log($this->lastError);
            return null;
        }

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        return $row ? $row['collection'] : null;
    }
}
