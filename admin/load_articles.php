
<?php
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
    die("La connexion a échoué : " . $conn->connect_error);
}

// Instancier ArticleManager
$articleManager = new ArticleManager($conn);

// Récupérer tous les articles
$articles = $articleManager->getAllArticles();

// Générer le HTML pour l'affichage des articles
echo '<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">';
foreach ($articles as $article) {
    $imagePath = IMAGE_PATH . ($article['image_url'] ?? 'default.jpg');
    echo '<div class="bg-white rounded-lg shadow-md overflow-hidden">';
    echo '<img src="' . htmlspecialchars($imagePath) . '" alt="Image de ' . htmlspecialchars($article['nom'] ?? '') . '" class="w-full h-48 object-cover">';
    echo '<div class="p-4">';
    echo '<h3 class="text-lg font-semibold mb-2">' . htmlspecialchars($article['nom'] ?? '') . '</h3>';
    echo '<p class="text-sm text-gray-600 mb-2">' . htmlspecialchars($article['marque'] ?? '') . '</p>';
    echo '<p class="text-sm text-gray-600 mb-2">Prix : ' . htmlspecialchars($article['prix'] ?? '') . ' €</p>';
    echo '<p class="text-sm text-gray-600 mb-2">Catégories : ' . htmlspecialchars($article['categories'] ?? '') . '</p>';
    echo '<div class="flex justify-between items-center">';
    echo '<button class="text-red-500 hover:text-red-700" onclick="deleteArticle(' . ($article['id_produit'] ?? 0) . ')">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
    echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />';
    echo '</svg>';
    echo '</button>';
    echo '<button class="text-blue-500 hover:text-blue-700" onclick="editArticle(' . ($article['id_produit'] ?? 0) . ')">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
    echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />';
    echo '</svg>';
    echo '</button>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
echo '</div>';

// Fermer la connexion à la base de données
$conn->close();