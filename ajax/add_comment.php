<?php
session_start();
require_once '../includes/_db.php';
require_once '../classe/Avis.php';
require_once '../classe/AvisManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifiez si toutes les données nécessaires sont présentes
    if (!isset($_POST['id_produit']) || !isset($_POST['note']) || !isset($_POST['commentaire'])) {
        echo json_encode(['success' => false, 'message' => 'Données manquantes']);
        exit;
    }

    $id_produit = intval($_POST['id_produit']);
    $id_utilisateur = $_SESSION['id_utilisateur'] ?? null;
    $note = intval($_POST['note']);
    $commentaire = $_POST['commentaire'];

    // Vérifiez si l'utilisateur est connecté
    if (!$id_utilisateur) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
        exit;
    }

    $avisManager = new AvisManager($conn);
    $result = $avisManager->addAvis($id_produit, $id_utilisateur, $note, $commentaire);

    if ($result) {
        echo json_encode(['success' => true, 'comment' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout de l\'avis']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
