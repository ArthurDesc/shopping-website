<?php
require_once '_db.php';

error_log("Autocomplete.php appelé avec q=" . ($_GET['q'] ?? 'non défini'));

if (isset($_GET['q']) && strlen($_GET['q']) >= 2) {
    $search = $_GET['q'] . '%';
    $stmt = $conn->prepare("SELECT nom FROM produits WHERE nom LIKE ? ORDER BY nom ASC LIMIT 10");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = array();
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['nom'];
    }
    
    error_log("Suggestions trouvées : " . json_encode($suggestions));
    
    header('Content-Type: application/json');
    echo json_encode($suggestions);
} else {
    error_log("Requête invalide ou trop courte");
    echo json_encode([]);
}
