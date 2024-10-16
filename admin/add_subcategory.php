<?php
header('Content-Type: application/json');
require_once '../includes/_db.php';
require_once '../classe/CategoryManager.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['parent_id']) && isset($data['nom'])) {
    $categoryManager = new CategoryManager($conn);
    $result = $categoryManager->addCategory($data['nom'], $data['parent_id']);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Sous-catégorie ajoutée avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de la sous-catégorie']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
}

