<?php
header('Content-Type: application/json');
// Fichier pour charger les articles depuis la base de données et les afficher sur backoffice 
require_once '../classe/ArticleManager.php';
include '../includes/_db.php';

// Définir le chemin de base pour les images
define('BASE_URL', '/shopping-website/'); // Ajustez ceci selon votre configuration
define('IMAGE_PATH', BASE_URL . 'assets/images/produits/');

// Créer une connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die(json_encode(["error" => "La connexion a échoué : " . $conn->connect_error]));
}

// Instancier ArticleManager
$articleManager = new ArticleManager($conn);

// Récupérer tous les articles
$articles = $articleManager->getAllArticles();

// Préparer les données pour le JSON
$articlesData = array_map(function($article) {
    return [
        'id_produit' => $article['id_produit'] ?? 0,
        'nom' => $article['nom'] ?? '',
        'marque' => $article['marque'] ?? '',
        'prix' => $article['prix'] ?? '',
        'categories' => $article['categories'] ?? '',
        'image_url' => IMAGE_PATH . ($article['image_url'] ?? 'default.jpg')
    ];
}, $articles);

// Renvoyer les données en JSON
echo json_encode($articlesData);

// Fermer la connexion à la base de données
$conn->close();
