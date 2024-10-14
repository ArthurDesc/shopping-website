<?php
require_once '_db.php';

if (isset($_GET['q'])) {
    $search = $_GET['q'] . '%'; // Modifié pour chercher les mots qui commencent par la saisie
    $stmt = $conn->prepare("SELECT nom FROM produits WHERE nom LIKE ? LIMIT 10"); // Augmenté la limite à 10
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = array();
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['nom'];
    }
    
    echo json_encode($suggestions);
}
