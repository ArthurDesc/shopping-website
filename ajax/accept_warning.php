<?php
session_start();

// Définir le chemin de base
if (!defined('BASE_URL')) {
    define('BASE_URL', '/shopping-website/');
}

// Marquer l'avertissement comme accepté dans la session
$_SESSION['warning_accepted'] = true;

// Renvoyer une réponse JSON
header('Content-Type: application/json');
echo json_encode(['success' => true]); 