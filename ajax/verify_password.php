<?php
session_start();
require_once dirname(__FILE__) . '/../includes/_db.php';

$response = ['success' => false];

if (isset($_POST['motdepasse_actuel']) && isset($_SESSION['id_utilisateur'])) {
    $stmt = $conn->prepare("SELECT motdepasse FROM utilisateurs WHERE id_utilisateur = ?");
    $stmt->bind_param("i", $_SESSION['id_utilisateur']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (password_verify($_POST['motdepasse_actuel'], $user['motdepasse'])) {
        $response['success'] = true;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
