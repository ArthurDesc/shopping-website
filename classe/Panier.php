<?php
class Panier {
    private $items = [];

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->items = $_SESSION['panier'] ?? [];
    }

    public function ajouter($id_produit, $quantite = 1, $taille = null) {
        $key = $this->genererCleProduit($id_produit, $taille);
        if (isset($this->items[$key])) {
            $this->items[$key] += $quantite;
        } else {
            $this->items[$key] = $quantite;
        }
        $this->sauvegarder();
        return true;
    }

    public function retirer($id_produit, $taille = null) {
        $key = $this->genererCleProduit($id_produit, $taille);
        unset($this->items[$key]);
        $this->sauvegarder();
    }

    public function getContenu() {
        return $this->items;
    }

    public function getNombreArticles() {
        return array_sum($this->items);
    }

    public function getTotal() {
        global $conn;
        $total = 0;
        foreach ($this->items as $key => $quantite) {
            list($id_produit, $taille) = explode('_', $key . '_');
            $sql = "SELECT prix FROM produits WHERE id_produit = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id_produit);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($produit = $result->fetch_assoc()) {
                $total += $produit['prix'] * $quantite;
            }
        }
        return $total;
    }

    private function sauvegarder() {
        $_SESSION['panier'] = $this->items;
    }

    private function genererCleProduit($id_produit, $taille) {
        return $id_produit . ($taille ? '_' . $taille : '');
    }

    public function augmenterQuantite($id_produit, $taille = null) {
        $key = $this->genererCleProduit($id_produit, $taille);
        if (isset($this->items[$key])) {
            $this->items[$key]++;
            $this->sauvegarder();
        }
    }

    public function diminuerQuantite($id_produit, $taille = null) {
        $key = $this->genererCleProduit($id_produit, $taille);
        if (isset($this->items[$key]) && $this->items[$key] > 1) {
            $this->items[$key]--;
        } elseif (isset($this->items[$key])) {
            unset($this->items[$key]);
        }
        $this->sauvegarder();
    }

    public function mettreAJourQuantite($id_produit, $quantite, $taille = null) {
        $key = $this->genererCleProduit($id_produit, $taille);
        if ($quantite > 0) {
            $this->items[$key] = $quantite;
        } else {
            unset($this->items[$key]);
        }
        $this->sauvegarder();
    }

    public function retirerProduit($id_produit, $taille = null) {
        $this->retirer($id_produit, $taille);
    }

    public function produitExiste($id_produit, $taille = null) {
        $key = $this->genererCleProduit($id_produit, $taille);
        return isset($this->items[$key]);
    }

    public function getCartInfo() {
        return [
            'items' => $this->getContenu(),
            'totalItems' => $this->getNombreArticles(),
            'totalPrice' => number_format($this->getTotal(), 2, '.', '')
        ];
    }
}
