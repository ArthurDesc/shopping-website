<?php
session_start();
require_once dirname(__FILE__) . '/../includes/_db.php';
require_once dirname(__FILE__) . '/../classe/Profile.php';

header('Content-Type: application/json');

try {
    // Vérification de la connexion
    if (!isset($_SESSION['id_utilisateur'])) {
        throw new Exception('Utilisateur non connecté');
    }

    // Récupération des données
    $field = $_POST['name'] ?? '';
    $value = trim($_POST['value'] ?? ''); // Ajout du trim() pour nettoyer les espaces

    // Validation de base
    if (empty($value)) {
        throw new Exception('La valeur ne peut pas être vide');
    }

    // Liste des champs autorisés avec leurs règles de validation
    $allowed_fields = ['nom', 'prenom', 'email', 'telephone', 'adresse'];
    if (empty($field) || !in_array($field, $allowed_fields)) {
        throw new Exception('Champ invalide');
    }

    // Initialisation du profil
    $profile = new Profile($conn);
    if (!$profile->loadProfile($_SESSION['id_utilisateur'])) {
        throw new Exception('Impossible de charger le profil');
    }

    // Validation spécifique pour l'email
    if ($field === 'email') {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format d\'email invalide');
        }
        if ($profile->isEmailExists($value)) {
            throw new Exception('Cet email est déjà utilisé');
        }
    }

    // Mise à jour du champ
    if ($profile->updateSingleField($field, $value)) {
        // Mise à jour de la session
        if (in_array($field, ['nom', 'prenom', 'email'])) {
            $_SESSION[$field] = $value;
        }

       
        echo json_encode([
            'success' => true,
            'message' => ucfirst($field) . ' mis à jour avec succès',
            'newValue' => $value
        ]);
    } else {
        throw new Exception('Erreur lors de la mise à jour');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

?>