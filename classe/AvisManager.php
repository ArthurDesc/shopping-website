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
}
