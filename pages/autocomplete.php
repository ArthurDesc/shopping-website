<?php
require_once '_db.php';

if (isset($_GET['q'])) {
    $search = $_GET['q'];
    $stmt = $conn->prepare("SELECT nom FROM produits WHERE nom LIKE ? LIMIT 5");
    $searchTerm = $search . '%';
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['nom'];
    }
    
    header('Content-Type: application/json');
    echo json_encode($suggestions);
}