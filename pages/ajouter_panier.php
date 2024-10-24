<?php 
// Configurations de session
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60); // 30 jours en secondes
session_set_cookie_params(30 * 24 * 60 * 60);

// Démarrage de la session
session_start();

if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');
}

// Initialize the cart session as an array if it doesn't exist
if (!isset($_SESSION['panier']) || !is_array($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Vérification si l'ID du produit est passé dans l'URL
if (isset($_GET['id_produit'])) {
    // S'assurer que l'ID est un entier pour éviter les failles
    $id = (int)$_GET['id_produit']; 
    $taille = isset($_GET['taille']) ? $_GET['taille'] : null;

    // Include the database connection file
    include_once "../includes/_db.php"; // Ensure this file contains the database connection logic

    // Check if the connection is established
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

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

    // Create a unique key for the product based on ID and size
    $key = $id . ($taille ? '_' . $taille : '');

    // Check if the product is already in the cart
    if (isset($_SESSION['panier'][$key])) {
        // Ensure the product entry is an array before incrementing
        if (is_array($_SESSION['panier'][$key])) {
            $_SESSION['panier'][$key]['quantite']++;
        } else {
            // Reinitialize if it was incorrectly set as a scalar
            $_SESSION['panier'][$key] = [
                'id_produit' => $id,
                'quantite' => 1,
                'taille' => $taille
            ];
        }
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

// Example of using the connection
$stmt = $conn->prepare("INSERT INTO your_table (column1, column2) VALUES (?, ?)");
if ($stmt) {
    // Bind parameters and execute the statement
    $stmt->bind_param("ss", $value1, $value2);
    $stmt->execute();
    $stmt->close();
} else {
    die("Prepare failed: " . $conn->error);
}
