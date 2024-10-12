<?php
// delete_article.php
header('Content-Type: application/json');

// Désactiver l'affichage des erreurs PHP en production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Démarrer la mise en tampon de sortie
ob_start();

require_once '../includes/_db.php';
require_once '../classe/ArticleManager.php';

try {
    // Vérifier si la connexion est établie
    if (!$conn) {
        throw new Exception("La connexion à la base de données n'a pas pu être établie.");
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $articleId = $data['id_article'] ?? null;

    if ($articleId) {
        $articleManager = new ArticleManager($conn);
        $result = $articleManager->deleteArticle($articleId);
        
        if ($result) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => 'Erreur lors de la suppression'];
        }
    } else {
        $response = ['success' => false, 'message' => 'ID d\'article non fourni'];
    }
} catch (Exception $e) {
    // Log l'erreur côté serveur
    error_log("Erreur lors de la suppression de l'article: " . $e->getMessage());
    
    $response = ['success' => false, 'message' => 'Une erreur inattendue s\'est produite'];
}

// Nettoyer la sortie tampon
ob_clean();

// Envoyer la réponse JSON
echo json_encode($response);

// Fermer la connexion
if ($conn) {
    mysqli_close($conn);
}