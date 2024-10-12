<?php
require_once '../includes/config.php';
require_once '../includes/_db.php';
require_once '../classe/CategoryManager.php';

// Vérifiez si l'utilisateur est admin (à implémenter selon votre système d'authentification)

header('Content-Type: application/json');

$categoryManager = new CategoryManager($conn);

// Supposons que nous avons une méthode getParentCategories dans CategoryManager
$parentCategories = $categoryManager->getAllCategories();

echo json_encode($parentCategories);

?>