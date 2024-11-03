<?php
// ajax/get_avis.php

// Activer temporairement l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    require_once '../includes/_db.php';
    require_once '../includes/session.php';
    require_once '../classe/AvisManager.php';
    require_once '../classe/Avis.php';

    // Vérification de l'ID produit
    if (!isset($_GET['id_produit']) || !is_numeric($_GET['id_produit'])) {
        throw new Exception('ID produit invalide ou manquant');
    }

    $id_produit = intval($_GET['id_produit']);
    
    // Vérification de la connexion à la base de données
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception('Erreur de connexion à la base de données');
    }

    // Création de l'instance AvisManager
    $avisManager = new AvisManager($conn);
    
    // Récupération des avis
    $avis = $avisManager->getAvisForProduct($id_produit);
    
    // Préparation des données pour le JSON
    $avis_data = [];
    foreach ($avis as $a) {
        $avis_data[] = [
            'id_avis' => $a->getIdAvis(),
            'id_produit' => $a->getIdProduit(),
            'id_utilisateur' => $a->getIdUtilisateur(),
            'nom_utilisateur' => $a->getNomUtilisateur() ?? 'Anonyme',
            'note' => $a->getNote(),
            'commentaire' => $a->getCommentaire(),
            'date_creation' => $a->getDateCreation()
        ];
    }

    echo json_encode([
        'success' => true,
        'avis' => $avis_data
    ]);

} catch (Exception $e) {
    // Log l'erreur pour le débogage
    error_log("Erreur dans get_avis.php: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
