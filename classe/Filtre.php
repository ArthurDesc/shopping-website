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

    public function setMarques($marques) {
        if (!is_array($marques)) {
            $marques = [$marques];
        }
        $this->marques = array_map('trim', $marques);
    }

    public function setCollections($collections) {
        if (!is_array($collections)) {
            $collections = [$collections];
        }
        $this->collections = array_map('strtolower', $collections);
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



  
    public function resetFiltres() {
        $this->categories = [];
        $this->marques = [];
        $this->collections = [];
        $this->prixMin = null;
        $this->prixMax = null;
    }
}
