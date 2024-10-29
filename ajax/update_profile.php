<?php
session_start();
require_once '../includes/_db.php';

if (!isset($_SESSION['id_utilisateur'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

$response = ['success' => false, 'message' => ''];
$errors = [];

// Traitement des informations personnelles
if (isset($_POST['personal_nom'])) {
    $nom = $_POST['personal_nom'];
    $prenom = $_POST['personal_prenom'];
    $email = $_POST['personal_email'];

    // Vérification email
    $stmt = $conn->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ? AND id_utilisateur != ?");
    $stmt->bind_param("si", $email, $_SESSION['id_utilisateur']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Cet email est déjà utilisé";
    }
}

// Traitement des coordonnées
$adresse = $_POST['contact_adresse'] ?? null;
$telephone = $_POST['contact_telephone'] ?? null;

// Traitement du mot de passe
if (!empty($_POST['security_nouveau_motdepasse'])) {
    $motdepasse_actuel = $_POST['security_motdepasse_actuel'];
    $nouveau_motdepasse = $_POST['security_nouveau_motdepasse'];
    $confirmer_motdepasse = $_POST['security_confirmer_nouveau_motdepasse'];

    // Vérifications du mot de passe...
    // [Ajouter votre logique de vérification existante]
}

if (empty($errors)) {
    try {
        $conn->begin_transaction();

        // Mise à jour des informations personnelles
        $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, adresse = ?, telephone = ?";
        $params = [$nom, $prenom, $email, $adresse, $telephone];
        $types = "sssss";

        if (!empty($nouveau_motdepasse)) {
            $sql .= ", motdepasse = ?";
            $params[] = password_hash($nouveau_motdepasse, PASSWORD_DEFAULT);
            $types .= "s";
        }

        $sql .= " WHERE id_utilisateur = ?";
        $params[] = $_SESSION['id_utilisateur'];
        $types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();

        $conn->commit();
        
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['email'] = $email;

        $response['success'] = true;
        $response['message'] = 'Profil mis à jour avec succès';
    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = 'Erreur lors de la mise à jour';
    }
} else {
    $response['message'] = implode(', ', $errors);
}

echo json_encode($response);