<?php
// ajax/get_avis.php

// Inclure les fichiers nécessaires
require_once '../includes/session.php';
require_once '../includes/_db.php';
require_once '../classe/AvisManager.php';
require_once '../classe/Avis.php';

// Vérifier si l'ID du produit est fourni
if (!isset($_GET['id_produit']) || !is_numeric($_GET['id_produit'])) {
    echo json_encode(['error' => 'ID de produit non valide']);
    exit;
}

$id_produit = intval($_GET['id_produit']);

// Créer une instance de AvisManager
$avisManager = new AvisManager($conn);

// Récupérer les avis pour ce produit
$avis_produit = $avisManager->getAvisForProduct($id_produit);

// Préparer les données pour le JSON
$avis_data = [];
foreach ($avis_produit as $avis) {
    $avis_data[] = [
        'id' => $avis->getIdAvis(),
        'id_produit' => $avis->getIdProduit(),
        'id_utilisateur' => $avis->getIdUtilisateur(),
        'nom_utilisateur' => $avis->getNomUtilisateur(),
        'note' => $avis->getNote(),
        'commentaire' => $avis->getCommentaire(),
        'date_creation' => $avis->getDateCreation()
    ];
}

// Envoyer les données au format JSON
header('Content-Type: application/json');
echo json_encode($avis_data);
