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
}
