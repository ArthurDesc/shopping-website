<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/_db.php';

try {
    $sql = file_get_contents(__DIR__ . '/boutique.sql');
    if ($sql === false) {
        throw new Exception("Impossible de lire le fichier SQL");
    }

    // Désactiver la vérification des clés étrangères
    $conn->query('SET FOREIGN_KEY_CHECKS = 0');

    $queries = explode(';', $sql);
    $executedQueries = 0;
    $skippedQueries = 0;

    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            try {
                // Exécuter la requête
                $result = $conn->multi_query($query);
                if ($result === false) {
                    throw new mysqli_sql_exception($conn->error);
                }
                // Consommer tous les résultats
                do {
                    if ($res = $conn->store_result()) {
                        $res->free();
                    }
                } while ($conn->more_results() && $conn->next_result());
                $executedQueries++;
            } catch (mysqli_sql_exception $e) {
                // Ignorer certaines erreurs spécifiques
                $ignoredErrors = [
                    'already exists',
                    'Duplicate entry',
                    'Multiple primary key defined',
                    'Duplicate foreign key constraint name',
                    'Duplicate column name'
                ];
                $shouldIgnore = false;
                foreach ($ignoredErrors as $errorMessage) {
                    if (strpos($e->getMessage(), $errorMessage) !== false) {
                        $shouldIgnore = true;
                        break;
                    }
                }
                if ($shouldIgnore) {
                    echo "Note: " . $e->getMessage() . "<br>";
                    $skippedQueries++;
                } else {
                    throw $e;
                }
            }
        }
    }

    // Réactiver la vérification des clés étrangères
    $conn->query('SET FOREIGN_KEY_CHECKS = 1');

    echo "<br>Mise à jour de la base de données terminée.<br>";
    echo "Requêtes exécutées : $executedQueries<br>";
    echo "Requêtes ignorées : $skippedQueries<br>";
} catch (Exception $e) {
    echo "<br>Une erreur est survenue : " . $e->getMessage();
}

$conn->close();
?>