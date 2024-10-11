<?php
// Inclure la page de connexion
require_once "../includes/_db.php";

// Démarrer la session si elle n'existe pas
if (session_status() == PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400, // 1 jour de durée de vie pour le cookie de session
        'cookie_secure' => true,    // Cookies sécurisés (HTTPS)
        'cookie_httponly' => true,  // Empêche l'accès des scripts JS au cookie de session
    ]);
}

// Créer la session panier si elle n'existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = array();
}

// Récupération de l'ID dans le lien
if (isset($_GET['id_produit'])) {
    // S'assurer que l'ID est un entier
    $id = (int)$_GET['id_produit']; 

    // Vérifier si le produit existe dans la base de données
    $stmt = $conn->prepare("SELECT * FROM produits WHERE id_produit = ?");
    $stmt->bind_param("i", $id); 
    $stmt->execute();
    $result = $stmt->get_result();
    $produit = $result->fetch_assoc();

    if (empty($produit)) {
        // Si le produit n'existe pas, message d'erreur et redirection
        echo "<script>alert('Ce produit n\'existe pas'); window.location.href='produit.php';</script>";
        exit;
    }

    // Ajouter le produit dans le panier
    if (isset($_SESSION['panier'][$id])) {
        // Si le produit est déjà dans le panier, augmenter la quantité
        $_SESSION['panier'][$id]++;
    } else {
        // Sinon, ajouter le produit avec une quantité de 1
        $_SESSION['panier'][$id] = 1;
    }

    // Redirection vers la page produit.php
    header("Location: produit.php");
    exit();
}
?>
