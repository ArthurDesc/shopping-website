<?php
class Produit {
    private $id;
    private $nom;
    private $prix;
    private $image_url;
    private $marque;
    private $description;
    private $stock;
    private $tailles_disponibles;
    private $categories;
    private $collection;

    public function __construct($id, $nom, $prix, $image_url, $marque, $description = '', $stock = 0, $tailles_disponibles = '', $categories = [], $collection = '') {
        $this->id = $id;
        $this->nom = $nom;
        $this->prix = $prix;
        $this->image_url = $image_url;
        $this->marque = $marque;
        $this->description = $description;
        $this->stock = $stock;
        $this->tailles_disponibles = $tailles_disponibles;
        $this->setCategories($categories);
        $this->collection = $collection;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrix() { return $this->prix; }
    public function getImageUrl() { return $this->image_url; }
    public function getMarque() { return $this->marque; }
    public function getDescription() { return $this->description; }
    public function getStock() { return $this->stock; }
    public function getTaillesDisponibles() { return $this->tailles_disponibles; }
    public function getCategories() { return $this->categories; }

    public function setPrix($prix) {
        $this->prix = $prix;
    }

    public function setStock($stock) {
        $this->stock = $stock;
    }

    public function setCategories($categories) {
        $this->categories = is_array($categories) ? $categories : explode(',', $categories);
    }

    public function estDisponible() {
        return $this->stock > 0;
    }

    public function getTaillesDisponiblesArray() {
        return explode(',', $this->tailles_disponibles);
    }

    public function formatPrix() {
        return number_format($this->prix, 2, ',', ' ') . ' €';
    }

    public function hasCategory($categoryId) {
        return in_array($categoryId, $this->categories);
    }

    public function getCollection() {
        return $this->collection;
    }
}
