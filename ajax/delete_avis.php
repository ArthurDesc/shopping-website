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
$id_utilisateur = $_SESSION['id_utilisateur'];

try {
    $sql = "DELETE FROM avis WHERE id_avis = ? AND id_utilisateur = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_avis, $id_utilisateur);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Erreur lors de la suppression");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

