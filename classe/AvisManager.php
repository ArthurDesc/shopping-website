<?php

class AvisManager {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAvisForProduct($id_produit) {
        try {
            $sql = "SELECT a.*, u.nom as nom_utilisateur 
                    FROM avis a 
                    LEFT JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur 
                    WHERE a.id_produit = ? 
                    ORDER BY a.date_creation DESC";
            
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête");
            }
            
            $stmt->bind_param("i", $id_produit);
            if (!$stmt->execute()) {
                throw new Exception("Erreur d'exécution de la requête");
            }
            
            $result = $stmt->get_result();
            $avis = [];
            
            while ($row = $result->fetch_assoc()) {
                $avis[] = new Avis(
                    $row['id_avis'],
                    $row['id_produit'],
                    $row['id_utilisateur'],
                    $row['note'],
                    $row['commentaire'],
                    $row['date_creation'],
                    $row['nom_utilisateur'] ?? 'Anonyme'
                );
            }
            
            return $avis;
            
        } catch (Exception $e) {
            error_log("Erreur dans AvisManager::getAvisForProduct: " . $e->getMessage());
            throw $e;
        }
    }

    public function addAvis($id_produit, $id_utilisateur, $note, $commentaire) {
        $sql = "INSERT INTO avis (id_produit, id_utilisateur, note, commentaire) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
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
        $stmt = $this->db->prepare($sql);
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
