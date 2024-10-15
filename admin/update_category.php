<?php
// admin/update_category.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once '../includes/_db.php';
require_once '../classe/CategoryManager.php';
require_once '../classe/AdminManager.php';

try {
    // Vérifier si l'utilisateur est connecté et a les droits d'administrateur
    session_start();
    if (!isset($_SESSION['id_utilisateur'])) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
        exit;
    }

    $adminManager = new AdminManager($conn);
    if (!$adminManager->isAdmin($_SESSION['id_utilisateur'])) {
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
        $error = $categoryManager->getLastError();
        error_log("Erreur dans update_category.php: " . $error);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour de la catégorie: ' . $error]);
    }
} catch (Exception $e) {
    error_log("Exception dans update_category.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    exit;
}
