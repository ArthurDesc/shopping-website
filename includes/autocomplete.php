<?php
require_once '_db.php'; // Assurez-vous que ce fichier contient la connexion à la base de données

header('Content-Type: application/json');

if (isset($_GET['q'])) {
    $query = trim($_GET['q']);
    if (empty($query)) {
        echo json_encode([]);
        exit();
    }

    // Préparez la requête SQL
    $stmt = $conn->prepare("SELECT id_produit, nom FROM produits WHERE nom LIKE ? LIMIT 5");
    $searchTerm = "%$query%";
    $stmt->bind_param("s", $searchTerm);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $suggestions = [];

        while ($row = $result->fetch_assoc()) {
            $suggestions[] = $row;
        }

        echo json_encode($suggestions);
    } else {
        echo json_encode(['error' => 'Erreur lors de l\'exécution de la requête.']);
    }
} else {
    echo json_encode([]);
}
?>

<script src="<?php echo BASE_URL; ?>assets/js/autocomplete.js"></script>
