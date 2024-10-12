<?php
include '../includes/session.php';
include '../includes/_db.php';
require_once '../classe/produit.php';
require_once '../classe/ArticleManager.php';

// Vérifiez si l'utilisateur est admin
if (!$adminManager->isAdmin($id_utilisateur)) {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleManager = new ArticleManager($conn);
    
    $id_article = $_POST['id_article'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];
    $taille = $_POST['taille'];
    $marque = $_POST['marque'];
    $collection = $_POST['collection'];
    $categories = isset($_POST['categories']) ? $_POST['categories'] : [];
    
    $success = $articleManager->updateArticle($id_article, $nom, $description, $prix, $stock, $taille, $marque, $collection, $categories);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
