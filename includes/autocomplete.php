<?php
require_once '_db.php';

if (isset($_GET['q'])) {
    $search = '%' . $_GET['q'] . '%';
    $stmt = $conn->prepare("SELECT nom FROM produits WHERE nom LIKE ? LIMIT 5");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = array();
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['nom'];
    }
    
    echo json_encode($suggestions);
}