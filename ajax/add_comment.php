<?php
// Désactiver l'affichage des erreurs dans la sortie
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '../logs/php-error.log');

// Définir les headers
header('Content-Type: application/json');

session_start();

try {
    require_once '../includes/_db.php';
    
    // Vérification de la session
    if (!isset($_SESSION['id_utilisateur'])) {
        throw new Exception('Utilisateur non connecté');
    }

    // Vérification des données POST
    if (!isset($_POST['id_produit']) || !isset($_POST['note']) || !isset($_POST['commentaire'])) {
        throw new Exception('Données manquantes');
    }

    // Nettoyage et validation des données
    $id_produit = filter_var($_POST['id_produit'], FILTER_VALIDATE_INT);
    $id_utilisateur = filter_var($_SESSION['id_utilisateur'], FILTER_VALIDATE_INT);
    $note = filter_var($_POST['note'], FILTER_VALIDATE_INT);
    $commentaire = trim($_POST['commentaire']);

    // Validations supplémentaires
    if ($id_produit === false || $id_produit <= 0) {
        throw new Exception('ID produit invalide');
    }

    if ($note === false || $note < 1 || $note > 5) {
        throw new Exception('Note invalide');
    }

    if (strlen($commentaire) < 10) {
        throw new Exception('Le commentaire doit faire au moins 10 caractères');
    }

    // Insertion de l'avis
    $sql = "INSERT INTO avis (id_produit, id_utilisateur, note, commentaire, date_creation) 
            VALUES (?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erreur de préparation de la requête: " . $conn->error);
    }

    $stmt->bind_param("iiis", $id_produit, $id_utilisateur, $note, $commentaire);
    
    if (!$stmt->execute()) {
        throw new Exception("Erreur lors de l'insertion: " . $stmt->error);
    }

    $id_avis = $stmt->insert_id;

    // Récupération de l'avis avec le nom d'utilisateur
    $sql = "SELECT a.*, u.nom as nom_utilisateur 
            FROM avis a 
            LEFT JOIN utilisateurs u ON a.id_utilisateur = u.id_utilisateur 
            WHERE a.id_avis = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erreur de préparation de la requête de sélection");
    }

    $stmt->bind_param("i", $id_avis);
    
    if (!$stmt->execute()) {
        throw new Exception("Erreur lors de la sélection: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $avis = $result->fetch_assoc();

    if (!$avis) {
        throw new Exception("Impossible de récupérer l'avis créé");
    }

    echo json_encode([
        'success' => true,
        'comment' => [
            'id_avis' => $avis['id_avis'],
            'id_produit' => $avis['id_produit'],
            'id_utilisateur' => $avis['id_utilisateur'],
            'nom_utilisateur' => $avis['nom_utilisateur'] ?? 'Anonyme',
            'note' => intval($avis['note']),
            'commentaire' => $avis['commentaire'],
            'date_creation' => $avis['date_creation']
        ]
    ]);
} catch (Exception $e) {
    // Log l'erreur pour le débogage
    error_log("Erreur dans add_comment.php: " . $e->getMessage());

    // Envoyer une réponse JSON d'erreur
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
