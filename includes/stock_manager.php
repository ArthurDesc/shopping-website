<?php
class StockManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Vérifie et met à jour les stocks pour une commande
     */
    public function processOrderStock($panier, $id_commande) {
        try {
            foreach ($panier as $id_produit => $quantity) {
                // Vérifier le stock disponible
                if (!$this->checkStock($id_produit, $quantity)) {
                    throw new Exception("Stock insuffisant pour le produit ID: " . $id_produit);
                }

                // Mettre à jour le stock
                $this->updateStock($id_produit, $quantity);

                // Insérer la ligne de commande
                $this->createOrderLine($id_commande, $id_produit, $quantity);
            }
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur lors du traitement des stocks: " . $e->getMessage());
        }
    }

    /**
     * Vérifie si le stock est suffisant
     */
    private function checkStock($id_produit, $quantity) {
        $stmt = $this->conn->prepare("SELECT stock FROM produits WHERE id_produit = ?");
        $stmt->bind_param("i", $id_produit);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        return $product && $product['stock'] >= $quantity;
    }

    /**
     * Met à jour le stock d'un produit
     */
    private function updateStock($id_produit, $quantity) {
        $stmt = $this->conn->prepare("UPDATE produits SET stock = stock - ? WHERE id_produit = ?");
        $stmt->bind_param("ii", $quantity, $id_produit);
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de la mise à jour du stock");
        }
    }

    /**
     * Crée une ligne de commande
     */
    private function createOrderLine($id_commande, $id_produit, $quantity) {
        $stmt = $this->conn->prepare("INSERT INTO lignes_commandes (id_commande, id_produit, quantite, prix_unitaire) 
                                    VALUES (?, ?, ?, (SELECT prix FROM produits WHERE id_produit = ?))");
        $stmt->bind_param("iiii", $id_commande, $id_produit, $quantity, $id_produit);
        if (!$stmt->execute()) {
            throw new Exception("Erreur lors de la création de la ligne de commande");
        }
    }
}
