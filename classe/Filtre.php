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
            $categories = explode(',', $categories);
        }
        $this->categories = array_filter($categories);
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
        $sql = "SELECT p.*, GROUP_CONCAT(c.nom) as categories 
                FROM produits p 
                LEFT JOIN produit_categorie pc ON p.id_produit = pc.id_produit 
                LEFT JOIN categories c ON pc.id_categorie = c.id_categorie 
                WHERE p.stock > 0";
        
        $params = [];
        $conditions = [];

        // Ajouter les conditions pour les catégories
        if (!empty($this->categories)) {
            $placeholders = str_repeat('?,', count($this->categories) - 1) . '?';
            $conditions[] = "c.id_categorie IN ($placeholders)";
            $params = array_merge($params, $this->categories);
        }

        // Ajouter les conditions pour les collections
        if (!empty($this->collections)) {
            $placeholders = str_repeat('?,', count($this->collections) - 1) . '?';
            $conditions[] = "p.collection IN ($placeholders)";
            $params = array_merge($params, $this->collections);
        }

        // Ajouter les conditions pour les marques
        if (!empty($this->marques)) {
            $placeholders = str_repeat('?,', count($this->marques) - 1) . '?';
            $conditions[] = "p.marque IN ($placeholders)";
            $params = array_merge($params, $this->marques);
        }

        // Ajouter toutes les conditions à la requête
        if (!empty($conditions)) {
            $sql .= " AND " . implode(" AND ", $conditions);
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
