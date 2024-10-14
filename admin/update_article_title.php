<?php
include '../includes/session.php';
include '../includes/_db.php';
require_once '../classe/AdminManager.php';

$adminManager = new AdminManager($conn);

// Vérifiez si l'utilisateur est admin
if (!$adminManager->isAdmin(get_id_utilisateur())) {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé']);
    exit();
}

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

$id_produit = $data['id_produit'] ?? null;
$new_title = $data['new_title'] ?? null;

if (!$id_produit || !$new_title) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

// Mettre à jour le titre dans la base de données
$query = "UPDATE produits SET nom = ? WHERE id_produit = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $new_title, $id_produit);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
}

