<?php
// Inclure la page de connexion
require_once "../includes/_db.php";

// Vérifier si une session existe
if (!isset($_SESSION)) {
    // Si non, démarrer la session
    session_start();
}

// Créer la session panier si elle n'existe pas
if (!isset($_SESSION['panier'])) {
    // Si la session panier n'existe pas, on la crée et on met un tableau à l'intérieur
    $_SESSION['panier'] = array();
}

// Récupération de l'ID dans le lien
if (isset($_GET['id'])) {
    // Si un ID a été envoyé
    $id = (int)$_GET['id']; // S'assurer que l'ID est un entier pour éviter les injections

    // Vérifier grâce à l'ID si le produit existe dans la base de données
    $stmt = $conn->prepare("SELECT * FROM produits WHERE id_produit = ?");
    $stmt->bind_param("i", $id); // Associer le paramètre (ID du produit)
    $stmt->execute();
    $result = $stmt->get_result();
    $produit = $result->fetch_assoc();

    if (empty($produit)) {
        // Si ce produit n'existe pas
        die("Ce produit n'existe pas");
    }

    // Ajouter le produit dans le panier (le tableau)
    if (isset($_SESSION['panier'][$id])) {
        // Si le produit est déjà dans le panier
        $_SESSION['panier'][$id]++; // Augmenter la quantité
    } else {
        // Sinon, ajouter le produit avec une quantité de 1
        $_SESSION['panier'][$id] = 1;
    }

    // Redirection vers la page index.php
    header("Location:index.php");
}
?>
