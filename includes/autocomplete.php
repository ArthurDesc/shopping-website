<?php
require_once '_db.php'; // Assurez-vous que ce fichier contient la connexion à la base de données

header('Content-Type: application/json');

if (isset($_GET['q'])) {
    $query = trim($_GET['q']);
    
    // Vérifiez que la requête n'est pas vide
    if (empty($query)) {
        echo json_encode([]);
        exit();
    }

    // Préparez la requête SQL
    $stmt = $conn->prepare("SELECT id_produit, nom FROM produits WHERE nom LIKE ? LIMIT 5");
    $searchTerm = "%$query%";
    $stmt->bind_param("s", $searchTerm);

    // Exécutez la requête
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $suggestions = [];

        // Récupérez les résultats
        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row;
        }

        // Retournez les résultats en JSON
        echo json_encode($suggestions);
    } else {
        // En cas d'erreur lors de l'exécution de la requête
        echo json_encode(['error' => 'Erreur lors de l\'exécution de la requête.']);
    }
} else {
    // Si 'q' n'est pas défini
    echo json_encode([]);
}
?>
