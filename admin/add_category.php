<?php
header('Content-Type: application/json');

try {
    error_log("Début de add_category.php");
    require_once '../includes/_db.php';
    require_once '../classe/CategoryManager.php';

    $input = file_get_contents('php://input');
    error_log("Données reçues: " . $input);
    $data = json_decode($input, true);

    if (!isset($data['nom'])) {
        throw new Exception('Nom de catégorie manquant');
    }

    error_log("Tentative d'ajout de la catégorie: " . $data['nom']);
    $categoryManager = new CategoryManager($conn);
    $result = $categoryManager->addCategory($data['nom'], $data['parent_id'] ?? null);

    if ($result) {
        $response = ['success' => true, 'message' => 'Catégorie ajoutée avec succès'];
    } else {
        $error = $categoryManager->getLastError(); // Assurez-vous d'avoir une méthode getLastError dans CategoryManager
        $response = ['success' => false, 'message' => 'Erreur lors de l\'ajout de la catégorie: ' . $error];
    }
    error_log("Réponse: " . json_encode($response));
    echo json_encode($response);
} catch (Exception $e) {
    error_log("Exception dans add_category.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue : ' . $e->getMessage()]);
}
error_log("Fin de add_category.php");
