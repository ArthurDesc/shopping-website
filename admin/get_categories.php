<?php
require_once '../includes/_db.php';
require_once '../classe/ArticleManager.php';

header('Content-Type: application/json');

$articleManager = new ArticleManager($conn);
$categories = $articleManager->getAllCategories();

echo json_encode($categories);
