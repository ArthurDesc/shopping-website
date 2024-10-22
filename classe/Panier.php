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
        $key = $id_produit . ($taille ? '_' . $taille : '');
        if (isset($this->items[$key])) {
            $this->items[$key] += $quantite;
        } else {
            $this->items[$key] = $quantite;
        }
        $this->sauvegarder();
        return true;
    }

    public function retirer($id_produit) {
        unset($this->items[$id_produit]);
        $this->sauvegarder();
    }

    public function getContenu() {
        return $this->items;
    }

    public function getNombreArticles() {
        return array_sum($this->items);
    }

    private function sauvegarder() {
        $_SESSION['panier'] = $this->items;
    }

    // Nouvelles méthodes

    public function augmenterQuantite($id_produit) {
        if (isset($this->items[$id_produit])) {
            $this->items[$id_produit]++;
            $this->sauvegarder();
        }
    }

    public function diminuerQuantite($id_produit) {
        if (isset($this->items[$id_produit])) {
            if ($this->items[$id_produit] > 1) {
                $this->items[$id_produit]--;
            } else {
                unset($this->items[$id_produit]);
            }
            $this->sauvegarder();
        }
    }

    public function mettreAJourQuantite($id_produit, $quantite) {
        if ($quantite > 0) {
            $this->items[$id_produit] = $quantite;
        } else {
            unset($this->items[$id_produit]);
        }
        $this->sauvegarder();
    }

    public function retirerProduit($id_produit) {
        $this->retirer($id_produit); // Utilise la méthode existante
    }
}
