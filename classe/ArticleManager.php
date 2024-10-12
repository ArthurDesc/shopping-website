<?php
class ArticleManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addArticle($nom, $description, $prix, $stock, $taille, $marque, $collection, $image, $categories = []) {
        try {
            // Préparer la requête d'insertion de l'article
            $query = "INSERT INTO produits (nom, description, prix, stock, taille, marque, collection, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                die("Erreur de préparation de la requête : " . $this->conn->error);
            }
            
            // Convertir les valeurs numériques en nombres
            $prix = floatval($prix);
            $stock = intval($stock);
            
            $stmt->bind_param("ssdiisss", $nom, $description, $prix, $stock, $taille, $marque, $collection, $image);
            
            // Exécuter la requête
            if ($stmt->execute()) {
                $article_id = $stmt->insert_id;
                
                // Insérer les catégories pour cet article seulement si des catégories sont fournies
                if (!empty($categories)) {
                    $category_query = "INSERT INTO produit_categorie (id_produit, id_categorie) VALUES (?, ?)";
                    $category_stmt = $this->conn->prepare($category_query);
                    
                    foreach ($categories as $category_id) {
                        $category_stmt->bind_param("ii", $article_id, $category_id);
                        $category_stmt->execute();
                    }
                    
                    $category_stmt->close();
                }
                
                $stmt->close();
                return true;
            } else {
                error_log("Erreur lors de l'ajout de l'article : " . $stmt->error);
                $stmt->close();
                return false;
            }
        } catch (Exception $e) {
            error_log("Erreur lors de l'ajout de l'article : " . $e->getMessage());
            return false;
        }
    }

    public function updateArticle($id, $nom, $description, $prix, $stock, $taille, $marque, $collection, $categories) {
        $this->conn->begin_transaction();
        try {
            $sql = "UPDATE produits SET nom = ?, description = ?, prix = ?, stock = ?, taille = ?, marque = ?, collection = ? WHERE id_produit = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssdiissi", $nom, $description, $prix, $stock, $taille, $marque, $collection, $id);
            $stmt->execute();

            // Vérifiez si $categories est une chaîne et convertissez-la en tableau si nécessaire
            if (is_string($categories)) {
                $categories = explode(',', $categories);
            }

            // Assurez-vous que $categories est un tableau
            if (is_array($categories)) {
                $this->updateArticleCategories($id, $categories);
            } else {
                throw new Exception("Les catégories doivent être un tableau ou une chaîne séparée par des virgules.");
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Erreur lors de la mise à jour de l'article : " . $e->getMessage());
            return false;
        }
    }

    private function updateArticleCategories($articleId, array $newCategories) {
        // Supprimer toutes les anciennes catégories
        $sql = "DELETE FROM produit_categorie WHERE id_produit = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $articleId);
        $stmt->execute();

        // Ajouter les nouvelles catégories
        if (!empty($newCategories)) {
            $sql = "INSERT INTO produit_categorie (id_produit, id_categorie) VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            foreach ($newCategories as $categoryId) {
                if (!empty($categoryId)) { // Vérifier que l'ID de catégorie n'est pas vide
                    $stmt->bind_param("ii", $articleId, $categoryId);
                    $stmt->execute();
                }
            }
        }
    }

    public function deleteArticle($id) {
        try {
            $this->conn->begin_transaction();

            // Supprimer d'abord les relations dans la table produit_categorie
            $sql = "DELETE FROM produit_categorie WHERE id_produit = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Ensuite, supprimer le produit
            $sql = "DELETE FROM produits WHERE id_produit = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $result = $stmt->execute();

            if ($result) {
                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollback();
                throw new Exception("Erreur lors de la suppression du produit");
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log($e->getMessage());
            throw $e; // Propager l'exception pour qu'elle soit gérée dans delete_article.php
        }
    }

    public function getArticle($id) {
        $sql = "SELECT * FROM produits WHERE id_produit = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAllArticles() {
        $sql = "SELECT p.*, GROUP_CONCAT(c.nom SEPARATOR ', ') as categories
                FROM produits p
                LEFT JOIN produit_categorie pc ON p.id_produit = pc.id_produit
                LEFT JOIN categories c ON pc.id_categorie = c.id_categorie
                GROUP BY p.id_produit";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getArticleCategories($articleId) {
        $sql = "SELECT c.id_categorie, c.nom 
                FROM categories c
                JOIN produit_categorie pc ON c.id_categorie = pc.id_categorie
                WHERE pc.id_produit = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $articleId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addCategoryToArticle($article_id, $category_id) {
        $sql = "INSERT INTO produit_categorie (id_produit, id_categorie) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $article_id, $category_id);
        return $stmt->execute();
    }

    public function removeCategoryFromArticle($article_id, $category_id) {
        $sql = "DELETE FROM produit_categorie WHERE id_produit = ? AND id_categorie = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $article_id, $category_id);
        return $stmt->execute();
    }

    public function getAllCategories() {
        $sql = "SELECT * FROM categories";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addCategory($nom, $description) {
        $sql = "INSERT INTO categories (nom, description) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $nom, $description);
        return $stmt->execute();
    }

    public function updateCategory($id, $nom, $description) {
        $sql = "UPDATE categories SET nom = ?, description = ? WHERE id_categorie = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $nom, $description, $id);
        return $stmt->execute();
    }

    public function deleteCategory($id) {
        // Vérifiez d'abord s'il y a des produits dans cette catégorie
        $sql = "SELECT COUNT(*) FROM produit_categorie WHERE id_categorie = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];

        if ($count > 0) {
            return false; // Ne supprimez pas la catégorie si elle contient des produits
        }

        $sql = "DELETE FROM categories WHERE id_categorie = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}