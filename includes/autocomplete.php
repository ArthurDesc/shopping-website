<?php
require_once '_db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

file_put_contents('debug.log', "Requête reçue: " . print_r($_GET, true) . "\n", FILE_APPEND);

if (isset($_GET['q'])) {
    $query = $_GET['q'];
    $stmt = $conn->prepare("SELECT id_produit, nom FROM produits WHERE nom LIKE ? LIMIT 5");
    $searchTerm = "%$query%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row;
    }
    
    file_put_contents('debug.log', "Résultats: " . print_r($suggestions, true) . "\n", FILE_APPEND);
    
    header('Content-Type: application/json');
    echo json_encode($suggestions);
} else {
    file_put_contents('debug.log', "Aucune requête 'q' trouvée\n", FILE_APPEND);
}
