<?php
session_start();
require_once dirname(__FILE__) . '/../includes/_db.php';
require_once dirname(__FILE__) . '/../classe/Profile.php';

header('Content-Type: application/json');

try {
    // Log des données reçues
    error_log('Données POST reçues : ' . print_r($_POST, true));
    
    // Vérification de la connexion
    if (!isset($_SESSION['id_utilisateur'])) {
        throw new Exception('Utilisateur non connecté');
    }

    // Récupération et validation des données
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Log des mots de passe (longueur uniquement pour la sécurité)
    error_log('Longueurs des mots de passe - Actuel: ' . strlen($current_password) . ', Nouveau: ' . strlen($new_password));

    // Validations de base
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        throw new Exception('Tous les champs sont obligatoires');
    }

    if ($new_password !== $confirm_password) {
        throw new Exception('Les nouveaux mots de passe ne correspondent pas');
    }

    // Initialisation du profil
    $profile = new Profile($conn);
    if (!$profile->loadProfile($_SESSION['id_utilisateur'])) {
        throw new Exception('Impossible de charger le profil');
    }

    // Mise à jour du mot de passe
    if ($profile->updatePassword($current_password, $new_password)) {
        echo json_encode([
            'success' => true,
            'message' => 'Mot de passe modifié avec succès'
        ]);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
