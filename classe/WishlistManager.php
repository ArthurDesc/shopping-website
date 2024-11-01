<?php

class WishlistManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addToWishlist($id_utilisateur, $id_produit) {
        $sql = "INSERT INTO wishlist (id_utilisateur, id_produit) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $id_utilisateur, $id_produit);
        return $stmt->execute();
    }

    public function removeFromWishlist($id_utilisateur, $id_produit) {
        $sql = "DELETE FROM wishlist WHERE id_utilisateur = ? AND id_produit = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $id_utilisateur, $id_produit);
        return $stmt->execute();
    }

    public function getWishlist($id_utilisateur) {
        $sql = "SELECT p.* FROM produits p 
                JOIN wishlist w ON p.id_produit = w.id_produit 
                WHERE w.id_utilisateur = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_utilisateur);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function isInWishlist($id_utilisateur, $id_produit) {
        $sql = "SELECT 1 FROM wishlist WHERE id_utilisateur = ? AND id_produit = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $id_utilisateur, $id_produit);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
