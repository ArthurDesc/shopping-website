<?php 
// Configurations de session
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60); // 30 jours en secondes
session_set_cookie_params(30 * 24 * 60 * 60);

// Démarrage de la session
session_start();

if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');
}

// Créer la session panier si elle n'existe pas
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = array();
}

// Vérification si l'ID du produit est passé dans l'URL
if (isset($_GET['id_produit'])) {
    // S'assurer que l'ID est un entier pour éviter les failles
    $id = (int)$_GET['id_produit']; 
    $taille = isset($_GET['taille']) ? $_GET['taille'] : null;

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

    $key = $id . ($taille ? '_' . $taille : '');

    if (isset($_SESSION['panier'][$key])) {
        $_SESSION['panier'][$key]['quantite']++;
    } else {
        $_SESSION['panier'][$key] = [
            'id_produit' => $id,
            'quantite' => 1,
            'taille' => $taille
        ];
    }

    // Calculer le nombre total d'articles dans le panier
    $total_items = 0;
    foreach ($_SESSION['panier'] as $item) {
        $total_items += $item['quantite'];
    }

    // Stocker le nombre total d'articles dans la session
    $_SESSION['total_items'] = $total_items;

    // Rediriger vers la page produit.php après l'ajout au panier
    header("Location: produit.php?added=1");
    exit();

    // Après avoir ajouté un article au panier
    error_log("Ajout au panier - ID produit : " . $_POST['id_produit']);
    error_log("Ajout au panier - Quantité : " . $_POST['quantite']);
    error_log("Ajout au panier - Taille : " . $_POST['taille']);
    error_log("Contenu du panier après ajout : " . print_r($_SESSION['panier'], true));
} else {
    echo "<script>alert('Aucun ID de produit fourni.'); window.location.href='produit.php';</script>";
    exit();
}
?>
