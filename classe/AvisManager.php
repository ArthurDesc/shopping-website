<?php

class AvisManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAvisForProduct($id_produit) {
        $avis = [];
        $sql = "SELECT a.*, u.nom as nom_utilisateur 
                FROM avis a 
                JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur 
                WHERE a.id_produit = ? 
                ORDER BY a.date_creation DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_produit);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $avis[] = new Avis(
                $row['id_avis'],
                $row['id_produit'],
                $row['id_utilisateur'],
                $row['note'],
                $row['commentaire'],
                $row['date_creation'],
                $row['nom_utilisateur']
            );
        }

        return $avis;
    }

    public function addAvis($id_produit, $id_utilisateur, $note, $commentaire) {
        $sql = "INSERT INTO avis (id_produit, id_utilisateur, note, commentaire) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiis", $id_produit, $id_utilisateur, $note, $commentaire);
        
        if ($stmt->execute()) {
            $id_avis = $stmt->insert_id;
            return $this->getAvisById($id_avis);
        }
        return false;
    }

    private function getAvisById($id_avis) {
        $sql = "SELECT a.*, u.nom as nom_utilisateur 
                FROM avis a 
                JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur 
                WHERE a.id_avis = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id_avis);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return new Avis(
                $row['id_avis'],
                $row['id_produit'],
                $row['id_utilisateur'],
                $row['note'],
                $row['commentaire'],
                $row['date_creation'],
                $row['nom_utilisateur']
            );
        }
        return null;
    }
}
