<?php
require_once '../includes/_db.php'; // Assurez-vous que ce fichier contient vos informations de connexion à la base de données

$sql = file_get_contents(__DIR__ . '/boutique (1).sql');

// Séparez les requêtes individuelles
$queries = explode(';', $sql);

// Exécutez chaque requête
foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        $result = $conn->multi_query($query);
        if (!$result) {
            echo "Erreur lors de l'exécution de la requête : " . $conn->error . "<br>";
            echo "Requête problématique : " . $query . "<br>";
        } else {
            do {
                if ($res = $conn->store_result()) {
                    $res->free();
                }
            } while ($conn->more_results() && $conn->next_result());
        }
    }
}



echo "Mise à jour de la base de données terminée.";

$conn->close();
?>
