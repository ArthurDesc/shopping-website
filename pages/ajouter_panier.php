<?php 
// Configurations de session
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60); // 30 jours en secondes
session_set_cookie_params(30 * 24 * 60 * 60);

// Démarrage de la session
session_start();

if (!defined('BASE_URL')) {
    define('BASE_URL', '/php-vanilla/shopping-website/');  // Chemin depuis la racine web
  }

// Initialize the cart session as an array if it doesn't exist
if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Vérification si l'ID du produit est passé dans l'URL
if (isset($_GET['id_produit'])) {
    $id = (int)$_GET['id_produit']; 
    $taille = isset($_GET['taille']) ? $_GET['taille'] : null;
    $quantite_demandee = isset($_POST['quantite']) ? (int)$_POST['quantite'] : 1;

    include_once "../includes/_db.php";
    require_once "../classe/Panier.php";
    
    $panier = new Panier();
    
    // Vérification du stock
    $stmt = $conn->prepare("SELECT stock FROM produits WHERE id_produit = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produit = $result->fetch_assoc();
    
    if ($produit && $quantite_demandee <= $produit['stock']) {
        $panier->ajouter($id, $quantite_demandee, $taille);
        header("Location: produit.php?added=1");
        exit();
    } else {
        echo "<script>alert('Stock insuffisant'); window.location.href='produit.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Aucun ID de produit fourni.'); window.location.href='produit.php';</script>";
    exit();
}
