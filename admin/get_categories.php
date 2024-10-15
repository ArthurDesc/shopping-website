<?php
header('Content-Type: application/json');
require_once '../includes/_db.php';
require_once '../classe/CategoryManager.php';

$categoryManager = new CategoryManager($conn);
$categories = $categoryManager->getAllCategories();

echo json_encode($categories);
?>
