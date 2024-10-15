<?php
require_once '../includes/_db.php';
require_once '../classe/CategoryManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $categoryId = $data['id_categorie'] ?? null;

    if ($categoryId) {
        $categoryManager = new CategoryManager($conn);
        $result = $categoryManager->deleteCategory($categoryId);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'La catégorie ne peut pas être supprimée car elle est associée à des produits.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID de catégorie non fourni.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
}

