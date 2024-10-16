<?php

class Produit {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function obtenirTousLesProduits() {
        $req = mysqli_query($this->conn, "SELECT p.*, GROUP_CONCAT(c.id_categorie) as categories 
                                          FROM produits p 
                                          LEFT JOIN produit_categorie pc ON p.id_produit = pc.id_produit 
                                          LEFT JOIN categories c ON pc.id_categorie = c.id_categorie 
                                          GROUP BY p.id_produit, p.nom, p.image_url, p.description, p.prix, p.stock, p.taille, p.marque, p.date_ajout, p.collection");
        return $req;
    }

    public function obtenirCategories() {
        $categories_query = mysqli_query($this->conn, "SELECT * FROM categories");
        return mysqli_fetch_all($categories_query, MYSQLI_ASSOC);
    }

    public function obtenirMarques() {
        $marques_query = mysqli_query($this->conn, "SELECT DISTINCT marque FROM produits WHERE marque IS NOT NULL AND marque != ''");
        return mysqli_fetch_all($marques_query, MYSQLI_ASSOC);
    }

    public function obtenirCollections() {
        $collections_query = mysqli_query($this->conn, "SELECT DISTINCT collection FROM produits WHERE collection IS NOT NULL AND collection != ''");
        return mysqli_fetch_all($collections_query, MYSQLI_ASSOC);
    }
}

