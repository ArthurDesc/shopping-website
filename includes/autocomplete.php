<?php
require_once __DIR__ . '/../includes/_db.php';
require_once __DIR__ . '/../classe/ArticleManager.php';

header('Content-Type: application/json');

$articleManager = new ArticleManager($conn);

if (isset($_GET['q'])) {
    $query = trim($_GET['q']);
    if (empty($query)) {
        echo json_encode([]);
        exit();
    }

    // Ajoutez une nouvelle méthode à ArticleManager pour la recherche d'autocomplétion
    $suggestions = $articleManager->searchArticles($query);

    echo json_encode($suggestions);
} else {
    echo json_encode([]);
}
?>
