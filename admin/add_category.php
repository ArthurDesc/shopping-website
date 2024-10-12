<?php
require_once '../includes/config.php';
require_once '../includes/_db.php';
require_once '../classe/CategoryManager.php';

// Vérifiez si l'utilisateur est admin (à implémenter selon votre système d'authentification)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = $_POST['categoryName'] ?? '';

    if (empty($categoryName)) {
        echo json_encode(['success' => false, 'message' => 'Le nom de la catégorie est requis']);
        exit;
    }

    $categoryManager = new CategoryManager($conn);
    $result = $categoryManager->addCategory($categoryName, ''); // Description vide

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Catégorie ajoutée avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la catégorie']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
