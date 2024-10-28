<?php
session_start();
require_once '../includes/_db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id_avis = $data['id_avis'] ?? 0;
$note = $data['note'] ?? 0;
$commentaire = $data['commentaire'] ?? '';
$id_utilisateur = $_SESSION['id_utilisateur'];

try {
    if (strlen($commentaire) < 10) {
        throw new Exception("Le commentaire doit faire au moins 10 caractères");
    }

    if ($note < 1 || $note > 5) {
        throw new Exception("La note doit être comprise entre 1 et 5");
    }

    $sql = "UPDATE avis SET note = ?, commentaire = ? WHERE id_avis = ? AND id_utilisateur = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $note, $commentaire, $id_avis, $id_utilisateur);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Erreur lors de la modification");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
