<?php
session_start();
require_once "../includes/_db.php";
require_once "../classe/Panier.php";

$panier = new Panier();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produit'])) {
    $id_produit = intval($_POST['id_produit']);
    $panier->ajouter($id_produit);
    
    echo json_encode([
        'success' => true,
        'cartCount' => $panier->getNombreArticles()
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Requête invalide'
    ]);
}

