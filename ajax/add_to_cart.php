<?php
session_start();
require_once "../includes/_db.php";
require_once "../classe/Panier.php";

$panier = new Panier();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produit'])) {
    $id_produit = intval($_POST['id_produit']);
    $quantite = isset($_POST['quantite']) ? intval($_POST['quantite']) : 1;
    $taille = isset($_POST['taille']) ? $_POST['taille'] : null;

    // Vérifier si le produit existe et s'il y a assez de stock
    $stmt = $conn->prepare("SELECT stock FROM produits WHERE id_produit = ?");
    $stmt->bind_param("i", $id_produit);
    $stmt->execute();
    $result = $stmt->get_result();
    $produit = $result->fetch_assoc();

    if ($produit && $produit['stock'] >= $quantite) {
        $panier->ajouter($id_produit, $quantite, $taille);
        $cartInfo = $panier->getCartInfo();
        
        echo json_encode([
            'success' => true,
            'message' => 'Produit ajouté au panier',
            'cartCount' => $cartInfo['totalItems']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Stock insuffisant'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Requête invalide'
    ]);
}
