<?php

class Filtre {
    private $categories = [];
    private $marques = [];
    private $collections = [];
    private $prixMin;
    private $prixMax;

    public function __construct() {
        // Initialisation si nécessaire
    }

    public function setCategories($categories) {
        if (!is_array($categories)) {
            $categories = [$categories];
        }
        
        // Convertir les noms de catégories en IDs si nécessaire
        $this->categories = array_map(function($category) {
            // Si c'est déjà un ID, le retourner tel quel
            if (is_numeric($category)) {
                return $category;
            }
            
            // Sinon, chercher l'ID correspondant au nom
            global $conn;
            $stmt = $conn->prepare("SELECT id_categorie FROM categories WHERE nom = ?");
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return $row['id_categorie'];
            }
            
            return $category;
        }, $categories);
    }

    public function setMarques(array $marques) {
        $this->marques = $marques;
    }

    public function setCollections(array $collections) {
        $this->collections = $collections;
    }

    public function setPrixRange($min, $max) {
        $this->prixMin = floatval($min);
        $this->prixMax = floatval($max);
    }

    public function hasCategory($categoryId) {
        return in_array(intval($categoryId), $this->categories);
    }

    public function hasMarque($marque) {
        return in_array($marque, $this->marques);
    }

    public function hasCollection($collection) {
        return in_array($collection, $this->collections);
    }

    public function getCategories() {
        return $this->categories;
    }

    public function getMarques() {
        return $this->marques;
    }

    public function getCollections() {
        return $this->collections;
    }

    public function getPrixMin() {
        return $this->prixMin;
    }

    public function getPrixMax() {
        return $this->prixMax;
    }

    public function appliquerFiltres(array $produits) {
        return array_filter($produits, function($produit) {
            // Filtre par catégorie
            if (!empty($this->categories) && !$this->estDansCategories($produit)) {
                return false;
            }

            // Filtre par marque
            if (!empty($this->marques) && !in_array($produit->getMarque(), $this->marques)) {
                return false;
            }

            // Filtre par collection
            if (!empty($this->collections) && !in_array($produit->getCollection(), $this->collections)) {
                return false;
            }

            // Filtre par prix
            if (isset($this->prixMin) && $produit->getPrix() < $this->prixMin) {
                return false;
            }
            if (isset($this->prixMax) && $produit->getPrix() > $this->prixMax) {
                return false;
            }

            return true;
        });
    }

    private function estDansCategories($produit) {
        // Supposons que $produit a une méthode getCategories() qui retourne un tableau d'IDs de catégories
        $produitsCategories = $produit->getCategories();
        return !empty(array_intersect($this->categories, $produitsCategories));
    }

    public function getRequeteSQL() {
        $conditions = [];
        $params = [];
        
        if (!empty($this->categories)) {
            $placeholders = str_repeat('?,', count($this->categories) - 1) . '?';
            $conditions[] = "pc.id_categorie IN ($placeholders)";
            $params = array_merge($params, $this->categories);
        }

        if (!empty($this->collections)) {
            $conditions[] = "p.collection = ?";
            $params[] = $this->collections[0];
        }

        $sql = "SELECT DISTINCT p.*, GROUP_CONCAT(c.nom) as categories 
                FROM produits p 
                LEFT JOIN produit_categorie pc ON p.id_produit = pc.id_produit 
                LEFT JOIN categories c ON pc.id_categorie = c.id_categorie";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $sql .= " GROUP BY p.id_produit";

        return [
            'sql' => $sql,
            'params' => $params
        ];
    }

    public function resetFiltres() {
        $this->categories = [];
        $this->marques = [];
        $this->collections = [];
        $this->prixMin = null;
        $this->prixMax = null;
    }
}
