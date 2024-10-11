<?php
// Inclure la connexion à la base de données
require_once "../includes/_db.php";

// Démarrer la session si elle n'existe pas
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Créer la session panier si elle n'existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = array();
}

// Vérification si l'ID du produit est passé dans l'URL
if (isset($_GET['id_produit'])) {
    // S'assurer que l'ID est un entier pour éviter les failles
    $id = (int)$_GET['id_produit']; 

    // Préparer la requête SQL pour vérifier si le produit existe
    $stmt = $conn->prepare("SELECT * FROM produits WHERE id_produit = ?");
    if (!$stmt) {
        die('Erreur de requête : ' . $conn->error); // Gestion d'erreur de la requête
    }
    $stmt->bind_param("i", $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $produit = $result->fetch_assoc();

    // Si le produit n'existe pas, afficher un message et rediriger
    if (empty($produit)) {
        echo "<script>alert('Ce produit n\'existe pas'); window.location.href='produit.php';</script>";
        exit;
    }

    // Ajouter ou mettre à jour la quantité du produit dans le panier
    if (isset($_SESSION['panier'][$id])) {
        // Si le produit est déjà dans le panier, augmenter la quantité
        $_SESSION['panier'][$id]++;
    } else {
        // Sinon, ajouter le produit avec une quantité de 1
        $_SESSION['panier'][$id] = 1;
    }

    // Rediriger vers la page produit.php après l'ajout au panier
    header("Location: produit.php");
    exit();
} else {
    echo "<script>alert('Aucun ID de produit fourni.'); window.location.href='produit.php';</script>";
    exit();
}
?>
