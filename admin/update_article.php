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
$field = $data['field'] ?? null;
$new_value = $data['new_value'] ?? null;

if (!$id_produit || !$field || !$new_value) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit();
}

// Vérifier que le champ est valide
$allowed_fields = ['nom', 'description', 'prix', 'marque', 'collection'];
if (!in_array($field, $allowed_fields)) {
    echo json_encode(['success' => false, 'message' => 'Champ non autorisé']);
    exit();
}

// Préparer la requête SQL
$query = "UPDATE produits SET $field = ? WHERE id_produit = ?";
$stmt = $conn->prepare($query);

// Bind les paramètres en fonction du type de champ
if ($field === 'prix') {
    if (!is_numeric($new_value)) {
        echo json_encode(['success' => false, 'message' => 'Valeur de prix invalide']);
        exit();
    }
    $stmt->bind_param("di", $new_value, $id_produit);
} else {
    $stmt->bind_param("si", $new_value, $id_produit);
}

// Exécuter la requête
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
}
