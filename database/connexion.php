<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "boutique";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

echo json_encode(['status' => 'connected']);
?>
