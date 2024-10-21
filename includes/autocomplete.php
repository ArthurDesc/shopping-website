<?php
require_once '_db.php';

header('Content-Type: application/json');

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
    
    echo json_encode($suggestions);
} else {
    echo json_encode([]);
}
