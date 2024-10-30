<?php
session_start();
require_once '../includes/_db.php';
require_once '../classe/Profile.php';

header('Content-Type: application/json');

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false, 
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

// Vérification de l'authentification
if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Utilisateur non authentifié'
    ]);
    exit;
}

// Vérification du token CSRF
if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Token de sécurité invalide'
    ]);
    exit;
}

try {
    // Instanciation de la classe Profile
    $profile = new Profile($conn);
    
    // Chargement du profil existant
    if (!$profile->loadProfile($_SESSION['id_utilisateur'])) {
        throw new Exception('Impossible de charger le profil');
    }

    // Récupération et nettoyage des données du formulaire
    $data = [
        'nom' => trim(strip_tags($_POST['nom'] ?? '')),
        'prenom' => trim(strip_tags($_POST['prenom'] ?? '')),
        'email' => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL),
        'telephone' => trim(strip_tags($_POST['telephone'] ?? '')),
        'adresse' => trim(strip_tags($_POST['adresse'] ?? '')),
        'csrf_token' => $_POST['csrf_token']
    ];

    // Validation des données requises
    if (empty($data['nom']) || empty($data['prenom']) || empty($data['email'])) {
        throw new Exception('Tous les champs obligatoires doivent être remplis');
    }

    // Mise à jour du profil
    if ($profile->updateProfile($data)) {
        // Mise à jour de la session
        $_SESSION['nom'] = $data['nom'];
        $_SESSION['prenom'] = $data['prenom'];
        $_SESSION['email'] = $data['email'];

        // Génération d'un nouveau token CSRF pour la prochaine requête
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        echo json_encode([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'user' => $profile->toArray(),
            'newCsrfToken' => $_SESSION['csrf_token']
        ]);
    } else {
        throw new Exception('Erreur lors de la mise à jour du profil');
    }

} catch (Exception $e) {
    // Log de l'erreur côté serveur
    error_log("Erreur mise à jour profil utilisateur {$_SESSION['id_utilisateur']}: " . $e->getMessage());
    
    // Réponse au client
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    // Nettoyage et fermeture des connexions
    if (isset($conn)) {
        $conn->close();
    }
}

?>