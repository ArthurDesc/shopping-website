<?php
define('BASE_URL', '/shopping-website/');
require_once '../includes/session.php';

// Appel de la fonction logout
logout();

// Redirection vers la page d'authentification
header("Location: " . BASE_URL . "pages/auth.php");
exit();
?>
