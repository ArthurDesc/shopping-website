<?php
require_once 'classe/ArticleManager.php';
include '../includes/_db.php';

// Créer une connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

$articleManager = new ArticleManager($conn);

$id = $_POST['id'] ?? null;

if ($id) {
    $result = $articleManager->deleteArticle($id);
    echo json_encode(['success' => $result]);
} else {
    echo json_encode(['success' => false, 'message' => 'ID de l\'article non fourni']);
}

$conn->close();

?>