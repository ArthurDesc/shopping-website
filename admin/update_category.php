<?php
// admin/update_category.php

require_once '../includes/_db.php';
require_once '../classe/CategoryManager.php';
require_once '../classe/AdminManager.php';

header('Content-Type: application/json');

try {
    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    session_start();
    if (!isset($_SESSION['id_utilisateur'])) {
        throw new Exception('Utilisateur non connecté');
    }

    $adminManager = new AdminManager($conn);
    if (!$adminManager->isAdmin($_SESSION['user_id'])) {
        throw new Exception('Accès non autorisé');
    }

    // Vérifier si la requête est de type POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }

    // Récupérer les données JSON du corps de la requête
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id_categorie']) || !isset($data['nom'])) {
        throw new Exception('Données manquantes');
    }

    $categoryId = intval($data['id_categorie']);
    $newName = trim($data['nom']);

    // Vérifier si le nouveau nom n'est pas vide
    if (empty($newName)) {
        throw new Exception('Le nom de la catégorie ne peut pas être vide');
    }

    // Créer une instance de CategoryManager
    $categoryManager = new CategoryManager($conn);

    // Récupérer la catégorie existante
    $existingCategory = $categoryManager->getCategory($categoryId);

    if (!$existingCategory) {
        throw new Exception('Catégorie non trouvée');
    }

    // Mettre à jour la catégorie
    $result = $categoryManager->updateCategory($categoryId, $newName, $existingCategory['description']);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Catégorie mise à jour avec succès']);
    } else {
        $error = $categoryManager->getLastError(); // Ajoutez cette méthode à votre CategoryManager
        throw new Exception('Erreur lors de la mise à jour de la catégorie: ' . $error);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
