<?php
session_start();
require_once "../includes/_db.php";
require_once "../classe/Panier.php";

$panier = new Panier();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produit'], $_POST['taille'], $_POST['quantite'])) {
    $id_produit = intval($_POST['id_produit']);
    $taille = $_POST['taille'];
    $quantite = intval($_POST['quantite']);

    if ($panier->ajouter($id_produit, $quantite, $taille)) {
        echo json_encode([
            'success' => true,
            'message' => 'Produit ajouté au panier',
            'cartCount' => $panier->getNombreArticles()
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de l\'ajout au panier'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Données invalides'
    ]);
}
