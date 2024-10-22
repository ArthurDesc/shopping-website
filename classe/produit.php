<?php
class Produit {
    private $id;
    private $nom;
    private $prix;
    private $image_url;
    private $marque;

    public function __construct($id, $nom, $prix, $image_url, $marque) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prix = $prix;
        $this->image_url = $image_url;
        $this->marque = $marque;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPrix() { return $this->prix; }
    public function getImageUrl() { return $this->image_url; }
    public function getMarque() { return $this->marque; }
}